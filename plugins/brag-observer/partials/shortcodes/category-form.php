<?php
$category_atts = shortcode_atts(array(
    'id' => NULL,
), $atts);

if (is_null($category_atts['id']))
    return;

$post_id = $category_atts['id'];

$topic = get_post_meta($post_id, 'observer-topic', true);

if (!$topic) {

    $posttags = get_the_tags($post_id);
    if ($posttags) {
        foreach ($posttags as $tag) {
            if (stripos($tag->name, 'vegan') !== false) {
                $topic = 6;
                break;
            }
        }
    }

    $topics = $this->get_observer_topics();
    $topic_titles = wp_list_pluck($topics, 'title', 'id');
    $topic_links = wp_list_pluck($topics, 'link', 'id');

    if (!$topic) {
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

        $topic = get_term_meta($category->term_id, 'observer-topic', true);

        if (!in_array($topic, array_keys($topic_titles))) {
            foreach ($categories as $category) {
                if (in_array(get_term_meta($category->term_id, 'observer-topic', true), array_keys($topic_titles))) {
                    $topic = get_term_meta($category->term_id, 'observer-topic', true);
                    break;
                }
            }
        }
    }
}

?>
<style>
    .observer-sub-form {
        padding: .25rem;
        background: #f3f3f3;
        /* border-radius: .25rem; */
        max-width: none;
        border: 1px solid #ddd;
        position: relative;
    }

    .observer-sub-form .l-learn-more {
        /* position: absolute;
            right: 0;
            top: 0;
            padding: .25rem .5rem;
            background: #ddd;
            border-top-right-radius: 10px;
            border-bottom-left-radius: 10px; */
        text-decoration: underline;
        font-weight: bold;
    }
</style>
<?php

