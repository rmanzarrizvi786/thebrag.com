<?php
class API {

  protected $rest_api_keys;

  protected $plugin_name;

  public function __construct() {

    $this->plugin_name = 'brag_observer';

    $this->rest_api_keys =[
      'au.rollingstone.com' => '1fc08f46-3537-43f6-b5c1-c68704acf3fa',
      'tonedeaf.thebrag.com' => '3ce4efdd-a39c-4141-80f7-08a828500831',
      'dontboreus.thebrag.com' => '23455eda-057a-44b5-aa85-09b1222d4bd8',
    ];

    // REST API
    add_action( 'rest_api_init', [ $this, '_rest_api_init' ] );
  }

  /*
  * REST: API Endpoints
  */
  public function _rest_api_init() {
    register_rest_route( $this->plugin_name . '/v1', '/get_topics', array(
      'methods' => 'GET',
      'callback' => [ $this, 'rest_get_topics' ],
      'permission_callback' => '__return_true',
    ) );

    register_rest_route( $this->plugin_name . '/v1', '/get_topic_by_slug', array(
      'methods' => 'GET',
      'callback' => [ $this, 'rest_get_topic_by_slug' ],
      'permission_callback' => '__return_true',
    ) );

    register_rest_route( $this->plugin_name . '/v1', '/sub_unsub', array(
      'methods' => 'POST',
      'callback' => [ $this, 'rest_sub_unsub' ],
      'permission_callback' => '__return_true',
    ) );

    // Tastemaker
    register_rest_route( $this->plugin_name . '/v1', '/get_tastemaker_form', array(
      'methods' => 'GET',
      'callback' => [ $this, 'rest_get_tastemaker_form' ],
      'permission_callback' => '__return_true',
    ) );

    register_rest_route( $this->plugin_name . '/v1', '/create_tastemaker_review', array(
      'methods' => 'POST',
      'callback' => [ $this, 'rest_create_tastemaker_review' ],
      'permission_callback' => '__return_true',
    ) );

    // Lead Generator
    register_rest_route( $this->plugin_name . '/v1', '/get_lead_generator_form', array(
      'methods' => 'GET',
      'callback' => [ $this, 'rest_get_lead_generator_form' ],
      'permission_callback' => '__return_true',
    ) );

    register_rest_route( $this->plugin_name . '/v1', '/create_lead_generator_response', array(
      'methods' => 'POST',
      'callback' => [ $this, 'rest_create_lead_generator_response' ],
      'permission_callback' => '__return_true',
    ) );

    register_rest_route( $this->plugin_name . '/v1', '/competitions', array(
      'methods' => 'GET',
      'callback' => [ $this, 'rest_get_competition_articles' ],
      'permission_callback' => '__return_true',
    ) );
  }

  /*
  * REST: Get Tastemaker Form
  */
  public function rest_get_tastemaker_form() {

    if ( ! isset( $_GET['key'] ) || ! $this->isRequestValid( $_GET['key'] ) ) {
      wp_send_json_error( [ 'Invalid Request' ] ); wp_die();
    }

    unset( $_GET['key'] );

    $args = $_GET;

    $tastemaker = new Tastemaker();
    $form = $tastemaker->get_form( $args );
    if ( $form ) {
      wp_send_json_success( $form );
    }
  }

  /*
  * REST: Create Tastemaker Review
  */
  public function rest_create_tastemaker_review() {

    if ( ! isset( $_POST['key'] ) || ! $this->isRequestValid( $_POST['key'] ) ) {
      wp_send_json_error( [ 'Invalid Request' ] ); wp_die();
    }

    $tastemaker = new Tastemaker();

    // error_log( print_r( $_POST, true ) );
    return $tastemaker->save_tastemaker_review( $_POST, true );
  }

  /*
  * REST: Get Lead Generator Form
  */
  public function rest_get_lead_generator_form() {

    if ( ! isset( $_GET['key'] ) || ! $this->isRequestValid( $_GET['key'] ) ) {
      wp_send_json_error( [ 'Invalid Request' ] ); wp_die();
    }

    unset( $_GET['key'] );

    $args = $_GET;

    $lead_generator = new LeadGenerator();
    $form = $lead_generator->get_form( $args );
    if ( $form ) {
      wp_send_json_success( $form );
    }
  }

