<?php
extract($args);
$story_ID = $story->ID;
?>
<a href="<?php the_permalink($story); ?>">
    <div class="story-hero text-white d-flex flex-column rounded justify-content-start" style="height: 100% !important">
        <div class="featured-img rounded">
            <div>
                <img src="<?php echo $story_src[0]; ?>" alt="<?php echo $story_alt_text; ?>" title="<?php echo $story_alt_text; ?>" class="rounded" loading="lazy" width="1200" height="625" style="width: 100%; height: auto;">
            </div>
        </div>
        <div class="details-wrap">
            <div class="title-wrap">
                <h2 class="story-title">
                    <?php echo get_the_title($story); ?>
                </h2>
                <p class="mt-2">
                    <?php
                    $metadesc = get_post_meta($story_ID, '_yoast_wpseo_metadesc', true);
                    $excerpt = trim($metadesc) != '' ? $metadesc : string_limit_words(get_the_excerpt($story_ID), 25);
                    echo $excerpt;
                    ?>
                </p>
            </div>
            <div class="read-article-wrap pt-2">
                <div class="d-flex justify-content-between">
                    <div class="read-article d-flex">
                        <span><img src="<?php echo ICONS_URL; ?>icon_arrow-right.svg" width="20" height="20" alt=">"></span>
                        <span>Read Article</span>
                    </div>
                    <?php
                    $author_byline = '';
                    if (get_field('author', $story_ID) || get_field('Author', $story_ID)) :
                        if (get_field('author', $story_ID)) :
                            $author_byline = get_field('author', $story_ID);
                        elseif (get_field('Author', $story_ID)) :
                            $author_byline = get_field('Author', $story_ID);
                        endif; // If custom author is set

                        $author_img_src = get_field('author_profile_picture', $story_ID) ? wp_get_attachment_image_src(get_field('author_profile_picture', $story_ID), 'thumbnail') : CDN_URL . 'default-avatar-4.png';
                    else : // If custom author has not been set
                        $author_byline = get_the_author_meta('display_name', $story->post_author);
                    endif; // If custom author is set
                    ?>
                    <div class="align-items-center text-uppercase">
                        <div class="d-flex">
                            <span class="author-avatar mr-1">
                                <?php
                                if (isset($author_img_src)) {
                                    if ($author_img_src) {
                                        echo  '<img src="' . $author_img_src . '" class="rounded" width="24" height="24">';
                                    }
                                } else {
                                    echo get_avatar($story->post_author, 24, CDN_URL . 'default-avatar-4.png', '', array('class' => 'rounded'));
                                }
                                ?>
                            </span>
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