<?php
extract($args);
$exclude_posts = [];
?>
<section class="trending container d-flex flex-column flex-md-row pb-2 align-items-start px-0 px-md-2" style="overflow: hidden;">
    <?php
    $trending_story_args = [
        'post_status' => 'publish',
        'posts_per_page' => 1,
    ];
    if (get_option('most_viewed_yesterday')) {
        $trending_story_args['p'] = get_option('most_viewed_yesterday');
    }
    $trending_story_query = new WP_Query($trending_story_args);
    if ($trending_story_query->have_posts()) :
        while ($trending_story_query->have_posts()) :
            $trending_story_query->the_post();
            $trending_story_ID = get_the_ID();
            $exclude_posts[] = $trending_story_ID;
            $args['exclude_posts'][] = $trending_story_ID;
        endwhile;
        wp_reset_query();
    endif;
    ?>
    <div class="left trending-main m-2 mr-md-0 align-self-stretch col-12 col-md-8 pl-0 pl-md-2">
        <div class="text-center p-1 pt-0 mb-0 mx-1 subheading h-trending d-flex">
            <span><img src="<?php echo ICONS_URL; ?>star.svg" width="24" height="24" alt="★" style="padding-top: .12rem; padding-bottom: .18rem;"></span>
            <span>Most Read</span>
        </div>
        <?php
        if (!is_null($trending_story_ID) && $trending_story_ID != '') :
            $trending_story = get_post($trending_story_ID);
            if ($trending_story) :
                $categories = get_the_category($trending_story);

                $trending_story_image_id = get_post_thumbnail_id($trending_story->ID);
                $trending_story_src = wp_get_attachment_image_src($trending_story_image_id, 'large');
                $trending_story_alt_text = get_post_meta(get_post_thumbnail_id($trending_story->ID), '_wp_attachment_image_alt', true);
                if ($trending_story_alt_text == '') {
                    $trending_story_alt_text = trim(strip_tags(get_the_title()));
                }

                $featured_args = [
                    'trending_story' => $trending_story,
                    'trending_story_src' => $trending_story_src,
                    'trending_story_alt_text' => $trending_story_alt_text,
                    'categories' => $categories,
                    'exclude_posts' => $exclude_posts,
                ];

                if (get_field('image_has_text', $trending_story_image_id)) {
                    get_template_part('template-parts/home/featured', null, $featured_args);
                } else {
                    get_template_part('template-parts/home/featured', 'overlay', $featured_args);
                }
            endif; // If Trending Story
        endif;
        ?>
    </div>
    <div class="right trending-stories m-2 ml-0 col-12 col-md-auto pr-0 pr-md-2">
        <div class="text-center p-1 pt-0 mb-0 mx-1 subheading h-trending d-flex">
            <span><img src="<?php echo ICONS_URL; ?>line-graph.svg" width="24" height="24" alt="^"></span>
            <span>Trending</span>
        </div>
        <div class="pl-2">
            <?php
            global $wpdb;
            $exclude_posts_str = implode(',', $exclude_posts);
            $trending_article_ids = $wpdb->get_results(
                "SELECT post_id FROM (
                    SELECT post_id FROM `{$wpdb->prefix}tbm_trending`
                        ORDER BY `created_at` DESC LIMIT 10
                    ) AS temptable
                WHERE post_id NOT IN ( {$exclude_posts_str} )
                ORDER BY RAND()
                LIMIT 2"
            );
            $trending_articles_args = [
                'post_status' => 'publish',
                'post_type' => array('any'),
                'ignore_sticky_posts' => 1,
                'posts_per_page' => 2,
            ];
            if ($trending_article_ids && count($trending_article_ids) > 0) :
                $trending_articles_args['post__in'] = wp_list_pluck($trending_article_ids, 'post_id');
            endif;
            $trending_articles = new WP_Query($trending_articles_args);
            if ($trending_articles->have_posts()) :
            ?>
                <?php
                while ($trending_articles->have_posts()) :
                    $trending_articles->the_post();
                    $categories = get_the_category(get_the_ID());
                ?>
                    <a href="<?php the_permalink(); ?>" class="story p-2 pb-0 mb-2">
                        <div class="mb-1 text-uppercase trending-story-category">
                            <?php
                            if (isset($categories)) :
                                foreach ($categories as $category) :
                                    if (in_array($category->cat_name, ['Instagram Explore', 'Evergreen'])) :
                                        continue;
                                    else :
                                        echo $category->cat_name;
                                        break;
                                    endif; // If category name is Evergreen
                                endforeach; // For Each Category
                            endif; // If there are categories for the post 
                            ?>
                        </div><!-- Cats -->
                        <div class="d-flex flex-row justify-content-between align-items-start">
                            <div class="pb-3">

                                <h3><?php the_title(); ?></h3>
                            </div>
                            <div class="rounded ml-1 post-thumbnail mb-1">
                                <?php if ('' !== get_the_post_thumbnail()) :
                                    the_post_thumbnail('thumbnail');
                                endif; ?>
                            </div>
                        </div>
                    </a>
            <?php endwhile;
                wp_reset_postdata();
            endif; // If there are trending articles
            ?>
            <div class="ad-mrec" style="min-width: 300px;">
                <div class="mx-auto text-center">
                    <?php render_ad_tag('vrec_1'); ?>
                </div>
            </div>
        </div>
    </div>
</section>