<?php
wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' );
?>
<div class="container-fluid">
    <h1>Step 3. Export for MailChimp</h1>

    <?php
    $query = "SELECT count(u.ID) FROM {$wpdb->prefix}users u JOIN {$wpdb->prefix}usermeta um ON u.ID = um.user_id WHERE um.meta_key = 'oc_token' AND DATE( u.user_registered ) >= '" . date('Y-m-d', strtotime('-2 days') ) . "'";
    $total_users = $wpdb->get_var( $query );

    /*
    $users = get_users(
      [
        'meta_query' => [
          [
            'key' => 'oc_token', // 'source_import',
            // 'value' => 'MailChimp',
            'compare' => 'EXISTS', // '='
          ],
        ],
        // 'role__in' => [ 'subscriber' ],
        'orderby' => 'ID',
        'order' => 'ASC',

        'date_query' => [
          'after' => date('Y-m-d', strtotime( '-2 days' ) ),
        ]
      ]
    );
    // echo '<pre>'; print_r( wp_list_pluck( $users, 'user_email' ) ); echo '</pre>';
    $total_users = $users ? count( $users ) : 0;
    */

    // $total_users = 177000;
    ?>

    <div class="row">
      <div class="col-12">
        <h4>Total: <?php echo $total_users; ?></h4>
        <?php
        if( $total_users > 0 ) :
          $batch = 50000;
          for( $i = 0; $i < $total_users; $i += $batch ) : ?>
          <form action="<?php echo admin_url( 'admin.php' ); ?>" method="post" class="d-inline mr-2">
            <input type="hidden" name="action" value="export_for_mailchimp">
            <input type="hidden" name="offset" value="<?php echo $i; ?>">
            <input type="hidden" name="number" value="<?php echo $batch; ?>">
            <button type="submit" name="submit-export-for-mc" id="export-for-mc" class="btn btn-primary">Export <?php echo $i + 1; ?> to <?php echo ( $i + $batch ) < $total_users ? $i + $batch : $total_users; ?></button>
          </form>
        <?php
          endfor;
        endif;
        ?>
      </div>
    </div>
  </div>


<script>
jQuery(document).ready(function($){

});
</script>
