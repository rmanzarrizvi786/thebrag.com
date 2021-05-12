<?php $no_of_columns = ! isset( $no_of_columns ) ? 3 : $no_of_columns; ?>
<article class="col-lg-<?php echo 12 / $no_of_columns; ?> col-md-6 col-12  d-flex flex-column align-items-center mb-4">
    <?php if ( isset( $show_cats ) && $show_cats === true ) : ?>
    <div class="align-self-start mb-2 text-uppercase cat font-weight-bold">
        <?php if ( 'snaps' == $post->post_type ) : ?>
        GALLERY
        <?php elseif ( 'dad' == $post->post_type ) : ?>
        <?php $categories = get_the_terms(get_the_ID(), 'dad-category');
        if ( $categories ) :
            if ( $categories[0] && 'Uncategorised' != $categories[0]->name ) : ?>
            <a class="text-dark" href="<?php echo get_term_link( $categories[0], 'dad-category' ); ?>"><?php echo $categories[0]->name; ?></a>
          <?php elseif ( isset( $categories[1] ) ) : ?>
            <a class="text-dark" href="<?php echo get_term_link( $categories[1], 'dad-category' ); ?>"><?php echo $categories[1]->name; ?></a>
            <?php else : ?>
            <br>
            <?php endif; // If Uncategorised ?>
        <?php endif; // If there are Dad categories ?>
        <?php else : ?>
        <?php $categories = get_the_category();
        if ( $categories ) :
            if ( isset( $categories[0] ) && 'Evergreen' != $categories[0]->cat_name ) : ?>
                <?php if ( 0 == $categories[0]->parent ) : ?>
                    <a class="text-dark" href="<?php echo get_category_link( $categories[0]->term_id ); ?>"><?php echo $categories[0]->cat_name; ?></a>
                <?php else : $parent_category = get_category( $categories[0]->parent ); ?>
                    <a class="text-dark" href="<?php echo get_category_link( $parent_category->term_id ); ?>"><?php echo $parent_category->cat_name; ?></a>
                <?php endif; ?>
            <?php elseif ( isset( $categories[1] ) ) : ?>
                <?php if ( 0 == $categories[1]->parent ) : ?>
                    <a class="text-dark" href="<?php echo get_category_link( $categories[1]->term_id ); ?>"><?php echo $categories[1]->cat_name; ?></a>
                <?php else : $parent_category = get_category( $categories[1]->parent ); ?>
                    <a class="text-dark" href="<?php echo get_category_link( $parent_category->term_id ); ?>"><?php echo $parent_category->cat_name; ?></a>
                <?php endif; ?>
            <?php endif; // If Evergreen ?>
        <?php endif; // If there are Dad categories ?>
        <?php endif; // If Photo Gallery ?>
    </div>
    <?php endif; ?>
    <a href="<?php the_permalink(); ?>">
        <div class="post-thumbnail">
            <?php
                $post_id = get_the_ID();
                if ( '' !== get_the_post_thumbnail() ) :
                    $alt_text = get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true );
                    if ( $alt_text == '' ) {
                        $alt_text = trim(strip_tags(get_the_title()));
                    }
                    $img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'medium_large' );
            ?>
            <img data-src="<?php echo $img_src[0]; ?>" alt="<?php echo $alt_text; ?>" title="<?php echo $alt_text; ?>" class="lazyload img-fluid">
            <?php endif; ?>
        </div>
        <div class="post-content align-self-start">
            <h3 class="mt-3 mb-2"><?php the_title(); ?></h3>

            <p class="excerpt"><?php
            $author_name =
                    get_field('photographer') ? get_field('photographer') : (
                    get_field( 'author' ) ? get_field( 'author' ) : ( get_field( 'Author' ) ? get_field( 'Author' ): get_the_author_meta( 'first_name', $post->post_author ) . ' ' . get_the_author_meta( 'last_name', $post->post_author ) )
                            );
            if ( 'snaps' == $post->post_type ) :
                echo 'Relive all the highlights (or check out what you missed) with our full photo gallery by ' . $author_name . '.';
            else :
                $metadesc = get_post_meta( get_the_ID(), '_yoast_wpseo_metadesc', true );
                $excerpt = trim( $metadesc ) != '' ? $metadesc : string_limit_words( get_the_excerpt(), 25 );
                echo $excerpt;
            endif;
            ?></p>
        </div>
    </a>
</article>
