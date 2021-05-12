<?php
namespace Ari_Stream_Quiz\Models;

use Ari\Models\Model as Model;
use Ari_Stream_Quiz\Helpers\Statistics_Activity as Statistics_Activity;

class Statistics extends Model {
    public function log_activity( $quiz_id, $activity ) {
        $quiz_id = intval( $quiz_id, 10 );

        if ( $quiz_id < 1 )
            return false;

        if ( ! is_array( $activity ) )
            $activity = array( $activity );

        $inc_fields = array();
        foreach ( $activity as $current_activity ) {
            if ( false === $current_activity )
                continue;

            $inc_field = null;
            switch ( $current_activity ) {
                case Statistics_Activity::VIEW:
                    $inc_field = 'impression';
                    break;

                case Statistics_Activity::START:
                    $inc_field = 'start';
                    break;

                case Statistics_Activity::COMPLETE:
                    $inc_field = 'complete';
                    break;

                case Statistics_Activity::OPT_IN:
                    $inc_field = 'opt_in';
                    break;

                case Statistics_Activity::SHARE:
                    $inc_field = 'share';
                    break;
            }

            if ( ! is_null( $inc_field ) )
                $inc_fields[] = sprintf(
                    '`%1$s` = `%1$s` + 1',
                    $inc_field
                );
        }

        if ( count( $inc_fields ) == 0 )
            return false;

        $query = sprintf(
            'UPDATE `%1$sasq_statistics` SET %2$s WHERE quiz_id = %3$d',
            $this->db->prefix,
            join( ',', $inc_fields ),
            $quiz_id
        );
        $result = $this->db->query( $query );

        if ( $result !== false || $result > 0 )
            $result = true;

        return ( $result !== false && $result > 0 && count( $inc_fields ) == count( $activity ) );
    }
}