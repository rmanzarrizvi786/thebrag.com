<div class="row">
  <div class="col-12 mb-3">
    <h3>Update payment details <small class="text-muted">ID: <?php echo $sub_id; ?></small></h3>
  </div>
</div>

<form id="form-payment-details" action="#">
  <div class="row">
    <div class="col-12 mt-3">

    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <?php
      if ($paymentMethods && count($paymentMethods) > 0) : ?>
        <?php foreach ($paymentMethods as $paymentMethod) : ?>
          <h4>Current credit card details</h4>
          <div class="d-flex mb-2">
            <div>
              <?php
              if (file_exists(get_template_directory() . '/images/observer/card-types/' . $paymentMethod->card->brand . '.png')) :
                $paymentMethodCardImg = get_template_directory_uri() . '/images/observer/card-types/' . $paymentMethod->card->brand . '.png';
              else :
                $paymentMethodCardImg = get_template_directory_uri() . '/images/observer/card-types/card.png';
              endif;
              ?>
              <img src="<?php echo $paymentMethodCardImg; ?>" width="70">
            </div>
            <div class="ml-2">
              <strong>
                <?php echo ucfirst($paymentMethod->card->brand); ?>
                <span>•••• <?php echo $paymentMethod->card->last4; ?></span>
              </strong>
              <br>
              <span class="text-muted">Expires <?php
                                                $dateObj = DateTime::createFromFormat('!m', $paymentMethod->card->exp_month);
                                                echo $dateObj->format('F') . ' ' . $paymentMethod->card->exp_year;
                                                ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else : ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="row">
    <div class="col-12 mt-3">
      <h4>New credit card details</h4>
    </div>
  </div>

  <div class="row">
    <div class="col-12 mb-1">
      <input type="text" name="full_name" id="full_name" placeholder="Full Name *" maxlength="30" value="<?php echo isset($subscription->full_name) ? $subscription->full_name : ''; ?>" class="form-control" required>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <?php echo $bo->setupIntent($subscription->stripe_customer_id, $subscription); ?>
    </div>
  </div>
</form>