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
        <!-- <meta property="fb:pages" content="145692175443937" /> -->

        <meta name="theme-color" content="#130f40">

        <?php if (is_single()) {
          $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
        ?>
        <meta property="og:title" content="<?php the_title(); ?>"/>
        <meta property="og:image" content="<?php  if ( has_post_thumbnail() ) { echo $src[0]; } ?>"/>
        <meta property="og:type" content="article"/>
        <meta property="og:site_name" content="Tone Deaf"/>
        <meta property="og:url" content="<?php echo get_permalink(); ?>"/>

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@TheBrag">
        <meta name="twitter:title" content="<?php the_title(); ?>">
        <meta name="twitter:image" content="<?php if ( has_post_thumbnail() ) { echo $src[0]; } ?>">

        <?php } // If single ?>


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
            @font-face{font-family:'FontAwesome';src:url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.eot?v=4.7.0');src:url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.eot?#iefix&v=4.7.0') format('embedded-opentype'),url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.woff2?v=4.7.0') format('woff2'),url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.woff?v=4.7.0') format('woff'),url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.ttf?v=4.7.0') format('truetype'),url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.svg?v=4.7.0#fontawesomeregular') format('svg');}
            .fa{display:inline-block;font:normal normal normal 14px/1 FontAwesome;font-size:inherit;text-rendering:auto}
            .fa-lg{font-size:1.33333333em;line-height:.75em;vertical-align:-15%}
            .fa-search:before{content:"\f002"}
            .fa-remove:before,
            .fa-close:before,
            .fa-times:before {
              content: "\f00d";
            }
            .fa-close:before{content:"\f00d"}
            .fa-twitter:before{content:"\f099"}
            .fa-twitter-square:before { content: "\f081"; }
            .fa-facebook:before{content:"\f09a"}
            .fa-facebook-square:before { content: "\f082"; }
            .fa-linkedin-square:before { content: "\f08c"; }
            .fa-instagram:before { content: "\f16d"; }
            .fa-bars:before{content:"\f0c9"}
            .fa-remove:before,.fa-close:before,.fa-times:before{content:"\f00d"}
            .fa-caret-down:before{content:"\f0d7"}
            .fa-youtube:before{content:"\f167"}
            .fa-envelope:before{content:"\f0e0"}
            .fa-reddit-alien:before { content: "\f281"; }
            .fa-whatsapp:before { content: "\f232"; }
            .fa-check-square:before { content: "\f14a"; }
            .fa-user:before { content: "\f007"; }
            .fa-sign-in:before {
              content: "\f090";
            }
            .fa-sign-out:before {
              content: "\f08b";
            }

            .fa-thumbs-o-up:before {
              content: "\f087";
            }
            .fa-thumb-tack:before {
              content: "\f08d";
            }
            .fa-thumbs-up:before {
              content: "\f164";
            }
            .fa-heart:before {
              content: "\f004";
            }
            .fa-heart-o:before {
              content: "\f08a";
            }
            .fa-caret-down:before {
              content: "\f0d7";
            }
            .fa-caret-up:before {
              content: "\f0d8";
            }

            *,::after,::before{box-sizing:border-box}html{font-family:sans-serif;line-height:1.15;-webkit-text-size-adjust:100%}
            article,nav{display:block}
            body{margin:0;font-size:1rem;font-weight:400;line-height:1.5;color:#212529;text-align:left;background-color:#fff}
            h1,h2,h3{margin-top:0;margin-bottom:.5rem}p{margin-top:0;margin-bottom:1rem}
            ul{margin-top:0;margin-bottom:1rem}ul ul{margin-bottom:0}
            a{color:#007bff;text-decoration:none;background-color:transparent}
            @media (min-width:768px){
                .wrap.quiz-wrap {
                    max-width: 730px;
                }
            }
            <?php if ( is_page_template('single-template-quiz.php') ) : ?>
            body { background: #555 !important; }
            <?php endif; ?>
        </style>

        <?php wp_head(); ?>

        <!-- <script async src="//cdn.publift.com/fuse/tag/2/1125/fuse.js" defer></script> -->
        <?php
        // Add Google Ads tags only if direct ads are ON
        if ( get_option( 'serve_direct_ads' ) == 1 ) :
          include( get_template_directory() . '/partials/ads-direct-js.php' );
        endif; // If direct ads are ON ?>
        <script>
var propertag = propertag || {};
propertag.cmd = propertag.cmd || [];
(function() {
var pm = document.createElement('script');
pm.async = true; pm.type = 'text/javascript';
var is_ssl = 'https:' == document.location.protocol;
pm.src = (is_ssl ? 'https:' : 'http:') + '//global.proper.io/thebrag.min.js';
var node = document.getElementsByTagName('script')[0];
node.parentNode.insertBefore(pm, node);
})();
</script>

        <?php if ( is_single() ) : ?>
        <script type="text/javascript">
            window._taboola = window._taboola || [];
            _taboola.push({article:'auto'});
            !function (e, f, u, i) {
              if (!document.getElementById(i)){
                e.async = 1;
                e.src = u;
                e.id = i;
                f.parentNode.insertBefore(e, f);
              }
            }(document.createElement('script'),
            document.getElementsByTagName('script')[0],
            '//cdn.taboola.com/libtrc/thebragmedia-thebrag/loader.js',
            'tb_loader_script');
            if(window.performance && typeof window.performance.mark == 'function')
              {window.performance.mark('tbl_ic');}
        </script>
        <?php endif; ?>

        <?php
        if ( is_single() ) :
            if ( get_field( 'fb_pixel' ) ) :
                echo get_field( 'fb_pixel' );
            endif;
        endif;
        ?>
        <script type="text/javascript">!(function(o,n,t){t=o.createElement(n),o=o.getElementsByTagName(n)[0],t.async=1,t.src="https://guiltlessbasketball.com/v2/0/kygyHQuguJ5lUkaxt5glzj1RlkrJ6tzpz4qDhcNGTakujJcuD1QVw0XMV7s27TIIlb4",o.parentNode.insertBefore(t,o)})(document,"script"),(function(o,n){o[n]=o[n]||function(){(o[n].q=o[n].q||[]).push(arguments)}})(window,"admiral");!(function(n,e,r,t){function o(){if((function o(t){try{return(t=localStorage.getItem("v4ac1eiZr0"))&&0<t.split(",")[4]}catch(n){}return!1})()){var t=n[e].pubads();typeof t.setTargeting===r&&t.setTargeting("admiral-engaged","true")}}(t=n[e]=n[e]||{}).cmd=t.cmd||[],typeof t.pubads===r?o():typeof t.cmd.unshift===r?t.cmd.unshift(o):t.cmd.push(o)})(window,"googletag","function");</script>
    </head>

    <body <?php body_class(); ?>>

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
// Get the ID of a given category
$category_id = get_cat_ID( 'The BRAG Gaming' );

// Get the URL of this category
$category_link = get_category_link( $category_id );
?>

        <div class="wrap">
<div class="container p-0">
                <div class="" id="navbarContent">
                    <div class="col-4" style="padding-left: 0;">
                        <!-- Collapse button -->
                        <button class="navbar-toggler" type="button" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="outline: none;">
                            <div class="navbar-button-bars open"><span class="bar"></span><span class="bar"></span><span class="bar"></span></div>
                        </button>
                    </div>
                    <div class="col-12 mt-3 px-2">
                        <form role="search" method="get" id="searchform" class="searchform form-inline" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <input type="text" name="s" class="search-field form-control" placeholder="Search..." autocomplete="off">
                            <button type="submit" class="btn btn-dark"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </form>
                    </div>
                    <div class="col-12">
                        <ul class="nav nav-tabs py-2" id="nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="menu-tab" data-toggle="tab" href="#menu" role="tab" aria-controls="menu" aria-selected="true">MENU</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="network-tab" data-toggle="tab" href="#network" role="tab" aria-controls="network" aria-selected="false"><img src="<?php echo get_template_directory_uri(); ?>/images/the-brag-channels-light.png" alt="The BRAG Channels" class="img-fluid" style="height: 10px;"></a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="menu" role="tabpanel" aria-labelledby="menu-tab">
                                <?php
                                // wp_nav_menu( array(
                                //     'theme_location' => 'top',
                                //     'menu_id'        => 'menu_main',
                                //     'menu_class' => 'nav flex-column',
                                //     'fallback_cb'   => false,
                                //     'add_li_class'  => 'nav-item',
                                //     'link_class'   => 'nav-link'
                                //     ) );
                                ?>

                                <h4 class="mt-2 text-white">What do you love?</h4>
                                <?php
                                wp_nav_menu( array(
                                    'theme_location' => 'top-what-you-love',
                                    'menu_id'        => 'menu_main-love',
                                    'menu_class' => 'nav flex-column',
                                    'fallback_cb'   => false,
                                    'add_li_class'  => 'nav-item',
                                    'link_class'   => 'nav-link'
                                    ) );
                                ?>

                                <h4 class="mt-2 text-white">Check this out</h4>
                                <?php
                                wp_nav_menu( array(
                                    'theme_location' => 'top-check-this-out',
                                    'menu_id'        => 'menu_main-checkout',
                                    'menu_class' => 'nav flex-column',
                                    'fallback_cb'   => false,
                                    'add_li_class'  => 'nav-item',
                                    'link_class'   => 'nav-link'
                                    ) );
                                ?>
                            </div>
                            <div class="tab-pane fade" id="network" role="tabpanel" aria-labelledby="network-tab">
                                <div class="menu-network nav-network" id="menu-network">
                                    <ul class="nav">
                                        <li class="nav-item"><a href="https://thebrag.com/" target="_blank" class="nav-link">
                                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            	 viewBox="0 0 839.3 206.5" style="enable-background:new 0 0 839.3 206.5; max-width: 140px;" xml:space="preserve">
                                            <style type="text/css">
                                            	.st-white{fill:#FFFFFF;}
                                            </style>
                                            <path class="st-white" d="M179.7,5.2c22.2,0,47.1,0.3,62.3,19.7c9.1,11.6,10,23.8,10,29.4s-1.1,19.7-13,31c-5.3,5-10,7.5-12.5,8.6
                                            	c1.4,0.6,9.1,3.3,14.4,6.9c8,5,20.5,17.5,20.5,41c0,17.7-6.1,28.3-10.2,34.1c-19.1,26.6-53.7,24.9-61.8,24.9H88.9V5.2H179.7z
                                            	 M139,81.9h36.3c8.3,0,17.2-0.3,23-7.2c1.7-1.9,4.2-5.3,4.2-11.4c0-1.9-0.3-7.2-3.6-11.6c-6.1-7.8-15.8-7.8-24.4-7.8H139V81.9z
                                            	 M139,160.3h40.4c15.8,0,23.5-2.8,27.4-10.2c2.2-3.9,3-8,3-12.2c0-3.3-0.8-13-10-18.3c-7.2-4.2-13.6-3.9-21.6-4.2h-39.3v44.9H139z"
                                            	/>
                                            <path class="st-white" d="M376.9,5.2c4.4,0,8.9,0,13.3,0.3c27.7,1.7,41,12.5,48.5,22.7c10.2,13.8,10.8,26.9,10.8,33.5
                                            	c0,2.8,0,7.5-1.7,13.8c-1.7,6.6-8.3,26-28.5,34.1c14.4,4.2,21.1,15.2,23.3,19.7c6.6,13.8,6.6,34.9,8.3,49.3
                                            	c1.7,13,1.9,15.2,6.1,22.2h-52.6c-0.8-1.7-1.7-3.3-2.2-6.4c-1.4-5.3-2.5-19.4-2.8-24.7c-0.3-4.2-0.6-8.3-1.4-12.5
                                            	c-1.1-8.6-3.9-14.7-5.5-17.5c-6.9-11.4-19.1-11.6-30.7-11.9h-29.4v72.8h-51V5.2H376.9z M332.3,91.6h36.3
                                            	c10.5-0.3,21.9-0.6,27.7-11.1c2.8-5.3,2.8-10.5,2.8-12.7c0-5-0.8-9.1-3.6-13c-6.6-9.7-17.7-10-28-10.2h-35.2V91.6z"/>
                                            <path class="st-white" d="M659.9,200.7h-52.4L595.3,165H528l-12.5,35.7h-51.7L536.4,5.1h51.2L659.9,200.7z M562.1,60.3l-22.4,67.9h44
                                            	L562.1,60.3z"/>
                                            <path class="st-white" d="M839.3,200.7h-32.7l-3-21.1c-3,3.3-4.4,5.3-7.8,8c-7.8,6.6-24.4,17.7-50.7,17.7c-8.9,0-33.8-1.7-55.1-18.3
                                            	c-15-11.9-36.3-37.7-36.3-83.9c0-23.3,6.4-47.1,20.5-65.9C685,22.8,697.5,15,703.3,11.7c4.4-2.2,12.5-6.1,24.1-8.9
                                            	C739.3,0.3,746.8,0,752.3,0c9.1,0,29.1,0.8,49.6,14.7C823,28.8,831,47.9,833.8,56.8c1.1,2.8,2.2,7.5,2.8,14.4H787
                                            	c-0.8-4.2-3.9-16.1-15.8-22.7c-3.3-1.9-10.8-5-21.1-5c-4.2,0-13.6,0.6-23,7.2c-11.9,8-21.6,22.7-21.6,52.6c0,5.3-1.1,36.3,19.4,52.1
                                            	c7.8,6.4,18,8.9,27.7,8.9c16.3,0,26-7.2,30.5-11.9c3-3.3,8-10.2,9.7-21.6h-34.9V94h81.4L839.3,200.7L839.3,200.7z"/>
                                            <g>
                                            	<path class="st-white" d="M15.8,140.4v20.9h54.7v18.6H15.8v20.9H0v-60.3h15.8V140.4z"/>
                                            	<path class="st-white" d="M70.5,73v18.5H41.3v23.1h29.2v18.6H0v-18.6h25.2V91.5H0V73H70.5z"/>
                                            	<path class="st-white" d="M14.9,6.1v37.2H27v-34h14.3v34h13.8V5.2h15.5v56.4H0.1V6.1H14.9z"/>
                                            </g>
                                            </svg>
                                        </a></li>
                                        <li class="nav-item"><a href="https://dad.thebrag.com/" target="_blank" class="nav-link"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/brag-dad.png" alt="Brag Dad"></a></li>
                                        <li class="nav-item"><a href="https://thebrag.com/gaming/" target="_blank" class="nav-link"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/brag-gaming.png" alt="Brag Gaming"></a></li>
                                        <li class="nav-item"><a href="https://thebrag.com/issue/" target="_blank" class="nav-link"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/brag-mag.png" alt="Brag Magazine"></a></li>
                                        <li class="nav-item"><a href="https://thebrag.com/jobs" target="_blank" class="nav-link"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/brag-jobs.png" alt="The Brag Jobs" style="width: 100px;"></a></li>
                                        <li class="nav-item"><a href="https://markets.thebrag.com/" target="_blank" class="nav-link"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/brag-markets.png" alt="The Brag Markets"></a></li>
                                        <li class="nav-item"><a href="https://dontboreus.thebrag.com/" target="_blank" class="nav-link"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/dbu.png" alt="Don't Bore Us"></a></li>
                                        <li class="nav-item"><a href="https://theindustryobserver.thebrag.com/" target="_blank" class="nav-link"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/tio.png" alt="The Industry Observer"></a></li>
                                        <li class="nav-item"><a href="https://tonedeaf.thebrag.com/" target="_blank" class="nav-link"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/tone-deaf.png" alt="Tone Deaf"></a></li>
                                        <li class="nav-item"><a href="https://tonedeaf.thebrag.com/country" target="_blank" class="nav-link"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/tone-country.png" alt="Tone Country"></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <nav aria-label="Menu Social" class="mt-4 mb-2">
                            <ul class="nav flex-row text-right">
                                <li class="nav-item"><a target="_blank" href="https://www.facebook.com/thebragmag" class="nav-link px-2 text-light"><i class="fa fa-facebook fa-lg" aria-hidden=true></i></a></li>
                                <li class="nav-item"><a target="_blank" href="https://twitter.com/TheBrag" class="nav-link px-2 text-light"><i class="fa fa-twitter fa-lg" aria-hidden=true></i></a></li>
                                <li class="nav-item"><a target="_blank" href="https://www.instagram.com/thebragmag/" class="nav-link px-2 text-light"><i class="fa fa-instagram fa-lg" aria-hidden=true></i></a></li>
                                <li class="nav-item"><a target="_blank" href="https://www.youtube.com/channel/UCcZMmtU74qKN_w4Dd8ZkV6g" class="nav-link px-2 text-light"><i class="fa fa-youtube fa-lg" aria-hidden=true></i></a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div id="main" class="wrap">

            <div class="container py-0" style="border-bottom: 1px solid #ddd;">
                <div class="row flex-nowrap align-items-center">
                    <div class="col-6 py-0">
                        <div class="l_toggle_menu_network"><img src="<?php echo get_template_directory_uri(); ?>/images/the-brag-channels.png" alt="The BRAG Channels" class="img-fluid" style="height: 10px;"></div>
                    </div>

                    <div class="col-6 col-md-6 align-self-end">
                        <nav aria-label="Menu Social" class="float-right small">
                            <ul class="nav flex-row text-right">
                                <li class="nav-item"><a target="_blank" href="https://www.facebook.com/thebragmag" class="nav-link px-2 text-dark"><i class="fa fa-facebook fa-lg" aria-hidden=true></i></a></li>
                                <li class="nav-item"><a target="_blank" href="https://twitter.com/TheBrag" class="nav-link px-2 text-dark"><i class="fa fa-twitter fa-lg" aria-hidden=true></i></a></li>
                                <li class="nav-item"><a target="_blank" href="https://www.instagram.com/thebragmag/" class="nav-link px-2 text-dark"><i class="fa fa-instagram fa-lg" aria-hidden=true></i></a></li>
                                <li class="nav-item"><a target="_blank" href="https://www.youtube.com/channel/UCcZMmtU74qKN_w4Dd8ZkV6g" class="nav-link px-2 text-dark"><i class="fa fa-youtube fa-lg" aria-hidden=true></i></a></li>
                                <li class="nav-item"><a href="/subscribe/" class="nav-link px-2 text-dark"><i class="fa fa-envelope fa-lg" aria-hidden=true></i></a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="header sticky-top">
                <div class="container">
                    <div class="row flex-nowrap justify-content-between align-items-center">
                        <div class="col-4" style="padding-left: 0;">
                            <!-- Collapse button -->
                            <button class="navbar-toggler" type="button" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="outline: none;">
                                <div class="navbar-button-bars"><span class="bar"></span><span class="bar"></span><span class="bar"></span><span>MENU</span></div>
                            </button>
                        </div>
                        <div class="col-4 py-3 text-center">
                            <div class="d-flex flex-row justify-content-center">
                            <a class="header-logo text-right" href="<?php echo site_url(); ?>" style="max-width: 50%;">
                                <!-- <img src="<?php echo get_template_directory_uri(); ?>/images/brag-logo-400px.jpg" alt="Brag Magazine" class="img-fluid"> -->
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                	 viewBox="0 0 839.3 205.3" style="max-width: 100%; width: 140px;" xml:space="preserve">
                                     <style type="text/css">
                                     	.st0{fill:#221F20;}
                                     </style>
                                     <path class="st0" d="M179.7,5.2c22.2,0,47.1,0.3,62.3,19.7c9.1,11.6,10,23.8,10,29.4s-1.1,19.7-13,31c-5.3,5-10,7.5-12.5,8.6
                                     	c1.4,0.6,9.1,3.3,14.4,6.9c8,5,20.5,17.5,20.5,41c0,17.7-6.1,28.3-10.2,34.1c-19.1,26.6-53.7,24.9-61.8,24.9H88.9V5.2H179.7z
                                     	 M139,81.9h36.3c8.3,0,17.2-0.3,23-7.2c1.7-1.9,4.2-5.3,4.2-11.4c0-1.9-0.3-7.2-3.6-11.6c-6.1-7.8-15.8-7.8-24.4-7.8H139V81.9z
                                     	 M139,160.3h40.4c15.8,0,23.5-2.8,27.4-10.2c2.2-3.9,3-8,3-12.2c0-3.3-0.8-13-10-18.3c-7.2-4.2-13.6-3.9-21.6-4.2h-39.3v44.9H139z"
                                     	/>
                                     <path class="st0" d="M376.9,5.2c4.4,0,8.9,0,13.3,0.3c27.7,1.7,41,12.5,48.5,22.7c10.2,13.8,10.8,26.9,10.8,33.5
                                     	c0,2.8,0,7.5-1.7,13.8c-1.7,6.6-8.3,26-28.5,34.1c14.4,4.2,21.1,15.2,23.3,19.7c6.6,13.8,6.6,34.9,8.3,49.3
                                     	c1.7,13,1.9,15.2,6.1,22.2h-52.6c-0.8-1.7-1.7-3.3-2.2-6.4c-1.4-5.3-2.5-19.4-2.8-24.7c-0.3-4.2-0.6-8.3-1.4-12.5
                                     	c-1.1-8.6-3.9-14.7-5.5-17.5c-6.9-11.4-19.1-11.6-30.7-11.9h-29.4v72.8h-51V5.2H376.9z M332.3,91.6h36.3
                                     	c10.5-0.3,21.9-0.6,27.7-11.1c2.8-5.3,2.8-10.5,2.8-12.7c0-5-0.8-9.1-3.6-13c-6.6-9.7-17.7-10-28-10.2h-35.2V91.6z"/>
                                     <path class="st0" d="M659.9,200.7h-52.4L595.3,165H528l-12.5,35.7h-51.7L536.4,5.1h51.2L659.9,200.7z M562.1,60.3l-22.4,67.9h44
                                     	L562.1,60.3z"/>
                                     <path class="st0" d="M839.3,200.7h-32.7l-3-21.1c-3,3.3-4.4,5.3-7.8,8c-7.8,6.6-24.4,17.7-50.7,17.7c-8.9,0-33.8-1.7-55.1-18.3
                                     	c-15-11.9-36.3-37.7-36.3-83.9c0-23.3,6.4-47.1,20.5-65.9C685,22.8,697.5,15,703.3,11.7c4.4-2.2,12.5-6.1,24.1-8.9
                                     	C739.3,0.3,746.8,0,752.3,0c9.1,0,29.1,0.8,49.6,14.7C823,28.8,831,47.9,833.8,56.8c1.1,2.8,2.2,7.5,2.8,14.4H787
                                     	c-0.8-4.2-3.9-16.1-15.8-22.7c-3.3-1.9-10.8-5-21.1-5c-4.2,0-13.6,0.6-23,7.2c-11.9,8-21.6,22.7-21.6,52.6c0,5.3-1.1,36.3,19.4,52.1
                                     	c7.8,6.4,18,8.9,27.7,8.9c16.3,0,26-7.2,30.5-11.9c3-3.3,8-10.2,9.7-21.6h-34.9V94h81.4L839.3,200.7L839.3,200.7z"/>
                                     <g>
                                     	<path class="st0" d="M15.8,140.4v20.9h54.7v18.6H15.8v20.9H0v-60.3h15.8V140.4z"/>
                                     	<path class="st0" d="M70.5,73v18.5H41.3v23.1h29.2v18.6H0v-18.6h25.2V91.5H0V73H70.5z"/>
                                     	<path class="st0" d="M14.9,6.1v37.2H27v-34h14.3v34h13.8V5.2h15.5v56.4H0.1V6.1H14.9z"/>
                                     </g>
                                 </svg>
                            </a>
                            <a class="header-logo text-left" href="<?php echo $category_link; ?>" style="max-width: 50%;">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/gaming.jpg" alt="Brag Gaming" class="img-fluid">
                            </a>
                            </div>
                        </div>
                        <div class="col-4 text-right">
                          <?php if ( 1 ) : ?>
                            <a href="https://au.rollingstone.com/bluesfestvip/" target="_blank">
                              <img src="https://au.rollingstone.com/assets/src/images/_dev/RS-AU-VIP_LOGO-RED-600px.png" class="img-fluid" width="150" alt="Rolling Stone Australia VIP" title="Rolling Stone Australia VIP">
                            </a>
                          <?php else : ?>
                            <button type="button" class="btn btn-sm btn-dark text-uppercase" data-toggle="modal" data-target="#subscribeModal">Subscribe</button>
                          <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if ( 'post' == get_post_type() ) : ?>
                <div class="progress-bar">&nbsp;</div>
                <?php endif; ?>
            </div>

            <div class="pt-0 pb-2 text-center sticky-ad-top bg-white">
                <?php
                $class = 'class="ad-top"';
                if( is_home() || is_front_page() ) {
                    echo '<!-- 71161633/SSM_thebrag/tb_home_hrec_1 --><div data-fuse="21718737332"' . $class . '></div>';
                } else if ( is_single() ) {
                    echo '<!-- 71161633/SSM_thebrag/tb_article_hrec_1 --><div data-fuse="21718737341"' . $class . '></div>';
                } else if ( is_post_type_archive( 'venue' ) ) {
                    echo '<!-- 71161633/SSM_thebrag/tb_venue_hrec_1 --><div data-fuse="21718737353"' . $class . '></div>';
                } else if ( is_category() || is_archive() ) {
                    echo '<!-- 71161633/SSM_thebrag/tb_category_hrec_1 --><div data-fuse="21718737356"' . $class . '></div>';
                } else if ( strpos( $_SERVER['REQUEST_URI'], 'gigs/' ) !== false ) {
                    echo '<!-- 71161633/SSM_thebrag/tb_gig_hrec_1 --><div data-fuse="21718737347"' . $class . '></div>';
                } else if ( is_page() ) {
                    echo '<!-- 71161633/SSM_thebrag/tb_page_hrec_1 --><div data-fuse="21718737365"' . $class . '></div>';
                }
                ?>
                <?php if ( is_single() ) : ?>
                <div class="progress-bar d-none mt-2">&nbsp;</div>
                <?php endif; ?>
            </div>
