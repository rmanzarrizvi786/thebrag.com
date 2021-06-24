<?php

/**
 * Plugin Name: The Brag Observer
 * Plugin URI: https://thebrag.media/
 * Description:
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI:
 */

class BragObserver
{

  protected $plugin_name;
  protected $plugin_slug;

  protected $page_template1, $page_template2;

  protected $mailchimp_list_id;
  protected $mailchimp_interest_category_id;
  protected $mailchimp_api_key;
  protected $MailChimp;

  protected $categories;

  protected $mag_sub;
  protected $is_sandbox;

  // protected $rest_api_keys;

  public function __construct()
  {

    if (!defined('PLUGINPATH'))
      define('PLUGINPATH', plugin_dir_path(__FILE__));

    $this->plugin_name = 'brag_observer';
    $this->plugin_slug = 'brag-observer';

    $this->page_template1 = 'page-templates/brag-observer.php';
    $this->page_template2 = 'page-templates/brag-observer-subscriptions.php';

    $this->mailchimp_list_id = '5f6dd9c238';
    $this->mailchimp_interest_category_id = 'b87c163ce8';
    $this->mailchimp_api_key = 'e5ad9623c8961a991f8737c3cc950c55-us1';

    require_once __DIR__ . '/classes/MailChimp.php';
    $this->MailChimp = new MailChimp($this->mailchimp_api_key);

    $this->is_sandbox = isset($_ENV) && isset($_ENV['ENVIRONMENT']) && 'sandbox' == $_ENV['ENVIRONMENT']; // in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

    $this->mag_sub['rest_api_key'] = '39d733e9-5277-4389-811a-a14c9f1e9294';
    if ($this->is_sandbox) {
      $this->mag_sub['api_url'] = 'http://au.rolling-stone.com/wp-json/tbm_mag_sub/v1/';
    } else {
      $this->mag_sub['api_url'] = 'https://au.rollingstone.com/wp-json/tbm_mag_sub/v1/';
    }

    // $this->rest_api_keys =
    //   [
    //     'rsau' => '1fc08f46-3537-43f6-b5c1-c68704acf3fa',
    //     'tonedeaf.thebrag.com' => '3ce4efdd-a39c-4141-80f7-08a828500831',
    //   ]
    // ;

    $this->categories = self::getCategories();

    add_action('init', array($this, '_init'), 1);

    add_action('admin_menu', array($this, '_admin_menu'));

    // AJAX Actions
    add_action('wp_ajax_subscribe_observer', [$this, 'subscribe_observer']);
    add_action('wp_ajax_nopriv_subscribe_observer', [$this, 'subscribe_observer']);

    add_action('user_register', [$this, '_user_register'], 10, 1);

    add_action('wp_ajax_vote_observer', [$this, 'vote_observer']);
    add_action('wp_ajax_nopriv_vote_observer', [$this, 'vote_observer']);

    add_action('wp_ajax_set_subscribe_to_list_pending', [$this, 'set_subscribe_to_list_pending']);
    add_action('wp_ajax_nopriv_set_subscribe_to_list_pending', [$this, 'set_subscribe_to_list_pending']);

    add_action('wp_ajax_set_apple_redirect_url', [$this, 'set_apple_redirect_url']);
    add_action('wp_ajax_nopriv_set_apple_redirect_url', [$this, 'set_apple_redirect_url']);

    add_action('wp_ajax_ajax_login', [$this, 'ajax_login']);
    add_action('wp_ajax_nopriv_ajax_login', [$this, 'ajax_login']);

    add_action('wp_ajax_get_remote_data', [$this, 'get_remote_data']);
    add_action('wp_ajax_save_observer_newsletter', [$this, 'save_newsletter']);
    add_action('wp_ajax_save_observer_solus', [$this, 'save_solus']);

    add_action('wp_ajax_send_refer_invite', [$this, 'send_refer_invite']);
    add_action('wp_ajax_nopriv_send_refer_invite', [$this, 'send_refer_invite']);

    // Load JS
    add_action('wp_enqueue_scripts', [$this, 'load_js_css']);

    // Footer
    // add_action( 'wp_footer', [ $this, 'wp_footer' ] );

    // Head -
    remove_action('wp_head', '_admin_bar_bump_cb');

    // Post actions
    add_action('admin_action_manage_observer_list', [$this, 'action_manage_observer_list']);

    // Filter - Query vars
    add_filter('query_vars', [$this, '_query_vars']);

    // OG Tags, etc. - Yoast
    $this->setupSEO();

    // Activation
    register_activation_hook(__FILE__, [$this, 'activate']);

    // Deactivation
    register_deactivation_hook(__FILE__, [$this, 'deactivate']);

    // Cron
    add_action('cron_hook_brag_observer', [$this, 'exec_cron_brag_observer']);

    // Payment Details for Mag Sub
    add_action('wp_ajax_update_payment_details', [$this, 'update_payment_details']);
    add_action('wp_ajax_nopriv_update_payment_details', [$this, 'update_payment_details']);

    // REST API
    // add_action( 'rest_api_init', [ $this, '_rest_api_init' ] );

    // Tastemaker class
    require_once  __DIR__ . '/classes/tastemaker.class.php';

    // Lead Generator class
    require_once  __DIR__ . '/classes/lead-generator.class.php';

    // API
    require_once  __DIR__ . '/classes/api.class.php';

    // Imports
    require_once  __DIR__ . '/classes/imports.class.php';

    // Shortcode, etc.
    require_once  __DIR__ . '/classes/shortcode.class.php';

    // Referrals
    require_once  __DIR__ . '/classes/referral.class.php';

    // TMP
    add_action('wp_ajax_update_profile_strength', [$this, 'update_profile_strength']);

    add_action('wp', [$this, '_wp']);

    // Add post meta if shortcode is present in the content
    add_action('save_post', [$this, '_save_post'], 10, 3);
  }

  /*
  * WP Initiated, create REFER_CODE in WP & push to MailChimp
  */
  public function _wp()
  {

    if (is_user_logged_in()) {
      $current_user = wp_get_current_user();

      if (!get_user_meta($current_user->ID, 'refer_code', true)) {
        do {
          $refer_code = substr(md5(uniqid($current_user->ID, true)), 0, 8);
        } while (!check_unique_refer_code($refer_code));

        update_user_meta($current_user->ID, 'refer_code', $refer_code);

        $subscriber_hash = $this->MailChimp->subscriberHash($current_user->user_email);
        $update_referrals_count = $this->MailChimp->patch("lists/{$this->mailchimp_list_id}/members/{$subscriber_hash}", [
          'merge_fields' => ['REFER_CODE' => $refer_code]
        ]);
      }
    }
  }

  /*
  * User register hook
  */
  public function _user_register($user_id)
  {

    // if ( '' === get_user_meta( $user_id, 'is_activated', true ) ) {
    //   update_user_meta( $user_id, 'is_activated', 1 ); // activate user if registered before is_activated implemented OR registering via Socials
    // }

    if (!get_user_meta($user_id, 'referrer_id', true) && isset($_SESSION['rc'])) {
      global $wpdb;
      $refer_code = sanitize_text_field($_SESSION['rc']);
      $referrer_id = $wpdb->get_var("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'refer_code' AND meta_value = '{$refer_code}' LIMIT 1");
      if ($referrer_id) {
        update_user_meta($user_id, 'referrer_id', $referrer_id);

        // Referrals in MailChimp {{
        $referrer = get_user_by('ID', $referrer_id);

        if ($referrer) {
          $referrals_query = "
            SELECT
              u.ID,
              u.user_email
            FROM
              $wpdb->users u
              JOIN
                $wpdb->usermeta um ON u.ID = um.user_id
            WHERE
              um.meta_key = 'referrer_id' AND
              um.meta_value = '{$referrer_id}'
            ORDER BY
              u.ID DESC
          ";
          $all_referrals = $wpdb->get_results($referrals_query);

          $referrals = [];
          if ($all_referrals) {
            foreach ($all_referrals as $referral) {
              if (get_user_meta($referral->ID, 'is_activated', true) == '1') {
                $referrals['confirmed'][] = $referral;
              }
            }
          }
          $referrals_count = isset($referrals['confirmed']) ? count($referrals['confirmed']) : 0;

          update_user_meta($referrer_id, 'referrals_count', $referrals_count);

          $subscriber_hash = $this->MailChimp->subscriberHash($referrer->user_email);
          $update_referrals_count = $this->MailChimp->patch("lists/{$this->mailchimp_list_id}/members/{$subscriber_hash}", [
            'merge_fields' => ['REFERRALS' => $referrals_count]
          ]);
        }
        // }} Referrals in MailChimp
      }
    }
  }

