<?php
$top_menu_html = '';
$number_of_menu_items = 8;
$top_menu_items = [];
$my_sub_lists = [];
$exclude_cats = [288366, 303097, 288238, 284732]; // Competitions, Evergreen, News, Features

if (is_user_logged_in()) :
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $my_subs = $wpdb->get_results("SELECT s.list_id FROM {$wpdb->prefix}observer_subs s JOIN {$wpdb->prefix}observer_lists l ON l.id = s.list_id WHERE user_id = '{$user_id}' AND s.status = 'subscribed' AND l.related_site='thebrag.com' ");
    $my_sub_lists = wp_list_pluck($my_subs, 'list_id');
endif;

ob_start();

if (isset($my_sub_lists) && !empty($my_sub_lists)) :
    $menu_cats = get_categories(
        array(
            // 'parent' => null,
            // 'hide_empty' => '0',
            'orderby'    => 'count',
            'order' => 'DESC',
            'exclude' => $exclude_cats,
            'meta_query' => array(
                array(
                    'key'     => 'observer-topic',
                    'value'   => $my_sub_lists,
                    'compare' => 'IN',
                )
            )
        )
    );
    $menu_cats_ids = wp_list_pluck($menu_cats, 'term_id');

    foreach ($menu_cats as $cat) :
        array_push($top_menu_items, [
            'link' => get_category_link($cat),
            'text' => $cat->name,
        ]);
    endforeach;

    if (count($menu_cats) < $number_of_menu_items) :
        $menu_cats2 = get_categories(
            array(
                'parent' => null,
                'orderby'    => 'count',
                'order' => 'DESC',
                'exclude' => array_merge($exclude_cats, $menu_cats_ids),
                'number' => $number_of_menu_items - count($menu_cats)
            )
        );

        // echo '<pre>'; print_r($menu_cats2);exit;
        foreach ($menu_cats2 as $cat) :
            array_push($top_menu_items, [
                'link' => get_category_link($cat),
                'text' => '<span class="plus"><img src="' . ICONS_URL . 'plus.svg" width="16" height="16" alt="+"></span>
        <span class="plus-hover"><img src="' . ICONS_URL . 'plus-color.svg" width="16" height="16" alt="+"></span>
        <span class="text-muted">' . $cat->name . '</span>',
                'class' => 'secondary',
            ]);
        endforeach;
    endif;
else : // Show all categories
    $menu_cats = get_categories(
        array(
            'parent' => null,
            'orderby'    => 'count',
            'order' => 'DESC',
            'exclude' => $exclude_cats,
        )
    );

    // echo '<pre>'; print_r($menu_cats2);exit;
    foreach ($menu_cats as $cat) :
        array_push($top_menu_items, [
            'link' => get_category_link($cat),
            'text' => $cat->name,
        ]);
    endforeach;
endif; // If user picked niche

array_push($top_menu_items, [
    'link' => home_url('/observer/competitions/'),
    'text' => 'Competitions',
]);
?>
<nav class="menu-top-menu-container">
    <ul id="menu_main" class="nav flex-column">
        <?php
        foreach ($top_menu_items as $i => $top_menu_item) :
        ?>
            <li class="nav-item">
                <a href="<?php echo $top_menu_item['link']; ?>" class="nav-link"><?php echo $top_menu_item['text']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<?php
// var_dump($menu_cats); exit;
/* else :
    wp_nav_menu(array(
      'theme_location' => 'top',
      'menu_id'        => 'menu_main',
      'menu_class' => 'nav flex-column flex-md-row',
      'fallback_cb'   => false,
      'add_li_class'  => 'nav-item',
      'link_class'   => 'nav-link',
      'container' => 'nav',
    ));
   */

$top_menu_html = ob_get_clean();

$logo_link = isset($logo_link) ? $logo_link : site_url(); // ! isset( $logo_link ) ? site_url() : $logo_link;
$logo_alt = isset($logo_alt) ? $logo_alt : 'The Brag';
?>
<div id="mobile-menu" class="d-flex flex-column">
    <div style="position: absolute; top: 0; left: 0;">
        <button class="navbar-toggler" type="button" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="outline: none;">
            <div class="navbar-button-bars text-white"><i class="fa fa-remove"></i></div>
        </button>
    </div>
    <div class="brand my-3">
        <a class="header-logo" href="<?php echo $logo_link; ?>">
            <img src="<?php echo $logo_url; ?>" alt="<?php echo $logo_alt; ?>" class="img-fluid" style="max-width: 150px;" loading="lazy">
        </a>
    </div>
    <nav aria-label="Menu Social" class="mb-3">
        <ul class="nav flex-row align-items-center justify-content-center">
            <li class="nav-item"><a target="_blank" href="https://www.facebook.com/thebragmag" class="nav-link px-2 text-white" aria-label="Facebook"><i class="fab fa-facebook fa-lg" aria-hidden=true></i></a></li>
            <li class="nav-item"><a target="_blank" href="https://twitter.com/TheBrag" class="nav-link px-2 text-white" aria-label="Twitter"><i class="fab fa-twitter fa-lg" aria-hidden=true></i></a></li>
            <li class="nav-item"><a target="_blank" href="https://www.instagram.com/thebragmag/" class="nav-link px-2 text-white" aria-label="Instagram"><i class="fab fa-instagram fa-lg" aria-hidden=true></i></a></li>
            <li class="nav-item"><a target="_blank" href="https://www.youtube.com/channel/UCcZMmtU74qKN_w4Dd8ZkV6g" class="nav-link px-2 text-white" aria-label="YouTube"><i class="fab fa-youtube fa-lg" aria-hidden=true></i></a></li>
            <li class="nav-item"><a href="<?php echo home_url('/observer/'); ?>" class="nav-link px-2 text-white"><i class="fa fa-envelope fa-lg" aria-hidden=true></i></a></li>
        </ul>
    </nav>
    <div role="search" method="get" id="searchform-mobile" class="searchform form-inline justify-content-center my-1 px-2" action="<?php echo esc_url(home_url('/')); ?>" style="">
        <div class="flex-fill"><input type="text" name="s" class="search-field form-control w-100" placeholder="Search..." autocomplete="off" aria-label="Search"></div>
        <button type="button" class="btn" aria-label="Search"><i class="fa fa-search" aria-hidden="true"></i></button>
    </div>
    <?php echo $top_menu_html; ?>
</div>