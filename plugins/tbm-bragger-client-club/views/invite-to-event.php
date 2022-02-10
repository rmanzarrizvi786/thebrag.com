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
$members = $wpdb->get_results("SELECT 
    u.`ID`, u.`user_email`
    FROM {$wpdb->prefix}users u
    JOIN {$wpdb->prefix}client_club_members m ON m.`user_id` = u.`ID`
    WHERE m.`status` = 'active'
");
if (!$members)
    return;
?>
<form method="post" id="invite-to-event">
    <div class="px-2">
        <select class="select2-members form-control" name="members[]" multiple="multiple">
            <?php foreach ($members as $member) : ?>
                <option value="<?php echo $member->ID; ?>"><?php echo $member->user_email; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-submit mt-3">Submit</button>
        <table id="result" class="result my-2">
        </table>
    </div>
</form>
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

            var members = theForm.find('select[name="members[]"]').val();

            $('.result').html('');

            $.post({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'invite_to_bragger_client_event',
                    event_id: <?php echo $id; ?>,
                    members: members,
                }
            }).success(function(res) {
                $('#result').prepend(res.data);
                if (res.success) {
                    // select2Members.val(null).trigger('change');
                }
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