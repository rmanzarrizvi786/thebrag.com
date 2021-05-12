<?php
namespace Ari_Stream_Quiz\Controllers\Quiz_Session;

use Ari\Controllers\Ajax as Ajax_Controller;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Helpers\Settings as Settings;

class Ajax_Save_Result extends Ajax_Controller {
    protected function process_request() {
        if ( ! check_ajax_referer( 'ari-stream-quiz-ajax-action', ARISTREAMQUIZ_AJAX_NONCE_FIELD, false ) || ! Settings::get_option( 'save_results' ) )
            return false;

        $quiz_id = Request::get_var( 'id', 0, 'num' );
        if ( $quiz_id < 1 )
            return false;

        $quiz_model = $this->model( 'Quiz' );
        $quiz = $quiz_model->get_quiz( $quiz_id );

        if ( is_null( $quiz ) ) {
            return false;
        }

        $data = stripslashes_deep( Request::get_var( 'data' ) );

        $data = json_decode( $data, true );
        if ( empty( $data ) )
            return false;

        $data['ip_address'] = Request::get_ip();
        $data['user_id'] = get_current_user_id();

        $result_model = $this->model( 'Quiz_Result' );
        $result = $result_model->save( $data, $quiz );

        if ( false !== $result )
            $result = $result->result_id;

        return $result;
    }
}
