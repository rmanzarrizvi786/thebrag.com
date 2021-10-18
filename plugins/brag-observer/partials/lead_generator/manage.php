<?php
global $wpdb;

$action = isset($_GET['action']) ? $_GET['action'] : null;

if (!is_null($action) && 'edit' != $action) {
  switch ($action) {
    case 'details':
      include __DIR__ . '/../../partials/lead_generator/details.php';
      break;
    default:
      break;
  }
  return;
} // If $action is set

wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css');

$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!is_null($id)) :
  $lead_generator = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_lead_generators WHERE id = {$id}");
  if (!$lead_generator) {
    echo '<div class="alert alert-danger">Lead Generator not found.</div>';
    return;
  }
endif;
?>
<style>
  .form-control {
    border: 1px solid #ced4da !important;
  }
</style>
<?php
wp_enqueue_script('observer-newsletter', plugin_dir_url(__FILE__) . '/../../../js/newsletter.js', array('jquery'), time(), true);

$args = array(
  'ajaxurl'   => admin_url('admin-ajax.php'),
);
wp_localize_script('observer-newsletter', 'observer', $args);

wp_enqueue_script('jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array('jquery'), 1.0, true);
wp_enqueue_style('jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css');
?>


<form method="post" action="#" class="create-lead_generator">
  <?php if (isset($lead_generator)) : ?>
    <input type="hidden" name="id" id="lead_generator-id" value="<?php echo $lead_generator->id; ?>">
    <h1>Edit "<?php echo $lead_generator->title; ?>"</h1>
  <?php else : ?>

    <h1>Create New Lead Generator</h1>
  <?php endif; ?>

  <div class="row">
    <div class="col-md-4 mt-3">
      <label>Title</label>
      <input type="text" name="title" class="form-control" value="<?php echo isset($lead_generator) && isset($lead_generator->title) ? htmlentities($lead_generator->title) : ''; ?>">
    </div>

    <div class="col-md-8 mt-3">
      <label>List</label>
      <?php
      $lists = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}observer_lists WHERE status = 'active' ORDER BY title ASC");
      $lead_generator_list_ids = isset($lead_generator) ? explode(',', $lead_generator->list_id) : [];
      if ($lists) :
      ?>
        <div class="row" style="height: 100px; overflow-y: scroll;">
          <?php foreach ($lists as $list) : ?>
            <label class="col-md-3">
              <input name="list_id[]" type="checkbox" value="<?php echo $list->id; ?>" <?php echo isset($lead_generator) && isset($lead_generator->list_id) && in_array($list->id, $lead_generator_list_ids) ? ' checked' : ''; ?>><?php echo $list->title; ?></option>
            </label>
          <?php endforeach; // For Each $list  
          ?>
        </div>
      <?php
      endif;
      if (0 && $lists) :
      ?>
        <select name="list_id" id="list_id" class="form-control">
          <?php foreach ($lists as $list) : ?>
            <option value="<?php echo $list->id; ?>" <?php echo isset($lead_generator) && isset($lead_generator->list_id) && $list->id == $lead_generator->list_id ? ' selected' : ''; ?>><?php echo $list->title; ?></option>
          <?php endforeach; // For Each $list 
          ?>
        </select>
      <?php
      endif; // If $lists
      ?>
    </div>

    <div class="col-12 mt-3">
      <label>Thanks message</label>
      <input type="text" name="msg_thanks" class="form-control" value="<?php echo isset($lead_generator) && isset($lead_generator->msg_thanks) ? htmlentities($lead_generator->msg_thanks) : ''; ?>">
    </div>

    <div class="col-12 mt-3">
      <label>Thanks message (Verify)</label>
      <input type="text" name="msg_thanks_verify" class="form-control" value="<?php echo isset($lead_generator) && isset($lead_generator->msg_thanks_verify) ? htmlentities($lead_generator->msg_thanks_verify) : ''; ?>">
    </div>

    <div class="col-12 mt-3">
      <label>Question 1</label>
      <input type="text" name="question1" class="form-control" value="<?php echo isset($lead_generator) && isset($lead_generator->question1) ? htmlentities($lead_generator->question1) : ''; ?>">
    </div>

    <div class="col-12 mt-3">
      <label>Consent to additional promotional marketing text<br><small>If not empty, it will add a Checkbox field to the form</small></label>
      <input type="text" name="consent_promotional_marketing_text" class="form-control" value="<?php echo isset($lead_generator) && isset($lead_generator->consent_promotional_marketing_text) ? htmlentities($lead_generator->consent_promotional_marketing_text) : ''; ?>">
    </div>

    <div class="col-12 mt-3">
      <label>Footer text to appear at the bottom of form. <small>(Optional)</small></label>
      <input type="text" name="footer_text" class="form-control" value="<?php echo isset($lead_generator) && isset($lead_generator->footer_text) ? htmlentities($lead_generator->footer_text) : ''; ?>">
    </div>
  </div>

  <div>
    <div class="submit">
      <div id="js-errors" class="hide alert alert-danger"></div>
      <input type="button" name="submit" id="submit-lead_generator" class="button button-primary" value="Save">
      <span class="status alert"></span>
    </div>
  </div>

</form>

<script>
  jQuery(document).ready(function($) {

  });
</script>