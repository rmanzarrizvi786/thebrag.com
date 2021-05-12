<?php
/*
 * Template Name: Featured (Full-width)
 * Template Post Type: post
 */
 if ( 'dad' == get_post_type() ) :
     get_template_part( 'partials/header-dad' );
 else:
     get_header();
 endif; ?>

<script src="https://api.dmcdn.net/all.js"></script>
<script>
var vid_pool = [ 'x7s53nx', 'x7r86iq', 'x7rqq2l', 'x7r83du', 'x7p1l1b' ];
</script>
<div id="articles-wrap">
  <?php
      if ( have_posts() ) :
          $main_post = true;
          while ( have_posts() ) :
              the_post();
              include( get_template_directory() . '/partials/single-featured.php' );
          endwhile;
          wp_reset_query();
      endif;
  ?>
</div>

<?php if ( 'dad' == get_post_type() ) :
    get_template_part( 'partials/footer-dad' );
else:
    get_footer();
endif;
