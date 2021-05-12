<?php
/*
* Template Name: Verify Account
*/
use \DrewM\MailChimp\MailChimp;

$errors = [];
$messages = [];

$redirectTo = isset( $_GET['returnTo'] ) ? urldecode( $_GET['returnTo'] ) : home_url( '/observer-subscriptions/' );

if ( isset( $_GET['a'] ) && 'unsub' == $_GET['a'] ) {
  $redirectTo = home_url('/observer-subscriptions/');
}

if ( isset( $_GET['oc'] ) ) {

  if ( is_user_logged_in() && ( current_user_can( 'edit_posts' ) || get_user_meta( $current_user->ID, 'is_activated', true ) === '1' ) ) {
    // wp_redirect( home_url( '/observer-subscriptions/' ) ); die();
    wp_redirect( $redirectTo ); die();
  }

  $data = @unserialize( base64_decode( $_GET['oc'] ) );

  if ( ! $data ) {
    if ( isset( $_GET['fl'] ) && 0 == $_GET['fl'] ) { // Force Login, if 0 => redirect to intended URL without logging in
      wp_redirect( $redirectTo ); die();
    }
    $errors[] = 'Invalid link';
  }

  $oc_token = get_user_meta( $data['id'], 'oc_token', true );

  if ( count( $errors ) == 0 ) {
    if( $oc_token == $data['oc_token'] ) { // checks whether the decoded code given is the same as the one in the data base
      update_user_meta( $data['id'], 'is_activated', 1 ); // updates the database upon successful activation
      // update_user_meta( $data['id'], 'is_imported', 1 ); // updates the database to set imported from old list
      // if ( isset( $_GET['a'] ) && 'unsub' == $_GET['a'] ) {
        // $_SESSION['ob_fresh'] = true;
      // }
      $user_id = $data['id'];
      delete_user_meta( $data['id'], 'no_welcome_email' );
      // delete_user_meta( $data['id'], 'oc_token' );
      $user = get_user_by( 'id', $user_id );
      if( $user ) { // logs the user in
        wp_set_current_user( $user_id, $user->user_login );
        wp_set_auth_cookie( $user_id );
        do_action( 'wp_login', $user->user_login, $user );
      }
      $redirectTo = isset( $_GET['returnTo'] ) ? urldecode( $_GET['returnTo'] ) : home_url( '/observer-subscriptions/' );
      $messages[] = 'Please wait...';
    } else {
      $errors[] = 'Invalid link';
    }
  }
} else if( isset( $_GET['p'] ) ) { // If accessed via an authentification link - Activation

  if ( is_user_logged_in() && ( current_user_can( 'edit_posts' ) || get_user_meta( $current_user->ID, 'is_activated', true ) === '1' ) ) {
    wp_redirect( home_url( '/observer-subscriptions/' ) ); die();
  }

  $data = @unserialize( base64_decode( $_GET['p'] ) );

  if ( ! $data ) {
    $errors[] = 'Invalid link';
  }

  $code = get_user_meta( $data['id'], 'activationcode', true );

  if ( ! get_user_by( 'id', $data['id'] ) ) { // If in case user is deleted or doesn't exist with the ID
    wp_redirect( $redirectTo ); die();
  }

  $isActivated = get_user_meta($data['id'], 'is_activated', true); // checks if the account has already been activated. We're doing this to prevent someone from logging in with an outdated confirmation link
  if( $isActivated ) { // generates an error message if the account was already active

    wp_redirect( $redirectTo ); die();

    $messages[] = 'This account has already been activated.';

  } else {
    if ( isset( $_GET['err'] ) && 'unverified' == $_GET['err'] ) {
      $errors[] = '<p>You account is not verified yet, please click the link sent in the activation email or <a href="/verify/?a=resend&u=' . $data['id'] . '&to=' . urlencode( $redirectTo ) . '&p=' . $_GET['p'] . '">resend the activation email</a>.</p><p>Please note that any activation links previously sent lose their validity as soon as a new activation email gets sent.<p>';
    } else if( isset( $_GET['a'] ) && 'resend' == $_GET['a'] && isset( $_GET['u'] ) ) { // If resending confirmation mail
      require_once( __DIR__ . '/../../../plugins/brag-observer/classes/email.class.php' );
      $email = new Email();
      $email->sendUserVerificationEmail( $_GET['u'], $redirectTo );
      $messages []=  'Your activation email has been resent. Please check your email and your spam folder.';
      $redirectTo = null;
    } else {
      if ( count( $errors ) == 0 ) {
        if( $code == $data['code'] ) { // checks whether the decoded code given is the same as the one in the data base
          update_user_meta( $data['id'], 'is_activated', 1 ); // updates the database upon successful activation
          $user_id = $data['id']; // logs the user in
          $user = get_user_by( 'id', $user_id );
          if( $user ) {
            wp_set_current_user( $user_id, $user->user_login );
            wp_set_auth_cookie( $user_id );
            do_action( 'wp_login', $user->user_login, $user );
          }
          $messages[] = 'Your account has been activated! Redirecting now...';

          // Referrals in MailChimp {{
          if ( get_user_meta( $user_id, 'referrer_id', true ) ) {
            $referrer_id = get_user_meta( $user_id, 'referrer_id', true );
            $referrer = get_user_by( 'ID', $referrer_id );

            if( $referrer ) {
              $referrals_query = "
                SELECT
                  u.ID,
                  u.user_email
                FROM
                  $wpdb->users u
                  JOIN
                    $wpdb->usermeta um ON u.ID = um.user_id
                WHERE
                  um.meta_key = 'referrer_id' AND
                  um.meta_value = '{$referrer_id}'
                ORDER BY
                  u.ID DESC
              ";
              $all_referrals = $wpdb->get_results( $referrals_query );

              $referrals = [];
              if ( $all_referrals ) {
                foreach( $all_referrals as $referral ) {
                  if ( get_user_meta( $referral->ID, 'is_activated', true ) == '1' ) {
                    $referrals[ 'confirmed' ][] = $referral;
                  } else {
                    $referrals[ 'unconfirmed' ][] = $referral;
                  }
                }
              }
              $referrals_count = isset( $referrals[ 'confirmed' ] ) ? count( $referrals[ 'confirmed' ] ) : 0;

              update_user_meta( $referrer_id, 'referrals_count', $referrals_count );

              require_once( get_template_directory() . '/MailChimp.php');
              $api_key = '727643e6b14470301125c15a490425a8-us1';
              $MailChimp = new MailChimp( $api_key );

              $subscriber_hash = $MailChimp->subscriberHash( $referrer->user_email );
              $update_referrals_count = $MailChimp->patch("lists/5f6dd9c238/members/{$subscriber_hash}", [
                'merge_fields' => [ 'REFERRALS' => $referrals_count ]
              ]);
            }
          }
          // }} Referrals in MailChimp
        } else {
          $errors[] = '<p>Account activation failed. Please try again in a few minutes or <a href="/verify/?a=resend&u=' . $data['id'] . '&to=' . urlencode( $redirectTo ) . '&p=' . $_GET['p'] . '">resend the activation email</a>.</p><p>Please note that any activation links previously sent lose their validity as soon as a new activation email gets sent.</p>';
        }
      }
    }
  }
} else {
  wp_redirect( home_url() );
}

get_template_part( 'page-templates/brag-observer/header' );
?>

<div class="container">
  <div class="row justify-content-center">
    <div id="update-profile" class="col-sm-9 col-lg-9 my-5">
      <main class="site-main" role="main">

        <?php if ( ! empty( $errors ) ) : ?>
          <div class="alert alert-danger">
            <?php foreach( $errors as $error ) : ?>
              <div><?php echo $error; ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if ( ! empty( $messages ) ) : ?>
          <div class="alert alert-success">
            <?php foreach( $messages as $message ) : ?>
              <div><?php echo $message; ?></div>
            <?php endforeach; ?>
          </div>

          <?php if ( ! is_null( $redirectTo ) ) : ?>
          <script>
          window.setTimeout( function(){
            window.location = '<?php echo $redirectTo; ?>';
          }, 3000 );
          </script>
          <?php endif; // If redirecting ?>
        <?php endif; ?>

      </main>
    </div>
  </div>
</div>
<?php
get_template_part( 'page-templates/brag-observer/footer' );
