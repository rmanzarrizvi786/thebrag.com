<?php

// define('ICONS_URL', get_template_directory_uri() . '/images/');
// define('CDN_URL', ICONS_URL);
define('ICONS_URL', 'https://cdn.thebrag.com/icons/');
define('CDN_URL', 'https://cdn.thebrag.com/tb/');
define('OBSERVER_CDN_URL', 'https://cdn.thebrag.com/observer/');

// Add default posts and comments RSS feed links to head.
add_theme_support('automatic-feed-links');

/*
 * Let WordPress manage the document title.
 * By adding theme support, we declare that this theme does not use a
 * hard-coded <title> tag in the document head, and expect WordPress to
 * provide it for us.
 */
add_theme_support('title-tag');

/*
* Enable support for Post Thumbnails on posts and pages.
*
* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
*/
add_theme_support('post-thumbnails');

//add_image_size( 'thebrag-featured-image', 2000, 1200, true );
//add_image_size( 'thebrag-thumbnail-home', 150, 150, true );

// This theme uses wp_nav_menu() in two locations.
/*
 * To-do
 */
register_nav_menus(array(
    'get_in_touch' => __('Get In Touch', 'thebrag'),
    'top' => __('Top Menu', 'thebrag'),
    'shitshow' => __('Sh!tShow', 'thebrag'),
    'top-what-you-love' => __('Top What You Love', 'thebrag'),
    'top-check-this-out' => __('Top Check This Out', 'thebrag'),
));

/*
* Switch default core markup for search form, comment form, and comments
* to output valid HTML5.
*/
add_theme_support('html5', array(
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
));

/*
* This theme styles the visual editor to resemble the theme style,
* specifically font, colors, and column width.
*/
add_editor_style(array('assets/css/editor-style.css', thebrag_fonts_url()));

/*
 * Added functions
 */

/**
 * Register custom fonts.
 */
function thebrag_fonts_url()
{
    $fonts_url = ''; {
        $font_families = array();

        $font_families[] = 'Droid Sans:400,700';

        $query_args = array(
            'family' => urlencode(implode('|', $font_families)),
        );

        $fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
    }
    return esc_url_raw($fonts_url);
}

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Seventeen 1.0
 */
function thebrag_javascript_detection()
{
    echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action('wp_head', 'thebrag_javascript_detection', 0);

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function thebrag_pingback_header()
{
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">' . "\n", get_bloginfo('pingback_url'));
    }
}
add_action('wp_head', 'thebrag_pingback_header');

/**
 * If more than one page exists, return TRUE.
 */
function show_posts_nav()
{
    global $wp_query;
    return ($wp_query->max_num_pages > 1);
}

function string_limit_words($string, $word_limit)
{
    $words = explode(' ', $string, ($word_limit + 1));
    if (count($words) > $word_limit) {
        array_pop($words);
        return implode(' ', $words) . '...';
    }
    return implode(' ', $words);
}

// Article Category
register_taxonomy('style', array(''), array('hierarchical' => true, 'label' => 'Article Style', 'query_var' => true, 'rewrite' => array('slug' => 'style'), 'capabilities' => array('manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_posts', 'assign_terms' => 'edit_gallerys', 'assign_terms' => 'manage_categories'), 'show_ui' => true, 'public' => true));

// Sub Category
/*
register_taxonomy(
    'sub-category',
    array ( 'post' ),
    array(
        'hierarchical' => true,
        'label' => 'Sub Category',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'sub-category' ), //, 'with_front' => false ),
        'capabilities' => array(
            'manage_terms' => 'manage_categories',
            'edit_terms' => 'manage_categories',
            'delete_terms' => 'manage_categories',
            'assign_terms' => 'edit_posts',
            'assign_terms' => 'edit_gallerys',
            'assign_terms' => 'manage_categories'
        ),
        'show_admin_column' => true,
        'show_ui' => true,
        'public' => true
    )
);
*/

// Venue Categories
register_taxonomy('venue-type', array('venue'), array('hierarchical' => true, 'label' => 'Venue Type', 'query_var' => true, 'rewrite' => array('slug' => 'venue-type'), 'capabilities' => array('manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_posts', 'assign_terms' => 'edit_gallerys', 'assign_terms' => 'manage_categories'), 'show_ui' => true, 'public' => true));

register_taxonomy('venue-area', array('venue'), array('hierarchical' => true, 'label' => 'Venue Area', 'query_var' => true, 'rewrite' => array('slug' => 'venue-area'), 'capabilities' => array('manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_posts', 'assign_terms' => 'edit_gallerys', 'assign_terms' => 'manage_categories'), 'show_ui' => true, 'public' => true));

register_taxonomy('venue-features', array('venue'), array('hierarchical' => true, 'label' => 'Venue Features', 'query_var' => true, 'rewrite' => array('slug' => 'venue-features'), 'capabilities' => array('manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_posts', 'assign_terms' => 'edit_gallerys', 'assign_terms' => 'manage_categories'), 'show_ui' => true, 'public' => true));

register_taxonomy('venue-suburb', array('venue'), array('hierarchical' => true, 'label' => 'Venue Suburb', 'query_var' => true, 'rewrite' => array('slug' => 'venue-suburb'), 'capabilities' => array('manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_posts', 'assign_terms' => 'edit_gallerys', 'assign_terms' => 'manage_categories'), 'show_ui' => true, 'public' => true));

// Gig Categories
register_taxonomy(
    'gig-type',
    array('gig'),
    array(
        'hierarchical' => true,
        'labels' => array(
            'name' => 'Gig Type',
            'singular_name' => 'Gig Type'
        ),
        'query_var' => true,
        'rewrite' => array('slug' => 'gig-type'),
        'capabilities' => array(
            'manage_terms' => 'read',
            'edit_terms' => 'read',
            'delete_terms' => 'read',
            'assign_terms' => 'read',
            'assign_terms' => 'read'
        ),
        'show_ui' => true,
        'public' => true
    )
);

register_taxonomy('gig-genre', array('gig'), array('hierarchical' => true, 'label' => 'Gig Genre', 'query_var' => true, 'rewrite' => array('slug' => 'gig-genre'), 'capabilities' => array('manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_posts', 'assign_terms' => 'edit_gallerys', 'assign_terms' => 'manage_categories'), 'show_ui' => true, 'public' => true));

register_taxonomy('gig-artist', array('gig'), array('hierarchical' => false, 'label' => 'Gig Artist', 'query_var' => true, 'rewrite' => array('slug' => 'gig-artist'), 'capabilities' => array('manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_posts', 'assign_terms' => 'edit_gallerys', 'assign_terms' => 'manage_categories'), 'show_ui' => true, 'public' => true));

register_taxonomy('gig-support', array('gig'), array('hierarchical' => false, 'label' => 'Gig Support', 'query_var' => true, 'rewrite' => array('slug' => 'gig-support'), 'capabilities' => array('manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_posts', 'assign_terms' => 'edit_gallerys', 'assign_terms' => 'manage_categories'), 'show_ui' => true, 'public' => true));

// Competition Category
register_taxonomy('competition-category', array('freeshit'), array('hierarchical' => true, 'label' => 'Competition', 'query_var' => true, 'rewrite' => array('slug' => 'competition-category'), 'capabilities' => array('manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_posts', 'assign_terms' => 'edit_gallerys', 'assign_terms' => 'manage_categories'), 'show_ui' => true, 'public' => true));

// Job Category
register_taxonomy('job-category', array(''), array('hierarchical' => true, 'label' => 'Job', 'query_var' => true, 'rewrite' => array('slug' => 'job-category'), 'capabilities' => array('manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_posts', 'assign_terms' => 'edit_gallerys', 'assign_terms' => 'manage_categories'), 'show_ui' => true, 'public' => true));

function load_js_css()
{
    wp_enqueue_script('scripts', CDN_URL . 'scripts.min.js', array('jquery'), '20230624.2', true);
    // wp_enqueue_script('scripts', get_template_directory_uri() . '/js/scripts.js', array('jquery'), time(), true);


    if (is_single()) {
        global $post;
        $args = array(
            'url'   => admin_url('admin-ajax.php'),
            'exclude_posts' => isset($post) ? $post->ID : NULL,
            'current_post' => isset($post) ? $post->ID : NULL
        );
        wp_localize_script('scripts', 'tbm_load_next_post', $args);
    }

    // wp_enqueue_script('lazysizes', get_template_directory_uri() . '/js/lazysizes.min.js', array(), '20181128', true);
}
add_action('wp_enqueue_scripts', 'load_js_css');

function get_post_excerpt_by_id($post_id)
{
    global $post;
    $post = get_post($post_id);
    setup_postdata($post);
    $the_excerpt = get_the_excerpt();
    wp_reset_postdata();
    return $the_excerpt;
}

/*
 * Theme Settings
 */
$themename = 'The BRAG';
$shortname = 'tb';

$categories = get_categories('hide_empty=0&orderby=name');
$wp_cats = array();
foreach ($categories as $category_list) {
    $wp_cats[$category_list->cat_ID] = $category_list->cat_name;
}
//array_unshift( $wp_cats, 'Choose a category' );

$theme_options = array(
    array(
        'label' => $themename . ' Options',
        'type' => 'title',
    ),
    array(
        'label' => 'Homepage',
        'type' => 'section',
    ),
    array('type' => 'open'),

    /*
    array(
        'label' => 'Cover Story',
        'desc' => 'for Home Page',
        'id' => $shortname . '_cover_story_title',
        'type' => 'text',
        'default' => NULL,
        'placeholder' => 'Search...'
    ),

    array(
        'label' => 'Cover Story ID',
        'desc' => 'DO NOT TOUCH THIS FIELD. Search for Cover Story in above field.',
        'id' => $shortname . '_cover_story_ID',
        'type' => 'text',
        'readonly' => true,
    ),
*/

    array(
        'label' => 'Featured Video',
        'desc' => 'for Home Page',
        'id' => $shortname . '_featured_video',
        'type' => 'text',
        'default' => NULL,
        'placeholder' => 'YouTube Link'
    ),

    array(
        'label' => 'Featured Video Artist Title',
        'desc' => 'for Home Page',
        'id' => $shortname . '_featured_video_artist_title',
        'type' => 'text',
        'default' => NULL,
        'placeholder' => 'Featured Video Artist Title'
    ),
    array(
        'label' => 'Featured Video Songs Title',
        'desc' => 'for Home Page',
        'id' => $shortname . '_featured_video_song_title',
        'type' => 'text',
        'default' => NULL,
        'placeholder' => 'Featured Video Songs Title'
    ),
    array(
        'label' => 'Spotify Playlist Embed URI',
        'desc' => 'for Right hand side.',
        'id' => $shortname . '_spotify_playlist',
        'type' => 'text',
    ),
    array(
        'label' => 'Music Gig',
        'desc' => 'for Home page',
        'id' => $shortname . '_music_gig_title',
        'type' => 'text',
        'default' => NULL,
        'placeholder' => 'Search...'
    ),
    array(
        'label' => 'Music Gig ID',
        'desc' => 'DO NOT TOUCH THIS FIELD. Search for Gig in above field.',
        'id' => $shortname . '_music_gig_ID',
        'type' => 'text',
        'readonly' => true,
    ),
    array(
        'label' => 'Comedy Gig',
        'desc' => 'for Home page',
        'id' => $shortname . '_comedy_gig_title',
        'type' => 'text',
        'default' => NULL,
        'placeholder' => 'Search...'
    ),
    array(
        'label' => 'Comedy Gig ID',
        'desc' => 'DO NOT TOUCH THIS FIELD. Search for Gig in above field.',
        'id' => $shortname . '_comedy_gig_ID',
        'type' => 'text',
        'readonly' => true,
    ),
    array('type' => 'close'),
);

//echo '<pre>' .print_r( $theme_options, true ) . '</pre>';

function td_theme_add_init()
{
    $file_dir = get_template_directory_uri(); //get_bloginfo('template_directory');

    wp_enqueue_style('admin', $file_dir . '/css/admin.css', false, '2.3', 'all');

    wp_enqueue_script('td-jquery-autocomplete', $file_dir . '/js/jquery.auto-complete.js', array('jquery'), '1.0', true);
    wp_enqueue_script('td-options-ajax-search', $file_dir . '/js/scripts-admin.js', array('jquery'), '1.1', true);
}

add_action('admin_init', 'td_theme_add_init');

add_action('wp_ajax_nopriv_get_listing_gigs', 'ajax_listings_gig');
add_action('wp_ajax_get_listing_gigs', 'ajax_listings_gig');

function ajax_listings_gig()
{
    if (isset($_POST['name'])) {
        $args = array(
            'post_type' => 'gig',
            'post_status' => 'publish',
            's' => $_POST['name'],
            'posts_per_page' => 10,
        );

        $args['date_query'] = array(
            'after' => date_i18n('Y-m-d', strtotime('-30 days'))
        );

        $return = array();
        $query = get_posts($args);
        foreach ($query as $key => $post_data) {
            $return[] = array($post_data->ID, $post_data->post_title);
        }
        echo json_encode($return);
        wp_reset_postdata();
    }
    wp_die();
}

add_action('wp_ajax_nopriv_get_listing_posts', 'ajax_listings_posts');
add_action('wp_ajax_get_listing_posts', 'ajax_listings_posts');
function ajax_listings_posts()
{
    if (isset($_POST['name'])) {
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            's' => $_POST['name'],
            'posts_per_page' => 10,
        );

        $args['date_query'] = array(
            'after' => date_i18n('Y-m-d', strtotime('- 30 days'))
        );

        $query = get_posts($args);
        foreach ($query as $key => $post_data) {
            $return[] = array($post_data->ID, $post_data->post_title);
        }
        echo json_encode($return);
        wp_reset_postdata();
    }
    wp_die();
}

/*
 * Author Social Media Links
 */
function td_author_contactmethods($contactmethods)
{
    $contactmethods['twitter'] = 'Twitter'; // Twitter
    $contactmethods['facebook'] = 'Facebook'; // Facebook
    $contactmethods['linkedin'] = 'LinkedIn'; // LinkedIn
    $contactmethods['instagram'] = 'Instagram'; // Instagram
    return $contactmethods;
}
add_filter('user_contactmethods', 'td_author_contactmethods', 10, 1);

/**
 * Adding ajax search functionality to the theme
 * @return
 */
add_action('wp_ajax_nopriv_td_ajax_search', 'td_ajax_search');
add_action('wp_ajax_td_ajax_search', 'td_ajax_search');

function td_ajax_search()
{
    $post_type = isset($_POST['type']) ? $_POST['type'] : 'any';
    $args = array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        's' => $_POST['term'],
        'posts_per_page' => 10
    );
    $query = new WP_Query($args);

    $return = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $excerpt = string_limit_words(get_the_excerpt(), 30);
            $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
            $thumbnail = $thumbnail[0];
            $metadesc = htmlentities(get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true));
            $return[] = [get_the_ID(), get_the_title(), get_the_permalink(), get_the_date('d M, Y'), $excerpt, $thumbnail, $metadesc];
        }
    }
    //    $return[] = ['0', '---- Search for ' . $_POST['term'] . ' ----', '?s=' . $_POST['term'], date('d M, Y'), ''];
    echo json_encode($return);
    die();
}