if ($topic) {

    if (is_user_logged_in()) {
        global $wpdb;
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $my_sub_lists = [];
        $my_subs = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '{$topic}' AND status = 'subscribed' ");
        $my_sub_lists = wp_list_pluck($my_subs, 'list_id');

        if (in_array($topic, $my_sub_lists)) {
            return;
        }
    }

    $topic_title = trim(str_ireplace('Observer', '', $topic_titles[$topic]));

    if (in_array($topic, [27])) {
        $topic_title .= ' Music';
    }
?>
    <style>
        .observer-sub-form .observer-sub-email {
            background: #fff;
            border-radius: .25rem;
            padding: 25px 15px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;

        }

        .observer-sub-form input[type=email] {
            width: 100%;
            font-size: 16px;
            line-height: 1;
            color: #000;
            border: none;
        }

        .observer-sub-form input[type=submit],
        .observer-sub-form button.btn-join {
            padding: 5px 10px;
            font-weight: 300;
            /* background-color: #dc3545; */
            color: #fff;
            border: none;

        }

        <?php
        if (!is_user_logged_in()) {
        ?>.observer-sub-form .submit-wrap,
        .observer-sub-form .submit-wrap input[type=submit] {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        .observer-sub-form .submit-wrap {
            border: 1px solid #fff;
        }

        <?php
        } // If not logged in
        ?>.observer-sub-form .spinner {
            width: 25px !important;
            height: 25px !important;
            margin: 10px auto !important;
        }

        .observer-sub-form .spinner .double-bounce1,
        .observer-sub-form .spinner .double-bounce2 {
            background-color: #fff;
        }
    </style>

    <div class="observer-sub-form rounded justify-content-center my-3 p-3">
        <div class="mb-2">
            <h5 class="mb-0">Love <?php echo $topic_title; ?>?</h5>
        </div>
        <p class="mb-2">
            Get the latest <?php echo $topic_title; ?> news, features, updates and giveaways straight to your inbox.
            <a href="<?php echo $topic_links[$topic]; ?>" class="l-learn-more text-dark" target="_blank">Learn more</a>
        </p>
        <div class="d-block">
            <?php if (is_user_logged_in()) : ?>
                <form action="#" method="post" id="observer-subscribe-form<?php echo $post_id; ?>" name="observer-subscribe-form" class="observer-subscribe-form">
                    <input type="hidden" name="list" value="<?php echo $topic; ?>">
                    <button type="submit" class="button btn btn-danger">JOIN</button>
                    <div class="loading mx-3" style="display: none;">
                        <div class="spinner">
                            <div class="double-bounce1 bg-dark"></div>
                            <div class="double-bounce2 bg-dark"></div>
                        </div>
                    </div>
                </form>
                <div class="alert alert-info d-none js-msg-subscribe mt-2"></div>
                <div class="alert alert-danger d-none js-errors-subscribe mt-2"></div>
            <?php else : // If not logged in 
            ?>
                <button class="button btn btn-danger btn-join">JOIN</button>
                <form action="#" method="post" id="observer-subscribe-form<?php echo $post_id; ?>" name="observer-subscribe-form" class="observer-subscribe-form d-none">
                    <div class="d-flex rounded" style="<?php echo !is_user_logged_in() ? 'box-shadow: 0 0 0 1px rgba(0,0,0,.15), 0 2px 3px rgba(0,0,0,.2)' : ''; ?>;">
                        <input type="hidden" name="list" value="<?php echo $topic; ?>">
                        <input type="email" name="email" class="form-control observer-sub-email" placeholder="Your email" value="">
                        <div class="d-flex submit-wrap">
                            <input type="submit" value="JOIN" name="subscribe" class="button btn btn-danger rounded <?php echo is_user_logged_in() ? 'btn-observer-join' : ''; ?>">
                            <div class="loading mx-3" style="display: none;">
                                <div class="spinner">
                                    <div class="double-bounce1 bg-dark"></div>
                                    <div class="double-bounce2 bg-dark"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="alert alert-info d-none js-msg-subscribe mt-2"></div>
                <div class="alert alert-danger d-none js-errors-subscribe mt-2"></div>
            <?php endif; // If logged in 
            ?>
        </div>

    </div>


    <script>
        jQuery(document).ready(function($) {
            if ($('.btn-join').length) {
                $(document).on('click', '.btn-join', function() {
                    var theForm = $(this).next('form.observer-subscribe-form');
                    theForm.removeClass('d-none');
                    theForm.find('input[name="email"]').focus();
                    $(this).remove();
                })
            }
            if ($('.observer-subscribe-form').length) {
                $(document).on('submit', '.observer-subscribe-form', function(e) {
                    e.preventDefault();
                    var theForm = $(this);

                    <?php if (!is_user_logged_in()) { ?>
                        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

                        if (theForm.find('input[name="email"]').length &&
                            (
                                theForm.find('input[name="email"]').val() == '' ||
                                !re.test(String(theForm.find('input[name="email"]').val().toLowerCase()))
                            )) {
                            theForm.parent().find('.js-errors-subscribe').html('Please enter a valid email address.').removeClass('d-none');
                            return false;
                        }
                    <?php } ?>

                    var formData = $(this).serialize();
                    var loadingElem = $(this).find('.loading');
                    var button = $(this).find('.button');

                    var the_url = theForm.closest('.single_story').find('h1:first').data('href');
                    formData += '&source=' + the_url;

                    $('.js-errors-subscribe,.js-msg-subscribe').html('').addClass('d-none');
                    loadingElem.show();
                    button.hide();
                    var data = {
                        action: 'subscribe_observer_category',
                        formData: formData
                    };
                    $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(res) {
                        if (res.success) {
                            theForm.parent().find('.js-msg-subscribe').html(res.data.message).removeClass('d-none');
                            theForm.hide();
                        } else {
                            theForm.parent().find('.js-errors-subscribe').html(res.data.error.message).removeClass('d-none');
                            button.show();
                        }
                        loadingElem.hide();
                    }).error(function() {
                        theForm.parent().find('.js-errors-subscribe').html('Something went wrong, please try again later').removeClass('d-none');
                        loadingElem.hide();
                        button.show();
                    });
                });
            }
        });
    </script>
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
