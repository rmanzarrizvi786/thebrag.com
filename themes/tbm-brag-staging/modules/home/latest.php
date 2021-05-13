<?php
$news_args = array(
    'post_status' => 'publish',
    'post_type' => array('post', 'snaps', 'dad'),
    'ignore_sticky_posts' => 1,
    // 'post__not_in' => $exclude_posts,
    'posts_per_page' => 5,
);
$news_query = new WP_Query($news_args);
$no_of_columns = 2;
if ($news_query->have_posts()) :
    $count = 1;
?>
    <section class="container latest">
        <div class="m-2">
            <h2 class="p-1 pt-0 mb-0 mx-1 h-latest border-bottom">Latest</h2>
            <div class="d-flex flex-wrap align-items-start py-2">
                <?php
                while ($news_query->have_posts()) :
                    $news_query->the_post();
                    $post_id = get_the_ID();
                ?>
                    <div class="article-wrap col-3">
                        <article class="my-3">
                            <div class="mb-4 mx-2">
                                <a href="<?php the_permalink(); ?>">
                                    <div class="mb-2 text-uppercase cat">
                                        <?php if ('snaps' == $post->post_type) : ?>
                                            GALLERY
                                        <?php elseif ('dad' == $post->post_type) : ?>
                                            <?php $categories = get_the_terms(get_the_ID(), 'dad-category');
                                            if ($categories) :
                                                if ($categories[0] && 'Uncategorised' != $categories[0]->name) : ?>
                                                    <?php echo $categories[0]->name; ?>
                                                <?php elseif (isset($categories[1])) : ?>
                                                    <?php echo $categories[1]->name; ?>
                                                <?php else : ?>
                                                    <br>
                                                <?php endif; // If Uncategorised 
                                                ?>
                                            <?php endif; // If there are Dad categories 
                                            ?>
                                        <?php else : ?>
                                            <?php $categories = get_the_category();
                                            if ($categories) :
                                                if (isset($categories[0]) && 'Evergreen' != $categories[0]->cat_name) : ?>
                                                    <?php if (0 == $categories[0]->parent) : ?>
                                                        <?php echo $categories[0]->cat_name; ?>
                                                    <?php else : $parent_category = get_category($categories[0]->parent); ?>
                                                        <?php echo $parent_category->cat_name; ?>
                                                    <?php endif; ?>
                                                <?php elseif (isset($categories[1])) : ?>
                                                    <?php if (0 == $categories[1]->parent) : ?>
                                                        <?php echo $categories[1]->cat_name; ?>
                                                    <?php else : $parent_category = get_category($categories[1]->parent); ?>
                                                        <?php echo $parent_category->cat_name; ?>
                                                    <?php endif; ?>
                                                <?php endif; // If Evergreen 
                                                ?>
                                            <?php endif; // If there are Dad categories 
                                            ?>
                                        <?php endif; // If Photo Gallery 
                                        ?>
                                    </div>
                                    <div class="post-thumbnail">
                                        <?php

                                        if ('' !== get_the_post_thumbnail()) :
                                            $alt_text = get_post_meta(get_post_thumbnail_id(get_the_ID()), '_wp_attachment_image_alt', true);
                                            if ($alt_text == '') {
                                                $alt_text = trim(strip_tags(get_the_title()));
                                            }
                                            $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'medium_large');
                                        ?>
                                            <img src="<?php echo $img_src[0]; ?>" alt="<?php echo $alt_text; ?>" title="<?php echo $alt_text; ?>" loading="lazy">
                                        <?php endif; ?>
                                    </div>
                                    <div class="post-content align-self-start">
                                        <h3 class="my-2"><?php the_title(); ?></h3>

                                        <p class="excerpt">
                                            <?php
                                            $author_name =
                                                get_field('photographer') ? get_field('photographer') : (get_field('author') ? get_field('author') : (get_field('Author') ? get_field('Author') : get_the_author_meta('first_name', $post->post_author) . ' ' . get_the_author_meta('last_name', $post->post_author)));
                                            if ('snaps' == $post->post_type) :
                                                echo 'Relive all the highlights (or check out what you missed) with our full photo gallery by ' . $author_name . '.';
                                            else :
                                                $metadesc = get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);
                                                $excerpt = trim($metadesc) != '' ? $metadesc : string_limit_words(get_the_excerpt(), 25);
                                                echo $excerpt;
                                            endif;
                                            ?>
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </article>
                    </div>
                <?php
                    $count++;
                endwhile; ?>
                <div class="article-wrap col-3">
                    <article class="my-3">
                        <div class="ad-mrec mt-5">
                            <div class="mx-auto text-center">
                                <?php render_ad_tag('vrec_1');
                                ?>
                                <a href="https://thebrag.media?300x250" target="_blank"><img src="http://placehold.it/300x250/663366/fff?text=300x250"></a>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
<?php endif;
