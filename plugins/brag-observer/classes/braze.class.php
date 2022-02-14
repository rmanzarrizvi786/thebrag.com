<?php

/**
 * Handle Braze requests
 */
class Braze
{
    protected $sdk_api_key;
    protected $sdk_endpoint;
    protected $api_key;
    protected $api_url;
    protected static $config;

    public function __construct()
    {
        self::$config = include __DIR__ . '/config.php';
        $this->sdk_api_key = self::$config['braze']['sdk_api_key'];
        $this->sdk_endpoint = self::$config['braze']['sdk_endpoint'];
        $this->api_key = self::$config['braze']['api_key'];
        $this->api_url = self::$config['braze']['api_url'];
    }

    public function setMethod($method = 'GET')
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setUrl($api_url)
    {
        $this->api_url = $api_url;
    }

    public function getUrl()
    {
        if (isset($this->api_url)) {
            return $this->api_url;
        }
        return FALSE;
    }

    public function setPayload($payload = NULL)
    {
        $this->payload = $payload;
    }

    public function getPayload()
    {
        if (isset($this->payload)) {
            return $this->payload;
        }
        return FALSE;
    }

    public function setParams($params = array())
    {
        $this->params = $params;
    }

    public function getParams()
    {
        if (isset($this->params)) {
            return $this->params;
        }
        return false;
    }

    public function buildQuery($params)
    {
        if (!empty($params)) {
            return \http_build_query($params);
        }
        return FALSE;
    }

