<?php
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
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="msapplication-config" content="/icons/browserconfig.xml">
  <meta name="theme-color" content="#130f40">

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

  <?php if (strpos($_SERVER['REQUEST_URI'], '/gigs/') !== false) : ?>
    <link rel="canonical" href="<?php echo home_url() . $_SERVER['REQUEST_URI']; ?>" />
  <?php endif; ?>

  <?php
  if (is_single()) :
    if (has_post_thumbnail()) {
      $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
      if (isset($src[0])) {
  ?>
        <meta property="og:image" content="<?php echo $src[0]; ?>" />
        <meta name="twitter:image" content="<?php echo $src[0]; ?>">
        <link rel="preconnect" href="<?php echo $src[0]; ?>">
    <?php
      } // If full featured image src is set
    } // If post featured image is set
    ?>
    <meta property="og:title" content="<?php the_title(); ?>" />

    <meta property="og:type" content="article" />
    <meta property="og:site_name" content="The Brag" />
    <meta property="og:url" content="<?php echo get_permalink(); ?>" />

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@TheBrag">
    <meta name="twitter:title" content="<?php the_title(); ?>">

    <?php
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
  <?php endif; // If page_background_colour field is set
    if (get_field('fb_pixel')) :
      echo get_field('fb_pixel');
    endif; // If fb_pixel field is set


  endif; // If it's a Single Post
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

  <style>
    <?php
    echo file_get_contents(get_template_directory() . '/css/reset.css');
    echo file_get_contents(get_template_directory() . '/css/layout.css');
    echo file_get_contents(get_template_directory() . '/css/header.css');
    echo file_get_contents(get_template_directory() . '/css/nav.css');
    echo file_get_contents(get_template_directory() . '/css/observer-list.css');

    if (is_front_page() || is_home() || is_archive() || is_category()) {
      echo file_get_contents(get_template_directory() . '/css/home-trending.css');
    }
    ?>header .logo-wrap a img {
      width: 300px;
    }

    @media (min-width: 48rem) {
      header .logo-wrap a img {
        width: 300px;
      }
    }
  </style>
  <link rel="stylesheet" id="tbm-css" href="<?php echo get_template_directory_uri(); ?>/css/bragger-client-club.css?v=<?php echo time(); ?>" type="text/css" media="all" />
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

  <!-- <header class="fixed-top pb-1 py-md-0 d-flex">
  </header> -->

  <main>
    <div class="content container">