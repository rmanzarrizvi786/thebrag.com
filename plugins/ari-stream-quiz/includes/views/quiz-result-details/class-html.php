<?php
namespace Ari_Stream_Quiz\Views\Quiz_Result_Details;

use Ari_Stream_Quiz\Views\Base as Base;
use Ari\Utils\Array_Helper as Array_Helper;

class Html extends Base {
    public $quiz;

    public $result;

    public $quiz_session;

    public function display( $tmpl = null ) {
        $data = $this->get_data();

        $this->quiz = $data['quiz'];
        $this->result = $data['result'];
        $this->quiz_session = $this->prepare_quiz_session( $this->result );

        $this->set_title( __( $this->quiz->get_filtered_title(), 'ari-stream-quiz' ) );

        wp_enqueue_script( 'ari-page-quiz-result-details', ARISTREAMQUIZ_ASSETS_URL . 'common/pages/result_details.js', array( 'ari-streamquiz-app' ), ARISTREAMQUIZ_VERSION );

        parent::display( $tmpl );
    }

    public function prepare_quiz_session( $result ) {
        if ( empty( $result ) || empty( $result->quiz_session ) )
            return null;

        $session = $result->quiz_session;
        if ( is_string( $session ) ) {
            $session = json_decode( $session, true );

            if ( json_last_error() !== JSON_ERROR_NONE )
                return null;
        }

        $user_answers = null;
        $meta_data = $result->meta;
        if ( is_string( $meta_data ) ) {
            $meta_data = json_decode( $meta_data, true );

            if ( json_last_error() !== JSON_ERROR_NONE )
                return null;
        }

        if ( isset( $meta_data['user_answers'] ) )
            $user_answers = $meta_data['user_answers'];

        if ( empty( $user_answers ) )
            return null;

        $prepared_session = array();
        foreach ( $session['pages'] as $page ) {
            $questions = $page['questions'];

            foreach ( $questions as $question_id => &$question ) {
                if ( isset( $user_answers[$question_id] ) ) {
                    $question_user_answers = Array_Helper::ensure_array( $user_answers[$question_id] );

                    foreach ( $question_user_answers as $user_answer_id ) {
                        $question['answers'][$user_answer_id]['user_answer'] = true;
                    }
                }
            }

            $prepared_session = $prepared_session + $questions;
        }

        return $prepared_session;
    }
}
