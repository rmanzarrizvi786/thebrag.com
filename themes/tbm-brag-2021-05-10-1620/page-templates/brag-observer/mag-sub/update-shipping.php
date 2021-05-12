<?php
if ( isset( $_POST ) ) {
  if ( isset( $_POST['update-shipping']) ) {
    $post_data = stripslashes_deep( $_POST );
    $post_data['uniqid'] = $subscription->uniqid;
    $update = json_decode( $bo->updateShippingDetails( $post_data ) );
    // echo '<pre>'; var_dump( $update ); echo '</pre>';
    if ( ! $update->success ) {
      echo '<div class="alert alert-danger">' . $update->data->error->message . '</div>';
    } else {
      echo '<div class="alert alert-success">Your details have been updated!</div>
      <script>
      setTimeout(function(){
        window.location.href = "' . home_url( '/observer/magazine-subscriptions/' ) . '";
      }, 3000 );
      </script>';
    }
  } // If update-shipping
} // If form is submitted
?>
<form action="" method="post">
  <div class="row">
    <div class="col-12 mb-3">
      <h3>Update shipping details <small class="text-muted">ID: <?php echo $sub_id; ?></small></h3>
    </div>
  </div>

  <div id="shipping_address_wrap">
    <div class="row">
      <div class="col-12 mb-3">
        <h4>Subscriber Name <span class="text-danger">*</span></h4>
        <input type="text" name="sub_full_name" id="sub_full_name" placeholder="Full Name" value="<?php echo isset( $post_data['sub_full_name'] ) ? $post_data['sub_full_name'] : ( isset( $subscription->sub_full_name ) ? $subscription->sub_full_name : '' ); ?>" class="form-control" required>
      </div>
    </div>

    <div class="row">
      <div class="col-12 mt-3">
        <h4>Shipping Address</h4>
      </div>

      <div class="col-md-6 mb-3">
        Address Line 1 <span class="text-danger">*</span>
        <input type="text" name="shipping_address_1" placeholder="" maxlength="30" value="<?php echo isset( $post_data['shipping_address_1'] ) ? $post_data['shipping_address_1'] : ( isset( $subscription->shipping_address_1 ) ? $subscription->shipping_address_1 : '' ); ?>" class="form-control">
      </div>
      <div class="col-md-6 mb-3">
        Address Line 2
        <input type="text" name="shipping_address_2" placeholder="" maxlength="30" value="<?php echo isset( $post_data['shipping_address_2'] ) ? $post_data['shipping_address_2'] : ( isset( $subscription->shipping_address_2 ) ? $subscription->shipping_address_2 : '' ); ?>" class="form-control">
      </div>
      <div class="col-md-3 mb-3">
        City <span class="text-danger">*</span>
        <input type="text" name="shipping_city" placeholder="" maxlength="30" value="<?php echo isset( $post_data['shipping_city'] ) ? $post_data['shipping_city'] : ( isset( $subscription->shipping_city ) ? $subscription->shipping_city : '' ); ?>" class="form-control" required>
      </div>
      <div class="col-md-3 mb-3">
        State <span class="text-danger">*</span>
        <input type="text" name="shipping_state" placeholder="" maxlength="30" value="<?php echo isset( $post_data['shipping_state'] ) ? $post_data['shipping_state'] : ( isset( $subscription->shipping_state ) ? $subscription->shipping_state : '' ); ?>" class="form-control" required>
      </div>
      <div class="col-md-3 mb-3">
        Postcode (Zip) <span class="text-danger">*</span>
        <input type="text" name="shipping_postcode" placeholder="" maxlength="10" value="<?php echo isset( $post_data['shipping_postcode'] ) ? $post_data['shipping_postcode'] : ( isset( $subscription->shipping_postcode ) ? $subscription->shipping_postcode : '' ); ?>" class="form-control">
      </div>
      <div class="col-md-3 mb-3">
        Country <span class="text-danger">*</span>
        <select class="form-control" name="shipping_country" required>
          <option value="" disabled selected></option>
          <?php foreach ( BragObserver::getCountries() as $country_code => $country ) : ?>
            <option
              value="<?php echo $country; ?>"
              <?php echo isset( $post_data['shipping_country'] ) && $country == $post_data['shipping_country'] ? ' selected' : ( isset( $subscription->shipping_country ) && $country == $subscription->shipping_country ? ' selected' : '' );
              echo '' == $country_code ? ' disabled' : ''; ?>
              >
              <?php echo $country; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <?php if ( $subscription->is_gift != 'yes' ) : ?>
    <div class="row">
      <div class="col-12 mt-3">
        <label>
          <input type="checkbox" name="make-billing-same" id="make-billing-same" value="1">
          Update my billing details to same as above
        </label>
      </div>
    </div>
    <?php endif; // If NOT Gift ?>

    <div class="row">
      <div class="col-12 mt-3">
        <div id="js-errors" class="alert alert-danger d-none" role="alert"></div>
        <div class="spinner d-none" id="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>
        <button id="btn-update-shipping" name="update-shipping" class="btn btn-dark">Submit</button>
      </div>
    </div>
  </div><!-- #shipping_address_wrap -->
</form>
