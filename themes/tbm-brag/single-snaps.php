<?php get_header(); ?>

<?php
wp_enqueue_script('pswipe', get_template_directory_uri() . '/ps/photoswipe.min.js', array(), NULL, true);
wp_enqueue_script('pswipe-d', get_template_directory_uri() . '/ps/photoswipe-ui-default.min.js', array(), NULL, true);
wp_enqueue_style('pswipe-css', get_template_directory_uri() . '/ps/photoswipe.css');
wp_enqueue_style('pswipe-d-css', get_template_directory_uri() . '/ps/default-skin/default-skin.css');
wp_enqueue_script('gallery', get_template_directory_uri() . '/js/gallery.js', array('jquery'), '1', true);

$the_post_id = get_the_ID();
?>

<div class="ad-billboard container py-2 py-md-4">
    <div class="mx-auto text-center">
        <?php render_ad_tag('leaderboard'); ?>
    </div>
</div>

<div id="articles-wrap-gallery" class="container">
    <?php
    $photos = new Attachments('snaps_attachments');
    if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article class="single-article single-article-1 p-3 pb-1" id="<?php the_ID(); ?>">
                <h1 id="story_title<?php echo $the_post_id; ?>" class="story-title mb-3" data-href="<?php the_permalink(); ?>" data-title="<?php echo htmlentities($title); ?>" data-share-title="<?php echo urlencode($title); ?>" data-share-url="<?php echo urlencode(get_permalink()); ?>"><?php the_title(); ?></h1>

                <p class="text-center excerpt">
                    <?php
                    $metadesc = get_post_meta($the_post_id, '_yoast_wpseo_metadesc', true);
                    $excerpt = trim($metadesc) != '' ? $metadesc : string_limit_words(get_the_excerpt($the_post_id), 25);
                    echo $excerpt;
                    ?>
                </p>

                <hr class="h-divider mb-3">
                <div class="d-flex align-items-start">
                    <div class="col-md-8">
                        <?php
                        if ((get_field('photographer'))) {
                            echo 'Photographed by ' . get_field('photographer', '');
                        }
                        if ((get_field('gallery_date'))) {
                            echo ' on ' . date('M j, Y', strtotime(get_field('gallery_date', '')));
                        }
                        ?>
                        <div class="my-3"><?php do_action('ssm_social_sharing_buttons', 'row'); ?></div>

                        <?php the_content(); ?>

                        <?php $paged = get_query_var('page'); ?>

                        <?php
                        if ($photos->exist()) :
                            //                $photo = isset( $_GET['photo'] ) ? $_GET['photo'] : 0;
                        ?>
                            <!-- Root element of PhotoSwipe. Must have class pswp. -->
                            <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

                                <!-- Background of PhotoSwipe. It's a separate element as animating opacity is faster than rgba(). -->
                                <div class="pswp__bg"></div>

                                <!-- Slides wrapper with overflow:hidden. -->
                                <div class="pswp__scroll-wrap">

                                    <!-- Container that holds slides. PhotoSwipe keeps only 3 of them in the DOM to save memory. Don't modify these 3 pswp__item elements, data is added later on. -->
                                    <div class="pswp__container">
                                        <div class="pswp__item"></div>
                                        <div class="pswp__item"></div>
                                        <div class="pswp__item"></div>
                                    </div>

                                    <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
                                    <div class="pswp__ui pswp__ui--hidden">

                                        <div class="pswp__top-bar">

                                            <!--  Controls are self-explanatory. Order can be changed. -->

                                            <div class="pswp__counter"></div>

                                            <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>

                                            <button class="pswp__button pswp__button--share" title="Share"></button>

                                            <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

                                            <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

                                            <!-- Preloader demo https://codepen.io/dimsemenov/pen/yyBWoR -->
                                            <!-- element will get class pswp__preloader--active when preloader is running -->
                                            <div class="pswp__preloader">
                                                <div class="pswp__preloader__icn">
                                                    <div class="pswp__preloader__cut">
                                                        <div class="pswp__preloader__donut"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                                            <div class="pswp__share-tooltip"></div>
                                        </div>

                                        <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
                                        </button>

                                        <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
                                        </button>

                                        <div class="pswp__caption">
                                            <div class="pswp__caption__center"></div>
                                        </div>

                                    </div>

                                </div>

                            </div>
                            <div class="d-flex gallery flex-wrap justify-content-start">
                                <?php
                                while ($photo = $photos->get()) :
                                    $photo_meta = wp_get_attachment_metadata($photo->id);
                                    //                    $link = str_replace( '/beta/', '/', get_permalink($photo->id) );
                                    $link = get_permalink($photo->id);
                                ?>
                                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" class="col-md-3 col-6">
                                        <a href="<?php echo $link; ?>" itemprop="contentUrl" data-size="<?php echo $photo_meta['width']; ?>x<?php echo $photo_meta['height']; ?>" class="d-block m-1">
                                            <?php echo wp_get_attachment_image($photo->id, 'medium'); ?>
                                        </a>
                                    </figure>
                                <?php endwhile; ?>
                            </div>
                            <?php
                        else :
                            $title = get_the_title();
                            $date = get_field('Full Date', '');
                            // $venue = strip_tags(get_the_term_list( $post->ID, 'venue','', ', ', '' ));
                            $shooter = get_the_author();
                            $link = get_permalink($post->ID);

                            $pa = get_query_var('page');

                            $attachments2 = array(
                                'post_type' => 'attachment',
                                'post_status' => array('publish', 'draft', 'inherit'),
                                'numberposts' => -1,
                                'posts_per_page' => 9,
                                'post_parent' => $post->ID,
                                'orderby' => 'menu_order',
                                'order' => 'asc',
                                'paged' => $pa,
                            );
                            query_posts($attachments2);

                            if (have_posts()) : while (have_posts()) : the_post();
                            ?>
                                    <div>
                                        <div class="slide_img"><?php echo @wp_get_attachment_image(get_the_ID(), 'cover-story'); ?></div>
                                        <div class="clear"></div>
                                    </div>
                            <?php
                                endwhile;
                            endif;
                            ?>

                        <?php endif; ?>

                    </div><!-- /.col-8 -->
                    <div class="col-md-4 right-col-has-ad d-none d-md-block ml-2 align-self-stretch">
                        <div class="d-flex flex-column h-100 justify-content-start">
                            <div class="align-self-center mb-3">
                                <?php render_ad_tag('rail1'); ?>
                            </div>
                            <div class="sticky-ad-right">
                                <div class="mt-3">
                                    <?php
                                    render_ad_tag('rail2');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div><!-- Right Pane - for desktop/tablet devices -->
                </div><!-- /.row -->
            </article>

    <?php
        endwhile;
    endif;
    ?>
</div>
<?php
wp_reset_query();
get_footer();
