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
  <!--<link rel="dns-prefetch" href="https://certify-js.alexametrics.com">-->
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

  <style>
    *,
    ::after,
    ::before {
      box-sizing: border-box
    }

    html {
      font-family: sans-serif;
      line-height: 1.15;
      -webkit-text-size-adjust: 100%
    }

    article,
    nav {
      display: block
    }

    body {
      margin: 0;
      font-size: 1rem;
      font-weight: 400;
      line-height: 1.5;
      color: #212529;
      text-align: left;
      background-color: #fff
    }

    h1,
    h2,
    h3 {
      margin-top: 0;
      margin-bottom: .5rem
    }

    p {
      margin-top: 0;
      margin-bottom: 1rem
    }

    ul {
      margin-top: 0;
      margin-bottom: 1rem
    }

    ul ul {
      margin-bottom: 0
    }

    a {
      color: #007bff;
      text-decoration: none;
      background-color: transparent
    }

    @media (min-width:768px) {
      .wrap.quiz-wrap {
        max-width: 730px;
      }
    }

    <?php if (is_page_template('single-template-quiz.php')) : ?>body {
      background: #555 !important;
    }

    <?php endif; ?>.sticky-ad-bottom .proper-ad-unit .inner-wrapper {
      display: none;
    }

    .teads-inread {
      margin: 1rem auto !important;
    }

    @media (min-width:992px) {
      .sticky-rail {
        position: sticky;
        top: 80px;
      }
    }

    /* #page {
      display: none;
    }

    #page-loader {
      position: fixed;
      left: 0px;
      top: 0px;
      width: 100vw;
      height: 100vh;
      z-index: 9999;
      background: #fff;
    } */

    .double-bounce1,
    .double-bounce2 {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background-color: #333;
      opacity: .6;
      position: absolute;
      top: 0;
      left: 0;
      animation: sk-bounce 2.0s infinite ease-in-out;
    }

    .double-bounce2 {
      animation-delay: -1.0s
    }

    @keyframes sk-bounce {

      0%,
      100% {
        transform: scale(0.0)
      }

      50% {
        transform: scale(1.0)
      }
    }

    .post-thumbnail img {
      width: 100%;
      height: auto;
    }
  </style>

  <?php wp_head(); ?>

  <?php
  // Add Google Ads tags
  if (!is_page_template('page-templates/brag-observer.php')) :
    include(get_template_directory() . '/partials/ads-direct-js.php');
  endif; // If direct ads are ON 
  ?>

  <?php if (0 && is_single()) : // Disabled on 19 Feb, 2021 ?>
    <script type="text/javascript">
      window._taboola = window._taboola || [];
      _taboola.push({
        article: 'auto'
      });
      ! function(e, f, u, i) {
        if (!document.getElementById(i)) {
          e.async = 1;
          e.src = u;
          e.id = i;
          f.parentNode.insertBefore(e, f);
        }
      }(document.createElement('script'),
        document.getElementsByTagName('script')[0],
        '//cdn.taboola.com/libtrc/thebragmedia-thebrag/loader.js',
        'tb_loader_script');
      if (window.performance && typeof window.performance.mark == 'function') {
        window.performance.mark('tbl_ic');
      }
    </script>
  <?php endif; ?>

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

  <!-- Apester -->
  <!-- <script type="text/javascript" src="https://static.apester.com/js/sdk/latest/apester-sdk.js" async></script> -->


</head>

