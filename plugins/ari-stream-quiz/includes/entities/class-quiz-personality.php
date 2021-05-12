<?php
namespace Ari_Stream_Quiz\Entities;

use Ari\Wordpress\Helper as WP_Helper;
use Ari\Utils\Utils as Utils;
use Ari\Template\Template as Template;
use Ari_Stream_Quiz\Helpers\Settings as Settings;

class Quiz_Personality extends Quiz {
    public $quiz_type = ARISTREAMQUIZ_QUIZTYPE_PERSONALITY;

    public $personalities = array();

    protected $json_fields = array( 'questions', 'personalities' );

    function __construct( $db ) {
        $this->meta_fields['personality'] = array(
            'multiple_answers_selection' => false,

            'show_several' => array(
                'enabled' => false,

                'count' => 1,

                'only_main_content' => false,
            ),
        );

        parent::__construct( $db );
    }

    public function bind( $data, $ignore = array() ) {
        return parent::bind( $data, $ignore );
    }

    public function store( $force_insert = false ) {
        $db = $this->db;
        $is_new = $this->is_new();

        $result = parent::store( $force_insert );

        if ( ! $result )
            return $result;

        if ( ! $is_new ) {
            $query = sprintf(
                'DELETE Q,A,AP FROM `%1$sasq_questions` Q LEFT JOIN `%1$sasq_answers` A ON Q.question_id = A.question_id LEFT JOIN `%1$sasq_answers_personalities` AP ON A.answer_id = AP.answer_id WHERE Q.quiz_id = %2$d',
                $db->prefix,
                $this->quiz_id
            );
            $db->query( $query );

            $query = sprintf(
                'DELETE FROM `%1$sasq_personalities` WHERE quiz_id = %2$d',
                $db->prefix,
                $this->quiz_id
            );
            $db->query( $query );
        }

        $order = 0;
        $personalities_mapping = array(); // guid -> id
        foreach ( $this->personalities as $personality ) {
            $personality_entity = new Personality( $db );
            $personality_entity->bind( $personality );
            $personality_entity->quiz_id = $this->quiz_id;

            if ( $is_new )
                $personality_entity->personality_id = 0;

            if ( $personality_entity->validate() ) {
                $personality_entity->personality_order = $order;
                $personality_entity->store( true );

                $personalities_mapping[$personality_entity->personality_guid] = $personality_entity->personality_id;

                ++$order;
            }
        }

        $order = 0;
        $filtered_questions = array();
        foreach ( $this->questions as $question ) {
            foreach ( $question->answers as $answer ) {
                if ( empty( $answer->answer_guid ) )
                    $answer->answer_guid = Utils::guid();
            }

            $question_entity = new Question( $db );
            $question_entity->bind( $question );

            if ( $question_entity->is_empty() ) {
                continue ;
            }

            $question_entity->quiz_id = $this->quiz_id;

            if ( $is_new )
                $question->question_id = 0;

            if ( $question_entity->validate() ) {
                $question_entity->question_order = $order;
                $question_entity->store( true );

                $question->question_id = $question_entity->question_id;

                ++$order;

                $filtered_questions[] = $question;
            }
        }

        $this->questions = $filtered_questions;

        $query = sprintf(
            'SELECT answer_guid,answer_id FROM `%1$sasq_answers` WHERE quiz_id = %2$d',
            $db->prefix,
            $this->quiz_id
        );
        $answer_keys = $db->get_results(
            $query,
            OBJECT_K
        );

        $answers_personalities = array();
        foreach ( $this->questions as $question ) {
            if ( empty( $question->question_id ) )
                continue ;

            foreach ( $question->answers as $answer ) {
                $answer_id = $answer->answer_id;

                if ( empty( $answer_id ) && ! empty( $answer->answer_guid) && isset( $answer_keys[$answer->answer_guid] ) ) {
                    $answer_id = $answer_keys[$answer->answer_guid]->answer_id;
                }

                if ( empty( $answer_id ) || !is_array( $answer->answer_personalities ) )
                    continue ;

                foreach ( $answer->answer_personalities as $answer_personality ) {
                    $personality_id = isset( $personalities_mapping[$answer_personality->personality_guid] )
                        ? $personalities_mapping[$answer_personality->personality_guid]
                        : 0;

                    $answer_personality_data = $db->prepare(
                        join(
                            ',',
                            array(
                                '%d', // 1 - answer_personality_id
                                '%d', // 2 - answer_id
                                '%d', // 3 - personality_id
                                '%s', // 4 - personality_guid
                                '%d', // 5 - score
                                '%d', // 6 - question_id
                                '%d', // 7 - quiz_id
                            )
                        ),
                        $answer_personality->answer_personality_id, // 1
                        $answer_id, // 2
                        $personality_id, // 3
                        $answer_personality->personality_guid, // 4
                        $answer_personality->score, // 5
                        $question->question_id, // 6
                        $this->quiz_id // 7
                    );

                    $answer_personality_data = '(' . $answer_personality_data . ')';
                    $answers_personalities[] = $answer_personality_data;
                }
            }
        }

        if ( count( $answers_personalities ) > 0 ) {
            $query = sprintf(
                'INSERT INTO `%1$sasq_answers_personalities` (answer_personality_id,answer_id,personality_id,personality_guid,score,question_id,quiz_id) VALUES %2$s',
                $db->prefix,
                join( ',', $answers_personalities )
            );
            $db->query( $query );
        }

        if ( $this->question_count != count( $this->questions ) ) {
            $this->question_count = count( $this->questions );

            $this->db->update(
                $this->db_tbl,
                array(
                    'question_count' => $this->question_count,
                ),
                array(
                    'quiz_id' => $this->quiz_id,
                )
            );
        }

        return true;
    }