if (function_exists('register_sidebars'))
    register_sidebars(5);
//  register_sidebars(1, array('name'=>'Header'));

add_action('init', 'fbInstantArticleRSS');
function fbInstantArticleRSS()
{
    add_feed('instant_articles', 'fbInstantArticle');
}
function fbInstantArticle()
{
    get_template_part('rss', 'instant_articles');
}

function td_remove_p_tags_around_iframes($content)
{
    $content = str_replace('&nbsp;', '', $content);
    $wraped_content = preg_replace(
        '/<p( style=\".*?\")?>(<iframe .*?><\/iframe>)(.*)?<\/p>/',
        '<figure class="op-interactive">$2</figure>',
        $content
    );
    $wraped_content = str_replace('width="100%"', 'width="400"', $wraped_content);
    return $wraped_content;
}
add_filter('the_content', 'td_remove_p_tags_around_iframes');

function td_remove_p_tags_around_script($content)
{
    $content = str_replace('&nbsp;', '', $content);
    $wraped_content = preg_replace(
        '/<p( style=\".*?\")?>(<script .*?><\/script>)(.*)?<\/p>/',
        '$2',
        $content
    );
    return $wraped_content;
}
add_filter('the_content', 'td_remove_p_tags_around_script');

function td_filter_ptags_on_images($content)
{
    if (function_exists('is_amp_endpoint') && !is_amp_endpoint()) {
        return preg_replace('/<p(.*)>(<img .* \/>)<\/p>/', '<figure>$2</figure>', $content);
    }
    return $content;
}
add_filter('the_content', 'td_filter_ptags_on_images');

function td_image_resize($attachment_id, $width, $height, $crop = true)
{
    $path = get_attached_file($attachment_id);
    if (!file_exists($path)) {
        return false;
    }

    $upload    = wp_upload_dir();
    $path_info = pathinfo($path);
    $base_url  = $upload['baseurl'] . str_replace($upload['basedir'], '', $path_info['dirname']);

    //    $meta = wp_get_attachment_metadata( $attachment_id );
    //    foreach ( $meta['sizes'] as $key => $size ) {
    //        if ( $size['width'] == $width && $size['height'] == $height )
    //        {
    //            return "{$base_url}/{$size['file']}";
    //        }
    //    }

    // Generate new size
    $resized = image_make_intermediate_size($path, $width, $height, $crop);
    if ($resized && !is_wp_error($resized)) {
        // Let metadata know about our new size.
        $key                 = sprintf('resized-%dx%d', $width, $height);
        $meta['sizes'][$key] = $resized;
        wp_update_attachment_metadata($attachment_id, $meta);
        return "{$base_url}/{$resized['file']}";
    }

    // Return original if fails
    return "{$base_url}/{$path_info['basename']}";
}

/*
 * Custom Post Type - Freeshit - Start
 */
