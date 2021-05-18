<?php
extract($args);

$news_args = array(
    'post_status' => 'publish',
    'post_type' => array('post', 'snaps', 'dad'),
    'ignore_sticky_posts' => 1,
    'posts_per_page' => 8,
    'cat' => $cat_id,
    'post__not_in' => $exclude_posts,
    'paged' => $paged,
);
$news_query = new WP_Query($news_args);
$no_of_columns = 2;
if ($news_query->have_posts()) :
    $count = 1;
?>
    <section class="container latest pb-3">
        <div class="m-2">
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
                    $count++;
                endwhile; ?>
                <div class="article-wrap col-6 col-md-4">
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
            <div class="col-12 col-lg-6 offset-lg-3 page-nav">
                <div class="d-flex justify-content-center my-4">
                    <?php if ($paged > 1) : ?>
                        <div class="m-3"><?php previous_posts_link('Newer'); ?></div>
                    <?php endif; ?>
                    <div class="m-3">
                        <?php
                        if ($paged == 1) :
                            next_posts_link('MORE STORIES', '');
                        else :
                            next_posts_link('Older', '');
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif;
