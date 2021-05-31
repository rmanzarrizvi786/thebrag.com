<?php

/**
 * Plugin Name: TBM Users
 * Plugin URI: https://thebrag.media/
 * Description:
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI:
 */

class TBMUsers
{

  protected $plugin_name;
  protected $plugin_slug;

  protected $rest_api_keys;

  public function __construct()
  {

    $this->plugin_name = 'tbm_users';
    $this->plugin_slug = 'tbm-users';

    $this->rest_api_keys =
      [
        'rsau' => '1fc08f46-3537-43f6-b5c1-c68704acf3fa',
        'tio' => '1bb3d123-788f-4ec4-8d4c-a68cf00406e4',
      ];

    // Hide Admin Bar
    add_filter('show_admin_bar', [$this, '_show_admin_bar']);
    remove_action('wp_head', '_admin_bar_bump_cb');

    // WP Logout
    // add_action('wp_logout', [$this, '_wp_logout']);

    // Lost Password
    add_action('lost_password', [$this, '_lost_password']);

    add_action('login_form_lostpassword', [$this, '_login_form_lostpassword']);

    add_filter('retrieve_password_title', [$this, '_retrieve_password_title'], 10, 1);
    add_filter('retrieve_password_message', [$this, '_retrieve_password_message'], 10, 4);

    add_action('login_form_rp', [$this, '_login_form_rp']);
    add_action('login_form_resetpass', [$this, '_login_form_rp']);

    // add_action( 'wp_login_failed', [ $this, '_wp_login_failed' ] );

    add_action('admin_init', [$this, '_admin_init']);

    add_action('wp', [$this, '_wp']);

    add_filter('send_email_change_email', '__return_false');
    add_filter('send_password_change_email', '__return_false');

    // REST API
    add_action('rest_api_init', [$this, '_rest_api_init']);

    // CORS
    add_filter('init', [$this, 'add_cors_http_header'], 11, 1);
  }

  /*
  * WP Initiated
  */
  public function _wp()
  {
    if (
      // is_page_template( 'page-templates/profile.php' ) ||
      is_page_template('page-templates/logout.php') ||
      // is_page_template( 'page-templates/propose-a-newsletter.php' ) ||
      is_page_template('page-templates/verify-account.php')
    )
      return;

    if (is_user_logged_in()) {
      if (current_user_can('edit_posts'))
        return;

      $current_user = wp_get_current_user();

      if ('' === get_user_meta($current_user->ID, 'is_activated', true)) {
        update_user_meta($current_user->ID, 'is_activated', 1); // activate user if registered before is_activated implemented
      } else if (get_user_meta($current_user->ID, 'is_activated', true) === '0' || is_null(get_user_meta($current_user->ID, 'is_activated', true))) {
        global $wp;
        $returnTo = home_url($wp->request);

        $user_info = get_userdata($current_user->ID); // gets user data
        $code = get_user_meta($current_user->ID, 'activationcode', true);
        $string = ['id' => $current_user->ID, 'code' => $code];

        wp_redirect(home_url('/verify/') . '?err=unverified&p=' . base64_encode(serialize($string)) . '&returnTo=' . urlencode($returnTo));
        die();
      } else {
        /*
        if (
          is_page_template( 'page-templates/profile.php' ) ||
          is_page_template( 'page-templates/logout.php' ) ||
          is_page_template( 'page-templates/propose-a-newsletter.php' ) ||
          is_page_template( 'page-templates/brag-observer-subscriptions.php' ) ||
          is_single() || is_home() || is_front_page()
        )
          return;
        */

        if (
          // strpos( $current_user->user_email, '@privaterelay.appleid' ) ||
          // ! get_user_meta( $current_user->ID, 'birthday', true ) ||
          !get_user_meta($current_user->ID, 'state', true)
        ) {

          update_user_meta($current_user->ID, 'incomplete_profile', "true");

          /*
          global $wp;
          $returnTo = home_url( $wp->request );

          if ( get_user_meta( $current_user->ID, 'is_imported', true ) === '1' ) {
            wp_redirect( home_url( 'profile' ) . "?err=new&returnTo=" . urlencode( $returnTo ) );
          } else {
            wp_redirect( home_url( 'profile' ) . "?err=incomplete&returnTo=" . urlencode( $returnTo ) );
          }
          die();
          */
        }
      }
    }
  }

