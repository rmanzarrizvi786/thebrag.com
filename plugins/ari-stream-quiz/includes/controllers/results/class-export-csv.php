<?php
namespace Ari_Stream_Quiz\Controllers\Results;

use Ari\Controllers\Controller as Controller;
use Ari\Utils\Date as Date_Helper;
use Ari\Csv\Writer as CSV_Writer;

class Export_Csv extends Controller {
    public function execute() {
        $model = $this->model();
        $filter = $model->get_state( 'filter' );
        $filter['page_size'] = 0;
        $filter['page_num'] = 0;

        $results = $model->export_data( $filter );

        $csv = new CSV_Writer(
            array(
                'delimiter' => "\t",

                'output_encoding' => 'UTF-16LE',

                'input_encoding' => 'UTF-8',

                'header' => array(
                    'Quiz',

                    'Result',

                    'Name',

                    'Email',

                    'Completed',

                    'Type',
                ),
            )
        );

        $csv->write( $results, function( $data ) {
            $data['Result'] = strip_tags( $data['Result'] );

            if ( $data['Completed'] ) {
                $data['Completed'] = Date_Helper::db_gmt_to_local( $data['Completed'], 'm/d/Y H:i:s' );
            }

            return $data;
        });
        $csv->output( 'results.csv' );
        exit();
    }
}
