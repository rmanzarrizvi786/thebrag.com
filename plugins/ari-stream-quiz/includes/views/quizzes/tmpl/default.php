<?php
use Ari_Stream_Quiz\Helpers\Helper as Helper;

$list = $data['list'];
$filter = $data['filter'];

$remove_url_params = array( 'filter', 'preview' );
$add_personality_quiz_url = Helper::build_url(
    array(
        'page' => 'ari-stream-quiz-quiz',
        'action' => 'add',
        'type' => ARISTREAMQUIZ_QUIZTYPE_PERSONALITY,
    ),
    $remove_url_params
);
$add_trivia_quiz_url = Helper::build_url(
    array(
        'page' => 'ari-stream-quiz-quiz',
        'action' => 'add',
        'type' => ARISTREAMQUIZ_QUIZTYPE_TRIVIA,
    ),
    $remove_url_params
);
$action_url = Helper::build_url(
    array(
        'noheader' => '1',
    ),
    $remove_url_params
);
$current_path = dirname( __FILE__ );
?>
<div>
    <a href="<?php echo $add_personality_quiz_url; ?>" class="btn waves-effect waves-light"><i class="right material-icons hide-on-small-only">add</i><?php _e( 'Add a personality quiz', 'ari-stream-quiz' ); ?></a>
    <a href="<?php echo $add_trivia_quiz_url; ?>" class="btn waves-effect waves-light"><i class="right material-icons hide-on-small-only">add</i><?php _e( 'Add a trivia quiz', 'ari-stream-quiz' ); ?></a>
</div>
<form action="<?php echo esc_url( $action_url ); ?>" method="POST">
    <div class="card-panel">
        <div class="row">
            <div class="col s12 m8 push-m4">
                <input type="text" autocomplete="off" id="tbxSearchText" name="quiz_search[search]" placeholder="<?php esc_attr_e( 'Search...', 'ari-stream-quiz' ); ?>" value="<?php echo esc_attr( $filter['search'] ); ?>" />
            </div>
            <div class="col s12 m4 pull-m8">
                <select id="ddlSearchQuizType" name="quiz_search[quiz_type]" class="listbox">
                    <option value=""<?php if ( empty( $filter['quiz_type']) ): ?> selected="selected"<?php endif; ?>><?php _e( 'All quiz types', 'ari-stream-quiz' ); ?></option>
                    <option value="<?php echo esc_attr( ARISTREAMQUIZ_QUIZTYPE_PERSONALITY ); ?>"<?php if ( ARISTREAMQUIZ_QUIZTYPE_PERSONALITY == $filter['quiz_type'] ): ?> selected="selected"<?php endif; ?>><?php _e( 'Personality', 'ari-stream-quiz' ); ?></option>
                    <option value="<?php echo esc_attr( ARISTREAMQUIZ_QUIZTYPE_TRIVIA ); ?>"<?php if ( ARISTREAMQUIZ_QUIZTYPE_TRIVIA == $filter['quiz_type'] ): ?> selected="selected"<?php endif; ?>><?php _e( 'Trivia', 'ari-stream-quiz' ); ?></option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col m4 left-align hide-on-small-only">
                <div class="grid-search-message"><?php printf( __( '%d items found.', 'ari-stream-quiz' ), $data['count'] ); ?></div>
            </div>
            <div class="col s12 m8 right-align">
                <a href="#" id="btnQuizSearch" class="btn btn-cmd blue waves-effect waves-light"><i class="right material-icons hide-on-small-only">search</i><?php echo _e( 'Search', 'ari-stream-quiz' ); ?></a>
                <a href="#" id="btnQuizSearchReset" class="btn btn-cmd red waves-effect waves-light"><i class="right material-icons hide-on-small-only">clear</i><?php echo _e( 'Reset', 'ari-stream-quiz' ); ?></a>
            </div>
        </div>
    </div>

    <div class="hide-on-small-only">
    <?php
        $this->show_template( $current_path . '/grid-toolbar.php', $data );
    ?>
    </div>

    <?php $this->grid->render( $list ); ?>

    <?php
        require $current_path . '/grid-toolbar.php';
    ?>

    <input type="hidden" id="ctrl_action" name="action" value="display" />
    <input type="hidden" id="hidQuizzesSortBy" name="quiz_sort[column]" value="" />
    <input type="hidden" id="hidQuizzesSortDir" name="quiz_sort[dir]" value="" />
    <input type="hidden" id="hidQuizzesPageNum" name="quiz_page" value="-1" />
    <input type="hidden" id="hidQuizId" name="action_quiz_id" value="" />
    <input type="hidden" id="hidPostId" name="action_post_id" value="" />
    <input type="hidden" name="filter" value="<?php echo esc_attr( $data['filter_encoded'] ); ?>" />
</form>