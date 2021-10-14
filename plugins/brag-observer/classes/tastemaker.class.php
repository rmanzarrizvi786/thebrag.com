<?php
class Tastemaker extends BragObserver
{

  private $list_id;

  public function __construct()
  {

    $this->list_id = 48;

    // AJAX
    add_action('wp_ajax_save_observer_tastemaker', [$this, 'save_tastemaker']);

    add_action('wp_ajax_save_tastemaker_review', [$this, 'save_tastemaker_review']);
    add_action('wp_ajax_nopriv_save_tastemaker_review', [$this, 'save_tastemaker_review']);

    // Shortcodes
    add_shortcode('observer_tastemaker_form', [$this, 'shortcode_observer_tastemaker_form']);

    // Admin menu
    add_action('admin_menu', array($this, '_admin_menu'));

    // Load JS
    add_action('wp_enqueue_scripts', [$this, 'load_js_css']);

    // REST API
    add_action('rest_api_init', [$this, '_rest_api_init']);
  }

  public function _admin_menu()
  {

    add_submenu_page(
      'brag-observer',
      'Create/Edit/Preview Tastemaker',
      'Create Tastemaker',
      'edit_posts',
      'brag-observer-manage-tastemaker',
      array($this, 'manage_tastemaker_show_form')
    );

    add_submenu_page(
      'brag-observer',
      'Observer Tastemakers',
      'Tastemakers',
      'edit_posts',
      'brag-observer-view-tastemakers-list',
      array($this, 'view_tastemakers_list')
    );
  }

  /*
  * Load JS and CSS
  */
  public function load_js_css()
  {
    wp_enqueue_script('brag_observer', plugin_dir_url(__FILE__) . '../js/scripts.min.js', array('jquery'), '20211014', true);
    $args = array(
      'url'   => admin_url('admin-ajax.php'),
      // 'ajax_nonce' => wp_create_nonce( $this->plugin_slug . '-nonce' ),
    );
    wp_localize_script('brag_observer', 'brag_observer', $args);
  }

  /*
  * Manage Tastemaker - Show form
  */
  public function manage_tastemaker_show_form()
  {
    include __DIR__ . '/../partials/tastemaker/manage.php';
  } // Add/Edit Solus

  /*
  * View list of Tastemakers
  */
  public function view_tastemakers_list()
  {
    include __DIR__ . '/../partials/tastemaker/list.php';
  } // View Tastemakers list

  /*
  * Save Tastemaker
  */
  public function save_tastemaker()
  {
    if ($_POST['data']) {

      $errors = [];

      parse_str($_POST['data'], $data);

      $data = stripslashes_deep($data);

      $required_fields = [
        'title',
      ];

      foreach ($required_fields as $required_field) :
        if (!isset($data[$required_field]) || '' == $data[$required_field]) :
          $errors[] = ucfirst(str_replace(array('-', '_',), ' ', $required_field)) . ' is required.';
        endif;
      endforeach; // For Each $required_fields

      if (count($errors) > 0) :
        wp_send_json_error($errors);
      endif;

      global $wpdb;
      $table = $wpdb->prefix . "observer_tastemakers";

      if (isset($data['id'])) :
        $wpdb->update(
          $table,
          array(
            'title' => $data['title'],
            // 'image_url' => $data['image_url'],
          ),
          array('id' => $data['id'])
        );
      else :
        $wpdb->insert(
          $table,
          array(
            'title' => $data['title'],
            // 'image_url' => $data['image_url'],
            'created_at' => current_time('mysql'),
          )
        );
      endif;
      wp_send_json_success();
    }
  } // save_tastemaker()

  /*
  * Shortcode: Tastemaker
  */
  public function shortcode_observer_tastemaker_form($atts)
  {
    require_once __DIR__ . '/../partials/tastemaker/shortcode-form.php';

    return observer_tastemaker_form($atts);
  } // shortcode_observer_tastemaker_form()

