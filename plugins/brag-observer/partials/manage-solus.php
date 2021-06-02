<?php
global $wpdb;

$action = isset( $_GET['action'] ) ? $_GET['action'] : null;

if ( ! is_null( $action ) && 'edit' != $action ) {
  switch( $action ) {
    case 'preview':
      include __DIR__ . '/../partials/solus-template.php';
      break;
    case 'create-on-mc':
      include __DIR__ . '/../partials/create-solus-mc.php';
      break;
    default:
      break;
  }
  return;
} // If $action is set

wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' );

$lists = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}observer_lists WHERE status = 'active' ORDER BY title ASC" );

$list_ids = [];

$id = isset( $_GET['id'] ) ? $_GET['id'] : null;
if ( !is_null( $id ) ):
  $solus = $wpdb->get_row( "SELECT * FROM {$wpdb->base_prefix}observer_solus WHERE id = {$id}" );
  if ( ! $solus ) {
    echo '<div class="alert alert-danger">Solus not found.</div>';
    return;
  }
  $solus->details = json_decode( $solus->details );

  foreach ( $solus->details as $k => $v ) {
    if ( is_object( $v ) ) {
      $v = (array) $v;
      // $v = array_values( $v );
      $solus->details->{$k} = $v;
    }
  }

  $list_ids = explode( ',', $solus->lists );

endif;
?>
<style>
  .input-group-text,
  #campaign-posts input,
  #campaign-posts textarea { font-size: .8rem !important;}
  #campaign-posts label{ display: block; }

  .form-control {
    border: 1px solid #ced4da !important;
  }
</style>
<?php
wp_enqueue_script( 'observer-newsletter', plugin_dir_url( __FILE__ ) . '/../../js/newsletter.js', array( 'jquery' ), time(), true );

$args = array(
  'ajaxurl'   => admin_url( 'admin-ajax.php' ),
);
wp_localize_script( 'observer-newsletter', 'observer', $args );

wp_enqueue_script( 'jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array ( 'jquery' ), 1.0, true);
wp_enqueue_style( 'jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css' );
?>


<form method="post" action="#" class="create-solus">
  <?php if ( isset($solus) ): ?>
    <input type="hidden" name="id" id="newsletter-id" value="<?php echo $solus->id; ?>">
    <h1>Edit "<?php echo $solus->details->title; ?>"</h1>
  <?php else: ?>

    <h1>Create New Solus</h1>
  <?php endif; ?>

  <table class="table table-sm table-light">
    <!-- MailChimp Campaign Details -->
    <tr>
      <th colspan="2">Campaign Details <small>(for MailChimp)</small></th>
    </tr>
    <tr>
      <td colspan="4">Subject <small>Max 150 characters</small>
        <input type="text" name="subject" value="<?php echo isset($solus) && isset($solus->details->subject) ? htmlentities( $solus->details->subject ) : ''; ?>" maxlength="150" class="form-control"></td>
    </tr>
    <tr>
      <td colspan="4">Preview Text <small>Max 150 characters</small>
        <input type="text" name="preview_text" value="<?php echo isset($solus) && isset($solus->details->preview_text) ? htmlentities( $solus->details->preview_text ) : ''; ?>" maxlength="150" class="form-control"></td>
    </tr>
    <tr>
      <td>Date<br>
        <input type="text" name="date_for" class="datepicker form-control" readonly value="<?php echo isset($solus) && isset($solus->details->date_for) ? date('j F Y', strtotime($solus->details->date_for)) : date('j F Y'); ?>">
      </td>

      <td>Title<br><input type="text" name="title" value="<?php echo isset($solus) && isset($solus->details->title) ? htmlentities( $solus->details->title ) : '[' . date('d M, Y'). '] Solus'; ?>" class="form-control"></td>

      <td>Reply to <small>Email Address</small><br>
        <input type="text" name="reply_to" value="<?php echo isset($solus) && isset($solus->details->reply_to) ? $solus->details->reply_to : 'observer@thebrag.media'; ?>" class="form-control"></td>

      <!-- <td>From Name<br>
        <input type="text" name="from_name" value="<?php // echo isset($solus) && isset($solus->details->from_name) ? $solus->details->from_name : 'The Brag Observer'; ?>" class="form-control"></td> -->
    </tr>
    <!-- MailChimp Campaign Details -->
  </table>

  <p><strong>Select list(s)</strong></p>
  <div class="row">
    <?php foreach ( $lists as $list ) { ?>
      <label class="col-md-3">
      <input type="checkbox" name="lists[]" id="list_<?php echo $list->id; ?>" value="<?php echo $list->id; ?>" <?php echo in_array( $list->id, $list_ids ) ? ' checked' : ''; ?>> <?php echo $list->title; ?>
    </label>
    <?php } // For Each $list ?>
  </div>

    <div class="mt-3">
      <p><strong>Solus Image</strong></p>
      <input type="text" name="solus_image_url" id="solus_image_url" class="form-control" value="<?php echo isset( $solus->solus_image_url ) && $solus->solus_image_url != '' ? $solus->solus_image_url : '';?>" placeholder="https://">
      <?php
        if(function_exists( 'wp_enqueue_media' )){
          wp_enqueue_media();
        } else{
          wp_enqueue_style('thickbox');
          wp_enqueue_script('media-upload');
          wp_enqueue_script('thickbox');
        }
      ?>
      <?php if ( isset( $solus->solus_image_url ) && $solus->solus_image_url != '' ) : ?>
      <img src="<?php echo $solus->solus_image_url; ?>" width="100" id="solus_image" class="img-fluid d-block">
      <?php endif; ?>
      <button id="btn-observer-solus-image" type="button" class="button">Upload / Select from Library</button>
    </div>

    <div class="mt-3">
      <p><strong>Click-through URL</strong></p>
      <input name="solus_link" id="solus_link" type="text"
        value="<?php echo isset( $solus->solus_link ) && $solus->solus_link != '' ? stripslashes( $solus->solus_link ) : ''; ?>"
        placeholder="https://" class="form-control">
    </div>

    <div>
      <div class="submit">
        <div id="js-errors" class="hide alert alert-danger"></div>
        <input type="button" name="submit" id="submit-solus" class="button button-primary" value="Save">
        <span class="status alert"></span>
      </div>
    </div>


</form>

<script>
jQuery(document).ready(function($){
  $('#btn-observer-solus-image').click(function(e) {
      e.preventDefault();

      var custom_uploader = wp.media({
        title: 'Select Solus Image',
        button: {
          text: 'Select'
        },
        multiple: false  // Set this to true to allow multiple files to be selected
      })
      .on('select', function() {
        var attachment = custom_uploader.state().get('selection').first().toJSON();
        if ( $('#solus_image').length )
            $('#solus_image').detach();
          console.log( attachment );
          $('#solus_image_url').val(attachment.url);
          $('#btn-observer-solus-image').before('<img src="' + attachment.sizes.thumbnail.url + '" width="100" id="solus_image" class="img-fluid d-block">');
      })
      .open();
  });

});
</script>
