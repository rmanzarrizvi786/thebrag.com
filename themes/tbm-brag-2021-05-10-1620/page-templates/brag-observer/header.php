<?php
$page_template = get_page_template_slug();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.png" />

        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="profile" href="http://gmpg.org/xfn/11">

        <meta name="google-site-verification" content="Tf8gbZdF2WOW_R5JIuceGcMuqUNy7TAvdrYKaeoLP5I" />
        <meta name="msvalidate.01" content="E0857D4C8CDAF55341D0839493BA8129" />
        <meta property="fb:app_id" content="1950298011866227" />
        <meta property="fb:pages" content="145692175443937" />

        <meta name="theme-color" content="#130f40">

        <?php if (is_single()) { ?>
        <meta property="og:title" content="<?php the_title(); ?>"/>
        <meta property="og:image" content="<?php $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); if ( has_post_thumbnail() ) { echo $src[0]; } ?>"/>
        <meta property="og:type" content="article"/>
        <meta property="og:site_name" content="Brag Magazine - Everything Sydney"/>
        <meta property="og:url" content="<?php echo get_permalink(); ?>"/>
        <?php } ?>

        <link rel="dns-prefetch" href="https://cdn.onesignal.com/">
        <link rel="dns-prefetch" href="https://www.googletagservices.com/">
        <link rel="dns-prefetch" href="//cdn.publift.com/">
        <link rel="dns-prefetch" href="https://idsync.rlcdn.com/">
        <link rel="dns-prefetch" href="https://x.bidswitch.net/">
        <link rel="dns-prefetch" href="https://ad.yieldmanager.com/">
        <link rel="dns-prefetch" href="https://cm.g.doubleclick.net/">
        <link rel="dns-prefetch" href="https://dpm.demdex.net/">
        <link rel="dns-prefetch" href="https://fw.adsafeprotected.com/">
        <link rel="dns-prefetch" href="https://www.googletagmanager.com">
        <link rel="dns-prefetch" href="https://tpc.googlesyndication.com">
        <link rel="dns-prefetch" href="https://adservice.google.com">
        <link rel="dns-prefetch" href="https://s.ytimg.com">
        <link rel="dns-prefetch" href="https://adservice.google.com.au">
        <link rel="dns-prefetch" href="https://pagead2.googlesyndication.com">
        <link rel="dns-prefetch" href="https://securepubads.g.doubleclick.net">
        <link rel="dns-prefetch" href="https://fonts.googleapis.com">
        <!--<link rel="dns-prefetch" href="https://certify-js.alexametrics.com">-->
        <link rel="dns-prefetch" href="https://www.google-analytics.com">
        <link rel="dns-prefetch" href="https://connect.facebook.net">
        <link rel="dns-prefetch" href="https://bs.serving-sys.com">
        <link rel="dns-prefetch" href="https://bid.g.doubleclick.net">
        <link rel="dns-prefetch" href="https://gum.criteo.com">
        <link rel="dns-prefetch" href="https://sc.iasds01.com">
        <link rel="dns-prefetch" href="https://dt.adsafeprotected.com">
        <link rel="dns-prefetch" href="https://googleads.g.doubleclick.net">
        <link rel="dns-prefetch" href="https://secure-ds.serving-sys.com">

        <link rel="manifest" href="/manifest.json">

        <?php
        if ( is_single() ) :
            if ( get_field( 'author' ) ) {
                $author = get_field( 'author' );
            } else if ( get_field( 'Author' ) ) {
                $author =get_field( 'Author' );
            } else {
                if ( '' != get_the_author_meta( 'first_name', $post->post_author ) && '' != get_the_author_meta( 'last_name', $post->post_author ) ) {
                    $author = get_the_author_meta( 'first_name', $post->post_author ) . ' ' . get_the_author_meta( 'last_name', $post->post_author );
                } else {
                    $author = get_the_author_meta( 'display_name', $post->post_author );
                }
            }

            $categories = get_the_category(get_the_ID());
            $CategoryCD = '';
            if ( $categories ) :
                foreach( $categories as $category ) :
                    $CategoryCD .= $category->slug . ' ';
                endforeach; // For Each Category
            endif; // If there are categories for the post

            $tags = get_the_tags(get_the_ID());
            $TagsCD = '';
            if ( $tags ) :
                foreach( $tags as $tag ) :
                    $TagsCD .= $tag->slug . ' ';
                endforeach; // For Each Tag
            endif; // If there are tags for the post
            ?>
            <script>
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                    'AuthorCD': '<?php echo $author; ?>',
                    'CategoryCD': '<?php echo $CategoryCD; ?>',
                    'TagsCD': '<?php echo $TagsCD; ?>',
                    'PubdateCD': '<?php echo get_the_time('M d, Y', get_the_ID() ); ?>'
                });
            </script>
        <?php endif; // If it's a Single Post ?>

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-TQC6WRH');</script>
        <!-- End Google Tag Manager -->

        <style>
            *,::after,::before{box-sizing:border-box}html{font-family:sans-serif;line-height:1.15;-webkit-text-size-adjust:100%}
            article,nav{display:block}
            body{margin:0;font-size:1rem;font-weight:400;line-height:1.5;color:#212529;text-align:left;background-color:#fff}
            h1,h2,h3{margin-top:0;margin-bottom:.5rem}p{margin-top:0;margin-bottom:1rem}
            ul{margin-top:0;margin-bottom:1rem}ul ul{margin-bottom:0}
            a{color:#007bff;text-decoration:none;background-color:transparent}

            .topic .tags {
              font-size: 1rem;
              /* max-width: 5rem; */
              position: absolute;
              right: 0;
              top: 0;
              padding: .25rem .5rem;
            }
            .topic-inner {
              border: 1px solid #ccc;
              padding: .5rem;
            }

            .topic .subscribed {
              background: rgba(0,0,0,.07);
            }

            .topic h3 {
              font-size: 1.3rem;
            }

            .topic figure.img-wrap {
              /* width: 100px;
              height: 100px; */
              margin-left: auto;
              margin-right: auto;
              position: relative;
              overflow: hidden;
            }
            .topic figure.img-wrap img {
              /* height: 100%;
              width: auto;
              max-width: none;
              position: absolute;
              top: 50%;
              left: 50%;
              transform: translate(-50%, -50%); */
            }

            .topic-tastemakers .topic-inner {
              background-color: #dc3545;
              color: #fff;
              border: 1px solid transparent;
            }

            @media (min-width:768px){
                .wrap.quiz-wrap {
                    max-width: 730px;
                }
            }
            <?php if ( is_page_template('single-template-quiz.php') ) : ?>
            body { background: #555 !important; }
            <?php endif; ?>

            .sticky-ad-bottom .proper-ad-unit .inner-wrapper {
              display: none;
            }
            .teads-inread {
              margin: 1rem auto !important;
            }

            @media (min-width:992px){
              .sticky-rail {
                position: sticky;
                top: 80px;
              }
            }

            .ob-single-h-wrap {
              width: 100%;
              position: relative; overflow: hidden;
              /* min-height: 350px;
              max-height: 100vh; */
            }

            .ob-single-h-overlay {
              width: 100%; height: auto; position: absolute; overflow: hidden; top: 0; bottom: 0; left: 0; right: 0;
            }


            @media (max-width: 767px) {
              .ob-single-h-wrap {
                /* min-height: 50vh; */
                height: auto;
              }

              .ob-single-h-overlay {
                background: rgba(0,0,0,.45);
              }

              .ob-single-h-img {
                width: auto; height: calc(100% - 30px); position: absolute; overflow: hidden; top: 50%; left: 0; transform: translateY(-50%);
                width: 100%; height: auto; position: absolute; overflow: hidden; top: 50%; left: 50%; transform: translate(-50%, -50%);
              }

              .ob-single-h-info {
                height: auto;
                /* position: absolute; */
                overflow: hidden;
                /* top: 50%; */
                max-width: 100%;
                width: 100%;
                /* left: 50%;
                transform: translate(-50%, -50%); */
                text-align: center;
                max-width: none;
              }

              .ob-single-h-info button{
                margin: auto;
                float: none;
              }
            }

            .ob-single-h-wrap {
              background-color: rgb(19,19,19);
              background-repeat: no-repeat;
              background-size: cover;
              background-position: center center;
            }

            @media (min-width: 768px) {
              .ob-single-h-overlay {
                background: rgba(0,0,0,.45);
              }
              .ob-single-h-wrap {
                width: 100%;
                position: relative;
                overflow: hidden;
                /* min-height: calc(100vh - 170px); */
                /* height: calc(100vh - 150px); */
                height: 40vh;
              }
              .ob-single-h-img-wrap {
                margin: auto;
                display: block;
                text-align: center;
                overflow: hidden;
                position: relative;
                /* height: calc(100% - 150px); */
                width: 100%;
                height: 100%;
                padding-bottom: 2rem;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
              }
              .ob-single-h-img {
                /* box-shadow: 10px 0px 100px #000; */
                height: auto;
                width: 100%;
                max-width: none;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
              }
              .ob-single-h-info {
                height: auto;
                margin: 1rem auto;
                text-align: center;
                width: 100%;
                /* position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%); */
              }

              .ob-single-h-info button{
                margin: auto;
              }

              .ob-single-hero-img {
                transform: scale(1.1);
              }
            }

            @media (min-width: 1300px) {
              .ob-single-h-wrap {
                max-height: 440px;
                height: 50vh;
              }
            }

            @media (max-width: 419px){
              .d-xs-none {
                display: none !important;
              }
            }

            .l-list-info {
              font-size: 150% !important;
            }

            .l-list-info,
            .observer-action-wrap {
              transition: .25s all linear;
            }

            /* Base for label styling */
.checkbox-list:not(:checked),
.checkbox-list:checked {
  position: absolute;
  left: -9999px;
}
.checkbox-list:not(:checked) + label,
.checkbox-list:checked + label {
  position: relative;
  /* padding-left: 1.95rem; */
  cursor: pointer;
  width: 2rem;
  height: 2rem;
  margin: 2rem auto -1rem;
}

/* checkbox aspect */
.checkbox-list:not(:checked) + label:before,
.checkbox-list:checked + label:before {
  content: '';
  position: absolute;
  left: 0;
  top: -50%;
  width: 2rem;
  height: 2rem;
  border: 1px solid #ccc;
  background: #fff;
  border-radius: 4px;
  box-shadow: inset 0 1px 3px rgba(0,0,0,.1);
  padding: .5rem;
}
/* checked mark aspect */
.checkbox-list:not(:checked) + label:after,
.checkbox-list:checked + label:after {
  content: '\2713\0020';
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%) scale(1.5);
  max-width: 100%;
  font-size: 1.25rem;
  line-height: 0;
  color: #dc3545;
  transition: all .2s;
  font-family: 'Lucida Sans Unicode', 'Arial Unicode MS', Arial;
}
/* checked mark aspect changes */
.checkbox-list:not(:checked) + label:after {
  opacity: 0;
  transform: scale(0);
}
.checkbox-list:checked + label:after {
  opacity: 1;
  /* transform: scale(1.6); */
}
/* disabled checkbox */
.checkbox-list:disabled:not(:checked) + label:before,
.checkbox-list:disabled:checked + label:before {
  box-shadow: none;
  border-color: #bbb;
  background-color: #ddd;
}
.checkbox-list:disabled:checked + label:after {
  color: #999;
}
.checkbox-list:disabled + label {
  color: #aaa;
}
/* accessibility */
.checkbox-list:checked:focus + label:before,
.checkbox-list:not(:checked):focus + label:before {
  border: 2px dotted blue;
}

#page {
  display: none;
}
#page-loader {
	position: fixed;
	left: 0px;
	top: 0px;
	width: 100vw;
	height: 100vh;
	z-index: 9999;
	background:  #fff;
}
.double-bounce1, .double-bounce2 {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background-color: #333;
    opacity: .6;
    position: absolute;
    top: 0;
    left: 0;
    animation: sk-bounce 2.0s infinite ease-in-out;
}
.double-bounce2{animation-delay:-1.0s}
@keyframes sk-bounce{0%,100%{transform:scale(0.0)}
  50%{transform:scale(1.0)}
}

