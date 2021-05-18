<section class="container spotlight">
    <div class="m-0 m-md-2">
        <h2 class="text-center text-uppercase p-1 pt-0 mb-0 mx-1 h-spotlight">Spotlight</h2>
        <div class="spotlight-stories d-flex flex-column flex-md-row align-items-start rounded bg-white mt-2">
            <?php
            global $wpdb;
            $spotlight_article_ids = $wpdb->get_results(
                "SELECT post_id FROM ( 
                    SELECT post_id FROM `{$wpdb->prefix}tbm_trending`
                    ORDER BY `created_at` DESC LIMIT 10
                    ) AS temptable
                    ORDER BY RAND()
                    LIMIT 3"
            );
            if ($spotlight_article_ids && count($spotlight_article_ids) > 0) :
                $spotlight_articles_args = array(
                    'post_status' => 'publish',
                    'post_type' => array('post', 'country'),
                    'ignore_sticky_posts' => 1,
                    'post__in' => wp_list_pluck($spotlight_article_ids, 'post_id'),
                );
                $spotlight_articles = new WP_Query($spotlight_articles_args);
                if ($spotlight_articles->have_posts()) :
            ?>
                    <?php
                    while ($spotlight_articles->have_posts()) :
                        $spotlight_articles->the_post();
                        $categories = get_the_category(get_the_ID());

                        if ('' !== get_the_post_thumbnail()) :
                            $alt_text = get_post_meta(get_post_thumbnail_id(get_the_ID()), '_wp_attachment_image_alt', true);
                            if ($alt_text == '') {
                                $alt_text = trim(strip_tags(get_the_title()));
                            }
                            $img_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'thumbnail');
                        endif;
                    ?>
                        <a href="<?php the_permalink(); ?>" class="story m-0 my-1 m-md-2 pb-0">
                            <div class="d-flex flex-row align-items-start">
                                <div class="img-wrap rounded mr-1">
                                    <?php if ($img_src && $img_src[0]) : ?>
                                        <img src="<?php echo $img_src[0]; ?>" alt="<?php echo $alt_text; ?>" title="<?php echo $alt_text; ?>" class="rounded" loading="lazy">
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="mb-1 mt-1 mt-md-0 text-uppercase spotlight-story-category">
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
                                    <h3 class="h6"><?php the_title(); ?></h3>
                                </div>

                            </div>
                        </a>
            <?php endwhile;
                    wp_reset_postdata();
                endif; // If there are spotlight articles
            endif;
            ?>
        </div>
    </div>
</section>