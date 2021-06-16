<?php

/**
 * Template name: homepage
 */
get_header();

?>

<div class="ad-billboard ad-billboard-top container py-1 py-md-2">
    <div class="mx-auto text-center">
        <?php render_ad_tag('leaderboard'); ?>
    </div>
</div>

<section class="bg-yellow p-3">
    <h1 class="story-title mb-3" style="text-align: center;">
        <?php the_title(); ?>
    </h1>

    <div class="post-content">
        <?php the_content(); ?>
    </div>
</section>

<?php
get_footer();
