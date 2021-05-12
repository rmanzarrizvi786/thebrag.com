<?php
wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' );

$lists = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}observer_lists WHERE status = 'active' ORDER BY title ASC" );
?>

<form action="<?php echo admin_url( 'admin.php' ); ?>" method="post" class="container-fluid" enctype="multipart/form-data">
  <input type="hidden" name="action" value="mc_to_wp">
  <div class="container-fluid">
    <h1>Step 1. MailChimp subs to WP</h1>

    <div class="row">
      <div class="col-md-12">
        <h4>Select List(s)</h4>
          <?php if ( $lists ) : ?>
          <div class="row">
            <?php foreach ( $lists as $list ) : ?>
            <!-- <option value="<?php // echo $list->id; ?>"><?php echo $list->title; ?></option> -->
            <div class="col-md-3">
              <label>
                <input type="checkbox" name="list[]" id="list_<?php echo $list->id; ?>" value="<?php echo $list->id; ?>">
                <?php echo $list->title; ?>
              </label>
            </div>
            <?php endforeach; // For Each $list ?>
          </div>
          <?php endif; // If $lists ?>
      </div>

      <div class="col-md-5 mt-3">
        <h4>Upload file (CSV)</h4>
        <input type="file" name="csv_file" id="csv_file" class="form-control" required>
      </div>

      <div class="col-md-2 mt-3">
        <h4>&nbsp;</h4>
        <button type="submit" name="submit-mc-to-wp" id="submit-mc-to-wp" class="btn btn-primary">Submit</button>
      </div>

      <table class="table table-sm" id="results">

      </table>


    </div>
  </div>
</form>

  <script>
  jQuery(document).ready(function($){
    /*
    // $('#submit-mc-to-wp').on('click', function(e) {
    $('#mc-to-wp-form').on('submit', function(e) {
      e.preventDefault();

      var theForm = $(this);

      var file_list = $('#csv_file');
      if ( file_list[0].files.length == 0 ) {
        alert( 'Please select a file.' );
        return;
      }

      // theForm.find( 'button#submit-mc-to-wp' ).prop( 'disabled', true );

      var fd = new FormData(this);
      fd.append( 'action', 'mc_to_wp' );
      fd.append( 'csv_file', file_list[0].files[0]);

      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: fd,
        contentType:false,
        processData:false,
        success: function(res){
          console.log( res );
          if ( res.success ) {
            $.each( res.data, function (i, e ) {
              $('#results').append( '<tr><td>' + e + '</td></tr>' );
            });
            // $('#results').append( '<tr><td>' + res.data + '</td></tr>' );
            // console.log(res.data);
          } else {
            alert (res.data );
            theForm.find( 'button#submit-mc-to-wp' ).prop( 'disabled', false );
          }
        },
        error: function(xhr, ajaxOptions, thrownError){
          alert(xhr.responseText);
          theForm.find( 'button#submit-mc-to-wp' ).prop( 'disabled', false );
        }
      });
    });
    */
  });
  </script>
