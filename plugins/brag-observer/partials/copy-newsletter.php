<?php
$id = isset( $_GET['id'] ) ? $_GET['id'] : null;
if ( is_null( $id ) ):
  return;
endif;

$clone = $wpdb->get_row( "SELECT * FROM {$wpdb->base_prefix}observer_newsletters WHERE id = {$id}" );
if( is_null( $clone ) ):
  return;
endif;

$list = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}observer_lists WHERE id = {$clone->list_id}" );

$today =  date( 'j F, Y', strtotime( current_time( 'mysql' ) ) );
$clone->details = json_decode( $clone->details );
unset( $clone->details->id );
$clone->details->date_for = $today;

$clone->details->title = '[' . date('d M, Y'). '] ' . $list->title; // '#';

$clone->details = json_encode( $clone->details );
$wpdb->insert(
    $wpdb->base_prefix . 'observer_newsletters',
    array(
      'list_id' => $clone->list_id,
      'date_for' => date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) ),
      'details' => $clone->details,
      'status' => '0',
      'created_at' => current_time( 'mysql' ),
      'updated_at' => current_time( 'mysql' ),
    )
);
$clone_id = $wpdb->insert_id;
?>
<script>
  window.location = '?page=brag-observer-manage-newsletter&action=edit&id=<?php echo $clone_id; ?>';
</script>
