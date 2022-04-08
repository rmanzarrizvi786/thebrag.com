<?php
global $wpdb;

// wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css');
wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css');

$per_page = 10;

$page = isset($_GET['p']) ? $_GET['p'] : 1;
$limit_from = ($page - 1) * $per_page;

$total = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->base_prefix}observer_solus ");
$total_pages = ceil($total / $per_page);

$soluses = $wpdb->get_results("SELECT * FROM {$wpdb->base_prefix}observer_solus ORDER BY date_for DESC, id DESC LIMIT {$limit_from}, {$per_page}");
?>
<div class="d-flex justify-content-between align-items-center">
  <h1>Solus</h1>
  <a href="admin.php?page=brag-observer-manage-solus" class="btn btn-sm btn-primary">Create Solus</a>
</div>

<div class="d-flex flex-wrap justify-content-center mb-3">
  <div class="btn">Total: <?php echo $total; ?></div>
  <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
    <a href="<?php echo esc_url(add_query_arg(array('p' => $i))); ?>" class="btn <?php echo $i == $page ? ' btn-dark' : ''; ?>"><?php echo $i; ?></a>
  <?php endfor; ?>
</div>

<table class="table table-sm table-striped">
  <?php if ($soluses) {
    foreach ($soluses as $solus) {
      $solus->details = json_decode($solus->details);
      $lists = $wpdb->get_results("SELECT `id`, `title` FROM {$wpdb->prefix}observer_lists WHERE id IN ( {$solus->lists} ) ORDER BY title ASC");
  ?>
      <tr>
        <th class="py-4">
          <p><a href="<?php echo add_query_arg(['page' => 'brag-observer-manage-solus', 'action' => 'edit', 'id' => $solus->id], remove_query_arg('list_id')); ?>" class="btn btn-sm btn-info"><?php echo $solus->details->title; ?></a></p>
          <p><?php echo $solus->details->subject; ?></p>
          <p><small>(<?php echo date('d M, Y', strtotime($solus->date_for)); ?>)</small></p>
          <!-- <div class="mt-2">
            <a href="<?php echo add_query_arg(['page' => 'brag-observer-manage-solus', 'action' => 'edit', 'id' => $solus->id], remove_query_arg('list_id')); ?>" class="btn btn-sm btn-info">Edit</a>
          </div> -->
        </th>

        <!-- <td><?php // echo implode('<br>', wp_list_pluck($lists, 'title')); 
                  ?></td> -->

        <!-- <td><?php echo $solus->status == 1 ? 'Created on MC' : 'Draft'; ?></td> -->
        <td class="py-4">
          <!-- <a href="<?php echo add_query_arg(['page' => 'brag-observer-manage-solus', 'action' => 'preview', 'id' => $solus->id, 'list_id']); ?>" class="btn btn-sm btn-primary" target="_blank" title="Click to preview">Preview</a> -->


        </td>
        <td class="py-4">

          <div class="mx-2">
            <div class="list-group">
              <div class="list-group-item small p-1"><strong>Preview</strong></div>
              <?php foreach ($lists as $list) : ?>
                <!-- <a href="<?php echo add_query_arg(['page' => 'brag-observer-manage-solus', 'action' => 'preview', 'id' => $solus->id, 'list_id' => $list->id]); ?>" target="_blank" class="list-group-item small p-1"><?php echo $list->title; ?></a> -->
                <a href="<?php echo home_url("/preview-newsletter/solus/{$solus->id}/l/{$list->id}/"); ?>" target="_blank" class="list-group-item small p-1"><?php echo $list->title; ?></a>
              <?php endforeach; ?>
            </div>
          </div>
        </td>
        <td class="py-4">
          <div class="mx-2">
            <div class="list-group">
              <div class="list-group-item small p-1"><strong>Show HTML</strong></div>
              <?php foreach ($lists as $list) : ?>
                <a href="<?php echo add_query_arg(['page' => 'brag-observer-manage-solus', 'action' => 'show-html', 'id' => $solus->id, 'list_id' => $list->id]); ?>" target="_blank" class="list-group-item small p-1"><?php echo $list->title; ?></a>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- <a href="<?php echo add_query_arg(['page' => 'brag-observer-manage-solus', 'action' => 'create-on-mc', 'id' => $solus->id], remove_query_arg('list_id')); ?>" class="btn btn-sm btn-warning">Push to MailChimp</a> -->
          </div>
        </td>
      </tr>
    <?php
    } // For Each $soluses
    ?>
</table>
<?php
  } else {
?>
  <div class="alert alert-info">No Solus found</div>
<?php
  } // If $soluses
  return;
