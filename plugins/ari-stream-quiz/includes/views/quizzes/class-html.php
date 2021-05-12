<?php
namespace Ari_Stream_Quiz\Views\Quizzes;

use Ari_Stream_Quiz\Views\Base as Base;
use Ari_Stream_Quiz\Controls\Grid\Grid as Grid;
use Ari_Stream_Quiz\Controls\Paging\Paging as Paging;
use Ari_Stream_Quiz\Helpers\Helper as Helper;
use Ari\Utils\Date as Date_Helper;

class Html extends Base {
    public $preview_post_id = 0;

    public function display( $tmpl = null ) {
        $this->set_title( __( 'Quizzes', 'ari-stream-quiz' ) );

        wp_enqueue_script( 'ari-clipboard' );

        wp_enqueue_script( 'ari-page-quizzes', ARISTREAMQUIZ_ASSETS_URL . 'common/pages/quizzes.js', array( 'ari-streamquiz-app' ), ARISTREAMQUIZ_VERSION );

        $this->grid = $this->create_grid();
        $this->paging = $this->create_paging();

        parent::display( $tmpl );
    }

    protected function get_app_options() {
        $app_options = array(
            'actionEl' => '#ctrl_action',

            'messages' => array(
                'deleteConfirm' => __( 'Do you want to delete the selected quiz?', 'ari-stream-quiz' ),

                'copyConfirm' => __( 'Create a copy of the selected quiz?', 'ari-stream-quiz' ),

                'bulkDeleteConfirm' => __( 'Do you want to delete the selected quizzes?', 'ari-stream-quiz' ),

                'bulkCopyConfirm' => __( 'Create copies of the selected quizzes?', 'ari-stream-quiz' ),

                'selectQuizzesWarning' => __( 'Select at least one quiz please.', 'ari-stream-quiz' ),

                'shortcodeCopied' => __( 'Copied', 'ari-stream-quiz' ),

                'shortcodeCopyFailed' => __( 'Press Ctrl+C to copy', 'ari-stream-quiz' ),
            ),

            'preview' => $this->preview_post_id > 0 ? get_permalink( $this->preview_post_id ) : null
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
        $create_post_lbl = _x( 'Create <a href="%1$s" target="_blank">post</a> / <a href="%2$s" target="_blank">page</a>', '%1$s = new post link, %2$s = new page link', 'ari-stream-quiz' );

        $remove_url_params = array( 'filter', 'preview' );
        $edit_url = Helper::build_url(
            array(
                'page' => 'ari-stream-quiz-quiz',
                'action' => 'edit',
                'id' => '__quizId__'
            ),
            $remove_url_params
        );
        $preview_quiz_url = Helper::build_url(
            array(
                'page' => 'ari-stream-quiz-quizzes',
                'action' => 'preview',
                'post_id' => '__postId__',
            ),
            $remove_url_params
        );
        $stat_url = Helper::build_url(
            array(
                'page' => 'ari-stream-quiz-quiz-statistics',
                'id' => '__quizId__'
            ),
            $remove_url_params
        );

        $grid = new Grid(
            'gridQuizzes',

            array(
                'options' => array(
                    'order_by' => $order_by,

                    'order_dir' => $order_dir,
                ),

                'columns' => array(
                    array(
                        'key' => 'quiz_id',

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
                                '<input type="checkbox" autocomplete="off" class="filled-in select-item" name="quiz_id[]" id="%1$s" value="%2$d" /><label for="%1$s"> </label>',
                                'chkQuiz_' . $val,
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

                        'title' => __( 'Title', 'ari-stream-quiz' ),

                        'header' => function() {
                            return '<div class="column-wrapper">' . __( 'Title', 'ari-stream-quiz' ) . '</div>';
                        },

                        'column' => function( $val, $data ) use ( $can_edit_other_quizzes, $current_user_id, $edit_url, $stat_url, $preview_quiz_url ) {
                            $html = '';
                            $can_edit = $can_edit_other_quizzes || $current_user_id == $data->author_id;
                            $quiz_title = $data->quiz_title_filtered ? $data->quiz_title_filtered : $data->quiz_title;
                            $item_edit_url = str_replace( '__quizId__', $data->quiz_id, $edit_url );

                            if ( $can_edit ) {
                                $html .= sprintf(
                                    '<a class="quiz-title" href="%1$s">%2$s</a>',
                                    $item_edit_url,
                                    $quiz_title
                                );
                            } else {
                                $html .= sprintf(
                                    '<span class="quiz-title">%1$s</span>',
                                    $quiz_title
                                );
                            }

                            $html .= '<div class="grid-row-actions">';
                            if ( $can_edit ) {
                                $preview_item_url = $data->post_id > 0 ? str_replace( '__postId__', $data->post_id, $preview_quiz_url ) : '';

                                $html .= sprintf(
                                    '<a href="%1$s">%2$s</a> | ',
                                    $item_edit_url,
                                    __( 'Edit', 'ari-stream-quiz' )
                                );

                                $html .= sprintf(
                                    '<a href="#" class="btn-quiz-copy" data-quiz-id="%1$d">%2$s</a> | ',
                                    $data->quiz_id,
                                    __( 'Copy', 'ari-stream-quiz' )
                                );

                                $html .= sprintf(
                                    '<a href="#" class="red-text btn-quiz-delete" data-quiz-id="%1$d">%2$s</a>',
                                    $data->quiz_id,
                                    __( 'Delete', 'ari-stream-quiz' )
                                );

                                if ( $preview_item_url ) {
                                    if ( $can_edit )
                                        $html .= ' | ';

                                    $html .= sprintf(
                                        '<a href="%1$s" target="_blank">%2$s</a>',
                                        $preview_item_url,
                                        __( 'View', 'ari-stream-quiz' )
                                    );
                                }
                            }

                            if ( $can_edit )
                                $html .= ' | ';

                            $html .= sprintf(
                                '<a href="%1$s">%2$s</a>',
                                str_replace( '__quizId__', $data->quiz_id, $stat_url ),
                                __( 'Stat', 'ari-stream-quiz' )
                            );

                            $html .= '</div>';

                            $html .= sprintf(
                                '<button type="button" class="toggle-row"><span class="screen-reader-text">%1$s</span></button>',
                                __( 'Show more details', 'ari-stream-quiz' )
                            );

                            return $html;
                        },

                        'sortable' => true,
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
                        'key' => 'author',

                        'header_class' => 'manage-column',

                        'header' => __( 'Author', 'ari-stream-quiz' ),

                        'column' => function( $val, $data ) {
                            return $val ? $val : '&nbsp;';
                        },
                    ),

                    array(
                        'key' => 'created',

                        'header_class' => 'manage-column',

                        'title' => __( 'Created on', 'ari-stream-quiz' ),

                        'header' => function() {
                            return '<div class="column-wrapper">' . __( 'Created on', 'ari-stream-quiz' ) . '</div>';
                        },

                        'column' => function( $val, $data ) {
                            echo Date_Helper::db_gmt_to_local( $val );
                        },

                        'sortable' => true,
                    ),

                    array(
                        'key' => 'modified',

                        'title' => __( 'Last update', 'ari-stream-quiz' ),

                        'header_class' => 'manage-column',

                        'header' => function() {
                            return '<div class="column-wrapper">' . __( 'Last update', 'ari-stream-quiz' ) . '</div>';
                        },

                        'column' => function( $val, $data ) use ( $time_ago_format ) {
                            return ARISTREAMQUIZ_DB_EMPTYDATE != $val ? sprintf( $time_ago_format, human_time_diff( mysql2date( 'G', $val ), current_time( 'timestamp', 1 ) ) ) : '—';
                        },

                        'sortable' => true,
                    ),

                    array(
                        'key' => 'shortcode',

                        'header_class' => 'manage-column',

                        'header' => __( 'Shortcode', 'ari-stream-quiz' ),

                        'column' => function( $val, $data ) use ( $create_post_lbl ) {
                            $tbx_shortcode_id = 'asq_shortcode_' . $data->quiz_id;
                            $shortcode = '[streamquiz id="' . $data->quiz_id . '"]';

                            $html = sprintf(
                                '<input class="black-text" type="text" id="%1$s" size="30" readonly="readonly" value="%2$s" />',
                                $tbx_shortcode_id,
                                esc_attr( $shortcode )
                            );
                            $html .= sprintf(
                                '<a href="#" class="asq-shortcode-btn-copy" onclick="return false;" data-clipboard-target="#%1$s">%2$s</a><hr />',
                                $tbx_shortcode_id,
                                __( 'Copy to clipboard', 'ari-stream-quiz' )
                            );

                            $html .= sprintf(
                                $create_post_lbl,
                                admin_url( 'post-new.php?stream_quiz[id]=' . $data->quiz_id . '&stream_quiz[title]=' . rawurlencode( $data->quiz_title ) ),
                                admin_url( 'post-new.php?post_type=page&stream_quiz[id]=' . $data->quiz_id . '&stream_quiz[title]=' . rawurlencode( $data->quiz_title ) )
                            );

                            return $html;
                        },

                        'virtual' => true,
                    ),
                )
            )
        );

        return $grid;
    }
}
