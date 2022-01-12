<?php
/*
 * Template Name: Profile
 */

use \DrewM\MailChimp\MailChimp;

if (!is_user_logged_in()) :
  wp_redirect(home_url('/wp-login.php?redirect_to=') . urlencode(home_url('/profile/')));
  exit;
endif;

$current_user = wp_get_current_user();

require get_template_directory() . '/vendor/autoload.php';

use Auth0\SDK\Auth0;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\API\Management;

$dotenv = Dotenv\Dotenv::createImmutable(ABSPATH);
$dotenv->load();

$auth0_api = new Authentication(
  $_ENV['AUTH0_DOMAIN'],
  $_ENV['AUTH0_CLIENT_ID']
);

$config = [
  'client_secret' => $_ENV['AUTH0_CLIENT_SECRET'],
  'client_id' => $_ENV['AUTH0_CLIENT_ID'],
  'audience' => $_ENV['AUTH0_MANAGEMENT_AUDIENCE'],
];

try {
  $result = $auth0_api->client_credentials($config);
  $access_token = $result['access_token'];
} catch (Exception $e) {
  // die($e->getMessage());
}

$auth0_user = null;

if (isset($access_token)) {
  // Instantiate the base Auth0 class.
  $auth0 = new Auth0([
    // The values below are found on the Application settings tab.
    'domain' => $_ENV['AUTH0_DOMAIN'],
    'client_id' => $_ENV['AUTH0_CLIENT_ID'],
    'client_secret' => $_ENV['AUTH0_CLIENT_SECRET'],
    'redirect_uri' => $_ENV['AUTH0_REDIRECT_URI'],
  ]);

  $mgmt_api = new Management($access_token, $_ENV['AUTH0_DOMAIN']);
  try {
    if ($wp_auth0_id = get_user_meta($current_user->ID, 'wp_auth0_id', true)) {
      $auth0_user = $mgmt_api->users()->get($wp_auth0_id);
    } elseif ($wp_auth0_id = get_user_meta($current_user->ID, $wpdb->prefix . 'auth0_id', true)) {
      $auth0_user = $mgmt_api->users()->get($wp_auth0_id);
    }
  } catch (Exception $e) {
    // die($e->getMessage());
  }
}

$returnTo = isset($_REQUEST['returnTo']) ? $_REQUEST['returnTo'] : home_url('/profile?success=1');

$errors = [];
$messages = [];

if (isset($_GET['success'])) {
  $messages[] = 'Your details have been saved.';
}

$profile_strength = 0;
$current_user = wp_get_current_user();

if (get_user_meta($current_user->ID, 'profile_strength', true)) {
  $profile_strength = get_user_meta($current_user->ID, 'profile_strength', true);
} else {
  if (get_user_meta($current_user->ID, 'first_name', true))
    $profile_strength += 20;

  if (get_user_meta($current_user->ID, 'last_name', true))
    $profile_strength += 20;

  if (get_user_meta($current_user->ID, 'state', true))
    $profile_strength += 20;

  if (get_user_meta($current_user->ID, 'birthday', true))
    $profile_strength += 20;

  if (get_user_meta($current_user->ID, 'gender', true))
    $profile_strength += 20;

  update_user_meta($current_user->ID, 'profile_strength', $profile_strength);
}

if ($profile_strength < 20) {
  $profile_complete_class = 'text-danger';
} else if ($profile_strength <= 40) {
  $profile_complete_class = 'text-warning';
} else if ($profile_strength <= 60) {
  $profile_complete_class = 'text-info';
} else if ($profile_strength <= 80) {
  $profile_complete_class = 'text-primary';
} else if ($profile_strength <= 100) {
  $profile_complete_class = 'text-success';
}

