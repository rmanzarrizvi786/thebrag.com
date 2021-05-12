<?php
add_action('admin_menu', 'ssm_import_whatslively_menu');
function ssm_import_whatslively_menu() {
    if ( 1 == get_current_blog_id() ) {
        add_menu_page( 'WhatsLively Importer', 'WhatsLively Importer', 'manage_options', 'import_whatslively', 'ssm_import_whatslively_index', 'dashicons-download', 5 );
    }
}

function ssm_import_whatslively_index() {
    global $wpdb;
//    print_obj( _get_cron_array() );
    date_default_timezone_set( 'Australia/NSW' );
    include( 'form.php' );
//    $next_run_timestamp = wp_next_scheduled( 'cron_hook_ssm_import_whatslively', array( NULL, NULL ) );
//    echo '<br>Scheduled automatic run is at ' . date( 'd-M-Y h:i:sa', $next_run_timestamp );
//    echo '<br>Current Date/Time: ' . date( 'd-M-Y h:i:sa' );
}

add_action( 'admin_action_import_whatslively', 'ssm_import_whatslively' );
function ssm_import_whatslively() {
    $start = date( 'Y-m-d\TH:i:s', ( strtotime( $_POST['start'] ) ) );
    $end = date( 'Y-m-d\TH:i:s', ( strtotime( $_POST['end'] ) ) );
    
    exec_cron_ssm_import_whatslively( $start, $end );
    
    wp_redirect( $_SERVER['HTTP_REFERER'] );
    exit();
}

