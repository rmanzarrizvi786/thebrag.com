<?php
extract($args);
$pages = [];
if (is_user_logged_in()) {
    $pages['profile'] = [
        'link' => 'profile',
        'text' => 'Profile',
    ];
}
$pages += [
    'refer-a-friend' => [
        'link' => 'refer-a-friend',
        'text' => 'Earn rewards',
    ],
    'competitions' => [
        'link' => 'observer/competitions',
        'text' => 'Competitions',
    ],
    'magazine-subscriptions' => [
        'link' => 'observer/magazine-subscriptions',
        'text' => 'Magazine subscriptions'
    ]
];
$pages['observer-subscriptions'] = [
    'link' => 'observer-subscriptions',
    'text' => 'My preferences',
];

$current_user = wp_get_current_user();

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

$auth0_user = null;

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
        if ($wp_auth0_id = get_user_meta($current_user->ID, 'wp_auth0_id', true)) {
            $auth0_user = $mgmt_api->users()->get($wp_auth0_id);
        } else if ($wp_auth0_id = get_user_meta($current_user->ID, $wpdb->prefix . 'auth0_id', true)) {
            $auth0_user = $mgmt_api->users()->get($wp_auth0_id);
        }
    } catch (Exception $e) {
        // die($e->getMessage());
    }
}

if (is_user_logged_in()) {
    if (
        !is_null($auth0_user) &&
        isset($auth0_user['identities']) &&
        isset($auth0_user['identities'][0]) &&
        in_array($auth0_user['identities'][0]['connection'], ['Username-Password-Authentication', 'brag-observer'])
    ) {
        $pages['change-password'] = [
            'link' => 'change-password',
            'text' => 'Settings',
        ];
    }
}
$current_page = get_query_var('pagename'); // $wp_query->get_required_object();

?>

<div class="col-12 col-md-3 border-none border-md-right align-self-stretch py-3">
    <nav class="nav-v d-flex justify-content-between align-items-start collapsed">
        <ul class="d-flex flex-fill w-100">
            <?php foreach ($pages as $page => $link_details) : ?>
                <li class="<?php echo $page == $current_page ? 'active' : ''; ?>"><a href="<?php echo home_url('/' . $link_details['link'] . '/'); ?>"><?php echo $link_details['text']; ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="arrow-down d-flex d-md-none px-1 toggle-nav"><img src="<?php echo ICONS_URL; ?>icon_arrow-down-tb.svg" width="10" height="9" alt="▼"></div>
    </nav>
</div>