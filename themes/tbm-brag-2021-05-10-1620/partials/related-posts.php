<?php if ( ! post_password_required( $post ) ) : ?>
<div class="related-story col-md-3 col-6 mb-4">
    <?php $categories = get_the_category(get_the_ID()); if ( $categories && $categories[0] ) : ?>
    <a class="align-self-start mb-2 text-uppercase cat text-dark" href="<?php echo get_category_link( $categories[0]->term_id ); ?>"><?php echo $categories[0]->cat_name; ?></a>
    <?php endif; ?>
    <a href="<?php the_permalink(); ?>">
    <div class="post-thumbnail">
        <?php if ( '' !== get_the_post_thumbnail() ) :
            $alt_text = get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true );
            if ( $alt_text == '' ) {
                $alt_text = trim(strip_tags(get_the_title()));
            }
            $img_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' );
            $img_src = $img_src[0];
        ?>
            <img data-src="<?php echo $img_src; ?>" alt="<?php echo $alt_text; ?>" title="<?php echo $alt_text; ?>" class="lazyload">
        <?php else: ?>
        <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder.png">
        <?php endif; ?>
        
    </div><!-- .post-thumbnail -->
    <h3 class="mt-2"><?php the_title(); ?></h3>
    </a>
</div>
<?php endif;