add_action( 'cron_hook_ssm_import_whatslively', 'exec_cron_ssm_import_whatslively' );
function exec_cron_ssm_import_whatslively( $start = NULL, $end = NULL ) {
    
    global $wpdb;
    
    // Set Deafault Timezone to Australia/NSW to avoid date/time issue
    date_default_timezone_set( 'Australia/NSW' );
    
    $options_counter = array(
        'existing_gigs' => 0,
        'new_gigs' => 0,
        'existing_venues' => 0,
        'new_venues' => 0
    );
    update_option( 'WhatsLively_Import_Start', date('Y-m-d H:i:s'), false );
    update_option( 'WhatsLively_Import_Status', 'Started', false );
    
    $base_url = 'https://api.whatslively.com/v1/events';
    $key = 'bcf970ec-f5df-4afc-bcf1-01c606f77075';
    
    if ( is_null( $start ) ) {
//        $last_gig_datetime = $wpdb->get_var( "SELECT MAX(gig_datetime) FROM {$wpdb->prefix}gig_details WHERE imported_from = 'WhatsLively' LIMIT 1" );
//        if ( ! is_null( $last_gig_datetime ) ) {
//            $start = date( 'd M Y', strtotime( $last_gig_datetime ) );
//        } else {
//            $start = date( 'd M Y' );
//        }
        $start = date( 'd M Y' );
    }
    
    $date_from = new DateTime( $start );
    if ( ! is_null( $end ) ) {
        $date_to = new DateTime( $end );
    } else {
        $date_to = new DateTime( date( 'Y-m-d', strtotime( '+30 days', strtotime( $start ) ) ) );
    }
    
//    $interval = DateInterval::createFromDateString('1 day');
    message( '<table border="1" cellpadding="5" style="border-collapse: collapse">' );
    for( $date = $date_from; $date <= $date_to; $date->modify( '+1 day' ) ) :
        
        $post_url = $base_url . '?key=' . $key . '&state=NSW&dateKey=custom&date=' . $date->format("Y-m-d");
        $output = ssm_curl( $post_url );
//        var_dump( $output ); exit;
        $gigs = json_decode( $output );
        message( '<tr><th colspan="10">' . $date->format("Y-m-d") . '</th></tr>' );
        message( '<tr>
            <th>&nbsp;</th>
            <th>Gig WL ID</th>
            <th>Gig WL Name</th>
            <th>Artists</th>
            <th>WL Venue ID</th>
            <th>WL Venue</th>
            <th>WL Venue Address</th>
            <th>TB Venue</th>
            </tr>' );
        if ( count( $gigs ) > 0 ) :
            $count = 1;
            foreach ( $gigs as $gig ) :
                // WL = WhatsLively
                // Check if Gig exists with matching WL_ID
                $existing_gig = $wpdb->get_row( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}gig_details WHERE whatslively_ID = %s LIMIT 1", $gig->id ) );
                
                // Gig using WL_ID NOT found. Check if Gig exists with matching Title and Date
                if ( is_null( $existing_gig ) ) :
                    $existing_gig = $wpdb->get_row( $wpdb->prepare( "SELECT gd.post_id
                        FROM {$wpdb->prefix}posts p INNER JOIN {$wpdb->prefix}gig_details gd ON p.ID = gd.post_id
                        WHERE p.post_title = %s AND DATE(gd.gig_datetime) = %s
                        LIMIT 1", $gig->name, $date->format("Y-m-d") ) );
                    if ( ! is_null( $existing_gig ) ) :
                        // Record WL_ID if the Gig is found with matching Title and Date
                        $wpdb->update(
                            $wpdb->prefix . 'gig_details',
                            array( 'whatslively_ID' => $gig->id, ),
                            array( 'post_id' => $existing_gig->post_id, )
                        );
                    endif; // Gig found with Title and Date
                endif; // Gig NOT found with WL_ID
                
                // Update Venue Details if Existing Gig Found
                if ( ! is_null( $existing_gig ) ) :
                    $venue = $wpdb->get_row( $wpdb->prepare( "SELECT p2p_to AS venue_id FROM {$wpdb->prefix}p2p WHERE p2p_from = %s LIMIT 1", $existing_gig->post_id ) );
                    $wpdb->replace(
                        $wpdb->prefix . 'venue_details',
                        array(
                            'post_id' => $venue->venue_id,
                            'lat' => @$gig->venue->coordinates->lat,
                            'lng' => @$gig->venue->coordinates->lon,
                            'imported_from' => 'WhatsLively',
                            'whatslively_ID' => @$gig->venue->id
                        )
                    );
                    $options_counter['existing_gigs']++;
                else : // Create Gig
                    // Check if Venue exists with the WL_ID
                    if ( isset( $gig->venue->id ) && ! is_null( $gig->venue->id ) ) {
                        $existing_venue = $wpdb->get_row( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}venue_details WHERE whatslively_ID = %s LIMIT 1", $gig->venue->id ) );
                    }
                    
                    // Venue with WL_ID not found, check with Title
                    if ( ! isset( $existing_venue ) || is_null( $existing_venue ) ) :
                        $existing_venue = $wpdb->get_row( $wpdb->prepare( "SELECT ID AS post_id FROM {$wpdb->prefix}posts WHERE post_title = %s AND post_type = 'venue' ", $gig->venue->name ) );
                    endif;
                    
                    // Venue with Title not found, create one
                    if ( is_null( $existing_venue ) ) :
                        $new_venue = array(
                            'post_title' => $gig->venue->name,
                            'post_content' => '',
                            'post_status' => 'publish',
                            'post_date' => date('Y-m-d H:i:s'),
                            'post_author' => 51096, // Gig Guide
                            'post_type' => 'venue',
                            'post_category' => array(0),
                            'meta_input' => array(
                                'street' => $gig->venue->address->street,
                                'suburb' => $gig->venue->address->city,
                                'state' => $gig->venue->address->state,
                                'postcode' => $gig->venue->address->postcode,
                            )
                        );
                        $venue_id = wp_insert_post($new_venue);
                        $options_counter['new_venues']++;
                    else:
                        $venue_id = $existing_venue->post_id;
                        $options_counter['existing_venues']++;
                    endif;
                    
                    if ( !is_null( $venue_id ) ) :
                        $wpdb->replace(
                            $wpdb->prefix . 'venue_details',
                            array(
                                'post_id' => $venue_id,
                                'lat' => @$gig->venue->coordinates->lat,
                                'lng' => @$gig->venue->coordinates->lon,
                                'imported_from' => 'WhatsLively',
                                'whatslively_ID' => @$gig->venue->id
                            )
                        );
                        $new_gig = array(
                            'post_title' => $gig->name,
                            'post_content' => $gig->short_description,
                            'post_status' => 'publish',
                            'post_date' => date('Y-m-d H:i:s'),
                            'post_author' => 51096, // Gig Guide
                            'post_type' => 'gig',
                            'post_category' => array(0),
                            'meta_input' => array(
                                'ticket_link_url_1' => $gig->ticket->url,
                            )
                        );
                        $gig_id = wp_insert_post($new_gig);
                        
                        $options_counter['new_gigs']++;
                        
                        $gig_date = date( 'Y-m-d H:i:s', strtotime( $gig->date ) );
                        if ( !is_null( $gig_id ) && !is_null( $gig->id ) ) :
                            $wpdb->insert(
                                $wpdb->prefix . 'p2p',
                                array(
                                    'p2p_from' => $gig_id,
                                    'p2p_to' => $venue_id,
                                    'p2p_type' => 'gig_to_venue'
                                )
                            );
                            $wpdb->delete( $wpdb->prefix . "gig_details", array( 'post_id' => $gig_id ) );
                            $wpdb->insert(
                                $wpdb->prefix . 'gig_details',
                                array(
                                    'post_id' => $gig_id,
                                    'gig_datetime' => $gig_date,
                                    'imported_from' => 'WhatsLively',
                                    'whatslively_ID' => $gig->id
                                )
                            );
                            $artists = array();
                            foreach ( $gig->artists as $artist ) :
                                array_push( $artists, $artist->name );
                            endforeach;
                            $artists = array_unique( $artists );
                            if ( count( $artists ) == 0 )
                                $artists = $gig->name;
                            wp_set_post_terms($gig_id, $artists, 'gig-artist');
                            
                            wp_set_object_terms($gig_id, 'Other', 'gig-genre');
                        endif;
                    endif;
                endif; // End Create Gig if does not exist
                
                message(
                        '<tr>' .
                        '<td>' . $count . '</td>' . 
                        '<td>' . $gig->id . '</td>' .
                        '<td>' . $gig->name . '</td>' .
                        '<td>' . date( 'd-m-Y h:ia', strtotime( $gig->date ) ) . '</td>' . 
                        '<td><ul>'
                        );
                foreach ( $gig->artists as $artist ) :
                    message( '<li>'. $artist->name . '</li>' );
                endforeach;
                message( '</ul></td>' );
                message( '<td>' . @$gig->venue->id . '</td>' );
                message( '<td>' . $gig->venue->name . ' (' . @$gig->venue->coordinates->lat . ',' . @$gig->venue->coordinates->lon . ')</td>' );
                $check_venue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}posts WHERE post_title = %s AND post_type = 'venue' ", $gig->venue->name ) );
                if ( $check_venue ) :
                    message( '<td><a href="' . get_the_permalink( $check_venue->ID ) . '" target="_blank">' . $check_venue->post_title . '</a></td>' );
                else :
                    $check_venue = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}posts WHERE post_title LIKE '%{$gig->venue->name}%' AND post_type = 'venue' ");
                    message( '<td><strong><a href="' . get_the_permalink( $check_venue->ID ) . '" target="_blank">' . $check_venue->post_title . '</a></strong></td>' );
                endif;
//                if ( $count == 1 )
//                    exit;
                $count ++;
            endforeach;
            
        endif;
    endfor;
    
    update_option( 'WhatsLively_Import_Status', json_encode( $options_counter ), false );
    update_option( 'WhatsLively_Import_Finish', date('Y-m-d H:i:s'), false );
    
    message( '</table>' );
}

function print_obj( $obj ) {
    echo '<pre>' . print_r( $obj, true ) . '</pre>';
}

function ssm_curl( $url ) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

    $curl_output = curl_exec($ch);
    curl_close($ch);
    
    return $curl_output;
}

