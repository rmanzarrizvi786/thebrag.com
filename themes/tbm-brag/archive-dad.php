<?php get_header(); ?>

<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$exclude_posts = [];

$hero_stories_args = [
    'post_status' => 'publish',
    'posts_per_page' => 3,
    'post_type' => 'dad',
];
$hero_stories_query = new WP_Query($hero_stories_args);
$hero_stories = [];
if ($hero_stories_query->have_posts()) :
    $hero_stories = $hero_stories_query->posts;
    wp_reset_query();
endif;
$exclude_posts = array_merge($exclude_posts, wp_list_pluck($hero_stories, 'ID'));

$template_args = [
    'post_type' => 'dad',
    'paged' => $paged,
];
?>

<div class="ad-billboard ad-billboard-top container py-1 py-md-2">
    <div class="mx-auto text-center">
        <?php render_ad_tag('leaderboard'); ?>
    </div>
</div>

<?php
if (1 === $paged) {
    get_template_part('template-parts/category/trending', null, array_merge($template_args, ['hero_stories' => $hero_stories]));
}
?>

<div class="container bg-yellow pt-1">
    <?php get_template_part('template-parts/category/latest', null, array_merge($template_args, ['exclude_posts' => $exclude_posts, 'post_type' => 'dad'])); ?>
</div>

<?php get_footer();
