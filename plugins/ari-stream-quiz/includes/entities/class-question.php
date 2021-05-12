<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Entities\Entity as Entity;

class Question extends Entity {
    public $question_id;

    public $quiz_id;

    public $question_title;

    public $image_id;

    public $image = array();

    public $question_order;

    public $question_explanation = '';

    public $show_explanation = 0;

    public $multiple = 0;

    public $answers = array();

    protected $bool_fields = array(
        'show_explanation',
    );

    function __construct( $db ) {
        parent::__construct( 'asq_questions', 'question_id', $db );
    }

    public function store( $force_insert = false ) {
        $result = parent::store( $force_insert );

        if ( ! $result )
            return $result;

        $is_new = $this->is_new();

        $db = $this->db;
        $answers = array();

        $filtered_answers = array();
        $answer_order = 0;
        foreach ( $this->answers as $answer ) {
            if ( $this->is_empty_answer( $answer ) )
                continue ;

            $answer_id = $is_new ? 0 : $answer->answer_id;

            $answer_data = $db->prepare(
                join(
                    ',',
                    array(
                        '%d', // 1 - answer_id
                        '%d', // 2 - question_id
                        '%d', // 3 - quiz_id
                        '%d', // 4 - image_id
                        '%s', // 5 - answer_title
                        '%d', // 6 - answer_correct
                        '%d', // 7 - answer_order,
                        '%s', // 8 - answer_guid
                    )
                ),
                $answer_id, // 1
                $this->question_id, // 2
                $this->quiz_id, // 3
                $answer->image_id, // 4
                $answer->answer_title, // 5
                isset( $answer->answer_correct ) ? ( (bool)$answer->answer_correct ? 1 : 0 ) : 0, // 6
                $answer_order, // 7
                isset( $answer->answer_guid ) ? $answer->answer_guid : '' // 8
            );

            $answer_data = '(' . $answer_data . ')';
            $answers[] = $answer_data;

            ++$answer_order;
            $filtered_answers[] = $answer;
        }

        $this->answers = $filtered_answers;

        if ( count( $answers ) > 0 ) {
            $query = sprintf(
                'INSERT INTO `%1$sasq_answers` (answer_id,question_id,quiz_id,image_id,answer_title,answer_correct,answer_order,answer_guid) VALUES %2$s',
                $db->prefix,
                join( ',', $answers )
            );
            $query_result = $db->query( $query );

            $result = ( false !== $query_result );
        }

        return $result;
    }

    public function validate() {
        return true;
    }

    public function is_empty() {
        if ( ( strlen( $this->question_title ) == 0 && empty( $this->image_id ) ) || count( $this->answers ) == 0 )
            return true;

        $is_empty = true;
        foreach ( $this->answers as $answer ) {
            if ( ! $this->is_empty_answer( $answer ) ) {
                $is_empty = false;
                break;
            }
        }

        return $is_empty;
    }

    public function correct_answer_count() {
        $count = 0;

        foreach ( $this->answers as $answer ) {
            if ( $this->is_empty_answer( $answer ) )
                continue ;

            $is_correct = isset( $answer->answer_correct ) ? ( (bool) $answer->answer_correct ? true : false ) : false;
            if ( $is_correct )
                ++$count;
        }

        return $count;
    }

    protected function is_empty_answer( $answer ) {
        return strlen( $answer->answer_title ) == 0 && empty( $answer->image_id );
    }
}
