<?php
$id = isset($_GET['id']) ? $_GET['id'] : null;
if (is_null($id)) :
  return;
endif;

$solus = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_solus WHERE id = {$id}");
if (is_null($solus)) :
  return;
endif;
$solus->details = json_decode($solus->details);
foreach ($solus->details as $k => $v) {
  if (is_object($v)) {
    $v = (array) $v;
    // $v = array_values( $v );
    $solus->details->{$k} = $v;
  }
}

// $list = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_lists WHERE id = {$solus->list_id}");

if ($solus->details->subject == '') {
  $solus->details->subject = 'Newsletter';
}

ob_start();

include(plugin_dir_path(__FILE__) . '/solus-template.php');

$html = ob_get_contents();
ob_end_clean();

?>
<div style="margin-top: 1rem;">
  <input id="solus-subject" type="text" value="<?php echo $solus->details->subject; ?>" style="width: 90%; padding: .5rem;">
</div>
<div style="display: flex;">
  <div style="flex: 0 0 40%">
    <div style="margin: 1rem auto;">
      <textarea id="solus-content" style="width: 100%; height: 90vh;"><?php echo $html; ?></textarea>
    </div>
  </div>
  <div style="flex: 0 0 60%">
    <div style="margin: 1rem auto; width: 100%; height: 90vh; overflow: scroll;">
      <?php echo $html; ?>
    </div>
  </div>
</div>

<script>
  jQuery(document).ready(function($) {
    $('#solus-content, #solus-subject').on('focus', function() {
      $(this).select();
    });
  })
</script>