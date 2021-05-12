<?php
namespace Ari_Stream_Quiz\Models;

use Ari\Models\Model as Model;
use Ari\Utils\Request as Request;
use Ari\Utils\Array_Helper as Array_Helper;
use Ari_Stream_Quiz\Helpers\Results_Screen as Results_Screen;
use Ari_Stream_Quiz\Models\Quiz as Quiz_Model;

class Results extends Model {
    protected $sort_columns = array(
        'end_date',
    );

    protected function populate_state() {
        $filter = array(
            'search' => '',

            'quiz_type' => '',

            'anonymous' => true,

            'author_id' => 0,

            'order_by' => 'end_date',

            'order_dir' => 'DESC',

            'page_size' => 10,

            'page_num' => 0,
        );

        $user_filter = null;
        if ( Request::exists( 'filter' ) ) {
            $user_filter = Request::get_var( 'filter' );

            if ( ! empty ( $user_filter ) )
                $user_filter = unserialize( base64_decode( $user_filter ) );
            else
                $user_filter = null;
        }

        if ( is_array( $user_filter ) ) {
            foreach ( $user_filter as $filter_key => $filter_val ) {
                if ( isset( $filter[$filter_key] ) )
                    $filter[$filter_key] = $filter_val;
            }
        }

        $screen_options = Results_Screen::get_options();
        $filter['page_size'] = $screen_options['per_page'];

        $this->state['filter'] = $filter;
    }

    public function data() {
        $filter = $this->get_state( 'filter' );

        $items = $this->items( $filter );
        $items_count = $this->items_count( $filter );

        $data = array(
            'count' => $items_count,

            'list' => $items,

            'filter' => $filter,

            'filter_encoded' => $this->encoded_filter_state()
        );

        return $data;
    }

    public function items( $filter = null ) {
        if ( is_null( $filter ) )
            $filter = $this->get_state( 'filter' );

        $query = sprintf(
            'SELECT R.result_id,R.quiz_id,R.is_anonymous,IF(R.user_id > 0 AND LENGTH(R.username) = 0,U2.user_nicename,R.username) AS username,IF(R.user_id > 0 AND LENGTH(R.email) = 0,U2.user_email,R.email) AS email,R.start_date,R.end_date,R.user_id,R.title as result,Q.quiz_title,Q.quiz_title_filtered,Q.quiz_type,Q.post_id,Q.author_id FROM `%1$sasq_results` R INNER JOIN `%1$sasq_quizzes` Q ON R.quiz_id = Q.quiz_id LEFT JOIN `%2$susers` U ON Q.author_id = U.ID LEFT JOIN `%2$susers` U2 ON R.user_id = U2.ID',
            $this->db->prefix,
            $this->db->base_prefix
        );

        $query = $this->prepare_query( $query, $filter );

        $items = $this->db->get_results( $query, OBJECT );

        return $items;
    }

    public function items_count( $filter = null ) {
        if ( is_null( $filter ) )
            $filter = $this->get_state( 'filter' );

        $query = sprintf(
            'SELECT COUNT(*) FROM `%1$sasq_results` R INNER JOIN `%1$sasq_quizzes` Q ON R.quiz_id = Q.quiz_id ',
            $this->db->prefix
        );

        $query = $this->prepare_query( $query, $filter, false );

        $count = $this->db->get_var( $query );

        return $count;
    }

    public function export_data( $filter = null ) {
        if ( is_null( $filter ) )
            $filter = $this->get_state( 'filter' );

        $query = sprintf(
            'SELECT Q.quiz_title_filtered AS Quiz,R.title as Result,R.username AS Name,R.email AS Email,R.end_date AS Completed,Q.quiz_type AS `Type` FROM `%1$sasq_results` R INNER JOIN `%1$sasq_quizzes` Q ON R.quiz_id = Q.quiz_id LEFT JOIN `%2$susers` U ON Q.author_id = U.ID',
            $this->db->prefix,
            $this->db->base_prefix
        );

        $query = $this->prepare_query( $query, $filter );

        $items = $this->db->get_results( $query, ARRAY_A );

        return $items;
    }

