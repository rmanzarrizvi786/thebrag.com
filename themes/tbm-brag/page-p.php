<?php
/* Template Name: Preview */

function hide_admin_bar() {
    return false;
}
add_filter( 'show_admin_bar' , 'hide_admin_bar');

$id = (int) $_GET['id'];
$query = new WP_Query(
    array(
        'p' => $id
    )
);

get_header();
?>

<div id="articles-wrap" class="container">
    <?php
        while ( $query->have_posts() ) : $query->the_post();
            get_template_part('template-parts/single/single', 'post', ['count_articles' => 1]);
        endwhile;
        wp_reset_query();
    ?>
</div>

<?php get_footer();