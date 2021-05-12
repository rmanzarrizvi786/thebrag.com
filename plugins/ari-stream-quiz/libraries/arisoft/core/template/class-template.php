<?php
namespace Ari\Template;

use Ari\Utils\Array_Helper as Array_Helper;
use Ari\Utils\Object_Factory as Object_Factory;

class Template {
    private static $filters = array();

	public static function display( $template_file, $data = array() ) {
		include $template_file;
	}

    public static function parse( $template, $params, $remove_unrecognized = false ) {
        if ( empty( $params ) )
            return $template;

        $params_regex = '/\{\$([^}\|]+)((\|[^}\|]+)*)}/si';

        $matches = array();
        @preg_match_all( $params_regex, $template, $matches, PREG_SET_ORDER );

        if ( empty( $matches ) )
            return $template;

        $search = array();
        $replace = array();
        foreach ( $matches as $match ) {
            $value = self::get_param_value( $match[1], $params );
            if ( is_null( $value ) && ! $remove_unrecognized )
                continue;

            $value = self::apply_filters( $value, ! empty( $match[2] ) ? $match[2] : '' );

            $search[] = $match[0];
            $replace[] = $value;
        }

        return str_replace( $search, $replace, $template );
    }

    private static function get_filter( $name ) {
        if ( isset( self::$filters[$name] ) && ! is_null( self::$filters[$name]['instance'] ) )
            return self::$filters[$name]['instance'];

        $filter = Object_Factory::get_object( $name, 'Ari\\Template\\Filters' );
        if ( is_null( $filter ) )
            return $filter;

        self::$filters[$name]['instance'] = $filter;

        return $filter;
    }

    private static function apply_filter( $name, $value, $params = null ) {
        $filter = self::get_filter( $name );

        if ( is_null( $filter ) )
            return $value;

        return $filter->parse( $value, $params );
    }

    private static function apply_filters( $value, $filterStr ) {
        $filters = explode( '|', $filterStr );
        if ( empty( $filters ) )
            return $value;

        foreach ($filters as $filter) {
            if ( empty( $filter ) )
                continue;

            $filter_info = explode( ':', $filter );
            $filter_name = $filter_info[0];
            array_shift( $filter_info );

            $value = self::apply_filter( $filter_name, $value, $filter_info );
        }

        return $value;
    }

    private static function get_param_value( $key, $params ) {
        $value = null;

        if ( !$key )
            return $value;

        $keys = null;
        if ( false !== strpos( $key, ':' ) ) {
            $keys = explode( ':', $key );
        } else {
            $keys = array( $key );
        }

        $value = $params;
        foreach ($keys as $c_key) {
            $value = Array_Helper::get_value( $value, $c_key );

            if ( is_null( $value ) )
                break;
        }

        if ( is_array( $value ) )
            $value = null;

        return $value;
    }
}
