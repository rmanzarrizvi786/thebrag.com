<?php
global $wpdb;

wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' );

$id = isset( $_GET['id'] ) ? $_GET['id'] : null;
if ( !is_null( $id ) ):
  $lead_generator = $wpdb->get_row( "SELECT * FROM {$wpdb->base_prefix}observer_lead_generators WHERE id = {$id}" );
  if ( ! $lead_generator ) {
    echo '<div class="alert alert-danger">Lead Generator not found.</div>';
    return;
  }

  $status = isset( $_GET['status'] ) ? $_GET['status'] : NULL;

  $reviews_query = "
    SELECT
      r.id,
      r.response1,
      r.source,
      r.status,
      r.source,
      r.created_at,
      u.ID,
      u.user_email
    FROM {$wpdb->base_prefix}observer_lead_generator_responses r
      JOIN {$wpdb->base_prefix}users u
        ON r.user_id = u.ID
    WHERE r.lead_generator_id = {$id}
  ";
  if ( ! is_null( $status ) ) {
    $reviews_query .= " AND r.status = '{$status}' ";
  }
  $reviews_query .= "
    ORDER BY r.id DESC
  ";
  $reviews = $wpdb->get_results( $reviews_query );
  if ( $reviews ) :
?>
  <h1><?php echo $lead_generator->title; ?></h1>
  <div class="row">
    <div class="col-auto">
      <h2>Total<?php echo ! is_null( $status ) ? ' (' . $status . ')' : ''; ?>: <?php echo count( $reviews ); ?></h2>
    </div>
    <div class="col d-flex align-items-center">
      <a href="<?php echo remove_query_arg( [ 'status' ] ); ?>" class="btn btn-sm <?php echo is_null( $status ) ? 'btn-primary' : ''; ?>">All</a>
      <a href="<?php echo add_query_arg( [ 'status' => 'verified' ] ); ?>" class="btn btn-sm <?php echo ! is_null( $status ) ? 'btn-primary' : ''; ?>">Verified</a>
    </div>
  </div>
  <table class="table table-sm table-hover table-bordered mt-3" id="list-reviews">
    <tr>
      <th>Email</th>
      <th>Response1</th>
      <th>Status</th>
      <th>Source</th>
      <th>Submitted at</th>
    </tr>
    <?php foreach ( $reviews as $review ) : ?>
    <tr>
      <th><?php echo $review->user_email; ?></th>
      <td><?php echo wpautop( $review->response1 ); ?></td>
      <td><?php echo $review->status; ?></td>
      <td><a href="<?php echo $review->source; ?>" target="_blank"><?php echo $review->source; ?></a></td>
      <td><?php echo date('d M, Y h:ia', strtotime( $review->created_at ) ); ?></td>
    </tr>
    <?php endforeach; // For Each $review ?>
  </table>
<?php endif; // IF $reviews ?>

<?php
endif; // If id is set