    protected function prepare_query( $query, $filter, $paging = true ) {
        $db = $this->db;

        $where = array();

        if ( empty( $filter['anonymous'] ) ) {
            $where[] = 'R.is_anonymous = 0';
        }

        if ( ! empty( $filter['quiz_type'] ) ) {
            $where[] = $db->prepare( 'Q.quiz_type = %s', $filter['quiz_type'] );
        }

        if ( ! empty( $filter['author_id'] ) && $filter['author_id'] > 0 ) {
            $where[] = $db->prepare( 'Q.author_id = %d', $filter['author_id'] );
        }

        if ( ! empty( $filter['search'] ) ) {
            $where[] = $db->prepare( 'Q.quiz_title_filtered LIKE CONCAT("%%",%s,"%%")', $filter['search'] );
        }

        if ( count( $where ) > 0 ) {
            $query .= ' WHERE ' . join( ' AND ', $where );
        }

        if ( $paging ) {
            if ( $filter['order_by'] && in_array( $filter['order_by'], $this->sort_columns ) ) {
                $order_by = $filter['order_by'];
                $order_dir = 'DESC' == $filter['order_dir'] ? 'DESC' : 'ASC';

                $query .= sprintf(
                    ' ORDER BY %s %s',
                    $order_by,
                    $order_dir
                );
            }

            if ( $filter['page_size'] > 0 && $filter['page_num'] >= 0 ) {
                $page_num = $filter['page_num'];
                $page_size = $filter['page_size'];

                $offset = $page_num * $page_size;

                $query .= sprintf(
                    ' LIMIT %d,%d',
                    $offset,
                    $page_size
                );
            }
        }

        return $query;
    }

    public function encoded_filter_state() {
        $filter = $this->get_state( 'filter' );

        return $filter ? base64_encode( serialize( $filter ) ) : '';
    }

    public function get_quizzes_id( $id_list ) {
        $id_list = Array_Helper::to_int( $id_list, 1 );

        if ( count( $id_list ) == 0 )
            return array();

        $res = $this->db->get_results(
            sprintf(
                'SELECT result_id,quiz_id FROM `%1$sasq_results` WHERE result_id IN (%2$s)',
                $this->db->prefix,
                join( ',', $id_list )
            ),
            OBJECT_K
        );

        return $res;
    }

    public function get_quiz_id( $id ) {
        $quiz_id_list = $this->get_quizzes_id( $id );

        return isset( $quiz_id_list[$id] ) ? $quiz_id_list[$id]->quiz_id : 0;
    }

    public function delete( $id_list, $author_id = 0 ) {
        if ( ! is_array( $id_list ) )
            $id_list = array( $id_list );

        if ( count( $id_list ) == 0 )
            return false;

        $author_id = intval( $author_id, 10 );

        $id_list = array_map(
            function( $v ) {
                return intval( $v, 10 );
            },
            $id_list
        );

        $query = sprintf(
            'DELETE RES,RES_T,RES_P,RQ FROM
              `%1$sasq_results` RES INNER JOIN `%1$sasq_quizzes` Q
                ON RES.quiz_id = Q.quiz_id
              LEFT JOIN `%1$sasq_result_questions` RQ
                ON RQ.result_id = RES.result_id
              LEFT JOIN `%1$sasq_trivia_results` RES_T
                ON RES.result_id = RES_T.result_id
              LEFT JOIN `%1$sasq_personality_results` RES_P
                ON RES.result_id = RES_P.result_id
              WHERE RES.result_id IN (%2$s)' . ( $author_id > 0 ? ' AND Q.author_id = ' . $author_id : '' ),
            $this->db->prefix,
            join( ',', $id_list )
        );

        $result = $this->db->query( $query );

        return ( false !== $result );
    }

    public function delete_all( $author_id = 0 ) {
        $author_id = intval( $author_id, 10 );

        $query = sprintf(
            'DELETE RES,RES_T,RES_P,RQ FROM
              `%1$sasq_results` RES INNER JOIN `%1$sasq_quizzes` Q
                ON RES.quiz_id = Q.quiz_id
              LEFT JOIN `%1$sasq_result_questions` RQ
                ON RQ.result_id = RES.result_id
              LEFT JOIN `%1$sasq_trivia_results` RES_T
                ON RES.result_id = RES_T.result_id
              LEFT JOIN `%1$sasq_personality_results` RES_P
                ON RES.result_id = RES_P.result_id'
            . ( $author_id > 0 ? ' WHERE Q.author_id = ' . $author_id : '' ),
            $this->db->prefix
        );

        $result = $this->db->query( $query );

        return ( false !== $result );
    }
}
