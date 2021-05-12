<?php
$show_continue_button = $data['show_continue_button'];
$prefix = $data['prefix'];
$template_wrap_with_tag = $data['template_wrap_with_tag'];
?>
<?php
    if ( $template_wrap_with_tag ):
?>
<ari-template id="<?php echo $prefix; ?>_questions_tpl">
<?php
    else:
?>
<script type="text/x-ari-template" id="<?php echo $prefix; ?>_questions_tpl">
<?php
    endif;
?>
{{$pages}}
<div class="quiz-page" id="{{{prefix}}}_page_{{@index}}" data-page="{{@index}}">
    {{$questions}}
    <div class="quiz-question {{#if multiple}}quiz-question-multiple{{/if}} {{#if has_answer_with_image}}quiz-question-has-image-answer{{/if}}" id="{{{prefix}}}_question_{{question_id}}" data-question-id="{{question_id}}">
        <div class="quiz-question-title" data-question-index="{{question_number}}">
            {{question_title}}
        </div>
        {{#if has_image}}
        <div class="quiz-question-image">
            <div class="quiz-question-image-holder">
                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="{{image:url}}" class="lazy-load" alt="" />
                {{#if image:description}}
                <div class="quiz-image-credit">{{image:description}}</div>
                {{/if}}
            </div>
        </div>
        {{/if}}
        <div class="quiz-question-answers answer-col-{{{column_count}}} clearfix" id="{{{prefix}}}_answers_{{question_id}}">
            {{$answers}}
            <div class="quiz-question-answer-holder">
                <div class="quiz-question-answer" id="{{{prefix}}}_answercontainer_{{answer_id}}">
                    {{#if has_answer_with_image}}
                    <div class="quiz-question-answer-image">
                        {{#if has_image}}
                        <div class="quiz-question-answer-image-wrapper">
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="{{image:url}}?t=<?php echo time(); ?>" class="lazy-load" alt="" />
                            {{#if image:description}}
                            <div class="quiz-image-credit">{{image:description}}</div>
                            {{/if}}
                        </div>
                        {{/if}}
                    </div>
                    {{/if}}
                    <div class="quiz-question-answer-controls">
                        <input type="{{#if @iterator:questions:multiple}}checkbox{{/if}}{{#if !@iterator:questions:multiple}}radio{{/if}}" class="ari-checkbox quiz-question-answer-ctrl" name="{{{prefix}}}_answer_{{question_id}}" id="{{{prefix}}}_answer_{{answer_id}}" value="{{answer_id}}" data-question-id="{{question_id}}" />
                        <label class="ari-checkbox-label quiz-question-answer-ctrl-lbl" for="{{{prefix}}}_answer_{{answer_id}}">{{answer_title}}&nbsp;</label>
                    </div>
                </div>
            </div>
            {{/answers}}
        </div>

        <div id="{{{prefix}}}_question_status_{{question_id}}" class="quiz-question-status quiz-section" style="display:none;">
            <div class="quiz-question-result"></div>
            <div class="quiz-question-explanation"></div>
        </div>
    </div>
    {{/questions}}
    <div class="quiz-actions">
        <button class="button button-green full-width button-complete-page"><?php _e( 'Continue', 'ari-stream-quiz' ); ?></button>
    <?php
        if ( $show_continue_button ):
    ?>
        <button class="button button-green full-width button-next-page"><?php _e( 'Continue', 'ari-stream-quiz' ); ?></button>
    <?php
        endif;
    ?>
    </div>
</div>
{{/pages}}
<?php
    if ( $template_wrap_with_tag ):
?>
    </ari-template>
<?php
    else:
?>
</script>
<?php
    endif;
?>
