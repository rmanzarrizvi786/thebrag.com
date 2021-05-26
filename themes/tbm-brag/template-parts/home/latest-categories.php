<?php
$count = 1;
$vrec = 3;
$incontent = 2;
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
    if ($news_query->have_posts()) :
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
                        <div class="article-wrap col-12 col-md-4">
                            <?php get_template_part('template-parts/single/tile'); ?>
                        </div>
                    <?php
                    endwhile; ?>
                    <div class="article-wrap col-6 col-md-4">
                        <article class="my-3">
                            <div class="ad-mrec mt-5">
                                <div class="mx-auto text-center">
                                    <?php
                                    render_ad_tag('vrec_' . $vrec);
                                    $vrec++;
                                    ?>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
            <div class="d-flex">
                <a href="<?php echo get_category_link($category); ?>" class="btn btn-dark text-uppercase">More Stories</a>
            </div>
        </section>
        <div class="container mb-4">
            <div class="mx-auto text-center">
                <?php
                if ($count % 2 !== 0) {
                    render_ad_tag('incontent_' . $incontent);
                    $incontent++;
                }
                ?>
            </div>
        </div>
        <?php
        $count++;
        ?>
<?php
    endif;
endforeach;
