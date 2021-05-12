<?php
/**
 * Plugin Name: The Brag Observer Feed
 * Plugin URI: https://thebrag.media/
 * Description:
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI:
 */

class BragObserverFeed {

  protected $plugin_name;
  protected $plugin_slug;

  public function __construct() {

    $this->plugin_name = 'brag_observer';
    $this->plugin_slug = 'brag-observer';

    add_action('init', array($this, 'init'), 1);

    add_action( 'rest_api_init', [ $this, '_rest_api_init' ] );
  }

  public function init() {

  }
  public function admin_menu() {
   $main_menu = add_menu_page(
      'Brag Observer',
      'Brag Observer',
      'administrator',
      $this->plugin_slug,
      array( $this, 'index' ),
      'dashicons-email-alt2',
      10
   );
  }

  public function index() {

  } // Index

  public function _rest_api_init() {
    register_rest_route( 'api/v1', '/observer/articles', array(
      'methods' => 'GET',
      'callback' => [ $this, 'get_articles_for_topic' ],
    ) );
  }

  function get_articles_for_topic($data) {
    $topic = isset( $_GET['topic'] ) ? $_GET['topic'] : null;

    if ( is_null( $topic ) )
      return;

    $topics = array_map( 'trim', explode( ',', $topic ) );
    $keywords = implode( '+', $topics );

    $posts_per_page = isset( $_GET['size'] ) ? (int) $_GET['size'] : 10;

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

    $return = array();

    $args = [
      'date_query' => array(
        'after' => $after,
        'before' => $before,
      ),
      'fields' => 'ids',
      'post_type' => array( 'post', 'dad' ),
      'post_status' => 'publish',
      'posts_per_page' => $posts_per_page,
    ];

    $posts_keyword = new WP_Query(
      $args +
      [
        's' => $keywords,
        'exact' => isset( $_GET['exact'] ), // true,
        'sentence' => isset( $_GET['sentence'] ), // true,
      ]
    );

    // Category
    $posts_genre = new WP_Query(
      $args +
      [
        'tax_query' => [
          'relation' => 'OR',
          [
            'taxonomy' => 'genre',
            'field' => 'slug',
            'terms' => $topics,
          ],
          [
            'taxonomy' => 'artist',
            'field' => 'slug',
            'terms' => $topics,
          ],
          [
            'taxonomy' => 'category',
            'field' => 'slug',
            'terms' => $topics,
          ],
        ],
      ]
    );

    // Tags
    $posts_tags = new WP_Query(
      $args +
      [
        'tag' => $keywords
      ]
    );

    $combined_ids = array_merge(
      $posts_keyword->posts,
      $posts_genre->posts,
      $posts_tags->posts
    );
    $combined_ids = array_unique( $combined_ids );

    if ( count( $combined_ids ) < 1 )
      return;

    $posts = new WP_Query( [ 'post__in' => $combined_ids ] );

    global $post;
    if( $posts->have_posts() ) {
      while( $posts->have_posts() ) {
        $posts->the_post();
        $url = get_the_permalink();
        $author = get_field('Author') ? get_field('Author') : get_the_author();

        $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium_large' );

        $return[] = array(
          'title' => get_the_title(),
          'link' => $url,
          'publish_date' => mysql2date('c', get_post_time('c', true), false),
          'description' => get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true) ? : get_the_excerpt(),
          'image' => $src[0],
        );
      }
    }
    return $return;
  }

}

new BragObserverFeed();
