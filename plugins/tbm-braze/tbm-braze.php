<?php

/**
 * Plugin Name: TBM Braze
 * Plugin URI: https://thebrag.media/
 * Description:
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI:
 */

namespace TBM;

class Braze
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
    if (is_user_logged_in()) {
      $user_id = get_current_user_id();
      if (!$user_id)
        return;
      $auth0_user_id = get_user_meta($user_id, $wpdb->prefix . 'auth0_id', true);
      if ($auth0_user_id) {
?>
        <script>
          window.dataLayer = window.dataLayer || [];
          window.dataLayer.push({
            'Auth0ID': '<?php echo $auth0_user_id; ?>',
          });
        </script>
<?php
      } // If $auth0_user_id
    } // If user is logged in
  }
}

new Braze();
