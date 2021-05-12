<?php
/**
 * Plugin Name: Custom URLs
 * Plugin URI: http://www.seventhstreet.media
 * Description: Generate Custom URLs
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI: http://www.seventhstreet.media
 */
add_action('admin_menu', 'ssm_custom_urls_plugin_menu');
function ssm_custom_urls_plugin_menu() {
    add_options_page('Custom URLs', 'Custom URLs', 'edit_pages', 'ssm-artist-urls', 'ssm_custom_urls');
}

function ssm_custom_urls() {
    enqueue_js_ssm_custom_urls();
?>
<div class="wrap">
    <?php
    if ( ! current_user_can( 'edit_pages' ) ) {
        wp_die( 'You are not allowed to be on this page.' );
    }
    global $wpdb;
    $table = $wpdb->base_prefix . 'td_artist_urls';
    $action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'index';
    $plugin_url = '?page=ssm-artist-urls';
    switch ( $action ) :
        case 'index' :
        default :
            $artists = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY artist_name ASC" );
            include ( 'list.php' );
            break;
        case 'add' :
            include ( 'form.php' );
            break;
        case 'edit' :
            $artist_ID = isset( $_GET['id'] ) ? $_GET['id'] : '';
            $artist = $wpdb->get_row( "SELECT * FROM {$table} WHERE artist_ID = {$artist_ID} LIMIT 1 " );
            include ( 'form.php' );
            break;
        case 'search_a' :
            $ch = isset( $_GET['ch'] ) ? $_GET['ch'] : '';
            $artists = $wpdb->get_results( "SELECT * FROM {$table} WHERE artist_name LIKE '{$ch}%' OR artist_slug LIKE '{$ch}%'  ORDER BY artist_name ASC" );
            include ( 'list.php' );
            break;
        case 'search' :
            $term = isset( $_GET['term'] ) ? $_GET['term'] : '';
            $artists = $wpdb->get_results( "SELECT * FROM {$table} WHERE artist_name LIKE '%{$term}%' OR artist_slug LIKE '%{$term}%'  ORDER BY artist_name ASC" );
            include ( 'list.php' );
            break;
    endswitch;
    ?>
</div>
<?php
}

function save_artist_url( $data ) {
    global $wpdb;
    global $wp_rewrite;
    $table = $wpdb->base_prefix . 'td_artist_urls';

    $artist_ID = isset( $data['artist_ID'] ) ? $data['artist_ID'] : NULL;

    $artist_name = $data['artist_name'];
    $url_slug = strtolower( $data['url_slug'] );
    $artist_slug = strtolower( $data['artist_slug'] );

    if ( trim( $artist_name ) == '' || trim ( $artist_slug ) == '' ) {
        $errors[] = 'Artist Name and URL Slug are required fields.';
    } else if ( preg_match('/[\/\\\'^£$%&*()}{@#~?><>,|=_+¬-]/', $artist_slug) || strlen($artist_slug) != mb_strlen($artist_slug, 'utf-8') ) {
        $errors[] = 'URL Slug cannot contain special characters.';
    }

    $url_slug = sanitize_title( $url_slug );
    $artist_slug = sanitize_title( $artist_slug );

    if ( count( $errors ) === 0 ) :
        if ( ! is_null( $artist_ID ) ) :
            $update = $wpdb->update(
                $table,
                array(
                    'artist_name' => $artist_name,
                    'url_slug' => $url_slug,
                    'artist_slug' => $artist_slug,
                    'image_id' => $data['image_id'],
                    'intro_para' => $data['intro_para'],
                    'metadesc' => $data['metadesc'],
                    'facebook' => $data['facebook'],
                    'twitter' => $data['twitter'],
                    'instagram' => $data['instagram'],
                ),
                array( 'artist_ID' => $artist_ID )
            );
        else:
            $wpdb->insert(
                $table,
                array(
                    'artist_name' => $artist_name,
                    'url_slug' => $url_slug,
                    'artist_slug' => $artist_slug,
                    'image_id' => $data['image_id'],
                    'intro_para' => $data['intro_para'],
                    'metadesc' => $data['metadesc'],
                    'facebook' => $data['facebook'],
                    'twitter' => $data['twitter'],
                    'instagram' => $data['instagram'],
                )
            );
        endif;
//        update_option( "rewrite_rules", FALSE ); $wp_rewrite->flush_rules( true );
        return true;
    else :
        $_SESSION['form_posts'] = $data;
        $_SESSION['ssm_errors'] = $errors;
        return false;
    endif; // If no Errors
}

