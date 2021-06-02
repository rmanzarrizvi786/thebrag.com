<!-- Login / Register Modal -->
<?php if (!is_user_logged_in()) : ?>
  <style>
    @media (min-width:576px) {
      #loginModal .inputEmail {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
      }

      #loginModal .inputPassword {
        border-radius: 0;
        border-left: 0;
        border-right: 0;
      }

      #loginModal .btn-submit,
      #loginModal .loading-wrap {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
      }

      #loginModal .loading-wrap {

        padding: 0 !important;
      }

      #loginModal .loading-wrap .spinner {
        margin: 7px 0 6px 0 !important;
      }
    }

    .menu-pubs ul li a {
      padding: .5rem .25rem !important;
    }

    .menu-pubs a img {
      filter: grayscale(1);
      transition: .25s all linear;
    }

    .menu-pubs a:hover img {
      filter: grayscale(0);
    }

    @media (min-width: 768px) {
      .menu-pubs ul li {
        margin: 0 !important;
      }

      .menu-pubs ul li a {
        padding: .5rem !important;
      }
    }

    .wp-social-login-provider-list-wrap {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
    }

    .wp-social-login-connect-with {
      display: none;
    }

    a.wp-social-login-provider {
      border-radius: .25rem;
      color: #fff;
      font-size: 1.5rem;
      text-align: center;
      padding: 1rem .5rem;
      margin: 1rem .5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      height: 4rem;
      text-decoration: none;
    }


    .wp-social-login-provider-list {
      display: flex;
      padding: 0;
    }

    .wp-social-login-provider-list img {
      display: none;
    }

    a.wp-social-login-provider:before {
      content: "";
      background-color: #fff;
      border-radius: 2px;
      background-size: 70%;

      display: block;
      width: 3rem;
      height: 3rem;
      color: #fff;
      background-repeat: no-repeat;
      background-position: 50%;
    }

    a.wp-social-login-provider:hover {
      color: #fff;
    }

    a.wp-social-login-provider:after {
      display: block;
      width: 100%;
      margin-left: .5rem;
    }

    a.wp-social-login-provider-facebook {
      background: #3b5998;
    }

    a.wp-social-login-provider-facebook:before {
      background-image: url(<?php echo get_template_directory_uri(); ?>/images/facebook.svg);
      background-size: 90%;
    }

    a.wp-social-login-provider-facebook:after {
      content: "Facebook" !important;
      background-color: #3b5998;
    }

    a.wp-social-login-provider-google {
      background: #DB4437;
    }

    a.wp-social-login-provider-google:before {
      background-image: url(<?php echo get_template_directory_uri(); ?>/images/google.svg);
    }

    a.wp-social-login-provider-google:after {
      content: "Google";
      background-color: #DB4437;
    }

    a.wp-social-login-provider-apple {
      background: #000;
    }

    a.wp-social-login-provider-apple:before {
      background-image: url(<?php echo get_template_directory_uri(); ?>/images/apple.svg);
    }

    a.wp-social-login-provider-apple:after {
      content: "Apple";
      background-color: #000;
    }

    hr.or-separator {
      margin: 3rem auto;
      position: relative;
    }

    hr.or-separator:after {
      content: "OR";
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      width: 50px;
      height: 50px;
      margin: auto;
      text-align: center;
      border: 1px solid rgba(0, 0, 0, .1);
      border-radius: 50%;
      font-size: 1rem;
      padding: 10px;
      box-sizing: border-box;
      background: #fff;
      color: rgba(0, 0, 0, .5);
    }

    .modal-dialog {
      width: 600px !important;
      max-width: 100% !important;
      margin: auto !important;
    }

    @media (max-width: 640px) {

      .wp-social-login-provider-list-wrap,
      .wp-social-login-provider-list {
        /* flex-flow: column; */
      }

      a.wp-social-login-provider:after {
        display: none;
      }

      .modal-dialog {
        width: 100% !important;
      }
    }

    #loginModal .spinner {
      width: 25px;
      height: 25px;
    }

    .double-bounce1.light,
    .double-bounce.light {
      background-color: #fff;
    }
  </style>
  <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="newsletter-box">
            <div class="row">
              <div class="col-12">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>

            <div class="container-fluid">
              <div class="row">
                <div class="col-12 pb-5" style="background: #fff;">
                  <div class="text-center mb-2">
                    <a href="https://thebrag.com/media"><img data-src="https://thebrag.com/media/wp-content/themes/bragm/images/TheBragMedia_LOGO_v6@100x.png" alt="The Brag Media" class="lazyload img-responsive" width="" style="display: inline; width: 150px; max-width: 100%;"></a>
                  </div>
                  <div class="menu-pubs menu-network d-flex align-items-center justify-content-center">
                    <ul class="nav d-flex align-items-center justify-content-center">
                      <li class="nav-item"><a href="https://au.rollingstone.com/" target="_blank" class="nav-link"><img data-src="<?php echo content_url(); ?>/uploads/edm/Rolling-Stone-Australia.jpg" alt="Rolling Stone Australia" class="lazyload" style="width: 100px"></a></li><!-- Rolling Stone Australia -->

                      <li class="nav-item"><a href="https://tonedeaf.thebrag.com/" target="_blank" class="nav-link"><img data-src="<?php echo content_url(); ?>/uploads/edm/Tone-Deaf.jpg" alt="Tone Deaf" class="lazyload" style="width: 80px"></a></li><!-- Tone Deaf -->

                      <li class="nav-item"><a href="https://dontboreus.thebrag.com/" target="_blank" class="nav-link"><img data-src="<?php echo content_url(); ?>/uploads/edm/Dont-Bore-Us.jpg" alt="Don't Bore Us" class="lazyload" style="width: 100px;"></a></li><!-- Don't Bore Us -->

                      <li class="nav-item"><a href="https://thebrag.com/" target="_blank" class="nav-link"><img data-src="<?php echo content_url(); ?>/uploads/edm/The-Brag.jpg" alt="The Brag" class="lazyload" style="width: 80px;"></a></li>

                      <li class="nav-item"><a href="https://theindustryobserver.thebrag.com/" target="_blank" class="nav-link"><img data-src="<?php echo content_url(); ?>/uploads/edm/The-Industry-Observer.jpg" alt="The Industry Observer" class="lazyload" style="width: 50px;"></a></li><!-- The Industry Observer -->
                    </ul>
                  </div><!-- .menu-network -->
                </div>
              </div>
            </div>

            <div>

              <h4 class="text-center">Login / Signup with</h4>
              <div class="wp-social-login-provider-list-wrap">
                <?php
                // WSL
                do_action('wordpress_social_login');

                // Apple
                global $wp;
                $_SESSION['apple_signin_state'] = bin2hex(random_bytes(5));
                $_SESSION['ReturnToUrl'] = home_url($wp->request);

                // echo '<pre>'; print_r( $_SESSION ); echo '</pre>';

                $apple['client_id'] = 'com.thebrag';
                $apple['redirect_uri'] = home_url('/sign-in-with-apple/'); // home_url( 'login/' );

                $authorize_url = 'https://appleid.apple.com/auth/authorize' . '?' . http_build_query([
                  'response_type' => 'code',
                  'response_mode' => 'form_post',
                  'client_id' => $apple['client_id'],
                  'redirect_uri' => $apple['redirect_uri'],
                  'state' => $_SESSION['apple_signin_state'],
                  'scope' => 'name email',
                ]);
                ?>
                <div class="wp-social-login-provider-list">
                  <a rel="nofollow" href="<?php echo $authorize_url; ?>" class="wp-social-login-provider wp-social-login-provider-apple"></a>
                </div>
              </div>

              <hr class="or-separator">

              <form method="post" action="#" id="modal-form-login" class="d-flex flex-column justify-content-center">
                <input type="hidden" name="ReturnTo" value="<?php echo home_url($wp->request); ?>">

                <div class="d-flex flex-row">
                  <input type="text" name="username" class="form-control form-control-md mb-2 inputEmail" placeholder="Email/Username">

                  <input type="password" name="password" class="form-control form-control-md mb-2 inputPassword" placeholder="Password">

                  <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>

                  <button type="submit" class="btn btn-dark btn-md mb-2 float-right align-self-end btn-submit rounded">Login/Signup</button>

                  <div class="bg-dark rounded loading-wrap mb-2">
                    <div class="loading mx-3" style="display: none;">
                      <div class="spinner">
                        <div class="double-bounce1 light"></div>
                        <div class="double-bounce2 light"></div>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="alert alert-success d-none js-success-login"></div>
                <div class="alert alert-info d-none js-msg-login"></div>
                <div class="alert alert-danger d-none js-errors-login"></div>
              </form>

            </div>
            <div class="col-12 mt-3 pr-md-0 text-right">
              <a href="<?php echo home_url('/forgot-password/'); ?>" class="">Forgot password?</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; // If user is NOT logged in - Login Modal 
?>