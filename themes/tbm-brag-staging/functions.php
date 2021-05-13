<?php

define('ICONS_URL', get_template_directory_uri() . '/images/');

register_nav_menus(array(
    'get_in_touch' => __('Get In Touch', 'thebrag'),
    'top' => __('Top Menu', 'thebrag'),
    'shitshow' => __('Sh!tShow', 'thebrag'),
    'top-what-you-love' => __('Top What You Love', 'thebrag'),
    'top-check-this-out' => __('Top Check This Out', 'thebrag'),
));

function render_ad_tag($tag, $slot_no = 1)
{
    if (!file_exists(WP_PLUGIN_DIR . '/tbm-adm/tbm-adm.php'))
        return;
    require_once WP_PLUGIN_DIR . '/tbm-adm/tbm-adm.php';
    $ads = TBMAds::get_instance();
    echo $ads->get_ad($tag, $slot_no, get_the_ID());
    return;
}

/*
* Enable support for Post Thumbnails on posts and pages.
*
* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
*/
add_theme_support('post-thumbnails');

function load_js_css()
{
    // wp_enqueue_script( 'scripts', get_template_directory_uri() . '/js/scripts.js', array ( 'jquery' ), time(), true);

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
// add_action('wp_enqueue_scripts', 'load_js_css');

function string_limit_words($string, $word_limit)
{
    $words = explode(' ', $string, ($word_limit + 1));
    if (count($words) > $word_limit) {
        array_pop($words);
        return implode(' ', $words) . '...';
    }
    return implode(' ', $words);
}


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