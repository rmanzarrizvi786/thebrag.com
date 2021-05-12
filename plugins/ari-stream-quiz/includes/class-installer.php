<?php
namespace Ari_Stream_Quiz;

use Ari\App\Installer as Ari_Installer;
use Ari\Database\Helper as DB;
use Ari\Crypt\Crypt as Crypt;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Installer extends Ari_Installer {
    function __construct( $options = array() ) {
        if ( ! isset( $options['installed_version'] ) ) {
            $installed_version = get_option( ARISTREAMQUIZ_VERSION_OPTION );

            if ( false !== $installed_version) {
                $options['installed_version'] = $installed_version;
            }
        }

        if ( ! isset( $options['version'] ) ) {
            $options['version'] = ARISTREAMQUIZ_VERSION;
        }

        parent::__construct( $options );
    }

    private function init() {
        $sql = file_get_contents( ARISTREAMQUIZ_INSTALL_PATH . 'install.sql' );
        $utf8mb4_supported = DB::is_utf8mb4_supported();

        if ( ! $utf8mb4_supported ) {
            $sql = str_replace( 'utf8mb4_unicode_ci', 'utf8_general_ci', $sql );
            $sql = str_replace( 'utf8mb4', 'utf8', $sql );
        }

        $queries = DB::split_sql( $sql );

        foreach( $queries as $query ) {
            $this->db->query( $query );
        }
    }

    public function run() {
        $this->init();

        if ( ! $this->run_versions_updates() ) {
            return false;
        }

        update_option( ARISTREAMQUIZ_VERSION_OPTION, $this->options->version );

        $this->ensure_crypt_key();
        $this->create_custom_themes_folder();

        return true;
    }

    private function ensure_crypt_key() {
        $crypt_key = Helper::get_crypt_key();

        if ( strlen( $crypt_key ) > 0 )
            return ;

        $crypt_key = Crypt::get_random_string();
        Helper::save_crypt_key( $crypt_key );
    }

    private function create_custom_themes_folder() {
        $upload_dir = wp_upload_dir();
        $theme_dir = $upload_dir['basedir'] . '/ari-stream-quiz-themes';

        if ( ! @file_exists( $theme_dir) ) {
            wp_mkdir_p( $theme_dir );
        }
    }

    protected function update_to_1_1_0() {
        if ( ! DB::column_exists( '#__asq_results', 'is_anonymous' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_results` ADD COLUMN `is_anonymous` tinyint(1) unsigned NOT NULL DEFAULT "0"',
                    $this->db->prefix
                )
            );
        }

        if ( ! DB::index_exists( '#__asq_results', 'anonymous' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_results` ADD INDEX `anonymous` (`is_anonymous`)',
                    $this->db->prefix
                )
            );
        }
    }

    protected function update_to_1_3_0() {
        $utf8mb4_supported = DB::is_utf8mb4_supported();
        $collation = $utf8mb4_supported ? 'utf8mb4_unicode_ci' : 'utf8_general_ci';

        if ( ! DB::column_exists( '#__asq_results', 'quiz_session' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_results` ADD COLUMN `quiz_session` text COLLATE %2$s NOT NULL',
                    $this->db->prefix,
                    $collation
                )
            );
        }

        if ( ! DB::column_exists( '#__asq_results', 'ip_address' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_results` ADD COLUMN `ip_address` bigint(20) unsigned NOT NULL DEFAULT "0"',
                    $this->db->prefix
                )
            );
        }

        if ( ! DB::column_exists( '#__asq_results', 'session_key' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_results` ADD COLUMN `session_key` varchar(36) COLLATE %2$s NOT NULL DEFAULT ""',
                    $this->db->prefix,
                    $collation
                )
            );
        }

        if ( ! DB::column_exists( '#__asq_results', 'activity' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_results` ADD COLUMN `activity` set("FORCE_FACEBOOK","OPT_IN","SHARE_FACEBOOK","SHARE_TWITTER","SHARE_GOOGLEPLUS","SHARE_LINKEDIN","SHARE_PIN","SHARE_VK") COLLATE %2$s DEFAULT NULL',
                    $this->db->prefix,
                    $collation
                )
            );
        }

        if ( ! DB::column_exists( '#__asq_personality_results', 'is_primary' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_personality_results` ADD COLUMN `is_primary` tinyint(1) unsigned NOT NULL DEFAULT "0"',
                    $this->db->prefix
                )
            );
        }

        if ( ! DB::index_exists( '#__asq_results', 'completed_on' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_results` ADD INDEX `completed_on` (`end_date`)',
                    $this->db->prefix
                )
            );
        }

        if ( ! DB::index_exists( '#__asq_personality_results', 'is_primary' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_personality_results` ADD INDEX `is_primary` (`is_primary`)',
                    $this->db->prefix
                )
            );
        }

        $current_time_db_gmt = current_time( 'mysql', 1 );
        $this->db->query(
            $this->db->prepare(
                sprintf(
                    'INSERT INTO `%1$sasq_statistics` (quiz_id,start_date) SELECT `%1$sasq_quizzes`.quiz_id,%%s FROM `%1$sasq_quizzes` ON DUPLICATE KEY UPDATE `%1$sasq_statistics`.quiz_id = `%1$sasq_quizzes`.quiz_id',
                    $this->db->prefix
                ),
                $current_time_db_gmt
            )
        );

        $this->db->query(
            sprintf(
                'UPDATE `%1$sasq_personality_results` SET is_primary = 1 WHERE `order` = 0',
                $this->db->prefix
            )
        );
    }

    protected function update_to_1_3_3() {
        if ( ! DB::column_exists( '#__asq_quiz_sessions', 'prefetched' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_quiz_sessions` ADD COLUMN `prefetched` tinyint(1) unsigned NOT NULL DEFAULT "0"',
                    $this->db->prefix
                )
            );
        }
    }

    protected function update_to_1_3_5() {
        if ( ! DB::column_exists( '#__asq_results', 'user_id' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_results` ADD COLUMN `user_id` bigint(20) unsigned NOT NULL DEFAULT "0"',
                    $this->db->prefix
                )
            );
        }

        if ( ! DB::index_exists( '#__asq_results', 'user_id' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_results` ADD INDEX `user_id` (`user_id`)',
                    $this->db->prefix
                )
            );
        }
    }

    protected function update_to_1_4_0() {
        if ( ! DB::column_exists( '#__asq_questions', 'multiple' ) ) {
            $this->db->query(
                sprintf(
                    'ALTER TABLE `%1$sasq_questions` ADD COLUMN `multiple` tinyint(1) unsigned NOT NULL DEFAULT "0"',
                    $this->db->prefix
                )
            );
        }
    }

    protected function update_to_1_4_7() {
        $this->db->query(
            sprintf(
                'ALTER TABLE `%1$sasq_quiz_sessions` MODIFY `data` LONGTEXT NOT NULL',
                $this->db->prefix
            )
        );

        $this->db->query(
            sprintf(
                'ALTER TABLE `%1$sasq_quiz_sessions` MODIFY `state` LONGTEXT NOT NULL',
                $this->db->prefix
            )
        );

        $this->db->query(
            sprintf(
                'ALTER TABLE `%1$sasq_results` MODIFY `quiz_session` LONGTEXT NOT NULL',
                $this->db->prefix
            )
        );
    }

    protected function update_to_1_5_21() {
        $this->db->query(
            sprintf(
                'DELETE RQ FROM `%1$sasq_result_questions` RQ LEFT JOIN `%1$sasq_results` R ON RQ.result_id = R.result_id WHERE R.result_id IS NULL',
                $this->db->prefix
            )
        );
    }

    protected function update_to_1_5_48() {
        $this->db->query(
            sprintf(
                'ALTER TABLE `%1$sasq_questions` MODIFY `question_explanation` LONGTEXT NOT NULL',
                $this->db->prefix
            )
        );
    }
}
