<?php get_header(); ?>

<?php $exclude_posts = []; ?>

<div class="container">
    <div class="row">
        <h1 class="col-12 archive-title mb-3">Venues</h1>
    </div>
    <div class="venue-nav mb-5 text-center">
        <a href="/venue/l/_">#</a><?php
        foreach ( range('A','Z') as $l ) {
            echo '<a href="/venue/l/'. $l . '" style="border-left: 1px solid #fff;">' . $l . '</a>';
        }
    ?>
    </div>
    <?php
    $per_page = 12;
    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
    
    if ( ! get_query_var( 'l' ) ) :
    ?>
    <div class="row posts">
    <?php
        $offset = $per_page * ( $paged - 1 );
        
        $query = "
            SELECT
                DISTINCT v.ID,
                v.post_title,
                v.post_name
            FROM
                {$wpdb->prefix}posts AS v
                    INNER JOIN {$wpdb->prefix}p2p p2p
                        ON v.ID = p2p.p2p_to
                    INNER JOIN {$wpdb->prefix}gig_details gd
                        ON gd.post_ID = p2p.p2p_from
            WHERE
                v.post_type = 'venue'
                AND
                v.post_status = 'publish'
                AND
                p2p.p2p_type = 'gig_to_venue'
            GROUP BY v.ID
        ";
                    
        $res_total = $wpdb->get_results( $query );
        $total = $wpdb->num_rows;
        
        $query .= "LIMIT {$offset}, {$per_page}";
            
        $venues = $wpdb->get_results( $query );
        
//        $no_of_columns = 4;
        $count = 1;
        foreach ( $venues as $key => $venue ) :
            array_push( $exclude_posts, $venue->ID );
            $post = get_post( $venue->ID );
            
//            get_template_part( 'partials/venue' );
            
            include( get_template_directory() . '/partials/venue.php' );
            
            if ( $count == 2 ) :
                echo '<div class="col-lg-4 col-md-6 col-12 p-0">';
                get_fuse_tag( 'mrec_1' );
                echo '</div>';
            endif;
            
            if ( $count == 8 ) :
                echo '</div><div class="row"><div class="col-lg-8 posts"><div class="row">';
            endif;
            
            if ( $count >= 8 ) :
                $no_of_columns = 2;
            endif;
            
            $count++;
        endforeach;
        
        echo '</div></div>';
        echo '<div class="col-lg-4 col-md-6 col-12 p-0">';
        get_fuse_tag( 'mrec_2' );
        echo '</div>';
        ?>
        </div>
    
    
        <div class="row">
            <div class="col-12 col-lg-6 offset-lg-3 page-nav">
                <div class="d-flex justify-content-center my-4">
                    <div class="m-3"><?php previous_posts_link( '<', ceil( $total / $per_page ) ); ?></div>
                    <div class="m-3 px-5 text-center font-weight-bold align-self-center">
                        <div class="">Page <?php echo $paged; ?> / <?php echo ceil( $total / $per_page ); ?></div>
                    </div>
                    <div class="m-3"><?php next_posts_link( '>', ceil( $total / $per_page ) ); ?></div>
                </div>
            </div>
        </div>
    <?php        
    endif; // If letter is NOT set in URL
    
    if ( get_query_var( 'l' ) ) :
    ?>
    <div class="row posts">
    <?php
        $args = array(
            'post_status' => 'publish',
            'post__not_in'=> $exclude_posts,
            'post_type' => 'venue',
            'posts_per_page' => $per_page,
            'paged' => $paged,
            'orderby' => 'title',
            'order' => 'ASC',
        );
        
        add_filter( 'posts_where', 'tb_venue_posts_where', 10, 2 );
        function tb_venue_posts_where( $where ) //, &$wp_query )
        {
            global $wpdb;
            if ( $tb_venue_letter = get_query_var( 'l' ) ) {
                if ( $tb_venue_letter == '_' ) {
                    $where .= ' AND ' . $wpdb->posts . '.post_title regexp \'^[0-9]+\'';
                } else {
                    $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql( $wpdb->esc_like( $tb_venue_letter ) ) . '%\'';
                }
            }
            return $where;
        }
    
    
        $query = new WP_Query($args);

        if ( $query->have_posts() ) :
            $count = 1;
            while ( $query->have_posts() ) : $query->the_post();

            include( get_template_directory() . '/partials/venue.php' );

                if ( $count == 2 ) :
                    echo '<div class="col-lg-4 col-md-6 col-12 p-0">';
                    get_fuse_tag( 'mrec_1' );
                    echo '</div>';
                endif;

                if ( $count == 8 ) :
                    echo '</div><div class="row"><div class="col-lg-8 posts"><div class="row">';
                endif;

                if ( $count >= 8 ) :
                    $no_of_columns = 2;
                endif;

            $count++;
            endwhile;

            echo '</div></div>';
            echo '<div class="col-lg-4 col-md-6 col-12 p-0">';
            get_fuse_tag( 'mrec_2' );
            echo '</div>';
        endif;
    ?>
    </div>
    
    
    <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3 page-nav">
            <div class="d-flex justify-content-center my-4">
                <div class="m-3"><?php previous_posts_link( '<', $query->max_num_pages ); ?></div>
                <div class="m-3 px-5 text-center font-weight-bold align-self-center">
                    <div class="">Page <?php echo $paged; ?> / <?php echo $query->max_num_pages; ?></div>
                </div>
                <div class="m-3"><?php next_posts_link( '>', $query->max_num_pages ); ?></div>
            </div>
        </div>
    </div>
    <?php
    
    endif; // If showing by letter
    ?>
    
</div>


<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="" style=""><?php get_fuse_tag( 'hrec_2' ); ?></div>
        </div>
    </div>
</div>

<?php get_footer();