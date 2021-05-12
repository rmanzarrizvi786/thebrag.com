<?php
namespace Ari_Stream_Quiz\Models;

use Ari\Models\Model as Model;
use Ari_Stream_Quiz\Models\Quiz as Quiz_Model;
use Ari_Stream_Quiz\Helpers\Settings as Settings;
use Ari\Utils\Request as Request;
use Ari\Utils\Utils as Utils;
use Ari\Cache\Lite as Lite_Cache;

class Quiz_Session extends Model {
    protected $data = null;

    protected function populate_state() {
        if ( ! empty( $this->state['id'] ) ) {
            $quiz_id = $this->state['id'];

            if ( Request::exists( 'asq_replay') && $quiz_id == Request::get_var( 'asq_replay' ) ) {
                $this->state['replay'] = true;
            } else if ( Lite_Cache::exists( 'asq_sessions' ) ) {
                $sessions = Lite_Cache::get( 'asq_sessions' );

                if ( isset( $sessions[$quiz_id] ) ) {
                    $current_quiz_id = Lite_Cache::get( 'current_quiz_id', 0 );
                    $session_key = $sessions[$quiz_id];

                    $session = $this->get_session( $session_key, $this->state['id'] );
                    if ( ! empty( $session ) ) {
                        $this->state['session'] = $session;
                        if ( $quiz_id == $current_quiz_id && ! $session->is_completed() )
                            $this->state['navigate_to'] = true;
                    }
                }
            }
        }
    }

    public function data() {
        if ( ! is_null( $this->data ) )
            return $this->data;

        $quiz_model = new Quiz_Model(
            array(
                'class_prefix' => $this->options->class_prefix,

                'disable_state_load' => true,
            )
        );

        $id = $this->get_state( 'id' );
        $col = intval($this->get_state( 'col', Settings::get_option( 'shortcode_quiz_column_count', 2 ) ), 10);
        $quiz = $quiz_model->get_quiz( $id );

        if ( $col < 1)
            $col = 1;

        $replay = (bool)$this->get_state( 'replay', false );
        $session = $this->get_state( 'session', null );
        $continue_session = ! empty ( $session );
        $prefetch_session = Settings::get_option( 'prefetch_quiz_session' );

        if ( $replay || $continue_session )
            $quiz->start_immediately = true;
        else if ( Request::is_prefetch_request() )
            $quiz->start_immediately = false;

        if ( $quiz->start_immediately || $prefetch_session ) {
            if ( is_null( $session ) ) {
                $session = $this->create_session( $id, $prefetch_session );
            }
        }

        $inline_scripts = $this->get_state( 'inline_scripts', false );

        if ( '0' === $inline_scripts )
            $inline_scripts = false;
        else
            $inline_scripts = (bool) $inline_scripts;

        $data = array(
            'id' => $this->get_state( 'id' ),

            'hide_title' => (bool)$this->get_state( 'hide_title', (bool)Settings::get_option( 'shortcode_quiz_hide_title', false ) ),

            'column_count' => $col,

            'quiz' => $quiz,

            'navigate_to' => (bool)$this->get_state( 'navigate_to', false ),

            'replay' => $replay,

            'session' => $session,

            'continue_session' => $continue_session,

            'inline_scripts' => $inline_scripts,
        );

        $this->data = $data;

        return $data;
    }

