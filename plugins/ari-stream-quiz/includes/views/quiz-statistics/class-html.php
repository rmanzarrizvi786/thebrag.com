<?php
namespace Ari_Stream_Quiz\Views\Quiz_Statistics;

use Ari_Stream_Quiz\Views\Base as Base;

class Html extends Base {
    public $quiz;

    public $quiz_stat;

    public function display( $tmpl = null ) {
        $data = $this->get_data();
        $quiz = $data['quiz'];

        $this->quiz = $quiz;
        $this->quiz_stat = $data['stat'];

        $this->set_title( __( 'Statistics', 'ari-stream-quiz' ) . ': ' . $quiz->get_filtered_title() );

        wp_enqueue_script( 'ari-chartist' );
        wp_enqueue_style( 'ari-chartist' );

        wp_enqueue_script( 'ari-page-quiz-stat', ARISTREAMQUIZ_ASSETS_URL . 'common/pages/stat.js', array( 'ari-streamquiz-app' ), ARISTREAMQUIZ_VERSION );

        parent::display( $tmpl );
    }

    protected function get_app_options() {
        $data = $this->get_data();
        $quiz_stat = $data['stat'];

        $app_options = array(
            'actionEl' => '#ctrl_action',

            'ajaxUrl' => admin_url( 'admin-ajax.php?action=ari_stream_quiz' ),

            'quizId' => $this->quiz->quiz_id,

            'quizType' => $this->quiz->quiz_type,

            'messages' => array(
                'views' => __( 'Views', 'ari-stream-quiz' ),

                'started' => __( 'Started', 'ari-stream-quiz' ),

                'completed' => __( 'Completed', 'ari-stream-quiz' ),

                'opt_in' => __( 'Opt-in', 'ari-stream-quiz' ),

                'shares' => __( 'Shares', 'ari-stream-quiz' ),

                'resetConfirm' => __( 'Do you want to reset quiz statistics?', 'ari-stream-quiz' ),

                'dataLoadFailed' => __( 'Data can not be loaded, try again.', 'ari-stream-quiz' ),
            ),

            'stat' => array(
                'views' => $quiz_stat->impression,

                'started' => $quiz_stat->start,

                'completed' => $quiz_stat->complete,

                'opt_in' => $quiz_stat->opt_in,

                'shares' => $quiz_stat->share,
            ),
        );

        if ( ARISTREAMQUIZ_QUIZTYPE_PERSONALITY == $this->quiz->quiz_type ) {
            $results_stat = $this->get_stat_results_personality();

            $app_options['stat_results'] = $results_stat;
        }

        return $app_options;
    }

    protected function get_stat_results_personality() {
        $stat = $this->quiz->get_results_stat();

        return $stat;
    }
}