<div class="single_story">
    <div class="post-content">
        <?php
            if ( get_field('review') ) :
                echo '<br>  <h2>Review</h2>';
                echo get_field('review');
            endif;
        ?>
        <p>
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
        </p>
        <p>
            <?php if ( get_field('phone') ) : ?>
                <strong>Phone:</strong> <?php echo get_field('phone'); ?>
                <br>
            <?php endif; ?>

            <?php if (get_field('link_url')) : ?>
                <strong>External URL:</strong>
                <?php
                    $link_url = get_field('link_url');
                    $link_title = get_field('link_title') ? get_field('link_title') : get_field('link_url');
                    if (!preg_match("~^(?:f|ht)tps?://~i", $link_url)) {
                        $link_url = "http://" . $link_url;
                    }
                    echo '<a href="' . $link_url . '" target="_blank">' . $link_title . '</a>';
                ?>
                <br>
            <?php endif; ?>
            <?php if (get_field('opening_hours')) : ?>
            <strong>Opening Hours:</strong><br><?php echo get_field('opening_hours'); ?>
            <?php endif; ?>
        </p>
        <div class="clear"></div>

        <?php
        global $wpdb;
        $query = "
            SELECT
                gigs.ID, gigs.post_title, gd.gig_datetime AS datetime
            FROM {$wpdb->prefix}posts gigs
                INNER JOIN {$wpdb->prefix}p2p p2p
                    ON gigs.ID = p2p.p2p_from
                INNER JOIN {$wpdb->prefix}gig_details gd
                    ON gd.post_ID = gigs.ID
            WHERE
                p2p_to = " . get_the_ID() . " AND p2p_type = 'gig_to_venue'
                AND
                DATE(gd.gig_datetime) >= '" . date('Y-m-d') . "'
                AND
                DATE(gd.gig_datetime) <= '" . date('Y-m-d', strtotime('12 months')) . "'
                AND
                gigs.post_status = 'publish'
            ORDER BY
                gd.gig_datetime
            ";
            $gigs = $wpdb->get_results($query);
            if ( count( $gigs ) > 0 ):
        ?>
        <table class="table-gig-listing" cellpadding="4">
            <tr>
                <th>Upcoming Gigs</th>
                <th>Date and Time</th>
            </tr>
            <?php $i = 0; foreach ( $gigs as $gig ): ?>
            <tr>
                <td>
                    <a href="<?php echo get_the_permalink( $gig->ID ); ?>">
                        <?php echo get_the_title( $gig->ID ); ?>
                    </a>
                </td>
                <td>
                    <?php echo date('j\<\s\u\p\>S\<\/\s\u\p\> \o\f F Y, h:ia', strtotime($gig->datetime)); ?>
                </td>
            </tr>
            <?php if ( 4 == $i ) : ?>
            <tr>
                <td colspan="2">
                    <div style="text-align: center">
                        <!-- <amp-ad width=300 height=250
                                type="doubleclick"
                            data-slot="/71161633/SSM_thebrag/tb_venue_hrec_1">
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
                </td>
            </tr>
            <?php endif; ?>
            <?php $i++; endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="clear"></div>
</div>
