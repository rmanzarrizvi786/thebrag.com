<?php /* Template name: Refer a friend (Observer) */

if ( is_user_logged_in() ) {
  $current_user = wp_get_current_user();
  $user_id = $current_user->ID;
} else {
  require_once(  ABSPATH . 'sso-sp/simplesaml/lib/_autoload.php');
  $auth = new SimpleSAML_Auth_Simple('default-sp');
  $auth->requireAuth([
    'ReturnTo' => home_url( $wp->request ),
    'KeepPost' => FALSE,
  ]);
  \SimpleSAML\Session::getSessionFromRequest()->cleanup();
  exit;
}

get_template_part( 'page-templates/brag-observer/header' );

if ( get_user_meta( $user_id, 'refer_code', true ) ) {
  $user_refer_code = get_user_meta( $user_id, 'refer_code', true );
} else {
  do {
    $user_refer_code = substr( md5(uniqid( $user_id, true) ), 0, 8 );
  } while ( ! check_unique_refer_code( $user_refer_code ) );

  update_user_meta( $user_id, 'refer_code', $user_refer_code );
}

$lists = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}observer_lists WHERE status = 'active' ORDER BY title ASC" );
$referral_link_base = home_url( '/refer/?rc=' . $user_refer_code );
$referral_link = $referral_link_base . '&l=' . $lists[0]->id;

$invitation_msg = "Hey, I highly recommend giving the " . trim( str_ireplace( [ 'the', 'observer', ], [ '', '', ], $lists[0]->title ) ) . " Observer newsletter a read. It's an awesome regular news email that delivers the biggest news and updates in a way that's informative and entertaining. Best of all, it's free, and only takes 5 minutes to read. Give it a try and subscribe using my personal invite link below:";

/*
$referrals_query = "
  SELECT
    u.ID,
    u.user_email
  FROM
    $wpdb->users u
    JOIN
      $wpdb->usermeta um ON u.ID = um.user_id
  WHERE
    um.meta_key = 'referrer_id' AND um.meta_value = '{$user_id}'
  ORDER BY
    u.ID DESC
";
$all_referrals = $wpdb->get_results( $referrals_query );

$referrals = [];
if ( $all_referrals ) {
  foreach( $all_referrals as $referral ) {
    if ( get_user_meta( $referral->ID, 'is_activated', true ) == '1' ) {
      $referrals[ 'confirmed' ][] = $referral;
    } else {
      $referrals[ 'unconfirmed' ][] = $referral;
    }
  }
}
*/
$invites = $wpdb->get_results( "SELECT DISTINCT(invitee), list_id FROM {$wpdb->prefix}observer_invites WHERE user_id = '{$current_user->ID}' GROUP BY invitee ORDER BY id DESC" );

// $referrals_count = isset( $referrals[ 'confirmed' ] ) ? count( $referrals[ 'confirmed' ] ) : 0;
$referrals_count = get_user_meta( $user_id, 'referrals_count', true ) ? : 0;

$rewards = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}observer_rewards" );

$rewards_steps = wp_list_pluck( $rewards, 'title', 'invites_required' );
$away_count = max( array_keys( $rewards_steps ) );

foreach( array_keys( $rewards_steps ) as $rewards_step ) {
  if ( $rewards_step > $referrals_count ) {
    $away_count = $rewards_step - $referrals_count;
    $prize_title = $rewards_steps[ $rewards_step ];
    break;
  }
}

$unconfirmed_body = "Hi!

You should have received a confirmation email from The Brag Observer (observer@thebrag.media). Confirm your email address to start receiving The Brag Observer! If you can't find it, check your spam and make sure it's in your primary inbox.";
?>

