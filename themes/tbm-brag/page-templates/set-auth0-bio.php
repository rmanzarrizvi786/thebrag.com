<?php
/*
 * Template Name: Set Auth0 Bio (temp)
 */

require get_template_directory() . '/vendor/autoload.php';

use Auth0\SDK\Auth0;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\API\Management;

$dotenv = Dotenv\Dotenv::createImmutable(ABSPATH);
$dotenv->load();

$auth0_api = new Authentication(
    $_ENV['AUTH0_DOMAIN'],
    $_ENV['AUTH0_CLIENT_ID']
);

$config = [
    'client_secret' => $_ENV['AUTH0_CLIENT_SECRET'],
    'client_id' => $_ENV['AUTH0_CLIENT_ID'],
    'audience' => $_ENV['AUTH0_MANAGEMENT_AUDIENCE'],
];

try {
    $result = $auth0_api->client_credentials($config);
    $access_token = $result['access_token'];
} catch (Exception $e) {
    // die($e->getMessage());
}

if (isset($access_token)) {
    // Instantiate the base Auth0 class.
    $auth0 = new Auth0([
        // The values below are found on the Application settings tab.
        'domain' => $_ENV['AUTH0_DOMAIN'],
        'client_id' => $_ENV['AUTH0_CLIENT_ID'],
        'client_secret' => $_ENV['AUTH0_CLIENT_SECRET'],
        'redirect_uri' => $_ENV['AUTH0_REDIRECT_URI'],
    ]);

    $mgmt_api = new Management($access_token, $_ENV['AUTH0_DOMAIN']);
    try {
        $users = $wpdb->get_results("SELECT `user_id`, `meta_value` AS `description` FROM {$wpdb->prefix}usermeta WHERE `meta_key` = 'description' AND `meta_value` != '' ");
        if ($users) {
            foreach ($users as $user) {
                if ($wp_auth0_id = get_user_meta($user->user_id, 'wp_auth0_id', true)) {
                    $auth0_user = $mgmt_api->users()->get($wp_auth0_id);
                } elseif ($wp_auth0_id = get_user_meta($user->user_id, $wpdb->prefix . 'auth0_id', true)) {
                    $auth0_user = $mgmt_api->users()->get($wp_auth0_id);
                }

                $auth0_usermeta['bio'] = $user->description;
                if (!is_null($auth0_user) && isset($auth0_usermeta) && !empty($auth0_usermeta)) {
                    $mgmt_api->users()->update($wp_auth0_id, [
                        'user_metadata' => $auth0_usermeta
                    ]);
                    echo $user->user_id . ' => ' . $wp_auth0_id . '<br>' . $user->description;
                    echo '<br><br>';
                }
            }
        }
    } catch (Exception $e) {
        // die($e->getMessage());
    }
}
