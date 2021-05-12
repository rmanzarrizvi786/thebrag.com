<?php
global $wpdb;
//$wpdb->prefix = 'wp_jk9t6xrnn4_';
$table = $wpdb->base_prefix . "td_newsletters";
$per_page = 10;
$page = isset( $_GET['p'] ) ? $_GET['p'] : 1;
$limit_from = ( $page - 1 ) * $per_page;
$newsletters = $wpdb->get_results(
        "
	SELECT *
	FROM $table
	ORDER BY id DESC
        LIMIT {$limit_from}, {$per_page}
	"
);

$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$table} ORDER BY id DESC" );

$total_pages = ceil( $total / $per_page );
?>
<div style="text-align: center;">
    <div style="float: right; font-weight: bold; background: #333; color: #fff; padding: 7px;">Total: <?php echo $total; ?></div>
    <?php for( $i = 1; $i <= $total_pages; $i++ ) : ?>
        <a href="<?php echo esc_url( add_query_arg( array( 'p' => $i ) ) ); ?>" class="button"<?php echo $i == $page ? ' style="background: #333; color: #fff;"': ''; ?>><?php echo $i; ?></a>
    <?php endfor; ?>
</div>
<hr>
    <?php
if ( count( $newsletters ) > 0 ):
?>
<table class="newsletter-list">
    <tr>
        <th>Subject / Title</th>
        <th>Date for</th>
        <th>Created</th>
        <th>Last modified</th>
        <th>Status</th>
        <th colspan="5"></th>
    </tr>
<?php
foreach ( $newsletters as $newsletter ):
    $newsletter->details = json_decode( $newsletter->details );
?>
    <tr>
        <td>
            <?php echo $newsletter->details->subject; ?>
            <br>
            <small><?php echo isset($newsletter->details->title) ? $newsletter->details->title : ''; ?></small>
        </td>
        <td><?php echo date('j M, Y', strtotime( $newsletter->date_for ) ); ?></td>
        <td><?php echo date('j M, Y <\s\m\a\l\l\>h:ia<\/\s\m\a\l\l\>', strtotime( $newsletter->created_at ) ); ?></td>
        <td><?php echo date('j M, Y <\s\m\a\l\l\>h:ia<\/\s\m\a\l\l\>', strtotime( $newsletter->updated_at ) ); ?></td>
        <td><?php echo $newsletter->status == 1 ? 'Created on MailChimp' : 'Draft'; ?></td>
        <td><a href="<?php echo esc_url( add_query_arg( array( 'preview' => 1, 'id' => $newsletter->id ) ) ); ?>" class="button">Preview</a></td>
        <td><a href="<?php echo esc_url( add_query_arg( array( 'copy' => 1, 'id' => $newsletter->id ) ) ); ?>" class="button">Duplicate</a></td>
        <td><a href="<?php echo esc_url( add_query_arg( array( 'edit' => 1, 'id' => $newsletter->id ) ) ); ?>" class="button">Edit</a></td>
        <td><a href="<?php echo esc_url( add_query_arg( array( 'delete' => 1, 'id' => $newsletter->id ) ) ); ?>" class="button delete">Delete</a></td>
        <td><a href="<?php echo esc_url( add_query_arg( array( 'create-on-mc' => 1, 'id' => $newsletter->id ) ) ); ?>" class="button">Push to MailChimp</a></td>
    </tr>
<?php
endforeach;
?>
</table>
<?php
endif;
?>
