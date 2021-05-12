<?php /* Template name: Home Template */ ?>
<?php get_header(); ?>

<?php $exclude_posts = [];
$paged = (get_query_var('page')) ? get_query_var('page') : 1;

if ($paged > 1) :
    $news_args = array(
        'post_status' => 'publish',
        'post_type' => array('post', 'snaps', 'dad'),
        'ignore_sticky_posts' => 1,
        'post__not_in' => $exclude_posts,
        //                    'posts_per_page' => 6,
        'paged' => $paged
    );
    $show_cats = true;
    $news_query = new WP_Query($news_args);
    $no_of_columns = 3;
?>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row latest posts">
                    <?php
                    if ($news_query->have_posts()) :
                        $count = 1;
                        while ($news_query->have_posts()) :
                            $news_query->the_post();
                            $exclude_posts[] = get_the_ID();
                            include(get_template_directory() . '/partials/post-tile.php');
                            if ($count == 2) :
                                echo '<div class="col-lg-4 col-md-6 col-12 p-0 mb-2">';
                                get_fuse_tag('mrec_1');
                                echo '</div>';
                            endif;

                            if ($count == 8) :
                                echo '</div><div class="row"><div class="col-lg-8 posts"><div class="row">';
                            endif;

                            if ($count >= 8) :
                                $no_of_columns = 2;
                            endif;

                            $count++;
                        endwhile;
                        wp_reset_postdata();
                        echo '</div></div>';
                        echo '<div class="col-lg-4 col-md-6 col-12 p-0 mb-2">';
                        get_fuse_tag('mrec_2');
                        echo '</div>';
                    endif;
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-6 offset-lg-3 page-nav">
                <div class="d-flex justify-content-center my-4">
                    <div class="m-3"><?php if ($paged > 2) previous_posts_link('Newer', $news_query->max_num_pages); ?></div>
                    <div class="m-3 px-5 text-center font-weight-bold align-self-center">
                        <div class="">Page <?php echo $paged; ?> / <?php echo $news_query->max_num_pages; ?></div>
                    </div>
                    <div class="m-3"><?php next_posts_link('Older', $news_query->max_num_pages); ?></div>
                </div>
            </div>
        </div>
    </div>
<?php
/*
 * If Home Page i.e. page query is not set
 */
