<?php
namespace Ari_Stream_Quiz\Controllers\Quiz_Session;

use Ari\Controllers\Ajax as Ajax_Controller;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Helpers\Statistics_Activity as Statistics_Activity;

class Ajax_Start extends Ajax_Controller {
    protected function process_request() {
        $result = false;
        if ( ! check_ajax_referer( 'ari-stream-quiz-ajax-action', ARISTREAMQUIZ_AJAX_NONCE_FIELD, false ) )
            return $result;

        if ( ! Request::exists( 'quiz_id' ) || ! Request::exists( 'session_key' ) )
            return $result;

        $quiz_id = (int)Request::get_var( 'quiz_id', 0, 'num' );
        $session_key = Request::get_var( 'session_key' );
        $date = (int)Request::get_var( 'date', 0, 'num' );

        if ( $quiz_id < 1 || empty( $session_key ) )
            return $result;

        $session_model = $this->model( 'Quiz_Session' );
        $result = $session_model->start( $session_key, $quiz_id, $date );

        if ( $result ) {
            $statistics_model = $this->model( 'Statistics' );
            $result = $statistics_model->log_activity( $quiz_id, Statistics_Activity::START );
        }

        return $result;
    }
}
