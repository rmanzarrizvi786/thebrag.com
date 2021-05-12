<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Entities\Entity as Entity;

class Quiz_Result_Trivia_Details extends Entity {
    public $trivia_result_id;

    public $result_id;

    public $user_score;

    public $max_score;

    public $percent;

    function __construct( $db ) {
        parent::__construct( 'asq_trivia_results', 'trivia_result_id', $db );
    }

    public function bind( $data, $ignore = array() ) {
        return parent::bind( $data, $ignore );
    }

    public function store( $force_insert = false ) {
        $percent = 0;

        if ( $this->max_score > 0 )
            $percent = 100 * ( $this->user_score / $this->max_score );

        $this->percent = $percent;

        return parent::store( $force_insert );
    }
}
