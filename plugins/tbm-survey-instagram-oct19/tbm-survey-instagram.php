<?php
/*
   Plugin Name: TBM Instagram Survey (Oct 2019)
   Plugin URI:
   description: Purpose: Save submitted entries for Survey (Oct 2019)
   Version: 1.0
   Author: Sachin Patel
   Author URI:
*/

class TBM_Insagram_Survey_Oct19 {

  protected $plugin_name;
  protected $plugin_slug;

  public function __construct() {
      $this->plugin_name = 'tbm_insagram_survey_oct2019';
      $this->plugin_slug = 'tbm-insagram-survey-oct-2019';

      add_action('init', array( $this, 'init' ) );

      add_action( 'admin_menu', array( $this, 'admin_menu' ) );

      add_filter ('theme_post_templates', array( $this, 'add_page_template' ) );
      add_filter( 'single_template', array( $this, 'custom_template' ) );

      add_action( 'admin_action_tbm_export_survey_instagram_oct19', array( $this, 'export' ) );
  }

  function admin_menu() {
      add_management_page('Instagram Survey Responses (Oct 2019)', 'Instagram Survey Responses (Oct 2019)', 'edit_posts', $this->plugin_slug, array( $this, 'show_results' ) );
  }

  public function export() {
      global $wpdb;
      $csv_data = array();
      header('Content-Encoding: UTF-8');
      header('Content-type: text/csv; charset=UTF-8');
      header('Content-Disposition: attachment; filename="DBU_export_survey_instagram_oct19.csv";');
      header("Pragma: no-cache");
      header("Expires: 0");

      $csv_content = "Email, Instagram Handle, What music or artists have you discovered on instagram?, What did you find?, Why do you love it?, How did you find it?, How did you find it? - Hash tag, How did you find it? - Account, Date/Time\n";

      $responses = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}insta_survey_responses_oct19" );
      if ( $responses ) :
          foreach ( $responses as $response ) :
              $csv_content .= '"' . $response->user_email . '",' .
                    '"' . $response->user_instagram_handle . '",' .
                  '"' . $response->music_artists_discovered . '",' .
                  '"' . $response->link_to_discovery . '",' .
                  '"' . $response->why_love_it . '",' .
                  '"' . $response->how_did_you_find . '",' .
                  '"' . $response->how_did_you_find_hashtag . '",' .
                  '"' . $response->how_did_you_find_account . '",' .
                  '"' . $response->created_at . '"'
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
    $templates['single-template-instagram-survey-oct19.php'] = 'Instagram Survey Oct 2019';
    return $templates;
  }


  public function custom_template($single) {
      global $post;
      if ( $post->post_type == 'post' && is_page_template( 'single-template-instagram-survey-oct19.php' ) ) {
          if ( file_exists( plugin_dir_path( __FILE__ ) . '/single-template-instagram-survey-oct19.php' ) ) {
              return plugin_dir_path( __FILE__ ) . '/single-template-instagram-survey-oct19.php';
          }
      }
      return $single;
  }

  public function show_results () {

      global $wpdb;

      echo '<h1>Instagram Survey Responses (Oct 2019)</h1>';

      $responses = $wpdb->get_results(
        "
          SELECT
            r.*
          FROM {$wpdb->prefix}insta_survey_responses_oct19 r
        "
      );

      if( $responses ) :
      ?>
      <p>Total: <?php echo count( $responses ); ?></p>

      <form method="post" action="<?php echo admin_url( 'tools.php' ); ?>">
          <input type="hidden" name="action" value="tbm_export_survey_instagram_oct19">
          <?php submit_button( 'Export' ); ?>
      </form>

      <table class="widefat fixed">
          <thead>
              <tr>
                  <th>Email</th>
                  <th>Instagram Handle</th>
                  <th>What music or artists have you discovered on instagram?</th>
                  <th>What did you find?</th>
                  <th>Why do you love it?</th>
                  <th>How did you find it?</th>
                  <th>How did you find it? - Hash tag</th>
                  <th>How did you find it? - Account</th>
                  <th>Date/Time</th>
              </tr>
          </thead>
          <tbody>
      <?php
          foreach ( $responses as $response ) :
      ?>

        <tr>
            <td><?php echo $response->user_email; ?></td>
            <td><?php echo $response->user_instagram_handle; ?></td>
            <td><?php echo $response->music_artists_discovered; ?></td>
            <td><a href="<?php echo $response->link_to_discovery; ?>" target="_blank"><?php echo $response->link_to_discovery; ?></a></td>
            <td><div style="max-height: 150px; overflow-y: scroll;"><?php echo nl2br( $response->why_love_it ); ?></div></td>
            <td><?php echo $response->how_did_you_find; ?></td>
            <td><?php echo $response->how_did_you_find_hashtag; ?></td>
            <td><?php echo $response->how_did_you_find_account; ?></td>
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



new TBM_Insagram_Survey_Oct19();
