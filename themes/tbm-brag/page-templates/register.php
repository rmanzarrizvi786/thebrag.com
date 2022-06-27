<?php
/*
 * Template Name: Register
 */
if (is_user_logged_in()) :
  wp_redirect(home_url());
  exit;
endif;

wp_redirect(home_url('/login/'));
exit;

$errors = [];

$returnTo = isset($_GET['returnTo']) ? $_GET['returnTo'] : home_url('/profile/');

if (isset($_POST) && isset($_POST['action']) && 'register' == $_POST['action']) {

  $post_vars = stripslashes_deep($_POST);

  if (!isset($post_vars['email']) || !is_email($post_vars['email'])) {
    $errors[] = 'Please enter valid email address.';
  }

  if (email_exists($post_vars['email']) && !is_user_logged_in()) {
    $errors[] = 'It seems you have already registered with the email address, please <a href="' . home_url('login') . '">login here</a>.';
  }

  if (count($errors) == 0) {
    $user_id = wp_insert_user(
      array(
        'user_login' => $post_vars['email'],
        'user_pass' => $post_vars['password'],
        'user_email' => trim($post_vars['email']),
        'first_name' => '',
        'last_name' => '',
        'user_registered' => date('Y-m-d H:i:s'),
        'role' => 'subscriber'
      )
    );


    // require_once( __DIR__ . '/../../../plugins/brag-observer/classes/email.class.php' );
    // $email = new Email();
    // $email->sendUserVerificationEmail( $user_id, $returnTo );

    // $current_user = wp_set_current_user( $user_id );
    // wp_set_auth_cookie( $user_id );

    wp_redirect(home_url('/register/?success=1'));
    die();
  }
}

get_header(); ?>

<div class="container login_register_page">
  <div class="login-form row justify-content-center">
    <div id="signup" class="col-12 my-5">
      <main class="site-main" role="main">
        <?php

        if (isset($_GET['success']) && 1 == $_GET['success']) {
        ?>
          <p class="alert alert-info">You will need to confirm your email address in order to activate your account. An email containing the activation link has been sent to your email address. If the email does not arrive within a few minutes, check your spam folder.</p>
        <?php
        }

        /* Start the Loop */
        while (have_posts()) :
          the_post();
        ?>
          <h1 class="title text-center">
            Register with
          </h1>
        <?php
        endwhile; // End of the loop.
        ?>
        <div class="wp-social-login-provider-list-wrap">
          <?php
          // WSL
          do_action('wordpress_social_login');

          // Apple
          $_SESSION['apple_signin_state'] = bin2hex(random_bytes(5));

          $apple['client_id'] = 'com.thebrag';
          $apple['redirect_uri'] = 'https://the-brag.com/login/';

          $authorize_url = 'https://appleid.apple.com/auth/authorize' . '?' . http_build_query([
            'response_type' => 'code',
            'response_mode' => 'form_post',
            'client_id' => $apple['client_id'],
            'redirect_uri' => $apple['redirect_uri'],
            'state' => $_SESSION['apple_signin_state'],
            'scope' => 'name email',
          ]);
          ?>
          <div class="wp-social-login-provider-list">
            <a rel="nofollow" href="<?php echo $authorize_url; ?>" class="wp-social-login-provider wp-social-login-provider-apple"></a>
          </div>
        </div>

        <form action="<?php echo home_url('/register/'); ?>" method="post" id="register-form" name="register-form" onSubmit="document.getElementById('btn-submit').disabled=true">

          <input type="hidden" name="action" value="register">

          <div class="col-md-6 offset-md-3">

            <hr>

            <?php if (!empty($errors)) : ?>
              <div class="alert alert-danger">
                <?php foreach ($errors as $error) : ?>
                  <div><?php echo $error; ?></div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <div class="mt-3">
              <h4>Email</h4>
              <input type="email" name="email" class="email form-control" placeholder="Email address" value="<?php echo isset($post_vars) && isset($post_vars['email']) ? $post_vars['email'] : ''; ?>" required>
            </div>

            <div class="mt-3">
              <h4>Password</h4>
              <input type="password" name="password" class="form-control" placeholder="Password" value="" required>
            </div>

            <div class="mt-3">
              <input type="submit" value="REGISTER" name="register" id="btn-submit" class="btn btn-dark rounded form-control mt-2">
              <div class="loading" style="display: none;">
                <div class="spinner">
                  <div class="double-bounce1"></div>
                  <div class="double-bounce2"></div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </main><!-- main tag -->
    </div><!-- #primary -->
  </div><!-- .row -->

  <div class="row justify-content-center my-3">
    <div class="col-12 margin-tb">
      <div class="register-forgot row">
        <div class="col-12 text-center">
          <span>Already registered?</span> <a href="<?php echo home_url(); ?>/login/" class="btn btn-success btn-sm">Login</a>
        </div>
      </div>
    </div>
  </div>

</div><!-- .container -->

<style>
  .wp-social-login-provider-list-wrap {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
  }

  .wp-social-login-connect-with {
    display: none;
  }

  a.wp-social-login-provider {
    border-radius: .25rem;
    color: #fff;
    font-size: 1.5rem;
    text-align: center;
    padding: 1rem .5rem;
    margin: 1rem .5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    height: 4rem;
    text-decoration: none;
  }


  .wp-social-login-provider-list {
    display: flex;
    padding: 0;
  }

  .wp-social-login-provider-list img {
    display: none;
  }

  a.wp-social-login-provider:before {
    content: "";
    background-color: #fff;
    border-radius: 2px;
    background-size: 70%;

    display: block;
    width: 3rem;
    height: 3rem;
    color: #fff;
    background-repeat: no-repeat;
    background-position: 50%;
  }

  a.wp-social-login-provider:hover {
    color: #fff;
  }

  a.wp-social-login-provider:after {
    display: block;
    width: 100%;
    margin-left: .5rem;
  }

  a.wp-social-login-provider-facebook {
    background: #3b5998;
  }

  a.wp-social-login-provider-facebook:before {
    background-image: url(<?php echo get_template_directory_uri(); ?>/images/facebook.svg);
    background-size: 90%;
  }

  a.wp-social-login-provider-facebook:after {
    content: "Facebook";
    background-color: #3b5998;
  }

  a.wp-social-login-provider-google {
    background: #DB4437;
  }

  a.wp-social-login-provider-google:before {
    background-image: url(<?php echo get_template_directory_uri(); ?>/images/google.svg);
  }

  a.wp-social-login-provider-google:after {
    content: "Google";
    background-color: #DB4437;
  }

  a.wp-social-login-provider-apple {
    background: #000;
  }

  a.wp-social-login-provider-apple:before {
    background-image: url(<?php echo get_template_directory_uri(); ?>/images/apple.svg);
  }

  a.wp-social-login-provider-apple:after {
    content: "Apple";
    background-color: #000;
  }

  .login_register_page hr {
    margin: 60px auto;
    position: relative;
  }

  .login_register_page hr:after {
    content: "OR";
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 50px;
    height: 50px;
    margin: auto;
    text-align: center;
    border: 1px solid rgba(0, 0, 0, .1);
    border-radius: 50%;
    font-size: 1rem;
    padding: 10px;
    box-sizing: border-box;
    background: #fff;
    color: rgba(0, 0, 0, .5);
  }

  @media (max-width: 640px) {

    .wp-social-login-provider-list-wrap,
    .wp-social-login-provider-list {
      /* flex-flow: column; */
    }

    a.wp-social-login-provider:after {
      display: none;
    }
  }
</style>

<?php
get_footer();
