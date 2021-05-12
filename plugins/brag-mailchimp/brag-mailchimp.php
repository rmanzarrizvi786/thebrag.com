<?php
/**
 * Plugin Name: The BRAG MailChimp Integration V2
 * Plugin URI: http://seventhstreet.media
 * Description: The BRAG MailChimp Integration
 * Version: 2.0.0
 * Author: Sachin Patel
 * Author URI: http://seventhstreet.media
 */
use \DrewM\MailChimp\MailChimp;
add_action('admin_menu', 'td_mailchimp_plugin_menu');

function td_mailchimp_plugin_menu() {
    add_menu_page('BRAG MailChimp', 'BRAG MailChimp', 'edit_pages', __FILE__, 'td_newsletter_index', 'dashicons-email', 10);
    add_submenu_page( __FILE__, 'New Newsletter', 'Create', 'edit_pages', __FILE__.'/create', 'create_td_newsletter' );
    //add_submenu_page( __FILE__, 'EDM Settings', 'EDM Settings', 'edit_pages', __FILE__.'/edm_settings', 'edm_settings' );
}

function edm_settings() {
?>
<div class="wrap">
<?php
    include ( plugin_dir_path( __FILE__ ) . 'edm-settings.php');
?>
</div>
<?php
}

function create_td_newsletter() {
?>
<div class="wrap">
<?php
    include ( plugin_dir_path( __FILE__ ) . 'form-newsletter.php');
?>
</div>
<?php
}
function td_newsletter_index() {
?>
<div class="wrap">
    <?php
        global $wpdb;
        $id = isset( $_GET['id'] ) ? $_GET['id'] : null;
        $table = $wpdb->base_prefix . "td_newsletters";

        if ( isset( $_GET['preview'] ) ):
            if ( !is_null( $id ) ):
                if ( '1' == get_current_blog_id() ) {
                    include ( plugin_dir_path( __FILE__ ) . 'preview-newsletter-1.php');
                } else {
                    include ( plugin_dir_path( __FILE__ ) . 'preview-newsletter.php');
                }
            else:
                echo 'Newsletter not found.';
            endif;
        elseif ( isset( $_GET['copy'] ) ) :
            if ( !is_null( $id ) ):
                $clone = $wpdb->get_row( "SELECT * FROM $table WHERE id = {$id}" );
                $today =  date( 'j F, Y', strtotime( current_time( 'mysql' ) ) );
                $clone->details = json_decode( $clone->details );
                unset( $clone->details->id );
                $clone->details->date_for = $today;
                $clone->details->subject = 'The BRAG';

                if ( isset( $clone->details->title ) ) :
                    $title_parts = explode( '#', $clone->details->title );
                    $clone->details->title = $title_parts[0] . '#' . ( $title_parts[1] + 1);
                else:
                    $clone->details->title = '#';
                endif;

//                die( '<pre>' . print_r( $clone, true ) . '</pre>' );

                $clone->details = json_encode( $clone->details );
                $wpdb->insert(
                    $table,
                    array(
                        'date_for' => date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) ),
                        'details' => $clone->details,
                        'blog_id' => get_current_blog_id(),
                        'created_at' => current_time( 'mysql' ),
                        'updated_at' => current_time( 'mysql' ),
                    ),
                    array(
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                    )
                );
                ?>
                <script>
                    window.location = '?page=brag-mailchimp/brag-mailchimp.php&edit=1&id=<?php echo $wpdb->insert_id; ?>';
                </script>
                <?php
            endif;
        elseif ( isset( $_GET['edit'] ) ) :
            if ( !is_null( $id ) ):
                $newsletter = $wpdb->get_row( "SELECT * FROM $table WHERE id = {$id}" );
                $newsletter->details = json_decode( $newsletter->details );
            endif;
            include ( plugin_dir_path( __FILE__ ) . 'form-newsletter.php');
        elseif ( isset( $_GET['delete'] ) ):
            $wpdb->delete( $table, array( 'id' => $id) );
        ?>
            <script>
                window.location = '?page=brag-mailchimp/brag-mailchimp.php';
            </script>
        <?php
        elseif ( isset( $_GET['create-on-mc'] ) ):
            if ( !is_null( $id ) ):
                require_once( plugin_dir_path( __FILE__ ) . 'mailchimp-api/MailChimp.php');
                $api_key = '727643e6b14470301125c15a490425a8-us1';
                $MailChimp = new MailChimp( $api_key );

                $newsletter = $wpdb->get_row( "SELECT * FROM $table WHERE id = {$id}" );
                $newsletter->details = json_decode( $newsletter->details );

                if ( $newsletter->details->subject == '' ) {
                    $newsletter->details->subject = 'Newsletter';
                }