  /*
  * AJAX - Login
  */
  public function ajax_login()
  {
    // First check the nonce, if it fails the function will break
    check_ajax_referer('ajax-login-nonce', 'security');

    $badUserPass = false;
    $message = '';
    $username = (string) $_POST['username'];
    $password = (string) $_POST['password'];

    if (!username_exists($username) && !email_exists($username)) {
      // Username and Email don't exist for the provided $username, register user
      if (!isset($username) || '' == trim($username) || !is_email($username)) {
        $badUserPass = true;
        $message = 'Please enter valid email address.';
      } elseif (!isset($password) || '' == trim($password)) {
        $badUserPass = true;
        $message = 'Please enter password.';
      }

      if (false == $badUserPass) {
        $user_id = wp_insert_user(
          array(
            'user_login' => $username,
            'user_pass' => $password,
            'user_email' => trim($username),
            'first_name' => '',
            'last_name' => '',
            'user_registered' => date('Y-m-d H:i:s'),
            'role' => 'subscriber'
          )
        );

        if (is_wp_error($user_id)) {
          $badUserPass = true;
          $message = $user_id->get_error_message();
        } else {
          require_once(ABSPATH . '/wp-content/plugins/brag-observer/classes/email.class.php');
          $email = new Email();
          $email->sendUserVerificationEmail($user_id, home_url('/observer/'));

          $username = '';
          $status = 'require_activation';
          $message = 'You will need to confirm your email address in order to activate your account. An email containing the activation link has been sent to your email address. If the email does not arrive within a few minutes, check your spam folder.';
        }
      }
    } else { // User exists with username or email, try login
      $auth_result = wp_authenticate($username, $password);
      if (is_wp_error($auth_result)) {
        if (is_email($username)) {
          $try_user = get_user_by('email', $username);
        } else {
          $try_user = get_user_by('login', $username);
        }
        if ($try_user) {
          if (
            get_user_meta($try_user->ID, 'oc_token') &&
            !get_user_meta($try_user->ID, 'wfls-last-login')
          ) {
            require_once(ABSPATH . '/wp-content/plugins/brag-observer/classes/email.class.php');
            $email = new Email();
            $email->sendUserVerificationEmail($try_user->ID, home_url('/observer/'));

            $username = '';
            $status = 'require_activation';
            $message = 'You will need to confirm your email address in order to activate your account. An email containing the activation link has been sent to your email address. If the email does not arrive within a few minutes, check your spam folder.';
          } else {
            $badUserPass = true;
            $message = 'Incorrect credentials, please try again.';
          }
        }
      } else {
        $wp_signon = wp_signon(
          [
            'user_login' => $auth_result->user_login,
            'user_password' => $password
          ],
          true
        );
        if ($wp_signon) {
          $status = 'success';
          $current_user = wp_set_current_user($wp_signon->ID);
          wp_set_auth_cookie($wp_signon->ID);
        }
        if (!session_id()) {
          // session_start not called before. Do it here.
          session_start();
        }
      }
    } // Username and Email don't exist for the provided $username

    if ($badUserPass) {
      wp_send_json_error([$message]);
    } else {
      wp_send_json_success(
        [
          'status' => $status,
          'message' => $message,
        ]
      );
      die();
    }

    die();
  } // ajax_login()

  public function activate()
  {
    if (!wp_next_scheduled('cron_hook_brag_observer', array(NULL, NULL))) {
      // wp_schedule_event( time(), 'hourly', 'cron_hook_brag_observer', array( NULL, NULL ) );
    }
  }

  public function deactivate()
  {
    $crons = _get_cron_array();
    if (empty($crons)) {
      return;
    }
    $hook = 'cron_hook_brag_observer';
    foreach ($crons as $timestamp => $cron) {
      if (!empty($cron[$hook])) {
        unset($crons[$timestamp][$hook]);
      }
      if (empty($crons[$timestamp])) {
        unset($crons[$timestamp]);
      }
    }
    _set_cron_array($crons);
  }

  public function exec_cron_brag_observer()
  {

    update_option('BragObserver_CronStart', date('Y-m-d H:i:s'), false);

    global $wpdb;

    require_once __DIR__ . '/classes/email.class.php';
    $email = new Email($this);

    /*
    * Send Email if Profile is incomplete after x days of registration
    */
    /*
    $reminder_frequencies = [
      [
        'reminder_count' => 1,
        'days' => 3,
        'subject' => 'Hey there!',
      ],
      [
        'reminder_count' => 2,
        'days' => 6,
        'subject' => 'Hey there, 6 days since you subscribed!',
      ],
      [
        'reminder_count' => 3,
        'days' => 10,
        'subject' => 'Hey there, 10 days since you subscribed!',
      ],
    ];

    foreach ( $reminder_frequencies as $reminder_frequency ) :

      $reminder_frequency_date = date('Y-m-d', strtotime( '-' . $reminder_frequency['days'] . ' days' ) );
      $reminder_frequency_date = explode( '-', $reminder_frequency_date );

      $users = get_users(
        [
          'meta_query' => [
              [
                'key' => 'incomplete_profile',
                'value' => 'true',
                'compare' => '='
              ],
              [
                'key' => 'sent_rego_reminder' . $reminder_frequency['reminder_count'],
                'compare' => 'NOT EXISTS'
              ],
          ],
          'role__in' => [ 'subscriber' ],
          'date_query'   => [
            'column' => 'user_registered',
            'year' => $reminder_frequency_date[0],
            'month' => $reminder_frequency_date[1],
            'day' => $reminder_frequency_date[2],
            // 'compare' => $past_date,
          ]
        ]
      );

      // echo '<pre>'; print_r( $users ); exit;

      if ( $users ) :
        foreach ( $users as $user ) :

          if ( get_user_meta( $user->ID, 'no_welcome_email', true ) ) {
            continue;
          }

          if ( $email->sendRegistrationReminderEmail( $user, $reminder_frequency['subject'] ) ) :
            update_user_meta( $user->ID, 'sent_rego_reminder' . $reminder_frequency['reminder_count'], "true" );
          endif; // Email Sent
        endforeach; // For Each $user
      endif; // If $users
    endforeach; // For Each $reminder_frequencies
    */

    /*
    * Process MailChimp subs and unsubs
    * + Send welcome emails
    */
    $query_subs = "
    SELECT
      s.id,
      s.list_id,
      s.status,
      u.ID user_id,
      u.user_email,
      l.interest_id,
      l.title list_title,
      l.status list_status
    FROM
      {$wpdb->prefix}observer_subs s
        JOIN {$wpdb->prefix}observer_lists l
          ON s.list_id = l.id
        JOIN {$wpdb->prefix}users u
          ON s.user_id = u.ID
        JOIN {$wpdb->prefix}usermeta um
          ON u.ID = um.user_id
    WHERE
      ( s.status_mailchimp IS NULL OR ( s.mc_subscribed_at IS NULL AND s.mc_unsubscribed_at IS NULL ) )
      AND um.meta_key = 'is_activated'
      AND um.meta_value = '1'
    ORDER BY
      s.subscribed_at DESC
    LIMIT 500
    ";
    $subs = $wpdb->get_results($query_subs);

    $sub_users = [];

    if ($subs) {
      foreach ($subs as $sub) {
        // if( get_user_meta( $sub->user_id, 'is_activated', true ) )
        {

          $sub_users[$sub->user_email]['user_id'] = $sub->user_id;

          if ($sub->status == 'subscribed') {
            $sub_users[$sub->user_email]['sub_lists'][$sub->id] = $sub->list_title;
          } else {
            $sub_users[$sub->user_email]['unsub_lists'][$sub->id] = $sub->list_title;
          }
          $sub_users[$sub->user_email]['interests'][$sub->interest_id] = $sub->status == 'subscribed' ? true : false;
        } // If user is activated
      } // For Each $sub
    } // If $subs

    // echo '<pre>'; print_r( $sub_users ); echo '</pre>';  exit;

    if (count($sub_users) > 0) {
      foreach ($sub_users as $sub_email => $sub_user) {

        $data = array(
          'email_address' => $sub_email,
          'status' => 'subscribed',
        );
        $subscribe = $this->MailChimp->post("lists/{$this->mailchimp_list_id}/members", $data);
        $subscriber_hash = $this->MailChimp->subscriberHash($sub_email);

        // Token
        if (!get_user_meta($sub_user['user_id'], 'oc_token', true)) :
          $oc_token = md5($sub_user['user_id'] . time()); // creates md5 code to verify later
          update_user_meta($sub_user['user_id'], 'oc_token', $oc_token);
        endif;

        $unserialized_oc_token = [
          'id' => $sub_user['user_id'],
          'oc_token' => get_user_meta($sub_user['user_id'], 'oc_token', true),
        ]; // makes it into a code to send it to user via email

        $merge_fields = [
          'OC_TOKEN' => base64_encode(serialize($unserialized_oc_token)),
        ];

        $interest_subscribe = $this->MailChimp->patch("lists/{$this->mailchimp_list_id}/members/{$subscriber_hash}", [
          'interests' => $sub_user['interests'],
          'merge_fields' => $merge_fields
        ]);

        // echo '<pre>'; print_r( $interest_subscribe ); echo '</pre>';

        /*
        * Update MailChimp Status in DB
        */
        if (isset($sub_user['sub_lists']) && count($sub_user['sub_lists']) > 0) {
          $wpdb->query(
            "
            UPDATE
            {$wpdb->prefix}observer_subs
            SET `status_mailchimp` =  'subscribed', `mc_subscribed_at` = '" . current_time('mysql') . "'
            WHERE id IN ( " . implode(',', array_keys($sub_user['sub_lists'])) . ")"
          );

          // Send consolidated Welcome email
          if (!get_user_meta($sub_user['user_id'], 'no_welcome_email', true)) {
            // $email->sendSubscribeConfirmationEmail( $sub_user['user_id'], $sub_user['sub_lists'] );
          }
        } // If there are $sub_user['sub_lists']

        if (isset($sub_user['unsub_lists']) && count($sub_user['unsub_lists']) > 0) {
          $wpdb->query(
            "
            UPDATE
            {$wpdb->prefix}observer_subs
            SET `status_mailchimp` =  'unsubscribed', `mc_unsubscribed_at` = '" . current_time('mysql') . "'
            WHERE id IN ( " . implode(',', array_keys($sub_user['unsub_lists'])) . ")"
          );

          // Send consolidated Sorry email
          // Disabled on 22 Sep 2020, limited Gmail use
          // $email->sendUnsubscribeConfirmationEmail( $sub_user['user_id'], $sub_user['unsub_lists'] );
        } // If there are $sub_user['unsub_lists']
      } // For Each $sub_user
    } // IF $sub_users is NOT empty

    /*
    * Push Tastemakers to MC
    */
    $tastemakers_query = "
      SELECT
        t.title,
        u.ID user_id,
        u.user_email
      FROM {$wpdb->prefix}observer_tastemakers t
        JOIN {$wpdb->prefix}observer_tastemaker_reviews tr
          ON t.id = tr.tastemaker_id
        JOIN {$wpdb->prefix}users u
          ON tr.user_id = u.ID
        WHERE
          tr.status = 'verified'
          AND
          tr.status_mailchimp IS NULL
        LIMIT 100
    ";
    $tastemakers = $wpdb->get_results($tastemakers_query);
    if ($tastemakers) {
      $tastemaker_subs = [];
      foreach ($tastemakers as $tastemaker) {
        $tags = [];
        $tastemakers_title = $tastemaker->title;
        $tag = [
          'name' => substr('Tastemakers: ' . $tastemaker->title, 0, 99),
          'status' => 'active'
        ];
        if (!isset($tastemaker_subs[$tastemaker->user_id])) {
          $tastemaker_subs[$tastemaker->user_id] = [];
        }
        $tastemaker_subs[$tastemaker->user_id]['email'] = $tastemaker->user_email;
        $tastemaker_subs[$tastemaker->user_id]['tags'][] = $tag;
      }


      if (count($tastemaker_subs) > 0) {
        foreach ($tastemaker_subs as $user_id => $details) {
          $subscriber_hash = $this->MailChimp->subscriberHash($details['email']);
          $tastemakers_subscribe = $this->MailChimp->post("lists/{$this->mailchimp_list_id}/members/{$subscriber_hash}/tags", [
            'tags' => $details['tags'],
          ]);
          $wpdb->update(
            $wpdb->prefix . 'observer_tastemaker_reviews',
            [
              'status_mailchimp' => 'processed',
            ],
            [
              'user_id' => $user_id,
              'status_mailchimp' => NULL,
            ]
          );
        }
      }
    }

    /*
    * Push Lead Generators (Comps) to MC
    */
    $comps_query = "
      SELECT
        l.title,
        u.ID user_id,
        u.user_email
      FROM {$wpdb->prefix}observer_lead_generators l
        JOIN {$wpdb->prefix}observer_lead_generator_responses lr
          ON l.id = lr.lead_generator_id
        JOIN {$wpdb->prefix}users u
          ON lr.user_id = u.ID
        WHERE
          lr.status = 'verified'
          AND
          lr.status_mailchimp IS NULL
        LIMIT 100
    ";
    $comp_entries = $wpdb->get_results($comps_query);
    if ($comp_entries) {
      $comp_subs = [];
      foreach ($comp_entries as $entry) {
        $tags = [];
        $entrys_title = $entry->title;
        $tag = [
          'name' => substr('Comp: ' . $entry->title, 0, 99),
          'status' => 'active'
        ];
        if (!isset($comp_subs[$entry->user_id])) {
          $comp_subs[$entry->user_id] = [];
        }
        $comp_subs[$entry->user_id]['email'] = $entry->user_email;
        $comp_subs[$entry->user_id]['tags'][] = $tag;
      }


      if (count($comp_subs) > 0) {
        foreach ($comp_subs as $user_id => $details) {
          $subscriber_hash = $this->MailChimp->subscriberHash($details['email']);
          $entry_subscribe = $this->MailChimp->post("lists/{$this->mailchimp_list_id}/members/{$subscriber_hash}/tags", [
            'tags' => $details['tags'],
          ]);
          if (is_array($entry_subscribe)) {
            echo '<pre>';
            print_r($entry_subscribe);
            echo '</pre>';
            $wpdb->update(
              $wpdb->prefix . 'observer_lead_generator_responses',
              [
                'status_mailchimp' => 'Error',
              ],
              [
                'user_id' => $user_id,
                'status_mailchimp' => NULL,
              ]
            );
            continue;
          }
          $wpdb->update(
            $wpdb->prefix . 'observer_lead_generator_responses',
            [
              'status_mailchimp' => 'processed',
            ],
            [
              'user_id' => $user_id,
              'status_mailchimp' => NULL,
            ]
          );
        }
      }
    }


    /*
    * Update subs counts in table
    */
    $sub_lists = $wpdb->get_results("SELECT list_id, COUNT(id) total FROM {$wpdb->prefix}observer_subs WHERE status = 'subscribed' GROUP BY list_id");
    if ($sub_lists) :
      foreach ($sub_lists as $sub_list) :
        $wpdb->update(
          $wpdb->prefix . 'observer_lists',
          [
            'sub_count' => $sub_list->total
          ],
          [
            'id' => $sub_list->list_id
          ]
        );
      endforeach;
    endif;

    /*
    * Update unsubs counts in table
    */
    $unsub_lists = $wpdb->get_results("SELECT list_id, COUNT(id) total FROM {$wpdb->prefix}observer_subs WHERE status = 'unsubscribed' GROUP BY list_id");
    if ($unsub_lists) :
      foreach ($unsub_lists as $unsub_list) :
        $wpdb->update(
          $wpdb->prefix . 'observer_lists',
          [
            'unsub_count' => $unsub_list->total
          ],
          [
            'id' => $unsub_list->list_id
          ]
        );
      endforeach;
    endif;

    update_option('BragObserver_CronEnd', date('Y-m-d H:i:s'), false);
  } // exec_cron_brag_observer

