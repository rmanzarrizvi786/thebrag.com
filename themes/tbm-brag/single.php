<?php get_header(); ?>

<div id="articles-wrap" class="container">
    <?php
    $count_articles = isset($_POST['count_articles']) ? absint($_POST['count_articles']) : 1;
    if (have_posts()) :
        $main_post = true;
        while (have_posts()) :
            the_post();
            get_template_part('template-parts/single/single', 'post', ['count_articles' => $count_articles]);
        endwhile;
        wp_reset_query();
    endif;
    ?>
</div>

<?php get_footer();