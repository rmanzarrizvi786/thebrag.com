<?php
// tbm_track_visits();
$page_template = get_page_template_slug();
$current_url = home_url(add_query_arg([], $GLOBALS['wp']->request));
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <link rel="shortcut icon" href="<?php echo CDN_URL; ?>favicon.png?v=<?php echo time(); ?>" />

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="profile" href="http://gmpg.org/xfn/11">

  <meta name="google-site-verification" content="Tf8gbZdF2WOW_R5JIuceGcMuqUNy7TAvdrYKaeoLP5I" />
  <meta name="msvalidate.01" content="E0857D4C8CDAF55341D0839493BA8129" />
  <meta property="fb:app_id" content="1950298011866227" />
  <!-- <meta property="fb:pages" content="145692175443937" /> -->

  <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">
  <link rel="manifest" href="/icons/site.webmanifest">
  <link rel="mask-icon" href="/icons/safari-pinned-tab.svg" color="#b98d5b">
  <link rel="shortcut icon" href="/icons/favicon.ico">
  <meta name="msapplication-TileColor" content="#b98d5b">
  <meta name="msapplication-config" content="/icons/browserconfig.xml">
  <meta name="theme-color" content="#b98d5b">

  <meta name='impact-site-verification' value='-771456124'>

  <link rel="preconnect" href="https://cdn.onesignal.com/">
  <link rel="preconnect" href="https://www.googletagservices.com/">
  <link rel="preconnect" href="//cdn.publift.com/">
  <link rel="preconnect" href="https://www.googletagmanager.com">
  <link rel="preconnect" href="https://tpc.googlesyndication.com">
  <link rel="preconnect" href="https://adservice.google.com">
  <link rel="preconnect" href="https://www.youtube.com">
  <link rel="preconnect" href="https://adservice.google.com.au">
  <link rel="preconnect" href="https://connect.facebook.net">
  <link rel="preconnect" href="https://bid.g.doubleclick.net">
  <link rel="preconnect" href="https://fonts.gstatic.com/">

  <link rel="manifest" href="/manifest.json">

  <?php if (strpos($_SERVER['REQUEST_URI'], '/gigs/') !== false): ?>
    <link rel="canonical" href="<?php echo home_url() . $_SERVER['REQUEST_URI']; ?>" />
  <?php endif; ?>

  <?php
  if (is_single()):
    ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@TheBrag">
    <meta name="twitter:title" content="<?php the_title(); ?>">
    <meta name="twitter:image" content="<?php
    if (has_post_thumbnail()) {
      $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');

      echo "https://images.thebrag.com/cdn-cgi/image/fit=crop,width=1200,height=628/$src[0]";
    }
    ?>">

    <?php
    if (get_field('page_background_colour')): ?>
      <style>
        body {
          background-color:
            <?php echo get_field('page_background_colour'); ?>
            !important;
        }
      </style>
    <?php endif; // If page_background_colour field is set
    if (get_field('fb_pixel')):
      echo get_field('fb_pixel');
    endif; // If fb_pixel field is set
  endif; // If it's a Single Post
  ?>

  <?php wp_head(); ?>

  <script type="text/javascript">
    !(function (o, n, t) {
      t = o.createElement(n), o = o.getElementsByTagName(n)[0], t.async = 1, t.src = "https://guiltlessbasketball.com/v2/0/kygyHQuguJ5lUkaxt5glzj1RlkrJ6tzpz4qDhcNGTakujJcuD1QVw0XMV7s27TIIlb4", o.parentNode.insertBefore(t, o)
    })(document, "script"), (function (o, n) {
      o[n] = o[n] || function () {
        (o[n].q = o[n].q || []).push(arguments)
      }
    })(window, "admiral");
    !(function (n, e, r, t) {
      function o() {
        if ((function o(t) {
          try {
            return (t = localStorage.getItem("v4ac1eiZr0")) && 0 < t.split(",")[4]
          } catch (n) { }
          return !1
        })()) {
          var t = n[e].pubads();
          typeof t.setTargeting === r && t.setTargeting("admiral-engaged", "true")
        }
      } (t = n[e] = n[e] || {}).cmd = t.cmd || [], typeof t.pubads === r ? o() : typeof t.cmd.unshift === r ? t.cmd.unshift(o) : t.cmd.push(o)
    })(window, "googletag", "function");
  </script>

  <style>
    <?php
    echo file_get_contents(get_template_directory() . '/css/reset.css');
    echo file_get_contents(get_template_directory() . '/css/layout.css');
    echo file_get_contents(get_template_directory() . '/css/header.css');
    echo file_get_contents(get_template_directory() . '/css/nav.css');
    echo file_get_contents(get_template_directory() . '/css/observer-list.css');

    if (is_front_page() || is_home() || is_archive() || is_category() || is_page('latest')) {
      echo file_get_contents(get_template_directory() . '/css/home-trending.css');
    }
    ?>
  </style>
  <?php if (get_field('paid_content')) { ?>
    <style>
      div.ad-billboard-top {
        height: 0px !important;
        transition: none !important;
      }
    </style>
  <?php } ?>
