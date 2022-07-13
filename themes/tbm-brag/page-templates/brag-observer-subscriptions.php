<?php /* Template name: Brag Observer Subscriptions */

$category_slug = $wp_query->get('category_slug');

$temp_user = false;
if (isset($_GET['oc'])) {
  $data = @unserialize(base64_decode($_GET['oc']));
  if ($data) {
    $oc_token = get_user_meta($data['id'], 'oc_token', true);
    if ($oc_token == $data['oc_token']) {
      $user_id = $data['id'];
    }
    $temp_user = true;
  }
}

if (!$temp_user) {
  if (!is_user_logged_in()) :
    wp_redirect(home_url('/wp-login.php?redirect_to=') . urlencode(home_url('/observer-subscriptions/')));
    exit;
  endif;

  $current_user = wp_get_current_user();
  $user_id = $current_user->ID;
}

if (isset($_GET['a'])) {
  if ('unsub-all' == trim($_GET['a'])) {
    $update_data = [
      'status' => 'unsubscribed',
      'status_mailchimp' => NULL,
      'unsubscribed_at' => current_time('mysql'),
    ];
    $wpdb->update(
      $wpdb->prefix . 'observer_subs',
      $update_data,
      [
        'user_id' => $user_id,
      ]
    );

    $task = 'update_newsletter_interests';
    $cron = new Cron();
    if (!$cron->getActiveBrazeQueueTask($user_id, $task)) {
      $cron->addToBrazeQueue($user_id, $task);
    }

    wp_redirect(home_url('/observer-subscriptions/'));
    exit;
  }
}

$my_sub_lists = [];
$my_vote_lists = [];
$my_subs = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND status = 'subscribed' ");
$my_sub_lists = wp_list_pluck($my_subs, 'list_id');

$my_votes = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_votes WHERE user_id = '{$current_user->ID}'");
$my_vote_lists = wp_list_pluck($my_votes, 'list_id');

/*
if ( isset( $_POST['save-subscriptions'] ) ) {
  if ( isset( $_POST['lists'] ) ) {
    $post_lists = $_POST['lists'];

    $unsubs = array_diff( $my_sub_lists, $post_lists );

    foreach ( $unsubs as $unsub ) :
      $wpdb->update( $wpdb->prefix . 'observer_subs',
        [
          'status' => 'unsubscribed',
          'unsubscribed_at' => current_time( 'mysql' ),
        ],
        [
          'user_id' => $user_id,
          'list_id' => $unsub,
        ]
      );
    endforeach;

    $my_unsub_lists = [];
    $my_unsubs = $wpdb->get_results( "SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND status = 'unsubscribed' " );
    $my_unsub_lists = wp_list_pluck( $my_unsubs, 'list_id' );

    $newsubs = array_diff( $post_lists, $my_sub_lists );

    foreach ( $newsubs as $sub ) {
      if ( ! in_array( $sub, $my_unsub_lists ) ) {
        $wpdb->insert( $wpdb->prefix . 'observer_subs',
          [
            'user_id' => $user_id,
            'list_id' => $sub,
            'status' => 'subscribed',
            'status_mailchimp' => NULL,
            'subscribed_at' => current_time( 'mysql' ),
          ]
        );
      } else {
        $wpdb->update( $wpdb->prefix . 'observer_subs',
          [
            'status' => 'subscribed',
            'status_mailchimp' => NULL,
            'subscribed_at' => current_time( 'mysql' ),
          ],
          [
            'user_id' => $user_id,
            'list_id' => $sub,
          ]
        );
      }
    }

  } else { // Lists are not posted
    $wpdb->update( $wpdb->prefix . 'observer_subs',
      [
        'status' => 'unsubscribed',
        'status_mailchimp' => NULL,
        'unsubscribed_at' => current_time( 'mysql' ),
      ],
      [
        'user_id' => $user_id,
      ]
    );
  } // If lists are posted

  header( 'Location: ' . home_url( 'observer-subscriptions' ) ); exit;
} // If $_POST
*/

