<?php
extract($args);
$posts_per_page = 1 === $paged ? 5 : 11;
$news_args = array(
    'post_status' => 'publish',
    'post_type' => array('post', 'snaps', 'dad'),
    'ignore_sticky_posts' => 1,
    // 'post__not_in' => $exclude_posts,
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'meta_query' => array(
        array(
            'key' => 'not_brand_safe',
            'value' => 0,
            'compare' => 'LIKE',
        )
    )
);
$news_query = new WP_Query($news_args);
$no_of_columns = 2;
if ($news_query->have_posts()) :
    $count = 1;
?>
    <section class="container latest pb-3<?php echo $paged !== 1 ? ' rounded-top' : ''; ?>">
        <div class="m-2">
            <?php if (1 === $paged) { ?>
                <h2 class="p-1 pt-0 mb-0 mx-0 mx-md-1 h-latest border-bottom">Latest</h2>
            <?php } ?>
            <div class="d-flex flex-wrap align-items-start mt-2">
                <?php
                while ($news_query->have_posts()) :
                    $news_query->the_post();
                    $post_id = get_the_ID();

                    $category = '';

                    if ('snaps' == $post->post_type) :
                        $category = 'GALLERY';
                    elseif ('dad' == $post->post_type) :
                        $categories = get_the_terms(get_the_ID(), 'dad-category');
                        if ($categories) :
                            if ($categories[0] && 'Uncategorised' != $categories[0]->name) :
                                $category = $categories[0]->name;
                            elseif (isset($categories[1])) :
                                $category = $categories[1]->name;
                            else :
                            endif; // If Uncategorised 
                        endif; // If there are Dad categories 
                    else :
                        $categories = get_the_category();
                        if ($categories) :
                            if (isset($categories[0]) && 'Evergreen' != $categories[0]->cat_name) :
                                if (0 == $categories[0]->parent) :
                                    $category = $categories[0]->cat_name;
                                else : $parent_category = get_category($categories[0]->parent);
                                    $category = $parent_category->cat_name;
                                endif;
                            elseif (isset($categories[1])) :
                                if (0 == $categories[1]->parent) :
                                    $category = $categories[1]->cat_name;
                                else : $parent_category = get_category($categories[1]->parent);
                                    $category = $parent_category->cat_name;
                                endif;
                            endif; // If Evergreen 
                        endif; // If there are Dad categories 
                    endif; // If Photo Gallery 
                ?>
                    <div class="col-12 col-md-4">
                        <?php get_template_part('template-parts/single/tile', null, ['category' => $category]); ?>
                    </div>
                <?php
                    $count++;
                endwhile; ?>
                <div class="col-6 col-md-4">
                    <article class="my-3">
                        <div class="ad-mrec mt-5">
                            <div class="mx-auto text-center">
                                <?php render_ad_tag('vrec_2'); ?>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
        <div class="d-flex">
            <?php if (1 === $paged) { ?>
                <a href="<?php echo home_url('/page/2'); ?>" class="btn btn-dark text-uppercase">More Stories</a>
            <?php } else { ?>
                <div class="col-12 col-lg-6 offset-lg-3 page-nav">
                    <div class="d-flex justify-content-center my-4">
                        <div class="m-3"><?php previous_posts_link('Newer', $news_query->max_num_pages); ?></div>
                        <div class="m-3"><?php next_posts_link('Older', $news_query->max_num_pages); ?> </div>
                    </div>
                <?php } ?>
                </div>
    </section>
<?php endif;
