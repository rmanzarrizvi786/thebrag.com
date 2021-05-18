<?php if ('dad' == get_post_type()) :
// get_template_part( 'partials/header-dad' );
else :
    get_header();
endif; ?>

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

<?php if ('dad' == get_post_type()) :
// get_template_part( 'partials/footer-dad' );
else :
    get_footer();
endif;