else :
?>

    <?php
    $cover_story_args = array(
        'post_status' => 'publish',
        //                'feature' => 'cover-story',
        'post__not_in' => $exclude_posts,
        'posts_per_page' => 1,
        'p' => get_option('most_viewed_yesterday'),
    );
    $cover_story_query = new WP_Query($cover_story_args);
    if ($cover_story_query->have_posts()) :
        while ($cover_story_query->have_posts()) :
            $cover_story_query->the_post();
            $cover_story_ID = get_the_ID();
            $exclude_posts[] = $cover_story_ID;
        endwhile;
        wp_reset_query();
    endif;

    if (!is_null($cover_story_ID) && $cover_story_ID != '') :
    ?>
        <div class="container py-md-4 text-white" style="background: black; position: relative;">
            <div id="section-hero" class="row">
                <div class="col-md-8 pt-3 pt-md-0">
                    <!-- Cover Story (home page) -->
                    <?php

                    $cover_story = get_post($cover_story_ID);
                    if ($cover_story) :
                        $cover_story_src = wp_get_attachment_image_src(get_post_thumbnail_id($cover_story->ID), 'large');
                        $cover_story_alt_text = get_post_meta(get_post_thumbnail_id($cover_story->ID), '_wp_attachment_image_alt', true);
                        if ($cover_story_alt_text == '') {
                            $cover_story_alt_text = trim(strip_tags(get_the_title()));
                        }
                    ?>

                        <div>
                            <?php if (has_post_thumbnail($cover_story)) : ?>
                                <a href="<?php the_permalink($cover_story); ?>">
                                    <h2 class="d-inline-block text-left p-2" style="position: absolute; top: 0; z-index: 2; transform: rotate(-15deg); background: rgba(0,0,0,1); color: #fff; left: 0.5rem; font-weight: normal; font-size: 1.5rem; box-shadow: 0px 0px 10px #888;">TRENDING</h2>
                                    <img data-src="<?php echo $cover_story_src[0]; ?>" alt="<?php echo $cover_story_alt_text; ?>" title="<?php echo $cover_story_alt_text; ?>" class="lazyload img-fluid">
                                </a>
                            <?php endif; // If thumbnail 
                            ?>
                        </div>
                </div>
                <div class="col-md-4 mt-3">
                    <h2>
                        <a href="<?php the_permalink($cover_story); ?>">
                            <?php echo get_the_title($cover_story); ?>
                        </a>
                    </h2>
                    <p class="pb-5"><?php
                                    $metadesc = get_post_meta($cover_story_ID, '_yoast_wpseo_metadesc', true);
                                    $excerpt = trim($metadesc) != '' ? $metadesc : string_limit_words(get_the_excerpt($cover_story_ID), 25);
                                    echo $excerpt;
                                    ?></p>
                    <?php
                        $author_byline = '';
                        if (get_field('author', $cover_story_ID) || get_field('Author', $cover_story_ID)) :
                            if (get_field('author', $cover_story_ID)) :
                                $author_byline = get_field('author', $cover_story_ID);
                            elseif (get_field('Author', $cover_story_ID)) :
                                $author_byline = get_field('Author', $cover_story_ID);
                            endif; // If custom author is set

                            $author_img_src = wp_get_attachment_image_src(get_field('author_profile_picture', $cover_story), 'thumbnail');
                        else : // If custom author has not been set
                            $author_byline = '<a href="' . get_author_posts_url($cover_story->post_author) . '">' . get_the_author_meta('display_name', $cover_story->post_author) . '</a>';
                        endif; // If custom author is set
                    ?>
                    <div class="align-items-center text-uppercase" style="position: absolute; bottom: 0;">
                        <div class="small">
                            <?php echo $author_byline; ?>
                        </div>
                    </div>
                </div>

                <?php wp_reset_query(); ?>
                <!-- End Cover Story -->
            </div><!-- /#section-hero.row -->
        </div><!-- /.container -->
<?php
                    endif; // If Cover Story
                endif; // If Cover Story ID
?>

<?php
    /*
* Observer {{
*/
    $my_sub_lists = [];
    $my_vote_lists = [];
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $my_subs = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$current_user->ID}' AND status = 'subscribed' ");
        $my_sub_lists = wp_list_pluck($my_subs, 'list_id');

        $my_votes = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_votes WHERE user_id = '{$current_user->ID}'");
        $my_vote_lists = wp_list_pluck($my_votes, 'list_id');
    }
    $lists_query = "
    SELECT
        l.id, l.title, l.slug, l.description, l.image_url, l.frequency, sub_count
    FROM {$wpdb->prefix}observer_lists l
    WHERE
        l.status = 'active'
    ORDER BY
        l.sub_count DESC
    ";
    $lists = $wpdb->get_results($lists_query);
    if ($lists) :
        $list_count = 0;
