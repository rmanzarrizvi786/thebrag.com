<?php
namespace Ari_Stream_Quiz\Models;

use Ari\Models\Model as Model;
use Ari_Stream_Quiz\Models\Quiz_Session as Quiz_Session_Model;
use Ari\Utils\Utils as Utils;

class Quiz_Result extends Model {
    public function save( $data, $quiz ) {
        $quiz_session = null;
        if ( ! empty( $data['session_key'] ) ) {
            $session_model = new Quiz_Session_Model(
                array(
                    'class_prefix' => $this->options->class_prefix
                )
            );

            $quiz_session = $session_model->get_session( $data['session_key'], $quiz->quiz_id );
        }

        if ( empty( $quiz_session ) )
            return false;

        if ( $quiz_session->result_id > 0 )
            return false;

        $data['user_answers'] = Utils::get_value( $quiz_session->state, 'answers' );

        $bind_data = array(
            'quiz_id' => $quiz->quiz_id,

            'meta_results' => $data,

            'session_key' => $quiz_session->session_key,

            'quiz_session' => $quiz_session->data,

            'start_date' => $quiz_session->created,

            'end_date' => $quiz_session->completed,

            'activity' => $quiz_session->activity,
        );

        $entity = $this->entity( 'Quiz_Result_' . ucfirst( strtolower( $quiz->quiz_type ) ) );
        if ( ! $entity->extend_bind( $bind_data ) )
            return false;

        if ( ! $entity->store() )
            return false;

        return $entity;
    }

    public function get_result( $result_id, $quiz_id = false, $session_key = false ) {
        $entity = $this->entity();

        if ( ! $entity->load( $result_id ) )
            return false;

        if ( ( false !== $quiz_id && $entity->quiz_id != $quiz_id ) || ( false !== $session_key && $entity->session_key != $session_key ) )
            return false;

        return $entity;
    }

    public function add_activity( $result_id, $activity, $quiz_id = false, $session_key = false ) {
        $result_entity = $this->get_result( $result_id, $quiz_id, $session_key );

        if ( empty( $result_entity ) )
            return false;

        return $result_entity->add_activity( $activity );
    }
}
