<?php extract($args); ?>
<div id="<?php echo isset($container_id) ? $container_id : 'observer-list-top'; ?>" class="observer-list p-2 bg-dark text-white" <?php echo isset($show_container) && $show_container ? '' : 'style="display: none;"'; ?>>
  <h2><strong>Pick your niche.</strong> Follow the topics you want.</h2>
  <p class="font-primary desc">Tick to subscribe, untick to unsubscribe from any newsletters below:</p>

  <?php
  $my_sub_lists = [];

  if (is_user_logged_in()) :
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $my_subs = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND status = 'subscribed' ");
    $my_sub_lists = wp_list_pluck($my_subs, 'list_id');
  endif;

  $lists_query = "
    SELECT l.id, l.title, l.slug, l.description, l.image_url, l.frequency, sub_count,
    CASE l.frequency
      WHEN 'Daily' THEN 1
      WHEN 'Weekly' THEN 2
      WHEN 'Fortnightly' THEN 3
      WHEN 'Breaking News' THEN 4
    END frequency_weight
    FROM {$wpdb->prefix}observer_lists l
    WHERE
      l.status = 'active'
      AND
      l.related_site = 'thebrag.com'
    ORDER BY
      l.sub_count DESC 
    ";
  $lists = $wpdb->get_results($lists_query);

  if ($lists) :
  ?>
    <div class="d-flex flex-row flex-wrap justify-content-start topics <?php echo is_user_logged_in() ? 'topics-active' : ''; ?>">
      <?php foreach ($lists as $index => $list) : ?>
        <a href="<?php echo home_url("/observer/{$list->slug}"); ?>" class="d-flex <?php echo in_array($list->id, $my_sub_lists) ? 'subscribed' : ''; ?>" target="_blank" data-list="<?php echo $list->id; ?>">
          <span class="text-primary tick mr-1"><img src="<?php echo ICONS_URL; ?>check.svg" width="16" height="16" alt="-"></span>
          <span class="text-primary plus mr-1"><img src="<?php echo ICONS_URL; ?>plus-tb.svg" width="16" height="16" alt="+"></span>
          <span class="text-primary plus-hover mr-1"><img src="<?php echo ICONS_URL; ?>plus.svg" width="16" height="16" alt="+"></span>
          <span><?php echo !in_array($list->id, [4,]) ? trim(str_ireplace('Observer', '', $list->title)) : trim($list->title); ?></span>
        </a>
      <?php endforeach; // For Each $list in $lists 
      ?>
    </div>
  <?php
  endif; // If $lists
  ?>
</div>