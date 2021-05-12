<?php get_header(); ?>

<div id="articles-wrap">
  <?php
      if ( have_posts() ) :
          $main_post = true;
          while ( have_posts() ) :
              the_post();
              include( 'partials/single-post.php' );
          endwhile;
          wp_reset_query();
      endif;
  ?>
</div>

<?php get_footer();
