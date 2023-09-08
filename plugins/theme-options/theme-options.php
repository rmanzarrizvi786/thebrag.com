<?php

/**
 * Plugin Name: Theme Options
 * Plugin URI: https://thebrag.media/
 * Description: Theme Options
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI: http://www.patelsachin.com
 */

add_action('admin_menu', 'tbm_theme_options_plugin_menu');
add_action('rest_api_init', 'tbm_theme_options_rest_api_init');
function tbm_theme_options_plugin_menu()
{
    add_menu_page('Theme Options', 'Theme Options', 'edit_pages', 'tbm_theme_options', 'tbm_theme_options');
}

function tbm_theme_options_rest_api_init()
{
    register_rest_route('tbm', '/votw', array(
        'methods' => 'GET',
        'callback' => 'rest_get_votw',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('tbm', '/rotw', array(
        'methods' => 'GET',
        'callback' => 'rest_get_rotw',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('tbm', '/spotlight', array(
        'methods' => 'GET',
        'callback' => 'rest_get_most_spotlight',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('tbm', '/most-read', array(
        'methods' => 'GET',
        'callback' => 'rest_get_most_read',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('tbm', '/trending', array(
        'methods' => 'GET',
        'callback' => 'rest_get_trending',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('tbm', '/latest', array(
        'methods' => 'GET',
        'callback' => 'rest_get_latest',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('tbm', '/latest-network', array(
        'methods' => 'GET',
        'callback' => 'rest_get_latest_network',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('tbm', '/floating_dailymotion_playlist_id', array(
        'methods' => 'GET',
        'callback' => function () {
            return get_option('tbm_floating_dm_playlist_id');
        },
        'permission_callback' => '__return_true',
    ));
}


function rest_get_most_spotlight()
{
    $articles_arr = array();

    if (get_option('must_read_network')) {
        $must_read_network = json_decode(get_option('must_read_network'));

        $articles_arr[] = $must_read_network;
    }

    if (get_option('spotlight_1_network')) {
        $spotlight_1_network = json_decode(get_option('spotlight_1_network'));

        $articles_arr[] = $spotlight_1_network;
    }

    if (get_option('spotlight_2_network')) {
        $spotlight_2_network = json_decode(get_option('spotlight_2_network'));

        $articles_arr[] = $spotlight_2_network;
    }

    if (get_option('spotlight_3_network')) {
        $spotlight_3_network = json_decode(get_option('spotlight_3_network'));

        $articles_arr[] = $spotlight_3_network;
    }

    if (get_option('spotlight_4_network')) {
        $spotlight_4_network = json_decode(get_option('spotlight_4_network'));

        $articles_arr[] = $spotlight_4_network;
    }

    // needs fallback option

    if (count($articles_arr) > 0) {
        return $articles_arr;
    }

    return array([
        'image' => '',
        'title' => '',
        'category' => '',
        'brand_logo_light' => '',
        'brand_logo_dark' => '',
        'brand_link' => '',
        'excerpt' =>  '',
        'link' => ''
    ]);
}

function rest_get_most_read()
{
    if (get_option('must_read_network')) {
        $must_read_network = json_decode(get_option('must_read_network'));

        return $must_read_network;
    }

    // needs fallback option

    return [
        'image' => '',
        'title' => '',
        'category' => '',
        'brand_logo_light' => '',
        'brand_logo_dark' => '',
        'brand_link' => '',
        'excerpt' =>  '',
        'link' => ''
    ];
}

function rest_get_trending()
{
    $articles_arr = array();

    $tonedeaf_resp = wp_remote_get('https://tonedeaf.thebrag.com/wp-json/tbm/most-read');
    $rsau_resp = wp_remote_get('https://au.rollingstone.com/wp-json/tbm/most-read');

    if (is_array($rsau_resp) && !is_wp_error($rsau_resp)) {
        $trending2 = json_decode($rsau_resp['body']);

        $articles_arr[] = [
            "image" => $trending2[0]->image,
            "title" => $trending2[0]->title,
            "category" => $trending2[0]->category,
            "brand_logo_light" => $trending2[0]->brand_logo_light,
            "brand_logo_dark" => $trending2[0]->brand_logo_dark,
            "brand_link" => $trending2[0]->brand_link,
            "excerpt" => $trending2[0]->excerpt,
            "link" => $trending2[0]->link
        ];
    }

    if (is_array($tonedeaf_resp) && !is_wp_error($tonedeaf_resp)) {
        $trending1 = json_decode($tonedeaf_resp['body']);

        $articles_arr[] = [
            "image" => $trending1[0]->image,
            "title" => $trending1[0]->title,
            "category" => $trending1[0]->category,
            "brand_logo_light" => $trending1[0]->brand_logo_light,
            "brand_logo_dark" => $trending1[0]->brand_logo_dark,
            "brand_link" => $trending1[0]->brand_link,
            "excerpt" => $trending1[0]->excerpt,
            "link" => $trending1[0]->link
        ];
    }

    return $articles_arr;
}

function rest_get_latest()
{
    global $post;

    $trending_story_args = [
        'post_status' => 'publish',
        'posts_per_page' => 1,
    ];
    if (get_option('most_viewed_yesterday')) {
        $trending_story_args['p'] = get_option('most_viewed_yesterday');
    }
    $trending_story_query = new WP_Query($trending_story_args);
    if ($trending_story_query->have_posts()) :
        while ($trending_story_query->have_posts()) :
            $trending_story_query->the_post();
            $trending_story_ID = get_the_ID();
            $exclude_posts[] = $trending_story_ID;
            $args['exclude_posts'][] = $trending_story_ID;
        endwhile;
        wp_reset_query();
    endif;

    $posts_per_page = 5;
    $news_args = array(
        'post_status' => 'publish',
        'post_type' => array('post', 'snaps', 'dad'),
        'ignore_sticky_posts' => 1,
        'post__not_in' => $exclude_posts,
        'posts_per_page' => $posts_per_page,
    );
    $news_query = new WP_Query($news_args);
    $no_of_columns = 2;
    if ($news_query->have_posts()) :
        $count = 1;
        $articles_arr = array();

        while ($news_query->have_posts()) :
            $news_query->the_post();
            $post_id = get_the_ID();

            $category = '';

            if ('snaps' == $post->post_type) :
                $category = 'GALLERY';
            elseif ('dad' == $post->post_type) :
                $categories = get_the_terms(get_the_ID(), 'dad-category');
                if ($categories) :
                    if ($categories[0] && 'Uncategorised' != $categories[0]->name) :
                        $category = $categories[0]->name;
                    elseif (isset($categories[1])) :
                        $category = $categories[1]->name;
                    else :
                    endif; // If Uncategorised 
                endif; // If there are Dad categories 
            else :
                $categories = get_the_category();
                if ($categories) :
                    if (isset($categories[0]) && 'Evergreen' != $categories[0]->cat_name) :
                        if (0 == $categories[0]->parent) :
                            $category = $categories[0]->cat_name;
                        else : $parent_category = get_category($categories[0]->parent);
                            $category = $parent_category->cat_name;
                        endif;
                    elseif (isset($categories[1])) :
                        if (0 == $categories[1]->parent) :
                            $category = $categories[1]->cat_name;
                        else : $parent_category = get_category($categories[1]->parent);
                            $category = $parent_category->cat_name;
                        endif;
                    endif; // If Evergreen 
                endif; // If there are Dad categories 
            endif; // If Photo Gallery 

            $image = '' !== get_the_post_thumbnail() ? get_the_post_thumbnail_url() : '';
            $metadesc = get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);
            $excerpt = tbm_the_excerpt( $metadesc );

            $articles_arr[] = [
                'image' => $image,
                'title' => get_the_title(),
                'category' => $category,
                'brand_logo_light' => 'https://images.thebrag.com/common/brands/202309/thebrag-light.png',
                'brand_logo_dark' => 'https://images.thebrag.com/common/brands/202309/thebrag-dark.png',
                'brand_link' => 'https://thebrag.com',
                'excerpt' => $excerpt,
                'link' => get_the_permalink(),
            ];
            
            $count++;
        endwhile;
    endif;

    return $articles_arr;
}

function rest_get_latest_network() {
    $transient = get_transient('tbm_latest_network');

    if ($transient) {
        return json_decode($transient);
    }

    $rsau_resp = wp_remote_get('https://au.rollingstone.com/wp-json/wp/v2/posts?per_page=1');
    $tonedeaf_resp = wp_remote_get('https://tonedeaf.thebrag.com/wp-json/wp/v2/posts?per_page=1');
    $variety_resp = wp_remote_get('http://au.variety.com/wp-json/wp/v2/posts?per_page=1');
    $tmn_resp = wp_remote_get('https://themusicnetwork.com/wp-json/wp/v2/posts?per_page=1');

    if (is_array($rsau_resp) && !is_wp_error($rsau_resp)) {
        $rsau = json_decode($rsau_resp['body']);

        $url_parts = parse_url($rsau[0]->link);

        $filename = str_replace('.com', '', $url_parts['host']);
        $filename = str_replace('.thebrag', '', $filename );

        $excerpt = tbm_the_excerpt( $rsau[0]->excerpt->rendered );

        $articles_arr[] = [
            'image' => $rsau[0]->yoast_head_json->og_image[0]->url,
            'title' => $rsau[0]->title->rendered,
            'category' => '',
            'brand_logo_light' => 'https://images.thebrag.com/common/brands/202309/au.rollingstone-light.png',
            'brand_logo_dark' => 'https://images.thebrag.com/common/brands/202309/au.rollingstone-dark.png',
            'brand_link' => 'https://au.rollingstone.com',
            'excerpt' => $excerpt,
            'link' => $rsau[0]->link,
        ];
    }

    if (is_array($tonedeaf_resp) && !is_wp_error($tonedeaf_resp)) {
        $tonedeaf = json_decode($tonedeaf_resp['body']);

        $url_parts = parse_url($tonedeaf[0]->link);

        $filename = str_replace('.com', '', $url_parts['host']);
        $filename = str_replace('.thebrag', '', $filename );

        $excerpt = tbm_the_excerpt( $tonedeaf[0]->excerpt->rendered );

        $articles_arr[] = [
            'image' => $tonedeaf[0]->yoast_head_json->og_image[0]->url,
            'title' => $tonedeaf[0]->title,
            'category' => '',
            'brand_logo_light' => 'https://images.thebrag.com/common/brands/202309/tonedeaf-light.png',
            'brand_logo_dark' => 'https://images.thebrag.com/common/brands/202309/tonedeaf-dark.png',
            'brand_link' => 'https://tonedeaf.thebrag.com',
            'excerpt' => $excerpt,
            'link' => $tonedeaf[0]->link,
        ];
    }

    if (is_array($variety_resp) && !is_wp_error($variety_resp)) {
        $variety = json_decode($variety_resp['body']);

        $url_parts = parse_url($variety[0]->link);

        $filename = str_replace('.com', '', $url_parts['host']);
        $filename = str_replace('.thebrag', '', $filename );

        $excerpt = tbm_the_excerpt( $variety[0]->excerpt->rendered );

        $articles_arr[] = [
            'image' => $variety[0]->yoast_head_json->og_image[0]->url,
            'title' => $variety[0]->title,
            'category' => '',
            'brand_logo_light' => 'https://images.thebrag.com/common/brands/202309/au.variety-light.png',
            'brand_logo_dark' => 'https://images.thebrag.com/common/brands/202309/au.variety-dark.png',
            'brand_link' => 'https://au.variety.com',
            'excerpt' => $excerpt,
            'link' => $variety[0]->link,
        ];
    }

    if (is_array($tmn_resp) && !is_wp_error($tmn_resp)) {
        $tmn = json_decode($tmn_resp['body']);

        $url_parts = parse_url($tmn[0]->link);

        $filename = str_replace('.com', '', $url_parts['host']);
        $filename = str_replace('.thebrag', '', $filename );

        $excerpt = tbm_the_excerpt( $tmn[0]->excerpt->rendered );

        $articles_arr[] = [
            'image' => $tmn[0]->yoast_head_json->og_image[0]->url,
            'title' => $tmn[0]->title,
            'category' => '',
            'brand_logo_light' => 'https://images.thebrag.com/common/brands/202309/themusicnetwork-light.png',
            'brand_logo_dark' => 'https://images.thebrag.com/common/brands/202309/themusicnetwork-dark.png',
            'brand_link' => 'https://themusicnetwork.com',
            'excerpt' => $excerpt,
            'link' => $tmn[0]->link,
        ];
    }

    set_transient('tbm_latest_network', json_encode($articles_arr), 300);

    return  $articles_arr;
}

function rest_get_votw()
{
    $featured_yt_vid_id = NULL;
    $featured_video = get_option('tbm_featured_video');
    $tbm_featured_video_link = get_option('tbm_featured_video_link');
    if (!is_null($featured_video) && $featured_video != '') :
        parse_str(parse_url($featured_video, PHP_URL_QUERY), $featured_video_vars);
        $featured_yt_vid_id = isset($featured_video_vars['v']) ? $featured_video_vars['v'] : NULL;
        $featured_video_img = !is_null($featured_yt_vid_id) ? 'https://i.ytimg.com/vi/' . $featured_yt_vid_id . '/0.jpg' : NULL;
        if ($tbm_featured_video_link) :
            $tbm_featured_video_link_html = file_get_contents($tbm_featured_video_link);
            $tbm_featured_video_link_html_dom = new DOMDocument();
            @$tbm_featured_video_link_html_dom->loadHTML($tbm_featured_video_link_html);
            // $meta_og_img_tbm_featured_video_link = null;
            foreach ($tbm_featured_video_link_html_dom->getElementsByTagName('meta') as $meta) {
                if ($meta->getAttribute('property') == 'og:image') {
                    $featured_video_img = $meta->getAttribute('content');
                    break;
                }
            }
        endif;
    endif;
    // fix logo
    $featured_video_img = str_ireplace('/img-socl/?url=', '', substr($featured_video_img, strpos($featured_video_img, '/img-socl/?url=')));
    $featured_video_img = str_ireplace('&nologo=1', '', $featured_video_img);

    return [
        'image' => $featured_video_img,
        'artist' => get_option('tbm_featured_video_artist') ? '' . esc_html(stripslashes(get_option('tbm_featured_video_artist'))) : '',
        'song' => get_option('tbm_featured_video_song') ? '' . esc_html(stripslashes(get_option('tbm_featured_video_song'))) : '',
        'link' => $tbm_featured_video_link,
    ];
}

function rest_get_rotw()
{
    return [
        'image' => get_option('tbm_featured_album_image_url') ? '' . esc_html(stripslashes(get_option('tbm_featured_album_image_url'))) : '',
        'artist' => get_option('tbm_featured_album_artist') ? '' . esc_html(stripslashes(get_option('tbm_featured_album_artist'))) : '',
        'title' => get_option('tbm_featured_album_title') ? '' . esc_html(stripslashes(get_option('tbm_featured_album_title'))) : '',
        'link' => get_option('tbm_featured_album_link') ? '' . esc_html(stripslashes(get_option('tbm_featured_album_link'))) : '',
    ];
}

function get_remote_data( $url )
{
    if ($url) :        
        $sites_html = file_get_contents( $url );
        
        $html = new DOMDocument();
        @$html->loadHTML($sites_html);
    
        $meta_og_title = $meta_og_img = $meta_og_description = null;
    
        foreach ($html->getElementsByTagName('meta') as $meta) {
            if ($meta->getAttribute('property') == 'og:image') {
                if (!isset($meta_og_img)) {
                    $meta_og_img = $meta->getAttribute('content');
                    $meta_og_img = str_ireplace('&#038;nologo=1', '', $meta_og_img);
                    $meta_og_img = str_ireplace('&nologo=1', '', $meta_og_img);
                    $meta_og_img = str_ireplace('/img-socl/?url=', '', substr($meta_og_img, strpos($meta_og_img, '/img-socl/?url=')));
                }
            } elseif ($meta->getAttribute('property') == 'og:title') {
                if (!isset($meta_og_title)) {
                    $meta_og_title = $meta->getAttribute('content');
                }
            } elseif ($meta->getAttribute('property') == 'og:description') {
                if (!isset($meta_og_description)) {
                    $meta_og_description = $meta->getAttribute('content');
                }
            }
        }

        

        return [
            'title' => trim($meta_og_title),
            'excerpt' => trim($meta_og_description),
            'image' => trim($meta_og_img)
        ];
    endif;

    return [
        'title' => '',
        'excerpt' => '',
        'image' => ''
    ];
}

function tbm_theme_options()
{
    wp_enqueue_script('bs', get_template_directory_uri() . '/bs/js/bootstrap.bundle.min.js', array('jquery'), '20190424', true);
    wp_enqueue_style('bs', get_template_directory_uri() . '/bs/css/bootstrap.min.css');
    wp_enqueue_style('edm-mailchimp', plugin_dir_url(__FILE__) . '/css/style.css');

    wp_enqueue_script('td-jquery-autocomplete', get_template_directory_uri() . '/js/jquery.auto-complete.min.js', array('jquery'), NULL, true);
    wp_enqueue_script('td-options-ajax-search', get_template_directory_uri() . '/js/scripts-admin.js', array('jquery'), NULL, true);

    wp_enqueue_script('tbm-theme-options', plugin_dir_url(__FILE__) . '/js/scripts.js', array('jquery'), '20190429', true);

    $curl_metadata = array(
        'auth_key' => 'cacU1r_3wUpusw9cadltIratL8+glt*s',
        'data' => array()
    );

    // $current_blog_id = get_current_blog_id();

    // $sites = get_sites();
    /*
     * Save options
     */
    if (isset($_POST) && count($_POST) > 0) :
        if (isset($_POST['tbm_featured_infinite_ID'])) :
            // $tbm_featured_infinite_ID = absint($_POST['tbm_featured_infinite_ID']);
            $tbm_featured_infinite_IDs = trim($_POST['tbm_featured_infinite_ID']);
            if ($tbm_featured_infinite_IDs != '') :
                update_option('tbm_featured_infinite_ID', $tbm_featured_infinite_IDs);
            else :
                update_option('tbm_featured_infinite_ID', '');
            endif;
        endif; // tbm_featured_infinite_ID

        if (isset($_POST['force_most_viewed'])) :
            $force_most_viewed = absint($_POST['force_most_viewed']);
            if ($force_most_viewed > 0) :
                update_option('force_most_viewed', absint($_POST['force_most_viewed']));
                update_option('most_viewed_yesterday', absint($_POST['force_most_viewed']));
            else :
                update_option('force_most_viewed', '');
            endif;
        endif; // force_most_viewed

        if (isset($_POST['must_read_network'])) :
                $article_url = $_POST['must_read_network'];
                $article_remote_data = get_remote_data( $article_url );

                $url_parts = parse_url($article_url);

                $filename = str_replace('.com', '', $url_parts['host']);
                $filename = str_replace('.thebrag', '', $filename );

                $excerpt = tbm_the_excerpt( $article_remote_data['excerpt'] );

                $height = 'h-5';

                if( $filename == 'hypebeast' ) {
                    $height = 'h-3';
                }

                if( $filename == 'lifewithoutandy' ) {
                    $height = 'h-6';
                }

                $articles_arr = [
                    'image' => $article_remote_data['image'],
                    'title' => $article_remote_data['title'],
                    'category' => '',
                    'brand_logo_light' => "https://images.thebrag.com/common/brands/202309/" . $filename . "-light.png",
                    'brand_logo_dark' => "https://images.thebrag.com/common/brands/202309/" . $filename . "-dark.png",
                    'brand_logo_height' => $height,
                    'brand_link' => $url_parts['scheme'] . '://' . $url_parts['host'],
                    'excerpt' => $excerpt,
                    'link' => $article_url,
                ];

                update_option('must_read_network', json_encode($articles_arr));
            else :
                update_option('must_read_network', '');
        endif; // mmust_read_network

        if (isset($_POST['spotlight_1_network'])) :
            $article_url = $_POST['spotlight_1_network'];
            $article_remote_data = get_remote_data( $article_url );

            $url_parts = parse_url($article_url);

            $filename = str_replace('.com', '', $url_parts['host']);
            $filename = str_replace('.thebrag', '', $filename );

            $excerpt = tbm_the_excerpt( $article_remote_data['excerpt'] );

            $articles_arr = [
                'image' => $article_remote_data['image'],
                'title' => $article_remote_data['title'],
                'category' => '',
                'brand_logo_light' => "https://images.thebrag.com/common/brands/202309/" . $filename . "-light.png",
                'brand_logo_dark' => "https://images.thebrag.com/common/brands/202309/" . $filename . "-dark.png",
                'brand_link' => $url_parts['scheme'] . '://' . $url_parts['host'],
                'excerpt' => $excerpt,
                'link' => $article_url,
            ];

            update_option('spotlight_1_network', json_encode($articles_arr));
        else :
            update_option('spotlight_1_network', '');
        endif; // spotlight_1_network

        if (isset($_POST['spotlight_2_network'])) :
            $article_url = $_POST['spotlight_2_network'];
            $article_remote_data = get_remote_data( $article_url );

            $url_parts = parse_url($article_url);

            $filename = str_replace('.com', '', $url_parts['host']);
            $filename = str_replace('.thebrag', '', $filename );

            $excerpt = tbm_the_excerpt( $article_remote_data['excerpt'] );

            $height = 'h-5';

            if( $filename == 'hypebeast' ) {
                $height = 'h-3';
            }

            if( $filename == 'lifewithoutandy' ) {
                $height = 'h-6';
            }

            $articles_arr = [
                'image' => $article_remote_data['image'],
                'title' => $article_remote_data['title'],
                'category' => '',
                'brand_logo_light' => "https://images.thebrag.com/common/brands/202309/" . $filename . "-light.png",
                'brand_logo_dark' => "https://images.thebrag.com/common/brands/202309/" . $filename . "-dark.png",
                'brand_logo_height' => $height,
                'brand_link' => $url_parts['scheme'] . '://' . $url_parts['host'],
                'excerpt' => $excerpt,
                'link' => $article_url,
            ];

            update_option('spotlight_2_network', json_encode($articles_arr));
        else :
            update_option('spotlight_2_network', '');
        endif; // spotlight_2_network

        if (isset($_POST['spotlight_3_network'])) :
            $article_url = $_POST['spotlight_3_network'];
            $article_remote_data = get_remote_data( $article_url );

            $url_parts = parse_url($article_url);

            $filename = str_replace('.com', '', $url_parts['host']);
            $filename = str_replace('.thebrag', '', $filename );

            $excerpt = tbm_the_excerpt( $article_remote_data['excerpt'] );

            $height = 'h-5';

            if( $filename == 'hypebeast' ) {
                $height = 'h-3';
            }

            if( $filename == 'lifewithoutandy' ) {
                $height = 'h-6';
            }

            $articles_arr = [
                'image' => $article_remote_data['image'],
                'title' => $article_remote_data['title'],
                'category' => '',
                'brand_logo_light' => "https://images.thebrag.com/common/brands/202309/" . $filename . "-light.png",
                'brand_logo_dark' => "https://images.thebrag.com/common/brands/202309/" . $filename . "-dark.png",
                'brand_logo_height' => $height,
                'brand_link' => $url_parts['scheme'] . '://' . $url_parts['host'],
                'excerpt' => $excerpt,
                'link' => $article_url,
            ];

            update_option('spotlight_3_network', json_encode($articles_arr));
        else :
            update_option('spotlight_3_network', '');
        endif; // spotlight_3_network

        if (isset($_POST['spotlight_4_network'])) :
            $article_url = $_POST['spotlight_4_network'];
            $article_remote_data = get_remote_data( $article_url );

            $url_parts = parse_url($article_url);

            $filename = str_replace('.com', '', $url_parts['host']);
            $filename = str_replace('.thebrag', '', $filename );

            $excerpt = tbm_the_excerpt( $article_remote_data['excerpt'] );

            $height = 'h-5';

            if( $filename == 'hypebeast' ) {
                $height = 'h-3';
            }

            if( $filename == 'lifewithoutandy' ) {
                $height = 'h-6';
            }

            $articles_arr = [
                'image' => $article_remote_data['image'],
                'title' => $article_remote_data['title'],
                'category' => '',
                'brand_logo_light' => "https://images.thebrag.com/common/brands/202309/" . $filename . "-light.png",
                'brand_logo_dark' => "https://images.thebrag.com/common/brands/202309/" . $filename . "-dark.png",
                'brand_logo_height' => $height,
                'brand_link' => $url_parts['scheme'] . '://' . $url_parts['host'],
                'excerpt' => $excerpt,
                'link' => $article_url,
            ];
            update_option('spotlight_4_network', json_encode($articles_arr));
        else :
            update_option('spotlight_4_network', '');
        endif; // spotlight_4_network

        foreach ($_POST as $key => $value) :
            if (strpos($key, 'tbm_') !== false && $key != 'tbm_featured_infinite_ID') :
                update_option($key, sanitize_text_field($value));
            endif;
        endforeach;

        echo '<div class="alert alert-success">Options have been saved!</div>';
    endif;
?>
    <h1 class="mt-3">Theme Options</h1>
    <div class="col-md-12">
        <hr/>
    </div>
    <form method="post" class="form">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-12">
                        <h3>Video of the week</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Link URL</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_video_link" id="tbm_featured_video_link" type="text" value="<?php echo stripslashes(get_option('tbm_featured_video_link')); ?>" placeholder="https://" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>YouTube URL</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_video" id="tbm_featured_video" type="text" value="<?php echo stripslashes(get_option('tbm_featured_video')); ?>" placeholder="https://" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Featured Video Artist Title</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_video_artist" id="tbm_featured_video_artist" type="text" value="<?php echo stripslashes(get_option('tbm_featured_video_artist')); ?>" placeholder="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Featured Video Song Title</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_video_song" id="tbm_featured_video_song" type="text" value="<?php echo stripslashes(get_option('tbm_featured_video_song')); ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div><!-- Video of the week -->
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-12">
                        <h3>Record of the week</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Artist</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_album_artist" id="tbm_featured_album_title" type="text" value="<?php echo stripslashes(get_option('tbm_featured_album_artist')); ?>" placeholder="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Title</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_album_title" id="tbm_featured_album_title" type="text" value="<?php echo stripslashes(get_option('tbm_featured_album_title')); ?>" placeholder="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Link</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_album_link" id="tbm_featured_album_link" type="text" value="<?php echo stripslashes(get_option('tbm_featured_album_link')); ?>" placeholder="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Image URL</label>
                            <label class="reset">x</label>
                            <input type="text" name="tbm_featured_album_image_url" id="tbm_featured_album_image_url" class="form-control" value="<?php echo get_option('tbm_featured_album_image_url') != '' ? get_option('tbm_featured_album_image_url')  : ''; ?>">
                            <?php
                            if (function_exists('wp_enqueue_media')) {
                                wp_enqueue_media();
                            } else {
                                wp_enqueue_style('thickbox');
                                wp_enqueue_script('media-upload');
                                wp_enqueue_script('thickbox');
                            }
                            ?>
                            <?php if (get_option('tbm_featured_album_image_url') != '') : 
                            ?>
                                <img src="<?php echo get_option('tbm_featured_album_image_url'); 
                                            ?>" width="100" id="tbm_featured_album_image" class="img-fluid d-block">
                            <?php endif; 
                            ?>
                            <button id="btn-featured-album-image" type="button" class="button">Upload / Select from Library</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Record of the week -->
            <div class="col-md-12">
                <hr/>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-12">
                        <h3>Spotlight Main (Network)</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>URL</label>
                            <label class="reset">x</label>
                            <?php $must_read_network = json_decode(get_option('must_read_network')); ?>
                            <input name="must_read_network" id="must_read_network" type="text" value="<?php echo !is_null($must_read_network) ?  $must_read_network->link : ''; ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h3>Spotlight 1 (Network)</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>URL</label>
                            <label class="reset">x</label>
                            <?php $spotlight_1_network = json_decode(get_option('spotlight_1_network')); ?>
                            <input name="spotlight_1_network" id="spotlight_1_network" type="text" value="<?php echo !is_null($spotlight_1_network) ? $spotlight_1_network->link : ''; ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h3>Spotlight 2 (Network)</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>URL</label>
                            <label class="reset">x</label>
                            <?php $spotlight_2_network = json_decode(get_option('spotlight_2_network')); ?>
                            <input name="spotlight_2_network" id="spotlight_2_network" type="text" value="<?php echo !is_null($spotlight_2_network) ? $spotlight_2_network->link : ''; ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-12">
                        <h3>Spotlight 3 (Network)</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>URL</label>
                            <label class="reset">x</label>
                            <?php $spotlight_3_network = json_decode(get_option('spotlight_3_network')); ?>
                            <input name="spotlight_3_network" id="spotlight_3_network" type="text" value="<?php echo !is_null($spotlight_3_network) ? $spotlight_3_network->link : ''; ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h3>Spotlight 4 (Network)</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>URL</label>
                            <label class="reset">x</label>
                            <?php $spotlight_4_network = json_decode(get_option('spotlight_4_network')); ?>
                            <input name="spotlight_4_network" id="spotlight_4_network" type="text" value="<?php echo !is_null($spotlight_4_network) ? $spotlight_4_network->link : ''; ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <hr/>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-12">
                        <h3>Force Trending on Home page</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Post ID</label>
                            <label class="reset">x</label>
                            <input name="force_most_viewed" id="force_most_viewed" type="number" value="<?php echo stripslashes(get_option('force_most_viewed')); ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div><!-- Force Trending on Home page -->
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-12">
                        <h3>Featured Article for Infinite Scroll</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Post ID</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_infinite_ID" id="tbm_featured_infinite_ID" type="text" value="<?php echo stripslashes(get_option('tbm_featured_infinite_ID')); ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div><!-- Featured Article for Infinite Scroll ID -->
            </div>
            <div class="col-md-12">
                <hr/>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-12">
                        <h3>DailyMotion Player</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Player ID</label>
                            <label class="reset">x</label>
                            <input name="tbm_floating_dm_player_id" id="tbm_floating_dm_player_id" type="text" value="<?php echo get_option('tbm_floating_dm_player_id'); ?>" placeholder="" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Playlist ID</label>
                            <label class="reset">x</label>
                            <input name="tbm_floating_dm_playlist_id" id="tbm_floating_dm_playlist_id" type="text" value="<?php echo get_option('tbm_floating_dm_playlist_id'); ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-12">
                        <div class="col-12">
                            <h3>GAM Ad Unit</h3>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>ID</label>
                            <input name="tbm_gam_ad_unit_id" id="tbm_gam_ad_unit_id" type="text" value="<?php echo get_option('tbm_gam_ad_unit_id'); ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <input type="submit" name="submit" id="submit-campaign" class="button button-primary" value="Save">
            </div>
        </div>
    </form>
<?php
}

function curl_post($post_url, $method = 'POST', $curl_post = array())
{
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

/* add_action('wp_head', function () {
    if (is_single()) {
        $current_blog_id = get_current_blog_id();
        $publisher_id = 1 === $current_blog_id ? 'GC_fd6c75ab568de8a0eac4f0bf23eff6aa87928ac2' : 'GC_ee6c4bd9831aaa81f108ff638f6555d120c09541';
    ?>
        <script>
            (function(c, e, n, o, i, r, s, t, u, a, h, f, l, d, p) {
                s = "querySelector";
                a = new Date;
                d = 0;
                c["GotChosenObject"] = o;
                c[o] = c[o] || function() {
                    (c[o].q = c[o].q || []).push(arguments);
                    r = r || c[o].q.filter(function(t) {
                        return t[0] === "init"
                    })[0][1];
                    p = function() {
                        try {
                            try {
                                h = [];
                                c[o].q[0][2].widgets.autoinstall.forEach(function(t) {
                                    h.push(t.selector)
                                });
                                h = h.join()
                            } catch (t) {
                                h = ".gcwp-carousel"
                            }
                            if (d < 6e4 && !e[s]("#" + r)) {
                                if (e[s](h)) {
                                    f = e.createElement(n);
                                    f.id = r;
                                    f.async = 1;
                                    f.src = i + "/gcjs/" + r + "/gc.js?cb=" + a.toJSON().slice(0, 13);
                                    e.head.appendChild(f)
                                } else {
                                    setTimeout(p, 100)
                                }
                                d += 100
                            }
                        } catch (t) {
                            throw new Error(t)
                        }
                    };
                    if (r) {
                        p()
                    }
                }
            })(window, document, "script", "gc", "https://cdn.gotchosen.com");
            gc("init", "<?php echo $publisher_id; ?>", {
                widgets: {
                    autoinstall: [{
                        selector: '.gcwp-carousel',
                        insertion: 'into'
                    }]
                }
            });
        </script>
<?php
    }
}); */

add_action('edit_form_after_title', function ($post) {
    $screen = get_current_screen();
    if ($screen->id != 'post') {
        return;
    }
    if ($post && ('post' != $post->post_type || 'publish' == $post->post_status)) {
        return;
    }
?>
    <div style="background-color: lightyellow; padding: 0.25rem 0.5rem">
        <h3>Checklist:</h3>
        <ol>
            <li>Does this article adhere EXACTLY to the Crib Notes for this publication?</li>
            <li>If it’s a news piece: Was it first published by another publication within the last hour?</li>
            <li>If it was first published over an hour ago, do you have an original & exclusive angle?!</li>
            <li>Are there any opps to add in a link to a relevant Observer newsletter?</li>
        </ol>
    </div>
<?php
});

add_action('admin_post_thumbnail_html', function ($content, $post_id, $thumbnail_id) {
    $html = '<div style="background-color: lightyellow; padding: 0.25rem">
    <em>Recommended size: 1200 x 630 (px)</em>
    </div>';
    return  $content . $html;
}, 10, 3);

// Coil - Monetize content
/* add_action('wp_head', function () {
    echo '<meta name="monetization" content="$ilp.uphold.com/68Q7DryfNX4d">';
}); */

class TBM_WP_HTML_Compression
{
    // Settings
    protected $compress_css = true;
    protected $compress_js = false;
    protected $info_comment = false;
    protected $remove_comments = true;

    // Variables
    protected $html;
    public function __construct($html)
    {
        if (!empty($html)) {
            $this->parseHTML($html);
        }
    }
    public function __toString()
    {
        return $this->html;
    }
    protected function bottomComment($raw, $compressed)
    {
        $raw = strlen($raw);
        $compressed = strlen($compressed);

        $savings = ($raw - $compressed) / $raw * 100;

        $savings = round($savings, 2);

        return '<!--HTML compressed, size saved ' . $savings . '%. From ' . $raw . ' bytes, now ' . $compressed . ' bytes-->';
    }
    protected function minifyHTML($html)
    {
        $pattern = '/<(?<script>script).*?<\/script\s*>|<(?<style>style).*?<\/style\s*>|<!(?<comment>--).*?-->|<(?<tag>[\/\w.:-]*)(?:".*?"|\'.*?\'|[^\'">]+)*>|(?<text>((<[^!\/\w.:-])?[^<]*)+)|/si';
        preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);
        $overriding = false;
        $raw_tag = false;
        // Variable reused for output
        $html = '';
        foreach ($matches as $token) {
            $tag = (isset($token['tag'])) ? strtolower($token['tag']) : null;

            $content = $token[0];

            if (is_null($tag)) {
                if (!empty($token['script'])) {
                    $strip = $this->compress_js;
                } else if (!empty($token['style'])) {
                    $strip = $this->compress_css;
                } else if ($content == '<!--wp-html-compression no compression-->') {
                    $overriding = !$overriding;

                    // Don't print the comment
                    continue;
                } else if ($this->remove_comments) {
                    if (!$overriding && $raw_tag != 'textarea') {
                        // Remove any HTML comments, except MSIE conditional comments
                        $content = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $content);
                    }
                }
            } else {
                if ($tag == 'pre' || $tag == 'textarea') {
                    $raw_tag = $tag;
                } else if ($tag == '/pre' || $tag == '/textarea') {
                    $raw_tag = false;
                } else {
                    if ($raw_tag || $overriding) {
                        $strip = false;
                    } else {
                        $strip = true;

                        // Remove any empty attributes, except:
                        // action, alt, content, src
                        $content = preg_replace('/(\s+)(\w++(?<!\baction|\balt|\bcontent|\bsrc)="")/', '$1', $content);

                        // Remove any space before the end of self-closing XHTML tags
                        // JavaScript excluded
                        $content = str_replace(' />', '/>', $content);
                    }
                }
            }

            if ($strip) {
                $content = $this->removeWhiteSpace($content);
            }

            $html .= $content;
        }

        return $html;
    }

    public function parseHTML($html)
    {
        $this->html = $this->minifyHTML($html);

        if ($this->info_comment) {
            $this->html .= "\n" . $this->bottomComment($html, $this->html);
        }
    }

    protected function removeWhiteSpace($str)
    {
        $str = str_replace("\t", ' ', $str);
        $str = str_replace("\n",  '', $str);
        $str = str_replace("\r",  '', $str);

        while (stristr($str, '  ')) {
            $str = str_replace('  ', ' ', $str);
        }

        return $str;
    }
}

function tbm_wp_html_compression_finish($html)
{
    return new TBM_WP_HTML_Compression($html);
}

function tbm_wp_html_compression_start()
{
    ob_start('tbm_wp_html_compression_finish');
}
add_action('get_header', 'tbm_wp_html_compression_start');



/*
add_action( 'admin_enqueue_scripts', 'tbm_theme_options_enqueue_admin_scripts' );

function tbm_theme_options_enqueue_admin_scripts( $screen ) {
    $screen = get_current_screen();

    if ( 'post' === $screen->base || 'edit' === $screen->base ) {
        wp_enqueue_script( 'tbm-to-admin-script', plugins_url( 'js/scripts.js', __FILE__ ), array( 'jquery' ), NULL, true );
        wp_localize_script( 'tbm-to-admin-script', 'tbm_theme_options', tbm_theme_options_get_plugin_settings() );
    }
}

function tbm_theme_options_get_plugin_settings() {
    $screen = get_current_screen();

    return array(
        'placeholder' => esc_html__( 'Filter %s', 'admin-category-filter' ),
        'screenName'  => $screen->base
    );
}

add_action( 'wp_ajax_tbm_theme_options_get_cat_count', 'tbm_theme_options_get_cat_count' );
add_action( 'wp_ajax_nopriv_tbm_theme_options_get_cat_count', 'tbm_theme_options_get_cat_count' );

function tbm_theme_options_get_cat_count() {
    $term = get_term( $_REQUEST['tax_id'], 'category' );
    $tax_name = $_REQUEST['tax_name'];
    if ( $term ) {
        wp_send_json_success( array( 'tax_name' => $tax_name,  'count' => $term->count ) );
//        echo json_encode(  );
    } else {
        wp_send_json_success( array( 'tax_name' => $tax_name,  'count' => 0 ) );
    }
    wp_die();
}
 *
 */


// JS to make BB sticky
add_action('wp_footer', function () {
    if (
        is_page_template('page-templates/brag-observer.php') ||
        is_page_template('page-templates/brag-client-club.php') ||
        is_page_template('page-templates/brag-client-rsvp-event.php')
    )
        return;
?>
    <script>
        fusetag.onSlotRenderEnded((e) => {
            if (e.slotId === 'fuse-slot-22339066295-1' || e.slotId === 'fuse-slot-22339226185-1') {
                googletag.pubads().addEventListener('slotRenderEnded', function(event) {
                    const slot = event.slot
                    if (slot.getSlotElementId() === 'fuse-slot-22339066295-1' || slot.getSlotElementId() === 'fuse-slot-22339226185-1') {
                        if (event.creativeId === 138373276463) {
                            const skin = document.getElementById('skin')
                            skin.style.setProperty('display', 'none', 'important')

                            const ad_billboard = parent.document.querySelector('.ad-billboard .mx-auto')

                            ad_billboard.style.position = 'fixed'
                            ad_billboard.style.zIndex = 999
                            ad_billboard.style.bottom = '15px'
                            ad_billboard.style.transform = 'translateX(-50%)';
                            ad_billboard.style.left = '50%';

                            setTimeout(function() {
                                ad_billboard.style.bottom = '0'
                                ad_billboard.style.position = 'relative'
                            }, 6000)
                        }
                    }
                })
            }
        })
    </script>
<?php
});

// Brand lift study
add_action('wp_footer', function () {
    if (is_page_template("page-templates/rs-awards-nominate-2021.php")) {
        return;
    }
?>
    <script>
        jQuery(document).ready(function($) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'json',
                cache: 'false',
                data: {
                    'action': 'tbm_set_cookie',
                    'key': 'tbm_sm_seen',
                    'value': 'true',
                    'duration': '<?php echo 60 * 60 * 24 * 30; ?>'
                }
            });
        }); {
            var tbm_e = document.cookie,
                tbm_t = tbm_e.indexOf("; tbm_sm_seen=");
            if (-1 == tbm_t) {
                {
                    const e = (e, t, n, s) => {
                            var c, o, r;
                            e.SMCX = e.SMCX || [], t.getElementById(s) || (o = (c = t.getElementsByTagName(n))[c.length - 1], (r = t.createElement(n)).type = "text/javascript", r.async = !0, r.id = s, r.src = "https://widget.surveymonkey.com/collect/website/js/tRaiETqnLgj758hTBazgd36CitCEEwoE44pTCPBWttcrfN2mODXNCsr6H61j_2BkMD.js", o.parentNode.insertBefore(r, o))
                        },
                        t = (e, t, n, s) => {
                            var c, o, r;
                            e.SMCX = e.SMCX || [], t.getElementById(s) || (o = (c = t.getElementsByTagName(n))[c.length - 1], (r = t.createElement(n)).type = "text/javascript", r.async = !0, r.id = s, r.src = "https://widget.surveymonkey.com/collect/website/js/tRaiETqnLgj758hTBazgd36CitCEEwoE44pTCPBWtteffxwhXTTNQIUFZGZf1MZH.js", o.parentNode.insertBefore(r, o))
                        };
                    null !== (() => {
                        var e = document.cookie,
                            t = e.indexOf("; tbm_v=");
                        if (-1 == t) {
                            if (0 != (t = e.indexOf("tbm_v="))) return null
                        } else {
                            t += 2;
                            var n = document.cookie.indexOf(";", t); - 1 == n && (n = e.length)
                        }
                        return decodeURI(e.substring(t + "tbm_v=".length, n))
                    })() ? (console.log("cookie"), e(window, document, "script", "smcx-sdk")) : (console.log("no cookie"))
                }
            }
        }
    </script>
    <style>
        .smcx-modal:before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin-top: -100%;
            margin-bottom: -100%;
            background: rgba(0, 0, 0, .75);
            margin-left: -100%;
            margin-right: -100%;
        }

        .smcx-modal-header,
        .smcx-modal>.smcx-modal-content,
        .smcx-widget-footer {
            background-color: #2E073E;
        }

        .smcx-modal-header {
            border-radius: 5px 5px 0 0;
        }

        .smcx-modal>.smcx-modal-content {
            border-radius: 0;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 10px !important;
        }

        @media (max-width: 600px) {
            .smcx-widget.smcx-modal>.smcx-modal-content {
                width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding: 0 2% !important;
            }
        }

        .smcx-widget-footer {
            border-radius: 0 0 5px 5px;
        }
    </style>
<?php
});
