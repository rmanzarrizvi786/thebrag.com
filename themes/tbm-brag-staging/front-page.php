<?php

/**
 * Template name: homepage
 */
get_header();
?>

<div class="ad-billboard container py-4">
    <div class="mx-auto text-center">
        <?php render_ad_tag('leaderboard');
        ?>
        <a href="https://thebrag.media?970x250" target="_blank"><img src="http://placehold.it/970x250/663366/fff?text=970x250"></a>
    </div>
</div>

<?php get_template_part('modules/home/trending'); ?>

<div class="container bg-yellow">
    <div class="container py-4">
        <div class="mx-auto text-center">
            <?php render_ad_tag('incontent_1');
            ?>
            <a href="https://thebrag.media?970x250" target="_blank"><img src="http://placehold.it/970x250/663366/fff?text=970x250"></a>
        </div>
    </div>

    <?php get_template_part('modules/home/spotlight'); ?>

    <?php get_template_part('modules/home/latest'); ?>

    <div class="container mb-4">
        <div class="mx-auto text-center">
            <?php render_ad_tag('incontent_2');
            ?>
            <a href="https://thebrag.media?970x250" target="_blank"><img src="http://placehold.it/970x250/663366/fff?text=970x250"></a>
        </div>
    </div>

    <?php get_template_part('modules/home/video-record'); ?>

    <?php get_template_part('modules/home/latest-categories'); ?>

</div>

<?php
get_footer();
