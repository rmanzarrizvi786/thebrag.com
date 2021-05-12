<?php get_header(); ?>

<?php $exclude_posts = []; ?>

<div class="container category">
    <div class="row">
        <h1 class="col-12 archive-title mb-3"><?php
        $category = $wp_query->get_queried_object();
        $parent_category = get_category($category->parent);
        // var_dump( $parent_category ); exit;
        if( isset( $parent_category->name ) ) :
            echo '<a class="" href="' . get_category_link( $parent_category ) . '">' . $parent_category->name . '</a>';
            echo '<span class="text-muted"> > </span>';
        endif;
        echo single_cat_title( '');
        ?></h1>
    </div>
    <div class="row posts">
    <?php
        $count = 1;
        $show_cats = false;
        while ( have_posts() ) :
            the_post();
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
                <div class="m-3"><?php next_posts_link( 'Older', '' ); ?></div>
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