  /*
  * REST: Create Lead Generator Review
  */
  public function rest_create_lead_generator_response() {

    if ( ! isset( $_POST['key'] ) || ! $this->isRequestValid( $_POST['key'] ) ) {
      wp_send_json_error( [ 'Invalid Request' ] ); wp_die();
    }

    $lead_generator = new LeadGenerator();

    // error_log( print_r( $_POST, true ) );
    return $lead_generator->save_lead_generator_review( $_POST, true );
  }

  /*
  * REST: Get Topics
  */
  public function rest_get_topics($obj = null) {

    if ( is_null( $obj ) && ( ! isset( $_GET['key'] ) || ! $this->isRequestValid( $_GET['key'] ) ) ) {
      wp_send_json_error( [ 'Invalid Request' ] ); wp_die();
    }

    global $wpdb;
    
    $status = isset( $_GET['status'] ) && in_array( $_GET['status'], [ 'active', 'soon'] ) ? trim( $_GET['status'] ) : 'active';

    $lists_query = "SELECT id, title, slug, image_url, frequency, description FROM {$wpdb->prefix}observer_lists WHERE status = '{$status}'";
    if ( isset($_GET['id']) && '' != trim($_GET['id'])) {
      $id = absint($_GET['id']);
      $lists_query .= " AND id = '{$id}'";
    }
    $lists_query .= " ORDER BY sub_count DESC";
    $lists = $wpdb->get_results( $lists_query );

    $my_sub_lists = [];
    if( isset( $_GET['email'] ) ) {
      $user = get_user_by( 'email', sanitize_text_field( $_GET['email' ] ) );
      if ( $user ) {
        $my_subs = $wpdb->get_results( "SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user->ID}' AND status = 'subscribed' " );
        $my_sub_lists = wp_list_pluck( $my_subs, 'list_id' );
      }
    }

    $return = [];

    foreach( $lists as $list ) {
      $return[] = [
        'id' => $list->id,
        'title' => $list->title,
        'link' => home_url( '/observer/' . $list->slug . '/' ),
        'image_url' => $list->image_url,
        'description' => $list->description,
        'subscribed' => in_array( $list->id, $my_sub_lists ),
        'frequency' => $list->frequency,
      ];
    }

    wp_send_json_success( $return );
  } // rest_get_topics() }}

  /*
  * REST: Get Topic By Slug
  */
  public function rest_get_topic_by_slug($obj = null) {

    if ( is_null( $obj ) && ( ! isset( $_GET['key'] ) || ! $this->isRequestValid( $_GET['key'] ) ) ) {
      wp_send_json_error( [ 'Invalid Request' ] ); wp_die();
    }

    global $wpdb;
    
    $slug = isset( $_GET['slug'] ) ? trim( $_GET['slug'] ) : null;

    if ( is_null( $slug ) || '' == $slug ) {
      wp_send_json_error( [ 'Invalid Request' ] ); wp_die();
    }

    $list_query = "SELECT id, title, slug, image_url, status, intro_text, hero_image_url, frequency, description FROM {$wpdb->prefix}observer_lists WHERE slug = '{$slug}'";
    $list = $wpdb->get_row( $list_query );

    if ( 'soon' == $list->status ) {
      $list->votes_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}observer_votes v WHERE v.list_id = {$list->id}" );
    }

    $list->link = home_url( '/observer/' . $list->slug . '/' );

