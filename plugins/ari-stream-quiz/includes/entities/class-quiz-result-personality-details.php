<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Entities\Entity as Entity;

class Quiz_Result_Personality_Details extends Entity {
    public $personality_result_id;

    public $result_id;

    public $personality_id = 0;

    public $order = 0;

    public $score = 0;

    public $title = '';

    public $is_primary = 0;

    function __construct( $db ) {
        parent::__construct( 'asq_personality_results', 'personality_result_id', $db );
    }

    public function bind( $data, $ignore = array() ) {
        return parent::bind( $data, $ignore );
    }

    public function store( $force_insert = false ) {
        $this->is_primary = ( 0 == $this->order );

        return parent::store( $force_insert );
    }
}
