<?php
$default_score_list = array( -100, -5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5, 100 );
$score_list = apply_filters( 'asq_personality_score', $default_score_list );
?>
<ul class="tabs" id="quiz_settings_tabs">
    <li class="tab col s3"><a class="teal-text active" href="#quiz_personalities_tab" data-container-class="tab-personalities"><?php _e( 'Personalities', 'ari-stream-quiz' ); ?></a></li>
    <li class="tab col s3"><a class="teal-text" href="#quiz_questions_tab" data-container-class="tab-questions"><?php _e( 'Questions', 'ari-stream-quiz' ); ?></a></li>
    <li class="tab col s3"><a class="teal-text" href="#quiz_settings_tab" data-container-class="tab-settings"><?php _e( 'Settings', 'ari-stream-quiz' ); ?></a></li>
    <div class="indicator teal indicator-fix"></div>
</ul>
<div id="quiz_personalities_tab" class="section">
    <div class="card-panel">
        <div class="row">
            <input type="checkbox" class="filled-in block-switcher" data-ref-id="personalitySettingsContainer" id="chkShowSeveralPersonalities" name="entity[quiz_meta][personality][show_several][enabled]" value="1"<?php if ( $entity->quiz_meta->personality->show_several->enabled ): ?> checked="checked"<?php endif; ?> />
            <label for="chkShowSeveralPersonalities" class="label"><?php _e( 'Show several personalities on final page', 'ari-stream-quiz' ); ?></label>
        </div>
        <div class="row sub-section" id="personalitySettingsContainer">
            <div class="row">
                <div class="col s3 input-field">
                    <input type="number" id="tbxPersonalityShowSeveralCount" name="entity[quiz_meta][personality][show_several][count]" value="<?php echo esc_attr( $entity->quiz_meta->personality->show_several->count ); ?>" />
                    <label for="tbxPersonalityShowSeveralCount"><?php _e( 'Count', 'ari-stream-quiz' ); ?></label>
                </div>
            </div>
			
            <div class="row">
                <div class="col s12">
					<input type="checkbox" class="filled-in" id="chkPersonalityShowOnlyMainContent" name="entity[quiz_meta][personality][show_several][only_main_content]" value="1"<?php if ( $entity->quiz_meta->personality->show_several->only_main_content ): ?> checked="checked"<?php endif; ?> />
                    <label for="chkPersonalityShowOnlyMainContent" class="label"><?php _e( 'Show content only for main personality', 'ari-stream-quiz' ); ?></label>
                </div>
            </div>
        </div>
    </div>

    <div id="quiz_personality_templates" class="ari-cloner-container" data-cloner-control-key="personalities" data-cloner-id="personalities" data-cloner-opt-items="1">
        <div class="center-align">
            <a href="#" class="btn ari-cloner-add-item waves-effect waves-light"><i class="right material-icons">add</i><?php _e( 'Add personality', 'ari-stream-quiz' ); ?></a>
            <a href="#" id="btnDeleteTemplates" class="btn ari-cloner-removeall-item red waves-effect waves-light"><i class="right material-icons">delete</i><?php _e( 'Delete all personalities', 'ari-stream-quiz' ); ?></a>
        </div>

        <ul class="collapsible-container" data-collapsible="expandable">
            <li class="ari-cloner-template hoverable">
                <div class="collapsible-header sort-handle clearfix">
                    <div class="right actions-panel">
                        <a href="#" class="btn-floating ari-cloner-remove-item red" title=""><i class="material-icons red">delete</i></a>
                    </div>
                    <div class="truncate personality-title"><?php _e( 'Untitled', 'ari-stream-quiz' ); ?></div>
                </div>
                <div class="collapsible-body">
                    <div class="card flex">
                        <div class="card-content">
                            <div>
                                <input type="text" placeholder="<?php esc_attr_e( 'Enter title here', 'ari-stream-quiz' ); ?>" data-cloner-type="control" data-cloner-control-key="personality_title" />
                            </div>
                            <div class="ari-wp-image-container">
                                <div class="ari-wp-image-holder" data-lazy-load>
                                </div>
                                <div>
                                    <button class="btn waves-effect waves-light ari-media-library" data-wpmedia-title="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>" data-wpmedia-button="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>"><i class="right material-icons">image</i><?php _e( 'Select image', 'ari-stream-quiz' ); ?></button>
                                    <button class="btn waves-effect waves-light ari-media-library-remove red"><?php _e( 'Remove image', 'ari-stream-quiz' ); ?></button>
                                </div>
                                <input type="hidden" class="ari-wp-image-id" data-ref-column="image" data-cloner-type="control" data-cloner-data-type="image" data-cloner-control-key="image_id" />
                            </div>
                            <div>
                                <div id="tbxPersonalityDescription" data-cloner-type="control" data-cloner-control-key="personality_content" data-cloner-data-type="html"></div>
                            </div>
                        </div>
                        <div class="card-action right-align">
                            <a href="#" class="btn-floating ari-cloner-movedown-item light-blue" title=""><i class="material-icons">arrow_drop_down</i></a>
                            <a href="#" class="btn-floating ari-cloner-moveup-item light-blue" title=""><i class="material-icons">arrow_drop_up</i></a>
                            <a href="#" class="btn-floating ari-cloner-remove-item red" title=""><i class="material-icons">delete</i></a>
                        </div>
                    </div>
                    <input type="hidden" data-cloner-type="control" data-cloner-control-key="personality_id" />
                    <input type="hidden" data-cloner-type="control" data-cloner-control-key="personality_guid" />
                </div>
            </li>
        </ul>

        <div class="ari-cloner-empty card-panel" style="display:none;">
            <?php _e( 'One of the created personalities will be shown at the end of the quiz based on the selected answers. Use "ADD PERSONALITY +" button to add personalities.', 'ari-stream-quiz' ); ?>
        </div>
    </div>
