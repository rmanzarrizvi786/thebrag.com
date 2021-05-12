<?php /* Template name: Competitions (Observer) */

get_template_part( 'partials/header-wide' );
// get_template_part( 'page-templates/brag-observer/header' );

$urls = [
  'https://thebrag.com/wp-json/brag_observer/v1/competitions/',
  'https://tonedeaf.thebrag.com/wp-json/api/v1/observer/competitions/',
  'https://dontboreus.thebrag.com/wp-json/api/v1/observer/competitions/',
];

$all_competitions = [];
foreach( $urls as $url ) {
  $response = wp_remote_get( $url, [ 'sslverify' => ! in_array( $_SERVER['REMOTE_ADDR'], [ '127.0.0.1', '::1'] ) ] );

  $responseBody = wp_remote_retrieve_body( $response );

  if( $responseBody ) {
    $all_competitions = array_merge( $all_competitions, json_decode( $responseBody ) );
  }
}

$new_competitions = $all_competitions;
$lead_generator_ids = array_unique( wp_list_pluck( $new_competitions, 'lead_generator' ) );

$my_entries_ids = [];
$my_competitions = [];
$exclude_competitions = [];
if ( is_user_logged_in() ) {
  $current_user = wp_get_current_user();
  $user_id = $current_user->ID;

  $my_entries_query = "
    SELECT
      lg.id
    FROM {$wpdb->prefix}observer_lead_generators lg
      JOIN {$wpdb->prefix}observer_lead_generator_responses lr
        ON lg.id = lr.lead_generator_id
    WHERE
      lr.user_id = '{$current_user->ID}'
      AND
      lr.status = 'verified'
  ";

  $my_entries = $wpdb->get_results( $my_entries_query );
  $my_entries_ids = wp_list_pluck( $my_entries, 'id' );

  if ( count( $my_entries_ids ) > 0 && ! get_user_meta( $current_user->ID, 'comp_credits' ) ) {
    update_user_meta(
      $current_user->ID,
      'comp_credits',
      array_fill_keys( $my_entries_ids, '1' )
    );
  }

  $profile_strength = 0;
  $current_user = wp_get_current_user();

  if ( get_user_meta( $current_user->ID, 'profile_strength', true ) ) {
    $profile_strength = get_user_meta( $current_user->ID, 'profile_strength', true );
  } else {
    if( get_user_meta( $current_user->ID, 'first_name', true ) )
      $profile_strength += 20;

    if( get_user_meta( $current_user->ID, 'last_name', true ) )
      $profile_strength += 20;

    if( get_user_meta( $current_user->ID, 'state', true ) )
      $profile_strength += 20;

    if( get_user_meta( $current_user->ID, 'birthday', true ) )
      $profile_strength += 20;

    if( get_user_meta( $current_user->ID, 'gender', true ) )
      $profile_strength += 20;

    update_user_meta( $current_user->ID, 'profile_strength', $profile_strength );
  }

  if ( $profile_strength < 20 ) {
    $profile_complete_class = 'text-danger';
  } else if ( $profile_strength <= 40 ) {
    $profile_complete_class = 'text-warning';
  } else if ( $profile_strength <= 60 ) {
    $profile_complete_class = 'text-info';
  } else if ( $profile_strength <= 80 ) {
    $profile_complete_class = 'text-primary';
  } else if ( $profile_strength <= 100 ) {
    $profile_complete_class = 'text-success';
  }

  if ( $all_competitions ) {
    foreach( $all_competitions as $key => $competition ) {
      if ( in_array( $competition->lead_generator, $my_entries_ids ) ) {
        $my_competitions[] = $competition;
        unset( $new_competitions[$key] );

      }
    }
  }
}
?>

<style>
.post-thumbnail {
    width: 100%;
    padding-top: 56.64%;
    overflow: hidden;
    position: relative;
}
.post-thumbnail img {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%,-50%);
    height: 100%;
    width: auto;
    max-width: none;
}
</style>

<?php if ( $new_competitions ) : ?>

