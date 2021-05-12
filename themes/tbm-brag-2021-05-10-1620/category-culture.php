<?php get_header(); ?>

<?php $exclude_posts = [];
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

if ( $paged === 1 ) :
?>
<div class="container p-4 text-white" style="background: black; position: relative;">
    <div id="section-hero" class="row">
        <!-- Cover Story (home page) -->
        <?php
            $cover_story_args = array(
                'post_status' => 'publish',
                'post__not_in'=> $exclude_posts,
                'posts_per_page' => 1,
                'post_type' => 'post',
                'category_name' => 'culture',
            );
            $cover_story_query = new WP_Query($cover_story_args);
            if ( $cover_story_query->have_posts() ) :
                while ( $cover_story_query->have_posts() ) :
                    $cover_story_query->the_post();
                    $cover_story_ID = get_the_ID();
                    $exclude_posts[] = $cover_story_ID;
                endwhile;
                wp_reset_query();
            endif;

            if ( !is_null( $cover_story_ID ) && $cover_story_ID != '' ) :
                $cover_story = get_post( $cover_story_ID );
                if ( $cover_story ) :
                    $cover_story_src = wp_get_attachment_image_src(get_post_thumbnail_id($cover_story->ID), 'cover-story');
                    $cover_story_alt_text = get_post_meta( get_post_thumbnail_id( $cover_story->ID ), '_wp_attachment_image_alt', true );
                    if ( $cover_story_alt_text == '' ) {
                        $cover_story_alt_text = trim(strip_tags(get_the_title()));
                    }
        ?>

        <div class="col-md-8">
            <?php if ( has_post_thumbnail( $cover_story ) ) : ?>
            <a href="<?php the_permalink( $cover_story ); ?>">
                <img data-src="<?php echo $cover_story_src[0]; ?>" alt="<?php echo $cover_story_alt_text; ?>" title="<?php echo $cover_story_alt_text; ?>" class="lazyload img-fluid">
            </a>
            <?php endif; // If thumbnail ?>
        </div>
        <div class="col-md-4 mt-3">
            <h2>
                <a href="<?php the_permalink( $cover_story ); ?>">
                    <?php echo get_the_title( $cover_story ); ?>
                </a>
            </h2>
            <p class="pb-5"><?php
            $metadesc = get_post_meta( $cover_story_ID, '_yoast_wpseo_metadesc', true );
            $excerpt = trim( $metadesc ) != '' ? $metadesc : string_limit_words( get_the_excerpt($cover_story_ID), 25 );
            echo $excerpt;
            ?></p>
            <?php
            $author_byline = '';
            if ( get_field('author', $cover_story_ID ) || get_field('Author', $cover_story_ID) ) :
                if ( get_field('author', $cover_story_ID ) ) :
                    $author_byline = get_field( 'author', $cover_story_ID );
                elseif ( get_field('Author', $cover_story_ID) ) :
                    $author_byline = get_field( 'Author', $cover_story_ID );
                endif; // If custom author is set

                $author_img_src = wp_get_attachment_image_src( get_field('author_profile_picture', $cover_story), 'thumbnail' );
            else : // If custom author has not been set
                $author_byline = '<a href="' . get_author_posts_url( $cover_story->post_author ) . '">' . get_the_author_meta( 'display_name', $cover_story->post_author ) . '</a>';
            endif; // If custom author is set
            ?>
            <div class="align-items-center text-uppercase" style="position: absolute; bottom: 0;">
              <div class="small">
                  <?php echo $author_byline; ?>
              </div>
            </div>
        </div>
        <?php
            endif; // If Cover Story
        endif; // If Cover Story ID
        ?>
        <?php wp_reset_query(); ?>
        <!-- End Cover Story -->
    </div><!-- /#section-hero.row -->
</div><!-- /.container -->
<?php endif; // If Gaming Category Home ?>

<div class="container category py-2">
<!--    <div class="row">
        <h1 class="col-12 archive-title mb-3"><?php echo single_tag_title( ''); ?></h1>
    </div>-->
    <div class="row posts">
    <?php
        $count = 1;
        $show_cats = false;
        while ( have_posts() ) :
            the_post();
            if ( in_array( get_the_ID(), $exclude_posts ) ) :
                continue;
            endif;
            $exclude_posts[] = get_the_ID();
            include( get_template_directory() . '/partials/post-tile.php' );
            if ( $count == 2 ) :
                echo '<div class="col-lg-4 col-md-6 col-12 p-0">';
                get_fuse_tag( 'mrec_1' );
                echo '</div>';
            endif;

            if ( $count == 8 ) :
                echo '</div><div class="row"><div class="col-lg-8 posts"><div class="row">';
            endif;

            if ( $count >= 8 ) :
                $no_of_columns = 2;
            endif;

            $count++;
        endwhile;

        wp_reset_postdata();
        echo '</div></div>';
        echo '<div class="col-lg-4 col-md-6 col-12 p-0">';
        get_fuse_tag( 'mrec_2' );
        echo '</div>';
    ?>
    </div>

    <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3 page-nav">
            <div class="d-flex justify-content-center my-4">
                <div class="m-3"><?php previous_posts_link( 'Newer' ); ?></div>
                <div class="m-3"><?php
                if ( $paged == 1 ) :
                    next_posts_link( 'MORE STORIES', '' );
                else :
                    next_posts_link( 'Older', '' );
                endif;
                ?></div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="" style=""><?php get_fuse_tag( 'hrec_2' ); ?></div>
        </div>
    </div>
</div>

<?php get_footer();
