<?php /* Template name: Magazine Subscriptions */

if (is_user_logged_in()) {
  $current_user = wp_get_current_user();
  $user_id = $current_user->ID;
} else {
  wp_redirect(wp_login_url());
}

$action = isset($_GET['a']) ? trim($_GET['a']) : NULL;
$sub_id = isset($_GET['id']) ? trim($_GET['id']) : NULL;

// Initiate Brag Observer
$bo = new BragObserver();

if (!is_null($action) && !is_null($sub_id)) {
  $subscription = $bo->getMagSubscription($sub_id);
  if ($subscription && $subscription[0]) {
    $subscription = $subscription[0];

    /* if ($subscription->is_gift == 'yes' && in_array($action, ['update_billing', 'update_payment', 'cancel-auto-renew', 'enable-auto-renew'])) {
      wp_redirect(home_url('/observer/magazine-subscriptions/'));
    } */
  }
}

get_header();
// get_template_part('page-templates/brag-observer/header');
?>

<div class="ad-billboard ad-billboard-top container py-1 py-md-2">
  <div class="mx-auto text-center">
    <?php render_ad_tag('leaderboard'); ?>
  </div>
</div>

<div class="container bg-yellow rounded-top p-2">
  <?php get_template_part('template-parts/account/header'); ?>
  <div class="row justify-content-center align-items-start">
    <?php get_template_part('template-parts/account/menu', 'left'); ?>
    <div class="col-12 col-md-9 p-3">
      <div class="row">
        <div class="col-12 mb-3">
          <h2>Magazine Subscriptions</h2>
        </div>
      </div>

      <div class="row align-items-start">
        <div class="col-md-3 mb-3">
          <div style="position: sticky; top: 100px; text-align: center;">
            <?php $current_issue_img = file_get_contents('https://au.rollingstone.com/wp-json/tbm_mag_sub/v1/next_issue_img');
            ?>
            <img src="<?php echo stripslashes(str_replace('"', '', $current_issue_img)); ?>" class="img-fluid">
          </div>
        </div>
        <div class="col-md-9 px-2">
          <div class="row">
            <div class="col-12">
              <?php
              $subscriptions = $bo->getMagSubscriptions($current_user->user_email);
              if ($subscriptions && is_array($subscriptions) && count($subscriptions) > 0) {
                $sub_ids = wp_list_pluck($subscriptions, 'uniqid');
              } else {
                $sub_ids = [];
              }

              if (!is_null($action) && !is_null($sub_id) && in_array($sub_id, $sub_ids)) {
                // $subscription = $bo->getMagSubscription( $sub_id );
                if ($subscription) {
                  // $subscription = $subscription[0];
                  if ('update_billing_shipping' == $action) {
                    include get_template_directory() . '/page-templates/brag-observer/mag-sub/update-billing-shipping.php';
                  } elseif ('update_shipping' == $action) {
                    include get_template_directory() . '/page-templates/brag-observer/mag-sub/update-shipping.php';
                  } elseif ('update_billing' == $action) {
                    include get_template_directory() . '/page-templates/brag-observer/mag-sub/update-billing.php';
                  } elseif ('update_payment' == $action) {
                    $paymentMethods = $bo->getPaymentMethods($subscription->stripe_customer_id);
                    include get_template_directory() . '/page-templates/brag-observer/mag-sub/update-payment.php';
                  } elseif ('cancel-auto-renew' == $action) {
                    include get_template_directory() . '/page-templates/brag-observer/mag-sub/cancel-auto-renew.php';
                  } elseif ('enable-auto-renew' == $action) {
                    include get_template_directory() . '/page-templates/brag-observer/mag-sub/enable-auto-renew.php';
                  }
                } // If $subscription
              } else { // $action is NULL i.e. show all active subscriptions
                if ($subscriptions && is_array($subscriptions) && count($subscriptions) > 0) {
                  include get_template_directory() . '/page-templates/brag-observer/mag-sub/list.php';
                } // If $subscriptions
                else {
              ?>
                  <div class="jumbotron">
                    <h4 class="text-center" style="line-height: 1.7">You currently don't have any <a href="https://au.rollingstone.com/" target="_blank">Rolling Stone Australia</a> Magazine Subscriptions, you can subscribe <a href="https://au.rollingstone.com/subscribe-magazine/" target="_blank">here</a>.</h4>
                  </div>
              <?php
                }
              } // $action is NULL
              ?>
            </div>
          </div>
        </div>
      </div><!-- .row -->
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>

<style>
  .is-gift {
    position: relative;
  }

  .is-gift .banner {
    width: auto;
    padding: .25rem .5rem;
    background-color: #d32531;
  }
</style>

<?php
get_footer();
// get_template_part('page-templates/brag-observer/footer');
