<?php
/**
 * Plugin Name: Migrate Brag Dad
 * Plugin URI: https://thebrag.media
 * Description: 
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI: https://thebrag.media
 */





class TBM_Migrate_Dad {
    
    protected static $instance = null;
    
    protected $plugin_name;
    protected $plugin_slug;
    
    public function __construct() {
        $this->plugin_name = 'tbm_migrate_brag_dad';
        $this->plugin_slug = 'tbm-migrate-brag-dad';
        
        add_action('admin_menu', array( $this, 'tbm_tbd_migrate_admin_menu' ) );
        
        // Load admin JavaScript.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        
        add_action( 'wp_ajax_start_pause_migration', array( $this, 'start_pause_migration' ) ); 
        add_action( 'wp_ajax_nopriv_start_pause_migration', array( $this, 'start_pause_migration' ) );
    }
    
    public function tbm_tbd_migrate_admin_menu() {
        add_menu_page(
            'Migrate Brag Dad',
            'Migrate Brag Dad',
            'administrator',
            $this->plugin_slug,
            array( $this, 'index' ),
            'dashicons-admin-tools',
            10
        );
    }
    
    public function index() {
        
    ?>
        <h1>Migrate from dad.thebrag.com to thebrag.com/dad</h1>
        <h2>Click Start to start/pause migration.</h2>
        
        <button id="start-pause-migration" class="button button-primary">Start</button>
        
        <div id="migration-results" style="margin-top: 20px;"></div>
        
    <?php    
    }
    
