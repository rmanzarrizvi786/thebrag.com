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

    add_action('wp_ajax_invite_to_bragger_client_club', [$this, 'ajax_invite']);
    add_action('wp_ajax_update_status_bragger_client_club', [$this, 'ajax_update_status']);
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
      $invites = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}client_club_members");
      if ($invites) :
      ?>
        <table class="table table-sm">
          <tr>
            <th>Email</th>
            <th>Status</th>
            <th>Invited at</th>
            <th>Joined at</th>
            <th>Updated at</th>
            <th>Action</th>
          </tr>
          <?php foreach ($invites as $invite) : ?>
            <tr id="invite-<?php echo $invite->id; ?>">
              <td><?php echo $invite->email; ?></td>
              <td><span class="invite_status text-uppercase"><?php echo $invite->status; ?></span></td>
              <td><?php echo $invite->created_at; ?></td>
              <td><?php echo $invite->joined_at; ?></td>
              <td><?php echo $invite->updated_at; ?></td>
              <td>
                <?php if ('invited' != $invite->status) : ?>
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
  } // index()

  public function ajax_update_status()
  {
    $invite_id = isset($_POST['invite_id']) ? absint($_POST['invite_id']) : null;
    $user_id = isset($_POST['user_id']) ? absint($_POST['user_id']) : null;
    $new_status = isset($_POST['new_status']) ? trim($_POST['new_status']) : null;

    if (
      is_null($invite_id) || 0 == $invite_id ||
      is_null($user_id) || 0 == $user_id ||
      is_null($new_status)
    ) {
      wp_send_json_error("Invalid Data");
      die();
    }

    if ($this->updateStatus($user_id, $new_status)) {
      wp_send_json_success();
    }
    wp_send_json_error("Error!");
    die();
  }

  public function ajax_invite()
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
    $check = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}client_club_members WHERE `email` = '{$email}' LIMIT 1");
    if ($check) {
      wp_send_json_error("Already invited, status: {$check->status}");
      die();
    }
    $wpdb->insert(
      $wpdb->prefix . 'client_club_members',
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

  /**
   * Update client club status in DB and Auth0
   *
   * @param int $user_id
   * @param string $status
   */
  public function updateStatus($user_id, $status)
  {
    global $wpdb;

    $user = get_user_by('ID', $user_id);
    if (!$user)
      return false;

    require get_template_directory() . '/vendor/autoload.php';

    $dotenv = \Dotenv\Dotenv::createImmutable(ABSPATH);
    $dotenv->load();

    $auth0_api = new \Auth0\SDK\API\Authentication(
      $_ENV['AUTH0_DOMAIN'],
      $_ENV['AUTH0_CLIENT_ID']
    );

    $config = [
      'client_secret' => $_ENV['AUTH0_CLIENT_SECRET'],
      'client_id' => $_ENV['AUTH0_CLIENT_ID'],
      'audience' => $_ENV['AUTH0_MANAGEMENT_AUDIENCE'],
    ];

    $wp_auth0_id = get_user_meta($user_id, $wpdb->prefix . 'auth0_id', true);
    if (!$wp_auth0_id) {
      $wp_auth0_id = get_user_meta($user_id, 'wp_auth0_id', true);
    }

    if ($wp_auth0_id) {
      $auth0_token = $auth0_api->oauth_token(
        [
          'client_id' => $config['client_id'],
          'client_secret' => $config['client_secret'],
          'audience' => 'https://thebragmedia.au.auth0.com/api/v2/',
          'grant_type' => 'client_credentials'
        ]
      );
      if (isset($auth0_token['access_token'])) {
        if (isset($auth0_token['access_token'])) {
          $response_user = wp_remote_post(
            "https://thebragmedia.au.auth0.com/api/v2/users/{$wp_auth0_id}",
            [
              'method' => 'PATCH',
              'headers' => [
                'authorization' => "Bearer {$auth0_token['access_token']}",
              ],
              'body' => [
                'app_metadata' => [
                  'client_club_member' => $status
                ]
              ]
            ]
          );
          if (is_wp_error($response_user)) {
            $error_message = $response_user->get_error_message();
            return $error_message;
          } else {
            $response_user = json_decode(wp_remote_retrieve_body($response_user));
            if (isset($response_user->user_id)) {
              $wpdb->update(
                $wpdb->prefix . 'client_club_members',
                [
                  'status' => $status,
                  'user_id' => $user_id,
                  'updated_at' => current_time('mysql')
                ],
                [
                  'email' => $user->user_email
                ],
                ['%s', '%d', '%s'],
                ['%s']
              );
            }
            return true;
          }
        }
      }
      return false;
    }
    return false;
  } // updateStatus()
}

new BraggerClientClub();
