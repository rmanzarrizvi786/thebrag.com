<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
    <input type="hidden" name="action" value="import_whatslively" />
<div>
    <h1>Import</h1>
    <label>
        <?php
        $last_gig_datetime = $wpdb->get_var( "SELECT MAX(gig_datetime) FROM {$wpdb->prefix}gig_details WHERE imported_from = 'WhatsLively' LIMIT 1" );
        if ( ! is_null( $last_gig_datetime ) ) {
            $start = date( 'd M Y', strtotime( $last_gig_datetime ) );
        } else {
            $start = date( 'd M Y', strtotime( '-12 hours' ) ); // Start Date/Time to get songs list 12 hours before current time
        }
        ?>
        Start: <input type="text" name="start" id="start" class="datepicker" value="<?php echo $start; ?>">
    </label>
    <label>
        End: <input type="text" name="end" id="end" class="datepicker" value="<?php echo date( 'd M Y', strtotime( '+7 days', strtotime( $start ) ) ); ?>">
    </label>
    <input type="submit" class="button button-primary" value="Import">
</div>
</form>
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-ui.js"></script>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/jquery-ui.css">
<script>
    jQuery(document).ready(function () {
        jQuery(".datepicker").datepicker({
            dateFormat: 'dd M yy'
        });
    });
</script>