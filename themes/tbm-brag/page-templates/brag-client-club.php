<?php
/*
* Template Name: Brag Client Club (Index)
*/

get_header('brag-client-club');

$current_url = home_url(add_query_arg([], $GLOBALS['wp']->request));

/**
 * Update DB as joined if already logged in as invitee
 */
$current_user = wp_get_current_user();
if ($wpdb->get_var("SELECT COUNT(1) FROM {$wpdb->prefix}client_club_members WHERE `email` = '{$current_user->user_email}' AND `status` = 'invited' LIMIT 1")) {
  $wpdb->update(
    $wpdb->prefix . 'client_club_members',
    [
      'status' => 'joined',
      'user_id' => $current_user->ID,
      'joined_at' => current_time('mysql')
    ],
    [
      'email' => $current_user->user_email
    ],
    ['%s', '%d', '%s'],
    ['%s']
  );
}

/**
 * Update DB as active if already logged in has joined
 * Add Auth0 app_metadata
 */
if ($wpdb->get_var("SELECT COUNT(1) FROM {$wpdb->prefix}client_club_members WHERE `email` = '{$current_user->user_email}' AND `status` = 'joined' LIMIT 1")) {
  require_once WP_PLUGIN_DIR . '/tbm-brag-client-club/tbm-brag-client-club.php';
  $bcc = new \TBM\bragClientClub();
  $auth0_user = $bcc->updateStatus(get_current_user_id(), 'active');
}

/**
 * RS AU Mag Subscriptions
 */
require_once WP_PLUGIN_DIR . '/brag-observer/brag-observer.php';
$bo = new \BragObserver();
$current_user = wp_get_current_user();
$subscriptions = $bo->getMagSubscriptions($current_user->user_email);

// If there are no subscriptions, and if user is active member, create one
$has_rsmag_subscription = false;
$member_status = $wpdb->get_var("SELECT `status` FROM {$wpdb->prefix}client_club_members WHERE `email` = '{$current_user->user_email}'  LIMIT 1");

if (!$subscriptions) {
} else {
  foreach ($subscriptions as $key => $subscription) {
    if (isset($subscription->crm_record)) {
      if ('inactive' == $member_status) { // Deactivate RS Mag Sub if member is still active
        $bo->cancelSubscription(['uniqid' => $subscription->uniqid]);
        $has_rsmag_subscription = false;
      } else if ('active' == $member_status) { // Activate RS Mag Sub if member is still active
        if (isset($subscription->crm_record->Active__c) && $subscription->crm_record->Active__c) {
          $has_rsmag_subscription = true;
        } else {
          $bo->enableAutoRenew(['uniqid' => $subscription->uniqid]);
          $has_rsmag_subscription = true;
        }
      }
    }
  }
}
?>

