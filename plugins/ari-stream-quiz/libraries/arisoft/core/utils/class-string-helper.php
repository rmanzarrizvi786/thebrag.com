<?php
namespace Ari\Utils;

class String_Helper {
    public static function truncate( $val, $limit, $more = '...' ) {
        $len = strlen( $val );

        if ( $len <= $limit ) {
            return $val;
        }

        return substr( $val, 0, $limit ) . $more;
    }
}
