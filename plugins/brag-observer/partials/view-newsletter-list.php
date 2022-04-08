<?php
global $wpdb;

wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css');

$list_id = isset($_GET['list_id']) ? $_GET['list_id'] : null;
if (is_null($list_id)) {
  $lists = $wpdb->get_results("
  SELECT
    l.id,
    l.image_url,
    l.title,
    l.status,
    COUNT(n.id) count_nl
  FROM
    {$wpdb->prefix}observer_lists l
    LEFT JOIN
      {$wpdb->prefix}observer_newsletters n
        ON l.id = n.list_id
  WHERE
    l.status = 'active'
  GROUP BY
    l.id
  ORDER BY title ASC");
  if ($lists) {
?>
    <h1>Click the list you want to view newsletters list for;</h1>
    <div class="container-fluid">
      <div class="row">
        <?php foreach ($lists as $list) { ?>
          <a href="<?php echo add_query_arg('list_id', $list->id); ?>" class="btn btn-default col-md-2 m-2" style="height: 150px; overflow: hidden;">
            <img src="<?php echo $list->image_url; ?>" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 100%; height: auto;">

            <span class="text-white p-2 d-block w-100" style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,.7)"><?php echo $list->title; ?></span>

            <?php if ($list->count_nl > 0) : ?>
              <span class="badge badge-primary" style="position: absolute; top: 0; right: 0;"><?php echo $list->count_nl; ?></span>
            <?php endif; ?>
          </a>
        <?php } ?>
      </div>
    </div>
<?php  }
  return;
}

$list = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}observer_lists WHERE id = {$list_id} LIMIT 1");
if (!$list)
  return;

$per_page = 10;
$page = isset($_GET['p']) ? $_GET['p'] : 1;
$limit_from = ($page - 1) * $per_page;

$total = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->base_prefix}observer_newsletters WHERE list_id = {$list_id}");
$total_pages = ceil($total / $per_page);

$newsletters = $wpdb->get_results("SELECT * FROM {$wpdb->base_prefix}observer_newsletters WHERE list_id = {$list_id} ORDER BY date_for DESC, id DESC LIMIT {$limit_from}, {$per_page}");
?>
<div class="d-flex justify-content-between align-items-center">
  <h1><?php echo $list->title; ?></h1>
  <a href="admin.php?page=brag-observer-manage-newsletter&list_id=<?php echo $list->id; ?>" class="btn btn-sm btn-primary">Create <?php echo $list->title; ?> Newsletter</a>
</div>

<div class="d-flex flex-wrap justify-content-center mb-3">
  <div class="btn">Total: <?php echo $total; ?></div>
  <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
    <a href="<?php echo esc_url(add_query_arg(array('p' => $i))); ?>" class="btn <?php echo $i == $page ? ' btn-dark' : ''; ?>"><?php echo $i; ?></a>
  <?php endfor; ?>
</div>


<table class="table table-sm table-hover">
  <?php if ($newsletters) {
    foreach ($newsletters as $newsletter) {
      // print_r( $newsletter ); exit;
      $newsletter->details = json_decode($newsletter->details);
  ?>

      <tr>
        <th>
          <?php echo $newsletter->details->title; ?>
          <br>
          <small><?php echo $newsletter->details->subject; ?></small>
          <small>(<?php echo date('d M, Y', strtotime($newsletter->date_for)); ?>)</small>
          <div class="badge badge-dark"><?php echo isset($newsletter->details->posts) ? count((array)$newsletter->details->posts) : 0; ?></div>
          <br>
          <small><?php echo $newsletter->status == 1 ? 'Created on MC' : 'Draft'; ?></small>
        </th>
        <td class="text-right" style="width: 50%;">
          <!-- <a href="<?php echo add_query_arg(['page' => 'brag-observer-manage-newsletter', 'action' => 'preview', 'id' => $newsletter->id, 'list_id' => $newsletter->list_id]); ?>" class="btn btn-sm btn-primary my-1" target="_blank" title="Click to preview">Preview (MC)</a> -->

          <!-- <a href="<?php echo add_query_arg(['page' => 'brag-observer-manage-newsletter', 'action' => 'preview', 'id' => $newsletter->id, 'list_id' => $newsletter->list_id, 'template' => 'braze']); ?>" class="btn btn-sm btn-primary my-1" target="_blank" title="Click to preview">Preview</a> -->

          <a href="<?php echo home_url("/preview-newsletter/newsletter/{$newsletter->id}/"); ?>" class="btn btn-sm btn-primary my-1" target="_blank" title="Click to preview">Preview</a>

          <a href="<?php echo add_query_arg(['page' => 'brag-observer-manage-newsletter', 'action' => 'edit', 'id' => $newsletter->id], remove_query_arg('list_id')); ?>" class="btn btn-sm btn-info my-1">Edit</a>

          <a href="<?php echo add_query_arg(['page' => 'brag-observer-manage-newsletter', 'action' => 'copy', 'id' => $newsletter->id], remove_query_arg('list_id')); ?>" class="btn btn-sm btn-warning my-1">Copy</a>

          <!-- <a href="<?php echo add_query_arg(['page' => 'brag-observer-manage-newsletter', 'action' => 'create-on-mc', 'id' => $newsletter->id], remove_query_arg('list_id')); ?>" class="btn btn-sm btn-success my-1">Push to MailChimp</a> -->

          <a href="<?php echo add_query_arg(['page' => 'brag-observer-manage-newsletter', 'action' => 'show-html', 'id' => $newsletter->id], remove_query_arg('list_id')); ?>" target="_blank" class="btn btn-sm btn-success my-1">Show HTML</a>
        </td>
      </tr>
    <?php
    } // For Each $newsletters
    ?>
</table>
<?php
  } else {
?>
  <div class="alert alert-info">No newsletters found</div>
<?php
  } // If $newsletters
  return;
