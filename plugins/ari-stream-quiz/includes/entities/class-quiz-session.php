<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Entities\Entity as Entity;
use Ari\Utils\Utils as Utils;
use Ari_Stream_Quiz\Helpers\Quiz_Activity as Quiz_Activity;
use Ari_Stream_Quiz\Entities\Quiz_Session_Activity as Quiz_Session_Activity;

class Quiz_Session extends Entity {
    public $session_id;

    public $session_key;

    public $quiz_id;

    public $data = '';

    public $state = '';

    public $created = '0000-00-00 00:00:00';

    public $modified = '0000-00-00 00:00:00';

    public $activity;

    public $completed;

    public $result_id = 0;

    public $prefetched = 0;

    protected $json_fields = array( 'data', 'state' );

    function __construct( $db ) {
        parent::__construct( 'asq_quiz_sessions', 'session_id', $db );
    }

    public function load( $keys, $reset = true ) {
        $result = parent::load( $keys, $reset );

        if ( ! $result )
            return $result;

        $this->restore_json_fields();

        return $result;
    }

    public function custom_load( $filter, $reset = true ) {
        $result = parent::custom_load( $filter, $reset);

        if ( ! $result )
            return $result;

        $this->restore_json_fields();

        return $result;
    }

    protected function restore_json_fields() {
        foreach ( $this->json_fields as $json_field ) {
            if ( is_string( $this->$json_field ) ) {
                $this->$json_field = json_decode( $this->$json_field );
            }
        }
    }

    public function store( $force_insert = false ) {
        $is_new = $this->is_new();
        $current_time_db_gmt = current_time( 'mysql', 1 );

        if ( $is_new ) {
            $this->session_key = Utils::guid();
            $this->created = $current_time_db_gmt;
        } else {
            $this->modified = $current_time_db_gmt;
        }

        $json_field_values = array();

        foreach ( $this->json_fields as $json_field ) {
            $field_val = $this->$json_field;

            if ( ! is_string( $field_val ) ) {
                $this->$json_field = json_encode( $field_val );
                $json_field_values[$json_field] = $field_val;
            }
        }

        $result = parent::store( $force_insert );

        if ( count( $json_field_values ) > 0 ) {
            foreach ( $json_field_values as $json_field => $field_val ) {
                $this->$json_field = $field_val;
            }
        }

        if ( $result && $is_new ) {
            $session_activity = new Quiz_Session_Activity( $this->db );
            $session_activity->bind(
                array(
                    'quiz_id' => $this->quiz_id,

                    'session_id' => $this->session_id,

                    'created' => $this->created,
                )
            );

            $session_activity->store();
        }

        return $result;
    }

    public function add_activity( $activity ) {
        if ( $this->is_new() || ! $this->is_completed() || ! Quiz_Activity::exists( $activity ) )
            return false;

        if ( $this->activity ) {
            $quiz_activities = explode( ',', $this->activity );
            if ( in_array( $activity, $quiz_activities ) )
                return false;
        }

        $db = $this->db;

        $query = $db->prepare(
            sprintf(
                'UPDATE `%1$s` SET `activity` = CONCAT_WS(",", activity, %%s) WHERE `session_id` = %%d',
                $this->db_tbl
            ),
            $activity,
            $this->session_id
        );
        $result = $db->query( $query );

        if ( $this->result_id > 0 ) {
            $query = $db->prepare(
                sprintf(
                    'UPDATE `%1$sasq_results` SET `activity` = CONCAT_WS(",", activity, %%s) WHERE `result_id` = %%d',
                    $db->prefix
                ),
                $activity,
                $this->result_id
            );
            $db->query( $query );
        }

        if ( Quiz_Activity::OPT_IN == $activity ) {
            $query = $db->prepare(
                sprintf(
                    'UPDATE `%1$sasq_quiz_session_activities` SET opt_in = 1 WHERE session_id = %%d',
                    $db->prefix
                ),
                $this->session_id
            );
            $db->query( $query );
        } else if ( Quiz_Activity::FORCE_FACEBOOK == $activity || strpos( strtolower( $activity ), 'share_' ) === 0 ) {
            $query = $db->prepare(
                sprintf(
                    'UPDATE `%1$sasq_quiz_session_activities` SET share = share + 1 WHERE session_id = %%d',
                    $db->prefix
                ),
                $this->session_id
            );
            $db->query( $query );
        }

        return ( false !== $result && $result > 0 );
    }

    public function start( $date ) {
        if ( ! $this->prefetched || $this->is_completed() )
            return false;

        $now = current_time( 'timestamp', true );
        if ( $now < $date )
            $date = $now;

        $db_date = date( 'Y-m-d H:i:s', $date );

        $query = $this->db->prepare(
            sprintf(
                'UPDATE `%1$s` S LEFT JOIN `%2$sasq_quiz_session_activities` SA ON S.session_id = SA.session_id SET S.prefetched = 0,S.created = %%s,SA.created = %%s WHERE S.session_id = %%d',
                $this->db_tbl,
                $this->db->prefix
            ),
            $db_date,
            $db_date,
            $this->session_id
        );

        $result = $this->db->query( $query );

        return ( false !== $result && $result > 0 );
    }

    public function complete( $state ) {
        $this->completed = current_time( 'mysql', 1 );

        if ( $this->is_new() )
            return false;

        $query = $this->db->prepare(
            sprintf(
                'UPDATE `%1$s` S LEFT JOIN `%2$sasq_quiz_session_activities` SA ON S.session_id = SA.session_id SET S.completed = %%s, S.state = %%s, SA.completed = %%s, SA.is_completed = 1 WHERE S.session_id = %%d',
                $this->db_tbl,
                $this->db->prefix
            ),
            $this->completed,
            json_encode( $state ),
            $this->completed,
            $this->session_id
        );

        $result = $this->db->query( $query );

        return ( false !== $result && $result > 0 );
    }

    public function is_completed() {
        return ! empty( $this->completed );
    }
}
