<?php
class Imports extends BragObserver
{
  public function __construct()
  {

    parent::__construct();

    // AJAX
    add_action('wp_ajax_mc_imps_to_wp', [$this, 'mc_imps_to_wp']);
    add_action('wp_ajax_mc_delete_from_wp', [$this, 'mc_delete_from_wp']);
    add_action('wp_ajax_export_to_braze', [$this, 'export_to_braze']);

    add_action('cron_hook_observer_braze_export', [$this, 'export_to_braze']);

    // Admin menu
    add_action('admin_menu', array($this, '_admin_menu'));

    // Load JS
    add_action('wp_enqueue_scripts', [$this, 'load_js_css']);
  }

  public function _admin_menu()
  {

    add_submenu_page(
      $this->plugin_slug,
      'Process MC Imports to WP',
      'Process MC Imports to WP',
      'administrator',
      $this->plugin_slug . '-mailchimp-imps-to-wp',
      array($this, 'show_mailchimp_imps_to_wp')
    );

    add_submenu_page(
      $this->plugin_slug,
      'Delete users from WP',
      'Delete users from WP',
      'administrator',
      $this->plugin_slug . '-delete-from-wp',
      array($this, 'show_delete_from_wp')
    );

    add_submenu_page(
      $this->plugin_slug,
      'Export to Braze',
      'Export to Braze',
      'administrator',
      $this->plugin_slug . '-export-to-braze',
      array($this, 'show_export_to_braze')
    );
  }

  public function show_mailchimp_imps_to_wp()
  {
    global $wpdb;
    include PLUGINPATH . '/partials/imports/mailchimp-imps-to-wp.php';
  }

