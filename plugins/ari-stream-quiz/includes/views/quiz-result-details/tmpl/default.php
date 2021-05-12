<?php
use Ari_Stream_Quiz\Helpers\Helper as Helper;
use Ari\Utils\Date as Date_Helper;
use Ari_Stream_Quiz\Helpers\Quiz_Activity as Quiz_Activity;

$date_time_format = get_option( 'date_format' ) . ' H:i:s';
$img_tmpl = dirname( __FILE__ ) . '/image.php';
$is_trivia = ( ARISTREAMQUIZ_QUIZTYPE_TRIVIA == $this->quiz->quiz_type );
?>
<div class="card-panel quiz-results-summary">
    <div class="row">
        <div class="col s12">
            <h4 class="teal-text"><?php _e( 'Summary', 'ari-stream-quiz' ); ?></h4>
        </div>
    </div>
    <div class="row">
        <div class="col s3 title">
            <?php _e( 'Summary', 'ari-stream-quiz' ); ?>
        </div>
        <div class="col s9">
            <?php echo $this->result->title; ?>
        </div>
    </div>
    <div class="row">
        <div class="col s3 title">
            <?php _e( 'Start date', 'ari-stream-quiz' ); ?>
        </div>
        <div class="col s9">
            <?php
                echo Date_Helper::db_gmt_to_local( $this->result->start_date, $date_time_format );
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col s3 title">
            <?php _e( 'End date', 'ari-stream-quiz' ); ?>
        </div>
        <div class="col s9">
            <?php
                echo Date_Helper::db_gmt_to_local( $this->result->end_date, $date_time_format );
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col s3 title">
            <?php _e( 'Elapsed time', 'ari-stream-quiz' ); ?>
        </div>
        <div class="col s9">
            <?php
                echo Date_Helper::format_duration( $this->result->elapsed_time, Helper::duration_rules() );
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col s3 title">
            <?php _e( 'Opt-in', 'ari-stream-quiz' ); ?>
        </div>
        <div class="col s9">
            <?php
                if ( $this->result->is_anonymous == '1' ):
            ?>
            -
            <?php
                else:
                    $name_exists = ! empty( $this->result->username );
                    if ( $name_exists )
                        echo $this->result->username;

                    if ( $this->result->email )
                        printf(
                            '%1$s<a href="mailto:%2$s">%2$s</a>%3$s',
                            $name_exists ? ' (' : '',
                            $this->result->email,
                            $name_exists ? ')' : ''
                        );
            ?>
            <?php
                endif;
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col s3 title">
            <?php _e( 'Share', 'ari-stream-quiz' ); ?>
        </div>
        <div class="col s9">
            <?php
            if ( $this->result->is_shared() ):
                if ( in_array( Quiz_Activity::SHARE_FACEBOOK, $this->result->activity ) || in_array( Quiz_Activity::FORCE_FACEBOOK, $this->result->activity ) ):
            ?>
            <div class="chip chip-facebook"><?php _e( 'Facebook', 'ari-stream-quiz' ); ?></div>
            <?php
                endif;
            ?>
            <?php
                if ( in_array( Quiz_Activity::SHARE_TWITTER, $this->result->activity ) ):
            ?>
                <div class="chip chip-twitter"><?php _e( 'Twitter', 'ari-stream-quiz' ); ?></div>
            <?php
                endif;
            ?>
            <?php
                if ( in_array( Quiz_Activity::SHARE_GOOGLEPLUS, $this->result->activity ) ):
            ?>
                <div class="chip chip-googleplus"><?php _e( 'Google+', 'ari-stream-quiz' ); ?></div>
            <?php
                endif;
            ?>
            <?php
                if ( in_array( Quiz_Activity::SHARE_PIN, $this->result->activity ) ):
            ?>
                <div class="chip chip-pin"><?php _e( 'Pinterest', 'ari-stream-quiz' ); ?></div>
            <?php
                endif;
            ?>
            <?php
                if ( in_array( Quiz_Activity::SHARE_LINKEDIN, $this->result->activity ) ):
            ?>
                <div class="chip chip-linkedin"><?php _e( 'LinkedIn', 'ari-stream-quiz' ); ?></div>
            <?php
                endif;
            ?>
            <?php
                if ( in_array( Quiz_Activity::SHARE_VK, $this->result->activity ) ):
            ?>
                <div class="chip chip-vk"><?php _e( 'VKontakt', 'ari-stream-quiz' ); ?></div>
            <?php
                endif;
            else:
            ?>
            -
            <?php
            endif;
            ?>
        </div>
    </div>
</div>
<div class="card-panel quiz-results">
    <div class="row">
        <div class="col s12">
            <h4 class="teal-text"><?php _e( 'Questions', 'ari-stream-quiz' ); ?></h4>
<?php
if ( $this->quiz_session ):
    foreach ( $this->quiz_session as $question ):
        $question_id = $question['question_id'];
        $has_answer_with_image = $question['has_answer_with_image'];
?>
<div class="card flex question<?php if ( $has_answer_with_image ): ?> has-answer-image<?php endif; ?>">
    <div class="question-title">
        <h5><?php echo $question['question_title']; ?></h5>
    </div>
    <?php
        if ( $question['has_image'] ):
            $image = $question['image'];
    ?>
    <div class="quiz-question-image">
        <?php $this->show_template( $img_tmpl, array( 'image' => $image ) ); ?>
    </div>
    <?php
        endif;
    ?>
    <div class="quiz-question-answers answer-col-2 clearfix">
    <?php
        foreach ( $question['answers'] as $answer ):
            $is_user_answer = isset( $answer['user_answer'] ) && $answer['user_answer'];
            $has_image = $answer['has_image'];
            $ctrl_id = 'chk_' . $question_id . '_' . $answer['answer_id'];
            $answer_css_class = null;

            if ( $is_trivia ) {
                if ( (bool)$answer['correct'] ) {
                    $answer_css_class = $is_user_answer ? 'user-answer-correct' : 'answer-correct';
                } else if ( $is_user_answer ) {
                    $answer_css_class = 'user-answer-incorrect';
                }
            } else if ( $is_user_answer ) {
                $answer_css_class = 'user-answer';
            }
    ?>
            <div class="quiz-question-answer-holder">
                <div class="quiz-question-answer card flex<?php if ( $answer_css_class ) echo ' ' . $answer_css_class; ?>">
                    <?php
                        if ( $has_answer_with_image ):
                    ?>
                    <div class="quiz-question-answer-image">
                        <?php
                            if ( $has_image ):
                                $image = $answer['image'];
                        ?>
                            <div class="quiz-question-answer-image-holder">
                                <?php $this->show_template( $img_tmpl, array( 'image' => $image ) ); ?>
                            </div>
                        <?php
                            endif;
                        ?>
                    </div>
                    <?php
                        endif;
                    ?>
                    <div class="answer-text">
                        <input type="checkbox" class="filled-in quiz-question-answer-ctrl" id="<?php echo $ctrl_id; ?>" disabled="disabled"<?php if ( $is_user_answer ): ?> checked="checked"<?php endif; ?> />
                        <label class="quiz-question-answer-ctrl-lbl" for="<?php echo $ctrl_id; ?>"><?php echo strlen( $answer['answer_title'] ) > 0 ? $answer['answer_title'] : '&nbsp;'; ?></label>
                    </div>
                </div>
            </div>
    <?php
        endforeach;
    ?>
    </div>
</div>
<?php
    endforeach;
else:
    _e( 'The detailed report is not available.', 'ari-stream-quiz' );
endif;
?>
        </div>
    </div>
</div>