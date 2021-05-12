<?php
/**
 * Template Name: Flipboard RSS Template - flipboard
 */
$args = array(
    'showposts' => -1,
    'ignore_sticky_posts' => 1,
    'post_type' => array( 'post' ),
    'date_query' => array(
        'column' => 'post_date',
        'after' => '-80 days'
    ),
    'post_status' => 'publish',
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
    <language><?php bloginfo_rss( 'language' ); ?></language>
<?php while(have_posts()) : the_post(); ?>
    <item>
        <title><?php the_title_rss(); ?></title>
        <link><?php the_permalink_rss(); ?></link>
        <guid><?php the_guid(); ?></guid>
        <pubDate><?php echo mysql2date('c', get_post_time('c', true), false); ?></pubDate>
        <author><?php
        if ( get_field('Author') != '') {
            the_field ('Author');
        } else {
            the_author();
        }
        ?></author>
        <description><![CDATA[
            <?php the_excerpt_rss(); ?>
        ]]</description>
        <?php
        $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
        if ( $src ) : ?><enclosure url="<?php echo $src[0]; ?>"<?php echo (int)$src[1] > 0 ? ' length="' . $src[1] . '"' : ''; ?> /><?php endif; ?>
        
        <?php
        $post_categories = wp_get_post_categories( get_the_ID() );
        $cats = array();
        if ( count( $post_categories ) > 0 ) :
            foreach ( $post_categories as $c ) :
                $cat = get_category( $c ); ?><category><?php echo $cat->name; ?></category><?php endforeach;
        endif;
        ?>
    
    </item>
<?php endwhile; ?>
</channel>
</rss>