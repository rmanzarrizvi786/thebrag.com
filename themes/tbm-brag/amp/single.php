<?php global $amp_post_id;
$amp_post_id = $post_id = $this->get('post_id'); ?>
<!doctype html>
<html amp <?php echo AMP_HTML_Utils::build_attributes_string($this->get('html_tag_attributes')); ?>>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <?php do_action('amp_post_template_head', $this); ?>
    <style amp-custom>
        <?php $this->load_parts(array('style')); ?><?php do_action('amp_post_template_css', $this); ?>
    </style>
    <!--<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>-->
    <script async custom-element="amp-social-share" src="https://cdn.ampproject.org/v0/amp-social-share-0.1.js"></script>
    <script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>
    <script async custom-element="amp-apester-media" src="https://cdn.ampproject.org/v0/amp-apester-media-0.1.js"></script>
    <script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
</head>

<body class="<?php echo esc_attr($this->get('body_class')); ?>">
    <?php
    if (isset($_GET['screenshot'])) {
        $pagepath = 'screenshot';
    } else if (isset($_GET['dfp_key'])) {
        $pagepath = $_GET['dfp_key'];
    } else if (is_home() || is_front_page()) {
        $pagepath = 'homepage';
    } else {
        $pagepath_e = explode('/', $_SERVER['REQUEST_URI']);
        $pagepath = substr($pagepath_e[1], 0, 40);
    }
    ?>
    <?php $this->load_parts(array('header-bar')); ?>

    <article class="amp-wp-article">

        <div class="amp-ad" style="text-align: center; margin: auto;">
            <?php echo render_ad_tag('header'); ?>
        </div>

        <?php $this->load_parts(array('featured-image')); ?>

        <header class="amp-wp-article-header">
            <h1 class="amp-wp-title" id="story_title"><?php echo wp_kses_data($this->get('post_title')); ?></h1>
            <?php $this->load_parts(apply_filters('amp_post_article_header_meta', array('meta-author', 'meta-time'))); ?>
        </header>

        <div class="amp-social-share-bar" style="text-align: center;">
            <amp-social-share type="email" width="40" height="40"></amp-social-share>
            <amp-social-share type="facebook" data-param-app_id="812299355633906" width="40" height="40"></amp-social-share>
            <amp-social-share type="linkedin" width="40" height="40"></amp-social-share>
            <amp-social-share type="twitter" width="40" height="40"></amp-social-share>
            <amp-social-share type="whatsapp" width="40" height="40"></amp-social-share>
        </div>

        <div class="amp-wp-article-content">
            <?php
            $content = $this->get('post_amp_content');

            $content = str_replace('frameborder="0"', '', $content);
            $content = str_replace('frameborder', '', $content);

            $hasApester = false;
            if (strpos($content, 'apester-media') !== FALSE) :
                $hasApester = true;
                $content = preg_replace(
                    '/<div(.*?)class=\"apester-media\" data-media-id=\"(.*)\"><\/div>/',
                    '<amp-apester-media height="390" data-apester-media-id="$2"> </amp-apester-media>',
                    $content
                );
            endif; // If apester-media is present
            //                    $content = preg_replace('/<amp-img[^>]+>/i', '', $content);

            $content = explode("</p>", $content);
            for ($i = 0; $i < count($content); $i++) :
                if (!in_array($this->post->post_type, array('gig', 'snaps'))) :
                    if (count($content) > 2 && $i == 2) :
            ?>
                        <div class="amp-ad" style="text-align: center; margin: auto;">
                            <?php echo render_ad_tag('mrec_1'); ?>
                        </div>
                    <?php
                    endif; // Para 2

                    if (count($content) > 7 && $i == 7) :
                    ?>


                        <?php
                        $topic_id = get_post_meta(absint($post_id), 'observer-topic', true);

                        if (!$topic_id || '' == trim($topic_id)) {
                            $posttags = get_the_tags($post_id);
                            if ($posttags) {
                                foreach ($posttags as $tag) {
                                    if (stripos($tag->name, 'vegan') !== false) {
                                        $topic_id = 6;
                                        break;
                                    }
                                }
                            }

                            $categories = get_the_terms($post_id, 'category');
                            if (!$categories) {
                                return;
                            }

                            $primary_category = null;

                            foreach ($categories as $category) {
                                if (get_post_meta($post_id, '_yoast_wpseo_primary_category', true) == $category->term_id) {
                                    $primary_category = $category;
                                    break;
                                }
                            }

                            if (is_null($primary_category)) {
                                $primary_category = $categories[0];
                            }

                            $topic_id = get_term_meta($category->term_id, 'observer-topic', true);
                        }

                        if (!is_null($topic_id)) {

                            include_once WP_PLUGIN_DIR . '/brag-observer/brag-observer.php';
                            $bo = new BragObserver();

                            $topic = $bo->get_observer_topics($topic_id);
                            if ($topic) {

                                $topic->title = trim(str_ireplace('Observer', '', $topic->title));

                                if (in_array($topic_id, [27])) {
                                    $$topic->title .= ' Music';
                                }
                        ?>

                                <div style="text-align: center">
                                    <form method="post" action-xhr="<?php echo admin_url('admin-ajax.php'); ?>" target="_top">
                                        <input type="hidden" name="list" value="<?php echo $topic_id; ?>">
                                        <input type="hidden" name="source" value="<?php echo get_permalink(); ?>">
                                        <input type="hidden" name="action" value="subscribe_observer">
                                        <div style="background-color: #B98D5B; border-radius: .5rem; padding: .5rem 1rem 1rem; margin: .25rem auto 1rem; color: #fff; text-align: left;">
                                            <h3 class="text-white">
                                                <a href="https://thebrag.com/observer/<?php echo $topic->slug; ?>" target="_blank" style="color: #fff; text-decoration: none;">Love <?php echo $topic->title; ?>?</a>
                                            </h3>
                                            <p class="text-white">Get the latest <?php echo $topic->title; ?> news, features, updates and giveaways straight to your inbox</p>
                                            <div>
                                                <input type="email" name="email" placeholder="Your email" required style="width: calc(100% - 30px); font-size: 16px; color: #000; border: none; border-radius: .25rem; padding: 15px;">
                                                <div>
                                                    <input type="submit" value="Join" style="padding: 10px; font-size: 16px; font-weight: 300; color: #fff; background: #000; border-radius: .25rem; cursor: pointer; border: none;">
                                                </div>
                                            </div>
                                            <div submit-success style="padding-top: 1rem; text-align: center;">
                                                <template type="amp-mustache">
                                                    Thank you!
                                                </template>
                                            </div>
                                        </div>
                                    </form>
                                </div>
            <?php
                            } // If $topic
                        }
                    endif;
                endif;
                echo $content[$i] . "</p>";
            endfor;

            //                    echo $content;
            if ('venue' == $this->post->post_type) {
                include('single-venue.php');
            } elseif ('gig' == $this->post->post_type) {
                include('single-gig.php');
            } elseif ('snaps' == $this->post->post_type) {
                include('single-snaps.php');
            }
            ?>
        </div>

        <div class="amp-ad" style="text-align: center; margin: auto;">
            <?php echo render_ad_tag('mrec_2'); ?>
        </div>

        <?php if (!$hasApester) : ?>
            <amp-apester-media height="390" data-apester-channel-token="5decf535b7735f79ae29b691"> </amp-apester-media>
            <p>&nbsp;</p>
        <?php endif; ?>

        <div class="amp-ad" style="text-align: center; margin: auto;">
            <?php echo render_ad_tag('sticky_footer'); ?>
        </div>

        <footer class="amp-wp-article-footer">
            <?php $this->load_parts(apply_filters('amp_post_article_footer_meta', array('meta-taxonomy'))); ?>
        </footer>

        <div class="related-stories-wrap">
            <h2 class="title">You may also like</h2>
            <?php
            $post_id = get_the_ID();
            // Related Posts from tags
            $tags = wp_get_post_tags($post_id);
            $arg_tags = array();
            foreach ($tags as $tag) {
                array_push($arg_tags, $tag->term_id);
            }
            $args = array(
                'post_status' => 'publish',
                'tag__in' => $arg_tags,
                'post__not_in' => array($post_id),
                'posts_per_page' => 2,
                'orderby' => 'rand',
                'date_query' => array(
                    'column' => 'post_date',
                    'after' => '-60 days'
                )
            );
            $related_posts_query = new WP_Query($args);
            if ($related_posts_query->have_posts()) :
                while ($related_posts_query->have_posts()) :
                    $related_posts_query->the_post();
            ?>
                    <div class="related-story">
                        <div class="post-thumbnail">
                            <?php if ('' !== get_the_post_thumbnail()) : ?>
                                <a href="<?php echo get_permalink() . 'amp'; ?>">
                                    <?php $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail'); ?>
                                    <figure class="amp-wp-article-featured-image wp-caption">
                                        <amp-img src="<?php echo $thumbnail[0]; ?>" class="attachment-large size-large wp-post-image amp-wp-enforced-sizes" width="150" height="75"></amp-img>
                                    </figure>
                                </a>
                            <?php endif; ?>
                        </div><!-- .post-thumbnail -->
                        <div class="post-content">
                            <h2><a href="<?php echo get_permalink() . 'amp'; ?>"><?php the_title(); ?></a></h2>
                            <p class="excerpt">
                                <?php
                                $excerpt = get_the_excerpt();
                                echo string_limit_words($excerpt, 25); ?>
                            </p>
                        </div>
                    </div>
                <?php
                endwhile;
                wp_reset_query();
            endif;

            // Related Posts from Categories
            $cats = wp_get_post_categories($post_id);
            $arg_tags = array();
            foreach ($tags as $tag) {
                array_push($arg_tags, $tag->term_id);
            }
            $args = array(
                'post_status' => 'publish',
                'category__in' => $cats,
                'post__not_in' => array($post_id),
                'posts_per_page' => 2,
                'orderby' => 'rand',
                'date_query' => array(
                    'column' => 'post_date',
                    'after' => '-30 days'
                )
            );
            $related_posts_query = new WP_Query($args);
            if ($related_posts_query->have_posts()) :
                while ($related_posts_query->have_posts()) :
                    $related_posts_query->the_post();
                ?>
                    <div class="related-story">
                        <div class="post-thumbnail">
                            <?php if ('' !== get_the_post_thumbnail()) : ?>
                                <a href="<?php echo get_permalink() . 'amp'; ?>">
                                    <?php // the_post_thumbnail( 'thumbnail' ); 
                                    ?>
                                    <?php
                                    $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail');
                                    ?>
                                    <figure class="amp-wp-article-featured-image wp-caption">
                                        <amp-img src="<?php echo $thumbnail[0]; ?>" class="attachment-large size-large wp-post-image amp-wp-enforced-sizes" width="150" height="75"></amp-img>
                                    </figure>
                                </a>
                            <?php endif; ?>
                        </div><!-- .post-thumbnail -->
                        <div class="post-content">
                            <h2><a href="<?php echo get_permalink() . 'amp'; ?>"><?php the_title(); ?></a></h2>
                            <p class="excerpt"><?php $excerpt = get_the_excerpt();
                                                echo string_limit_words($excerpt, 25); ?></p>
                        </div>
                    </div>
            <?php
                endwhile;
                wp_reset_query();
            endif;
            ?>
            <div class="clear">&nbsp;</div>
        </div>

        <div class="clear"></div>

        <div class="amp-wp-article-content">
            <amp-embed width=100 height=100 type=taboola layout=responsive heights="(min-width:1313px) 39%, (min-width:795px) 46%, (min-width:480px) 58%, (min-width:439px) 261%, 284%" data-publisher="thebragmedia-thebrag" data-mode="alternating-thumbnails-a" data-placement="Below Article Thumbnails AMP" data-target_type="mix" data-article="auto" data-url="">
            </amp-embed>
        </div>

    </article>

    <?php $this->load_parts(array('footer')); ?>

    <?php do_action('amp_post_template_footer', $this); ?>

    <!-- Start Alexa AMP Certify Javascript -->
    <amp-analytics type="alexametrics">
        <script type="application/json">
            {
                "vars": {
                    "atrk_acct": "O3NOq1WyR620WR",
                    "domain": "thebrag.com"
                }
            }
        </script>
    </amp-analytics>
    <!-- End Alexa AMP Certify Javascript -->

</body>

</html>