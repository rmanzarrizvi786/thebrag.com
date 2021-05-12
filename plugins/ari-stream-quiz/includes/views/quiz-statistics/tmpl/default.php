<?php
use Ari_Stream_Quiz\Helpers\Helper as Helper;
use Ari\Utils\Date as Date_Helper;

$remove_url_params = array();
$can_edit = Helper::can_edit_quiz( $this->quiz->quiz_id );
$action_url = Helper::build_url(
    array(
        'noheader' => '1',
    ),
    $remove_url_params
);
$has_results = ( ARISTREAMQUIZ_QUIZTYPE_PERSONALITY == $this->quiz->quiz_type );
?>
<form action="<?php echo esc_url( $action_url ); ?>" method="POST">
    <ul id="quiz_stat_tabs">
        <li class="tab col s3"><a class="teal-text active" href="#quiz_stat_report_tab"><?php _e( 'Report', 'ari-stream-quiz' ); ?></a></li>
        <?php
            if ( $has_results ):
        ?>
        <li class="tab col s3"><a class="teal-text" href="#quiz_stat_results_tab"><?php _e( 'Results', 'ari-stream-quiz' ); ?></a></li>
        <?php
            endif;
        ?>
        <li class="tab col s3"><a class="teal-text" href="#quiz_stat_custom_tab"><?php _e( 'Custom Report', 'ari-stream-quiz' ); ?></a></li>
        <div class="indicator teal indicator-fix"></div>
    </ul>
    <div id="quiz_stat_report_tab" class="section" data-tab-id="report">
        <?php
            if ( $can_edit ):
        ?>
        <div>
            <a href="#" id="btnStatReset" class="btn btn-cmd red waves-effect waves-light"><i class="right material-icons hide-on-small-only">clear</i><?php _e( 'Reset', 'ari-stream-quiz' ); ?></a>
        </div>
        <?php
            endif;
        ?>

        <?php
            if ( ARISTREAMQUIZ_DB_EMPTYDATE != $this->quiz_stat->start_date ):
        ?>
        <h6 class="header"><?php _e( 'Show statistics since', 'ari-stream-quiz' ); ?>: <span class="green-text"><?php echo Date_Helper::db_gmt_to_local( $this->quiz_stat->start_date ); ?></span></h6>
        <?php
            endif;
        ?>

        <div class="quiz-bar-chart ct-chart ct-octave" id="chartQuizStat"></div>
    </div>

    <?php
        if ( $has_results ):
    ?>
    <div id="quiz_stat_results_tab" class="section" data-tab-id="results">
        <div class="quiz-bar-chart ct-chart ct-octave" id="chartQuizResults"></div>
    </div>
    <?php
        endif;
    ?>

    <div id="quiz_stat_custom_tab" class="section" data-tab-id="custom">
        <div class="card-panel">
            <div class="row">
                <div class="col s12 m6">
                    <label for="dateCustomStartDate"><?php _e( 'Start date', 'ari-stream-quiz' ); ?></label>
                    <input id="dateCustomStartDate" type="text" class="datepicker" autocomplete="off" />
                </div>
                <div class="col s12 m6">
                    <label for="dateCustomEndDate"><?php _e( 'End date', 'ari-stream-quiz' ); ?></label>
                    <input id="dateCustomEndDate" type="text" class="datepicker" autocomplete="off" />
                </div>
            </div>
            <div class="row">
                <div class="col s12 right-align">
                    <a href="#" id="btnShowCustomReport" class="btn btn-cmd blue waves-effect waves-light"><i class="right material-icons hide-on-small-only">search</i><?php echo _e( 'Show report', 'ari-stream-quiz' ); ?></a>
                </div>
            </div>
            <div class="row">
                <div class="col s12 left-align grid-search-message">
                    <?php _e( 'Select "Start date" and/or "End date" and click "SHOW REPORT" button to report for the selected period.', 'ari-stream-quiz' ); ?>
                </div>
            </div>
        </div>

        <div class="quiz-bar-chart ct-chart ct-octave" id="chartQuizStatCustom"></div>
    </div>

    <input type="hidden" name="id" value="<?php echo $this->quiz->quiz_id; ?>" />
    <input type="hidden" id="ctrl_action" name="action" value="display" />
</form>