<?php
namespace Ari\Template\Filters;

use Ari\Template\Filter as Filter;

class Upper_Case extends Filter {
    public function parse( $val, $params ) {
        return strtoupper( $val );
    }
}
