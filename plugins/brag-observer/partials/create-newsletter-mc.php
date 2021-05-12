<?php
$id = isset( $_GET['id'] ) ? $_GET['id'] : null;
if ( is_null( $id ) ):
  return;
endif;



$newsletter = $wpdb->get_row( "SELECT * FROM {$wpdb->base_prefix}observer_newsletters WHERE id = {$id}" );
if( is_null( $newsletter ) ):
  return;
endif;
$newsletter->details = json_decode( $newsletter->details );
foreach ( $newsletter->details as $k => $v ) {
  if ( is_object( $v ) ) {
    $v = (array) $v;
    // $v = array_values( $v );
    $newsletter->details->{$k} = $v;
  }
}

$list = $wpdb->get_row( "SELECT * FROM {$wpdb->base_prefix}observer_lists WHERE id = {$newsletter->list_id}" );

// var_dump( $list ); exit;

if ( $newsletter->details->subject == '' ) {
  $newsletter->details->subject = 'Newsletter';
}

// echo '<pre>'; print_r( $newsletter->details ); exit;
$data = array(
  "type" => "regular",
  "recipients" => array(
    "list_id" => $this->mailchimp_list_id,
    "segment_opts" => array(
      'match' => 'any',
      'conditions' => array(
        array(
          'field' => 'interests-' . $list->interest_id,
          'condition_type' => 'Interests',
          'op' => 'interestcontainsall',
          'value' => [ $list->interest_id ]
        )
      )
    )
  ),
  "settings" => array(
    "subject_line" => $newsletter->details->subject,
    "preview_text" => $newsletter->details->preview_text,
    "title" => $newsletter->details->title,
    "reply_to" => $newsletter->details->reply_to,
    "from_name" => $newsletter->details->from_name
  ),
);

$campaign = $this->MailChimp->post( 'campaigns', $data );

// echo '<pre>'; var_dump( $campaign ); exit;
$campaign_id = $campaign['id'];

ob_start();

include(plugin_dir_path( __FILE__ ) . '/newsletter-template.php');

$html = ob_get_contents();
ob_end_clean();

$content = array(
  'html' => $html,
);
$this->MailChimp->put( 'campaigns/' . $campaign_id . '/content', $content );

$wpdb->update(
  $wpdb->base_prefix . 'observer_newsletters',
  array( 'status' => '1' ),
  array ( 'id' => $newsletter->id )
);
?>
<script>
  window.location = '?page=brag-observer-view-newsletter-list&list_id=<?php echo $list->id; ?>';
</script>
