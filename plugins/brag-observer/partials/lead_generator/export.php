<?php
global $wpdb;

$id = isset($_POST['id']) ? $_POST['id'] : null;
if (!is_null($id)) :
  $lead_generator = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_lead_generators WHERE id = {$id}");
  if (!$lead_generator) {
    echo '<div class="alert alert-danger">Lead Generator not found.</div>';
    return;
  }

  $status = isset($_POST['status']) ? $_POST['status'] : NULL;

  $reviews_query = "
    SELECT
      u.user_email Email,
      r.response1 Response1,
      r.response2 Response2,
      r.consent_promotional_marketing Consent,
      r.status Status,
      r.created_at 'Submitted at'
    FROM {$wpdb->base_prefix}observer_lead_generator_responses r
      JOIN {$wpdb->base_prefix}users u
        ON r.user_id = u.ID
    WHERE r.lead_generator_id = {$id}
  ";
  if (!is_null($status)) {
    $reviews_query .= " AND r.status = '{$status}' ";
  }
  $reviews_query .= "
    ORDER BY r.id DESC
  ";
  $reviews = $wpdb->get_results($reviews_query);

  if ($reviews) :
    $reviews = json_decode(json_encode($reviews), true);
    $reviews = stripslashes_deep($reviews);

    ob_start();
    $df = fopen("php://output", 'w');
    fputcsv($df, array_keys(reset($reviews)));
    foreach ($reviews as $row) {
      fputcsv($df, $row);
    }
    fclose($df);
    $csv_content = ob_get_clean();

    $filename = sanitize_title($lead_generator->title) . '-responses.csv';

    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");

    echo $csv_content;
  endif; // IF $reviews 
endif; // If id is set