  public function _init()
  {
    /* if(!session_id()) {
      session_start();
  	} */

    add_rewrite_rule(
      'observer/category/([a-zA-Z0-9-]+)/?$',
      'index.php?pagename=observer&category_slug=$matches[1]',
      'top'
    );

    add_rewrite_rule(
      'observer/(?!magazine-subscriptions|refer-a-friend|competitions)([a-zA-Z0-9-]+)/?$',
      'index.php?pagename=observer&observer_slug=$matches[1]',
      'top'
    );

    if (is_user_logged_in()) {

      global $wpdb;

      $current_user = wp_get_current_user();
      $user_id = $current_user->ID;

      if (isset($_SESSION['subscribe_to_list_pending'])) {

        $list_id = absint($_SESSION['subscribe_to_list_pending']);

        $check_sub = $wpdb->get_row("SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '{$list_id}' LIMIT 1");
        if ($check_sub) {
          $status = isset($formData['status']) ? $formData['status'] : 'subscribed';

          $update_data = [
            'status' => $status, // 'subscribed',
            'status_mailchimp' => NULL,
          ];

          if ('subscribed' == $status)
            $update_data['subscribed_at'] = current_time('mysql');

          if ('unsubscribed' == $status)
            $update_data['unsubscribed_at'] = current_time('mysql');

          $wpdb->update(
            $wpdb->prefix . 'observer_subs',
            $update_data,
            [
              'id' => $check_sub->id,
            ]
          );
        } else {
          $wpdb->insert(
            $wpdb->prefix . 'observer_subs',
            [
              'user_id' => $user_id,
              'list_id' => $list_id,
              'status' => 'subscribed',
              'status_mailchimp' => NULL,
              'subscribed_at' => current_time('mysql'),
            ]
          );
        }

        unset($_SESSION['subscribe_to_list_pending']);
      } // If $_SESSION['subscribe_to_list_pending'] is set
    } // If user is logged in
  }

  public function _query_vars($query_vars)
  {
    $query_vars[] = 'observer_slug';
    $query_vars[] = 'category_slug';
    return $query_vars;
  }

  public function _admin_menu()
  {
    $main_menu = add_menu_page(
      'Brag Observer',
      'Brag Observer',
      'administrator',
      $this->plugin_slug,
      array($this, 'index'),
      'dashicons-email-alt2',
      10
    );

    add_submenu_page(
      $this->plugin_slug,
      'Manage List',
      'Add List',
      'administrator',
      $this->plugin_slug . '-manage-list',
      array($this, 'manage_list_show_form')
    );

    // Newsletters
    add_submenu_page(
      $this->plugin_slug,
      'Create/Edit/Preview Observer Newsletter',
      'Create Newsletter',
      'edit_posts',
      $this->plugin_slug . '-manage-newsletter',
      array($this, 'manage_newsletter_show_form')
    );

    add_submenu_page(
      $this->plugin_slug,
      'Observer Newsletters',
      'Newsletters',
      'edit_posts',
      $this->plugin_slug . '-view-newsletter-list',
      array($this, 'view_newsletter_list')
    );

    // Solus
    add_submenu_page(
      $this->plugin_slug,
      'Create/Edit/Preview Observer Solus',
      'Create Solus',
      'edit_posts',
      $this->plugin_slug . '-manage-solus',
      array($this, 'manage_solus_show_form')
    );

    add_submenu_page(
      $this->plugin_slug,
      'Observer Solus',
      'Solus',
      'edit_posts',
      $this->plugin_slug . '-view-solus-list',
      array($this, 'view_solus_list')
    );

    /* {{ Admins only */
    add_submenu_page(
      $this->plugin_slug,
      'Process cron',
      'Process cron',
      'administrator',
      $this->plugin_slug . '-process-cron',
      array($this, 'process_cron')
    );
    /* }} Admins only */

    // TMP
    /*
   add_submenu_page(
     $this->plugin_slug,
     'Update profile strength',
     'Update profile strength',
     'administrator',
     $this->plugin_slug .'-update-profile-strength',
     array( $this, 'show_update_profile_strength' )
   );
   */
  }

  /*
  public function show_update_profile_strength() {
    global $wpdb;
    include __DIR__ . '/partials/imports/update-profile-strength.php';
  }

  public function update_profile_strength() {
    global $wpdb;
    $tmp_subs_query = "SELECT * FROM tmp_profile_strength WHERE strength IS NULL LIMIT 500";
    $tmp_subs = $wpdb->get_results( $tmp_subs_query );
    if ( $tmp_subs ) {

      $message_user = '';

      foreach( $tmp_subs as $tmp_sub ) {

        $process = true;

        $user = get_user_by( 'ID', $tmp_sub->user_id );

        if( ! $user ) {
          $message_user .= '<div class="text-danger">User with ID ' . $tmp_sub->user_id . ' does not exist.</div>';
          $wpdb->update( 'tmp_profile_strength', [ 'strength' => '0', ], [ 'id' => $tmp_sub->id ] );
          wp_send_json_success( [ $message_user ] ); die();
        }

        $profile_strength = 0;
        if( get_user_meta( $tmp_sub->user_id, 'first_name', true ) )
          $profile_strength += 20;

        if( get_user_meta( $tmp_sub->user_id, 'last_name', true ) )
          $profile_strength += 20;

        if( get_user_meta( $tmp_sub->user_id, 'state', true ) )
          $profile_strength += 20;

        if( get_user_meta( $tmp_sub->user_id, 'birthday', true ) )
          $profile_strength += 20;

        if( get_user_meta( $tmp_sub->user_id, 'gender', true ) )
          $profile_strength += 20;

        $message_user .= '<div class="text-success">User with ID ' . $tmp_sub->user_id . ' | strength: ' . $profile_strength . '</div>';

        $wpdb->update( 'tmp_profile_strength', [ 'strength' => $profile_strength, ], [ 'id' => $tmp_sub->id ] );

      } // For Each $tmp_sub

      wp_send_json_success( [ $message_user ] ); die();
    } else {
      wp_send_json_error( [ 'Done' ] );
    }
  } // update_profile_strength()
  */

