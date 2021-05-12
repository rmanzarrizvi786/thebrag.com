<?php
namespace Ari_Stream_Quiz\Views\Quiz_Session;

use Ari_Stream_Quiz\Views\Site_Base as Site_Base;
use Ari_Stream_Quiz\Helpers\Helper as Helper;
use Ari_Stream_Quiz\Helpers\Settings as Settings;
use \Ari\Utils\Request as Request;
use Ari\Wordpress\Helper as WP_Helper;

class Html extends Site_Base {
    protected static $js_l10n_loaded = false;

    protected static $custom_js_loaded = false;

    public $share_buttons;

    public $need_to_load_facebook_sdk;

    public $progress_bar_position;

    public $show_progress_bar;

    public function display( $tmpl = null ) {
        $data = $this->get_data();
        if ( is_null( $tmpl ) || $tmpl == 'default' ) {
            if ( empty( $data['quiz'] ) )
                $tmpl = 'error';
        }

        if ( 'error' == $tmpl ) {
            parent::display( $tmpl );
            return ;
        }

        $quiz = $data['quiz'];
        $progress_bar = Settings::get_option( 'progress_bar' );
        $progress_bar_position = Settings::get_option( 'progress_bar_position' );

        $this->need_to_load_facebook_sdk = $this->need_to_load_facebook_sdk( $quiz );
        $this->progress_bar_position = $progress_bar_position;
        $this->show_progress_bar = ! empty( $progress_bar ) && count( $progress_bar_position ) > 0;
        $id = $this->id();

        $options = array(
            'prefix' => $id,

            'column_count' => $data['column_count'],

            'data' => $this->prepare_client_data(),

            'reloadUrl' => Request::root_url() . add_query_arg( array( 'asq_reload' => '__ASQ_RELOAD_KEY__' , 'asq_replay' => false ) ),

            'tryAgainUrl' => Request::root_url() . add_query_arg( array( 'asq_replay' => $quiz->quiz_id, 'asq_reload' => false ) ),

            'nonce' => wp_create_nonce( 'ari-stream-quiz-ajax-action' ),

            'ajaxUrl' => admin_url( 'admin-ajax.php?action=ari_stream_quiz' ),

            'smartScroll' => (bool)Settings::get_option( 'smart_scroll' ),

            'smartScrollDisableOnLoad' => (bool)Settings::get_option( 'scroll_disable_on_load' ) && $quiz->start_immediately,

            'lazyLoad' => (bool)Settings::get_option( 'lazy_load' ),

            'reloadPage' => (bool)Settings::get_option( 'reload_page' ),

            'warningOnExit' => (bool)Settings::get_option( 'warning_on_exit' ),

            'scrollOnLoad' => $data['navigate_to'],

            'scroll' => array(
                'duration' => Settings::get_option( 'scroll_duration' ),

                'options' => array(
                    'offset' => Settings::get_option( 'scroll_offset' )
                ),
            ),

            'messages' => array(
                'correct' => __( 'Correct', 'ari-stream-quiz' ),

                'wrong' => __( 'Wrong', 'ari-stream-quiz' ),
            )
        );

        $js_l10n = array(
            'warningOnExit' => __( 'The quiz is not completed, do you want to leave the page?', 'ari-stream-quiz' ),

            'quizLoadingFailed' => __( 'The quiz can not be loaded. Do you want to reload the page and try again?', 'ari-stream-quiz' ),
        );

        $inline_scripts = $data['inline_scripts'];

        if ( ! $inline_scripts ) {
            wp_enqueue_script( 'ari-quiz' );

            wp_localize_script( 'ari-quiz', 'ARI_STREAM_QUIZ_' . $id, $options );

            if ( ! self::$js_l10n_loaded ) {
                wp_localize_script( 'ari-quiz', 'ARI_STREAM_QUIZ_L10N', $js_l10n );

                self::$js_l10n_loaded = true;
            }
        } else {
            $script_vars = array();

            if ( ! self::$js_l10n_loaded ) {
                $script_vars['ARI_STREAM_QUIZ_L10N'] = $js_l10n;
            }

            $script_vars['ARI_STREAM_QUIZ_' . $id] = $options;
            $this->script_vars = $script_vars;
        }

        $this->share_buttons = Settings::get_option( 'share_buttons' );

        $custom_js = Settings::get_option( 'custom_js' );
        if ( strlen( $custom_js ) > 0 )
            $custom_js = trim( $custom_js );

        if ( strlen( $custom_js ) > 0 ) {
            add_action('wp_footer', function() use ( $custom_js ) {
                if ( self::$custom_js_loaded )
                    return ;

                printf('<script>%1$s</script>', $custom_js);

                self::$custom_js_loaded = true;
            }, 100);
        }

        parent::display( $tmpl );
    }

    public function get_theme() {
        if ( ! is_null( $this->theme ) ) {
            return $this->theme;
        }

        $data = $this->get_data();
        $quiz = $data['quiz'];

        if ( empty( $quiz->theme ) )
            return parent::get_theme();

        $theme = Helper::resolve_theme_name( $quiz->theme );
        $theme_class_name = \Ari_Loader::prepare_name( $theme );
        $theme_class = '\\Ari_Stream_Quiz_Themes\\' . $theme_class_name . '\\Loader';
        
        if ( ! class_exists( $theme_class ) ) {
            $theme_class = '\\Ari_Stream_Quiz_Themes\\Generic_Loader';
            $this->theme = new $theme_class( $theme );
        } else {
            $this->theme = new $theme_class();
        }

        return $this->theme;
    }

