<?php
namespace Ari_Stream_Quiz\Models;

use Ari\Models\Model as Model;
use Ari_Stream_Quiz\Models\Quiz as Quiz_Model;
use Ari\Utils\Request as Request;

class Quiz_Result_Details extends Model {
    protected function populate_state() {
        if ( Request::exists( 'id' ) )
            $this->state['result_id'] = Request::get_var( 'id', 0, 'num' );
    }

    public function data() {
        $result_id = $this->get_state( 'result_id' );
        $result = $this->get_result( $result_id );
        $quiz = null;

        if ( $result && $result->quiz_id) {
            $quiz_model = new Quiz_Model(
                array(
                    'class_prefix' => $this->options->class_prefix,

                    'disable_state_load' => true,
                )
            );

            $quiz = $quiz_model->get_quiz( $result->quiz_id );
        }

        $data = array(
            'result' => $result,

            'quiz' => $quiz,
        );

        return $data;
    }

    public function get_result( $id ) {
        $quiz_type = $this->db->get_var(
            sprintf(
                'SELECT Q.quiz_type FROM `%1$sasq_results` R INNER JOIN `%1$sasq_quizzes` Q ON R.quiz_id = Q.quiz_id WHERE R.result_id = %2$d LIMIT 0,1',
                $this->db->prefix,
                $id
            )
        );

        if ( is_null( $quiz_type) )
            return null;

        $entity = $this->entity_by_type( $quiz_type );
        $entity->load( $id );

        return $entity;
    }

    protected function entity_by_type( $quiz_type ) {
        if ( empty( $quiz_type ) )
            return null;

        $entity_name = 'Quiz_Result_' . ucfirst( strtolower( $quiz_type ) );

        return $this->entity( $entity_name );
    }
}
