<?php
extract($args);
?>
<section class="trending container d-flex flex-column flex-md-row pb-2 align-items-start px-0 px-md-2" style="overflow: hidden;">
    <div class="left trending-main m-2 mr-md-0 align-self-stretch col-12 col-md-8">
        <?php
        if ($hero_stories[0]) :
            $story = array_shift($hero_stories);
            $exclude_posts[] = $story->ID;
            $story_image_id = get_post_thumbnail_id($story->ID);
            $story_src = wp_get_attachment_image_src($story_image_id, 'large');
            $story_alt_text = get_post_meta(get_post_thumbnail_id($story->ID), '_wp_attachment_image_alt', true);
            if ($story_alt_text == '') {
                $story_alt_text = trim(strip_tags(get_the_title()));
            }

            $featured_args = [
                'story' => $story,
                'story_src' => $story_src,
                'story_alt_text' => $story_alt_text,
                // 'exclude_posts' => $exclude_posts,
            ];

            if (get_field('image_has_text', $story_image_id)) {
                get_template_part('template-parts/category/featured', null, $featured_args);
            } else {
                get_template_part('template-parts/category/featured', 'overlay', $featured_args);
            }
        endif;
        ?>
    </div>
    <div class="right trending-stories m-2 ml-0 col-12 col-md-auto">
        <div class="pl-2">
            <?php
            foreach ($hero_stories as $story) :
            ?>
                <a href="<?php the_permalink($story->ID); ?>" class="story p-2 pb-0 mb-2">
                    <div class="d-flex flex-row justify-content-between align-items-start">
                        <div class="pb-3">
                            <h3><?php echo get_the_title($story->ID); ?></h3>
                        </div>
                        <div class="rounded ml-1 post-thumbnail mb-1">
                            <?php if ('' !== get_the_post_thumbnail($story->ID)) :
                                echo get_the_post_thumbnail($story->ID, 'thumbnail');
                            endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach;
            ?>
            <div class="ad-mrec" style="min-width: 300px;">
                <div class="mx-auto text-center">
                    <?php render_ad_tag('vrec_1');
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>