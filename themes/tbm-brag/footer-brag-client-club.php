</div><!-- .content -->

<?php
// get_template_part('template-parts/observer-list');
?>

<!-- <div id="skin" class="d-none d-md-block"> -->
<?php // render_ad_tag('skin'); 
?>
<!-- </div> -->

</main>
<noscript id="deferred-styles">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@200;300;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" id="tbm-css" href="<?php echo CDN_URL; ?>css/style.css?v=20211025.1" type="text/css" media="all" />
</noscript>

<script src="https://www.youtube.com/iframe_api" defer></script>

<script>
  var BASE = "<?php echo home_url() ?>";
  var SITE_NAME = "<?php echo html_entity_decode(get_bloginfo('name'), ENT_QUOTES); ?>";
  var window_width = jQuery(window).width();
  var window_height = jQuery(window).height();

  var loadDeferredStyles = function() {
    var addStylesNode = document.getElementById("deferred-styles");
    var replacement = document.createElement("div");
    replacement.innerHTML = addStylesNode.textContent;
    document.body.appendChild(replacement);
    addStylesNode.parentElement.removeChild(addStylesNode);
  };
  var raf = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
    window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
  if (raf) raf(function() {
    window.setTimeout(loadDeferredStyles, 0);
  });
  else window.addEventListener('load', loadDeferredStyles);
</script>

<?php wp_footer(); ?>

<?php if (is_single()) :
  $post_featured_images = array();
  $img_src1 = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'full');
  $img_src2 = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'thumbnail');
  $img_src3 = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'medium');
  array_push($post_featured_images, '"' . $img_src1[0] . '"', '"' . $img_src2[0] . '"', '"' . $img_src3[0] . '"');

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
        <?php if (count($post_featured_images) > 0) : echo implode(',', $post_featured_images);
        endif; ?>
      ],
      "datePublished": "<?php echo date('Y-m-d\TH:i:s+10:00', get_the_time('U')); ?>",
      "dateModified": "<?php echo the_modified_date('Y-m-d\TH:i:s+10:00'); ?>",
      "author": {
        "@type": "Person",
        "name": "<?php echo str_replace(['"', '\\'], ['\"', '\\\\',], $author); ?>"
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
  jQuery(document).ready(function($) {
    $(window).trigger('scroll');

    $('#form-rs-mag-sub').on('submit', function(e) {
      e.preventDefault();
      theForm = $(this);
      var formData = theForm.serialize();
      var elemResponse = $('#sub-response');
      elemResponse.removeClass('alert-danger alert-success').addClass('alert-success').text('Processing, please wait...');
      $.post({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        data: {
          action: 'submit_rs_mag_new_subscription',
          formData: formData
        }
      }).success(function(res) {
        elemResponse.removeClass('d-none').text(res.data);
        if (res.success) {
          elemResponse.removeClass('alert-danger').addClass('alert-success');
          setTimeout(function() {
            window.location.reload();
          }, 1000);
        } else {
          elemResponse.removeClass('alert-success').addClass('alert-danger');
        }
      }).done(function() {});
    })
  });
</script>

</body>

</html>