get_template_part('page-templates/brag-observer/header');
?>
<?php
if (isset($_GET['id'])) {
  $q_ids = array_map('absint', $_GET['id']);
}

if ($category_slug) {
  $lists_query = "
SELECT l.id, l.title, l.description, l.image_url, l.frequency FROM
  {$wpdb->prefix}observer_lists l
  JOIN {$wpdb->prefix}observer_list_categories lc
      ON l.id = lc.list_id
    JOIN {$wpdb->prefix}observer_categories c
      ON c.id = lc.category_id
WHERE
  l.status = 'active' AND
  c.slug = '{$category_slug}'
GROUP BY
  l.id
ORDER BY
  l.sub_count DESC
";
} else {
  $lists_query = "
SELECT l.id, l.title, l.description, l.image_url, l.frequency FROM
  {$wpdb->prefix}observer_lists l
WHERE
  l.status = 'active'
";
  if (isset($q_ids) && is_array($q_ids) && !empty($q_ids)) {
    $lists_query .= " AND id IN ( " . implode(',', $q_ids) . ")";
  }
  $lists_query .= "
GROUP BY
  l.id
ORDER BY
  l.sub_count DESC
";
}
$lists = $wpdb->get_results($lists_query);

// $coming_soon_lists = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}observer_lists WHERE status = 'soon'" );

$coming_soon_query = "
  SELECT l.id, l.title, l.slug, l.description, l.image_url, l.frequency, COUNT(v.id) votes_count FROM
    {$wpdb->prefix}observer_lists l
  LEFT JOIN
    {$wpdb->prefix}observer_votes v
      ON l.id = v.list_id
  WHERE
    l.status = 'soon'
  GROUP BY
    l.id
    ORDER BY
      votes_count DESC
  ";

$coming_soon_lists = $wpdb->get_results($coming_soon_query);

$profile_strength = 0;
$current_user = wp_get_current_user();

if (get_user_meta($current_user->ID, 'profile_strength', true)) {
  $profile_strength = get_user_meta($current_user->ID, 'profile_strength', true);
} else {
  if (get_user_meta($current_user->ID, 'first_name', true))
    $profile_strength += 20;

  if (get_user_meta($current_user->ID, 'last_name', true))
    $profile_strength += 20;

  if (get_user_meta($current_user->ID, 'state', true))
    $profile_strength += 20;

  if (get_user_meta($current_user->ID, 'birthday', true))
    $profile_strength += 20;

  if (get_user_meta($current_user->ID, 'gender', true))
    $profile_strength += 20;

  update_user_meta($current_user->ID, 'profile_strength', $profile_strength);
}

if ($profile_strength < 20) {
  $profile_complete_class = 'text-danger';
} else if ($profile_strength <= 40) {
  $profile_complete_class = 'text-warning';
} else if ($profile_strength <= 60) {
  $profile_complete_class = 'text-info';
} else if ($profile_strength <= 80) {
  $profile_complete_class = 'text-primary';
} else if ($profile_strength <= 100) {
  $profile_complete_class = 'text-success';
}

if ($temp_user) {
?>
  <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>">
<?php
}
?>

<div id="update-profile" class="col-12 col-md-6 offset-md-3 mb-2">
  <div class="text-center mx-auto pt-4 mb-3" style="max-width: 100%;">
    <p><strong>Profile Strength: <a href="<?php echo home_url('/profile/'); ?>" class="<?php echo $profile_complete_class; ?>"><?php echo $profile_strength; ?>%</a> complete</strong></p>
    <a href="<?php echo home_url('/profile/'); ?>">
      <div class="progress profile-strength">
        <?php for ($i = 20; $i <= 100; $i += 20) : ?>
          <div class="profile-strength-step<?php echo $i <= $profile_strength ? '-active' : ''; ?>" style="width: 20%; <?php echo $i != 20 ? 'border-left: 2px solid #fff;' : ''; ?>">
            <?php if (0 == $profile_strength && $i == 20) : ?>
              <div style="position: relative;">
                <div class="profile-strength-start d-flex justify-content-center align-items-center <?php echo $profile_complete_class; ?>">
                  <i class="fa fa-check"></i>
                </div>
              </div>
            <?php elseif ($profile_strength != 100 && $i == $profile_strength) : ?>
              <div style="position: relative;">
                <div class="profile-strength-complete d-flex justify-content-center align-items-center <?php echo $profile_complete_class; ?>">
                  <i class="fa fa-check"></i>
                </div>
              </div>
            <?php elseif ($i == 100) : ?>
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

