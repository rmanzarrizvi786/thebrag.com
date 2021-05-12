<?php
namespace Ari_Stream_Quiz\Helpers;

use Ari_Stream_Quiz\Helpers\Settings as Settings;
use Ari\Utils\Array_Helper as Array_Helper;
use Ari\Wordpress\Helper as Wordpress_Helper;
use Ari\Facebook\Helper as Facebook_Helper;
use Ari\Utils\Utils as Utils;

define( 'ARI_STREAM_QUIZ_CACHE_LIFETIME', 30 * MINUTE_IN_SECONDS );

class Helper {
    private static $facebook_sdk_loaded = false;

    private static $system_args = array(
        'action',

        'msg',

        'msg_type',

        'noheader',
    );

    private static $quiz_types = array(
        ARISTREAMQUIZ_QUIZTYPE_PERSONALITY,

        ARISTREAMQUIZ_QUIZTYPE_TRIVIA
    );

    private static $themes = null;

    public static function build_url( $add_args = array(), $remove_args = array(), $remove_system_args = true, $encode_args = true ) {
        if ( $remove_system_args ) {
            $remove_args = array_merge( $remove_args, self::$system_args );
        }

        if ( $encode_args )
            $add_args = array_map( 'rawurlencode', $add_args );

        return add_query_arg( $add_args, remove_query_arg( $remove_args ) );
    }

    public static function is_valid_quiz_type( $type ) {
        return $type && in_array( $type, self::$quiz_types );
    }

    public static function get_themes() {
        if ( ! is_null( self::$themes ) ) {
            return self::$themes;
        }

        $folders = array();
        $path_list = array(
            array(
                'dir' => ARISTREAMQUIZ_THEMES_PATH,

                'exclude' => array( 'assets' ),
            ),

            array(
                'dir' => ARISTREAMQUIZ_CUSTOM_THEMES_PATH,
            )
        );

        foreach ( $path_list as $path_info ) {
            $path = $path_info['dir'];
            if ( ! ( $handle = @opendir( $path ) ) ) {
                continue ;
            }

            $exclude = Utils::get_value( $path_info, 'exclude', array() );

            while ( false !== ( $file = readdir( $handle ) ) ) {
                if ( '.' == $file || '..' == $file || in_array( $file, $exclude ) )
                    continue ;

                $is_dir = is_dir( $path . $file );

                if ( ! $is_dir )
                    continue ;

                $folders[] = $file;
            }
        }

        self::$themes = $folders;

        return self::$themes;
    }

    public static function resolve_theme_name( $theme ) {
        $themes = self::get_themes();

        if ( ! in_array( $theme, $themes ) )
            $theme = ARISTREAMQUIZ_THEME_DEFAULT;

        return $theme;
    }

    public static function duration_rules() {
        return array(
            86400 => __( 'd', 'ari-stream-quiz' ),

            3600 => __( 'h', 'ari-stream-quiz' ),

            60 => __( 'min', 'ari-stream-quiz' ),

            1 => __( 'sec', 'ari-stream-quiz' ),
        );
    }

    public static function quiz_type_nicename( $quiz_type ) {
        $nicename = '';

        if ( ARISTREAMQUIZ_QUIZTYPE_PERSONALITY == $quiz_type ) {
            $nicename = __( 'Personality', 'ari-stream-quiz' );
        } else if ( ARISTREAMQUIZ_QUIZTYPE_TRIVIA == $quiz_type ) {
            $nicename = __( 'Trivia', 'ari-stream-quiz' );
        }

        return $nicename;
    }

    public static function load_facebook_sdk() {
        if ( self::$facebook_sdk_loaded )
            return ;

        echo '<script>
        (function(d, s, id){
             var js, fjs = d.getElementsByTagName(s)[0];
             if (d.getElementById(id)) {return;}
             js = d.createElement(s); js.id = id; js.async = true;
             js.src = "//connect.facebook.net/' . Facebook_Helper::convert_locale( get_locale() ) . '/sdk.js";
             fjs.parentNode.insertBefore(js, fjs);
           }(document, "script", "facebook-jssdk"));
        </script>';

        self::$facebook_sdk_loaded = true;
    }

    public static function can_edit_other_quizzes() {
        return current_user_can( 'edit_others_posts' );
    }

