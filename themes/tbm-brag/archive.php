<?php get_header(); ?>

<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$exclude_posts = [];
$queried_obj = $wp_query->get_queried_object();
$post_type = $queried_obj->name;

$hero_stories_args = [
    'post_status' => 'publish',
    'posts_per_page' => 3,
    'post_type' => $post_type,
];
$hero_stories_query = new WP_Query($hero_stories_args);
$hero_stories = [];
if ($hero_stories_query->have_posts()) :
    $hero_stories = $hero_stories_query->posts;
    wp_reset_query();
endif;
$exclude_posts = array_merge($exclude_posts, wp_list_pluck($hero_stories, 'ID'));

$template_args = [
    'paged' => $paged,
    'post_type' => $post_type
];
?>

<div class="ad-billboard container py-2 py-md-4">
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
    <?php get_template_part('template-parts/category/latest', null, array_merge($template_args, ['exclude_posts' => $exclude_posts])); ?>
</div>

<?php get_footer();