<style>
.dark-pill {
  background: #000;
  color: #fff;
  width: fit-content;
  display: inline-flex;
  align-items: center;
  padding: .5rem .5rem .5rem 1rem;
}
.white-pill {
  background: #fff;
  color: #000;
  min-width: 50px;
  border: none;
  font-weight: 700;
  font-size: 1.5rem;
}
.btn-social-icon {
  color: #fff;
  height: 70px;
  min-width: 70px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  transition: all .1s ease-out;
  box-shadow: 0 0.063em 0.313em 0 rgba(0,0,0,.07), 0 0.438em 1.063em 0 rgba(0,0,0,.1);
  font-size: 200%;
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
.ul-no-indent {
    list-style-type: none;
    padding: 0;
    margin: 0;
}
.card-header, .card i.social-icon {
    font-size: 1.5rem;
}
<?php $fragment_width = '25'; ?>


.show-neighbors {
  overflow: hidden;
}
.show-neighbors .carousel-indicators {
    margin-right: <?php echo $fragment_width; ?>%;
    margin-left: <?php echo $fragment_width; ?>%;
  }

.show-neighbors .carousel-control-prev,
.show-neighbors .carousel-control-next {
  opacity: .9;
    background: #000;
    width: <?php echo $fragment_width - 1; ?>%;
    z-index: 11;  /* .carousel-caption has z-index 10 */
  }
  .show-neighbors .carousel-control-prev {
    background: linear-gradient(90deg, rgba(0,0,0,1) 0%, rgba(255,255,255,0) 100%);
  }
  .show-neighbors .carousel-control-next {
    background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(0,0,0,1) 100%);
  }

.show-neighbors .carousel-inner {
    width: <?php echo ( 100 - 2 * $fragment_width ) * 3; ?>%;
    left: <?php echo 3 * $fragment_width - 100; ?>%;
  }

.show-neighbors .carousel-item-next:not(.carousel-item-left),
.show-neighbors .carousel-item-right.active {
    -webkit-transform: translate3d(33%, 0, 0);
    transform: translate3d(33%, 0, 0);
  }

.show-neighbors .carousel-item-prev:not(.carousel-item-right),
.show-neighbors .carousel-item-left.active {
    -webkit-transform: translate3d(-33%, 0, 0);
    transform: translate3d(-33%, 0, 0);
  }

.show-neighbors .item__third {
    float: left;
    position: relative;  /* captions can now be added */
    width: 33.33333333%;
    padding: 0 0.5%;
  }
</style>

<div class="container">
  <div class="row my-4 pt-4">
    <div class="col d-flex justify-content-between align-items-end">
      <h3 class="m-0">
        Share
        The Brag Observer
        <i class="fas fa-arrow-right" aria-hidden="true"></i>
        Earn Rewards
      </h3>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body d-flex justify-content-center align-items-center p-2 p-md-4">
          <div class="row">
            <div class="col-12">
              <div class="row d-none d-md-flex">
              <?php foreach( $rewards as $reward ) : ?>
                <div class="col-md text-center col-6 my-2 my-md-0">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/observer/refer-a-friend/rewards_0<?php echo $reward->id; ?>.jpg">
                </div>
              <?php endforeach; ?>
              </div>
              <div class="d-block d-md-none">
                <div id="carouselRewardsIndicators" class="carousel slide show-neighbors" data-ride="carousel">
                  <div class="carousel-inner">
                    <?php foreach( $rewards as $key => $reward ) : ?>
                    <div class="carousel-item <?php echo 0 == $key ? 'active' : ''; ?>">
                      <div class="item__third">
                      <img class="d-block" src="<?php echo get_template_directory_uri(); ?>/images/observer/refer-a-friend/rewards_0<?php echo $reward->id; ?>.jpg" alt="<?php echo $reward->title; ?>">
                    </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                  <a class="carousel-control-prev" href="#carouselRewardsIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                  </a>
                  <a class="carousel-control-next" href="#carouselRewardsIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col align-items-center d-block d-md-flex mb-1">
      <div class="dark-pill rounded my-4 mr-3">
        <span>Your Referral Count</span>
        <a href="#share"><div class="white-pill rounded ml-2 d-flex justify-content-center align-items-center"><?php echo $referrals_count; ?></div></a>
      </div>
      <div class="d-inline-flex flex-column">
        <div class="d-block d-md-inline">You're only <strong><?php echo $away_count; ?> referrals</strong> away from receiving</div>
        <div class="d-block d-md-inline">
          <strong><?php echo $prize_title; ?></strong>!
        </div>
      </div>
    </div>
  </div>
