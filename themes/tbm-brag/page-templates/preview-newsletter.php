<?php
/*
 * Template Name: Preview Newsletter
 */

if (!isset($_GET['id'])) {
    wp_redirect(home_url());
    die();
}

$frontend = true;

include WP_PLUGIN_DIR . '/brag-observer/partials/newsletter-template-braze.php';
