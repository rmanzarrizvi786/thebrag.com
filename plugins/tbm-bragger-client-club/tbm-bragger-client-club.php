<?php

namespace TBM;

use AmpProject\Validator\Spec\Tag\P;

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
    add_action('wp_ajax_bcc_toggle_welcome_package_sent', [$this, 'ajax_toggle_welcome_package_sent']);

    add_action('wp_ajax_response_to_bragger_client_event', [$this, 'ajax_rsponse_to_event']);
    add_action('wp_ajax_nopriv_response_to_bragger_client_event', [$this, 'ajax_rsponse_to_event']);

    add_action('wp_ajax_submit_rs_mag_new_subscription', [$this, 'ajax_rs_mag_new_subscription']);
    add_action('wp_ajax_nopriv_submit_rs_mag_new_subscription', [$this, 'ajax_rs_mag_new_subscription']);

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
            ['status' => 'invited'],
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
      case 'invitations':
        include __DIR__ . '/views/manage-invitations.php';
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
      die();
    }
    wp_send_json_error("Error!");
    die();
  } // ajax_update_status()

  public function ajax_toggle_welcome_package_sent()
  {
    $user_id = isset($_POST['user_id']) ? absint($_POST['user_id']) : null;
    $status =  isset($_POST['status']) ? trim($_POST['status']) : null;

    if (
      is_null($user_id) || 0 == $user_id ||
      is_null($status) || '' == $status
    ) {
      wp_send_json_error("Invalid Data");
      die();
    }

    $meta_value = ['status' => $status, 'updated_at' => current_time('mysql'), 'user_id' => get_current_user_id()];
    update_user_meta($user_id, 'bcc_welcome_package_status', json_encode($meta_value));
    /*
    if ('sent' == $status) {
    } elseif ('not-sent' == $status) {
      delete_user_meta($user_id, 'bcc_welcome_package_status');
    }
    */

    /**
     * Trigger Event in Braze
     */
    /* require_once WP_PLUGIN_DIR . '/brag-observer/classes/braze.class.php';
    $braze = new \Braze();
    $braze->setMethod('POST');

    $braze->triggerEvent($user_id, 'brag_bcc_welcome_package_sent', [
      'status' => $status
    ]); */

    wp_send_json_success($status);
    die();
  }

  public function ajax_invite_to_club()
  {
    global $wpdb;

    $emails = [];

    /* 
    // CSV Method
    $bom = "\xef\xbb\xbf";
    $fp = fopen($_FILES['csv']['tmp_name'], 'r');
    if (fgets($fp, 4) !== $bom) {
      rewind($fp);
    }
    while (!feof($fp) && ($line = fgetcsv($fp)) !== false) {
      $emails[] = $line;
    } */


    $emails = explode("\n", str_replace("\r", "", trim($_POST['emails'])));

    if (!is_array($emails) || empty($emails)) {
      wp_send_json_error('<tr><td class="text-danger">List is empty</td></tr>');
      die();
    }

    $emails = array_map('trim', $emails);
    $emails = array_unique($emails);

    // $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $response = '';

    $count = 0;
    foreach ($emails as $email) {
      $count++;

      // $email = $email_arr[0]; // Was used with CSV option

      $errors = [];

      if (!is_email($email)) {
        $errors[] = "<tr class='text-danger'><th>{$count}</th><td>{$email}</td><td>Invalid Email</td></tr>";
      }

      // wp_send_json_error(print_r($errors, true));
      // die();

      /**
       * Add to DB
       */
      // Check if already in DB
      $check = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}client_club_members WHERE `email` = '{$email}' LIMIT 1");
      if ($check) {
        $errors[] = "<tr class='text-warning'><th>{$count}</th><td>{$email}</td><td>Already invited</td></tr>";
      }

      if (empty($errors)) {
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
        $response .= "<tr class='text-success'><th>{$count}</th><td>{$email}</td><td>Will be invited to join the club!</td></tr>";
      } else {
        // $response .= '<tr><td class="text-danger">' . implode('<br>', $errors) . '</td></tr>';
        $response .= implode('', $errors);
      }
    }

    wp_send_json_success($response);
    die();
  } // ajax_invite_to_club()

  public function ajax_invite_to_event()
  {
    global $wpdb;

    // Check if Event ID is sent
    $event_id = isset($_POST['event_id']) ? absint($_POST['event_id']) : null;
    if (is_null($event_id) || 0 == $event_id) {
      wp_send_json_error('<tr><td class="text-danger">Missing Event ID</td></tr>');
      die();
    }

    // Check if file is sent
    /* $fp = fopen($_FILES['csv']['tmp_name'], 'r');
    if (!$fp) {
      wp_send_json_error('<tr><td class="text-danger">Please upload a file.</td></tr>');
      die();
    }

    $bom = "\xef\xbb\xbf";
    if (fgets($fp, 4) !== $bom) {
      rewind($fp);
    }

    // Add each line from CSV to $emails array
    $emails = [];
    while (!feof($fp) && ($line = fgetcsv($fp)) !== false) {
      $emails[] = $line;
    }
    */

    $emails = explode("\n", str_replace("\r", "", trim($_POST['emails'])));

    if (!is_array($emails) || empty($emails)) {
      wp_send_json_error('<tr><td class="text-danger">List is empty</td></tr>');
      die();
    }

    $emails = array_map('trim', $emails);
    $emails = array_unique($emails);

    $response = '';
    $count = 0;
    foreach ($emails as $email) {
      $count++;
      // $email = $email_arr[0]; // Was used with CSV option

      if (!is_email($email)) { // Check if email is valid
        $response .= "<tr class='text-danger'><th>{$count}</th><td>{$email}</td><td>Invalid Email</td></tr>";
      } else {

        // Get user by email
        $user = get_user_by('email', $email);

        if (!$user) { // Do not proceed if user doesn't exit
          $response .= "<tr class='text-danger'><th>{$count}</th><td>{$email}</td><td>User doesn't exist</td></tr>";
        } else {
          // Check if user is club member
          $check_member = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}client_club_members WHERE `user_id` = '{$user->ID}' AND `status` IN ('active', 'joined')");
          if (!$check_member) { // Do not proceed if user is not a club member
            $response .= "<tr class='text-warning'><th>{$count}</th><td>{$email}</td><td>Not a club member</td></tr>";
          } else {
            // Check if already invited and member responded yes/no
            $check_invite = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}client_club_event_invites WHERE `event_id` = '{$event_id}' AND `user_id` = '{$user->ID}'");
            // AND `status` IN ('yes', 'no')");
            if ($check_invite) { // Already invited
              if (in_array($check_invite->status, ['yes', 'no'])) {
                $response .= "<tr class='text-info'><th>{$count}</th><td>{$email}</td><td>Already responded: " . strtoupper($check_invite->status) . "</td></tr>";
              } else { // Not responded yet, set status to NULL, so cron picks and sends another email
                $wpdb->update(
                  $wpdb->prefix . 'client_club_event_invites',
                  ['status' => NULL,],
                  ['id' => $check_invite->id,],
                  ['%s'],
                  ['%d']
                );
                $response .= "<tr class='text-success'><th>{$count}</th><td>{$email}</td><td>Will be invited</td></tr>";
              }
            } else { // Member was not invited OR not responded yes/no yet i.e. Invite
              $guid = $this->generate_guid();

              $wpdb->insert(
                $wpdb->prefix . 'client_club_event_invites',
                [
                  'event_id' => $event_id,
                  'user_id' => $user->ID,
                  // 'status' => 'invited',
                  'guid' => $guid,
                ],
                ['%d', '%d', '%s', '%s',]
              );

              $response .= "<tr class='text-success'><th>{$count}</th><td>{$email}</td><td>Will be invited</td></tr>";
            }
          }
        }
      }
    }

    wp_send_json_success($response);
    die();

    /* $user_ids = isset($_POST['members']) ? $_POST['members'] : [];

    if (!is_array($user_ids) || empty($user_ids)) {
      wp_send_json_error('Empty members list');
      die();
    } */

    /**
     * Add to DB
     */

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

    $old_response = $wpdb->get_var("SELECT `status` FROM {$wpdb->prefix}client_club_event_invites WHERE `id` = '{$invite->id}' LIMIT 1");

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
    if ($response != $old_response) {
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
    }

    /**
     * Add/Update Custom Attribute in Braze
     */

    $message = 'yes' == $response ? 'Thank you, see you there!' : 'You wil be missed!';

    wp_send_json_success($message);
    die();
  } // ajax_rsponse_to_event()

  public function ajax_rs_mag_new_subscription()
  {
    parse_str($_POST['formData'], $formData);

    /**
     * RS Mag Subscription
     */
    $required_fields = [
      'buyer_full_name',
      'sub_email',

      'sub_address_1',
      'sub_city',
      'sub_state',
      'sub_postcode',
      'sub_country',

      'sub_full_name',

      'shipping_address_1',
      'shipping_city',
      'shipping_state',
      'shipping_postcode',
      'shipping_country',
    ];

    $current_user = wp_get_current_user();

    $formData['sub_email'] = $current_user->user_email;

    $formData['buyer_full_name'] = isset($formData['sub_full_name']) ? $formData['sub_full_name'] : '';
    $formData['sub_address_1'] = isset($formData['shipping_address_1']) ? $formData['shipping_address_1'] : '';
    $formData['sub_address_2'] = isset($formData['shipping_address_2']) ? $formData['shipping_address_2'] : '';
    $formData['sub_city'] = isset($formData['shipping_city']) ? $formData['shipping_city'] : '';
    $formData['sub_state'] = isset($formData['shipping_state']) ? $formData['shipping_state'] : '';
    $formData['sub_postcode'] = isset($formData['shipping_postcode']) ? $formData['shipping_postcode'] : '';
    $formData['sub_country'] = isset($formData['shipping_country']) ? $formData['shipping_country'] : '';

    foreach ($required_fields as $required_field) :
      if (!isset($formData[$required_field]) || '' == trim($formData[$required_field])) :
        wp_send_json_error('Whoops, looks like you have forgotten to fill out all the necessary fields. Make sure you go back and give us all the info we need!');
        die();
      endif;
    endforeach;

    $formData['coupon_code'] = 'Comp (BCC)';
    $formData['coupon_id'] = null;
    $formData['is_gift'] = 'no';

    /**
     * Update user metas
     */
    update_user_meta($current_user->ID, 'address_1', $formData['shipping_address_1']);
    if ('' != trim($formData['shipping_address_2']))
      update_user_meta($current_user->ID, 'address_2', $formData['shipping_address_2']);

    update_user_meta($current_user->ID, 'city', $formData['shipping_city']);
    update_user_meta($current_user->ID, 'state', $formData['shipping_state']);
    update_user_meta($current_user->ID, 'postcode', $formData['shipping_postcode']);
    update_user_meta($current_user->ID, 'country', $formData['shipping_country']);
    update_user_meta($current_user->ID, 'company_name', $formData['company_name']);
    update_user_meta($current_user->ID, 'job_title', $formData['job_title']);

    /* require_once WP_PLUGIN_DIR . '/brag-observer/brag-observer.php';
    $bo = new \BragObserver();
    $bo->createRSMagSubscription($formData); */

    wp_send_json_success('Thank you!');
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

              require_once WP_PLUGIN_DIR . '/brag-observer/brag-observer.php';
              $bo = new \BragObserver();

              $subscriptions = $bo->getMagSubscriptions($user->user_email);
              if ($subscriptions) { // If there are Subscriptions
                foreach ($subscriptions as $key => $subscription) {
                  if (isset($subscription->crm_record)) {
                    if ('inactive' == $status) { // Deactivate RS Mag Sub if member is still active
                      $bo->cancelSubscription(['uniqid' => $subscription->uniqid]);
                    } else if ('active' == $status) { // Activate RS Mag Sub if member is still active
                      if (isset($subscription->crm_record->Active__c) && $subscription->crm_record->Active__c) {
                      } else {
                        $bo->enableAutoRenew(['uniqid' => $subscription->uniqid]);
                      }
                    }
                  } // If Subscription CRM Record is set
                } // For Each $subscription
              } // If there are Subscription
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