    wp_send_json_success( $list );
  } // rest_get_topic_by_slug() }}

  /*
  * REST: Sub/Unsub
  */
  public function rest_sub_unsub($request_data) {
    $formData = $request_data->get_params();

    return $this->sub_unsub( $formData );
  }

  public function sub_unsub( $formData ) {

    if ( ! is_user_logged_in() ) {
      if ( ! isset( $formData['email'] ) || '' == trim( $formData['email'] ) || ! is_email( $formData['email'] ) ) {
        wp_send_json_error( [ 'error' => [ 'message' => 'Please enter valid email address.' ] ] ); wp_die();
      }
    }

    if ( isset( $formData['status'] ) && isset( $formData['list'] ) ) {
      if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
      } else if ( isset( $formData['email'] ) ) {
        $user = get_user_by( 'email', $formData['email'] );
        if ( $user ) {
          $user_id = $user->ID;
        } else {
          $user_id = wp_insert_user(array(
            'user_login' => trim( $formData['email'] ),
            'user_pass' => NULL,
            'user_email' => trim( $formData['email'] ),
            'first_name' => '',
            'last_name' => '',
            'user_registered' => date('Y-m-d H:i:s'),
            'role' => 'subscriber'
            )
          );
          $new_user = true;
          update_user_meta( $user_id, 'is_activated', 1 );
        }
      }

      if ( $user_id ) {
        global $wpdb;

        $list = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}observer_lists l WHERE id = '{$formData['list']}' LIMIT 1" );
        $share_url = $list->slug ? home_url( '/observer/' . $list->slug . '/' ) : home_url( '/observer/' );
        $share_message = '<div class="d-flex align-items-center mt-2">
          <div>Share the joy</div>
          <div><a class="btn btn-default btn-outline-primary btn-share mx-1" href="http://www.facebook.com/share.php?u=' . $share_url . '" target="_blank"><i class="fab fa-facebook-f"></i></a></div>
          <div><a class="btn btn-default btn-outline-info btn-share mx-1" href="https://twitter.com/share?url=' . $share_url . '&amp;text=' . urlencode( $list->title ) . ' target="_blank"><i class="fab fa-twitter"></i></a></div>
          </div>
        ';

        $check_sub = $wpdb->get_row( "SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '{$formData['list']}' LIMIT 1" );
        if ( $check_sub ) { // Already subscribed / unsubscribed, just update
          $status = isset( $formData['status'] ) ? $formData['status'] : 'subscribed';

          $update_data = [
            'status' => $status, // 'subscribed',
            'status_mailchimp' => NULL,
          ];

          if ( 'subscribed' == $status )
            $update_data['subscribed_at'] = current_time( 'mysql' );

          if ( 'unsubscribed' == $status )
            $update_data['unsubscribed_at'] = current_time( 'mysql' );

          if ( isset( $formData['source'] ) ) {
            $update_data['source'] = sanitize_text_field( $formData['source'] );
          }

          $wpdb->update( $wpdb->prefix . 'observer_subs', $update_data,
            [
              'id' => $check_sub->id,
            ]
          );
        } else { // Existing subscription not found, insert

          $insert_data = [
            'user_id' => $user_id,
            'list_id' => $formData['list'],
            'status' => 'subscribed',
            'status_mailchimp' => NULL,
            'subscribed_at' => current_time( 'mysql' ),
          ];

          if ( isset( $formData['source'] ) ) {
            $insert_data['source'] = sanitize_text_field( $formData['source'] );
          }

          $wpdb->insert( $wpdb->prefix . 'observer_subs', $insert_data );
        }

        if( isset( $new_user ) && $new_user ) {
          return wp_send_json_success(
            [ 'message' => '<div>Thank you!</div>' . $share_message ]
          ); wp_die();
        }
        return wp_send_json_success(
          [ 'message' => '<div>Thank you!</div>' . $share_message ]
        ); wp_die();
      }
    }
    wp_send_json_error( [ 'error' => [ 'message' => 'Whoops, like something unexpected happened on our side of things. Feel free to refresh your browser and give it another shot!' ] ] ); wp_die();
  }

  /*
  * REST: Validate Key for API
  */
  private function isRequestValid( $key ) {
    return isset( $key ) && ! is_null ( $key ) && in_array( $key, $this->rest_api_keys );
  }

  /*
  * REST: Competition Articles
  */
  function rest_get_competition_articles($data) {

    $timezone = new DateTimeZone('Australia/Sydney');

    $return = array();
    $today = date('Ymd');

    $args = [
      'post_type' => array( 'any' ),
      'post_status' => 'publish',
      'orderby' => 'date',
      'order'   => 'DESC',
      'has_password'   => FALSE,
      'posts_per_page' => '-1',
      'meta_query' => [
        'relation' => 'AND',
        // 'lg_shortcode' => [
        //   'key' => 'has_lead_generator',
        //   'compare' => 'EXISTS',
        // ],
        'lg_close_date' => [
          'key' => 'competition_end_date',
          'value' => $today,
          'compare' => '>=',
          'type' => 'DATE',
        ]
      ]
    ];
    $posts = new WP_Query( $args );

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
          'competition_end_date' => mysql2date('Y-m-d', get_post_meta( $post->ID, 'competition_end_date', true ), false),
          'lead_generator' => get_post_meta( $post->ID, 'has_lead_generator', true ),
          'image' => $src[0],
        );
      }
    }
    return $return;
  }

}

new API();
