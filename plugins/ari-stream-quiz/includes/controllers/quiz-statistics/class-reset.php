<?php
namespace Ari_Stream_Quiz\Controllers\Quiz_Statistics;

use Ari\Controllers\Controller as Controller;
use Ari\Utils\Response as Response;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Reset extends Controller {
    public function execute() {
        $result = false;
        $model = $this->model();

        $quiz_id = Request::get_var( 'id', 0, 'num' );
        if ( $quiz_id > 0 && Helper::can_edit_quiz( $quiz_id ) ) {
            $result = $model->reset( $quiz_id );
        }

        if ( $result ) {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quiz-statistics',

                        'id' => $quiz_id,

                        'msg' => __( 'Quiz statistics were reset successfully', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_SUCCESS,
                    )
                )
            );
        } else {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quiz-statistics',

                        'id' => $quiz_id,

                        'msg' => __( 'Quiz statistics can not be reset', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_ERROR,
                    )
                )
            );
        }
    }
}