  /*
  * Process CRON
  */
  public function process_cron()
  {
    date_default_timezone_set('Australia/NSW');
    $next_run_timestamp = wp_next_scheduled('cron_hook_brag_observer', array(NULL, NULL));
    echo '<br>Scheduled automatic run is at ' . date('d-M-Y h:i:sa', $next_run_timestamp);
    echo '<br>Current Date/Time: ' . date('d-M-Y h:i:sa');

    $this->exec_cron_brag_observer();
  }

  /*
  * Home - List Index
  */
  public function index()
  {
    include_once __DIR__ . '/partials/index.php';
  } // Index

  /*
  * Setup SEO
  */
  public function setupSEO()
  {
    require_once  __DIR__ . '/classes/seo.class.php';
    $seo = new SEO();
    add_filter('wpseo_opengraph_image', [$seo, 'wpseo_opengraph_image']);
    add_filter('wpseo_canonical', [$seo, 'wpseo_canonical'], 10, 1);
    add_filter('wpseo_opengraph_url', [$seo, 'wpseo_opengraph_url']);
    add_filter('wpseo_opengraph_desc', [$seo, 'wpseo_opengraph_desc']);
    add_filter('wpseo_opengraph_title', [$seo, 'wpseo_opengraph_title']);
    add_filter('wpseo_title', [$seo, 'wpseo_title']);
    add_filter('wpseo_metadesc', [$seo, 'wpseo_metadesc']);
  }

