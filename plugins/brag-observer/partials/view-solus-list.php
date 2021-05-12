<?php
global $wpdb;

wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' );

$soluses = $wpdb->get_results( "SELECT * FROM {$wpdb->base_prefix}observer_solus ORDER BY date_for DESC, id DESC" );
?>
<div class="text-right my-3">
  <a href="admin.php?page=brag-observer-manage-solus" class="btn btn-sm btn-primary">Create Solus</a>
</div>

  <table class="table table-sm table-hover">
<?php if ( $soluses ) {
  foreach ( $soluses as $solus ) {
    $solus->details = json_decode( $solus->details );
?>
  <tr>
    <th>
      <?php echo $solus->details->title; ?>
      <br>
      <small><?php echo $solus->details->subject; ?></small>
      <small>(<?php echo date('d M, Y', strtotime( $solus->date_for ) ); ?>)</small>
    </th>
    <td>
      <?php $lists = $wpdb->get_results( "SELECT title FROM {$wpdb->prefix}observer_lists WHERE id IN ( {$solus->lists} ) ORDER BY title ASC" );
      echo implode( '<br>', wp_list_pluck( $lists, 'title' ) );
      ?>
    </td>
    <td><?php echo $solus->status == 1 ? 'Created on MC' : 'Draft'; ?></td>
    <td class="text-right" style="width: 275px;">
      <a href="<?php echo add_query_arg( [ 'page' => 'brag-observer-manage-solus', 'action' => 'preview', 'id' => $solus->id ] ); ?>" class="btn btn-sm btn-primary" target="_blank" title="Click to preview">Preview</a>

      <a href="<?php echo add_query_arg( [ 'page' => 'brag-observer-manage-solus', 'action' => 'edit', 'id' => $solus->id ], remove_query_arg( 'list_id' ) ); ?>" class="btn btn-sm btn-info">Edit</a>

      <a href="<?php echo add_query_arg( [ 'page' => 'brag-observer-manage-solus', 'action' => 'create-on-mc', 'id' => $solus->id ], remove_query_arg( 'list_id' ) ); ?>" class="btn btn-sm btn-warning">Push to MailChimp</a>
    </td>
  </tr>
<?php
  } // For Each $soluses
?>
</table>
<?php
} else {
?>
  <div class="alert alert-info">No Solus found</div>
<?php
} // If $soluses
return;
