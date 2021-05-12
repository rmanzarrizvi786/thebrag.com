<?php
/*
 * Template Name: Login
 */

if (!is_user_logged_in()) {
  wp_redirect(wp_login_url());
} else {
  wp_redirect(home_url('/observer'));
}
exit;

/*
function http($url, $params=false) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  if($params)
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'User-Agent: curl', # Apple requires a user agent header at the token endpoint
  ]);
  $response = curl_exec($ch);
  return json_decode($response);
}
if(isset($_POST['code'])) { // Handle "Sign in with Apple"
  $apple['client_id'] = 'com.thebrag';
  $apple['client_secret'] = 'eyJraWQiOiJLU0ZETjJNQjg3IiwiYWxnIjoiRVMyNTYifQ.eyJpc3MiOiIzWUhKMjdVWkxYIiwiaWF0IjoxNTkwMDMxMTEzLCJleHAiOjE2MDU1ODMxMTMsImF1ZCI6Imh0dHBzOi8vYXBwbGVpZC5hcHBsZS5jb20iLCJzdWIiOiJjb20udGhlYnJhZyJ9.Z_NfIdfKuiYJKZAX_LDCTg4bs22sj3WCoesf2CBjyDBEqImIhXX1VnTfnAhnQA-XVmllRZlmylLag79sr8bxyw';
  $apple['redirect_uri'] = home_url( 'login/' );

  $response = http('https://appleid.apple.com/auth/token', [
    'grant_type' => 'authorization_code',
    'code' => $_POST['code'],
    'redirect_uri' => $apple['redirect_uri'],
    'client_id' => $apple['client_id'],
    'client_secret' => $apple['client_secret'],
  ]);

  var_dump( $response ); exit;

  if( ! isset($response->access_token ) ) {
    wp_redirect( home_url( 'login' ) ); die();
  }

  $apple['claims'] = explode('.', $response->id_token)[1];
  $apple['claims'] = json_decode(base64_decode($apple['claims']));

  $user = get_user_by( 'email', $apple['claims']->email );

  if ( ! $user ) {
    $user = get_user_by( 'login', $apple['claims']->email );
  }

  if ( $user ) {
    $user_id = $user->ID;
  } else {
    $user_id = wp_insert_user([
      'user_login' => $apple['claims']->email,
      'user_pass' => NULL,
      'user_email' => trim( $apple['claims']->email ),
      'first_name' => '',
      'last_name' => '',
      'user_registered' => date('Y-m-d H:i:s'),
      'role' => 'subscriber'
    ]);

    // var_dump( $user_id ); exit;

    if ( $apple['claims']->is_private_email ) {
      update_user_meta( $user_id, 'is_private_email', true );
    }
  }

   $current_user = wp_set_current_user( $user_id );
   wp_set_auth_cookie( $user_id );

   wp_redirect( home_url( 'login' ) ); die();
}
*/

require_once(ABSPATH . 'sso-sp/simplesaml/lib/_autoload.php');
$auth = new SimpleSAML_Auth_Simple('default-sp');

if ($auth->isAuthenticated()) {
  $sso_user = $auth->getAttributes();
  $user = get_user_by('email', $sso_user['mail'][0]);
  if (!$user) :
    $user_id = wp_insert_user(
      array(
        'user_login' => $sso_user['mail'][0],
        'user_pass' => NULL,
        'user_email' => $sso_user['mail'][0],
        'first_name' => $sso_user['first_name'][0],
        'last_name' => $sso_user['last_name'][0],
        'user_registered' => date('Y-m-d H:i:s'),
        'role' => $sso_user['role'][0]
      )
    );
  else :
    $user_id = $user->ID;
  endif; // User not found using SSO email

  $current_user = wp_set_current_user($user_id);
  wp_set_auth_cookie($user_id);
  // wp_redirect( home_url() );
}

$wp_referer = wp_get_referer();

if (!$wp_referer || strpos($wp_referer, 'wp-login.php')) {
  $wp_referer = home_url('/observer/');
}

if (!isset($_SESSION['ReturnToUrl'])) {
  $_SESSION['ReturnToUrl'] = $wp_referer;
}

$auth->requireAuth([
  'ReturnTo' => $wp_referer,
  'KeepPost' => FALSE,
]);
\SimpleSAML\Session::getSessionFromRequest()->cleanup();

wp_redirect(isset($_SESSION['ReturnTo']) ? $_SESSION['ReturnTo'] : home_url('/observer/'));
exit;

get_header();
?>

<div class="container login_register_page">
  <div class="row">
    <div class="col-md-8">
      <div class="login-form row justify-content-center">
        <div id="login" class="col-12 py-3">
          <main class="site-main py-3" role="main">
            <?php

            // echo '<pre>'; print_r( wp_get_current_user() ); echo '</pre>';

            // $attributes = $auth->getAttributes();
            // echo '<pre>'; print_r( $attributes ); echo '</pre>';

            echo '<a href="' . home_url('/logout/') . '">Logout</a>';
            /* Start the Loop */
            while (have_posts()) :
              the_post();
            ?>
              <!-- <h1 class="title text-center">
                    <?php // the_title(); 
                    ?>
                </h1> -->
              <!-- <h2 class="text-center">Login with your Facebook account</h2> -->
            <?php
              the_content();
            endwhile; // End of the loop.
            ?>
          </main><!-- main tag -->
        </div><!-- #primary -->
      </div><!-- .row -->
    </div>
    <div class="col-md-4">
      <?php get_fuse_tag('mrec_1', 'single'); ?>
      <?php get_fuse_tag('mrec_2', 'single'); ?>
    </div>
  </div>

</div><!-- .container -->

<?php
get_footer();
