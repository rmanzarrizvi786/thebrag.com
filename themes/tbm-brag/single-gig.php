<?php get_header(); ?>

<?php
$post = $wp_query->post;
?>
<style>
.table tr td,
.table tr th {
    padding: .5rem;
}
</style>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <?php
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
            ?>
                    <div class="story">
                        <?php if ('' !== get_the_post_thumbnail()) : ?>
                            <div class="mb-2">
                                <?php
                                $alt_text = get_post_meta(get_post_thumbnail_id(get_the_ID()), '_wp_attachment_image_alt', true);
                                if ($alt_text == '') {
                                    $alt_text = trim(strip_tags(get_the_title()));
                                }
                                the_post_thumbnail('full', array(
                                    'alt' => $alt_text,
                                    'title' => $alt_text
                                ));
                                ?>
                            </div><!-- .post-thumbnail -->
                        <?php endif; ?>
                        <div class="post-content">
                            <h1 id="story_title"><?php the_title(); ?></h1>

                            <table class="table table-borderless">
                                <?php
                                $query_datetime_furutre = "SELECT * FROM {$wpdb->prefix}gig_details
                        WHERE
                        post_id = '" . get_the_ID() . "'
                        AND
                        DATE(gig_datetime) >= '" . date('Y-m-d') . "'";
                                $datetimes = $wpdb->get_results($query_datetime_furutre);
                                if ($datetimes) :
                                ?>
                                    <tr>
                                        <th class="text-right">When</th>
                                        <td>
                                            <?php if (count($datetimes) == 1) :
                                                if ('whatslively' != strtolower($datetimes[0]->imported_from)) :
                                                    echo date('D d M Y @ h:ia', strtotime($datetimes[0]->gig_datetime));
                                                    $show_whatslively_credit = true;
                                                else :
                                                    echo date('D d M Y', strtotime($datetimes[0]->gig_datetime));
                                                endif;
                                            else : ?>
                                                <ul>
                                                    <?php foreach ($datetimes as $datetime) : ?>
                                                        <li>
                                                            <?php if ('whatslively' != strtolower($datetime->imported_from)) :
                                                                $show_whatslively_credit = true;
                                                                echo date('d M Y @ h:ia', strtotime($datetime->gig_datetime));
                                                            else :
                                                                echo date('d M Y', strtotime($datetime->gig_datetime));
                                                            endif;
                                                            ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php
                                endif; // If there are datetimes

                                $venue = array();
                                $venues = new WP_Query(array(
                                    'connected_type' => 'gig_to_venue',
                                    'connected_items' => get_queried_object(),
                                    'nopaging' => true,
                                ));
                                if ($venues->have_posts()) :
                                ?>
                                    <tr>
                                        <th class="text-right">Where</th>
                                        <?php
                                        while ($venues->have_posts()) :
                                            $venues->the_post();
                                            $venue['title'] = get_the_title();
                                        ?>
                                            <td>
                                                <h4 style="margin: 0;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                                <?php $address = get_field('street') . ' ' . get_field('suburb') . ' ' . get_field('state') . ' ' . get_field('coutry'); ?>
                                                <a href="http://maps.google.com/?q=<?php echo urlencode($address); ?>" target="_blank">
                                                    <?php
                                                    if (get_field('street')) {
                                                        echo get_field('street');
                                                        $venue['street'] = get_field('street');
                                                        if (get_field('additional')) {
                                                            echo ' ' . get_field('additional');
                                                            $venue['street'] .= get_field('additional');
                                                        }
                                                        echo '<br>';
                                                    }
                                                    if (get_field('suburb')) {
                                                        echo get_field('suburb') . ', ';
                                                        $venue['suburb'] = get_field('suburb');
                                                    }
                                                    if (get_field('state')) {
                                                        echo get_field('state') . ' ';
                                                        $venue['state'] = get_field('state');
                                                    }
                                                    if (get_field('postcode')) {
                                                        echo get_field('postcode');
                                                        $venue['postcode'] = get_field('postcode');
                                                    }
                                                    ?>
                                                </a>
                                            </td>
                                        <?php endwhile; // Venues 
                                        ?>
                                    </tr>
                                <?php
                                    wp_reset_postdata();
                                endif; // If Venues

                                $artists = get_the_terms($post->ID, 'gig-artist');
                                $artist_names = array();
                                if ($artists && count($artists) > 0 && $artists[0]) :
                                ?>
                                    <tr>
                                        <th class="text-right">Artists</th>
                                        <td>
                                        <?php
                                        foreach ($artists as $artist) :
                                            echo $artist->name . '<br />';
                                            array_push($artist_names, $artist->name);
                                        endforeach;
                                    endif;

                                    $supports = get_the_terms($post->ID, 'gig-support');
                                    if ($supports && count($supports) > 0 && $supports[0]) :
                                        echo '<div>w/ ';
                                        $support_names = array();
                                        foreach ($supports as $support) :
                                            array_push($support_names, $support->name);
                                        endforeach;
                                        echo implode(', ', $support_names);
                                        echo '</div>';
                                    endif;
                                        ?>
                                        </td>
                                    </tr>
                                    <?php if (get_field('price')) : ?>
                                        <tr>
                                            <th class="text-right">Price</th>
                                            <td><?php
                                                $price = get_field('price');
                                                // echo preg_match('~[0-9]+~', $price) ? '$' : '';
                                                echo strpos($price, '$') === FALSE && preg_match('~[0-9]+~', $price) ? '$' . str_replace('$', '', $price) : $price;
                                                ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th class="text-right">Ticket Information</th>
                                        <td>
                                            <?php


                                            if (get_field('ticket_info')) :
                                                echo get_field('ticket_info');
                                            endif;

                                            ?>
                                            <div>
                                                <?php
                                                if (get_field('ticket_link_url_1')) :
                                                    $link_url = get_field('ticket_link_url_1');
                                                    if (filter_var($link_url, FILTER_VALIDATE_EMAIL)) {
                                                        $ticket_url = 'mailto:' . $link_url;
                                                    } else {
                                                        $ticket_url = addhttp(trim($link_url));
                                                    }
                                                    //                                    $link_title = get_field('ticket_link_title_1') ? get_field('ticket_link_title_1') : get_field('ticket_link_url_1');
                                                    echo '<a href="' . $ticket_url . '" target="_blank" rel="nofollow noopener noreferrer" class="btn btn-dark">Get Tickets</a>';
                                                endif;
                                                ?>
                                            </div>
                                        </td>
                                    </tr>
                            </table>
                            <?php the_content(); ?>
                        </div>
                        <?php // do_action('ssm_social_sharing_buttons', 'row'); 
                        ?>

                        <?php if (isset($show_whatslively_credit) && $show_whatslively_credit) : ?>
                            <a href="http://www.whatslively.com/" target="_blank"><img src="<?php echo get_template_directory_uri() . '/images/WL-Footer-300px.jpg'; ?>" width="300" style="display: inline-block; margin-top: 30px;"></a>
                        <?php endif; ?>

                    </div>
                    <!-- Story End -->
            <?php
                endwhile;

            endif;
            ?>
        </div>

        <div class="col-md-4 p-0" style="min-width: 320px;">
            <div class="gig-search-wrap px-3 py-1 mb-3 bg-dark">
                <h2 class="text-center text-uppercase mt-3 text-white">Gig Search</h2>
                <?php $in_sidebar = true;
                include(get_template_directory() . '/gig-search-form.php'); ?>
            </div>
            <?php get_fuse_tag('mrec_1'); ?>
            <?php get_fuse_tag('mrec_2'); ?>
        </div>
    </div>
