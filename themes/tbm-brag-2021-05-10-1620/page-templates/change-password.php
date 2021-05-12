<?php
/*
 * Template Name: Change Password
 */
if ( ! is_user_logged_in() ) :
  require_once(  ABSPATH . 'sso-sp/simplesaml/lib/_autoload.php');
  $auth = new SimpleSAML_Auth_Simple('default-sp');
  $auth->requireAuth([
   'ReturnTo' => wp_get_referer() ? : home_url(),
   'KeepPost' => FALSE,
  ]);
  \SimpleSAML\Session::getSessionFromRequest()->cleanup();
  exit;
endif;

$current_user = wp_get_current_user();

$returnTo = isset( $_REQUEST['returnTo'] ) ? $_REQUEST['returnTo'] : home_url( '/change-password?success=1' );

$errors = [];
$messages = [];

if ( isset( $_GET['success'] ) ) {
  $messages[] = 'Your password has been updated.';
}

if ( isset( $_POST ) && isset( $_POST['action'] ) && 'change-password' == $_POST['action'] ) {
  $post_vars = stripslashes_deep( $_POST );

  $user_data = [
    'ID' => $current_user->ID
  ];

  if ( get_user_meta( $current_user->ID, 'is_imported', true ) !== '1' ) {
    if(
      ! isset( $post_vars['current_password'] ) || '' == trim( $post_vars['current_password'] ) ||
      ! isset( $post_vars['password'] ) || '' == trim( $post_vars['password'] )
    ) {
      $errors[] = 'Please enter password.';
    }
  }

  if ( isset( $post_vars['password'] ) && '' != $post_vars['password'] ) {
    if ( ! isset( $post_vars['confirm_password'] ) || $post_vars['password'] != $post_vars['confirm_password'] ) {
      $errors[] = 'Please make sure the password and confirm password are same.';
    } else {
      $user_data['user_pass'] = $post_vars['password'];
    }
  }

  // var_dump( $current_user ); exit;

  if ( get_user_meta( $current_user->ID, 'is_imported', true ) !== '1' ) {
    if ( ! wp_check_password( $post_vars['current_password'], $current_user->data->user_pass, $current_user->ID ) ) {
      $errors[] = 'You entered incorrect current password.';
    }
  }

  $update_user = wp_update_user( $user_data );

  if ( is_wp_error( $update_user ) ) {
    $errors[] = $update_user->get_error_messages()[0];
  }

  if ( count( $errors ) == 0 ) {
    delete_user_meta( $current_user->ID, 'is_imported' );
    wp_redirect( $returnTo ); exit;
  }
}

get_template_part( 'page-templates/brag-observer/header' );
?>

<div class="container">
  <div class="row justify-content-center">
    <div id="change-password" class="col-12 col-lg-10 my-5">
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
        <?php endif;

        /* Start the Loop */
        while ( have_posts() ) :
          the_post();
        ?>
        <h1 class="title text-center">
          <?php the_title(); ?>
        </h1>
        <?php
        the_content();

        ?>

        <form action="<?php echo home_url( '/change-password/' ); ?>" method="post" onSubmit="document.getElementById('btn-submit').disabled=true;">

          <input type="hidden" name="returnTo" value="<?php echo $returnTo; ?>">

          <input type="hidden" name="action" value="change-password">

          <?php if ( get_user_meta( $current_user->ID, 'is_imported', true ) !== '1' ) { ?>

          <div class="row">

            <div class="col-12 mt-3 col-md-6">
              <h4>Current Password</h4>
              <input type="password" name="current_password" id="current_password" class="form-control" value="" autocomplete="new-password">
            </div>

          </div>

          <?php } ?>

          <div class="row">

            <div class="col-12 mt-3 col-md-6">
              <h4>New Password</h4>
              <input type="password" name="password" id="password" class="form-control" value="" autocomplete="new-password">
            </div>

            <div class="col-12 mt-3 col-md-6">
              <h4>Confirm New Password</h4>
              <input type="password" name="confirm_password" id="confirm_password" class="form-control" value="" autocomplete="new-password">
            </div>

            <div class="col-12 mt-3">
              <input type="submit" name="submit" id="btn-submit" class="btn btn-dark rounded" value="Save">
            </div>
          </div>
        </form>
        <?php
        endwhile; // End of the loop.
        ?>
      </main><!-- main tag -->
    </div><!-- #primary -->
  </div><!-- .row -->
</div><!-- .container -->

<?php
// get_footer();
get_template_part( 'page-templates/brag-observer/footer' );
