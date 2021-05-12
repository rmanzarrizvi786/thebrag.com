<?php
?>
<?php  settings_errors(); ?>
<form method="post" action="options.php" class="settings-page">
    <ul class="tabs" id="quiz_settings_tabs">
        <li class="tab col s3"><a class="teal-text active" href="#general_settings_tab"><?php _e( 'General', 'ari-stream-quiz' ); ?></a></li>
        <li class="tab col s3"><a class="teal-text" href="#sharing_settings_tab"><?php _e( 'Sharing', 'ari-stream-quiz' ); ?></a></li>
        <li class="tab col s3"><a class="teal-text" href="#advanced_settings_tab"><?php _e( 'Advanced', 'ari-stream-quiz' ); ?></a></li>
        <div class="indicator teal indicator-fix"></div>
    </ul>
    <div id="general_settings_tab" class="section">
        <div class="card-panel">
            <?php do_settings_sections( ARISTREAMQUIZ_SETTINGS_GENERAL_PAGE ); ?>
        </div>
    </div>
    <div id="sharing_settings_tab" class="section">
        <div class="card-panel">
            <?php do_settings_sections( ARISTREAMQUIZ_SETTINGS_SHARING_PAGE ); ?>
        </div>
    </div>
    <div id="advanced_settings_tab" class="section">
        <div class="card-panel">
            <?php do_settings_sections( ARISTREAMQUIZ_SETTINGS_ADVANCED_PAGE ); ?>
        </div>
    </div>

    <button type="submit" class="btn btn-cmd waves-effect waves-light"><?php _e( 'Save', 'ari-stream-quiz' ); ?></button>
    <?php settings_fields( ARISTREAMQUIZ_SETTINGS_GROUP ); ?>
</form>