<?php
/*
 * Template Name: Change Password
 */
if (!is_user_logged_in()) :
  wp_redirect(home_url('/wp-login.php?redirect_to=') . urlencode(home_url('/change-password/')));
  exit;
endif;

$current_user = wp_get_current_user();

require get_template_directory() . '/vendor/autoload.php';

use Auth0\SDK\Auth0;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\API\Management;

$dotenv = Dotenv\Dotenv::createImmutable(ABSPATH);
$dotenv->load();

$returnTo = isset($_REQUEST['returnTo']) ? $_REQUEST['returnTo'] : home_url('/change-password?success=1');

$errors = [];
$messages = [];

if (isset($_GET['success'])) {
  $messages[] = 'Your password has been updated.';
}
if (isset($_POST) && isset($_POST['action']) && 'change-password' == $_POST['action']) {
  $curl = curl_init();

  curl_setopt_array($curl, [
    CURLOPT_URL => "https://" . $_ENV['AUTH0_DOMAIN'] . "/dbconnections/change_password",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "{\"client_id\": \"{$_ENV['AUTH0_CLIENT_ID']}\",\"email\": \"{$current_user->user_email}\",\"connection\": \"Username-Password-Authentication\"}",
    CURLOPT_HTTPHEADER => [
      "content-type: application/json"
    ],
  ]);

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    $errors[] = 'There has been some issues sending you the email, please try again.';
  } else {
    $messages[] = 'The email with the link to reset your password has been sent.';
  }
}

if (0 && isset($_POST) && isset($_POST['action']) && 'change-password' == $_POST['action']) {
  $post_vars = stripslashes_deep($_POST);

  $user_data = [
    'ID' => $current_user->ID
  ];

  if (get_user_meta($current_user->ID, 'is_imported', true) !== '1') {
    if (
      !isset($post_vars['current_password']) || '' == trim($post_vars['current_password']) ||
      !isset($post_vars['password']) || '' == trim($post_vars['password'])
    ) {
      $errors[] = 'Please enter password.';
    }
  }

  if (isset($post_vars['password']) && '' != $post_vars['password']) {
    if (!isset($post_vars['confirm_password']) || $post_vars['password'] != $post_vars['confirm_password']) {
      $errors[] = 'Please make sure the password and confirm password are same.';
    } else {
      $user_data['user_pass'] = $post_vars['password'];
    }
  }

  // var_dump( $current_user ); exit;

  if (get_user_meta($current_user->ID, 'is_imported', true) !== '1') {
    if (!wp_check_password($post_vars['current_password'], $current_user->data->user_pass, $current_user->ID)) {
      $errors[] = 'You entered incorrect current password.';
    }
  }

  $update_user = wp_update_user($user_data);

  if (is_wp_error($update_user)) {
    $errors[] = $update_user->get_error_messages()[0];
  }

  if (count($errors) == 0) {
    delete_user_meta($current_user->ID, 'is_imported');
    wp_redirect($returnTo);
    exit;
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
      <div class="container">
        <div class="row justify-content-center">
          <div id="change-password" class="col-12">

            <?php if (!empty($errors)) : ?>
              <div class="alert alert-danger my-3">
                <?php foreach ($errors as $error) : ?>
                  <div class="text-center"><?php echo $error; ?></div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($messages)) : ?>
              <div class="alert alert-success my-3">
                <?php foreach ($messages as $message) : ?>
                  <div class="text-center"><?php echo $message; ?></div>
                <?php endforeach; ?>
              </div>
            <?php endif;

            /* Start the Loop */
            while (have_posts()) :
              the_post();
            ?>
              <h2 class="title">
                <?php the_title(); ?>
              </h2>
              <?php the_content(); ?>
              <form action="<?php echo home_url('/change-password/'); ?>" method="post" onSubmit="document.getElementById('btn-submit').disabled=true;">
                <input type="hidden" name="action" value="change-password">
                <div class="mt-2">
                  <input type="submit" name="submit" id="btn-submit" class="btn btn-dark rounded" value="Email me the password reset link">
                </div>
              </form>
            <?php
            endwhile; // End of the loop.
            ?>
          </div><!-- #primary -->
        </div><!-- .row -->
      </div><!-- .container -->
    </div>
  </div>
</div>

<?php
get_footer();
// get_template_part('page-templates/brag-observer/footer');
