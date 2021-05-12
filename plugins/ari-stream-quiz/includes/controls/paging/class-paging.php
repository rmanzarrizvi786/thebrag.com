<?php
namespace Ari_Stream_Quiz\Controls\Paging;

use Ari\Controls\Paging\Paging as Paging_Base;

class Paging extends Paging_Base {
    function __construct( $options ) {
        $options = array_replace(
            array(
                'go_to_message' => __( 'Go to', 'ari-stream-quiz' ),
            ),
            $options
        );

        parent::__construct( $options );

        wp_enqueue_script( 'ari-paging', ARISTREAMQUIZ_ASSETS_URL . 'common/paging.js', array( 'jquery' ), ARISTREAMQUIZ_VERSION );
    }
}
