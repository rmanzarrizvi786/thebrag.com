<?php
namespace Ari_Stream_Quiz\Controllers\Results;

use Ari\Controllers\Controller as Controller;
use Ari\Utils\Response as Response;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Delete extends Controller {
    public function execute() {
        $result = false;
        $model = $this->model();

        if ( Request::exists( 'action_result_id' ) ) {
            $result_id = Request::get_var( 'action_result_id', 0, 'num' );
            $quiz_id = $model->get_quiz_id( $result_id );

            if ( $quiz_id > 0 && Helper::can_edit_quiz( $quiz_id ) ) {
                $result = $model->delete( $result_id );
            }
        }

        if ( $result ) {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-results',

                        'filter' => $model->encoded_filter_state(),

                        'msg' => __( 'The quiz results deleted successfully', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_SUCCESS,
                    )
                )
            );
        } else {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-results',

                        'filter' => $model->encoded_filter_state(),

                        'msg' => __( 'The quiz result can not be deleted', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_WARNING,
                    )
                )
            );
        }
    }
}
