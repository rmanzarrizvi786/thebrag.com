<?php
/*
 * Template Name: Sign in with Apple
 */

if(!session_id()) {
  session_start();
}

if ( ! function_exists( 'http' ) ) {
  function http($url, $params=false) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if($params)
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/json',
      'User-Agent: curl', # Apple requires a user agent header at the token endpoint
    ]);
    $response = curl_exec($ch);
    return json_decode($response);
  }
}

$ReturnTo = isset( $_SESSION['ReturnToUrl'] ) ? $_SESSION['ReturnToUrl'] : ( wp_get_referer() ? : home_url() );

if ( isset( $_POST['error'] ) ) {

  require_once(  ABSPATH . 'sso-sp/simplesaml/lib/_autoload.php');
  $auth = new SimpleSAML_Auth_Simple('default-sp');

  $_SESSION['login_error'] = $_POST['error'];
  $_SESSION['ReturnToUrl'] = $ReturnTo;

  $auth->requireAuth([
    'ReturnTo' => $ReturnTo,
    'KeepPost' => FALSE,
  ]);
  \SimpleSAML\Session::getSessionFromRequest()->cleanup();

} else if(isset($_POST['code'])) { // Handle "Sign in with Apple"

  $apple['client_id'] = 'com.thebrag';
  $apple['client_secret'] = 'eyJraWQiOiJLU0ZETjJNQjg3IiwiYWxnIjoiRVMyNTYifQ.eyJpc3MiOiIzWUhKMjdVWkxYIiwiaWF0IjoxNTkwMDMxMTEzLCJleHAiOjE2MDU1ODMxMTMsImF1ZCI6Imh0dHBzOi8vYXBwbGVpZC5hcHBsZS5jb20iLCJzdWIiOiJjb20udGhlYnJhZyJ9.Z_NfIdfKuiYJKZAX_LDCTg4bs22sj3WCoesf2CBjyDBEqImIhXX1VnTfnAhnQA-XVmllRZlmylLag79sr8bxyw';
  $apple['redirect_uri'] = home_url( '/sign-in-with-apple/'); // home_url( 'login/' );

  $response = http('https://appleid.apple.com/auth/token', [
    'grant_type' => 'authorization_code',
    'code' => $_POST['code'],
    'redirect_uri' => $apple['redirect_uri'],
    'client_id' => $apple['client_id'],
    'client_secret' => $apple['client_secret'],
  ]);

  if ( isset( $response->error ) || ! isset($response->access_token ) ) {

    require_once(  ABSPATH . 'sso-sp/simplesaml/lib/_autoload.php');
    $auth = new SimpleSAML_Auth_Simple('default-sp');

    $_SESSION['login_error'] = 'invalid_grant';
    // $ReturnTo = isset( $_SESSION['ReturnToUrl'] ) ? $_SESSION['ReturnToUrl'] : ( wp_get_referer() ? : home_url() );
    $_SESSION['ReturnToUrl'] = $ReturnTo;

    $auth->requireAuth([
      'ReturnTo' => $ReturnTo,
      'KeepPost' => FALSE,
    ]);
    \SimpleSAML\Session::getSessionFromRequest()->cleanup();
  }
  //
  // if( ! isset($response->access_token ) ) {
  //   wp_redirect( home_url( 'login' ) ); die();
  // }

  $apple['claims'] = explode('.', $response->id_token)[1];
  $apple['claims'] = json_decode(base64_decode($apple['claims']));

  $user = get_user_by( 'email', $apple['claims']->email );

  if ( ! $user ) {
    $user = get_user_by( 'login', $apple['claims']->email );
  }

  if ( $user ) {
    $user_id = $user->ID;
  } else {
    $user_id = wp_insert_user([
      'user_login' => $apple['claims']->email,
      'user_pass' => NULL,
      'user_email' => trim( $apple['claims']->email ),
      'first_name' => '',
      'last_name' => '',
      'user_registered' => date('Y-m-d H:i:s'),
      'role' => 'subscriber'
    ]);

    // var_dump( $user_id ); exit;

    // if ( $apple['claims']->is_private_email ) {
    //   update_user_meta( $user_id, 'is_private_email', true );
    // }
  }

  update_user_meta( $user_id, 'is_activated', 1 );

  // echo '<pre>'; var_dump( $data_state ); exit;

  $current_user = wp_set_current_user( $user_id );
  wp_set_auth_cookie( $user_id );

  // wp_redirect( home_url( '/login/' ) ); die();
  wp_redirect( $ReturnTo ); die();
}
