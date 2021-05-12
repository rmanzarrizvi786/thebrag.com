<?php
/*
	Plugin Name: ARI Stream Quiz
	Plugin URI: http://wp-quiz.ari-soft.com
	Description: Powerful and easy to use quiz plugin for WordPress.
	Version: 1.5.53
	Author: ARI Soft
	Author URI: http://www.ari-soft.com
	Text Domain: ari-stream-quiz
	Domain Path: /languages
	License: GPL2
 */

defined( 'ABSPATH' ) or die( 'Access forbidden!' );

define( 'ARISTREAMQUIZ_EXEC_FILE', __FILE__ );
define( 'ARISTREAMQUIZ_URL', plugin_dir_url( __FILE__ ) );
define( 'ARISTREAMQUIZ_PATH', plugin_dir_path( __FILE__ ) );

if ( ! function_exists( 'ari_stream_quiz_init' ) ) {
    function ari_stream_quiz_init() {
        if ( defined( 'ARISTREAMQUIZ_INITED' ) )
            return ;

        define( 'ARISTREAMQUIZ_INITED', true );

        require_once ARISTREAMQUIZ_PATH . 'includes/defines.php';
        require_once ARISTREAMQUIZ_PATH . 'libraries/arisoft/loader.php';

        Ari_Loader::register_prefix('Ari_Stream_Quiz', ARISTREAMQUIZ_PATH . 'includes');
        Ari_Loader::register_prefix('Ari_Stream_Quiz_Themes', untrailingslashit( ARISTREAMQUIZ_THEMES_PATH ));
        Ari_Loader::register_prefix('Ari_Stream_Quiz_Themes', untrailingslashit( ARISTREAMQUIZ_CUSTOM_THEMES_PATH ));

        $plugin = new \Ari_Stream_Quiz\Plugin(
            array(
                'class_prefix' => 'Ari_Stream_Quiz',

                'version' => ARISTREAMQUIZ_VERSION,

                'path' => ARISTREAMQUIZ_PATH,

                'url' => ARISTREAMQUIZ_URL,

                'assets_url' => ARISTREAMQUIZ_ASSETS_URL,

                'view_path' => ARISTREAMQUIZ_PATH . 'includes/views/',

                'main_file' => __FILE__,

                'page_prefix' => 'ari-stream-quiz',
            )
        );
        $plugin->init();
    }
}

if ( ! function_exists( 'ari_stream_quiz_activation_check' ) ) {
    function ari_stream_quiz_activation_check() {
        $min_php_version = '5.4.0';
        $min_wp_version = '4.0.0';

        $current_wp_version = get_bloginfo( 'version' );
        $current_php_version = PHP_VERSION;

        $is_supported_php_version = version_compare( $current_php_version, $min_php_version, '>=' );
        $is_spl_installed = function_exists( 'spl_autoload_register' );
        $is_supported_wp_version = version_compare( $current_wp_version, $min_wp_version, '>=' );

        if ( ! $is_supported_php_version || ! $is_spl_installed || ! $is_supported_wp_version ) {
            deactivate_plugins( basename( ARISTREAMQUIZ_EXEC_FILE ) );

            $recommendations = array();

            if ( ! $is_supported_php_version )
                $recommendations[] = sprintf(
                    __( 'update PHP version on your server from v. %s to at least v. %s', 'ari-stream-quiz' ),
                    $current_php_version,
                    $min_php_version
                );

            if ( ! $is_spl_installed )
                $recommendations[] = __( 'install PHP SPL extension', 'ari-stream-quiz' );

            if ( ! $is_supported_wp_version )
                $recommendations[] = sprintf(
                    __( 'update WordPress v. %s to at least v. %s', 'ari-stream-quiz' ),
                    $current_wp_version,
                    $min_wp_version
                );

            wp_die(
                sprintf(
                    __( '"ARI Stream Quiz" can not be activated. It requires PHP version 5.4.0+ with SPL extension and WordPress 4.0+.<br /><br /><b>Recommendations:</b> %s.<br /><br /><a href="%s" class="button button-primary">Back</a>', 'ari-stream-quiz' ),
                    join( ', ', $recommendations ),
                    get_dashboard_url()
                )
            );
        } else {
            ari_stream_quiz_init();

            if ( ! wp_next_scheduled( 'ari_stream_quiz_cron_job' ) ) {
                wp_schedule_event( time(), 'daily', 'ari_stream_quiz_cron_job' );
            }
        }
    }
}

if ( ! function_exists( 'ari_stream_quiz_deactivation' ) ) {
    function ari_stream_quiz_deactivation() {
        wp_clear_scheduled_hook( 'ari_stream_quiz_cron_job' );
    }
}

add_action( 'plugins_loaded', 'ari_stream_quiz_init' );
register_activation_hook( ARISTREAMQUIZ_EXEC_FILE, 'ari_stream_quiz_activation_check' );
register_deactivation_hook( ARISTREAMQUIZ_EXEC_FILE, 'ari_stream_quiz_deactivation' );
