<?php if ('dad' == get_post_type()) :
// get_template_part( 'partials/header-dad' );
else :
    get_header();
endif; ?>

<div id="articles-wrap" class="container">
    <?php
    if (have_posts()) :
        $main_post = true;
        while (have_posts()) :
            the_post();
            get_template_part('modules/single/single-post');
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
