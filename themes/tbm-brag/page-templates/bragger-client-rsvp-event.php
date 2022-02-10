<?php
/*
* Template Name: Bragger Client Event (RSVP)
*/

$event_id = isset($_GET['id']) ? absint($_GET['id']) : null;
$guid = isset($_GET['guid']) ? trim($_GET['guid']) : null;

$event = $wpdb->get_row("SELECT
  e.*, i.`status` invite_status
FROM {$wpdb->prefix}client_club_event_invites i
JOIN {$wpdb->prefix}client_club_events e ON i.`event_id` = e.`id`
WHERE i.`event_id` = '{$event_id}' AND i.`guid` = '{$guid}'
LIMIT 1
");

$current_url = home_url(add_query_arg([], $GLOBALS['wp']->request));

get_header('bragger-client-club');

if ($event) {
  add_action('wp_footer', function () use ($event_id, $guid) {
?>
    <script>
      jQuery(document).ready(function($) {
        $('.btn-change-rsvp').on('click', function(e) {
          e.preventDefault();
          $(this).hide();
          $('.rsvp-response').addClass('d-none');
          $('.rsvp-wrap').removeClass('d-none').addClass('d-flex flex-column flex-md-row align-items-start');
        });
        $('.btn-rsvp.active').on('click', function(e) {
          e.preventDefault();

          if (!$(this).hasClass('active'))
            return;

          $('.rsvp-response').text('').addClass('d-none');
          $('.btn-rsvp').addClass('muted').removeClass('active');
          var response = $(this).data('response');
          $.post({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
              action: 'response_to_bragger_client_event',
              event_id: '<?php echo $event_id; ?>',
              guid: '<?php echo $guid; ?>',
              response: response,
            }
          }).success(function(res) {
            console.log(res.data);
            if (res.success) {
              $('.rsvp-wrap').hide();
            }
            $('.rsvp-response').text(res.data).removeClass('d-none');
            return;
            return;
          }).done(function() {});
        });
      });
    </script>
<?php
  });
}
?>

<div class="hero-wrap">
  <div class="text-primary content container p-r h-100 d-flex flex-wrap hero">
    <div class="p-3" style="z-index: 3;">
      <div class="d-flex col-12">
        <div class="logo-wrap">
          <a href="https://thebrag.com/media" target="_blank"><img src="https://cdn.thebrag.com/tbm/The-Brag-Media-light.svg" width="200" height="19" alt="The Brag Media" title="The Brag Media" loading="lazy"></a>
        </div>
      </div>
      <div class="col-12">
        <?php if ($event) : ?>
          <h1 class="content-heading my-3">
            <?php echo $event->title; ?>
          </h1>
          <h2><?php echo $event->location; ?></h2>
          <?php if (!is_null($event->event_date)) : ?>
            <h2><?php echo date('d M, Y', strtotime($event->event_date)); ?></h2>
          <?php endif; ?>
        <?php else : ?>
          <h1 class="content-heading">
            Bragger<br>Client<br>Club
          </h1>
        <?php endif; ?>
      </div>
      <?php if ($event) : ?>
        <div class="d-flex justify-content-start align-items-start pt-3 pt-md-4">
          <div class="rsvp-wrap <?php echo in_array($event->invite_status, ['yes', 'no']) ? 'd-none' : 'd-flex flex-column flex-md-row align-items-start'; ?>">
            <div>Going?</div>
            <div class="mt-3 mt-md-0">
              <buttton class="text-white btn btn-rsvp active yes mr-1 ml-md-2" data-response="yes">Yes</buttton>
              <buttton class="text-white btn btn-rsvp active no ml-1" data-response="no">No</buttton>
            </div>
          </div>
          <div class="rsvp-response <?php echo !in_array($event->invite_status, ['yes', 'no']) ? 'd-none' : ''; ?>">
            <?php echo 'yes' == $event->invite_status ? 'Thank you, see you there!' : ('no' == $event->invite_status ? 'You wil be missed!' : 'Thank you!'); ?>
            <?php if (in_array($event->invite_status, ['yes', 'no'])) : ?>
              <div class="mt-3">
                <buttton class="btn-change-rsvp active">I changed my mind</buttton>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php else : ?>
        <?php if (!is_user_logged_in()) : ?>
          <div class="col-12 pt-3 pt-md-4">
            <div class="login">
              <a href="<?php echo esc_url(wp_login_url($current_url)); ?>" class="text-white btn-login">Get started</a>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
if (is_user_logged_in()) :
  $user_id = get_current_user_id();
  if ($wpdb->get_var("SELECT COUNT(1) FROM {$wpdb->prefix}client_club_members WHERE `user_id` = '{$user_id}' AND `status` = 'active'")) :
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
