<?php
define( 'ARISTREAMQUIZ_VERSION', '1.5.53' );
define( 'ARISTREAMQUIZ_SLUG', 'ari-stream-quiz' );
define( 'ARISTREAMQUIZ_', 'ari-stream-quiz' );
define( 'ARISTREAMQUIZ_ASSETS_URL', ARISTREAMQUIZ_URL . 'assets/' );
define( 'ARISTREAMQUIZ_THEMES_PATH', ARISTREAMQUIZ_PATH . 'themes/' );
define( 'ARISTREAMQUIZ_THEMES_URL', ARISTREAMQUIZ_URL . 'themes/' );
define( 'ARISTREAMQUIZ_INSTALL_PATH', ARISTREAMQUIZ_PATH . 'install/' );
define( 'ARISTREAMQUIZ_THEME_DEFAULT', 'buzzfeed' );
define( 'ARISTREAMQUIZ_POST_TYPE', 'aristreamquiz' );
define( 'ARISTREAMQUIZ_3RDPARTY_PATH', ARISTREAMQUIZ_PATH . 'libraries/vendor/vendor/' );
define( 'ARISTREAMQUIZ_3RDPARTY_LOADER', ARISTREAMQUIZ_3RDPARTY_PATH . 'autoload.php' );
define( 'ARISTREAMQUIZ_VERSION_OPTION', 'ari_stream_quiz_version' );
define( 'ARISTREAMQUIZ_AJAX_NONCE_FIELD', 'nonce' );
define( 'ARISTREAMQUIZ_CRYPT_KEY_OPTION', 'asq_crypt_key' );

define( 'ARISTREAMQUIZ_DB_EMPTYDATE', '0000-00-00 00:00:00' );

define( 'ARISTREAMQUIZ_RESULTTEMPLATE_MAXSCORE', 9999 );

define( 'ARISTREAMQUIZ_QUIZTYPE_PERSONALITY', 'PERSONALITY' );
define( 'ARISTREAMQUIZ_QUIZTYPE_TRIVIA', 'TRIVIA' );

define( 'ARISTREAMQUIZ_MESSAGETYPE_SUCCESS', 'success' );
define( 'ARISTREAMQUIZ_MESSAGETYPE_NOTICE', 'notice' );
define( 'ARISTREAMQUIZ_MESSAGETYPE_ERROR', 'error' );
define( 'ARISTREAMQUIZ_MESSAGETYPE_WARNING', 'warning' );

$upload_dir = wp_upload_dir();
define( 'ARISTREAMQUIZ_CUSTOM_THEMES_PATH', $upload_dir['basedir'] . '/ari-stream-quiz-themes/' );
define( 'ARISTREAMQUIZ_CUSTOM_THEMES_URL', $upload_dir['baseurl'] . '/ari-stream-quiz-themes/' );