.progress {
  overflow: unset !important;
}
.profile-strength {
  /* background: -webkit-linear-gradient(90deg,#544bc2,#17afb8,#2f7b15) !important;
  background: -moz-linear-gradient(90deg,#544bc2,#17afb8,#2f7b15) !important;
  background: -o-linear-gradient(90deg,#544bc2,#17afb8,#2f7b15) !important; */
  background: linear-gradient(90deg,#dc3545,#007bff) !important;
}
.profile-strength-step {
  background: #ccc;
}
.profile-strength-complete, .profile-strength-start {
  position: absolute;
  width: 32px; height: 32px;
  top: -9px;
  left: calc(100% - 29px);
  border: 3px solid #e1e9ee;
  border-radius: 50%;
  background-color: #fff;
  box-shadow: 0 0 0 1px rgba(0,0,0,.15), 0 2px 3px rgba(0,0,0,.2);
}
.profile-strength-start{
  left: -2px;
}

/* Observer single pages */
.observer-single {
  padding-top: 2rem;
  padding-bottom: 2rem;
}
.observer-single {
  /* height: 100vh; */
}
@media (min-width:768px){
  .observer-single {
    min-height: 100vh;
    /* height: 100%; */
    padding: 0;
  }
}
.observer-logo {
  max-width: 300px;
  margin-bottom: 30px;
}
.subheader {
  font-size: 20px;
  font-weight: 400;
  line-height: 1.4;
  text-align: left;
  max-width: 700px;
}
.observer-sub-form {
  display: flex;
  padding: .25rem;
  background: #dc3545;
  /* margin-top: 40px; */
  /* margin-left: 1.5rem; */
  /* margin-right: 1.5rem; */
  border-radius: 10px;
  max-width: none;
  box-shadow: 0 0 1em 5px rgba(50,50,93,.06), 0 0.313em 0.938em 1px rgba(0,0,0,.28);
}
.observer-sub-form #observer-sub-email {
  background: #fff;
  border-radius: 8px;
  padding: 25px 15px;
}
.observer-sub-form input[type=email] {
  width: 100%;
  font-size: 16px;
  line-height: 1;
  color: #000;
  border: none;
}
.observer-sub-form input[type=submit] {
  /* font-size: 25px; */
  padding: 5px 10px;
  font-weight: 300;
  background-color: #dc3545;
  color: #fff;
  border: none;
}
.observer-hero {
  background-repeat: no-repeat;
  background-position: left;
  background-size: cover;
}
.btn-observer-sub {
  margin-left: 1.5rem;
  margin-right: 1.5rem;
  margin-top: 40px;
  width: 450px;
  max-width: 100%;
}
.btn-observer-sub .btn {
  /* box-shadow: 0 0 1em 5px rgba(50,50,93,.06), 0 0.313em 0.938em 1px rgba(0,0,0,.28); */
}