</div>

<?php
if ( $lists ) :
?>
<div class="bg-light pb-4">
  <div class="container">
    <div class="row" id="share">
      <div class="col">
        <h3 class="mt-4">Share your link</h3>
        <p class="text-muted m-0">Rack up referrals by sharing your personal referral link with others:</p>
      </div>
    </div>
    <div class="row">
      <div class="col-md mt-3">
        <select name="list_id" id="list_id" class="form-control">
          <?php foreach ( $lists as $list ) : ?>
          <option value="<?php echo $list->id; ?>"><?php echo $list->title; ?></option>
          <?php endforeach; // For Each $list ?>
        </select>
      </div>
      <div class="col-md mt-3">
        <textarea class="form-control" id="share-link" onclick="this.select();" readonly="" rows="1" style="resize: none; word-break: break-all; background-color: #fff;"><?php echo $referral_link; ?></textarea>
      </div>
      <div class="col-auto mt-3">
        <button class="btn btn-dark rounded btn-copy" data-clipboard-action="copy" data-clipboard-target="#share-link">
          <i class="fa fa-copy mr-2"></i>
          <span>Copy Link</span>
        </button>
      </div>
    </div>

    <div class="row" id="share">
      <div class="col">
        <h3 class="mt-4">Share on social</h3>
        <p class="text-muted m-0">Rack up referrals by sharing your personal referral link to your network:</p>
      </div>
    </div>
    <div class="row mt-4">
      <div class="col">
        <div class="row">
          <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-4 d-flex d-sm-none justify-content-center">
            <a class="btn-social-icon sms-button" id="share_sms" href="#"><i class="fas fa-sms" aria-hidden="true"></i></a>
          </div>
          <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-4 d-flex d-sm-none justify-content-center">
            <a class="btn-social-icon whatsapp-button" id="share_whatsapp" href="#"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>
          </div>
          <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-4 d-flex d-sm-none justify-content-center">
            <a class="btn-social-icon messenger-button" id="share_messenger" href="#"><i class="fab fa-facebook-messenger" aria-hidden="true"></i></a>
          </div>
          <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-4 d-flex justify-content-center">
            <a class="btn-share btn-social-icon facebook-button" id="share_facebook" href="#"><i class="fab fa-facebook" aria-hidden="true"></i></a>
          </div>
          <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-4 d-flex justify-content-center">
            <a class="btn-share btn-social-icon twitter-button" id="share_twitter" href="#"><i class="fab fa-twitter" aria-hidden="true"></i></a>
          </div>
          <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-4 d-flex justify-content-center">
            <a class="btn-share btn-social-icon linkedin-button" id="share_linkedin" href="#"><i class="fab fa-linkedin" aria-hidden="true"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div><!-- Share link + Copy -->
<?php
endif; // If $lists
?>