<div class="row content">
  <div class="my-3 col-12 col-md-8 offset-md-2">
    <?php the_content(); ?>
  </div>
</div>

<?php
if ($lists) :
?>
  <div class="row <?php echo isset($q_ids) ? 'justify-content-center' : ''; ?>">

    <!-- Tone Deaf Tastemakers -->
    <?php
    if (!isset($q_ids)) :
      $tastemaker = $wpdb->get_row("SELECT l.id, l.title, l.slug, l.description, l.image_url, l.frequency, sub_count FROM {$wpdb->prefix}observer_lists l WHERE l.id = '48' ");
      if ($tastemaker) :
    ?>
        <div class="col-lg-2 col-md-4 col-6 my-4 px-2 topic topic-tastemakers">
          <label class="text-center d-flex flex-column justify-content-between h-100 topic-inner sub-unsub <?php echo in_array($tastemaker->id, $my_sub_lists) ? 'subscribed' : ''; ?>" style="border: 1px solid #ccc; padding: .5rem; cursor: pointer; ">
            <div class="d-inline text-center text-white text-uppercase bg-danger p-2" style="font-size: 125%; white-space: nowrap; position: absolute; top: -1rem; left: 50%; transform: translateX(-50%); z-index: 1;">&#9733; VIP &#9733;</div>
            <div class="list-info">
              <figure class="img-wrap rounded-circle2">
                <img alt="" src="<?php echo $tastemaker->image_url; ?>" class="rounded-circle2" width="">
              </figure>
              <h3 class="text-white"><?php echo $tastemaker->title; ?></h3>
              <div class="text-white"><?php echo wpautop($tastemaker->description); ?></div>
            </div>
            <div class="list-subscription-action">
              <input type="checkbox" name="lists[]" class="checkbox-list" id="lists_<?php echo $tastemaker->id; ?>" value="<?php echo $tastemaker->id; ?>" <?php echo in_array($tastemaker->id, $my_sub_lists) ? 'checked' : ''; ?>>
              <label for="lists_<?php echo $tastemaker->id; ?>"></label>
              <div class="loading" style="display: none;">
                <div class="spinner">
                  <div class="double-bounce1"></div>
                  <div class="double-bounce2"></div>
                </div>
              </div>
            </div>
          </label>
        </div>
    <?php
      endif; // If $tastemaker
    endif; // If $q_ids is NOT set
    ?>

    <?php
    // My list {{
    if (isset($my_sub_lists) && !empty($my_sub_lists)) :
      $my_lists = $wpdb->get_results("SELECT l.id, l.title, l.slug, l.description, l.image_url, l.frequency, sub_count FROM {$wpdb->prefix}observer_lists l WHERE l.id IN (" . implode(',', $my_sub_lists) . ") and l.status = 'active' ORDER BY sub_count ASC ");
      if ($my_lists) :
        foreach ($my_lists as $list) :
          if (48 == $list->id) // Exclude Tone Deaf Tastemakers
            continue;
          $list_image_url = $list->image_url;
    ?>
          <div class="col-lg-2 col-md-4 col-6 my-4 px-2 topic">
            <label class="text-center d-flex flex-column justify-content-between h-100 sub-unsub <?php echo in_array($list->id, $my_sub_lists) ? 'subscribed' : ''; ?>" style="border: 1px solid #ccc; padding: .5rem; cursor: pointer; ">
              <div class="list-info">
                <figure class="img-wrap rounded-circle2">
                  <img alt="" src="<?php echo $list_image_url; ?>" class="rounded-circle2" width="">
                  <div class="tags text-center text-white bg-danger text-uppercase"><?php echo $list->frequency ?: 'Breaking News'; ?></div>
                </figure>
                <h3><?php
                    echo !in_array($list->id, [4,]) ? trim(str_ireplace('Observer', '', $list->title)) : trim($list->title);
                    ?></h3>
                <?php echo wpautop($list->description); ?>
              </div>
              <div class="list-subscription-action">
                <input type="checkbox" name="lists[]" class="checkbox-list" id="lists_<?php echo $list->id; ?>" value="<?php echo $list->id; ?>" <?php echo in_array($list->id, $my_sub_lists) ? 'checked' : ''; ?>>
                <label for="lists_<?php echo $list->id; ?>"></label>
                <div class="loading" style="display: none;">
                  <div class="spinner">
                    <div class="double-bounce1"></div>
                    <div class="double-bounce2"></div>
                  </div>
                </div>
              </div>
            </label>
          </div>
      <?php
        endforeach; // For Each $list in $my_lists
      endif; // If $my_lists
    endif; // If $my_sub_lists is set and not empty
    // }} My list

    foreach ($lists as $index => $list) :

      if (48 == $list->id || in_array($list->id, $my_sub_lists)) // Exclude Tone Deaf Tastemakers
        continue;

      $list_image_url = $list->image_url;
      ?>
      <div class="col-lg-2 col-md-4 col-6 my-4 px-2 topic">
        <label class="text-center d-flex flex-column justify-content-between h-100 sub-unsub <?php echo in_array($list->id, $my_sub_lists) ? 'subscribed' : ''; ?>" style="border: 1px solid #ccc; padding: .5rem; cursor: pointer; ">
          <div class="list-info">
            <figure class="img-wrap rounded-circle2">
              <img alt="" src="<?php echo $list_image_url; ?>" class="rounded-circle2" width="">
              <div class="tags text-center text-white bg-danger text-uppercase"><?php echo $list->frequency ?: 'Breaking News'; ?></div>
            </figure>
            <h3><?php
                echo !in_array($list->id, [4,]) ? trim(str_ireplace('Observer', '', $list->title)) : trim($list->title);
                ?></h3>
            <?php echo wpautop($list->description); ?>
          </div>
          <div class="list-subscription-action">
            <input type="checkbox" name="lists[]" class="checkbox-list" id="lists_<?php echo $list->id; ?>" value="<?php echo $list->id; ?>" <?php echo in_array($list->id, $my_sub_lists) ? 'checked' : ''; ?>>
            <label for="lists_<?php echo $list->id; ?>"></label>
            <div class="loading" style="display: none;">
              <div class="spinner">
                <div class="double-bounce1"></div>
                <div class="double-bounce2"></div>
              </div>
            </div>
          </div>
        </label>
      </div>
    <?php endforeach; // For Each List 
    ?>
  </div>
