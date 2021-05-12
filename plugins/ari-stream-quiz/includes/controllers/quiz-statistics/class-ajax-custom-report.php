<?php
namespace Ari_Stream_Quiz\Controllers\Quiz_Statistics;

use Ari\Controllers\Ajax as Ajax_Controller;
use Ari\Utils\Request as Request;

class Ajax_Custom_Report extends Ajax_Controller {
    protected function process_request() {
        if ( $this->options->nopriv || ( ! current_user_can( 'edit_others_posts' ) && ! current_user_can( 'edit_posts' ) ) )
            return null;

        $quiz_id = Request::get_var( 'quiz_id', 0,  'num' );

        if ( $quiz_id < 1 )
            return null;

        $start_date = $this->prepare_date( Request::get_var( 'start_date' ) );
        $end_date = $this->prepare_date( Request::get_var( 'end_date' ), true );

        $model = $this->model();
        $data = $model->report( $quiz_id, $start_date, $end_date );

        return $data;
    }

    private function prepare_date( $date, $end = false ) {
        $prepared_date = null;

        if ( empty( $date ) )
            return $prepared_date;

        $parsedDate = \DateTime::createFromFormat( 'd-m-Y', $date, new \DateTimeZone('UTC') );
        if ( false === $parsedDate )
            return $prepared_date;

        if ( $end ) {
            $parsedDate->setTime( 23, 59, 59 );
        } else {
            $parsedDate->setTime( 0, 0, 0);
        }

        $local_offset = get_option( 'gmt_offset' );
        $offset_in_seconds = $local_offset ? 3600 * $local_offset : 0;

        if ( $offset_in_seconds > 0 )
            $parsedDate->sub( new \DateInterval( 'PT' . $offset_in_seconds . 'S' ) );

        $prepared_date = $parsedDate->format( 'Y-m-d H:i:s' );

        return $prepared_date;
    }
}
