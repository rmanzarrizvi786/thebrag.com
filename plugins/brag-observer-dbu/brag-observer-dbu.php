<?php

/**
 * Plugin Name: The Brag Observer (DBU)
 * Plugin URI: https://thebrag.media/
 * Description:
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI:
 */

class BragObserverDBU
{

  protected $plugin_name;
  protected $plugin_slug;

  protected $rest_api_key;
  protected $api_url;

  public function __construct()
  {

    $this->plugin_name = 'brag_observer';
    $this->plugin_slug = 'brag-observer';

    $this->is_sandbox = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

    $this->rest_api_key = '23455eda-057a-44b5-aa85-09b1222d4bd8';
    if ($this->is_sandbox) {
      $this->api_url = 'https://the-brag.com/wp-json/brag_observer/v1/';
    } else {
      $this->api_url = 'https://thebrag.com/wp-json/brag_observer/v1/';
    }

    // Shortcodes
    add_shortcode('observer_tastemaker_form', [$this, 'shortcode_observer_tastemaker_form']);
    add_shortcode('observer_subscribe_genre', [$this, 'shortcode_subscribe_genre_form']);
    add_shortcode('observer_lead_generator_form', [$this, 'shortcode_observer_lead_generator_form']);

    // AJAX
    add_action('wp_ajax_save_tastemaker_review', [$this, 'save_tastemaker_review']);
    add_action('wp_ajax_nopriv_save_tastemaker_review', [$this, 'save_tastemaker_review']);

    add_action('wp_ajax_save_lead_generator_response', [$this, 'save_lead_generator_response']);
    add_action('wp_ajax_nopriv_save_lead_generator_response', [$this, 'save_lead_generator_response']);

    // REST API Endpoints
    add_action('rest_api_init', [$this, '_rest_api_init']);

    // Term metas for Genre
    add_action('genre_add_form_fields', [$this, 'add_observer_topic_field'], 10, 2);
    add_action('created_genre', [$this, 'save_genre_meta'], 10, 2);
    add_action('genre_edit_form_fields', [$this, 'edit_genre_field'], 10, 2);
    add_action('edited_genre', [$this, 'update_genre_meta'], 10, 2);
    add_filter('manage_edit-genre_columns', [$this, 'add_genre_column']);
    add_filter('manage_genre_custom_column', [$this, 'add_genre_column_content'], 10, 3);

    // Post meta for Observer topic, takes priority over Genre (term) meta
    add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
    add_action('save_post', [$this, 'save_post']);

    // AJAX
    add_action('wp_ajax_subscribe_observer', array($this, 'ajax_subscribe_observer'));
    add_action('wp_ajax_nopriv_subscribe_observer', array($this, 'ajax_subscribe_observer'));

    // Add post meta if shortcode is present in the content
    add_action('save_post', [$this, '_save_post'], 10, 3);
  }

  /*
  * Post meta box
  */
  public function add_meta_boxes()
  {
    $screens = ['post'];
    foreach ($screens as $screen) {
      add_meta_box(
        'observer-topic',
        'The Brag Observer topic',
        [$this, 'add_observer_topic_field'],
        $screen
      );
    }
  }

  public function save_post($post_id)
  {
    if (array_key_exists('observer-topic', $_POST)) {
      update_post_meta(
        $post_id,
        'observer-topic',
        $_POST['observer-topic']
      );
    }
  }

  /*
  * Add a field to New Genre Taxonomy for term meta
  */
  public function add_observer_topic_field($post)
  {
    $topics = wp_list_pluck($this->get_observer_topics(), 'title', 'id');
    asort($topics);

    $value = 0;
    if (isset($post) && isset($post->ID)) {
      $value = get_post_meta($post->ID, 'observer-topic', true);
    }

?><div class="form-field term-group">
      <label for="observer-topic">Observer topic</label>
      <select class="postform" id="observer-topic" name="observer-topic">
        <option value="">None</option>
        <?php foreach ($topics as $id => $title) : ?>
          <option value="<?php echo $id; ?>" <?php selected($value, $id); ?>><?php echo $title; ?></option>
        <?php endforeach; ?>
      </select>
    </div><?php
        }

        /*
  * Save term meta for New Genre Taxonomy
  */
        public function save_genre_meta($term_id, $tt_id)
        {
          if (isset($_POST['observer-topic']) && '' !== $_POST['observer-topic']) {
            $topic = sanitize_title($_POST['observer-topic']);
            add_term_meta($term_id, 'observer-topic', $topic, true);
          }
        }

