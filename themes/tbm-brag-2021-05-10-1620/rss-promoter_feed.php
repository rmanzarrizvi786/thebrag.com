<?php
/**
 * Template Name: Promoter RSS Template
 */

$promoter = isset( $_GET['promoter'] ) ? $_GET['promoter'] : NULL;
$size = isset( $_GET['size'] ) ? $_GET['size'] : NULL;
if ( is_null( $promoter ) ) :
    return;
endif;

$postCount = ! is_null( $size ) ? $size : -1; // The number of posts to show in the feed
$args = array(
    'showposts' => $postCount,
    'ignore_sticky_posts' => 1,
    'post_type' => array( 'post' ),
    'meta_key' => 'promoter',
    'meta_value' => $promoter
);
$posts = query_posts( $args );
header('Content-Type: '.feed_content_type('rss2').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>

<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">

<channel>
        <title><?php bloginfo_rss('name'); ?></title>
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss('description') ?></description>
        <lastBuildDate><?php echo mysql2date('c', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php bloginfo_rss( 'language' ); ?></language>
        
        
        <?php while(have_posts()) : the_post(); ?>
<item>
        <title><?php the_title_rss(); ?></title>
        <guid><?php the_permalink_rss(); ?></guid>
        <pubDate><?php echo mysql2date('c', get_post_time('c', true), false); ?></pubDate>
        <author><?php
        if ( get_field('author') ) {
            echo get_field('author');
        } else if ( get_field('Author') ) {
            echo get_field('Author');
        } else {
            the_author();
        }
        ?></author>
</item>
        <?php endwhile; ?>
</channel>
</rss>