  /*
  * Load JS and CSS
  */
  public function load_js_css()
  {
    // if ( is_page_template( $this->page_template1 ) || is_page_template( $this->page_template2 ) )
    {
      wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '/js/scripts.js', array('jquery'), time(), true);
      $args = array(
        'url'   => admin_url('admin-ajax.php'),
        // 'ajax_nonce' => wp_create_nonce( $this->plugin_slug . '-nonce' ),
      );
      wp_localize_script($this->plugin_name, $this->plugin_name, $args);
    }
  }

  /*
  * Send refer invite (AJAX)
  */
  public function send_refer_invite()
  {
    if (defined('DOING_AJAX') && DOING_AJAX) :

      global $wpdb;

      parse_str($_POST['formData'], $formData);

      $current_user = wp_get_current_user();

      $list_id = sanitize_text_field($formData['list']);
      $list = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}observer_lists WHERE id = '{$list_id}' LIMIT 1");

      if (!$list)
        wp_send_json_error(['Invalid request']);

      require_once __DIR__ . '/classes/email.class.php';
      $email = new Email($this);

      $invitees = explode(',', $formData['email']);
      $invitees = array_map('trim', $invitees);
      foreach ($invitees as $invitee) {

        if ($invitee == $current_user->user_email)
          continue;

        if (!is_email($invitee))
          continue;

        $invitee = sanitize_text_field($invitee);

        $check_sub_query = "
        SELECT
          s.id
        FROM {$wpdb->prefix}observer_subs s
          JOIN {$wpdb->users} u
            ON u.ID = s.user_id
        WHERE
          u.user_email = '{$invitee}' AND
          s.status = 'subscribed' AND
          s.list_id = '{$list_id}'
        LIMIT 1
        ";

        $check_sub = $wpdb->get_row($check_sub_query);

        if (!$check_sub) {
          $wpdb->insert(
            $wpdb->prefix . 'observer_invites',
            [
              'user_id' => $current_user->ID,
              'invitee' => $invitee,
              'list_id' => $list_id,
              'message' => sanitize_textarea_field($formData['message']),
            ],
            ['%d', '%s', '%d', '%s',]
          );
          $email->sendObserverInvitation($current_user, $list, $invitee, $formData['message']);
        }
      }

      wp_send_json_success(['Your invitation is on the way!']);
    else :
      wp_send_json_error(['Invalid request']);
    endif; // If doing AJAX
  }

  /*
  * Subscribe (AJAX)
  */
  public function subscribe_observer()
  {
    if (defined('DOING_AJAX') && DOING_AJAX) :

      // parse_str($_POST['formData'], $formData);
      if (isset($_POST['formData'])) {
        parse_str($_POST['formData'], $formData);
      } else {
        $formData = $_POST;
      }

      global $wpdb;

      if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        if (!$current_user->roles) {
          $current_user->add_role('subscriber');
        }
        $user_id = $current_user->ID;
      } else {
        require_once  __DIR__ . '/classes/user.class.php';
        $user = new User();
        $user_id = $user->create($formData, home_url('/observer/'));
      }

      $check_sub = $wpdb->get_row("SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '{$formData['list']}' LIMIT 1");
      if ($check_sub) {
        $status = isset($formData['status']) ? $formData['status'] : 'subscribed';

        // error_log( $check_sub->id );

        $update_data = [
          'status' => $status, // 'subscribed',
          'status_mailchimp' => NULL,
        ];

        if ('subscribed' == $status)
          $update_data['subscribed_at'] = current_time('mysql');

        if ('unsubscribed' == $status)
          $update_data['unsubscribed_at'] = current_time('mysql');

        $wpdb->update(
          $wpdb->prefix . 'observer_subs',
          $update_data,
          [
            // 'id' => $check_sub->id,
            'user_id' => $user_id,
            'list_id' => $formData['list'],
          ]
        );
      } else {
        $wpdb->insert(
          $wpdb->prefix . 'observer_subs',
          [
            'user_id' => $user_id,
            'list_id' => $formData['list'],
            'status' => 'subscribed',
            'status_mailchimp' => NULL,
            'subscribed_at' => current_time('mysql'),
          ]
        );
      }

      // require_once __DIR__ . '/classes/email.class.php';
      // $email = new Email();
      // $email->sendSubscribeConfirmationEmail( $formData['list'] );

      wp_send_json_success($formData);
    else :
      wp_send_json_error(['Invalid request']);
    endif; // If doing AJAX
  } // subscribe_observer - AJAX

  /*
  * Set Subscribe Pending for a list (AJAX)
  */
  public function set_subscribe_to_list_pending()
  {
    if (defined('DOING_AJAX') && DOING_AJAX) :
      global $wpdb;

      parse_str($_POST['formData'], $formData);

      if (isset($formData['list_id']) && is_numeric($formData['list_id'])) {
        $list = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}observer_lists WHERE id = {$formData['list_id']} LIMIT 1");

        if ($list) {
          $_SESSION['subscribe_to_list_pending'] = $list->id; // $formData['list_id'];
          $_SESSION['ReturnToUrl'] = home_url('/observer/' . $list->slug . '/');
        }
      }
    endif;
  } // set_subscribe_to_list_pending

  /*
  * Set Apple Redirect URL
  */
  public function set_apple_redirect_url()
  {
    if (defined('DOING_AJAX') && DOING_AJAX) :
      global $wpdb;

      parse_str($_POST['formData'], $formData);

      if (isset($formData['list_id']) && is_numeric($formData['list_id'])) {
        $list = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}observer_lists WHERE id = {$formData['list_id']} LIMIT 1");

        if ($list) {
          $_SESSION['ReturnToUrl'] = home_url('/observer/' . $list->slug . '/');
        }
      } else {
        $_SESSION['ReturnToUrl'] = wp_get_referer() ?: home_url();
      }
    endif;
  } // set_apple_redirect_url

  /*
  * Vote (AJAX)
  */
  public function vote_observer()
  {
    if (defined('DOING_AJAX') && DOING_AJAX) :

      parse_str($_POST['formData'], $formData);

      global $wpdb;

      if (is_user_logged_in()) {

        $current_user = wp_get_current_user();
        if (!$current_user->roles) {
          $current_user->add_role('subscriber');
        }

        $user_id = $current_user->ID;
      } else {
        require_once  __DIR__ . '/classes/user.class.php';
        $user = new User();
        $user_id = $user->create($formData, home_url('/observer/'));
      }

      $check_vote = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}observer_votes WHERE user_id = '{$user_id}' AND list_id = '{$formData['list']}' LIMIT 1");
      if (!$check_vote) {
        $wpdb->insert(
          $wpdb->prefix . 'observer_votes',
          [
            'user_id' => $user_id,
            'list_id' => $formData['list'],
          ]
        );
      }

      $check_sub = $wpdb->get_row("SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '{$formData['list']}' LIMIT 1");
      if ($check_sub) {
        if ('unsubscribed' == $check_sub->status) {
          $wpdb->update(
            $wpdb->prefix . 'observer_subs',
            [
              'status' => 'subscribed',
              'subscribed_at' => current_time('mysql'),
            ],
            [
              'id' => $check_sub->id,
            ]
          );
        }
      } else {
        $wpdb->insert(
          $wpdb->prefix . 'observer_subs',
          [
            'user_id' => $user_id,
            'list_id' => $formData['list'],
            'status' => 'subscribed',
            'subscribed_at' => current_time('mysql'),
          ]
        );
      }

      // require_once __DIR__ . '/classes/email.class.php';
      // $email = new Email();
      // $email->sendSubscribeConfirmationEmail( $formData['list'] );

      wp_send_json_success($formData);
    else :
      wp_send_json_error(['Invalid request']);
    endif; // If doing AJAX
  } // vote_observer - AJAX

  /*
  * Manage List - Show form
  */
  public function manage_list_show_form()
  {
    include_once __DIR__ . '/partials/manage-list.php';
  } // Add/Edit List

  /*
  * Create/Update List
  */
  public function action_manage_observer_list()
  {
    global $wpdb;
    $errors = [];
    if (isset($_POST)) {

      $post = stripslashes_deep($_POST);

      $observer_id = isset($post['id']) ? (int) $post['id'] : null;

      $_SESSION['formdata'] = $post;

      // echo '<pre>'; print_r( $_SESSION['formdata'] ); exit;
      // var_dump( $post['category'] ); exit;

      $required_fields = [
        'title',
        'slug',
        // 'description',
        'image_url',
        // 'email_header_image_url',
        'frequency',
        'interest_id',
        'category',
      ];

      foreach ($required_fields as $required_field) :
        if (!isset($_POST[$required_field]) || '' == $_POST[$required_field]) :
          $errors[] = ucfirst(str_replace(array('-', '_',), ' ', $required_field)) . ' is required.';
        endif;
      endforeach; // For Each $required_fields

      if (filter_var($_POST['image_url'], FILTER_VALIDATE_URL) === FALSE) :
        $errors[] = 'Please input valid Image URL.';
      endif;

      if (isset($_POST['email_header_image_url']) && '' != trim($_POST['email_header_image_url']) && filter_var($_POST['email_header_image_url'], FILTER_VALIDATE_URL) === FALSE) :
        $errors[] = 'Please input valid Email Header Image URL.';
      endif;

      // Check Slug has already assigned to any list
      $check_slug_query = "SELECT * FROM {$wpdb->prefix}observer_lists WHERE 1 = 1 AND slug = '{$_POST['slug']}' ";
      if (!is_null($observer_id)) :
        $check_slug_query .= " AND id != '{$observer_id}' ";
      endif;
      $check_slug_query .= " LIMIT 1";
      $check_slug = $wpdb->get_row($check_slug_query);
      if ($check_slug) :
        $errors[] = 'Slug has already been assigned to ' . $check_slug->title . ' list.';
      endif;

      // Check if MailChimp Interest has already assigned to any list
      $check_interest_query = "SELECT * FROM {$wpdb->prefix}observer_lists WHERE 1 = 1 AND interest_id = '{$_POST['interest_id']}' ";
      if (!is_null($observer_id)) :
        $check_interest_query .= " AND id != '{$observer_id}' ";
      endif;
      $check_interest_query .= " LIMIT 1";
      $check_interest = $wpdb->get_row($check_interest_query);
      if ($check_interest) :
        $errors[] = 'MailChimp Interest has already been assigned to ' . $check_interest->title . ' list.';
      endif;

      if (count($errors) > 0) :
        $_SESSION['errors'] = $errors;
        wp_redirect($_SERVER['HTTP_REFERER']);
      else :

        $data = [
          'interest_id' => $post['interest_id'],
          'title' => $post['title'],
          'slug' => $post['slug'],
          'description' => $post['description'],
          'keywords' => $post['keywords'],
          'frequency' => $post['frequency'],
          'image_url' => $post['image_url'],
          'email_header_image_url' => $post['email_header_image_url'],
          'status' => $post['status'],
          'welcome_email_intro' => $post['welcome_email_intro'],
          'welcome_email_outro' => $post['welcome_email_outro'],
        ];

        if (is_null($observer_id)) :
          $observer_id = $wpdb->insert($wpdb->prefix . 'observer_lists', $data);
        else :
          $wpdb->update($wpdb->prefix . 'observer_lists', $data, ['id' => $observer_id]);
          $wpdb->delete(
            $wpdb->prefix . 'observer_list_categories',
            ['list_id' => $observer_id]
          );
        endif;

        if (isset($post['category']) && is_array($post['category']) && count($post['category']) > 0) :
          foreach ($post['category'] as $category) :
            $wpdb->insert(
              $wpdb->prefix . 'observer_list_categories',
              [
                'list_id' => $observer_id,
                'category_id' => $category,
              ]
            );
          endforeach;
        endif;

        // wp_redirect( $_SERVER['HTTP_REFERER'] );
        unset($_SESSION['formdata']);
        wp_redirect('admin.php?page=' . $this->plugin_slug);
      endif;

      exit();
    }
  } // action_manage_observer_list

  /*
  * Manage Newsletter - Show form
  */
  public function manage_newsletter_show_form()
  {
    include_once __DIR__ . '/partials/manage-newsletter.php';
  } // Add/Edit Newsletter

  /*
  * View list of newsletters
  */
  public function view_newsletter_list()
  {
    include_once __DIR__ . '/partials/view-newsletter-list.php';
  } // View Newsletter list

  /*
  * Manage Solus - Show form
  */
  public function manage_solus_show_form()
  {
    include_once __DIR__ . '/partials/manage-solus.php';
  } // Add/Edit Solus

  /*
  * View list of Solus
  */
  public function view_solus_list()
  {
    include_once __DIR__ . '/partials/view-solus-list.php';
  } // View Solus list

  /*
  * Footer: add modal
  */
  public function wp_footer()
  {
    // if ( ! is_page_template( $this->page_template1 ) )
    //   return;

    // include_once __DIR__ . '/partials/modal-login-register.php';

    $current_user = wp_get_current_user();
    // if ( ! is_user_logged_in() && ( is_home() || is_front_page() || is_page_template( $this->page_template1 ) ) ) :
    if (!is_user_logged_in() && is_page_template($this->page_template1)) :
      include_once __DIR__ . '/partials/modal-subscribe-vote.php';
    endif; // Modal to add only when user is NOT logged in
  } // wp_footer

  /*
  * Call Remote API
  */
  private static function callAPI($method, $url, $data = '', $content_type = '')
  {
    $curl = curl_init();
    switch ($method) {
      case "POST":
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data)
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        break;
      case "PUT":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        if ($data)
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        break;
      default:
        if ($data)
          $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    if ($content_type !== false) {
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
      ));
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    // if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    if (isset($_ENV) && isset($_ENV['ENVIRONMENT']) && 'sandbox' == $_ENV['ENVIRONMENT']) {
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    // EXECUTE:
    $result = curl_exec($curl);

    // error_log( $url );
    // if ( 'POST' == $method ) {
    // echo '<pre>'; var_dump( curl_error( $curl ) ); echo '</pre>';
    // }
    if (!$result)
      return;
    curl_close($curl);
    return $result;
  }

  /*
  * Get OG data from any URL
  */
  public function get_remote_data()
  {
    if (strlen($_POST['data']) > 0) :
      parse_str($_POST['data'], $data);
      $sites_html = file_get_contents($data['url']);
      $html = new DOMDocument();
      @$html->loadHTML($sites_html);
      $meta_og_title = $meta_og_img = $meta_og_description = null;
      foreach ($html->getElementsByTagName('meta') as $meta) {
        if ($meta->getAttribute('property') == 'og:image') {
          if (!isset($meta_og_img)) {
            $meta_og_img = $meta->getAttribute('content');
          }
        } elseif ($meta->getAttribute('property') == 'og:title') {
          if (!isset($meta_og_title)) {
            $meta_og_title = $meta->getAttribute('content');
          }
        } elseif ($meta->getAttribute('property') == 'og:description') {
          if (!isset($meta_og_description)) {
            $meta_og_description = $meta->getAttribute('content');
          }
        }
      }
      echo json_encode(array('success' => true, 'title' => trim($meta_og_title), 'description' => trim($meta_og_description), 'image' => trim($meta_og_img)));
      die();
    endif;
    echo json_encode(array('success' => false, 'url' => $data['url']));
    die();
  }

  /*
  * Save Newsletter
  */
  public function save_newsletter()
  {

    // if ( count( $_POST['data'] ) > 0 ) {
    if ($_POST['data']) {

      $errors = [];
      $character_limit = 170;

      parse_str($_POST['data'], $data);

      // wp_send_json_error( [ '<pre>' . print_r( $data, true ) . '</pre>' ] );

      if (!isset($data['subject']) || '' == trim($data['subject'])) :
        array_push($errors, 'Subject is required.');
      endif;

      if (strtotime($data['date_for']) < strtotime(date('Y-m-d'))) :
        array_push($errors, 'Date must be today or in future.');
      endif;

      if (!isset($data['posts']) || (count($data['posts']) > 6 && count($data['posts']) % 2 != 0)) :
        array_push($errors, 'Number of articles must be even if there are more than 6 articles, either remove one or add one.');
      endif;

      foreach ($data['post_excerpts'] as $key => $post_excerpt) :
        if (strlen($post_excerpt) == 0) :
          array_push($errors, 'Blurb required for <strong>' . $data['post_titles'][$key] . '</strong>');
        endif;
        if (strlen($post_excerpt) > $character_limit) :
          array_push($errors, 'Character limit (' . $character_limit . ') for blurb exceeded for the article <strong>' . $data['post_titles'][$key] . '</strong>');
        endif;
      endforeach;

      if (count($errors) > 0) :
        wp_send_json_error($errors);
      endif;

      if (isset($data['post_images'])) :
        foreach ($data['post_images'] as $key => $image_url) :
          if ($image_url) :
            $data['post_images'][$key] = $this->resize_image($image_url, 660, 370, $data['post_links'][$key]);
          endif;
        endforeach;
      endif;

      if (isset($data['posts']) && count($data['posts']) > 0)
        asort($data['posts']);

      $data = stripslashes_deep($data);

      unset($data['articles']);

      global $wpdb;
      $table = $wpdb->prefix . "observer_newsletters";

      if (isset($data['id'])) :
        $newsletter_id = $data['id'];
        $wpdb->update(
          $table,
          array(
            'date_for' => date('Y-m-d', strtotime($data['date_for'])),
            'details' => json_encode($data),
            'status' => '0',
            'updated_at' => current_time('mysql'),
          ),
          array('id' => $data['id'])
        );
      else :
        $wpdb->insert(
          $table,
          array(
            'list_id' => $data['list_id'],
            'date_for' => date('Y-m-d', strtotime($data['date_for'])),
            'details' => json_encode($data),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
          )
        );
        $newsletter_id = $wpdb->insert_id;
      endif;

      $wpdb->delete(
        $wpdb->prefix . 'observer_newsletter_articles',
        [
          'newsletter_id' => $newsletter_id,
        ]
      );
      foreach ($data['post_links'] as $key => $value) {
        $wpdb->insert(
          $wpdb->prefix . 'observer_newsletter_articles',
          [
            'newsletter_id' => $newsletter_id,
            'article_url' => $value,
          ]
        );
      }

      wp_send_json_success();
    }
    // wp_send_json_error( [ 'Invalid request' ] );
  } // Save newsletter

  /*
  * Save Solus
  */
  public function save_solus()
  {
    if ($_POST['data']) {

      $errors = [];

      parse_str($_POST['data'], $data);

      if (strtotime($data['date_for']) < strtotime(date('Y-m-d'))) :
        array_push($errors, 'Date must be today or in future.');
      endif;

      if (!isset($data['lists'])) {
        array_push($errors, 'Please select at least one List.');
      }

      if (
        !isset($data['solus_image_url']) ||
        '' == ($data['solus_image_url']) ||
        filter_var($data['solus_image_url'], FILTER_VALIDATE_URL) === FALSE
      ) {
        array_push($errors, 'Valid Solus Image is required (with http:// or https://).');
      }

      $solus_image_headers = get_headers($data['solus_image_url'], true);
      if ($solus_image_headers['Content-Length'] / 1024 > 300) {
        array_push($errors, 'Solus Image must be less than 300kb in size.');
      }

      if (
        !isset($data['solus_link']) ||
        '' == ($data['solus_link']) ||
        filter_var($data['solus_link'], FILTER_VALIDATE_URL) === FALSE
      ) {
        array_push($errors, 'Valid Solus Click-through URL is required (with http:// or https://).');
      }

      $required_fields = [
        'solus_image_url',
        'solus_link',
      ];

      if (count($errors) > 0) :
        wp_send_json_error($errors);
      endif;

      $data = stripslashes_deep($data);

      global $wpdb;
      $table = $wpdb->prefix . "observer_solus";

      if (isset($data['id'])) :
        $wpdb->update(
          $table,
          array(
            'lists' => implode(',', $data['lists']),
            'date_for' => date('Y-m-d', strtotime($data['date_for'])),
            'details' => json_encode($data),
            'solus_image_url' => $data['solus_image_url'],
            'solus_link' => $data['solus_link'],
            'status' => '0',
            'updated_at' => current_time('mysql'),
          ),
          array('id' => $data['id'])
        );
      else :
        $wpdb->insert(
          $table,
          array(
            'lists' => implode(',', $data['lists']),
            'details' => json_encode($data),
            'date_for' => date('Y-m-d', strtotime($data['date_for'])),
            'solus_image_url' => $data['solus_image_url'],
            'solus_link' => $data['solus_link'],
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
          )
        );
      endif;
      wp_send_json_success();
    }
  } // save_solus()

  /*
  * Resize image
  */
  function resize_image($url, $thumb_width, $thumb_height, $link_url = null, $import_dir_part = NULL, $filename = NULL)
  {
    $dir = wp_upload_dir();

    $path = explode('?', $url);
    $url = $path[0];

    if (is_null($import_dir_part)) {
      $import_dir_part = '/observer/' . date('Y-m/d/');
    }
    $import_dir =  $dir['basedir'] . $import_dir_part;
    if (!is_dir($import_dir))
      wp_mkdir_p($import_dir);
    $img = $import_dir . basename($url);

    $arrContextOptions = array(
      "ssl" => array(
        "verify_peer" => false,
        "verify_peer_name" => false,
      ),
    );

    file_put_contents($img, file_get_contents($url, false, stream_context_create($arrContextOptions)));

    $explode = explode(".", basename($url));
    $filetype = end($explode);

    if ($filetype == 'jpg') {
      $image = imagecreatefromjpeg("$img");
    } else
    if ($filetype == 'jpeg') {
      $image = imagecreatefromjpeg("$img");
    } else
    if ($filetype == 'png') {
      $image = imagecreatefrompng("$img");
    } else
    if ($filetype == 'gif') {
      $image = imagecreatefromgif("$img");
    }

    if (is_null($filename)) {
      $filename = str_replace('.' . $filetype, '.jpg', basename($url));
    }
    $filepath = $import_dir . $filename;

    $width = imagesx($image);
    $height = imagesy($image);
    $original_aspect = $width / $height;
    $thumb_aspect = $thumb_width / $thumb_height;
    if ($original_aspect >= $thumb_aspect) {
      // If image is wider than thumbnail (in aspect ratio sense)
      $new_height = $thumb_height;
      $new_width = $width / ($height / $thumb_height);
    } else {
      // If the thumbnail is wider than the image
      $new_width = $thumb_width;
      $new_height = $height / ($width / $thumb_width);
    }
    $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
    // Resize and crop
    imagecopyresampled(
      $thumb,
      $image,
      0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
      0 - ($new_height - $thumb_height) / 2, // Center the image vertically
      0,
      0,
      $new_width,
      $new_height,
      $width,
      $height
    );

    /*
    if ( ! is_null( $link_url ) ) {
      $site_icon_img_path = plugin_dir_path( __FILE__ ) . '/images/rsau-2.png';
      $site_icon_img_path2 = plugin_dir_path( __FILE__ ) . '/images/rsau-3.png';

      $site_icon_img = imagecreatefrompng( $site_icon_img_path );


      $img_site_icon = imagecreatetruecolor( 1000, 1000 );
      imagecopyresampled( $img_site_icon, $site_icon_img, 0, 1000, 0, 1000, 1000, 1000, 1000, 1000);

      imagepng( $img_site_icon, $site_icon_img_path2, 0 );

      imagecopyresampled( $thumb, $img_site_icon, 0, 320, 0, 0, 50, 50, 50, 50);
    }
    */

    imagejpeg($thumb, $filepath, 90);
    $upload    = wp_upload_dir();
    $base_url = $upload['baseurl'] . $import_dir_part;
    return $base_url . $filename;
  } // resize_image

  /*
  * Get Observer Categories from DB
  */
  function getCategories()
  {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}observer_categories");
  }

  /*
  * Get Magazine Subscriptions
  */
  public function getMagSubscriptions($email = NULL)
  {
    if (is_null($email))
      return;
    $subscriptions = $this->callAPI(
      'get',
      $this->mag_sub['api_url'] . 'get',
      [
        'key' => $this->mag_sub['rest_api_key'],
        'email' => $email
      ],
      false
    );

    $subscriptions = json_decode($subscriptions);

    return $subscriptions;
  }

  /*
  * Get Single Magazine Subscription
  */
  public function getMagSubscription($uniqid = NULL)
  {
    if (is_null($uniqid))
      return;
    $subscriptions = $this->callAPI(
      'get',
      $this->mag_sub['api_url'] . 'get',
      [
        'key' => $this->mag_sub['rest_api_key'],
        'uniqid' => $uniqid
      ],
      false
    );
    $subscriptions = json_decode($subscriptions);

    return $subscriptions;
  }

  /*
  * Get payment methods (Stripe) for customer
  */
  public function getPaymentMethods($customer_id = NULL)
  {
    if (is_null($customer_id))
      return;
    require_once __DIR__ . '/classes/payment.class.php';
    $payment = Payment::getInstance();
    return $payment->getPaymentMethods($customer_id);
  }

  /*
  * Setup Stripe Payment Intent for Mag Subs
  */
  public function setupIntent($customer_id = NULL)
  {
    if (is_null($customer_id))
      return;
    require_once __DIR__ . '/classes/payment.class.php';
    $payment = Payment::getInstance();
    return $payment->setupIntent($customer_id);
  }

  /*
  * Print Social icons for newsletter
  */
  public function print_social_icons()
  {
    /**
     * added seperate social links and logo for all multisite
     */
    $bm_social_links = array(
      'facebook' => 'https://www.facebook.com/thebragmag',
      'twitter' => 'https://twitter.com/thebrag',
      'instagram' => 'https://instagram.com/thebragmag',
      'link' => 'https://thebrag.com',
    );
?>
    <table align="center" border="0" cellpadding="0" cellspacing="0">
      <tbody>
        <tr>
          <td align="center" valign="top">
            <!--[if mso]>
          <table align="center" border="0" cellspacing="0" cellpadding="0">
          <tr>
          <![endif]-->
            <!--[if mso]>
          <td align="center" valign="top">
          <![endif]-->
            <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;">
              <tbody>
                <tr>
                  <td valign="top" style="padding-right:5px; padding-bottom:9px;" class="mcnFollowContentItemContainer">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnFollowContentItem">
                      <tbody>
                        <tr>
                          <td align="left" valign="middle" style="padding-top:5px; padding-right:10px; padding-bottom:5px; padding-left:9px;">
                            <table align="left" border="0" cellpadding="0" cellspacing="0">
                              <tbody>
                                <tr>
                                  <td align="center" valign="middle" width="24" class="mcnFollowIconContent">
                                    <a href="<?php echo esc_url($bm_social_links['facebook']); ?>" target="_blank"><img alt="Facebook" src="https://cdn-images.mailchimp.com/icons/social-block-v2/color-facebook-48.png" style="display:block;" height="24" width="24" class=""></a>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <!--[if mso]>
        </td>
        <![endif]-->
            <!--[if mso]>
        <td align="center" valign="top">
        <![endif]-->
            <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;">
              <tbody>
                <tr>
                  <td valign="top" style="padding-right:5px; padding-bottom:9px;" class="mcnFollowContentItemContainer">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnFollowContentItem">
                      <tbody>
                        <tr>
                          <td align="left" valign="middle" style="padding-top:5px; padding-right:10px; padding-bottom:5px; padding-left:9px;">
                            <table align="left" border="0" cellpadding="0" cellspacing="0" width="">
                              <tbody>
                                <tr>
                                  <td align="center" valign="middle" width="24" class="mcnFollowIconContent">
                                    <a href="<?php echo esc_url($bm_social_links['twitter']); ?>" target="_blank"><img alt="Twitter" src="https://cdn-images.mailchimp.com/icons/social-block-v2/color-twitter-48.png" style="display:block;" height="24" width="24" class=""></a>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <!--[if mso]>
      </td>
      <![endif]-->
            <!--[if mso]>
      <td align="center" valign="top">
      <![endif]-->
            <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;">
              <tbody>
                <tr>
                  <td valign="top" style="padding-right:5px; padding-bottom:9px;" class="mcnFollowContentItemContainer">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnFollowContentItem">
                      <tbody>
                        <tr>
                          <td align="left" valign="middle" style="padding-top:5px; padding-right:10px; padding-bottom:5px; padding-left:9px;">
                            <table align="left" border="0" cellpadding="0" cellspacing="0" width="">
                              <tbody>
                                <tr>
                                  <td align="center" valign="middle" width="24" class="mcnFollowIconContent">
                                    <a href="<?php echo esc_url($bm_social_links['instagram']); ?>" target="_blank"><img alt="Instagram" src="https://cdn-images.mailchimp.com/icons/social-block-v2/color-instagram-48.png" style="display:block;" height="24" width="24" class=""></a>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <!--[if mso]>
    </td>
    <![endif]-->

            <!--[if mso]>
  </tr>
  </table>
  <![endif]-->
          </td>
        </tr>
      </tbody>
    </table>
<?php
  }

  /*
  * Mag Sub: Update Billing + Shipping Details
  */
  public function updateBillingShippingDetails($data)
  {

    $data['key'] = $this->mag_sub['rest_api_key'];

    $update_billing = $this->callAPI(
      'POST',
      $this->mag_sub['api_url'] . 'update_billing',
      $data,
      false
    );

    $update_shipping = $this->callAPI(
      'POST',
      $this->mag_sub['api_url'] . 'update_shipping',
      $data,
      false
    );

    return json_encode(array_merge((array)json_decode($update_billing), (array)json_decode($update_shipping)));
  }

  /*
  * Mag Sub: Update Shipping Details
  */
  public function updateShippingDetails($data)
  {

    $data['key'] = $this->mag_sub['rest_api_key'];

    $update_json = $this->callAPI(
      'POST',
      $this->mag_sub['api_url'] . 'update_shipping',
      $data,
      false
    );

    $update = json_decode($update_json);

    if ($update->success) {
      if (isset($data['make-billing-same']) && $data['make-billing-same'] == '1') {

        $data['full_name'] = $data['sub_full_name'];
        $data['sub_address_1'] = $data['shipping_address_1'];
        $data['sub_address_2'] = $data['shipping_address_2'];
        $data['sub_city'] = $data['shipping_city'];
        $data['sub_state'] = $data['shipping_state'];
        $data['sub_postcode'] = $data['shipping_postcode'];
        $data['sub_country'] = $data['shipping_country'];

        $update_json = $this->callAPI(
          'POST',
          $this->mag_sub['api_url'] . 'update_billing',
          $data,
          false
        );
      }
    }

    return $update_json;
  }

  /*
  * Mag Sub: Update Billing Details
  */
  public function updateBillingDetails($data)
  {

    $data['key'] = $this->mag_sub['rest_api_key'];

    $update_json = $this->callAPI(
      'POST',
      $this->mag_sub['api_url'] . 'update_billing',
      $data,
      false
    );

    $update = json_decode($update_json);

    if ($update->success) {
      if (isset($data['make-shipping-same']) && $data['make-shipping-same'] == '1') {

        $data['shipping_address_1'] = $data['sub_address_1'];
        $data['shipping_address_2'] = $data['sub_address_2'];
        $data['shipping_city'] = $data['sub_city'];
        $data['shipping_state'] = $data['sub_state'];
        $data['shipping_postcode'] = $data['sub_postcode'];
        $data['shipping_country'] = $data['sub_country'];

        $update_json = $this->callAPI(
          'POST',
          $this->mag_sub['api_url'] . 'update_shipping',
          $data,
          false
        );
      }
    }

    return $update_json;
  }

  /*
  * Mag Sub: Cancel Autorenew - Untick Active field in Salesforce
  */
  public function cancelAutoRenew($data)
  {

    $data['key'] = $this->mag_sub['rest_api_key'];

    $cancelAutoRenew = $this->callAPI(
      'POST',
      $this->mag_sub['api_url'] . 'cancel_autorenew',
      $data,
      false
    );

    return $cancelAutoRenew;
  }

  /*
  * Mag Sub: Cancel Autorenew - Tick Active field in Salesforce
  */
  public function enableAutoRenew($data)
  {

    $data['key'] = $this->mag_sub['rest_api_key'];

    $enableAutoRenew = $this->callAPI(
      'POST',
      $this->mag_sub['api_url'] . 'enable_autorenew',
      $data,
      false
    );

    return $enableAutoRenew;
  }

  /*
  * Mag Sub: Update Payment Details
  */
  public function update_payment_details()
  {
    require_once __DIR__ . '/classes/payment.class.php';

    $sub_id = isset($_POST['id']) ? trim($_POST['id']) : NULL;
    $subscription = $this->getMagSubscription($sub_id);

    if ($subscription && $subscription[0]) {
      $subscription = $subscription[0];
      $payment = Payment::getInstance();
      return $payment->update_payment_details($subscription->stripe_customer_id);
    } else {
      error_log('BO Error: Subscription not found for : ' . $sub_id);
    }
  }

  protected function getPluginSlug()
  {
    return $this->plugin_slug;
  }

  /*
  * Save post meta if shortcode is present
  */
  public function _save_post($post_id, $post, $update)
  {

    if (strpos($post->post_content, '[observer_lead_generator_form') !== FALSE) {
      preg_match('/(.*?)\[observer_lead_generator_form id=\"(\d+)\"(.*?)\]/', $post->post_content, $matches);
      if ($matches && $matches[2]) {
        update_post_meta($post_id, 'has_lead_generator', $matches[2]);
      } else {
        delete_post_meta($post_id, 'has_lead_generator');
      }
    } else {
      delete_post_meta($post_id, 'has_lead_generator');
    }
  }

  /*
  * REST: API Endpoints
  */
  /*
  public function _rest_api_init() {
    register_rest_route( $this->plugin_name . '/v1', '/get_topics', array(
      'methods' => 'GET',
      'callback' => [ $this, 'rest_get_topics' ],
    ) );

    register_rest_route( $this->plugin_name . '/v1', '/sub_unsub', array(
      'methods' => 'POST',
      'callback' => [ $this, 'rest_sub_unsub' ],
    ) );
  }
  */

  /*
  * REST: Get Topics
  */
  /*
  public function rest_get_topics() {
    if ( ! isset( $_GET['key'] ) || ! $this->isRequestValid( $_GET['key'] ) ) {
      wp_send_json_error( [ 'Invalid Request' ] ); wp_die();
    }

    global $wpdb;

    $lists_query = "SELECT id, title, slug, image_url, frequency, description FROM {$wpdb->prefix}observer_lists WHERE status = 'active' ORDER BY sub_count DESC";
    $lists = $wpdb->get_results( $lists_query );

    $my_sub_lists = [];
    if( isset( $_GET['email'] ) ) {
      $user = get_user_by( 'email', sanitize_text_field( $_GET['email' ] ) );
      if ( $user ) {
        $my_subs = $wpdb->get_results( "SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user->ID}' AND status = 'subscribed' " );
        $my_sub_lists = wp_list_pluck( $my_subs, 'list_id' );
      }
    }

    $return = [];

    foreach( $lists as $list ) {
      $return[] = [
        'id' => $list->id,
        'title' => $list->title,
        'link' => home_url( '/observer/' . $list->slug . '/' ),
        'image_url' => $list->image_url,
        'description' => $list->description,
        'subscribed' => in_array( $list->id, $my_sub_lists ),
        'frequency' => $list->frequency,
      ];
    }

    wp_send_json_success( $return );
  } // rest_get_topics() }}
  */

  /*
  * REST: Sub/Unsub
  */
  /*
  public function rest_sub_unsub($request_data) {
    $formData = $request_data->get_params();

    if ( isset( $formData['email'] ) && isset( $formData['status'] ) && isset( $formData['list'] ) ) {
      $user = get_user_by( 'email', $formData['email'] );
      if ( $user ) {

        global $wpdb;

        $user_id = $user->ID;

        $check_sub = $wpdb->get_row( "SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '{$formData['list']}' LIMIT 1" );
        if ( $check_sub ) {
          $status = isset( $formData['status'] ) ? $formData['status'] : 'subscribed';

          $update_data = [
            'status' => $status, // 'subscribed',
            'status_mailchimp' => NULL,
          ];

          if ( 'subscribed' == $status )
            $update_data['subscribed_at'] = current_time( 'mysql' );

          if ( 'unsubscribed' == $status )
            $update_data['unsubscribed_at'] = current_time( 'mysql' );

          $wpdb->update( $wpdb->prefix . 'observer_subs', $update_data,
            [
              'id' => $check_sub->id,
            ]
          );
        } else {
          $wpdb->insert( $wpdb->prefix . 'observer_subs',
            [
              'user_id' => $user_id,
              'list_id' => $formData['list'],
              'status' => 'subscribed',
              'status_mailchimp' => NULL,
              'subscribed_at' => current_time( 'mysql' ),
            ]
          );
        }

        // return wp_send_json_success(); wp_die();
      }
    }
    wp_send_json_error( [ 'error' => [ 'message' => 'Whoops, like something unexpected happened on our side of things. Feel free to refresh your browser and give it another shot!' ] ] ); wp_die();
  }
  */

  /*
  * REST: Validate Key for API
  */
  /*
  protected function isRequestValid( $key ) {
    var_dump( $this->rest_api_keys ); exit;
    return isset( $key ) && ! is_null ( $key ) && in_array( $key, $this->rest_api_keys );
  }
  */

  /*
  * Get Countries
  */
  public static function getCountries()
  {
    return array(
      "AU" => "Australia",
      "NZ" => "New Zealand",
      "GB" => "United Kingdom",
      "US" => "United States",
      "CA" => "Canada",

      "0" => "──────────",

      "AF" => "Afghanistan",
      "AL" => "Albania",
      "DZ" => "Algeria",
      "AS" => "American Samoa",
      "AD" => "Andorra",
      "AO" => "Angola",
      "AI" => "Anguilla",
      "AQ" => "Antarctica",
      "AG" => "Antigua and Barbuda",
      "AR" => "Argentina",
      "AM" => "Armenia",
      "AW" => "Aruba",

      "AT" => "Austria",
      "AZ" => "Azerbaijan",
      "BS" => "Bahamas",
      "BH" => "Bahrain",
      "BD" => "Bangladesh",
      "BB" => "Barbados",
      "BY" => "Belarus",
      "BE" => "Belgium",
      "BZ" => "Belize",
      "BJ" => "Benin",
      "BM" => "Bermuda",
      "BT" => "Bhutan",
      "BO" => "Bolivia",
      "BA" => "Bosnia and Herzegovina",
      "BW" => "Botswana",
      "BV" => "Bouvet Island",
      "BR" => "Brazil",
      "BQ" => "British Antarctic Territory",
      "IO" => "British Indian Ocean Territory",
      "VG" => "British Virgin Islands",
      "BN" => "Brunei",
      "BG" => "Bulgaria",
      "BF" => "Burkina Faso",
      "BI" => "Burundi",
      "KH" => "Cambodia",
      "CM" => "Cameroon",

      "CT" => "Canton and Enderbury Islands",
      "CV" => "Cape Verde",
      "KY" => "Cayman Islands",
      "CF" => "Central African Republic",
      "TD" => "Chad",
      "CL" => "Chile",
      "CN" => "China",
      "CX" => "Christmas Island",
      "CC" => "Cocos [Keeling] Islands",
      "CO" => "Colombia",
      "KM" => "Comoros",
      "CG" => "Congo - Brazzaville",
      "CD" => "Congo - Kinshasa",
      "CK" => "Cook Islands",
      "CR" => "Costa Rica",
      "HR" => "Croatia",
      "CU" => "Cuba",
      "CY" => "Cyprus",
      "CZ" => "Czech Republic",
      "CI" => "Côte d’Ivoire",
      "DK" => "Denmark",
      "DJ" => "Djibouti",
      "DM" => "Dominica",
      "DO" => "Dominican Republic",
      "NQ" => "Dronning Maud Land",
      "DD" => "East Germany",
      "EC" => "Ecuador",
      "EG" => "Egypt",
      "SV" => "El Salvador",
      "GQ" => "Equatorial Guinea",
      "ER" => "Eritrea",
      "EE" => "Estonia",
      "ET" => "Ethiopia",
      "FK" => "Falkland Islands",
      "FO" => "Faroe Islands",
      "FJ" => "Fiji",
      "FI" => "Finland",
      "FR" => "France",
      "GF" => "French Guiana",
      "PF" => "French Polynesia",
      "TF" => "French Southern Territories",
      "FQ" => "French Southern and Antarctic Territories",
      "GA" => "Gabon",
      "GM" => "Gambia",
      "GE" => "Georgia",
      "DE" => "Germany",
      "GH" => "Ghana",
      "GI" => "Gibraltar",
      "GR" => "Greece",
      "GL" => "Greenland",
      "GD" => "Grenada",
      "GP" => "Guadeloupe",
      "GU" => "Guam",
      "GT" => "Guatemala",
      "GG" => "Guernsey",
      "GN" => "Guinea",
      "GW" => "Guinea-Bissau",
      "GY" => "Guyana",
      "HT" => "Haiti",
      "HM" => "Heard Island and McDonald Islands",
      "HN" => "Honduras",
      "HK" => "Hong Kong SAR China",
      "HU" => "Hungary",
      "IS" => "Iceland",
      "IN" => "India",
      "ID" => "Indonesia",
      "IR" => "Iran",
      "IQ" => "Iraq",
      "IE" => "Ireland",
      "IM" => "Isle of Man",
      "IL" => "Israel",
      "IT" => "Italy",
      "JM" => "Jamaica",
      "JP" => "Japan",
      "JE" => "Jersey",
      "JT" => "Johnston Island",
      "JO" => "Jordan",
      "KZ" => "Kazakhstan",
      "KE" => "Kenya",
      "KI" => "Kiribati",
      "KW" => "Kuwait",
      "KG" => "Kyrgyzstan",
      "LA" => "Laos",
      "LV" => "Latvia",
      "LB" => "Lebanon",
      "LS" => "Lesotho",
      "LR" => "Liberia",
      "LY" => "Libya",
      "LI" => "Liechtenstein",
      "LT" => "Lithuania",
      "LU" => "Luxembourg",
      "MO" => "Macau SAR China",
      "MK" => "Macedonia",
      "MG" => "Madagascar",
      "MW" => "Malawi",
      "MY" => "Malaysia",
      "MV" => "Maldives",
      "ML" => "Mali",
      "MT" => "Malta",
      "MH" => "Marshall Islands",
      "MQ" => "Martinique",
      "MR" => "Mauritania",
      "MU" => "Mauritius",
      "YT" => "Mayotte",
      "FX" => "Metropolitan France",
      "MX" => "Mexico",
      "FM" => "Micronesia",
      "MI" => "Midway Islands",
      "MD" => "Moldova",
      "MC" => "Monaco",
      "MN" => "Mongolia",
      "ME" => "Montenegro",
      "MS" => "Montserrat",
      "MA" => "Morocco",
      "MZ" => "Mozambique",
      "MM" => "Myanmar [Burma]",
      "NA" => "Namibia",
      "NR" => "Nauru",
      "NP" => "Nepal",
      "NL" => "Netherlands",
      "AN" => "Netherlands Antilles",
      "NT" => "Neutral Zone",
      "NC" => "New Caledonia",

      "NI" => "Nicaragua",
      "NE" => "Niger",
      "NG" => "Nigeria",
      "NU" => "Niue",
      "NF" => "Norfolk Island",
      "KP" => "North Korea",
      "VD" => "North Vietnam",
      "MP" => "Northern Mariana Islands",
      "NO" => "Norway",
      "OM" => "Oman",
      "PC" => "Pacific Islands Trust Territory",
      "PK" => "Pakistan",
      "PW" => "Palau",
      "PS" => "Palestinian Territories",
      "PA" => "Panama",
      "PZ" => "Panama Canal Zone",
      "PG" => "Papua New Guinea",
      "PY" => "Paraguay",
      "YD" => "People's Democratic Republic of Yemen",
      "PE" => "Peru",
      "PH" => "Philippines",
      "PN" => "Pitcairn Islands",
      "PL" => "Poland",
      "PT" => "Portugal",
      "PR" => "Puerto Rico",
      "QA" => "Qatar",
      "RO" => "Romania",
      "RU" => "Russia",
      "RW" => "Rwanda",
      "RE" => "Réunion",
      "BL" => "Saint Barthélemy",
      "SH" => "Saint Helena",
      "KN" => "Saint Kitts and Nevis",
      "LC" => "Saint Lucia",
      "MF" => "Saint Martin",
      "PM" => "Saint Pierre and Miquelon",
      "VC" => "Saint Vincent and the Grenadines",
      "WS" => "Samoa",
      "SM" => "San Marino",
      "SA" => "Saudi Arabia",
      "SN" => "Senegal",
      "RS" => "Serbia",
      "CS" => "Serbia and Montenegro",
      "SC" => "Seychelles",
      "SL" => "Sierra Leone",
      "SG" => "Singapore",
      "SK" => "Slovakia",
      "SI" => "Slovenia",
      "SB" => "Solomon Islands",
      "SO" => "Somalia",
      "ZA" => "South Africa",
      "GS" => "South Georgia and the South Sandwich Islands",
      "KR" => "South Korea",
      "ES" => "Spain",
      "LK" => "Sri Lanka",
      "SD" => "Sudan",
      "SR" => "Suriname",
      "SJ" => "Svalbard and Jan Mayen",
      "SZ" => "Swaziland",
      "SE" => "Sweden",
      "CH" => "Switzerland",
      "SY" => "Syria",
      "ST" => "São Tomé and Príncipe",
      "TW" => "Taiwan",
      "TJ" => "Tajikistan",
      "TZ" => "Tanzania",
      "TH" => "Thailand",
      "TL" => "Timor-Leste",
      "TG" => "Togo",
      "TK" => "Tokelau",
      "TO" => "Tonga",
      "TT" => "Trinidad and Tobago",
      "TN" => "Tunisia",
      "TR" => "Turkey",
      "TM" => "Turkmenistan",
      "TC" => "Turks and Caicos Islands",
      "TV" => "Tuvalu",
      "UM" => "U.S. Minor Outlying Islands",
      "PU" => "U.S. Miscellaneous Pacific Islands",
      "VI" => "U.S. Virgin Islands",
      "UG" => "Uganda",
      "UA" => "Ukraine",
      "SU" => "Union of Soviet Socialist Republics",
      "AE" => "United Arab Emirates",


      "ZZ" => "Unknown or Invalid Region",
      "UY" => "Uruguay",
      "UZ" => "Uzbekistan",
      "VU" => "Vanuatu",
      "VA" => "Vatican City",
      "VE" => "Venezuela",
      "VN" => "Vietnam",
      "WK" => "Wake Island",
      "WF" => "Wallis and Futuna",
      "EH" => "Western Sahara",
      "YE" => "Yemen",
      "ZM" => "Zambia",
      "ZW" => "Zimbabwe",
      "AX" => "Åland Islands",
    );
  }

  /*
  * Get Observer topics
  */
  public function get_observer_topics($topic_id = null)
  {
    global $wpdb;

    if (!is_null($topic_id)) {
      $id = absint($topic_id);
      return $wpdb->get_row("SELECT id, title, slug, image_url, frequency, description FROM {$wpdb->prefix}observer_lists WHERE id = '{$topic_id}'");
    }

    $lists_query = "SELECT id, title, slug, image_url, frequency, description FROM {$wpdb->prefix}observer_lists WHERE status = 'active' ORDER BY sub_count DESC";

    $lists = $wpdb->get_results($lists_query);

    $return = [];

    foreach ($lists as $list) {
      $return[] = [
        'id' => $list->id,
        'title' => $list->title,
        'link' => home_url('/observer/' . $list->slug . '/'),
        'image_url' => $list->image_url,
        'description' => $list->description,
        'frequency' => $list->frequency,
      ];
    }

    return $return;
  }
}

new BragObserver();
