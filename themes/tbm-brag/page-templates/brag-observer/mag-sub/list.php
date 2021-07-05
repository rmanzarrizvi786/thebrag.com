<?php
if ($subscriptions && is_array($subscriptions) && count($subscriptions) > 0) :
  foreach ($subscriptions as $key => $subscription) :
    $subscription->paymentMethods = $bo->getPaymentMethods($subscription->stripe_customer_id);
?>
    <div class="row align-items-start mb-2<?php echo $subscription->is_gift == 'yes' ? ' is-gift' : ''; ?>">

      <div class="col-md-6 mb-4">
        <?php if (0 && $subscription->is_gift == 'yes') : ?>
          <div class="btn btn-sm banner text-uppercase text-center text-white d-inline">Gift</div>
        <?php endif; ?>
        <div>
          <h3><?php echo $subscription->sub_full_name; ?><br><small class="text-muted">ID: <?php echo $subscription->uniqid; ?></small></h3>
          <?php
          echo $subscription->shipping_address_1 ? $subscription->shipping_address_1 . '<br>' : '';
          echo $subscription->shipping_address_2 ? '<br>' . $subscription->shipping_address_2 . '<br>' : '';
          echo $subscription->shipping_city ? $subscription->shipping_city . '<br>' : '';
          echo $subscription->shipping_state ? $subscription->shipping_state . ' ' : '';
          echo $subscription->shipping_postcode ? $subscription->shipping_postcode . ' ' : '';
          echo $subscription->shipping_country ? '<br>' . $subscription->shipping_country : '';
          ?>
        </div>
      </div>
      <div class="col-md-6 mb-4 text-right">
        <?php if (isset($subscription->paymentMethods) && count($subscription->paymentMethods) > 0) : ?>
          <h3>Payment Method</h3>
          <?php foreach ($subscription->paymentMethods as $paymentMethod) : ?>
            **** **** **** <?php echo $paymentMethod->card->last4; ?> <?php echo ucfirst($paymentMethod->card->brand); ?><br>
        <?php
          endforeach;
        endif;
        // echo '<pre>'; print_r( $subscription->paymentMethods ); echo '</pre>';
        ?>
        <div class="btn-group">
          <?php if ($subscription->is_gift == 'yes') : ?>
            <button type="button" class="btn banner text-uppercase text-center text-white d-inline">Gift</button>
          <?php endif; ?>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuReference<?php echo $key; ?>">
            <!-- <a class="dropdown-item" href="<?php echo add_query_arg(['a' => 'update_shipping', 'id' => $subscription->uniqid]); ?>">Update shipping details</a> -->
            <a class="dropdown-item" href="<?php echo add_query_arg(['a' => 'update_billing_shipping', 'id' => $subscription->uniqid]); ?>">Update billing/shipping details</a>

            <?php // if ($subscription->is_gift != 'yes') : 
            ?>

            <a class="dropdown-item" href="<?php echo add_query_arg(['a' => 'update_payment', 'id' => $subscription->uniqid]); ?>">Update payment details</a>
            <?php if (isset($subscription->crm_record->Active__c) && $subscription->crm_record->Active__c) : ?>

              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="mailto:subscribe@thebrag.media?subject=Cancel%20auto-renew&body=ID:<?php echo $subscription->uniqid; ?>">Cancel auto-renew</a>
              <!-- <a class="dropdown-item" href="<?php // echo add_query_arg(['a' => 'cancel-auto-renew', 'id' => $subscription->uniqid]); 
                                                  ?>" onClick="return confirm('Are you sure?');">Cancel auto-renew</a> -->
            <?php else : ?>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?php echo add_query_arg(['a' => 'enable-auto-renew', 'id' => $subscription->uniqid]); ?>">Enable auto-renew</a>
            <?php endif; // If Active 
            ?>
            <?php // endif; // If Gift 
            ?>
          </div>
        </div>

        <div class="mt-2">
          <?php // if ($subscription->is_gift != 'yes') : 
          ?>
          <?php if (isset($subscription->crm_record->Active__c) && $subscription->crm_record->Active__c) :
          ?>
            <?php if (isset($subscription->paymentMethods) && count($subscription->paymentMethods) > 0) : ?>
              <div class="text-success">
                Active, will be auto-renewed
                <?php echo !$subscription->crm_record->Remaining_Issues__c ? 'shortly' : 'after ' . $subscription->crm_record->Remaining_Issues__c . ' issues'; ?>
              </div>
            <?php else : ?>
              <div class="text-danger">
                Please <a class="text-danger" href="<?php echo add_query_arg(['a' => 'update_payment', 'id' => $subscription->uniqid]); ?>">update payment details</a>
              </div>
            <?php endif; ?>
          <?php elseif (isset($subscription->crm_record->Active__c) && !$subscription->crm_record->Active__c && $subscription->crm_record->Remaining_Issues__c > 0) : ?>
            <div class="text-danger">Inactive, will NOT be auto-renewed after <?php echo $subscription->crm_record->Remaining_Issues__c; ?> issues</div>
          <?php else : ?>
            <div class="text-danger">Inactive, will NOT be auto-renewed.</div>
          <?php endif; // If Active 
          ?>
          <?php // endif; // If Gift 
          ?>
        </div>
      </div>
    </div>
<?php
  endforeach;
endif;
