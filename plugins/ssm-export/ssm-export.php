<?php
/**
 * Plugin Name: Export to CSV
 * Plugin URI: http://www.seventhstreet.media
 * Description: Export Posts (Titles and URLs) to CSV
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI: http://www.seventhstreet.media
 */
add_action('admin_menu', 'ssm_export_csv_plugin_menu');
function ssm_export_csv_plugin_menu() {
    add_management_page('Export CSV', 'Export CSV', 'edit_pages', 'ssm-export-csv', 'ssm_export_csv');
    add_management_page('Export Gigs', 'Export Gigs', 'edit_pages', 'ssm-export-gigs-xml', 'ssm_export_gigs_xml');
}

function ssm_export_csv() {
    if( !session_id())
        session_start();
    enqueue_jquery_ui_datepicker_ssm_export();
?>
<div class="wrap">
    <?php
    if ( isset( $_SESSION ) && isset( $_SESSION['ssm_errors'] ) ) :
        if ( is_array( $_SESSION['ssm_errors'] ) ) :
            echo '<ul class="errors">';
            foreach ( $_SESSION['ssm_errors'] as $error ) :
               echo '<li>' . $error . '</li>';
            endforeach;
            echo '</ul>';
        endif;
    endif;
   ?>
    <h1>Export to CSV</h1>
    <form method="post" action="<?php echo admin_url( 'tools.php' ); ?>">
        <input type="hidden" name="action" value="ssm_export_csv">
        <?php wp_nonce_field( 'ssm_verify' ); ?>

    <div class="td_input">
        <label>
            Published from <input type="text" name="date_from" id="date_from" value="<?php echo isset( $_SESSION['date_from'] ) ? $_SESSION['date_from'] : ''; ?>" class="datepicker" readonly>
        </label>
        <label>
            to <input type="text" name="date_to" id="date_to" value="<?php echo isset( $_SESSION['date_to'] ) ? $_SESSION['date_to'] : ''; ?>" class="datepicker" readonly>
        </label>
    </div>

    <div class="td_input"><?php submit_button( 'Export' ); ?></div>
    </form>
</div>
<?php
    if ( isset( $_SESSION ) ) :
        foreach ( $_SESSION as $key => $value ):
            unset ( $_SESSION[$key] );
        endforeach;
    endif;
}

/*
 * Post action for Export to CSV
 */
add_action( 'admin_action_ssm_export_csv', 'ssm_export_csv_admin_action' );
function ssm_export_csv_admin_action() {
    if ( ! current_user_can( 'edit_pages' ) ) {
        wp_die( 'You are not allowed to be on this page.' );
    }
    check_admin_referer( 'ssm_verify' );

    foreach ( $_POST as $key => $value ) :
        $_SESSION[$key] = $value;
    endforeach;
    $errors = array();

    $date_from = isset( $_POST['date_from'] ) && $_POST['date_from'] != '' ? date('Y-m-d', strtotime( $_POST['date_from'] ) ) : NULL;
    $date_to = isset( $_POST['date_to'] ) && $_POST['date_to'] != '' ? date('Y-m-d', strtotime( $_POST['date_to'] ) ) : NULL;

    if ( is_null( $date_from ) || is_null( $date_to ) || strtotime( $date_from ) > strtotime( $date_to ) ) :
        array_push( $errors, 'Please select the correct dates.' );
    endif;

    if ( count( $errors ) > 0 ):
        $_SESSION['ssm_errors'] = $errors;
        wp_redirect( admin_url( 'tools.php?page=ssm-export-csv' ) );
        exit();
    else:
        $args = array(
            'post_type' => 'any',
            'post_status' => 'publish',
            'date_query' => array(
                'after' => ( $date_from ),
                'before' => ( $date_to ),
                'inclusive' => true,
            ),
            'posts_per_page' => -1,
        );
        $posts_query = new WP_Query( $args );
        if ( $posts_query->have_posts() ) :
            $csv_data = array();
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');
//            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="export_' . $date_from . '_' . $date_to . '.csv";');
            header("Pragma: no-cache");
//            header("Pragma: public");
            header("Expires: 0");

            $csv_content = "Title, URL, Publish Date, Post Type\n";
            while( $posts_query->have_posts() ) :
                $posts_query->the_post();
                array_push( $csv_data, array(
                    get_the_title(),
                    get_the_permalink(),
                    date('d-M-Y', strtotime( get_the_date() ) ),
                    get_post_type(),
                ));
                $csv_content .= '"' . get_the_title() . '",' .
                    '"' . get_the_permalink() . '",' .
                    '"' . date('d-M-Y', strtotime( get_the_date() ) ) . '",' .
                    '"' . get_post_type() . '"';
                $csv_content .= "\n";
            endwhile;
//            foreach ($csv_data as $line) {
//                fputcsv( $f, $line, ',' );
//            }
//            echo chr(255) . chr(254) . mb_convert_encoding($csv_content, 'UTF-16LE', 'UTF-8');
            echo $csv_content;
        endif;
        exit();
//        wp_redirect( admin_url( 'tools.php?page=ssm-export-csv&s=1' ) );
    endif;
}

