<?php
/*
   Plugin Name: SSM Articles JSON
   Plugin URI:
   description:
   Version: 1.0
   Author: Sachin Patel
   Author URI:
*/
function ssm_articles_json_func($data) {
    $return = array();

    $posts_per_page = isset( $_GET['size'] ) ? (int) $_GET['size'] : 10;
    $paged = isset( $_GET['page'] ) ? (int) $_GET['page'] : 1;

    $timezone = new DateTimeZone('Australia/Sydney');

    if ( isset( $_GET['after'] ) ) :
        $after_dt = new DateTime( date_i18n( 'Y-m-d H:i:s', strtotime( trim( $_GET['after'] ) ) ) );
        $after_dt->setTimezone($timezone);
        $after = $after_dt->format( 'Y-m-d H:i:s' );
    else :
        $after = NULL;
    endif;

    if ( isset( $_GET['before'] ) ) :
        $before_dt = new DateTime( date_i18n( 'Y-m-d H:i:s', strtotime( trim( $_GET['before'] ) ) ) );
        $before_dt->setTimezone($timezone);
        $before = $before_dt->format( 'Y-m-d H:i:s' );
    else :
        $before = NULL;
    endif;

    if ( is_null( $after ) || is_null( $before ) )
        return $return;

    $posts = new WP_Query( array(
        'date_query' => array(
            'after' => $after,
            'before' => $before,
        ),
//        'post_type' => array( 'post', 'snaps', 'podcast', 'issue', 'freeshit' ),
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
    ) );

    global $post;
    if( $posts->have_posts() ) {
        while( $posts->have_posts() ) {
            $posts->the_post();
            $url = get_the_permalink();
            $author = get_field('Author') ? get_field('Author') : get_the_author();

            $category_names = $tag_names = array();

            $post_categories = wp_get_post_categories( get_the_ID() );
            if ( count( $post_categories ) > 0 ) :
                foreach ( $post_categories as $c ) :
                    $cat = get_category( $c );
                    array_push( $category_names, $cat->name );
                endforeach;
            endif;

            $post_tags = wp_get_post_tags( get_the_ID() );
            if ( count( $post_tags ) > 0 ) :
                foreach ( $post_tags as $t ) :
                    $tag = get_tag( $t );
                    array_push( $tag_names, $tag->name );
                endforeach;
            endif;

            $content = apply_filters( 'the_content', get_the_content() );
            $content .= '<p>The article was originally published on <a href="' . get_the_permalink() . '" target="_blank">' . get_bloginfo_rss('name') . '</a></p>';

            $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

            $return[] = array(
                'title' => get_the_title(),
                'link' => $url,
//                'guid' => get_the_guid(),
                'publish_date' => mysql2date('c', get_post_time('c', true), false),
                'description' => get_the_excerpt(),
                'image' => $src[0],
                'author' => $author,
                'categories' => $category_names,
                'tags' => $tag_names,
                'content' => $content,
            );
        }
    }
    return $return;
}

/*
 * Promoter Post Meta Articles
 */
function ssm_articles_promoter_json_func($data) {
    $return = array();

    $posts_per_page = isset( $_GET['size'] ) ? (int) $_GET['size'] : 10;
    $paged = isset( $_GET['page'] ) ? (int) $_GET['page'] : 1;
    $promoter = isset( $_GET['promoter'] ) ? $_GET['promoter'] : NULL;

    if ( is_null( $promoter ) ) :
        return $return;
    endif;

    $timezone = new DateTimeZone('Australia/Sydney');

    $posts = new WP_Query( array(
        'post_type' => array( 'post', ),
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'meta_key' => 'promoter',
        'meta_value' => $promoter
    ) );

    global $post;
    if( $posts->have_posts() ) {
        while( $posts->have_posts() ) {
            $posts->the_post();
            $url = get_the_permalink();
            $author = get_field('Author') ? get_field('Author') : get_the_author();

            $category_names = $tag_names = array();

            $post_categories = wp_get_post_categories( get_the_ID() );
            if ( count( $post_categories ) > 0 ) :
                foreach ( $post_categories as $c ) :
                    $cat = get_category( $c );
                    array_push( $category_names, $cat->name );
                endforeach;
            endif;

            $post_tags = wp_get_post_tags( get_the_ID() );
            if ( count( $post_tags ) > 0 ) :
                foreach ( $post_tags as $t ) :
                    $tag = get_tag( $t );
                    array_push( $tag_names, $tag->name );
                endforeach;
            endif;

            $content = apply_filters( 'the_content', get_the_content() );

            $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

            $return[] = array(
                'title' => get_the_title(),
                'link' => $url,
//                'guid' => get_the_guid(),
                'publish_date' => mysql2date('c', get_post_time('c', true), false),
                'description' => get_the_excerpt(),
                'image' => $src[0],
                'author' => $author,
                'categories' => $category_names,
                'tags' => $tag_names,
                'content' => $content,
            );
        }
    }
    return $return;
}
add_action( 'rest_api_init', function () {
    register_rest_route( 'api/v1', '/articles', array(
        'methods' => 'GET',
        'callback' => 'ssm_articles_json_func',
    ) );
    register_rest_route( 'api/v1', '/articles/promoter', array(
        'methods' => 'GET',
        'callback' => 'ssm_articles_promoter_json_func',
    ) );

    register_rest_route('api/v2', '/articles', array(
        'methods' => 'GET',
        'callback' => 'ssm_articles_json_func_v2',
    ));
} );

