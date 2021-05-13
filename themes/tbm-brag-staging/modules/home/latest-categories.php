<?php
$cats_home = array('food-drink', 'travel', 'comedy', 'culture');
foreach ($cats_home as $i => $cat_home) :
    $category = get_category_by_slug($cat_home);
    if (!$category)
        continue;
    $news_args = array(
        'post_status' => 'publish',
        'post_type' => array('post', 'snaps', 'dad'),
        'ignore_sticky_posts' => 1,
        // 'post__not_in' => $exclude_posts,
        'posts_per_page' => 5,
        'category__in' => $category->term_id,
    );
    $news_query = new WP_Query($news_args);
    $no_of_columns = 2;
    if ($news_query->have_posts()) :
        $count = 1;
?>
        <section class="container latest py-3">
            <div class="m-2">
                <h2 class="p-1 pt-0 mb-0 mx-1 h-latest border-bottom"><?php echo $category->name; ?></h2>
                <div class="d-flex flex-wrap align-items-start mt-2">
                    <?php
                    while ($news_query->have_posts()) :
                        $news_query->the_post();
                        $post_id = get_the_ID();
                    ?>
                        <div class="article-wrap col-3">
                            <article class="my-3">
                                <div class="mb-4 mx-3">
                                    <a href="<?php the_permalink(); ?>">
                                        <div class="post-thumbnail">
                                            <?php
                                            if ('' !== get_the_post_thumbnail()) :
                                                $alt_text = get_post_meta(get_post_thumbnail_id(get_the_ID()), '_wp_attachment_image_alt', true);
                                                if ($alt_text == '') {
                                                    $alt_text = trim(strip_tags(get_the_title()));
                                                }
                                                $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'thumbnail');
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
                                    <?php render_ad_tag('vrec_1'); ?>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <a href="<?php echo get_category_link($category); ?>" class="btn btn-dark text-uppercase">More Stories</a>
            </div>
        </section>
<?php endif;
endforeach;
