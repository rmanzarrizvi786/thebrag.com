<?php /* Template name: Brag Observer UnSubscribe from ALL */

if (!is_user_logged_in()) :
  $redirect_uri = home_url('/wp-login.php?redirect_to=') . urlencode(home_url('/unsubscribe/'));
  // echo $redirect_uri;
  // exit;
  wp_redirect($redirect_uri);
  exit;
endif;

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

get_template_part('page-templates/brag-observer/header');

?>
<div class="row content">
  <div class="my-3 col-12 col-md-6 offset-md-3">
    <?php the_content(); ?>

    <div class="text-center">
      <a href="#" onClick="return confirm('Are you sure?');">Click here</a> to unsubscribe from all newsletters
    </div>
  </div>
</div>


<?php
get_template_part('page-templates/brag-observer/footer');
