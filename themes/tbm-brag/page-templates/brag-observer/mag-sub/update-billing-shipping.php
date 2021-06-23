<?php
if (isset($_POST)) {
  if (isset($_POST['update-billing-shipping'])) {
    $post_data = stripslashes_deep($_POST);
    $post_data['uniqid'] = $subscription->uniqid;
    $update = json_decode($bo->updateBillingShippingDetails($post_data));
    // echo '<pre>'; var_dump( $update ); echo '</pre>';
    if (!$update->success) {
      echo '<div class="alert alert-danger">' . $update->data->error->message . '</div>';
    } else {
      echo '<div class="alert alert-success">Your details have been updated!</div>
      <script>
      setTimeout(function(){
        window.location.href = "' . home_url('/observer/magazine-subscriptions/') . '";
      }, 3000 );
      </script>';
    }
  } // If update-billing-shipping
} // If form is submitted
?>
<form action="" method="post">
  <div class="row">
    <div class="col-12">
      <div id="billing_address_wrap">
        <div class="row">
          <div class="col-12 mb-3">
            <h3>Billing details <small class="text-muted">ID: <?php echo $sub_id; ?></small></h3>
          </div>
          <div class="col-12 mb-3">
            <h4>You Name</h4>
            <input type="text" name="full_name" id="full_name" placeholder="Full Name *" maxlength="30" value="<?php echo isset($post_data['full_name']) ? $post_data['full_name'] : (isset($subscription->full_name) ? $subscription->full_name : ''); ?>" class="form-control" data-bind="sub_full_name" required>
          </div>

          <div class="col-12 mt-3">
            <h4>Billing Address</h4>
          </div>
          <div class="col-md-6 mb-3">
            Address Line 1
            <input type="text" name="sub_address_1" id="sub_address_1" placeholder="Address Line 1 *" maxlength="30" value="<?php echo isset($post_data['sub_address_1']) ? $post_data['sub_address_1'] : (isset($subscription->address_1) ? $subscription->address_1 : ''); ?>" class="form-control" data-bind="shipping_address_1" required>
          </div>
          <div class="col-md-6 mb-3">
            Address Line 2
            <input type="text" name="sub_address_2" id="sub_address_2" placeholder="Address Line 2" maxlength="30" value="<?php echo isset($post_data['sub_address_2']) ? $post_data['sub_address_2'] : (isset($subscription->address_2) ? $subscription->address_2 : ''); ?>" data-bind="shipping_address_2" class="form-control">
          </div>
          <div class="col-lg-3 col-md-6 mb-3">
            City
            <input type="text" name="sub_city" id="sub_city" placeholder="City *" maxlength="30" value="<?php echo isset($post_data['sub_city']) ? $post_data['sub_city'] : (isset($subscription->city) ? $subscription->city : ''); ?>" class="form-control" data-bind="shipping_city" required>
          </div>
          <div class="col-lg-3 col-md-6 mb-3">
            State
            <input type="text" name="sub_state" id="sub_state" placeholder="State *" maxlength="30" value="<?php echo isset($post_data['sub_state']) ? $post_data['sub_state'] : (isset($subscription->state) ? $subscription->state : ''); ?>" class="form-control" data-bind="shipping_state" required>
          </div>
          <div class="col-lg-3 col-md-6 mb-3">
            Postcode (Zip)
            <input type="text" name="sub_postcode" id="sub_postcode" placeholder="Postcode (Zip) *" maxlength="10" value="<?php echo isset($post_data['sub_postcode']) ? $post_data['sub_postcode'] : (isset($subscription->postcode) ? $subscription->postcode : ''); ?>" data-bind="shipping_postcode" class="form-control" required>
          </div>
          <div class="col-lg-3 col-md-6 mb-3">
            Country
            <select class="form-control" name="sub_country" id="sub_country" data-bind="shipping_country" required>
              <option value="" disabled selected>Country *</option>
              <?php foreach (BragObserver::getCountries() as $country_code => $country) : ?>
                <option value="<?php echo $country; ?>" <?php echo isset($post_data['sub_country']) && $country == $post_data['sub_country'] ? ' selected' : (isset($subscription->country) && $country == $subscription->country ? ' selected' : '');
                                                        echo '' == $country_code ? ' disabled' : ''; ?>>
                  <?php echo $country; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div><!-- #billing_address_wrap -->

      <div class="row">
        <div class="col-12 my-3">
          <label>
            <input type="checkbox" name="make-shipping-same" id="make-shipping-same" value="1">
            Update my shipping details to same as above
          </label>
        </div>
      </div>

    </div>

    <div class="col-12">
      <div id="shipping_address_wrap">
        <div class="row">
          <div class="col-12 mb-3">
            <h3>Shipping details <small class="text-muted">ID: <?php echo $sub_id; ?></small></h3>
          </div>
          <div class="col-12 mb-3">
            <h4>Subscriber Name <span class="text-danger">*</span></h4>
            <input type="text" name="sub_full_name" id="sub_full_name" placeholder="Full Name" value="<?php echo isset($post_data['sub_full_name']) ? $post_data['sub_full_name'] : (isset($subscription->sub_full_name) ? $subscription->sub_full_name : ''); ?>" class="form-control" required>
          </div>
        </div>

        <div class="row">
          <div class="col-12 mt-3">
            <h4>Shipping Address</h4>
          </div>

          <div class="col-md-6 mb-3">
            Address Line 1 <span class="text-danger">*</span>
            <input type="text" name="shipping_address_1" id="shipping_address_1" placeholder="" maxlength="30" value="<?php echo isset($post_data['shipping_address_1']) ? $post_data['shipping_address_1'] : (isset($subscription->shipping_address_1) ? $subscription->shipping_address_1 : ''); ?>" class="form-control">
          </div>
          <div class="col-md-6 mb-3">
            Address Line 2
            <input type="text" name="shipping_address_2" id="shipping_address_2" placeholder="" maxlength="30" value="<?php echo isset($post_data['shipping_address_2']) ? $post_data['shipping_address_2'] : (isset($subscription->shipping_address_2) ? $subscription->shipping_address_2 : ''); ?>" class="form-control">
          </div>
          <div class="col-lg-3 col-md-6 mb-3">
            City <span class="text-danger">*</span>
            <input type="text" name="shipping_city" id="shipping_city" placeholder="" maxlength="30" value="<?php echo isset($post_data['shipping_city']) ? $post_data['shipping_city'] : (isset($subscription->shipping_city) ? $subscription->shipping_city : ''); ?>" class="form-control" required>
          </div>
          <div class="col-lg-3 col-md-6 mb-3">
            State <span class="text-danger">*</span>
            <input type="text" name="shipping_state" id="shipping_state" placeholder="" maxlength="30" value="<?php echo isset($post_data['shipping_state']) ? $post_data['shipping_state'] : (isset($subscription->shipping_state) ? $subscription->shipping_state : ''); ?>" class="form-control" required>
          </div>
          <div class="col-lg-3 col-md-6 mb-3">
            Postcode (Zip) <span class="text-danger">*</span>
            <input type="text" name="shipping_postcode" id="shipping_postcode" placeholder="" maxlength="10" value="<?php echo isset($post_data['shipping_postcode']) ? $post_data['shipping_postcode'] : (isset($subscription->shipping_postcode) ? $subscription->shipping_postcode : ''); ?>" class="form-control">
          </div>
          <div class="col-lg-3 col-md-6 mb-3">
            Country <span class="text-danger">*</span>
            <select class="form-control" name="shipping_country" id="shipping_country" required>
              <option value="" disabled selected></option>
              <?php foreach (BragObserver::getCountries() as $country_code => $country) : ?>
                <option value="<?php echo $country; ?>" <?php echo isset($post_data['shipping_country']) && $country == $post_data['shipping_country'] ? ' selected' : (isset($subscription->shipping_country) && $country == $subscription->shipping_country ? ' selected' : '');
                                                        echo '' == $country_code ? ' disabled' : ''; ?>>
                  <?php echo $country; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
    </div><!-- #shipping_address_wrap -->
  </div>

  <div class="row">
    <div class="col-12 mt-3">
      <div id="js-errors" class="alert alert-danger d-none" role="alert"></div>
      <div class="spinner d-none" id="spinner">
        <div class="double-bounce1"></div>
        <div class="double-bounce2"></div>
      </div>
      <button id="btn-update-billing-shipping" name="update-billing-shipping" class="btn btn-dark">Submit</button>
    </div>
  </div>

  <style>
    #shipping_address_wrap.same-as-billing:after {
      content: "";
      display: block;
      position: absolute;
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(255, 255, 255, .85);
    }
  </style>

  <script>
    jQuery(document).ready(function($) {
      if ($('#make-shipping-same').is(':checked')) {
        $('#shipping_address_wrap').addClass('same-as-billing');
      } else {
        $('#shipping_address_wrap').removeClass('same-as-billing');
      }

      $('#make-shipping-same').on('change', function() {
        if ($(this).is(':checked')) {
          $('#shipping_address_wrap').addClass('same-as-billing');
          $('#billing_address_wrap').find('input,select').each(function(i) {
            var bind = $('#' + $(this).data('bind'));
            bind.data('original', bind.val());
            bind.val($(this).val());
          });
        } else {
          $('#billing_address_wrap').find('input,select').each(function(i) {
            var bind = $('#' + $(this).data('bind'));
            $(bind).val($(bind).data('original'));
          });
          $('#shipping_address_wrap').removeClass('same-as-billing');
        }
      });

      $('#billing_address_wrap').find('input,select').each(function(i) {
        $(this).on('change', function() {
          if ($('#make-shipping-same').is(':checked')) {
            var bind = $('#' + $(this).data('bind'));
            bind.val($(this).val());
          }
        });
      });
    });
  </script>
</form>