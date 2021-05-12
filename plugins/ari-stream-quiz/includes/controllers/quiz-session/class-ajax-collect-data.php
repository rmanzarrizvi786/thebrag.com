<?php
namespace Ari_Stream_Quiz\Controllers\Quiz_Session;

use Ari\Controllers\Ajax as Ajax_Controller;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Helpers\Settings as Settings;
use Ari_Stream_Quiz\Helpers\Quiz_Activity as Quiz_Activity;
use Ari_Stream_Quiz\Helpers\Statistics_Activity as Statistics_Activity;

class Ajax_Collect_Data extends Ajax_Controller {
    protected function process_request() {
        if ( ! check_ajax_referer( 'ari-stream-quiz-ajax-action', ARISTREAMQUIZ_AJAX_NONCE_FIELD, false ) )
            return false;

        $session_key = Request::get_var( 'session_key' );
        $quiz_id = Request::get_var( 'id', 0, 'num' );
        if ( $quiz_id < 1 )
            return false;

        $quiz_model = $this->model( 'Quiz' );
        $quiz = $quiz_model->get_quiz( $quiz_id );

        if ( is_null( $quiz ) ) {
            return false;
        }

        $result_data = stripslashes_deep( Request::get_var( 'result' ) );
        $user_data = stripslashes_deep( Request::get_var( 'user_data' ) );
        $user_data = json_decode( $user_data, true );

        $has_email = ! empty( $user_data['email'] );
        if ( ! empty( $user_data['name'] ) || $has_email ) {
            $this->add_activity( $quiz_id, $session_key, $user_data );
        }

        $result = true;

        if ( $quiz->quiz_meta->mailchimp->enabled && ! empty( $quiz->quiz_meta->mailchimp->list_id ) ) {
            if ( ! $this->add_to_mailchimp_list( $user_data, $quiz->quiz_meta->mailchimp->list_id ) )
                $result = false;
        }

        if ( $quiz->quiz_meta->mailerlite->enabled && ! empty( $quiz->quiz_meta->mailerlite->list_id ) ) {
            if ( ! $this->add_to_mailerlite_list( $user_data, $quiz->quiz_meta->mailerlite->list_id ) )
                $result = false;
        }

        if ( $quiz->quiz_meta->aweber->enabled && ! empty( $quiz->quiz_meta->aweber->list_id ) ) {
            if ( ! $this->add_to_aweber_list( $user_data, $quiz->quiz_meta->aweber->list_id ) )
                $result = false;
        }

        if ( $quiz->quiz_meta->getresponse->enabled && ! empty( $quiz->quiz_meta->getresponse->campaign_id ) ) {
            if ( ! $this->add_to_getresponse_campaign( $user_data, $quiz->quiz_meta->getresponse->campaign_id ) )
                $result = false;
        }

        if ( $quiz->quiz_meta->drip->enabled && ! empty( $quiz->quiz_meta->drip->campaign_id ) ) {
            if ( ! $this->add_to_drip_campaign( $user_data, $quiz->quiz_meta->drip->campaign_id ) )
                $result = false;
        }

        if ( $quiz->quiz_meta->activecampaign->enabled && ! empty( $quiz->quiz_meta->activecampaign->list_id ) ) {
            if ( ! $this->add_to_activecampaign_list( $user_data, $quiz->quiz_meta->activecampaign->list_id ) )
                $result = false;
        }

        if ( $quiz->quiz_meta->constantcontact->enabled && ! empty( $quiz->quiz_meta->constantcontact->list_id ) ) {
            if ( ! $this->add_to_constantcontact_list( $user_data, $quiz->quiz_meta->constantcontact->list_id ) )
                $result = false;
        }

        if ( $quiz->quiz_meta->zapier->enabled && ! empty( $quiz->quiz_meta->zapier->webhook_url ) ) {
            if ( ! $this->send_data_to_zapier( $user_data, $quiz->quiz_meta->zapier->webhook_url, $quiz, $result_data ) )
                $result = false;
        }

        if ( $quiz->quiz_meta->send_mail->enabled && Request::exists( 'result' ) ) {
            $user = wp_get_current_user();
            $email = $has_email ? $user_data['email'] : $user->user_email;
            $user_name = !empty( $user_data['name'] ) ? $user_data['name'] : $user->user_nicename;
            if ( $email ) {
                $quiz_results = stripslashes_deep( Request::get_var( 'result' ) );
                if ( is_array( $quiz_results ) ) {
                    $quiz_results['user'] = array(
                        'name' => $user_name,
                        'email' => $email,
                        'login' => $user->user_login,
                    );
                }
                $quiz->send_mail( $email, $quiz_results );
            }
        }

        return $result;
    }