    public function prepare_session_data( $quiz_id ) {
        $quiz_model = new Quiz_Model(
            array(
                'class_prefix' => $this->options->class_prefix,

                'disable_state_load' => true,
            )
        );
        $quiz = $quiz_model->get_quiz( $quiz_id );

        if ( is_null( $quiz ) ) {
            return null;
        }

        $data = new \stdClass();
        $data->pages = array();

        $shuffle_answers = (bool)$quiz->shuffle_answers;
        $random = (bool)$quiz->random_questions;
        $question_number = $quiz->random_question_count;
        $question_per_page = (bool)$quiz->use_paging ? $quiz->questions_per_page : 0;

        $questions =& $quiz->questions;

        $question_count = count( $questions );
        if ( $random ) {
            shuffle( $questions );

            if ( $question_number > 0 && $question_count > $question_number ) {
                $questions = array_slice( $questions, 0, $question_number );
                $question_count = $question_number;
            }
        }

        if ( $shuffle_answers ) {
            foreach ( $questions as $question ) {
                shuffle( $question->answers );
            }
        }

        if ( $question_per_page > 0 ) {
            $page_count = ceil( $question_count / $question_per_page );

            for ( $i = 0; $i < $page_count; $i++ ) {
                $page = new \stdClass();
                $page->questions = array_slice( $questions, $question_per_page * $i, $question_per_page );

                $data->pages[] = $page;
            }
        } else {
            $page = new \stdClass();
            $page->questions = $questions;

            $data->pages[] = $page;
        }

        $session = new \stdClass();
        $session->pages = array();
        $session->quiz_meta = new \stdClass();
        if ( $quiz->quiz_type == ARISTREAMQUIZ_QUIZTYPE_TRIVIA ) {
            $question_number = 1;
            foreach ( $data->pages as $page ) {
                $current_page = array(
                    'questions' => array(),
                );

                $question_order = 0;
                foreach ( $page->questions as $question ) {
                    $question_has_image = $question->image_id > 0;
                    $current_question = array(
                        'answers' => array(),

                        'explanation' => '',

                        'question_id' => $question->question_id,

                        'question_title' => $question->question_title,

                        'image' => $question_has_image ? $question->image : null,

                        'has_image' => $question_has_image,

                        'order' => $question_order,

                        'question_number' => $question_number,

                        'multiple' => $question->multiple,
                    );

                    $has_answer_with_image = false;
                    foreach ( $question->answers as $answer ) {
                        if ( $answer->image_id > 0 ) {
                            $has_answer_with_image = true;
                            break;
                        }
                    }

                    $current_question['has_answer_with_image'] = $has_answer_with_image;

                    $answer_order = 0;
                    foreach ( $question->answers as $answer ) {
                        $answer_has_image = $answer->image_id > 0;
                        $current_question['answers'][$answer->answer_id] = (object)array(
                            'answer_id' => $answer->answer_id,

                            'question_id' => $answer->question_id,

                            'answer_title' => $answer->answer_title,

                            'correct' => $answer->answer_correct,

                            'image' => $answer_has_image ? $answer->image : null,

                            'has_image' => $answer_has_image,

                            'has_answer_with_image' => $has_answer_with_image,

                            'order' => $answer_order,
                        );

                        ++$answer_order;
                    }

                    if ( $question->show_explanation && $question->question_explanation ) {
                        $current_question['explanation'] = $question->question_explanation;
                    }

                    $current_page['questions'][$question->question_id] = (object)$current_question;

                    ++$question_order;
                    ++$question_number;
                }

                $session->pages[] = (object)$current_page;
            }

            $result_templates = array();

            foreach ( $quiz->result_templates as $result_template ) {
                $result_templates[] = (object)array(
                    'title' => $result_template->template_title,

                    'content' => $result_template->template_content,

                    'image_id' => $result_template->image_id,

                    'image' => $result_template->image_id > 0 ? $result_template->image : array(),

                    'end_point' => $result_template->end_point,
                );
            }

            $session->quiz_meta->resultTemplates = $result_templates;
        } else if ( $quiz->quiz_type == ARISTREAMQUIZ_QUIZTYPE_PERSONALITY ) {
            $question_number = 1;
            $multiple_answers_selection = $quiz->quiz_meta->personality->multiple_answers_selection;
            foreach ( $data->pages as $page ) {
                $current_page = array(
                    'questions' => array(),
                );

                $question_order = 0;
                foreach ( $page->questions as $question ) {
                    $question_has_image = $question->image_id > 0;
                    $current_question = array(
                        'answers' => array(),

                        'question_id' => $question->question_id,

                        'question_title' => $question->question_title,

                        'image' => $question_has_image ? $question->image : null,

                        'has_image' => $question_has_image,

                        'order' => $question_order,

                        'question_number' => $question_number,

                        'multiple' => count( $question->answers ) > 1 ? $multiple_answers_selection || $question->multiple : false,
                    );

                    $has_answer_with_image = false;
                    foreach ( $question->answers as $answer ) {
                        if ( $answer->image_id > 0 ) {
                            $has_answer_with_image = true;
                            break;
                        }
                    }

                    $current_question['has_answer_with_image'] = $has_answer_with_image;

                    $answer_order = 0;
                    foreach ( $question->answers as $answer ) {
                        $answer_personalities = array();

                        foreach ( $answer->answer_personalities as $personality ) {
                            $answer_personalities[$personality->personality_id] = array(
                                'score' => $personality->score,
                            );
                        }

                        $answer_has_image = $answer->image_id > 0;
                        $current_question['answers'][$answer->answer_id] = (object)array(
                            'answer_id' => $answer->answer_id,

                            'question_id' => $answer->question_id,

                            'answer_title' => $answer->answer_title,

                            'image' => $answer_has_image ? $answer->image : null,

                            'has_image' => $answer_has_image,

                            'has_answer_with_image' => $has_answer_with_image,

                            'personalities' => $answer_personalities,

                            'order' => $answer_order,
                        );
                        ++$answer_order;
                    }

                    $current_page['questions'][$question->question_id] = (object)$current_question;
                    ++$question_order;
                    ++$question_number;
                }

                $session->pages[] = (object)$current_page;
            }

            $personalities = array();

            foreach ( $quiz->personalities as $personality ) {
                $personalities[$personality->personality_id] = (object)array(
                    'title' => $personality->personality_title,

                    'content' => $personality->personality_content,

                    'image_id' => $personality->image_id,

                    'image' => $personality->image_id > 0 ? $personality->image : array(),
                );
            }

            $session->quiz_meta->personalities = $personalities;
        }

        $session->question_count = $question_count;
        $session->page_count = count( $session->pages );

        $session = apply_filters( 'asq_prepare_quiz_data', $session, $quiz );

        return $session;
    }

