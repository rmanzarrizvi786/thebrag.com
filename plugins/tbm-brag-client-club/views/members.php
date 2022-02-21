<?php
global $wpdb;
wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css');
?>
<h1><?php echo $this->plugin_title; ?></h1>

<div class="mt-3">
    <h2>Invite to <?php echo $this->plugin_title; ?></h2>
    <form id="invite-to-club">
        <!-- <div class="col d-flex align-items-center"> -->
        <!-- <input type="email" id="club-member-email" class="form-control" placeholder="Email address"> -->

        <!-- <input type="file" id="file-club-member-emails" class="form-control" accept=".csv"> -->
        <textarea id="club-member-emails" class="form-control" placeholder="Email addresses (one per line)" rows="10"></textarea>

        <button type="submit" class="btn btn-primary btn-submit mt-3">Submit</button>
        <!-- <div class="result mx-2"></div> -->
        <table class="table table-sm table-striped result mt-2"></table>
    </form>
</div>

<div class="mt-3">
    <h2>Past Invitations</h2>
    <?php
    $members = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}client_club_members");
    if ($members) :
    ?>
        <table class="table table-sm">
            <tr>
                <th>Email</th>
                <th>Status</th>
                <th>Welcome package</th>
            </tr>
            <?php foreach ($members as $member) : ?>
                <tr id="invite-<?php echo $member->id; ?>">
                    <td><?php echo $member->email; ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div><span class="invite_status text-uppercase"><?php echo $member->status; ?></span></div>

                            <?php if (!is_null($member->status) && !is_null($member->user_id)) : ?>
                                <div class="ml-2">
                                    <button class="btn btn-sm btn-action <?php echo in_array($member->status, ['joined', 'active']) ? 'btn-danger' : 'btn-success'; ?>" data-id="<?php echo $member->id; ?>" data-newstatus="<?php echo in_array($member->status, ['joined', 'active']) ? 'inactive' : 'active'; ?>" data-userid="<?php echo $member->user_id; ?>">
                                        <?php echo in_array($member->status, ['joined', 'active']) ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <?php if ('active' == $member->status) : ?>
                            <div class="d-flex welcome-package-action-wrap align-items-center">
                                <?php
                                $welcome_package_status = null;
                                $welcome_package_status_um = get_user_meta($member->user_id, 'bcc_welcome_package_status', true) ?: null;
                                if ($welcome_package_status_um) {
                                    $welcome_package_status_um = json_decode($welcome_package_status_um);
                                    $welcome_package_status = $welcome_package_status_um->status;
                                }
                                ?>
                                <div class="welcome-package-status <?php echo 'sent' == $welcome_package_status ? 'badge bg-success mr-2 text-white' : ('not-sent' == $welcome_package_status ? 'badge bg-secondary mr-2 text-white' : ''); ?>" id="welcome-package-status-<?php echo $member->user_id; ?>">
                                    <?php
                                    if ($welcome_package_status) {
                                        echo 'Marked ' . $welcome_package_status;
                                        echo ' by ' . get_user_by('id', $welcome_package_status_um->user_id)->user_nicename;
                                        echo ' on ' . date('d M, Y', strtotime($welcome_package_status_um->updated_at));
                                    }
                                    ?>
                                </div>
                                <?php
                                // if ('sent' == $welcome_package_status) :
                                ?>
                                <button class=" btn btn-sm btn-warning btn-sent-welcome-package <?php echo 'sent' == $welcome_package_status ? '' : 'd-none'; ?>" data-userid="<?php echo $member->user_id; ?>" data-status="not-sent">Click if not sent</button>
                                <?php // else :
                                ?>
                                <button class="btn btn-sm btn-info btn-sent-welcome-package <?php echo 'sent' != $welcome_package_status ? '' : 'd-none'; ?>" data-userid="<?php echo $member->user_id; ?>" data-status="sent">Click if sent</button>
                                <?php // endif; 
                                ?>
                            </div>
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

        $('.btn-sent-welcome-package').on('click', function() {
            var user_id = $(this).data('userid');
            if (!user_id)
                return false;

            var status = $(this).data('status');
            if (!status)
                return false;

            var theBtn = $(this).addClass('blink');
            var container = theBtn.closest('.welcome-package-action-wrap');

            theBtn.prop('disabled', true);

            $.post({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'bcc_toggle_welcome_package_sent',
                    user_id: user_id,
                    status: status,
                }
            }).success(function(res) {
                if (res.success) {

                    container.find('.btn-sent-welcome-package').addClass('d-none');
                    if ('sent' == status) {
                        container.find('.btn-sent-welcome-package[data-status="not-sent"]').removeClass('d-none');
                    } else {
                        container.find('.btn-sent-welcome-package[data-status="sent"]').removeClass('d-none');
                    }

                    // theBtn.hide();
                    $('#welcome-package-status-' + user_id).text(res.data);
                    // theBtn.parent().append('SENT');
                } else {
                    alert(res.data);
                    theBtn.prop('disabled', false).removeClass('blink');
                    return;
                }
            }).done(function() {
                theBtn.prop('disabled', false).removeClass('blink');
            });
        });

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
                    action: 'update_status_brag_client_club',
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
                } else {
                    alert(res.data);
                    return;
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

            $('.result').html('');

            /**
             * Using file upload
             */
            /* var formData = new FormData();
            formData.append('action', 'invite_to_brag_client_club');

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
            }); */

            /**
             * Using Textarea
             */

            $.post({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'invite_to_brag_client_club',
                    emails: $('#club-member-emails').val()
                }
            }).success(function(res) {
                theForm.find('.result').prepend(res.data);
                theForm.find('.result').find('.result-status').hide();

                btnSubmit.prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');

                // $('#club-member-emails').val('');

                return;
            }).error(function(e) {
                console.error(e);
                btnSubmit.prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
                return;
            });
        })
    })
</script>