  /**
   * Redirect non-admin users to home page
   */
  public function _admin_init()
  {
    $user = wp_get_current_user();
    // if (!current_user_can('edit_posts') && ('/wp-admin/admin-ajax.php' != $_SERVER['PHP_SELF'])) {
    // $allowed_roles = array('editor', 'administrator', 'snaps');
    // if (!array_intersect($allowed_roles, $user->roles) && ('/wp-admin/admin-ajax.php' != $_SERVER['PHP_SELF'])) {
    if (!current_user_can('edit_posts') && !current_user_can('snaps') && ('/wp-admin/admin-ajax.php' != $_SERVER['PHP_SELF'])) {
      wp_redirect(home_url());
      exit;
    }
  }

  /*
   * Navigate failed login to /login?status=failed page
   */
  public function _wp_login_failed($username)
  {
    $_SESSION['login_status'] = 'failed';
    wp_redirect(home_url('/login/'));
    exit;
  }

  /*
   * Show admin bar only for admins and editors
   */
  public function _show_admin_bar()
  {
    return false;
    return current_user_can('edit_posts');
  }

  /*
  * Logout of SSO when logged out of WP
  */
  public function _wp_logout()
  {
    require_once(ABSPATH . 'sso-sp/simplesaml/lib/_autoload.php');
    $auth = new SimpleSAML_Auth_Simple('default-sp');
    // $auth->logout( wp_get_referer() ? : home_url() );

    $returnTo = isset($_GET['returnTo']) ? $_GET['returnTo'] : (wp_get_referer() ?: home_url());
    $auth->logout($returnTo);
  }

  /**
   * Redirects the user to the custom "Forgot your password?" page instead of
   * wp-login.php?action=lostpassword.
   */
  public function _lost_password()
  {
    if ('GET' == $_SERVER['REQUEST_METHOD']) {
      wp_redirect(home_url('forgot-password'));
      exit;
    }
  }

  /**
   * Initiates password reset.
   */
  public function _login_form_lostpassword()
  {
    if ('POST' == $_SERVER['REQUEST_METHOD']) {
      $errors = retrieve_password();
      if (is_wp_error($errors)) {
        // Errors found
        $redirect_url = home_url('forgot-password');
        $redirect_url = add_query_arg('errors', join(',', $errors->get_error_codes()), $redirect_url);
      } else {
        // Email sent
        $redirect_url = home_url('forgot-password');
        $redirect_url = add_query_arg('checkemail', 'confirm', $redirect_url);
      }
      wp_redirect($redirect_url);
      exit;
    }
  }

  public function _retrieve_password_title($old_subject)
  {
    $subject = '[The BRAG] Password Reset';
    return $subject;
  }

  /**
   * Returns the message body for the password reset mail.
   * Called through the retrieve_password_message filter.
   *
   * @param string  $message    Default mail message.
   * @param string  $key        The activation key.
   * @param string  $user_login The username for the user.
   * @param WP_User $user_data  WP_User object.
   *
   * @return string   The mail message to send.
   */
  public function _retrieve_password_message($message, $key, $user_login, $user_data)
  {
    ob_start();
    include(get_template_directory() . '/email-templates/header.php');
?>
    <div>
      <p><strong>Hello!</strong></p>
      <p>You asked us to reset your password for your account using the email address <?php echo $user_data->user_email; ?>.
      <p>If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.</p>
      <p>To reset your password, visit the following address:<br>
        <?php echo site_url("/reset-password/?action=rp&key=$key&login=" . rawurlencode($user_login), 'login'); ?>
      <p>&nbsp;</p>
      <p style="color: #999;">Regards,<br><strong>The Brag</strong></p>
    </div>
<?php
    include(get_template_directory() . '/email-templates/footer.php');

    $msg = ob_get_contents();
    ob_end_clean();
    return $msg;
  }