    public function get_session( $session_key, $quiz_id ) {
        $entity = $this->entity();

        if (
            ! $entity->custom_load( array( 'session_key' => $session_key, 'quiz_id' => $quiz_id ) ) ||
            ( $entity->quiz_id != $quiz_id || $entity->session_key != $session_key )
        ) {
            $entity = null;
        }

        return $entity;
    }

    public function save_sessions( $sessions ) {
        if ( ! is_array( $sessions ) )
            return false;

        $db = $this->db;
        $result = array(
            'result' => null,

            'sessions' => array(), // quiz_id => session_key
        );

        foreach ( $sessions as $quiz_id => $quiz_session ) {
            $quiz_id = intval( $quiz_id, 10 );

            if ( $quiz_id < 1 )
                continue ;

            $current_result = null;
            $session_key = Utils::get_value( $quiz_session, 'sessionKey' );
            $state = Utils::get_value( $quiz_session, 'data' );
            if ( ! is_string( $state ) )
                $state = json_encode( $state );

            if ( ! empty ( $session_key ) ) {
                $query = $db->prepare(
                    sprintf(
                        'UPDATE `%1$sasq_quiz_sessions` SET `state` = %%s,`modified` = %%s WHERE `session_key` = %%s AND `quiz_id` = %%d AND completed IS NULL',
                        $db->prefix
                    ),
                    $state,
                    current_time( 'mysql', 1 ),
                    $session_key,
                    $quiz_id
                );
                $current_result = $db->query( $query );
                $current_result = ( $current_result !== false && $current_result > 0 );

                $result['sessions'][$quiz_id] = $session_key;

                if ( false !== $current_result && 0 === $current_result ) {
                    $current_result = false;
                } else {
                    $current_result = true;
                }
            } else {
                $current_result = false;
            }

            if ( false !== $result['result'] ) {
                $result['result'] = $current_result;
            }
        }

        $result['result'] = !!$result['result'];

        return $result;
    }

    public function create_session( $quiz_id, $prefetched = false ) {
        $session_data = $this->prepare_session_data( $quiz_id );
        if ( empty( $session_data ) )
            return false;

        $session = $this->entity();

        if ( ! $session->bind(
            array(
                'quiz_id' => $quiz_id,

                'data' => $session_data,

                'prefetched' => $prefetched ? 1 : 0,
            )
        ) ) {
            return false;
        }

        if ( ! $session->store() )
            return false;

        return $session;
    }

    public function delete_session( $session_key, $quiz_id ) {
        $quiz_id = intval( $quiz_id, 10 );

        if ( $quiz_id < 1 || empty( $session_key ) )
            return false;

        $query = $this->db->prepare(
            sprintf(
                'DELETE FROM %1$sasq_quiz_sessions WHERE session_key = %%s AND quiz_id = %%d',
                $this->db->prefix
            ),
            $session_key,
            $quiz_id
        );

        $result = $this->db->query( $query );

        return ( $result !== false && $result > 0 );
    }

    public function complete_session( $session_key, $quiz_id, $state ) {
        $session = $this->get_session( $session_key, $quiz_id );

        if ( empty( $session ) || $session->completed )
            return false;

        return $session->complete( $state );
    }

    public function add_activity( $session_key, $activity, $quiz_id, $data ) {
        $session_entity = $this->get_session( $session_key, $quiz_id );

        if ( empty( $session_entity ) )
            return false;

        if ( 'OPT_IN' === $activity ) {
            do_action( 'asq_session_user_data', $data );
        }

        return $session_entity->add_activity( $activity );
    }

    public function delete_old_sessions( $ttl = 86400 ) {
        $date_limit = gmdate( 'Y-m-d H:i:s', time() - $ttl );

        $query = $this->db->prepare(
            sprintf(
                'DELETE FROM `%1$sasq_quiz_sessions` WHERE created < %%s',
                $this->db->prefix
            ),
            $date_limit
        );

        $result = $this->db->query( $query );

        return ( $result !== false );
    }

    public function start( $session_key, $quiz_id, $date ) {
        $session = $this->get_session( $session_key, $quiz_id );

        if ( empty( $session ) )
            return false;

        return $session->start( $date );
    }
}