.progress-bar {
  border-bottom-left-radius: .25rem; border-top-left-radius: .25rem;
}

@media (max-width:768px){
  .btn-observer-sub {
    width: auto;
  }
}
</style>

        <?php wp_head(); ?>

        <?php
        if ( is_single() ) :
            if ( get_field( 'fb_pixel' ) ) :
                echo get_field( 'fb_pixel' );
            endif;
        endif;
        ?>

        <!-- Apester -->
        <script type="text/javascript" src="https://static.apester.com/js/sdk/latest/apester-sdk.js" async></script>

        <script>
        	jQuery(window).on('load', function() {
        		jQuery("#page-loader").fadeOut('fast');
            jQuery('#page').show();
        	});
        </script>
        <noscript>
          <style>
          #page-loader {
            display: none !important;
          }
          #page {
            display: block !important;
          }
          </style>
        </noscript>
    </head>

    <body <?php body_class(); ?> id="body">

      <div id="page-loader">
        <div style="width: 30px; height: 30px; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);">
          <div class="double-bounce1"></div><div class="double-bounce2"></div>
        </div>
      </div>

        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQC6WRH" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->

        <div id="fb-root"></div>
        <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.9&appId=1950298011866227";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));</script>

<?php
if ( is_user_logged_in() ) :
  $profile_complete_percentage = 0;
  $current_user = wp_get_current_user();

  if( get_user_meta( $current_user->ID, 'first_name', true ) )
    $profile_complete_percentage += 20;

  if( get_user_meta( $current_user->ID, 'last_name', true ) )
    $profile_complete_percentage += 20;

  if( get_user_meta( $current_user->ID, 'state', true ) )
    $profile_complete_percentage += 20;

  if( get_user_meta( $current_user->ID, 'birthday', true ) )
    $profile_complete_percentage += 20;

  if( get_user_meta( $current_user->ID, 'gender', true ) )
    $profile_complete_percentage += 20;

  if ( $profile_complete_percentage < 20 ) {
    $profile_complete_class = 'badge-danger';
  } else if ( $profile_complete_percentage <= 40 ) {
    $profile_complete_class = 'badge-warning';
  } else if ( $profile_complete_percentage <= 60 ) {
    $profile_complete_class = 'badge-info';
  } else if ( $profile_complete_percentage <= 80 ) {
    $profile_complete_class = 'badge-primary';
  } else if ( $profile_complete_percentage <= 100 ) {
    $profile_complete_class = 'badge-success';
  }