</div>

<?php if (isset($datetime->gig_datetime)) : ?>
    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "Event",
            "name": "<?php the_title(); ?>",
            "startDate": "<?php echo date('Y-m-d', strtotime($datetime->gig_datetime)); ?>",
            "endDate": "<?php echo date('Y-m-d', strtotime($datetime->gig_datetime)); ?>",
            "location": {
                "@type": "Place",
                "name": "<?php echo isset($venue['title']) ? $venue['title'] : ''; ?>",
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "<?php echo isset($venue['street']) ? $venue['street'] : ''; ?>",
                    "addressLocality": "<?php echo isset($venue['suburb']) ? $venue['suburb'] : ''; ?>",
                    "postalCode": "<?php echo isset($venue['postcode']) ? $venue['postcode'] : ''; ?>",
                    "addressRegion": "NSW",
                    "addressCountry": "Australia"
                }
            },
            "image": "<?php echo get_template_directory_uri(); ?>/images/concert-300x300.jpg",
            "description": "<?php echo get_the_content(); ?>",
            "offers": {
                "@type": "Offer",
                "url": "<?php echo isset($ticket_url) ? $ticket_url : '#'; ?>"
            },
            <?php if (count($artist_names) > 1) : ?> "performer": [
                    <?php foreach ($artist_names as $i => $artist_name) : ?> {
                            "@type": "PerformingGroup",
                            "name": "<?php echo $artist_name; ?>"
                        }
                        <?php echo ($i < count($artist_names) - 1) ? ',' : ''; ?>
                    <?php endforeach; ?>
                ]
            <?php else : ?> "performer": {
                    "@type": "PerformingGroup",
                    "name": "<?php echo implode('', $artist_names); ?>"
                }
            <?php endif; ?>
        }
    </script>
<?php endif; ?>

<?php get_footer();
