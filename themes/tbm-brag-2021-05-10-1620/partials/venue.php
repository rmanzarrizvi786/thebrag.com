<?php $no_of_columns = ! isset( $no_of_columns ) ? 3 : $no_of_columns; ?>
<article class="col-lg-<?php echo 12 / $no_of_columns; ?> col-md-6 col-6  d-flex flex-column align-items-center mb-4">
    <a href="<?php the_permalink(); ?>" class="venue-item-wrap">
        <div class="venue-thumbnail">
            <?php if ( '' !== get_the_post_thumbnail() ) :
                the_post_thumbnail('thumbnail');
            else: ?>
            <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder.png" alt="Brag Magazine" />
            <?php endif; ?>
        </div><!-- .post-thumbnail -->
        <div class="venue-name text-white p-2 text-center d-block"><?php the_title(); ?></div>
    </a>
</article>