<?php
wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' );
?>

<form action="<?php echo admin_url( 'admin.php' ); ?>" method="post" class="container-fluid" enctype="multipart/form-data">
  <input type="hidden" name="action" value="mc_delete_from_wp">
  <div class="container-fluid">
    <h1>MailChimp subs DELETE from WP</h1>

    <div class="row">
      <div class="col-md-2 mt-3">
        <button type="button" name="start-delete-from-wp" id="start-delete-from-wp" class="btn btn-danger">Start</button>
      </div>

      <table class="table table-sm" id="results">

      </table>
    </div>
  </div>
</form>

  <script>
  jQuery(document).ready(function($){
    $('#start-delete-from-wp').on('click', function(e) {
      e.preventDefault();

      processSubs();
    });

    function processSubs() {
      $( '#start-delete-from-wp' ).prop( 'disabled', true );

      var fd = new FormData();
      fd.append( 'action', 'mc_delete_from_wp' );

      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: fd,
        contentType:false,
        processData:false,
        success: function(res){
          console.log(res);
          if ( res.success ) {
            $.each( res.data, function (i, e ) {
              $('#results').prepend( '<tr><td>' + e + '</td></tr>' );
            });
            processSubs();
          } else {
            alert (res.data );
            if ( res.data == 'Done' ) {
            }
            $( '#start-delete-from-wp' ).prop( 'disabled', false );
          }
        },
        error: function(xhr, ajaxOptions, thrownError){
          alert(xhr.responseText);
          $( '#start-delete-from-wp' ).prop( 'disabled', false );
        }
      });
    }
  });
  </script>
