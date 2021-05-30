<?php /* Template name: Gigs */ ?>
<?php
date_default_timezone_set('Australia/Sydney');

$city = isset($wp_query->query_vars['gig_city']) ? $wp_query->query_vars['gig_city'] : '';
$date = isset($wp_query->query_vars['gig_date']) ? $wp_query->query_vars['gig_date'] : date('Y-M-d');

$query_title = isset($_GET['title']) ? $_GET['title'] : '';
$query_artist = isset($_GET['artist']) ? $_GET['artist'] : '';
$query_date = isset($_GET['date']) ? $_GET['date'] : '';
$query_city = isset($_GET['city']) ? $_GET['city'] : '';


if ('' != $query_date) :

  $q_d = DateTime::createFromFormat('Y-m-d', $query_date);
  if ($q_d && $q_d->format('Y-m-d') === $query_date) {
    $query_date = $q_d->format('Y-M-d');
  } else {
    $query_date = date('Y-M-d');
  }

  header("HTTP/1.1 301 Moved Permanently");
  header('Location: /gigs/' . $query_date);
  exit();
endif;

if ('' == $city && '' != $query_city) :
  $city = $query_city;
endif;

$query_date = isset($_GET['gig_date']) ? $_GET['gig_date'] : '';

$city_state_map = array(
  'sydney' => 'NSW',
  'melbourne' => 'VIC',
  'brisbane' => 'QLD',
  'perth' => 'WA',
  'adelaide' => 'SA',
  'canberra' => 'ACT',
  'darwin' => 'NT',
  'hobart' => 'TAS',
);

$d = DateTime::createFromFormat('Y-M-d', $date);
if ($d && $d->format('Y-M-d') === $date) {
  $date = $d->format('Y-m-d');
} else {
  $date = date('Y-m-d');
}
$querystring = http_build_query($_GET);

$prev_date = date('Y-M-d', strtotime('-1 days', strtotime($date)));
$next_date = date('Y-M-d', strtotime('+1 days', strtotime($date)));

get_header();

