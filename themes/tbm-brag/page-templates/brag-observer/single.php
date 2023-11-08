<style>
  /* #header, */
  #header-logo-wrap,
  #header-submenu,
  #footer-frequency-details {
    display: none !important;
  }

  #header-submenu-wrap {
    flex: 0 0 100% !important;
    max-width: 100% !important;
  }

  #header,
  #masthead {
    background-color: transparent !important;
    border-color: transparent !important;
  }

  #footer-copyright {
    width: 100% !important;
    text-align: center !important;
    flex: 0 0 100% !important;
    max-width: 100% !important;
  }

  #footer-socials {
    justify-content: center !important;
  }

  #main {
    margin-top: 0 !important;
    /* padding-left: 0 !important;
  padding-right: 0 !important; */
  }

  #main #content {
    padding-bottom: 0 !important;
  }

  #footer {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
  }

  .spinner {
    width: 25px !important;
    height: 25px !important;
    margin: 10px auto !important;
  }

  .spinner .double-bounce1,
  .spinner .double-bounce2 {
    background-color: #fff;
  }
</style>
<?php
$list = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}observer_lists WHERE slug = '{$observer_slug}' LIMIT 1");
if (!$list)
  return;

$_SESSION['ReturnToUrl'] = home_url('/observer/' . $list->slug . '/');

$hero_image_url = !is_null($list->hero_image_url) && '' != $list->hero_image_url ? $list->hero_image_url : $list->image_url;

$refer_code = isset($_GET['rc']) ? sanitize_text_field($_GET['rc']) : null;
?>