add_action( 'wp_loaded', 'ssm_process_form' );
function ssm_process_form(){
    $plugin_url = '?page=ssm-artist-urls';
    if ( isset( $_POST['form'] ) && $_POST['form'] == 'ssm-artist-urls' ) :
        if( isset( $_POST['action'] ) && $_POST['action'] == 'save' ) :
            $form_posts = stripslashes_deep( $_POST );
            if( save_artist_url( $form_posts ) ) :
                wp_redirect( $plugin_url );
            else:
                if ( isset( $form_posts['artist_ID'] ) ) :
                    wp_redirect( $plugin_url . '&action=edit&id=' . $form_posts['artist_ID'] );
                else:
                    wp_redirect( $plugin_url . '&action=add' );
                endif;
            endif;
            exit();
        endif;
    endif;

    if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'delete_custom_url' ) :
        $form_posts = $_REQUEST;
        if ( isset( $form_posts['id'] ) ) :
            global $wpdb;
            $wpdb->delete( $wpdb->base_prefix . 'td_artist_urls', array( 'artist_ID' => $form_posts['id'] ) );
            wp_redirect( $plugin_url );
        endif;
        exit();
    endif;
}

function enqueue_js_ssm_custom_urls () {
    wp_enqueue_script( 'jquery-ui', plugin_dir_url(__FILE__) . '/js/artist-urls.js', array ( 'jquery' ), 1.1, true);
}

function ssm_register_session(){
    /* if ( session_status() == PHP_SESSION_NONE )
        session_start(); */
}
add_action( 'init','ssm_register_session' );

// Custom Rewrite for Artist Profile Page
function custom_rewrite_tag() {
    add_rewrite_tag('%artistnameslug%', '([^&]+)');
}
add_action('init', 'custom_rewrite_tag', 10, 0);
function custom_rewrite_rule_artist_festival() {
    global $wpdb;
    $urls = $wpdb->get_results( "SELECT DISTINCT url_slug FROM {$wpdb->base_prefix}td_artist_urls WHERE url_slug IS NOT NULL" );
    if ( count( $urls ) > 0 ) :
        foreach ( $urls as $url ) :
            add_rewrite_rule('^' . $url->url_slug . '/([^/]*)/page/([0-9]{1,})/?','index.php?page_id=783061&artistnameslug=$matches[1]&paged=$matches[2]','top');
            add_rewrite_rule('^' . $url->url_slug . '/([^/]*)/?','index.php?page_id=783061&artistnameslug=$matches[1]','top');
        endforeach;
    endif;
    /*
    add_rewrite_rule('^artist/([^/]*)/page/([0-9]{1,})/?','index.php?page_id=502679&artistnameslug=$matches[1]&paged=$matches[2]','top');
    add_rewrite_rule('^artist/([^/]*)/?','index.php?page_id=502679&artistnameslug=$matches[1]','top');

    add_rewrite_rule('^festival/([^/]*)/page/([0-9]{1,})/?','index.php?page_id=502679&artistnameslug=$matches[1]&paged=$matches[2]','top');
    add_rewrite_rule('^festival/([^/]*)/?','index.php?page_id=502679&artistnameslug=$matches[1]','top');
     *
     */
}
add_action('init', 'custom_rewrite_rule_artist_festival', 10, 0);

// Assign Page Title for Artist Profile Page
function assignPageTitleArtist( $title ) {
    if ( is_page_template( 'page-artist.php' ) ) {
        global $wp_query;
        global $wpdb;
        $artistnameslug = $wp_query->query_vars['artistnameslug'];
        $artist_name = $wpdb->get_var( "SELECT artist_name FROM {$wpdb->base_prefix}td_artist_urls WHERE artist_slug = '{$artistnameslug}' LIMIT 1");
        $title = $artist_name . ' - ' . get_bloginfo( 'name' );
    }
    return $title;
}
add_filter( 'wpseo_title', 'assignPageTitleArtist' );

