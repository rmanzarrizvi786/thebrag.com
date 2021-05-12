<?php
namespace Ari_Stream_Quiz\Views\Results;

use Ari_Stream_Quiz\Views\Base as Base;
use Ari_Stream_Quiz\Controls\Grid\Grid as Grid;
use Ari_Stream_Quiz\Controls\Paging\Paging as Paging;
use Ari_Stream_Quiz\Helpers\Helper as Helper;

class Html extends Base {
    public $grid = null;

    public function display( $tmpl = null ) {
        $this->set_title( __( 'Results', 'ari-stream-quiz' ) );

        wp_enqueue_script( 'ari-page-results', ARISTREAMQUIZ_ASSETS_URL . 'common/pages/results.js', array( 'ari-streamquiz-app' ), ARISTREAMQUIZ_VERSION );

        $this->grid = $this->create_grid();
        $this->paging = $this->create_paging();

        parent::display( $tmpl );
    }

    protected function get_app_options() {
        $app_options = array(
            'actionEl' => '#ctrl_action',

            'messages' => array(
                'deleteConfirm' => __( 'Do you want to delete the selected quiz results?', 'ari-stream-quiz' ),

                'bulkDeleteConfirm' => __( 'Do you want to delete the selected results?', 'ari-stream-quiz' ),

                'deleteAllConfirm' => __( 'Do you want to delete all results?', 'ari-stream-quiz' ),

                'selectResultsWarning' => __( 'Select at least one quiz result please.', 'ari-stream-quiz' ),
            )
        );

        return $app_options;
    }

    private function create_paging() {
        $data = $this->get_data();
        $filter = $data['filter'];

        $paging = new Paging(
            array(
                'page_num' => $filter['page_num'],

                'page_size' => $filter['page_size'],

                'count' => $data['count'],
            )
        );

        return $paging;
    }

    private function create_grid() {
        $data = $this->get_data();
        $filter = $data['filter'];

        $order_by = $filter['order_by'];
        $order_dir = $filter['order_dir'];

        $can_edit_other_quizzes = Helper::can_edit_other_quizzes();
        $current_user_id = get_current_user_id();
        $time_ago_format = _x( '%s ago', '%s = human-readable time difference', 'ari-stream-quiz' );

        $remove_url_params = array( 'filter' );
        $result_url = Helper::build_url(
            array(
                'page' => 'ari-stream-quiz-quiz-result-details',
                'id' => '__resultId__'
            ),
            $remove_url_params
        );

        $grid = new Grid(
            'gridResults',

            array(
                'options' => array(
                    'order_by' => $order_by,

                    'order_dir' => $order_dir,
                ),

                'columns' => array(
                    array(
                        'key' => 'result_id',

                        'header' => function() {
                            $postfix = uniqid( '_hd', false );

                            return sprintf(
                                '<input type="checkbox" class="filled-in select-all-items select-item" id="chkAll%1$s" autocomplete="off" /><label for="chkAll%1$s"> </label>',
                                $postfix
                            );
                        },

                        'column' => function( $val, $data ) use ( $can_edit_other_quizzes, $current_user_id ) {
                            $can_edit = $can_edit_other_quizzes || $current_user_id == $data->author_id;

                            if ( ! $can_edit )
                                return '';

                            return sprintf(
                                '<input type="checkbox" autocomplete="off" class="filled-in select-item" name="result_id[]" id="%1$s" value="%2$d" /><label for="%1$s"> </label>',
                                'chkResult_' . $val,
                                $val
                            );
                        },

                        'header_class' => 'manage-column select-column',

                        'class' => 'select-column',
                    ),

                    array(
                        'key' => 'quiz_title',

                        'header_class' => 'manage-column column-primary',

                        'class' => 'column-primary',

                        'header' => __( 'Title', 'ari-stream-quiz' ),

                        'column' => function( $val, $data ) use ( $can_edit_other_quizzes, $current_user_id, $result_url ) {
                            $html = '';
                            $can_edit = $can_edit_other_quizzes || $current_user_id == $data->author_id;
                            $quiz_title = $data->quiz_title_filtered ? $data->quiz_title_filtered : $data->quiz_title;
                            $result_url = str_replace( '__resultId__', $data->result_id, $result_url );

                            /*if ( $can_edit ) */{
                                $html .= sprintf(
                                    '<a class="quiz-title" href="%1$s">%2$s</a>',
                                    $result_url,
                                    $quiz_title
                                );
                            }/* else {
                                $html .= sprintf(
                                    '<span class="quiz-title">%1$s</span>',
                                    $quiz_title
                                );
                            }*/

                            $html .= '<div class="grid-row-actions">';

                            $html .= sprintf(
                                '<a href="%1$s">%2$s</a>',
                                $result_url,
                                __( 'Details', 'ari-stream-quiz' )
                            );

                            if ( $can_edit ) {
                                $html .= sprintf(
                                    ' | <a href="#" class="red-text btn-result-delete" data-result-id="%1$d">%2$s</a>',
                                    $data->result_id,
                                    __( 'Delete', 'ari-stream-quiz' )
                                );
                            }

                            $html .= '</div>';

                            $html .= sprintf(
                                '<button type="button" class="toggle-row"><span class="screen-reader-text">%1$s</span></button>',
                                __( 'Show more details', 'ari-stream-quiz' )
                            );

                            return $html;
                        }
                    ),

                    array(
                        'key' => 'quiz_type',

                        'header_class' => 'manage-column',

                        'header' => __( 'Type', 'ari-stream-quiz' ),

                        'column' => function( $val, $data ) {
                            return '<div class="chip quiz-type-' . strtolower( $val ) . '">' . Helper::quiz_type_nicename( $val ) . '</div>';
                        }
                    ),

                    array(
                        'key' => 'username',

                        'header_class' => 'manage-column',

                        'header' => __( 'Username', 'ari-stream-quiz' ),

                        'column' => function( $val, $data ) {
                            return $val ? $val : '&nbsp;';
                        },
                    ),

                    array(
                        'key' => 'email',

                        'header_class' => 'manage-column',

                        'header' => __( 'Email', 'ari-stream-quiz' ),

                        'column' => function( $val, $data ) {
                            return $val ? $val : '&nbsp;';
                        },
                    ),

                    array(
                        'key' => 'end_date',

                        'title' => __( 'Completed on', 'ari-stream-quiz' ),

                        'header_class' => 'manage-column',

                        'header' => function() {
                            return '<div class="column-wrapper">' . __( 'Completed on', 'ari-stream-quiz' ) . '</div>';
                        },

                        'column' => function( $val, $data ) use ( $time_ago_format ) {
                            return ARISTREAMQUIZ_DB_EMPTYDATE != $val ? sprintf( $time_ago_format, human_time_diff( mysql2date( 'G', $val ), current_time( 'timestamp', 1 ) ) ) : '—';
                        },

                        'sortable' => true,
                    ),

                    array(
                        'key' => 'result',

                        'header_class' => 'manage-column',

                        'header' => __( 'Result', 'ari-stream-quiz' ),
                    ),
                )
            )
        );

        return $grid;
    }
}
