<?php
wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css');
?>

<form action="<?php echo admin_url('admin.php'); ?>" method="post" class="container-fluid" enctype="multipart/form-data">
  <input type="hidden" name="action" value="mc_to_wp">
  <div class="container-fluid">
    <h1>Mailchimp (IP Warmup tags) to Braze</h1>

    <div class="row">
      <div class="col-md-2 mt-3">
        <button type="button" name="start-ip-warmup-export-to-braze" id="start-ip-warmup-export-to-braze" class="btn btn-primary">Start</button>
      </div>

      <table class="table table-sm" id="results"></table>
    </div>
  </div>
</form>

<script>
  jQuery(document).ready(function($) {
    $('#start-ip-warmup-export-to-braze').on('click', function(e) {
      e.preventDefault();

      processSubs();
    });

    function processSubs() {
      $('#start-ip-warmup-export-to-braze').prop('disabled', true);
      // $('#results').html('');

      var fd = new FormData();
      fd.append('action', 'ip_warmup_export_to_braze');
      fd.append('limit_users', 50);

      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: fd,
        contentType: false,
        processData: false,
        success: function(res) {
          if (res.success) {
            $.each(res.data, function(i, e) {
              $('#results').prepend('<tr><td>' + e + '</td></tr>');
            });
            processSubs();
          } else {
            alert(res.data);
            $('#start-ip-warmup-export-to-braze').prop('disabled', false);
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          if (524 == xhr.status) {
            console.error("524 Error at: ", new Date());
            setTimeout(function() {
              processSubs();
            }, 1000);
          } else {
            alert(xhr.responseText);
          }
          $('#start-ip-warmup-export-to-braze').prop('disabled', false);
        }
      });
    }
  });
</script>