    public function enqueue_admin_scripts( $hook ) {
        if ( 'toplevel_page_' . $this->plugin_slug == $hook ) :
            wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), NULL, true );
            wp_localize_script( 
                $this->plugin_slug . '-admin-script', 
                $this->plugin_name, 
                array(
                    'url'   => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( $this->plugin_name . '_nonce' ),
                )
            );
        endif;
    }
    
    public function start_pause_migration() {
        if( check_ajax_referer( $this->plugin_name . '_nonce', 'nonce' ) ) :
            
            global $wpdb;
            
            $start_pause = isset( $_POST['start_pause'] ) && ! is_null( $_POST['start_pause'] ) ? $_POST['start_pause'] : 'start';
        
            $current_post = isset( $_POST['current_post'] ) ? absint( $_POST['current_post'] ) : 0;
        
            if ( $current_post > -1 && 'start' === $start_pause ) :
                $curl_url = 'http://dad.the-brag.com/wp-json/api/v1/export';
                $curl_url = 'https://dad.thebrag.com/wp-json/api/v1/export';
                if ( $current_post > 0 ) :
                    $curl_url .= '?post_id=' . $current_post;
                endif;
                $curl_output = json_decode( curl_post( $curl_url, 'GET' ) );
                $article = $curl_output[0];
                
                if ( $article ) :
                    
                    // Check if article is already migrated.
                    $check_existing = $wpdb->get_var( "SELECT post_id FROM {$wpdb->prefix}migrate_dad WHERE post_id = {$article->ID}" );
                    if ( $check_existing ) :
                        wp_send_json_success( array( 'processed_post' => $article->ID, 'result' => 'Already migrated <em><a href="' . $article->guid . '" target="_blank">' . $article->post_title . '</a></em>' ) ); wp_die();
                    endif;
                    
                    $cats = array();
                    if ( $article->post_categories && count( $article->post_categories ) > 0 ) :
                        foreach ( $article->post_categories as $article_category ) :
                            $cat = get_term_by( 'slug', $article_category->slug, 'dad-category' );
                            if ( ! $cat ) :
                                $new_cat = wp_create_term( $article_category->name, 'dad-category' );
                                $cats[] = $new_cat['term_id']; // wp_create_category( $article_category->name, 291447 );
                            else :
                                $cats[] = $cat->term_id;
                            endif;
                        endforeach;
                    endif;
                    
//                    wp_send_json_success( $cats ); wp_die();
                    
                    // Tags
                    $tags = array();
                    if ( $article->post_tags && count( $article->post_tags ) > 0 ) :
                        foreach ( $article->post_tags as $article_tag ) :
                            $tags[] = $article_tag->name;
                        endforeach;
                    endif;                   
                    
                    
                    $new_post_id = wp_insert_post(
                        array(
                            'post_author' => $article->post_author,
                            'post_date' => $article->post_date,
                            'post_date_gmt' => $article->post_date_gmt,
                            'post_content' => $article->post_content,
                            'post_content_filtered' => $article->post_content_filtered,
                            'post_title' => $article->post_title,
                            'post_excerpt' => $article->post_excerpt,
                            'post_status' => $article->post_status,
                            'post_type' => 'dad', // $article->post_type,
                            'comment_status' => $article->comment_status,
                            'ping_status' => $article->ping_status,
                            'post_password' => $article->post_password,
                            'post_name' => $article->post_name,
                            'to_ping' => $article->to_ping,
                            'pinged' => $article->pinged,
                            'post_modified' => $article->post_modified,
                            'post_modified_gmt' => $article->post_modified_gmt,
                            'post_parent' => $article->post_parent,
                            'meta_input' => $article->post_meta,
//                            'post_category' => $cats,
                            'tax_input' => array(
                                'dad-category' => $cats,
                            ),
                            'tags_input' => $tags
                        )
                    );
                
                    if( ! is_wp_error( $new_post_id ) ) {
                        
                        if ( $article->featured_image && $article->featured_image->url ) :
                            // Add Featured Image to Post
                            $image_url        = $article->featured_image->url;
                            $image_name       = $article->featured_image->filename;
                            $upload_dir       = wp_upload_dir(); // Set upload folder
                            $image_data       = file_get_contents($image_url); // Get image data
                            $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
                            $filename         = basename( $unique_file_name ); // Create image file name

                            // Check folder permission and define file location
                            if( wp_mkdir_p( $upload_dir['path'] ) ) {
                                $file = $upload_dir['path'] . '/' . $filename;
                            } else {
                                $file = $upload_dir['basedir'] . '/' . $filename;
                            }

                            // Create the image  file on the server
                            file_put_contents( $file, $image_data );

                            // Check image file type
                            $wp_filetype = wp_check_filetype( $filename, null );

                            // Set attachment data
                            $attachment = array(
                                'post_mime_type' => $wp_filetype['type'],
                                'post_title'     => $article->featured_image->title, // sanitize_file_name( $filename ),
                                'post_excerpt'   => $article->featured_image->caption,
                                'post_content'   => $article->featured_image->description,
                                'post_status'    => 'inherit',
                                'post_author' => $article->featured_image->author
                            );

                            // Create the attachment
                            $attach_id = wp_insert_attachment( $attachment, $file, $new_post_id );

                            // Include image.php
                            require_once(ABSPATH . 'wp-admin/includes/image.php');

                            // Define attachment metadata
                            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                            
                            update_post_meta( $attach_id, '_wp_attachment_image_alt', $article->featured_image->alt );

                            // Assign metadata to attachment
                            wp_update_attachment_metadata( $attach_id, $attach_data );

                            // And finally assign featured image to post
                            set_post_thumbnail( $new_post_id, $attach_id );
                        endif; // If Featured Image is retrieved for the article
                        
                        $wpdb->insert(
                            $wpdb->prefix . 'migrate_dad',
                            array(
                                'post_id' => $article->ID,
                            )
                        );
                        
                        wp_send_json_success( array( 'processed_post' => $article->ID, 'result' => 'Migrated <em><a href="' . $article->guid . '" target="_blank">' . $article->post_title . '</a></em>' ) ); wp_die();
                    } else {
                        wp_send_json_error( $new_post_id->get_error_message() );
                    }
                else :
                    wp_send_json_error();
                endif;
            endif;
        endif;
    }
    
    public function curl_post( $post_url, $method = 'POST', $curl_post = array() ) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $post_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if (in_array($method, array('POST', 'PUT'))) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($curl_post));
        }

        $curl_output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return $curl_output;
    }
}

new TBM_Migrate_Dad();
//add_action( 'plugins_loaded', array( 'TBM_Migrate_Dad', 'get_instance' ) );