function ssm_export_gigs_xml() {
    if( !session_id())
        session_start();
    enqueue_jquery_ui_datepicker_ssm_export();
?>
<div class="wrap">
    <?php
    if ( isset( $_SESSION ) && isset( $_SESSION['ssm_errors'] ) ) :
        if ( is_array( $_SESSION['ssm_errors'] ) ) :
            echo '<ul class="errors">';
            foreach ( $_SESSION['ssm_errors'] as $error ) :
               echo '<li>' . $error . '</li>';
            endforeach;
            echo '</ul>';
        endif;
    endif;
   ?>
   <script>
   jQuery(document).ready(function($) {
       if ( $('.datepicker').length ) {
           $('.datepicker').datepicker( { dateFormat: 'dd M yy' } );
       }
    });
   </script>
    <h1>Export Gigs</h1>
    <form method="post" action="<?php echo admin_url( 'tools.php' ); ?>">
        <input type="hidden" name="action" value="ssm_export_gigs">
        <?php wp_nonce_field( 'ssm_verify' ); ?>

    <div class="td_input">
        <label>
            Date from <input type="text" name="date_from" id="date_from" value="<?php echo isset( $_SESSION['date_from'] ) ? $_SESSION['date_from'] : ''; ?>" class="datepicker" readonly>
        </label>
        <label>
            to <input type="text" name="date_to" id="date_to" value="<?php echo isset( $_SESSION['date_to'] ) ? $_SESSION['date_to'] : ''; ?>" class="datepicker" readonly>
        </label>
        <label>
        City
            <?php
            $states = array(
                'sydney' => 'NSW',
                'melbourne' => 'VIC',
                'brisbane' => 'QLD',
                'perth' => 'WA',
                'adelaide' => 'SA',
                'canberra' => 'ACT',
                'darwin' => 'NT',
                'hobart' => 'TAS',
            );
            if ( isset( $states ) ) : ?>
            <select name="state">
                <?php foreach ( $states as $state ) : ?>
                <option value="<?php echo $state; ?>" <?php echo isset( $query_city ) && $query_city == $state ? 'selected="selected"' : ''; ?>><?php echo ucfirst( $state ); ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
        </label>
    </div>

    <div class="td_input"><?php submit_button( 'Download XML' ); ?></div>
    </form>
</div>
<?php
    if ( isset( $_SESSION ) ) :
        foreach ( $_SESSION as $key => $value ):
            unset ( $_SESSION[$key] );
        endforeach;
    endif;
}

/*
 * Post action for Export to CSV
 */