endif; // If user is logged in
?>

<div id="page" style="display: none;">
      <?php
      $logo_link = home_url('/');
      $logo_url = get_template_directory_uri() . '/images/TheBragLOGOwhiteNOSHIELD.svg';
      $logo_alt = 'The Brag';
      include( get_template_directory() . '/page-templates/brag-observer/menu.php' );?>
      <div id="header" class=" bg-white p-0" style="position: fixed; top: 0;">
        <?php // include( get_template_directory() . '/partials/menu-network.php' ); ?>
        <div id="masthead" class="navbar navbar-inverse navbar-fixed-top hidden-print px-0">
          <div class="container-fluid d-flex justify-content-between" style="position: relative; padding-left: 0; padding-right: 0;">
            <div id="header-logo-wrap" class="col-8 col-md-4 px-0">
              <button class="navbar-toggler" type="button" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="outline: none;">
                <div class="navbar-button-bars"><i class="fa fa-bars"></i></div>
              </button>

              <a id="header-logo" class="header-logo" href="<?php echo home_url( '/observer/' ); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/images/observer/TBOLogo.svg" alt="The Brag Observer" class="img-fluid" width="150" height="37">
              </a>
            </div>
            <div id="header-submenu-wrap" class="col-4 col-md-8 text-right align-items-end d-flex flex-column">
              <div>
                <div class="d-inline float-right">
                  <?php
                  global $current_user;
                  wp_get_current_user();
                  if ( is_user_logged_in() ) :  ?>
                  <div class="dropdown float-right2">
                    <button class="btn btn-dark dropdown-toggle rounded" type="button" id="dropdownUserButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fa fa-user"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownUserButton" style="left: auto; right: 0;">
                      <a class="dropdown-item" href="<?php echo home_url( '/observer-subscriptions/' ); ?>">My preferences</a>
                      <a class="dropdown-item" href="<?php echo home_url( '/observer/magazine-subscriptions/' ); ?>">Magazine subscriptions</a>
                      <a class="dropdown-item" href="<?php echo home_url( '/observer/competitions/' ); ?>">Competitions</a>
                      <a class="dropdown-item" href="<?php echo home_url( '/profile/' ); ?>">Profile <span class="badge <?php echo $profile_complete_class; ?>"><?php echo $profile_complete_percentage; ?>% complete</span></a>
                      <a class="dropdown-item" href="<?php echo home_url( '/observer/refer-a-friend/' ); ?>">Refer a friend and earn</a>
                      <a class="dropdown-item" href="<?php echo home_url( '/change-password/' ); ?>">Change password</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="<?php echo home_url( 'logout' ); ?>">Logout</a>
                    </div>
                  </div>
                  <?php else : ?>
                    <!-- <a href="<?php echo home_url( '/login/' ); ?>" class="btn btn-sm btn-dark rounded">Login / Signup</a> -->
                    <button
                      type="button"
                      class="btn btn-sm btn-dark rounded btn-login"
                      data-target="#loginModal">
                      Login / Signup
                    </button>
                  <?php endif; ?>
                </div>

                <nav id="header-submenu" class="navbar navbar-expand-md float-right p-0 d-none d-md-block">
                  <div class="m-auto" id="navbarNav">
                    <ul class="navbar-nav">
                      <li class="nav-item">
                        <a class="nav-link" href="<?php echo home_url( '/observer/' ); ?>">All</a>
                      </li>
                      <?php
                      $categories = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}observer_categories" );
                      if ( $categories ) :
                        foreach ( $categories as $category ) :
                      ?>
                      <li class="nav-item">
                        <a class="nav-link" href="<?php echo home_url( '/observer/category/' . $category->slug . '/'); ?>"><?php echo $category->title; ?></a>
                      </li>
                      <?php
                        endforeach;
                      endif;
                      ?>
                    </ul>
                  </div>
                </nav>
              </div>
            </div>
          </div><!-- / #masthead .container -->
        </div><!-- / #masthead -->
      </div><!-- /#header -->

      <div id="main" class="container-fluid" style="margin-top: 81px;">
        <div id="content" class="pb-2">