        /*
  * Add a field to Edit Genre Taxonomy for term meta
  */
        public function edit_genre_field($term, $taxonomy)
        {
          $topics = wp_list_pluck($this->get_observer_topics(), 'title', 'id');
          asort($topics);

          // get current topic
          $topic = get_term_meta($term->term_id, 'observer-topic', true);

          ?><tr class="form-field term-group-wrap">
      <th scope="row">
        <label for="observer-topic">Observer topic</label>
      </th>
      <td><select class="postform" id="observer-topic" name="observer-topic">
          <option value="">None</option>
          <?php foreach ($topics as $id => $title) : ?>
            <option value="<?php echo $id; ?>" <?php selected($topic, $id); ?>><?php echo $title; ?></option>
          <?php endforeach; ?>
        </select></td>
    </tr><?php
        }

        /*
  * Save term meta for Existing Genre Taxonomy
  */
        public function update_genre_meta($term_id, $tt_id)
        {
          if (isset($_POST['observer-topic']) && '' !== $_POST['observer-topic']) {
            $topic = sanitize_title($_POST['observer-topic']);
            update_term_meta($term_id, 'observer-topic', $topic);
          } else {
            delete_term_meta($term_id, 'observer-topic');
          }
        }

        /*
  * Add column to Genre Taxonomy
  */
        public function add_genre_column($columns)
        {
          $columns['observer_topic'] = 'Observer topic';
          return $columns;
        }

        function add_genre_column_content($content, $column_name, $term_id)
        {
          if ($column_name !== 'observer_topic') {
            return $content;
          }

          $topics = wp_list_pluck($this->get_observer_topics(), 'title', 'id');
          asort($topics);

          $term_id = absint($term_id);
          $topic = get_term_meta($term_id, 'observer-topic', true);

          if (!empty($topic)) {
            $content .= esc_attr($topics[$topic]);
          }
          return $content;
        }

        /*
  * Shortcode: Tastemaker
  */
        public function shortcode_observer_tastemaker_form($atts)
        {

          $tastemaker_atts = shortcode_atts(array(
            'id' => NULL,
            'background' => '#e9ecef',
            'border' => '#fff',
            'width' => NULL
          ), $atts);

          if (is_null($tastemaker_atts['id']))
            return;

          $id = absint($tastemaker_atts['id']);

          wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '/js/scripts.js', array('jquery'), time(), true);
          $args = array(
            'url'   => admin_url('admin-ajax.php'),
            // 'ajax_nonce' => wp_create_nonce( $this->plugin_slug . '-nonce' ),
          );
          wp_localize_script($this->plugin_name, $this->plugin_name, $args);

          // $api_url = $this->api_url . 'get_tastemaker_form?key=' . $this->rest_api_key . '&id=' . $id;
          $api_url = $this->api_url . 'get_tastemaker_form?key=' . $this->rest_api_key . '&' . http_build_query($tastemaker_atts);

          $response = wp_remote_get($api_url, ['sslverify' => !$this->is_sandbox]);

          $responseBody = wp_remote_retrieve_body($response);

          if ($responseBody) {
            $resonseJson = json_decode($responseBody);
            $form_html = isset($resonseJson->success) && $resonseJson->success ? $resonseJson->data : '';
          } else {
            $form_html = '';
          }