    protected function need_to_load_facebook_sdk( $quiz ) {
        if ( ! Settings::get_option( 'facebook_load_sdk' ) )
            return false;

        $facebook_app_id = Settings::get_option( 'facebook_app_id' );
        if ( empty( $facebook_app_id ) )
            return false;

        return $this->is_facebook_integration_required( $quiz, false );
    }

    protected function is_facebook_integration_required( $quiz, $check_share_button = true ) {
        if ( $quiz->quiz_meta->share_to_see ) {
            return true;
        }

        if ( $check_share_button && $quiz->quiz_meta->show_share_buttons ) {
            $share_buttons = Settings::get_option( 'share_buttons' );

            if ( in_array( 'facebook', $share_buttons ) ) {
                return true;
            }
        }

        return false;
    }

    protected function prepare_client_data() {
        $model_data = $this->get_data();

        $quiz = $model_data['quiz'];

        $progress_bar = Settings::get_option( 'progress_bar' );
        $progress_bar_message = '';

        if ( $progress_bar == 'percent' ) {
            $progress_bar_message = '{{percent}}%';
        } else if ( $progress_bar == 'pages' ) {
            $progress_bar_message = '{{completedPages}} / {{pageCount}}';
        }

        $facebook_integration = $this->is_facebook_integration_required( $quiz );
        $facebook_integration_app = $facebook_integration && $this->is_facebook_integration_required( $quiz, false );
        $facebook_app_id = $facebook_integration_app ? Settings::get_option( 'facebook_app_id' ) : '';

        $current_url = get_permalink();

        $data = array(
            'quizId' => $quiz->quiz_id,

            'quizType' => $quiz->quiz_type,

            'startImmediately' => $quiz->start_immediately || $model_data['replay'],

            'forceToShare' => $quiz->quiz_meta->share_to_see,

            'collectData' => $quiz->collect_data(),

            'processUserData' => $quiz->need_to_process_user_data(),

            'collectDataOptional' => $quiz->collect_data_optional,

            'collectName' => $quiz->collect_name,

            'collectEmail' => $quiz->collect_email,

            'askConfirmation' => $quiz->quiz_meta->lead_form->ask_confirmation,

            'changePageTimeout' => Settings::get_option( 'next_page_timeout' ),

            'progressMessage' => $progress_bar_message,

            'saveResult' => Settings::get_option( 'save_results' ),

            'pages' => array(),

            'share' => array(
                'url' => $current_url,

                'title' => $quiz->quiz_title,

                'description' => $quiz->quiz_description,

                'image' => $quiz->quiz_image_id > 0 ? $quiz->quiz_image->url : null,
            ),

            'facebook' => array(
                'enabled' => $facebook_integration,

                'settings' => array(
                    'appId' => $facebook_app_id,
                )
            ),

            'lockoutAnswers' => (bool)Settings::get_option( 'lockout_answers', true ),

            'showQuestionsOnComplete' => (bool)Settings::get_option( 'show_questions_oncomplete' ),
        );

        $session = $model_data['session'];
        if ( ! empty( $session ) ) {
            $data['sessionKey'] = $session->session_key;
            $data['quizMeta'] = $session->data->quiz_meta;
            $data['questionCount'] = $session->data->question_count;
            $data['pageCount'] = $session->data->page_count;
            $data['pages'] = $session->data->pages;

            if ( ! empty( $session->state ) ) {
                $data['initState'] = $session->state;

                if ( $session->is_completed() )
                    $data['session_completed'] = true;
            }

            $prefetched_session = (bool)Settings::get_option( 'prefetch_quiz_session' );
            $data['prefetched'] = $prefetched_session;

            if ( $quiz->quiz_meta->shortcode ) {
                $quiz_session_meta =& $data['quizMeta'];
                if ( ARISTREAMQUIZ_QUIZTYPE_TRIVIA == $quiz->quiz_type ) {
                    foreach ( $quiz_session_meta->resultTemplates as $result_template ) {
                        $result_template->content = WP_Helper::do_shortcode( $result_template->content );
                    }
                } else {
                    foreach ( $quiz_session_meta->personalities as $personality ) {
                        $personality->content = WP_Helper::do_shortcode( $personality->content );
                    }
                }

                $session = Helper::prepare_quiz_session_questions( $session );
            }
        }

        if ( ARISTREAMQUIZ_QUIZTYPE_TRIVIA == $quiz->quiz_type ) {
            $data['useContinueButton'] = ( $quiz->use_paging && $quiz->quiz_meta->paging_nav_button );
            $data['showResults'] = Settings::get_option( 'show_results' );
        } else if ( ARISTREAMQUIZ_QUIZTYPE_PERSONALITY == $quiz->quiz_type ) {
            $data['personalityCount'] = $quiz->main_personality_count();
        }

        return base64_encode( json_encode( $data, JSON_NUMERIC_CHECK ) );
    }
}