<?php if ( is_user_logged_in() && isset( $profile_strength ) && $profile_strength < 100 ) : ?>
  <div id="update-profile" class="col-12 col-md-6 offset-md-3 mb-5">
  <div class="text-center mx-auto pt-4 mb-3" style="max-width: 100%;">
    <p><strong>Profile Strength: <a href="<?php echo home_url( '/profile/' ); ?>" class="<?php echo $profile_complete_class; ?>"><?php echo $profile_strength; ?>%</a> complete</strong></p>
    <a href="<?php echo home_url( '/profile/' ); ?>">
      <div class="progress profile-strength">
        <?php for( $i = 20; $i <= 100; $i+=20 ) : ?>
        <div class="profile-strength-step<?php echo $i <= $profile_strength ? '-active' : ''; ?>" style="width: 20%; <?php echo $i != 20 ? 'border-left: 2px solid #fff;' : ''; ?>">
          <?php if ( 0 == $profile_strength && $i == 20 ) : ?>
            <div style="position: relative;">
              <div class="profile-strength-start d-flex justify-content-center align-items-center <?php echo $profile_complete_class; ?>">
                <i class="fa fa-check"></i>
              </div>
            </div>
          <?php elseif ( $profile_strength != 100 && $i == $profile_strength ) : ?>
            <div style="position: relative;">
              <div class="profile-strength-complete d-flex justify-content-center align-items-center <?php echo $profile_complete_class; ?>">
                <i class="fa fa-check"></i>
              </div>
            </div>
          <?php elseif ( $i == 100 ) : ?>
            <div style="position: relative;">
              <div class="profile-strength-complete d-flex justify-content-center align-items-center text-primary">
                <i class="fa fa-star"></i>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <?php endfor; ?>
      </div>
    </a>
  </div>
  </div>
<?php endif; // If user is logged in ?>

<h1 class="text-center mb-4">New Competitions</h1>

<div class="container-fluid">
  <div class="row">
    <?php
    foreach ( $new_competitions as $competition ) :
      if ( strtotime( $competition->competition_end_date ) < strtotime( date('Y-m-d') ) )
        continue;

      if ( in_array( $competition->lead_generator, $exclude_competitions ) )
        continue;

      array_push( $exclude_competitions, $competition->lead_generator );
    ?>
    <article class="col-lg-3 col-md-4 col-6  d-flex flex-column align-items-center mb-4">
      <a href="<?php echo $competition->link; ?>" target="_blank" class="d-block w-100 mb-3">
        <div class="post-thumbnail">
          <?php if ( $competition->image && '' != trim( $competition->image ) ) : ?>
            <img src="<?php echo $competition->image; ?>">
          <?php endif; ?>
        </div>
        <div class="post-content align-self-start">
          <h5 class="mt-3 mb-2"><?php echo $competition->title; ?></h5>
        </div>
      </a>
    </article>
    <?php endforeach; // For Each Entry ?>
  </div>
</div>
<?php endif; // If $new_competitions ?>


<?php
if ( is_user_logged_in() ) :
  $comp_credits = get_user_meta( $current_user->ID, 'comp_credits', true );
  if ( $my_competitions ) :
?>

