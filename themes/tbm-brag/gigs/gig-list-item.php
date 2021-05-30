<?php if ('whatslively' == strtolower($gig->imported_from)) :
    $show_whatslively_credit = true;
endif;
?>
<div class="row py-3 gig-item">
    <div class="col-3 text-center gig-date">
        <div class="text-uppercase"><?php echo date('D', strtotime($gig->datetime)); ?></div>
        <div class="h2"><?php echo date('d', strtotime($gig->datetime)); ?></div>
        <span class="gig-date-month-year"><?php echo date('M Y', strtotime($gig->datetime)); ?></span>
    </div>

    <div class="col-9 col-sm-6">
        <div class="gig-title">
            <h3 class="h5">
                <a href="<?php echo get_the_permalink($gig->ID); ?>">
                    <?php echo get_the_title($gig->ID); ?>
                </a>
            </h3>
        </div>

        <div class="gig-artist">
            <?php
            $artists = get_the_terms($gig->ID, 'gig-artist');
            $artist_names = array();
            if ($artists && count($artists) > 0 && $artists[0]) :
                foreach ($artists as $artist) :
                    array_push($artist_names, $artist->name);
                endforeach;
            endif;
            echo implode(', ', $artist_names);
            $supports = get_the_terms($gig->ID, 'gig-support');
            $support_names = array();
            if ($supports && count($supports) > 0 && $supports[0]) :
                echo ' w/ ';
                foreach ($supports as $support) :
                    array_push($support_names, $support->name);
                endforeach;
                echo implode(', ', $support_names);
            endif;
            ?>
        </div>

        <div itemprop="location" class="gig-location">
            <?php
            $venue_name = $venue_suburb = '';
            //            while ( $venues->have_posts() ) : $venues->the_post();
            ?>

            <a href="<?php the_permalink($gig->venue_id); ?>"><?php echo $venue_name = get_the_title($gig->venue_id); ?></a>
            <br>
            <?php if (get_field('suburb', $gig->venue_id)) {
                echo $venue_suburb = get_field('suburb', $gig->venue_id);
            } ?>
            <?php // endwhile; 
            ?>
        </div>
    </div>

    <div class="col-12 col-sm-3 mt-3">
        <div class="heading-social text-center text-uppercase">Share</div>
        <div class="gig-social nav d-flex align-items-center">

            <?php
            // Get current page URL
            $gig_url = urlencode(get_the_permalink($gig->ID));

            // Get current page title
            $gig_title = str_replace(' ', '%20', get_the_title($gig->ID));

            // Construct sharing URL without using any script
            $facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . $gig_url;
            $twitterURL = 'https://twitter.com/intent/tweet?text=' . $gig_title . '&amp;url=' . $gig_url;

            $email_body = $gig_title . ' (' . $gig_url . ')';
            $email_body .= "%0D%0AArtist: " . implode(', ', $artist_names);
            if (count($support_names) > 0) :
                $email_body .= ' w/ ' . implode(', ', $support_names);
            endif;
            $email_body .= "%0D%0ADate: " . date('d M, Y (D)', strtotime($gig->datetime));
            if (isset($venue_name) && $venue_name != '') :
                $email_body .= "%0D%0AVenue: " . $venue_name . ' ';
            endif;
            if (isset($venue_suburb) && $venue_suburb != '') :
                $email_body .= "%0D%0ALocation: " . $venue_suburb . ' ';
            endif;
            ?>
            <a href="<?php echo $facebookURL; ?>" class="nav-link facebook social-share-link text-white flex-fill text-center"><span class="fab fa-facebook"></span></a>
            <a href="<?php echo $twitterURL; ?>" class="nav-link twitter social-share-link text-white flex-fill text-center"><span class="fab fa-twitter"></span></a>
            <a href="mailto:?subject=<?php echo $gig_title; ?>&body=<?php echo $email_body; ?>" class="nav-link email text-white flex-fill text-center">@</a>
        </div>
        <?php if (get_field('ticket_link_url_1', $gig->ID)) :
            $ticket_url = get_field('ticket_link_url_1', $gig->ID);
            if (filter_var($ticket_url, FILTER_VALIDATE_EMAIL)) {
                $ticket_url = 'mailto:' . $ticket_url;
            } else {
                $ticket_url = addhttp(trim($ticket_url));
            }
        ?>
            <a href="<?php echo $ticket_url; ?>" target="_blank" rel="nofollow noopener noreferrer" title="Get Tickets" class="mt-2 p-1 text-center ticket">Get Tickets</a>
        <?php endif; ?>
    </div>
</div>