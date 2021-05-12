<?php
global $wpdb;

$action = isset( $_GET['action'] ) ? $_GET['action'] : null;

if ( ! is_null( $action ) && 'edit' != $action ) {
  switch( $action ) {
    case 'details':
      include __DIR__ . '/../../partials/tastemaker/details.php';
      break;
    default:
      break;
  }
  return;
} // If $action is set

wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' );

$id = isset( $_GET['id'] ) ? $_GET['id'] : null;
if ( !is_null( $id ) ):
  $tastemaker = $wpdb->get_row( "SELECT * FROM {$wpdb->base_prefix}observer_tastemakers WHERE id = {$id}" );
  if ( ! $tastemaker ) {
    echo '<div class="alert alert-danger">Tastemaker not found.</div>';
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
wp_enqueue_script( 'observer-newsletter', plugin_dir_url( __FILE__ ) . '/../../../js/newsletter.js', array( 'jquery' ), time(), true );

$args = array(
  'ajaxurl'   => admin_url( 'admin-ajax.php' ),
);
wp_localize_script( 'observer-newsletter', 'observer', $args );

wp_enqueue_script( 'jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array ( 'jquery' ), 1.0, true);
wp_enqueue_style( 'jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css' );
?>


<form method="post" action="#" class="create-tastemaker">
  <?php if ( isset($tastemaker) ): ?>
    <input type="hidden" name="id" id="tastemaker-id" value="<?php echo $tastemaker->id; ?>">
    <h1>Edit "<?php echo $tastemaker->title; ?>"</h1>
  <?php else: ?>

    <h1>Create New Tastemaker</h1>
  <?php endif; ?>

  <div class="row">
    <div class="col-md-4 mt-3">
      <label>Title</label>
      <input type="text" name="title" class="form-control" value="<?php echo isset($tastemaker) && isset($tastemaker->title) ? htmlentities( $tastemaker->title ) : ''; ?>">
    </div>

    <!-- <div class="col-md-6 mt-3">
      <label>Description</label>
      <textarea name="description" class="form-control"><?php // echo isset( $formdata['description'] ) ? $formdata['description'] : ''; ?></textarea>
    </div> -->

    <div class="col-md-4 mt-3">
      <!-- <p><strong>Image</strong></p> -->
      <!-- <input type="text" name="image_url" id="image_url" class="form-control" value="<?php // echo isset( $tastemaker->image_url ) && $tastemaker->image_url != '' ? $tastemaker->image_url : '';?>" placeholder="https://"> -->
      <?php
      /*
        if(function_exists( 'wp_enqueue_media' )){
          wp_enqueue_media();
        } else{
          wp_enqueue_style('thickbox');
          wp_enqueue_script('media-upload');
          wp_enqueue_script('thickbox');
        }
      */
      ?>
      <?php // if ( isset( $tastemaker->image_url ) && $tastemaker->image_url != '' ) : ?>
      <!-- <img src="<?php // echo $tastemaker->image_url; ?>" width="100" id="tastemaker_image" class="img-fluid d-block"> -->
      <?php // endif; ?>
      <!-- <button id="btn-observer-tastemaker-image" type="button" class="button">Upload / Select from Library</button> -->
    </div>
</div>

<div>
  <div class="submit">
    <div id="js-errors" class="hide alert alert-danger"></div>
    <input type="button" name="submit" id="submit-tastemaker" class="button button-primary" value="Save">
    <span class="status alert"></span>
  </div>
</div>

</form>

<script>
jQuery(document).ready(function($){
  $('#btn-observer-tastemaker-image').click(function(e) {
      e.preventDefault();

      var custom_uploader = wp.media({
        title: 'Select Tastemaker Image',
        button: {
          text: 'Select'
        },
        multiple: false  // Set this to true to allow multiple files to be selected
      })
      .on('select', function() {
        var attachment = custom_uploader.state().get('selection').first().toJSON();
        if ( $('#tastemaker_image').length )
            $('#tastemaker_image').detach();
          console.log( attachment );
          $('#image_url').val(attachment.url);
          $('#btn-observer-tastemaker-image').before('<img src="' + attachment.sizes.thumbnail.url + '" width="100" id="tastemaker_image" class="img-fluid d-block">');
      })
      .open();
  });

});
</script>
