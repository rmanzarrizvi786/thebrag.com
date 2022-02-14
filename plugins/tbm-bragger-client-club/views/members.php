<?php
global $wpdb;
wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css');
?>
<h1><?php echo $this->plugin_title; ?></h1>

<div class="mt-3">
    <h2>Invite to <?php echo $this->plugin_title; ?></h2>
    <form id="invite-to-club">
        <div class="row">
            <div class="col d-flex align-items-center">
                <!-- <input type="email" id="club-member-email" class="form-control" placeholder="Email address"> -->
                <input type="file" id="file-club-member-emails" class="form-control" accept=".csv">
            </div>
            <div class="col d-flex align-items-center">
                <button type="submit" class="btn btn-primary btn-submit">Submit</button>
                <!-- <div class="result mx-2"></div> -->
            </div>
        </div>
        <table class="result mt-2"></table>
    </form>
</div>

<div class="mt-3">
    <h2>Past Invitations</h2>
    <?php
    $invites = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}client_club_members");
    if ($invites) :
    ?>
        <table class="table table-sm">
            <tr>
                <th>Email</th>
                <th>Status</th>
                <!-- <th>Invited at</th>
                <th>Joined at</th>
                <th>Updated at</th> -->
                <th>Action</th>
            </tr>
            <?php foreach ($invites as $invite) : ?>
                <tr id="invite-<?php echo $invite->id; ?>">
                    <td><?php echo $invite->email; ?></td>
                    <td><span class="invite_status text-uppercase"><?php echo $invite->status; ?></span></td>
                    <!-- <td><?php echo $invite->created_at; ?></td>
                    <td><?php // echo $invite->joined_at; 
                        ?></td>
                    <td><?php // echo $invite->updated_at; 
                        ?></td> -->
                    <td>
                        <?php if (!is_null($invite->status) && !is_null($invite->user_id)) : ?>
                            <button class="btn btn-sm btn-action <?php echo in_array($invite->status, ['joined', 'active']) ? 'btn-danger' : 'btn-success'; ?>" data-id="<?php echo $invite->id; ?>" data-newstatus="<?php echo in_array($invite->status, ['joined', 'active']) ? 'inactive' : 'active'; ?>" data-userid="<?php echo $invite->user_id; ?>">
                                <?php echo in_array($invite->status, ['joined', 'active']) ? 'Deactivate' : 'Activate'; ?>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<style>
    .blink {
        animation: blinker .5s linear infinite;
    }

    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }
</style>

<script>
    jQuery(document).ready(function($) {

        $('.btn-action').on('click', function() {
            var invite_id = $(this).data('id');
            if (!invite_id)
                return false;

            var user_id = $(this).data('userid');
            if (!user_id)
                return false;

            var btnAction = $(this);

            var newStatus = $(this).data('newstatus');

            btnAction.prop('disabled', true).addClass('blink');
            $('#invite-' + invite_id).find('.invite_status').addClass('blink');

            $.post({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'update_status_bragger_client_club',
                    invite_id: invite_id,
                    user_id: user_id,
                    new_status: newStatus
                }
            }).success(function(res) {
                if (res.success) {
                    if (newStatus == 'active') {
                        btnAction.removeClass('btn-success').addClass('btn-danger').text('Deactivate').data('newstatus', 'inactive');
                        $('#invite-' + invite_id).find('.invite_status').text('Active');
                        return;
                    }
                    if (newStatus == 'inactive') {
                        btnAction.removeClass('btn-danger').addClass('btn-success').text('Activate').data('newstatus', 'active');
                        $('#invite-' + invite_id).find('.invite_status').text('Inactive');
                        return;
                    }
                }
            }).done(function() {
                btnAction.prop('disabled', false).removeClass('blink');
                $('#invite-' + invite_id).find('.invite_status').removeClass('blink');
            });
        });

        $('#invite-to-club').on('submit', function(e) {
            e.preventDefault();

            var theForm = $(this);
            var btnSubmit = $(this).find('.btn-submit');
            btnSubmit.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');

            theForm.find('.result').removeClass('text-success text-danger').prepend('<tr><td class="result-status">Processing, please wait...</td></tr>');

            /**
             * Using file upload
             */
            var formData = new FormData();
            formData.append('action', 'invite_to_bragger_client_club');

            var file = $('#file-club-member-emails')[0].files[0];
            formData.append('csv', file);

            $.post({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: formData,
                processData: false,
                contentType: false
            }).success(function(res) {
                theForm.find('.result').prepend(res.data);
                theForm.find('.result').find('.result-status').hide();

                btnSubmit.prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');

                return;
                // $('#club-member-email').val('').focus();
                return;
            }).error(function(e) {
                // theForm.find('.result').addClass('text-danger').text(res.data);
                console.error(e);
                btnSubmit.prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
                return;
            });

            /**
             * Using Single Email Input
             */
            /* $.post({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'invite_to_bragger_client_club',
                    email: $('#club-member-email').val()
                }
            }).success(function(res) {
                if (!res.success) {
                    console.error(res);
                    theForm.find('.result').addClass('text-danger').text(res.data);
                    btnSubmit.prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
                    return;
                }
                theForm.find('.result').addClass('text-success').text(res.data);
                console.info(res.data);
                btnSubmit.prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
                $('#club-member-email').val('').focus();
                return;
            }).error(function(e) {
                theForm.find('.result').addClass('text-danger').text(res.data);
                console.error(e);
                btnSubmit.prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
                return;
            }); */
        })
    })
</script>