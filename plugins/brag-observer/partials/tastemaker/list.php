<?php
global $wpdb;

wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' );

$tastemakers_query = "
  SELECT
    t.id,
    t.title,
    t.created_at
  FROM {$wpdb->base_prefix}observer_tastemakers t
  ORDER BY t.id DESC
";
$tastemakers = $wpdb->get_results( $tastemakers_query );
?>
<div class="text-right my-3">
  <a href="admin.php?page=brag-observer-manage-tastemaker" class="btn btn-sm btn-primary">Create Tastemaker</a>
</div>

<?php if ( $tastemakers ) : ?>
  <table class="table table-sm table-hover table-bordered">
    <tr>
      <th>Title</th>
      <th>Shortcode</th>
      <th>Total verified submissions</th>
      <th>Average rating</th>
      <th>Actions</th>
    </tr>
<?php foreach ( $tastemakers as $tastemaker ) :
  $reviews_query = "
    SELECT
      AVG( r.rating ) avg_rating,
      COUNT( r.id ) total_reviews
    FROM
      {$wpdb->base_prefix}observer_tastemaker_reviews r
    WHERE
      r.status = 'verified' AND
      r.tastemaker_id = '{$tastemaker->id}'
    GROUP BY
      r.tastemaker_id
  ";
  $reviews = $wpdb->get_row( $reviews_query );
  ?>
  <tr>
    <th>
      <a href="<?php echo add_query_arg( [ 'page' => 'brag-observer-manage-tastemaker', 'action' => 'details', 'id' => $tastemaker->id, 'status' => 'verified' ] ); ?>" class="btn btn-sm btn-primary"><?php echo $tastemaker->title; ?></a>
      <small>(Created: <?php echo date('d M, Y', strtotime( $tastemaker->created_at ) ); ?>)</small>
    </th>
    <td>
      <input type="text" value='[observer_tastemaker_form id="<?php echo $tastemaker->id; ?>"]' readonly class="form-control" onClick="this.select();">
    </td>
    <td class="text-right">
      <?php echo $reviews && isset( $reviews->total_reviews ) ? $reviews->total_reviews : 0; ?>
    </td>
    <td class="text-right">
      <?php echo $reviews && isset( $reviews->avg_rating ) ? round( $reviews->avg_rating, 2 ) : 0; ?>
    </td>
    <td>
      <a href="<?php echo add_query_arg( [ 'page' => 'brag-observer-manage-tastemaker', 'action' => 'edit', 'id' => $tastemaker->id ] ); ?>" class="btn btn-sm btn-info">Edit</a>
    </td>
  </tr>
<?php endforeach; // For Each $tastemakers ?>
  </table>
<?php else : ?>
  <div class="alert alert-info">No Tastemaker found</div>
<?php
endif; // If $tastemakers
return;
