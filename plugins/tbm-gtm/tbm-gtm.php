<?php

/**
 * Plugin Name: TBM GTM
 * Plugin URI: https://thebrag.media/
 * Description:
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI:
 */

namespace TBM;

class GTM
{

  protected $plugin_title;
  protected $plugin_name;
  protected $plugin_slug;

  public function __construct()
  {

    $this->plugin_title = 'TBM Braze';
    $this->plugin_name = 'tbm_braze';
    $this->plugin_slug = 'tbm-braze';

    add_action('wp_head', [$this, 'wp_head']);
  }

  public function wp_head()
  {
    global $wpdb;

    if (is_single()) {
      global $post;
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
    }

    if (is_user_logged_in()) {
      $user_id = get_current_user_id();
      if (!$user_id)
        return;

      // if (!get_user_meta($user_id, 'braze_change_user_done')) 
      {
        $auth0_user_id = get_user_meta($user_id, $wpdb->prefix . 'auth0_id', true);
        if ($auth0_user_id) {
          update_user_meta($user_id, 'braze_change_user_done', 1);
      ?>
          <script>
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
              'Auth0ID': '<?php echo $auth0_user_id; ?>',
            });
          </script>
    <?php
        } // If $auth0_user_id
      } // If braze change user not done
    } // If user is logged in
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
<?php
  }
}

new GTM();
