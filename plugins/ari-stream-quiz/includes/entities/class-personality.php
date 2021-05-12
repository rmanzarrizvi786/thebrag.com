<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Entities\Entity as Entity;

class Personality extends Entity {
    public $personality_id;

    public $personality_guid;

    public $quiz_id;

    public $personality_title;

    public $personality_content;

    public $image_id = 0;

    public $image = array();

    public $personality_order;

    function __construct( $db ) {
        parent::__construct( 'asq_personalities', 'personality_id', $db );
    }

    public function validate() {
        return true;
    }
}