//                $list_id = 'c9114493ef';
                if ( '127.0.0.1' == $_SERVER['REMOTE_ADDR'] ) {
                    $list_ids = array(
                        1 => 'c9114493ef',
                        5 => 'f0eedde184',
                        6 => '2a48cd9086',
                    );
                } else {
                    $list_ids = array(
                        1 => 'c9114493ef',
                        2 => 'f0eedde184',
                        3 => '2a48cd9086',
                    );
                }


                $data = array(
                    "type" => "regular",
                    "recipients" => array(
                        "list_id" => $list_ids[get_current_blog_id()],
                    ),
                    "settings" => array(
                        "subject_line" => $newsletter->details->subject,
                        "preview_text" => $newsletter->details->preview_text,
                        "title" => $newsletter->details->title,
                        "reply_to" => $newsletter->details->reply_to,
                        "from_name" => $newsletter->details->from_name
                    ),
                );

                $campaign = $MailChimp->post( 'campaigns', $data );

//                var_dump( $campaign ); exit;
                $campaign_id = $campaign['id'];

                ob_start();
//                include(plugin_dir_path( __FILE__ ) . 'preview-newsletter.php');
                if ( '1' == get_current_blog_id() ) {
                    include ( plugin_dir_path( __FILE__ ) . 'preview-newsletter-1.php');
                } else {
                    include ( plugin_dir_path( __FILE__ ) . 'preview-newsletter.php');
                }
                $html = ob_get_contents();
                ob_end_clean();

                $content = array(
                    'html' => $html,
                );
                $MailChimp->put( 'campaigns/' . $campaign_id . '/content', $content );

                $wpdb->update( $table, array( 'status' => '1' ), array ( 'id' => $newsletter->id ) );
                ?>
                <script>
                    window.location = '?page=brag-mailchimp/brag-mailchimp.php';
                </script>
                <?php

            endif;
        else:
            include ( plugin_dir_path( __FILE__ ) . 'list.php');
        endif;
    ?>
</div>
<?php
}

add_action('wp_ajax_save_edm_settings', 'ajax_save_edm_settings');
function ajax_save_edm_settings() {
    $errors = array();
    if ( count( $_POST['data'] ) > 0 ):
        parse_str($_POST['data'], $data);
        $data = stripslashes_deep( $data );
        if ( isset( $data['edm_include_video_story'] ) && $data['edm_include_video_story'] == 1 ) :
            foreach ( $data as $value ) :
                if ( trim( $value ) == '' ) :
                    $errors[] = 'Please input all fields.';
                    break;
                endif;
            endforeach;
        endif;
    else:
        $errors[] = 'Incomplete data.';
    endif; // If there is post data
    if ( count( $errors ) > 0 ) {
        echo json_encode( array( 'errors' => $errors ) );
    } else {
        if ( ! isset( $data['edm_include_video_story'] ) )
            delete_option( 'edm_include_video_story' );

        /*
        $featured_video_image_id = td_get_image_id( $data['edm_featured_video_image'] );
        if ( !is_null( $featured_video_image_id ) ) :
            td_image_resize( $featured_video_image_id, 625, 313 );
            $featured_video_image_src = wp_get_attachment_image_src( $featured_video_image_id, array( 625, 313 ) );
            $data['edm_featured_video_image'] = $featured_video_image_src[0];
        endif;
         *
         */
        if ( $data['edm_featured_video_image'] ) :
            $data['edm_featured_video_image'] = ssm_resize_image( $data['edm_featured_video_image'], 625, 313 );
        endif;

        foreach ( $data as $key => $value ) :
            update_option( $key, $value );
        endforeach;
        if ( count( $errors ) == 0 ):
            echo json_encode( array( 'success' => true ) );
        endif;
    }
    if ( count( $errors ) > 0 )
        echo json_encode( array( 'errors' => $errors ) );
    die();
}