// Assign canonical URL for Artist Profile Page
function assignCanonicalURLArtist( $title ) {
    if ( is_page_template( 'page-artist.php' ) ) {
        global $wp_query;
        global $wpdb;
        $artistnameslug = $wp_query->query_vars['artistnameslug'];
        $artist = $wpdb->get_row( "SELECT * FROM {$wpdb->base_prefix}td_artist_urls WHERE artist_slug = '{$artistnameslug}' LIMIT 1");
        $title = site_url() . '/' . $artist->url_slug . '/' . $artistnameslug . '/';
    }
    return $title;
}
add_filter( 'wpseo_canonical', 'assignCanonicalURLArtist' );

// Assign Meta Desc for Artist Profile Page
function assignMetaDescArtist( $metadesc ) {
    if ( is_page_template( 'page-artist.php' ) ) {
        global $wp_query;
        global $wpdb;
        $artistnameslug = $wp_query->query_vars['artistnameslug'];
        $metadesc = $wpdb->get_var( "SELECT metadesc FROM {$wpdb->base_prefix}td_artist_urls WHERE artist_slug = '{$artistnameslug}' LIMIT 1");
        return $metadesc;
    }
    return $metadesc;
}
add_filter( 'wpseo_metadesc', 'assignMetaDescArtist' );

// Assign OG Image for Artist Profile Page
function assignOgImageArtist() {
    if ( is_page_template( 'page-artist.php' ) ) {
        global $wp_query;
        global $wpdb;
        $artistnameslug = $wp_query->query_vars['artistnameslug'];
        $image_id = $wpdb->get_var( "SELECT image_id FROM {$wpdb->base_prefix}td_artist_urls WHERE artist_slug = '{$artistnameslug}' LIMIT 1");
        $artist_img_src = wp_get_attachment_image_src( $image_id, 'cover-story' );
        $GLOBALS['wpseo_og']->image_output( $artist_img_src[0] );
    }
}
// add_filter( 'wpseo_opengraph', 'assignOgImageArtist' );

/**
 * Load Posts from next page AJAX
 */
function ssm_ajax_load_more_artist_posts() {
    global $wpdb;
    $args = isset( $_POST['query'] ) ? array_map( 'esc_attr', $_POST['query'] ) : array();
    $artistnameslug = $args['artistnameslug'];
    $artist = $wpdb->get_row( "SELECT * FROM {$wpdb->base_prefix}td_artist_urls WHERE artist_slug = '{$artistnameslug}' LIMIT 1");

    if ( !is_null( $artist ) ) :
        $args = array();
        $args['post_type'] = 'post';
        $args['paged'] = esc_attr( $_POST['page'] );
        $args['post_status'] = 'publish';
        $args['s'] = $artist->artist_name;
        $args['sentence'] = true;

        ob_start();

        query_posts( $args );
        if( have_posts() ):
            while( have_posts() ):
                the_post();
                ?>
                <!-- Story Start -->
        <div class="news_story">
            <div class="post-thumbnail-home">
                <a href="<?php the_permalink(); ?>">
                    <?php
                    if ( '' !== get_the_post_thumbnail() ) :
                        the_post_thumbnail( 'thumbnail' );
                    else:
                    ?>
                    <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder.png" alt="Brag Magazine" />
                    <?php endif; ?>
                </a>
            </div><!-- .post-thumbnail -->
            <div class="post-content">
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <p class="post-excerpt"><?php echo string_limit_words(get_the_excerpt(), 60); ?></p>
            </div>
            <div class="clear"></div>
        </div>
        <!-- Story End -->
                <?php
            endwhile;
        endif;
        wp_reset_postdata();
        $data = ob_get_clean();
        wp_send_json_success( $data );
    endif;
    wp_die();
}
add_action( 'wp_ajax_ssm_ajax_load_more_artist_posts', 'ssm_ajax_load_more_artist_posts' );
add_action( 'wp_ajax_nopriv_ssm_ajax_load_more_artist_posts', 'ssm_ajax_load_more_artist_posts' );
