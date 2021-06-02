<?php

/**
 * Template name: homepage
 */
get_header();

$paged = (get_query_var('page')) ? get_query_var('page') : 1;
?>

<div class="ad-billboard ad-billboard-top container py-1 py-md-2">
    <div class="mx-auto text-center">
        <?php render_ad_tag('leaderboard'); ?>
    </div>
</div>

<?php
if (1 === $paged) {
    get_template_part('template-parts/home/trending');
}
?>

<div class="container bg-yellow">
    <!-- <div class="container py-4">
        <div class="mx-auto text-center">
            <?php // render_ad_tag('incontent_1'); 
            ?>
        </div>
    </div> -->

    <?php if (1 === $paged) {
        get_template_part('template-parts/home/spotlight');
    } ?>

    <?php get_template_part('template-parts/home/latest', null, ['paged' => $paged]); ?>

    <?php if (1 === $paged) { ?>
        <div class="container mb-4">
            <div class="mx-auto text-center">
                <?php render_ad_tag('incontent_1'); ?>
            </div>
        </div>

        <?php get_template_part('template-parts/home/video-record'); ?>

        <?php get_template_part('template-parts/home/latest-categories'); ?>
    <?php } // If front page
    ?>
</div>

<?php
get_footer();