add_action('wp_ajax_save_campaign', 'ajax_save_newsletter');
function ajax_save_newsletter() {
    $errors = array();
    if ( count( $_POST['data'] ) > 0 ):
        parse_str($_POST['data'], $data);

        //add content area
        if( ! empty( $data[ 'banner_content_visual' ] ) ) {
            $data[ 'banner_content' ] = wp_kses_post( $data[ 'banner_content_visual' ]  );
        } elseif( ! empty( $data[ 'banner_content' ] ) ) {
            $data[ 'banner_content' ] = wp_kses_post( $data[ 'banner_content' ]  );
        } else {
            $data[ 'banner_content' ] = '';
        }

        if ( strtotime( $data['date_for'] ) < strtotime( date('Y-m-d') ) ):
            $errors[] = 'Date must be today or in future.';
        endif;

        /*
        $cover_story_image_id = td_get_image_id( $data['cover_story_image'] );
        if ( !is_null( $cover_story_image_id ) ) :
            td_image_resize( $cover_story_image_id, 625, 313 );
            $cover_story_image_src = wp_get_attachment_image_src( $cover_story_image_id, array( 625, 313 ) );
            $data['cover_story_image'] = $cover_story_image_src[0];
        endif;
         *
         */
        if ( $data['cover_story_image'] ) :
            $data['cover_story_image'] = ssm_resize_image( $data['cover_story_image'], 625, 313 );
        endif;

        /*
        $featured_story_image_id_1 = td_get_image_id( $data['featured_story_image_1'] );
        if ( !is_null( $featured_story_image_id_1 ) ) :
            td_image_resize( $featured_story_image_id_1, 625, 313 );
            $cover_story_image_src = wp_get_attachment_image_src( $featured_story_image_id_1, array( 625, 313 ) );
            $data['featured_story_image_1'] = $cover_story_image_src[0];
        endif;
         *
         */
        if ( $data['featured_story_image_1'] ) :
            $data['featured_story_image_1'] = ssm_resize_image( $data['featured_story_image_1'], 625, 313 );
        endif;

        /*
        $featured_story_image_id_2 = td_get_image_id( $data['featured_story_image_2'] );
        if ( !is_null( $featured_story_image_id_2 ) ) :
            td_image_resize( $featured_story_image_id_2, 625, 313 );
            $cover_story_image_src = wp_get_attachment_image_src( $featured_story_image_id_2, array( 625, 313 ) );
            $data['featured_story_image_2'] = $cover_story_image_src[0];
        endif;
         *
         */
        if ( $data['featured_story_image_2'] ) :
            $data['featured_story_image_2'] = ssm_resize_image( $data['featured_story_image_2'], 625, 313 );
        endif;

        /*
        $featured_video_image_id = td_get_image_id( $data['featured_video_image'] );
        if ( !is_null( $featured_video_image_id ) ) :
            td_image_resize( $featured_video_image_id, 625, 313 );
            $cover_story_image_src = wp_get_attachment_image_src( $featured_video_image_id, array( 625, 313 ) );
            $data['featured_video_image'] = $cover_story_image_src[0];
        endif;
         *
         */
        if ( $data['featured_video_image'] ) :
            $data['featured_video_image'] = ssm_resize_image( $data['featured_video_image'], 625, 313 );
        endif;

        if ( isset( $data['post_images'] ) ) :
            foreach ( $data['post_images'] as $key => $image_url ) :
                if ( $image_url ) :
                    /*
                    $image_id = td_get_image_id( $image_url );
                    if ( !is_null( $image_id ) ) :
                        td_image_resize( $image_id, 625, 313 );
                        $img_src = wp_get_attachment_image_src( $image_id, array( 625, 313 ) );
                        $data['post_images'][$key] = $img_src[0];
                    endif;
                     *
                     */
                    $data['post_images'][$key] = ssm_resize_image( $image_url, 625, 313 );
                endif;
            endforeach;
        endif;

        if ( count( $errors ) == 0 ):
            asort( $data['posts'] );

            $data = stripslashes_deep( $data );

            /*
            foreach ( $data['posts'] as $post_id => $order ):
                $post_image_id = get_post_thumbnail_id( $post_id );
                td_image_resize( $post_image_id, 625, 313 );
            endforeach;

            if ( isset( $data['featured_gig'] ) ):
                $gig_image_id = get_post_thumbnail_id( $data['featured_gig'] );
                td_image_resize( $gig_image_id, 280, 280 );
            endif;
            */

            global $wpdb;
            $table = $wpdb->base_prefix . "td_newsletters";

            if ( isset( $data['id'] ) ):
                $wpdb->update(
                    $table,
                    array(
                        'date_for' => date( 'Y-m-d', strtotime( $data['date_for'] ) ),
                        'details' => json_encode( $data ),
                        'status' => '0',
                        'updated_at' => current_time( 'mysql' ),
                    ),
                    array ( 'id' => $data['id'] ) );
            else:
                $wpdb->insert(
                    $table,
                    array(
                        'date_for' => date( 'Y-m-d', strtotime( $data['date_for'] ) ),
                        'details' => json_encode( $data ),
                        'blog_id' => get_current_blog_id(),
                        'created_at' => current_time( 'mysql' ),
                        'updated_at' => current_time( 'mysql' ),
                    ),
                    array(
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                    )
            );
            endif;


            echo json_encode( array( 'success' => true ) );
        endif; // If no errors
    else:
        $errors[] = 'Incomplete data.';
    endif; // If there is post data
    if ( count( $errors ) > 0 )
        echo json_encode( array( 'errors' => $errors ) );
    die();
}

