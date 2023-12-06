<?php
/*
 * Template Name: Verify Review (Tastemaker)
 */

$errors = [];
$messages = [];

$redirectTo = isset($_GET['returnTo']) ? urldecode($_GET['returnTo']) : home_url('/');

if (isset($_GET['p'])) { // If accessed via an authentification link - Review Verification

  $data = @unserialize(base64_decode($_GET['p']));

  if (!$data) {
    $errors[] = 'Invalid link';
  }

  if (count($errors) == 0) {
    $code = get_user_meta($data['id'], 'tastemaker_verification_code', true);

    if (!get_user_by('id', $data['id'])) { // If in case user is deleted or doesn't exist with the ID
      wp_redirect($redirectTo);
      die();
    }

    if ($code == $data['code']) { // checks whether the decoded code given is the same as the one in the data base

      update_user_meta($data['id'], 'is_activated', 1); // updates the database upon successful activation

      $wpdb->update(
        $wpdb->prefix . 'observer_tastemaker_reviews',
        [
          'status' => 'verified',
        ],
        [
          'tastemaker_id' => $data['t_id'],
          'user_id' => $data['id'],
        ]
      );

      $user_id = $data['id']; // logs the user in

      // Subscribe to the list
      $check_sub = $wpdb->get_row("SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '48' LIMIT 1");
      if (!$check_sub) {
        $wpdb->insert(
          $wpdb->prefix . 'observer_subs',
          [
            'user_id' => $user_id,
            'list_id' => 48,
            'status' => 'subscribed',
            'status_mailchimp' => NULL,
            'subscribed_at' => current_time('mysql'),
          ]
        );
      }

      $user = get_user_by('id', $user_id);
      if ($user) {
        wp_set_current_user($user_id, $user->user_login);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $user->user_login, $user);
      }
      $messages[] = 'Your review has been verified! Redirecting now...';
    } else {
      $errors[] = '<p>Verification failed.</p>';
    }
  }
} else {
  wp_redirect(home_url());
}

get_template_part('page-templates/brag-observer/header');
?>

<div class="container">
  <div class="row justify-content-center">
    <div id="update-profile" class="col-sm-9 col-lg-9 my-5">
      <main class="site-main" role="main">

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
              <div>
                <?php echo $error; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($messages)): ?>
          <div class="alert alert-success">
            <?php foreach ($messages as $message): ?>
              <div>
                <?php echo $message; ?>
              </div>
            <?php endforeach; ?>
          </div>

          <?php
          $profile_complete_percentage = 0;
          $current_user = wp_get_current_user();

          if (get_user_meta($current_user->ID, 'first_name', true))
            $profile_complete_percentage += 20;

          if (get_user_meta($current_user->ID, 'last_name', true))
            $profile_complete_percentage += 20;

          if (get_user_meta($current_user->ID, 'state', true))
            $profile_complete_percentage += 20;

          if (get_user_meta($current_user->ID, 'birthday', true))
            $profile_complete_percentage += 20;

          if (get_user_meta($current_user->ID, 'gender', true))
            $profile_complete_percentage += 20;

          if ($profile_complete_percentage < 100) {
            $redirectTo = home_url('/profile/?err=tastemaker');
          } else {
            $redirectTo = home_url('/observer-subscriptions/');
          }

          if (!is_null($redirectTo)):
            ?>
            <script>
              window.setTimeout(function () {
                window.location = '<?php echo $redirectTo; ?>';
              }, 3000);
            </script>
          <?php endif; // If redirecting ?>
        <?php endif; ?>

      </main>
    </div>
  </div>
</div>
<?php
get_template_part('page-templates/brag-observer/footer');
