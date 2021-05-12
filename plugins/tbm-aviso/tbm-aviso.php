<?php
/*
   Plugin Name: TBM Aviso Lead Generation
   Plugin URI:
   description: Purpose: Save submitted entries and verify email addresses
   Version: 1.0
   Author: Sachin Patel
   Author URI:
*/

class TBM_Aviso_Quiz {

  protected $plugin_name;
  protected $plugin_slug;

  public function __construct() {
      $this->plugin_name = 'tbm_aviso_quiz';
      $this->plugin_slug = 'tbm-aviso-quiz';

      add_action('init', array( $this, 'init' ) );

      add_action( 'admin_menu', array( $this, 'admin_menu' ) );

      add_filter ('theme_post_templates', array( $this, 'add_page_template' ) );
      add_filter( 'single_template', array( $this, 'custom_template' ) );

  }

  function admin_menu() {
      add_management_page('Aviso Leads', 'Aviso Leads', 'edit_posts', $this->plugin_slug, array( $this, 'show_results' ) );
  }

  /*
   * Show Thank you message OR
   * Process the form submitted from the posts
   */
  public function init() {

  }

  public function add_page_template ($templates) {
    $templates['single-template-quiz-aviso.php'] = 'Aviso Lead Generation';
    return $templates;
  }


  public function custom_template($single) {
      global $post;
      if ( $post->post_type == 'post' && is_page_template( 'single-template-quiz-aviso.php' ) ) {
          if ( file_exists( plugin_dir_path( __FILE__ ) . '/single-template-quiz-aviso.php' ) ) {
              return plugin_dir_path( __FILE__ ) . '/single-template-quiz-aviso.php';
          }
      }
      return $single;
  }

  public function show_results () {

      global $wpdb;

      echo '<h1>Aviso Leads</h1>';

      $leads = $wpdb->get_results(
        "
          SELECT
            l.*
          FROM {$wpdb->prefix}aviso_leads l
          GROUP BY l.email
          ORDER BY l.id DESC
        "
      );

      if( $leads ) :
      ?>
      <table class="widefat fixed">
          <thead>
              <tr>
                  <th>Product</th>
                  <th>Total leads</th>
                  <th>Valid leads</th>
              </tr>
          </thead>
          <tbody>
      <?php
      $separate_leads = $wpdb->get_results(
          "SELECT l.product, COUNT(DISTINCT l.email) total FROM {$wpdb->prefix}aviso_leads l GROUP BY l.product"
      );
          
          $total_leads = 0;

      if ( $separate_leads ) :
          $total_valid = 0;
          foreach ( $separate_leads as $separate_lead ) :
      ?>
      
        <tr>
            <td><?php echo $separate_lead->product; ?></td>
            <td><?php echo $separate_lead->total; $total_leads += $separate_lead->total; ?></td>
            <td>
                <?php
                $valid_leads = $wpdb->get_var("SELECT
                      COUNT(l.id) total
                  FROM {$wpdb->prefix}aviso_leads l
                      LEFT JOIN {$wpdb->prefix}aviso_lead_responses lr
                          ON l.id = lr.lead_id
                      LEFT JOIN {$wpdb->prefix}aviso_lead_results lre
                          ON l.id = lre.lead_id
                  WHERE
                      lr.question = 'will_hook_up' AND
                      lr.response LIKE '%Definitely%' AND
                      l.verified = 1 AND
                      l.product = '{$separate_lead->product}'
                  GROUP BY l.product
                ");
              echo $valid_leads;
              $total_valid += $valid_leads;
                ?>
            </td>
        </tr>
    
      <?php
          endforeach;
      endif;
      ?>
        </tbody>
      <tfoot>
          <tr>
              <th>Total</th>
              <th><?php echo $total_leads; ?></th>
              <th><?php echo $total_valid; ?></th>
          </tr>
      </tfoot>
      </table>

      <p>&nbsp;</p>

      <table class="widefat">
        <thead>
          <tr>
            <th>Lead</th>
            <th>Responses / Result</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach( $leads as $k => $lead ) : ?>

          <tr class="<?php echo $k % 2 == 0 ? 'alternate' : ''; ?>">
            <td>
              Full name: <?php echo $lead->firstname . ' ' . $lead->lastname; ?><br>
              Email: <?php echo $lead->email; ?><br>
              Phone: <?php echo $lead->phone; ?><br>
              Verified: <?php echo $lead->verified; ?><br>
              Page: <a href="<?php echo get_the_permalink( $lead->quiz_post ); ?>" target="_blank"><?php echo get_the_title( $lead->quiz_post ); ?></a>
          </td>

          <td>
              <table class="widefat fixed">
                  <tr>
                      <td>
              <?php
              $responses = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}aviso_lead_responses WHERE lead_id = {$lead->id}" );
              if ( $responses ) : ?>
                    <table class="widefat fixed">
                      <?php foreach( $responses as $response ) : ?>
                        <tr>
                          <th><?php echo ucwords( str_replace( '_', ' ', $response->question ) ); ?></th>
                          <td><?php echo wpautop( stripslashes( $response->response ) ); ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </table>
              <?php endif; // If there are responses ?>
                      </td>
                  </tr>
                  <tr>
                      <td>
          
              <?php
              $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}aviso_lead_results WHERE lead_id = {$lead->id}" );
              if ( $results ) : ?>
                    <table class="widefat fixed">
                      <?php foreach( $results as $result ) :
                        $result_response = json_decode( $result->response );
                        foreach( $result_response as $k => $v ) :
                          if ( ! in_array( $k, array( 'response', 'timestamp' ) ) ) :
                            continue;
                          endif;
                        ?>
                        <tr>
                          <th><?php echo ucwords( str_replace( '_', ' ', $k ) ); ?></th>
                          <td><?php echo wpautop( stripslashes( $v ) ); ?></td>
                        </tr>
                        <?php endforeach; ?>
                      <?php endforeach; ?>
                    </table>
              <?php endif; // If there are responses ?>
                      </td>
                  </tr>
              </table>
          </td>
      </tr>

          <?php endforeach; ?>
        </tbody>
      </table>
      <?php
      endif;
  }
}



new TBM_Aviso_Quiz();