function ssm_resize_image( $url, $thumb_width, $thumb_height ) {
    $dir = wp_upload_dir();
    $import_dir_part = '/edm/' . date('Y-m/d/');
    $import_dir =  $dir['basedir'] . $import_dir_part;
    if ( ! is_dir( $import_dir ) )
        wp_mkdir_p( $import_dir );
    $img = $import_dir . basename( $url );
    file_put_contents( $img, file_get_contents( $url ) );

    $explode = explode( ".", basename( $url ) );
    $filetype = end( $explode );

    if ($filetype == 'jpg') {
        $image = imagecreatefromjpeg("$img");
    } else
    if ($filetype == 'jpeg') {
        $image = imagecreatefromjpeg("$img");
    } else
    if ($filetype == 'png') {
        $image = imagecreatefrompng("$img");
    } else
    if ($filetype == 'gif') {
        $image = imagecreatefromgif("$img");
    }

    $filename = str_replace( '.' . $filetype, '.jpg', basename( $url ) );
//uniqid() . '.jpg';
    $filepath = $import_dir . $filename;

//    $image = imagecreatefromjpeg( $img );
    $width = imagesx($image);
    $height = imagesy($image);
    $original_aspect = $width / $height;
    $thumb_aspect = $thumb_width / $thumb_height;
    if ( $original_aspect >= $thumb_aspect ) {
       // If image is wider than thumbnail (in aspect ratio sense)
       $new_height = $thumb_height;
       $new_width = $width / ($height / $thumb_height);
    } else {
       // If the thumbnail is wider than the image
       $new_width = $thumb_width;
       $new_height = $height / ($width / $thumb_width);
    }
    $thumb = imagecreatetruecolor( $thumb_width, $thumb_height );
    // Resize and crop
    imagecopyresampled($thumb,
                       $image,
                       0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                       0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                       0, 0,
                       $new_width, $new_height,
                       $width, $height);
    imagejpeg($thumb, $filepath, 80);
    $upload    = wp_upload_dir();
    $base_url = $upload['baseurl'] . $import_dir_part;
//    @unlink( $img );
    return $base_url . $filename;
}

function td_get_image_id( $url ) {
//    global $wpdb;
//    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
//        return $attachment[0];
    $attachment_id = NULL;
    $dir = wp_upload_dir();
    if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?
            $file = basename( $url );
            $query_args = array(
                    'post_type'   => 'attachment',
                    'post_status' => 'inherit',
                    'fields'      => 'ids',
                    'meta_query'  => array(
                            array(
                                    'value'   => $file,
                                    'compare' => 'LIKE',
                                    'key'     => '_wp_attachment_metadata',
                            ),
                    )
            );
            $query = new WP_Query( $query_args );
            if ( $query->have_posts() ) {
                    foreach ( $query->posts as $post_id ) {
                            $meta = wp_get_attachment_metadata( $post_id );
                            $original_file       = basename( $meta['file'] );
                            $cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
                            if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
                                    $attachment_id = $post_id;
                                    break;
                            }
                    }
            }
    } else {
        $import_dir =  $dir['basedir'] . '/import/' . date('Y-m/d/');
        if ( ! is_dir( $import_dir ) )
            wp_mkdir_p( $import_dir );
        $img = $import_dir . basename( $url );
        file_put_contents( $img, file_get_contents( $url ) );

        $filename = $import_dir . basename( $url );
        $parent_post_id = 0;

        $filetype = wp_check_filetype( basename( $filename ), null );

        $wp_upload_dir = wp_upload_dir();

        $attachment = array(
                'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
        );

        $attachment_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );
    }
    return $attachment_id;
}


add_action('wp_ajax_get_remote_data', 'ajax_get_remote_data');
function ajax_get_remote_data() {
    if ( strlen( $_POST['data'] ) > 0 ):
        parse_str($_POST['data'], $data);
        $sites_html = file_get_contents( $data['url'] );
        $html = new DOMDocument();
        @$html->loadHTML($sites_html);
        $meta_og_title = $meta_og_img = $meta_og_description = null;
        foreach( $html->getElementsByTagName('meta') as $meta ) {
            if( $meta->getAttribute( 'property' ) == 'og:image' ){
                if ( ! isset( $meta_og_img ) ) {
                    $meta_og_img = $meta->getAttribute('content');
                }
            }
            if( $meta->getAttribute( 'property' )=='og:title' ){
                if ( ! isset( $meta_og_title ) ) {
                    $meta_og_title = $meta->getAttribute('content');
                }
            }
            if( $meta->getAttribute( 'property' )=='og:description' ) {
                if ( ! isset( $meta_og_description ) ) {
                    $meta_og_description = $meta->getAttribute('content');
                }
            }
        }
        echo json_encode( array( 'success' => true, 'title' => trim( $meta_og_title ), 'description' => trim( $meta_og_description ), 'image' => trim( $meta_og_img ) ) );
        die();
    endif;
    echo json_encode( array( 'success' => false, 'url' => $data['url'] ) );
    die();
}
?>
