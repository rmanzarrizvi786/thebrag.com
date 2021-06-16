<?php
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
if (is_user_logged_in()) {
    $pages['change-password'] = [
        'link' => 'change-password',
        'text' => 'Settings',
    ];
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