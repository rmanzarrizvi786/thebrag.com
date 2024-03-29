<?php

use AmpProject\Validator\Spec\Tag\P;

class Cron // extends BragObserver
{
    protected $plugin_name;
    protected $plugin_slug;

    protected static $config;

    protected $mailchimp_list_id;
    protected $mailchimp_interest_category_id;
    protected $mailchimp_api_key;
    protected $MailChimp;

    public function __construct()
    {
        $this->plugin_name = 'brag_observer';
        $this->plugin_slug = 'brag-observer';

        // Admin menu
        add_action('admin_menu', [$this, '_admin_menu']);

        add_action('cron_hook_observer_mailchimp', [$this, 'exec_cron_observer_mailchimp']);
        add_action('cron_hook_observer_braze_update_newsletter_interests', [$this, 'exec_cron_observer_braze_update_newsletter_interests']);
        add_action('cron_hook_observer_braze_update_profile', [$this, 'exec_cron_observer_braze_update_profile']);
        add_action('cron_hook_observer_sync_with_auth0', [$this, 'exec_cron_observer_sync_with_auth0']);

        add_filter('cron_schedules', [$this, '_cron_schedules']);

        // AJAX
        add_action('wp_ajax_brag_observer_process_braze', [$this, 'ajax_process_braze']);

        // Config
        self::$config = include __DIR__ . '/config.php';

        $this->mailchimp_list_id = '5f6dd9c238';
        $this->mailchimp_interest_category_id = 'b87c163ce8';
        $this->mailchimp_api_key = 'e5ad9623c8961a991f8737c3cc950c55-us1';

        require_once __DIR__ . '/MailChimp.php';
        $this->MailChimp = new MailChimp($this->mailchimp_api_key);
    }

    public function _admin_menu()
    {
        /* {{ Admins only */
        add_submenu_page(
            $this->plugin_slug,
            'Process cron',
            'Process cron',
            'administrator',
            $this->plugin_slug . '-process-cron',
            array($this, 'process_cron')
        );
        add_submenu_page(
            $this->plugin_slug,
            'Process Braze cron',
            'Process Braze cron',
            'administrator',
            $this->plugin_slug . '-process-braze-cron',
            array($this, 'show_process_braze')
        );
        /* }} Admins only */
    }

    /**
     * Hook cron_schedules
     *
     * @return void
     * @param void
     */
    public function _cron_schedules($schedules)
    {
        $schedules['every40minutes'] = array(
            'interval' => 40 * 60,
            'display'  => esc_html__('Every 40 Minutes'),
        );
        $schedules['every30minutes'] = array(
            'interval' => 30 * 60,
            'display'  => esc_html__('Every 30 Minutes'),
        );
        $schedules['every20minutes'] = array(
            'interval' => 20 * 60,
            'display'  => esc_html__('Every 20 Minutes'),
        );
        $schedules['every10minutes'] = array(
            'interval' => 10 * 60,
            'display'  => esc_html__('Every 10 Minutes'),
        );
        $schedules['every5minutes'] = array(
            'interval' => 5 * 60,
            'display'  => esc_html__('Every 5 Minutes'),
        );
        $schedules['every2minutes'] = array(
            'interval' => 2 * 60,
            'display'  => esc_html__('Every 2 Minutes'),
        );
        $schedules['everyminute'] = array(
            'interval' => 1 * 60,
            'display'  => esc_html__('Every Minute'),
        );
        return $schedules;
    }

    /**
     * Process CRON
     */
    public function process_cron()
    {
        date_default_timezone_set('Australia/NSW');
        $next_run_timestamp = wp_next_scheduled('cron_hook_observer_mailchimp', array(NULL, NULL));
        echo '<br>Scheduled automatic run is at ' . date('d-M-Y h:i:sa', $next_run_timestamp);
        echo '<br>Current Date/Time: ' . date('d-M-Y h:i:sa');

        $this->exec_cron_observer_mailchimp();
    }

