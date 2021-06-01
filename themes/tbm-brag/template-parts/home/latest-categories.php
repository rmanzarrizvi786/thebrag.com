<?php
if (is_user_logged_in()) :
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $my_subs = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND status = 'subscribed' ");
    $my_sub_lists = wp_list_pluck($my_subs, 'list_id');
endif;

$count = 1;
$vrec = 3;
$incontent = 2;
$exclude_cats = [303097, 288366, 288238, 284732]; // Evergreen, Competitions, News, Features

$cats_home = array('food-drink', 'travel', 'comedy', 'culture');
if (isset($my_sub_lists) && !empty($my_sub_lists)) :
    $cats_home = get_categories(
        array(
            'parent' => null,
            // 'hide_empty' => '0',
            'meta_query' => array(
                array(
                    'key'     => 'observer-topic',
                    'exclude' => $exclude_cats,
                    'value'   => $my_sub_lists,
                    'compare' => 'IN',
                )
            )
        )
    );
else :
    $cats_home = get_categories(
        array(
            'parent' => null,
            // 'slug' => ['food-drink', 'travel', 'comedy', 'culture'],
            'exclude' => $exclude_cats,
            'orderby'    => 'count',
            'order' => 'DESC',
        )
    );
endif;

foreach ($cats_home as $i => $category) :
    // $category = get_category_by_slug($cat_home);
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
        if ($vrec > 7)
            $vrec = 3;
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
