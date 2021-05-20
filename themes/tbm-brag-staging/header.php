<?php
$page_template = get_page_template_slug();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.png" />

  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="profile" href="http://gmpg.org/xfn/11">

  <meta name="google-site-verification" content="Tf8gbZdF2WOW_R5JIuceGcMuqUNy7TAvdrYKaeoLP5I" />
  <meta name="msvalidate.01" content="E0857D4C8CDAF55341D0839493BA8129" />
  <meta property="fb:app_id" content="1950298011866227" />
  <meta property="fb:pages" content="145692175443937" />

  <meta name="theme-color" content="#130f40">

  <?php if (is_single()) {
    $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
  ?>
    <meta property="og:title" content="<?php the_title(); ?>" />
    <meta property="og:image" content="<?php if (has_post_thumbnail()) {
                                          echo $src[0];
                                        } ?>" />
    <meta property="og:type" content="article" />
    <meta property="og:site_name" content="The Brag" />
    <meta property="og:url" content="<?php echo get_permalink(); ?>" />

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@TheBrag">
    <meta name="twitter:title" content="<?php the_title(); ?>">
    <meta name="twitter:image" content="<?php if (has_post_thumbnail()) {
                                          echo $src[0];
                                        } ?>">

  <?php } // If single 
  ?>

  <?php if (strpos($_SERVER['REQUEST_URI'], '/gigs/') !== false) : ?>
    <link rel="canonical" href="<?php echo home_url() . $_SERVER['REQUEST_URI']; ?>" />
  <?php endif; ?>


  <link rel="dns-prefetch" href="https://cdn.onesignal.com/">
  <link rel="dns-prefetch" href="https://www.googletagservices.com/">
  <link rel="dns-prefetch" href="//cdn.publift.com/">
  <link rel="dns-prefetch" href="https://idsync.rlcdn.com/">
  <link rel="dns-prefetch" href="https://x.bidswitch.net/">
  <link rel="dns-prefetch" href="https://ad.yieldmanager.com/">
  <link rel="dns-prefetch" href="https://cm.g.doubleclick.net/">
  <link rel="dns-prefetch" href="https://dpm.demdex.net/">
  <link rel="dns-prefetch" href="https://fw.adsafeprotected.com/">
  <link rel="dns-prefetch" href="https://www.googletagmanager.com">
  <link rel="dns-prefetch" href="https://tpc.googlesyndication.com">
  <link rel="dns-prefetch" href="https://adservice.google.com">
  <link rel="dns-prefetch" href="https://s.ytimg.com">
  <link rel="dns-prefetch" href="https://adservice.google.com.au">
  <link rel="dns-prefetch" href="https://pagead2.googlesyndication.com">
  <link rel="dns-prefetch" href="https://securepubads.g.doubleclick.net">
  <link rel="dns-prefetch" href="https://fonts.googleapis.com">
  <link rel="dns-prefetch" href="https://www.google-analytics.com">
  <link rel="dns-prefetch" href="https://connect.facebook.net">
  <link rel="dns-prefetch" href="https://bs.serving-sys.com">
  <link rel="dns-prefetch" href="https://bid.g.doubleclick.net">
  <link rel="dns-prefetch" href="https://gum.criteo.com">
  <link rel="dns-prefetch" href="https://sc.iasds01.com">
  <link rel="dns-prefetch" href="https://dt.adsafeprotected.com">
  <link rel="dns-prefetch" href="https://googleads.g.doubleclick.net">
  <link rel="dns-prefetch" href="https://secure-ds.serving-sys.com">

  <link rel="manifest" href="/manifest.json">

  <?php
  if (is_single()) :
    if (get_field('author')) {
      $author = get_field('author');
    } else if (get_field('Author')) {
      $author = get_field('Author');
    } else {
      if ('' != get_the_author_meta('first_name', $post->post_author) && '' != get_the_author_meta('last_name', $post->post_author)) {
        $author = get_the_author_meta('first_name', $post->post_author) . ' ' . get_the_author_meta('last_name', $post->post_author);
      } else {
        $author = get_the_author_meta('display_name', $post->post_author);
      }
    }

    $categories = get_the_category(get_the_ID());
    $CategoryCD = '';
    if ($categories) :
      foreach ($categories as $category) :
        $CategoryCD .= $category->slug . ' ';
      endforeach; // For Each Category
    endif; // If there are categories for the post

    $tags = get_the_tags(get_the_ID());
    $TagsCD = '';
    if ($tags) :
      foreach ($tags as $tag) :
        $TagsCD .= $tag->slug . ' ';
      endforeach; // For Each Tag
    endif; // If there are tags for the post
  ?>
    <script>
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({
        'AuthorCD': '<?php echo $author; ?>',
        'CategoryCD': '<?php echo $CategoryCD; ?>',
        'TagsCD': '<?php echo $TagsCD; ?>',
        'PubdateCD': '<?php echo get_the_time('M d, Y', get_the_ID()); ?>'
      });
    </script>

    <?php
    if (get_field('page_background_colour')) : ?>
      <style>
        body {
          background-color: <?php echo get_field('page_background_colour'); ?> !important;
        }
      </style>
    <?php endif; ?>
  <?php endif; // If it's a Single Post 
  ?>

  <!-- Google Tag Manager -->
  <script>
    (function(w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({
        'gtm.start': new Date().getTime(),
        event: 'gtm.js'
      });
      var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s),
        dl = l != 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src =
        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-TQC6WRH');
  </script>
  <!-- End Google Tag Manager -->

  <?php wp_head(); ?>

  <?php
  if (is_single()) :
    if (get_field('fb_pixel')) :
      echo get_field('fb_pixel');
    endif;
  endif;
  ?>
  <script type="text/javascript">
    !(function(o, n, t) {
      t = o.createElement(n), o = o.getElementsByTagName(n)[0], t.async = 1, t.src = "https://guiltlessbasketball.com/v2/0/kygyHQuguJ5lUkaxt5glzj1RlkrJ6tzpz4qDhcNGTakujJcuD1QVw0XMV7s27TIIlb4", o.parentNode.insertBefore(t, o)
    })(document, "script"), (function(o, n) {
      o[n] = o[n] || function() {
        (o[n].q = o[n].q || []).push(arguments)
      }
    })(window, "admiral");
    !(function(n, e, r, t) {
      function o() {
        if ((function o(t) {
            try {
              return (t = localStorage.getItem("v4ac1eiZr0")) && 0 < t.split(",")[4]
            } catch (n) {}
            return !1
          })()) {
          var t = n[e].pubads();
          typeof t.setTargeting === r && t.setTargeting("admiral-engaged", "true")
        }
      }(t = n[e] = n[e] || {}).cmd = t.cmd || [], typeof t.pubads === r ? o() : typeof t.cmd.unshift === r ? t.cmd.unshift(o) : t.cmd.push(o)
    })(window, "googletag", "function");
  </script>
</head>

<body <?php body_class(); ?> id="body">

  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQC6WRH" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->

  <div id="fb-root"></div>
  <script>
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s);
      js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.9&appId=1950298011866227";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  </script>

  <header class="fixed-top pb-1 py-md-0">
    <div class="btn btn-media-top btn-toggle-slidedown d-flex d-md-none mb-1 mx-auto" data-target="network">
      <span class="brag-media-top"><img src="<?php echo ICONS_URL; ?>The-Brag-Media-150px-light.png"></span>
      <span class="arrow-down"><img src="<?php echo ICONS_URL; ?>triangle-down-color.svg"></span>
    </div>
    <div class="d-flex justify-content-between container">
      <div class="network-socials-wrap d-none d-md-block">
        <div class="network-socials">
          <div class="btn btn-media-top btn-toggle-slidedown" data-target="network">
            <span class="brag-media-top"><img src="<?php echo ICONS_URL; ?>The-Brag-Media-150px-light.png"></span>
            <span class="arrow-down"><img src="<?php echo ICONS_URL; ?>triangle-down-color.svg"></span>
          </div>
          <div class="socials-top d-flex justify-content-between">
            <a href="#"><img src="<?php echo ICONS_URL; ?>facebook.svg"></a>
            <a href="#"><img src="<?php echo ICONS_URL; ?>twitter.svg"></a>
            <a href="#"><img src="<?php echo ICONS_URL; ?>instagram.svg"></a>
            <a href="#"><img src="<?php echo ICONS_URL; ?>youtube.svg"></a>
            <a href="#"><img src="<?php echo ICONS_URL; ?>mail.svg"></a>
          </div>
        </div><!-- .network-socials.hide-m -->
      </div><!-- .network-socials-wrap -->

      <div class="w-auto d-md-none pl-1 btn-toggle-menu">
        <svg viewBox="0 0 40 30" width="40" height="30" style="width: 24px; height: auto;">
          <rect width="40" height="6" rx="3" style="fill: #fff"></rect>
          <rect y="12" width="40" height="6" rx="3" style="fill: #fff"></rect>
          <rect y="24" width="30" height="6" rx="3" style="fill: #fff"></rect>
        </svg>
      </div>

      <div class="logo-wrap">
        <a href="<?php echo site_url(); ?>"><img src="<?php echo ICONS_URL; ?>The-Brag_combo-white.svg"></a>
      </div>
      <div class="user-wrap d-flex flex-column justify-content-end pr-1 pr-md-2 w-sm-auto">
        <div class="user-info d-none d-md-flex flex-row my-1">
          <?php
          if (is_user_logged_in()) :
            $current_user = wp_get_current_user();
            $user_info = get_userdata($current_user->ID);
          ?>
            <div class="user-name d-flex flex-row btn" style="padding: 0;">
              <span><?php echo $user_info->first_name != '' ? $user_info->first_name : 'My profile'; ?></span>
              <div class="arrow-down"><img src="<?php echo ICONS_URL; ?>triangle-down.svg"></div>
            </div>
          <?php else : ?>
            <a href="<?php echo wp_login_url(); ?>" class="text-white">Login / Signup</a>
          <?php endif; ?>
        </div>
        <button class="btn btn-primary btn-observer-top btn-toggle-slidedown d-flex" data-target="observer-list-top">
          <span>Pick Your Niche</span>
          <span class="ml-1">
            <img src="<?php echo ICONS_URL; ?>mail.svg" class="btn-img">
            <img src="<?php echo ICONS_URL; ?>mail-color.svg" class="btn-img hover">
          </span>
        </button>
      </div>
    </div>

    <div class="nav-wrap container d-md-flex flex-column flex-md-row">
      <div class="btn d-block d-md-none btn-close-menu">
        <img src="<?php echo ICONS_URL; ?>chevron-thin-left.svg" width="24" height="24">
      </div>
      <div class="search-wrap mt-2 mt-md-0">
        <img src="<?php echo ICONS_URL; ?>magnifying-glass.svg" width="24" height="24">
      </div>
      <div id="nav-primary" class="nav w-100 my-0">
        <?php
        wp_nav_menu(array(
          'theme_location' => 'top',
          'menu_id'        => 'menu_main-love',
          'menu_class' => 'nav flex-column flex-md-row',
          'fallback_cb'   => false,
          'add_li_class'  => 'nav-item',
          'link_class'   => 'nav-link',
          'container' => 'nav',
        ));
        ?>
      </div>
    </div>
  </header>

  <main>
    <div class="content">