    public function exec_cron_observer_mailchimp()
    {
        update_option('BragObserver_CronStart', date('Y-m-d H:i:s'), false);

        global $wpdb;

        require_once __DIR__ . '/email.class.php';
        $email = new Email($this);

        /**
         * Process MailChimp subs and unsubs
         * + Send welcome emails
         */
        $query_subs = " SELECT
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
            WHERE
            ( s.status != s.status_mailchimp OR s.status_mailchimp IS NULL)
            AND l.interest_id != ''
            ORDER BY
            s.unsubscribed_at DESC
            LIMIT 100
        ";
        $subs = $wpdb->get_results($query_subs);

        $sub_users = [];

        if ($subs) {
            foreach ($subs as $sub) {
                $sub_users[$sub->user_email]['user_id'] = $sub->user_id;
                if ($sub->status == 'subscribed') {
                    $sub_users[$sub->user_email]['sub_lists'][$sub->id] = $sub->list_title;
                } else {
                    $sub_users[$sub->user_email]['unsub_lists'][$sub->id] = $sub->list_title;
                }
                $sub_users[$sub->user_email]['interests'][$sub->interest_id] = $sub->status == 'subscribed' ? true : false;
            } // For Each $sub
        } // If $subs

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
                /**
                 * Update MailChimp Status in DB
                 */
                if (isset($sub_user['sub_lists']) && count($sub_user['sub_lists']) > 0) {
                    $wpdb->query(
                        " UPDATE
                        {$wpdb->prefix}observer_subs
                        SET `status_mailchimp` =  'subscribed', `mc_subscribed_at` = '" . current_time('mysql') . "'
                        WHERE id IN ( " . implode(',', array_keys($sub_user['sub_lists'])) . ")"
                    );

                    // Send consolidated Welcome email
                    // if (!get_user_meta($sub_user['user_id'], 'no_welcome_email', true)) {
                    // $email->sendSubscribeConfirmationEmail( $sub_user['user_id'], $sub_user['sub_lists'] );
                    // }
                } // If there are $sub_user['sub_lists']

                if (isset($sub_user['unsub_lists']) && count($sub_user['unsub_lists']) > 0) {
                    $wpdb->query(
                        " UPDATE
                        {$wpdb->prefix}observer_subs
                        SET `status_mailchimp` =  'unsubscribed', `mc_unsubscribed_at` = '" . current_time('mysql') . "'
                        WHERE id IN ( " . implode(',', array_keys($sub_user['unsub_lists'])) . ")"
                    );
                } // If there are $sub_user['unsub_lists']
            } // For Each $sub_user
        } // IF $sub_users is NOT empty

        /**
         * Push Tastemakers to MC
         */
        $tastemakers_query = " SELECT
            t.title,
            u.ID user_id,
            u.user_email
        FROM {$wpdb->prefix}observer_tastemakers t
            JOIN {$wpdb->prefix}observer_tastemaker_reviews tr
                ON t.id = tr.tastemaker_id
            JOIN {$wpdb->prefix}users u
                ON tr.user_id = u.ID
            WHERE
            tr.status = 'verified' AND tr.status_mailchimp IS NULL
        LIMIT 75
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

        /**
         * Push Lead Generators (Comps) to MC
         */
        $comps_query = " SELECT
            l.`title`, l.`list_id`, u.`ID` user_id, u.`user_email`
        FROM `{$wpdb->prefix}observer_lead_generators` l
            JOIN `{$wpdb->prefix}observer_lead_generator_responses` lr
                ON l.`id` = lr.`lead_generator_id`
            JOIN `{$wpdb->prefix}users` u
                ON lr.`user_id` = u.`ID`
        WHERE
            lr.`status` = 'verified'
            AND lr.`status_mailchimp` IS NULL
            AND 
            (
                `lr`.`last_attempt` IS NULL
                OR (
                    DATE(`lr`.`last_attempt`) <= '" . date('Y-m-d', strtotime('-1 day')) . "' AND
                    DATE(`lr`.`last_attempt`) > '" . date('Y-m-d', strtotime('-3 day')) . "'
                )
            )
        ORDER BY lr.created_at DESC
        LIMIT 75
            ";

        $lead_generator = new LeadGenerator();

        $comp_entries = $wpdb->get_results($comps_query);
        if ($comp_entries) {
            $comp_subs = [];
            foreach ($comp_entries as $entry) {

                $lists = explode(',', $entry->list_id);
                foreach ($lists as $list_id) {

                    $check_sub = $wpdb->get_row("SELECT id, status FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$entry->user_id}' AND list_id = '{$list_id}' LIMIT 1");

                    if (!$check_sub) {
                        $lead_generator->subscribe($entry->user_id, $list_id);
                    }
                }

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
                                'last_attempt' => current_time('mysql'),
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
                            'last_attempt' => current_time('mysql'),
                        ],
                        [
                            'user_id' => $user_id,
                            'status_mailchimp' => NULL,
                        ]
                    );
                }
            }
        }


        /**
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

        /**
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
    } // exec_cron_observer_mailchimp()


    /**
     * Process Braze CRON
     */
    public function addToBrazeQueue($user_id, $task, $task_values = null)
    {
        global $wpdb;
        if (!$user_id || !$task) {
            return false;
        }
        $data = [
            'user_id' => absint($user_id),
            'task' => trim($task),
            'queued_at' => current_time('mysql')
        ];
        $format = ['%d', '%s', '%s'];
        if (!is_null($task_values)) {
            $data['task_values'] = json_encode($task_values);
            $format[] = '%s';
        }
        $wpdb->insert(
            $wpdb->prefix . 'observer_braze_cron',
            $data,
            $format
        );
    }

    public function getActiveBrazeQueueTask($user_id, $task = null)
    {
        global $wpdb;
        $query = " SELECT * FROM {$wpdb->prefix}observer_braze_cron WHERE `user_id` = '{$user_id}' AND `completed_at` IS NULL ";
        if (!is_null($task)) {
            return $wpdb->get_row($query . " AND `task` = '{$task}' LIMIT 1 ");
        }
        return $wpdb->get_results($query);
    }

    public function ajax_process_braze()
    {
        $this->exec_cron_observer_braze_update_newsletter_interests();
        wp_send_json_success();
        die();
    }

    public function show_process_braze()
    {
        date_default_timezone_set('Australia/NSW');

        if (isset($_GET['manual']) && 1 == trim($_GET['manual'])) {
?>
            <div style="margin-top: 2rem;">
                <button id="brag-observer-process-braze" class="button button-primary">Process</button>
            </div>
            <script>
                jQuery(document).ready(function($) {
                    var counterBrazeProcess = 0;
                    var bragObserverProcessingBraze = false;
                    $('#brag-observer-process-braze').on('click', function() {
                        $(this).prop('disabled', true).text('Processing...');
                        processBraze();
                    });

                    function processBraze() {
                        counterBrazeProcess++;
                        if (!bragObserverProcessingBraze) {
                            bragObserverProcessingBraze = true;
                            $.post(
                                '<?php echo admin_url('admin-ajax.php'); ?>', {
                                    action: 'brag_observer_process_braze',
                                },
                                function(res) {
                                    if (res.success) {
                                        console.log('counterBrazeProcess', counterBrazeProcess);
                                        bragObserverProcessingBraze = false;
                                        processBraze();
                                    } else {
                                        alert(res.data);
                                        $('#brag-observer-process-braze').prop('disabled', false).text('Process');
                                    }
                                }
                            )
                        }
                    }
                });
            </script>
<?php
        } else {
            echo '<h2>Current Date/Time: ' . date('d-M-Y h:i:sa') . '</h2>';

            echo '<hr>';

            $next_run_timestamp = wp_next_scheduled('cron_hook_observer_braze_update_newsletter_interests', array(NULL, NULL));
            echo '<h2>update_newsletter_interests</h2>Scheduled automatic run is at ' . date('d-M-Y h:i:sa', $next_run_timestamp);
            $this->exec_cron_observer_braze_update_newsletter_interests();

            echo '<hr>';

            $next_run_timestamp = wp_next_scheduled('cron_hook_observer_braze_update_profile', array(NULL, NULL));
            echo '<h2>update_profile</h2>Scheduled automatic run is at ' . date('d-M-Y h:i:sa', $next_run_timestamp);
            $this->exec_cron_observer_braze_update_profile();
        }
    }

    public function exec_cron_observer_braze_update_profile()
    {
        global $wpdb;

        require_once __DIR__ . '/braze.class.php';
        $braze = new Braze();
        $braze->setMethod('POST');

        $attributes = [];
        $task_ids = [];

        $query_tasks = " SELECT c.`id`, c.`user_id`, c.`task_values`
            FROM {$wpdb->prefix}observer_braze_cron c
            WHERE c.`task` = 'update_profile' AND c.`completed_at` IS NULL
            ORDER BY c.`queued_at`
            LIMIT 30
            ";
        $tasks = $wpdb->get_results($query_tasks);

        if ($tasks) {
            $task_ids = wp_list_pluck($tasks, 'id');
            foreach ($tasks as $task) {
                $user_attributes = [];

                $user = get_user_by('ID', $task->user_id);

                if (!$user) {
                    $wpdb->update(
                        $wpdb->prefix . 'observer_braze_cron',
                        [
                            'completed_at' => current_time('mysql'),
                            'comments' => 'User not found',
                        ],
                        [
                            'id' => $task->id,
                        ]
                    );
                    continue;
                }

                /**
                 * Set user's Auth0 ID as external ID (if set)
                 * OR
                 * Set user_alias
                 */
                $braze_user_found = false;
                $braze->setPayload([
                    'email_address' => $user->user_email
                ]);
                $res_user = $braze->request('/users/export/ids');

                if (201 == $res_user['code']) {
                    $braze_users = json_decode($res_user['response']);

                    if (isset($braze_users->users[0])) {
                        $braze_user = $braze_users->users[0];

                        if (isset($braze_user->external_id)) {
                            $user_attributes['external_id'] = $braze_user->external_id;
                        } else {
                            $user_attributes['user_alias'] = $braze_user->user_aliases[0];
                            $user_attributes['_update_existing_only'] = false;
                        }

                        $braze_user_found = true;
                    }
                }

                if (!$braze_user_found) {
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
                }

                if (1 != get_user_meta($user->ID, 'created_braze_user')) {
                    $user_attributes['email'] = $user->user_email;
                    if (!get_user_meta($user->ID, 'oc_token', true)) :
                        $oc_token = md5($user->ID . time()); // creates md5 code to verify later
                        update_user_meta($user->ID, 'oc_token', $oc_token);
                    endif;

                    $unserialized_oc_token = [
                        'id' => $user->ID,
                        'oc_token' => get_user_meta($user->ID, 'oc_token', true),
                    ]; // makes it into a code to send it to user via email

                    $user_attributes['observer_token'] = base64_encode(serialize($unserialized_oc_token));
                }

                /**
                 * Set 'manual_import' custom attribute if no_welcome_email or manual_import usermeta is set
                 */
                if (
                    get_user_meta($user->ID, 'no_welcome_email', true) ||
                    get_user_meta($user->ID, 'manual_import', true)
                ) {
                    $user_attributes['manual_import'] = true;
                }

                /**
                 * Set 'profile_completion' custom attribute
                 */
                if (get_user_meta($user->ID, 'profile_strength', true)) {
                    $user_attributes['profile_completion_%'] = get_user_meta($user->ID, 'profile_strength', true);
                }

                $user_attributes = array_merge($user_attributes, (array)json_decode($task->task_values));

                if (!empty($user_attributes)) {
                    $attributes[] = $user_attributes;
                }
            }
        }

        if (!empty($attributes)) {
            /**
             * to Braze
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
                // echo '<pre>' . print_r($attributes, true) . '</pre>';
                // echo '<pre>' . print_r($res_track, true) . '</pre>';
                $wpdb->query("UPDATE {$wpdb->prefix}observer_braze_cron SET `completed_at` = '" . current_time('mysql') . "' WHERE `id` IN (" . implode(',', $task_ids) . ")");
            } else {
                wp_mail('sachin.patel@thebrag.media', 'Braze error', 'Line: ' . __LINE__  . "\n\r Method: " . __METHOD__ . "\n\r " . print_r($res_track, true));
            }
        }
    }

    public function exec_cron_observer_sync_with_auth0()
    {
        global $wpdb;
        $task_ids = [];

        $query_tasks = " SELECT c.`id`, c.`user_id`
            FROM {$wpdb->prefix}observer_braze_cron c
            WHERE c.`task` = 'sync_with_auth0' AND c.`completed_at` IS NULL
            ORDER BY c.`queued_at`
            LIMIT 10
            ";
        $tasks = $wpdb->get_results($query_tasks);

        if ($tasks) {
            foreach ($tasks as $task) {
                require get_template_directory() . '/vendor/autoload.php';

                $dotenv = Dotenv\Dotenv::createImmutable(ABSPATH);
                $dotenv->load();

                $auth0_api = new Auth0\SDK\API\Authentication(
                    AUTH0_DOMAIN,
                    AUTH0_CLIENT_ID
                );

                $config = [
                    'client_secret' => AUTH0_CLIENT_SECRET,
                    'client_id' => AUTH0_CLIENT_ID,
                    'audience' => AUTH0_MANAGEMENT_AUDIENCE,
                ];

                try {
                    $result = $auth0_api->client_credentials($config);
                    $access_token = $result['access_token'];
                } catch (Exception $e) {
                    // die($e->getMessage());
                }

                if (isset($access_token)) {
                    // Instantiate the base Auth0 class.
                    $auth0 = new Auth0\SDK\Auth0([
                        // The values below are found on the Application settings tab.
                        'domain' => AUTH0_DOMAIN,
                        'client_id' => AUTH0_CLIENT_ID,
                        'client_secret' => AUTH0_CLIENT_SECRET,
                        'redirect_uri' => AUTH0_REDIRECT_URI,
                    ]);

                    $mgmt_api = new Auth0\SDK\API\Management($access_token, AUTH0_DOMAIN);
                    try {
                        if ($wp_auth0_id = get_user_meta($task->user_id, 'wp_auth0_id', true)) {
                            $auth0_user = $mgmt_api->users()->get($wp_auth0_id);
                        } elseif ($wp_auth0_id = get_user_meta($task->user_id, $wpdb->prefix . 'auth0_id', true)) {
                            $auth0_user = $mgmt_api->users()->get($wp_auth0_id);
                        }
                        // $auth0_user = $mgmt_api->users()->get($data['auth0_id']);
                    } catch (Exception $e) {
                        // die($e->getMessage());
                    }
                }

                $first_name = !is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata']['first_name'])
                    ?
                    $auth0_user['user_metadata']['first_name']
                    :
                    get_user_meta($task->user_id, 'first_name', true);

                $last_name = !is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata']['last_name'])
                    ?
                    $auth0_user['user_metadata']['last_name']
                    :
                    get_user_meta($task->user_id, 'last_name', true);

                $state = !is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata']['state'])
                    ?
                    $auth0_user['user_metadata']['state']
                    : get_user_meta($task->user_id, 'state', true);

                $birthday = !is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata']['birthday'])
                    ?
                    $auth0_user['user_metadata']['birthday']
                    : get_user_meta($task->user_id, 'birthday', true);

                $gender = !is_null($auth0_user) && isset($auth0_user['user_metadata']) && isset($auth0_user['user_metadata']['gender'])
                    ?
                    $auth0_user['user_metadata']['gender']
                    : get_user_meta($task->user_id, 'gender', true);

                $user_data = [
                    'ID' => $task->user_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'display_name' => $first_name . ' ' . $last_name,
                ];

                /**
                 * Queue to update in Braze
                 */
                $braze_updates = [];
                if (get_user_meta($task->user_id, 'first_name', true) != $user_data['first_name']) {
                    $braze_updates['first_name'] = $user_data['first_name'];
                }

                if (get_user_meta($task->user_id, 'last_name', true) != $user_data['last_name']) {
                    $braze_updates['last_name'] = $user_data['last_name'];
                }

                if (!get_user_meta($task->user_id, 'birthday') || get_user_meta($task->user_id, 'birthday', true) != $birthday) {
                    $braze_updates['birthday'] = $birthday;
                }

                if (!get_user_meta($task->user_id, 'state') || get_user_meta($task->user_id, 'state', true) != trim($state)) {
                    $braze_updates['state'] = trim($state);
                }

                if (!get_user_meta($task->user_id, 'gender') || get_user_meta($task->user_id, 'gender', true) != trim($gender)) {
                    $braze_updates['gender'] = trim($gender);
                }

                wp_update_user($user_data);

                update_user_meta($task->user_id, 'birthday', $birthday);
                update_user_meta($task->user_id, 'state', $state);
                update_user_meta($task->user_id, 'gender', $gender);

                if (!empty($braze_updates)) {
                    $task_name = 'update_profile';
                    if (!$this->getActiveBrazeQueueTask($task->user_id, $task_name)) {
                        $this->addToBrazeQueue($task->user_id, $task_name, $braze_updates);
                    }
                }

                $wpdb->query("UPDATE {$wpdb->prefix}observer_braze_cron SET `completed_at` = '" . current_time('mysql') . "' WHERE `id` = '{$task->id}'");
            }
        }
    }

    /**
     * Update Newsletter interests in Braze
     */
    public function exec_cron_observer_braze_update_newsletter_interests()
    {
        global $wpdb;

        require_once __DIR__ . '/braze.class.php';
        $braze = new Braze();
        $braze->setMethod('POST');

        $attributes = [];
        $task_ids = [];
        $user_ids = [];

        $query_tasks = " SELECT c.`id`, c.`user_id`
            FROM {$wpdb->prefix}observer_braze_cron c
            WHERE c.`task` = 'update_newsletter_interests' AND c.`completed_at` IS NULL
            ORDER BY c.`queued_at`
            LIMIT 50
            ";
        $tasks = $wpdb->get_results($query_tasks);

        if ($tasks) {
            $task_ids = wp_list_pluck($tasks, 'id');
            foreach ($tasks as $task) {
                $user_attributes = [];

                $user = get_user_by('ID', $task->user_id);

                if (!$user) {
                    $wpdb->update(
                        $wpdb->prefix . 'observer_braze_cron',
                        [
                            'completed_at' => current_time('mysql'),
                            'comments' => 'User not found',
                        ],
                        [
                            'id' => $task->id,
                        ]
                    );
                    continue;
                }

                $user_ids[] = $user->ID;

                /**
                 * Set user's Auth0 ID as external ID (if set)
                 * OR
                 * Set user_alias
                 */
                $braze_user_found = false;
                $braze->setPayload([
                    'email_address' => $user->user_email
                ]);
                $res_user = $braze->request('/users/export/ids');

                if (201 == $res_user['code']) {
                    $braze_users = json_decode($res_user['response']);

                    if (isset($braze_users->users[0])) {
                        $braze_user = $braze_users->users[0];

                        if (isset($braze_user->external_id)) {
                            $user_attributes['external_id'] = $braze_user->external_id;
                        } else {
                            $user_attributes['user_alias'] = $braze_user->user_aliases[0];
                            $user_attributes['_update_existing_only'] = false;
                        }

                        $braze_user_found = true;
                    }
                }

                if (!$braze_user_found) {
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
                }

                if (1 != get_user_meta($user->ID, 'created_braze_user')) {
                    $user_attributes['email'] = $user->user_email;
                    if (!get_user_meta($user->ID, 'oc_token', true)) :
                        $oc_token = md5($user->ID . time()); // creates md5 code to verify later
                        update_user_meta($user->ID, 'oc_token', $oc_token);
                    endif;

                    $unserialized_oc_token = [
                        'id' => $user->ID,
                        'oc_token' => get_user_meta($user->ID, 'oc_token', true),
                    ]; // makes it into a code to send it to user via email

                    $user_attributes['observer_token'] = base64_encode(serialize($unserialized_oc_token));
                }

                $query_subs = " SELECT
                        l.slug
                    FROM {$wpdb->prefix}observer_subs s
                        JOIN {$wpdb->prefix}observer_lists l
                            ON s.list_id = l.id
                    WHERE
                        s.`status` = 'subscribed'
                        AND
                        s.`user_id` = '{$user->ID}'
                    ";
                $subs = $wpdb->get_results($query_subs);


                if ($subs) {
                    $user_attributes['newsletter_interests'] = wp_list_pluck($subs, 'slug');
                } else {
                    $user_attributes['newsletter_interests'] = [];
                }

                /**
                 * Set `imported_from` custom attribute if set
                 */
                if (get_user_meta($user->ID, 'imported_from', true)) {
                    $arr_imported_from = [];
                    if ($braze_user_found && isset($braze_user->custom_attributes->imported_from)) {
                        if (is_array($braze_user->custom_attributes->imported_from)) {
                            $arr_imported_from = array_merge($braze_user->custom_attributes->imported_from, [get_user_meta($user->ID, 'imported_from', true)]);
                        } else {
                            $arr_imported_from = [
                                $braze_user->custom_attributes->imported_from,
                                get_user_meta($user->ID, 'imported_from', true)
                            ];
                        }
                    } else {
                        $arr_imported_from = [get_user_meta($user->ID, 'imported_from', true)];
                    }
                    $user_attributes['imported_from'] = $arr_imported_from;
                }

                /**
                 * Set 'manual_import' custom attribute if no_welcome_email or manual_import usermeta is set
                 */
                if (
                    get_user_meta($user->ID, 'no_welcome_email', true) ||
                    get_user_meta($user->ID, 'manual_import', true)
                ) {
                    $user_attributes['manual_import'] = true;
                }

                if (!empty($user_attributes)) {
                    $attributes[] = $user_attributes;
                }
            }
        }

        if (!empty($attributes)) {
            /**
             * to Braze
             */
            $braze->setPayload(
                [
                    'attributes' => $attributes
                ]
            );
            $res_track = $braze->request('/users/track', true);
            if (201 === $res_track['code']) {

                $wpdb->query("UPDATE {$wpdb->prefix}observer_braze_cron SET `completed_at` = '" . current_time('mysql') . "' WHERE `id` IN (" . implode(',', $task_ids) . ")");

                /**
                 * Delete user meta, as it doesn't need to be processed again unless set again
                 */
                foreach ($user_ids as $user_id)
                    delete_user_meta($user_id, 'imported_from');
            } else {
                wp_mail('sachin.patel@thebrag.media', 'Braze error', 'Line: ' . __LINE__  . "\n\r Method: " . __METHOD__ . "\n\r " . print_r($res_track, true));
            }
        }
        return;
        /*

        $query_users = " SELECT
                u.ID
            FROM
            {$wpdb->prefix}observer_subs s
                JOIN {$wpdb->prefix}observer_lists l
                    ON s.list_id = l.id
                JOIN {$wpdb->prefix}users u
                    ON s.user_id = u.ID
            WHERE
                ( s.status != s.status_braze OR s.status_braze IS NULL)
                AND
                l.braze_sub_group_id IS NOT NULL
                AND
                s.`status` = 'subscribed'
            -- ORDER BY
            --     s.unsubscribed_at DESC
            LIMIT 75
        ";
        $users = $wpdb->get_results($query_users);

        $sub_users = [];
        $user_ids = [];

        require_once __DIR__ . '/braze.class.php';
        $braze = new Braze();
        $braze->setMethod('POST');

        if ($users) {
            foreach ($users as $user) {
                $auth0_id = get_user_meta($user->ID, $wpdb->prefix . 'auth0_id', true);
                if ($auth0_id) {
                    $attributes = [];
                    $user_ids[] = $user->ID;

                    if (get_user_meta($user->ID, 'created_braze_user', true) === 1) {
                        continue;
                    }

                    $attributes = [
                        'external_id'  => $auth0_id,
                        'first_name' => get_user_meta($user->ID, 'first_name', true),
                        'last_name' => get_user_meta($user->ID, 'last_name', true),
                        'gender' => get_user_meta($user->ID, 'gender', true),
                        'dob' => get_user_meta($user->ID, 'birthday', true)
                    ];

                    $payload = [
                        'attributes' => [
                            $attributes
                        ],
                    ];

                    // echo '<pre>' . print_r($payload, true) . '</pre>';

                    $braze->setPayload(
                        $payload
                    );
                    $res_track = $braze->request('/users/track');
                    if (201 === $res_track['code']) {
                        update_user_meta($user->ID, 'created_braze_user', 1);
                    }
                }
            } // For Each $sub
        } // If $subs

        if (!empty($user_ids)) {
            $query_subs = " SELECT
                s.id,
                s.list_id,
                s.status,
                u.ID user_id,
                u.user_email,
                l.interest_id,
                l.braze_sub_group_id,
                l.title list_title,
                l.status list_status
            FROM
            {$wpdb->prefix}observer_subs s
                JOIN {$wpdb->prefix}observer_lists l
                    ON s.list_id = l.id
                JOIN {$wpdb->prefix}users u
                    ON s.user_id = u.ID
            WHERE
                ( s.status != s.status_braze OR s.status_braze IS NULL)
                AND
                l.braze_sub_group_id IS NOT NULL
                AND
                s.`status` = 'subscribed'
                AND
                u.ID IN (" . implode(',', $user_ids) . ")
            LIMIT 50
            ";
            $subs = $wpdb->get_results($query_subs);


            if ($subs) {
                foreach ($subs as $sub) {
                    $auth0_id = get_user_meta($sub->user_id, $wpdb->prefix . 'auth0_id', true);
                    if ($auth0_id) {
                        $sub_users[$sub->braze_sub_group_id][] = $auth0_id;
                    }
                } // For Each $sub
            } // If $subs

            if (!empty($sub_users)) {
                try {
                    foreach ($sub_users as $sub_group_id => $auth0_ids) {
                        $payload = [
                            'subscription_group_id' => $sub_group_id,
                            'subscription_state' => "subscribed",
                            'external_id' =>  $auth0_ids,
                        ];

                        // echo '<pre>' . print_r($payload, true) . '</pre>';

                        $braze->setPayload(
                            $payload
                        );
                        $res_subscribe = $braze->request('/subscription/status/set');

                        if (201 === $res_subscribe['code']) {
                            foreach ($subs as $sub) {
                                $wpdb->update(
                                    $wpdb->prefix . 'observer_subs',
                                    [
                                        'status_braze' => 'subscribed',
                                        'braze_subscribed_at' => current_time('mysql')
                                    ],
                                    [
                                        'id' => $sub->id,
                                    ]
                                );
                            }
                        }
                        // echo '<pre>' . print_r($res_subscribe, true) . '</pre>';
                    } // For Each sub group
                } catch (\Exception $e) {
                    die($e->getMessage());
                }
            } // If $sub_users is NOT empty
        } // If $users */
    }
}

new Cron();
