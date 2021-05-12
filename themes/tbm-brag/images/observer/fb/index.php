<!doctype html>
<html lang="en">
  <head>
    <style>
    @font-face{font-family:'FontAwesome';src:url('https://thebrag.com/wp-content/themes/tbm-brag/font-awesome/fonts/fontawesome-webfont.eot?v=4.7.0');src:url('https://thebrag.com/wp-content/themes/tbm-brag/font-awesome/fonts/fontawesome-webfont.eot?#iefix&v=4.7.0') format('embedded-opentype'),url('https://thebrag.com/wp-content/themes/tbm-brag/font-awesome/fonts/fontawesome-webfont.woff2?v=4.7.0') format('woff2'),url('https://thebrag.com/wp-content/themes/tbm-brag/font-awesome/fonts/fontawesome-webfont.woff?v=4.7.0') format('woff'),url('https://thebrag.com/wp-content/themes/tbm-brag/font-awesome/fonts/fontawesome-webfont.ttf?v=4.7.0') format('truetype'),url('https://thebrag.com/wp-content/themes/tbm-brag/font-awesome/fonts/fontawesome-webfont.svg?v=4.7.0#fontawesomeregular') format('svg')}.fa{display:inline-block;font:normal normal normal 14px/1 FontAwesome;font-size:inherit;text-rendering:auto}

    .fa-facebook:before{content:"\f09a"}
    .fa-twitter:before{content:"\f099"}
    </style>
  </head>
<body>
<?php

define( 'WP_USE_THEMES', false ); // Don't load theme support functionality
require_once( '../../../../../../wp-load.php' );

$topic_title = isset( $_GET['topic_title'] ) ? urldecode( trim( $_GET['topic_title'] ) ) : NULL;
$topic = isset( $_GET['topic'] ) ? absint( $_GET['topic'] ) : NULL;
if ( ! is_null( $topic_title ) ) {
  $filename = str_replace( ' ', '-', $topic_title ) . '.jpg';
  $link = isset( $_GET['link'] ) ? urldecode( trim( $_GET['link'] ) ) : NULL;
  $width = 400;
  $height = 300;
  if ( 0 && file_exists( $filename ) ) {
?>
  <div style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px; margin: auto; max-width: 100%;">
    <a href="<?php echo $link; ?>" target="_blank" style="display: block;">
      <img src="https://thebrag.com/wp-content/themes/tbm-brag/images/observer/fb/<?php echo $filename; ?>" style="width: <?php echo $width; ?>px; max-width: 100%;">
    </a>
  </div>
<?php
  } else {
  ?>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <style>
  .observer-sub-form {
    padding: .25rem;
    /* background: #dc3545; */
    border-radius: 10px;
    max-width: none;
  }
  .observer-sub-form .observer-sub-email {
    background: #fff;
    border-radius: .25rem;
    padding: 25px 15px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
  }
  .observer-sub-form input[type=email] {
    width: 100%;
    font-size: 16px;
    line-height: 1;
    color: #000;
    border: none;
  }
  .observer-sub-form input[type=submit] {
    padding: 5px 10px;
    font-weight: 300;
    /* background-color: #dc3545; */
    color: #fff;
    border: none;

  }
  .observer-sub-form .submit-wrap,
  .observer-sub-form .submit-wrap input[type=submit] {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
  }
  .observer-sub-form .submit-wrap {
    border: 1px solid #fff;
  }
  .observer-sub-form .spinner {
    width: 25px !important;
    height: 25px !important;
    margin: 10px auto !important;
  }

  .spinner{width:40px;height:40px;position:relative;margin:20px auto}
  .double-bounce1,.double-bounce2{width:100%;height:100%;border-radius:50%;background-color:#333;opacity:.6;position:absolute;top:0;left:0;animation:sk-bounce 2.0s infinite ease-in-out}
.double-bounce2{animation-delay:-1.0s}
@keyframes sk-bounce{0%,100%{transform:scale(0.0)}
50%{transform:scale(1.0)}
}
  .observer-sub-form .spinner .double-bounce1, .observer-sub-form .spinner .double-bounce2 {
    background-color: #fff;
  }
  </style>
  <form action="#" method="post" id="observer-subscribe-form<?php echo uniqid(); ?>" name="observer-subscribe-form" class="observer-subscribe-form">
    <div class="observer-sub-form bg-success d-flex flex-column justify-content-center p-3" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px; margin: auto; max-width: 100%;">
      <h4 class="text-white">Love <?php echo $topic_title; ?>?</h4>
      <p class="text-white">Get the latest <?php echo $topic_title; ?> news, features, updates and giveaways straight to your inbox</p>
      <div class="d-flex" style="box-shadow: 0 0 0 1px rgba(0,0,0,.15), 0 2px 3px rgba(0,0,0,.2);">
        <input type="hidden" name="list" value="<?php echo $topic; ?>">
        <input type="email" name="email" class="form-control observer-sub-email" placeholder="Your email" value="">
        <div class="d-flex submit-wrap rounded">
          <input type="submit" value="Join" name="subscribe" class="button btn btn-dark rounded">
          <div class="loading mx-3" style="display: none;"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>
        </div>
      </div>
      <div class="alert alert-info d-none js-msg-subscribe mt-2"></div>
      <div class="alert alert-danger d-none js-errors-subscribe mt-2"></div>
    </div>
  </form>

  <script>
  jQuery(document).ready(function($) {
    if ( $('.observer-subscribe-form').length ) {
      $(document).on('submit', '.observer-subscribe-form', function(e) {
        e.preventDefault();
        var theForm = $(this);

        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        if ( theForm.find('input[name="email"]').length &&
        (
          theForm.find('input[name="email"]').val() == '' ||
          ! re.test( String( theForm.find('input[name="email"]').val().toLowerCase() ) )
        ) ) {
          theForm.find('.js-errors-subscribe').html( 'Please enter a valid email address.' ).removeClass('d-none');
          return false;
        }

        var formData = $(this).serialize();
        var loadingElem = $(this).find('.loading');
        var button = $(this).find('.button');

        var the_url = 'FB Instant Article';
        formData += '&source=' + the_url;

        $('.js-errors-subscribe,.js-msg-subscribe').html('').addClass('d-none');
        loadingElem.show();
        button.hide();
        var data = {
          action: 'subscribe_observer_category',
          formData: formData
        };
        $.post( '<?php echo admin_url( 'admin-ajax.php' ) ; ?>', data, function(res) {
          if( res.success ) {
            theForm.find('.js-msg-subscribe').html(res.data.message).removeClass('d-none');
            // window.location.reload();
          } else {
            theForm.find('.js-errors-subscribe').html(res.data.error.message).removeClass('d-none');
          }
          loadingElem.hide();
          button.show();
        }).error(function(e){
          theForm.find('.js-errors-subscribe').html(e).removeClass('d-none');
          loadingElem.hide();
          button.show();
        });
      });
    }
  });
  </script>
  <?php
  }
}
?>
</body>
</html>
