<?php
namespace Ari_Stream_Quiz\Controllers\Quizzes;

use Ari\Controllers\Controller as Controller;
use Ari\Utils\Response as Response;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Bulk_Copy extends Controller {
    public function execute() {
        $result = false;
        $model = $this->model();

        if ( Request::exists( 'quiz_id' ) ) {
            $quiz_id = Request::get_var( 'quiz_id', array() );
            if ( is_array( $quiz_id ) && count( $quiz_id ) > 0 ) {
                $quiz_id = Helper::filter_edit_quizzes( $quiz_id );

                $result = $model->copy( $quiz_id );
            }
        }

        if ( $result ) {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quizzes',

                        'filter' => $model->encoded_filter_state(),

                        'msg' => __( 'Copies of the quizzes created successfully', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_SUCCESS,
                    )
                )
            );
        } else {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quizzes',

                        'filter' => $model->encoded_filter_state(),

                        'msg' => __( 'Copies of the quizzes are not created', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_ERROR,
                    )
                )
            );
        }
    }
}
