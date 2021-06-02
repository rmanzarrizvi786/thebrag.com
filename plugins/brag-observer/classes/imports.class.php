<?php
class Imports extends BragObserver
{

  public function __construct()
  {

    parent::__construct();

    // AJAX
    add_action('wp_ajax_mc_imps_to_wp', [$this, 'mc_imps_to_wp']);
    add_action('wp_ajax_mc_delete_from_wp', [$this, 'mc_delete_from_wp']);

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

}

new Imports();