    protected function populate_entity( $images ) {
        $db = $this->db;

        $personalities = $db->get_results(
            sprintf(
                'SELECT * FROM `%1$sasq_personalities` WHERE quiz_id = %2$d',
                $db->prefix,
                $this->quiz_id
            ),
            OBJECT
        );

        if ( is_array( $personalities ) ) {
            foreach ( $personalities as $personality ) {
                if ( $personality->image_id > 0 )
                    $personality->image = $this->get_image( $personality->image_id, $images );
            }
        } else {
            $personalities = array();
        }

        $questions = $db->get_results(
            sprintf(
                'SELECT Q.* FROM `%1$sasq_questions` Q WHERE Q.quiz_id = %2$d ORDER BY Q.question_order ASC',
                $db->prefix,
                $this->quiz_id
            ),
            OBJECT_K
        );

        $answers = $db->get_results(
            sprintf(
                'SELECT A.* FROM `%1$sasq_answers` A WHERE A.quiz_id = %2$d ORDER BY A.question_id,A.answer_order ASC',
                $db->prefix,
                $this->quiz_id
            ),
            OBJECT_K
        );

        $answers_personalities = $db->get_results(
            sprintf(
                'SELECT AP.* FROM `%1$sasq_answers_personalities` AP INNER JOIN `%1$sasq_personalities` P ON AP.personality_id = P.personality_id WHERE AP.quiz_id = %2$d ORDER BY AP.answer_id,P.personality_order ASC',
                $db->prefix,
                $this->quiz_id
            ),
            OBJECT
        );

        if ( is_array( $answers ) && is_array( $answers_personalities ) ) {
            foreach ( $answers_personalities as $answer_personalities ) {
                $answer_id = $answer_personalities->answer_id;

                if ( ! isset( $answers[$answer_id] ) )
                    continue ;

                $answer = $answers[$answer_id];
                if ( ! isset ( $answer->answer_personalities ) )
                    $answer->answer_personalities = array();

                $answer->answer_personalities[] = $answer_personalities;
            }
        }

        $question_answers = array();

        if ( is_array( $answers ) ) {
            foreach ( $answers as $answer ) {
                $question_id = $answer->question_id;

                if ( ! isset( $question_answers[$question_id] ) )
                    $question_answers[$question_id] = array();

                if ( $answer->image_id > 0 )
                    $answer->image = $this->get_image( $answer->image_id, $images );

                $question_answers[$question_id][$answer->answer_id] = $answer;
            }
        }

        if ( is_array( $questions ) ) {
            foreach ( $questions as $question ) {
                $question_id = $question->question_id;
                $question->multiple = ( $question->multiple != 0 );

                if ( isset( $question_answers[$question_id] ) )
                    $question->answers = $question_answers[$question_id];
                else
                    $question->answers = array();

                if ( $question->image_id > 0 )
                    $question->image = $this->get_image( $question->image_id, $images );
            }
        } else {
            $questions = array();
        }

        $this->questions = $questions;
        $this->personalities = $personalities;

        return true;
    }

