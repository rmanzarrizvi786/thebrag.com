<?php
wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' );
?>

<form action="<?php echo admin_url( 'admin.php' ); ?>" method="post" class="container-fluid" enctype="multipart/form-data">
  <input type="hidden" name="action" value="mc_to_wp_empty">
  <div class="container-fluid">
    <h1>MailChimp subs imports to WP (Empty)</h1>

    <div class="row">
      <div class="col-md-2 mt-3">
        <button type="button" name="start-mc-imps-to-wp-empty" id="start-mc-imps-to-wp-empty" class="btn btn-primary">Start</button>
      </div>

      <table class="table table-sm" id="results">

      </table>
    </div>
  </div>
</form>

  <script>
  jQuery(document).ready(function($){
    $('#start-mc-imps-to-wp-empty').on('click', function(e) {
      e.preventDefault();

      processSubs();
    });

    function processSubs() {
      $( '#start-mc-imps-to-wp-empty' ).prop( 'disabled', true );

      var fd = new FormData();
      fd.append( 'action', 'mc_to_wp_empty' );

      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: fd,
        contentType:false,
        processData:false,
        success: function(res){
          if ( res.success ) {
            $.each( res.data, function (i, e ) {
              $('#results').prepend( '<tr><td>' + e + '</td></tr>' );
            });
            processSubs();
          } else {
            alert (res.data );
            if ( res.data == 'Done' ) {
              // window.location = 'admin.php?page=brag-observer-export-for-mailchimp';
            }
            $( '#start-mc-imps-to-wp-empty' ).prop( 'disabled', false );
          }
        },
        error: function(xhr, ajaxOptions, thrownError){
          console.log( xhr.responseText );
          alert(xhr.responseText);
          $( '#start-mc-imps-to-wp-empty' ).prop( 'disabled', false );
        }
      });
    }
  });
  </script>