<style>
.btn-social-icon {
  color: #fff;
  height: 35px;
  min-width: 35px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  transition: all .1s ease-out;
  box-shadow: 0 0.063em 0.313em 0 rgba(0,0,0,.07), 0 0.438em 1.063em 0 rgba(0,0,0,.1);
  font-size: 100%;
}
.btn-social-icon:visited {
  color: #fff;
}
.btn-social-icon:hover {
  color: #fff;
  box-shadow: 0 15px 20px -10px rgba(0,0,0,.5);
  transform: translateY(-3px);
}
.btn-social-icon.sms-button {
  background: linear-gradient(90deg,#0f2027,#203a43,#2c5364);
  color: #fff;
}
.btn-social-icon.whatsapp-button {
  background: #25d366;
}
.btn-social-icon.messenger-button {
  background: #0084ff;
}
.btn-social-icon.facebook-button {
  background: #3b5998;
}
.btn-social-icon.twitter-button {
  background: #1da1f2;
}
.btn-social-icon.linkedin-button {
  background: #0077b5;
}
.btn-social-icon.email-button {
  background: #000;
}
</style>

<h1 class="text-center mb-4">Competitions I have entered</h1>

<div class="container-fluid">
  <div class="row">
    <?php
    foreach ( $my_competitions as $competition ) :
      if ( strtotime( $competition->competition_end_date ) < strtotime( date('Y-m-d') ) )
        continue;

      if ( in_array( $competition->lead_generator, $exclude_competitions ) )
        continue;

      array_push( $exclude_competitions, $competition->lead_generator );
    ?>
    <article class="col-lg-3 col-md-4 col-6 d-flex flex-column align-items-center justify-content-between mb-4">
      <a href="<?php echo $competition->link; ?>" target="_blank" class="d-block w-100 mb-3">
        <div class="post-thumbnail">
          <?php if ( $competition->image && '' != trim( $competition->image ) ) : ?>
            <img src="<?php echo $competition->image; ?>">
          <?php endif; ?>
        </div>
        <div class="post-content align-self-start">
          <h5 class="mt-3 mb-2"><?php echo $competition->title; ?></h5>
        </div>
      </a>
      <?php

      /*
      if ( get_user_meta( $current_user->ID, 'comp_code', true ) ) {
        $user_comp_code = get_user_meta( $current_user->ID, 'comp_code', true );
      } else {
        do {
          $user_comp_code = substr( md5(uniqid( $current_user->ID, true) ), 0, 8 );
        } while ( ! check_unique_comp_code( $user_comp_code ) );

        update_user_meta( $current_user->ID, 'comp_code', $user_comp_code );
      }

      $referral_link = $competition->link . '?lc=' . $user_comp_code;
      */

      if ( 0 ) { // Disabled to activate later with changed to Brag Bucks
      ?>
        <div>My credits: <?php echo isset( $comp_credits[ $competition->lead_generator ] ) ? $comp_credits[ $competition->lead_generator ] : 0; ?></div>

        <div class="row mt-4">
          <div class="col">
            <div class="row">
              <div class="col px-1 mb-4 d-flex d-sm-none justify-content-center">
                <a class="btn-social-icon sms-button" id="share_sms" href="sms:?body=<?php echo urlencode( $referral_link . '&utm_source=share_sms' ); ?>"><i class="fas fa-sms" aria-hidden="true"></i></a>
              </div>
              <div class="col px-1 mb-4 d-flex d-sm-none justify-content-center">
                <a class="btn-social-icon whatsapp-button" id="share_whatsapp" href="whatsapp://send?text=<?php echo urlencode( $referral_link . '&utm_source=share_whatsapp' ); ?>"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
              </div>
              <div class="col px-1 mb-4 d-flex d-sm-none justify-content-center">
                <a class="btn-social-icon messenger-button" id="share_messenger" href="fb-messenger://share/?link=<?php echo urlencode( $referral_link . '&utm_source=share_messenger' ); ?>"><i class="fab fa-facebook-messenger" aria-hidden="true"></i></a>
              </div>
              <div class="col px-1 mb-4 d-flex justify-content-center">
                <a class="btn-share btn-social-icon facebook-button" id="share_facebook" href="https://www.facebook.com/sharer.php?u=<?php echo urlencode( $referral_link . '&utm_source=share_facebook' ); ?>"><i class="fa fa-facebook" aria-hidden="true"></i></a>
              </div>
              <div class="col px-1 mb-4 d-flex justify-content-center">
                <a class="btn-share btn-social-icon twitter-button" id="share_twitter" href="https://twitter.com/intent/tweet?text=&url=<?php echo urlencode( $referral_link . '&utm_source=share_twitter' ); ?>"><i class="fa fa-twitter" aria-hidden="true"></i></a>
              </div>
              <div class="col px-1 mb-4 d-flex justify-content-center">
                <a class="btn-share btn-social-icon linkedin-button" id="share_linkedin" href="https://www.linkedin.com/shareArticle/?mini=true&url=<?php echo urlencode( $referral_link . '&utm_source=share_linkedin' ); ?>"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
              </div>
              <div class="col px-1 mb-4 d-flex justify-content-center">
                <a class="btn-share-email btn-social-icon email-button" id="share_email" href="mailto:?subject=&body=<?php echo urlencode( $referral_link . '&utm_source=share_linkedin' ); ?>"><i class="fas fa-envelope" aria-hidden="true"></i></a>
              </div>
            </div>
          </div>
        </div>
        <?php  } ?>
    </article>
    <?php
    endforeach; // For Each Entry
    ?>
  </div>
</div>
<?php
  endif; // If $my_competitions
endif; // If user is logged in ?>

<?php
get_template_part( 'partials/footer-wide' );
// get_template_part( 'page-templates/brag-observer/footer' );
