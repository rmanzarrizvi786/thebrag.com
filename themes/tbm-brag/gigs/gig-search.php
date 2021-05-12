<!--<h1 style="margin: 20px 10px;">Gig Search Results</h1>-->
<?php
    $today = date('Y-m-d');
    $week_from_now = date('Y-m-d', strtotime( '+7 days' ) );
//    $where = "p.post_status = 'publish'
//            AND tt.taxonomy = 'gig-artist'";
    $where = "p.post_status = 'publish' ";
    
    if ( $query_date != '' ) {
        $where .= " AND DATE(gd.gig_datetime) = '" . date('Y-m-d', strtotime( $query_date ) ) . "' ";
    } else {
        $where .=" AND DATE(gd.gig_datetime) >= '{$today}' ";
    }
    /*
    if ( $query_title != '' ) {
        $where .= " AND p.post_title LIKE '%" . $query_title . "%'";
    }
    if ( $query_artist != '' ) {
        $where .= " AND t.name LIKE '%" . $query_artist . "%'";
    }
    if ( $query_city != '' ) {
        $where .= "
            AND
            pm.meta_key = 'state'
            AND
            pm.meta_value = '" . $city_state_map[$query_city] ."'
        ";
    }
     */
    
    if ( $query_date == '' || $query_title == '' || $query_artist == '' ) {
        $limit = 365;
    }
    
    /*    
    $query = "
        SELECT
            t.term_id, t.name, gd.gig_datetime
        FROM {$wpdb->prefix}terms t
            INNER JOIN {$wpdb->prefix}term_taxonomy tt
                ON tt.term_id = t.term_id
            INNER JOIN {$wpdb->prefix}term_relationships tr
                ON tt.term_taxonomy_id = tr.term_taxonomy_id
            INNER JOIN {$wpdb->prefix}posts p
                ON p.ID = tr.object_id
            INNER JOIN {$wpdb->prefix}gig_details gd
                ON gd.post_id = p.ID
            JOIN {$wpdb->prefix}p2p p2p
                ON gd.post_id = p2p.p2p_from
            JOIN {$wpdb->prefix}posts v
                ON v.ID = p2p.p2p_to
            JOIN {$wpdb->prefix}postmeta pm
                ON v.ID = pm.post_id
        WHERE
            {$where}
        GROUP BY
            gd.gig_datetime
    ";
            
//            
            
    
     * 
     */
    $query = "
        SELECT
            gd.gig_datetime
        FROM {$wpdb->prefix}posts p
            INNER JOIN {$wpdb->prefix}gig_details gd
                ON gd.post_id = p.ID
        WHERE
            {$where}
        GROUP BY
            gd.gig_datetime
    ";
    if ( isset ( $limit ) ) :
        $query .= " LIMIT {$limit} ";
    endif;
    
    $gig_dates = $wpdb->get_results($query);
    $date_gigs = array();
    if ( count( $gig_dates ) > 0 ):
        foreach ( $gig_dates as $gig_date ):
            $where = " p.post_status = 'publish' ";
            $where .= "
                AND
                DATE(gd.gig_datetime) = '" . date('Y-m-d', strtotime($gig_date->gig_datetime)) . "'
            ";
            if ( $query_title != '' ) {
                $where .= " AND p.post_title LIKE '%" . $query_title . "%'";
            }
            if ( $query_artist != '' ) {
                $where .= " AND t.name LIKE '%" . $query_artist . "%'";
            }
            if ( $query_city != '' ) {
                $where .= "
                    AND
                    pm.meta_key = 'state'
                    AND
                    pm.meta_value = '" . $city_state_map[$query_city] ."'
                ";
            }
            $where .= " AND
                    v.post_type = 'venue' ";
            $query = "
                SELECT
                    DISTINCT
                    p.ID, p.post_title, p.post_content, gd.gig_datetime datetime, gd.imported_from,
                    v.ID venue_id,
                    v.post_title venue_title
                FROM {$wpdb->prefix}posts p
                INNER JOIN {$wpdb->prefix}gig_details gd
                    ON p.ID = gd.post_id
                JOIN {$wpdb->prefix}p2p p2p
                    ON p.ID = p2p.p2p_from
                JOIN {$wpdb->prefix}posts v
                    ON v.ID = p2p.p2p_to
                JOIN {$wpdb->prefix}postmeta pm
                    ON v.ID = pm.post_id
                LEFT JOIN {$wpdb->prefix}term_relationships tr
                    ON p.ID = tr.object_id
                LEFT JOIN {$wpdb->prefix}term_taxonomy tt
                    ON tt.term_taxonomy_id = tr.term_taxonomy_id
                LEFT JOIN {$wpdb->prefix}terms t
                    ON tt.term_id = t.term_id
                
                WHERE
                    {$where}
                ORDER BY
                    gd.gig_datetime ASC
            ";
                    
//                    echo $query; exit;
            $date_gigs[ date('Y-m-d', strtotime( $gig_date->gig_datetime) )] = $wpdb->get_results($query);
        endforeach;
    else :
            echo '<p style="text-align: center;">We couldn\'t find any gigs matching your search criteria.</p>';
    endif;

    $i = 0;
    foreach ( $date_gigs as $date_gig => $gigs ):
        if ( count( $gigs ) > 0 ) :
            echo '<h2 class="accordion" style="margin-top: 1.5rem;">' . date('d M, Y', strtotime( $date_gig ) ) . ' (' . count( $gigs ) . ') <i class="fa fa-caret-down" aria-hidden="true"></i></h2>';
            echo '<div class="panel">';
            $i++;
            foreach ( $gigs as $gig ):
            $venues = new WP_Query( array(
                'connected_type' => 'gig_to_venue',
                'connected_items' => $gig->ID,
                'nopaging' => true,
                ) );
            include( get_template_directory() . '/gigs/gig-list-item.php' );
            endforeach; // For Each Gigs
            echo '</div>';
        endif;
    endforeach; // For Each Genre Gigs
?>