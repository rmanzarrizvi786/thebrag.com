<?php
/*
 * Template Name: Forgot Password
 */
if ( is_user_logged_in() ) :
  wp_redirect( home_url() );
  exit;
endif;
get_header(); ?>

<div class="container">
  <div class="lost-password-form row justify-content-center">
    <div id="lost-password" class="col-sm-9 col-lg-6 my-5">
      <main class="site-main" role="main">
        <?php
        /* Start the Loop */
        while ( have_posts() ) :
          the_post();
        ?>
        <h1 class="title text-center">
          <?php the_title(); ?>
        </h1>
        <?php the_content(); ?>
        <p>Enter your email address and we'll send you a link you can use to pick a new password.</p>

        <?php
        $attributes['errors'] = array();
        if ( isset( $_REQUEST['errors'] ) ) {
          $error_codes = explode( ',', $_REQUEST['errors'] );
          foreach ( $error_codes as $error_code ) {
            $attributes['errors'] []= get_auth_error_message( $error_code );
          }
        }
        if ( count( $attributes['errors'] ) > 0 ) :
          foreach ( $attributes['errors'] as $error ) :
            ?>
            <p class="alert alert-danger"><?php echo $error; ?></p>
            <?php
          endforeach;
        endif;

        $attributes['lost_password_sent'] = isset( $_REQUEST['checkemail'] ) && $_REQUEST['checkemail'] == 'confirm';
        if ( $attributes['lost_password_sent'] ) :
        ?>
        <p class="alert alert-info">Check your email for a link to reset your password.</p>
      <?php endif; ?>

        <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post" onSubmit="document.getElementById('btn-submit').disabled=true;">
          <p>
            <label for="user_login">Email
              <input type="text" name="user_login" id="user_login" class="form-control">
            </label>
          </p>
          <p class="lostpassword-submit">
            <input type="submit" name="submit" id="btn-submit" class="btn btn-dark rounded" value="Reset Password">
          </p>
        </form>
        <?php
        endwhile; // End of the loop.
        ?>
      </main><!-- main tag -->
    </div><!-- #primary -->
  </div><!-- .row -->
</div><!-- .container -->

<?php
get_footer();