function create_posttype_freeshit()
{
    // Set UI labels for Custom Post Type
    $labels = array(
        'name' => __('Freeshits'),
        'singular_name' => __('Freeshit'),
        'menu_name' => __('Freeshits'),
        'all_items' => __('All Freeshits'),
        'view_item' => __('View Freeshit'),
        'add_new_item' => __('Add New Freeshit'),
        'add_new' => __('Add New'),
        'edit_item' => __('Edit Freeshit'),
        'update_item' => __('Update Freeshit'),
        'search_items' => __('Search Freeshit'),
        'not_found' => __('Not Found'),
        'not_found_in_trash' => __('Not found in Trash'),
    );

    // Set other options for Custom Post Type
    $args = array(
        'label' => __('freeshit'),
        'description' => __('Freeshits'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments',),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type('freeshit', $args);
}
add_action('init', 'create_posttype_freeshit');

/*
 * Custom Post Type - Venue - Start
 */
function create_posttype_venue()
{
    // Set UI labels for Custom Post Type
    $labels = array(
        'name' => __('Venues'),
        'singular_name' => __('Venue'),
        'menu_name' => __('Venues'),
        'all_items' => __('All Venues'),
        'view_item' => __('View Venue'),
        'add_new_item' => __('Add New Venue'),
        'add_new' => __('Add New'),
        'edit_item' => __('Edit Venue'),
        'update_item' => __('Update Venue'),
        'search_items' => __('Search Venue'),
        'not_found' => __('Not Found'),
        'not_found_in_trash' => __('Not found in Trash'),
    );

    // Set other options for Custom Post Type
    $args = array(
        'label' => __('venue'),
        'description' => __('Venues'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments',),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type('venue', $args);
}
add_action('init', 'create_posttype_venue');

/*
 * Custom Post Type - Gig - Start
 */
function create_posttype_gig()
{
    // Set UI labels for Custom Post Type
    $labels = array(
        'name' => __('Gigs'),
        'singular_name' => __('Gig'),
        'menu_name' => __('Gigs'),
        'all_items' => __('All Gigs'),
        'view_item' => __('View Gig'),
        'add_new_item' => __('Add New Gig'),
        'add_new' => __('Add New'),
        'edit_item' => __('Edit Gig'),
        'update_item' => __('Update Gig'),
        'search_items' => __('Search Gig'),
        'not_found' => __('Not Found'),
        'not_found_in_trash' => __('Not found in Trash'),
    );

    // Set other options for Custom Post Type
    $args = array(
        'label' => __('gig'),
        'description' => __('Gigs'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments',),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
        //        'rewrite' => array( 'slug' => 'gig/%gigdate%', 'with_front' => false),
    );
    register_post_type('gig', $args);
}
add_action('init', 'create_posttype_gig');
/*
add_action( 'wp_loaded', 'add_gig_permastructure' );
function add_gig_permastructure() {
    global $wp_rewrite;
    add_permastruct( 'gig', 'gig/%gigdate%/%gig%/', false );
}
*/
/*
function gig_permalink( $permalink, $post ) {
    global $wpdb;
    if ( $post->post_type !== 'gig' )
        return $permalink;
    $query_datetime_furutre = "SELECT * FROM {$wpdb->prefix}gig_details
                WHERE
                    post_id = '" . $post->ID . "'
                ORDER BY
                    gig_datetime DESC
                LIMIT 1
            ";
    $datetime = $wpdb->get_row( $query_datetime_furutre );
    if ( ! is_null( $query_datetime_furutre ) ) {
        $permalink = str_replace( '%gigdate%',  date( 'd-M-Y', strtotime( $datetime->gig_datetime ) ), $permalink );
    } else {
        $permalink = str_replace( '%gigdate%/',  '', $permalink );
    }
    return $permalink;
}
// Translate the custom post type permalink tags
add_filter('post_type_link', 'gig_permalink', 10, 2);
 *
 */

//var_dump( $wp_rewrite ); exit;

/* Custom Post Type - Gig - End */

/*
 * Custom Post Type - Sydney Eat - Start
 */
/*
function create_posttype_sydney_eats() {
    // Set UI labels for Custom Post Type
    $labels = array(
        'name' => __( 'Sydney Eats' ),
        'singular_name' => __( 'Sydney Eat' ),
        'menu_name' => __( 'Sydney Eats' ),
        'all_items' => __( 'All Sydney Eats' ),
        'view_item' => __( 'View Sydney Eat' ),
        'add_new_item' => __( 'Add New Sydney Eat' ),
        'add_new' => __( 'Add New' ),
        'edit_item' => __( 'Edit Sydney Eat' ),
        'update_item' => __( 'Update Sydney Eat' ),
        'search_items' => __( 'Search Sydney Eat' ),
        'not_found' => __( 'Not Found' ),
        'not_found_in_trash' => __( 'Not found in Trash' ),
    );

    // Set other options for Custom Post Type
    $args = array(
        'label' => __( 'gig' ),
        'description' => __( 'Sydney Eats' ),
        'labels' => $labels,
        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'sydney-eats', $args );
}
add_action( 'init', 'create_posttype_sydney_eats' );
 *
 */
/* Custom Post Type - Sydney Eat - End */

/*
 * Custom Post Type - Issue - Start
 */
function create_posttype_issue()
{
    // Set UI labels for Custom Post Type
    $labels = array(
        'name' => __('Issues'),
        'singular_name' => __('Issue'),
        'menu_name' => __('Issues'),
        'all_items' => __('All Issues'),
        'view_item' => __('View Issue'),
        'add_new_item' => __('Add New Issue'),
        'add_new' => __('Add New'),
        'edit_item' => __('Edit Issue'),
        'update_item' => __('Update Issue'),
        'search_items' => __('Search Issue'),
        'not_found' => __('Not Found'),
        'not_found_in_trash' => __('Not found in Trash'),
    );

    // Set other options for Custom Post Type
    $args = array(
        'label' => __('issue'),
        'description' => __('Issues'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments',),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type('issue', $args);
}
add_action('init', 'create_posttype_issue');
/* Custom Post Type - Issue - End */

/*
 * Custom Post Type - Podcast - Start
 */
function create_posttype_podcast()
{
    // Set UI labels for Custom Post Type
    $labels = array(
        'name' => __('Podcasts'),
        'singular_name' => __('Podcast'),
        'menu_name' => __('Podcasts'),
        'all_items' => __('All Podcasts'),
        'view_item' => __('View Podcast'),
        'add_new_item' => __('Add New Podcast'),
        'add_new' => __('Add New'),
        'edit_item' => __('Edit Podcast'),
        'update_item' => __('Update Podcast'),
        'search_items' => __('Search Podcast'),
        'not_found' => __('Not Found'),
        'not_found_in_trash' => __('Not found in Trash'),
    );

    // Set other options for Custom Post Type
    $args = array(
        'label' => __('podcast'),
        'description' => __('Podcasts'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions',),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type('podcast', $args);
}
add_action('init', 'create_posttype_podcast');

function my_connection_types()
{
    p2p_register_connection_type(array(
        'name' => 'gig_to_venue',
        'from' => 'gig',
        'to' => 'venue',
        'admin_box' => array(
            'show' => 'from',
            'context' => 'side'
        ),
        'title' => array(
            'from' => __('Venue', 'my-textdomain'),
            'to' => __('Gig', 'my-textdomain')
        ),
        'from_labels' => array(
            'singular_name' => __('Gig', 'my-textdomain'),
            'search_items' => __('Search Gig', 'my-textdomain'),
            'not_found' => __('No gig found.', 'my-textdomain'),
            'create' => __('Gig', 'my-textdomain'),
        ),
        'to_labels' => array(
            'singular_name' => __('Venue', 'my-textdomain'),
            'search_items' => __('Search Venues', 'my-textdomain'),
            'not_found' => __('No venue found.', 'my-textdomain'),
            'create' => __('Select Venue', 'my-textdomain'),
        ),
    ));
}
add_action('p2p_init', 'my_connection_types');

function remove_quick_edit($actions, $post)
{
    if ($post->post_type == 'gig') :
        unset($actions['inline hide-if-no-js']);
    endif;
    return $actions;
}
add_filter('post_row_actions', 'remove_quick_edit', 10, 2);
/*
 * Store Extra Gig Information (date/time, repeat rule) in separate table
 */
add_action('save_post', 'tb_gig_insert', 10, 2);
function tb_gig_insert($post_id, $post)
{
    global $wpdb;
    if (is_admin() && get_post_type($post) == 'gig' && $post->post_status != 'trash' && $post->post_status != 'auto-draft') {

        if (!is_array($_POST) || !isset($_POST['gig']))
            return;

        $wpdb->delete($wpdb->prefix . "gig_details", array('post_id' => $post_id));

        $repeat_settings = isset($_POST['gig']['repeat']['settings']) ? $_POST['gig']['repeat']['settings'] : '';

        $start = $_POST['gig']['date'];

        if ($repeat_settings != '') :
            if (isset($_POST['gig']['repeat']['freq'])) :
                $repeat_rule = 'RRULE:FREQ=' . $_POST['gig']['repeat']['freq'] . ';';
                switch ($_POST['gig']['repeat']['freq']):
                    case 'DAILY':
                        $daily_interval = $_POST['gig']['repeat']['rule']['daily']['INTERVAL'];

                        if ($_POST['gig']['repeat']['rule']['daily']['byday'] == 'every_weekday') :
                            $byday = 'MO,TU,WE,TH,FR';
                            if ($_POST['gig']['repeat']['rule']['range_of_repeat'] == 'COUNT') :
                                $count = $_POST['gig']['repeat']['rule']['count_child'];
                                $i = 0;
                                $j = 1;
                                while ($j <= $count) :
                                    $date_to_add = date('Y-m-d', strtotime($i . 'days', strtotime($start)));
                                    if (in_array(date('D', strtotime($date_to_add)), array('Mon', 'Tue', 'Wed', 'Thu', 'Fri'))) {
                                        $dates[] = $date_to_add;
                                        $j++;
                                    }
                                    $i++;
                                endwhile;
                            endif;
                        elseif ($_POST['gig']['repeat']['rule']['daily']['byday'] == 'every_mo_we_fr') :
                            $byday = 'MO,WE,FR';
                            if ($_POST['gig']['repeat']['rule']['range_of_repeat'] == 'COUNT') :
                                $count = $_POST['gig']['repeat']['rule']['count_child'];
                                $i = 0;
                                $j = 1;
                                while ($j <= $count) :
                                    $date_to_add = date('Y-m-d', strtotime($i . 'days', strtotime($start)));
                                    if (in_array(date('D', strtotime($date_to_add)), array('Mon', 'Wed', 'Fri'))) {
                                        $dates[] = $date_to_add;
                                        $j++;
                                    }
                                    $i++;
                                endwhile;
                            endif;
                        elseif ($_POST['gig']['repeat']['rule']['daily']['byday'] == 'every_tu_th') :
                            $byday = 'TU,TH';
                            if ($_POST['gig']['repeat']['rule']['range_of_repeat'] == 'COUNT') :
                                $count = $_POST['gig']['repeat']['rule']['count_child'];
                                $i = 0;
                                $j = 1;
                                while ($j <= $count) :
                                    $date_to_add = date('Y-m-d', strtotime($i . 'days', strtotime($start)));
                                    if (in_array(date('D', strtotime($date_to_add)), array('Tue', 'Thu'))) {
                                        $dates[] = $date_to_add;
                                        $j++;
                                    }
                                    $i++;
                                endwhile;
                            endif;
                        endif;

                        if (!isset($byday)) :
                            if ($_POST['gig']['repeat']['rule']['range_of_repeat'] == 'COUNT') :
                                $count = $_POST['gig']['repeat']['rule']['count_child'];
                                $i = 0;
                                $j = 1;
                                $date_to_add = $start;
                                $dates[] = $date_to_add;
                                while ($j < $count) :
                                    $date_to_add = date('Y-m-d', strtotime($daily_interval . 'days', strtotime($date_to_add)));
                                    $dates[] = $date_to_add;
                                    $j++;
                                endwhile;
                            endif;
                            $repeat_rule .= 'INTERVAL=' . $daily_interval . ';';
                        else :
                            $repeat_rule .= 'INTERVAL=1;';
                            $repeat_rule .= 'BYDAY=' . $byday . ';';
                        endif;

                        break; // Case DAILY
                    case 'WEEKLY':
                        $dates[] = $start;
                        if ($_POST['gig']['repeat']['rule']['range_of_repeat'] == 'COUNT') :
                            $count = $_POST['gig']['repeat']['rule']['count_child'];
                            $weekly_interval = $_POST['gig']['repeat']['rule']['weekly']['INTERVAL'];
                            $repeat_rule .= 'INTERVAL=' . $weekly_interval . ';';
                            $i = 0;
                            while ($i < ($count - 1)) :
                                $date_to_add = date('Y-m-d', strtotime(($i + $weekly_interval) . 'weeks', strtotime($start)));
                                $dates[] = $date_to_add;
                                $i++;
                            endwhile;
                        endif;
                        break; // Case WEEKLY
                    case 'MONTHLY':
                        $dates[] = $start;

                        if ($_POST['gig']['repeat']['rule']['range_of_repeat'] == 'COUNT') :
                            $count = $_POST['gig']['repeat']['rule']['count_child'];
                            $monthly_byday = isset($_POST['gig']['repeat']['rule']['monthly']['BYMONTHDAY_BYMONTH_child']['BYMONTHDAY']) ? $_POST['gig']['repeat']['rule']['monthly']['BYMONTHDAY_BYMONTH_child']['BYMONTHDAY'] : '';
                            if ($monthly_byday != '') :
                                $monthly_byday_months = $_POST['gig']['repeat']['rule']['monthly']['BYMONTHDAY_BYMONTH_child']['BYMONTH'];
                                $i = 0;
                                while ($i < $count) :
                                    foreach ($monthly_byday_months as $monthly_byday_month) :
                                        $month_name = date('F', mktime(0, 0, 0, $monthly_byday_month, 10));

                                    endforeach;
                                endwhile;
                            endif;
                        endif;
                        break; // Case MONTHLY
                    default:
                        break;
                endswitch;
                if ($_POST['gig']['repeat']['rule']['range_of_repeat'] == 'COUNT')
                    $repeat_rule .= 'COUNT=' . $_POST['gig']['repeat']['rule']['count_child'] . ';';
                elseif ($_POST['gig']['repeat']['rule']['range_of_repeat'] == 'UNTIL')
                    $repeat_rule .= 'UNTIL=' . date('Y-m-d', strtotime($_POST['gig']['repeat']['rule']['until'])) . ';';
            endif;
        else :
            $dates[] = $start;
        endif;

        foreach ($dates as $date) :
            $wpdb->insert(
                $wpdb->prefix . "gig_details",
                array(
                    'post_id' => $post_id,
                    'gig_datetime' => $date . ' ' . $_POST['gig']['time'],
                    'repeat_rule' => $repeat_rule,
                )
            );
        endforeach;

        //        die( $repeat_rule );
    }
}

add_action("admin_init", "add_gig_detail_fields");
function add_gig_detail_fields()
{
    add_meta_box("gig-details", "Gig Date and Time Details", "gig_details", "gig", "normal", "high");
}
function gig_details()
{
    global $post;
    global $wpdb;
    $gig = array();

    $details = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "gig_details WHERE post_id = '" . $post->ID . "' AND gig_datetime != '0000-00-00 00:00:00' LIMIT 1", ARRAY_A);

    $date = NULL;
    if ($details && is_array($details) && count($details) > 0) :
        $gig['date'] = date('Y-m-d', strtotime($details['gig_datetime']));
        $gig['time'] = date('H:i', strtotime($details['gig_datetime']));
        $rules = !is_null($details['repeat_rule']) ? explode(';', $details['repeat_rule']) : array();
        if ($rules && is_array($rules) && count($rules) > 0) :
            foreach ($rules as $rule) :
                $t_rules = explode('=', $rule);
                if ($t_rules && is_array($t_rules) && count($t_rules) > 1) :
                    $rules[str_replace('RRULE:', '', $t_rules[0])] = $t_rules[1];
                endif;
            endforeach;


            switch ($rules['FREQ']):
                case 'DAILY':
                    if (isset($rules['BYDAY'])) :
                        if (array('MO', 'TU', 'WE', 'TH', 'FR') == explode(',', $rules['BYDAY'])) :
                            $repeat['daily']['byday'] = 'every_weekday';
                        elseif (array('MO', 'WE', 'FR') == explode(',', $rules['BYDAY'])) :
                            $repeat['daily']['byday'] = 'every_mo_we_fr';
                        elseif (array('TU', 'TH') == explode(',', $rules['BYDAY'])) :
                            $repeat['daily']['byday'] = 'every_tu_th';
                        endif;
                    else :
                        $repeat['daily']['byday'] = 'everyday';
                    endif;
                    //                    $gig['repeat']['daily']['INTERVAL'] = str_replace()
                    break;
                case 'WEEKLY':
                    $repeat['weekly']['byday'] = explode(',', $rules['BYDAY']);
                    break;
                case 'MONTHLY':
                    $repeat['monthly']['day_month'] = NULL;
                    if (isset($rules['BYDAY']) && isset($rules['BYMONTH']) && $rules['BYDAY'] != '' && $rules['BYMONTH'] != '') {
                        $repeat['monthly']['day_month'] = 'BYDAY_BYMONTH';
                        $repeat['rule_byday_count'] = substr($rules['BYDAY'], 0, 2);
                        $repeat['rule_byday_day'] = substr($rules['BYDAY'], 2);
                        $repeat['months'] = explode(',', $rules['BYMONTH']);
                        //                        echo ' ' . $rule_byday_count . ' ' . $rule_byday_day;
                        //                        gig[repeat][rrule][monthly][BYDAY_BYMONTH_child][BYDAY_COUNT]
                    } else {
                        $repeat['monthly']['day_month'] = 'BYMONTHDAY_BYMONTH';
                    }
                    break;
                case 'YEARLY':
                    break;
            endswitch;

            if (isset($rules['COUNT'])) :
                $repeat['range_of_repeat'] =  'COUNT';
            elseif (isset($rules['UNTIL'])) :
                $repeat['range_of_repeat'] =  'UNTIL';
                $rules['UNTIL'] = date('Y-m-d', strtotime($rules['UNTIL']));
            endif;
        endif;
    endif;


    include('gig-date-time-form.php');
?>
    <?php
}

add_action('init', 'register_cpt_snaps');

function register_cpt_snaps()
{
    $labels = array(
        'name' => _x('Snaps', 'snaps'),
        'singular_name' => _x('Snaps', 'snaps'),
        'add_new' => _x('Add New', 'snaps'),
        'add_new_item' => _x('Add New Snaps', 'snaps'),
        'edit_item' => _x('Edit Snaps', 'snaps'),
        'new_item' => _x('New Snaps', 'snaps'),
        'view_item' => _x('View Snaps', 'snaps'),
        'search_items' => _x('Search Snaps Galleries', 'snaps'),
        'not_found' => _x('No snaps galleries found', 'snaps'),
        'not_found_in_trash' => _x('No snaps galleries found in Trash', 'snaps'),
        'parent_item_colon' => _x('Parent Snaps:', 'snaps'),
        'menu_name' => _x('Snaps', 'snaps'),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Snaps from gigs around Australia',
        'supports' => array('title', 'editor', 'thumbnail', 'author'),
        'taxonomies' => array('category',),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        //        'register_meta_box_cb' => 'add_gallery_metaboxes',
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array('slug' => 'snaps',),
        //        'capability_type'     => 'page',
        'capability_type'     => array('page', 'snaps'),
        'capabilities' => array(
            'publish_posts' => 'snaps',
            'edit_posts' => 'snaps',
            'edit_others_posts' => 'snaps',
            'read_private_posts' => 'snaps',
            'edit_post' => 'snaps',
            'delete_post' => 'snaps',
            'read_post' => 'snaps',
            'publish_post' => 'snaps',
        ),
    );

    register_post_type('snaps', $args);
}


function snaps_attachments($attachments)
{
    $fields = array();
    $args = array(
        'label'         => 'Photos', // title of the meta box (string)
        'post_type'     => array('snaps'), // all post types to utilize (string|array)
        'position'      => 'normal', // meta box position (string) (normal, side or advanced)
        'priority'      => 'high', // meta box priority (string) (high, default, low, core)
        'filetype'      => null,  // no filetype limit // allowed file type(s) (array) (image|video|text|audio|application)
        'note'          => 'Attach photos here!', // include a note within the meta box (string)
        'append'        => true, // by default new Attachments will be appended to the list but you can have then prepend if you set this to false
        'button_text'   => __('Attach Photos', 'photos'), // text for 'Attach' button in meta box (string)
        'modal_text'    => __('Attach', 'photos'), // text for modal 'Attach' button (string)
        'router'        => 'browse', // which tab should be the default in the modal (string) (browse|upload)
        'post_parent'   => false, // whether Attachments should set 'Uploaded to' (if not already set)
        'fields'        => $fields, // fields array
    );

    $attachments->register('snaps_attachments', $args); // unique instance name
}

add_action('attachments_register', 'snaps_attachments');

add_filter('attachments_default_instance', '__return_false');

// Get First Sentence of the $string
function tb_first_sentence($string)
{
    // First remove unwanted spaces
    $string = str_replace(" .", ".", $string);
    $string = str_replace(" ?", "?", $string);
    $string = str_replace(" !", "!", $string);
    // Find periods, exclamation- or questionmarks with a word before but not after.
    preg_match('/^.*[^\s](\.|\?|\!)/U', $string, $match);
    return isset($match[0]) ? $match[0] : '';
}

add_filter('document_title_separator', 'tb_document_title_separator');
function tb_document_title_separator($sep)
{
    $sep = "|";
    return $sep;
}

// Add Featured Image to RSS Feed Item
//add_action('rss2_item', 'add_my_rss_node');
function add_my_rss_node()
{
    global $post;
    if (has_post_thumbnail($post->ID)) :
        $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium');
        echo ("<image>{$thumbnail[0]}</image>");
    endif;
}

// Add Custom Author to RSS Feed Item
//add_action('rss2_item', 'add_author_field_node');
function add_author_field_node()
{
    global $post;
    echo '<author>';
    if (get_field('Author', $post->ID)) {
        echo '<![CDATA[' . get_field('Author', $post->ID) . ']]>';
    } else {
        the_author_meta('display_name', $post->post_author);
    }
    echo '</author>';
}

/*
 * Custom Post Type - Letter to Editor - Start
 */
function create_posttype_tbLetters()
{
    // Set UI labels for Custom Post Type
    $labels = array(
        'name' => __('The BRAG Letters'),
        'singular_name' => __('The BRAG Letter'),
        'menu_name' => __('The BRAG Letters'),
        //        'parent_item_colon' => __( 'Parent Community' ),
        'all_items' => __('All The BRAG Letters'),
        'view_item' => __('View Letter'),
        'add_new_item' => __('Add New Letter'),
        'add_new' => __('Add New'),
        'edit_item' => __('Edit Letter'),
        'update_item' => __('Update Letter'),
        'search_items' => __('Search Letter'),
        'not_found' => __('Not Found'),
        'not_found_in_trash' => __('Not found in Trash'),
    );

    // Set other options for Custom Post Type
    $args = array(
        'label' => __('thebrag-letters'),
        'description' => __('The BRAG Letters'),
        'labels' => $labels,
        // Features this CPT supports in Post Editor
        'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions',),
        //        'taxonomies'          => array( 'genre' ),
        /* A hierarchical CPT is like Pages and can have
         * Parent and child items. A non-hierarchical CPT is like Posts.
         * */
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
        //        'rewrite' => array( 'slug' => 'gigs' ),
    );

    // Registering your Custom Post Type
    register_post_type('thebrag-letters', $args);
}
add_action('init', 'create_posttype_tbLetters');
/* Custom Post Type - Letter to Editor - End */

/*
 * Add Custom Meta Description for Gig Post Type
 */
function custom_add_meta_description_tag()
{
    if (is_single() && 'gig' == get_post_type()) {
        global $wpdb;
        $description = '';
        $query_datetime_furutre = "SELECT * FROM {$wpdb->prefix}gig_details
            WHERE
                post_id = '" . get_the_ID() . "'
                AND
                DATE(gig_datetime) >= '" . date('Y-m-d') . "'
            LIMIT 1";
        $datetimes = $wpdb->get_results($query_datetime_furutre);
        $dates_desc = '';
        if (count($datetimes) > 0) :
            $dates_desc .= ' | Date/Time: ';
            foreach ($datetimes as $datetime) :
                $dates_desc .= date('d M Y @ h:ia', strtotime($datetime->gig_datetime));
            endforeach;
        endif;

        $venues = new WP_Query(array(
            'connected_type' => 'gig_to_venue',
            'connected_items' => get_queried_object(),
            'nopaging' => true,
        ));
        $venues_desc = '';

        if ($venues->have_posts()) :
            while ($venues->have_posts()) :
                $venues->the_post();
                $venues_desc .= ' | Location: ' . get_the_title();
                if (get_field('street')) {
                    $venues_desc .= ' ' . get_field('street');
                    if (get_field('additional')) {
                        $venues_desc .= ' ' . get_field('additional');
                    }
                }
                if (get_field('suburb')) {
                    $venues_desc .= get_field('suburb') . ', ';
                }
                if (get_field('state')) {
                    $venues_desc .= get_field('state') . ' ';
                }
                if (get_field('postcode')) {
                    $venues_desc .= get_field('postcode');
                }
            endwhile;
            wp_reset_query();
        endif;

        $price_desc = '';
        /*
        if (get_field('price')):
            $price_desc .= ' | $' . get_field('price');
        endif;
        */

        $genre_desc = '';
        /*
        $genres = get_the_terms( get_the_ID(), 'gig-genre' );
        if ( count( $genres ) > 0 ) :
            $genre_desc = ' | Genre: ';
            $genre_names = array();
            if ( count( $genres ) > 0 ) :
                foreach ( $genres as $genre ) :
                    array_push( $genre_names, $genre->name );
                endforeach;
            endif;
            $genre_desc .= implode( ', ', $genre_names );
        endif;
        */

        $artist_desc = '';
        $artists = get_the_terms(get_the_ID(), 'gig-artist');
        if ($artists && count($artists) > 0 && $artists[0]) :
            $artist_desc = ' | Artists: ';
            $artist_names = array();
            if (count($artists) > 0) :
                foreach ($artists as $artist) :
                    array_push($artist_names, $artist->name);
                endforeach;
            endif;
            $artist_desc .= implode(', ', $artist_names);
        endif;

        $description .= get_the_title() . $dates_desc . $venues_desc . $price_desc . $genre_desc . $artist_desc;
    ?>
        <meta name="description" content="<?php echo $description; ?>" />
    <?php
        wp_reset_query();
    }
}
add_action('wp_head', 'custom_add_meta_description_tag', 1);

// Defer JS files
function defer_parsing_of_js($url)
{
    if (!is_admin()) { // } && function_exists('is_amp_endpoint') && !is_amp_endpoint()) {
        if (FALSE === strpos($url, '.js')) return $url;
        if (strpos($url, 'jquery.js') || strpos($url, 'jquery.min.js') || strpos($url, 'fuseplatform') || strpos($url, 'amp')) return $url;
        if (strpos($url, 'amp')) return $url;
        return "$url' defer ";
    }
    return $url;
}
add_action('init', function () {
    add_filter('clean_url', 'defer_parsing_of_js', 11, 1);
});

// Resize Featured Image for Issue Post Type
function save_issue_posttype($post_id, $post, $update)
{
    $post_type = get_post_type($post_id);
    if ("issue" != $post_type) return;
    $attachment_id = get_post_thumbnail_id($post_id);
    td_image_resize($attachment_id, 300, 420); //, true );
}
add_action('save_post', 'save_issue_posttype', 10, 3);

// AMP - START */
add_action('amp_post_template_css', 'ssm_amp_additional_css_styles');
function ssm_amp_additional_css_styles($amp_template)
{
    // only CSS here please...
    ?>
    html { background: #ffffff; }
    body { font-family: 'Sans-serif', 'Arial'; background: #ffffff; }
    a, a:visited, a:hover, a:active, a:focus { color: #2982b3; }
    amp-img{max-width: 100%;height:auto;}
    .amp-wp-header {
    padding: 0;
    background: #fff;
    position: absolute; top: 0; margin: auto; width: 100%; z-index: 999999;
    }
    .amp-wp-header div.amp-wp-header-inner {
    position: fixed;
    top: 0;
    background: #fff;
    margin: auto;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    padding: 7px;
    border-bottom: 1px solid #ccc;
    }
    .amp-wp-header a {
    background-image: url( '<?php echo CDN_URL; ?>The-Brag-300px.png' );
    background-repeat: no-repeat;
    background-size: contain;
    display: block;
    height: 30px;
    width: 170px;
    margin: 0 auto;
    text-indent: -9999px;
    background-position: center;
    }
    .amp-wp-title { color: #0f0c0c; }
    .amp-wp-article, .amp-wp-article-header { margin-top: 0; background: #fff; }
    .amp-wp-article { padding: 55px 0 10px 0; }
    #pagination { border-top: 1px solid #ccc; }
    #pagination .prev a, #pagination .next a {
    display: block;
    margin-bottom: 12px;
    background: #fefefe;
    text-decoration: none;
    font-size: 0.8rem;
    padding: 5px 15px;
    color: #666;
    }
    #pagination .prev a { text-align: left; }
    #pagination .next a { text-align: right; }
    .related-stories-wrap { background:#1fcabf; background: #fff; margin-top: 0px; padding: 10px 0; width: 100%; border-top: 1px solid #cecece; }
    .related-stories-wrap .title { margin:0 0 15px 0; padding:0 10px; text-transform:uppercase; font-size:20px; line-height:22px; }
    .related-story { min-height: 100px; clear: both; border-bottom: 1px solid #dedede; padding: 10px 0; }
    .related-story .post-thumbnail { float: left; overflow: hidden; padding: 0 10px; }
    .related-story .post-thumbnail amp-img { width: 100px; height: auto; }
    .related-story .post-content { padding: 0 10px; margin-left: 160px; }
    .related-story .post-content .excerpt { font-size: 0.8rem; line-height: 1.2rem; }
    .related-story h2 { font-size: 1.2rem; line-height: 1rem; margin: 0 0 5px 0; }
    .related-story a { text-decoration: none; font-size: 14px; }
    .share-buttons-bottom {
    position:fixed; text-align: center; bottom: 0; padding-top: 10px; width: 100%; background: #fff; z-index: 9999;
    }
    .hamburger {
    position: relative;
    padding: 9px 10px;
    background-color: transparent;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
    }
    .hamburger .icon-bar {
    display: block;
    width: 22px;
    height: 2px;
    border-radius: 1px;
    background-color: #888;
    }
    .hamburger .icon-bar+.icon-bar {
    margin-top: 4px;
    }
    amp-sidebar {
    width: 318px;
    background-color: #0f0c0c;
    }
    amp-sidebar .menu {
    margin: 0;
    background-color: #0f0c0c;
    box-shadow: 0 100vh 0 100vh #000;
    }
    amp-sidebar .menu li {
    padding: 0;
    border: none;
    border-bottom: 1px solid #303030;
    }
    amp-sidebar .menu li a {
    font-size: 1.25rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    height: auto;
    padding: .8125rem 1.25rem;
    color: #fff;
    text-decoration: none;
    line-height: 1;
    }
    .comp-footer a {
    display: block;
    background-color: #fff;
    border-radius: 1rem;
    padding: 1rem;
    text-align: center;
    text-decoration: none;
    color: #B98D5B;
    border: 1px solid #B98D5B;
    box-shadow: 0 0 5px;
    margin: 2rem auto;
    }
    amp-social-share.rounded {
    border-radius: 50%;
    background-size: 80%;
    }
<?php
}
add_filter('amp_content_max_width', 'ssm_amp_change_content_width');
function ssm_amp_change_content_width($content_max_width)
{
    return 940;
}

add_filter('amp_post_article_header_meta', 'ssm_amp_remove_time_meta');
function ssm_amp_remove_time_meta($meta_parts)
{
    foreach (array_keys($meta_parts, 'meta-time', true) as $key) {
        unset($meta_parts[$key]);
    }
    return $meta_parts;
}

add_filter('amp_post_template_metadata', 'ssm_amp_modify_json_metadata', 10, 2);
function ssm_amp_modify_json_metadata($metadata, $post)
{
    //    $metadata['@type'] = 'BlogPosting';
    if (!in_category('evergreen', $post)) :
        $metadata['@type'] = 'NewsArticle';
    endif;
    if (get_field('author')) {
        $metadata['author']['name'] = get_field('author');
    } else if (get_field('Author')) {
        $metadata['author']['name'] = get_field('Author');
    }
    $metadata['publisher']['logo'] = array(
        '@type' => 'ImageObject',
        'url' => get_template_directory_uri() . '/images/brag_logo_225x60.png',
        'height' => 60,
        'width' => 225,
    );
    if (!isset($metadata['image'])) {
        $metadata['image'] = array(
            '@type' => 'ImageObject',
            'url' => get_template_directory_uri() . '/images/brag_logo_300x80.png',
            'height' => '80',
            'width' => '300',
        );
    }
    return $metadata;
}

/* AMP - END */

// Remove srcset for image html
add_filter('wp_calculate_image_srcset', '__return_false');



// Assign Meta Desc for Gigs Page
function assignMetaDescGigPage($metadesc)
{
    if (isset($_SERVER) && strpos($_SERVER['REQUEST_URI'], '/gigs/') !== false) {
        $metadesc = "Want to know who's playing in Sydney? Find out about the latest Australian and Interntional artists gig announcements here on The Brag. ";
    }
    return $metadesc;
}
add_filter('wpseo_metadesc', 'assignMetaDescGigPage');

function addhttp($url)
{
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

/*
 * ShitShow - MicroSite
 */
/*
 * Custom Post Type - ShitShow - Start
 */
function create_posttype_shitshow()
{
    // Set UI labels for Custom Post Type
    $labels = array(
        'name' => __('Sh!tShow'),
        'singular_name' => __('Sh!tShow'),
        'menu_name' => __('Sh!tShow'),
        'all_items' => __('All Sh!tShow'),
        'view_item' => __('View Sh!tShow'),
        'add_new_item' => __('Add New Sh!tShow'),
        'add_new' => __('Add New'),
        'edit_item' => __('Edit Sh!tShow'),
        'update_item' => __('Update Sh!tShow'),
        'search_items' => __('Search Sh!tShow'),
        'not_found' => __('Not Found'),
        'not_found_in_trash' => __('Not found in Trash'),
    );

    // Set other options for Custom Post Type
    $args = array(
        'label' => __('shitshow'),
        'description' => __('Sh!tShows'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions',),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => array('page', 'shitshow'),
        'capabilities' => array(
            'publish_posts' => 'shitshow',
            'edit_posts' => 'shitshow',
            'edit_others_posts' => 'shitshow',
            'read_private_posts' => 'shitshow',
            'edit_post' => 'shitshow',
            'delete_post' => 'shitshow',
            'read_post' => 'shitshow',
            'publish_post' => 'shitshow',
        ),
    );
    register_post_type('shitshow', $args);
}
add_action('init', 'create_posttype_shitshow');

register_taxonomy(
    'shitshow-episode',
    array('shitshow'),
    array(
        'hierarchical' => true,
        'label' => 'ShitShow Episodes',
        'query_var' => true,
        'rewrite' => array('slug' => 'shitshow-episode'),
        'capabilities' => array(
            'manage_terms' => 'shitshow',
            'edit_terms' => 'shitshow',
            'delete_terms' => 'shitshow',
            'assign_terms' => 'shitshow',
            'assign_terms' => 'shitshow',
            'assign_terms' => 'shitshow'
        ),
        'show_ui' => true,
        'public' => true
    )
);

add_role('shitshow', 'Shitshow Contributor', array(
    'read' => true,
    'edit_posts' => true,
    'delete_posts' => true,
));

//if( get_role('shitshow') ){
//      remove_role( 'shitshow' );
//}

function modify_shitshow_capability()
{
    $roles = array(
        get_role('shitshow'),
        get_role('administrator'),
    );

    foreach ($roles as $role) {
        $role->add_cap('shitshow');
        $role->add_cap('upload_files');
    }
    $role = get_role('shitshow');
    $role->add_cap('edit_posts', false);
    $role->add_cap('read_posts', false);
}
add_action('admin_init', 'modify_shitshow_capability');

/*
 * User Role for Snaps
 */
add_role('snaps', 'Snaps Contributor', array(
    'read' => true,
    'edit_posts' => true,
    'delete_posts' => true,
));
function modify_snaps_capability()
{
    $roles = array(
        get_role('snaps'),
        get_role('administrator'),
        get_role('editor'),
    );

    foreach ($roles as $role) {
        $role->add_cap('snaps');
        $role->add_cap('upload_files');
    }
    $role = get_role('snaps');
    $role->add_cap('edit_posts', false);
    $role->add_cap('read_posts', false);
}
add_action('admin_init', 'modify_snaps_capability');

/*
 * Rewrite for Venues
 */
function tbm_custom_rewrite_rules()
{
    add_rewrite_rule('^venue/l/([^/]+)/page/([0-9]{1,})/?', 'index.php?post_type=venue&l=$matches[1]&paged=$matches[2]', 'top');
    add_rewrite_rule('^venue/l/([^/]+)?', 'index.php?post_type=venue&l=$matches[1]', 'top');

    // add_rewrite_rule('^((?!(sub-category))\w*)/features/page/([0-9]{1,})/?','index.php?category_name=$matches[1]&sub-category=features&paged=$matches[3]','top');
    // add_rewrite_rule('^((?!(sub-category))\w*)/features/?', 'index.php?category_name=$matches[1]&sub-category=features', 'top' );
    //
    // add_rewrite_rule('^((?!(sub-category))\w*)/news/page/([0-9]{1,})/?','index.php?category_name=$matches[1]&sub-category=news&paged=$matches[3]','top');
    // add_rewrite_rule('^((?!(sub-category))\w*)/news/?', 'index.php?category_name=$matches[1]&sub-category=news', 'top' );

    // add_rewrite_rule('^((?!(sub-category))\w*)/competitions/page/([0-9]{1,})/?','index.php?category_name=$matches[1]&sub-category=competitions&paged=$matches[3]','top');
    // add_rewrite_rule('^((?!(sub-category))\w*)/competitions/?', 'index.php?category_name=$matches[1]&sub-category=competitions', 'top' );

    add_rewrite_rule('^((?!(sub-category))\w*)/op-ed-comment/page/([0-9]{1,})/?', 'index.php?category_name=$matches[1]&sub-category=op-ed-comment&paged=$matches[3]', 'top');
    add_rewrite_rule('^((?!(sub-category))\w*)/op-ed-comment/?', 'index.php?category_name=$matches[1]&sub-category=op-ed-comment', 'top');

    add_rewrite_rule('^((?!(sub-category))\w*)/review/page/([0-9]{1,})/?', 'index.php?category_name=$matches[1]&sub-category=review&paged=$matches[3]', 'top');
    add_rewrite_rule('^((?!(sub-category))\w*)/review/?', 'index.php?category_name=$matches[1]&sub-category=review', 'top');

    add_rewrite_rule('^((?!(sub-category))\w*)/video/page/([0-9]{1,})/?', 'index.php?category_name=$matches[1]&sub-category=video&paged=$matches[3]', 'top');
    add_rewrite_rule('^((?!(sub-category))\w*)/video/?', 'index.php?category_name=$matches[1]&sub-category=video', 'top');
}
add_action('init', 'tbm_custom_rewrite_rules', 10, 0);
function custom_rewrite_tag2()
{
    add_rewrite_tag('%l%', '([^&]+)');
}
add_action('init', 'custom_rewrite_tag2', 10, 0);

function template_chooser($template)
{
    global $wp_query;
    $post_type = $wp_query->query_vars["post_type"];
    if (isset($_GET['s']) && $post_type == 'shitshow') {
        return locate_template('search-shitshow.php');
    }
    return $template;
}
add_filter('template_include', 'template_chooser');

function ssm_insert_after_paragraph($insertion, $paragraph_id, $content)
{
    $closing_p = '</p>';
    $paragraphs = explode($closing_p, $content);
    foreach ($paragraphs as $index => $paragraph) {
        if (trim($paragraph)) {
            $paragraphs[$index] .= $closing_p;
        }
        if ($paragraph_id == $index + 1) {
            $paragraphs[$index] .= $insertion;
        }
    }
    return implode('', $paragraphs);
}

function show_related_posts($post_id, $number_of_posts = 3, $main_post)
{

    if ('800215' == $post_id || get_field('paid_content', $post_id)) :
        return;
    endif;

    if (post_password_required($post)) :
        return;
    endif;
?>
    <h2 class="title-related-articles">Related Articles</h2>
    <div class="related-stories-wrap">

        <?php
        $tags = wp_get_post_tags($post_id);
        $arg_tags = array();
        foreach ($tags as $tag) {
            array_push($arg_tags, $tag->term_id);
        }
        $args = array(
            'post_status' => 'publish',
            'tag__in' => $arg_tags,
            'post__not_in' => array($post_id),
            'posts_per_page' => $number_of_posts,
            'orderby' => 'rand',
            'date_query' => array(
                'column' => 'post_date',
                'after' => '-60 days'
            )
        );
        $related_posts_query = new WP_Query($args);
        $require_more_posts = 3; //count( $related_posts_query->have_posts() ) < $number_of_posts ? $number_of_posts - count( $related_posts_query->have_posts() ) : 0;
        if (count($arg_tags) > 0 && $related_posts_query->have_posts()) :
            while ($related_posts_query->have_posts()) :
                $related_posts_query->the_post();
                echo '<!-- Related Posts -->';
                include('partials/related-posts.php');
                $require_more_posts--;
            endwhile;
            wp_reset_query();
        endif;

        if ($require_more_posts > 0) :
            show_random_posts($post_id, $require_more_posts);
        endif;
        ?>
        <div class="clear"></div>
    </div>
<?php
}

function show_random_posts($post_id, $number_of_posts)
{
    $cats = wp_get_post_categories($post_id);
    $args = array(
        'post_status' => 'publish',
        'post__not_in' => array($post_id),
        'posts_per_page' => $number_of_posts,
        'category__in' => $cats,
        'orderby' => 'rand',
        'date_query' => array(
            'column' => 'post_date',
            'after' => '-30 days'
        )
    );
    $random_posts_query = new WP_Query($args);
    if ($random_posts_query->have_posts()) :
        while ($random_posts_query->have_posts()) :
            $random_posts_query->the_post();
            echo '<!-- Random Posts -->';
            include('partials/related-posts.php');
        endwhile;
        wp_reset_query();
    endif;
}

//add rest api files
//require get_template_directory() . '/inc/tb-rest-api.php';
//add best pub module
//require get_template_directory() . '/best-pub/functions.php';

function ssm_social_sharing_buttons_func($style, $show_text = true)
{
    global $post;
    $post_url = (get_permalink());

    $post_title = str_replace(' ', '%20', get_the_title());

    $twitterURL = 'https://twitter.com/intent/tweet?text=' . $post_title . '&amp;url=' . urlencode($post_url . '?utm_source=Twitter&amp;utm_content=Twitter_share_btn');
    $facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($post_url . '?utm_source=Facebook&utm_content=FB_share_btn');
    //    $googleURL = 'https://plus.google.com/share?url=' . $post_url . '&amp;text=' . $post_title . '&amp;hl=en_AU';
    $redditURL = 'https://reddit.com/submit?url=' . $post_url . '&amp;title=' . urlencode($post_title . '?utm_source=Reddit&utm_content=Reddit_share_btn');
    $whatsappURL = 'https://wa.me/?text=' . urlencode(get_the_title()) . ' ' . urlencode($post_url . '?utm_source=Whatsapp&utm_content=WA_share_btn');

    $content = '<div class="social-share-buttons-' . $style . ' nav">';
    $content .= '<a class="social-share-link social-share-facebook nav-link" id="social-share-facebook-' . $style . '" href="' . $facebookURL . '" target="_blank" data-type="share-fb"><span class="d-none d-lg-inline' . (!$show_text ? ' d-none' : '') . '">Share</span> <i class="fa fa-facebook"></i></a>';
    $content .= '<a class="social-share-link social-share-twitter nav-link" id="social-share-twitter-' . $style . '" href="' . $twitterURL . '" target="_blank" data-type="share-twitter"><span class="d-none d-lg-inline' . (!$show_text ? ' d-none' : '') . '">Tweet</span> <i class="fa fa-twitter"></i></a>';
    //    $content .= '<a class="social-share-link social-share-google nav-link" id="social-share-google-' . $style . '" href="' . $googleURL . '" target="_blank" data-type="share-google"><i class="fa fa-google-plus"></i></a>';
    $content .= '<a class="social-share-link social-share-reddit nav-link" id="social-share-reddit-' . $style . '" href="' . $redditURL . '" target="_blank" data-type="share-reddit"><i class="fa fa-reddit-alien"></i></a>';
    $content .= '<a class="social-share-link social-share-whatsapp nav-link" id="social-share-whatsapp-' . $style . '" href="' . $whatsappURL . '" target="_blank" data-type="share-whatsapp"><i class="fa fa-whatsapp"></i></a>';
    $content .= '</div>';
    echo $content;
};
add_action('ssm_social_sharing_buttons', 'ssm_social_sharing_buttons_func', 10, 2);

// Remove dashicons in frontend for unauthenticated users
add_action('wp_enqueue_scripts', 'bs_dequeue_dashicons');
function bs_dequeue_dashicons()
{
    if (!is_user_logged_in()) {
        wp_deregister_style('dashicons');
    }
}

//add_action('wp_print_styles', 'show_all_styles');
function show_all_styles()
{
    global $wp_styles;

    $wp_styles->all_deps($wp_styles->queue);

    $handles = $wp_styles->to_do;

    $css_code = '';

    $merged_file_location = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'style-combined.css';

    foreach ($handles as $handle) {
        $src = strtok($wp_styles->registered[$handle]->src, '?');

        if (strpos($src, 'http') !== false) {
            $site_url = site_url();

            if (strpos($src, $site_url) !== false)
                $css_file_path = str_replace($site_url, '', $src);
            else
                $css_file_path = $src;

            $css_file_path = ltrim($css_file_path, '/');
        } else {
            $css_file_path = ltrim($src, '/');
        }
        if (file_exists($css_file_path)) {
            $css_code .=  "/*** " . $handle . " ***/ \n" . file_get_contents($css_file_path) . "\n\n";
        }
    }

    file_put_contents($merged_file_location, $css_code);

    wp_enqueue_style('site-style',  get_stylesheet_directory_uri() . '/css/style-combined.css');

    foreach ($handles as $handle) {
        wp_deregister_style($handle);
    }
}

function wp_html_compression_finish($html)
{
    include 'WP_HTML_Compression.php';

    $html = str_replace(
        array(
            "type='text/javascript'",
            'type="text/css"',
            "defer '>",
        ),
        array(
            "",
            "",
            "defer>",
        ),
        $html
    );

    return new WP_HTML_Compression($html);
}

function wp_html_compression_start()
{
    ob_start('wp_html_compression_finish');
}
//add_action('get_header', 'wp_html_compression_start');

/**
 * Enable unfiltered_html capability for Editors.
 *
 * @param  array  $caps    The user's capabilities.
 * @param  string $cap     Capability name.
 * @param  int    $user_id The user ID.
 * @return array  $caps    The user's capabilities, with 'unfiltered_html' potentially added.
 */
function km_add_unfiltered_html_capability_to_editors($caps, $cap, $user_id)
{
    if ('unfiltered_html' === $cap && user_can($user_id, 'editor')) {
        $caps = array('unfiltered_html');
    }
    return $caps;
}
add_filter('map_meta_cap', 'km_add_unfiltered_html_capability_to_editors', 1, 3);

/*
 * Restrict Image Upload Size
 */
add_filter('wp_handle_upload_prefilter', 'ssm_limit_image_size');
function ssm_limit_image_size($file)
{
    if (!isset($file['type'])) {
        return $file;
    }
    $errors = array();
    if (strpos($file['type'], 'image') !== false) {
        $filename = $file['name'];
        if (
            strpos(str_replace(array('-', '_', ' '), '', strtolower($filename)), 'screenshot') !== false
            ||
            strpos(str_replace(array('-', '_', ' '), '', strtolower($filename)), 'untitled') !== false
        ) {
            array_push($errors, '(+) Please rename the file before uploading.');
        }

        // Calculate the image size in KB
        $file_size = $file['size'] / 1024;

        $image = getimagesize($file['tmp_name']);
        $maximum = array(
            'width' => '2000',
            'height' => '2000'
        );
        $image_width = $image[0];
        $image_height = $image[1];

        if ($image_width > $maximum['width'] || $image_height > $maximum['height']) {
            array_push($errors, '(+) Image dimensions are too large. Maximum size is ' . $maximum['width'] . ' x ' . $maximum['height'] . ' pixels. Uploaded image is ' . $image_width . ' x ' . $image_height . ' pixels.');
        }

        // File size limit in KB
        $limit = 500;

        if (($file_size > $limit))
            array_push($errors, '(+) Uploaded file is too large. It has to be smaller than ' . $limit . 'KB');
    }
    if (!empty($errors)) {
        $file['error'] = implode(" ", $errors);
    }
    return $file;
}

// Add JS to make Alt Text compulsory
add_action('admin_footer', function () {
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var checkForAlt = function(showNotice) {
                var showNotice = (typeof showNotice !== 'undefined') ? showNotice : false;
                var $altText = $('.media-modal-content label[data-setting="alt"] input');
                if (!$altText.length) {
                    $altText = $('.media-frame-content .media-embed .embed-media-settings .column-settings label.alt-text input');
                }
                var $parent = $('.media-frame-toolbar .media-toolbar-primary');
                //                if ( ! $altText.length ) { // No image selected in the first place; bail out
                //                    return;
                //                }
                if (!$altText.length || $altText.val().length) {
                    $parent.addClass('ssm-has-alt-text');
                    $altText.removeClass('ssm-alt-error');
                    return true;
                } else {
                    $parent.removeClass('ssm-has-alt-text');
                    if (showNotice) {
                        alert('Missing Alt Text!');
                        $altText.focus();
                    }
                    $altText.addClass('ssm-alt-error');
                    return false;
                }
            };
            // Bind to keyup
            $('body').on('keyup', '.media-modal-content label[data-setting="alt"] input', function() {
                checkForAlt();
            });
            // Bind to the 'Inesert into post' button
            $('body').on('mouseenter mouseleave click', '.media-frame-toolbar .media-toolbar-primary', function(e) {
                checkForAlt(e.type === "click");
            });
        });
    </script>
    <style type="text/css">
        .media-frame-toolbar .media-toolbar-primary {
            position: relative;
        }

        .media-frame-toolbar .media-toolbar-primary:after {
            display: block;
            background: transparent;
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        .media-frame-toolbar .media-toolbar-primary.ssm-has-alt-text:after {
            display: none;
        }

        .ssm-alt-error {
            border: 1px solid #ff0000 !important;
        }
    </style>
<?php });

//add_action( 'wp_head', 'ssm_inject_alexa_code' );
function ssm_inject_alexa_code()
{
    echo '<script type="text/javascript">
_atrk_opts = { atrk_acct:"O3NOq1WyR620WR", domain:"thebrag.com",dynamic: true};
(function() { var as = document.createElement(\'script\'); as.type = \'text/javascript\'; as.async = true; as.src = "https://certify-js.alexametrics.com/atrk.js"; var s = document.getElementsByTagName(\'script\')[0];s.parentNode.insertBefore(as, s); })();
</script>
<noscript><img src="https://certify.alexametrics.com/atrk.gif?account=O3NOq1WyR620WR" style="display:none" height="1" width="1" alt="" /></noscript>';
}

add_action('init', 'ssm_rss_flipboard');
function ssm_rss_flipboard()
{
    add_feed('flipboard', 'ssm_rss_flipboard_func');
}
function ssm_rss_flipboard_func()
{
    get_template_part('rss', 'flipboard');
}

add_action('init', 'ssm_rss_external');
function ssm_rss_external()
{
    add_feed('external', 'ssm_rss_external_func');
}
function ssm_rss_external_func()
{
    get_template_part('rss', 'external');
}

// REST JSON number of articles published in year|month for GA report
function ssm_number_of_posts_func($data)
{
    $date_e = explode('|', urldecode($data['month_year']));
    $date = $date_e[0] . '-' . $date_e[1] . '-01';
    $posts = new WP_Query(array(
        'date_query' => array(
            'after' => date_i18n('Y-m-01', strtotime($date)),
            'before' => date_i18n('Y-m-t', strtotime($date)),
        ),
        'post_type' => array('post', 'freeshit', 'issue', 'podcast', 'snaps'),
        'post_status' => 'publish',
        'posts_per_page' => -1
    ));
    return $posts->post_count;
}
add_action('rest_api_init', function () {
    register_rest_route('ssm_posts/v1', '/month-year/(?P<month_year>\d+(\%7C)\d+)', array(
        'methods' => 'GET',
        'callback' => 'ssm_number_of_posts_func',
        'permission_callback' => '__return_true',
    ));
});

// Open links in new Window (or Tab)
function ssm_autoblank($content)
{
    $content = preg_replace('/(<a.*?)[ ]?target="(.*?)"(.*?)/', '$1$3', $content);
    $content = preg_replace("/<a(.*?)>/", "<a$1 target=\"_blank\">", $content);
    return $content;
}
// add_filter('the_content', 'ssm_autoblank');

function tbm_add_rel_to_links($content)
{
    $content = preg_replace_callback(
        '/<a[^>]*href=["|\']([^"|\']*)["|\'][^>]*>([^<]*)<\/a>/i',
        function ($m) {
            if ((strpos(strtolower($m[1]), $_SERVER['HTTP_HOST']) !== false) || (substr($m[1], 0, 1) == "#")) {
                // return $m[0];
                return '<a href="' . $m[1] . '" target="_blank">' . $m[2] . '</a>';
            } else {
                return '<a href="' . $m[1] . '" rel="noreferrer" target="_blank">' . $m[2] . '</a>';
            }
        },
        $content
    );

    return $content;
}

add_filter('the_content', 'tbm_add_rel_to_links', 100);


// Inject Unruly code
function ssm_inject_unruly($content)
{
    $paragraphAfter = 3;
    $unruly_ad_code = "
        <script src='https://www.googletagservices.com/tag/js/gpt.js'></script>
        <script> googletag.pubads().definePassback('/9876188/unruly/unruly_tb', [11, 11]).display(); </script>
    ";
    return ssm_insert_after_paragraph($unruly_ad_code, $paragraphAfter, $content);
}
//add_filter('the_content', 'ssm_inject_unruly');

// Inject Teads code
function ssm_inject_teads($content)
{
    if ((function_exists('get_field') && get_field('paid_content')) || is_page_template('single-template-featured.php')) :
        return $content;
    endif;

    if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
        return $content;
    }

    if (is_singular('page'))
        return $content;

    $paragraphAfter = 1;
    $tag = render_ad_tag('teads');
    // return $tag . $content;
    return ssm_insert_after_paragraph($tag, $paragraphAfter, $content);
}
// add_filter('the_content', 'ssm_inject_teads');

// Inject Minute Media ad tag
function ssm_inject_minutemedia($content)
{
    if (get_field('paid_content')) :
        return $content;
    endif;

    $count_articles = isset($_POST['count_articles']) ? (int) $_POST['count_articles'] : 1;

    if ($count_articles > 1) :
        return $content;
    endif;

    $paragraphAfter = 0;
    ob_start();
?>
    <div id='brag_minute_media'>
        <script>
            googletag.cmd.push(function() {
                googletag.display('brag_minute_media');
            });
        </script>
    </div>
<?php
    $tag = ob_get_clean();
    $content = $tag . $content;
    return $content;
    // return ssm_insert_after_paragraph( $tag, $paragraphAfter, $content );
}
// add_filter('the_content', 'ssm_inject_minutemedia');

// Custom single template by category
add_filter('single_template', 'check_for_category_single_template');
function check_for_category_single_template($t)
{
    foreach ((array) get_the_category() as $cat) {
        if (file_exists(TEMPLATEPATH . "/single-category-{$cat->slug}.php")) return TEMPLATEPATH . "/single-category-{$cat->slug}.php";
        if ($cat->parent) {
            $cat = get_the_category_by_ID($cat->parent);
            if (isset($cat->slug) && file_exists(TEMPLATEPATH . "/single-category-{$cat->slug}.php")) return TEMPLATEPATH . "/single-category-{$cat->slug}.php";
        }
    }
    return $t;
}

function get_fuse_tag($tag, $page = '')
{
    return render_ad_tag($tag, $page);
}

// Optional parameter to limit number of items in RSS2 feed
function feed_limit_ppp($query)
{
    if ($query->is_feed('rss2') && isset($_GET['size']) && '' != (int) $_GET['size']) {
        add_filter('option_posts_per_rss', function () {
            return (int) $_GET['size'];
        });
    }
}
add_action('pre_get_posts', 'feed_limit_ppp');

// Taxonomy - Feature - For Cover Story, etc.
/*
register_taxonomy(
        'feature',
        array(
            'post',
            'gig',
        ),
        array(
            'hierarchical' => true,
            'labels' =>
                array(
                    'name' => 'Features',
                    'singular_name' => 'Feature'
                ),
            'query_var' => true,
            'rewrite' => array( 'slug' => 'feature' ),
            'capabilities' => array(
                'manage_terms' => 'manage_categories',
                'edit_terms' => 'manage_categories',
                'delete_terms' => 'manage_categories',
                'assign_terms' => 'edit_posts',
            ),
            'show_ui' => true,
            'public' => true
        )
    );
*/

add_filter('the_time', 'dynamictime');
function dynamictime()
{
    global $post;
    $date = $post->post_date;
    $time = get_post_time('G', true, $post);
    $mytime = time() - $time;
    if ($mytime > 0 && $mytime < 7 * 24 * 60 * 60)
        $mytimestamp = sprintf(__('%s ago'), human_time_diff($time));
    else
        $mytimestamp = date(get_option('date_format'), strtotime($date));
    return $mytimestamp;
}

add_post_type_support('page', 'excerpt');

add_action('init', 'ssm_chat_bot_rss');
function ssm_chat_bot_rss()
{
    add_feed('chat_bot', 'ssm_chat_bot_articles');
    add_feed('chat_bot2', 'ssm_chat_bot_articles');
    add_feed('chat_bot3', 'ssm_chat_bot_articles');
}
function ssm_chat_bot_articles()
{
    get_template_part('rss', 'chat_bot');
    get_template_part('rss', 'chat_bot2');
    get_template_part('rss', 'chat_bot3');
}

/*
 * Star Wars - Quiz
 */
add_action('wp_ajax_ssm_save_quiz_starwars_result', 'ssm_save_quiz_starwars_result');
add_action('wp_ajax_nopriv_ssm_save_quiz_starwars_result', 'ssm_save_quiz_starwars_result');
function ssm_save_quiz_starwars_result()
{
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'starwars_quiz_results',
        array(
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'result' => $_POST['result'],
            'created_at' => current_time('mysql', 0)
        )
    );
    $id = $wpdb->insert_id;
    $url_suffix = md5($id . 'ssm-quiz-starw-live');
    $wpdb->update(
        $wpdb->prefix . 'starwars_quiz_results',
        array(
            'url_suffix' => $url_suffix
        ),
        array(
            'id' => $id
        )
    );
    $page_url = $_POST['page_url'];
    $page_title = $_POST['page_title'];

    $page_url .= '?r=' . $url_suffix;
    $page_title = 'In the Star Wars: Identities quiz, I achieved the rank of ' . $_POST['result'];

    $page_url = urlencode($page_url);
    $page_title = str_replace(' ', '%20', $page_title);

    $facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . $page_url;
    $twitterURL = 'https://twitter.com/intent/tweet?text=' . $page_title . '&amp;url=' . $page_url;
    $data = array(
        'fb_share_url' => $facebookURL,
        'twitter_share_url' => $twitterURL,
    );
    wp_send_json_success($data);
}

add_filter('wpseo_opengraph_url', 'change_opengraph_url');
function change_opengraph_url($url)
{
    if (is_page_template('page-quiz-starwars.php') && isset($_GET['r'])) :
        return get_permalink() . '?r=' . $_GET['r'];
    endif;
    return $url;
}

add_filter('wpseo_opengraph_title', 'change_opengraph_title');
function change_opengraph_title($title)
{
    if (is_page_template('page-quiz-starwars.php') && isset($_GET['r'])) :
        global $wpdb;
        $result = $wpdb->get_var(
            $wpdb->prepare("SELECT result FROM {$wpdb->prefix}starwars_quiz_results WHERE url_suffix = %s", $_GET['r'])
        );
        return 'In the Star Wars: Identities quiz, I achieved the rank of ' .  $result;
    endif;
    return $title;
}

add_filter('wpseo_opengraph_image', 'change_opengraph_image_url');
function change_opengraph_image_url($url)
{
    if (is_page_template('page-quiz-starwars.php') && isset($_GET['r'])) :
        global $wpdb;
        $image = get_template_directory_uri() . '/images/quiz-starwars/share-';
        $result = $wpdb->get_var(
            $wpdb->prepare("SELECT result FROM {$wpdb->prefix}starwars_quiz_results WHERE url_suffix = %s", $_GET['r'])
        );
        switch ($result):
            case 'Youngling':
                $image .= 'Result1_youngling.jpg';
                break;
            case 'Padawan':
                $image .= 'Result2_padwan.jpg';
                break;
            case 'Jedi Knight':
                $image .= 'Result3_JediKnight.jpg';
                break;
            case 'Jedi Master':
                $image .= 'Result4_JediMaster.jpg';
                break;
            case 'Jedi Council':
                $image .= 'Result5_JediCouncil.jpg';
                break;
            case 'Grand Master':
                $image .= 'Result6_GrandJedi.jpg';
                break;
        endswitch;
        return $image;
    endif;
    return $url;
}
/*
 * Star Wars - Quiz End
 */

/*
 * Block Bad Referrers
 */
add_action('init', 'ssm_referrer_check');
function ssm_referrer_check()
{
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
    if (
        strpos($referer, 'redirect.') != false || strpos($referer, 'filter.') != false ||
        strpos($referer, 'catchthesun') != false || strpos($referer, 'myfitnesspal') != false
    ) {
        header('Status: 403 Forbidden', true, 403);
        die();
        exit;
    }
}

/*
 * Add custom dimension 'Author' to AMP
 */
add_filter('amp_post_template_analytics', 'ssm_amp_add_custom_analytics');
function ssm_amp_add_custom_analytics($analytics)
{
    global $amp_post_id;
    $post = get_post($amp_post_id);
    if (!is_array($analytics)) {
        $analytics = array();
    }

    if (get_field('author')) {
        $author = get_field('author');
    } else if (get_field('Author')) {
        $author = get_field('Author');
    } else {
        if ('' != get_the_author_meta('first_name', $post->post_author) && '' != get_the_author_meta('last_name', $post->post_author)) {
            $author = get_the_author_meta('first_name', $post->post_author) . ' ' . get_the_author_meta('last_name', $post->post_author);
        } else {
            $author = get_the_author_meta('display_name', $post->post_author);
        }
    }

    $categories = get_the_category(get_the_ID());
    $CategoryCD = '';
    if ($categories) :
        foreach ($categories as $category) :
            $CategoryCD .= $category->slug . ' ';
        endforeach; // For Each Category
    endif; // If there are categories for the post

    $analytics['ssm-googleanalytics'] = array(
        'type' => 'googleanalytics',
        'attributes' => array(
            // 'data-credentials' => 'include',
        ),
        'config_data' => array(
            'vars' => array(
                'account' => "UA-101631840-1"
            ),
            'triggers' => array(
                'trackPageview' => array(
                    'on' => 'visible',
                    'request' => 'pageview',
                    'extraUrlParams' => array(
                        'cd3' => str_replace('&', 'and', $author),
                        'cd4' => $CategoryCD,
                    )
                ),
            ),
        ),
    );

    $analytics['tb-googleanalytics'] = array(
        'type' => 'googleanalytics',
        'attributes' => array(
            // 'data-credentials' => 'include',
        ),
        'config_data' => array(
            'vars' => array(
                'account' => "UA-16753498-1"
            ),
            'triggers' => array(
                'trackPageview' => array(
                    'on' => 'visible',
                    'request' => 'pageview',
                    'extraUrlParams' => array(
                        'cd3' => str_replace('&', 'and', $author),
                        'cd4' => $CategoryCD,
                    )
                ),
            ),
        ),
    );

    $section = "The Brag";
    if (is_post_type_archive('dad') || (is_single() && 'dad' == get_post_type())) :
        $section = "The Brag Dad";
    elseif (is_post_type_archive('issue') || (is_single() && 'issue' == get_post_type())) :
        $section = "The Brag Magazine";
    elseif (is_category('gaming') || (is_single() && in_category('gaming'))) :
        $section = "The Brag Gaming";
    endif;

    $analytics['nielsen'] = array(
        'type' => 'nielsen',
        'attributes' => array(
            // 'data-credentials' => 'include',
        ),
        'config_data' => array(
            'vars' => array(
                "apid" => "DD902D41-39DF-457F-985D-9B4E4CDF3726",
                "apv" => "1.0",
                "apn" => "The Brag Network",
                "section" => $section,
                "segA" => "",
                "segB" => "",
                "segC" => "The Brag Network - Google AMP"
            ),
        ),
    );

    return $analytics;
}

/*
 * Inject FB Pixel
 */
add_action('wp_head', 'ssm_inject_fb_pixel');
function ssm_inject_fb_pixel()
{
?>
    <!-- Facebook Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '243859349395737');
        fbq('track', 'PageView');
        fbq.disablePushState = true;
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=243859349395737&ev=PageView&noscript=1" /></noscript>
    <!-- End Facebook Pixel Code -->
<?php
}

/*
 * YouTube Lazy Load
 */
function ssm_youtube_lazy_load($content)
{
    $pattern = '/<figure class=\"op-interactive\"><iframe(.*?)width=\"(.*)\"(.*?)height=\"(.*)\"(.*?)src=\"https:\/\/www.youtube.com\/embed\/(.*)\?(.*?)\" (.*)><\/iframe><\/figure>/';
    $replacement = '<div class="yt-lazy-load my-2" data-id="$6" id="yt-$6"><img src="https://i.ytimg.com/vi/$6/hqdefault.jpg" width="$2" height="$4" class="yt-img" loading="lazy" alt="YouTube Video"><img class="p-a-center play-button" src="' . ICONS_URL . 'controller-play.svg" alt="Play" title="Play" loading="lazy" width="100" height="100"></div>';
    $lazy_content = preg_replace($pattern, $replacement, $content);
    return $lazy_content;
}
// add_filter('the_content', 'ssm_youtube_lazy_load');

/*
 * WSU - Quiz 2
 */
add_action('wp_ajax_ssm_save_wsu_quiz_2', 'ssm_save_wsu_quiz_2');
add_action('wp_ajax_nopriv_ssm_save_wsu_quiz_2', 'ssm_save_wsu_quiz_2');
function ssm_save_wsu_quiz_2()
{
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'wsu_quiz_2_results',
        array(
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'results' => $_POST['result'],
            'created_at' => current_time('mysql', 0)
        )
    );
    $id = $wpdb->insert_id;
    wp_send_json_success(array());
}
/*
 * WSU - Quiz 2 End
 */

/*
* Instagram Frame Embedder
*/
wp_embed_register_handler('instagram', '#https?://(www.)?instagr(\.am|am\.com)/p/([^/]+)#i', 'ssm_embed_handler_instagram');
function ssm_embed_handler_instagram($matches, $attr, $url, $rawattr)
{
    if (!empty($rawattr['width']) && !empty($rawattr['height'])) {
        $width  = (int) $rawattr['width'];
        $height = (int) $rawattr['height'];
    } else {
        list($width, $height) = wp_expand_dimensions(575, 1200, $attr['width'], $attr['height']);
    }
    return apply_filters('embed_instagram', "<iframe src='https://instagram.com/p/" . esc_attr($matches[3]) . "/embed/captioned' width='{$width}' height='{$height}' frameborder='0' scrolling='no' allowtransparency='true' style='border: 1px solid rgb(219, 219, 219); border-radius: 3px;'></iframe>");
}

// RSS for Promoter
add_action('init', 'promoterFeedRSS');
function promoterFeedRSS()
{
    add_feed('promoter', 'promoterFeed');
}
function promoterFeed()
{
    get_template_part('rss', 'promoter_feed');
}

function add_additional_class_on_li($classes, $item, $args)
{
    if ($args->add_li_class) {
        $classes[] = $args->add_li_class;
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'add_additional_class_on_li', 1, 3);

function add_menu_link_class($atts, $item, $args)
{
    if (property_exists($args, 'link_class')) {
        $atts['class'] = $args->link_class;
    }
    return $atts;
}
add_filter('nav_menu_link_attributes', 'add_menu_link_class', 1, 3);

/*
 * User Role for Brag Dad
 */
add_role('dad', 'Dad Contributor', array(
    'read' => false,
    'edit_posts' => false,
    'delete_posts' => false,
));
function modify_dad_capability()
{
    $roles = array(
        get_role('dad'),
        get_role('administrator'),
        get_role('editor'),
    );
    foreach ($roles as $role) {
        $role->add_cap('dad');
        $role->add_cap('upload_files');
    }
    //    $role = get_role('dad');
}
add_action('admin_init', 'modify_dad_capability');

/*
 * Custom Post Type - Dad
 */
/* add_action('init', 'register_cpt_dad');

function register_cpt_dad()
{
    $labels = array(
        'name' => _x('Dad', 'dad'),
        'singular_name' => _x('Dad', 'dad'),
        'add_new' => _x('Add New', 'dad'),
        'add_new_item' => _x('Add New Dad Article', 'dad'),
        'edit_item' => _x('Edit Dad Article', 'dad'),
        'new_item' => _x('New Dad Article', 'dad'),
        'view_item' => _x('View Dad Article', 'dad'),
        'search_items' => _x('Search Dad Articles', 'dad'),
        'not_found' => _x('No dad articles found', 'dad'),
        'not_found_in_trash' => _x('No dad articles found in Trash', 'dad'),
        'parent_item_colon' => _x('Parent Dad Articles:', 'dad'),
        'menu_name' => _x('Dad Articles', 'dad'),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Brag Dad Articles',
        'supports' => array('title', 'editor', 'thumbnail', 'author'),
        'taxonomies' => array('dad-category', 'post_tag', 'category'),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array('slug' => 'dad',),
        'capability_type'     => array('page', 'dad'),
        'capabilities' => array(
            'publish_posts' => 'dad',
            'edit_posts' => 'dad',
            'edit_others_posts' => 'dad',
            'read_private_posts' => 'dad',
            'edit_post' => 'dad',
            'delete_post' => 'dad',
            'read_post' => 'dad',
            'publish_post' => 'dad',
        ),
    );

    register_post_type('dad', $args);
}

register_taxonomy(
    'dad-category',
    array('dad'),
    array(
        'hierarchical' => true,
        'label' => 'Dad Categories',
        'query_var' => true,
        'rewrite' => array('slug' => 'dad/category'),
        'capabilities' => array(
            'manage_terms' => 'dad',
            'edit_terms' => 'manage_categories',
            'delete_terms' => 'manage_categories',
            'assign_terms' => 'dad',
        ),
        'show_ui' => true,
        'public' => true
    )
); */

/*
* Modify Header for Dad listing
*/
add_filter('manage_edit-dad_columns', 'dad_table_head');
function dad_table_head($defaults)
{
    $defaults['dad-category'] = 'Categories';
    return $defaults;
}

/*
* Modify column values for Dad listing
*/
add_action('manage_dad_posts_custom_column', 'dad_table_columns', 10, 2);
function dad_table_columns($column_id, $post_id)
{
    global $post;
    if ('dad-category' == $column_id) :
        $terms = get_the_terms($post_id, 'dad-category');
        if (!empty($terms)) {
            $out = array();
            /* Loop through each term, linking to the 'edit posts' page for the specific term. */
            foreach ($terms as $term) {
                $out[] = sprintf(
                    '<a href="%s">%s</a>',
                    esc_url(add_query_arg(array('post_type' => $post->post_type, 'dad-category' => $term->slug), 'edit.php')),
                    esc_html(sanitize_term_field('name', $term->name, $term->term_id, 'dad-category', 'display'))
                );
            }
            /* Join the terms, separating them with a comma. */
            echo join(', ', $out);
        }
    endif;
}

/*
 * Add custom post type e.g. Dad to queries
 */
function tbm_custom_posts_archive($query)
{
    if ($query->is_author)
        $query->set('post_type', array('post', 'dad'));
    remove_action('pre_get_posts', 'tbm_custom_posts_archive');
}
add_action('pre_get_posts', 'tbm_custom_posts_archive');

/*
 * Country
 */
add_action('init', 'register_cpt_country');

function register_cpt_country()
{
    $labels = array(
        'name' => _x('Country', 'country'),
        'singular_name' => _x('Country', 'country'),
        'add_new' => _x('Add New', 'country'),
        'add_new_item' => _x('Add New Country Article', 'country'),
        'edit_item' => _x('Edit Country Article', 'country'),
        'new_item' => _x('New Country Article', 'country'),
        'view_item' => _x('View Country Article', 'country'),
        'search_items' => _x('Search Country Articles', 'country'),
        'not_found' => _x('No country articles found', 'country'),
        'not_found_in_trash' => _x('No country articles found in Trash', 'country'),
        'parent_item_colon' => _x('Parent Country Articles:', 'country'),
        'menu_name' => _x('Country Articles', 'country'),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Country Articles',
        'supports' => array('title', 'editor', 'thumbnail', 'author'),
        'taxonomies' => array('category', 'post_tag'),
        'public' => true,
        'show_ui' => true,
        'show_in_menu'        => false,
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'menu_position' => 5,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array('slug' => 'country',),
        'capability_type'     => array('page', 'country'),
        'capabilities' => array(
            'publish_posts' => 'country',
            'edit_posts' => 'country',
            'edit_others_posts' => 'country',
            'read_private_posts' => 'country',
            'edit_post' => 'country',
            'delete_post' => 'country',
            'read_post' => 'country',
            'publish_post' => 'country',
        ),
    );

    register_post_type('country', $args);
}
/*
 * User Role for Country
 */
add_role('country', 'Country Contributor', array(
    'read' => true,
    'edit_posts' => true,
    'delete_posts' => true,
));
function modify_country_capability()
{
    $roles = array(
        get_role('country'),
        get_role('administrator'),
        get_role('editor'),
    );

    foreach ($roles as $role) {
        $role->add_cap('country');
        $role->add_cap('upload_files');
    }
    //    $role = get_role('country');
    //    $role->add_cap('edit_posts', false);
    //    $role->add_cap('read_posts', false);
}
add_action('admin_init', 'modify_country_capability');

//if( get_role('country') ) {
//    remove_role( 'country' );
//}

function ssm_inject_ads($content)
{
    if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
        return $content;
    }
    // return tbm_inject_ads( $content );
    if ((function_exists('get_field') && get_field('paid_content')) || is_page_template('single-template-featured.php') || is_page_template('page-templates/brag-observer.php')) :
        return $content;
    endif;

    if (is_page()) {
        return $content;
    }

    $count_articles = isset($_POST['count_articles']) ? (int) $_POST['count_articles'] : 1;

    $closing_p = '</p>';
    $after_para = 2;

    ob_start();
    render_ad_tag('incontent_1', $count_articles);
    $content_ad_tag = ob_get_contents();
    ob_end_clean();
    $content = ssm_insert_after_paragraph('<div class="my-2 text-center ad-mrec" id="ad-incontent-' . $count_articles . '">' . $content_ad_tag . '</div>', $after_para, $content);

    return $content;
}
add_filter('the_content', 'ssm_inject_ads');

function convert_seconds_to_redable($seconds)
{
    $t = round($seconds);
    return sprintf('%02d:%02d:%02d', ($t / 3600), ($t / 60 % 60), $t % 60);
}

/*
 * Nielsen
 */
add_action('wp_footer', 'inject_nielsen', 99, 2);
function inject_nielsen()
{
    $assetId = $_SERVER['REQUEST_URI'];

    $section = "The Brag";
    if (is_post_type_archive('dad') || (is_single() && 'dad' == get_post_type())) :
        $section = "The Brag Dad";
    elseif (is_post_type_archive('issue') || (is_single() && 'issue' == get_post_type())) :
        $section = "The Brag Magazine";
    elseif (is_category('gaming') || (is_single() && in_category('gaming'))) :
        $section = "The Brag Gaming";
    endif;

    $html = '<script type="text/JavaScript">  
!function(t,n){t[n]=t[n]||
{
nlsQ:function(e,o,c,r,s,i)
{
return s=t.document,
r=s.createElement("script"), 
r.async=1,
r.src=("http:"===t.location.protocol?"http:":"https:")+"//cdn-gl.imrworldwide.com/conf/"+e+".js#name="+o+"&ns="+n,
i=s.getElementsByTagName("script")[0],
i.parentNode.insertBefore(r,i),
t[n][o]=t[n][o]||{g:c||{},
ggPM:function(e,c,r,s,i){(t[n][o].q=t[n][o].q||[]).push([e,c,r,s,i])} },
t[n][o]
}
}
}
(window,"NOLBUNDLE");
var nSdkInstance = NOLBUNDLE.nlsQ("P59D1CA7E-CA1C-4718-8E85-F8807D018FED","nSdkInstance");
var dcrStaticMetadata = {type:"static",dataSrc:"cms",assetid:"' . $assetId . '", section:"' . $section . '",segA:"",segB:""}
nSdkInstance.ggPM("staticstart",dcrStaticMetadata);
</script>';
    echo $html;
}


/*
 * Change Default Email Address and From Name for the outgoing emails
 */
add_filter("wp_mail_content_type", "tbm_mail_content_type");
function tbm_mail_content_type()
{
    return "text/html";
}
add_filter('wp_mail_from', 'tbm_mail_from_address');
function tbm_mail_from_address($email)
{
    return 'noreply@thebrag.media';
}
add_filter('wp_mail_from_name', 'tbm_mail_from_name');
function tbm_mail_from_name($from_name)
{
    // if ( ! $from_name || '' == $from_name ) {
    return "The Brag";
    // }
    // return $from_name;
}
add_action('phpmailer_init', 'tbm_send_smtp_email');
function tbm_send_smtp_email($phpmailer)
{
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.gmail.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587;
    $phpmailer->Username   = 'noreply@thebrag.media';
    $phpmailer->Password   = '<%QA5hXy1';
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->From       = 'noreply@thebrag.media';
    // $phpmailer->FromName   = 'The Brag';

    $phpmailer->IsSMTP();
}

/*
 * Get Next Post AJAX
 */
function tbm_ajax_load_next_post()
{
    global $post;

     if ('single-template-featured.php' == get_page_template_slug($post->ID)) :
        wp_die();
    endif;

    $count_articles = isset($_POST['count_articles']) ? absint($_POST['count_articles']) : 1;

    if (get_field('paid_content', $_POST['id']) && 2 == $count_articles) :
        wp_die();
    endif;

    $exclude_posts = (!is_null($_POST['exclude_posts']) && $_POST['exclude_posts'] != '') ? $_POST['exclude_posts'] : '';
    $exclude_posts_array = explode(',', $exclude_posts);

    $tbm_featured_infinite_IDs = trim(get_option('tbm_featured_infinite_ID'));
    if ($tbm_featured_infinite_IDs) :
        $tbm_featured_infinite_IDs = array_map('trim', explode(',', $tbm_featured_infinite_IDs));
        $tbm_featured_infinite_IDs = array_map('absint', $tbm_featured_infinite_IDs);
        $tbm_featured_infinite_ID = $tbm_featured_infinite_IDs[array_rand($tbm_featured_infinite_IDs)];
    endif;

    if ($tbm_featured_infinite_ID && $_POST['id'] != $tbm_featured_infinite_ID && !in_array($tbm_featured_infinite_ID, $exclude_posts_array)) :
        $prevPost = get_post($tbm_featured_infinite_ID);
    else :
        $post = get_post($_POST['id']);
        $prevPost = get_previous_post();
    endif;

    if (
        in_array($prevPost->ID, $exclude_posts_array) ||
        // get_field( 'paid_content', $prevPost->ID ) ||
        strpos(strtolower($prevPost->post_title), 'quiz') !== false ||
        strpos(strtolower($prevPost->post_title), 'poll') !== false
    ) {
        $data['content'] = '';
        $data['loaded_post'] = $prevPost->ID;
        wp_send_json_success($data);
        wp_die();
    }
    if ($prevPost) :
        $post = $prevPost;
        $data['exclude_post'] = $prevPost->ID;
        ob_start();
        $main_post = false;

        // if ('single-template-featured.php' == get_page_template_slug($post->ID)) :
        // include(get_template_directory() . '/partials/single-featured.php');
        // else :
        if ('single-template-featured.php' == get_page_template_slug($post->ID)) {
            get_template_part('template-parts/single/single', 'featured', ['count_articles' => $count_articles]);
        } else {
            get_template_part('template-parts/single/single', 'post', ['count_articles' => $count_articles]);
        }
        // endif;
        wp_reset_query();
        wp_reset_postdata();
        $data['content'] = ob_get_clean();
        //        $data['content'] = $count_articles;
        $data['loaded_post'] = $prevPost->ID;
        $data['page_title'] = html_entity_decode(get_the_title($prevPost));
        $author = get_the_author_meta('first_name', $post->post_author) . ' ' . get_the_author_meta('last_name', $post->post_author);
        if (get_field('author', $prevPost->ID)) {
            $author = get_field('author', $prevPost->ID);
        } else if (get_field('Author', $prevPost->ID)) {
            $author = get_field('Author', $prevPost->ID);
        }
        $data['author'] = $author;

        $categories = get_the_category($prevPost->ID);
        if ($categories) {
            foreach ($categories as $category_obj) :
                $category = $category_obj->slug;
                break;
            endforeach;
            $data['category'] = $category;
        }

        $pagepath = parse_url(get_the_permalink($prevPost->ID), PHP_URL_PATH);
        $pagepath = substr(str_replace('/', '', $pagepath), 0, 40);
        $data['pagepath'] = $pagepath;

        wp_send_json_success($data);
    endif;
    wp_die();
}
add_action('wp_ajax_tbm_ajax_load_next_post', 'tbm_ajax_load_next_post');
add_action('wp_ajax_nopriv_tbm_ajax_load_next_post', 'tbm_ajax_load_next_post');

/*
* Force Focus keyphrase (Yoast) for the posts
*/
function tbm_admin_enqueue($hook)
{
    if (!in_array($hook, array('post.php', 'post-new.php'))) {
        return;
    }
    wp_enqueue_script('admin-validate-post', get_template_directory_uri() . '/js/admin-validate-post.js', array('jquery'), '20190819-2', true);
}
add_action('admin_enqueue_scripts', 'tbm_admin_enqueue');

/*
* Reset (Tree) Category Checklist
*/
add_filter('wp_terms_checklist_args', 'tbm_checklist_args');
function tbm_checklist_args($args)
{
    $args['checked_ontop'] = false;
    return $args;
}

/*
* Custom Taxonomy - Topic
register_taxonomy( 'topic', array ( 'post'), array( 'hierarchical' => false, 'labels' => array( 'name' => 'Topics', 'singular_name' => 'Topic'), 'query_var' => true, 'rewrite' => array( 'slug' => 'topic' ), 'capabilities' => array( 'manage_terms' => 'manage_categories', 'edit_terms' => 'manage_categories', 'delete_terms' => 'manage_categories', 'assign_terms' => 'edit_posts', 'assign_terms' => 'manage_categories' ), 'show_ui' => true, 'public' => true ) );
*/

/*
* Force tags, etc. for the posts (using plugin hook - Require Post Category - https://en-au.wordpress.org/plugins/require-post-category/)
*/
function tbm_rpc_post_types($post_types)
{
    // Add a key to the $post_types array for each post type and list the slugs of the taxonomies you wish to require

    // Simplest usage
    $post_types['post'] = array('category', 'post_tag', 'topic',);

    // Always return $post_types after your modifications
    return $post_types;
}
add_filter('rpc_post_types', 'tbm_rpc_post_types');

/*
* Set cookie
*/
add_action('wp_ajax_nopriv_tbm_set_cookie', 'ajax_tbm_set_cookie');
add_action('wp_ajax_tbm_set_cookie', 'ajax_tbm_set_cookie');
function ajax_tbm_set_cookie()
{
    $data = isset($_POST) ? $_POST : [];
    tbm_set_cookie($data);
    wp_die();
}

/* function tbm_track_visits()
{
    if (get_field('track_visitors')) {
        $track_visitors = get_field('track_visitors');

        $cookie_key = 'tbm_v';

        tbm_set_cookie([
            'key' => $cookie_key,
            'value' => $track_visitors,
            'duration' => 60 * 60 * 24 * 365
        ]);
    }
} */

function tbm_set_cookie($data)
{
    if (!empty($data) && isset($data['key']) && isset($data['value']) && isset($data['duration'])) :
        setcookie($data['key'], $data['value'], time() + (int) $data['duration'], '/', $_SERVER['HTTP_HOST']);
    endif;
}

function render_ad_tag($tag, $slot_no = 1)
{
    if(!is_home() && !is_front_page()) {
        if (function_exists('get_field') && isset($post) && get_field('paid_content', $post->ID)) {
            return;
        }
        if (!file_exists(WP_PLUGIN_DIR . '/tbm-adm/tbm-adm.php')) {
            return;
        }
    }

    require_once WP_PLUGIN_DIR . '/tbm-adm/tbm-adm.php';

    $ads = TBMAds::get_instance();
    echo $ads->get_ad($tag, $slot_no, get_the_ID());
    return;
}

function is_mobile($kind = 'any', $caller = '')
{
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        $is_mobile = false;
    } elseif (
        strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
    ) {
        $is_mobile = true;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false) {
        $is_mobile = true;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) {
        $is_mobile = false;
    } else {
        $is_mobile = false;
    }

    return $is_mobile;
}

/*
* FIX for Password reset link not showing
*/
// add_filter( 'retrieve_password_message', 'tbm_custom_password_reset', 99, 4);
function tbm_custom_password_reset($message, $key, $user_login, $user_data)
{
    $message = "Someone has requested a password reset for the following account:
        " . sprintf(__('%s'), $user_data->user_email) . "
        If this was a mistake, just ignore this email and nothing will happen.
        To reset your password, visit the following address:
        " . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n";
    return $message;
}

/*
* Check if post is in subcategory
*/
if (!function_exists('post_is_in_descendant_category')) {
    function post_is_in_descendant_category($cats, $_post = null)
    {
        foreach ((array) $cats as $cat_slug) {
            // get_term_children() accepts integer ID only
            $cat = get_category_by_slug($cat_slug);
            if (!$cat)
                return false;
            $descendants = get_term_children((int) $cat->term_id, 'category');
            if ($descendants && in_category($descendants, $_post))
                return true;
        }
        return false;
    }
}

function get_auth_error_message($error_code)
{
    switch ($error_code) {
        case 'empty_username':
            return 'You do have an email address, right?';

        case 'empty_password':
            return 'You need to enter a password to login.';

        case 'invalid_username':
            return "We don't have any users with that email address. Maybe you used a different one when signing up?";

        case 'incorrect_password':
            $err = "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?";
            return sprintf($err, wp_lostpassword_url());

        case 'empty_username':
            return 'You need to enter your email address to continue.';
        case 'invalid_email':
        case 'invalidcombo':
            return 'There are no users registered with this email address.';

        case 'expiredkey':
        case 'invalidkey':
            return 'The password reset link you used is not valid anymore.';

        case 'password_reset_mismatch':
            return "The two passwords you entered don't match.";

        case 'password_reset_empty':
            return "Sorry, we don't accept empty passwords.";

        default:
            break;
    }

    return 'An unknown error occurred. Please try again later.';
}

function getStates()
{
    return [
        "NSW" => "New South Wales",
        "VIC" => "Victoria",
        "QLD" => "Queensland",
        "TAS" => "Tasmania",
        "SA" => "South Australia",
        "WA" => "Western Australia",
        "NT" => "Northern Territory",
        "ACT" => "Australian Capital Territory",
        "NZ" => "New Zealand",
        "INT" => "Outside AU / NZ",
    ];
}

function getGenders()
{
    return [
        "Male",
        "Female",
        "Gender not listed here",
    ];
}


/*
* Include featured image in RSS feed
*/
function tbm_post_thumbnails_in_feeds($content)
{
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
        $content = '<figure><img src="' . $img_src[0] . '" class="type:primaryImage"></figure>' . $content;
    }
    return $content;
}
add_filter('the_excerpt_rss', 'tbm_post_thumbnails_in_feeds');
add_filter('the_content_feed', 'tbm_post_thumbnails_in_feeds');


/*
* Check unique refer code (Brag Observer Invites)
*/
function check_unique_refer_code($unique)
{
    global $wpdb;
    $result = $wpdb->get_var("SELECT meta_value from $wpdb->usermeta where meta_key='refer_code' AND meta_value = '{$unique}'");
    if (!$result)
        return true;
    return false;
}

/*
* Check unique copm code (Brag Observer Competitions)
*/
function check_unique_comp_code($unique)
{
    global $wpdb;
    $result = $wpdb->get_var("SELECT meta_value from $wpdb->usermeta where meta_key='comp_code' AND meta_value = '{$unique}'");
    if (!$result)
        return true;
    return false;
}


add_action('simple_jwt_login_jwt_payload_auth', function ($payload, $request) {

    $payload['aud'] = 'application-1-kaekd';
    $payload['sub'] = 'aeRNRPdIfLkBKBnBkRKoWwxhE4hGYZOS';
    // error_log( print_r( $payload, true ) );
    return $payload;
}, 10, 2);

/* function tbm_admin_notice(){
    echo '<div class="notice notice-success is-dismissible">
          <p>You are on new server.</p>
         </div>';
}
add_action('admin_notices', 'tbm_admin_notice'); */

function brands()
{
    $pub_logos = [
        'the-brag' => [
            'title' => 'The Brag',
            'link' => 'https://thebrag.com/',
            'logo_name' => 'The-Brag_combo',
            'ext' => 'svg',
        ],
        'brag-jobs' => [
            'title' => 'The Brag Jobs',
            'link' => 'https://thebrag.com/jobs',
            'logo_name' => 'The-Brag-Jobs',
            'width' => 80,
            'ext' => 'png',
        ],
        /*'dbu' => [
            'title' => 'Don\'t Bore Us',
            'link' => 'https://dontboreus.thebrag.com/',
            'logo_name' => 'Dont-Bore-Us',
            'ext' => 'svg',
        ],
        'tio' => [
            'title' => 'The Industry Observer',
            'link' => 'https://theindustryobserver.thebrag.com/',
            'logo_name' => 'The-Industry-Observer',
            'ext' => 'svg',
        ], */
        'rolling-stone-australia' => [
            'title' => 'Rolling Stone Australia',
            'link' => 'https://au.rollingstone.com/',
            'logo_name' => 'Rolling-Stone-Australia',
            'ext' => 'png',
        ],
        'tone-deaf' => [
            'title' => 'Tone Deaf',
            'link' => 'https://tonedeaf.thebrag.com/',
            'logo_name' => 'Tone-Deaf',
            'ext' => 'svg',
            'width' => 80
        ],
        'tmn' => [
            'title' => 'The Music Network',
            'link' => 'https://themusicnetwork.com/',
            'logo_name' => 'TMN',
            'ext' => 'svg',
            'width' => 80
        ],
        'variety-au' => [
            'title' => 'Variety Australia',
            'link' => 'https://au.variety.com/',
            'logo_name' => 'Variety-Australia',
            'ext' => 'svg',
            'width' => 120
        ],
    ];
    return $pub_logos;
} // brands()

function brands_network()
{
    $pub_logos = [
        /**
         * EPIC
         */
        'lwa' => [
            'title' => 'Life Without Andy',
            'link' => 'https://lifewithoutandy.com/',
            'logo_name' => 'lwa',
            'ext' => 'png',
            'width' => 60
        ],
        'hypebeast' => [
            'title' => 'Hypebeast',
            'link' => 'https://hypebeast.com/',
            'logo_name' => 'Hypebeast',
            'ext' => 'png',
        ],
        'funimation' => [
            'title' => 'Funimation',
            'link' => 'https://www.funimation.com/',
            'logo_name' => 'Funimation',
            'ext' => 'png',
        ],
        'crunchyroll' => [
            'title' => 'Crunchyroll',
            'link' => 'https://www.crunchyroll.com/en-gb',
            'logo_name' => 'Crunchyroll',
            'ext' => 'png',
        ],
        'enthusiast' => [
            'title' => 'Enthusiast Gaming',
            'link' => 'https://www.enthusiastgaming.com/',
            'logo_name' => 'enthusiast',
            'ext' => 'png',
        ],
        'gamelancer' => [
            'title' => 'Gamelancer',
            'link' => 'https://gamelancer.com/',
            'logo_name' => 'Gamelancer',
            'ext' => 'png',
        ],
        'toongoggles' => [
            'title' => 'ToonGoggles',
            'link' => 'https://www.toongoggles.com/',
            'logo_name' => 'ToonGoggles',
            'ext' => 'png',
        ],
        'kidoodle' => [
            'title' => 'kidoodle',
            'link' => 'https://www.kidoodle.tv/',
            'logo_name' => 'kidoodle',
            'ext' => 'png',
        ],

        /**
         * PMC
         */
        'artnews' => [
            'title' => 'ARTnews',
            'link' => 'https://www.artnews.com/',
            'logo_name' => 'ARTnews',
        ],
        'bgr' => [
            'title' => 'BGR',
            'link' => 'https://bgr.com/',
            'logo_name' => 'bgr',
            'width' => 80
        ],
        'billboard' => [
            'title' => 'Billboard',
            'link' => 'https://billboard.com/',
            'logo_name' => 'billboard',
        ],
        'deadline' => [
            'title' => 'Deadline',
            'link' => 'https://deadline.com/',
            'logo_name' => 'DEADLINE',
        ],
        'dirt' => [
            'title' => 'Dirt',
            'link' => 'https://www.dirt.com/',
            'logo_name' => 'Dirt',
            'width' => 80
        ],
        'footwear' => [
            'title' => 'Footwear News',
            'link' => 'https://footwearnews.com/',
            'logo_name' => 'FootwearNews',
            'width' => 60
        ],
        'gold-derby' => [
            'title' => 'Gold Derby',
            'link' => 'https://www.goldderby.com/',
            'logo_name' => 'GoldDerby',
        ],
        'indiewire' => [
            'title' => 'IndieWire',
            'link' => 'https://www.indiewire.com/',
            'logo_name' => 'IndieWire',
        ],
        'sheknows' => [
            'title' => 'SheKnows',
            'link' => 'https://www.sheknows.com/',
            'logo_name' => 'SheKnows',
        ],
        'sourcing-journal' => [
            'title' => 'Sourcing Journal',
            'link' => 'https://sourcingjournal.com/',
            'logo_name' => 'SourcingJournal',
        ],
        'sportico' => [
            'title' => 'Sportico',
            'link' => 'https://www.sportico.com/',
            'logo_name' => 'Sportico',
        ],
        'spy' => [
            'title' => 'Spy',
            'link' => 'https://spy.com/',
            'logo_name' => 'Spy',
            'width' => 120,
        ],
        'stylecaster' => [
            'title' => 'Stylecaster',
            'link' => 'https://stylecaster.com/',
            'logo_name' => 'Stylecaster',
        ],
        'the-hollywood-reporter' => [
            'title' => 'The Hollywood Reporter',
            'link' => 'https://www.hollywoodreporter.com/',
            'logo_name' => 'The-Hollywood-Reporter',
        ],
        'tvline' => [
            'title' => 'TVLine',
            'link' => 'https://tvline.com/',
            'logo_name' => 'TVLine',
            'width' => 120,
        ],
        /* 'variety' => [
            'title' => 'Variety',
            'link' => 'https://variety.com/',
            'logo_name' => 'Variety',
            'width' => 120,
        ], */
        'vibe' => [
            'title' => 'VIBE',
            'link' => 'https://www.vibe.com/',
            'logo_name' => 'Vibe',
            'width' => 120,
        ],
    ];
    return $pub_logos;
} // brands_network()

add_filter('next_posts_link_attributes', 'tbm_posts_link_attributes');
add_filter('previous_posts_link_attributes', 'tbm_posts_link_attributes');

function tbm_posts_link_attributes()
{
    return 'class="btn btn-dark"';
}


// Observer sub form
add_filter('the_content', function ($content) {
    if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
        return $content;
    }
    if ('single-template-featured.php' == get_page_template_slug(get_the_ID())) {
        return $content;
    }
    if (get_field('hide_observer_form'))
        return $content;

    if (shortcode_exists('observer_subscribe_category')) :
        ob_start();
        echo do_shortcode('[observer_subscribe_category id="' . get_the_ID() . '"]');
        $content_shortcode = ob_get_contents();
        ob_end_clean();
        $content = ssm_insert_after_paragraph($content_shortcode, 7, $content);
    endif;
    return $content;
});


// add_action('wp_footer', 'inject_roymorgan', 99, 2);
function inject_roymorgan()
{
?>
    <script type="text/javascript">
        jQuery(function() {
            var cachebuster = Date.now();
            var script = document.createElement('script');
            script.src = 'https://pixel.roymorgan.com/stats_v2/Tress.php?u=k7b7oit54p&ca=20005195&a=6id59hbq' + '&cb=' + cachebuster;
            script.async = true;
            document.body.appendChild(script);
        });
    </script>
<?php
}

/*
* Add Comps link in article
*/
/* add_filter('the_content', function ($content) {
    if ((function_exists('get_field') && get_field('paid_content')) || is_page_template('single-template-featured.php')) :
        return $content;
    endif;

    if (is_singular('page'))
        return $content;

    if (get_field('override_footer_comps_box')) {
        if (get_field('override_footer_comps_colour')) {
            $content .= '<style>.comp-footer a {
            color: ' . get_field('override_footer_comps_colour') . ' !important;
            border: 1px solid ' . get_field('override_footer_comps_colour') . ' !important;
            }</style>';
        }
        $content .= '<div class="comp-footer"><a href="' . get_field('override_footer_comps_link') . '" target="_blank" rel="noopener">' . get_field('override_footer_comps_text') . '</a></div>';
    } else {
        $content .= '<div class="comp-footer"><a href="https://thebrag.com/observer/competitions/" target="_blank" rel="noopener">Did you know we\'re constantly giving away <strong>FREE</strong> stuff? Check out our giveaways here.</a></div>';
    }

    return $content;
}); */

add_filter('the_content', function ($content) {
    if ((function_exists('get_field') && get_field('paid_content')) || is_page_template('single-template-featured.php')) :
        return $content;
    endif;

    if (is_singular('page'))
        return $content;

    if (function_exists('amp_is_request') && amp_is_request()) {
        return $content;
    }

    $mag_cover_res = wp_remote_get('https://au.rollingstone.com/wp-json/tbm_mag_sub/v1/next_issue_img_thumb');

    if (is_array($mag_cover_res) && !is_wp_error($mag_cover_res)) {
        $mag_cover = json_decode($mag_cover_res['body']);
    }

    $content .= '<a href="https://au.rollingstone.com/subscribe-magazine/" target="_blank" rel="noopener" class="d-flex flex-column flex-md-row align-items-start rs-subscribe-footer"><div class="d-flex">';
    if (isset($mag_cover) && '' != $mag_cover) {
        $content .= '<div class="flex-fill img-wrap"><img src="' . $mag_cover . '" width="100" loading="lazy"></div>';
    }
    $content .= '<div>Get unlimited access to the coverage that shapes our culture.';
    $content .= '<div class="d-none d-md-block mt-1"><span class="subscribe">Subscribe</span> to <strong>Rolling Stone magazine</strong></div></div></div>';
    $content .= '<div class="d-block d-md-none mt-3 w-100"><span class="subscribe">Subscribe</span> to <strong>Rolling Stone magazine</strong></div>';
    $content .= '</a>';

    return $content;
});

function get_social_platforms()
{
    // platform => Title
    return [
        'twitter' => 'Twitter',
        'instagram' => 'Instagram',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok'
    ];
}

add_action('wp_footer', 'inject_ga4', 99, 2);
function inject_ga4()
{
?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-L8V4HEDPRH"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-L8V4HEDPRH');
    </script>
    <?php
}