<?php
/*
 * Template Name: Login Redirect
 */
$redirectTo = home_url( '/' );
if ( isset( $_GET['returnTo'] ) && '' != $_GET['returnTo'] ) {
  $redirectTo = urldecode( $_GET['returnTo'] );
  if ( is_user_logged_in() ) {
    // $redirectTo .= '?ss=1';
    $redirectTo = add_query_arg( 'ss', '1', $redirectTo );
  } else {
    $parsed_url = parse_url( $redirectTo );
    if ( isset( $parsed_url['query'] ) ) {
      parse_str( $parsed_url['query'], $url_params );

      if ( isset( $url_params['oc'] ) ) {
        $data = @unserialize( base64_decode( $url_params['oc'] ) );
        $oc_token = get_user_meta( $data['id'], 'oc_token', true );
        if( $oc_token == $data['oc_token'] ) {
          $user_id = $data['id'];
          $user = get_user_by( 'id', $user_id );
          if( $user ) { // logs the user in
            wp_set_current_user( $user_id, $user->user_login );
            wp_set_auth_cookie( $user_id );
            do_action( 'wp_login', $user->user_login, $user );
          }
        }
      }
    }
  }
}
wp_redirect( $redirectTo ); exit;
