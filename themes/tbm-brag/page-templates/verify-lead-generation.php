<?php
/*
* Template Name: Verify Response (Observer Lead Generation)
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
    $code = get_user_meta($data['id'], 'lead_generator_verification_code', true);

    if (!get_user_by('id', $data['id'])) { // If in case user is deleted or doesn't exist with the ID
      wp_redirect($redirectTo);
      die();
    }

    if ($code == $data['code']) { // checks whether the decoded code given is the same as the one in the data base

      update_user_meta($data['id'], 'is_activated', 1); // updates the database upon successful activation

      $lead_generator = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_lead_generators WHERE id = {$data['t_id']}");

      $wpdb->update(
        $wpdb->prefix . 'observer_lead_generator_responses',
        [
          'status' => 'verified',
          'status_mailchimp' => NULL
        ],
        [
          'lead_generator_id' => $data['t_id'],
          'user_id' => $data['id'],
        ]
      );

      // Referrer : update comp credits
      $response = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_lead_generator_responses WHERE lead_generator_id = {$data['t_id']} AND comp_code IS NOT NULL");
      if ($response) {
        $comp_code = $response->comp_code;
        $referrer_id = $wpdb->get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'comp_code' AND meta_value = '{$comp_code}' LIMIT 1");
        if ($referrer_id) {
          $comp_credits = get_user_meta($referrer_id, 'comp_credits', true);
          if ($comp_credits && is_array($comp_credits)) {
            if (in_array($lead_generator->id, array_keys($comp_credits))) {
              $comp_credits[$lead_generator->id]++;
            } else {
              $comp_credits[$lead_generator->id] = 1;
            }
            update_user_meta($referrer_id, 'comp_credits', $comp_credits);
          }
        }
      }

      $user_id = $data['id']; // logs the user in

      // Subscribe to the list
      foreach (explode(',', $lead_generator->list_id) as $list_id) {
        $check_sub = $wpdb->get_row("SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '{$list_id}' LIMIT 1");
        if (!$check_sub) {
          $wpdb->insert(
            $wpdb->prefix . 'observer_subs',
            [
              'user_id' => $user_id,
              'list_id' => $list_id,
              'status' => 'subscribed',
              'status_mailchimp' => NULL,
              'subscribed_at' => current_time('mysql'),
            ]
          );
        } elseif ('subscribed' != $check_sub->status) {
          $wpdb->update(
            $wpdb->prefix . 'observer_subs',
            [
              'status' => 'subscribed',
              'status_mailchimp' => NULL,
              'subscribed_at' => current_time('mysql'),
            ],
            [
              'id' => $check_sub->id,
            ]
          );
        }
      }

      $user = get_user_by('id', $user_id);
      if ($user) {
        wp_set_current_user($user_id, $user->user_login);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $user->user_login, $user);
      }
      $messages[] = 'Your feedback has been verified! Redirecting now...';
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

          <?php
          $redirectTo = home_url('/observer-subscriptions/');

          if (!is_null($redirectTo)) :
          ?>
            <script>
              window.setTimeout(function() {
                window.location = '<?php echo $redirectTo; ?>';
              }, 3000);
            </script>
          <?php endif; // If redirecting 
          ?>
        <?php endif; ?>

      </main>
    </div>
  </div>
</div>
<?php
get_template_part('page-templates/brag-observer/footer');
