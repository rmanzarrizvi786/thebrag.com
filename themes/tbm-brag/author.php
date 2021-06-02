<?php
$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
get_header();
?>

<div class="ad-billboard ad-billboard-top container py-1 py-md-2">
    <div class="mx-auto text-center">
        <?php render_ad_tag('leaderboard'); ?>
    </div>
</div>

<div class="container bg-yellow pt-1 author rounded-top">
    <div>
        <div class="d-flex">
            <div>
                <div class="author-avatar"><?php echo get_avatar($curauth->ID, 96, 'blank', '', array('class' => 'rounded-circle')); ?></div>
            </div>
            <div class="ml-5">
                <h1><?php echo $curauth->display_name; ?></h1>
                <ul class="nav">
                    <?php if ($curauth->twitter != '') : ?>
                        <li class="nav-item"><a href="<?php echo $curauth->twitter; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-twitter-square fa-lg" aria-hidden="true"></i></a></li>
                    <?php endif; ?>
                    <?php if ($curauth->facebook != '') : ?>
                        <li class="nav-item"><a href="<?php echo $curauth->facebook; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-facebook-square fa-lg" aria-hidden="true"></i></a></li>
                    <?php endif; ?>
                    <?php if ($curauth->linkedin != '') : ?>
                        <li class="nav-item"><a href="<?php echo $curauth->linkedin; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-linkedin-square fa-lg" aria-hidden="true"></i></a></li>
                    <?php endif; ?>
                    <?php if ($curauth->instagram != '') : ?>
                        <li class="nav-item"><a href="<?php echo $curauth->instagram; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-instagram fa-lg" aria-hidden="true"></i></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="p-3">
            <p><?php echo nl2br($curauth->user_description); ?></p>
        </div>

    </div>
    <section class="container latest pb-3">
        <div class="m-2">
            <div class="d-flex flex-wrap align-items-start mt-2">
                <?php
                while (have_posts()) :
                    the_post();
                    $post_id = get_the_ID();

                    $category = '&nbsp;';

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
                endwhile; ?>
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
</div>



<?php get_footer();
