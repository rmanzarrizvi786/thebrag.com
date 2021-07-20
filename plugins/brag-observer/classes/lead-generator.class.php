<?php
class LeadGenerator extends BragObserver
{

  private $list_id;

  public function __construct()
  {

    // AJAX
    add_action('wp_ajax_save_observer_lead_generator', [$this, 'save_lead_generator']);

    add_action('wp_ajax_save_lead_generator_review', [$this, 'save_lead_generator_review']);
    add_action('wp_ajax_nopriv_save_lead_generator_review', [$this, 'save_lead_generator_review']);

    // Shortcodes
    add_shortcode('observer_lead_generator_form', [$this, 'shortcode_observer_lead_generator_form']);

    // Admin menu
    add_action('admin_menu', array($this, '_admin_menu'));

    // Load JS
    // add_action( 'wp_enqueue_scripts', [ $this, 'load_js_css' ] );

    // REST API
    add_action('rest_api_init', [$this, '_rest_api_init']);
  }

  public function _admin_menu()
  {

    add_submenu_page(
      'brag-observer',
      'Create/Edit/Preview Lead Generator',
      'Create Lead Generator',
      'edit_posts',
      'brag-observer-manage-lead-generator',
      array($this, 'manage_lead_generator_show_form')
    );

    add_submenu_page(
      'brag-observer',
      'Observer Lead Generators',
      'Lead Generators',
      'edit_posts',
      'brag-observer-view-lead_generators-list',
      array($this, 'view_lead_generators_list')
    );
  }

  /*
  * Manage lead_generator - Show form
  */
  public function manage_lead_generator_show_form()
  {
    include __DIR__ . '/../partials/lead_generator/manage.php';
  } // Add/Edit Solus

  /*
  * View list of lead_generators
  */
  public function view_lead_generators_list()
  {
    include __DIR__ . '/../partials/lead_generator/list.php';
  } // View lead_generators list

  /*
  * Save lead_generator
  */
  public function save_lead_generator()
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
      $table = $wpdb->prefix . "observer_lead_generators";

      $values = [
        'title' => $data['title'],
        'list_id' => implode(',', $data['list_id']),
        'msg_thanks' => $data['msg_thanks'],
        'msg_thanks_verify' => $data['msg_thanks_verify'],
        'question1' => $data['question1'],
      ];

