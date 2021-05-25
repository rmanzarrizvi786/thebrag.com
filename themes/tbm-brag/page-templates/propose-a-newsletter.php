<?php
/*
 * Template Name: Propose a Newsletter
 */

if (!is_user_logged_in()) :
  wp_redirect(home_url('/wp-login.php?redirect_to=') . urlencode(home_url('/observer-subscriptions/')));
  exit;
endif;

$current_user = wp_get_current_user();

if ( isset( $_POST['action'] ) && 'propose-a-newsletter' == $_POST['action'] ) :
  $headers[] = 'Content-Type: text/html; charset=UTF-8';
  $headers[] = 'From: The Brag <noreply@thebrag.media>';
  $to = 'poppy@thebrag.media';

  $subject = 'Propose a Newsletter submission on The Brag Observer';

  ob_start();
  include( get_template_directory() . '/email-templates/header.php' );

  echo '<p><strong>Name: ' . get_user_meta( $current_user->ID, 'first_name', true ) . ' ' . get_user_meta( $current_user->ID, 'last_name', true ) . '</strong></p>';
  echo '<p><strong>Email: ' . $current_user->user_email . '</strong></p><p>&nbsp;</p>';

  foreach ( $_POST as $k => $v ) :
    if ( in_array( $k, [ 'action' ] ) )
      continue;
    echo '<p><strong>' . ucwords( str_replace( [ '_', ], [ ' ', ], $k ) ) . ':</strong><p>' . wpautop( $v ) . '</p><p>&nbsp;</p>';
  endforeach;

  include( get_template_directory() . '/email-templates/footer.php' );
  $body = ob_get_contents();
  ob_end_clean();

  if( $body ) {
    wp_mail( $to, $subject, $body, $headers );
  }

  wp_redirect( home_url( 'propose-a-newsletter/?success=1' ) ); die();
endif;

get_header();
?>

<div class="container">
  <div class="lost-password-form row justify-content-center">
    <div id="lost-password" class="col-sm-9 col-lg-9 my-5">
      <?php if ( isset( $_GET['success'] ) && 1 == $_GET['success'] ): ?>
        <div class="alert alert-success text-center">Thank you!</div>
      <?php endif; ?>
      <form action="" method="post" onSubmit="document.getElementById('btn-submit').disabled=true;">
        <input type="hidden" name="action" value="propose-a-newsletter">
        <main class="site-main" role="main">
          <div class="row">
            <div class="col-12 mt-3">
              <h4>Describe the newsletter topic you'd like to propose:<h4>
              <input type="text" name="topic" class="form-control" required>
            </div>

            <div class="col-12 mt-3">
              <h4>Are you interested in curating this newsletter?<h4>
              <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-secondary">
                  <input type="radio" name="interested_curating" value="yes"> Yes
                </label>

                <label class="btn btn-secondary"><input type="radio" name="interested_curating" value="no"> No</label>
              </div>
            </div>

            <div class="col-12 mt-3">
              <h4>Why are you well-suited to curate this newsletter? How many hours/week are you interested in putting into it?<h4>
              <input type="text" name="why_suited" class="form-control" required>
            </div>

            <div class="col-12 mt-3">
              <h4>Why is this is a great idea?<h4>
              <textarea name="why_great_idea" class="form-control" required></textarea>
            </div>

            <div class="col-12 mt-3 text-center">
              <button type="submit" id="btn-submit" class="btn btn-dark rounded">Submit</button>
            </div>
          </div>
        </main><!-- main tag -->
      </form>
    </div><!-- #primary -->
  </div><!-- .row -->
</div><!-- .container -->

<?php
get_footer();