  /*
  * Save Review - Frontend
  */
  public function save_tastemaker_review($formData = [], $is_rest = false)
  {

    if (isset($_POST['formData'])) {
      parse_str($_POST['formData'], $formData);
    }

    $errors = [];

    global $wpdb;

    $tastemaker_id = isset($formData['id']) ? $formData['id'] : null;
    if (is_null($tastemaker_id)) {
      $errors[] = 'Invalid submission.';
    } else {
      $tastemaker = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_tastemakers WHERE id = {$tastemaker_id}");
      if (!$tastemaker) {
        $errors[] = 'Invalid submission.';
      }
    }

    $formData['rating'] = isset($formData['rating']) ? absint($formData['rating']) : 0;
    if (!isset($formData['rating']) || !in_array($formData['rating'], [1, 2, 3, 4, 5])) {
      $errors[] = 'Please select a valid rating from 1 to 5 stars.';
    }

    $verified = false;
    $message = '';
    if (is_user_logged_in() && !$is_rest) :
      $user = wp_get_current_user();
      $verified = true;
      $message = 'Thank you! Your review has been submitted.';
    else :
      $formData['email'] = trim($formData['email']);
      if (!isset($formData['email']) || !is_email($formData['email'])) {
        $errors[] = 'Please enter a valid email address.';
      } else {
        if (email_exists($formData['email'])) {
          $user = get_user_by('email', $formData['email']);
        } else {
          $user = $this->create_user($formData['email']);
        }

        $message = 'Please verify your review by clicking the link we sent you via email.';
      }
    endif; // If user is logged in AND function is not called via REST API

    if (count($errors) > 0) {
      wp_send_json_error($errors);
      wp_die();
    }

    $check_review = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}observer_tastemaker_reviews WHERE tastemaker_id = '{$tastemaker->id}' AND user_id = '{$user->ID}' LIMIT 1");

    if (!$check_review) {

      $source = isset($formData['source']) ? trim($formData['source']) : NULL;

      $insert_values = [
        'tastemaker_id' => $tastemaker->id,
        'user_id' => $user->ID,
        'rating' => $formData['rating'],
        'comments' => $formData['comments'],
        'source' => $source,
      ];
      $insert_format = ['%d', '%d', '%d', '%s', '%s'];

      if ($verified) {
        $insert_values['status'] = 'verified';
        $insert_format[] = '%s';
      }

      $wpdb->insert(
        $wpdb->prefix . 'observer_tastemaker_reviews',
        $insert_values,
        $insert_format
      );

      if ($verified) {
        $this->subscribe($user->ID);
      } else {
        require_once PLUGINPATH . '/classes/email.class.php';
        $email = new Email($this);
        $email->sendTastemakersVerificationEmail($user->ID, $tastemaker->id);
        // $this->sendVerificationEmail( $user->ID, $tastemaker->id );
      }

      wp_send_json_success(
        [
          'verified' => $verified,
          'message' => $message,
        ]
      );
      wp_die();
    } else {
      wp_send_json_error(['You have already provided feedback here, thank you!']);
      wp_die();
    }
  } // save_tastemaker_review()



  /*
  * Subscribe to list
  */
  public function subscribe($user_id)
  {
    global $wpdb;
    $check_sub = $wpdb->get_row("SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '{$this->list_id}' LIMIT 1");
    if (!$check_sub) {
      $wpdb->insert(
        $wpdb->prefix . 'observer_subs',
        [
          'user_id' => $user_id,
          'list_id' => $this->list_id,
          'status' => 'subscribed',
          'status_mailchimp' => NULL,
          'subscribed_at' => current_time('mysql'),
        ]
      );
    }
  } // subscribe()

  /*
  * Create a new user
  */
  public function create_user($email)
  {
    $user_pass = wp_generate_password();

    $user_id = wp_insert_user(
      array(
        'user_login' => trim($email),
        'user_pass' => $user_pass,
        'user_email' => trim($email),
        'first_name' => '',
        'last_name' => '',
        'user_registered' => date('Y-m-d H:i:s'),
        'role' => 'subscriber'
      )
    );

    return get_user_by('ID', $user_id);
  } // create_user()

  /*
  * REST: API Endpoints
  */
  public function _rest_api_init()
  {
  }

  /*
  * REST: Get form
  */
  public function get_form($args = [])
  {
    if (empty($args) || !isset($args['id']))
      return;
    return $this->shortcode_observer_tastemaker_form($args);
  }
}

new Tastemaker();
