<?php
class User
{

  public function create($user = [], $redirectTo = null)
  {

    if (!$user || is_null($user) || empty($user))
      return;

    if (!isset($user['email']) || !is_email($user['email'])) {
      wp_send_json_error('email_invalid');
    }

    if (email_exists($user['email']) && !is_user_logged_in()) {
      wp_send_json_error('email_exists');
    }

    /*
      if ( ! checkdate ( $user['birthday_month'], $user['birthday_day'], $user['birthday_year'] ) ||
        new DateTime( $user['birthday_year'] . '-' . $user['birthday_month'] . '-' . $user['birthday_day'] ) >= new DateTime()
      ) {
        wp_send_json_error( 'birthday_invalid');
      }

      if ( '0' == $user['state'] ) {
        wp_send_json_error( 'state_invalid');
      }
      */

    $user_pass = wp_generate_password();

    $user_id = wp_insert_user(
      array(
        'user_login' => $user['email'],
        'user_pass' => $user_pass,
        'user_email' => trim($user['email']),
        'first_name' => '',
        'last_name' => '',
        'user_registered' => date('Y-m-d H:i:s'),
        'role' => 'subscriber'
      )
    );

    global $wpdb;

    if (!get_user_meta($user_id, 'referrer_id', true) && isset($user['rc'])) {
      $refer_code = sanitize_text_field($user['rc']);
      $referrer_id = $wpdb->get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'refer_code' AND meta_value = '{$refer_code}' LIMIT 1");
      if ($referrer_id) {
        update_user_meta($user_id, 'referrer_id', $referrer_id);
      }
    }

    // require_once __DIR__ . '/email.class.php';
    // $email = new Email();
    // $email->sendUserVerificationEmail( $user_id, $redirectTo );

    return $user_id;
  }
}