  /**
   * Redirects to the custom password reset page, or the login page
   * if there are errors.
   * Resets the user's password if the password reset form was submitted.
   */
  public function _login_form_rp()
  {
    if ('GET' == $_SERVER['REQUEST_METHOD']) {
      // Verify key / login combo
      $user = check_password_reset_key($_REQUEST['key'], $_REQUEST['login']);
      if (!$user || is_wp_error($user)) {
        if ($user && $user->get_error_code() === 'expired_key') {
          wp_redirect(home_url('/forgot-password/?errors=expiredkey'));
        } else {
          wp_redirect(home_url('/forgot-password/?errors=invalidkey'));
        }
        exit;
      }

      $redirect_url = home_url('reset-password');
      $redirect_url = add_query_arg('login', esc_attr($_REQUEST['login']), $redirect_url);
      $redirect_url = add_query_arg('key', esc_attr($_REQUEST['key']), $redirect_url);
      wp_redirect($redirect_url);
      exit;
    } else if ('POST' == $_SERVER['REQUEST_METHOD']) {
      $rp_key = $_REQUEST['rp_key'];
      $rp_login = $_REQUEST['rp_login'];

      $user = check_password_reset_key($rp_key, $rp_login);

      if (!$user || is_wp_error($user)) {
        if ($user && $user->get_error_code() === 'expired_key') {
          wp_redirect(home_url('/forgot-password/?errors=expiredkey'));
        } else {
          wp_redirect(home_url('/forgot-password/?errors=invalidkey'));
        }
        exit;
      }

      if (isset($_POST['pass1'])) {
        if ($_POST['pass1'] != $_POST['pass2']) {
          // Passwords don't match
          $redirect_url = home_url('reset-password');
          $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
          $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
          $redirect_url = add_query_arg('errors', 'password_reset_mismatch', $redirect_url);
          wp_redirect($redirect_url);
          exit;
        }

        if (empty($_POST['pass1'])) {
          // Password is empty
          $redirect_url = home_url('reset-password');
          $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
          $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
          $redirect_url = add_query_arg('errors', 'password_reset_empty', $redirect_url);
          wp_redirect($redirect_url);
          exit;
        }

        // Parameter checks OK, reset password
        reset_password($user, $_POST['pass1']);
        wp_redirect(home_url('/login/?password=changed'));
      } else {
        echo "Invalid request.";
      }
      exit;
    }
  }

  /*
  * API Endpoints for User
  */
  public function _rest_api_init()
  {
    register_rest_route($this->plugin_name . '/v1', '/get', array(
      'methods' => 'GET',
      'callback' => [$this, 'rest_get_user'],
      'permission_callback' => '__return_true',
    ));

    register_rest_route($this->plugin_name . '/v1', '/create', array(
      'methods' => 'POST',
      'callback' => [$this, 'rest_create_user'],
      'permission_callback' => '__return_true',
    ));

    register_rest_route($this->plugin_name . '/v1', '/update', array(
      'methods' => 'POST',
      'callback' => [$this, 'rest_update_user'],
      'permission_callback' => '__return_true',
    ));

    register_rest_route($this->plugin_name . '/v1', '/login', array(
      'methods' => 'POST',
      'callback' => [$this, 'rest_login_user'],
      'permission_callback' => '__return_true',
    ));
  }

  /*
  * REST - Get user
  */
  public function rest_get_user()
  {
    if (!isset($_GET['key']) || !$this->isRequestValid($_GET['key'])) {
      wp_send_json_error(['Invalid Request']);
      wp_die();
    }

    $email = isset($_GET['email']) ? sanitize_text_field(trim($_GET['email'])) : NULL;

    if (is_null($email) || !is_email($email)) {
      wp_send_json_error(['Invalid Email']);
      wp_die();
    }

    $user = get_user_by('email', $email);

    if (!$user) {
      wp_send_json_error([]);
      wp_die();
    }

    $return = [
      'display_name' => $user->display_name,
      'roles' => $user->roles,
      'state' => get_user_meta($user->ID, 'state', true),
      'birthday' => get_user_meta($user->ID, 'birthday', true),
      'gender' => get_user_meta($user->ID, 'gender', true),
    ];

    wp_send_json_success($return);
  } // rest_get_user() }}