    protected function prepare_copy( $quiz_copy ) {
        $new_personalities = array();
        $new_questions = array();

        foreach ( $this->personalities as $personality ) {
            $new_personality = clone $personality;

            $new_personality->personality_id = 0;
            $new_personality->quiz_id = 0;

            $new_personalities[] = $new_personality;
        }

        foreach ( $this->questions as $question ) {
            $new_question = clone $question;

            $new_question->question_id = 0;
            $new_question->quiz_id = 0;

            foreach ( $new_question->answers as $answer ) {
                $answer->answer_id = 0;
                $answer->question_id = 0;
                $answer->quiz_id = 0;

                foreach ( $answer->answer_personalities as $answer_personality ) {
                    $answer_personality->answer_personality_id = 0;
                    $answer_personality->answer_id = 0;
                    $answer_personality->personality_id = 0;
                    $answer_personality->quiz_id = 0;
                }
            }

            $new_questions[] = $new_question;
        }

        $quiz_copy->questions = $new_questions;
        $quiz_copy->personalities = $new_personalities;
    }

    public function validate() {
        $is_valid = parent::validate();

        if ( ! $is_valid )
            return $is_valid;

        return true;
    }

    public function get_results_stat() {
        $query = $this->db->prepare(
            sprintf(
                'SELECT personality_id,COUNT(*) AS `count` FROM `%1$sasq_personality_results` PR INNER JOIN `%1$sasq_results` R ON PR.result_id = R.`result_id` WHERE R.`quiz_id` = %%d AND PR.is_primary = 1 GROUP BY PR.personality_id',
                $this->db->prefix
            ),
            $this->quiz_id
        );

        $stat = $this->db->get_results( $query, OBJECT_K );

        $results_stat = array();

        foreach ( $this->personalities as $personality ) {
            $personality_id = $personality->personality_id;
            $count = 0;

            if ( isset( $stat[$personality_id] ) ) {
                $stat_item = $stat[$personality_id];
                $count = $stat_item->count;
            }

            $results_stat[] = array(
                'personality_id' => $personality_id,

                'personality_title' => strip_tags( $personality->personality_title ),

                'count' => $count,
            );
        }

        return $results_stat;
    }

    protected function get_images() {
        $id_list = $this->db->get_col(
            sprintf(
                '(SELECT quiz_image_id FROM `%1$sasq_quizzes` WHERE quiz_id = %2$d AND quiz_image_id > 0)
                UNION
                (SELECT image_id FROM `%1$sasq_questions` WHERE quiz_id = %2$d AND image_id > 0)
                UNION
                (SELECT image_id FROM `%1$sasq_answers` WHERE quiz_id = %2$d AND image_id > 0)
                UNION
                (SELECT image_id FROM `%1$sasq_personalities` WHERE quiz_id = %2$d AND image_id > 0)',
                $this->db->prefix,
                $this->quiz_id
            )
        );

        $images = WP_Helper::get_attachment_list( $id_list, $this->image_size );

        return $images;
    }

    public function main_personality_count() {
        $count = 1;

        if ( $this->quiz_meta->personality->show_several->enabled ) {
            $show_count = intval( $this->quiz_meta->personality->show_several->count, 10 );
            if ( $show_count > -1 )
                $count = $show_count;
        }

        return $count;
    }

    protected function compose_mail_body( $template, $quiz_results ) {
        $summary = $this->get_result_summary( $quiz_results );
        $quiz_results['summary'] = join( '<hr />', $summary );

        $summary_primary = array_shift( $summary );
        $quiz_results['summaryPrimary'] = strlen( $summary_primary ) > 0 ? $summary_primary : '';
        $quiz_results['summarySecondary'] = join( '<hr />', $summary );

        return parent::compose_mail_body( $template, $quiz_results );
    }

    private function get_result_summary( $quiz_results ) {
        $summary = array();
        if ( ! is_array( $quiz_results ) || ! is_array( $quiz_results['resultPersonalities'] ) || count( $quiz_results['resultPersonalities'] ) == 0 )
            return $summary;

        $idx = 0;
        $only_main_personality_content = $this->quiz_meta->personality->show_several->only_main_content;
        $share_title = Settings::get_option( 'share_personality_title' );
        if ( $share_title )
            $share_title = preg_replace( '/\{\{([^{}]+)\}\}/', '{\$$1}', $share_title );

        foreach ( $quiz_results['resultPersonalities'] as $personality ) {
            $summary_item = '';

            $summary_item .= '<div class="quiz-result-item quiz-result-item' . $idx . '">';
            $summary_item .= '<h3 class="result-title">' . Template::parse( $share_title, $personality ) . '</h3>';

            if ( ! $only_main_personality_content || $idx == 0 ) {
                if ( ! empty( $personality['image'] ) )
                    $summary_item .= '<div class="result-image">' . $personality['image'] . '</div>';

                $summary_item .= '<div class="result-content">' . $personality['content'] . '</div>';
            }

            $summary_item .= '</div>';

            $summary[] = $summary_item;
            ++$idx;
        }

        return $summary;
    }
}
