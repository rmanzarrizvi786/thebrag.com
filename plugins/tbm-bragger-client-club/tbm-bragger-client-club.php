<?php

namespace TBM;

/**
 * Plugin Name: TBM Bragger Client Club
 * Plugin URI: https://thebrag.media/
 * Description:
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI:
 */

class BraggerClientClub
{

  protected $plugin_title;
  protected $plugin_name;
  protected $plugin_slug;

  protected static $_instance;

  public function __construct()
  {
    $this->plugin_title = 'Bragger Client Club';
    $this->plugin_name = 'tbm_bragger_client_club';
    $this->plugin_slug = 'tbm-bragger-client-club';

    add_action('admin_menu', array($this, 'admin_menu'));

    add_action('wp_ajax_invite_to_bragger_client_club', [$this, 'invite']);
  }

  public function admin_menu()
  {
    $main_menu = add_menu_page(
      $this->plugin_title,
      $this->plugin_title,
      'administrator',
      $this->plugin_slug,
      array($this, 'index'),
      'dashicons-superhero',
      10
    );
  }

  public function index()
  {
    global $wpdb;
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css');
?>
    <h1><?php echo $this->plugin_title; ?></h1>

    <div class="mt-3">
      <h2>Invite someone to <?php echo $this->plugin_title; ?></h2>
      <form id="invite-to-club">
        <div class="row">
          <div class="col d-flex align-items-center">
            <input type="email" id="club-member-email" class="form-control" placeholder="Email address">
          </div>
          <div class="col d-flex align-items-center">
            <button type="submit" class="btn btn-primary btn-submit">Submit</button>
            <div class="result mx-2"></div>
          </div>
        </div>
      </form>
    </div>

    <div class="mt-3">
      <h2>Past Invitations</h2>
      <?php
      $invitees = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}client_club");
      if ($invitees) :
      ?>
        <table class="table table-sm">
          <tr>
            <th>Email</th>
            <th>Status</th>
            <th>Invited at</th>
            <th>Joined at</th>
          </tr>
          <?php foreach ($invitees as $invitee) : ?>
            <tr>
              <td><?php echo $invitee->email; ?></td>
              <td><?php echo $invitee->status; ?></td>
              <td><?php echo $invitee->created_at; ?></td>
              <td><?php echo $invitee->joined_at; ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>

    <script>
      jQuery(document).ready(function($) {
        $('#invite-to-club').on('submit', function(e) {
          e.preventDefault();
          var theForm = $(this);
          var btnSubmit = $(this).find('.btn-submit');
          btnSubmit.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');

          theForm.find('.result').removeClass('text-success text-danger').text('Processing, please wait...');

          $.post({
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
            theForm.find('.result').addClass('text-success').text('Success!');
            console.info(res.data);
            btnSubmit.prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
            $('#club-member-email').val('').focus();
            return;
          }).error(function(e) {
            theForm.find('.result').addClass('text-danger').text(res.data);
            console.error(e);
            btnSubmit.prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
            return;
          });
        })
      })
    </script>
<?php
  }

  public function invite()
  {
    global $wpdb;

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (!is_email($email)) {
      wp_send_json_error("Invalid Email");
      die();
    }

    /**
     * Save to DB
     */
    // Check if already in DB
    $check = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}client_club WHERE `email` = '{$email}' LIMIT 1");
    if ($check) {
      wp_send_json_error("Already invited, status: {$check->status}");
      die();
    }
    $wpdb->insert(
      $wpdb->prefix . 'client_club',
      [
        'email' => $email,
        'status' => 'invited',
        'created_at' => current_time('mysql')
      ],
      [
        '%s', '%s', '%s'
      ]
    );

    /**
     * Trigger Event in Braze
     */
    require_once WP_PLUGIN_DIR . '/brag-observer/classes/braze.class.php';
    $braze = new \Braze();
    $braze->setMethod('POST');

    $res_track = $braze->triggerEventByEmail($email, 'brag_invited_bragger_client_club', [
      'login_url' => home_url("/bragger-client-club/"),
    ]);

    wp_send_json_success($res_track);
    die();
  }
}

new BraggerClientClub();
