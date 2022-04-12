<?php
class API
{

  protected $rest_api_keys;

  protected $plugin_name;

  public function __construct()
  {

    $this->plugin_name = 'brag_observer';

    $this->rest_api_keys = [
      'au.rollingstone.com' => '1fc08f46-3537-43f6-b5c1-c68704acf3fa',
      'tonedeaf.thebrag.com' => '3ce4efdd-a39c-4141-80f7-08a828500831',
      'dontboreus.thebrag.com' => '23455eda-057a-44b5-aa85-09b1222d4bd8',
      'theindustryobserver.thebrag.com' => 'e0738d93-cadb-4ca7-9b75-d0da76dcd8b4',
      'braze' => '7b9a5563-3736-4f80-9e4e-743d5f594577'
    ];

    // REST API
    add_action('rest_api_init', [$this, '_rest_api_init']);
  }

  /*
  * REST: API Endpoints
  */
  public function _rest_api_init()
  {
    register_rest_route($this->plugin_name . '/v1', '/get_topics', array(
      'methods' => 'GET',
      'callback' => [$this, 'rest_get_topics'],
      'permission_callback' => '__return_true',
    ));

    register_rest_route($this->plugin_name . '/v1', '/get_topic_by_slug', array(
      'methods' => 'GET',
      'callback' => [$this, 'rest_get_topic_by_slug'],
      'permission_callback' => '__return_true',
    ));

    register_rest_route($this->plugin_name . '/v1', '/sub_unsub', array(
      'methods' => 'POST',
      'callback' => [$this, 'rest_sub_unsub'],
      'permission_callback' => '__return_true',
    ));

    // Tastemaker
    register_rest_route($this->plugin_name . '/v1', '/get_tastemaker_form', array(
      'methods' => 'GET',
      'callback' => [$this, 'rest_get_tastemaker_form'],
      'permission_callback' => '__return_true',
    ));

    register_rest_route($this->plugin_name . '/v1', '/create_tastemaker_review', array(
      'methods' => 'POST',
      'callback' => [$this, 'rest_create_tastemaker_review'],
      'permission_callback' => '__return_true',
    ));

    // Lead Generator
    register_rest_route($this->plugin_name . '/v1', '/get_lead_generator_form', array(
      'methods' => 'GET',
      'callback' => [$this, 'rest_get_lead_generator_form'],
      'permission_callback' => '__return_true',
    ));

    register_rest_route($this->plugin_name . '/v1', '/create_lead_generator_response', array(
      'methods' => 'POST',
      'callback' => [$this, 'rest_create_lead_generator_response'],
      'permission_callback' => '__return_true',
    ));

    register_rest_route($this->plugin_name . '/v1', '/competitions', array(
      'methods' => 'GET',
      'callback' => [$this, 'rest_get_competition_articles'],
      'permission_callback' => '__return_true',
    ));

    register_rest_route($this->plugin_name . '/v1', '/get_my_subs', array(
      'methods' => 'GET',
      'callback' => [$this, 'rest_get_my_subs'],
      'permission_callback' => '__return_true',
    ));

    register_rest_route($this->plugin_name . '/v1', '/get_external_id', array(
      'methods' => 'GET',
      'callback' => [$this, 'rest_get_external_id'],
      'permission_callback' => '__return_true',
    ));

    register_rest_route($this->plugin_name . '/v1', '/latest_articles', array(
      'methods' => 'GET',
      'callback' => [$this, 'rest_latest_articles'],
      'permission_callback' => '__return_true',
    ));

    register_rest_route($this->plugin_name . '/v1', '/get_users_related_topics', array(
      'methods' => 'GET',
      'callback' => [$this, 'rest_get_users_related_topics'],
      'permission_callback' => '__return_true',
    ));
  }

