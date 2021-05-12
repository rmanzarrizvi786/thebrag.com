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
  <!-- <meta property="fb:pages" content="145692175443937" /> -->

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

    $categories = get_the_terms(get_the_ID(), 'dad-category');
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
    /*
            @font-face{font-family:'FontAwesome';src:url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.eot?v=4.7.0');src:url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.eot?#iefix&v=4.7.0') format('embedded-opentype'),url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.woff2?v=4.7.0') format('woff2'),url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.woff?v=4.7.0') format('woff'),url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.ttf?v=4.7.0') format('truetype'),url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.svg?v=4.7.0#fontawesomeregular') format('svg');}
            .fa{display:inline-block;font:normal normal normal 14px/1 FontAwesome;font-size:inherit;text-rendering:auto}
            .fa-lg{font-size:1.33333333em;line-height:.75em;vertical-align:-15%}
            .fa-search:before{content:"\f002"}
            .fa-remove:before,
            .fa-close:before,
            .fa-times:before {
              content: "\f00d";
            }
            .fa-close:before{content:"\f00d"}
            .fa-twitter:before{content:"\f099"}
            .fa-twitter-square:before { content: "\f081"; }
            .fa-facebook:before{content:"\f09a"}
            .fa-facebook-square:before { content: "\f082"; }
            .fa-linkedin-square:before { content: "\f08c"; }
            .fa-instagram:before { content: "\f16d"; }
            .fa-bars:before{content:"\f0c9"}
            .fa-remove:before,.fa-close:before,.fa-times:before{content:"\f00d"}
            .fa-caret-down:before{content:"\f0d7"}
            .fa-youtube:before{content:"\f167"}
            .fa-envelope:before{content:"\f0e0"}
            .fa-reddit-alien:before { content: "\f281"; }
            .fa-whatsapp:before { content: "\f232"; }
            .fa-check-square:before { content: "\f14a"; }
            .fa-user:before { content: "\f007"; }
            .fa-sign-in:before {
              content: "\f090";
            }
            .fa-sign-out:before {
              content: "\f08b";
            }

            .fa-thumbs-o-up:before {
              content: "\f087";
            }
            .fa-thumb-tack:before {
              content: "\f08d";
            }
            .fa-thumbs-up:before {
              content: "\f164";
            }
            .fa-heart:before {
              content: "\f004";
            }
            .fa-heart-o:before {
              content: "\f08a";
            }
            .fa-caret-down:before {
              content: "\f0d7";
            }
            .fa-caret-up:before {
              content: "\f0d8";
            }
            */

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

    <?php endif; ?>.post-thumbnail img,
    .post-content img {
      width: 100%;
      height: auto;
    }
  </style>

  <?php wp_head(); ?>

  <!-- <script async src="//cdn.publift.com/fuse/tag/2/1125/fuse.js" defer></script> -->
  <?php
  // Add Google Ads tags only if direct ads are ON
  if (get_option('serve_direct_ads') == 1) :
    include(get_template_directory() . '/partials/ads-direct-js.php');
  endif; // If direct ads are ON 
  ?>
  <script>
    var propertag = propertag || {};
    propertag.cmd = propertag.cmd || [];
    (function() {
      var pm = document.createElement('script');
      pm.async = true;
      pm.type = 'text/javascript';
      var is_ssl = 'https:' == document.location.protocol;
      pm.src = (is_ssl ? 'https:' : 'http:') + '//global.proper.io/thebrag.min.js';
      var node = document.getElementsByTagName('script')[0];
      node.parentNode.insertBefore(pm, node);
    })();
  </script>

  <?php if (is_single()) : ?>
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

  <?php
  $logo_link = get_post_type_archive_link('dad');
  $logo_url = get_template_directory_uri() . '/images/TheBragLOGOwhiteNOSHIELD.svg';
  $logo_alt = 'The Brag';
  // include( get_template_directory() . '/partials/menu-mobile.php' );
  ?>
  <?php $logo_link = !isset($logo_link) ? site_url() : $logo_link; ?>
  <div id="mobile-menu" class="d-flex flex-column">
    <div style="position: absolute; top: 0; left: 0;">
      <button class="navbar-toggler" type="button" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="outline: none;">
        <div class="navbar-button-bars text-white"><i class="fas fa-times"></i></div>
      </button>
    </div>
    <div class="brand my-3">
      <a class="header-logo" href="<?php echo site_url(); ?>">
        <img src="<?php echo $logo_url; ?>" alt="<?php echo $logo_alt; ?>" class="img-fluid" style="max-width: 150px;">
      </a>
    </div>
    <nav aria-label="Menu Social" class="mb-3">
      <ul class="nav flex-row align-items-center justify-content-center">
        <li class="nav-item"><a target="_blank" href="https://www.facebook.com/thebragmag" class="nav-link px-2 text-white" aria-label="Facebook"><i class="fab fa-facebook-f fa-lg" aria-hidden=true></i></a></li>
        <li class="nav-item"><a target="_blank" href="https://twitter.com/TheBrag" class="nav-link px-2 text-white" aria-label="Twitter"><i class="fab fa-twitter fa-lg" aria-hidden=true></i></a></li>
        <li class="nav-item"><a target="_blank" href="https://www.instagram.com/thebragmag/" class="nav-link px-2 text-white" aria-label="Instagram"><i class="fab fa-instagram fa-lg" aria-hidden=true></i></a></li>
        <li class="nav-item"><a target="_blank" href="https://www.youtube.com/channel/UCcZMmtU74qKN_w4Dd8ZkV6g" class="nav-link px-2 text-white" aria-label="YouTube"><i class="fab fa-youtube fa-lg" aria-hidden=true></i></a></li>
        <li class="nav-item"><a target="_blank" href="<?php echo home_url('/observer/'); ?>" class="nav-link px-2 text-white l-ico-observer"><i class="fas fa-envelope fa-lg" aria-hidden=true></i></a></li>
      </ul>
    </nav>
    <form role="search" method="get" id="searchform-mobile" class="searchform form-inline justify-content-center my-1" action="<?php echo esc_url(home_url('/')); ?>" style="">
      <div><input type="text" name="s" class="search-field form-control" placeholder="Search..." autocomplete="off" aria-label="Search"></div>
      <button type="submit" class="btn" aria-label="Search"><i class="fa fa-search" aria-hidden="true"></i></button>
    </form>
    <div id="menu" class="mt-3" style="width: 300px; margin: auto;">
      <h4 class="mt-2 text-white menu-genres-header pb-2" style="margin-left: .7rem;">What do you love?</h4>
      <?php
      wp_nav_menu(array(
        'theme_location' => 'top-what-you-love',
        'menu_id'        => 'menu_main-love',
        'menu_class' => 'nav flex-column',
        'fallback_cb'   => false,
        'add_li_class'  => 'nav-item',
        'link_class'   => 'nav-link'
      ));
      ?>
    </div>
    <div id="menu-genres" class="my-3" style="width: 300px; margin: auto;">
      <h4 class="mt-2 text-white menu-genres-header pb-2" style="margin-left: .7rem;">Check this out</h4>
      <?php
      wp_nav_menu(array(
        'theme_location' => 'top-check-this-out',
        'menu_id'        => 'menu_main-checkout',
        'menu_class' => 'nav flex-column',
        'fallback_cb'   => false,
        'add_li_class'  => 'nav-item',
        'link_class'   => 'nav-link'
      ));
      ?>
    </div>

    <div class="mb-3 pt-3" style="width: 300px; margin: auto; border-top: 1px solid #555;">
      <a href="<?php echo home_url('/observer/'); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/observer/mrec-600px.jpg"></a>
    </div>

    <?php if (0) : ?>
      <div class="mb-3 pt-3" style="width: 300px; margin: auto; border-top: 1px solid #555;">
        <h5 class="text-left text-white">Get The Brag Dad to your inbox daily</h2>
          <p class="text-white">Stay on top of the most important music news and interviews each day, delivered free, direct to your inbox.</p>

          <div class="newsletter-form">
            <form action="https://thebrag.us1.list-manage.com/subscribe/post?u=a9d74bfce08ba307bfa8b9c78&amp;id=f0eedde184" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
              <input type="text" name="EMAIL" required class="required email input-email form-control" placeholder="Email address">
              <input type="submit" value="SUBSCRIBE" required name="subscribe" class="button btn btn-dark form-control mt-2">

              <div id="mce-responses" class="clear">
                <div class="response" id="mce-error-response" style="display:none"></div>
                <div class="response" id="mce-success-response" style="display:none"></div>
              </div> <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
              <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_a9d74bfce08ba307bfa8b9c78_f0eedde184" tabindex="-1" value=""></div>
            </form>
          </div>
      </div>
    <?php endif; ?>
  </div>

  <div id="main" class="wrap">
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

        <div id="masthead" class="navbar navbar-inverse navbar-fixed-top hidden-print">
          <div class="container" style="position: relative; padding-left: 0; padding-right: 0;">
            <div class="col-1 col-md-4 px-0">
              <button class="navbar-toggler" type="button" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="outline: none;">
                <div class="navbar-button-bars"><i class="fa fa-bars"></i></div>
              </button>
            </div>
            <div class="brand col-4 text-center">
              <a class="header-logo" href="<?php echo $logo_link; ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/the-brag-dad-400px.jpg" alt="The Brag Dad" class="img-fluid" width="150" height="37">
              </a>
            </div>

            <div class="col-7 col-md-4 text-right">
              <div class="d-inline float-right">
                <?php
                global $current_user;
                wp_get_current_user();
                if (is_user_logged_in()) :  ?>
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
                      <a class="dropdown-item" href="<?php echo home_url('logout'); ?>">Logout</a>
                    </div>
                  </div>
                <?php else : ?>
                  <a href="<?php echo home_url('/login/'); ?>" class="btn btn-sm btn-dark rounded">Login / Signup</a>
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

    <?php if (0 && !is_mobile()) : ?>
      <div class="text-center sticky-ad-bottom d-md-block">
        <?php echo render_ad_tag('leaderboard'); ?>
      </div>
    <?php endif; // If NOT mobile  
    ?>

    <div id="brag_inskin">
      <script>
        googletag.cmd.push(function() {
          googletag.display('brag_inskin');
        });
      </script>
    </div>

    <div id="content" class="py-2">