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

    add_action('wp_ajax_invite_to_bragger_client_club', [$this, 'ajax_invite_to_club']);
    add_action('wp_ajax_update_status_bragger_client_club', [$this, 'ajax_update_status']);
    add_action('wp_ajax_invite_to_bragger_client_event', [$this, 'ajax_invite_to_event']);

    add_action('wp_ajax_response_to_bragger_client_event', [$this, 'ajax_rsponse_to_event']);
    add_action('wp_ajax_nopriv_response_to_bragger_client_event', [$this, 'ajax_rsponse_to_event']);

    // Activation
    register_activation_hook(__FILE__, [$this, 'activate']);

    // Deactivation
    register_deactivation_hook(__FILE__, [$this, 'deactivate']);

    add_action('cron_hook_bragger_client_club_invites', [$this, 'exec_cron_club_invites']);
    add_action('cron_hook_bragger_client_event_invites', [$this, 'exec_cron_event_invites']);
  }

  public function activate()
  {
    if (!wp_next_scheduled('cron_hook_bragger_client_club_invites', array(NULL, NULL))) {
      wp_schedule_event(strtotime('00:00:00'), 'every10minutes', 'cron_hook_bragger_client_club_invites', array(NULL, NULL));
    }

    if (!wp_next_scheduled('cron_hook_bragger_client_event_invites', array(NULL, NULL))) {
      wp_schedule_event(strtotime('00:05:00'), 'every10minutes', 'cron_hook_bragger_client_event_invites', array(NULL, NULL));
    }
  }

  /**
   * Hook cron_schedules
   */
  public function _cron_schedules($schedules)
  {
    $schedules['every10minutes'] = array(
      'interval' => 10 * 60,
      'display'  => esc_html__('Every 10 Minutes'),
    );
  }

  public function deactivate()
  {
    $crons = _get_cron_array();
    if (empty($crons)) {
      return;
    }
    $hooks = ['cron_hook_bragger_client_club_invites', 'cron_hook_bragger_client_event_invites'];
    foreach ($crons as $timestamp => $cron) {
      foreach ($hooks as $hook) {
        if (!empty($cron[$hook])) {
          unset($crons[$timestamp][$hook]);
        }
      }
      if (empty($crons[$timestamp])) {
        unset($crons[$timestamp]);
      }
    }
    _set_cron_array($crons);
  }

  public function admin_menu()
  {
    $main_menu = add_menu_page(
      $this->plugin_title,
      $this->plugin_title,
      'administrator',
      $this->plugin_slug,
      [$this, 'index'],
      'dashicons-superhero',
      10
    );
    add_submenu_page($this->plugin_slug, "{$this->plugin_title} Members", 'Members', 'administrator', "{$this->plugin_slug}", [$this, 'index']);
    add_submenu_page($this->plugin_slug, "{$this->plugin_title} Events", 'Events', 'administrator', "{$this->plugin_slug}-events", [$this, 'manage_events']);
  }

  public function exec_cron_club_invites()
  {
    global $wpdb;
    $invites = $wpdb->get_results("SELECT
        m.`id`,
        m.`email`
      FROM {$wpdb->prefix}client_club_members m
      WHERE m.`status` IS NULL
      LIMIT 40
    ");
    if ($invites) {
      foreach ($invites as $invite) {
        /**
         * Trigger Event in Braze
         */
        require_once WP_PLUGIN_DIR . '/brag-observer/classes/braze.class.php';
        $braze = new \Braze();
        $braze->setMethod('POST');

        $brazeEventRes = $braze->triggerEventByEmail($invite->email, 'brag_invited_bragger_client_club', [
          'login_url' => home_url("/bragger-client-club/"),
        ]);

        if (201 ==  $brazeEventRes['code']) {
          $wpdb->update(
            $wpdb->prefix . 'client_club_members',
            ['status' => 'invited',],
            ['id' => $invite->id],
            ['%s',],
            ['%d']
          );
        }
      }
    }
  } // exec_cron_club_invites()

  public function exec_cron_event_invites()
  {
    global $wpdb;
    $invites = $wpdb->get_results("SELECT
        i.`id` invite_id,
        i.`user_id`,
        i.`guid`,
        e.`id` event_id,
        e.`title` event_title,
        e.`event_date`,
        e.`location` event_location
      FROM {$wpdb->prefix}client_club_event_invites i
      JOIN {$wpdb->prefix}client_club_events e ON e.`id` = i.`event_id`
      -- JOIN {$wpdb->prefix}users u ON u.`ID` = i.`user_id`
      WHERE i.`status` IS NULL
      LIMIT 40
    ");
    if ($invites) {
      foreach ($invites as $invite) {
        /**
         * Trigger Event in Braze
         */
        require_once WP_PLUGIN_DIR . '/brag-observer/classes/braze.class.php';
        $braze = new \Braze();
        $braze->setMethod('POST');

        $brazeEventRes = $braze->triggerEvent($invite->user_id, 'brag_invited_bragger_client_event', [
          'event_title' => $invite->event_title,
          'event_date' => $invite->event_date,
          'location' => $invite->event_location,
          'rsvp_url' => home_url("/bragger-client-club/rsvp-event/?id={$invite->event_id}&guid={$invite->guid}")
        ]);

        if (201 ==  $brazeEventRes['code']) {
          $wpdb->update(
            $wpdb->prefix . 'client_club_event_invites',
            ['status' => 'invited',],
            ['id' => $invite->invite_id],
            ['%s',],
            ['%d']
          );
        }
      }
    }
  } // exec_cron_event_invites()

  public function index()
  {
    include __DIR__ . '/views/members.php';
  } // index()

  public function manage_events()
  {
    $action = isset($_GET['action']) ? trim($_GET['action']) : 'index';
    switch ($action):
      case 'invite':
        include __DIR__ . '/views/invite-to-event.php';

        break;
      case 'index':
      default:
        include __DIR__ . '/views/events.php';
        break;
    endswitch;
  } // manage_events()

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

  public function ajax_invite_to_club()
  {
    global $wpdb;

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (!is_email($email)) {
      wp_send_json_error("Invalid Email");
      die();
    }

    /**
     * Add to DB
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
        'status' => null, //'invited',
        'created_at' => current_time('mysql')
      ],
      [
        '%s', '%s', '%s'
      ]
    );

    wp_send_json_success("{$email} will be invited to join the club!");
    die();
  } // ajax_invite_to_club()

  public function ajax_invite_to_event()
  {
    global $wpdb;

    $user_ids = isset($_POST['members']) ? $_POST['members'] : [];

    if (!is_array($user_ids) || empty($user_ids)) {
      wp_send_json_error('Empty members list');
      die();
    }

    $event_id = isset($_POST['event_id']) ? absint($_POST['event_id']) : null;
    if (is_null($event_id) || 0 == $event_id) {
      wp_send_json_error('Missing Event ID');
      die();
    }

    /**
     * Add to DB
     */
    $response = '';
    foreach ($user_ids as $user_id) {

      $user = get_user_by('ID', $user_id);

      $guid = $this->generate_guid();

      $wpdb->insert(
        $wpdb->prefix . 'client_club_event_invites',
        [
          'event_id' => $event_id,
          'user_id' => $user_id,
          // 'status' => 'invited',
          'guid' => $guid,
        ],
        ['%d', '%d', '%s', '%s',]
      );

      $response .= "<tr><td class=\"text-success\">{$user->user_email} will be invited</td></tr>";
    }
    wp_send_json_success($response);
    die();
  } // ajax_invite_to_event()

  public function ajax_rsponse_to_event()
  {
    global $wpdb;

    $event_id = isset($_POST['event_id']) ? absint($_POST['event_id']) : null;
    $guid = isset($_POST['guid']) ? trim($_POST['guid']) : null;

    // $invite = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}client_club_event_invites i WHERE i.`event_id` = '{$event_id}' AND i.`guid` = '{$guid}' LIMIT 1 ");
    $invite = $wpdb->get_row("SELECT
        i.`id`,
        i.`user_id`,
        e.`title` event_title,
        e.`event_date`,
        e.`location` event_location,
        i.`guid`
      FROM {$wpdb->prefix}client_club_event_invites i
      JOIN {$wpdb->prefix}client_club_events e ON i.`event_id` = e.`id`
      WHERE i.`event_id` = '{$event_id}' AND i.`guid` = '{$guid}'
      LIMIT 1
");


    if (!$invite) {
      wp_send_json_error('Sorry, the invitation was not found :(');
      die();
    }

    $response = isset($_POST['response']) ? trim($_POST['response']) : null;
    if (!in_array($response, ['yes', 'no'])) {
      wp_send_json_error('Sorry, invalid response :(');
      die();
    }

    $wpdb->update(
      $wpdb->prefix . 'client_club_event_invites',
      [
        'status' => $response,
        'updated_at' => current_time('mysql')
      ],
      ['id' => $invite->id],
      ['%s', '%s'],
      ['%d']
    );

    /**
     * Trigger Event in Braze
     */
    require_once WP_PLUGIN_DIR . '/brag-observer/classes/braze.class.php';
    $braze = new \Braze();
    $braze->setMethod('POST');

    $brazeEventRes = $braze->triggerEvent($invite->user_id, 'brag_rsvped_bragger_client_event', [
      'event_title' => $invite->event_title,
      'event_date' => $invite->event_date,
      'location' => $invite->event_location,
      'rsvp' => $response,
      'rsvp_url' => home_url("/bragger-client-club/rsvp-event/?id={$event_id}&guid={$invite->guid}")
    ]);

    $message = 'yes' == $response ? 'Thank you, see you there!' : 'You wil be missed!';

    wp_send_json_success($message);
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

  private function generate_guid()
  {
    if (function_exists('com_create_guid') === true) {
      return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
  }
}

new BraggerClientClub();
