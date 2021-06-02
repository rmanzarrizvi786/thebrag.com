<?php
extract($args);
$trending_story_ID = $trending_story->ID;
?>
<a href="<?php the_permalink($trending_story); ?>">
    <div class="story-hero story-hero-overlay text-white rounded">
        <div class="featured-img-overlay rounded" style="background-image: url(<?php echo $trending_story_src[0]; ?>);">
            <div>
                <img src="<?php echo $trending_story_src[0]; ?>" alt="<?php echo $trending_story_alt_text; ?>" title="<?php echo $trending_story_alt_text; ?>" class="rounded" loading="lazy">
            </div>
        </div>
        <div class="details-wrap d-flex flex-column">
            <div class="mb-1 text-uppercase trending-story-category align-self-start">
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
            <div class="title-wrap flex-fill">
                <h2 class="story-title">
                    <?php echo get_the_title($trending_story); ?>
                </h2>

            </div>
            <div class="mt-3">
                <p>
                    <?php
                    $metadesc = get_post_meta($trending_story_ID, '_yoast_wpseo_metadesc', true);
                    $excerpt = trim($metadesc) != '' ? $metadesc : string_limit_words(get_the_excerpt($trending_story_ID), 25);
                    echo $excerpt;
                    ?>
                </p>
            </div>
            <div class="read-article-wrap pt-2 w-100">
                <div class="d-flex justify-content-between">
                    <div class="read-article d-flex">
                        <span><img src="<?php echo ICONS_URL; ?>arrow-with-circle-right.svg" width="20" height="20" alt=">"></span>
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

                        $author_img_src = get_field('author_profile_picture', $storytrending_story->ID_ID) ? wp_get_attachment_image_src(get_field('author_profile_picture', $trending_story->ID), 'thumbnail') : ICONS_URL . 'default-avatar-v4.png';
                    else : // If custom author has not been set
                        $author_byline = get_the_author_meta('display_name', $trending_story->post_author);
                    endif; // If custom author is set
                    ?>
                    <div class="align-items-center text-uppercase">
                        <div class="d-flex">
                            <div class="author-avatar mr-1">
                                <?php
                                if (isset($author_img_src)) {
                                    if ($author_img_src) {
                                        echo  '<img src="' . $author_img_src . '" class="rounded" width="24" height="24">';
                                    }
                                } else {
                                    echo get_avatar($trending_story->post_author, 24, ICONS_URL . 'default-avatar-v4.png', '', array('class' => 'rounded'));
                                }
                                ?>
                            </div>
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