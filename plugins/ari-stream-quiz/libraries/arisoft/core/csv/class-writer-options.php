<?php
namespace Ari\Csv;

use Ari\Utils\Options as Options;

class Writer_Options extends Options {
    public $delimiter = ',';

    public $enclosure = '"';

    public $add_header = true;

    public $auto_detect_header = false;

    public $header = null;

    public $output_encoding = '';

    public $input_encoding = '';

    public $add_bom = true;
}