add_action( 'admin_action_ssm_export_gigs', 'ssm_export_gigs_admin_action' );
function ssm_export_gigs_admin_action() {
    global $wpdb;
    if ( ! current_user_can( 'edit_pages' ) ) {
        wp_die( 'You are not allowed to be on this page.' );
    }
    check_admin_referer( 'ssm_verify' );

    foreach ( $_POST as $key => $value ) :
        $_SESSION[$key] = $value;
    endforeach;
    $errors = array();

    $date_from = isset( $_POST['date_from'] ) && $_POST['date_from'] != '' ? date('Y-m-d', strtotime( $_POST['date_from'] ) ) : NULL;
    $date_to = isset( $_POST['date_to'] ) && $_POST['date_to'] != '' ? date('Y-m-d', strtotime( $_POST['date_to'] ) ) : NULL;
    $state = isset( $_POST['state'] ) && $_POST['state'] != '' ? $_POST['state'] : NULL;

    if ( is_null( $date_from ) || is_null( $date_to ) || is_null( $state ) || strtotime( $date_from ) > strtotime( $date_to ) ) :
        array_push( $errors, 'Please select the correct dates and state.' );
    endif;

    if ( count( $errors ) > 0 ):
        $_SESSION['ssm_errors'] = $errors;
        wp_redirect( admin_url( 'tools.php?page=ssm-export-gigs-xml' ) );
        exit();
    else:
        $query = "SELECT
                    DISTINCT
                    g.ID ID,
                    g.post_title gig_title,
                    v.ID venue_id,
                    v.post_title venue_title,
                    gd.gig_datetime,
                    gd.imported_from
                FROM
                    {$wpdb->prefix}posts g
                        JOIN {$wpdb->prefix}p2p p2p
                            ON g.ID = p2p.p2p_from
                        JOIN {$wpdb->prefix}posts v
                            ON v.ID = p2p.p2p_to
                        JOIN {$wpdb->prefix}gig_details gd
                            ON g.ID = gd.post_id
                        JOIN {$wpdb->prefix}postmeta pmv
                            ON v.ID = pmv.post_id
                WHERE
                    g.post_type = 'gig'
                    AND
                    v.post_type = 'venue'
                    AND
                    DATE(gd.gig_datetime) >= '" . $date_from . "'
                    AND
                    DATE(gd.gig_datetime) <= '" . $date_to . "'
                    AND
                    pmv.meta_key = 'state'
                    AND
                    pmv.meta_value = '" . $state ."'
                    AND
                    g.post_status = 'publish'
                    AND
                    v.post_status = 'publish'
                ORDER BY
                    gig_datetime ASC
            ";
        $gigs = $wpdb->get_results( $query );
        if ( count( $gigs ) > 0 ) :

            $all_gigs = array();
            foreach ( $gigs as $gig ) :
                $gig_date = date( 'Y-m-d', strtotime( $gig->gig_datetime ) );
                if ( ! isset( $all_gigs[$gig_date] ) ) :
                    $all_gigs[$gig_date] = array();
                endif;
                array_push( $all_gigs[$gig_date], $gig );
            endforeach; // For Each Gig

            header('Content-type: application/xml');
            header('Content-Disposition: attachment; filename="gigs_' . $date_from . '_' . $date_to . '_' . $state . '.xml";');
            header("Pragma: no-cache");
            header("Expires: 0");
            echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>
<nodes>
    <allGigs>
        <?php foreach ( $all_gigs as $gig_date => $gigs ):
            usort( $gigs, function($a, $b) {
                return $a->gig_title <=> $b->gig_title;
            });
            ?>
            <date>
                <dateTitle><?php echo strtoupper( date('l d M', strtotime( $gig_date ) ) ); ?>&#x000A;</dateTitle>
                <?php foreach ( $gigs as $gig ) :
                    $gig_details = $gig->venue_title;
                    if ( get_field( 'suburb', $gig->venue_id ) ) :
                        $gig_details .= ', ' . trim( get_field( 'suburb', $gig->venue_id ) ) . '.';
                    endif;
                    if ( get_field( 'price', $gig->ID ) ) :
                        $gig_details .= ' $' . str_replace( '$', '', get_field('price', $gig->ID ) );
                    endif;
                    ?>
                <gig>
                    <gigTitle><?php echo htmlspecialchars( trim( $gig->gig_title ), ENT_XML1 | ENT_COMPAT, 'UTF-8'); ?>&#x000A;</gigTitle>
                    <gigDetails><?php echo htmlspecialchars( $gig_details ); ?>&#x000A;</gigDetails>
                    <promoter><?php
                    if ( get_field( 'promoter', $gig->ID ) ) :
                        echo htmlspecialchars( trim( get_field( 'promoter', $gig->ID ) ), ENT_XML1 | ENT_COMPAT, 'UTF-8') . '&#x000A;';
                    endif;
                    ?></promoter>
                </gig>
                <?php endforeach; // For Each Gig ?>
            </date>
        <?php endforeach; // For Each All_Gigs ?>
    </allGigs>
</nodes>
<?php
        endif; // If there are gigs
        exit();
    endif;
}

/*
 * Enqueue CSS and JS needed for the plugin
 */
function enqueue_jquery_ui_datepicker_ssm_export () {
    wp_enqueue_script( 'jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array ( 'jquery' ), 1.0, true);
    wp_enqueue_style( 'jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css' );
}