      if (isset($data['id'])) :
        $wpdb->update(
          $table,
          $values,
          array('id' => $data['id'])
        );
      else :
        $values['created_at'] = current_time('mysql');
        $wpdb->insert(
          $table,
          $values
        );
      endif;
      wp_send_json_success();
    }
  } // save_lead_generator()

  /*
  * Shortcode: lead_generator
  */
  public function shortcode_observer_lead_generator_form($atts)
  {
    require_once __DIR__ . '/../partials/lead_generator/shortcode-form.php';

    return observer_lead_generator_form($atts);
  } // shortcode_observer_lead_generator_form()

  /*
  * Save Review - Frontend
  */
  public function save_lead_generator_review($formData = [], $is_rest = false)
  {

    if (isset($_POST['formData'])) {
      parse_str($_POST['formData'], $formData);
    }

    $errors = [];

    global $wpdb;

    $lead_generator_id = isset($formData['id']) ? $formData['id'] : null;
    if (is_null($lead_generator_id)) {
      $errors[] = 'Invalid submission.';
    } else {
      $lead_generator = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_lead_generators WHERE id = {$lead_generator_id}");
      if (!$lead_generator) {
        $errors[] = 'Invalid submission.';
      }
    }

    $verified = false;
    $message = '';
    if (is_user_logged_in() && !$is_rest) :
      $user = wp_get_current_user();
      $verified = true;
      $message = !is_null($lead_generator->msg_thanks) ? $lead_generator->msg_thanks : 'Thank you! Your feedback has been submitted.';

      update_user_meta($user->ID, 'is_activated', 1);
    else :
      $formData['email'] = trim($formData['email']);
      if (!isset($formData['email']) || !is_email($formData['email'])) {
        $errors[] = 'Please enter a valid email address.';
      } else {
        if (email_exists($formData['email'])) {
          $user = get_user_by('email', $formData['email']);
          $verified = true;
          $message = !is_null($lead_generator->msg_thanks) ? $lead_generator->msg_thanks : 'Thank you! Your feedback has been submitted.';
        } else {
          $user = $this->create_user($formData['email']);

          $verified = true;

          /* TMP {{ */
          // update_user_meta( $user->ID, 'is_activated', 1 );
          /* }} TMP */

          /* if (!get_user_meta($user->ID, 'is_activated', true)) {
            update_user_meta($user->ID, 'is_activated', 0);
          } */

          $message = !is_null($lead_generator->msg_thanks_verify) ? $lead_generator->msg_thanks_verify : 'Please verify your feedback by clicking the link we sent you via email.';
          // $message = 'Please verify your feedback by clicking the link we sent you via email.';
        }
      }
    endif; // If user is logged in AND function is not called via REST API

    if (count($errors) > 0) {
      wp_send_json_error($errors);
      wp_die();
    }

    $check_review = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}observer_lead_generator_responses WHERE lead_generator_id = '{$lead_generator->id}' AND user_id = '{$user->ID}' LIMIT 1");

    if (!$check_review) {

      $source = isset($formData['source']) ? trim($formData['source']) : NULL;

      $insert_values = [
        'lead_generator_id' => $lead_generator->id,
        'user_id' => $user->ID,
        'response1' => isset($formData['response1']) ? sanitize_textarea_field($formData['response1']) : NULL,
        'source' => $source,
      ];
      $insert_format = ['%d', '%d', '%s', '%s',];

      if ($verified) {
        $insert_values['status'] = 'verified';
        $insert_format[] = '%s';
      }

      $wpdb->insert(
        $wpdb->prefix . 'observer_lead_generator_responses',
        $insert_values,
        $insert_format
      );
      $response_id = $wpdb->insert_id;

      if ($verified) {
        $lists = explode(',', $lead_generator->list_id);
        foreach ($lists as $list_id) {
          $this->subscribe($user->ID, $list_id);
        }

        // If referrer is set for competition
        if (isset($formData['lc'])) {
          $comp_code = sanitize_text_field($formData['lc']);
          $referrer_id = $wpdb->get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'comp_code' AND meta_value = '{$comp_code}' LIMIT 1");
          if ($referrer_id && $referrer_id != $user->ID) {
            $comp_credits = get_user_meta($referrer_id, 'comp_credits', true);
            if ($comp_credits && is_array($comp_credits)) {
              if (in_array($lead_generator->id, array_keys($comp_credits))) {
                $comp_credits[$lead_generator->id]++;
              } else {
                $comp_credits[$lead_generator->id] = 1;
              }
              update_user_meta($referrer_id, 'comp_credits', $comp_credits);
            }
          }
        }
      } else {

        // If referrer is set for competition
        if (isset($formData['lc'])) {
          $comp_code = sanitize_text_field($formData['lc']);
          $wpdb->update(
            $wpdb->prefix . 'observer_lead_generator_responses',
            ['comp_code' => $comp_code,],
            ['id' => $response_id]
          );
        }

        require_once PLUGINPATH . '/classes/email.class.php';
        $email = new Email($this);
        $email->sendLeadGeneratorVerificationEmail($user->ID, $lead_generator->id);
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
  } // save_lead_generator_review()



  /*
  * Subscribe to list
  */
  public function subscribe($user_id, $list_id)
  {
    global $wpdb;
    $check_sub = $wpdb->get_row("SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '{$list_id}' LIMIT 1");
    if (!$check_sub) {
      $wpdb->insert(
        $wpdb->prefix . 'observer_subs',
        [
          'user_id' => $user_id,
          'list_id' => $list_id,
          'status' => 'subscribed',
          'status_mailchimp' => NULL,
          'subscribed_at' => current_time('mysql'),
        ]
      );
    } elseif ('subscribed' != $check_sub->status) {
      $wpdb->update(
        $wpdb->prefix . 'observer_subs',
        [
          'status' => 'subscribed',
          'status_mailchimp' => NULL,
          'subscribed_at' => current_time('mysql'),
        ],
        [
          'id' => $check_sub->id,
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
    return $this->shortcode_observer_lead_generator_form($args);
  }
}

new LeadGenerator();
