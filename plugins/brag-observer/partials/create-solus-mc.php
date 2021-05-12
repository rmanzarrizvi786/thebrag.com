<?php
$id = isset( $_GET['id'] ) ? $_GET['id'] : null;
if ( is_null( $id ) ):
  return;
endif;

$solus = $wpdb->get_row( "SELECT * FROM {$wpdb->base_prefix}observer_solus WHERE id = {$id}" );
if( is_null( $solus ) ):
  return;
endif;
$solus->details = json_decode( $solus->details );
foreach ( $solus->details as $k => $v ) {
  if ( is_object( $v ) ) {
    $v = (array) $v;
    $solus->details->{$k} = $v;
  }
}

$lists = $wpdb->get_results( "SELECT * FROM {$wpdb->base_prefix}observer_lists WHERE id IN( {$solus->lists} ) ORDER BY sub_count DESC" );

if ( $solus->details->subject == '' ) {
  $solus->details->subject = 'The Brag Observer Newsletter';
}

$interest_ids = wp_list_pluck( $lists, 'interest_id' );

// echo '<pre>'; print_r( $interest_ids ); exit;

if( $lists ) {
  foreach ( $lists as $index => $list ) {

    // echo '<pre>'; print_r( $list->interest_id ); echo '</pre>';
    $from_name = trim( str_ireplace( 'Observer', '', $list->title ) ) . ' Observer';

    $conditions = [
      [
        'field' => 'interests-' . $list->interest_id,
        'condition_type' => 'Interests',
        'op' => 'interestcontains', // all',
        'value' => [ $list->interest_id ]
      ]
    ];

    if ( 0 != $index ) {

      $exclude_interest_ids = $interest_ids;

      unset( $exclude_interest_ids[array_keys( $exclude_interest_ids, $list->interest_id )[0]] );
      $exclude_interest_ids = array_values( $exclude_interest_ids );

      $conditions[] = [
        'field' => 'interests-' . $list->interest_id,
        'condition_type' => 'Interests',
        'op' => 'interestnotcontains',
        'value' => $exclude_interest_ids
      ];
    }

    $data = array(
      "type" => "regular",
      "recipients" => array(
        "list_id" => $this->mailchimp_list_id,
        "segment_opts" => array(
          'match' => 'all',
          'conditions' => $conditions
        )
      ),
      "settings" => array(
        "subject_line" => $solus->details->subject,
        "preview_text" => $solus->details->preview_text,
        "title" => '[' . $list->title . '] ' . $solus->details->title,
        "reply_to" => $solus->details->reply_to,
        "from_name" => $from_name, //$solus->details->from_name
      ),
    );




    $campaign = $this->MailChimp->post( 'campaigns', $data );

    if ( isset( $campaign['errors'] ) ) {
      echo 'Error creating solus for: ' . $from_name;
      echo '<pre>'; print_r( $campaign ); echo '</pre>';
      exit;
    }

    $campaign_id = $campaign['id'];

    ob_start();

    include(plugin_dir_path( __FILE__ ) . '/solus-template.php');

    $html = ob_get_contents();
    ob_end_clean();

    $content = array(
      'html' => $html,
    );
    $this->MailChimp->put( 'campaigns/' . $campaign_id . '/content', $content );
  } // For Each $lis
} // If $lists

// exit;

$wpdb->update(
  $wpdb->base_prefix . 'observer_solus',
  array( 'status' => '1' ),
  array ( 'id' => $solus->id )
);
?>
<script>
  window.location = '?page=brag-observer-view-solus-list&list_id=<?php echo $list->id; ?>';
</script>
