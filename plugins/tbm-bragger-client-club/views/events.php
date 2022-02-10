<?php
global $wpdb;
wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css');
?>
<h1><?php echo $this->plugin_title; ?> Events</h1>
<?php
$events = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}client_club_events ORDER BY -`event_date` DESC");
if ($events) :
?>
    <table class="table table-sm">
        <tr>
            <th>Title</th>
            <th>Event Date</th>
            <th>Event Location</th>
            <th>Action</th>
        </tr>
        <?php foreach ($events as $event) : ?>
            <tr>
                <td><?php echo $event->title; ?></td>
                <td><?php echo !is_null($event->event_date) ? date('d M, Y', strtotime($event->event_date)) : ''; ?></td>
                <td><?php echo $event->location; ?></td>
                <td><a href="<?php echo add_query_arg(['action' => 'invite', 'id' => $event->id]); ?>">Invite</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif;
