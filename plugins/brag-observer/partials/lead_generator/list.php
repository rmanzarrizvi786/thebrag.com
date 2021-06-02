<?php
global $wpdb;

wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' );

$lead_generators_query = "
  SELECT
    t.id,
    t.title,
    t.question1,
    t.created_at,
    t.msg_thanks,
    t.msg_thanks_verify,
    l.title list_title,
    (SELECT COUNT(r.id) FROM {$wpdb->base_prefix}observer_lead_generator_responses r WHERE r.status = 'verified' AND r.lead_generator_id = t.id) total_responses
  FROM {$wpdb->base_prefix}observer_lead_generators t
    JOIN {$wpdb->base_prefix}observer_lists l
      ON l.id = t.list_id
  GROUP BY
    t.id
  ORDER BY
    t.id DESC
";
$lead_generators = $wpdb->get_results( $lead_generators_query );
?>
<div class="text-right my-3">
  <a href="admin.php?page=brag-observer-manage-lead-generator" class="btn btn-sm btn-primary">Create Lead Generator</a>
</div>

<?php if ( $lead_generators ) : ?>
  <table class="table table-sm table-hover table-bordered">
    <tr>
      <th>Title</th>
      <th>Question1</th>
      <th>List</th>
      <th>Thanks Message</th>
      <th>Thanks Message (verify)</th>
      <th style="width: 330px;">Shortcode</th>
      <th>Verified Responses</th>
      <th>Actions</th>
    </tr>
<?php foreach ( $lead_generators as $lead_generator ) : ?>
  <tr>
    <th>
      <a href="<?php echo add_query_arg( [ 'page' => 'brag-observer-manage-lead-generator', 'action' => 'details', 'id' => $lead_generator->id, 'status' => 'verified' ] ); ?>" class="btn btn-sm btn-primary"><?php echo $lead_generator->title; ?></a>
      <small>(Created: <?php echo date('d M, Y', strtotime( $lead_generator->created_at ) ); ?>)</small>
    </th>
    <td><?php echo $lead_generator->question1; ?></td>
    <td><?php echo $lead_generator->list_title; ?></td>
    <td><?php echo $lead_generator->msg_thanks; ?></td>
    <td><?php echo $lead_generator->msg_thanks_verify; ?></td>
    <td><input type="text" value='[observer_lead_generator_form id="<?php echo $lead_generator->id; ?>"]' readonly class="form-control" onClick="this.select();"></td>
    <td><?php echo $lead_generator->total_responses; ?></td>
    <td>
      <a href="<?php echo add_query_arg( [ 'page' => 'brag-observer-manage-lead-generator', 'action' => 'edit', 'id' => $lead_generator->id ] ); ?>" class="btn btn-sm btn-info">Edit</a>
    </td>
  </tr>
<?php endforeach; // For Each $lead_generators ?>
  </table>
<?php else : ?>
  <div class="alert alert-info">No Lead Generators found</div>
<?php
endif; // If $lead_generators
return;
