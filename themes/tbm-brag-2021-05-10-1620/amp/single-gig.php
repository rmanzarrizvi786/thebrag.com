<div class="single_story">
    <div class="post-content">
        <?php
        global $wpdb;
        $post = $this->post;
        $query_datetime_furutre = "SELECT * FROM {$wpdb->prefix}gig_details
            WHERE
                post_id = '" . get_the_ID() . "'
                AND
                DATE(gig_datetime) >= '" . date('Y-m-d') . "'";
        $datetimes = $wpdb->get_results( $query_datetime_furutre );

        if ( $datetimes ) :
        ?>
        <h3>Date / Time</h3>
        <ul>
        <?php
            foreach ( $datetimes as $datetime ) :
        ?>
            <li>
                <?php if ( 'whatslively' != strtolower( $datetime->imported_from ) ):
                    echo date('d M Y @ h:ia', strtotime($datetime->gig_datetime));
                else:
                    echo date('d M Y', strtotime( $datetime->gig_datetime ) );
                endif; ?>
                <?php // echo date('d M Y @ h:ia', strtotime( $datetime->gig_datetime ) ); ?>
            </li>
        <?php endforeach; ?>
        </ul>
        <?php
        endif; ?>

        <?php
        $venues = new WP_Query( array(
            'connected_type' => 'gig_to_venue',
            'connected_items' => get_queried_object(),
            'nopaging' => true,
            ) );
        if ( $venues->have_posts() ) :
        ?>
        <div>
            <h3>Venue</h3>
            <?php while ( $venues->have_posts() ) : $venues->the_post(); ?>
            <div>
                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                <?php
                $address = get_field('street') . ' ' . get_field('suburb') . ' ' . get_field('state') . ' ' . get_field('coutry');
                ?>
                <a href="http://maps.google.com/?q=<?php echo urlencode($address); ?>" target="_blank">
                <?php
                if(get_field('street')) {
                    echo get_field('street');
                    if(get_field('additional')) { echo ' ' . get_field('additional'); }
                    echo '<br>';
                }
                if(get_field('suburb')) { echo get_field('suburb') . ', '; }
                if(get_field('state')) { echo get_field('state') . ' '; }
                if(get_field('postcode')) { echo get_field('postcode'); }
                ?>
                </a>
            </div>
            <?php endwhile; ?>
            <?php
            wp_reset_postdata();
            endif;
            ?>
        </div>

        <div style="text-align: center; margin: 10px auto;">
            <!-- <amp-ad width=300 height=250
                    type="doubleclick"
                data-slot="/71161633/SSM_thebrag/tb_gig_hrec_1">
            </amp-ad> -->
            <?php if ( time() % 2 == 0 ) : ?>
    					<amp-ad width=320 height=50
    						type="doubleclick"
    						data-slot="/9876188/brag/brag_hrec_1">
    					</amp-ad>
    				<?php else : ?>
    					<amp-ad width=320 height=50
    					    type="doubleclick"
    					    data-slot="/5376056/thebrag_amp_leaderboard">
    					</amp-ad>
    				<?php endif; ?>
        </div>

        <?php
        $artists = get_the_terms( $post->ID, 'gig-artist' );
        if ( $artists && count($artists) > 0 && $artists[0] ):
            echo '<h3>Artists</h3><div>';
            foreach( $artists as $artist ):
                echo $artist->name . '<br />';
            endforeach;
            echo '</div>';
        endif;

        $supports = get_the_terms( $post->ID, 'gig-support' );
        if ( $supports && count($supports) > 0 && $supports[0]):
            echo '<div>w/ ';
            $support_names = array();
            foreach( $supports as $support ):
                array_push( $support_names, $support->name );
            endforeach;
            echo implode( ', ', $support_names );
            echo '</div>';
        endif;
        ?>
        <?php if (get_field('price')): ?>
        <h3>Price:
        $<?php echo str_replace( '$', '', get_field('price') ); ?>
        <?php endif; ?>
        </h3>

        <?php if (get_field('ticket_info')): ?>
        <?php echo '<br>' . get_field('ticket_info'); ?>
        <?php endif; ?>

        <?php if (get_field('ticket_link_url_1') || get_field('ticket_link_url_2')): ?>
        <div>
        <h3>Tickets:</h3>
        <ul>
        <?php if (get_field('ticket_link_url_1')) : ?>
            <?php
                $link_url = get_field('ticket_link_url_1');
                $link_title = get_field('ticket_link_title_1') ? get_field('ticket_link_title_1') : get_field('ticket_link_url_1');
                echo '<li><a href="' . addhttp( trim( $link_url ) ) . '" target="_blank">' . $link_title . '</a></li>';
            ?>
        <?php endif; ?>
        <?php if (get_field('ticket_link_url_2')) : ?>
            <?php
                $link_url = get_field('ticket_link_url_2');
                $link_title = get_field('ticket_link_title_2') ? get_field('ticket_link_title_2') : get_field('ticket_link_url_2');
                echo '<li><a href="' . addhttp( trim( $link_url ) ) . '" target="_blank">' . $link_title . '</a></li>';
            ?>
        <?php endif; ?>
        </ul>
        </div>
        <?php endif; ?>
    </div>
    <div class="clear"></div>
</div>
