<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Entities\Entity as Entity;

class Quiz_Session_Activity extends Entity {
    public $session_activities_id;

    public $quiz_id;

    public $session_id;

    public $is_completed = 0;

    public $created;

    public $completed = '0000-00-00 00:00:00';

    public $opt_in = 0;

    public $share = 0;

    function __construct( $db ) {
        parent::__construct( 'asq_quiz_session_activities', 'session_activities_id', $db );
    }
}
