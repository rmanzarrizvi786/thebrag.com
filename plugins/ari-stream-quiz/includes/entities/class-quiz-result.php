<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Entities\Entity as Entity;
use Ari\Utils\Array_Helper as Array_Helper;
use Ari_Stream_Quiz\Helpers\Quiz_Activity as Quiz_Activity;

class Quiz_Result extends Entity {
    public $result_id;

    public $quiz_id;

    public $is_anonymous = 0;

    public $username = '';

    public $email = '';

    public $meta = '';

    public $start_date = '0000-00-00 00:00:00';

    public $end_date = '0000-00-00 00:00:00';

    public $title = '';

    public $elapsed_time = 0;

    public $quiz_session = '';

    public $ip_address = 0;

    public $session_key;

    public $activity;

    public $user_id = 0;

    function __construct( $db ) {
        parent::__construct( 'asq_results', 'result_id', $db );
    }

    public function extend_bind( $data, $ignore = array() ) {
        $meta = array();

        $results = isset( $data['meta_results'] ) ? $data['meta_results'] : array();
        $user_data = isset( $results['user_data'] ) ? $results['user_data'] : array();
        $user_answers = isset( $results['user_answers'] ) ? $results['user_answers'] : array();
        $start_date = isset( $data['start_date'] ) ? $data['start_date'] : null;
        $user_id = (int)Array_Helper::get_value( $results, 'user_id', 0 );

        if ( ! isset( $data['end_date'] ) )
            $data['end_date'] = current_time( 'mysql', 1 );

        $data['ip_address'] = Array_Helper::get_value( $results, 'ip_address', null );
        $data['user_id'] = $user_id;

        if ( ! empty( $user_data['name'] ) ) {
            $data['username'] = $user_data['name'];
        }

        if ( ! empty( $user_data['email'] ) ) {
            $data['email'] = $user_data['email'];
        }

        $data['elapsed_time'] = ! empty( $start_date ) ? max( strtotime( $data['end_date'] ) - strtotime( $start_date ), 0 ) : 0;
        $data['is_anonymous'] = ( $user_id == 0 && empty( $data['username'] ) && empty( $data['email'] ) ) ? 1 : 0;

        if ( isset( $user_data['name'] ) )
            unset( $user_data['name'] );

        if ( isset( $user_data['email'] ) )
            unset( $user_data['email'] );

        if ( is_array( $user_data ) && count( $user_data ) > 0) {
            $meta['user_data'] = $user_data;
        }

        if ( ( is_array( $user_answers ) && count( $user_answers ) > 0 ) || is_object( $user_answers ) ) {
            $meta['user_answers'] = $user_answers;
        }

        $data['meta'] = $meta;

        return parent::bind( $data, $ignore );
    }

    public function store( $force_insert = false ) {
        $meta = $this->meta;
        $is_new = $this->is_new();
        if ( ! is_string( $this->meta ) )
            $this->meta = json_encode( $this->meta );

        $quiz_session = $this->quiz_session;
        if ( ! is_string( $this->quiz_session ) )
            $this->quiz_session = json_encode( $this->quiz_session );

        $ip_address = $this->ip_address;
        if ( ! empty( $ip_address ) && false !== filter_var( $ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            $this->ip_address = ip2long( $ip_address );
        }

        if ( is_array( $this->activity ) )
            $this->activity = join( ',', $this->activity );

        $result = parent::store( $force_insert );

        $this->meta = $meta;
        $this->quiz_session = $quiz_session;
        $this->ip_address = $ip_address;

        if ( ! $result )
            return $result;

        if ( $is_new ) {
            $query = $this->db->prepare(
                sprintf(
                    'UPDATE `%1$sasq_quiz_sessions` SET result_id = %%d WHERE session_key = %%s AND quiz_id = %%d',
                    $this->db->prefix
                ),
                $this->result_id,
                $this->session_key,
                $this->quiz_id
            );
            $this->db->query( $query );
        }

        if ( ! empty( $meta['user_answers'] ) ) {
            $insert_values = array();

            foreach ( $meta['user_answers'] as $question_id => $answer_id ) {
                $question_id = intval( $question_id, 10 );
                $answer_id = intval( $answer_id, 10 );

                if ( $question_id > 0 && $answer_id > 0 ) {
                    $insert_values[] = sprintf(
                        '(%d,%d,%d)',
                        $this->result_id,
                        $question_id,
                        $answer_id
                    );
                }
            }

            $query = sprintf(
                'INSERT INTO %1$sasq_result_questions (result_id,question_id,answer_id) VALUES %2$s',
                $this->db->prefix,
                join( ',', $insert_values )
            );
            $this->db->query( $query );
        }

        return $result;
    }

    public function load( $keys, $reset = true ) {
        $result = parent::load( $keys, $reset );

        if ( ! $result )
            return $result;

        if ( ! empty( $this->activity ) )
            $this->activity = explode( ',', $this->activity );

        return $result;
    }

    public function add_activity( $activity ) {
        if ( ! Quiz_Activity::exists( $activity ) )
            return false;

        if ( $this->activity ) {
            $quiz_activities = is_array( $this->activity ) ? $this->activity : explode( ',', $this->activity );
            if ( in_array( $activity, $quiz_activities ) )
                return false;
        }

        $db = $this->db;

        $query = $db->prepare(
            sprintf(
                'UPDATE %1$s SET activity = CONCAT_WS(",", activity, %%s) WHERE result_id = %%d',
                $this->db_tbl
            ),
            $activity,
            $this->result_id
        );
        $result = $db->query( $query );

        return ( false !== $result && $result > 0 );
    }

    public function is_shared() {
        $is_shared = false;

        if ( empty( $this->activity ) )
            return $is_shared;

        $acitivities_str = is_array( $this->activity ) ? join( ',', $this->activity) : $this->activity;

        $is_shared = ( strpos( $acitivities_str, Quiz_Activity::FORCE_FACEBOOK ) !== false || strpos( $acitivities_str, 'SHARE_' ) !== false );

        return $is_shared;
    }
}