  public function mc_imps_to_wp()
  {
    global $wpdb;
    $tmp_subs_query = "SELECT * FROM tmp_subs_import WHERE processed IS NULL LIMIT 500";
    $tmp_subs = $wpdb->get_results($tmp_subs_query);
    if ($tmp_subs) {

      $return_msg = '';

      foreach ($tmp_subs as $tmp_sub) {

        $message_user = '';

        $process = true;

        $tmp_sub->email = trim($tmp_sub->email);

        if (!isset($tmp_sub->email) || !is_email($tmp_sub->email)) {
          $message_user .= '<div class="text-danger">Invalid Email Address ' . $tmp_sub->email . '</div>';
          $return_msg .= $message_user;
          $process = false;
        }

        if (!$process) {
          $wpdb->update('tmp_subs_import', ['processed' => '0', 'comments' => strip_tags($message_user)], ['id' => $tmp_sub->id]);
          wp_send_json_success([$message_user]);
          die();
        }

        if (email_exists($tmp_sub->email)) {
          $message_user .= '<div class="text-danger">User with Email Address <strong>' . $tmp_sub->email . '</strong> already exists.</div>';
          $return_msg .= $message_user;
          $user = get_user_by('email', $tmp_sub->email);
        } else {
          $user_id = wp_insert_user([
            'user_login' => $tmp_sub->email,
            'user_pass' => NULL,
            'user_email' => trim($tmp_sub->email),
            'first_name' => $tmp_sub->first_name,
            'last_name' => $tmp_sub->last_name,
            'user_registered' => date('Y-m-d H:i:s'),
            'role' => 'subscriber'
          ]);

          if (is_wp_error($user_id)) {
          } else {
            $message_user .= $tmp_sub->email . '<br>';
            $return_msg .= $message_user;
            $user = get_user_by('ID', $user_id);

            if (!is_null($tmp_sub->gender) && $tmp_sub->gender != '') {
              if (!get_user_meta($user->ID, 'gender') && !get_user_meta($user->ID, 'predicted_gender')) {
                update_user_meta($user->ID, 'predicted_gender', strtoupper($tmp_sub->gender));
              }
            }

            if (!is_null($tmp_sub->state) && $tmp_sub->state != '') {
              if (!get_user_meta($user->ID, 'state') && !get_user_meta($user->ID, 'predicted_state')) {
                update_user_meta($user->ID, 'predicted_state', strtoupper($tmp_sub->state));
              }
            }

            if (!is_null($tmp_sub->birthday) && $tmp_sub->birthday != '') {
              if (!get_user_meta($user->ID, 'birthday') && !get_user_meta($user->ID, 'predicted_birthday')) {
                update_user_meta($user->ID, 'predicted_birthday', strtoupper($tmp_sub->birthday));
              }
            }
          }
        }

        if ($user) {

          update_user_meta($user->ID, 'no_welcome_email', 1);
          update_user_meta($user->ID, 'is_activated', 1);

          if (!get_user_meta($user->ID, 'oc_token')) {
            $oc_token = md5($user->ID . time()); // creates md5 code to verify later
            update_user_meta($user->ID, 'oc_token', $oc_token);
          }

          $unserialized_oc_token = [
            'id' => $user->ID,
            'oc_token' => get_user_meta($user->ID, 'oc_token', true),
          ]; // makes it into a code to send it to user via email

          if (!is_null($tmp_sub->lists) && $tmp_sub->lists != '') {
            $lists = explode(',', $tmp_sub->lists);
            $lists = array_map('trim', $lists);
            foreach ($lists as $list) {

              if (is_numeric($list)) {
                $list_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}observer_lists WHERE id = '{$list}' LIMIT 1 ");
              } else {
                $list_id = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}observer_lists WHERE title = '{$list}' LIMIT 1 ");
              }

              if (!$list_id)
                continue;

              $list_query = "SELECT id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user->ID}' AND list_id = '{$list_id}' LIMIT 1";
              $check_sub = $wpdb->get_row($list_query);
              if (!$check_sub) {
                $insert_values = [
                  'user_id' => $user->ID,
                  'list_id' => $list_id,
                  'status' => 'subscribed',
                  'status_mailchimp' => NULL, // 'subscribed', //
                  'subscribed_at' => current_time('mysql'),
                ];
                if (!is_null($tmp_sub->source) && $tmp_sub->source != '') {
                  $insert_values['source'] = $tmp_sub->source;
                }
                $wpdb->insert($wpdb->prefix . 'observer_subs', $insert_values);
              }
            } // For Each $list_id
          }
        } // If $user

        $wpdb->update(
          'tmp_subs_import',
          [
            'user_id' => $user->ID,
            'oc_token' => base64_encode(serialize($unserialized_oc_token)),
            'processed' => '1',
            'comments' => strip_tags($message_user)
          ],
          [
            'id' => $tmp_sub->id
          ]
        );
      } // For Each $tmp_sub
      // wp_send_json_error( [ 'Done' ] ); die();
      wp_send_json_success([$return_msg]);
      die();
    } else {
      wp_send_json_error(['Done']);
      die();
    }
  } // mc_imps_to_wp()

  public function show_delete_from_wp()
  {
    global $wpdb;
    include PLUGINPATH . '/partials/imports/delete-from-wp.php';
  }

  public function mc_delete_from_wp()
  {
    global $wpdb;
    $tmp_subs_query = "SELECT * FROM tmp_subs_delete WHERE processed IS NULL LIMIT 500";
    $tmp_subs = $wpdb->get_results($tmp_subs_query);
    if ($tmp_subs) {

      $return_msg = '';

      foreach ($tmp_subs as $tmp_sub) {

        $message_user = '';

        $process = true;

        $tmp_sub->email = trim($tmp_sub->email);

        if (!isset($tmp_sub->email) || !is_email($tmp_sub->email)) {
          $message_user .= '<div class="text-danger">Invalid Email Address ' . $tmp_sub->email . '</div>';
          $return_msg .= $message_user;
          $process = false;
        }

        if (!$process) {
          $wpdb->update('tmp_subs_delete', ['processed' => '0', 'comments' => strip_tags($message_user)], ['id' => $tmp_sub->id]);
          wp_send_json_success([$message_user]);
          die();
        }

        if (email_exists($tmp_sub->email)) {

          $user = get_user_by('email', $tmp_sub->email);

          if ($user) {

            if (!in_array('subscriber', (array) $user->roles)) {
              $message_user .= '<div class="text-danger">' . $tmp_sub->email . ' role is not subscriber.</div>';
              $return_msg .= $message_user;
              $wpdb->update(
                'tmp_subs_delete',
                [
                  'processed' => '2',
                  'comments' => strip_tags($message_user)
                ],
                [
                  'id' => $tmp_sub->id
                ]
              );
            } else {

              $message_user .= '<div class="text-info">' . $tmp_sub->email . ' exists.</div>';
              $return_msg .= $message_user;

              $wpdb->delete(
                $wpdb->prefix . 'observer_subs',
                [
                  'user_id' => $user->ID
                ]
              );

              require_once(ABSPATH . 'wp-admin/includes/user.php');

              wp_delete_user($user->ID);

              $wpdb->update(
                'tmp_subs_delete',
                [
                  'user_id' => $user->ID,
                  'processed' => '1',
                  'comments' => strip_tags($message_user)
                ],
                [
                  'id' => $tmp_sub->id
                ]
              );
            }
          } // If $user
        } // If Email exists
        else {
          $message_user .= '<div class="text-danger">' . $tmp_sub->email . ' doesn\'t exist.</div>';
          $return_msg .= $message_user;
          $wpdb->update(
            'tmp_subs_delete',
            [
              'processed' => '0',
              'comments' => strip_tags($message_user)
            ],
            [
              'id' => $tmp_sub->id
            ]
          );
        }
      } // For Each $tmp_sub
      // wp_send_json_error( [ 'Done' ] ); die();
      wp_send_json_success([$return_msg]);
      die();
    } else {
      wp_send_json_error(['Done']);
      die();
    }
  } // mc_delete_from_wp()

  public function show_export_to_braze()
  {
    date_default_timezone_set('Australia/NSW');
    echo '<br><p>Current Date/Time: ' . date('d-M-Y h:i:sa') . '</p>';
    $next_run_timestamp = wp_next_scheduled('cron_hook_observer_braze_export', array(NULL, NULL));
    echo '<p>Scheduled automatic run is at ' . date('d-M-Y h:i:sa', $next_run_timestamp) . '</p>';

    include PLUGINPATH . '/partials/imports/export-to-braze.php';
  } // show_export_to_braze()

  public function export_to_braze()
  {
    global $wpdb;
    $errors = [];
    $attributes = [];
    $limit_users = isset($_POST['limit_users']) ? absint($_POST['limit_users']) : 75;
    $return = [];

    /**
     * Get users
     */
    $users = get_users(
      [
        'number' => $limit_users,
        'meta_key' => 'created_braze_user',
        'meta_compare' => 'NOT EXISTS',
        'orderby' => 'ID',
        'order' => isset($_POST['order']) ? strtoupper(trim($_POST['order'])) : 'ASC'
      ]
    );

    try {
      if ($users) {
        foreach ($users as $user) {

          /**
           * Get user's subscription topics
           */
          $query_subs = " SELECT
                l.slug
            FROM {$wpdb->prefix}observer_subs s
                JOIN {$wpdb->prefix}observer_lists l
                    ON s.list_id = l.id
            WHERE
                s.`status` = 'subscribed'
                AND
                s.user_id = '{$user->ID}'
            ";
          $subs = $wpdb->get_results($query_subs);
          if (!$subs) {
            update_user_meta($user->ID, 'created_braze_user', 0);
            continue;
          }

          /**
           * Get user's meta
           */
          $user_attributes = [];
          $user_attributes['email'] = $user->user_email;
          if (get_user_meta($user->ID, 'first_name') && '' != trim(get_user_meta($user->ID, 'first_name', true))) {
            $user_attributes['first_name'] = get_user_meta($user->ID, 'first_name', true);
          }
          if (get_user_meta($user->ID, 'last_name') && '' != trim(get_user_meta($user->ID, 'last_name', true))) {
            $user_attributes['last_name'] = get_user_meta($user->ID, 'last_name', true);
          }

          /**
           * Set user's Auth0 ID as external ID (if set)
           * OR
           * Set user_alias
           */
          if (get_user_meta($user->ID, $wpdb->prefix . 'auth0_id')) {
            $user_attributes['external_id'] = get_user_meta($user->ID, $wpdb->prefix . 'auth0_id', true);
          } else if (get_user_meta($user->ID, 'wp_auth0_id')) {
            // If user's Auth0 ID is not set using wpdb prefix, check if set using wp_ prefix
            $user_attributes['external_id'] = get_user_meta($user->ID, 'wp_auth0_id', true);
          } else {
            // User's Auth0 ID not set, set alias for user
            $user_attributes['user_alias'] = [
              'alias_name' => $user->user_email,
              'alias_label' => 'email',
            ];
            $user_attributes['_update_existing_only'] = false;
          }

          /**
           * Add subscriptions to custom user attributes
           */
          if ($subs) {
            $user_attributes['newsletter_interests'] = wp_list_pluck($subs, 'slug');
          }

          /**
           * Get user's tags from MailChimp
           */
          $subscriber_hash = $this->MailChimp->subscriberHash($user->user_email);

          $mc_tags = $this->MailChimp->get("lists/{$this->mailchimp_list_id}/members/{$subscriber_hash}/tags");
          $tags = is_array($mc_tags) && isset($mc_tags['tags']) && is_array($mc_tags['tags']) && !empty($mc_tags['tags']) ? wp_list_pluck($mc_tags['tags'], 'name') : [];
          if (!empty($tags)) {
            $user_attributes['mc_tags'] = $tags;
          }

          $mc_last_click = $this->getMailchimpLastActivity('click', $user->user_email);
          if ($mc_last_click !== false) {
            $user_attributes['legacy_lastclickdate'] = $mc_last_click;
          }
          $mc_last_open = $this->getMailchimpLastActivity('open', $user->user_email);
          if ($mc_last_open !== false) {
            $user_attributes['legacy_lastopendate'] = $mc_last_open;
          }

          /* if (is_array($mc_activities) && isset($mc_activities['activity']) && is_array($mc_activities['activity']) && !empty($mc_activities['activity'])) {
            foreach ($mc_activities['activity'] as $mc_activity) {
              if (!isset($user_attributes['legacy_lastopendate'])) {
                if ('open' == $mc_activity['action']) {
                  $user_attributes['legacy_lastopendate'] = $mc_activity['timestamp'];
                }
              }
              if (!isset($user_attributes['legacy_lastclickdate'])) {
                if ('click' == $mc_activity['action']) {
                  $user_attributes['legacy_lastclickdate'] = $mc_activity['timestamp'];
                }
              }
            }
          } */

          // Token
          if (!get_user_meta($user->ID, 'oc_token', true)) :
            $oc_token = md5($user->ID . time()); // creates md5 code to verify later
            update_user_meta($user->ID, 'oc_token', $oc_token);
          endif;

          $unserialized_oc_token = [
            'id' => $user->ID,
            'oc_token' => get_user_meta($user->ID, 'oc_token', true),
          ]; // makes it into a code to send it to user via email

          $user_attributes['observer_token'] = base64_encode(serialize($unserialized_oc_token));

          if (get_user_meta($user->ID, 'gender')) {
            $user_attributes['gender'] = get_user_meta($user->ID, 'gender', true);
          }
          if (get_user_meta($user->ID, 'birthday')) {
            $user_attributes['dob'] = get_user_meta($user->ID, 'birthday', true);
          }
          if (get_user_meta($user->ID, 'state')) {
            $user_attributes['state'] = get_user_meta($user->ID, 'state', true);
          }
          if (get_user_meta($user->ID, 'profile_strength')) {
            $user_attributes['profile_completion_%'] = get_user_meta($user->ID, 'profile_strength', true);
          }

          if (!empty($user_attributes)) {
            $attributes[] = $user_attributes;
          }

          $return[] = $user->user_email;
        } // For Each $user

        /**
         * Export to Braze
         */
        require_once __DIR__ . '/braze.class.php';
        $braze = new Braze();
        $braze->setMethod('POST');
        $braze->setPayload(
          [
            'attributes' => $attributes
          ]
        );
        $res_track = $braze->request('/users/track', true);
        if (201 === $res_track['code']) {
          foreach ($users as $user) {
            update_user_meta($user->ID, 'created_braze_user', 1);
          }
        }

        /* $return = [
        '<pre>' . print_r($attributes, true) . '</pre>',
        '<pre>' . print_r($res_track, true) . '</pre>',
      ]; */
      } // If $users
      else {
        wp_send_json_error('Done');
        die();
      }
    } catch (\Exception $e) {
      wp_send_json_error($e->getMessage());
      die();
    }

    if (!empty($errors)) {
      wp_send_json_error($errors);
      die();
    }

    wp_send_json_success($return);
    die();
  } // export_to_braze()

  private function getMailchimpLastActivity($activity_type, $email, $offset = 0)
  {
    $count = 100;

    $subscriber_hash = $this->MailChimp->subscriberHash($email);

    $request = "lists/{$this->mailchimp_list_id}/members/{$subscriber_hash}/activity-feed?count={$count}&fields=activity.activity_type,activity.created_at_timestamp";

    if ($offset > 0) {
      $request .= "&offset={$offset}";
    }

    if ($offset > 200)
      return false;

    $mc_activities = $this->MailChimp->get($request);
    if (is_array($mc_activities) && isset($mc_activities['activity']) && is_array($mc_activities['activity']) && !empty($mc_activities['activity'])) {
      foreach ($mc_activities['activity'] as $mc_activity) {
        if ($activity_type == $mc_activity['activity_type']) {
          return $mc_activity['created_at_timestamp'];
        }
      }
      return $this->getMailchimpLastActivity($activity_type, $email, $offset + $count);
    }
    return false;
  }
}

new Imports();
