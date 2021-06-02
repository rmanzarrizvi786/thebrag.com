<?php

/**
 * Header bar template part.
 *
 * @package AMP
 */

?>
<header id="top" class="amp-wp-header">
    <div class="amp-wp-header-inner">
        <?php if ('dad' != get_post_type()) : ?>
            <a href="<?php echo esc_url($this->get('home_url')); ?>">
                <?php $site_icon_url = $this->get('site_icon_url'); ?>
                <?php if ($site_icon_url) : ?>
                    <amp-img src="<?php echo esc_url($site_icon_url); ?>" width="32" height="32" class="amp-wp-site-icon"></amp-img>
                <?php endif; ?>
                <span class="amp-site-title">
                    <?php echo esc_html(wptexturize($this->get('blog_name'))); ?>
                </span>
            </a>
        <?php else :
            $category_link = get_post_type_archive_link('dad');
        ?>
            <a href="<?php echo esc_url($category_link); ?>">
                <span class="amp-site-title">Brag Dad</span>
            </a>
        <?php endif; ?>
    </div>
</header>
<div style="position: fixed; top: 0; z-index: 9999999999; height: 45px; display: flex;">
    <amp-sidebar id="amp_side_menu" class="i-amphtml-layout-nodisplay i-amphtml-element i-amphtml-overlay i-amphtml-scrollable i-amphtml-built i-amphtml-layout" role="menu" tabindex="-1" hidden layout="nodisplay" side="left">
        <button class="btn hamburger left" on="tap:amp_side_menu.toggle" style="height: 45px; width: 100%; padding-left: 1.25rem;">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <?php
        $top_menu_items = [];
        $exclude_cats = [288366, 303097, 288238, 284732]; // Competitions, Evergreen, News, Features
        $menu_cats = get_categories(
            array(
                'parent' => null,
                'orderby'    => 'count',
                'order' => 'DESC',
                'exclude' => $exclude_cats,
            )
        );
        foreach ($menu_cats as $cat) :
            array_push($top_menu_items, [
                'link' => get_category_link($cat),
                'text' => $cat->name,
            ]);
        endforeach;
        array_push($top_menu_items, [
            'link' => home_url('/observer/competitions/'),
            'text' => 'Competitions',
        ]);
        /* wp_nav_menu(array(
            'theme_location' => 'top',
            'menu_id'        => 'menu_main',
            'menu_class' => 'menu',
            'fallback_cb'   => false,
            'add_li_class'  => 'nav-item',
            'link_class'   => 'nav-link',
            'container' => 'nav',
        )); */
        ?>
        <nav>
            <ul class="menu">
                <?php
                foreach ($top_menu_items as $i => $top_menu_item) :
                ?>
                    <li class="nav-item">
                        <a href="<?php echo $top_menu_item['link']; ?>" class="nav-link"><?php echo $top_menu_item['text']; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </amp-sidebar>
    <button class="btn hamburger left" on="tap:amp_side_menu.toggle">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
</div>