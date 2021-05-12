<?php
/*
   Plugin Name: TBM Instagram Survey (Sep 2019)
   Plugin URI:
   description: Purpose: Save submitted entries for Survey (Sep 2019)
   Version: 1.0
   Author: Sachin Patel
   Author URI:
*/

class TBM_Insagram_Survey {

  protected $plugin_name;
  protected $plugin_slug;

  public function __construct() {
      $this->plugin_name = 'tbm_insagram_survey_sep2019';
      $this->plugin_slug = 'tbm-insagram-survey-sep-2019';

      add_action('init', array( $this, 'init' ) );

      add_action( 'admin_menu', array( $this, 'admin_menu' ) );

      add_filter ('theme_post_templates', array( $this, 'add_page_template' ) );
      add_filter( 'single_template', array( $this, 'custom_template' ) );

      add_action( 'admin_action_tbm_export_survey_instagram', array( $this, 'export' ) );
  }

  function admin_menu() {
      add_management_page('Instagram Survey Responses (Sep 2019)', 'Instagram Survey Responses (Sep 2019)', 'edit_posts', $this->plugin_slug, array( $this, 'show_results' ) );
  }

  public function export() {
      global $wpdb;
      $csv_data = array();
      header('Content-Encoding: UTF-8');
      header('Content-type: text/csv; charset=UTF-8');
      header('Content-Disposition: attachment; filename="DBU_export_survey_instagram.csv";');
      header("Pragma: no-cache");
      header("Expires: 0");

      $csv_content = "Email, Looked at Instagram Explore page, Discovered new artist, Discovered new hashtags, Country, Age\n";

      $responses = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}insta_survey_responses" );
      if ( $responses ) :
          foreach ( $responses as $response ) :
              $csv_content .= '"' . $response->user_email . '",' .
                  '"' . $response->looked_at_explore . '",' .
                  '"' . $response->discovered_new_artist . '",' .
                  '"' . $response->discovered_new_hashtags . '",' .

                  '"' . $response->user_country . '",' .
                  '"' . $response->age . '"'
                  ;
              $csv_content .= "\n";
          endforeach;
      endif;
      echo $csv_content;
      exit;
  }

  /*
   * Show Thank you message OR
   * Process the form submitted from the posts
   */
  public function init() {

  }

  public function add_page_template ($templates) {
    $templates['single-template-instagram-survey.php'] = 'Instagram Survey Sep 2019';
    return $templates;
  }


  public function custom_template($single) {
      global $post;
      if ( $post->post_type == 'post' && is_page_template( 'single-template-instagram-survey.php' ) ) {
          if ( file_exists( plugin_dir_path( __FILE__ ) . '/single-template-instagram-survey.php' ) ) {
              return plugin_dir_path( __FILE__ ) . '/single-template-instagram-survey.php';
          }
      }
      return $single;
  }

  public function show_results () {

      global $wpdb;

      echo '<h1>Instagram Survey Responses (Sep 2019)</h1>';

      $responses = $wpdb->get_results(
        "
          SELECT
            r.*
          FROM {$wpdb->prefix}insta_survey_responses r
        "
      );

      if( $responses ) :
      ?>
      <p>Total: <?php echo count( $responses ); ?></p>

      <form method="post" action="<?php echo admin_url( 'tools.php' ); ?>">
          <input type="hidden" name="action" value="tbm_export_survey_instagram">
          <?php submit_button( 'Export' ); ?>
      </form>

      <table class="widefat fixed">
          <thead>
              <tr>
                  <th>Email</th>
                  <th>Have you looked at your Instagram Explore page in the last 7 days?</th>
                  <th>Have you discovered a new artist on Instagram over the last 7 weeks?</th>
                  <th>Have you discovered new hashtags to follow in the last 7 weeks?</th>
                  <th>What country do you live in?</th>
                  <th>What's your age?</th>
                  <th>Date/Time</th>
              </tr>
          </thead>
          <tbody>
      <?php
          foreach ( $responses as $response ) :
      ?>

        <tr>
            <td><?php echo $response->user_email; ?></td>
            <td><?php echo $response->looked_at_explore; ?></td>
            <td><?php echo $response->discovered_new_artist; ?></td>
            <td><?php echo $response->discovered_new_hashtags; ?></td>
            <td><?php echo $response->user_country; ?></td>
            <td><?php echo $response->age; ?></td>
            <td><?php echo $response->created_at; ?></td>
        </tr>

      <?php
          endforeach;
      ?>
        </tbody>
      </table>

      <?php
      endif;
  }
}



new TBM_Insagram_Survey();