  /*
  * REST - Create user
  */
  public function rest_create_user($request_data)
  {

    global $wpdb;

    $data = $request_data->get_params();

    if (!isset($data['key']) || !$this->isRequestValid($data['key'])) {
      wp_send_json_error(['invalid_request']);
      wp_die();
    }

    $data = stripslashes_deep($data);

    $errors = [];

    if (!isset($data['email']) || !is_email($data['email'])) {
      $errors[] = 'invalid_email';
    }

    if (email_exists($data['email'])) {
      $errors[] = 'eu'; // = existing_user

      $user = get_user_by('email', $data['email']);
      $user_id = $user->ID;

      if (isset($data['source']) && 'tio'  == $data['source']) {
        $check_sub = $wpdb->get_row("SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '4' LIMIT 1");
        if (!$check_sub) {
          $wpdb->insert(
            $wpdb->prefix . 'observer_subs',
            [
              'user_id' => $user_id,
              'list_id' => '4',
              'status' => 'subscribed',
              'status_mailchimp' => NULL,
              'subscribed_at' => current_time('mysql'),
            ]
          );
        }
      }

      if (isset($data['source']) && 'rs-mag'  == $data['source']) {
        update_user_meta($user_id, 'has_rs_mag', true);
      }
    }

    if (count($errors) == 0) {

      $user_pass = wp_generate_password();

      $name_parts = explode(" ", $data['full_name']);
      $first_name = array_shift($name_parts);
      $last_name = implode(' ', $name_parts);

      $user_id = wp_insert_user(array(
        'user_login' => $data['email'],
        'user_pass' => $user_pass,
        'user_email' => trim($data['email']),
        'first_name' => $first_name,
        'last_name' => $last_name,
        'user_registered' => date('Y-m-d H:i:s'),
        'role' => 'subscriber'
      ));

      if ($user_id) {
        if (!get_user_meta($user_id, 'oc_token', true)) :
          $oc_token = md5($user_id . time()); // creates md5 code to verify later
          update_user_meta($user_id, 'oc_token', $oc_token);
        endif;

        if (isset($data['source']) && 'rs-mag'  == $data['source']) {

          if (isset($data['source']) && 'rs-mag'  == $data['source']) {
            update_user_meta($user_id, 'has_rs_mag', true);
          }

          require_once(ABSPATH . 'wp-content/plugins/brag-observer/classes/email.class.php');
          $email = new Email();
          $email->sendUserLoginDetailsRSMag($user_id, $user_pass);
        }

        $unserialized_oc_token = [
          'id' => $user_id,
          'oc_token' => get_user_meta($user_id, 'oc_token', true),
        ];
        $user = get_user_by('email', $data['email']);
        $errors[] = 'nu|' . base64_encode(serialize($unserialized_oc_token));

        if (isset($data['source']) && 'tio'  == $data['source']) {
          $check_sub = $wpdb->get_row("SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '4' LIMIT 1");
          if (!$check_sub) {
            $wpdb->insert(
              $wpdb->prefix . 'observer_subs',
              [
                'user_id' => $user_id,
                'list_id' => '4',
                'status' => 'subscribed',
                'status_mailchimp' => NULL,
                'subscribed_at' => current_time('mysql'),
              ]
            );
          }
        }

        // error_log( '..API..' . print_r( $errors, true ) );

        wp_send_json_error($errors);
        wp_die();
      }
    } else { // There are errors

      // error_log( '..API..' . print_r( $errors, true ) );

      wp_send_json_error($errors);
      wp_die();
    }
  }

  public function rest_login_user($request_data)
  {
    if (is_user_logged_in()) {
      $current_user = wp_get_current_user();
      wp_send_json_success($current_user->ID);
    }
    $data = $request_data->get_params();

    $wp_signon = wp_signon(
      [
        'user_login' => $data['email'],
        'user_password' => $data['password']
      ],
      true
    );
    if (!is_wp_error($wp_signon)) {
      wp_send_json_success($wp_signon->ID);
      wp_die();
    }
    wp_send_json_error('Incorrect credentials, please try again.');
    wp_die();
  }

  /*
  * Validate Key for REST API
  */
  private function isRequestValid($key)
  {
    return isset($key) && !is_null($key) && in_array($key, $this->rest_api_keys);
  }

  public function add_cors_http_header()
  {
    header("Access-Control-Allow-Origin: *");
  }
}

new TBMUsers();
