<?php
namespace Ari_Stream_Quiz\Models;

use Ari\Models\Model as Model;
use Ari\Utils\Request as Request;
use Ari_Stream_Quiz\Models\Quiz as Quiz_Model;

class Quiz_Statistics extends Model {
    protected function populate_state() {
        if ( Request::exists( 'id' ) )
            $this->state['quiz_id'] = Request::get_var( 'id', 0, 'num' );
    }

    public function data() {
        $quiz_id = $this->get_state( 'quiz_id' );

        $quiz_model = new Quiz_Model(
            array(
                'class_prefix' => $this->options->class_prefix,

                'disable_state_load' => true,
            )
        );

        $quiz = $quiz_model->get_quiz( $quiz_id );
        $quiz_stat = $this->get_quiz_simple_stat( $quiz_id );

        $data = array(
            'quiz' => $quiz,

            'stat' => $quiz_stat,
        );

        return $data;
    }

    public function get_quiz_simple_stat( $quiz_id ) {
        $stat = $this->entity( 'Statistics' );

        if ( ! $stat->load_by_quiz_id( $quiz_id ) ) {
            return null;
        }

        return $stat;
    }

    public function reset( $quiz_id ) {
        $quiz_id = intval( $quiz_id, 10 );

        if ( $quiz_id < 1 )
            return false;

        $stat = $this->entity( 'Statistics' );

        if ( ! $stat->load_by_quiz_id( $quiz_id ) )
            return false;

        return $stat->reset_stat();
    }

    public function report( $quiz_id, $start_date, $end_date ) {
        $query = sprintf(
            'SELECT COUNT(*) AS started,SUM(opt_in) AS opt_in,SUM(is_completed) AS completed,SUM(share) AS shares FROM `%1$sasq_quiz_session_activities` WHERE `quiz_id` = %2$d',
            $this->db->prefix,
            $quiz_id
        );

        if ( ! empty( $start_date ) ) {
            $query .= sprintf(
                ' AND `created` >= \'%s\'',
                esc_sql( $start_date )
            );
        }

        if ( ! empty( $end_date ) ) {
            $query .= sprintf(
                ' AND `created` <= \'%s\'',
                esc_sql( $end_date )
            );
        }

        $query .= ' GROUP BY quiz_id';

        $data = $this->db->get_row( $query, ARRAY_A );

        return $data;
    }
}
