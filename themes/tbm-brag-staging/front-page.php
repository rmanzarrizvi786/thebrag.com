<?php

/**
 * Template name: homepage
 */
get_header();
?>

<div class="ad-billboard container py-2 py-md-4">
    <div class="mx-auto text-center">
        <?php render_ad_tag('leaderboard'); ?>
    </div>
</div>

<?php get_template_part('modules/home/trending'); ?>

<div class="container bg-yellow">
    <div class="container py-4">
        <div class="mx-auto text-center">
            <?php render_ad_tag('incontent_1'); ?>
        </div>
    </div>

    <?php get_template_part('modules/home/spotlight'); ?>

    <?php get_template_part('modules/home/latest'); ?>

    <div class="container mb-4">
        <div class="mx-auto text-center">
            <?php render_ad_tag('incontent_2'); ?>
        </div>
    </div>

    <?php get_template_part('modules/home/video-record'); ?>

    <?php get_template_part('modules/home/latest-categories'); ?>

</div>

<?php
get_footer();