    public static function can_edit_quiz( $quiz_id ) {
        if ( self::can_edit_other_quizzes() )
            return true;

        $can_edit = false;

        $quiz_id = intval( $quiz_id, 10 );
        if ( $quiz_id < 1 )
            return $can_edit;

        $quizzes_model = new \Ari_Stream_Quiz\Models\Quizzes(
            array(
                'class_prefix' => 'Ari_Stream_Quiz'
            )
        );
        $quiz_author_id = $quizzes_model->get_quiz_author_id( $quiz_id );
        if ( $quiz_author_id > 0 && get_current_user_id() == $quiz_author_id ) {
            $can_edit = true;
        }

        return $can_edit;
    }

    public static function filter_edit_quizzes( $id_list ) {
        if ( self::can_edit_other_quizzes() )
            return $id_list;

        $id_list = \Ari\Utils\Array_Helper::to_int( $id_list, 1 );

        if ( count( $id_list ) == 0 )
            return $id_list;

        $quizzes_model = new \Ari_Stream_Quiz\Models\Quizzes(
            array(
                'class_prefix' => 'Ari_Stream_Quiz'
            )
        );
        $quizzes_author_id = $quizzes_model->get_quizzes_author_id( $id_list );

        $filter_id_list = array();
        $user_id = get_current_user_id();

        foreach ( $id_list as $quiz_id ) {
            if ( isset( $quizzes_author_id[$quiz_id] ) ) {
                $quiz_author_id = $quizzes_author_id[$quiz_id]->author_id;

                if ( $user_id == $quiz_author_id )
                    $filter_id_list[] = $quiz_id;
            }
        }

        return $filter_id_list;
    }

    public static function get_mailchimp_lists( $reload = false ) {
        $api_key = Settings::get_option( 'mailchimp_apikey' );

        if ( empty( $api_key ) )
            return array();

        $cache_key = md5( 'mailchimp_lists_' . $api_key );
        if ( ! $reload ) {
            $lists = get_transient( $cache_key );

            if ( false !== $lists ) {
                return $lists;
            }
        }

        require_once ARISTREAMQUIZ_3RDPARTY_LOADER;

        $lists = array();
        try {
            $mailchimp = new \DrewM\MailChimp\MailChimp( $api_key );

            $result = $mailchimp->get(
                'lists',

                array(
                    'fields' => 'lists.id,lists.name',

                    'count' => 9999,
                )
            );

            if ( ! empty ( $result['lists'] ) && is_array( $result['lists'] ) ) {
                foreach ( $result['lists'] as $list ) {
                    $list_obj = new \stdClass();
                    $list_obj->id = $list['id'];
                    $list_obj->name = $list['name'];

                    $lists[] = $list_obj;
                }

                $lists = Array_Helper::sort_assoc( $lists, 'name' );
                set_transient( $cache_key, $lists, ARI_STREAM_QUIZ_CACHE_LIFETIME );
            }
        } catch (\Exception $ex) {
        }

        return $lists;
    }

    public static function get_aweber_lists( $reload = false ) {
        $consumer_key = Settings::get_option( 'aweber_consumer_key' );
        $consumer_secret = Settings::get_option( 'aweber_consumer_secret' );
        $access_token = Settings::get_option( 'aweber_access_token' );
        $access_secret = Settings::get_option( 'aweber_access_secret' );

        if ( empty( $consumer_key) || empty( $consumer_secret ) || empty( $access_token ) || empty( $access_secret ) ) {
            return array();
        }

        $cache_key = md5( 'aweber_lists_' . $consumer_key . $consumer_secret . $access_token . $access_secret );
        if ( ! $reload ) {
            $lists = get_transient( $cache_key );

            if ( false !== $lists ) {
                return $lists;
            }
        }

        if ( ! class_exists('AWeberAPI') ) require_once ARISTREAMQUIZ_3RDPARTY_PATH . 'aweber/aweber/aweber_api/aweber.php';

        $lists = array();
        try {
            $aweber_app = new \AWeberAPI( $consumer_key, $consumer_secret );
            $aweber_account = $aweber_app->getAccount( $access_token, $access_secret );

            $list_url = '/accounts/' . $aweber_account->id . '/lists/';

            $result = $aweber_account->loadFromUrl($list_url);

            if ( isset( $result->data ) && ! empty( $result->data['entries'] ) ) {
                foreach ( $result->data['entries'] as $list ) {
                    $list_obj = new \stdClass();
                    $list_obj->id = $list['id'];
                    $list_obj->name = $list['name'];

                    $lists[] = $list_obj;
                }

                $lists = Array_Helper::sort_assoc( $lists, 'name' );
                set_transient( $cache_key, $lists, ARI_STREAM_QUIZ_CACHE_LIFETIME );
            }
        } catch (\Exception $ex) {
        }

        return $lists;
    }

