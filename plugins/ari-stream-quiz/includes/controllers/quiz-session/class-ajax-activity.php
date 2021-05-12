<?php
namespace Ari_Stream_Quiz\Controllers\Quiz_Session;

use Ari\Controllers\Ajax as Ajax_Controller;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Helpers\Statistics_Activity as Statistics_Activity;

class Ajax_Activity extends Ajax_Controller {
    protected function process_request() {
        $result = false;
        if ( ! check_ajax_referer( 'ari-stream-quiz-ajax-action', ARISTREAMQUIZ_AJAX_NONCE_FIELD, false ) )
            return $result;

        if ( ! Request::exists( 'activity' ) || ! Request::exists( 'quiz_id' ) || ! Request::exists( 'session_key' ) )
            return $result;

        $quiz_id = (int)Request::get_var( 'quiz_id', 0, 'num' );
        $activity = Request::get_var( 'activity' );
        $session_key = Request::get_var( 'session_key' );

        if ( $quiz_id < 1 || empty( $activity ) || empty( $session_key ) )
            return $result;

        $data = stripslashes_deep( Request::get_var( 'data' ) );
        $data = strlen( $data ) > 0 ? json_decode( $data, true ) : null;

        $session_model = $this->model( 'Quiz_Session' );
        $result = $session_model->add_activity( $session_key, $activity, $quiz_id, $data );

        if ( $result ) {
            $statistics_model = $this->model( 'Statistics' );
            $result = $statistics_model->log_activity( $quiz_id, Statistics_Activity::convert_quiz_activity( $activity ) );
        }

        return $result;
    }
}
