<?php get_header(); ?>

<div class="container search">

    <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <div class="row pt-3 pb-5 px-5">
            <input type="search" class="search-field col-11 form-control" placeholder="Search &hellip;" value="<?php echo get_search_query(); ?>" name="s">
            <input type="submit" class="search-submit btn btn-dark col-1" value="Search">
        </div>
    </form>
    <div class="row">
        <div class="col-12 cats mt-2 mb-4">
            <div style="border-top: 2px solid #000;"></div>
        </div>
    </div>
    <div class="row posts">
    <?php
    $s = get_search_query();
    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
    $args = array(
        's' => $s,
        'paged' => $paged,
        'post_type' => 'post'
    );

    // The Query
    $the_query = new WP_Query( $args );
    if ( $the_query->have_posts() ) :
        $count = 1;
        $show_cats = true;
        while ( $the_query->have_posts() ) :
           $the_query->the_post();
           include( get_template_directory() . '/partials/post-tile.php' );
           if ( $count == 2 ) :
                echo '<div class="col-lg-4 col-md-6 col-12 p-0">';
                get_fuse_tag( 'mrec_1', 'single' );
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
        $show_cats = false;

        wp_reset_postdata();
        echo '</div></div>';
        echo '<div class="col-lg-4 col-md-6 col-12 p-0">';
        get_fuse_tag( 'mrec_2', 'single' );
        echo '</div>';
    endif;
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

<div class="pt-0 pb-2"><?php get_fuse_tag( 'hrec_2', 'search' ); ?></div>


<?php get_footer(); ?>
