<?php
$states = array(
  "NSW" => "New South Wales",
  "VIC" => "Victoria",
  "QLD" => "Queensland",
  "TAS" => "Tasmania",
  "SA" => "South Australia",
  "WA" => "Western Australia",
  "NT" => "Northern Territory",
  "ACT" => "Australian Capital Territory",
  "INT" => "Outside Australia",
);
?>

<input type="hidden" name="<?php echo $this->plugin_name . '_nonce'; ?>" value="<?php echo wp_create_nonce( $this->plugin_name . '_nonce' ); ?>" id="<?php echo $this->plugin_slug . '-nonce'; ?>">

<!-- Subscribe Modal -->
<div class="modal fade" id="subscribeObserverModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
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
          <h2 id="modal-title-subscribe" class="text-center px-5"></h2>
          <p id="modal-desc-subscribe" class="text-center"></p>

          <div class="newsletter-form">

            <h4 class="text-center">Register with</h4>
            <div class="wp-social-login-provider-list-wrap">
              <?php
              // WSL
              do_action( 'wordpress_social_login' );

              // Apple
              $_SESSION['apple_signin_state'] = bin2hex(random_bytes(5));
              if ( ! isset( $_SESSION['ReturnToUrl'] ) ) {
                $_SESSION['ReturnToUrl'] = home_url( '/observer/' );
              }

              // echo '<pre>'; print_r( $_SESSION ); echo '</pre>';

              $apple['client_id'] = 'com.thebrag';
              $apple['redirect_uri'] = home_url( '/sign-in-with-apple/'); // home_url( 'login/' );

              $authorize_url = 'https://appleid.apple.com/auth/authorize'.'?'.http_build_query([
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

            <!-- <h4 class="text-center">Register</h4> -->

            <form action="" method="post" id="observer-subscribe-form" name="observer-subscribe-form" class="justify-content-center">

              <div class="observer-sub-form justify-content-center bg-dark">
                <input type="hidden" name="list" id="modal-list-subscribe">
                <input type="email" name="email" class="email form-control" placeholder="Your email" value="" style="border-radius: 5px; padding: 25px 15px;">
                <input type="submit" value="Subscribe" name="subscribe" class="button btn rounded" style="background-color: #343a40;">
                <div class="loading mx-3" style="display: none;"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>
              </div>

              <!-- <div class="mt-3">
                <div class="input-group mb-2 mr-sm-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text">Email</div>
                  </div>
                  <input type="email" name="email" class="email form-control" placeholder="Email address" value="">
                </div>
              </div>

              <div class="mt-3 mb-2">
                <input type="hidden" name="list" id="modal-list-subscribe">

                <input type="submit" value="SUBSCRIBE" name="subscribe" class="button btn btn-dark rounded form-control">
                <div class="loading" style="display: none;"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>
              </div> -->

              <div class="alert alert-info d-none js-msg-subscribe"></div>
              <div class="alert alert-danger d-none js-errors-subscribe"></div>

            </form>



              <!--
              <div class="mt-3 row">
                <div class="col-md-6">
                  <h4>First Name</h4>
                  <input type="text" name="first_name" class="form-control" placeholder="First Name" value="">
                </div>

                <div class="col-md-6">
                  <h4>Last Name</h4>
                  <input type="text" name="last_name" class="form-control" placeholder="Last Name" value="">
                </div>
              </div>

              <div class="mt-3">
                <h4>Birthday</h4>
                <div class="input-group">
                  <select aria-label="Day" name="birthday_day" id="day" title="Day" class="form-control">
                    <option value="0">Day</option>
                    <?php // for( $birthday_day = 1; $birthday_day <= 31; $birthday_day++ ) : ?>
                      <option value="<?php // echo $birthday_day; ?>"><?php // echo $birthday_day; ?></option>
                    <?php // endfor; ?>
                  </select>

                  <select aria-label="Month" name="birthday_month" id="month" title="Month" class="form-control">
                    <option value="0">Month</option>
                    <?php
                    // $months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
                    ?>
                    <?php // foreach( $months as $month_no => $month ) : ?>
                      <option value="<?php // echo $month_no; ?>"><?php // echo $month; ?></option>
                    <?php // endforeach; ?>
                  </select>

                  <select aria-label="Year" name="birthday_year" id="year" title="Year" class="form-control">
                    <option value="0">Year</option>
                    <?php // for( $birthday_year = date('Y'); $birthday_year >= date('Y') - 115; $birthday_year-- ) : ?>
                      <option value="<?php // echo $birthday_year; ?>"><?php // echo $birthday_year; ?></option>
                    <?php // endfor; ?>
                  </select>
                </div>
              </div>

              <div class="mt-3">
                <h4>State</h4>
                <select aria-label="State" name="state" id="state" title="State" class="form-control">
                  <option value="0">State</option>
                  <?php // foreach( $states as $state_abbr => $state ) : ?>
                    <option value="<?php // echo $state_abbr; ?>"><?php // echo $state; ?></option>
                  <?php // endforeach; ?>
                </select>
              </div>
              -->
            <div class="row justify-content-center mt-5 mb-3">
              <div class="col-12 margin-tb">
                <div class="register row">
                  <div class="mx-auto">
                    <span>Already registered?</span> <a href="<?php echo home_url( '/login/' ); ?>" class="btn btn-sm btn-dark rounded">Login</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Vote Modal -->
<div class="modal fade" id="voteObserverModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
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
          <h2 id="modal-title-vote" class="text-center px-5"></h2>

          <div class="progress mt-3" style="height: .5rem;">
            <div class="progress-bar bg-success h-100" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <div class="d-flex justify-content-between">
            <div id="votes_count">50</div>
            <div>1000</div>
          </div>

          <p id="modal-desc-vote" class="text-center"></p>

          <div class="newsletter-form">

            <h4 class="text-center">Please register to vote!</h4>

            <p class="text-center">If we get 1,000 votes, we'll bring on expert writers and launch this newsletter for you!</p>
            <div class="wp-social-login-provider-list-wrap">
              <?php
              // WSL
              do_action( 'wordpress_social_login' );

              // Apple
              $_SESSION['apple_signin_state'] = bin2hex(random_bytes(5));

              $apple['client_id'] = 'com.thebrag';
              $apple['redirect_uri'] = home_url( '/sign-in-with-apple/');

              $authorize_url = 'https://appleid.apple.com/auth/authorize'.'?'.http_build_query([
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

            <!-- <h4 class="text-center">Register</h4> -->

            <form action="" method="post" id="observer-vote-form" name="observer-vote-form" class="justify-content-center">

              <div class="observer-sub-form justify-content-center bg-dark">
                <input type="hidden" name="list" id="modal-list-vote">
                <input type="email" name="email" class="email form-control" placeholder="Your email" value="" style="border-radius: 5px; padding: 25px 15px;">
                <input type="submit" value="Vote" name="vote" class="button btn rounded" style="background-color: #343a40;">
                <div class="loading mx-3" style="display: none;"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>
              </div>

              <!--
              <div class="mt-3">
                <div class="input-group mb-2 mr-sm-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text">Email</div>
                  </div>
                  <input type="email" name="email" class="email form-control" placeholder="Email address" value="">
                </div>
              </div>

              <div class="mt-3 mb-2">
                <input type="hidden" name="list" id="modal-list-vote">

                <input type="submit" value="VOTE" name="vote" class="button btn btn-dark rounded form-control">
                <div class="loading" style="display: none;"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>
              </div>
            -->

              <div class="alert alert-info d-none js-msg-vote"></div>
              <div class="alert alert-danger d-none js-errors-vote"></div>
            </form>


              <!--
              <div class="mt-3 row">
                <div class="col-md-6">
                  <h4>First Name</h4>
                  <input type="text" name="first_name" class="form-control" placeholder="First Name" value="">
                </div>

                <div class="col-md-6">
                  <h4>Last Name</h4>
                  <input type="text" name="last_name" class="form-control" placeholder="Last Name" value="">
                </div>
              </div>

              <div class="mt-3">
                <h4>Birthday</h4>
                <div class="input-group">
                  <select aria-label="Day" name="birthday_day" id="day" title="Day" class="form-control">
                    <option value="0">Day</option>
                    <?php // for( $birthday_day = 1; $birthday_day <= 31; $birthday_day++ ) : ?>
                      <option value="<?php // echo $birthday_day; ?>"><?php // echo $birthday_day; ?></option>
                    <?php // endfor; ?>
                  </select>

                  <select aria-label="Month" name="birthday_month" id="month" title="Month" class="form-control">
                    <option value="0">Month</option>
                    <?php
                    // $months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
                    ?>
                    <?php // foreach( $months as $month_no => $month ) : ?>
                      <option value="<?php // echo $month_no; ?>"><?php // echo $month; ?></option>
                    <?php // endforeach; ?>
                  </select>

                  <select aria-label="Year" name="birthday_year" id="year" title="Year" class="form-control">
                    <option value="0">Year</option>
                    <?php // for( $birthday_year = date('Y'); $birthday_year >= date('Y') - 115; $birthday_year-- ) : ?>
                      <option value="<?php // echo $birthday_year; ?>"><?php // echo $birthday_year; ?></option>
                    <?php // endfor; ?>
                  </select>
                </div>
              </div>

              <div class="mt-3">
                <h4>State</h4>
                <select aria-label="State" name="state" id="state" title="State" class="form-control">
                  <option value="0">State</option>
                  <?php // foreach( $states as $state_abbr => $state ) : ?>
                    <option value="<?php // echo $state_abbr; ?>"><?php // echo $state; ?></option>
                  <?php // endforeach; ?>
                </select>
              </div>
              -->

            <div class="row justify-content-center mt-5 mb-3">
              <div class="col-12 margin-tb">
                <div class="register row">
                  <div class="mx-auto">
                    <span>Already registered?</span> <a href="<?php echo home_url(); ?>/login/" class="btn btn-sm btn-dark rounded">Login</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
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
    border: 1px solid rgba(0,0,0,.1);
    border-radius: 50%;
    font-size: 1rem;
    padding: 10px;
    box-sizing: border-box;
    background: #fff;
    color: rgba(0,0,0,.5);
}

.modal-dialog {
  width: 600px !important;
  max-width: 100% !important;
  margin: auto !important;
}

@media (max-width: 640px) {
  .wp-social-login-provider-list-wrap, .wp-social-login-provider-list {
    /* flex-flow: column; */
  }
  a.wp-social-login-provider:after {
    display:none;
  }

  .modal-dialog {
    width: 100% !important;
  }
}


</style>
