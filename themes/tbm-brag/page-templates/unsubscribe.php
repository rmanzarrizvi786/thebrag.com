<?php
/*
* Template Name: Unsubscribe
*/

$errors = [];
$messages = [];

$unsub_types_redirects_mapping = [
  'dbu_hottest100' => 'https://dontboreus.thebrag.com/profile/'
];
$site_abbrs = [
  'dbu',
];

$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$valid_link = true;
if (isset($_GET['type'])) {
  $unsub_type = trim($_GET['type']);

  if (in_array($unsub_type, array_keys($unsub_types_redirects_mapping))) {
    $redirectTo = isset($unsub_types_redirects_mapping[$unsub_type]) ? $unsub_types_redirects_mapping[$unsub_type] : home_url('/observer-subscriptions/');

    $unsub_type_e = explode('_', $unsub_type);
    $site_abbr = $unsub_type_e[0];

    $unsub_attr = str_replace($site_abbr . '_', '', $unsub_type);

    if (!in_array($site_abbr, $site_abbrs)) {
      $valid_link = false;
    }
  } else {
    $valid_link = false;
  }
}

if ($valid_link && isset($_GET['token'])) {

  $custom_attribute_unsub = "{$site_abbr}_email_unsubs";

  $data = @unserialize(base64_decode($_GET['token']));

  if (!$data) {
    $valid_link = false;
  }

  $oc_token = get_user_meta($data['id'], 'oc_token', true);

  if (count($errors) == 0) {
    if ($oc_token == $data['oc_token']) {
      $user_id = $data['id'];
      $user = get_user_by('id', $user_id);

      if ($user) {
        require_once WP_PLUGIN_DIR . '/brag-observer/classes/braze.class.php';
        $braze = new Braze();

        $unsubs = [];
        $braze_user = $braze->getUser($user_id);
        $user_attributes = $braze_user['user_attributes'];
        if (!is_null($braze_user['user']) && isset($braze_user['user']->custom_attributes)) {
          if (isset($braze_user['user']->custom_attributes->{$custom_attribute_unsub})) {
            if (is_array($braze_user['user']->custom_attributes->{$custom_attribute_unsub})) {
              $unsubs = array_merge($braze_user['user']->custom_attributes->{$custom_attribute_unsub}, [$unsub_attr]);

              // var_dump($unsubs);
              // exit;
            } else {
              $unsubs = [
                $braze_user['user']->custom_attributes->{$custom_attribute_unsub},
                $unsub_attr
              ];
            }
          } else {
            $unsubs = [$unsub_attr];
          }
        } else {
          $unsubs = [$unsub_attr];
        }

        $braze_payload = [];
        if (!empty($unsubs)) {
          $braze_payload['attributes'] = [array_merge($user_attributes, [$custom_attribute_unsub => $unsubs])];
        }

        if (!empty($braze_payload)) {
          $braze->setPayload($braze_payload);
          $res_track = $braze->request('/users/track', true);
          if (201 === $res_track['code']) {
            $messages[] = 'You have been succesfully unsubscribed.';
          }
        }
      }
    } else {
      $valid_link = false;
    }
  }
} else {
  $valid_link = false;
}

if (!$valid_link) {
  $errors[] = 'Looks like there is any issue with the request, please contact us at <a href="mailto:office@thebrag.media?subject=Unable to unsubscribe&body=URL: ' . $current_url . '">office@thebrag.media</a>';
  $redirectTo = home_url('/observer-subscriptions/');
}

get_header();
?>

<div class="ad-billboard ad-billboard-top container py-1 py-md-2">
  <div class="mx-auto text-center">
    <?php render_ad_tag('leaderboard'); ?>
  </div>
</div>


<div class="container bg-yellow">
  <div class="row justify-content-center">
    <div id="update-profile" class="col-sm-9 col-lg-9 my-3">

      <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $error) : ?>
            <div><?php echo $error; ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($messages)) : ?>
        <div class="alert alert-success">
          <?php foreach ($messages as $message) : ?>
            <div><?php echo $message; ?></div>
          <?php endforeach; ?>
        </div>

        <?php if (0 && !is_null($redirectTo)) : ?>
          <script>
            window.setTimeout(function() {
              window.location = '<?php echo $redirectTo; ?>';
            }, 3000);
          </script>
        <?php endif; // If redirecting 
        ?>
      <?php endif; ?>

    </div>
  </div>
</div>
<?php
get_footer();
