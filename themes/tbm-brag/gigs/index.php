<?php

    
    date_default_timezone_set ( 'Australia/Sydney' );
    $date = isset( $_GET['date'] ) ? $_GET['date'] : date('Y-m-d');
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if ( $d && $d->format('Y-m-d') === $date ) {
    } else {
        $date = date('Y-m-d');
    }
    unset($_GET['date']);
    $querystring = http_build_query($_GET);

    $prev_date = date('Y-m-d', strtotime('-1 days', strtotime($date)));
    $next_date = date('Y-m-d', strtotime('+1 days', strtotime($date)));
    
    $query_title = isset( $_GET['title'] ) ? $_GET['title'] : '';
    $query_artist = isset( $_GET['artist'] ) ? $_GET['artist'] : '';
    $query_date = isset( $_GET['gig_date'] ) ? $_GET['gig_date'] : '';
    
    require_once('../wp-load.php'); get_header();
    
    wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array ( 'jquery' ), 1.1, true);
    wp_enqueue_script( 'jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array ( 'jquery' ), 1.1, true);
    wp_enqueue_style( 'jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css' );
?>
<div class="container">
<div class="col-left">
    <?php
    if ( isset( $_GET['search'] ) ) :
        include( 'gig-search.php' );
    else:
        ?>
    <div class="pagenav-gigs">
        <a href="/gigs/?date=<?php echo $prev_date; ?>">« <?php echo date('j M', strtotime($prev_date)); ?></a>
        <a href="/gigs/?date=<?php echo $next_date; ?>"><?php echo date('j M', strtotime($next_date)); ?> »</a>
    </div>
    <h1 class="gig-date-heading"><?php echo date('j M, Y', strtotime($date)); ?></h1>
    <div class="clear"></div>
    <?php
        $where = "
            p.post_status = 'publish'
            AND
            tt.taxonomy = 'gig-genre'
            AND
            DATE(gd.gig_datetime) = '{$date}'";
        $query = "
            SELECT
                t.term_id, t.name
            FROM {$wpdb->prefix}terms t
                INNER JOIN {$wpdb->prefix}term_taxonomy tt
                    ON tt.term_id = t.term_id
                INNER JOIN {$wpdb->prefix}term_relationships tr
                    ON tt.term_taxonomy_id = tr.term_taxonomy_id
                INNER JOIN {$wpdb->prefix}posts p
                    ON p.ID = tr.object_id
                INNER JOIN {$wpdb->prefix}gig_details gd
                    ON gd.post_id = p.ID
            WHERE
                {$where}
            GROUP BY
                t.term_id
        ";
        $genres = $wpdb->get_results($query);
        $genre_gigs = array();
        if ( count( $genres ) > 0 ):
            foreach ( $genres as $genre ):
                $where = "
                    p.post_status = 'publish'
                        AND
                        DATE(gd.gig_datetime) = '{$date}'
                        AND
                        t.term_id = {$genre->term_id}
                ";
                $query = "
                    SELECT
                        p.ID, p.post_title, p.post_content, gd.gig_datetime datetime, gd.imported_from
                    FROM {$wpdb->prefix}posts p
                    LEFT JOIN {$wpdb->prefix}gig_details gd
                        ON p.ID = gd.post_id
                    LEFT JOIN {$wpdb->prefix}term_relationships tr
                        ON p.ID = tr.object_id
                    LEFT JOIN {$wpdb->prefix}term_taxonomy tt
                        ON tt.term_taxonomy_id = tr.term_taxonomy_id
                    LEFT JOIN {$wpdb->prefix}terms t
                        ON t.term_id = tt.term_id
                    WHERE
                        {$where}
                    ORDER BY
                        p.post_title
                ";
                $genre_gigs[$genre->name] = $wpdb->get_results($query);
            endforeach;
        endif;
        
        $i = 0;
        if ( count( $genre_gigs ) > 0 ) :
        foreach ( $genre_gigs as $genre => $gigs ):
            $accordion_class = $i == 0 ? 'active' : 'active';
            $panel_class = ''; //$i == 0 ? '' : 'hide';
//            echo '<h2 class="accordion ' . $accordion_class . '">' . $genre . ' (' . count( $gigs ) . ') <i class="fa fa-caret-down" aria-hidden="true"></i> </h2>';
//            echo '<div class="panel ' . $panel_class . '">';
            $i++;
    ?>
    <div class="gigs-wrap">
    <?php
            foreach ( $gigs as $gig ):
            $venues = new WP_Query( array(
                'connected_type' => 'gig_to_venue',
                'connected_items' => $gig->ID,
                'nopaging' => true,
                ) );
            include( 'gig-list-item.php' );
            endforeach; // For Each Gigs
    ?>
    </div>
    <?php
//            echo '</div>';
        endforeach; // For Each Genre Gigs
        else :
            echo '<p style="text-align: center;">We couldn\'t find any gigs on that date. Please use search form to find more gigs.</p>';
        endif; // If array $genre_gigs is NOT empty
        endif;
    ?>
    
    <?php if ( isset( $show_whatslively_credit ) && $show_whatslively_credit ) : ?>
<a href="http://www.whatslively.com/" target="_blank"><img src="<?php echo get_template_directory_uri() . '/images/WL-Footer-300px.jpg'; ?>" width="300" style="display: inline-block; margin-top: 30px;"></a>
<?php endif; ?>
</div>
    
<div class="col-right" id="gigs-sidebar">
    <?php include( get_template_directory() . '/gig-search-form.php' ); ?>
    <?php include( get_template_directory() . '/column-3.php' ); ?>
</div>
    
</div>



<?php get_footer();