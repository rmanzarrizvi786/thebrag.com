<?php
namespace Ari_Stream_Quiz\Helpers;

class Results_Screen {
    static public function register() {
        $args = array(
            'label' => __( 'Number of items per page', 'ari-stream-quiz' ),
            'default' => 25,
            'option' => 'aristreamquiz_results_per_page'
        );

        add_screen_option( 'per_page', $args );
    }

    static public function get_options() {
        $options = array();

        $user_id = get_current_user_id();
        $screen = get_current_screen();

        $per_page_option = $screen->get_option( 'per_page', 'option' );
        $per_page = get_user_meta( $user_id, $per_page_option, true );
        if ( empty ( $per_page) || $per_page < 1 ) {
            $per_page = $screen->get_option( 'per_page', 'default' );
        }

        $options['per_page'] = $per_page;

        return $options;
    }

    static public function set_options( $status, $option, $value ) {
        if ( 'aristreamquiz_results_per_page' == $option ) {
            return $value;
        }

        return $status;
    }
}