    public static function get_getresponse_campaigns( $reload = false ) {
        $api_key = Settings::get_option( 'getresponse_apikey' );

        if ( empty( $api_key ) )
            return array();

        $cache_key = md5( 'getresponse_campaigns_' . $api_key );
        if ( ! $reload ) {
            $campaigns = get_transient( $cache_key );

            if ( false !== $campaigns ) {
                return $campaigns;
            }
        }

        require_once ARISTREAMQUIZ_3RDPARTY_LOADER;

        $campaigns = array();
        try {
            $getResponse = new \GetResponse( $api_key );

            $result = (array)$getResponse->getCampaigns();

            if ( ! empty ( $result ) && is_array( $result ) ) {
                foreach ( $result as $campaign ) {
                    $campaign_obj = new \stdClass();
                    $campaign_obj->id = $campaign->campaignId;
                    $campaign_obj->name = $campaign->name;

                    $campaigns[] = $campaign_obj;
                }

                $campaigns = Array_Helper::sort_assoc( $campaigns, 'name' );
                set_transient( $cache_key, $campaigns, ARI_STREAM_QUIZ_CACHE_LIFETIME );
            }
        } catch (\Exception $ex) {
        }

        return $campaigns;
    }

    public static function get_drip_campaigns( $reload = false ) {
        $api_key = Settings::get_option( 'drip_apikey' );
        $account_id = Settings::get_option( 'drip_account_id' );

        if ( empty( $api_key ) || empty( $account_id ) )
            return array();

        $cache_key = md5( 'drip_campaigns_' . $api_key );
        if ( ! $reload ) {
            $campaigns = get_transient( $cache_key );

            if ( false !== $campaigns ) {
                return $campaigns;
            }
        }

        if ( ! class_exists('\Drip_Api') )
            require_once ARISTREAMQUIZ_PATH . 'libraries/drip/Drip_API.class.php';

        $campaigns = array();
        try {
            $getResponse = new \Drip_Api( $api_key );

            $result = (array)$getResponse->get_campaigns(
                array(
                    'account_id' => $account_id,

                    'status' => 'all',
                )
            );

            if ( false !== $result ) {
                if ( is_array( $result ) ) {
                    foreach ( $result as $campaign ) {
                        $campaign_obj = new \stdClass();
                        $campaign_obj->id = $campaign['id'];
                        $campaign_obj->name = $campaign['name'];

                        $campaigns[] = $campaign_obj;
                    }
                }

                $campaigns = Array_Helper::sort_assoc( $campaigns, 'name' );
                set_transient( $cache_key, $campaigns, ARI_STREAM_QUIZ_CACHE_LIFETIME );
            }
        } catch (\Exception $ex) {
        }

        return $campaigns;
    }

    public static function get_activecampaign_lists( $reload = false ) {
        $api_key = Settings::get_option( 'activecampaign_apikey' );
        $api_url = Settings::get_option( 'activecampaign_url' );

        if ( empty( $api_key ) || empty( $api_url ) )
            return array();

        $cache_key = md5( 'activecampaign_lists_' . $api_key );
        if ( ! $reload ) {
            $lists = get_transient( $cache_key );

            if ( false !== $lists ) {
                return $lists;
            }
        }

        require_once ARISTREAMQUIZ_3RDPARTY_LOADER;

        $lists = array();
        try {
            $activeCampaign = new \ActiveCampaign( $api_url, $api_key );

            $result = $activeCampaign->api(
                'list/list',
                array(
                    'ids' => 'all',

                    'global_fields' => 0,

                    'full' => 1,
                )
            );

            if ( ! empty( $result->success ) ) {
                $i = 0;
                while ( isset( $result->{$i} ) ) {
                    $list = $result->{$i};

                    $list_obj = new \stdClass();
                    $list_obj->id = $list->id;
                    $list_obj->name = $list->name;

                    $lists[] = $list_obj;

                    ++$i;
                }

                $lists = Array_Helper::sort_assoc( $lists, 'name' );
                set_transient( $cache_key, $lists, ARI_STREAM_QUIZ_CACHE_LIFETIME );
            }
        } catch (\Exception $ex) {
        }

        return $lists;
    }