<div class="observer-single row d-md-flex align-items-center2">
  <div class="col-md-7 px-0 py-5 mt-5 mt-md-0 h-100">
    <div class="d-md-flex align-items-center h-100 justify-content-start mx-lg-5 mx-md-3 ml-2" style="max-width: 100%;">
      <div class="py-6">
        <a href="<?php echo home_url('/observer/'); ?>">
          <img class="observer-logo mx-4" src="https://cdn.thebrag.com/observer/The-Brag-Observer-300px.jpg">
        </a>
        <h1 class="mx-4 text-dark"><?php echo $list->title; ?></h1>
        <h3 class="subheader mx-4 mb-4"><?php echo ($list->description); ?></h3>
        <div class="mx-4"><?php echo !is_null($list->intro_text) ? wpautop($list->intro_text) : ''; ?></div>

        <div class="btn-observer-sub mt-0" style="width: auto;">
          <?php if ('active' == $list->status) { // Active 
          ?>
            <?php if (in_array($list->id, $my_sub_lists)) { // If already subscribed
              $share_url = $list->slug ? home_url('/observer/' . $list->slug . '/') : home_url('/observer/');
            ?>
              <a class="btn btn-primary btn-lg btn-share mx-1" href="http://www.facebook.com/share.php?u=<?php echo $share_url; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>

              <a class="btn btn-info btn-lg btn-share mx-1" href="https://twitter.com/share?url=<?php echo $share_url; ?>&amp;text=<?php echo urlencode($list->title); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
            <?php } else { // Not subscribed 
            ?>
              <p>It's free to subscribe and you can unsubscribe any time you like.</p>

              <?php if (is_user_logged_in()) : ?>

                <button type="button" class="btn btn-lg btn-danger rounded btn-subscribe-observer-l py-2 mt-3" data-target="#subscribeobserverModal" data-topic="<?php echo $list->title; ?>" data-list="<?php echo $list->id; ?>" data-desc="<?php echo $list->description; ?>">
                  <div><i class="fa fa-envelope mr-2"></i> <span class="btn-text">Subscribe</span></div>
                </button>

              <?php else : // Not logged in 
              ?>

                <div style="width: 450px; max-width: 100%;">
                  <form action="" method="post" id="observer-subscribe-form2" name="observer-subscribe-form" class="">
                    <div class="observer-sub-form justify-content-center">
                      <?php if (!is_null($refer_code)) : ?>
                        <input type="hidden" name="rc" value="<?php echo $refer_code; ?>">
                      <?php endif; // If $refer_code is set 
                      ?>
                      <input type="hidden" name="list" id="modal-list-subscribe2" value="<?php echo $list->id; ?>">
                      <input type="email" name="email" id="observer-sub-email" class="email form-control" placeholder="Your email" value="<?php echo isset($_GET['email']) ? esc_attr($_GET['email']) : ''; ?>" required>
                      <input type="submit" value="Subscribe" name="subscribe" class="button btn rounded">
                      <div class="loading mx-3" style="display: none;">
                        <div class="spinner">
                          <div class="double-bounce1"></div>
                          <div class="double-bounce2"></div>
                        </div>
                      </div>
                    </div>

                    <div class="alert alert-info d-none js-msg-subscribe"></div>
                    <div class="alert alert-danger d-none js-errors-subscribe"></div>
                  </form>

                  <div class="text-right">
                    <!-- <button type="button" class="btn text-dark rounded btn-subscribe-observer py-2 mt-3" data-target="#subscribeobserverModal" data-topic="<?php echo $list->title; ?>" data-list="<?php echo $list->id; ?>" data-desc="<?php echo $list->description; ?>" data-apple="<?php $apple_signin_state = base64_encode(serialize(['list_id' => $list->id, 'code' => md5(time() . 'tbm')]));
                                                                                                                                                                                                                                                                                            echo $apple_signin_state; ?>"> -->
                    <?php $current_url = home_url(add_query_arg([], $GLOBALS['wp']->request)); ?>
                    <a href="<?php echo esc_url(wp_login_url($current_url)); ?>" class="btn text-dark rounded btn-subscribe-observer py-2 mt-3">
                      <div class="d-flex flex-row align-items-center">
                        <span class="btn-text mr-2">or signup via</span>
                        <div class="" style="margin-top: .15rem;"><i class="fab fa-lg fa-facebook-f" style="color: #3b5998;"></i></div>
                        <div class="mx-2">
                          <img src="<?php echo get_template_directory_uri(); ?>/images/google.svg" style="width: 1.1rem">
                        </div>
                        <div class="" style="margin-top: .15rem;"><i class="fab fa-lg fa-apple"></i></div>
                      </div>
                    </a>
                    <!-- </button> -->
                  </div>
                </div>
              <?php endif; // If User logged in 
              ?>

              <div class="loading" style="display: none;">
                <div class="spinner">
                  <div class="double-bounce1"></div>
                  <div class="double-bounce2"></div>
                </div>
              </div>
            <?php } // If Subscribed / Not subscribed 
            ?>
            <?php } else if ('soon' == $list->status) { // Coming Soon
            $list->votes_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}observer_votes v WHERE v.list_id = {$list->id}");
            if (in_array($list->id, $my_vote_lists)) { // Already voted
              $share_url = $list->slug ? home_url('/observer/' . $list->slug . '/') : home_url('/observer/');
            ?>
              <a class="btn btn-primary btn-lg btn-share mx-1" href="http://www.facebook.com/share.php?u=<?php echo $share_url; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>

              <a class="btn btn-info btn-lg btn-share mx-1" href="https://twitter.com/share?url=<?php echo $share_url; ?>&amp;text=<?php echo urlencode($list->title); ?>" target="_blank"><i class="fa fa-twitter"></i></a>
            <?php } else { // Not voted yet 
            ?>

              <?php if (is_user_logged_in()) : ?>

                <button type="button" class="btn btn-danger btn-lg rounded btn-vote-observer-l py-2 mt-3" data-target="#voteobserverModal" data-topic="<?php echo $list->title; ?>" data-list="<?php echo $list->id; ?>" data-desc="<?php echo $list->description; ?>" data-votes="<?php echo $list->votes_count; ?>">
                  <div><i class="fa fa-thumbs-up mr-2"></i> <span class="btn-text">Vote</span></div>
                </button>

              <?php else : // Not logged in 
              ?>

                <form action="" method="post" id="observer-vote-form2" name="observer-vote-form" class="">
                  <div class="observer-sub-form justify-content-center">
                    <input type="hidden" name="list" id="modal-list-vote2" value="<?php echo $list->id; ?>">
                    <input type="email" name="email" id="observer-vote-email" class="email form-control" placeholder="Your email" value="" required>
                    <input type="submit" value="Vote" name="vote" class="button btn rounded">
                    <div class="loading mx-3" style="display: none;">
                      <div class="spinner">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                      </div>
                    </div>
                  </div>

                  <div class="alert alert-info d-none js-msg-vote"></div>
                  <div class="alert alert-danger d-none js-errors-vote"></div>
                </form>

                <div class="text-right">
                  <button type="button" class="btn text-dark rounded btn-vote-observer py-2 mt-3" data-target="#voteObserverModal" data-topic="<?php echo $list->title; ?>" data-list="<?php echo $list->id; ?>" data-desc="<?php echo $list->description; ?>" data-votes="<?php echo $list->votes_count; ?>" data-apple="<?php $apple_signin_state = base64_encode(serialize(['list_id' => $list->id, 'code' => md5(time() . 'tbm')]));
                                                                                                                                                                                                                                                                                                                          echo $apple_signin_state; ?>">
                    <div class="d-flex flex-row align-items-center">
                      <span class="btn-text mr-2">or signup via</span>
                      <div class="" style="margin-top: .15rem;"><i class="fab fa-lg fa-facebook-f" style="color: #3b5998;"></i></div>
                      <div class="mx-2">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/google.svg" style="width: 1.1rem">
                      </div>
                      <div class="" style="margin-top: .15rem;"><i class="fab fa-lg fa-apple"></i></div>
                    </div>
                  </button>
                </div>

              <?php endif; // If User logged in 
              ?>
            <?php } // If Voted / Not Voted
            ?>

            <?php
            $vote_target = 5000;
            $vote_progress = $list->votes_count * 100 / $vote_target;
            ?>
            <div>
              <div class="progress mt-3" style="height: .5rem; max-width: 100%; margin: auto;">
                <div class="progress-bar bg-success h-100" role="progressbar" style="width: <?php echo $vote_progress; ?>%;" aria-valuenow="<?php echo $vote_progress; ?> " aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <div class="d-flex justify-content-between" style=" max-width: 100%; margin: auto;">
                <div class="votes_count"><?php echo $list->votes_count; ?></div>
                <div class="votes_target"><?php echo $vote_target; ?></div>
              </div>
            </div>
          <?php } // Active OR Coming Soon 
          ?>
        </div><!-- .btn-observer-sub -->
      </div>
    </div>

    <div class="menu-pubs menu-network mt-5 mb-3 ml-lg-5 ml-md-3 mx-4 d-flex align-items-start">

      <ul class="nav d-flex align-items-center justify-content-start">
        <?php
        $brands_network = brands_network();
        ksort($brands_network);
        $exclude_brands = [];
        if ('entertainment-biz' == $observer_slug) {
          $exclude_brands = [
            'dbu',
            'crunchyroll',
            'enthusiast',
            'funimation',
            'gamelancer',
            'kidoodle',
            'lwa',
            'sheknows',
            'sportico',
            'spy',
            'toongoggles',
          ];
        }
        foreach (array_merge(brands(), $brands_network) as $brand => $brand_details) :
          if (in_array($brand, $exclude_brands))
            continue;
        ?>
          <li class="nav-item">
            <a href="<?php echo $brand_details['link']; ?>" title="<?php echo $brand_details['title']; ?>" target="_blank" class="nav-link my-2">
              <img src="https://images-r2.thebrag.com/common/pubs/<?php echo $brand_details['logo_name']; ?>.jpg" alt="<?php echo $brand_details['title']; ?>" class="lazyload" style="height: <?php echo 'lwa' == $brand ? '40' : '25'; ?>px;">
            </a>
          </li><!-- Rolling Stone Australia -->
        <?php endforeach; ?>
      </ul>
    </div><!-- .menu-network -->
  </div>

  <?php if (!is_null($hero_image_url)) : ?>
    <div class="col-md-5 p-0 bg-danger d-none d-md-block" style="min-height: 100vh;">
      <div class="d-md-flex align-items-center h-md-100 justify-content-center h-100 observer-hero" style="min-height: 100vh; background-image: url(<?php echo $hero_image_url; ?>); background-position: <?php echo !is_null($list->hero_image_url) && '' != $list->hero_image_url ? 'left 0%' : 'center 0%'; ?>;">
      </div>
    </div>
  <?php endif; ?>
</div>

<?php if (!is_null($hero_image_url)) : ?>
  <div class="row">
    <div class="col-12 p-0 d-block d-md-none" style="height: 50vh;">
      <div class="d-md-flex align-items-center h-100 justify-content-center h-100 observer-hero" style="background-position: <?php echo !is_null($list->hero_image_url) && '' != $list->hero_image_url ? '10vw 0%' : 'center 0%'; ?>; background-image: url(<?php echo $hero_image_url; ?>)">
      </div>
    </div>
  </div>
<?php endif; ?>