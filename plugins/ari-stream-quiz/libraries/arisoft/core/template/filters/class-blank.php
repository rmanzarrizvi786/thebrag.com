<?php
namespace Ari\Template\Filters;

use Ari\Template\Filter as Filter;

class Blank extends Filter {
	public function parse($val, $params) {
		$replace_value = isset( $params[0] ) ? $params[0] : '';

		return empty($val) ? $replace_value : $val;
	}	
}
