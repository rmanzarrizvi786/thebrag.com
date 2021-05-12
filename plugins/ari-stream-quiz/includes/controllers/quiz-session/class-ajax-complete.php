<?php
namespace Ari_Stream_Quiz\Controllers\Quiz_Session;

use Ari\Controllers\Ajax as Ajax_Controller;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Helpers\Statistics_Activity as Statistics_Activity;

class Ajax_Complete extends Ajax_Controller {
    protected function process_request() {
        $result = false;
        if ( ! check_ajax_referer( 'ari-stream-quiz-ajax-action', ARISTREAMQUIZ_AJAX_NONCE_FIELD, false ) )
            return $result;

        if ( ! Request::exists( 'quiz_id' ) || ! Request::exists( 'session_key' ) || ! Request::exists( 'state' ) )
            return $result;

        $quiz_id = (int)Request::get_var( 'quiz_id', 0, 'num' );
        $session_key = Request::get_var( 'session_key' );

        if ( $quiz_id < 1 || empty( $session_key ) )
            return $result;

        $state = stripslashes_deep( Request::get_var( 'state' ) );
        $state = json_decode( $state, true );
        if ( empty( $state ) )
            return false;

        $session_model = $this->model( 'Quiz_Session' );
        $result = $session_model->complete_session( $session_key, $quiz_id, $state );

        if ( $result ) {
            $statistics_model = $this->model( 'Statistics' );
            $result = $statistics_model->log_activity( $quiz_id, Statistics_Activity::COMPLETE );

            if ( Request::exists( 'result' ) ) {
                $quiz_model = $this->model( 'Quiz' );
                $quiz = $quiz_model->get_quiz( $quiz_id );

                if ( $quiz->quiz_meta->send_mail->enabled && ! $quiz->collect_data ) {
                    $current_user = wp_get_current_user();
                    if ( $current_user->ID > 0 && ! empty( $current_user->user_email ) )
                    {
                        $quiz_results = stripslashes_deep( Request::get_var( 'result' ) );
                        if ( is_array( $quiz_results ) ) {
                            $quiz_results['user'] = array(
                                'name' => $current_user->user_nicename,
                                'email' => $current_user->email,
                                'login' => $current_user->user_login,
                            );
                        }
                        $quiz->send_mail( $current_user->user_email, $quiz_results );
                    }
                }
            }
        }

        return $result;
    }
}
