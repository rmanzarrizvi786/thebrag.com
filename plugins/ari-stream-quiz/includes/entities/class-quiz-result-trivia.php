<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Utils\Array_Helper as Array_Helper;

class Quiz_Result_Trivia extends Quiz_Result {
    public $result_details = array();

    public function extend_bind( $data, $ignore = array() ) {
        $result = parent::extend_bind( $data, $ignore );

        $meta_results = isset( $data['meta_results'] ) ? $data['meta_results'] : array();
        $result_data = isset( $meta_results['result'] ) ? $meta_results['result'] : array();

        $details_data = array(
            'user_score' => Array_Helper::get_value( $result_data, 'userScore', 0, 'num' ),

            'max_score' => Array_Helper::get_value( $result_data, 'maxScore', 0, 'num' ),
        );

        $details_entity = new Quiz_Result_Trivia_Details( $this->db );
        $result = $details_entity->bind( $details_data ) && $result;

        $this->result_details = array( $details_entity );

        return $result;
    }

    public function store( $force_insert = false ) {
        $details_entity = $this->result_details[0];

        $this->title = sprintf(
            '%d / %d',
            $details_entity->user_score,
            $details_entity->max_score
        );
        $result = parent::store( $force_insert );

        if ( ! $result )
            return $result;

        $details_entity->result_id = $this->result_id;

        $result = $details_entity->store();

        return $result;
    }
}