<?php endif; // If $lists 
?>


<style>
  #coming-soon::before {
    display: block;
    content: " ";
    margin-top: -100px;
    height: 100px;
    visibility: hidden;
    pointer-events: none;
  }
</style>

<div class="my-5 text-center" id="coming-soon">
  <h1 class="text-center">Coming soon</h1>
  <h4 class="col-md-8 offset-md-2" style="line-height: 1.5">If these newsletters reach their target, we'll bring on expert writers and launch them for you. Vote for all your favorites below:</h4>
</div>

<div class="row">
  <?php foreach ($coming_soon_lists as $list) :
    $list_image_url = $list->image_url;
    if (!$list->image_url || '' == $list->image_url) :
      $list_image_url = null;
    endif; // If image url is not set
  ?>
    <div class="col-lg-2 col-md-4 col-6 my-4 px-2 topic">

      <div class="text-center d-flex flex-column justify-content-between h-100 topic-inner">
        <?php if ($list->slug) : ?>
          <a href="<?php echo home_url('/observer/' . $list->slug . '/'); ?>" class="text-dark">
          <?php endif; ?>
          <div class="list-info">
            <?php if (!is_null($list_image_url)) : ?>
              <figure><img alt="<?php echo $list->title; ?>" src="<?php echo $list_image_url; ?>"></figure>
            <?php endif; ?>
            <h3><?php
                echo !in_array($list->id, [4,]) ? trim(str_ireplace('Observer', '', $list->title)) : trim($list->title);
                ?></h3>
            <?php echo wpautop($list->description); ?>
          </div>
          <?php if ($list->slug) : ?>
          </a>
        <?php endif; ?>
        <div class="list-subscription-action">
          <?php if (in_array($list->id, $my_vote_lists)) :
            $share_url = $list->slug ? home_url('/observer/' . $list->slug . '/') : home_url('/observer/');
          ?>
            <a class="btn btn-default btn-outline-primary btn-share mx-1" href="http://www.facebook.com/share.php?u=<?php echo $share_url; ?>" target="_blank"><i class="fab fa-facebook fa-lg" aria-hidden=true></i></a>

            <a class="btn btn-default btn-outline-info btn-share mx-1" href="https://twitter.com/share?url=<?php echo $share_url; ?>&amp;text=<?php echo urlencode($list->title); ?>" target="_blank"><i class="fab fa-twitter fa-lg" aria-hidden=true></i></a>
          <?php else : ?>
            <button type="button" class="btn btn-dark rounded btn-block btn-vote-observer<?php echo is_user_logged_in() ? '-l' : ''; ?> d-flex justify-content-between py-2" data-target="#voteobserverModal" data-topic="<?php echo $list->title; ?>" data-list="<?php echo $list->id; ?>" data-desc="<?php echo $list->description; ?>" data-votes="<?php echo $list->votes_count; ?>">
              <div><i class="fa fa-thumbs-up mr-2"></i> <span class="btn-text">Vote</span></div>
              <div><i class="fa fa-caret-right"></i></div>
            </button>
            <div class="loading" style="display: none;">
              <div class="spinner">
                <div class="double-bounce1"></div>
                <div class="double-bounce2"></div>
              </div>
            </div>
          <?php endif; ?>
          <?php
          $vote_target = 5000;
          $vote_progress = $list->votes_count * 100 / $vote_target;
          ?>
          <div class="progress mt-3" style="height: .5rem;">
            <div class="progress-bar bg-success h-100" role="progressbar" style="width: <?php echo $vote_progress; ?>%" aria-valuenow="<?php echo $vote_progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <div class="d-flex justify-content-between">
            <div class="votes_count"><?php echo $list->votes_count; ?></div>
            <div class="votes_target"><?php echo $vote_target; ?></div>
          </div>
        </div>
      </div>

    </div>
  <?php endforeach; // For Each list 
  ?>
  <div class="col-lg-2 col-md-4 col-6 my-4 px-2 topic">
    <div class="text-center bg-light text-dark pt-2 pb-3 px-4 rounded" style="border: 1px solid #333;">
      <h3 class="text-center text-dark">Have an idea for a newsletter?</h3>
      <figure class="col-xs-12 text-center">
        <img src="<?php echo get_template_directory_uri(); ?>/images/observer/marketing.svg" alt="" style="width: 90%">
      </figure>
      <a class="btn btn-danger rounded" href="/propose-a-newsletter">Propose a newsletter</a>
    </div>
  </div>
</div>

<div class="text-center py-2" style="color: #ccc;">
  <a href="<?php echo home_url('/observer-subscriptions/'); ?>?a=unsub-all" class="unsubscribe-all" style="color: #ccc;" onClick="return confirm('Are you sure you want to unsubscribe from ALL newsletters?');">Click here</a> to unsubscribe from all newsletters
</div>

<?php
get_template_part('page-templates/brag-observer/footer');