    public function request($urlSuffix = '', $bulk = false)
    {
        $url = $this->getUrl();
        $url .= $urlSuffix;
        $payload = $this->getPayload();
        $method = $this->getMethod();
        $params = $this->getParams();
        $query = $this->buildQuery($params);
        if (!empty($query)) {
            $url = $url . '?' . $query;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout after 10 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if (strtoupper($method) == 'POST') {
            $data = json_encode($payload);
            $headers  = [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data),
                'Authorization: Bearer ' . $this->api_key
            ];
            if ($bulk) {
                $header[] = 'X-Braze-Bulk: true';
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $result = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        curl_close($ch);

        return array(
            'response' => $result,
            'code' => $status_code,
        );
    }

    public function getUser($user_id)
    {
        global $wpdb;
        $user = get_user_by('id', $user_id);
        if (!$user)
            return false;

        $user_info = get_userdata($user_id);

        $user_attributes = [];
        $user_attributes['email'] = $user->user_email;

        $this->setMethod('POST');
        $this->setPayload([
            'email_address' => $user_info->user_email
        ]);
        $res_braze_users = $this->request('/users/export/ids');

        $has_braze_user = false;

        if (201 == $res_braze_users['code']) {
            $braze_users = json_decode($res_braze_users['response']);

            if (isset($braze_users->users[0])) {
                $braze_user = $braze_users->users[0];

                $has_braze_user = true;

                // $user_attributes['custom_attributes'] = $braze_user->custom_attributes;

                if (isset($braze_user->external_id)) {
                    $user_attributes['external_id'] = $braze_user->external_id;
                } else {
                    $user_attributes['user_alias'] = $braze_user->user_aliases[0];
                    $user_attributes['_update_existing_only'] = false;
                }
            }
        }

        if (!$has_braze_user) {
            if (get_user_meta($user_id, $wpdb->prefix . 'auth0_id')) {
                $user_attributes['external_id'] = get_user_meta($user_id, $wpdb->prefix . 'auth0_id', true);
            } else if (get_user_meta($user_id, 'wp_auth0_id')) {
                // If user's Auth0 ID is not set using wpdb prefix, check if set using wp_ prefix
                $user_attributes['external_id'] = get_user_meta($user_id, 'wp_auth0_id', true);
            } else {
                // User's Auth0 ID not set, set alias for user
                $user_attributes['user_alias'] = [
                    'alias_name' => $user_info->user_email,
                    'alias_label' => 'email',
                ];
                $user_attributes['_update_existing_only'] = false;
            }

            $attributes[] = array_merge($user_attributes, ['email' => $user_info->user_email]);
            $braze_payload['attributes'] = $attributes;
            $this->setPayload($braze_payload);
            $this->request('/users/track', true);

            $braze_user = $this->getUser($user_id);
        }

        return [
            'user' => isset($braze_user) ? $braze_user : null,
            'user_attributes' => $user_attributes,
        ];
    } // getUser($user_id)

    public function getUserByEmail($email)
    {
        $user = get_user_by('email', $email);
        if ($user) {
            return $this->getUser($user->ID);
        }

        $user_attributes = [];
        $user_attributes['email'] = $email;

        $this->setMethod('POST');
        $this->setPayload([
            'email_address' => $email
        ]);
        $res_braze_users = $this->request('/users/export/ids');

        $has_braze_user = false;

        if (201 == $res_braze_users['code']) {
            $braze_users = json_decode($res_braze_users['response']);

            if (isset($braze_users->users[0])) {
                $braze_user = $braze_users->users[0];

                $has_braze_user = true;

                if (isset($braze_user->external_id)) {
                    $user_attributes['external_id'] = $braze_user->external_id;
                } else {
                    $user_attributes['user_alias'] = $braze_user->user_aliases[0];
                    $user_attributes['_update_existing_only'] = false;
                }
            }
        }

        if (!$has_braze_user) {
            $user_attributes['user_alias'] = [
                'alias_name' => $email,
                'alias_label' => 'email',
            ];
            $user_attributes['_update_existing_only'] = false;

            $attributes[] = array_merge($user_attributes, ['email' => $email]);
            $braze_payload['attributes'] = $attributes;
            $this->setPayload($braze_payload);
            $this->request('/users/track', true);

            $braze_user = $this->getUserByEmail($email);
        }

        return [
            'user' => isset($braze_user) ? $braze_user : null,
            'user_attributes' => $user_attributes,
        ];
    } // getUser($user_id)

    public function triggerEvent($user_id, $event_name, $properties = [])
    {
        $braze_user = $this->getUser($user_id);
        $user_attributes = $braze_user['user_attributes'];

        $braze_payload = [];

        $event_payload = [
            'name' => $event_name,
            'time' => current_time('c')
        ];
        if (is_array($properties) && !empty($properties)) {
            $event_payload = array_merge($event_payload, ['properties' => $properties]);
        }
        $braze_payload['events'] = [array_merge($user_attributes, $event_payload)];

        if (!empty($braze_payload)) {
            $this->setPayload($braze_payload);
            $res_track = $this->request('/users/track', true);
            if (201 !== $res_track['code']) {
                error_log("Error pushing event to Braze in " . __METHOD__ . " on line " . __LINE__ . ". " .  print_r($res_track, true)) . print_r($braze_payload, true);
            }
            return $res_track;
        }
    }

    public function triggerEventByEmail($email, $event_name, $properties = [])
    {
        $braze_user = $this->getUserByEmail($email);

        $user_attributes = $braze_user['user_attributes'];

        $braze_payload = [];

        $event_payload = [
            'name' => $event_name,
            'time' => current_time('c')
        ];
        if (is_array($properties) && !empty($properties)) {
            $event_payload = array_merge($event_payload, ['properties' => $properties]);
        }
        $braze_payload['events'] = [array_merge($user_attributes, $event_payload)];

        if (!empty($braze_payload)) {
            $this->setPayload($braze_payload);
            $res_track = $this->request('/users/track', true);
            if (201 !== $res_track['code']) {
                error_log("Error pushing event to Braze in " . __METHOD__ . " on line " . __LINE__ . ". " .  print_r($res_track, true)) . print_r($braze_payload, true);
            }
            return $res_track;
        }
        return false;
    }
}
