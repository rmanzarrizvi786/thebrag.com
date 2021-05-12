<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Entities\Entity as Entity;

class Statistics extends Entity {
    public $stat_id;

    public $quiz_id;

    public $impression = 0;

    public $start = 0;

    public $complete = 0;

    public $opt_in = 0;

    public $share = 0;

    public $start_date = '0000-00-00 00:00:00';

    function __construct( $db ) {
        parent::__construct( 'asq_statistics', 'stat_id', $db );
    }

    public function load_by_quiz_id( $quiz_id ) {
        if ( ! $this->custom_load( array( 'quiz_id' => $quiz_id ) ) )
            return false;

        return true;
    }

    public function store( $force_insert = false ) {
        if ( $this->is_new() ) {
            $this->start_date = current_time( 'mysql', 1 );
        }

        return parent::store( $force_insert );
    }

    public function reset_stat() {
        if ( $this->is_new() )
            return false;

        $this->impression = 0;
        $this->start = 0;
        $this->complete = 0;
        $this->opt_in = 0;
        $this->share = 0;
        $this->start_date = current_time( 'mysql', 1 );

        return $this->store();
    }
}