?>
    <div class="container my-4 text-white px-0">
        <h2 class="text-center"><a href="https://thebrag.com/observer/" target="_blank" class="text-dark">The Ultimate Newsletter Network – Choose Your Niche</a></h2>

        <div id="observer-carousel">
            <?php foreach ($lists as $list) : ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 px-2 <?php echo $list_count == 0 ? 'active' : ''; ?>">
                    <?php if ($list->slug) : ?>
                        <div class="observer-wrap">
                            <a href="<?php echo home_url('/observer/' . $list->slug . '/'); ?>" target="_blank" class="text-center text-dark d-flex flex-column h-100">
                            <?php endif; ?>
                            <img data-src="<?php echo $list->image_url; ?>" class="img-fluid mx-auto d-block lazyload" alt="<?php echo $list->title; ?>">
                            <h6 class="mt-2 flex-fill d-flex justify-content-center align-items-center px-md-4 px-2"><?php echo !in_array($list->id, [4,]) ? trim(str_ireplace('Observer', '', $list->title)) : trim($list->title); ?></h6>
                            <?php if ($list->slug) : ?>
                            </a>

                            <div class="list-subscription-action">
                                <?php if (in_array($list->id, $my_sub_lists)) :
                                    $share_url = $list->slug ? home_url('/observer/' . $list->slug . '/') : home_url('/observer/');
                                ?>
                                    <div class="d-flex flex-row justify-content-center align-items-center share-icons">
                                        <a class="btn btn-default btn-outline-primary btn-share mx-1" href="http://www.facebook.com/share.php?u=<?php echo $share_url; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>

                                        <a class="btn btn-default btn-outline-info btn-share mx-1" href="https://twitter.com/share?url=<?php echo $share_url; ?>&amp;text=<?php echo urlencode($list->title); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                                    </div>
                                <?php else : ?>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="flex-fill observer-action-wrap">

                                            <button type="button" class="btn btn-dark rounded btn-block btn-subscribe-observer<?php echo is_user_logged_in() ? '-l' : ''; ?> d-flex justify-content-between py-2" data-target="#subscribeobserverModal" data-topic="<?php echo $list->title; ?>" data-list="<?php echo $list->id; ?>" data-desc="<?php echo $list->description; ?>" data-apple="<?php $apple_signin_state = base64_encode(serialize(['list_id' => $list->id, 'code' => md5(time() . 'tbm')]));
                                                                                                                                                                                                                                                                                                                                                                                                echo $apple_signin_state; ?>">
                                                <div><i class="fa fa-envelope mr-2 d-none d-xl-inline"></i> <span class="btn-text">Subscribe</span></div>
                                            </button>
                                            <div class="loading" style="display: none;">
                                                <div class="spinner">
                                                    <div class="double-bounce1"></div>
                                                    <div class="double-bounce2"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($list->slug) : ?>
                                            <a href="<?php echo home_url('/observer/' . $list->slug . '/'); ?>" target="_blank" class="btn text-dark px-2 l-list-info">
                                                <span class="ico"><i class="fas fa-info-circle"></i></span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php $list_count++;
            endforeach; // For Each $list  
            ?>
        </div>

        <?php if (0) : ?>
            <div id="carousel-observer" class="carousel slide d-none" data-ride="carousel" data-interval="false">
                <div class="carousel-inner row mx-auto py-3" role="listbox">
                    <?php foreach ($lists as $list) : ?>
                        <div class="carousel-item col-12 col-sm-6 col-md-4 col-lg-3 px-2 <?php echo $list_count == 0 ? 'active' : ''; ?>">
                            <?php if ($list->slug) : ?>
                                <a href="<?php echo home_url('/observer/' . $list->slug . '/'); ?>" target="_blank" class="text-center text-dark d-flex flex-column h-100">
                                <?php endif; ?>
                                <img data-src="<?php echo $list->image_url; ?>" class="img-fluid mx-auto d-block lazyload" alt="<?php echo $list->title; ?>">
                                <h5 class="mt-2 flex-fill d-flex justify-content-center align-items-center px-2"><?php echo $list->title; ?></h5>
                                <?php if ($list->slug) : ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php $list_count++;
                    endforeach; // For Each $list  
                    ?>
                </div>
                <a class="carousel-control-prev" href="#carousel-observer" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carousel-observer" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
<?php
    endif; // If $lists
    /*
* }} Observer
*/
?>

<div class="container">
    <div class="row latest posts">
        <h2 class="col-12 text-left pt-2 pb-2">Latest</h2>
        <?php
        $news_args = array(
            'post_status' => 'publish',
            'post_type' => array('post', 'snaps', 'dad'),
            'ignore_sticky_posts' => 1,
            'post__not_in' => $exclude_posts,
            'posts_per_page' => 5
        );
        $news_query = new WP_Query($news_args);
        $no_of_columns = 3;
        $show_cats = true;
        if ($news_query->have_posts()) :
            $count = 1;
            while ($news_query->have_posts()) :
                $news_query->the_post();
                $exclude_posts[] = get_the_ID();

                include(get_template_directory() . '/partials/post-tile.php');

                if ($count == 2) :
                    echo '<div class="col-lg-4 col-md-6 col-12 p-0 mb-5">';
                    render_ad_tag('mrec_1');
                    echo '</div>';
                endif;

                $count++;
            endwhile;
        endif;
        $show_cats = false;
        ?>
    </div>
</div>

