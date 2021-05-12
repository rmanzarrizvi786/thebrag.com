<?php
namespace Ari_Stream_Quiz\Helpers;

define( 'ARISTREAMQUIZ_SETTINGS_GROUP', 'ari_stream_quiz' );
define( 'ARISTREAMQUIZ_SETTINGS_NAME', 'ari_stream_quiz_settings' );

define( 'ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE', 'ari-stream-quiz-settings-general' );
define( 'ARISTREAMQUIZ_SETTINGS_SHARING_PAGE', 'ari-stream-quiz-settings-sharing' );
define( 'ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE', 'ari-stream-quiz-settings-advanced' );

define( 'ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION', 'ari_stream_quiz_general_section' );
define( 'ARISTREAMQUIZ_SETTINGS_SHORTCODE_SECTION', 'ari_stream_quiz_shortcode_section' );
define( 'ARISTREAMQUIZ_SETTINGS_UPDATE_SECTION', 'ari_stream_quiz_update_section' );
define( 'ARISTREAMQUIZ_SETTINGS_MAILCHIMP_SECTION', 'ari_stream_quiz_mailchimp_section' );
define( 'ARISTREAMQUIZ_SETTINGS_MAILERLITE_SECTION', 'ari_stream_quiz_mailerlite_section' );
define( 'ARISTREAMQUIZ_SETTINGS_AWEBER_SECTION', 'ari_stream_quiz_aweber_section' );
define( 'ARISTREAMQUIZ_SETTINGS_CONSTANTCONTACT_SECTION', 'ari_stream_quiz_constantontact_section' );
define( 'ARISTREAMQUIZ_SETTINGS_GETRESPONSE_SECTION', 'ari_stream_quiz_getresponse_section' );
define( 'ARISTREAMQUIZ_SETTINGS_DRIP_SECTION', 'ari_stream_quiz_drip_section' );
define( 'ARISTREAMQUIZ_SETTINGS_ACTIVECAMPAIGN_SECTION', 'ari_stream_quiz_activecampaign_section' );
define( 'ARISTREAMQUIZ_SETTINGS_TRIVIAQUIZ_SECTION', 'ari_stream_quiz_triviaquiz_section' );
define( 'ARISTREAMQUIZ_SETTINGS_SHARING_SECTION', 'ari_stream_quiz_sharing_section' );
define( 'ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION', 'ari_stream_quiz_sharing_triviacontent_section' );
define( 'ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION', 'ari_stream_quiz_sharing_personalitycontent_section' );
define( 'ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION', 'ari_stream_quiz_advanced_section' );
define( 'ARISTREAMQUIZ_SETTINGS_MAIL_SECTION', 'ari_stream_quiz_mail_section' );

define( 'ARISTREAMQUIZ_SETTINGS_ARRAY_DELIMITER', ';' );