/*
add_filter( 'cron_schedules', 'add_cron_interval_five_seconds' ); 
function add_cron_interval_five_seconds( $schedules ) {
    $schedules['5_seconds'] = array(
        'interval' => 5,
        'display'  => esc_html__( 'Every Five Seconds' ),
    );
    return $schedules;
}
if ( ! wp_next_scheduled( 'cron_hook_ssm_import_whatslively', array( NULL, NULL ) ) ) {
    wp_schedule_event( time(), 'hourly', 'cron_hook_ssm_import_whatslively', array( NULL, NULL ) );
}
*/

/*
 * Activation
 */
register_activation_hook( __FILE__, 'activate_ssm_import_whatslively' );
function activate_ssm_import_whatslively() {
    if ( ! wp_next_scheduled( 'cron_hook_ssm_import_whatslively', array( NULL, NULL ) ) ) {
        wp_schedule_event( time(), 'hourly', 'cron_hook_ssm_import_whatslively', array( NULL, NULL ) );
    }
}

/*
 * DeActivation
 */
register_deactivation_hook( __FILE__, 'deactivate_ssm_import_whatslively' );
function deactivate_ssm_import_whatslively() {
    $crons = _get_cron_array();
    if ( empty( $crons ) ) {
        return;
    }
    $hook = 'cron_hook_ssm_import_whatslively';
    foreach( $crons as $timestamp => $cron ) {
        if ( ! empty( $cron[$hook] ) )  {
            unset( $crons[$timestamp][$hook] );
        }
        if ( empty( $crons[$timestamp] ) ) {
            unset( $crons[$timestamp] );
        }
    }
    _set_cron_array( $crons );
}