<!-- <div class="container">
    <div class="row">
        <div class="col-12">
            <?php //echo render_ad_tag('content_2'); ?>
        </div>
    </div>
</div> -->

<div class="container mt-4">
    <div class="row latest">
        <div class="col-12">
            <div style="border-top: 2px solid #333;"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-12 pr-md-0" id="video-of-week">
            <h2 class="mt-4">Video of the week</h2>
            <?php
            /*
* Featured Video (YouTube)
*/
            $featured_yt_vid_id = NULL;
            $featured_video = get_option('tbm_featured_video');
            $tbm_featured_video_link = get_option('tbm_featured_video_link');

            if (!is_null($featured_video) && $featured_video != '') :
                parse_str(parse_url($featured_video, PHP_URL_QUERY), $featured_video_vars);
                $featured_yt_vid_id = isset($featured_video_vars['v']) ? $featured_video_vars['v'] : NULL;
                $featured_video_img = !is_null($featured_yt_vid_id) ? 'https://i.ytimg.com/vi/' . $featured_yt_vid_id . '/0.jpg' : NULL;
                if ($tbm_featured_video_link) :
                    $tbm_featured_video_link_html = file_get_contents($tbm_featured_video_link);
                    $tbm_featured_video_link_html_dom = new DOMDocument();
                    @$tbm_featured_video_link_html_dom->loadHTML($tbm_featured_video_link_html);
                    // $meta_og_img_tbm_featured_video_link = null;
                    foreach ($tbm_featured_video_link_html_dom->getElementsByTagName('meta') as $meta) {
                        if ($meta->getAttribute('property') == 'og:image') {
                            $featured_video_img = $meta->getAttribute('content');
                            break;
                        }
                    }
            ?>
                    <a style="position: relative; width:100%; cursor:pointer; overflow:hidden; display: block;" class="mb-3 home-featured-content" href="<?php echo $tbm_featured_video_link; ?>" target="_blank">
                    <?php else : // $tbm_featured_video_link is not set, so display video 
                    ?>
                        <div style="position: relative; width:100%; cursor:pointer; overflow:hidden;" title="Click to play video" class="mb-3 yt-lazy-load home-featured-content" data-id="<?php echo $featured_yt_vid_id; ?>" id="<?php echo $featured_yt_vid_id; ?>">
                        <?php endif; // If $tbm_featured_video_link 
                        ?>
                        <div id="featured-video-player" class="youtube-player" data-id="<?php echo $featured_yt_vid_id; ?>" style="height: 140px">
                            <img data-src="<?php echo $featured_video_img; ?>" style="position: absolute; width: 100%; z-index: 1;top:50%;left:50%;transform:translate(-50%, -50%)" class="lazyload video-thumb" alt="<?php esc_html(get_option('tb_featured_video_artist_title')) . ' - ' . esc_html(get_option('tb_featured_video_song_title')); ?>" title="<?php esc_html(get_option('tb_featured_video_artist_title')) . ' - ' . esc_html(get_option('tb_featured_video_song_title')); ?>">
                            <img class="lazyload play-button-red" data-src="<?php echo get_template_directory_uri(); ?>/images/play-button-60px.png" style="width: 40px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 2;transition: .25s all linear;" alt="Play" title="Play">
                        </div>
                    <?php endif; // If Featured Video is set 
                    ?>
                    <div class="featured-video-meta d-flex justify-content-between align-items-center">
                        <h3>
                            <?php
                            //add artist title and songs title
                            if (get_option('tbm_featured_video_artist')) {
                                echo '' . esc_html(stripslashes(get_option('tbm_featured_video_artist')));
                            }
                            if (get_option('tbm_featured_video_song')) {
                                echo ' - \'' . esc_html(stripslashes(get_option('tbm_featured_video_song'))) . '\'';
                            }
                            ?>
                        </h3>
                    </div>
                    <?php if ($tbm_featured_video_link) : ?>
                    </a>
                <?php else : // $tbm_featured_video_link is not set, so display video 
                ?>
        </div>
    <?php endif; // If $tbm_featured_video_link 
    ?>
    </div><!-- Featured Video -->
    <div class="mb-3 col-md-4 col-12" id="record-of-week" style="overflow: hidden">
        <h2 class="mt-4">Record of the week</h2>
        <a href="<?php echo get_option('tbm_featured_album_link'); ?>" target="_blank" class="d-block home-featured-content" style="display: block; position: relative;">
            <img src="<?php echo get_option('tbm_featured_album_image_url'); ?>" style="height: 100%; width: auto; max-width: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <h3 class="title">
                <?php echo esc_html(stripslashes(get_option('tbm_featured_album_artist'))); ?>
                -
                <em><?php echo esc_html(stripslashes(get_option('tbm_featured_album_title'))); ?></em>
            </h3>
        </a>
    </div>
