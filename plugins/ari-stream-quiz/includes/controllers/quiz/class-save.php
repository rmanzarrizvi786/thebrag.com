<?php
namespace Ari_Stream_Quiz\Controllers\Quiz;

use Ari\Controllers\Controller as Controller;
use Ari\Utils\Response as Response;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Save extends Controller {
    public function execute() {
        $data = stripslashes_deep( Request::get_var( 'entity' ) );

        $quiz_id = ! empty( $data['quiz_id'] ) ? intval( $data['quiz_id'], 10 ) : 0;

        if ( $quiz_id > 0 && ! Helper::can_edit_quiz( $quiz_id ) ) {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quizzes',
                    )
                )
            );
        }

        if ( ! isset( $data['quiz_meta'] ) ) {
            $data['quiz_meta'] = array();
        }

        $model = $this->model();
        $entity = $model->save( $data );
        $isValid = ! empty( $entity );

        if ( $isValid ) {
            $this->saved_successfully( $entity );
        } else {
            Response::redirect(
                Helper::build_url(
                    array(
                        'page' => 'ari-stream-quiz-quiz',

                        'action' => 'add',

                        'msg' => __( 'The quiz is not saved. Probably data are corrupted or a database connection is broken.', 'ari-stream-quiz' ),

                        'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_ERROR,
                    )
                )
            );
        }
    }

    protected function saved_successfully( $entity, $url_params = array() ) {
        $default_ulr_params = array(
            'page' => 'ari-stream-quiz-quizzes',

            'msg' => __( 'The quiz is saved successfully', 'ari-stream-quiz' ),

            'msg_type' => ARISTREAMQUIZ_MESSAGETYPE_SUCCESS,
        );
        $default_url_params = array_replace( $default_ulr_params, $url_params );

        Response::redirect(
            Helper::build_url(
                $default_url_params,
                array(
                    'id',
                )
            )
        );
    }
}
