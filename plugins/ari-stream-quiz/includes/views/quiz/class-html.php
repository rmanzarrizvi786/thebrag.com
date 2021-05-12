<?php
namespace Ari_Stream_Quiz\Views\Quiz;

use Ari_Stream_Quiz\Views\Base as Base;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Html extends Base {
    public $themes;

    public function display( $tmpl = null ) {
        $this->main_class = 'material-wrap-loading';

        $data = $this->get_data();
        $entity = $data['entity'];

        wp_enqueue_script( 'ari-cloner-ext' );
        wp_enqueue_media();

        //wp_enqueue_script( 'ari-quill' );
        //wp_enqueue_style( 'ari-quill-snow' );
        wp_enqueue_script( 'ari-trumbowyg' );
        wp_enqueue_style( 'ari-trumbowyg' );

        wp_enqueue_script( 'ari-smart-dropdown' );
        wp_enqueue_script( 'ari-page-quiz', ARISTREAMQUIZ_ASSETS_URL . 'common/pages/quiz.js', array( 'ari-streamquiz-app' ), ARISTREAMQUIZ_VERSION );

        if ( $entity->quiz_type == ARISTREAMQUIZ_QUIZTYPE_TRIVIA )
            wp_enqueue_script( 'ari-page-trivia-quiz', ARISTREAMQUIZ_ASSETS_URL . 'common/pages/trivia_quiz.js', array( 'ari-streamquiz-app', 'ari-cloner', 'ari-page-quiz' ), ARISTREAMQUIZ_VERSION );
        else if ( $entity->quiz_type == ARISTREAMQUIZ_QUIZTYPE_PERSONALITY )
            wp_enqueue_script( 'ari-page-personality-quiz', ARISTREAMQUIZ_ASSETS_URL . 'common/pages/personality_quiz.js', array( 'ari-streamquiz-app', 'ari-cloner', 'ari-page-quiz' ), ARISTREAMQUIZ_VERSION );

        $this->themes = Helper::get_themes();

        do_action( 'asq_admin_quiz_page_load', $entity );

        parent::display( $tmpl );
    }

    protected function get_app_options() {
        $data = $this->get_data();
        $quiz = $data['entity'];
        $is_new = $quiz->is_new();

        $app_options = array(
            'ajaxUrl' => admin_url( 'admin-ajax.php?action=ari_stream_quiz' ),

            'actionEl' => '#ctrl_action',

            'isNew' => $is_new,

            'messages' => array(
                'untitled' => __( 'Untitled', 'ari-stream-quiz' ),

                'cancelWarning' => __( 'All changes will be lost. Continue?', 'ari-stream-quiz' ),

                'emptyTitleWarning' => __( 'Enter a quiz name', 'ari-stream-quiz' ),

                'noQuestionsWarning' => __( 'Populate the quiz with questions', 'ari-stream-quiz' ),

                'emptyQuestionWarning' => __( 'Question should contain a text or an image and answers', 'ari-stream-quiz' ),

                'noPersonalitiesWarning' => __( 'Add a personality', 'ari-stream-quiz' ),

                'loading' => __( 'Loading...', 'ari-stream-quiz' ),

                'zapierWebhookUrlWarning' => __( 'Enter a valid Webhook URL', 'ari-stream-quiz' ),

                'zapierTestFailed' => __( 'Webhook validation failed: {{status}}', 'ari-stream-quiz' ),

                'zapierChecked' => __( 'Success', 'ari-stream-quiz' ),
            )
        );

        if ( ! $is_new ) {
            $questions = array_values( $quiz->questions );
            foreach ( $questions as &$question )
                $question->answers = array_values( $question->answers );

            $app_options['questions'] = array( 'questions' => $questions );

            if ( $quiz->quiz_image_id > 0 ) {
                $app_options['quizImage'] = $quiz->quiz_image;
            }

            if ( $quiz->quiz_type == ARISTREAMQUIZ_QUIZTYPE_TRIVIA ) {
                $result_templates = array_map(
                    function( $val ) {
                        if ( ARISTREAMQUIZ_RESULTTEMPLATE_MAXSCORE == $val->end_point )
                            $val->end_point = '';

                        return $val;
                    },
                    $quiz->result_templates
                );

                $app_options['results'] = array(
                    'results' => $result_templates
                );
            } else if ( $quiz->quiz_type == ARISTREAMQUIZ_QUIZTYPE_PERSONALITY ) {
                $app_options['personalities'] = array(
                    'personalities' => $quiz->personalities
                );
            }
        }

        return $app_options;
    }
}
