<?php
$session = $data['session'];
$prefix = $data['prefix'];
$lazy_load = $data['lazy_load'];
$column_count = $data['column_count'];
$img_tmpl = dirname( __FILE__ ) . '/image.php';
$show_continue_button = $data['show_continue_button'];

$page_num = 0;
foreach ( $session->pages as $page ):
?>
<div class="quiz-page<?php if ( $page_num == 0 ): ?> current<?php endif; ?>" id="<?php echo $prefix . '_page_' . $page_num; ?>" data-page="<?php echo $page_num; ?>">
    <?php
    foreach ( $page->questions as $question ):
        $has_answer_with_image = $question->has_answer_with_image;
        ?>
        <div class="quiz-question<?php if ( $question->multiple ): ?> quiz-question-multiple<?php endif; ?><?php if ( $has_answer_with_image ): ?> quiz-question-has-image-answer<?php endif; ?>" id="<?php echo $prefix . '_question_' . $question->question_id; ?>" data-question-id="<?php echo $question->question_id; ?>">
            <div class="quiz-question-title" data-question-index="<?php echo $question->question_number; ?>">
                <?php echo $question->question_title; ?>
            </div>
            <?php
            if ( $question->has_image ):
                $image = $question->image;
                ?>
                <div class="quiz-question-image">
                    <div class="quiz-question-image-holder">
                        <?php $this->show_template( $img_tmpl, array( 'image' => $image, 'lazy_load' => $lazy_load ) ); ?>
                    </div>
                </div>
            <?php
            endif;
            ?>
            <div class="quiz-question-answers answer-col-<?php echo $column_count; ?> clearfix" id="<?php echo $prefix . '_answers_' . $question->question_id; ?>">
                <?php
                foreach ( $question->answers as $answer ):
                    $ctrl_id = $prefix . '_answer_' . $answer->answer_id;
                    $ctrl_name = $prefix . '_answer_' . $question->question_id;
                    $has_image = $answer->has_image;
                    ?>
                    <div class="quiz-question-answer-holder">
                        <div class="quiz-question-answer" id="<?php echo $prefix . '_answercontainer_' . $answer->answer_id; ?>">
                            <?php
                            if ( $has_answer_with_image ):
                                ?>
                                <div class="quiz-question-answer-image">
                                    <?php
                                    if ( $has_image ):
                                        $image = $answer->image;
                                    ?>
                                    <div class="quiz-question-answer-image-wrapper">
                                        <?php $this->show_template( $img_tmpl, array( 'image' => $image, 'lazy_load' => $lazy_load ) ); ?>
                                    </div>
                                    <?php
                                        endif;
                                    ?>
                                </div>
                            <?php
                            endif;
                            ?>
                            <div class="quiz-question-answer-controls">
                                <input type="<?php if ( $question->multiple ) : ?>checkbox<?php else: ?>radio<?php endif; ?>" class="ari-checkbox quiz-question-answer-ctrl" name="<?php echo $ctrl_name; ?>" id="<?php echo $ctrl_id; ?>" value="<?php echo $answer->answer_id; ?>" data-question-id="<?php echo $question->question_id; ?>" />
                                <label class="ari-checkbox-label quiz-question-answer-ctrl-lbl" for="<?php echo $ctrl_id; ?>"><?php echo strlen( $answer->answer_title ) > 0 ? $answer->answer_title : '&nbsp;'; ?></label>
                            </div>
                        </div>
                    </div>
                <?php
                endforeach;
                ?>
            </div>

            <div id="<?php echo $prefix . '_question_status_' . $question->question_id; ?>" class="quiz-question-status quiz-section" style="display:none;">
                <div class="quiz-question-result"></div>
                <div class="quiz-question-explanation"></div>
            </div>
        </div>
    <?php
    endforeach;
    ?>
    <div class="quiz-actions">
        <button class="button button-green full-width button-complete-page"><?php _e( 'Continue', 'ari-stream-quiz' ); ?></button>
    <?php
        if ($show_continue_button):
    ?>
            <button class="button button-green full-width button-next-page"><?php _e( 'Continue', 'ari-stream-quiz' ); ?></button>
    <?php
        endif;
    ?>
    </div>
</div>
<?php
    ++$page_num;
endforeach;
?>