</div>
</div>

<?php
    $ad_slot_content = 3;
    $cats_home = array('food-drink', 'travel', 'comedy', 'culture');
    foreach ($cats_home as $i => $cat_home) :
        $category = get_category_by_slug($cat_home);
        if (!$category)
            continue;
?>
    <div class="container mt-4">
        <div class="row latest">
            <div class="col-12">
                <div style="border-top: 2px solid #333;"></div>
            </div>
            <h2 class="col-12 text-left pt-4 pb-2"><?php echo $category->name; ?></h2>
        </div>
        <div class="row latest">
            <div class="col-lg-8">
                <div class="row posts">
                    <?php
                    $news_args = array(
                        'post_status' => 'publish',
                        'post_type' => array('post', 'snaps', 'dad'),
                        'ignore_sticky_posts' => 1,
                        'post__not_in' => $exclude_posts,
                        'posts_per_page' => 6,
                        'category__in' => $category->term_id,
                    );
                    $news_query = new WP_Query($news_args);
                    $no_of_columns = 2;
                    if ($news_query->have_posts()) :
                        $count = 1;
                        while ($news_query->have_posts()) :
                            $news_query->the_post();
                            $exclude_posts[] = get_the_ID();
                            include(get_template_directory() . '/partials/post-tile.php');
                            $count++;
                        endwhile;
                    endif;
                    ?>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-12 mb-5">
                <div class="sticky-rail">
                    <?php render_ad_tag($i == 0 ? 'rail2' : 'railX', 'homepage', $i); ?>
                </div>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-12 text-center">
                <a href="<?php echo get_category_link($category); ?>" class="btn btn-dark l-more-stories text-uppercase">More Stories</a>
            </div>
        </div>

<!--         <div class="row">
            <div class="col-12">
                <?php //echo render_ad_tag('content_' . $ad_slot_content); ?>
            </div>
        </div> -->
    </div>
<?php
        $ad_slot_content++;
    endforeach; // For Each Cat Home 
?>


<div class="container mt-4">
    <div class="row latest">
        <div class="col-12 pb-3">
            <div style="border-top: 2px solid #333;"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row latest posts">
                <h2 class="col-12 text-left pt-2 pb-2">More Stories</h2>
                <?php
                $show_cats = true;
                $news_args = array(
                    'post_status' => 'publish',
                    'post_type' => array('post', 'snaps', 'dad'),
                    'ignore_sticky_posts' => 1,
                    'post__not_in' => $exclude_posts,
                    'posts_per_page' => 10,
                    'paged' => $paged
                );
                $news_query = new WP_Query($news_args);
                $no_of_columns = 3;
                if ($news_query->have_posts()) :
                    $count = 1;
                    while ($news_query->have_posts()) :
                        $news_query->the_post();
                        $exclude_posts[] = get_the_ID();
                        include(get_template_directory() . '/partials/post-tile.php');

                        if ($count == 2) :
                            echo '<div class="col-lg-4 col-md-6 col-12 p-0 mb-5">';
                            get_fuse_tag('railX', 'homepage', $count . $count);
                            echo '</div>';
                        endif;

                        if ($count == 10) :
                            echo '<div class="col-lg-4 col-md-6 col-12 p-0 mb-5">';
                            get_fuse_tag('railX', 'homepage', $count . $count . $count);
                            get_fuse_tag('side_3');
                            echo '</div>';
                        endif;

                        $count++;
                    endwhile;

                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12 text-center">
            <a href="<?php echo home_url('/page/2'); ?>" class="btn btn-dark l-more-stories text-uppercase">More Stories</a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php get_footer();
