<?php
namespace Ari\Template\Filters;

use Ari\Template\Filter as Filter;

class Lower_Case extends Filter {
	public function parse( $val, $params ) {
		return strtolower( $val );
	}	
}
