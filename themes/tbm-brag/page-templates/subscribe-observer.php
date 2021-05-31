<?php
/*
 * Template Name: Subscribe (Observer)
 */


$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$lists = isset($_POST['list']) ? $_POST['list'] : [];

if (is_null($email) || empty($lists)) {
    return;
}

$api = new API();

$body = [
    'email' => $email,
    'status' => 'subscribed',
    'return' => 'bool'
];

foreach ($lists as $list) {
    $ob_list = $wpdb->get_row("SELECT id, slug FROM {$wpdb->prefix}observer_lists WHERE id = '{$lists[0]}' LIMIT 1");
    if ($ob_list) {
        $body['list'] = $list;
        $api->sub_unsub($body);
    }
}

if ( isset($_POST['redirect'])) {
    wp_redirect($_POST['redirect']);
    exit;
}

if (count($lists) > 1) {
    wp_redirect(home_url('/observer/'));
    exit;
} else {
    if (isset($ob_list) && !is_null($ob_list)) {
        wp_redirect(home_url('/observer/' . $ob_list->slug . '/'));
        exit;
    } else {
        wp_redirect(home_url('/observer/'));
        exit;
    }
}
