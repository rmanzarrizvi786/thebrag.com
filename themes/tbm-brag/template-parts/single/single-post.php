<?php extract($args); ?>
<?php if ($count_articles === 1) : ?>
    <div class="ad-billboard container py-2 py-md-4">
        <div class="mx-auto text-center">
            <?php render_ad_tag('leaderboard', $count_articles); ?>
        </div>
    </div>
<?php endif; ?>

<?php
$the_post_id = get_the_ID();
$count_articles = isset($_POST['count_articles']) ? (int) $_POST['count_articles'] : 1;
if (!post_password_required($post)) :
    $author_byline = '';
    $author_image = '';
    $author_id = 0;

    if (get_field('author') || get_field('Author')) :
        if (get_field('author')) :
            $author_byline = $author_name = get_field('author');
        elseif (get_field('Author')) :
            $author_byline = $author_name = get_field('Author');
        endif; // If custom author is set

        $author_img_src = wp_get_attachment_image_src(get_field('author_profile_picture'), 'thumbnail');
        if ($author_img_src) :
            $author_image = '<img src="' . $author_img_src[0] . '" width="24" class="rounded-circle">';
        else :
            $author_image = '<img src="' . get_template_directory_uri() . '/images/default-avatar.png" width="24" class="rounded-circle">';
        endif; // If custom author image is set
    else : // If custom author has not bee set

        $author_byline = '<a href="' . get_author_posts_url($post->post_author) . '" class="text-dark">' . get_the_author_meta('display_name', $post->post_author) . '</a>';
        $author_image = get_avatar($post->post_author, 24, get_template_directory_uri() . '/images/default-avatar.png', '', array('class' => 'rounded-circle'));
    endif; // If custom author is set