<div class="">
  <form id="refer-send-invite" action="#" accept-charset="UTF-8" method="post">
  <div class="container">
    <div class="row">
      <div class="col">
        <h3 class="mt-4">Share via email</h3>
        <p class="text-muted m-0">Invite people to subscribe to <select name="list" id="list-invite" class="form-control d-inline w-auto">
          <?php foreach ( $lists as $list ) : ?>
          <option value="<?php echo $list->id; ?>" data-invitation-msg="Hey, I highly recommend giving the <?php echo trim( str_ireplace( [ 'the', 'observer', ], [ '', '', ], $list->title ) ); ?> Observer newsletter a read. It's an awesome regular news email that delivers the biggest news and updates in a way that's informative and entertaining. Best of all, it's free, and only takes 5 minutes to read. Give it a try and subscribe using my personal invite link below:"><?php echo $list->title; ?></option>
          <?php endforeach; // For Each $list ?>
        </select> by entering in their emails. (We'll automatically add your referral link!)</p>
      </div>
    </div>
    <div class="row">
      <div class="col">

          <div class="row">
            <div class="col-12">
              <input type="text" name="email" id="email" value="" class="form-control mt-4" placeholder="To: (enter contact's email)" required="required">
              <small class="form-text text-muted">Separate multiple emails with commas.</small>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-12">
              <textarea name="message" id="invitation-message" class="form-control mb-3" rows="5"><?php echo $invitation_msg; ?></textarea>

              <div class="alert alert-info d-none js-msg-invite mb-1"></div>
              <div class="alert alert-danger d-none js-errors-invite mb-1"></div>

              <button name="button" type="submit" class="button btn btn-dark rounded mt-1 mb-4"><i class="fa fa-paper-plane mr-2" aria-hidden="true"></i>
                <span>Send The Invite</span>
              </button>
              <div class="loading" style="display: none;"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>
            </div>
          </div>

      </div>
    </div>
  </div>
  </form>
</div><!-- Share via email -->

<?php
if (
  ( isset( $referrals[ 'confirmed' ] ) && count( $referrals[ 'confirmed' ] ) > 0 ) ||
  ( isset( $referrals[ 'unconfirmed' ] ) && count( $referrals[ 'unconfirmed' ] ) > 0 ) ||
  $invites
) :
?>
<div class="">
  <div class="container">
    <div class="row">
      <div class="col">
        <h3 class="mt-4 mb-0">Your network</h3>
      </div>
    </div>
    <div class="row">
      <?php if ( isset( $referrals[ 'confirmed' ] ) && count( $referrals[ 'confirmed' ] ) > 0 ) : ?>
      <div class="col-lg-4 col-sm-6">
        <div class="card mb-4 mt-3">
          <div class="card-header bg-light text-dark">Confirmed</div>
          <div class="card-body">
            <ul class="ul-no-indent">
              <?php foreach( $referrals[ 'confirmed' ] as $referral ) : ?>
              <li>
                <a href="mailto:<?php echo $referral->user_email; ?>?subject=<?php echo rawurlencode( 'Thanks for joining my network on The Brag Observer!' ); ?>"><?php echo get_user_meta( $referral->ID, 'nickname', true ); ?></a>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div><!-- Confirmed -->
      <?php endif; // If there are confirmed referrals ?>

      <?php if ( isset( $referrals[ 'unconfirmed' ] ) && count( $referrals[ 'unconfirmed' ] ) > 0 ) : ?>
      <div class="col-lg-4 col-sm-6">
        <div class="card mb-4 mt-3">
          <div class="card-header bg-light text-dark">
            Unconfirmed
            <button class="btn btn-light" data-target="#unconfirmedModal" data-toggle="modal">
              <i class="fas fa-info-circle" aria-hidden="true"></i>
            </button>
          </div>
          <div class="card-body">
            <ul class="ul-no-indent">
              <?php foreach( $referrals[ 'unconfirmed' ] as $referral ) : ?>
              <li>
                <a href="mailto:<?php echo $referral->user_email; ?>?body=<?php echo rawurlencode( $unconfirmed_body ); ?>&amp;subject=<?php echo rawurldecode( 'The Brag Observer - Quick reminder to confirm your email' ); ?>"><?php echo $referral->user_email; ?></a>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div><!-- Uncofirmed -->
      <?php endif; // If there are unconfirmed referrals ?>

      <?php
      if ( $invites ) :
$invitation_body_base = "Hi!

You should have received an email invite from The Brag Observer (observer@thebrag.media). From there you can use my personal referral link to start reading The Brag Observer. If you can't find it, check your spam and make sure to place it in your primary inbox.

You can also follow the link below to subscribe:

";
      ?>
      <div class="col-lg-4 col-sm-6">
        <div class="card mb-4 mt-3">
          <div class="card-header bg-light text-dark">
            Email Invite Sent
            <button class="btn btn-light" data-target="#emailInviteModal" data-toggle="modal">
              <i class="fas fa-info-circle" aria-hidden="true"></i>
            </button>
          </div>
          <div class="card-body">
            <ul class="ul-no-indent">
              <?php foreach( $invites as $invite ) :
                $invitation_body = $invitation_body_base . $referral_link . "&email=" . $invite->invitee . "

Cheers
";
                ?>
              <li>
                <a href="mailto:<?php echo $invite->invitee; ?>?body=<?php echo rawurlencode ( $invitation_body ); ?>&amp;subject=<?php echo rawurlencode( 'The Brag Observer'); ?>"><?php echo $invite->invitee; ?></a>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div><!-- Invites sent -->
      <?php endif; // If there are invites ?>
    </div>
  </div>
</div>
<?php
endif; // If $referrals[ 'confirmed' ] || $referrals[ 'unconfirmed' ] || $invites
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>

<script>
var list_share = {
  <?php
  foreach ( $lists as $list ) :
    $list_referrer_link = $referral_link_base . '&l=' . $list->id;
  ?>
  '<?php echo $list->id ?>': {
    'link': '<?php echo $list_referrer_link; ?>',
    'sms': {
      'text': 'I highly recommend giving the <?php echo urlencode( trim( str_ireplace( [ 'the', 'observer', ], [ '', '', ], $list->title ) ) ); ?> Observer newsletter a read. Best of all, it\'s free. Give it a try and subscribe using my personal invite link below:<?php echo urlencode( $list_referrer_link . '&utm_source=share_sms' ); ?>'
    },
    'whatsapp': {
      'text': 'I highly recommend giving the <?php echo urlencode( trim( str_ireplace( [ 'the', 'observer', ], [ '', '', ], $list->title ) ) ); ?> Observer newsletter a read. Best of all, it\'s free. Give it a try and subscribe using my personal invite link below: <?php echo urlencode( $list_referrer_link . '&utm_source=share_whatsapp' ); ?>'
    },
    'messenger': {
      'url': '<?php echo urlencode( $list_referrer_link . '&utm_source=share_messenger' ); ?>'
    },
    'facebook': {
      'url': '<?php echo urlencode( $list_referrer_link . '&utm_source=share_facebook' ); ?>'
    },
    'twitter': {
      'text': 'I highly recommend giving the <?php echo urlencode( trim( str_ireplace( [ 'the', 'observer', ], [ '', '', ], $list->title ) ) ); ?> Observer newsletter a read. Best of all, it\'s free. Give it a try and subscribe using my personal invite link below:',
      'url': '<?php echo urlencode( $list_referrer_link . '&utm_source=share_twitter' ); ?>'
    },
    'linkedin': {
      'title': 'I highly recommend giving the <?php echo urlencode( trim( str_ireplace( [ 'the', 'observer', ], [ '', '', ], $list->title ) ) ); ?> Observer newsletter a read. Best of all, it\'s free. Give it a try and subscribe using my personal invite link below:',
      'url': '<?php echo urlencode( $list_referrer_link . '&utm_source=share_linkedin' ); ?>'
    }
  },
  <?php endforeach; ?>
};

var clipboard = new ClipboardJS('.btn-copy');
clipboard.on('success', function(e) {
  jQuery('.btn-copy').find('span').text('Copied!');
});
clipboard.on('error', function(e) {
  jQuery('.btn-copy').find('span').text('Failed.');
});
jQuery(document).ready(function($){

  setShareProps($('#list_id').val());

  $(document).on('change', '#list_id', function() {
    var list_id = $(this).val();
    setShareProps(list_id);
  });

  function setShareProps(list_id) {
    var list = list_share[list_id];
    $('#share-link').val( list.link );
    $('#share_sms').prop( 'href', 'sms:?body=' + encodeURIComponent( list.sms.text ) );
    $('#share_whatsapp').prop( 'href', 'whatsapp://send?text=' + list.whatsapp.text );
    $('#share_messenger').prop( 'href', 'fb-messenger://share/?link=' + list.messenger.url );
    $('#share_facebook').prop( 'href', 'https://www.facebook.com/sharer.php?u=' + list.facebook.url );
    $('#share_twitter').prop( 'href', 'https://twitter.com/intent/tweet?text=' + list.twitter.text + '&url=' + list.twitter.url );
    $('#share_linkedin').prop( 'href', 'https://www.linkedin.com/shareArticle/?mini=true&url=' + list.linkedin.url + '&title=' + list.linkedin.title );
  }

  $(document).on('change', '#list-invite', function() {
    $('#invitation-message').val( $(this).find(':selected').data('invitation-msg') );
  });

  $(document).on('submit', '#refer-send-invite', function(e) {
    e.preventDefault();
    var theForm = $(this);
    var formData = $(this).serialize();
    var loadingElem = $(this).find('.loading');
    var button = $(this).find('.button');
    $('.js-errors-invite,.js-msg-invite').html('').addClass('d-none');
    loadingElem.show();
    button.hide();
    var data = {
      action: 'send_refer_invite',
      nonce: $('#brag-observer-nonce').val(),
      formData: formData
    };
    $.post( brag_observer.url, data, function(res) {
      if( res.success ) {
        theForm.find('.js-msg-invite').html(res.data).removeClass('d-none');
        theForm.find('#email').val('');
      } else {
        theForm.find('.js-errors-invite').html(res.data).removeClass('d-none');
      }
      loadingElem.hide();
      button.show();
    }).error( function() {
      theForm.find('.js-errors-invite').html( 'Whoops, like something unexpected happened on our side of things. Feel free to give it another shot!' ).removeClass('d-none');
      loadingElem.hide();
      button.show();
    });
  });

  $('.carousel-item', '.show-neighbors').each(function(){
  var next = $(this).next();
  if (! next.length) {
    next = $(this).siblings(':first');
  }
  next.children(':first-child').clone().appendTo($(this));
}).each(function(){
  var prev = $(this).prev();
  if (! prev.length) {
    prev = $(this).siblings(':last');
  }
  prev.children(':nth-last-child(2)').clone().prependTo($(this));
});
});
</script>


<?php

add_action( 'wp_footer', function() {

  global $wpdb;

  $current_user = wp_get_current_user();
  global $invites;
?>

<?php
global $referrals;
if ( isset( $referrals[ 'unconfirmed' ] ) && count( $referrals[ 'unconfirmed' ] ) > 0 ) :
  global $unconfirmed_body;

  $referral = end( $referrals[ 'unconfirmed' ] );
  ?>
  <div aria-labelledby="unconfirmedLabel" class="modal fade" id="unconfirmedModal" role="dialog" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="unconfirmedLabel">FAQ</h5>
          <button aria-label="Close" class="close" data-dismiss="modal" type="button">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <h5>
            <a href="mailto:<?php echo $referral->user_email; ?>?body=<?php echo rawurlencode( $unconfirmed_body ); ?>&amp;subject=<?php echo rawurldecode( 'The Brag Observer - Quick reminder to confirm your email' ); ?>"><?php echo $referral->user_email; ?></a>
            has been unconfirmed for far too long. What should I do?
          </h5>
          <img alt="How I Met Your Mother GIF via Giphy" class="img-fluid rounded" src="https://media.giphy.com/media/LMUOHJCNPvN4I/giphy.gif">
          <p class="mt-2">See above image. Or click on their email to remind them to confirm their email address.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-dark rounded" data-dismiss="modal" type="button">
            <i class="fas fa-thumbs-up mr-2" aria-hidden="true"></i>
            <span>Okay</span>
          </button>
        </div>
      </div>
    </div>
  </div>
<?php endif; // If there are unconfirmed referrals ?>

<?php
if ( $invites ) :

  $invite = $invites[0]; // end( $invites );

  global $invitation_body_base;
  global $user_refer_code;
  $referral_link_base = home_url( '/refer/?rc=' . $user_refer_code );
  $referral_link = $referral_link_base . '&l=' . $invite->list_id;
  $invitation_body = $invitation_body_base . $referral_link . "&email=" . $invite->invitee . "

Cheers
";
?>
<div aria-labelledby="emailInviteLabel" class="modal fade" id="emailInviteModal" role="dialog" tabindex="-1" style="display: none;" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="emailInviteLabel">FAQ</h5>
        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="accordion" id="accordionEmailInvite">
          <div id='headingOneEmailInvite'>
            <div aria-controls='collapseOneEmailInvite' aria-expanded='true' class='d-flex justify-content-between align-items-center my-3 cursor-pointer' data-target='#collapseOneEmailInvite' data-toggle='collapse'>
              <h5 class='m-0'>I sent an invite but it's not showing up. What happened?</h5>
              <i class='fas fa-caret-down text-muted'></i>
            </div>
          </div>
          <div aria-labelledby='headingOneEmailInvite' class='collapse show' data-parent='#accordionEmailInvite' id='collapseOneEmailInvite'>
            <img class='img-fluid rounded w-100' src='https://media.giphy.com/media/WuS5qFGLQKp2M/giphy.gif'>
            <p class='mt-2'>If you sent an invite and it's not showing up on your network, it's possible this person is already subscribed to this specific Brag Observer newsletter. Take it as a compliment. All the cool kids are reading it.
            </p>
            <p class='mt-2'>
              There's another possibility: Maybe you made a typo when entering their email address? Did you try turning it off and on again? ¯\_(ツ)_/¯
            </p>
          </div>
          <hr>

          <div id="headingTwoEmailInvite">
            <div aria-controls="collapseOneEmailInvite" aria-expanded="true" class="d-flex justify-content-between align-items-center my-3 cursor-pointer" data-target="#collapseTwoEmailInvite" data-toggle="collapse">
              <h5 class="m-0">When will I receive credit for my invites?</h5>
              <i class="fas fa-caret-down text-muted" aria-hidden="true"></i>
            </div>
          </div>
          <div aria-labelledby="headingTwoEmailInvite" class="collapse" data-parent="#accordionEmailInvite" id="collapseTwoEmailInvite">
            <p>Once the person you shared the invite with confirms their email address, you'll be one step closer to the reward. You know you want it.</p>
          </div>
          <hr>
          <div id="headingThreeEmailInvite">
            <div aria-controls="collapseOneEmailInvite" aria-expanded="true" class="d-flex justify-content-between align-items-center my-3 cursor-pointer" data-target="#collapseThreeEmailInvite" data-toggle="collapse">
              <h5 class="m-0">
                <a href="mailto:<?php echo $invite->invitee; ?>?body=<?php echo rawurlencode( $invitation_body ); ?>&amp;subject=<?php echo rawurlencode( 'The Brag Observer'); ?>"><?php echo $invite->invitee; ?></a> has had their email invite for way too long. What should I do?
              </h5>
              <i class="fas fa-caret-down text-muted" aria-hidden="true"></i>
            </div>
          </div>
          <div aria-labelledby="headingThreeEmailInvite" class="collapse" data-parent="#accordionEmailInvite" id="collapseThreeEmailInvite">
            <p>Left on read? Not cool. Try clicking their email and we'll help you to encourage them to sign up.</p>
            <em>The Brag Observer is not responsible for your friends blocking your email.</em>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-dark rounded" data-dismiss="modal" type="button">
          <i class="fas fa-thumbs-up mr-2" aria-hidden="true"></i>
          <span>Okay</span>
        </button>
      </div>
    </div>
  </div>
</div>
<?php endif; // If there are invites ?>

<?php
}); // WP_Footer

get_template_part( 'page-templates/brag-observer/footer' );
