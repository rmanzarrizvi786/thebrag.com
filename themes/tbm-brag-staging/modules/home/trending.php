<section class="trending container d-flex flex-column flex-md-row pb-2 align-items-start">
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
        endwhile;
        wp_reset_query();
    endif;
    ?>
    <div class="left trending-main m-2 mr-md-0 align-self-stretch col-12 col-md-8">
        <div class="text-center p-1 pt-0 mb-0 mx-1 subheading border-bottom2 h-featured d-flex">Featured Today</div>
        <?php
        if (!is_null($trending_story_ID) && $trending_story_ID != '') :
            $trending_story = get_post($trending_story_ID);
            if ($trending_story) :
                $categories = get_the_category($trending_story);
        ?>
                <a href="<?php the_permalink($trending_story); ?>">
                    <div class="story-hero text-white">
                        <?php
                        $trending_story_src = wp_get_attachment_image_src(get_post_thumbnail_id($trending_story->ID), 'large');
                        $trending_story_alt_text = get_post_meta(get_post_thumbnail_id($trending_story->ID), '_wp_attachment_image_alt', true);
                        if ($trending_story_alt_text == '') {
                            $trending_story_alt_text = trim(strip_tags(get_the_title()));
                        }
                        ?>
                        <div class="featured-img rounded" style="background-image: url(<?php echo $trending_story_src[0]; ?>);">
                            <div>
                                <?php if (0 && has_post_thumbnail($trending_story)) : ?>
                                    <img src="<?php echo $trending_story_src[0]; ?>" alt="<?php echo $trending_story_alt_text; ?>" title="<?php echo $trending_story_alt_text; ?>" class="rounded" loading="lazy">
                                <?php endif; // If thumbnail 
                                ?>
                            </div>
                        </div>
                        <div class="details-wrap">
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
                            </div>
                            <div class="title-wrap">
                                <h2 class="story-title">
                                    <?php echo get_the_title($trending_story); ?>
                                </h2>
                                <p class="mt-3">
                                    <?php
                                    $metadesc = get_post_meta($trending_story_ID, '_yoast_wpseo_metadesc', true);
                                    $excerpt = trim($metadesc) != '' ? $metadesc : string_limit_words(get_the_excerpt($trending_story_ID), 25);
                                    echo $excerpt;
                                    ?>
                                </p>
                            </div>
                            <div class="read-article-wrap">
                                <div class="d-flex justify-content-between">
                                    <div class="read-article d-flex">
                                        <span><img src="<?php echo get_template_directory_uri() . '/images/arrow-with-circle-right.svg'; ?>"></span>
                                        <span>Read Article</span>
                                    </div>
                                    <?php
                                    $author_byline = '';
                                    if (get_field('author', $trending_story_ID) || get_field('Author', $trending_story_ID)) :
                                        if (get_field('author', $trending_story_ID)) :
                                            $author_byline = get_field('author', $trending_story_ID);
                                        elseif (get_field('Author', $trending_story_ID)) :
                                            $author_byline = get_field('Author', $trending_story_ID);
                                        endif; // If custom author is set

                                        $author_img_src = wp_get_attachment_image_src(get_field('author_profile_picture', $trending_story), 'thumbnail');
                                    else : // If custom author has not been set
                                        $author_byline = get_the_author_meta('display_name', $trending_story->post_author);
                                    endif; // If custom author is set
                                    ?>
                                    <div class="align-items-center text-uppercase">
                                        <div class="d-flex">
                                            <div class="author-avatar mr-1"><?php echo get_avatar($trending_story->post_author, 24, 'blank', '', array('class' => 'rounded-circle')); ?></div>
                                            <?php echo $author_byline; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <?php wp_reset_query(); ?>
                        <!-- End Cover Story -->
                    </div><!-- /.story-hero -->
                </a>

        <?php endif; // If Trending Story
        endif; ?>
    </div>
    <div class="right trending-stories m-2 ml-0 col-12 col-md-auto">
        <div class="text-center p-1 pt-0 mb-0 mx-1 subheading border-bottom2 h-trending d-flex">
            <span><img src="<?php echo get_template_directory_uri() . '/images/line-graph.svg'; ?>"></span>
            <span>Trending</span>
        </div>
        <div class="pl-2">
            <?php
            global $wpdb;
            $trending_article_ids = $wpdb->get_results(
                "SELECT post_id FROM ( 
                    SELECT post_id FROM `{$wpdb->prefix}tbm_trending`
                    ORDER BY `created_at` DESC LIMIT 10
                    ) AS temptable
                    ORDER BY RAND()
                    LIMIT 2"
            );
            $trending_articles_args = array(
                'post_status' => 'publish',
                'post_type' => array('any'),
                'ignore_sticky_posts' => 1,
                'posts_per_page' => 2

            );
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

                    if ('' !== get_the_post_thumbnail()) :
                        $alt_text = get_post_meta(get_post_thumbnail_id(get_the_ID()), '_wp_attachment_image_alt', true);
                        if ($alt_text == '') {
                            $alt_text = trim(strip_tags(get_the_title()));
                        }
                        $img_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'thumbnail');
                    endif;
                ?>
                    <a href="<?php the_permalink(); ?>" class="story p-2 pb-0 mb-2">
                        <div class="d-flex flex-row justify-content-between">
                            <div class="pb-3">
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
                                <h3><?php the_title(); ?></h3>
                            </div>
                            <div class="rounded ml-1 post-thumbnail">
                                <?php if ($img_src && $img_src[0]) : ?>
                                    <img src="<?php echo $img_src[0]; ?>" alt="<?php echo $alt_text; ?>" title="<?php echo $alt_text; ?>" class="rounded" loading="lazy">
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
            <?php endwhile;
                wp_reset_postdata();
            endif; // If there are trending articles
            ?>
            <div class="ad-mrec">
                <div class="mx-auto text-center">
                    <?php render_ad_tag('vrec_1'); ?>
                </div>
            </div>
        </div>
    </div>
</section>