<div class="hero-wrap">
  <div class="text-primary content container p-r h-100 d-flex flex-wrap hero">
    <div class="py-3" style="z-index: 3;">
      <div class="d-flex col-12">
        <div class="logo-wrap">
          <a href="https://thebrag.com/media" target="_blank"><img src="https://cdn.thebrag.com/tbm/The-Brag-Media-light.svg" width="200" height="19" alt="The Brag Media" title="The Brag Media" loading="lazy"></a>
        </div>
      </div>
      <div class="d-flex">
        <div class="col-12 col-md-9">
          <h1 class="content-heading text-center">
            Brag<br>Client<br>Club
          </h1>
        </div>
      </div>

      <?php if ($has_rsmag_subscription) { ?>
        <div class="d-flex flex-column align-items-center">
          <h2 class="text-center">Rolling Stone Australia Magazine Subscriptions</h2>
          <div class="d-flex flex-column flex-md-row align-items-start">
            <div class="col-12 col-md-3 p-2">
              <?php
              $res_next_mag_issue_cover = wp_remote_get('https://au.rollingstone.com/wp-json/tbm_mag_sub/v1/next_issue_img');
              if (is_array($res_next_mag_issue_cover) && !is_wp_error($res_next_mag_issue_cover)) {
                $body_next_mag_issue_cover    = $res_next_mag_issue_cover['body'];
                $body_next_mag_issue_cover = json_decode($body_next_mag_issue_cover);
                if ($body_next_mag_issue_cover) {
              ?>
                  <img src="<?php echo $body_next_mag_issue_cover; ?>">
              <?php
                }
              }
              ?>
            </div>
            <div class="d-flex flex-column py-2 py-md-0">
              <?php
              foreach ($subscriptions as $key => $subscription) {
              ?>
                <div class="p-2 mt-2" style="background-color: #fff; border-radius: .5rem">
                  <div>
                    <h3><?php echo $subscription->crm_record->Name; ?></h3>
                    <?php
                    echo $subscription->crm_record->Shipping_Address_1__c ? $subscription->crm_record->Shipping_Address_1__c . '<br>' : '';
                    echo $subscription->crm_record->Shipping_Address_2__c ? '<br>' . $subscription->crm_record->Shipping_Address_2__c . '<br>' : '';
                    echo $subscription->crm_record->Shipping_City__c ? $subscription->crm_record->Shipping_City__c . '<br>' : '';
                    echo $subscription->crm_record->Shipping_State__c ? $subscription->crm_record->Shipping_State__c . ' ' : '';
                    echo $subscription->crm_record->Shipping_Postcode__c ? $subscription->crm_record->Shipping_Postcode__c . ' ' : '';
                    echo $subscription->crm_record->Shipping_Country__c ? '<br>' . $subscription->crm_record->Shipping_Country__c : '';
                    ?>
                    <br><br>
                    <a href="<?php echo add_query_arg(['a' => 'update_billing_shipping', 'id' => $subscription->uniqid], home_url('/observer/magazine-subscriptions/')); ?>" target="_blank">Update shipping details</a>
                  </div>
                </div>
              <?php
              } // For Each $subscription
              ?>
            </div>
          </div>
        </div>
    </div>
    <?php } else {
        if ('active' == $member_status) {
          // Show form for Mag Subscription

          $userinfo = get_userdata($current_user->ID);
    ?>
      <form id="form-rs-mag-sub" method="POST">
        <div class="d-flex align-items-center">
          <div class="col-12 col-md-7">
            <h2 class="text-center">Please submit the form to activate your membership</h2>
            <div class="d-flex flex-wrap w-100" id="shipping_address_wrap">

              <div class="col-12 px-1">
                <label for="sub_full_name">Full Name *</label>
                <input type="text" name="sub_full_name" id="sub_full_name" placeholder="" maxlength="30" value="<?php echo isset($userinfo->first_name) ? $userinfo->first_name : ''; ?><?php echo isset($userinfo->last_name) ? ' ' . $userinfo->last_name : ''; ?>" required class="form-control">
              </div>

              <div class="col-12 mt-2 px-1">
                <label for="shipping_address_1">Address Line 1 *</label>
                <input type="text" name="shipping_address_1" id="shipping_address_1" placeholder="" maxlength="30" value="<?php echo get_user_meta($current_user->ID, 'address_1', true) ?: ''; ?>" required class="form-control">
              </div>

              <div class="col-12 col-md-6 mt-2 px-1">
                <label for="shipping_address_2">Address Line 2</label>
                <input type="text" name="shipping_address_2" id="shipping_address_2" placeholder="" maxlength="30" value="<?php echo get_user_meta($current_user->ID, 'address_2', true) ?: ''; ?>" class="form-control">
              </div>

              <div class="col-12 col-md-6 mt-2 px-1">
                <label for="shipping_city">City *</label>
                <input type="text" name="shipping_city" id="shipping_city" placeholder="" maxlength=" 30" value="<?php echo get_user_meta($current_user->ID, 'city', true) ?: ''; ?>" required class="form-control">
              </div>

              <div class="col-12 col-md-4 mt-2 px-1">
                <label for="shipping_state">State *</label>
                <input type="text" name="shipping_state" id="shipping_state" placeholder="" maxlength="30" value="<?php echo get_user_meta($current_user->ID, 'state', true) ?: ''; ?>" required class="form-control">
              </div>

              <div class="col-12 col-md-4 mt-2 px-1">
                <label for="shipping_postcode">Postcode (Zip) *</label>
                <input type="text" name="shipping_postcode" id="shipping_postcode" placeholder="" maxlength="10" value="<?php echo get_user_meta($current_user->ID, 'postcode', true) ?: ''; ?>" required class="form-control">
              </div>

              <div class="col-12 col-md-4 mt-2 px-1">
                <label for="shipping_country">Country *</label>
                <span class="custom-dropdown custom-dropdown--white">
                  <select class="custom-dropdown__select custom-dropdown--white form-control" name="shipping_country" id="shipping_country" required>
                    <option value="" disabled selected></option>
                    <?php
                    $user_country = get_user_meta($current_user->ID, 'country', true) ?: 'AU';
                    foreach ($bo::getCountries() as $country_code => $country) :
                    ?>
                      <option value="<?php echo $country_code; ?>" <?php echo $country_code === $user_country ? ' selected' : ''; ?><?php echo '' == $country_code ? ' disabled' : ''; ?>><?php echo $country; ?></option>
                    <?php endforeach; ?>
                  </select>
                </span>
              </div>

              <div class="col-12 col-md-6 mt-2 px-1">
                <label for="company_name">Company Name *</label>
                <input type="text" name="company_name" id="company_name" placeholder="" maxlength="30" value="<?php echo get_user_meta($current_user->ID, 'company_name', true) ?: ''; ?>" required class="form-control">
              </div>
              <div class="col-12 col-md-6 mt-2 px-1">
                <label for="job_title">Job Title *</label>
                <input type="text" name="job_title" id="job_title" placeholder="" maxlength="30" value="<?php echo get_user_meta($current_user->ID, 'job_title', true) ?: ''; ?>" required class="form-control">
              </div>

              <div class="col-12 d-flex flex-column mt-2 px-1">
                <div class="alert mb-2" id="sub-response"></div>
                <button type=" submit" class="text-white btn btn-primary btn-submit">Submit</button>
              </div>
            </div>
          </div>
        </div>
      </form>
  <?php
        }
      }
  ?>

  <?php if (!is_user_logged_in()) : ?>
    <div class="col-12 pt-3 pt-md-4">
      <div class="login d-flex">
        <a href="<?php echo esc_url(wp_login_url($current_url)); ?>" class="text-white btn-login">Get started</a>
      </div>
    </div>
  <?php endif; ?>
  </div>
