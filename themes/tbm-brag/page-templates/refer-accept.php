<?php /* Template name: Accept Refer a friend (Observer) */


$list_id = isset( $_GET['l'] ) ? absint( $_GET['l'] ) : null;
$list = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}observer_lists WHERE id = '{$list_id}' ");

if ( is_user_logged_in() ) {
  if( $list ) {
    $redirect_to = home_url( '/observer/' . $list->slug );
  } else {
    $redirect_to = home_url( '/observer/' );
  }
  wp_redirect( $redirect_to ); exit;
}

if( $list ) {

  if ( isset( $_GET['oc'] ) ) {
    $data = @unserialize( base64_decode( $_GET['oc'] ) );
    if ( $data ) {
      $oc_token = get_user_meta( $data['id'], 'oc_token', true );
      if( $oc_token == $data['oc_token'] ) { // checks whether the decoded code given is the same as the one in the data base
        $referrer_id = $data['id'];
        if ( get_user_meta( $referrer_id, 'refer_code', true ) ) {
          $refer_code = get_user_meta( $referrer_id, 'refer_code', true );
        } else {

          do {
            $refer_code = substr( md5(uniqid( $referrer_id, true) ), 0, 8 );
          } while ( ! check_unique_refer_code( $refer_code ) );

          update_user_meta( $referrer_id, 'refer_code', $refer_code );
        }
      }
    }
  } else {
    $refer_code = isset( $_GET['rc'] ) ? sanitize_text_field( $_GET['rc'] ) : null;
    $referrer_id = $wpdb->get_var( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'refer_code' AND meta_value = '{$refer_code}' LIMIT 1" );
  }

  if ( $referrer_id && isset( $refer_code ) ) {
    if ( isset( $_GET['email'] ) ) {
      $redirect_to = home_url( '/observer/' . $list->slug . '/?rc=' . $refer_code . '&email=' . trim( sanitize_email ( $_GET['email'] ) ) );
    } else {
      $redirect_to = home_url( '/observer/' . $list->slug . '/?rc=' . $refer_code );
    }
  } else {
    $redirect_to = home_url( '/observer/' . $list->slug );
  }
} else {
  $redirect_to = home_url( '/observer/' );
}
wp_redirect( $redirect_to ); exit;

get_template_part( 'page-templates/brag-observer/header' );
?>

<style>
</style>

<div class="row mx-0 justify-content-center" style="height: calc(100vh - 80px);">
  <div class="d-flex flex-column col-lg-6 col-md-12 col-sm-12 justify-content-center">
    <div class="my-5">
      <?php
      {
      if ( is_null( $refer_code ) || ! $referrer_id ) {
      ?>
        <div class="alert alert-info">That link is invalid, but we can't stop you from joining us!</div>
      <?php
      } // $c is not set
      ?>
      <h2 class="text-danger">You owe your friend big time.</h2>
      <h4 class="">Well technically, we owe your friend for referring you here. Maybe we’ll send them some cookies.</h3><br>
      <h4 class="">We're <strong>The Brag Observer</strong> - the free business newsletter landing in your inbox every morning. It sorta goes like this: Wake up. Read the Brew. Have a chuckle. Be in the know.</h3>
      <form action="" method="post" id="observer-subscribe-form2" name="observer-subscribe-form" class="mt-4">
        <div class="observer-sub-form justify-content-center">
          <input type="hidden" name="list" id="modal-list-subscribe2" value="<?php echo $list->id; ?>">
          <input type="email" name="email" id="observer-sub-email" class="email form-control" placeholder="Your email" value="">
          <input type="submit" value="Subscribe" name="subscribe" class="button btn rounded">
          <div class="loading mx-3" style="display: none;"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>
        </div>

        <div class="alert alert-info d-none js-msg-subscribe"></div>
        <div class="alert alert-danger d-none js-errors-subscribe"></div>
      </form>
      <?php
      } // If $list_id is invalid
      ?>
    </div>
</div>
</div>

<?php
get_template_part( 'page-templates/brag-observer/footer' );
