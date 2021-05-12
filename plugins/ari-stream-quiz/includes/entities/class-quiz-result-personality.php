<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Utils\Array_Helper as Array_Helper;

class Quiz_Result_Personality extends Quiz_Result {
    public $result_details = array();

    public function extend_bind( $data, $ignore = array() ) {
        $result = parent::extend_bind( $data, $ignore );

        $this->result_details = array();
        $meta_results = isset( $data['meta_results'] ) ? $data['meta_results'] : array();
        $result_data = isset( $meta_results['result'] ) ? $meta_results['result'] : array();
        $personalities = isset( $result_data['personalities'] ) ? $result_data['personalities'] : array();

        $order = 0;
        foreach ( $personalities as $personality ) {
            $details_data = array(
                'personality_id' => Array_Helper::get_value( $personality, 'personality_id', 0, 'num' ),

                'score' => intval( Array_Helper::get_value( $personality, 'score', 0 ), 10 ),

                'title' => Array_Helper::get_value( $personality, 'title', '' ),

                'order' => $order
            );

            $details_entity = new Quiz_Result_Personality_Details( $this->db );
            $result = $details_entity->bind( $details_data ) && $result;

            $this->result_details[] = $details_entity;
            ++$order;
        }

        return $result;
    }

    public function store( $force_insert = false ) {
        $first_details_entity = $this->result_details[0];

        $this->title = $first_details_entity->title;
        $result = parent::store( $force_insert );

        if ( ! $result )
            return $result;

        foreach ( $this->result_details as $details_entity ) {
            $details_entity->result_id = $this->result_id;
            $result = $details_entity->store() && $result;
        }

        return $result;
    }
}