  public function rest_get_users_related_topics()
  {
    global $wpdb;
    if (!isset($_GET['key']) || !$this->isRequestValid($_GET['key'])) {
      wp_send_json_error(['Invalid Request']);
      wp_die();
    }

    $email = isset($_GET['email']) ? trim($_GET['email']) : NULL;
    $user = get_user_by('email', $email);
    if (!$email) {
      wp_send_json_error();
      wp_die();
    }

    $slug = isset($_GET['slug']) ? trim($_GET['slug']) : NULL;
    if (is_null($slug) || '' == $slug) {
      wp_send_json_error();
      wp_die();
    }

    $list = $wpdb->get_row("SELECT c.id category_id, lc.list_id list_id FROM {$wpdb->prefix}observer_categories c
    JOIN {$wpdb->prefix}observer_list_categories lc ON c.id = lc.category_id
    JOIN {$wpdb->prefix}observer_lists l ON l.id = lc.list_id
    WHERE l.`slug` = '{$slug}' LIMIT 1");

    if (!$list) {
      wp_send_json_error();
      wp_die();
    }

    // wp_send_json_success($list);
    // wp_die();

    $count = isset($_GET['count']) ? absint($_GET['count']) : 3;

    $results = $wpdb->get_results("SELECT * FROM(
      SELECT DISTINCT l.id, l.title, l.slug, l.image_url FROM `{$wpdb->prefix}observer_lists` l
      JOIN `{$wpdb->prefix}observer_list_categories` oc ON l.id = oc.list_id
      WHERE
        l.`status` = 'active'
        AND oc.list_id != '{$list->list_id}'
        AND oc.list_id != 48
        AND oc.category_id = '{$list->category_id}'
        AND oc.list_id NOT IN (
          SELECT oc.list_id FROM `{$wpdb->prefix}observer_list_categories` oc
          -- JOIN `{$wpdb->prefix}observer_categories` c ON c.id = oc.category_id
          JOIN `{$wpdb->prefix}observer_subs` s ON oc.list_id = s.list_id
          WHERE s.status = 'subscribed' AND s.user_id = '{$user->ID}'
        )
      ORDER BY l.sub_count DESC
      LIMIT 5
      ) a
      ORDER BY RAND()
      LIMIT {$count}");

    if (!$results) {
      wp_send_json_error();
      wp_die();
    }

    wp_send_json_success($results);
    wp_die();
  }

  public function rest_latest_articles()
  {
    $news_args = array(
      'post_status' => 'publish',
      'post_type' => array('post', 'snaps', 'dad'),
      'ignore_sticky_posts' => 1,
      'posts_per_page' => 20,
    );
    $news_query = new WP_Query($news_args);
    $results = [];
    if ($news_query->have_posts()) :
      while ($news_query->have_posts()) :
        $news_query->the_post();

        $metadesc = get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);
        $excerpt = trim($metadesc) != '' ? $metadesc : string_limit_words(get_the_excerpt(), 25);

        $image_id = get_post_thumbnail_id(get_the_ID());
        $image_src = wp_get_attachment_image_src($image_id, 'medium');

        $results[] = [
          'title' => get_the_title(),
          'excerpt' => $excerpt,
          'url' => get_the_permalink(),
          'image_url' => $image_src[0],
        ];

      endwhile;
    endif;

    echo json_encode($results, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    die();
  }

  public function rest_get_external_id()
  {
    global $wpdb;
    if (!isset($_GET['email'])) {
      wp_send_json_error(['Invalid Request']);
      wp_die();
    }

    $email = isset($_GET['email']) ? trim($_GET['email']) : NULL;
    $user = get_user_by('email', $email);
    if (!$email) {
      die();
    }

    $auth0_id = null;
    if (get_user_meta($user->ID, $wpdb->prefix . 'auth0_id')) {
      $auth0_id = get_user_meta($user->ID, $wpdb->prefix . 'auth0_id', true);
    } else if (get_user_meta($user->ID, 'wp_auth0_id')) {
      $auth0_id = get_user_meta($user->ID, 'wp_auth0_id', true);
    }
    if (!is_null($auth0_id)) {
      echo json_encode($auth0_id);
    } else {
    }
  }

  /*
  * REST: Get Tastemaker Form
  */
  public function rest_get_tastemaker_form()
  {

    if (!isset($_GET['key']) || !$this->isRequestValid($_GET['key'])) {
      wp_send_json_error(['Invalid Request']);
      wp_die();
    }

    unset($_GET['key']);

    $args = $_GET;

    $tastemaker = new Tastemaker();
    $form = $tastemaker->get_form($args);
    if ($form) {
      wp_send_json_success($form);
    }
  }

  /*
  * REST: Create Tastemaker Review
  */
  public function rest_create_tastemaker_review()
  {

    if (!isset($_POST['key']) || !$this->isRequestValid($_POST['key'])) {
      wp_send_json_error(['Invalid Request']);
      wp_die();
    }

    $tastemaker = new Tastemaker();

    // error_log( print_r( $_POST, true ) );
    return $tastemaker->save_tastemaker_review($_POST, true);
  }

  /*
  * REST: Get Lead Generator Form
  */
  public function rest_get_lead_generator_form()
  {

    if (!isset($_GET['key']) || !$this->isRequestValid($_GET['key'])) {
      wp_send_json_error(['Invalid Request']);
      wp_die();
    }

    unset($_GET['key']);

    $args = $_GET;

    $lead_generator = new LeadGenerator();
    $form = $lead_generator->get_form($args);
    if ($form) {
      wp_send_json_success($form);
    }
  }

  /*
  * REST: Create Lead Generator Review
  */
  public function rest_create_lead_generator_response()
  {

    if (!isset($_POST['key']) || !$this->isRequestValid($_POST['key'])) {
      wp_send_json_error(['Invalid Request']);
      wp_die();
    }

    $lead_generator = new LeadGenerator();

    // error_log( print_r( $_POST, true ) );
    return $lead_generator->save_lead_generator_review($_POST, true);
  }

  /*
  * REST: Get Topics
  */
  public function rest_get_topics($obj = null)
  {

    if (is_null($obj) && (!isset($_GET['key']) || !$this->isRequestValid($_GET['key']))) {
      wp_send_json_error(['Invalid Request']);
      wp_die();
    }

    global $wpdb;

    $status = isset($_GET['status']) && in_array($_GET['status'], ['active', 'soon']) ? trim($_GET['status']) : 'active';

    $lists_query = "SELECT id, title, keywords, slug, image_url, frequency, description FROM {$wpdb->prefix}observer_lists WHERE status = '{$status}'";
    if (isset($_GET['id']) && '' != trim($_GET['id'])) {
      $id = absint($_GET['id']);
      $lists_query .= " AND id = '{$id}'";
    }
    if (isset($_GET['site']) && '' != trim($_GET['site'])) {
      $related_site = trim($_GET['site']);
      $lists_query .= " AND related_site = '{$related_site}'";
    }
    $lists_query .= " ORDER BY sub_count DESC";
    $lists = $wpdb->get_results($lists_query);

    $my_sub_lists = [];
    if (isset($_GET['email'])) {
      $user = get_user_by('email', sanitize_text_field($_GET['email']));
      if ($user) {
        $my_subs = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user->ID}' AND status = 'subscribed' ");
        $my_sub_lists = wp_list_pluck($my_subs, 'list_id');
      }
    }

    $return = [];

    foreach ($lists as $list) {
      $return[] = [
        'id' => $list->id,
        'title' => $list->title,
        'keywords' => $list->keywords,
        'link' => home_url('/observer/' . $list->slug . '/'),
        'image_url' => $list->image_url,
        'description' => $list->description,
        'subscribed' => in_array($list->id, $my_sub_lists),
        'frequency' => $list->frequency,
      ];
    }

    wp_send_json_success($return);
  } // rest_get_topics() }}