    private function add_activity( $quiz_id, $session_key, $data = null ) {
        $session_model = $this->model( 'Quiz_Session' );
        $session_model->add_activity( $session_key, Quiz_Activity::OPT_IN, $quiz_id, $data );
        $statistics_model = $this->model( 'Statistics' );
        $statistics_model->log_activity( $quiz_id, Statistics_Activity::OPT_IN );
    }

    private function add_to_mailchimp_list( $data, $list_id ) {
        $email = isset( $data['email'] ) ? $data['email'] : '';
        $api_key = Settings::get_option( 'mailchimp_apikey' );

        if ( empty( $email ) || empty( $api_key ) )
            return false;

        $double_optin = Settings::get_option( 'mailchimp_double_optin' );
        $status = $double_optin ? 'pending' : 'subscribed';
        $name = isset( $data['name'] ) ? $data['name'] : '';

        require_once ARISTREAMQUIZ_3RDPARTY_LOADER;

        $result = true;
        try {
            $mailchimp = new \DrewM\MailChimp\MailChimp( $api_key );

            $result = $mailchimp->post(
                'lists/' . $list_id . '/members',

                array(
                    'email_address' => $email,

                    'status' => $status,

                    'merge_fields' => array(
                        'FNAME' => $name,
                    ),
                )
            );
        } catch (\Exception $ex) {
            $result = false;
        }

        return $result;
    }

    private function add_to_aweber_list( $data, $list_id ) {
        $list_id = preg_replace( '/^[A-z]+/', '', $list_id );
        $email = isset( $data['email'] ) ? $data['email'] : '';

        if ( empty( $email ) || empty( $list_id ) ) {
            return false;
        }

        $name = isset( $data['name'] ) ? $data['name'] : '';

        if ( ! class_exists('AWeberAPI') ) require_once ARISTREAMQUIZ_3RDPARTY_PATH . 'aweber/aweber/aweber_api/aweber.php';

        $subscriber = array(
            'email' => $email,

            'name' => $name,
        );

        $consumer_key = Settings::get_option( 'aweber_consumer_key' );
        $consumer_secret = Settings::get_option( 'aweber_consumer_secret' );
        $access_token = Settings::get_option( 'aweber_access_token' );
        $access_secret = Settings::get_option( 'aweber_access_secret' );

        if ( empty( $consumer_key) || empty( $consumer_secret ) || empty( $access_token ) || empty( $access_secret ) ) {
            return false;
        }

        $result = true;
        try {
            $aweber_app = new \AWeberAPI( $consumer_key, $consumer_secret );
            $aweber_account = $aweber_app->getAccount( $access_token, $access_secret );

            $list_url = '/accounts/' . $aweber_account->id . '/lists/' . $list_id;

            $list = $aweber_account->loadFromUrl($list_url);
            $new_subscriber = $list->subscribers->create($subscriber);
        } catch (\Exception $ex) {
            $result = false;
        }

        return $result;
    }

    private function send_data_to_zapier( $data, $webhook_url, $quiz, $result_data ) {
        $email = isset( $data['email'] ) ? $data['email'] : '';

        if ( empty( $email ) || empty( $webhook_url ) ) {
            return false;
        }

        $name = isset( $data['name'] ) ? $data['name'] : '';

        $zapier_data = array(
            'email' => $email,

            'name' => $name,

            'quiz' => $data['quiz'],

            'result' => $data['result'],
        );

        $zapier_data = apply_filters( 'asq_zapier_data', $zapier_data, $result_data, $quiz );

        $args = array(
            'body' => $zapier_data,
        );

        $response = wp_safe_remote_post( $webhook_url, $args );

        $res = false;
        if ( is_wp_error( $response ) ) {
            //$error_message = $response->get_error_message();
        } else {
            if ( ! empty( $response['body'] ) ) {
                $response_data = json_decode( $response['body'], true );

                if ( json_last_error() === JSON_ERROR_NONE ) {
                    if ( isset( $response_data['status'] ) && 'success' == $response_data['status'] ) {
                        $res = true;
                    }
                }
            }
        }

        return $res;
    }

    private function add_to_getresponse_campaign( $data, $campaign_id ) {
        $email = isset( $data['email'] ) ? $data['email'] : '';
        $api_key = Settings::get_option( 'getresponse_apikey' );

        if ( empty( $email ) || empty( $api_key ) )
            return false;

        $name = isset( $data['name'] ) ? $data['name'] : '';

        require_once ARISTREAMQUIZ_3RDPARTY_LOADER;

        $result = true;
        try {
            $getResponse = new \GetResponse( $api_key );
            $ip_address = Request::get_ip();

            $data = array(
                'email' => $email,

                'dayOfCycle' => 0,

                'campaign' => array(
                    'campaignId' => $campaign_id
                )
            );

            if ( ! empty ( $ip_address ) )
                $data['ipAddress'] = $ip_address;

            if ( ! empty ( $name ) )
                $data['name'] = $name;

            $result = $getResponse->addContact( $data );
            if ( is_object( $result ) && isset( $result->httpStatus ) && $result->httpStatus == 400 ) {
                $result = false;
            } else {
                $result = true;
            }
        } catch (\Exception $ex) {
            $result = false;
        }

        return $result;
    }

