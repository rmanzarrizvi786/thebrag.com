<?php
if ( isset( $_POST ) ) {
  if ( isset( $_POST['cancel-auto-renew']) ) {
    $post_data = stripslashes_deep( $_POST );

    if ( wp_check_password ( $post_data['user_pass'], $current_user->user_pass) ) {

      $post_data['uniqid'] = $subscription->uniqid;
      $cancel = json_decode( $bo->cancelAutoRenew( $post_data ) );

      if ( ! $cancel->success ) {
        echo '<div class="alert alert-danger">' . $cancel->data->error->message . '</div>';
      } else {
        echo '<div class="alert alert-success">The subscription will NOT be auto-renewed!</div>
        <script>
        setTimeout(function(){
          window.location.href = "' . home_url( '/observer/magazine-subscriptions/' ) . '";
        }, 3000 );
        </script>';
      }
    } else {
      echo '<div class="alert alert-danger">Incorrect password.</div>';
    } // Incorrect password
  } // If cancel-auto-renew
} // If form is submitted
?>
<div class="row">
  <div class="col-12 mb-3">
    <h3>Cancel auto-renew <small class="text-muted">ID: <?php echo $sub_id; ?></small></h3>
  </div>
</div>

<form action="" method="post">
  <div class="row">
    <div class="col-12 mb-3">
      <strong>Please enter your password to cancel auto-renew for this subscription</h4>
      <input type="password" name="user_pass" id="user_pass" placeholder="Password" value="" class="form-control" required>
    </div>
  </div>

  <div class="row">
    <div class="col-12 mt-3">
      <div id="js-errors" class="alert alert-danger d-none" role="alert"></div>
      <div class="spinner d-none" id="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>
      <button id="btn-cancel-auto-renew" name="cancel-auto-renew" class="btn btn-danger">Cancel auto-renew</button>
    </div>
  </div>
</form>
