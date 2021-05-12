<?php
use Ari_Stream_Quiz\Helpers\Settings as Settings;

$constantcontact_apikey = Settings::get_option( 'constantcontact_apikey' );
$constantcontact_access_token = Settings::get_option( 'constantcontact_access_token' );
$mailchimp_apikey = Settings::get_option( 'mailchimp_apikey' );
$mailerlite_apikey = Settings::get_option( 'mailerlite_apikey' );
$aweber_auth_code = Settings::get_option( 'aweber_auth_code' );
$getresponse_apikey = Settings::get_option( 'getresponse_apikey' );
$drip_apikey = Settings::get_option( 'drip_apikey' );
$drip_account_id = Settings::get_option( 'drip_account_id' );
$activecampaign_apikey = Settings::get_option( 'activecampaign_apikey' );
$activecampaign_url = Settings::get_option( 'activecampaign_url' );
$question_count_input = '<input type="number" id="tbxQuestionCount" name="entity[random_question_count]" class="input-small center-align" placeholder="X" min="0" size="6" value="' . esc_attr( $entity->random_question_count ) . '" />';
$questions_per_page_input = '<input type="number" id="tbxQuestionsPerPage" name="entity[questions_per_page]" class="input-small center-align" placeholder="X" min="1" size="6" value="' . esc_attr( $entity->questions_per_page ) . '" />';
?>
<div class="card-panel">
    <?php do_action( 'asq_ui_quiz_settings_top', $entity ); ?>
    <div class="row">
        <div class="input-field">
            <select class="listbox" id="ddlQuizTheme" name="entity[theme]">
                <option value=""<?php if ( ! $entity->theme ): ?> selected="selected"<?php endif; ?>><?php _e( '- Default -', 'ari-stream-quiz' ); ?></option>
                <?php
                    foreach ( $this->themes as $theme ):
                ?>
                    <option value="<?php echo $theme; ?>"<?php if ( $entity->theme == $theme ): ?> selected="selected"<?php endif; ?>><?php echo $theme; ?></option>
                <?php
                    endforeach;
                ?>
            </select>
            <label class="label"><?php _e( 'Theme', 'ari-stream-quiz' ); ?></label>
        </div>
    </div>
    <div class="row">
        <div class="ari-wp-image-container quiz-image-container">
            <div class="ari-wp-image-holder">
            </div>
            <div class="row">
                <button class="btn waves-effect waves-light ari-media-library" data-wpmedia-title="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>" data-wpmedia-button="<?php esc_attr_e( 'Select image', 'ari-stream-quiz' ); ?>"><i class="right material-icons">image</i><?php _e( 'Select image', 'ari-stream-quiz' ); ?></button>
                <button class="btn waves-effect waves-light ari-media-library-remove red"><?php _e( 'Remove image', 'ari-stream-quiz' ); ?></button>
            </div>
            <div class="show-quiz-image">
                <input type="checkbox" class="filled-in" id="chkShowImageInDescription" name="entity[quiz_meta][show_quiz_image]" value="1"<?php if ( $entity->quiz_meta->show_quiz_image ): ?> checked="checked"<?php endif; ?> />
                <label for="chkShowImageInDescription" class="label"><?php _e( 'Show image in description', 'ari-stream-quiz' ); ?></label>
            </div>
            <input type="hidden" id="hidQuiImageId" name="entity[quiz_image_id]" class="ari-wp-image-id" value="<?php echo $entity->quiz_image_id; ?>" />
        </div>
    </div>
    <div class="row">
        <div>
            <label class="label" for="tbxQuizDescription"><?php _e( 'Description', 'ari-stream-quiz' ); ?></label>
        </div>
        <div>
            <textarea name="entity[quiz_description]" id="tbxQuizDescription" placeholder="<?php esc_attr_e( 'Enter quiz description here', 'ari-stream-quiz' ); ?>"><?php echo esc_attr( $entity->quiz_description ); ?></textarea>
        </div>
    </div>
    <div class="row">
        <input type="checkbox" class="filled-in" id="chkShuffleAnswers" name="entity[shuffle_answers]" value="1"<?php if ( $entity->shuffle_answers ): ?> checked="checked"<?php endif; ?> />
        <label for="chkShuffleAnswers" class="label"><?php _e( 'Shuffle answers', 'ari-stream-quiz' ); ?></label>
    </div>
    <div class="child-controls-inline">
        <input type="checkbox" class="filled-in" id="chkRandomQuestions" name="entity[random_questions]" value="1"<?php if ( $entity->random_questions ): ?> checked="checked"<?php endif; ?> />
        <label for="chkRandomQuestions" class="label"><?php _e( 'Random questions', 'ari-stream-quiz' ); ?></label>
        <label class="label" data-ref-id="chkRandomQuestions">
            <?php printf( __( 'and select %s questions', 'ari-stream-quiz' ), $question_count_input ); ?>
        </label>
    </div>
    <div class="row">
        <input type="checkbox" class="filled-in" id="chkStartImmediately" name="entity[start_immediately]" value="1"<?php if ( $entity->start_immediately ): ?> checked="checked"<?php endif; ?> />
        <label class="label" for="chkStartImmediately"><?php _e( 'Start quiz immediately', 'ari-stream-quiz' ); ?></label>
    </div>
    <div class="row">
        <input type="checkbox" class="filled-in" id="chkShortcode" name="entity[quiz_meta][shortcode]" value="1"<?php if ( $entity->quiz_meta->shortcode ): ?> checked="checked"<?php endif; ?> />
        <label class="label" for="chkShortcode"><?php _e( 'Support shortcodes in quiz description, questions, answers and results', 'ari-stream-quiz' ); ?></label>
        <sup class="teal-text"><?php _e( 'beta', 'ari-stream-quiz' ); ?></sup>
    </div>
    <div class="row">
        <input type="checkbox" class="filled-in" id="chkTryAgainButton" name="entity[quiz_meta][try_again_button]" value="1"<?php if ( $entity->quiz_meta->try_again_button ): ?> checked="checked"<?php endif; ?> />
        <label class="label" for="chkTryAgainButton"><?php _e( 'Show "Play again" button', 'ari-stream-quiz' ); ?></label>
    </div>
    <div>
        <div class="row">
            <input data-ref-id="pagingContainer" type="checkbox" class="filled-in block-switcher" id="chkUsePaging" name="entity[use_paging]" value="1"<?php if ( $entity->use_paging ): ?> checked="checked"<?php endif; ?> />
            <label class="label" for="chkUsePaging"><?php _e( 'Multi pages', 'ari-stream-quiz' ); ?></label>
        </div>
        <div class="row sub-section" id="pagingContainer">
            <div class="row">
                <label class="label">
                    <?php printf( __( 'Show %s question(s) per page', 'ari-stream-quiz' ), $questions_per_page_input ); ?>
                </label>
            </div>
            <?php
                if ( ARISTREAMQUIZ_QUIZTYPE_TRIVIA == $entity->quiz_type ):
            ?>
            <div class="row">
                <input type="checkbox" class="filled-in" id="chkPagingNavButton" name="entity[quiz_meta][paging_nav_button]" value="1"<?php if ( $entity->quiz_meta->paging_nav_button ): ?> checked="checked"<?php endif; ?> />
                <label for="chkPagingNavButton" class="label"><?php _e( 'Use "Continue" button to navigate to the next page', 'ari-stream-quiz' ); ?></label>
            </div>
            <?php
                endif;
            ?>
        </div>
    </div>
    <div class="divider"></div>
    <br /><br />
    <div class="row">
        <input type="checkbox" class="filled-in" id="chkShareToSee" name="entity[quiz_meta][share_to_see]" value="1"<?php if ( $entity->quiz_meta->share_to_see ): ?> checked="checked"<?php endif; ?> />
        <label class="label" for="chkShareToSee"><?php _e( 'The quiz should be shared on Facebook to see results', 'ari-stream-quiz' ); ?></label>
    </div>
    <div class="row">
        <input type="checkbox" class="filled-in" id="chkShareButtons" name="entity[quiz_meta][show_share_buttons]" value="1"<?php if ( $entity->quiz_meta->show_share_buttons ): ?> checked="checked"<?php endif; ?> />
        <label class="label" for="chkShareButtons"><?php _e( 'Show share buttons', 'ari-stream-quiz' ); ?></label>
    </div>
    <div>
        <div class="row">
            <input type="checkbox" class="filled-in block-switcher" data-ref-id="collectDataContainer" id="chkCollectUserData" name="entity[collect_data]" value="1"<?php if ( $entity->collect_data ): ?> checked="checked"<?php endif; ?> />
            <label for="chkCollectUserData" class="label"><?php _e( 'Collect users\' data', 'ari-stream-quiz' ); ?></label>
        </div>
        <div class="row sub-section" id="collectDataContainer">
            <div class="row">
                <input type="checkbox" class="filled-in" id="chkCollectName" name="entity[collect_name]" value="1"<?php if ( $entity->collect_name ): ?> checked="checked"<?php endif; ?> />
                <label for="chkCollectName" class="label"><?php _e( 'Ask user name', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row">
                <input type="checkbox" class="filled-in" id="chkCollectEmail" name="entity[collect_email]" value="1"<?php if ( $entity->collect_email ): ?> checked="checked"<?php endif; ?> />
                <label for="chkCollectEmail" class="label"><?php _e( 'Ask e-mail', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row">
                <input type="checkbox" class="filled-in block-switcher" data-ref-id="collectConfirmContainer" id="chkCollectConfirm" name="entity[quiz_meta][lead_form][ask_confirmation]" value="1"<?php if ( $entity->quiz_meta->lead_form->ask_confirmation ): ?> checked="checked"<?php endif; ?> />
                <label for="chkCollectConfirm" class="label"><?php _e( 'Ask confirmation', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row sub-section" id="collectConfirmContainer">
                <div class="row">
                    <div class="col s12">
                        <input type="checkbox" class="filled-in" id="chkConfirmEnabled" name="entity[quiz_meta][lead_form][ask_confirmation_enabled]" value="1"<?php if ( $entity->quiz_meta->lead_form->ask_confirmation_enabled ): ?> checked="checked"<?php endif; ?> />
                        <label for="chkConfirmEnabled" class="label"><?php _e( 'Enable by default', 'ari-stream-quiz' ); ?></label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <textarea id="txtCollectConfirmLabel" name="entity[quiz_meta][lead_form][confirmation_label]" class="materialize-textarea" placeholder="<?php echo esc_attr_e( 'I agree to processing my data ', 'ari-stream-quiz' ); ?>"><?php echo esc_attr( $entity->quiz_meta->lead_form->confirmation_label ); ?></textarea>
                        <label for="txtCollectConfirmLabel"><?php _e( 'Confirmation text', 'ari-stream-quiz' ); ?></label>
                    </div>
                </div>
            </div>
            <div class="row">
                <input type="checkbox" class="filled-in" id="chkCollectDataOptional" name="entity[collect_data_optional]" value="1"<?php if ( $entity->collect_data_optional ): ?> checked="checked"<?php endif; ?> />
                <label for="chkCollectDataOptional" class="label"><?php _e( 'Is optional?', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row">
                <input type="checkbox" class="filled-in block-switcher" data-ref-id="mailchimpContainer" id="chkMailchimp" name="entity[quiz_meta][mailchimp][enabled]" value="1"<?php if ( $entity->quiz_meta->mailchimp->enabled ): ?> checked="checked"<?php endif; ?> />
                <label for="chkMailchimp" class="label"><?php _e( 'MailChimp integration', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row sub-section" id="mailchimpContainer">
                <?php
                    if ( empty( $mailchimp_apikey ) ):
                ?>
                <div class="row">
                    <div class="red-text"><?php _e( 'Enter a MailChimp API key on "Settings" page otherwise integration with MailChimp service will not work.', 'ari-stream-quiz' ); ?></div>
                </div>
                <?php
                    else:
                ?>
                <div class="row">
                    <div class="col s12">
                        <div>
                            <label class="label" for="ddlMailchimpListId"><?php _e( 'List', 'ari-stream-quiz' ); ?></label>
                        </div>
                        <div class="clearfix">
                            <select id="ddlMailchimpListId" name="entity[quiz_meta][mailchimp][list_id]" class="browser-default left inline-block" autocomplete="off">
                                <option value=""><?php _e( '- None -', 'ari-stream-quiz' ); ?></option>
                                <?php
                                    if ($entity->quiz_meta->mailchimp->list_id):
                                ?>
                                <option value="<?php echo esc_attr( $entity->quiz_meta->mailchimp->list_id ); ?>" selected="selected"><?php echo esc_html( $entity->quiz_meta->mailchimp->list_name ); ?></option>
                                <?php
                                    endif;
                                ?>
                            </select><a href="#" id="mailchimpListRefresh" class="small"><i class="small material-icons">loop</i></a>
                            <input type="hidden" id="hidMailchimpListName" name="entity[quiz_meta][mailchimp][list_name]" value="<?php echo esc_attr( $entity->quiz_meta->mailchimp->list_name ); ?>" />
                        </div>
                    </div>
                </div>
                <?php
                    endif;
                ?>
            </div>

            <div class="row">
                <input type="checkbox" class="filled-in block-switcher" data-ref-id="mailerliteContainer" id="chkMailerLite" name="entity[quiz_meta][mailerlite][enabled]" value="1"<?php if ( $entity->quiz_meta->mailerlite->enabled ): ?> checked="checked"<?php endif; ?> />
                <label for="chkMailerLite" class="label"><?php _e( 'MailerLite integration', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row sub-section" id="mailerliteContainer">
                <?php
                    if ( empty( $mailerlite_apikey ) ):
                ?>
                    <div class="row">
                        <div class="red-text"><?php _e( 'Enter a MailerLite API key on "Settings" page otherwise integration with MailerLite service will not work.', 'ari-stream-quiz' ); ?></div>
                    </div>
                <?php
                    else:
                ?>
                    <div class="row">
                        <div class="col s12">
                            <div>
                                <label class="label" for="ddlMailerLiteListId"><?php _e( 'List', 'ari-stream-quiz' ); ?></label>
                            </div>
                            <div class="clearfix">
                                <select id="ddlMailerLiteListId" name="entity[quiz_meta][mailerlite][list_id]" class="browser-default left inline-block" autocomplete="off">
                                    <option value=""><?php _e( '- None -', 'ari-stream-quiz' ); ?></option>
                                    <?php
                                        if ($entity->quiz_meta->mailerlite->list_id):
                                    ?>
                                        <option value="<?php echo esc_attr( $entity->quiz_meta->mailerlite->list_id ); ?>" selected="selected"><?php echo esc_html( $entity->quiz_meta->mailerlite->list_name ); ?></option>
                                    <?php
                                        endif;
                                    ?>
                                </select><a href="#" id="mailerLiteListRefresh" class="small"><i class="small material-icons">loop</i></a>
                                <input type="hidden" id="hidMailerLiteListName" name="entity[quiz_meta][mailerlite][list_name]" value="<?php echo esc_attr( $entity->quiz_meta->mailerlite->list_name ); ?>" />
                            </div>
                        </div>
                    </div>
                <?php
                    endif;
                ?>
            </div>

            <div class="row">
                <input type="checkbox" class="filled-in block-switcher" data-ref-id="aweberContainer" id="chkAWeber" name="entity[quiz_meta][aweber][enabled]" value="1"<?php if ( $entity->quiz_meta->aweber->enabled ): ?> checked="checked"<?php endif; ?> />
                <label for="chkAWeber" class="label"><?php _e( 'AWeber integration', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row sub-section" id="aweberContainer">
                <?php
                if ( empty( $aweber_auth_code ) ):
                    ?>
                    <div class="row">
                        <div class="red-text"><?php _e( 'Generate AWeber credentials on "Settings" page otherwise integration with AWeber service will not work.', 'ari-stream-quiz' ); ?></div>
                    </div>
                <?php
                else:
                ?>
                <div class="row">
                    <div class="col s12">
                        <div>
                            <label class="label" for="ddlAweberListId"><?php _e( 'List', 'ari-stream-quiz' ); ?></label>
                        </div>
                        <div class="clearfix">
                            <select id="ddlAweberListId" name="entity[quiz_meta][aweber][list_id]" class="browser-default left inline-block" autocomplete="off">
                                <option value=""><?php _e( '- None -', 'ari-stream-quiz' ); ?></option>
                                <?php
                                if ($entity->quiz_meta->aweber->list_id):
                                    ?>
                                    <option value="<?php echo esc_attr( $entity->quiz_meta->aweber->list_id ); ?>" selected="selected"><?php echo esc_html( $entity->quiz_meta->aweber->list_name ); ?></option>
                                <?php
                                endif;
                                ?>
                            </select><a href="#" id="aweberListRefresh" class="small"><i class="small material-icons">loop</i></a>
                            <input type="hidden" id="hidAweberListName" name="entity[quiz_meta][aweber][list_name]" value="<?php echo esc_attr( $entity->quiz_meta->aweber->list_name ); ?>" />
                        </div>
                    </div>
                </div>
                <?php
                endif;
                ?>
            </div>

            <div class="row">
                <input type="checkbox" class="filled-in block-switcher" data-ref-id="zapierContainer" id="chkZapier" name="entity[quiz_meta][zapier][enabled]" value="1"<?php if ( $entity->quiz_meta->zapier->enabled ): ?> checked="checked"<?php endif; ?> />
                <label for="chkZapier" class="label"><?php _e( 'Zapier integration', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row sub-section" id="zapierContainer">
                <div class="row">
                        <div class="col s10 input-field">
                            <input type="text" id="tbxZapierWebhookUrl" name="entity[quiz_meta][zapier][webhook_url]" value="<?php echo esc_attr( $entity->quiz_meta->zapier->webhook_url ); ?>" />
                            <label for="tbxZapierWebhookUrl"><?php _e( 'Webhook URL', 'ari-stream-quiz' ); ?></label>
                        </div>
                        <div class="col s2 input-field">
                            <a href="#" id="zapierTestWebhook" class="small" title="<?php esc_attr_e( 'Click this button to verify webhook URL and send test data to Zapier', 'ari-stream-quiz' ); ?>"><i class="small material-icons icon-action">import_export</i></a>
                        </div>
                        <div class="col s10 right-align">
                            <a id="lnkZapierApp" href="https://zapier.com/developer/invite/50686/42ef335654e68df08b863220ebd1ce04/" target="_blank" title="<?php esc_attr_e( 'Click here to add "ARI Stream Quiz" Zapier app to your Zapier account', 'ari-stream-quiz' ); ?>"><?php _e( 'Add Zapier app', 'ari-stream-quiz' ); ?></a>
                        </div>
                </div>
            </div>

            <div class="row">
                <input type="checkbox" class="filled-in block-switcher" data-ref-id="getresponseContainer" id="chkGetResponse" name="entity[quiz_meta][getresponse][enabled]" value="1"<?php if ( $entity->quiz_meta->getresponse->enabled ): ?> checked="checked"<?php endif; ?> />
                <label for="chkGetResponse" class="label"><?php _e( 'GetResponse integration', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row sub-section" id="getresponseContainer">
                <?php
                    if ( empty( $getresponse_apikey ) ):
                ?>
                    <div class="row">
                        <div class="red-text"><?php _e( 'Enter a GetResponse API key on "Settings" page otherwise integration with GetResponse service will not work.', 'ari-stream-quiz' ); ?></div>
                    </div>
                <?php
                    else:
                ?>
                    <div class="row">
                        <div class="col s12">
                            <div>
                                <label class="label" for="ddlGetResponseCampaignId"><?php _e( 'Campaign', 'ari-stream-quiz' ); ?></label>
                            </div>
                            <div class="clearfix">
                                <select id="ddlGetResponseCampaignId" name="entity[quiz_meta][getresponse][campaign_id]" class="browser-default left inline-block" autocomplete="off">
                                    <option value=""><?php _e( '- None -', 'ari-stream-quiz' ); ?></option>
                                    <?php
                                        if ( $entity->quiz_meta->getresponse->campaign_id ):
                                    ?>
                                        <option value="<?php echo esc_attr( $entity->quiz_meta->getresponse->campaign_id ); ?>" selected="selected"><?php echo esc_html( $entity->quiz_meta->getresponse->campaign_name ); ?></option>
                                    <?php
                                        endif;
                                    ?>
                                </select><a href="#" id="getresponseListRefresh" class="small"><i class="small material-icons">loop</i></a>
                                <input type="hidden" id="hidGetresponseCampaignName" name="entity[quiz_meta][getresponse][campaign_name]" value="<?php echo esc_attr( $entity->quiz_meta->getresponse->campaign_name ); ?>" />
                            </div>
                        </div>
                    </div>
                <?php
                    endif;
                ?>
            </div>

            <div class="row">
                <input type="checkbox" class="filled-in block-switcher" data-ref-id="dripContainer" id="chkDrip" name="entity[quiz_meta][drip][enabled]" value="1"<?php if ( $entity->quiz_meta->drip->enabled ): ?> checked="checked"<?php endif; ?> />
                <label for="chkDrip" class="label"><?php _e( 'Drip integration', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row sub-section" id="dripContainer">
                <?php
                if ( empty( $drip_apikey ) || empty( $drip_account_id ) ):
                    ?>
                    <div class="row">
                        <div class="red-text"><?php _e( 'Enter a Drip API key and account ID on "Settings" page otherwise integration with Drip service will not work.', 'ari-stream-quiz' ); ?></div>
                    </div>
                <?php
                else:
                    ?>
                    <div class="row">
                        <div class="col s12">
                            <div>
                                <label class="label" for="ddlDripCampaignId"><?php _e( 'Campaign', 'ari-stream-quiz' ); ?></label>
                            </div>
                            <div class="clearfix">
                                <select id="ddlDripCampaignId" name="entity[quiz_meta][drip][campaign_id]" class="browser-default left inline-block" autocomplete="off">
                                    <option value=""><?php _e( '- None -', 'ari-stream-quiz' ); ?></option>
                                    <?php
                                    if ( $entity->quiz_meta->drip->campaign_id ):
                                        ?>
                                        <option value="<?php echo esc_attr( $entity->quiz_meta->drip->campaign_id ); ?>" selected="selected"><?php echo esc_html( $entity->quiz_meta->drip->campaign_name ); ?></option>
                                    <?php
                                    endif;
                                    ?>
                                </select><a href="#" id="dripListRefresh" class="small"><i class="small material-icons">loop</i></a>
                                <input type="hidden" id="hidDripCampaignName" name="entity[quiz_meta][drip][campaign_name]" value="<?php echo esc_attr( $entity->quiz_meta->drip->campaign_name ); ?>" />
                            </div>
                        </div>
                    </div>
                <?php
                endif;
                ?>
            </div>

            <div class="row">
                <input type="checkbox" class="filled-in block-switcher" data-ref-id="activeCampaignContainer" id="chkActiveCampaign" name="entity[quiz_meta][activecampaign][enabled]" value="1"<?php if ( $entity->quiz_meta->activecampaign->enabled ): ?> checked="checked"<?php endif; ?> />
                <label for="chkActiveCampaign" class="label"><?php _e( 'ActiveCampaign integration', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row sub-section" id="activeCampaignContainer">
                <?php
                if ( empty( $activecampaign_apikey ) || empty( $activecampaign_url ) ):
                    ?>
                    <div class="row">
                        <div class="red-text"><?php _e( 'Enter an ActiveCampaign API key and URL on "Settings" page otherwise integration with ActiveCampaign service will not work.', 'ari-stream-quiz' ); ?></div>
                    </div>
                <?php
                else:
                    ?>
                    <div class="row">
                        <div class="col s12">
                            <div>
                                <label class="label" for="ddlActiveCampaignListId"><?php _e( 'List', 'ari-stream-quiz' ); ?></label>
                            </div>
                            <div class="clearfix">
                                <select id="ddlActiveCampaignListId" name="entity[quiz_meta][activecampaign][list_id]" class="browser-default left inline-block" autocomplete="off">
                                    <option value=""><?php _e( '- None -', 'ari-stream-quiz' ); ?></option>
                                    <?php
                                    if ( $entity->quiz_meta->activecampaign->list_id ):
                                        ?>
                                        <option value="<?php echo esc_attr( $entity->quiz_meta->activecampaign->list_id ); ?>" selected="selected"><?php echo esc_html( $entity->quiz_meta->activecampaign->list_name ); ?></option>
                                    <?php
                                    endif;
                                    ?>
                                </select><a href="#" id="activeCampaignListRefresh" class="small"><i class="small material-icons">loop</i></a>
                                <input type="hidden" id="hidActiveCampaignListName" name="entity[quiz_meta][activecampaign][list_name]" value="<?php echo esc_attr( $entity->quiz_meta->activecampaign->list_name ); ?>" />
                            </div>
                        </div>
                    </div>
                <?php
                endif;
                ?>
            </div>

            <div class="row">
                <input type="checkbox" class="filled-in block-switcher" data-ref-id="constantContactContainer" id="chkConstantContact" name="entity[quiz_meta][constantcontact][enabled]" value="1"<?php if ( $entity->quiz_meta->constantcontact->enabled ): ?> checked="checked"<?php endif; ?> />
                <label for="chkConstantContact" class="label"><?php _e( 'ConstantContact integration', 'ari-stream-quiz' ); ?></label>
            </div>
            <div class="row sub-section" id="constantContactContainer">
                <?php
                    if ( empty( $constantcontact_access_token ) || empty( $constantcontact_apikey ) ):
                ?>
                    <div class="row">
                        <div class="red-text"><?php _e( 'Enter a ConstantContact API key and access token on "Settings" page otherwise integration with ConstantContact service will not work.', 'ari-stream-quiz' ); ?></div>
                    </div>
                <?php
                    else:
                ?>
                    <div class="row">
                        <div class="col s12">
                            <div>
                                <label class="label" for="ddlConstantContactListId"><?php _e( 'List', 'ari-stream-quiz' ); ?></label>
                            </div>
                            <div class="clearfix">
                                <select id="ddlConstantContactListId" name="entity[quiz_meta][constantcontact][list_id]" class="browser-default left inline-block" autocomplete="off">
                                    <option value=""><?php _e( '- None -', 'ari-stream-quiz' ); ?></option>
                                    <?php
                                        if ( $entity->quiz_meta->constantcontact->list_id ):
                                    ?>
                                        <option value="<?php echo esc_attr( $entity->quiz_meta->constantcontact->list_id ); ?>" selected="selected"><?php echo esc_html( $entity->quiz_meta->constantcontact->list_name ); ?></option>
                                    <?php
                                        endif;
                                    ?>
                                </select><a href="#" id="constantContactListRefresh" class="small"><i class="small material-icons">loop</i></a>
                                <input type="hidden" id="hidConstantContactListName" name="entity[quiz_meta][constantcontact][list_name]" value="<?php echo esc_attr( $entity->quiz_meta->constantcontact->list_name ); ?>" />
                            </div>
                        </div>
                    </div>
                <?php
                    endif;
                ?>
            </div>
        </div>
    </div>
    <div class="divider"></div>
    <br /><br />
    <div>
        <div class="row">
            <input type="checkbox" class="filled-in block-switcher" data-ref-id="customContentContainer" id="chkCustomContent" name="entity[quiz_meta][content][enabled]" value="1"<?php if ( $entity->quiz_meta->content->enabled ): ?> checked="checked"<?php endif; ?> />
            <label for="chkCustomContent" class="label"><?php _e( 'Custom content management', 'ari-stream-quiz' ); ?></label>
        </div>
        <div class="row sub-section" id="customContentContainer">
            <div class="row">
                <div class="input-field col s12">
                    <textarea id="txtContentBeforeResult" name="entity[quiz_meta][content][before_result]" class="materialize-textarea"><?php echo esc_attr( $entity->quiz_meta->content->before_result ); ?></textarea>
                    <label for="txtContentBeforeResult"><?php _e( 'Content before result area', 'ari-stream-quiz' ); ?></label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <textarea id="txtContentAfterResult" name="entity[quiz_meta][content][after_result]" class="materialize-textarea"><?php echo esc_attr( $entity->quiz_meta->content->after_result ); ?></textarea>
                    <label for="txtContentAfterResult"><?php _e( 'Content after result area', 'ari-stream-quiz' ); ?></label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <textarea id="txtContentBeforeLeadForm" name="entity[quiz_meta][content][before_lead_form]" class="materialize-textarea"><?php echo esc_attr( $entity->quiz_meta->content->before_lead_form ); ?></textarea>
                    <label for="txtContentBeforeLeadForm"><?php _e( 'Content before lead form', 'ari-stream-quiz' ); ?></label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <textarea id="txtContentLeadForm" name="entity[quiz_meta][content][after_lead_form]" class="materialize-textarea"><?php echo esc_attr( $entity->quiz_meta->content->after_lead_form ); ?></textarea>
                    <label for="txtContentLeadForm"><?php _e( 'Content after lead form', 'ari-stream-quiz' ); ?></label>
                </div>
            </div>
        </div>
    </div>
    <div class="divider"></div>
    <br /><br />
    <div>
        <div class="row">
            <input type="checkbox" class="filled-in block-switcher" data-ref-id="mailContainer" id="chkMail" name="entity[quiz_meta][send_mail][enabled]" value="1"<?php if ( $entity->quiz_meta->send_mail->enabled ): ?> checked="checked"<?php endif; ?> />
            <label for="chkMail" class="label"><?php _e( 'Send results by e-mail', 'ari-stream-quiz' ); ?></label>
        </div>
        <div class="row sub-section" id="mailContainer">
            <div class="row">
                <div class="col s12">
                    <b><?php _e( 'Note', 'ari-stream-quiz' ); ?>:</b> <?php _e( 'The mail will be sent to a quiz taker if an e-mail specified.', 'ari-stream-quiz' ); ?>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="tbxEmailSubject"><?php _e( 'Subject', 'ari-stream-quiz' ); ?></label>
                    <input type="text" id="tbxEmailSubject" name="entity[quiz_meta][send_mail][subject]" placeholder="<?php esc_attr_e( 'Subject', 'ari-stream-quiz' ); ?>"  value="<?php echo esc_attr( $entity->quiz_meta->send_mail->subject ); ?>" />
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div>
                        <label class="label" for="txtEmailTemplate"><?php _e( 'Mail template', 'ari-stream-quiz' ); ?></label>
                    </div>
                    <div>
                        <textarea name="entity[quiz_meta][send_mail][template]" id="txtEmailTemplate" placeholder="<?php esc_attr_e( 'Enter mail template here', 'ari-stream-quiz' ); ?>"><?php echo esc_attr( $entity->quiz_meta->send_mail->template ); ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <?php _e( 'The following predefined values can be used in mail subject and template:', 'ari-stream-quiz' ); ?>
                    <?php
                    if ( ARISTREAMQUIZ_QUIZTYPE_TRIVIA == $entity->quiz_type ):
                    ?>
                    <ul>
                        <li>{$userScore} <?php _e( 'contains number of correctly answered questions', 'ari-stream-quiz' ); ?></li>
                        <li>{$userScorePercent} <?php _e( 'contains number of correctly answered questions in percent', 'ari-stream-quiz' ); ?></li>
                        <li>{$maxScore} <?php _e( 'contains number of questions', 'ari-stream-quiz' ); ?></li>
                        <li>{$title} <?php _e( 'contains title of result template', 'ari-stream-quiz' ); ?></li>
                        <li>{$content} <?php _e( 'contains content of result template', 'ari-stream-quiz' ); ?></li>
                        <li>{$image} <?php _e( 'contains image of result template', 'ari-stream-quiz' ); ?></li>
                        <li>{$quiz} <?php _e( 'contains quiz name', 'ari-stream-quiz' ); ?></li>
                        <li>{$url} <?php _e( 'contains page URL', 'ari-stream-quiz' ); ?></li>
                    </ul>
                    <?php
                    elseif ( ARISTREAMQUIZ_QUIZTYPE_PERSONALITY == $entity->quiz_type ):
                    ?>
                    <ul>
                        <li>{$score} <?php _e( 'contains earned score for personality', 'ari-stream-quiz' ); ?></li>
                        <li>{$userScorePercent} <?php _e( 'contains number earned points in percent from maximum points for personality', 'ari-stream-quiz' ); ?></li>
                        <li>{$maxScore} <?php _e( 'contains number of maximum points for the selected personality', 'ari-stream-quiz' ); ?></li>
                        <li>{$title} <?php _e( 'contains personality name', 'ari-stream-quiz' ); ?></li>
                        <li>{$content} <?php _e( 'contains personality description', 'ari-stream-quiz' ); ?></li>
                        <li>{$image} <?php _e( 'contains personality image', 'ari-stream-quiz' ); ?></li>
                        <li>{$quiz} <?php _e( 'contains quiz name', 'ari-stream-quiz' ); ?></li>
                        <li>{$url} <?php _e( 'contains page URL', 'ari-stream-quiz' ); ?></li>
                        <li>{$summary} <?php _e( 'contains summary with all personalities like on quiz results page', 'ari-stream-quiz' ); ?></li>
                        <li>{$summarySecondary} <?php _e( 'contains summary with all personalities except main personality', 'ari-stream-quiz' ); ?></li>
                    </ul>
                    <div>
                        <b><?php _e( 'Note', 'ari-stream-quiz' ); ?>:</b> <?php _e( 'only data for main personality can be used.'); ?>
                    </div>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php do_action( 'asq_ui_quiz_settings_bottom', $entity ); ?>
</div>