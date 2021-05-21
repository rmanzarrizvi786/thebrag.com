<?php
$pages = [
    'profile' => [
        'link' => 'profile',
        'text' => 'Profile',
    ],
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
    ],
    'change-password' => [
        'link' => 'change-password',
        'text' => 'Settings',
    ]
];
$current_page = get_query_var('pagename'); // $wp_query->get_required_object();

// echo $current_page; exit;
?>

<div class="col-md-3 border-right align-self-stretch py-3">
    <nav class="nav-v">
        <ul>
            <?php foreach ($pages as $page => $link_details) : ?>
                <li class="<?php echo $page == $current_page ? 'active' : ''; ?>"><a href="<?php echo home_url('/' . $link_details['link'] . '/'); ?>"><?php echo $link_details['text']; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
</div>