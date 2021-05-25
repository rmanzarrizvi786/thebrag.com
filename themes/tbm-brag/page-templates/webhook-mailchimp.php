<?php
/*
 * Template Name: Webhook - MailChimp
 */

if ( isset( $_POST['type'] ) && '' != $_POST['type'] ) {

  $type = $_POST['type'];
  $email = isset( $_POST['data']['email'] ) ? $_POST['data']['email'] : null;
  if ( is_null( $email ) )
    exit;
  $user = get_user_by( 'email', $email );

  switch ( $type ) :
    case 'unsubscribe':
      if ( $user ) {
        $wpdb->update( $wpdb->prefix . 'observer_subs',
          [
            'status' => 'unsubscribed',
            'status_mailchimp' => 'unsubscribed',
            'unsubscribed_at' => current_time( 'mysql' ),
          ],
          [
            'user_id' => $user->ID
          ]
        );
      } else {
        wp_mail( 'sachin.patel@thebrag.media', 'Error in Webhook - MailChimp Unsub', 'User with email ' . $email . ' does not exist', [ 'Content-Type: text/html; charset=UTF-8' ] );
      }
      break;

    case 'subscribe':
      if ( $user ) {
        $wpdb->update( $wpdb->prefix . 'observer_subs',
          [
            'status' => 'subscribed',
            'status_mailchimp' => 'subscribed',
            'subscribed_at' => current_time( 'mysql' ),
          ],
          [
            'user_id' => $user->ID
          ]
        );
      } else {
        wp_mail( 'sachin.patel@thebrag.media', 'Error in Webhook - MailChimp Sub', 'User with email ' . $email . ' does not exist', [ 'Content-Type: text/html; charset=UTF-8' ] );
      }
      break;

    default:
      break;
  endswitch;
} else {
  wp_redirect( home_url() ); exit;
}
