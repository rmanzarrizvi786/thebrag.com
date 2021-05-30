<?php get_header(); ?>

<?php
$post = $wp_query->post;
?>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post()
            ?>
                    <!-- Story Start -->
                    <div class="story">
                        <?php if ('' !== get_the_post_thumbnail()) : ?>
                            <div>
                                <?php
                                $alt_text = get_post_meta(get_post_thumbnail_id(get_the_ID()), '_wp_attachment_image_alt', true);
                                if ($alt_text == '') {
                                    $alt_text = trim(strip_tags(get_the_title()));
                                }
                                the_post_thumbnail('full', array(
                                    'alt' => $alt_text,
                                    'title' => $alt_text,
                                    'style' => 'width:100%',
                                ));
                                ?>
                            </div><!-- .post-thumbnail -->
                        <?php endif; ?>
                        <div class="post-content">

                            <h1 id="story_title"><?php the_title(); ?></h1>

                            <?php
                            wpautop(the_content());
                            ?>
                            <?php
                            if (get_field('review')) :
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
                                    if (get_field('street')) {
                                        echo get_field('street');
                                        if (get_field('additional')) {
                                            echo ' ' . get_field('additional');
                                        }
                                        echo '<br>';
                                    }
                                    if (get_field('suburb')) {
                                        echo get_field('suburb') . ', ';
                                    }
                                    if (get_field('state')) {
                                        echo get_field('state') . ' ';
                                    }
                                    if (get_field('postcode')) {
                                        echo get_field('postcode');
                                    }
                                    ?>
                                </a>
                            </p>
                            <p>
                                <?php if (get_field('phone')) : ?>
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

                            <?php
                            $venue_details = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}venue_details WHERE post_id = '" . get_the_ID() . "' LIMIT 1");
                            if (
                                $venue_details &&
                                !is_null($venue_details->lat) &&
                                !is_null($venue_details->lng) &&
                                '-99.99999999' != $venue_details->lat &&
                                '999.99999999' != $venue_details->lng
                            ) :
                            ?>
                                <div id="venue_map" style="height: 400px; width: 100%;"></div>
                                <script>
                                    function initMap() {
                                        var venue = {
                                            lat: <?php echo $venue_details->lat; ?>,
                                            lng: <?php echo $venue_details->lng; ?>
                                        };
                                        var map = new google.maps.Map(document.getElementById('venue_map'), {
                                            zoom: 18,
                                            center: venue
                                        });
                                        var marker = new google.maps.Marker({
                                            position: venue,
                                            map: map
                                        });
                                    }
                                </script>
                                <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDLP0xWZoecgBG9azSgqFrznuLeWlLIL0c&callback=initMap"></script>
                            <?php endif; ?>

                            <div class="my-4">
                                <?php do_action('ssm_social_sharing_buttons', 'row'); ?>
                            </div>

                            <?php
                            $query = "
                SELECT
                    gigs.ID, gigs.post_title, gd.gig_datetime AS datetime, gd.imported_from
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
                            if (count($gigs) > 0) :
                            ?>
                                <table class="table">
                                    <tr>
                                        <th>Upcoming Gigs</th>
                                        <th>When</th>
                                    </tr>
                                    <?php foreach ($gigs as $gig) : ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo get_the_permalink($gig->ID); ?>">
                                                    <?php echo get_the_title($gig->ID); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if ('whatslively' != strtolower($gig->imported_from)) :
                                                    echo date('D j\<\s\u\p\>S<\/\s\u\p\> \o\f\ F, h:ia', strtotime($gig->datetime));
                                                else :
                                                    $show_whatslively_credit = true;
                                                    echo date('D j\<\s\u\p\>S<\/\s\u\p\> \o\f\ F', strtotime($gig->datetime));
                                                endif; ?>
                                                <?php // echo date('j\<\s\u\p\>S\<\/\s\u\p\> \o\f F Y, h:ia', strtotime($gig->datetime)); 
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>

                                <?php if (isset($show_whatslively_credit) && $show_whatslively_credit) : ?>
                                    <a href="http://www.whatslively.com/" target="_blank"><img src="<?php echo get_template_directory_uri() . '/images/WL-Footer-300px.jpg'; ?>" width="300" style="display: inline-block"></a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                    </div>
                    <!-- Story End -->
                    <?php
                    if (!is_numeric(get_the_title())) :
                        $args = array(
                            's' => get_the_title(),
                            'showposts' => 20,
                            'post_status' => 'publish',
                            'sentence' => true,
                            'post_type' => array('post')
                        );
                        $query_posts = new WP_Query($args);
                        if ($query_posts->have_posts()) :
                            $no_of_columns = 2;
                    ?>
                            <div class="row latest mt-5">
                                <?php
                                while ($query_posts->have_posts()) :
                                    $query_posts->the_post();
                                    include(get_template_directory() . '/template-parts/single/tile.php');
                                endwhile;
                                ?>
                            </div>
                    <?php endif;
                    endif; // If there are posts 
                    ?>
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


<?php get_footer();
