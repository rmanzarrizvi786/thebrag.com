<?php get_header(); ?>

<div class="ad-billboard ad-billboard-top container py-1 py-md-2">
    <div class="mx-auto text-center">
        <?php render_ad_tag('leaderboard'); ?>
    </div>
</div>

<div class="container search bg-yellow rounded-top">

    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
        <div class="d-flex p-5 align-items-start">
            <input type="search" class="search-field form-control" placeholder="Search..." value="<?php echo get_search_query(); ?>" name="s" id="s">
            <input type="submit" class="search-submit btn btn-dark" value="Search">
        </div>
    </form>
    <div class="d-flex flex-wrap align-items-start mt-2 latest px-2">
        <?php
        $s = get_search_query();
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array(
            's' => $s,
            'paged' => $paged,
            'post_type' => 'post'
        );

        // The Query
        $the_query = new WP_Query($args);
        if ($the_query->have_posts()) :
            while ($the_query->have_posts()) :
                $the_query->the_post();
        ?>
                <div class="article-wrap col-12 col-md-4">
                    <?php
                    get_template_part('template-parts/single/tile');
                    ?>
                </div>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>

    <div class="d-flex page-nav-numbered pb-3">
        <?php echo paginate_links(); ?>
    </div>
</div>


<?php get_footer(); ?>