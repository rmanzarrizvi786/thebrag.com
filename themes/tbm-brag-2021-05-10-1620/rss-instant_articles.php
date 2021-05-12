<?php
/**
 * Template Name: FB Instant Articles RSS Template - instant-articles
 */
date_default_timezone_set( 'Australia/Sydney' );
$postCount = 100; // The number of posts to show in the feed
//$posts = query_posts('showposts=' . $postCount . '&ignore_sticky_posts=1');
$args = array(
    'showposts' => $postCount,
    'ignore_sticky_posts' => 1,
    'post_type' => array( 'post', 'dad' ),
    'category__not_in' => array( 288366 ),
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


        <?php while(have_posts()) : the_post();
        if (
          strpos( strtolower( get_the_title() ), 'quiz') !== false ||
          strpos( strtolower( get_the_title() ), 'poll') !== false ||
          strpos( strtolower( get_the_content() ), 'tastemaker') !== false ||
          has_shortcode( get_the_content(), 'observer_tastemaker_form' ) ||
          has_shortcode( get_the_content(), 'observer_subscribe_genre' ) ||
          has_shortcode( get_the_content(), 'observer_lead_generator_form' )
        ) {
          continue;
        }
        ?>
<item>
        <title><?php the_title_rss(); ?></title>
        <link><?php the_permalink_rss(); ?></link>
        <guid><?php the_guid(); ?></guid>
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
        <description><?php the_excerpt_rss(); ?></description>
        <content:encoded>
            <![CDATA[
                <?php
                    $template_file = get_stylesheet_directory() . '/template-instant-article.php';
                    load_template($template_file, false);
                ?>
                <?php the_excerpt_rss() ?>
            ]]>
        </content:encoded>
</item>
        <?php endwhile; ?>
</channel>
</rss>