function message( $message ) {
//    echo $message;
}

// Custom Columns for Gigs CPT
add_filter( 'manage_gig_posts_columns', 'set_custom_edit_gig_columns' );
add_action( 'manage_gig_posts_custom_column' , 'custom_gig_column', 10, 2 );

function set_custom_edit_gig_columns($columns) {
    unset( $columns['author'] );
    $columns['imported_from'] = 'Imported From';
    $columns['gig_datetime'] = 'Gig Date/Time';
    return $columns;
}

function custom_gig_column( $column, $post_id ) {
    global $wpdb;
    $gig_details = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}gig_details WHERE post_id = '{$post_id}' LIMIT 1" );
    switch ( $column ) {
        case 'imported_from' :
            echo $gig_details->imported_from;
            break;
        case 'gig_datetime' :
            if ( 'WhatsLively' == $gig_details->imported_from) {
                echo date( 'd M, Y', strtotime( $gig_details->gig_datetime ) );
            } else {
                echo date( 'd M, Y h:ia', strtotime( $gig_details->gig_datetime ) );
            }
            break;
    }
}

// Custom Columns for Venues CPT
add_filter( 'manage_venue_posts_columns', 'set_custom_edit_venue_columns' );
add_action( 'manage_venue_posts_custom_column' , 'custom_venue_column', 10, 2 );

function set_custom_edit_venue_columns($columns) {
    unset( $columns['author'] );
    $columns['imported_from'] = __( 'Imported From', 'your_text_domain' );
    return $columns;
}

function custom_venue_column( $column, $post_id ) {
    global $wpdb;
    switch ( $column ) {
        case 'imported_from' :
            $imported_from = $wpdb->get_var( "SELECT imported_from FROM {$wpdb->prefix}venue_details WHERE post_id = '{$post_id}' LIMIT 1" );
            echo $imported_from;
            break;
    }
}