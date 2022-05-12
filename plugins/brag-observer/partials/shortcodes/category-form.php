<?php
$category_atts = shortcode_atts(array(
    'id' => NULL,
), $atts);

if (is_null($category_atts['id']))
    return;

$post_id = $category_atts['id'];

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

    $topic = $this->get_observer_topics($topic_id);
    if (!$topic) {
        return;
    }

    if (is_user_logged_in()) {
        global $wpdb;
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $my_sub_lists = [];
        $my_sub = $wpdb->get_row("SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '{$topic->id}' AND status = 'subscribed' LIMIT 1");

        if ($my_sub) {
            return;
        }
    }

    $topic->title = trim(str_ireplace('Observer', '', $topic->title));

    if (in_array($topic_id, [27])) {
        $topic->title .= ' Music';
    }
?>
    <div class="observer-sub-form justify-content-center my-3 p-0 d-flex align-items-stretch bg-dark text-white">
        <div class="img-wrap" style="background-image: url(<?php echo $topic->image_url; ?>);"></div>
        <div class="p-2 p-md-3 d-flex justify-content-center align-items-center">
            <div>
                <div class="mb-2">
                    <h2 class="h5 mb-0">Love <?php echo $topic->title; ?>?</h2>
                </div>
                <p class="mb-2">
                    Get the latest <?php echo $topic->title; ?> news, features, updates and giveaways straight to your inbox
                    <a href="https://thebrag.com/observer/<?php echo $topic->slug; ?>" class="l-learn-more" target="_blank" rel="noopener">Learn more</a>
                </p>
                <?php if (!is_user_logged_in()) : ?>
                    <button class="button btn btn-primary btn-join">JOIN</button>
                <?php endif; ?>
                <form action="#" method="post" id="observer-subscribe-form<?php echo $post_id; ?>" name="observer-subscribe-form" class="observer-subscribe-form <?php echo !is_user_logged_in() ? 'd-none bg-white' : ''; ?>">
                    <div class="d-flex justify-content-start">
                        <input type="hidden" name="list" value="<?php echo $topic_id; ?>">
                        <?php if (!is_user_logged_in()) : ?>
                            <input type="email" name="email" class="form-control observer-sub-email" placeholder="Your email" value="">
                        <?php endif; ?>
                        <div class="d-flex submit-wrap rounded pr-1 pr-md-0">
                            <input type="submit" value="Join" name="subscribe" class="btn btn-primary rounded">
                        </div>
                    </div>
                </form>
                <div class="alert alert-success d-none js-msg-subscribe mt-2"></div>
                <div class="alert alert-danger d-none js-errors-subscribe mt-2"></div>
            </div>
        </div>
    </div>
<?php
} else if (isset($primary_category)) { // Topic not set
?>
    <a href="https://thebrag.com/observer/" target="_blank" class="text-dark">
        <div class="observer-sub-form rounded justify-content-center my-3 p-3">
            <h5 class="">Newsletters tailored to you</h5>
            <p class="">Get the latest news, features, updates and giveaways straight to your inbox</p>
            <div class="btn btn-danger">Click here to join FREE</div>
        </div>
    </a>
<?php
}
