<?php
if ( isset( $_POST ) ) {
  if ( isset( $_POST['update-billing']) ) {
    $post_data = stripslashes_deep( $_POST );
    $post_data['uniqid'] = $subscription->uniqid;

    $update = json_decode( $bo->updateBillingDetails( $post_data ) );
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
  } // If update-billing
} // If form is submitted
?>
<div class="row">
  <div class="col-12 mb-3">
    <h3>Update billing details <small class="text-muted">ID: <?php echo $sub_id; ?></small></h3>
  </div>
</div>

<form action="" method="post">
  <div class="row">
    <div class="col-12 mb-3">
      <h4>You Name</h4>
      <input type="text" name="full_name" id="full_name" placeholder="Full Name *" maxlength="30" value="<?php echo isset( $post_data['full_name'] ) ? $post_data['full_name'] : ( isset( $subscription->full_name ) ? $subscription->full_name : '' ); ?>" class="form-control" required>
    </div>
  </div>

  <div class="row">
    <div class="col-12 mt-3">
      <h4>Billing Address</h4>
    </div>
    <div class="col-md-6 mb-3">
      Address Line 1
      <input type="text" name="sub_address_1" placeholder="Address Line 1 *" maxlength="30" value="<?php echo isset( $post_data['sub_address_1'] ) ? $post_data['sub_address_1'] : ( isset( $subscription->address_1 ) ? $subscription->address_1 : '' ); ?>" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
      Address Line 2
      <input type="text" name="sub_address_2" placeholder="Address Line 2" maxlength="30" value="<?php echo isset( $post_data['sub_address_2'] ) ? $post_data['sub_address_2'] : ( isset( $subscription->address_2 ) ? $subscription->address_2 : '' ); ?>" class="form-control">
    </div>
    <div class="col-md-3 mb-3">
      City
      <input type="text" name="sub_city" placeholder="City *" maxlength="30" value="<?php echo isset( $post_data['sub_city'] ) ? $post_data['sub_city'] : ( isset( $subscription->city ) ? $subscription->city : '' ); ?>" class="form-control" required>
    </div>
    <div class="col-md-3 mb-3">
      State
      <input type="text" name="sub_state" placeholder="State *" maxlength="30" value="<?php echo isset( $post_data['sub_state'] ) ? $post_data['sub_state'] : ( isset( $subscription->state ) ? $subscription->state : '' ); ?>" class="form-control" required>
    </div>
    <div class="col-md-3 mb-3">
      Postcode (Zip)
      <input type="text" name="sub_postcode" placeholder="Postcode (Zip) *" maxlength="10" value="<?php echo isset( $post_data['sub_postcode'] ) ? $post_data['sub_postcode'] : ( isset( $subscription->postcode ) ? $subscription->postcode : '' ); ?>" class="form-control" required>
    </div>
    <div class="col-md-3 mb-3">
      Country
      <select class="form-control" name="sub_country" required>
        <option value="" disabled selected>Country *</option>
        <?php foreach ( BragObserver::getCountries() as $country_code => $country ) : ?>
          <option
            value="<?php echo $country; ?>"
            <?php echo isset( $post_data['sub_country'] ) && $country == $post_data['sub_country'] ? ' selected' : ( isset( $subscription->country ) && $country == $subscription->country ? ' selected' : '' );
            echo '' == $country_code ? ' disabled' : ''; ?>
            >
            <?php echo $country; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="row">
    <div class="col-12 mt-3">
      <label>
        <input type="checkbox" name="make-shipping-same" id="make-shipping-same" value="1">
        Update my shipping details to same as above
      </label>
    </div>
  </div>

  <div class="row">
    <div class="col-12 mt-3">
      <div id="js-errors" class="alert alert-danger d-none" role="alert"></div>
      <div class="spinner d-none" id="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>
      <button id="btn-update-billing" name="update-billing" class="btn btn-dark">Submit</button>
    </div>
  </div>

</form>