    public static function get_mailerlite_lists( $reload = false ) {
        $api_key = Settings::get_option( 'mailerlite_apikey' );

        if ( empty( $api_key ) )
            return array();

        $cache_key = md5( 'mailerlite_lists_' . $api_key );
        if ( ! $reload ) {
            $lists = get_transient( $cache_key );

            if ( false !== $lists ) {
                return $lists;
            }
        }

        require_once ARISTREAMQUIZ_3RDPARTY_LOADER;

        $lists = array();
        try {
            $groups_api = ( new \MailerLiteApi\MailerLite( $api_key ) )->groups();

            $groups = $groups_api->get();

            if ( ! empty ( $groups ) && $groups->count() > 0 ) {
                foreach ( $groups as $list ) {
                    $list_obj = new \stdClass();
                    $list_obj->id = $list->id;
                    $list_obj->name = $list->name;

                    $lists[] = $list_obj;
                }

                $lists = Array_Helper::sort_assoc( $lists, 'name' );
                set_transient( $cache_key, $lists, ARI_STREAM_QUIZ_CACHE_LIFETIME );
            }
        } catch (\Exception $ex) {
        }

        return $lists;
    }

    public static function get_constantcontact_lists( $reload = false ) {
        $api_key = Settings::get_option( 'constantcontact_apikey' );
        $access_token = Settings::get_option( 'constantcontact_access_token' );

        if ( empty( $api_key ) || empty( $access_token ) )
            return array();

        $cache_key = md5( 'constantcontact_lists_' . $api_key );
        if ( ! $reload ) {
            $lists = get_transient( $cache_key );

            if ( false !== $lists ) {
                return $lists;
            }
        }

        require_once ARISTREAMQUIZ_3RDPARTY_LOADER;

        $lists = array();
        try {
            $cc_api = new \Ctct\ConstantContact( $api_key );
            $cc_lists = $cc_api->listService->getLists( $access_token );

            if ( is_array( $cc_lists ) && count( $cc_lists ) > 0 ) {
                foreach ( $cc_lists as $list ) {
                    $list_obj = new \stdClass();
                    $list_obj->id = $list->id;
                    $list_obj->name = $list->name;

                    $lists[] = $list_obj;
                }

                $lists = Array_Helper::sort_assoc( $lists, 'name' );
                set_transient( $cache_key, $lists, ARI_STREAM_QUIZ_CACHE_LIFETIME );
            }
        } catch (\Exception $ex) {
        }

        return $lists;
    }

    public static function prepare_quiz_session_questions( $quiz_session ) {
        if ( empty( $quiz_session->data->pages ) )
            return $quiz_session;

        foreach ( $quiz_session->data->pages as $page ) {
            foreach ( $page->questions as $question ) {
                $question->question_title = Wordpress_Helper::do_shortcode( $question->question_title );

                if ( isset( $question->explanation ) )
                    $question->explanation = Wordpress_Helper::do_shortcode( $question->explanation );

                foreach ( $question->answers as $answer ) {
                    $answer->answer_title = Wordpress_Helper::do_shortcode( $answer->answer_title );
                }
            }
        }

        return $quiz_session;
    }

    public static function get_quiz_meta_tags( $quiz_id ) {
        $quiz_id = intval( $quiz_id, 10 );
        if ( $quiz_id < 1 )
            return null;

        $quiz_model = new \Ari_Stream_Quiz\Models\Quiz(
            array(
                'class_prefix' => 'Ari_Stream_Quiz'
            )
        );

        return $quiz_model->get_quiz_meta_tags( $quiz_id );
    }

    public static function get_crypt_key() {
        $crypt_key = get_option( ARISTREAMQUIZ_CRYPT_KEY_OPTION, '' );

        return $crypt_key;
    }

    public static function save_crypt_key( $crypt_key ) {
        return update_option( ARISTREAMQUIZ_CRYPT_KEY_OPTION, $crypt_key );
    }
}
