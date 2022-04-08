<?php
/*
 * Template Name: Preview Newsletter
 */

if (!get_query_var('newsletter_id')) {
    wp_redirect(home_url());
    die();
}
$frontend = true;

$type = get_query_var('newsletter_type');

if ('newsletter' == $type) {
    include WP_PLUGIN_DIR . '/brag-observer/partials/newsletter-template-braze.php';
} elseif ('solus' == $type) {
    include WP_PLUGIN_DIR . '/brag-observer/partials/solus-template.php';
}