<body <?php body_class(); ?> id="body">

  <!-- <div id="page-loader">
    <div style="width: 30px; height: 30px; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);">
      <div class="double-bounce1"></div>
      <div class="double-bounce2"></div>
    </div>
  </div> -->

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

  <?php
  $logo_link = site_url();
  $logo_url = get_template_directory_uri() . '/images/TheBragLOGOwhiteNOSHIELD.svg';
  $logo_alt = 'The Brag';
  include(get_template_directory() . '/partials/menu-mobile.php'); ?>

  <div id="main" class="wrap<?php echo $page_template == 'single-template-quiz' ? ' quiz-wrap' : ''; ?>">
    <div id="header-wrap" style="height: auto;">
      <?php // if ( is_mobile() ) : 
      ?>
      <div class="text-center sticky-ad-bottom py-2 pb-md-0 pt-md-0">
        <?php echo render_ad_tag('leaderboard'); ?>
      </div>
      <?php // endif; // If mobile 
      ?>
      <div id="header" class="container bg-white p-0">
        <?php include(get_template_directory() . '/partials/menu-network.php'); ?>

        <div id="masthead" class="navbar navbar-inverse navbar-fixed-top hidden-print px-0">
          <div class="container" style="position: relative; padding-left: 0; padding-right: 0;">
            <div class="col-1 col-md-4 px-0">
              <button class="navbar-toggler" type="button" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="outline: none;">
                <div class="navbar-button-bars"><i class="fa fa-bars"></i></div>
              </button>
            </div>
            <div class="brand col-4 text-center">
              <a class="header-logo" href="<?php echo $logo_link; ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/TheBragLOGOblackNOSHIELD.svg" alt="The Brag" class="img-fluid" width="150" height="37">
              </a>
            </div>

            <div class="col-7 col-md-4 text-right">
              <div class="d-inline float-right">
                <?php
                global $current_user;
                wp_get_current_user();
                if (is_user_logged_in()) :
                  $profile_complete_percentage = 0;
                  $current_user = wp_get_current_user();

                  if (get_user_meta($current_user->ID, 'first_name', true))
                    $profile_complete_percentage += 20;

                  if (get_user_meta($current_user->ID, 'last_name', true))
                    $profile_complete_percentage += 20;

                  if (get_user_meta($current_user->ID, 'state', true))
                    $profile_complete_percentage += 20;

                  if (get_user_meta($current_user->ID, 'birthday', true))
                    $profile_complete_percentage += 20;

                  if (get_user_meta($current_user->ID, 'gender', true))
                    $profile_complete_percentage += 20;

                  if ($profile_complete_percentage < 20) {
                    $profile_complete_class = 'badge-danger';
                  } else if ($profile_complete_percentage <= 40) {
                    $profile_complete_class = 'badge-warning';
                  } else if ($profile_complete_percentage <= 60) {
                    $profile_complete_class = 'badge-info';
                  } else if ($profile_complete_percentage <= 80) {
                    $profile_complete_class = 'badge-primary';
                  } else if ($profile_complete_percentage <= 100) {
                    $profile_complete_class = 'badge-success';
                  }
                ?>
                  <div class="dropdown float-right2">
                    <button class="btn btn-dark dropdown-toggle rounded" type="button" id="dropdownUserButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fa fa-user"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownUserButton" style="left: auto; right: 0;">
                      <a class="dropdown-item" href="<?php echo home_url('/observer-subscriptions/'); ?>">My subscriptions</a>
                      <a class="dropdown-item" href="<?php echo home_url('/observer/magazine-subscriptions/'); ?>">Magazine subscriptions</a>
                      <a class="dropdown-item" href="<?php echo home_url('/observer/competitions/'); ?>">Competitions</a>
                      <a class="dropdown-item" href="<?php echo home_url('/profile/'); ?>">Profile <span class="badge <?php echo $profile_complete_class; ?>"><?php echo $profile_complete_percentage; ?>% complete</span></a>
                      <a class="dropdown-item" href="<?php echo home_url('/observer/refer-a-friend/'); ?>">Refer a friend and earn</a>
                      <a class="dropdown-item" href="<?php echo home_url('/change-password/'); ?>">Change password</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="<?php echo wp_logout_url(); ?>">Logout</a>
                    </div>
                  </div>
                <?php else : ?>
                  <a href="<?php echo wp_login_url(); ?>" class="btn btn-sm btn-dark rounded">Login / Signup</a>
                  <!-- <button type="button" class="btn btn-sm btn-dark rounded btn-login" data-target="#loginModal">
                    Login / Signup
                  </button> -->
                <?php endif; ?>
              </div>
            </div>
          </div><!-- / #masthead .container -->
        </div><!-- / #masthead -->

        <?php if (0 && 'post' == get_post_type()) : ?>
          <div class="progress-bar">&nbsp;</div>
        <?php endif; ?>
      </div><!-- /#header -->
    </div><!-- /#header-wrap -->

    <div id="content" class="py-2">