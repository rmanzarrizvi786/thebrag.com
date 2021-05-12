<?php
namespace Ari_Stream_Quiz\Controllers\Quiz_Session;

use Ari\Controllers\Display as Display_Controller;
use Ari\Utils\Request as Request;
use Ari\Cache\Lite as Lite_Cache;
use Ari_Stream_Quiz\Helpers\Statistics_Activity as Statistics_Activity;

class Display extends Display_Controller {
    public function display( $tmpl = null ) {
        if ( Request::exists( 'asq_reload' ) && ! Lite_Cache::exists( 'asq_sessions' ) ) {
            $reload_key = base64_decode( Request::get_var( 'asq_reload' ) );

            if ( ! empty ( $reload_key ) ) {
                $current_quiz_id = 0;
                $sessions = explode( '|', $reload_key );
                array_pop( $sessions );
                $sessions_meta = array();

                foreach ( $sessions as $session ) {
                    @list( $session_quiz_id, $session_key ) = explode( ':', $session );

                    $session_quiz_id = intval( $session_quiz_id, 10 );
                    if ( $session_quiz_id > 0 && ! empty( $session_key ) ) {
                        if ( $current_quiz_id == 0 )
                            $current_quiz_id = $session_quiz_id;

                        $sessions_meta[$session_quiz_id] = $session_key;
                    }
                }

                Lite_Cache::set( 'asq_sessions', $sessions_meta );
                Lite_Cache::set( 'current_quiz_id', $current_quiz_id );
            } else {
                unset( $_REQUEST['asq_reload'] );
            }
        }

        $model = $this->model();
        $data = $model->data();

        if ( ! $data['continue_session'] && ! Request::is_prefetch_request() ) {
            $quiz = $data['quiz'];

            $activity = array( Statistics_Activity::VIEW );
            if ( $quiz->start_immediately )
                $activity[] = Statistics_Activity::START;

            $statistics_model = $this->model( 'Statistics' );
            $statistics_model->log_activity(
                $data['id'],
                $activity
            );
        }

        parent::display( $tmpl );
    }
}
