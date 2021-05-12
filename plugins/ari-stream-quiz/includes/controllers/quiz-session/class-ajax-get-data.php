<?php
namespace Ari_Stream_Quiz\Controllers\Quiz_Session;

use Ari\Controllers\Ajax as Ajax_Controller;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Helpers\Statistics_Activity as Statistics_Activity;
use Ari_Stream_Quiz\Helpers\Helper as Helper;
use Ari\Wordpress\Helper as WP_Helper;

class Ajax_Get_Data extends Ajax_Controller {
    function __construct( $options = array() ) {
        parent::__construct( $options );

        $this->options->json_encode_options = JSON_NUMERIC_CHECK;
    }

    protected function process_request() {
        if ( ! check_ajax_referer( 'ari-stream-quiz-ajax-action', ARISTREAMQUIZ_AJAX_NONCE_FIELD, false ) )
            return false;

        if ( ! Request::exists( 'quiz_id' ) )
            return false;

        $quiz_id = (int)Request::get_var( 'quiz_id', 0, 'num' );

        $model = $this->model();
        $session = $model->create_session( $quiz_id );

        if ( empty( $session ) )
            return false;

        $quiz_model = $this->model( 'Quiz' );
        $quiz = $quiz_model->get_quiz( $quiz_id );

        if ( $quiz && $quiz->quiz_meta->shortcode ) {
            if ( ARISTREAMQUIZ_QUIZTYPE_TRIVIA == $quiz->quiz_type ) {
                foreach ( $session->data->quiz_meta->resultTemplates as $result_template ) {
                    $result_template->content = WP_Helper::do_shortcode( $result_template->content );
                }
            } else {
                foreach ( $session->data->quiz_meta->personalities as $personality ) {
                    $personality->content = WP_Helper::do_shortcode( $personality->content );
                }
            }

            $session = Helper::prepare_quiz_session_questions( $session );
        }

        $statistics_model = $this->model( 'Statistics' );
        $statistics_model->log_activity(
            $quiz_id,
            Statistics_Activity::START
        );

        return array(
            'sessionKey' => $session->session_key,

            'data' => $session->data,
        );
    }
}
