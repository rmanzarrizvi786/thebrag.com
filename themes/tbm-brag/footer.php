<?php get_template_part('template-parts/footer/footer'); ?>
</div><!-- .content -->

<div id="network" class="network" style="display: none;">
  <?php get_template_part('template-parts/network'); ?>
</div>
<?php get_template_part('template-parts/observer-list'); ?>

<div id="skin" class="d-none d-md-block">
  <?php render_ad_tag('skin'); ?>
</div>

</main>
<noscript id="deferred-styles">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@200;300;500&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" id="tbm-css" href="<?php echo CDN_URL; ?>css/style.css?v=20231206.1" type="text/css"
    media="all" />
  <!-- <link rel="stylesheet" id="tbm-css" href="<?php echo get_template_directory_uri(); ?>/css/style.css?v=<?php echo time(); ?>" type="text/css" media="all" /> -->
</noscript>

<script src="https://www.youtube.com/iframe_api" defer></script>

<script>
  var BASE = "<?php echo home_url() ?>";
  var SITE_NAME = "<?php echo html_entity_decode(get_bloginfo('name'), ENT_QUOTES); ?>";
  var window_width = jQuery(window).width();
  var window_height = jQuery(window).height();

  var loadDeferredStyles = function () {
    var addStylesNode = document.getElementById("deferred-styles");
    var replacement = document.createElement("div");
    replacement.innerHTML = addStylesNode.textContent;
    document.body.appendChild(replacement);
    addStylesNode.parentElement.removeChild(addStylesNode);
  };
  var raf = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
    window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
  if (raf) raf(function () {
    window.setTimeout(loadDeferredStyles, 0);
  });
  else window.addEventListener('load', loadDeferredStyles);
</script>

<?php wp_footer(); ?>

<?php if (is_single()):
  $post_featured_images = array();
  $img_src1 = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'full');
  $img_src2 = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'thumbnail');
  $img_src3 = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'medium');

  $img_src1 = isset($img_src1) ? $img_src1 : '';
  $img_src2 = isset($img_src2) ? $img_src2 : '';
  $img_src3 = isset($img_src3) ? $img_src3 : '';
  //$post_featured_images_new = array();
  if ($img_src1 != '') {
    array_push($post_featured_images, '"' . $img_src1[0] . '"');
  }
  if ($img_src2 != '') {
    array_push($post_featured_images, '"' . $img_src2[0] . '"');
  }
  if ($img_src3 != '') {
    array_push($post_featured_images, '"' . $img_src3[0] . '"');
  }
  //array_push($post_featured_images, '"' . $img_src1[0] . '"', '"' . $img_src2[0] . '"', '"' . $img_src3[0] . '"');

  //echo '<pre>';
  //print_r($post_featured_images_new);
  //print_r($post_featured_images);

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
  ?>
  <script type="application/ld+json">
            {
              "@context": "http://schema.org",
              "@type": "<?php echo !in_category('evergreen', $post) ? "NewsArticle" : "BlogPosting"; ?>",
              "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "<?php echo get_permalink(); ?>"
              },
              "headline": "<?php echo get_the_title(); ?>",
              "image": [
                <?php if (count($post_featured_images) > 0):
                  echo implode(',', $post_featured_images);
                endif; ?>
              ],
              "datePublished": "<?php echo date('Y-m-d\TH:i:s+10:00', get_the_time('U')); ?>",
              "dateModified": "<?php echo the_modified_date('Y-m-d\TH:i:s+10:00'); ?>",
              "author": {
                "@type": "Person",
                "name": "<?php echo htmlspecialchars($author, ENT_QUOTES); ?>"
              },
              "publisher": {
                "@type": "Organization",
                "name": "<?php echo get_bloginfo('name'); ?>",
                "logo": {
                  "@type": "ImageObject",
                  "url": "<?php echo CDN_URL; ?>The-Brag-300px.png"
                }
              },
              "description": "<?php echo get_bloginfo('description'); ?>"
            }
          </script>
<?php endif; ?>

<div id="overlay" class="d-none"></div>


<script>
  jQuery(document).ready(function ($) {
    $(window).trigger('scroll');

    $('div[role="search"] button').on('click', function (e) {
      const searchInput = $(this).parent().find(('input[name="s"]'))

      if (searchInput.val()) {
        window.location.href = `/search/${searchInput.val()}`
      }
    })

    $('input[name="s"]').on('keypress', e => {
      if (e.key === "Enter") {
        if (e.target.value) {
          window.location.href = `/search/${e.target.value}`
        }
      }
    })
  });
</script>

<!-- <div class="sticky-ad-bottom" style="
display: none;
position: fixed;
    bottom: 0;
    border: 0;
    padding: 0;
    margin: 0;
    text-align: center;
    z-index: 4;
    width: 100%;">
  <?php // render_ad_tag('mob_sticky'); 
  ?>
</div> -->
<script>
  jQuery(document).ready(function ($) {
    $(".op-interactive iframe").each(function () {
      var dmVideoPath = $(this).attr('src');
      var res1 = dmVideoPath.replace('geo', "www");
      var res2 = res1.replace('player.html?', "embed/");
      var res3 = res2.replace('video=', "video/");
      var lastCharacter = res3.charAt(res3.length - 1);
      if (lastCharacter == '&') {
        res3 = res3.slice(0, -1);
      }
      $(this).attr('src', res3);
      res3 = '';
    });
  }); 
</script>
<!-- 22071836792/outofpage/outofpage -->
<div data-fuse="22779881596"></div>
</body>

</html>