if (isset($_POST) && isset($_POST['action']) && 'save-profile' == $_POST['action']) {
  $post_vars = stripslashes_deep($_POST);

  if (isset($_POST["chosen_profile_picture_data"]) && '' != trim($_POST["chosen_profile_picture_data"])) {

    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $data = $_POST["chosen_profile_picture_data"];
    $image_array_1 = explode(";", $data);
    if (isset($image_array_1[1])) {
      $image_array_2 = explode(",", $image_array_1[1]);
      $data = base64_decode($image_array_2[1]);

      $imageName = 'tmp/u-' . md5($current_user->ID . time()) . '.jpg';
      file_put_contents($imageName, $data);

      $imageId = media_sideload_image(home_url('/' . $imageName), 0, '', 'id');
      if ($imageId) {
        $profile_picture = wp_get_attachment_image_src($imageId, 'full');
        $auth0_usermeta['picture'] = $profile_picture[0];

        @unlink($imageName);
      }
    }
  }
  $user_data = [
    'ID' => $current_user->ID
  ];

  $required_fields = [
    // 'first_name',
    // 'last_name',
    // 'birthday_day',
    // 'birthday_month',
    // 'birthday_year',
    // 'state',
    // 'gender',
  ];

  $braze_updates = [];

  // if (get_user_meta($current_user->ID, 'is_imported', true) === '1') {
  // array_push( $required_fields, 'password', 'confirm_password' );
  // }

  foreach ($required_fields as $required_field) {
    if (!isset($post_vars[$required_field]) || '' == trim($post_vars[$required_field])) {
      $errors[] = 'Please complete all required fields.';
      break;
    }
  }

  /*
  * Enable in Phase 2, allowing users to update email address after verification email
  if ( isset( $post_vars['email'] ) && $post_vars['email'] != $current_user->user_email ) {
    $user_data['user_email'] = $post_vars['email'];
  }
  */

  $user_data['first_name'] = sanitize_text_field($post_vars['first_name']);
  if (get_user_meta($current_user->ID, 'first_name', true) != $user_data['first_name']) {
    $braze_updates['first_name'] = $user_data['first_name'];
  }

  $user_data['last_name'] = sanitize_text_field($post_vars['last_name']);
  if (get_user_meta($current_user->ID, 'last_name', true) != $user_data['last_name']) {
    $braze_updates['last_name'] = $user_data['last_name'];
  }

  $user_data['display_name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];

  foreach (get_social_platforms() as $social_platform => $social_title) {
    if (isset($post_vars[$social_platform])) {
      $user_data[$social_platform] = esc_url_raw($post_vars[$social_platform]);
    }
  }

  $post_vars['description'] = substr($post_vars['description'], 0, 150);
  $user_data['description'] = sanitize_textarea_field($post_vars['description']);

  if (!is_null($auth0_user)) {
    $auth0_usermeta['first_name'] = $user_data['first_name'];
    $auth0_usermeta['last_name'] = $user_data['last_name'];
    $auth0_usermeta['display_name'] = $user_data['display_name'];
    $auth0_usermeta['bio'] = $user_data['description'];

    foreach (get_social_platforms() as $social_platform => $social_title) {
      if (isset($user_data[$social_platform])) {
        $auth0_usermeta[$social_platform] = $user_data[$social_platform];
      }
    }
  }

  if (
    isset($post_vars['birthday_month']) &&
    isset($post_vars['birthday_day']) &&
    isset($post_vars['birthday_year']) &&
    '0' != trim($post_vars['birthday_month']) &&
    '0' != trim($post_vars['birthday_day']) &&
    '0' != trim($post_vars['birthday_year'])
  ) {
    if (
      !checkdate($post_vars['birthday_month'], $post_vars['birthday_day'], $post_vars['birthday_year']) ||
      new DateTime($post_vars['birthday_year'] . '-' . $post_vars['birthday_month'] . '-' . $post_vars['birthday_day']) >= new DateTime()
    ) {
      $errors[] = 'Please input valid birthday.';
    }
  }

  if ('0' == $post_vars['state']) {
    $errors[] = 'Please select your state.';
  }

  $update_user = wp_update_user($user_data);

  if (is_wp_error($update_user)) {
    $errors[] = $update_user->get_error_messages()[0];
  }

  if (count($errors) == 0) {
    if (
      isset($post_vars['birthday_month']) &&
      isset($post_vars['birthday_day']) &&
      isset($post_vars['birthday_year']) &&
      '0' != trim($post_vars['birthday_month']) &&
      '0' != trim($post_vars['birthday_day']) &&
      '0' != trim($post_vars['birthday_year'])
    ) {
      $birthday = $post_vars['birthday_year'] . '-' . $post_vars['birthday_month'] . '-' . $post_vars['birthday_day'];

      if (get_user_meta($current_user->ID, 'birthday', true) != $birthday) {
        $braze_updates['birthday'] = $birthday;
      }

      update_user_meta($current_user->ID, 'birthday', $birthday);
      delete_user_meta($current_user->ID, 'predicted_birthday');

      $auth0_usermeta['birthday'] = $birthday;
    } else {
      delete_user_meta($current_user->ID, 'birthday');
      $auth0_usermeta['birthday'] = "";
      $braze_updates['birthday'] = "";
    }

    if (isset($post_vars['state'])) {
      if ('' != trim($post_vars['state'])) {

        if (get_user_meta($current_user->ID, 'state', true) != trim($post_vars['state'])) {
          $braze_updates['state'] = trim($post_vars['state']);
        }

        update_user_meta($current_user->ID, 'state', $post_vars['state']);
        delete_user_meta($current_user->ID, 'predicted_state');

        $auth0_usermeta['state'] = $post_vars['state'];

        update_user_meta($current_user->ID, 'incomplete_profile', "false");
      } else {
        delete_user_meta($current_user->ID, 'state');
        $auth0_usermeta['state'] = "";
        $braze_updates['state'] = "";
      }
    }


    if (isset($post_vars['gender'])) {
      if ('' != trim($post_vars['gender'])) {

        if (get_user_meta($current_user->ID, 'gender', true) != trim($post_vars['gender'])) {
          $braze_updates['gender'] = trim($post_vars['gender']);
        }

        update_user_meta($current_user->ID, 'gender', $post_vars['gender']);
        delete_user_meta($current_user->ID, 'predicted_gender');

        $auth0_usermeta['gender'] = $post_vars['gender'];
      } else {
        delete_user_meta($current_user->ID, 'gender');
        $auth0_usermeta['gender'] = "";
        $braze_updates['gender'] = "";
      }
    }

    delete_user_meta($current_user->ID, 'is_imported');

    if (!is_null($auth0_user) && isset($auth0_usermeta) && !empty($auth0_usermeta)) {
      $mgmt_api->users()->update($wp_auth0_id, [
        'user_metadata' => $auth0_usermeta
      ]);
    }

    /**
     * Update MailChimp
     */
    require_once(get_template_directory() . '/MailChimp.php');
    $api_key = '727643e6b14470301125c15a490425a8-us1';
    $MailChimp = new MailChimp($api_key);

    $data = array(
      'email_address' => $current_user->user_email,
      'status' => 'subscribed',
    );
    $subscribe = $MailChimp->post("lists/5f6dd9c238/members", $data);

    $subscriber_hash = $MailChimp->subscriberHash($current_user->user_email);

    $profile_strength = 0;
    if (get_user_meta($current_user->ID, 'first_name', true))
      $profile_strength += 20;

    if (get_user_meta($current_user->ID, 'last_name', true))
      $profile_strength += 20;

    if (get_user_meta($current_user->ID, 'state', true))
      $profile_strength += 20;

    if (get_user_meta($current_user->ID, 'birthday', true))
      $profile_strength += 20;

    if (get_user_meta($current_user->ID, 'gender', true))
      $profile_strength += 20;

    update_user_meta($current_user->ID, 'profile_strength', $profile_strength);

    $merge_fields = [
      'FNAME' => $user_data['first_name'],
      'LNAME' => $user_data['last_name'],
      // 'BIRTHDATE' => $birthday,
      'STATE' => $post_vars['state'],
      // 'GENDER' => $post_vars['gender'],
      'STRENGTH' => $profile_strength,
    ];

    if (
      isset($post_vars['birthday_month']) &&
      isset($post_vars['birthday_day']) &&
      isset($post_vars['birthday_year']) &&
      '0' != trim($post_vars['birthday_month']) &&
      '0' != trim($post_vars['birthday_day']) &&
      '0' != trim($post_vars['birthday_year'])
    ) {
      $merge_fields['BIRTHDATE'] = $birthday;
    }

    if (isset($post_vars['gender']) && '' != trim($post_vars['gender'])) {
      $merge_fields['GENDER'] = $post_vars['gender'];
    }

    // Token
    if (!get_user_meta($current_user->ID, 'oc_token', true)) :
      $oc_token = md5($current_user->ID . time()); // creates md5 code to verify later
      update_user_meta($current_user->ID, 'oc_token', $oc_token);
    endif;

    $unserialized_oc_token = [
      'id' => $current_user->ID,
      'oc_token' => get_user_meta($current_user->ID, 'oc_token', true),
    ]; // makes it into a code to send it to user via email

    $merge_fields['OC_TOKEN'] = base64_encode(serialize($unserialized_oc_token));

    $update_subscriber = $MailChimp->patch("lists/5f6dd9c238/members/$subscriber_hash", [
      'merge_fields' => $merge_fields
    ]);

    /**
     * Queue to update in Braze
     */
    if (!empty($braze_updates)) {
      $task = 'update_profile';
      include_once WP_PLUGIN_DIR . '/brag-observer/classes/cron.class.php';
      $cron = new Cron();
      if (!$cron->getActiveBrazeQueueTask($current_user->ID, $task)) {
        $cron->addToBrazeQueue($current_user->ID, $task, $braze_updates);
      }
    }

    wp_redirect($returnTo);
    exit;
  } // If $errors is empty
} // IF $_POST

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
    <div id="update-profile" class="col-12 col-md-9 p-3">
      <div class="site-main">
        <?php
        /* Start the Loop */
        while (have_posts()) :
          the_post();
        ?>
          <h2 class="title px-0 px-md-1">
            <?php the_title(); ?>
          </h2>
          <?php the_content(); ?>
          <?php

          ?>
          <div class="mx-auto mb-3 px-0 px-md-1" style="max-width: 100%;">
            <p><strong>Profile Strength: <a href="<?php echo home_url('/profile/'); ?>" class="<?php echo $profile_complete_class; ?>"><?php echo $profile_strength; ?>%</a> complete</strong></p>
            <div class="progress profile-strength">
              <?php for ($i = 20; $i <= 100; $i += 20) : ?>
                <div class="profile-strength-step<?php echo $i <= $profile_strength ? '-active' : ''; ?>" style="width: 20%; <?php echo $i != 20 ? 'border-left: 2px solid #fff;' : ''; ?>">
                  <?php if (0 == $profile_strength && $i == 20) : ?>
                    <div style="position: relative;">
                      <div class="profile-strength-start d-flex justify-content-center align-items-center <?php echo $profile_complete_class; ?>">
                        <img src="<?php echo ICONS_URL; ?>check.svg" width="16" height="16">
                      </div>
                    </div>
                  <?php elseif ($profile_strength != 100 && $i == $profile_strength) : ?>
                    <div style="position: relative;">
                      <div class="profile-strength-complete d-flex justify-content-center align-items-center <?php echo $profile_complete_class; ?>">
                        <img src="<?php echo ICONS_URL; ?>check.svg" width="16" height="16">
                      </div>
                    </div>
                  <?php elseif ($i == 100) : ?>
                    <div style="position: relative;">
                      <div class="profile-strength-complete d-flex justify-content-center align-items-center text-primary">
                        <img src="<?php echo ICONS_URL; ?>star.svg" width="16" height="16">
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endfor; ?>
            </div>
          </div>

          <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger my-3">
              <?php foreach ($errors as $error) : ?>
                <div><?php echo $error; ?></div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($messages)) : ?>
            <div class="alert alert-success my-3">
              <?php foreach ($messages as $message) : ?>
                <div><?php echo $message; ?></div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php

          if (isset($_GET['err'])) : ?>
            <div class="alert alert-info">
              <?php if (isset($_GET['err']) && 'new' == $_GET['err']) : ?>
                <h3 class="text-center mt-3">Welcome to The Brag Observer</h3>
                <p class="text-center">Boost your profile to receive more personalised and relevant content.</p>
              <?php elseif ((isset($_GET['err']) && 'incomplete' == $_GET['err'])) : ?>
                <div class="text-center">Boost your profile to receive more personalised and relevant content.</div>
              <?php elseif ((isset($_GET['err']) && 'tastemaker' == $_GET['err'])) : ?>
                <div class="text-center">Boost your profile to stay up-to-date on future Tastemaker competitions and all the news that matters to you.</div>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <form action="<?php echo home_url('profile'); ?>" enctype="multipart/form-data" id="form-profile" method="post" onSubmit="document.getElementById('btn-submit').disabled=true;">

            <input type="hidden" name="returnTo" value="<?php echo $returnTo; ?>">
            <input type="hidden" name="action" value="save-profile">

            <div class="row">
              <div class="col-12 px-0 px-md-1">
                <h4 class="mb-0">Profile picture</h4>
                <div>
                  <div class="d-none" id="profile-picture-crop-wrap">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/default-avatar-4.png" id="profile_picture" class="bg-dark" />
                  </div>

                  <div class="text-center d-none" id="btn-ok-wrap">
                    <button class="btn btn-dark btn-ok">OK</button>
                  </div>

                  <div class="text-center">
                    <img src="<?php echo (!is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata']['picture'])) ? $auth0_user['user_metadata']['picture'] : 'd-none'; ?>" id="chosen-profile-picture" class="rounded-circle <?php echo (!is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata']['picture'])) ? '' : 'd-none'; ?>" style="width: 200px; height: 200px;">
                    <textarea name="chosen_profile_picture_data" id="chosen_profile_picture_data" readonly class="d-none"></textarea>
                  </div>

                  <div class="mt-2 text-center">
                    <input type="file" name="upload_profile_picture" id="upload_profile_picture" class="form-control" accept="image/*" />
                  </div>

                  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/croppie/croppie.css" />
                  <script src="<?php echo get_template_directory_uri(); ?>/croppie/croppie.js"></script>
                  <script>
                    jQuery(document).ready(function($) {
                      $image_crop = $('#profile_picture').croppie({
                        enableExif: true,
                        viewport: {
                          width: 300,
                          height: 300,
                          type: 'square'
                        },
                        boundary: {
                          width: 350,
                          height: 350
                        }
                      });

                      $('#upload_profile_picture').on('change', function() {
                        var reader = new FileReader();
                        reader.onload = function(event) {
                          $image_crop.croppie('bind', {
                            url: event.target.result
                          }).then(function() {

                          });
                        }
                        reader.readAsDataURL(this.files[0]);
                        $('#chosen-profile-picture').addClass('d-none');
                        $('#profile-picture-crop-wrap, #btn-ok-wrap').removeClass('d-none');
                        $(this).addClass('d-none');
                      });

                      $('.btn-ok').on('click', function(e) {
                        e.preventDefault();
                        $image_crop.croppie('result', {
                          type: 'canvas',
                          size: 'viewport'
                        }).then(function(response) {
                          $('#chosen_profile_picture_data').val(response);
                          $('#chosen-profile-picture').attr('src', response).removeClass('d-none');
                          $('#profile-picture-crop-wrap, #btn-ok-wrap').addClass('d-none');
                        })
                      });

                      $('#form-profile').on('submit', function() {
                        if ($('#chosen_profile_picture_data').val() == '') {
                          $image_crop.croppie('result', {
                            type: 'canvas',
                            size: 'viewport'
                          }).then(function(response) {
                            $('#chosen_profile_picture_data').val(response);
                            $('#chosen-profile-picture').attr('src', response).removeClass('d-none');
                            $('#profile-picture-crop-wrap, #btn-ok-wrap').addClass('d-none');
                          });
                        }
                      });
                    });
                  </script>
                </div>
              </div>

              <div class="col-12 mt-3 px-0 px-md-1">
                <div>
                  <?php if (strpos($current_user->user_email, '@privaterelay.appleid.com') === FALSE) : ?>
                    <div>
                      <h4 class="mb-0">Email Address</h4>
                      <?php echo preg_replace('/(?:^|.@).\K|.\.[^@]*$(*SKIP)(*F)|.(?=.*?\.)/', '*', $current_user->user_email); ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>


              <div class="col-12 mt-3 col-md-6 px-0 px-md-1">
                <?php $first_name = isset($post_vars) && isset($post_vars['first_name']) ? $post_vars['first_name'] : (!is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata']['first_name'])
                  ?
                  $auth0_user['user_metadata']['first_name']
                  :
                  get_user_meta($current_user->ID, 'first_name', true)); ?>
                <h4 class="mb-0">First name</h4>
                <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo $first_name; ?>">
              </div>

              <div class="col-12 mt-3 col-md-6 px-0 px-md-1">
                <?php $last_name = isset($post_vars) && isset($post_vars['last_name']) ? $post_vars['last_name'] : (!is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata']['last_name'])
                  ?
                  $auth0_user['user_metadata']['last_name']
                  :
                  get_user_meta($current_user->ID, 'last_name', true)); ?>
                <h4 class="mb-0">Last name</h4>
                <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo  $last_name; ?>">
              </div>

              <div class="col-12 mt-3 col-md-4 px-0 px-md-1">
                <?php
                $user_state = !is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata']['state'])
                  ?
                  $auth0_user['user_metadata']['state']
                  : get_user_meta($current_user->ID, 'state', true);
                ?>
                <h4 class="mb-0">State</h4>
                <select aria-label="State" name="state" id="state" title="State" class="form-control">
                  <option value=""></option>
                  <?php foreach (getStates() as $state_abbr => $state) : ?>
                    <option value="<?php echo $state_abbr; ?>" <?php echo (isset($post_vars) && isset($post_vars['state']) && $post_vars['state'] == $state_abbr) ?
                                                                  ' selected' : (isset($user_state) && $user_state == $state_abbr ? ' selected' : ''); ?>><?php echo $state; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-12 mt-3 col-md-5 px-0 px-md-1">
                <?php
                $birthday = !is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata']['birthday'])
                  ?
                  $auth0_user['user_metadata']['birthday']
                  : get_user_meta($current_user->ID, 'birthday', true);
                $birthday = $birthday ? explode('-', $birthday) : [];
                ?>
                <h4 class="mb-0">Birthday</h4>
                <div class="input-group d-flex">
                  <select aria-label="Day" name="birthday_day" id="day" title="Day" class="form-control">
                    <option value="0">Day</option>
                    <?php for ($birthday_day = 1; $birthday_day <= 31; $birthday_day++) : ?>
                      <option value="<?php echo $birthday_day; ?>" <?php
                                                                    echo (isset($post_vars) && isset($post_vars['birthday_day']) && $post_vars['birthday_day'] == $birthday_day) ?
                                                                      ' selected' : (isset($birthday[2]) && $birthday[2] == $birthday_day ? ' selected' : ''); ?>><?php echo $birthday_day; ?></option>
                    <?php endfor; ?>
                  </select>

                  <select aria-label="Month" name="birthday_month" id="month" title="Month" class="form-control">
                    <option value="0">Month</option>
                    <?php
                    $months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
                    ?>
                    <?php foreach ($months as $month_no => $month) : ?>
                      <option value="<?php echo $month_no; ?>" <?php echo (isset($post_vars) && isset($post_vars['birthday_month']) && $post_vars['birthday_month'] == $month_no) ?
                                                                  ' selected' : (isset($birthday[1]) && $birthday[1] == $month_no ? ' selected' : ''); ?>><?php echo $month; ?></option>
                    <?php endforeach; ?>
                  </select>

                  <select aria-label="Year" name="birthday_year" id="year" title="Year" class="form-control">
                    <option value="0">Year</option>
                    <?php for ($birthday_year = date('Y'); $birthday_year >= date('Y') - 115; $birthday_year--) : ?>
                      <option value="<?php echo $birthday_year; ?>" <?php echo (isset($post_vars) && isset($post_vars['birthday_year']) && $post_vars['birthday_year'] == $birthday_year) ?
                                                                      ' selected' : (isset($birthday[0]) && $birthday[0] == $birthday_year ? ' selected' : ''); ?>><?php echo $birthday_year; ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
              </div>

              <div class="col-12 mt-3 col-md-3 px-0 px-md-1">
                <?php
                $user_gender = !is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata']['gender'])
                  ?
                  $auth0_user['user_metadata']['gender']
                  : get_user_meta($current_user->ID, 'gender', true);
                ?>
                <h4 class="mb-0">Gender</h4>
                <select aria-label="Gender" name="gender" id="gender" title="Gender" class="form-control">
                  <option value=""></option>
                  <?php foreach (getGenders() as $gender) : ?>
                    <option value="<?php echo $gender; ?>" <?php echo (isset($post_vars) && isset($post_vars['gender']) && $post_vars['gender'] == $gender) ?
                                                              ' selected' : (isset($user_gender) && $user_gender == $gender ? ' selected' : ''); ?>><?php echo $gender; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-12 mt-3 px-0 px-md-1">
                <h4 class="mb-0">Bio</h4>
                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Describe yourself..."><?php echo isset($auth0_user['user_metadata']['bio']) ? $auth0_user['user_metadata']['bio'] : get_user_meta($current_user->ID, 'description', true); ?></textarea>
                <div class="text-dull" style="font-size: .75rem; color: #bbb;">Max 150 characters</div>
              </div>

              <?php foreach (get_social_platforms() as $social_platform => $social_title) : ?>
                <div class="col-12 mt-3 px-0 px-md-1">
                  <?php $value = isset($post_vars) && isset($post_vars[$social_platform]) ? $post_vars[$social_platform] : (!is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata'][$social_platform])
                    ?
                    $auth0_user['user_metadata'][$social_platform]
                    :
                    get_user_meta($current_user->ID, $social_platform, true)); ?>
                  <h4 class="mb-0"><?php echo $social_title; ?></h4>
                  <input type="text" name="<?php echo $social_platform; ?>" id="<?php echo $social_platform; ?>" class="form-control" value="<?php echo $value; ?>" placeholder="https://">
                </div>
              <?php endforeach; ?>

              <div class="col-12 mt-3">
                <input type="submit" name="submit" id="btn-submit" class="btn btn-dark rounded" value="Save">
              </div>
            </div>
          </form>
        <?php
        endwhile; // End of the loop.
        ?>
      </div>
    </div><!-- #primary -->
  </div><!-- .row -->
</div><!-- .container -->

<?php
get_footer();
// get_template_part('page-templates/brag-observer/footer');
