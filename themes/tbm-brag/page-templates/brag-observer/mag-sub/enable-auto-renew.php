<div class="row">
  <div class="col-12 mb-3">
    <h3>Enable auto-renew <small class="text-muted">ID: <?php echo $sub_id; ?></small></h3>
  </div>
</div>

<?php
$post_data['uniqid'] = $subscription->uniqid;
$enable = json_decode($bo->enableAutoRenew($post_data));

if (!$enable->success) {
  echo '<div class="alert alert-danger">' . $cancel->data->error->message . '</div>';
} else {
  echo '<div class="alert alert-success">The subscription will be auto-renewed!</div>
  <script>
  setTimeout(function(){
    window.location.href = "' . home_url('/observer/magazine-subscriptions/') . '";
  }, 3000 );
  </script>';
}