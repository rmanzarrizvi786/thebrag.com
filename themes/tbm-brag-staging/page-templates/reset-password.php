<?php
/*
 * Template Name: Reset Password
 */
if ( is_user_logged_in() ) :
    wp_redirect( home_url() );
    exit;
endif;

if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
    $attributes['login'] = $_REQUEST['login'];
    $attributes['key'] = $_REQUEST['key'];
} else {
    wp_redirect( home_url() );
    exit;
}

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
        ?>
        <form name="resetpassform" id="resetpassform" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>" method="post" autocomplete="off" onSubmit="document.getElementById('btn-submit').disabled=true;">
          <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $attributes['login'] ); ?>" autocomplete="off" />
          <input type="hidden" name="rp_key" value="<?php echo esc_attr( $attributes['key'] ); ?>" />
          <p>
            <label>New password
              <input type="password" name="pass1" id="pass1" class="form-control" size="20" value="" autocomplete="off" />
            </label>
          </p>
          <p>
            <label>Repeat new password
              <input type="password" name="pass2" id="pass2" class="form-control" size="20" value="" autocomplete="off" />
            </label>
          </p>
          <p class="resetpass-submit">
            <input type="submit" name="submit" id="btn-submit" class="btn btn-dark rounded" value="Reset Password" />
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
