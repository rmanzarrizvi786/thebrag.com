<?php
use Ari_Stream_Quiz\Helpers\Helper as Helper;

$current_user_id = get_current_user_id();
$list = $data['list'];
$filter = $data['filter'];
$order_by = $filter['order_by'];
$order_dir = $filter['order_dir'];

$remove_url_params = array( 'filter' );
$action_url = Helper::build_url(
    array(
        'noheader' => '1',
    ),
    $remove_url_params
);
$current_path = dirname( __FILE__ );
?>
<form action="<?php echo esc_url( $action_url ); ?>" method="POST">
    <div>
        <a href="#" id="btnResultsDeleteAll" class="btn btn-cmd red waves-effect waves-light"><i class="right material-icons hide-on-small-only">clear</i><?php _e( 'Delete all', 'ari-stream-quiz' ); ?></a>
        <a href="#" id="btnResultsExportCSV" class="btn btn-cmd waves-effect waves-light"><i class="right material-icons hide-on-small-only">save</i><?php _e( 'Export to CSV', 'ari-stream-quiz' ); ?></a>
    </div>
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
            <div class="col s12">
                <input type="checkbox" class="filled-in" id="chkSearchAnonymous" name="quiz_search[anonymous]" value="1" autocomplete="off"<?php if ( $filter['anonymous'] ): ?> checked="checked"<?php endif; ?> />
                <label id="lblSearchAnonymous" for="chkSearchAnonymous" class="label"><?php _e( 'Show anonymous', 'ari-stream-quiz' ); ?></label>
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
    <input type="hidden" id="hidResultSortBy" name="results_sort[column]" value="" />
    <input type="hidden" id="hidResultSortDir" name="results_sort[dir]" value="" />
    <input type="hidden" id="hidResultPageNum" name="results_page" value="-1" />
    <input type="hidden" id="hidResultId" name="action_result_id" value="" />
    <input type="hidden" name="filter" value="<?php echo esc_attr( $data['filter_encoded'] ); ?>" />
</form>