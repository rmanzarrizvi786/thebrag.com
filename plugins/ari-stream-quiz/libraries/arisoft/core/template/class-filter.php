<?php
namespace Ari\Template;

class Filter {
	public function parse( $val, $params ) {
		return $val;
	}
	
	protected function prepare_params( $params, $default_values ) {
		$prepared_params = array();
		foreach ( $default_values as $idx => $default_value )
            $prepared_params[] = isset( $params[$idx] ) ? $params[$idx] : $default_value;

		return $prepared_params;
	}
}