          return $form_html;
        } // shortcode_observer_tastemaker_form()

        /*
  * Save Review - Frontend
  */
        public function save_tastemaker_review()
        {

          if (defined('DOING_AJAX') && DOING_AJAX) :

            parse_str($_POST['formData'], $formData);

            // wp_send_json_error( $formData );

            $errors = [];

            $tastemaker_id = isset($formData['id']) ? $formData['id'] : null;
            if (is_null($tastemaker_id)) {
              $errors[] = 'Invalid submission.';
            }

            $formData['rating'] = isset($formData['rating']) ? absint($formData['rating']) : 0;
            if (!isset($formData['rating']) || !in_array($formData['rating'], [1, 2, 3, 4, 5])) {
              $errors[] = 'Please select valid star rating.';
            }

            $formData['email'] = trim($formData['email']);
            if (!isset($formData['email']) || !is_email($formData['email'])) {
              $errors[] = 'Please enter valid email address.';
            }

            if (count($errors) > 0) {
              wp_send_json_error($errors);
            }

            $formData['key'] = $this->rest_api_key;

            $api_url = $this->api_url . 'create_tastemaker_review';
            $response = wp_remote_post(
              $api_url,
              [
                'method' => 'POST',
                'timeout' => 45,
                'body' => $formData,
                'sslverify' => !$this->is_sandbox
              ]
            );
            $responseBody = wp_remote_retrieve_body($response);
            $resonseJson = json_decode($responseBody);
            if ($resonseJson->success) {
              wp_send_json_success($resonseJson->data);
            } else {
              wp_send_json_error($resonseJson->data);
            }
          // return $resonseJson->data;
          endif;
        }

        /*
  * Shortcode: Lead Generator
  */
        public function shortcode_observer_lead_generator_form($atts)
        {

          $lead_generator_atts = shortcode_atts(array(
            'id' => NULL,
            'background' => '#e9ecef',
            'border' => '#fff',
            'width' => NULL
          ), $atts);

          if (is_null($lead_generator_atts['id']))
            return;

          $id = absint($lead_generator_atts['id']);

          wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '/js/scripts.js', array('jquery'), time(), true);
          $args = array(
            'url'   => admin_url('admin-ajax.php'),
            // 'ajax_nonce' => wp_create_nonce( $this->plugin_slug . '-nonce' ),
          );
          wp_localize_script($this->plugin_name, $this->plugin_name, $args);

          $api_url = $this->api_url . 'get_lead_generator_form?key=' . $this->rest_api_key . '&' . http_build_query($lead_generator_atts);

          $response = wp_remote_get($api_url, ['sslverify' => !$this->is_sandbox]);

          $responseBody = wp_remote_retrieve_body($response);
          if ($responseBody) {
            $resonseJson = json_decode($responseBody);
            $form_html = isset($resonseJson->success) && $resonseJson->success ? $resonseJson->data : '';
          } else {
            $form_html = '';
          }

          return $form_html;
        } // shortcode_observer_lead_generator_form()

        /*
  * Save Lead Generator Response - Frontend
  */
        public function save_lead_generator_response()
        {

          if (defined('DOING_AJAX') && DOING_AJAX) :

            parse_str($_POST['formData'], $formData);

            $errors = [];

            $tastemaker_id = isset($formData['id']) ? $formData['id'] : null;
            if (is_null($tastemaker_id)) {
              $errors[] = 'Invalid submission.';
            }

            $formData['email'] = trim($formData['email']);
            if (!isset($formData['email']) || !is_email($formData['email'])) {
              $errors[] = 'Please enter valid email address.';
            }

            if (count($errors) > 0) {
              wp_send_json_error($errors);
            }

            $formData['key'] = $this->rest_api_key;

            $api_url = $this->api_url . 'create_lead_generator_response';
            $response = wp_remote_post(
              $api_url,
              [
                'method' => 'POST',
                'timeout' => 45,
                'body' => $formData,
                'sslverify' => !$this->is_sandbox
              ]
            );
            $responseBody = wp_remote_retrieve_body($response);
            $resonseJson = json_decode($responseBody);
            if ($resonseJson->success) {
              wp_send_json_success($resonseJson->data);
            } else {
              wp_send_json_error($resonseJson->data);
            }
          endif;
        } // save_lead_generator_response()

        /*
  * Shortcode: Subscribe to Genre
  */
        public function shortcode_subscribe_genre_form($atts)
        {

          $genre_atts = shortcode_atts(array(
            'id' => NULL,
          ), $atts);

          if (is_null($genre_atts['id']))
            return;

          $post_id = $genre_atts['id'];

          $topic = get_post_meta($post_id, 'observer-topic', true);

          if (!$topic) {
            $genres = get_the_terms($post_id, 'genre');
            if (!$genres) {
              return;
            }

            $primary_genre = null;

            foreach ($genres as $genre) {
              if (get_post_meta($post_id, '_yoast_wpseo_primary_genre', true) == $genre->term_id) {
                $primary_genre = $genre;
                break;
              }
            }

            if (is_null($primary_genre)) {
              $primary_genre = $genres[0];
            }

            $topic = get_term_meta($primary_genre->term_id, 'observer-topic', true);
          }

          ob_start();
          ?>
    <style>
      .observer-sub-form {
        padding: .25rem;
        background: #f3f3f3;
        /* border-radius: .25rem; */
        max-width: none;
        border: 1px solid #ddd;
        position: relative;
      }
    </style>
    <?php
          if ($topic) {
            // $topics = wp_list_pluck( $this->get_observer_topics(), 'title', 'id' );
            // $topic_title = trim( str_ireplace( 'Observer', '', $topics[$topic] ) );
            $topics = $this->get_observer_topics();
            $topic_titles = wp_list_pluck($topics, 'title', 'id');
            $topic_links = wp_list_pluck($topics, 'link', 'id');
            $topic_title = trim(str_ireplace('Observer', '', $topic_titles[$topic]));

            if (in_array($topic, [27])) {
              $topic_title .= ' Music';
            }
    ?>
      <style>
        .observer-sub-form .l-learn-more {
          /* position: absolute;
            right: 0;
            top: 0;
            padding: .25rem .5rem;
            background: #ddd;
            border-top-right-radius: 10px;
            border-bottom-left-radius: 10px; */
          text-decoration: underline;
          font-weight: bold;
        }

        .observer-sub-form .observer-sub-email {
          background: #fff;
          border-radius: .25rem;
          padding: 25px 15px;
          border-top-right-radius: 0;
          border-bottom-right-radius: 0;
        }

        .observer-sub-form input[type=email] {
          width: 100%;
          font-size: 16px;
          line-height: 1;
          color: #000;
          border: none;
        }

        .observer-sub-form input[type=submit] {
          padding: 5px 10px;
          font-weight: 300;
          /* background-color: #dc3545; */
          color: #fff;
          border: none;

        }

        .observer-sub-form .submit-wrap,
        .observer-sub-form .submit-wrap input[type=submit] {
          border-top-left-radius: 0 !important;
          border-bottom-left-radius: 0 !important;
        }

        .observer-sub-form .submit-wrap {
          border: 1px solid #fff;
        }

        .observer-sub-form .spinner {
          width: 25px !important;
          height: 25px !important;
          margin: 10px auto !important;
        }

        .observer-sub-form .spinner .double-bounce1,
        .observer-sub-form .spinner .double-bounce2 {
          background-color: #fff;
        }
      </style>
      <div class="observer-sub-form rounded justify-content-center my-3 p-3">
        <div class="mb-2">
          <h5 class="mb-0">Love <?php echo $topic_title; ?>?</h5>
        </div>
        <p class="mb-2">
          Get the latest <?php echo $topic_title; ?> news, features, updates and giveaways straight to your inbox
          <a href="<?php echo $topic_links[$topic]; ?>" class="l-learn-more text-dark" target="_blank">Learn more</a>
        </p>
        <button class="button btn btn-danger btn-join">JOIN</button>
        <form action="#" method="post" id="observer-subscribe-form<?php echo $post_id; ?>" name="observer-subscribe-form" class="observer-subscribe-form d-none">
          <div class="d-flex" style="box-shadow: 0 0 0 1px rgba(0,0,0,.15), 0 2px 3px rgba(0,0,0,.2);">
            <input type="hidden" name="list" value="<?php echo $topic; ?>">
            <input type="email" name="email" class="form-control observer-sub-email" placeholder="Your email" value="">
            <div class="d-flex submit-wrap rounded">
              <input type="submit" value="Join" name="subscribe" class="button btn btn-danger rounded">
              <div class="loading mx-3" style="display: none;">
                <div class="spinner">
                  <div class="double-bounce1 bg-dark"></div>
                  <div class="double-bounce2 bg-dark"></div>
                </div>
              </div>
            </div>
          </div>
        </form>
        <div class="alert alert-info d-none js-msg-subscribe mt-2"></div>
        <div class="alert alert-danger d-none js-errors-subscribe mt-2"></div>
      </div>

      <script>
        jQuery(document).ready(function($) {
          if ($('.btn-join').length) {
            $(document).on('click', '.btn-join', function() {
              var theForm = $(this).next('form.observer-subscribe-form');
              theForm.removeClass('d-none');
              theForm.find('input[name="email"]').focus();
              $(this).remove();
            })
          }
          if ($('.observer-subscribe-form').length) {
            $(document).on('submit', '.observer-subscribe-form', function(e) {
              e.preventDefault();
              var theForm = $(this);

              const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

              if (theForm.find('input[name="email"]').length &&
                (
                  theForm.find('input[name="email"]').val() == '' ||
                  !re.test(String(theForm.find('input[name="email"]').val().toLowerCase()))
                )) {
                theForm.parent().find('.js-errors-subscribe').html('Please enter a valid email address.').removeClass('d-none');
                return false;
              }

              var formData = $(this).serialize();
              var loadingElem = $(this).find('.loading');
              var button = $(this).find('.button');

              var the_url = theForm.closest('.single_story').find('h1:first').data('href');
              formData += '&source=' + the_url;

              $('.js-errors-subscribe,.js-msg-subscribe').html('').addClass('d-none');
              loadingElem.show();
              button.hide();
              var data = {
                action: 'subscribe_observer',
                formData: formData
              };
              $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(res) {
                if (res.success) {
                  theForm.parent().find('.js-msg-subscribe').html(res.data.message).removeClass('d-none');
                  theForm.hide();
                } else {
                  theForm.parent().find('.js-errors-subscribe').html(res.data.error.message).removeClass('d-none');
                  button.show();
                }
                loadingElem.hide();
              });
            });
          }
        });
      </script>
    <?php
          } else if (isset($primary_genre)) { // Topic not set
    ?>
      <a href="https://thebrag.com/observer/" target="_blank" class="text-dark">
        <div class="observer-sub-form rounded justify-content-center my-3 p-3">
          <h5>Love Music?</h5>
          <p>Get the latest Music news, features, updates and giveaways straight to your inbox</p>
          <div class="btn btn-danger">Click here to join FREE</div>
        </div>
      </a>
<?php
          }
          $html = ob_get_contents();
          ob_end_clean();

          return $html;
        } // shortcode_subscribe_genre_form()

        public function ajax_subscribe_observer()
        {

          if (defined('DOING_AJAX') && DOING_AJAX) :

            parse_str($_POST['formData'], $formData);

            if (!is_numeric($formData['list'])) {
              error_log('Observer List _' . $formData['list'] . '_is not numeric');
              wp_mail('sachin.patel@thebrag.media', 'Observer Error', 'Observer List _' . $formData['list'] . '_is not numeric');
              wp_send_json_error(['error' => ['message' => 'Something went wrong']]);
              wp_die();
            }

            $brag_api_url_base = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) ? 'https://the-brag.com/' : 'https://thebrag.com/';

            $brag_api_url = $brag_api_url_base . 'wp-json/brag_observer/v1/sub_unsub/';

            $response = wp_remote_post(
              $brag_api_url,
              [
                'method'      => 'POST',
                'body'        => array(
                  'email' => $formData['email'],
                  'list' => $formData['list'],
                  'source' => $formData['source'],
                  'status' => 'subscribed',
                ),
                'sslverify' => !in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']),
              ]
            );
            $responseBody = wp_remote_retrieve_body($response);
            $resonseJson = json_decode($responseBody);
            if (isset($resonseJson->success) && $resonseJson->success == 1) {
              return wp_send_json_success($resonseJson->data);
              wp_die();
            }
            wp_send_json_error(['error' => ['message' => $resonseJson->data->error->message]]);
            wp_die();
          endif;
        } // ajax_subscribe_observer() }}

        /*
  * REST: Endpoints
  */
        public function _rest_api_init()
        {
          register_rest_route('api/v1', '/observer/articles', array(
            'methods' => 'GET',
            'callback' => [$this, 'get_articles_for_topic'],
          ));

          register_rest_route('api/v1', '/observer/competitions', array(
            'methods' => 'GET',
            'callback' => [$this, 'get_competition_articles'],
          ));
        }

        /*
  * REST: get articles
  */
        function get_articles_for_topic($data)
        {
          $topic = isset($_GET['topic']) ? $_GET['topic'] : null;

          if (is_null($topic))
            return;

          $topics = array_map('trim', explode(',', $topic));
          $keywords = implode('+', $topics);

          $posts_per_page = isset($_GET['size']) ? (int) $_GET['size'] : 10;

          $timezone = new DateTimeZone('Australia/Sydney');

          if (isset($_GET['after'])) :
            $after_dt = new DateTime(date_i18n('Y-m-d H:i:s', strtotime(trim($_GET['after']))));
            $after_dt->setTimezone($timezone);
            $after = $after_dt->format('Y-m-d H:i:s');
          else :
            $after = NULL;
          endif;

          if (isset($_GET['before'])) :
            $before_dt = new DateTime(date_i18n('Y-m-d H:i:s', strtotime(trim($_GET['before']))));
            $before_dt->setTimezone($timezone);
            $before = $before_dt->format('Y-m-d H:i:s');
          else :
            $before = NULL;
          endif;

          if (is_null($after) || is_null($before))
            return;

          $return = array();

          $args = [
            'date_query' => array(
              'after' => $after,
              'before' => $before,
            ),
            'fields' => 'ids',
            'post_type' => array('post', 'country'),
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
          ];

          $posts_keyword = new WP_Query(
            $args +
              [
                's' => $keywords,
                'exact' => isset($_GET['exact']), // true,
                'sentence' => isset($_GET['sentence']), // true,
              ]
          );

          // Genre + Artist + Category
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
          $combined_ids = array_unique($combined_ids);

          if (count($combined_ids) < 1)
            return;

          $posts = new WP_Query(['post__in' => $combined_ids]);

          global $post;
          if ($posts->have_posts()) {
            while ($posts->have_posts()) {
              $posts->the_post();
              $url = get_the_permalink();
              $author = get_field('Author') ? get_field('Author') : get_the_author();

              $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium_large');

              $return[] = array(
                'title' => get_the_title(),
                'link' => $url,
                'publish_date' => mysql2date('c', get_post_time('c', true), false),
                'description' => get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true) ?: get_the_excerpt(),
                'image' => $src[0],
              );
            }
          }
          return $return;
        }

        /*
  * Get Observer topics
  */
        public function get_observer_topics()
        {
          $api_url = $this->api_url . 'get_topics?key=' . $this->rest_api_key;
          $response = wp_remote_get($api_url, ['sslverify' => !$this->is_sandbox]);

          $responseBody = wp_remote_retrieve_body($response);
          if ($responseBody) {
            $resonseJson = json_decode($responseBody);
            $topics = isset($resonseJson->success) && $resonseJson->success ? $resonseJson->data : '';
          } else {
            $topics = '';
          }
          return $topics;
        }

        /*
  * Call Remote API
  */
        private static function callAPI($method, $url, $data = '', $content_type = '')
        {
          $curl = curl_init();
          switch ($method) {
            case "POST":
              curl_setopt($curl, CURLOPT_POST, 1);
              if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              break;
            case "PUT":
              curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
              if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              break;
            default:
              if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
          }
          // OPTIONS:
          curl_setopt($curl, CURLOPT_URL, $url);
          if ($content_type !== false) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
              'Content-Type: application/json',
            ));
          }
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

          if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
          }
          // EXECUTE:
          $result = curl_exec($curl);

          // error_log( $url );
          // if ( 'POST' == $method ) {
          // echo '<pre>'; var_dump( curl_error( $curl ) ); echo '</pre>';
          // }
          if (!$result)
            return;
          curl_close($curl);
          return $result;
        }

        /*
  * Save post meta if shortcode is present
  */
        public function _save_post($post_id, $post, $update)
        {

          if (strpos($post->post_content, '[observer_lead_generator_form') !== FALSE) {
            preg_match('/(.*?)\[observer_lead_generator_form id=\"(\d+)\"(.*?)\]/', $post->post_content, $matches);
            if ($matches && $matches[2]) {
              update_post_meta($post_id, 'has_lead_generator', $matches[2]);
            } else {
              delete_post_meta($post_id, 'has_lead_generator');
            }
          } else {
            delete_post_meta($post_id, 'has_lead_generator');
          }
        }

        /*
  * REST: Competition Articles
  */
        function get_competition_articles($data)
        {

          $timezone = new DateTimeZone('Australia/Sydney');

          $return = array();
          $today = date('Ymd');

          $args = [
            'post_type' => array('any'),
            'post_status' => 'publish',
            'orderby' => 'date',
            'order'   => 'ASC',
            'has_password'   => FALSE,
            'posts_per_page' => '-1',
            'meta_query' => [
              'relation' => 'AND',
              'lg_shortcode' => [
                'key' => 'has_lead_generator',
                'compare' => 'EXISTS',
              ],
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

              $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium_large');

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
      }

      new BragObserverDBU();