    private function add_to_drip_campaign( $data, $campaign_id ) {
        $email = isset( $data['email'] ) ? $data['email'] : '';
        $api_key = Settings::get_option( 'drip_apikey' );
        $account_id = Settings::get_option( 'drip_account_id' );

        if ( empty( $email ) || empty( $api_key ) || empty( $account_id ) )
            return false;

        $name = isset( $data['name'] ) ? $data['name'] : '';

        if ( ! class_exists('\Drip_Api') )
            require_once ARISTREAMQUIZ_PATH . 'libraries/drip/Drip_API.class.php';

        $result = true;
        try {
            $drip = new \Drip_Api( $api_key );
            $ip_address = Request::get_ip();

            $data = array(
                'account_id' => $account_id,

                'campaign_id' => $campaign_id,

                'email' => $email,

                //'double_optin' => true,
            );

            if ( ! empty ( $ip_address ) )
                $data['ip_address'] = $ip_address;

            if ( ! empty ( $name ) )
                $data['custom_fields'] = array(
                    'name' => $name,
                );

            $result = $drip->subscribe_subscriber( $data );
            if ( false !== $result ) {
                $result = true;
            }
        } catch (\Exception $ex) {
            $result = false;
        }

        return $result;
    }

    private function add_to_activecampaign_list( $data, $list_id ) {
        $email = isset( $data['email'] ) ? $data['email'] : '';
        $api_key = Settings::get_option( 'activecampaign_apikey' );
        $api_url = Settings::get_option( 'activecampaign_url' );

        if ( empty( $email ) || empty( $api_key ) || empty( $api_url ) )
            return false;

        $name = isset( $data['name'] ) ? $data['name'] : '';

        require_once ARISTREAMQUIZ_3RDPARTY_LOADER;

        $result = true;
        try {
            $activeCampaign = new \ActiveCampaign( $api_url, $api_key );
            $ip_address = Request::get_ip();

            $data = array(
                'email' => $email,

                "p[{$list_id}]" => $list_id,

                "status[{$list_id}]" => 1,

                //tags => 'tag 1, tag 2,',
            );

            if ( ! empty ( $ip_address ) )
                $data['ip4'] = $ip_address;

            if ( ! empty ( $name ) )
                $data['first_name'] = $name;

            $result = $activeCampaign->api(
                'contact/sync',
                $data
            );

            if ( (int)$result->success > 0 && (int)$result->subscriber_id ) {
                $result = true;
            } else {
                $result = false;
            }
        } catch (\Exception $ex) {
            $result = false;
        }

        return $result;
    }

    private function add_to_mailerlite_list( $data, $list_id ) {
        $email = isset( $data['email'] ) ? $data['email'] : '';
        $api_key = Settings::get_option( 'mailerlite_apikey' );

        if ( empty( $email ) || empty( $api_key ) )
            return false;

        $name = isset( $data['name'] ) ? $data['name'] : '';

        require_once ARISTREAMQUIZ_3RDPARTY_LOADER;

        $result = true;
        try {
            $groups_api = ( new \MailerLiteApi\MailerLite( $api_key ) )->groups();
            $subscriber = $groups_api->addSubscriber(
                $list_id,

                array(
                    'email' => $email,

                    'name' => $name,
                )
            );
        } catch (\Exception $ex) {
            $result = false;
        }

        return $result;
    }

    private function add_to_constantcontact_list( $data, $list_id ) {
        $email = isset( $data['email'] ) ? $data['email'] : '';
        $api_key = Settings::get_option( 'constantcontact_apikey' );
        $access_token = Settings::get_option( 'constantcontact_access_token' );

        if ( empty( $email ) || empty( $api_key ) || empty( $access_token ) )
            return false;

        $name = isset( $data['name'] ) ? $data['name'] : '';

        require_once ARISTREAMQUIZ_3RDPARTY_LOADER;

        $result = true;
        try {
            $subscriber = \Ctct\Components\Contacts\Contact::create(
                array(
                    'email_addresses' => array(
                        array(
                            'email_address' => $email,
                        ),
                    ),

                    'first_name' => $name,

                    'lists' => array(
                        array(
                            'id' => $list_id,
                        ),
                    ),
                )
            );

            $cc_api = new \Ctct\ConstantContact( $api_key );
            $cc_api->contactService->addContact( $access_token, $subscriber );
        } catch (\Exception $ex) {
            $result = false;
        }

        return $result;
    }
}
