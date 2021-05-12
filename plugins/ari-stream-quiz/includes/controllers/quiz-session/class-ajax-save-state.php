<?php
namespace Ari_Stream_Quiz\Controllers\Quiz_Session;

use Ari\Controllers\Ajax as Ajax_Controller;
use Ari\Utils\Request as Request;

class Ajax_Save_State extends Ajax_Controller {
    protected function process_request() {
        if ( ! check_ajax_referer( 'ari-stream-quiz-ajax-action', ARISTREAMQUIZ_AJAX_NONCE_FIELD, false ) )
            return false;

        $result = false;
        if ( Request::exists( 'state' ) && Request::exists( 'quiz_id' ) ) {
            $quiz_id = (int)Request::get_var( 'quiz_id', 0, 'num' );

            if ( $quiz_id > 0 ) {
                $state = json_decode(
                    base64_decode(
                        stripslashes(
                            Request::get_var( 'state' )
                        )
                    ),
                    true
                );

                if ( JSON_ERROR_NONE == json_last_error() ) {
                    if ( is_array( $state ) && count( $state ) > 0 && isset( $state[$quiz_id] ) ) {
                        $model = $this->model( 'Quiz_Session' );
                        $sessions = $model->save_sessions( $state );

                        if ( $sessions['result'] && isset( $sessions['sessions'][$quiz_id] ) ) {
                            $result = $this->get_reload_key( $quiz_id, $sessions['sessions'] );
                        }
                    }
                }
            }
        }

        return $result;
    }

    private function get_reload_key( $quiz_id, $sessions ) {
        $key_parts = array(
            $quiz_id . ':' . $sessions[$quiz_id]
        );

        foreach ( $sessions as $session_quiz_id => $session_key ) {
            if ( $quiz_id != $session_quiz_id )
                $key_parts[] = $session_quiz_id . ':' . $session_key;
        }

        $key_parts[] = time();

        return base64_encode( join( '|', $key_parts ) );
    }
}
