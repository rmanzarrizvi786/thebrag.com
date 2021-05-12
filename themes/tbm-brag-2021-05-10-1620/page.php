<?php get_header(); ?>

<?php
$post = $wp_query->post;
//$exclude_posts[] = $post->ID;
$exclude_posts = [];
?>

<div class="">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post()
    ?>
    <!-- Story Start -->
    <div class="news_story">
        <?php if ( '' !== get_the_post_thumbnail() ) : ?>
        
            <?php the_post_thumbnail(); ?>
        
        <?php endif; ?>
        <div class="post-content">
            <h1 id="story_title">
                <?php the_title(); ?>
            </h1>
            <p>
            <?php
                the_content();
            ?>
            </p>
        </div>
        <div class="clear"></div>
        <?php get_fuse_tag( 'hrec_2' ); ?>
    </div>
    <!-- Story End -->
    <?php
    endwhile;
    
    endif;
    ?>
</div>

<?php get_footer(); ?>