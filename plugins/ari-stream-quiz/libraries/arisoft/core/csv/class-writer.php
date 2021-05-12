<?php
namespace Ari\Csv;

class Writer {
    protected $csv_data = '';

    protected $options = null;

    protected $init = false;

    function __construct( $options = array() ) {
        $this->options = new Writer_Options( $options );
    }

    protected function init() {
        $this->init = true;

        if ( $this->options->add_header ) {
            $header = $this->options->header;
            $this->csv_data = $this->data_to_csv( array( $header ) ) . $this->csv_data;
        }
    }

    public function write( $data, $formatter = null ) {
        if ( $this->options->auto_detect_header && empty( $this->options->header ) ) {
            $this->detect_header( $data );
        }

        $this->csv_data .= $this->data_to_csv( $data, $formatter );
    }

    protected function detect_header( $data ) {
        if ( ! is_array( $data ) || count( $data ) == 0 )
            return false;

        $first_el = reset( $data );
        $this->options->header = array_keys( $first_el );

        return true;
    }

    protected function data_to_csv( $data, $formatter = null ) {
        $has_formatter = is_callable( $formatter );

        $buffer = fopen('php://memory', 'r+');
        foreach ( $data as $item ) {
            if ( $has_formatter )
                $item = $formatter( $item );

            fputcsv( $buffer, $item, $this->options->delimiter );
        }
        rewind( $buffer );
        $csv = stream_get_contents($buffer);
        fclose($buffer);

        return $csv;
    }

    public function output( $file_name = 'data.csv' ) {
        header( 'Content-Description: File Transfer' );
        header( 'Content-Type: application/octet-stream' );
        header( 'Content-Disposition: attachment; filename=' . $file_name );
        header( 'Content-Transfer-Encoding: binary' );
        header( 'Expires: 0' );
        header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
        header( 'Pragma: public' );

        echo $this;
    }

    public function __toString() {
        if ( ! $this->init ) {
            $this->init();
        }

        $csv_data = $this->csv_data;
        if ( $this->options->output_encoding && $this->options->input_encoding && $this->options->output_encoding != $this->options->input_encoding ) {
            $csv_data = mb_convert_encoding( $csv_data, $this->options->output_encoding, $this->options->input_encoding );
        }

        if ( $this->options->add_bom ) {
            $encoding = null;
            if ( $this->options->output_encoding )
                $encoding = $this->options->output_encoding;
            else if ( $this->options->input_encoding )
                $encoding = $this->options->input_encoding;

            if ( $encoding ) {
                $bom = null;

                switch ( $encoding ) {
                    case 'UTF-8';
                        $bom = "\xEF\xBB\xBF";
                        break;

                    case 'UTF-16LE':
                        $bom = "\xFF\xFE";
                        break;

                    case 'UTF-16BE':
                        $bom = "\xFE\xFF";
                        break;

                    case 'UTF-32LE':
                        $bom = "\xFF\xFE\x00\x00";
                        break;

                    case 'UTF-32BE':
                        $bom = "\x00\x00\xFE\xFF";
                        break;
                }

                if ( $bom )
                    $csv_data = $bom . $csv_data;
            }
        }

        return $csv_data;
    }
}