</head>

<body <?php body_class(); ?> id="body">

  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQC6WRH" height="0" width="0"
      style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->

  <?php
  $top_menu_html = '';
  $number_of_menu_items = 8;
  $top_menu_items = [];
  $my_sub_lists = [];
  $exclude_cats = [288366, 303097, 288238, 284732]; // Competitions, Evergreen, News, Features
  
  if (is_user_logged_in()):
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $my_subs = $wpdb->get_results("SELECT s.list_id FROM {$wpdb->prefix}observer_subs s JOIN {$wpdb->prefix}observer_lists l ON l.id = s.list_id WHERE user_id = '{$user_id}' AND s.status = 'subscribed' AND l.related_site='thebrag.com' ");
    $my_sub_lists = wp_list_pluck($my_subs, 'list_id');
  endif;

  ob_start();

  if (isset($my_sub_lists) && !empty($my_sub_lists)):
    $menu_cats = get_categories(
      array(
        // 'parent' => null,
        // 'hide_empty' => '0',
        'orderby' => 'count',
        'order' => 'DESC',
        'exclude' => array_merge($exclude_cats, [305297]),
        'meta_query' => array(
          array(
            'key' => 'observer-topic',
            'value' => $my_sub_lists,
            'compare' => 'IN',
          )
        )
      )
    );
    $menu_cats_ids = wp_list_pluck($menu_cats, 'term_id');

    foreach ($menu_cats as $cat):
      array_push($top_menu_items, [
        'link' => get_category_link($cat),
        'text' => $cat->name,
      ]);
    endforeach;

    if (count($menu_cats) < $number_of_menu_items):
      $menu_cats2 = get_categories(
        array(
          'parent' => null,
          'orderby' => 'count',
          'order' => 'DESC',
          'exclude' => array_merge($exclude_cats, $menu_cats_ids, [305297]),
          'number' => $number_of_menu_items - count($menu_cats)
        )
      );

      // echo '<pre>'; print_r($menu_cats2);exit;
      foreach ($menu_cats2 as $cat):
        array_push($top_menu_items, [
          'link' => get_category_link($cat),
          'text' => '<span class="plus"><img src="' . ICONS_URL . 'plus.svg" width="16" height="16" alt="+"></span>
          <span class="plus-hover"><img src="' . ICONS_URL . 'plus-color.svg" width="16" height="16" alt="+"></span>
          <span class="text-muted">' . $cat->name . '</span>',
          'class' => 'secondary',
        ]);
      endforeach;
    endif;

    // Internet Culture doesn't have any observer topic associated, so adding it manually
    array_push($top_menu_items, [
      'link' => home_url('/net-culture/'),
      'text' => 'Internet Culture',
    ]);
  else: // Show all categories
    $menu_cats = get_categories(
      array(
        'parent' => null,
        'orderby' => 'count',
        'order' => 'DESC',
        'exclude' => array_merge($exclude_cats, [305297]),
      )
    );

    // echo '<pre>'; print_r($menu_cats2);exit;
    foreach ($menu_cats as $cat):
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
    <ul id="menu_main" class="nav flex-column flex-md-row">
      <?php
      foreach ($top_menu_items as $i => $top_menu_item):
        if ($i < $number_of_menu_items):
          ?>
          <li class="<?php echo isset($top_menu_item['class']) ? $top_menu_item['class'] : ''; ?>">
            <a href="<?php echo $top_menu_item['link']; ?>">
              <?php echo $top_menu_item['text']; ?>
            </a>
          </li>
          <?php
        else:
          if ($i == $number_of_menu_items): ?>
            <li class="menu-more d-flex">
              <span class="arrow-down"><img src="<?php echo ICONS_URL; ?>icon_arrow-down-tb.svg" width="10" height="9"
                  alt="▼"></span>
              <ul>
              <?php endif; // $number_of_menu_items th menu item 
          ?>
              <li class="<?php echo isset($top_menu_item['class']) ? $top_menu_item['class'] : ''; ?>">
                <a href="<?php echo $top_menu_item['link']; ?>">
                  <?php echo $top_menu_item['text']; ?>
                </a>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php if (count($top_menu_items) > $number_of_menu_items): ?>
          </ul>
        </li>
      <?php endif; ?>
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
  ?>

  <div id="fb-root"></div>
  <script>
    (function (d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s);
      js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.9&appId=1950298011866227";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  </script>

  <header class="fixed-top pb-1 py-md-0">

    <div class="d-flex d-md-none justify-content-between header-mobile">
      <div class="d-flex">
        <div class="w-auto d-flex d-md-none mr-1 pl-1 btn-toggle-slidedown" data-target="mobile-nav-wrap">
          <svg viewBox="0 0 40 30" width="40" height="30" style="width: 24px; height: auto;">
            <rect width="40" height="6" rx="3" style="fill: #fff"></rect>
            <rect y="12" width="40" height="6" rx="3" style="fill: #fff"></rect>
            <rect y="24" width="30" height="6" rx="3" style="fill: #fff"></rect>
          </svg>
        </div>


        <div class="logo-wrap">
          <a href="<?php echo site_url(); ?>"><img src="<?php echo CDN_URL; ?>The-Brag-light.svg" width="140"
              height="25" alt="The Brag" title="The Brag"></a>
        </div>
      </div>

      <div class="d-flex">
        <div class="user-wrap d-flex flex-column justify-content-end pr-1 pr-md-2 w-sm-auto">
          <div class="user-info d-flex flex-row my-1">
            <?php
            if (is_user_logged_in()):
              $current_user = wp_get_current_user();
              $user_info = get_userdata($current_user->ID);
              ?>
              <button class="btn btn-primary btn-observer-top btn-toggle-slidedown d-flex" data-target="observer-list-top"
                style="padding: .15rem .45rem !important">
                <span class="">
                  <img src="<?php echo ICONS_URL; ?>icon_mail.svg" class="btn-img" width="24" height="24" alt="Mail">
                  <img src="<?php echo ICONS_URL; ?>icon_mail-tb.svg" class="btn-img hover" width="24" height="24"
                    alt="Mail">
                </span>
              </button>
              <a href="<?php echo home_url('/profile/'); ?>" class="ml-1 user-name d-flex flex-row btn user text-white"
                style="padding: 0;">
                <?php echo get_avatar($current_user, 24, 'mystery', $user_info->first_name, ['class' => 'rounded-circle']); ?>
              </a>
            <?php else: ?>
              <a href="<?php echo esc_url(wp_login_url($current_url)); ?>" class="text-white btn btn-primary">Login</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div id="mobile-nav-wrap" class="nav-wrap container flex-column p-r" style="display: none;">
      <div id="search-nav-wrap" class="w-100 d-flex flex-column">
        <div id="top-search-wrap-mobile"
          style="position: absolute; top: -.25rem; left: 0; z-index: 10; background: rgba(255,255,255,.75); border-radius: .5rem; width: 320px; transition: .25s left linear; border: 1px solid rgba(255,255,255,.15);">
          <div role="search" method="get" id="searchform" class="searchform d-flex justify-content-center"
            action="<?php echo esc_url(home_url('/')); ?>" style="">
            <div class="btn btn-close-top-search" style="padding: .25rem; margin: auto .25rem; opacity: .5;">
              <img src="<?php echo ICONS_URL; ?>cross-dark.svg" width="16" height="16" alt="<"
                style="width: 8px; height: auto;">
            </div>
            <div style="flex: 1 1 auto;">
              <input type="text" name="s" class="search-field form-control" placeholder="Search..." autocomplete="off"
                aria-label="Search"
                style="padding: .35rem; border: none; border-radius: .5rem; margin-left: .25rem; background-color: transparent; color: #fff;">
            </div>
            <button type="button" class="btn p-2" aria-label="Search"
              style="padding: .25rem .5rem .5rem; background-color: rgba(0,0,0,.5) !important; border-radius: 0; border-radius: .5rem;">
              <img src="<?php echo ICONS_URL; ?>magnifying-glass-dark.svg" width="22" height="22"></button>
          </div>
        </div>

        <div id="nav-primary" class="nav w-100 my-0 flex-fill">
          <?php
          echo $top_menu_html;
          /* wp_nav_menu(array(
            'theme_location' => 'top',
            'menu_id'        => 'menu_main',
            'menu_class' => 'nav flex-column flex-md-row',
            'fallback_cb'   => false,
            'add_li_class'  => 'nav-item',
            'link_class'   => 'nav-link',
            'container' => 'nav',
          )); */
          ?>
        </div>


        <div class="socials-top d-flex d-md-none justify-content-between" style="max-width: 320px; margin: 1rem auto;">
          <a href="https://www.facebook.com/thebragmag" target="_blank" rel="noreferrer"><img
              src="<?php echo ICONS_URL; ?>facebook.svg" width="32" height="32" alt="Facebook" title="Facebook"></a>
          <a href="https://twitter.com/TheBrag" target="_blank" rel="noreferrer"><img
              src="<?php echo ICONS_URL; ?>twitter.svg" width="32" height="32" alt="Twitter" title="Twitter"></a>
          <a href="https://www.instagram.com/thebragmag/" target="_blank" rel="noreferrer"><img
              src="<?php echo ICONS_URL; ?>instagram.svg" width="32" height="32" alt="Instagram" title="Instagram"></a>
          <a href="https://www.youtube.com/channel/UCcZMmtU74qKN_w4Dd8ZkV6g" target="_blank" rel="noreferrer"><img
              src="<?php echo ICONS_URL; ?>youtube.svg" width="32" height="32" alt="YouTube" title="YouTube"></a>
          <a href="https://thebrag.com/observer/" target="_blank" rel="noreferrer"><img
              src="<?php echo ICONS_URL; ?>icon_mail.svg" width="32" height="32" alt="Observer" title="Observer"></a>
        </div>

      </div>

      <div class="my-2 btn btn-media-top btn-toggle-network-mobile d-flex" style="width: 100%;">
        <span class="brag-media-top"><img src="https://cdn.thebrag.com/tbm/The-Brag-Media-300px-light.png" width="300"
            height="28" alt="The Brag Media" title="The Brag Media" style="width: 280px;" loading="lazy"></span>
        <span class="arrow-down ml-1"><img src="<?php echo ICONS_URL; ?>icon_arrow-down-tb.svg" width="15" height="14"
            alt="▼"></span>
      </div>
      <div id="network-mobile" class="network" style="display: none;">
        <?php get_template_part('template-parts/network'); ?>
      </div>
    </div><!-- Header on mobile -->

    <div class="d-none d-md-block">
      <div class="d-flex justify-content-between container">
        <div class="network-socials-wrap d-none d-md-block">
          <div class="network-socials">
            <div class="btn btn-media-top btn-toggle-slidedown" data-target="network">
              <span class="brag-media-top"><img src="https://cdn.thebrag.com/tbm/The-Brag-Media-300px-light.png"
                  width="130" height="13" alt="The Brag Media" title="The Brag Media" loading="lazy"></span>
              <span class="arrow-down"><img src="<?php echo ICONS_URL; ?>icon_arrow-down-tb.svg" width="10" height="20"
                  alt="▼"></span>
            </div>
            <div class="socials-top d-flex justify-content-between">
              <a href="https://www.facebook.com/thebragmag" target="_blank" rel="noreferrer"><img
                  src="<?php echo ICONS_URL; ?>facebook.svg" width="32" height="32" alt="Facebook" title="Facebook"></a>
              <a href="https://twitter.com/TheBrag" target="_blank" rel="noreferrer"><img
                  src="<?php echo ICONS_URL; ?>twitter.svg" width="32" height="32" alt="Twitter" title="Twitter"></a>
              <a href="https://www.instagram.com/thebragmag/" target="_blank" rel="noreferrer"><img
                  src="<?php echo ICONS_URL; ?>instagram.svg" width="32" height="32" alt="Instagram"
                  title="Instagram"></a>
              <a href="https://www.youtube.com/channel/UCcZMmtU74qKN_w4Dd8ZkV6g" target="_blank" rel="noreferrer"><img
                  src="<?php echo ICONS_URL; ?>youtube.svg" width="32" height="32" alt="YouTube" title="YouTube"></a>
              <a href="https://thebrag.com/observer/" target="_blank" rel="noreferrer"><img
                  src="<?php echo ICONS_URL; ?>icon_mail.svg" width="32" height="32" alt="Observer"
                  title="Observer"></a>
            </div>
          </div><!-- .network-socials.hide-m -->
        </div><!-- .network-socials-wrap -->

        <div class="d-flex">
          <div class="logo-wrap">
            <a href="<?php echo site_url(); ?>"><img src="<?php echo CDN_URL; ?>The-Brag_combo-light.svg" width="200"
                height="36" alt="The Brag" title="The Brag" loading="lazy"></a>
          </div>
        </div>
        <div class="user-wrap d-flex flex-column justify-content-end pr-1 pr-md-2 w-sm-auto">
          <div class="user-info d-none d-md-flex flex-row mb-1">
            <?php
            if (is_user_logged_in()):
              ?>
              <a href="<?php echo home_url('/profile/'); ?>" class="user-name d-flex flex-row btn user text-white"
                style="padding: 0;">
                <?php echo get_avatar($current_user, 24, 'mystery', $user_info->first_name, ['class' => 'rounded-circle']); ?>
                <!-- <span class="ml-1"><?php // echo $user_info->first_name != '' ? $user_info->first_name : 'My profile'; 
                  ?></span> -->
              </a>
            <?php else: ?>
              <a href="<?php echo esc_url(wp_login_url($current_url)); ?>" class="text-white"
                style="font-size: 0.9rem;">Login / Signup</a>
            <?php endif; ?>
          </div>
          <button class="btn btn-primary btn-observer-top btn-toggle-slidedown d-flex" data-target="observer-list-top">
            <span>Pick Your Niche</span>
            <span class="ml-1 d-xs-none" style="margin-top: -1px;">
              <img src="<?php echo ICONS_URL; ?>icon_mail.svg" class="btn-img" width="24" height="24" alt="Mail">
              <img src="<?php echo ICONS_URL; ?>icon_mail-tb.svg" class="btn-img hover" width="24" height="24"
                alt="Mail">
            </span>
          </button>
        </div>
      </div>

      <div class="nav-wrap container d-md-flex flex-column flex-md-row p-r">
        <div id="top-search-wrap"
          style="position: absolute; top: -.25rem; left: 0; z-index: 10; background: #fff; border-radius: .5rem; width: 320px; transition: .25s left linear; box-shadow: 15px 0 10px #000; display: none;">
          <div role="search" id="searchform" class="searchform d-flex justify-content-center" style="">
            <div class="btn btn-close-top-search" style="padding: .25rem; margin: auto .25rem; opacity: .5;">
              <img src="<?php echo ICONS_URL; ?>cross-dark.svg" width="16" height="16" alt="<"
                style="width: 8px; height: auto;">
            </div>
            <div style="flex: 1 1 auto;">
              <input type="text" name="s" id="s" class="search-field form-control" placeholder="Search..."
                autocomplete="off" aria-label="Search"
                style="padding: .35rem; border: none; border-radius: .5rem; margin-left: .25rem;">
            </div>
            <button type="button" class="btn p-2" aria-label="Search"
              style="padding: .25rem .5rem .5rem; background-color: #fff !important; border-radius: 0; border-radius: .5rem;">
              <img src="<?php echo ICONS_URL; ?>magnifying-glass-dark.svg" width="22" height="22"></button>
          </div>
        </div>
        <div class="btn d-block d-md-none btn-close-menu">
          <img src="<?php echo ICONS_URL; ?>chevron-thin-left.svg" width="24" height="24" alt="<">
        </div>
        <div class="d-none d-md-flex btn-open-top-search mt-2 mt-md-0" style="cursor: pointer;">
          <img src="<?php echo ICONS_URL; ?>magnifying-glass.svg" width="24" height="24">
        </div>

        <div id="nav-primary" class="nav w-100 my-0">
          <?php
          echo $top_menu_html;
          ?>
        </div>
        <div class="socials-top d-flex d-md-none justify-content-between">
          <a href="https://www.facebook.com/thebragmag" target="_blank" rel="noreferrer"><img
              src="<?php echo ICONS_URL; ?>facebook.svg" width="32" height="32" alt="Facebook" title="Facebook"></a>
          <a href="https://twitter.com/TheBrag" target="_blank" rel="noreferrer"><img
              src="<?php echo ICONS_URL; ?>twitter.svg" width="32" height="32" alt="Twitter" title="Twitter"></a>
          <a href="https://www.instagram.com/thebragmag/" target="_blank" rel="noreferrer"><img
              src="<?php echo ICONS_URL; ?>instagram.svg" width="32" height="32" alt="Instagram" title="Instagram"></a>
          <a href="https://www.youtube.com/channel/UCcZMmtU74qKN_w4Dd8ZkV6g" target="_blank" rel="noreferrer"><img
              src="<?php echo ICONS_URL; ?>youtube.svg" width="32" height="32" alt="YouTube" title="YouTube"></a>
          <a href="https://thebrag.com/observer/" target="_blank" rel="noreferrer"><img
              src="<?php echo ICONS_URL; ?>icon_mail.svg" width="32" height="32" alt="Observer" title="Observer"></a>
        </div>
      </div>
    </div>
  </header>

  <main>
    <div class="content container">