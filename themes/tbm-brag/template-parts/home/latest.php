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
    <section class="container latest pb-3">
        <div class="m-2">
            <h2 class="p-1 pt-0 mb-0 mx-0 mx-md-1 h-latest border-bottom">Latest</h2>
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
            <a href="<?php echo home_url('/page/2'); ?>" class="btn btn-dark text-uppercase">More Stories</a>
        </div>
    </section>
<?php endif;