  /*
  * REST: Get Topic By Slug
  */
  public function rest_get_topic_by_slug($obj = null)
  {

    if (is_null($obj) && (!isset($_GET['key']) || !$this->isRequestValid($_GET['key']))) {
      wp_send_json_error(['Invalid Request']);
      wp_die();
    }

    global $wpdb;

    $slug = isset($_GET['slug']) ? trim($_GET['slug']) : null;

    if (is_null($slug) || '' == $slug) {
      wp_send_json_error(['Invalid Request']);
      wp_die();
    }

    $list_query = "SELECT id, title, slug, image_url, status, intro_text, hero_image_url, frequency, description FROM {$wpdb->prefix}observer_lists WHERE slug = '{$slug}'";
    $list = $wpdb->get_row($list_query);

    if ('soon' == $list->status) {
      $list->votes_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}observer_votes v WHERE v.list_id = {$list->id}");
    }

    $list->link = home_url('/observer/' . $list->slug . '/');

    wp_send_json_success($list);
  } // rest_get_topic_by_slug() }}

  /*
  * REST: Sub/Unsub
  */
  public function rest_sub_unsub($request_data)
  {
    $formData = $request_data->get_params();

    return $this->sub_unsub($formData);
  }

  public function sub_unsub($formData)
  {

    if (!is_user_logged_in()) {
      if (!isset($formData['email']) || '' == trim($formData['email']) || !is_email($formData['email'])) {
        wp_send_json_error(['error' => ['message' => 'Please enter valid email address.']]);
        wp_die();
      }
    }

    if (isset($formData['status']) && isset($formData['list'])) {
      if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
      } else if (isset($formData['email'])) {
        $user = get_user_by('email', $formData['email']);
        if ($user) {
          $user_id = $user->ID;
        } else {
          $user_id = wp_insert_user(
            array(
              'user_login' => trim($formData['email']),
              'user_pass' => NULL,
              'user_email' => trim($formData['email']),
              'first_name' => '',
              'last_name' => '',
              'user_registered' => date('Y-m-d H:i:s'),
              'role' => 'subscriber'
            )
          );
          $new_user = true;
          update_user_meta($user_id, 'is_activated', 1);
        }
      }

      if ($user_id) {
        global $wpdb;

        $list = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}observer_lists l WHERE id = '{$formData['list']}' LIMIT 1");
        $share_url = $list->slug ? home_url('/observer/' . $list->slug . '/') : home_url('/observer/');
        $share_message = '';
        /*  '<div class="d-flex align-items-center mt-2">
          <div>Share the joy</div>
          <div><a class="btn btn-default btn-outline-primary btn-share mx-1" href="http://www.facebook.com/share.php?u=' . $share_url . '" target="_blank"><i class="fab fa-facebook-f"></i></a></div>
          <div><a class="btn btn-default btn-outline-info btn-share mx-1" href="https://twitter.com/share?url=' . $share_url . '&amp;text=' . urlencode($list->title) . ' target="_blank"><i class="fab fa-twitter"></i></a></div>
          </div>
        '; */

        $check_sub = $wpdb->get_row("SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '{$formData['list']}' LIMIT 1");
        if ($check_sub) { // Already subscribed / unsubscribed, just update
          $status = isset($formData['status']) ? $formData['status'] : 'subscribed';

          $update_data = [
            'status' => $status, // 'subscribed',
            'status_mailchimp' => NULL,
          ];

          if ('subscribed' == $status)
            $update_data['subscribed_at'] = current_time('mysql');

          if ('unsubscribed' == $status)
            $update_data['unsubscribed_at'] = current_time('mysql');

          if (isset($formData['source'])) {
            $update_data['source'] = sanitize_text_field($formData['source']);
          }

          $wpdb->update(
            $wpdb->prefix . 'observer_subs',
            $update_data,
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
            'subscribed_at' => current_time('mysql'),
          ];

          if (isset($formData['source'])) {
            $insert_data['source'] = sanitize_text_field($formData['source']);
          }

          $wpdb->insert($wpdb->prefix . 'observer_subs', $insert_data);
        }

        /**
         * Add to queue for Braze
         */
        require_once  __DIR__ . '/cron.class.php';
        $task = 'update_newsletter_interests';
        $cron = new Cron();
        if (!$cron->getActiveBrazeQueueTask($user_id, $task)) {
          $cron->addToBrazeQueue($user_id, $task);
        }

        if (isset($new_user) && $new_user) {
          if (isset($formData['return']) && 'bool' == $formData['return']) {
            return true;
          } else {
            return wp_send_json_success(
              ['message' => '<div>Thank you!</div>' . $share_message]
            );
            wp_die();
          }
        }
        if (isset($formData['return']) && 'bool' == $formData['return']) {
          return true;
        } else {
          return wp_send_json_success(
            ['message' => '<div>Thank you!</div>' . $share_message]
          );
          wp_die();
        }
      }
    }
    wp_send_json_error(['error' => ['message' => 'Whoops, like something unexpected happened on our side of things. Feel free to refresh your browser and give it another shot!']]);
    wp_die();
  }

  /*
  * REST: Validate Key for API
  */
  private function isRequestValid($key)
  {
    return isset($key) && !is_null($key) && in_array($key, $this->rest_api_keys);
  }

  /*
  * REST: Competition Articles
  */
  function rest_get_competition_articles($data)
  {

    $timezone = new DateTimeZone('Australia/Sydney');

    $return = array();
    $today = date('Ymd');

    $args = [
      'post_type' => array('any'),
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
    $posts = new WP_Query($args);

    global $post;
    if ($posts->have_posts()) {
      while ($posts->have_posts()) {
        $posts->the_post();
        $url = get_the_permalink();
        $author = get_field('Author') ? get_field('Author') : get_the_author();

        $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');

        $return[] = array(
          'title' => get_the_title(),
          'link' => $url,
          'competition_end_date' => mysql2date('Y-m-d', get_post_meta($post->ID, 'competition_end_date', true), false),
          'lead_generator' => get_post_meta($post->ID, 'has_lead_generator', true),
          'image' => $src[0],
        );
      }
    }
    return $return;
  }

  /*
  * REST: Get Topics
  */
  public function rest_get_my_subs($obj = null)
  {

    if (is_null($obj) && (!isset($_GET['key']) || !$this->isRequestValid($_GET['key'])) || !isset($_GET['email'])) {
      wp_send_json_error(['Invalid Request']);
      wp_die();
    }

    global $wpdb;

    $status = isset($_GET['status']) && in_array($_GET['status'], ['active', 'soon']) ? trim($_GET['status']) : 'active';

    if (isset($_GET['email'])) {
      $user = get_user_by('email', sanitize_text_field($_GET['email']));
      if (!$user) {
        wp_send_json_error(['Not found']);
        wp_die();
      }
    }

    $lists_query = "SELECT
      l.id,
      l.title,
      l.slug,
      l.image_url,
      l.frequency,
      l.description 
    FROM {$wpdb->prefix}observer_lists l 
      JOIN {$wpdb->prefix}observer_subs s ON s.list_id = l.id
    WHERE
      l.status = '{$status}' AND
      s.status = 'subscribed' AND
      s.user_id = '{$user->ID}'
    ";
    if (isset($_GET['site']) && '' != trim($_GET['site'])) {
      $related_site = trim($_GET['site']);
      $lists_query .= " AND l.related_site = '{$related_site}'";
    }
    $lists = $wpdb->get_results($lists_query);

    $return = [];

    foreach ($lists as $list) {
      $return[] = [
        'id' => $list->id,
        'title' => $list->title,
        'link' => home_url('/observer/' . $list->slug . '/'),
        'image_url' => $list->image_url,
        'description' => $list->description,
        'frequency' => $list->frequency,
      ];
    }

    wp_send_json_success($return);
  } // rest_get_my_subs() }}
}

new API();
