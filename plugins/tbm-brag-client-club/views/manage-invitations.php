<?php
global $wpdb;
wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css');

wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'));

$id = isset($_GET['id']) ? absint($_GET['id']) : null;

$event = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}client_club_events WHERE `id` = '{$id}' LIMIT 1");

if (!$event)
    return;
?>
<h1>Invite members to <strong><?php echo $event->title; ?></strong> <small>(<?php echo $event->event_date; ?>)</small></h1>
<?php
/* $members = $wpdb->get_results("SELECT 
    u.`ID`, u.`user_email`
    FROM {$wpdb->prefix}users u
    JOIN {$wpdb->prefix}client_club_members m ON m.`user_id` = u.`ID`
    WHERE m.`status` = 'active'
    AND u.`ID` NOT IN (
        SELECT i.`user_id` FROM {$wpdb->prefix}client_club_event_invites i WHERE i.`event_id` = '{$id}'
        )
"); */
?>
<form method="post" id="invite-to-event">
    <div>
        <!-- <input type="file" id="file-event-invite-emails" class="form-control" accept=".csv"> -->
        <textarea id="club-event-invite-emails" class="form-control" placeholder="Email addresses (one per line)" rows="10"></textarea>
        <button type="submit" class="btn btn-primary btn-submit mt-3">Submit</button>
        <table id="result" class="result my-2 table table-sm table-striped"></table>
    </div>
</form>

<hr>

<?php
$invites = $wpdb->get_results("SELECT 
u.`ID`, u.`user_email`, i.`status`
FROM {$wpdb->prefix}users u
JOIN {$wpdb->prefix}client_club_event_invites i ON i.`user_id` = u.`ID`
WHERE i.`event_id` = '{$id}'
");
?>
<h2>Invitations</h2>
<table class="table table-sm table-striped">
    <tr>
        <th>Email</th>
        <th>Status</th>
    </tr>
    <?php
    if ($invites) :
        foreach ($invites as $invite) : ?>
            <tr class="<?php echo 'yes' == $invite->status ? 'text-success' : ('no' == $invite->status ? 'text-danger' : ''); ?>">
                <td><?php echo $invite->user_email; ?></td>
                <td><?php echo is_null($invite->status) || 'invited' == $invite->status ? 'Awaiting response' : ('yes' == $invite->status ? 'Attending' : 'Not attending'); ?></td>
            </tr>
    <?php
        endforeach;
    endif; ?>
</table>

<script>
    jQuery(document).ready(function($) {
        var select2Members = $('.select2-members').select2({
            placeholder: "Search for members",
            // allowClear: true
        });

        $('#invite-to-event').on('submit', function(e) {
            e.preventDefault();
            var theForm = $(this);
            var btnSubmit = theForm.find('.btn-submit');
            btnSubmit.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');

            theForm.find('.result').removeClass('text-success text-danger').prepend('<tr><td class="result-status">Processing, please wait...</td></tr>');

            /**
             * Using file upload
             */
            /* var formData = new FormData();
            formData.append('action', 'invite_to_brag_client_event');

            var file = $('#file-event-invite-emails')[0].files[0];
            formData.append('csv', file);
            formData.append('event_id', <?php echo $id; ?>);

            $.post({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: formData,
                processData: false,
                contentType: false
            }).success(function(res) {
                // $('#result').prepend(res.data);

                theForm.find('.result').prepend(res.data);
                theForm.find('.result').find('.result-status').hide();
                btnSubmit.prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
                return;
            }).error(function(e) {
                console.error(e);
                btnSubmit.prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
                return;
            }).done(function() {
                btnSubmit.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
            });
 */

            // var members = theForm.find('select[name="members[]"]').val();

            $('.result').html('');

            $.post({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'invite_to_brag_client_event',
                    event_id: <?php echo $id; ?>,
                    emails: $('#club-event-invite-emails').val(),
                }
            }).success(function(res) {
                theForm.find('.result').prepend(res.data);
                theForm.find('.result').find('.result-status').hide();
                btnSubmit.prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
                return;
            }).done(function() {
                btnSubmit.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
            });
        })
    });
</script>
<style>
    .select2-container {
        width: 100% !important
    }
</style>