</div>
<div id="quiz_questions_tab" class="section" style="display:none;">
    <div class="card-panel">
        <div class="row">
            <input type="checkbox" class="filled-in" id="chkMultipleAnswersSelection" name="entity[quiz_meta][personality][multiple_answers_selection]" value="1"<?php if ( $entity->quiz_meta->personality->multiple_answers_selection ): ?> checked="checked"<?php endif; ?> />
            <label for="chkMultipleAnswersSelection" class="label"><?php _e( 'Multiple answers selection for all questions', 'ari-stream-quiz' ); ?></label>
        </div>
    </div>

    <div id="quiz_questions" class="ari-cloner-container<?php if ( ! $entity->quiz_meta->personality->multiple_answers_selection ): ?> personality-custom-multiple-answers<?php endif; ?>" data-cloner-control-key="questions" data-cloner-id="questions" data-cloner-opt-items="1">
        <div class="center-align">
            <a href="#" class="btn ari-cloner-add-item waves-effect waves-light"><i class="right material-icons">add</i><?php _e( 'Add question', 'ari-stream-quiz' ); ?></a>
            <a href="#" id="btnDeleteQuestions" class="btn ari-cloner-removeall-item red waves-effect waves-light"><i class="right material-icons">delete</i><?php _e( 'Delete all questions', 'ari-stream-quiz' ); ?></a>
        </div>

        <ul class="collapsible-container" data-collapsible="expandable">
            <li class="ari-cloner-template hoverable">
                <div class="collapsible-header sort-handle clearfix">
                    <div class="right actions-panel">
                        <a href="#" class="btn-floating ari-cloner-remove-item red" title=""><i class="material-icons red">delete</i></a>
                    </div>
                    <div class="truncate"><?php _e( 'Question', 'ari-stream-quiz' ); ?> #<span class="question-index"></span>: <span class="question-text"><?php _e( 'Untitled', 'ari-stream-quiz' ); ?></span></div>
                </div>
                <div class="collapsible-body">
                    <div class="card flex">
                        <div class="card-content">
                            <div>
                                <input type="text" placeholder="<?php esc_attr_e( 'Enter question', 'ari-stream-quiz' ); ?>" data-cloner-type="control" data-cloner-control-key="question_title" />
                            </div>
                            <?php do_action( 'asq_ui_question_options_top', $entity ); ?>
                            <div class="ari-wp-image-container">
                                <div class="ari-wp-image-holder" data-lazy-load>
                                </div>
                                <div>
                                    <button class="btn waves-effect waves-light ari-media-library" data-wpmedia-title="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>" data-wpmedia-button="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>"><i class="right material-icons">image</i><?php _e( 'Select image', 'ari-stream-quiz' ); ?></button>
                                    <button class="btn waves-effect waves-light ari-media-library-remove red"><?php _e( 'Remove image', 'ari-stream-quiz' ); ?></button>
                                </div>
                                <input type="hidden" class="ari-wp-image-id" data-ref-column="image" data-cloner-data-type="image" data-cloner-type="control" data-cloner-control-key="image_id" />
                            </div>
                            <div class="ari-cloner-container" data-cloner-id="answers" data-cloner-control-key="answers" data-cloner-opt-items="2">
                                <div class="right-align">
                                    <a href="#" class="btn blue ari-cloner-add-item waves-effect waves-light"><i class="right material-icons">add</i><?php _e( 'Add answer', 'ari-stream-quiz' ); ?></a>
                                </div>
                                <div class="ari-cloner-template card flex">
                                    <div>
                                        <input type="text" placeholder="<?php esc_attr_e( 'Enter answer', 'ari-stream-quiz' ); ?>" data-cloner-type="control" data-cloner-control-key="answer_title" />
                                    </div>
                                    <div class="ari-wp-image-container">
                                        <div class="ari-wp-image-holder" data-lazy-load>
                                        </div>
                                        <div>
                                            <button class="btn waves-effect waves-light ari-media-library" data-wpmedia-title="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>" data-wpmedia-button="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>"><i class="right material-icons">image</i><?php _e( 'Select image', 'ari-stream-quiz' ); ?></button>
                                            <button class="btn waves-effect waves-light ari-media-library-remove red"><?php _e( 'Remove image', 'ari-stream-quiz' ); ?></button>
                                        </div>
                                        <input type="hidden" class="ari-wp-image-id" data-ref-column="image" data-cloner-data-type="image" data-cloner-type="control" data-cloner-control-key="image_id" />
                                    </div>
                                    <div class="ari-cloner-container ari-answer-personalities-cloner clearfix" data-cloner-id="answer_personalities" data-cloner-control-key="answer_personalities" data-cloner-opt-items="0">
                                        <div class="ari-cloner-template card flex left">
                                            <div class="answer-personality-title truncate" data-cloner-data-type="html" data-cloner-type="control" data-cloner-control-key="title"></div>
                                            <div>
                                                <br />
                                                <?php _e( 'Earn', 'ari-stream-quiz' ); ?>
                                                <select class="browser-default inline-block" data-cloner-type="control" data-cloner-control-key="score">
                                                <?php
                                                    foreach( $score_list as $score ):
                                                ?>
                                                    <option value="<?php echo $score; ?>"<?php if ( 0 === $score ): ?> selected="selected"<?php endif; ?>><?php echo $score; ?></option>
                                                <?php
                                                    endforeach;
                                                ?>
                                                </select>
                                                <?php _e( 'points', 'ari-stream-quiz' ); ?>
                                            </div>
                                            <input type="hidden" data-cloner-type="control" data-cloner-control-key="personality_guid" />
                                            <input type="hidden" data-cloner-type="control" data-cloner-control-key="answer_personality_id" value="0" />
                                        </div>
                                    </div>
                                    <div class="card-action right-align">
                                        <a href="#" class="btn-floating ari-cloner-movedown-item light-blue" title=""><i class="material-icons">arrow_drop_down</i></a>
                                        <a href="#" class="btn-floating ari-cloner-moveup-item light-blue" title=""><i class="material-icons">arrow_drop_up</i></a>
                                        <a href="#" class="btn-floating ari-cloner-remove-item red" title=""><i class="material-icons">delete</i></a>
                                    </div>
                                    <input type="hidden" data-cloner-type="control" data-cloner-control-key="answer_id" />
                                </div>
                                <div class="ari-cloner-empty card flex red-text" style="display:none;">
                                    <?php _e( 'Add at least one answer to the question otherwise this question will be ignored and not saved.', 'ari-stream-quiz' ); ?>
                                </div>
                                <div class="right-align">
                                    <a href="#" class="btn blue ari-cloner-add-item waves-effect waves-light"><i class="right material-icons">add</i><?php _e( 'Add answer', 'ari-stream-quiz' ); ?></a>
                                </div>
                            </div>
                            <div class="personality-multiple-answers">
                                <input type="checkbox" class="filled-in" id="cbMultipleAnswers" data-cloner-type="control" data-cloner-control-key="multiple" />
                                <label for="cbMultipleAnswers" class="label"><?php _e( 'Multiple answers selection', 'ari-stream-quiz' ); ?></label>
                            </div>
                            <?php do_action( 'asq_ui_question_options_bottom', $entity ); ?>
                        </div>
                        <div class="card-action right-align">
                            <a href="#" class="btn-floating ari-cloner-movedown-item light-blue" title=""><i class="material-icons">arrow_drop_down</i></a>
                            <a href="#" class="btn-floating ari-cloner-moveup-item light-blue" title=""><i class="material-icons">arrow_drop_up</i></a>
                            <a href="#" class="btn-floating ari-cloner-remove-item red" title=""><i class="material-icons">delete</i></a>
                        </div>
                    </div>
                    <input type="hidden" data-cloner-type="control" data-cloner-control-key="question_id" />
                </div>
            </li>
        </ul>

        <div class="ari-cloner-empty card-panel" style="display:none;">
            <?php _e( 'Use "ADD QUESTION +" button to populate the quiz with questions. The quiz should contain at least one question.', 'ari-stream-quiz' ); ?>
        </div>
    </div>
</div>
<div id="quiz_settings_tab" class="section" style="display:none;">
    <?php require dirname( __FILE__ ) . '/quiz-settings.php'; ?>
</div>

<input type="hidden" name="entity[personalities]" id="hidQuizPersonalityTemplates" value="" />
<input type="hidden" name="entity[questions]" id="hidQuestions" value="" />
