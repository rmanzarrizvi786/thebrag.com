<?php
/*
* Template Name: Bragger Client Club (Index)
*/

get_header('bragger-client-club');

$current_url = home_url(add_query_arg([], $GLOBALS['wp']->request));

/**
 * Update DB as joined if already logged in as invitee
 */
$current_user = wp_get_current_user();
if ($wpdb->get_var("SELECT COUNT(1) FROM {$wpdb->prefix}client_club WHERE `email` = '{$current_user->user_email}' AND `status` = 'invited' LIMIT 1")) {
  $wpdb->update(
    $wpdb->prefix . 'client_club',
    [
      'status' => 'joined',
      'user_id' => $current_user->ID,
      'joined_at' => current_time('mysql')
    ],
    [
      'email' => $current_user->user_email
    ],
    ['%s', '%d', '%s'],
    ['%s']
  );
}
?>

<div class="hero-wrap">
  <div class="text-primary content container p-r h-100 d-flex flex-wrap hero">
    <div class="py-3" style="z-index: 3;">
      <div class="d-flex col-12">
        <div class="logo-wrap">
          <a href="https://thebrag.com/media" target="_blank"><img src="https://cdn.thebrag.com/tbm/The-Brag-Media-light.svg" width="200" height="19" alt="The Brag Media" title="The Brag Media" loading="lazy"></a>
        </div>
      </div>
      <div class="col-12">
        <h1 class="content-heading">
          Bragger<br>Client<br>Club
        </h1>
      </div>
      <?php if (!is_user_logged_in()) : ?>
        <div class="col-12 pt-3 pt-md-4">
          <div class="login mx-2 mx-md-0">
            <a href="<?php echo esc_url(wp_login_url($current_url)); ?>" class="text-white btn-login">Get started</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
if (is_user_logged_in()) :
  $user_id = get_current_user_id();
  if ($wpdb->get_var("SELECT COUNT(1) FROM {$wpdb->prefix}client_club WHERE `user_id` = '{$user_id}' AND `status` = 'joined'")) :
    $lists_query = "SELECT l.id, l.title, l.description, l.image_url, l.frequency
          FROM {$wpdb->prefix}observer_lists l
          WHERE l.status = 'active'
          ORDER BY l.sub_count DESC
        ";
    $lists = $wpdb->get_results($lists_query);
    if ($lists) :
      $my_sub_lists = [];
      $my_vote_lists = [];
      $my_subs = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND status = 'subscribed' ");
      $my_sub_lists = wp_list_pluck($my_subs, 'list_id');
?>
      <div class="mt-3">
        <h2 class="text-center text-primary">Brag Observer Newsletters</h2>
        <h4 class="text-center text-primary">Tick to subscribe, untick to unsubscribe from any newsletters below:</h4>
        <div class="row justify-content-start align-items-stretch">
          <!-- Tone Deaf Tastemakers -->
          <?php
          if (!isset($q_ids)) :
            $tastemaker = $wpdb->get_row("SELECT l.id, l.title, l.slug, l.description, l.image_url, l.frequency, sub_count FROM {$wpdb->prefix}observer_lists l WHERE l.id = '48' ");
            if ($tastemaker) :
          ?>
              <div class="col-lg-2 col-md-4 col-6 my-2 px-1 topic topic-tastemakers">
                <label class="p-r text-center d-flex flex-column justify-content-between h-100 topic-inner sub-unsub <?php echo in_array($tastemaker->id, $my_sub_lists) ? 'subscribed' : ''; ?>" style="border: 1px solid #ccc; padding: .5rem; cursor: pointer; ">
                  <div class="d-inline text-center text-white text-uppercase bg-danger p-1" style="font-size: 125%; white-space: nowrap; position: absolute; top: -1rem; left: 50%; transform: translateX(-50%); z-index: 1;">&#9733; VIP &#9733;</div>
                  <div class="list-info">
                    <figure class="img-wrap rounded-circle2">
                      <img alt="" src="<?php echo $tastemaker->image_url; ?>" class="rounded-circle2" width="">
                    </figure>
                    <h3 class="text-white"><?php echo $tastemaker->title; ?></h3>
                    <div class="text-white desc"><?php echo wpautop($tastemaker->description); ?></div>
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
                <div class="col-lg-2 col-md-4 col-6 my-2 px-1 topic">
                  <label class="text-center d-flex flex-column justify-content-between h-100 sub-unsub <?php echo in_array($list->id, $my_sub_lists) ? 'subscribed' : ''; ?>" style="border: 1px solid #ccc; padding: .5rem; cursor: pointer; ">
                    <div class="list-info">
                      <figure class="img-wrap rounded-circle2">
                        <img alt="" src="<?php echo $list_image_url; ?>" class="rounded-circle2" width="">
                        <div class="tags text-center text-white bg-danger text-uppercase"><?php echo $list->frequency ?: 'Breaking News'; ?></div>
                      </figure>
                      <h3><?php
                          echo !in_array($list->id, [4,]) ? trim(str_ireplace('Observer', '', $list->title)) : trim($list->title);
                          ?></h3>
                      <div class="desc"><?php echo wpautop($list->description); ?></div>
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
            <div class="col-lg-2 col-md-4 col-6 my-2 px-1 topic">
              <label class="text-center d-flex flex-column justify-content-between h-100 sub-unsub <?php echo in_array($list->id, $my_sub_lists) ? 'subscribed' : ''; ?>" style="border: 1px solid #ccc; padding: .5rem; cursor: pointer; ">
                <div class="list-info">
                  <figure class="img-wrap rounded-circle2">
                    <img alt="" src="<?php echo $list_image_url; ?>" class="rounded-circle2" width="">
                    <div class="tags text-center text-white bg-danger text-uppercase"><?php echo $list->frequency ?: 'Breaking News'; ?></div>
                  </figure>
                  <h3><?php
                      echo !in_array($list->id, [4,]) ? trim(str_ireplace('Observer', '', $list->title)) : trim($list->title);
                      ?></h3>
                  <div class="desc"><?php echo wpautop($list->description); ?></div>
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
      </div>
<?php
    endif; // If $lists
  endif; // If joined client clib
endif; // If logged in
?>
<?php
get_footer('bragger-client-club');
