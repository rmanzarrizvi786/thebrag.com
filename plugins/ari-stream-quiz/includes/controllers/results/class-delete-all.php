<?php
namespace Ari_Stream_Quiz\Controllers\Results;

use Ari\Controllers\Controller as Controller;
use Ari\Utils\Response as Response;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Delete_All extends Controller {
    public function execute() {
        $model = $this->model();

        $author_id = 0;
        if ( ! Helper::can_edit_other_quizzes() )
            $author_id = get_current_user_id();

        $result = $model->delete_all( $author_id );

        if ( $result ) {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-results',

                        'filter' => $model->encoded_filter_state(),

                        'msg' => __( 'The quizzes results deleted successfully', 'ari-stream-quiz' ),

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

                        'msg' => __( 'The quizzes results can not be deleted', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_WARNING,
                    )
                )
            );
        }
    }
}