?>

    <?php if ($count_articles > 1) : ?>
        <div class="ad-billboard ad-billboard-<?php echo $count_articles === 1 ? '1' : 'infinite'; ?> container py-2 py-md-4">
            <div class="mx-auto text-center">
                <?php render_ad_tag('leaderboard', $count_articles); ?>
            </div>
        </div>
    <?php endif; ?>
    <article class="single-article p-3 pb-1 single-article-<?php echo $count_articles === 1 ? '1' : 'infinite'; ?>" id="<?php the_ID(); ?>">
        <?php
        $title = get_post_meta($the_post_id, '_yoast_wpseo_title', true) ? get_post_meta($the_post_id, '_yoast_wpseo_title', true) : get_the_title();
        if (strpos($title, '%%title%%') !== FALSE) {
            $title = get_the_title();
        }

        $tags = get_the_tags($the_post_id);
        $TagsCD = '';
        if ($tags) :
            foreach ($tags as $tag) :
                $TagsCD .= $tag->slug . ' ';
            endforeach; // For Each Tag
        endif; // If there are tags for the post

        if ('dad' != get_post_type()) :
            $categories = get_the_category($the_post_id);
            $CategoryCD = '';
            if ($categories) :
                foreach ($categories as $category) :
                    $CategoryCD .= $category->slug . ' ';
                endforeach; // For Each Category
            endif; // If there are categories for the post
        ?>
            <div class="cats mb-3 text-center" data-category="<?php echo $CategoryCD; ?>" data-tags="<?php echo $TagsCD; ?>">
                <?php
                if (isset($categories)) :
                    foreach ($categories as $category) :
                        if ('Evergreen' == $category->cat_name) :
                            continue;
                        endif; // If category name is Evergreen
                ?>
                        <a class="text-uppercase cat mx-1" href="<?php echo get_category_link($category->term_id); ?>"><?php echo $category->cat_name; ?></a>
                <?php
                    endforeach; // For Each Category
                endif; // If there are categories for the post 
                ?>
            </div><!-- Cats -->
        <?php else : // Post type = Dad
            $categories = get_the_terms($the_post_id, 'dad-category');
            $CategoryCD = '';
            if ($categories) :
                foreach ($categories as $category) :
                    $CategoryCD .= $category->slug . ' ';
                endforeach; // For Each Category
            endif; // If there are categories for the post
        ?>
            <div class="cats mb-3" data-category="<?php echo $CategoryCD; ?>" data-tags="<?php echo $TagsCD; ?>">
                <?php
                if ($categories) :
                    foreach ($categories as $category) :
                        if ('Uncategorised' == $category->cat_name) :
                            continue;
                        endif; // If category name is Uncategorised
                ?>
                        <a class="text-uppercase cat" href="<?php echo get_term_link($category, 'dad-category'); ?>"><?php echo $category->cat_name; ?></a>
                <?php
                    endforeach; // For Each Category
                endif; // If there are categories for the post 
                ?>
            </div><!-- Cats -->
        <?php endif; // If Post Type != Dad 
        ?>

        <h1 id="story_title<?php echo $the_post_id; ?>" class="story-title mb-3" data-href="<?php the_permalink(); ?>" data-title="<?php echo htmlentities($title); ?>" data-share-title="<?php echo urlencode($title); ?>" data-share-url="<?php echo urlencode(get_permalink()); ?>" data-article-number="<?php echo $count_articles; ?>"><?php the_title(); ?></h1>

        <p class="text-center excerpt">
            <?php
            $metadesc = get_post_meta($the_post_id, '_yoast_wpseo_metadesc', true);
            $excerpt = trim($metadesc) != '' ? $metadesc : string_limit_words(get_the_excerpt($the_post_id), 25);
            echo $excerpt;
            ?>
        </p>

        <div class="post-meta d-block d-md-flex my-3 justify-content-around">
            <div class="d-flex mb-3 mb-md-0">
                <div class="author d-flex font-primary" data-author="<?php echo $author_name; ?>">
                    <div class="pr-1 text-uppercase d-flex">
                        <div class="mr-1"><?php echo $author_image; ?></div>
                        <div><?php echo $author_byline; ?></div>
                    </div>
                    <div class="v-divider">|</div>
                    <div class="pl-1">
                        <time datetime="<?php echo date('Y-m-d\TH:i:s+10:00', get_the_time('U')); ?>" data-pubdate="<?php echo get_the_time('M d, Y'); ?>"><?php echo get_the_time('d.m.Y'); ?></time>
                    </div>
                </div>
            </div>
            <!-- <div>
                        <?php
                        /* if (shortcode_exists('shout_writer_beer')) :
                            echo do_shortcode('[shout_writer_beer author="' . $author_name . '"]');
                        elseif (shortcode_exists('shout_writer_coffee')) :
                            echo do_shortcode('[shout_writer_coffee author="' . $author_name . '"]');
                        endif; // If shout writer shortcode exists */
                        ?>
                    </div> -->
        </div><!-- Author, Coffee and Share buttons -->

        <hr class="h-divider mb-3">
        <div class="d-flex align-items-start">
            <div class="col-md-8">
                <?php if (in_category('Op-Ed/Comment')) : ?>
                    <div style="padding: 10px 0; font-weight: bold;">COMMENT</div>
                <?php endif; // If the post has a category Op-Ed/Comment 
                ?>

                <?php if (in_category('Fiction')) : ?>
                    <div style="padding: 10px 0; font-weight: bold;">FICTION</div>
                <?php endif; // If the post has a category Fiction 
                ?>

                <?php if ('' !== get_the_post_thumbnail() && 'issue' != get_post_type()) : ?>
                    <div class="post-thumbnail mb-3">
                        <?php
                        the_post_thumbnail('full', array(
                            'class' => 'img-fluid rounded',
                        ));

                        if (get_field('image_credit')) :
                            echo '<p class="image-credit text-right">Image: ' . get_field('image_credit') . '</p>';
                        elseif ((get_field('Image Credit'))) :
                            echo '<p class="image-credit text-right">Image: ' . get_field('Image Credit', '') . '</p>';
                        endif; // If custom field - image credit - is set
                        ?>
                    </div><!-- .post-thumbnail -->
                <?php endif; // If post has thumbnail AND post type is not issue 
                ?>


                <?php
                // if (shortcode_exists('observer_subscribe_category')) :
                //     echo do_shortcode('[observer_subscribe_category id="' . $the_post_id . '"]');
                // endif;

                if (get_field('promoted_text') && '' != get_field('promoted_text')) :
                    if (get_field('promoted_text_link') && '' != get_field('promoted_text_link')) :
                ?>
                        <a href="<?php echo get_field('promoted_text_link'); ?>" target="_blank" class="text-dark">
                        <?php endif; // If promoted_text_link 
                        ?>
                        <div class="p-3 mb-3 d-flex align-items-center" style="border: 1px solid #b2b2b2; font-size: 110%">
                            <div><?php echo get_field('promoted_text'); ?></div>
                            <?php if (get_field('promoted_logo') && '' != get_field('promoted_logo')) : ?>
                                <img src="<?php echo get_field('promoted_logo'); ?>" style="width: 100px;">
                            <?php endif; // If promoted_logo 
                            ?>
                        </div>
                        <?php if (get_field('promoted_text_link') && '' != get_field('promoted_text_link')) : ?>
                        </a>
                <?php
                        endif; // If promoted_text_link
                    endif; // If promoted_text
                ?>

                <div class="post-content">
                    <?php
                    if ('issue' == get_post_type()) :
                        if (get_field('document_id')) :
                            $document_id = get_field('document_id');
                    ?>
                            <div style="width: 600px; margin: auto; max-width: 100%;">
                                <object classid="clsid:<?php echo $document_id; ?>" style="width: 600px; height:400px; max-width: 100%;" id="<?php echo $document_id; ?>">
                                    <param name="movie" value="https://static.issuu.com/webembed/viewers/style1/v2/IssuuReader.swf?mode=mini&amp;backgroundColor=&amp;documentId=<?php echo $document_id; ?>">
                                    <param name="allowfullscreen" value="true">
                                    <param name="menu" value="false">
                                    <param name="wmode" value="transparent">
                                    <embed src="https://static.issuu.com/webembed/viewers/style1/v2/IssuuReader.swf" type="application/x-shockwave-flash" allowfullscreen="true" menu="false" wmode="transparent" style="width: 600px; height:400px; max-width: 100%;" flashvars="mode=mini&amp;backgroundColor=&amp;documentId=<?php echo $document_id; ?>">
                                </object>
                            </div>
                        <?php endif; // If document_id is set for Post type Issue 
                        ?>

                        <?php if (get_field('issuu_link')) : ?>
                            <a href="<?php echo get_field('issuu_link'); ?>" target="_blank">PDF Download Link</a>
                    <?php endif; // If issuu_link is set for Post type Issue
                    endif; // If the post type is Issue

                    if (!get_field('paid_content', $the_post_id)) :
                        $content = apply_filters('the_content', $post->post_content);
                        echo $content;
                        $args = array(
                            'before'            => '<div class="page-links">',
                            'after'             => '</div>',
                            'link_before'       => '',
                            'link_after'        => '',
                            'next_or_number'    => 'next',
                            'separator'         => ' ',
                            'nextpagelink'      => 'Next &raquo',
                            'previouspagelink'  => '&laquo Previous',
                        );
                        wp_link_pages($args);
                    else :
                        $content = apply_filters('the_content', $post->post_content);
                        echo $content;
                    endif; // If it's a paid content
                    ?>

                    <?php if (in_category('Op-Ed/Comment')) : ?>
                        <div class="mt-5 py-3" style="border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
                            <div class="d-flex flex-column">
                                <div class="post-meta row">
                                    <div class="col-2 text-right"><?php echo $author_image; ?></div>
                                    <div class="author col-10 align-self-center">
                                        <div class="d-flex flex-row justify-content-between">
                                            <h5 data-author="<?php echo $author_name; ?>" class="align-self-center"><?php echo $author_byline; ?></h5>
                                            <?php if (get_field('author') || get_field('Author')) :
                                            else :
                                                $author = get_userdata(intval($post->post_author));
                                            ?>
                                                <ul class="nav align-self-center">
                                                    <?php if ($author->twitter != '') : ?>
                                                        <li class="nav-item"><a href="<?php echo $author->twitter; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-twitter-square fa-lg" aria-hidden="true"></i></a></li>
                                                    <?php endif; ?>
                                                    <?php if ($author->facebook != '') : ?>
                                                        <li class="nav-item"><a href="<?php echo $author->facebook; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-facebook-square fa-lg" aria-hidden="true"></i></a></li>
                                                    <?php endif; ?>
                                                    <?php if ($author->linkedin != '') : ?>
                                                        <li class="nav-item"><a href="<?php echo $author->linkedin; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-linkedin-square fa-lg" aria-hidden="true"></i></a></li>
                                                    <?php endif; ?>
                                                    <?php if ($author->instagram != '') : ?>
                                                        <li class="nav-item"><a href="<?php echo $author->instagram; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-instagram fa-lg" aria-hidden="true"></i></a></li>
                                                    <?php endif; ?>
                                                </ul>
                                            <?php
                                            endif;
                                            ?>
                                        </div>
                                        <div>
                                            <?php
                                            if (get_field('author') || get_field('Author')) :
                                                if ((get_field('author_bio'))) :
                                                    echo wpautop(get_field('author_bio'));
                                                endif;
                                            else : // If custom author has not bee set
                                                echo wpautop(get_the_author_meta('description', $post->post_author));
                                            endif;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- Author details, author socials -->
                    <?php endif; ?>

                </div><!-- /.post-content -->
                <?php
                if (get_field('impression_tag')) :
                    echo str_replace('[timestamp]', time(), get_field('impression_tag'));
                endif; // If custom field - impression tag - is set
                ?>
                <!-- Story End -->
            </div><!-- Left panel - content, etc. -->

            <div class="col-md-4 right-col-has-ad d-none d-md-block ml-2 align-self-stretch">
                <div class="d-flex flex-column h-100 justify-content-start">
                    <div class="align-self-center mb-3" style="min-width: 300px;">
                        <?php render_ad_tag('rail1', $count_articles); ?>
                    </div>
                    <div class="sticky-ad-right">
                        <div class="mt-3" style="min-width: 300px;">
                            <?php
                            render_ad_tag('rail2', $the_post_id . $count_articles);
                            ?>
                        </div>
                    </div>
                </div>
            </div><!-- Right Pane - for desktop/tablet devices -->
        </div><!-- Row 2 -->

        <div>
            <?php get_template_part('template-parts/single/spotlight'); ?>
        </div>
    </article><!-- .container .single_story -->
<?php elseif ($count_articles == 1) :
    echo '<style>.load-more{display:none;}</style>';
    echo get_the_password_form();
endif;