function ssm_articles_json_func_v2($data)
{
    $return = array();

    $posts_per_page = isset($_GET['size']) ? (int) $_GET['size'] : 10;
    $paged = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

    $timezone = new DateTimeZone('Australia/Sydney');

    $args = [
        //        'post_type' => array( 'post', 'photo_gallery' ),
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
    ];

    if ( $offset > 0 ) {
        $args['offset'] = $offset;
    }

    $posts = new WP_Query($args);

    global $post;
    if ($posts->have_posts()) {
        while ($posts->have_posts()) {
            $posts->the_post();
            $url = get_the_permalink();
            $author = get_field('Author') ? get_field('Author') : get_the_author();

            $category_names = $tag_names = array();

            $post_categories = wp_get_post_categories(get_the_ID());
            if (count($post_categories) > 0) :
                foreach ($post_categories as $c) :
                    $cat = get_category($c);
                    array_push($category_names, $cat->name);
                endforeach;
            endif;

            $post_tags = wp_get_post_tags(get_the_ID());
            if (count($post_tags) > 0) :
                foreach ($post_tags as $t) :
                    $tag = get_tag($t);
                    array_push($tag_names, $tag->name);
                endforeach;
            endif;

            $content = apply_filters('the_content', get_the_content());

            $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');

            $return[] = array(
                'ID' => get_the_ID(),
                'title' => get_the_title(),
                'link' => $url,
                'guid' => get_the_guid(),
                'publish_date' => mysql2date('c', get_post_time('c', true), false),
                'description' => get_the_excerpt(),
                'image' => $src[0],
                'author' => $author,
                'categories' => $category_names,
                'tags' => $tag_names,
                // 'content' => $content,
                'site' => get_bloginfo( 'name' ),
            );
        }
    }
    return $return;
}

// Get author's name from article Edit URL or ID
function ssm_article_author_json_func() {
    $article_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : NULL;
    if ( is_null( $article_id ) ) {
        if ( isset( $_GET['url'] ) ) {
            $url_parts = parse_url( $_GET['url'] );
            parse_str( $url_parts['query'], $query );
            $article_id = (int) $query['post'];
        }
    }
    if ( is_null( $article_id ) )
        return;

    $post = get_post( $article_id );
    $author = get_the_author_meta( 'display_name', $post->post_author );

    if ( get_field('Author', $article_id ) ) {
        $author .= ' (' . get_field('Author', $article_id ) . ')';
    }
    return $author;
}
add_action( 'rest_api_init', function () {
    register_rest_route( 'api/v1', '/article/author', array(
        'methods' => 'GET',
        'callback' => 'ssm_article_author_json_func',
    ) );
} );

/*
* Articles Search by keyword
*/
function ssm_articles_search_func($data) {
    $return = array();
    $posts_per_page = isset( $_GET['size'] ) ? (int) $_GET['size'] : 10;
    $timezone = new DateTimeZone('Australia/Sydney');
    if ( isset( $_GET['s'] ) ) :
        $s = $_GET['s'];
    else :
        $s = NULL;
    endif;

    if ( is_null( $s ) )
        return $return;

    $posts = new WP_Query( array(
      's' => $s,
      'post_type' => array( 'post', 'photo_gallery' ),
      'post_status' => 'publish',
      'posts_per_page' => $posts_per_page,
    ) );

    global $post;
    if( $posts->have_posts() ) {
        while( $posts->have_posts() ) {
            $posts->the_post();
            $url = get_the_permalink();
            $author = get_field('Author') ? get_field('Author') : get_the_author();

            $category_names = $tag_names = array();

            $post_categories = wp_get_post_categories( get_the_ID() );
            if ( count( $post_categories ) > 0 ) :
                foreach ( $post_categories as $c ) :
                    $cat = get_category( $c );
                    array_push( $category_names, $cat->name );
                endforeach;
            endif;

            $post_tags = wp_get_post_tags( get_the_ID() );
            if ( count( $post_tags ) > 0 ) :
                foreach ( $post_tags as $t ) :
                    $tag = get_tag( $t );
                    array_push( $tag_names, $tag->name );
                endforeach;
            endif;

            $content = apply_filters( 'the_content', get_the_content() );

            $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium_large' );

            $return[] = array(
                'title' => get_the_title(),
                'link' => $url,
//                'guid' => get_the_guid(),
                'publish_date' => mysql2date('c', get_post_time('c', true), false),
                'description' => get_the_excerpt(),
                'image' => $src[0],
                'author' => $author,
                'categories' => $category_names,
                'tags' => $tag_names,
                'content' => $content,
            );
        }
    }
    return $return;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'api/v1', '/articles/search', array(
        'methods' => 'GET',
        'callback' => 'ssm_articles_search_func',
    ) );
} );
