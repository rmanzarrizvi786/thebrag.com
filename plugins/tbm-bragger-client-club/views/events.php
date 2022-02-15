<?php
global $wpdb;
wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css');
?>
<h1><?php echo $this->plugin_title; ?> Events</h1>
<?php
$events = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}client_club_events WHERE `event_date` >= '" . date('Y-m-d') . "' OR `event_date` IS NULL ORDER BY -`event_date` DESC");
if ($events) :
?>
    <table class="table table-sm table-striped">
        <tr>
            <th>Title</th>
            <th>Event Date</th>
            <th>Event Location</th>
            <th>Action</th>
            <th>Invitations</th>
        </tr>
        <?php foreach ($events as $event) :
            $invites = $wpdb->get_results("SELECT `status`, COUNT(1) `total` FROM {$wpdb->prefix}client_club_event_invites WHERE `event_id` = '{$event->id}' GROUP BY `status`");

            // echo '<pre>' . print_r($invites, true) . '</pre>';
        ?>
            <tr>
                <td><?php echo $event->title; ?></td>
                <td><?php echo !is_null($event->event_date) ? date('d M, Y', strtotime($event->event_date)) : ''; ?></td>
                <td><?php echo $event->location; ?></td>
                <td><a href="<?php echo add_query_arg(['action' => 'invitations', 'id' => $event->id]); ?>" class="btn btn-sm btn-dark">View Invitations</a></td>
                <td>
                    <?php if ($invites) { ?>
                        <div class="mr-4 d-flex justify-content-end">
                            <?php foreach ($invites as $invite) { ?>
                                <div class="text-white px-2 py-1 rounded bg-<?php echo is_null($invite->status) || 'invited' == $invite->status ? 'info' : ('yes' == $invite->status ? 'success' : 'danger'); ?>">
                                    <?php
                                    // echo is_null($invite->status) || 'invited' == $invite->status ? 'Awaiting response' : ('yes' == $invite->status ? 'Attending' : 'Not attending');
                                    echo $invite->total;
                                    // echo 
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif;