wp_enqueue_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), 1.1, true);
wp_enqueue_script('jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array('jquery'), 1.1, true);
wp_enqueue_style('jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css');

if (!$city) :
  $city = 'sydney';
endif;
?>
<div class="container">
  <div class="row py-4 my-4" style="border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
    <div class="col-12">
      <ul class="nav nav-pills justify-content-center nav-wrap">
        <?php foreach ($city_state_map as $the_city => $state) : ?>
          <li class="nav-item">
            <a class="p-1 btn nav-link<?php echo $city == $the_city ? ' active btn-dark' : ''; ?>" href="/gigs/<?php echo $the_city; ?>/"><?php echo strtoupper($the_city); ?></a>
          </li>
        <?php endforeach; ?>
        <li class="nav-item d-none d-md-block ml-2">
          <a href="/submit-gig" class="btn btn-primary btn-sm nav-link">SUBMIT A GIG</a>
        </li>
      </ul>
    </div>
  </div>
</div>
<?php
if (isset($city_state_map[$city])) :
  $query_featured_gigs = "SELECT
    DISTINCT g.ID ID,
    g.post_title gig_title,
    v.ID venue_id,
    v.post_title venue_title,
    gd.gig_datetime datetime,
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
    JOIN {$wpdb->prefix}postmeta pmg
      ON g.ID = pmg.post_id
  WHERE
    g.post_type = 'gig'
    AND v.post_type = 'venue'
    AND DATE(gd.gig_datetime) >= '" . date('Y-m-d') . "'
    AND pmv.meta_key = 'state'
    AND pmv.meta_value = '" . $city_state_map[$city] . "'
    AND pmg.meta_key = 'promoter'
    AND pmg.meta_value = 'Livenation'
    AND g.post_status = 'publish'
    AND v.post_status = 'publish'
  ORDER BY
    datetime, gig_title
  ";
  $featured_gigs = $wpdb->get_results($query_featured_gigs);
  if ($featured_gigs) :
?>
    <div class="container">
      <div class="gigs-featured-heading">
        <img src="<?php echo get_template_directory_uri(); ?>/images/LiveNationBragGigGuide.png">
      </div>
      <div class="gigs-featured">
        <?php foreach ($featured_gigs as $featured_gig) : ?>
          <div class="gig-featured-item-wrap">
            <div class="gig-featured-item">
              <a href="<?php echo get_the_permalink($featured_gig->ID); ?>" class="link"></a>
              <div class="gig-featured-img">
                <?php
                if ('' !== get_the_post_thumbnail($featured_gig->ID)) :
                  $alt_text = get_post_meta(get_post_thumbnail_id($featured_gig->ID), '_wp_attachment_image_alt', true);
                  if ($alt_text == '') {
                    $alt_text = trim(strip_tags(get_the_title($featured_gig->ID)));
                  }
                  echo get_the_post_thumbnail($featured_gig->ID, 'thumbnail', array(
                    'alt' => $alt_text,
                    'title' => $alt_text
                  ));
                else :
                ?>
                  <img src="<?php echo get_template_directory_uri(); ?>/images/concert-300x300.jpg">
                <?php endif; ?>
              </div>
              <div class="gig-featured-date">
                <div class="day"><?php echo date('D', strtotime($featured_gig->datetime)); ?></div>
                <div class="date"><?php echo date('d', strtotime($featured_gig->datetime)); ?></div>
                <div class="month"><?php echo date('M', strtotime($featured_gig->datetime)); ?></div>
              </div>
              <div class="gig-details">
                <h2 class="gig-title">
                  <a href="<?php echo get_the_permalink($featured_gig->ID); ?>"><?php echo get_the_title($featured_gig->ID); ?></a>
                </h2>
                <div class="venue-title"><?php echo $featured_gig->venue_title; ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; // If there are Featured gigs 
  ?>
<?php endif; // If $city_state_map[$city] is set 
?>

<div class="container">
  <div class="row mb-2">
    <div class="col-12">
      <div class="text-center d-flex d-md-none justify-content-between">
        <button class="btn btn-dark btn-toggle-gig-search">Search for Gigs</button>
        <a href="/submit-gig" class="btn-primary nav-link">SUBMIT A GIG</a>
      </div>
      <div class="d-none d-md-block gig-search-form">
        <?php include(get_template_directory() . '/gig-search-form.php'); ?>
      </div>
    </div>
  </div>

  <div class="row align-items-stretch">
    <div class="col-md-8">
      <?php
      if (isset($_GET['search'])) :
        include(get_template_directory() . '/gigs/gig-search.php');
      else :
      ?>
        <div class="row">
          <div class="col-12">
            <div class="row d-flex my-3">
              <div class="col-3 text-left align-self-center pr-0"><a href="/gigs/<?php echo $city; ?>/<?php echo $prev_date; ?>" class="btn btn-primary"><?php echo date('j M', strtotime($prev_date)); ?></a></div>
              <h1 class="col-6 text-center align-self-center px-0"><?php echo date('j M, Y', strtotime($date)); ?></h1>
              <div class="col-3 text-right align-self-center pl-0"><a href="/gigs/<?php echo $city; ?>/<?php echo $next_date; ?>" class="btn btn-primary"><?php echo date('j M', strtotime($next_date)); ?></a></div>
            </div>
          </div>
        </div>
        <?php
        if (isset($city_state_map[$city])) :
          $query = "SELECT
    g.ID ID,
    g.post_title gig_title,
    v.ID venue_id,
    v.post_title venue_title,
    gd.gig_datetime datetime,
    gd.imported_from
  FROM
    {$wpdb->prefix}posts g
      JOIN {$wpdb->prefix}p2p p2p
        ON g.ID = p2p.p2p_from
      JOIN {$wpdb->prefix}posts v
        ON v.ID = p2p.p2p_to
      JOIN {$wpdb->prefix}gig_details gd
        ON g.ID = gd.post_id
      JOIN {$wpdb->prefix}postmeta pm
        ON v.ID = pm.post_id
  WHERE
    g.post_type = 'gig'
    AND v.post_type = 'venue'
    AND DATE(gd.gig_datetime) = '" . date('Y-m-d', strtotime($date)) . "'
    AND pm.meta_key = 'state'
    AND pm.meta_value = '" . $city_state_map[$city] . "'
    AND g.post_status = 'publish'
    AND v.post_status = 'publish'
  ORDER BY
    g.post_title
  ";

          $gigs = $wpdb->get_results($query);
          if (count($gigs) > 0) :
        ?>
            <div class="row">
              <div class="col-12">
                <?php
                foreach ($gigs as $gig) :
                  include(get_template_directory() . '/gigs/gig-list-item.php');
                endforeach; // For Each Gigs
                ?>
              </div>
            </div>
        <?php
          else :
            echo '<p style="text-align: center;">We couldn\'t find any gigs on that date. Please use search form to find more gigs.</p>';
          endif; // If array $gigs is NOT empty
        endif; // If $city_state_map[$city] is set
        ?>
      <?php endif; ?>

      <?php if (isset($show_whatslively_credit) && $show_whatslively_credit) : ?>
        <a href="http://www.whatslively.com/" target="_blank"><img src="<?php echo get_template_directory_uri() . '/images/WL-Footer-300px.jpg'; ?>" width="300" style="display: inline-block; margin-top: 30px;"></a>
      <?php endif; ?>
    </div>
    <div class="col-md-4">
      <div id="gigs-sidebar">
        <?php get_fuse_tag('mrec_2'); ?>
        <?php // get_fuse_tag( 'mrec_2' ); 
        ?>
      </div>
    </div>
  </div>

</div>


<?php get_footer();