</div>
</div>

<?php
if (is_user_logged_in()) :
  $user_id = get_current_user_id();
  if ($wpdb->get_var("SELECT COUNT(1) FROM {$wpdb->prefix}client_club_members WHERE `user_id` = '{$user_id}' AND `status` = 'active'")) :
    $lists_query = "SELECT l.id, l.title, l.description, l.image_url, l.frequency
          FROM {$wpdb->prefix}observer_lists l
          WHERE l.status = 'active'
          ORDER BY l.sub_count DESC
        ";
    $lists = $wpdb->get_results($lists_query);
    if ($lists) :
      $my_sub_lists = [];
      $my_vote_lists = [];
      $my_subs = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND status = 'subscribed' ");
      $my_sub_lists = wp_list_pluck($my_subs, 'list_id');
?>
      <div class="mt-3">
        <h2 class="text-center text-primary">Brag Observer Newsletters</h2>
        <h4 class="text-center text-primary">Tick to subscribe, untick to unsubscribe from any newsletters below:</h4>
        <div class="row justify-content-start align-items-stretch">
          <!-- Tone Deaf Tastemakers -->
          <?php
          if (!isset($q_ids)) :
            $tastemaker = $wpdb->get_row("SELECT l.id, l.title, l.slug, l.description, l.image_url, l.frequency, sub_count FROM {$wpdb->prefix}observer_lists l WHERE l.id = '48' ");
            if ($tastemaker) :
          ?>
              <div class="col-lg-2 col-md-4 col-6 my-2 px-1 topic topic-tastemakers">
                <label class="p-r text-center d-flex flex-column justify-content-between h-100 topic-inner sub-unsub <?php echo in_array($tastemaker->id, $my_sub_lists) ? 'subscribed' : ''; ?>" style="border: 1px solid #ccc; padding: .5rem; cursor: pointer; ">
                  <div class="d-inline text-center text-white text-uppercase bg-danger p-1" style="font-size: 125%; white-space: nowrap; position: absolute; top: -1rem; left: 50%; transform: translateX(-50%); z-index: 1;">&#9733; VIP &#9733;</div>
                  <div class="list-info">
                    <figure class="img-wrap rounded-circle2">
                      <img alt="" src="<?php echo $tastemaker->image_url; ?>" class="rounded-circle2" width="">
                    </figure>
                    <h3 class="text-white"><?php echo $tastemaker->title; ?></h3>
                    <div class="text-white desc"><?php echo wpautop($tastemaker->description); ?></div>
                  </div>
                  <div class="list-subscription-action">
                    <input type="checkbox" name="lists[]" class="checkbox-list" id="lists_<?php echo $tastemaker->id; ?>" value="<?php echo $tastemaker->id; ?>" <?php echo in_array($tastemaker->id, $my_sub_lists) ? 'checked' : ''; ?>>
                    <label for="lists_<?php echo $tastemaker->id; ?>"></label>
                    <div class="loading" style="display: none;">
                      <div class="spinner">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                      </div>
                    </div>
                  </div>
                </label>
              </div>
          <?php
            endif; // If $tastemaker
          endif; // If $q_ids is NOT set
          ?>

          <?php
          // My list {{
          if (isset($my_sub_lists) && !empty($my_sub_lists)) :
            $my_lists = $wpdb->get_results("SELECT l.id, l.title, l.slug, l.description, l.image_url, l.frequency, sub_count FROM {$wpdb->prefix}observer_lists l WHERE l.id IN (" . implode(',', $my_sub_lists) . ") and l.status = 'active' ORDER BY sub_count ASC ");
            if ($my_lists) :
              foreach ($my_lists as $list) :
                if (48 == $list->id) // Exclude Tone Deaf Tastemakers
                  continue;
                $list_image_url = $list->image_url;
          ?>
                <div class="col-lg-2 col-md-4 col-6 my-2 px-1 topic">
                  <label class="text-center d-flex flex-column justify-content-between h-100 sub-unsub <?php echo in_array($list->id, $my_sub_lists) ? 'subscribed' : ''; ?>" style="border: 1px solid #ccc; padding: .5rem; cursor: pointer; ">
                    <div class="list-info">
                      <figure class="img-wrap rounded-circle2">
                        <img alt="" src="<?php echo $list_image_url; ?>" class="rounded-circle2" width="">
                        <div class="tags text-center text-white bg-danger text-uppercase"><?php echo $list->frequency ?: 'Breaking News'; ?></div>
                      </figure>
                      <h3><?php
                          echo !in_array($list->id, [4,]) ? trim(str_ireplace('Observer', '', $list->title)) : trim($list->title);
                          ?></h3>
                      <div class="desc"><?php echo wpautop($list->description); ?></div>
                    </div>
                    <div class="list-subscription-action">
                      <input type="checkbox" name="lists[]" class="checkbox-list" id="lists_<?php echo $list->id; ?>" value="<?php echo $list->id; ?>" <?php echo in_array($list->id, $my_sub_lists) ? 'checked' : ''; ?>>
                      <label for="lists_<?php echo $list->id; ?>"></label>
                      <div class="loading" style="display: none;">
                        <div class="spinner">
                          <div class="double-bounce1"></div>
                          <div class="double-bounce2"></div>
                        </div>
                      </div>
                    </div>
                  </label>
                </div>
            <?php
              endforeach; // For Each $list in $my_lists
            endif; // If $my_lists
          endif; // If $my_sub_lists is set and not empty
          // }} My list

          foreach ($lists as $index => $list) :

            if (48 == $list->id || in_array($list->id, $my_sub_lists)) // Exclude Tone Deaf Tastemakers
              continue;

            $list_image_url = $list->image_url;
            ?>
            <div class="col-lg-2 col-md-4 col-6 my-2 px-1 topic">
              <label class="text-center d-flex flex-column justify-content-between h-100 sub-unsub <?php echo in_array($list->id, $my_sub_lists) ? 'subscribed' : ''; ?>" style="border: 1px solid #ccc; padding: .5rem; cursor: pointer; ">
                <div class="list-info">
                  <figure class="img-wrap rounded-circle2">
                    <img alt="" src="<?php echo $list_image_url; ?>" class="rounded-circle2" width="">
                    <div class="tags text-center text-white bg-danger text-uppercase"><?php echo $list->frequency ?: 'Breaking News'; ?></div>
                  </figure>
                  <h3><?php
                      echo !in_array($list->id, [4,]) ? trim(str_ireplace('Observer', '', $list->title)) : trim($list->title);
                      ?></h3>
                  <div class="desc"><?php echo wpautop($list->description); ?></div>
                </div>
                <div class="list-subscription-action">
                  <input type="checkbox" name="lists[]" class="checkbox-list" id="lists_<?php echo $list->id; ?>" value="<?php echo $list->id; ?>" <?php echo in_array($list->id, $my_sub_lists) ? 'checked' : ''; ?>>
                  <label for="lists_<?php echo $list->id; ?>"></label>
                  <div class="loading" style="display: none;">
                    <div class="spinner">
                      <div class="double-bounce1"></div>
                      <div class="double-bounce2"></div>
                    </div>
                  </div>
                </div>
              </label>
            </div>
          <?php endforeach; // For Each List 
          ?>
        </div>
      </div>
<?php
    endif; // If $lists
  endif; // If joined client clib
endif; // If logged in
?>
<?php
get_footer('brag-client-club');