use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Settings {
    static private $options = null;

    static private $default_settings = array(
        'api_key' => '',

        'theme' => ARISTREAMQUIZ_THEME_DEFAULT,

        'smart_scroll' => true,

        'scroll_disable_on_load' => false,

        'scroll_duration' => 600,

        'scroll_offset' => '0',

        'custom_styles' => '',

        'custom_js' => '',

        'facebook_app_id' => '',
        
        'facebook_description_limit' => 250,

        'facebook_load_sdk' => true,

        'share_buttons' => array( 'facebook', 'twitter', 'gplus', 'pinterest', 'linkedin', 'vk' ),

        'progress_bar' => '', // '','empty', 'percent', 'pages'

        'progress_bar_position' => array( 'top' ),

        'next_page_timeout' => 1000,

        'save_results' => 'all', // empty, 'known', 'all'

        'show_results' => '', // empty, 'immediately', 'on_complete'

        'show_questions_oncomplete' => true,

        'share_trivia_title' => 'You got {{userScore}} out of {{maxScore}} correct',

        'share_trivia_facebook_title' => 'I got {{userScore}} out of {{maxScore}}. What about you?',

        'share_trivia_facebook_content' => '{{content}}',

        'share_trivia_twitter_content' => 'I got {{userScore}} out of {{maxScore}}. What about you?',

        'share_trivia_pinterest_content' => 'I got {{userScore}} out of {{maxScore}}. What about you?',

        'share_trivia_linkedin_title' => 'I got {{userScore}} out of {{maxScore}}. What about you?',

        'share_trivia_linkedin_content' => '{{content}}',

        'share_trivia_vk_title' => 'I got {{userScore}} out of {{maxScore}}. What about you?',

        'share_trivia_vk_content' => '{{content}}',

        'share_trivia_email_subject' => '{{title}}',

        'share_trivia_email_body' => '{{url}}',

        'share_personality_title' => 'You are {{title}}',

        'share_personality_facebook_title' => 'I am {{title}}. What about you?',

        'share_personality_facebook_content' => '{{content}}',

        'share_personality_twitter_content' => 'I am {{title}}. What about you?',

        'share_personality_pinterest_content' => 'I am {{title}}. What about you?',

        'share_personality_linkedin_title' => 'I am {{title}}. What about you?',

        'share_personality_linkedin_content' => '{{content}}',

        'share_personality_vk_title' => 'I am {{title}}. What about you?',

        'share_personality_vk_content' => '{{content}}',

        'share_personality_email_subject' => '{{title}}',

        'share_personality_email_body' => '{{url}}',

        'mailchimp_apikey' => '',

        'mailchimp_double_optin' => false,

        'aweber_auth_code' => '',

        'aweber_app_id' => '85529226',

        'aweber_consumer_key' => 'AkeKyFN951brz9jev10U6sqn',

        'aweber_consumer_secret' => 'y93JIpb6QPhjSPaeqZCHrQpBIpVeK8XRhLF61Tgc',

        'aweber_access_token' => '',

        'aweber_access_secret' => '',

        'getresponse_apikey' => '',

        'drip_apikey' => '',

        'drip_account_id' => '',

        'activecampaign_url' => '',

        'activecampaign_apikey' => '',

        'lazy_load' => true,

        'reload_page' => false,

        'warning_on_exit' => false,

        'prefetch_quiz_session' => false,

        'shortcode_quiz_hide_title' => false,

        'shortcode_quiz_column_count' => 2,

        'lockout_answers' => true,

        'mailerlite_apikey' => '',

        'constantcontact_apikey' => '',

        'constantcontact_access_token' => '',

        'add_meta_tags' => true,

        'disable_script_optimization' => false,

        'mail_from' => '',

        'mail_from_name' => '',

        'template_wrap_with_tag' => false,
    );

    public static function init() {
        register_setting(
            ARISTREAMQUIZ_SETTINGS_GROUP,
            ARISTREAMQUIZ_SETTINGS_NAME,
            array( __CLASS__, 'sanitize' )
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_UPDATE_SECTION,
            '', // Title
            array( __CLASS__, 'render_update_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_SHORTCODE_SECTION,
            '', // Title
            array( __CLASS__, 'render_shortcode_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION,
            '', // Title
            array( __CLASS__, 'render_general_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_TRIVIAQUIZ_SECTION,
            '', // Title
            array( __CLASS__, 'render_triviaquiz_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_MAIL_SECTION,
            '', // Title
            array( __CLASS__, 'render_mail_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_MAILCHIMP_SECTION,
            '', // Title
            array( __CLASS__, 'render_mailchimp_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_MAILERLITE_SECTION,
            '', // Title
            array( __CLASS__, 'render_mailerlite_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_AWEBER_SECTION,
            '', // Title
            array( __CLASS__, 'render_aweber_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_GETRESPONSE_SECTION,
            '', // Title
            array( __CLASS__, 'render_getresponse_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_DRIP_SECTION,
            '', // Title
            array( __CLASS__, 'render_drip_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_ACTIVECAMPAIGN_SECTION,
            '', // Title
            array( __CLASS__, 'render_activecampaign_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_CONSTANTCONTACT_SECTION,
            '', // Title
            array( __CLASS__, 'render_constantcontact_section_info' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_SHARING_SECTION,
            '', // Title
            array( __CLASS__, 'render_sharing_section_info' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION,
            '', // Title
            array( __CLASS__, 'render_sharing_triviacontent_section_info' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION,
            '', // Title
            array( __CLASS__, 'render_sharing_personalitycontent_section_info' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE
        );

        add_settings_section(
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION,
            '', // Title
            array( __CLASS__, 'render_advanced_section_info' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE
        );

        add_settings_field(
            'api_key',
            self::format_option_name(
                __( 'API key', 'ari-stream-quiz' ),

                __( 'Enter API key which received when purchase the extension. It requires to enable notifications about new versions and install them automatically.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_update_api_key' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_UPDATE_SECTION
        );

        add_settings_field(
            'shortcode_quiz_hide_title',
            self::format_option_name(
                __( 'Hide title', 'ari-stream-quiz' ),

                __( 'If the parameter is enabled, title of the quizzes which are embedded via shortcode will be hidden. Can be changed directly into shortcode via hide_title shortcode parameter.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_shortcode_quiz_hide_title' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHORTCODE_SECTION
        );

        add_settings_field(
            'shortcode_quiz_column_count',
            self::format_option_name(
                __( 'Image answers per row', 'ari-stream-quiz' ),

                __( 'It is used to specify how many image-based answers will be shown per row. Can be changed directly into shortcode via col shortcode parameter.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_shortcode_quiz_col_count' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHORTCODE_SECTION
        );


        add_settings_field(
            'theme',
            self::format_option_name(
                __( 'Default theme', 'ari-stream-quiz' ),

                __( 'The selected theme will be used for all quizzes by default if it is not overridden in quiz settings.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_theme' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'smart_scroll',
            self::format_option_name(
                __( 'Smart scroll', 'ari-stream-quiz' ),

                __( 'If the parameter is enabled, the extension will automatically scroll to next element (question, quiz result and etc.) during quiz session.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_smart_scroll' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'scroll_disable_on_load',
            self::format_option_name(
                __( 'Disable scroll on load', 'ari-stream-quiz' ),

                __( 'If the parameter is enabled, smart scroll will be disabled on first load for quizzes which are configured to start immediately', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_scroll_disable_on_load' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'scroll_duration',
            self::format_option_name(
                __( 'Scroll duration', 'ari-stream-quiz' ),

                __( 'The duration in milliseconds of scrolling animation.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_scroll_duration' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'scroll_offset',
            self::format_option_name(
                __( 'Scroll offset', 'ari-stream-quiz' ),

                __( 'The defined offset in pixels will be added to final top position, useful if template contains fixed elements. Possible to use negative values. It is also possible to define CSS selector for element which height will be used as offset.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_scroll_offset' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'progress_bar',
            self::format_option_name(
                __( 'Progress bar', 'ari-stream-quiz' ),

                __( 'It is possible to hide progress bar or select type of content which will be shown in progress bar.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_progress_bar' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'progress_bar_position',
            self::format_option_name(
                __( 'Progress bar position', 'ari-stream-quiz' ),

                __( 'Select position(s) where progress bar will be shown.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_progress_bar_position' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'save_results',
            self::format_option_name(
                __( 'Save results to database', 'ari-stream-quiz' ),

                __( 'Enable or disable ability to save results to database. The saved results can be viewed on backend and used to generate statistics.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_save_results' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'show_questions_oncomplete',
            self::format_option_name(
                __( 'Show questions at the end', 'ari-stream-quiz' ),

                __( 'If it is enabled, all questions will be shown on quiz final page otherwise questions will be hidden.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_show_questions_oncomplete' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'reload_page',
            self::format_option_name(
                __( 'Reload browser on page changing', 'ari-stream-quiz' ),

                __( 'Reload browser when page in a quiz is changed. Can be used to refresh ads on pages with quizzes.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_reload_page' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        add_settings_field(
            'warning_on_exit',
            self::format_option_name(
                __( 'Warning on exit', 'ari-stream-quiz' ),

                __( 'Warning message will be shown if a user leaves non-completed quiz.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_general_warning_on_exit' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GENERAL_SECTION
        );

        // Trivia quiz parameters
        add_settings_field(
            'show_results',
            self::format_option_name(
                __( 'Show result per question', 'ari-stream-quiz' ),

                __( 'Specify should quiz takers see correct answers and explanations or not.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_triviaquiz_show_results' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_TRIVIAQUIZ_SECTION
        );

        add_settings_field(
            'next_page_timeout',
            self::format_option_name(
                __( 'Move to next page after', 'ari-stream-quiz' ),

                __( 'Define delay in milliseconds after what the next page will be shown. It is used when correct answers are shown immediately after user\'s answer and "Continue" button is not used to navigate to next page.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_triviaquiz_next_page_timeout' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_TRIVIAQUIZ_SECTION
        );

        add_settings_field(
            'lockout_answers',
            self::format_option_name(
                __( 'Lockout single answers', 'ari-stream-quiz' ),

                __( 'If the parameter is activated, answers will be disabled when a user selected an answer for questions with single answer selection otherwise users can change their answers.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_triviaquiz_lockout_answers' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_TRIVIAQUIZ_SECTION
        );

        // E-mail parameters
        add_settings_field(
            'mail_from',
            self::format_option_name(
                __( 'From e-mail', 'ari-stream-quiz' ),

                __( 'Mails which are sent by the plugin will be from the selected e-mail. WordPress uses wordpress@yourdomain.[com] address by default.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_mail_from' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_MAIL_SECTION
        );

        add_settings_field(
            'mail_from_name',
            self::format_option_name(
                __( 'From name', 'ari-stream-quiz' ),

                __( 'Mails will be sent from the entered name.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_mail_from_name' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_MAIL_SECTION
        );

        // MailChimp parameters
        add_settings_field(
            'mailchimp_apikey',
            self::format_option_name(
                __( 'API key', 'ari-stream-quiz' ),

                __( 'API key is required for integration with MailChimp service. Login to your MailChimp account, generate API key, copy it and populate the parameter with it.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_mailchimp_api_key' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_MAILCHIMP_SECTION
        );

        add_settings_field(
            'mailchimp_double_optin',
            self::format_option_name(
                __( 'Double opt-in', 'ari-stream-quiz' ),

                __( 'If the parameter is enabled, a user will be added into a list when accept invitation from a confirmation e-mail.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_mailchimp_double_optin' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_MAILCHIMP_SECTION
        );

        // MailerLite parameters
        add_settings_field(
            'mailerlite_apikey',
            self::format_option_name(
                __( 'API key', 'ari-stream-quiz' ),

                __( 'API key is required for integration with MailerLite service. Login to your MailerLite account, generate API key, copy it and populate the parameter with it.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_mailerlite_api_key' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_MAILERLITE_SECTION
        );

        // AWeber parameters
        add_settings_field(
            'aweber_apikey',
            self::format_option_name(
                __( 'Auth code', 'ari-stream-quiz' ),

                __( 'Auth code is required for integration with AWeber service. Use "Get AWeber auth code" link to grant access for "ARI Stream Quiz - AWeber Integrator" app and get auth code.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_aweber_auth_code' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_AWEBER_SECTION
        );

        // GetResponse parameters
        add_settings_field(
            'getresponse_apikey',
            self::format_option_name(
                __( 'API key', 'ari-stream-quiz' ),

                __( 'API key is required for integration with GetResponse service. Login to your GetResponse account, open "Account Details -> API & OAuth" page, copy an API key and populate the parameter with it.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_getresponse_api_key' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_GETRESPONSE_SECTION
        );

        // Drip parameters
        add_settings_field(
            'drip_apikey',
            self::format_option_name(
                __( 'API key', 'ari-stream-quiz' ),

                __( 'API key is required for integration with Drip service. Login to your Drip account, open "Settings -> My User Settings" page, copy value of "API token" parameter and populate the textbox with it.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_drip_api_key' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_DRIP_SECTION
        );

        add_settings_field(
            'drip_account_id',
            self::format_option_name(
                __( 'Account ID', 'ari-stream-quiz' ),

                __( 'Account ID is required for integration with Drip service. Login to your Drip account, open "Settings -> Site Setup" page, copy "Account ID" and populate the parameter with it.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_drip_account_id' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_DRIP_SECTION
        );

        // ActiveCampaign parameters
        add_settings_field(
            'activecampaign_url',
            self::format_option_name(
                __( 'URL', 'ari-stream-quiz' ),

                __( 'URL is required for integration with ActiveCampaign service. Login to your ActiveCampaign account, open "Account -> My Settings -> Developer" page, copy "API Access -> URL" and populate the parameter with it.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_activecampaign_url' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_ACTIVECAMPAIGN_SECTION
        );

        add_settings_field(
            'activecampaign_apikey',
            self::format_option_name(
                __( 'API key', 'ari-stream-quiz' ),

                __( 'API key is required for integration with ActiveCampaign service. Login to your ActiveCampaign account, open "Account -> My Settings -> Developer" page, copy "API Access -> Key" and populate the parameter with it.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_activecampaign_api_key' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_ACTIVECAMPAIGN_SECTION
        );

        // Constant Contact
        add_settings_field(
            'constantcontact_apikey',
            self::format_option_name(
                __( 'API key', 'ari-stream-quiz' ),

                __( 'API key and Access token are required for integration with ConstantContact service.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_constantcontact_apikey' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_CONSTANTCONTACT_SECTION
        );

        add_settings_field(
            'constantcontact_access_token',
            self::format_option_name(
                __( 'Access token', 'ari-stream-quiz' ),

                __( 'Access token and API key are required for integration with ConstantContact service.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_constantcontact_access_token' ),
            ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE,
            ARISTREAMQUIZ_SETTINGS_CONSTANTCONTACT_SECTION
        );

        // Sharing parameters
        add_settings_field(
            'facebook_app_id',
            self::format_option_name(
                __( 'Facebook App ID', 'ari-stream-quiz' ),

                __( 'App ID is required to use "Facebook" share button. If App ID is not defined, it will not be possible to possible to title, description and image for sharing content.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_share_facebook_app_id' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_SECTION
        );

        add_settings_field(
            'facebook_description_limit',
            self::format_option_name(
                __( 'Facebook description limit', 'ari-stream-quiz' ),

                __( 'Use this parameter to limit maximum number of characters which will be available when share results via Facebook.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_share_facebook_description_limit' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_SECTION
        );

        add_settings_field(
            'share_buttons',
            self::format_option_name(
                __( 'Share buttons', 'ari-stream-quiz' ),

                __( 'The selected share buttons will be shown on quiz final page.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_share_share_buttons' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_SECTION
        );

        // Sharing content - trivia quiz
        add_settings_field(
            'share_trivia_description',
            '',
            array( __CLASS__, 'render_share_triviaquiz_description' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_title',
            __( 'Title on result page', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_title' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_facebook_title',
            __( 'Title of Facebook post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_facebook_title' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_facebook_content',
            __( 'Content of Facebook post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_facebook_content' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_twitter_content',
            __( 'Content of Twitter post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_twitter_content' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_pinterest_content',
            __( 'Content of Pinterest post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_pinterest_content' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_linkedin_title',
            __( 'Title of LinkedIn post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_linkedin_title' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_linkedin_content',
            __( 'Content of LinkedIn post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_linkedin_content' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_vk_title',
            __( 'Title of VKontakte post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_vk_title' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_vk_content',
            __( 'Content of VKontakte post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_vk_content' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_email_subject',
            __( 'Mail subject', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_email_subject' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        add_settings_field(
            'share_trivia_email_body',
            __( 'Mail body', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_triviaquiz_email_body' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_TRIVIACONTENT_SECTION
        );

        // Sharing content - personality quiz
        add_settings_field(
            'share_personality_description',
            '',
            array( __CLASS__, 'render_share_personalityquiz_description' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION
        );

        add_settings_field(
            'share_personality_title',
            __( 'Title on result page', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_personalityquiz_title' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION
        );

        add_settings_field(
            'share_personality_facebook_title',
            __( 'Title of Facebook post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_personalityquiz_facebook_title' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION
        );

        add_settings_field(
            'share_personality_facebook_content',
            __( 'Content of Facebook post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_personalityquiz_facebook_content' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION
        );

        add_settings_field(
            'share_personality_twitter_content',
            __( 'Content of Twitter post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_personalityquiz_twitter_content' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION
        );

        add_settings_field(
            'share_personality_pinterest_content',
            __( 'Content of Pinterest post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_personalityquiz_pinterest_content' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION
        );

        add_settings_field(
            'share_personality_linkedin_title',
            __( 'Title of LinkedIn post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_personalityquiz_linkedin_title' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION
        );

        add_settings_field(
            'share_personality_linkedin_content',
            __( 'Content of LinkedIn post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_personalityquiz_linkedin_content' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION
        );

        add_settings_field(
            'share_personality_vk_title',
            __( 'Title of VKontakte post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_personalityquiz_vk_title' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION
        );

        add_settings_field(
            'share_personality_vk_content',
            __( 'Content of VKontakte post', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_personalityquiz_vk_content' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION
        );

        add_settings_field(
            'share_personality_email_subject',
            __( 'Mail subject', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_personalityquiz_email_subject' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION
        );

        add_settings_field(
            'share_personality_email_body',
            __( 'Mail body', 'ari-stream-quiz' ),
            array( __CLASS__, 'render_share_personalityquiz_email_body' ),
            ARISTREAMQUIZ_SETTINGS_SHARING_PAGE,
            ARISTREAMQUIZ_SETTINGS_SHARING_PERSONALITYCONTENT_SECTION
        );

        // Advanced parameters
        add_settings_field(
            'disable_script_optimization',
            self::format_option_name(
                __( 'Disable script optimization', 'ari-stream-quiz' ),

                __( 'If the parameter is enabled, the plugin will try to avoid optimization of script loading by 3rd party plugins.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_disable_script_optimization' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );

        add_settings_field(
            'facebook_load_sdk',
            self::format_option_name(
                __( 'Load Facebook SDK', 'ari-stream-quiz' ),

                __( 'If template or another plugin also loads Facebook JS SDK, it is possible to disabled SDK loading by the plugin to avoid conflicts.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_facebook_load_sdk' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );

        add_settings_field(
            'lazy_load',
            self::format_option_name(
                __( 'Images lazy loading', 'ari-stream-quiz' ),

                __( 'If the parameter is enabled, images in questions and answers will be loaded only when question page is changed and a loading icon will be shown until images are loaded.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_lazy_load' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );

        add_settings_field(
            'prefetch_quiz_session',
            self::format_option_name(
                __( 'Prefetch quiz session', 'ari-stream-quiz' ),

                __( 'If the parameter is enabled, quiz session will be prepared on quiz preview mode and will not be loaded via AJAX.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_prefetch_quiz_session' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );

        add_settings_field(
            'template_wrap_with_tag',
            self::format_option_name(
                __( 'Wrap templates in tags', 'ari-stream-quiz' ),

                __( 'If the parameter is enabled, template for quiz layout will be wrapped in a HTML tag instead of script tag. It helps to avoid conflicts with some 3rd party plugins.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_template_wrap_with_tag' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );

        add_settings_field(
            'add_meta_tags',
            self::format_option_name(
                __( 'Add meta tags', 'ari-stream-quiz' ),

                __( 'If the parameter is enabled, the plugin will add Open Graph and Twitter meta tags for current quiz.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_add_meta_tags' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );

        add_settings_field(
            'custom_styles',
            self::format_option_name(
                __( 'Custom CSS styles', 'ari-stream-quiz' ),

                __( 'The defined CSS rules will be added on frontend pages with quizzes. Can be used to resolve style conflicts or for customization.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_custom_styles' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );

        add_settings_field(
            'custom_js',
            self::format_option_name(
                __( 'Custom JS code', 'ari-stream-quiz' ),

                __( 'The defined JS code will be added on each page with a quiz.', 'ari-stream-quiz' )
            ),
            array( __CLASS__, 'render_advanced_custom_js' ),
            ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE,
            ARISTREAMQUIZ_SETTINGS_ADVANCED_SECTION
        );
    }

    public static function options() {
        if ( ! is_null( self::$options ) )
            return self::$options;

        self::$options = get_option( ARISTREAMQUIZ_SETTINGS_NAME );

        return self::$options;
    }

    public static function get_option( $name, $default = null ) {
        $options = self::options();

        $val = $default;

        if ( isset( $options[$name] ) ) {
            $val = $options[$name];
        } else if ( is_null( $default) && isset( self::$default_settings[$name] ) ) {
            $val = self::$default_settings[$name];
        }

        return $val;
    }

    public static function format_option_name( $title, $tooltip = '' ) {
        $html = $title;

        if ( $tooltip ) {
            $html = sprintf(
                '<span class="tooltipped" data-position="top" data-tooltip="%2$s">%1$s</span>',
                $title,
                esc_attr( $tooltip )
            );
        }

        return $html;
    }

    public static function render_header( $message, $class = '' ) {
        printf(
            '<div class="section-header %2$s">%1$s</div>',
            $message,
            $class
        );
    }

    public static function render_general_section_info() {
        self::render_header( __( 'Contains global parameters for configuration all types of quizzes', 'ari-stream-quiz' ), 'top' );
    }

    public static function render_update_section_info() {
        self::render_header( __( 'Auto-update', 'ari-stream-quiz' ), 'top' );
    }

    public static function render_shortcode_section_info() {
        self::render_header( __( 'Configure shortcode parameters', 'ari-stream-quiz' ) );
    }

    public static function render_triviaquiz_section_info() {
        self::render_header( __( 'Contains parameters for configuration trivia quizzes', 'ari-stream-quiz' ) );
    }

    public static function render_mail_section_info() {
        self::render_header( __( 'Configure e-mail parameters', 'ari-stream-quiz' ) );
    }

    public static function render_mailchimp_section_info() {
        self::render_header( __( 'The parameters are used for integration with MailChimp service', 'ari-stream-quiz' ) );
    }

    public static function render_mailerlite_section_info() {
        self::render_header( __( 'The parameters are used for integration with MailerLite service', 'ari-stream-quiz' ) );
    }

    public static function render_aweber_section_info() {
        self::render_header( __( 'The parameters are used for integration with AWeber service', 'ari-stream-quiz' ) );
    }

    public static function render_getresponse_section_info() {
        self::render_header( __( 'The parameters are used for integration with GetResponse service', 'ari-stream-quiz' ) );
    }

    public static function render_drip_section_info() {
        self::render_header( __( 'The parameters are used for integration with Drip service', 'ari-stream-quiz' ) );
    }

    public static function render_activecampaign_section_info() {
        self::render_header( __( 'The parameters are used for integration with ActiveCampaign service', 'ari-stream-quiz' ) );
    }

    public static function render_constantcontact_section_info() {
        self::render_header( __( 'The parameters are used for integration with ConstantContact service', 'ari-stream-quiz' ) );
    }

    public static function render_sharing_section_info() {
        self::render_header( __( 'Contains parameters for configuration share buttons', 'ari-stream-quiz' ), 'top' );
    }

    public static function render_sharing_triviacontent_section_info() {
        self::render_header( __( 'This parameters section is used to configure sharing content for trivia quizzes', 'ari-stream-quiz' ) );
    }

    public static function render_share_triviaquiz_description() {
        printf(
            '<div class="settings-description">%s</div>',
            __( 'The following predefined variables are supported: <ul><li><b>{{userScore}}</b> contains number of correctly answered questions</li><li><b>{{userScorePercent}}</b> contains number of correctly answered questions in percent</li><li><b>{{maxScore}}</b> contains number of questions</li><li><b>{{title}}</b> contains title of result template</li><li><b>{{content}}</b> contains content of result template</li><li><b>{{quiz}}</b> contain quiz name</li><li><b>{{url}}</b> contains page URL</li></ul>', 'ari-stream-quiz' )
        );
    }

    public static function render_sharing_personalitycontent_section_info() {
        self::render_header( __( 'This parameters section is used to configure sharing content for personality quizzes', 'ari-stream-quiz' ) );
    }

    public static function render_share_personalityquiz_description() {
        printf(
            '<div class="settings-description">%s</div>',
            __( 'The following predefined variables are supported: <ul><li><b>{{title}}</b> contains personality name</li><li><b>{{score}}</b> contains earned score for personality</li><li><b>{{userScorePercent}}</b> contains number earned points in percent from maximum points for personality</li><li><b>{{userTotalScorePercent}}</b> contains number earned points in percent from earned points for all personalities</li><li><b>{{maxScore}}</b> contains number of maximum points for the selected personality</li><li><b>{{content}}</b> contains personality description</li><li><b>{{quiz}}</b> contains quiz name</li><li><b>{{url}}</b> contains page URL</li></ul>', 'ari-stream-quiz' )
        );
    }

    public static function render_advanced_section_info() {
        self::render_header( __( 'This section contains advanced parameters for fine tuning of the plugin', 'ari-stream-quiz' ), 'top' );
    }

    public static function render_update_api_key() {
        $val = self::get_option( 'api_key' );

        printf(
            '<input type="text" id="tbxApiKey" name="%1$s[api_key]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_shortcode_quiz_hide_title() {
        $val = self::get_option( 'shortcode_quiz_hide_title' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkShortcodeHideTitle" name="%1$s[shortcode_quiz_hide_title]" value="1"%2$s /><label for="chkShortcodeHideTitle"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_shortcode_quiz_col_count() {
        $val = self::get_option( 'shortcode_quiz_column_count' );

        printf(
            '<input type="number" class="input-small center-align" id="tbxShortcodeColCount" name="%1$s[shortcode_quiz_column_count]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_general_theme() {
        $val = Helper::resolve_theme_name( self::get_option( 'theme' ) );
        $themes = Helper::get_themes();

        $html = sprintf(
            '<select id="ddlTheme" name="%1$s[theme]" class="browser-default">',
            ARISTREAMQUIZ_SETTINGS_NAME
        );

        foreach ( $themes as $theme ) {
            $html .= sprintf(
                '<option value="%1$s"%2$s>%1$s</option>',
                $theme,
                $theme == $val ? ' selected="selected"' : ''
            );
        }

        $html .= '</select>';

        echo $html;
    }

    public static function render_general_scroll_offset() {
        $val = self::get_option( 'scroll_offset' );

        printf(
            '<input type="text" class="input-medium center-align" id="tbxScrollOffset" name="%1$s[scroll_offset]" value="%2$s" /> %3$s',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            ''
        );
    }

    public static function render_general_scroll_duration() {
        $val = self::get_option( 'scroll_duration' );

        printf(
            '<input type="number" class="input-small center-align" id="tbxScrollDuration" min="0" name="%1$s[scroll_duration]" value="%2$s" /> %3$s',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            __( 'milliseconds', 'ari-stream-quiz' )
        );
    }

    public static function render_general_smart_scroll() {
        $val = self::get_option( 'smart_scroll' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkSmartScroll" name="%1$s[smart_scroll]" value="1"%2$s /><label for="chkSmartScroll"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_general_scroll_disable_on_load() {
        $val = self::get_option( 'scroll_disable_on_load' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkScrollDisableOnLoad" name="%1$s[scroll_disable_on_load]" value="1"%2$s /><label for="chkScrollDisableOnLoad"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_general_progress_bar() {
        $val = self::get_option( 'progress_bar' );

        $html = sprintf(
            '<select id="ddlProgressBar" name="%1$s[progress_bar]" class="browser-default">',
            ARISTREAMQUIZ_SETTINGS_NAME
        );

        $options = array(
            '' => __( 'Hide', 'ari-stream-quiz' ),

            'empty' => __( 'Without text', 'ari-stream-quiz' ),

            'percent' => __( 'Percent (e.g. 33%)', 'ari-stream-quiz' ),

            'pages' => __( 'Pages (e.g. 2/5)', 'ari-stream-quiz' ),
        );

        foreach ( $options as $key => $label ) {
            $html .= sprintf(
                '<option value="%1$s"%3$s>%2$s</option>',
                $key,
                $label,
                $key == $val ? ' selected="selected"' : ''
            );
        }

        $html .= '</select>';

        echo $html;
    }

    public static function render_general_progress_bar_position() {
        $val = self::get_option( 'progress_bar_position' );

        $html = '';

        $position_list = array(
            'top' => __( 'Top', 'ari-stream-quiz' ),
            'bottom' => __( 'Bottom', 'ari-stream-quiz' ),
        );

        foreach ( $position_list as $position => $label ) {
            $html .= sprintf(
                '<div class="left checkbox-group-item"><input type="checkbox" class="filled-in" id="chkProgressbarPos_%2$s" name="%1$s[progress_bar_position][]" value="%2$s"%3$s /><label class="label" for="chkProgressbarPos_%2$s">%4$s</label></div>',
                ARISTREAMQUIZ_SETTINGS_NAME,
                $position,
                in_array( $position, $val ) ? ' checked="checked"' : '',
                $label
            );
        }

        echo '<div class="clearfix">' . $html . '</div>';
    }

    public static function render_general_save_results() {
        $val = self::get_option( 'save_results' );

        $html = sprintf(
            '<select id="ddlSaveResults" name="%1$s[save_results]" class="browser-default">',
            ARISTREAMQUIZ_SETTINGS_NAME
        );

        $options = array(
            '' => __( 'No', 'ari-stream-quiz' ),

            'all' => __( 'For all users', 'ari-stream-quiz' ),

            'known' => __( 'Ignore anonymous users', 'ari-stream-quiz' ),
        );

        foreach ( $options as $key => $label ) {
            $html .= sprintf(
                '<option value="%1$s"%3$s>%2$s</option>',
                $key,
                $label,
                $key == $val ? ' selected="selected"' : ''
            );
        }

        $html .= '</select>';

        echo $html;
    }

    public static function render_general_show_questions_oncomplete() {
        $val = self::get_option( 'show_questions_oncomplete' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkShowQuestionsOnComplete" name="%1$s[show_questions_oncomplete]" value="1"%2$s /><label for="chkShowQuestionsOnComplete"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_general_reload_page() {
        $val = self::get_option( 'reload_page' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkReloadPage" name="%1$s[reload_page]" value="1"%2$s /><label for="chkReloadPage"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_general_warning_on_exit() {
        $val = self::get_option( 'warning_on_exit' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkWarningOnExit" name="%1$s[warning_on_exit]" value="1"%2$s /><label for="chkWarningOnExit"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_triviaquiz_lockout_answers() {
        $val = self::get_option( 'lockout_answers' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkLockoutAnswers" name="%1$s[lockout_answers]" value="1"%2$s /><label for="chkLockoutAnswers"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_triviaquiz_show_results() {
        $val = self::get_option( 'show_results' );

        $html = sprintf(
            '<select id="ddlTriviaShowResults" name="%1$s[show_results]" class="browser-default">',
            ARISTREAMQUIZ_SETTINGS_NAME
        );

        $options = array(
            '' => __( 'No', 'ari-stream-quiz' ),

            'immediately' => __( 'Immediately after user answer', 'ari-stream-quiz' ),

            'on_complete' => __( 'When quiz is completed', 'ari-stream-quiz' ),
        );

        foreach ( $options as $key => $label ) {
            $html .= sprintf(
                '<option value="%1$s"%3$s>%2$s</option>',
                $key,
                $label,
                $key == $val ? ' selected="selected"' : ''
            );
        }

        $html .= '</select>';

        echo $html;
    }

    public static function render_triviaquiz_next_page_timeout() {
        $val = self::get_option( 'next_page_timeout' );

        printf(
            '<input type="number" class="input-medium center-align" id="tbxPageTimeout" min="0" name="%1$s[next_page_timeout]" value="%2$s" /> %3$s',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            __( 'milliseconds', 'ari-stream-quiz' )
        );
    }

    public static function render_mail_from() {
        $val = self::get_option( 'mail_from' );

        printf(
            '<input type="text" id="tbxMailFrom" name="%1$s[mail_from]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_mail_from_name() {
        $val = self::get_option( 'mail_from_name' );

        printf(
            '<input type="text" id="tbxMailFromName" name="%1$s[mail_from_name]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_mailchimp_api_key() {
        $val = self::get_option( 'mailchimp_apikey' );

        printf(
            '<div><input type="text" id="tbxMailchimpKey" name="%1$s[mailchimp_apikey]" value="%2$s" /></div><div class="right-align"><a href="http://kb.mailchimp.com/integrations/api-integrations/about-api-keys" target="_blank">%3$s</a></div>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            __( 'Where get API key?', 'ari-stream-quiz' )
        );
    }

    public static function render_mailchimp_double_optin() {
        $val = self::get_option( 'mailchimp_double_optin' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkMailchimpDoubleOptin" name="%1$s[mailchimp_double_optin]" value="1"%2$s /><label for="chkMailchimpDoubleOptin"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_mailerlite_api_key() {
        $val = self::get_option( 'mailerlite_apikey' );

        printf(
            '<div><input type="text" id="tbxMailerLiteKey" name="%1$s[mailerlite_apikey]" value="%2$s" /></div><div class="right-align"><a href="https://app.mailerlite.com/subscribe/api" target="_blank">%3$s</a></div>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            __( 'Get API key', 'ari-stream-quiz' )
        );
    }

    public static function render_aweber_auth_code() {
        $val = self::get_option( 'aweber_auth_code' );
        $app_id = self::get_option( 'aweber_app_id' );

        printf(
            '<div><input type="text" id="tbxAWeberAuthCode" name="%1$s[aweber_auth_code]" value="%2$s" /></div><div class="right-align"><a href="https://auth.aweber.com/1.0/oauth/authorize_app/%3$s" target="_blank" id="linkAWeberAuthCode">%4$s</a></div>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            $app_id,
            __( 'Get AWeber auth code', 'ari-stream-quiz' )
        );
    }

    public static function render_constantcontact_apikey() {
        $val = self::get_option( 'constantcontact_apikey' );

        printf(
            '<div><input type="text" id="tbxConstantContactApiKey" name="%1$s[constantcontact_apikey]" value="%2$s" /></div><div class="right-align"><a href="https://developer.constantcontact.com/api-keys.html" target="_blank">%3$s</a></div>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            __( 'Where get ConstantContact API key?', 'ari-stream-quiz' )
        );
    }

    public static function render_constantcontact_access_token() {
        $val = self::get_option( 'constantcontact_access_token' );

        printf(
            '<div><input type="text" id="tbxConstantContactAccessToken" name="%1$s[constantcontact_access_token]" value="%2$s" /></div><div class="right-align"><a href="https://developer.constantcontact.com/api-keys.html" target="_blank">%3$s</a></div>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            __( 'Where get ConstantContact access token?', 'ari-stream-quiz' )
        );
    }

    public static function render_getresponse_api_key() {
        $val = self::get_option( 'getresponse_apikey' );

        printf(
            '<div><input type="text" id="tbxGetresponseKey" name="%1$s[getresponse_apikey]" value="%2$s" /></div><div class="right-align"><a href="https://support.getresponse.com/videos/where-do-i-find-the-api-key" target="_blank">%3$s</a></div>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            __( 'Where get API key?', 'ari-stream-quiz' )
        );
    }

    public static function render_drip_api_key() {
        $val = self::get_option( 'drip_apikey' );

        printf(
            '<div><input type="text" id="tbxDripKey" name="%1$s[drip_apikey]" value="%2$s" /></div><div class="right-align"><a href="http://www.ari-soft.com/docs/wordpress/ari-stream-quiz-pro/v1/en/index.html#admin_page_settings_drip" target="_blank">%3$s</a></div>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            __( 'Where get API key?', 'ari-stream-quiz' )
        );
    }

    public static function render_drip_account_id() {
        $val = self::get_option( 'drip_account_id' );

        printf(
            '<div><input type="text" id="tbxDripAccount" name="%1$s[drip_account_id]" value="%2$s" /></div><div class="right-align"><a href="http://www.ari-soft.com/docs/wordpress/ari-stream-quiz-pro/v1/en/index.html#admin_page_settings_drip" target="_blank">%3$s</a></div>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            __( 'Where get account ID?', 'ari-stream-quiz' )
        );
    }

    public static function render_activecampaign_url() {
        $val = self::get_option( 'activecampaign_url' );

        printf(
            '<div><input type="text" id="tbxActiveCampaignUrl" name="%1$s[activecampaign_url]" value="%2$s" /></div><div class="right-align"><a href="http://www.ari-soft.com/docs/wordpress/ari-stream-quiz-pro/v1/en/index.html#admin_page_settings_activecampaign" target="_blank">%3$s</a></div>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            __( 'Where get account API URL?', 'ari-stream-quiz' )
        );
    }

    public static function render_activecampaign_api_key() {
        $val = self::get_option( 'activecampaign_apikey' );

        printf(
            '<div><input type="text" id="tbxActiveCampaignKey" name="%1$s[activecampaign_apikey]" value="%2$s" /></div><div class="right-align"><a href="http://www.ari-soft.com/docs/wordpress/ari-stream-quiz-pro/v1/en/index.html#admin_page_settings_activecampaign" target="_blank">%3$s</a></div>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val ),
            __( 'Where get API key?', 'ari-stream-quiz' )
        );
    }

    public static function render_advanced_disable_script_optimization() {
        $val = self::get_option( 'disable_script_optimization' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkDisableScriptOptimization" name="%1$s[disable_script_optimization]" value="1"%2$s /><label for="chkDisableScriptOptimization"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_share_facebook_app_id() {
        $val = self::get_option( 'facebook_app_id' );

        printf(
            '<input type="text" id="tbxFacebookAppId" name="%1$s[facebook_app_id]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_facebook_description_limit() {
        $val = self::get_option( 'facebook_description_limit' );

        printf(
            '<input type="text" type="number" class="input-small center-align" min="0" id="tbxFacebookDescrLimit" name="%1$s[facebook_description_limit]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_share_buttons() {
        $val = self::get_option( 'share_buttons' );

        $html = '';

        $share_buttons = array(
            'facebook' => __( 'Facebook', 'ari-stream-quiz' ),
            'twitter' => __( 'Twitter', 'ari-stream-quiz' ),
            'gplus' => __( 'Google+', 'ari-stream-quiz' ),
            'pinterest' => __( 'Pinterest', 'ari-stream-quiz' ),
            'linkedin' => __( 'LinkedIn', 'ari-stream-quiz' ),
            'vk' => __( 'VKontakte', 'ari-stream-quiz' ),
            'email' => __( 'Email', 'ari-stream-quiz' ),
        );

        foreach ( $share_buttons as $share_button => $label ) {
            $html .= sprintf(
                '<div class="left checkbox-group-item"><input type="checkbox" class="filled-in" id="chkShareButton_%2$s" name="%1$s[share_buttons][]" value="%2$s"%3$s /><label class="label" for="chkShareButton_%2$s">%4$s</label></div>',
                ARISTREAMQUIZ_SETTINGS_NAME,
                $share_button,
                in_array( $share_button, $val ) ? ' checked="checked"' : '',
                $label
            );
        }

        echo '<div class="clearfix">' . $html . '</div>';
    }

    public static function render_share_triviaquiz_title() {
        $val = self::get_option( 'share_trivia_title' );

        printf(
            '<input type="text" id="tbxShareTriviaTitle" name="%1$s[share_trivia_title]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_facebook_title() {
        $val = self::get_option( 'share_trivia_facebook_title' );

        printf(
            '<input type="text" id="tbxShareTriviaFacebookTitle" name="%1$s[share_trivia_facebook_title]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_facebook_content() {
        $val = self::get_option( 'share_trivia_facebook_content' );

        printf(
            '<input type="text" id="tbxShareTriviaFacebookContent" name="%1$s[share_trivia_facebook_content]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_twitter_content() {
        $val = self::get_option( 'share_trivia_twitter_content' );

        printf(
            '<input type="text" id="tbxShareTriviaTwitterContent" name="%1$s[share_trivia_twitter_content]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_pinterest_content() {
        $val = self::get_option( 'share_trivia_pinterest_content' );

        printf(
            '<input type="text" id="tbxShareTriviaPinterestContent" name="%1$s[share_trivia_pinterest_content]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_linkedin_title() {
        $val = self::get_option( 'share_trivia_linkedin_title' );

        printf(
            '<input type="text" id="tbxShareTriviaLinkedinTitle" name="%1$s[share_trivia_linkedin_title]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_linkedin_content() {
        $val = self::get_option( 'share_trivia_linkedin_content' );

        printf(
            '<input type="text" id="tbxShareTriviaLinkedinContent" name="%1$s[share_trivia_linkedin_content]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_vk_title() {
        $val = self::get_option( 'share_trivia_vk_title' );

        printf(
            '<input type="text" id="tbxShareTriviaVKTitle" name="%1$s[share_trivia_vk_title]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_vk_content() {
        $val = self::get_option( 'share_trivia_vk_content' );

        printf(
            '<input type="text" id="tbxShareTriviaVKContent" name="%1$s[share_trivia_vk_content]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_email_subject() {
        $val = self::get_option( 'share_trivia_email_subject' );

        printf(
            '<input type="text" id="tbxShareTriviaEmailSubject" name="%1$s[share_trivia_email_subject]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_triviaquiz_email_body() {
        $val = self::get_option( 'share_trivia_email_body' );

        printf(
            '<input type="text" id="tbxShareTriviaEmailBody" name="%1$s[share_trivia_email_body]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_personalityquiz_title() {
        $val = self::get_option( 'share_personality_title' );

        printf(
            '<input type="text" id="tbxSharePersonalityTitle" name="%1$s[share_personality_title]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_personalityquiz_facebook_title() {
        $val = self::get_option( 'share_personality_facebook_title' );

        printf(
            '<input type="text" id="tbxSharePersonalityFacebookTitle" name="%1$s[share_personality_facebook_title]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_personalityquiz_facebook_content() {
        $val = self::get_option( 'share_personality_facebook_content' );

        printf(
            '<input type="text" id="tbxSharePersonalityFacebookContent" name="%1$s[share_personality_facebook_content]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_personalityquiz_twitter_content() {
        $val = self::get_option( 'share_personality_twitter_content' );

        printf(
            '<input type="text" id="tbxSharePersonalityTwitterContent" name="%1$s[share_personality_twitter_content]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_personalityquiz_pinterest_content() {
        $val = self::get_option( 'share_personality_pinterest_content' );

        printf(
            '<input type="text" id="tbxSharePersonalityPinterestContent" name="%1$s[share_personality_pinterest_content]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_personalityquiz_linkedin_title() {
        $val = self::get_option( 'share_personality_linkedin_title' );

        printf(
            '<input type="text" id="tbxSharePersonalityLinkedinTitle" name="%1$s[share_personality_linkedin_title]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_personalityquiz_linkedin_content() {
        $val = self::get_option( 'share_personality_linkedin_content' );

        printf(
            '<input type="text" id="tbxSharePersonalityLinkedinContent" name="%1$s[share_personality_linkedin_content]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_personalityquiz_vk_title() {
        $val = self::get_option( 'share_personality_vk_title' );

        printf(
            '<input type="text" id="tbxSharePersonalityVKTitle" name="%1$s[share_personality_vk_title]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_personalityquiz_vk_content() {
        $val = self::get_option( 'share_personality_vk_content' );

        printf(
            '<input type="text" id="tbxSharePersonalityVKContent" name="%1$s[share_personality_vk_content]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_personalityquiz_email_subject() {
        $val = self::get_option( 'share_personality_email_subject' );

        printf(
            '<input type="text" id="tbxSharePersonalityEmailSubject" name="%1$s[share_personality_email_subject]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_share_personalityquiz_email_body() {
        $val = self::get_option( 'share_personality_email_body' );

        printf(
            '<input type="text" id="tbxSharePersonalityEmailBody" name="%1$s[share_personality_email_body]" value="%2$s" />',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_advanced_facebook_load_sdk() {
        $val = self::get_option( 'facebook_load_sdk' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkFacebookLoadSDK" name="%1$s[facebook_load_sdk]" value="1"%2$s /><label for="chkFacebookLoadSDK"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_advanced_custom_styles() {
        $val = self::get_option( 'custom_styles' );

        printf(
            '<textarea id="tbxCustomStyles" name="%1$s[custom_styles]">%2$s</textarea>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_advanced_custom_js() {
        $val = self::get_option( 'custom_js' );

        printf(
            '<textarea id="tbxCustomJs" name="%1$s[custom_js]">%2$s</textarea>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            esc_attr( $val )
        );
    }

    public static function render_advanced_lazy_load() {
        $val = self::get_option( 'lazy_load' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkLazyLoad" name="%1$s[lazy_load]" value="1"%2$s /><label for="chkLazyLoad"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_advanced_prefetch_quiz_session() {
        $val = self::get_option( 'prefetch_quiz_session' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkPrefetchQuizSession" name="%1$s[prefetch_quiz_session]" value="1"%2$s /><label for="chkPrefetchQuizSession"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_advanced_template_wrap_with_tag() {
        $val = self::get_option( 'template_wrap_with_tag' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkTemplateWrapWithTag" name="%1$s[template_wrap_with_tag]" value="1"%2$s /><label for="chkTemplateWrapWithTag"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function render_advanced_add_meta_tags() {
        $val = self::get_option( 'add_meta_tags' );

        printf(
            '<input type="checkbox" class="filled-in" id="chkAddMetaTags" name="%1$s[add_meta_tags]" value="1"%2$s /><label for="chkAddMetaTags"> </label>',
            ARISTREAMQUIZ_SETTINGS_NAME,
            $val ? ' checked="checked"' : ''
        );
    }

    public static function sanitize( $input ) {
        $new_input = array();

        foreach ( self::$default_settings as $key => $val ) {
            $type = gettype( $val );

            if ( 'boolean' == $type && ! isset( $input[$key] ) ) {
                $new_input[$key] = false;
            } else if ( 'array' == $type && ! isset( $input[$key] ) ) {
                $new_input[$key] = array();
            } else if ( isset( $input[$key] ) ) {
                $input_val = $input[$key];
                $filtered_val = null;
                switch ( $type ) {
                    case 'boolean':
                        $filtered_val = (bool) $input_val;
                        break;

                    case 'integer':
                        $filtered_val = intval( $input_val, 10 );
                        break;

                    case 'double':
                        $filtered_val = floatval( $input_val );
                        break;

                    case 'array':
                        $filtered_val = $input_val;
                        break;

                    case 'string':
                        $filtered_val = trim( $input_val );
                        break;
                }

                if ( ! is_null( $filtered_val) ) {
                    $new_input[$key] = $filtered_val;
                }
            }
        }

        $prev_api_key = Settings::get_option( 'api_key' );
        if ( ! isset( $new_input['api_key'] ) || $new_input['api_key'] != $prev_api_key ) {
            delete_site_option( 'external_updates-' . ARISTREAMQUIZ_SLUG );
        }

        $new_input['aweber_access_token'] = Settings::get_option( 'aweber_access_token' );
        $new_input['aweber_access_secret'] = Settings::get_option( 'aweber_access_secret' );
        $new_input['aweber_consumer_key'] = Settings::get_option( 'aweber_consumer_key' );
        $new_input['aweber_consumer_secret'] = Settings::get_option( 'aweber_consumer_secret' );

        if ( isset( $new_input['aweber_auth_code'] ) ) {
            self::handle_aweber_credentials( $new_input['aweber_auth_code'], $new_input, $input );
        }

        return $new_input;
    }

    private static function handle_aweber_credentials( $new_auth_code, &$new_input, $input ) {
        if ( empty( $new_auth_code ) ) {
            return ;
        }

        $prev_aweber_auth_code = self::get_option( 'aweber_auth_code' );

        $access_key = self::get_option( 'aweber_access_token' );
        $access_secret = self::get_option( 'aweber_access_secret' );

        if ( $prev_aweber_auth_code == $new_auth_code && ! empty( $access_key ) && ! empty( $access_secret ) ) {
            return ;
        }

        if ( ! class_exists('AWeberAPI') ) require_once ARISTREAMQUIZ_3RDPARTY_PATH . 'aweber/aweber/aweber_api/aweber.php';

        try {
            $credentials = \AWeberAPI::getDataFromAweberID( $new_auth_code );
            list( $req_consumer_key, $req_consumer_secret, $req_access_key, $req_access_secret ) = $credentials;

            $new_input['aweber_access_token'] = $req_access_key;
            $new_input['aweber_access_secret'] = $req_access_secret;
			$new_input['aweber_consumer_key'] = $req_consumer_key;
			$new_input['aweber_consumer_secret'] = $req_consumer_secret;
        } catch (\Exception $ex) {
            $new_input['aweber_auth_code'] = '';
        }
    }
}
