<?php
$id = isset($_GET['id']) ? $_GET['id'] : null;
if (is_null($id)) :
  return;
endif;

$newsletter = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_newsletters WHERE id = {$id}");
if (is_null($newsletter)) :
  return;
endif;
$newsletter->details = json_decode($newsletter->details);
foreach ($newsletter->details as $k => $v) {
  if (is_object($v)) {
    $v = (array) $v;
    // $v = array_values( $v );
    $newsletter->details->{$k} = $v;
  }
}

$list = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_lists WHERE id = {$newsletter->list_id}");

// var_dump( $list ); exit;

if ($newsletter->details->subject == '') {
  $newsletter->details->subject = 'Newsletter';
}

ob_start();

include(plugin_dir_path(__FILE__) . '/newsletter-template-braze.php');

$html = ob_get_contents();
ob_end_clean();

// wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css');

?>
<div style="margin-top: 1rem;">
  <input id="newsletter-subject" type="text" value="<?php echo $newsletter->details->subject; ?>" style="width: 90%; padding: .5rem;">
</div>
<div style="display: flex;">
  <div style="flex: 0 0 40%">
    <div style="margin: 1rem auto;">
      <textarea id="newsletter-content" style="width: 100%; height: 90vh;"><?php echo $html; ?></textarea>
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
    $('#newsletter-content, #newsletter-subject').on('focus', function() {
      $(this).select();
    });
  })
</script>