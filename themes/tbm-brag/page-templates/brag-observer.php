<?php /* Template name: Brag Observer */

if (isset($_GET['login'])) {
} // If $_GET['login'] is set

$my_sub_lists = [];
$my_vote_lists = [];

$category_slug = $wp_query->get('category_slug');
$observer_slug = $wp_query->get('observer_slug');

if (is_user_logged_in()) {

  if (!$observer_slug) {
    $redirect_url = home_url('/observer-subscriptions/');
    if ($category_slug) {
      $redirect_url = home_url("/observer-subscriptions/category/{$category_slug}/");
    }
    wp_redirect($redirect_url);
    exit;
  }

  $current_user = wp_get_current_user();
  $my_subs = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$current_user->ID}' AND status = 'subscribed' ");
  $my_sub_lists = wp_list_pluck($my_subs, 'list_id');

  $my_votes = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_votes WHERE user_id = '{$current_user->ID}'");
  $my_vote_lists = wp_list_pluck($my_votes, 'list_id');
}

$sort_orders = [
  'popular' => 'Popular',
  'a-z' => 'A-Z',
  'frequency' => 'Frequency',
];
$query_sort_order = isset($_GET['sort']) && in_array($_GET['sort'], array_keys($sort_orders)) ? $_GET['sort'] : 'popular';

if ($observer_slug) {
  if (!$wpdb->get_row("SELECT id FROM {$wpdb->prefix}observer_lists WHERE slug = '{$observer_slug}' LIMIT 1")) {
    wp_redirect(home_url('/observer/'));
    exit;
  }
}

// Add custom header for Observer
get_template_part('page-templates/brag-observer/header');

if ($observer_slug) {
  include get_template_directory() . '/page-templates/brag-observer/single.php';
} else {

  if ($category_slug) {
    $lists_query = "
  SELECT
    l.id, l.title, l.slug, l.description, l.image_url, l.frequency, l.sub_count,
    CASE l.frequency
      WHEN 'Daily' THEN 1
      WHEN 'Weekly' THEN 2
      WHEN 'Fortnightly' THEN 3
      WHEN 'Breaking News' THEN 4
    END frequency_weight
  FROM {$wpdb->prefix}observer_lists l
    JOIN {$wpdb->prefix}observer_list_categories lc
      ON l.id = lc.list_id
    JOIN {$wpdb->prefix}observer_categories c
      ON c.id = lc.category_id
  WHERE
    l.status = 'active' AND
    c.slug = '{$category_slug}'
  ";
  } else {
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
  ";
  }

  if ('popular' == $query_sort_order) {
    $lists_query .= " ORDER BY
    l.sub_count DESC ";
  } elseif ('a-z' == $query_sort_order) {
    $lists_query .= " ORDER BY
    l.title ASC ";
  } elseif ('frequency' == $query_sort_order) {
    $lists_query .= " ORDER BY
    frequency_weight ASC ";
  }

  // die( $lists_query );

  $lists = $wpdb->get_results($lists_query);

  if ($category_slug) {
    $coming_soon_query = "
  SELECT l.id, l.title, l.slug, l.description, l.image_url, l.frequency, COUNT(v.id) votes_count FROM
    {$wpdb->prefix}observer_lists l
    JOIN {$wpdb->prefix}observer_list_categories lc
      ON l.id = lc.list_id
    JOIN {$wpdb->prefix}observer_categories c
      ON c.id = lc.category_id
    LEFT JOIN
      {$wpdb->prefix}observer_votes v
      ON l.id = v.list_id
  WHERE
    l.status = 'soon' AND
    c.slug = '{$category_slug}'
  GROUP BY
    l.id
  ";
  } else {
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
  ";
  }

  if ('popular' == $query_sort_order) {
    $coming_soon_query .= "ORDER BY
    votes_count DESC";
  } elseif ('a-z' == $query_sort_order) {
    $coming_soon_query .= "ORDER BY
    l.title ASC";
  } else {
    $coming_soon_query .= "ORDER BY
    votes_count DESC";
  }

  $coming_soon_lists = $wpdb->get_results($coming_soon_query);

  $featured_img_src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
?>

  <nav class="navbar navbar-expand px-0 d-md-none d-block mb-2 pt-3">
    <div class="m-auto d-flex justify-content-center" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?php echo $category_slug || $observer_slug ? 'text-dark' : ' text-white bg-dark'; ?>" href="<?php echo home_url('/observer/'); ?>">All</a>
        </li>
        <?php
        $categories = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}observer_categories");
        if ($categories) :
          foreach ($categories as $category) :
        ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $category_slug && $category->slug == $category_slug ? ' text-white bg-dark' : 'text-dark'; ?>" href="<?php echo home_url('/observer/category/' . $category->slug . '/'); ?>"><?php echo $category->title; ?></a>
            </li>
        <?php
          endforeach;
        endif;
        ?>
      </ul>
    </div>
  </nav>

  <?php
  if ($observer_slug) {
    include get_template_directory() . '/page-templates/brag-observer/single.php';
  } else if ($category_slug) {
    include get_template_directory() . '/page-templates/brag-observer/category.php';
  } else {
  ?>
    <div class="row content mb-3 pt-2 pb-0 px-0 bg-danger" style="position: relative; overflow: hidden;">
      <div class="mt-3 col-12 col-md-8 offset-md-2 text-white" style="/*position: absolute; top: 50%; transform: translateY(-50%);*/ height: 100%;">
        <?php the_content(); ?>
      </div>
      <div class="menu-network nav-network mx-auto mb-2">
        <ul class="nav d-flex align-items-center justify-content-center">
          <li class="nav-item"><a href="https://au.rollingstone.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/brands/Rolling-Stone-Australia-light.png" alt="Rolling Stone Australia" class="lazyload" style="width: 120px"></a></li><!-- Rolling Stone Australia -->

          <li class="nav-item"><a href="https://variety.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/brands/Variety-Australia-light.svg" alt="Variety Australia" class="lazyload"></a></li><!-- Variety -->

          <li class="nav-item"><a href="https://tonedeaf.thebrag.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/brands/Tone-Deaf-light.svg" alt="Tone Deaf" class="lazyload" style="width: 60px"></a></li><!-- Tone Deaf -->

          <li class="nav-item"><a href="https://dontboreus.thebrag.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/brands/Dont-Bore-Us-light.svg" alt="Don't Bore Us" class="lazyload" style="width: 150px;"></a></li><!-- Don't Bore Us -->

          <li class="nav-item"><a href="https://thebrag.com/" target="_blank" class="nav-link"><img src="https://cdn.thebrag.com/tb/The-Brag-light.png" alt="The Brag" style="width: 120px;"></a></li>

          <li class="nav-item"><a href="https://themusicnetwork.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/brands/TMN-light.svg" alt="The Music Network" style="width: 60px;"></a></li>
        </ul>
      </div>
    </div>
  <?php
  }
  ?>

  <?php
  if ($lists) :

    // Move EBO Next to TMN
    $key_ebo = array_search("entertainment-biz", array_column(json_decode(json_encode($lists), TRUE), 'slug'));
    $key_tmn = array_search("the-music-network", array_column(json_decode(json_encode($lists), TRUE), 'slug'));
    $out_ebo = array_splice($lists, $key_ebo, 1);
    array_splice($lists, $key_tmn + 1, 0, $out_ebo);
  ?>
    <div class="row">
      <div class="col-12">
        <ul class="nav nav-pills justify-content-end">
          <?php foreach ($sort_orders as $sort_order => $sort_order_title) : ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $query_sort_order == $sort_order ? ' text-white active bg-dark' : ' text-dark'; ?>" href="<?php echo add_query_arg('sort', $sort_order); ?>"><?php echo $sort_order_title; ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="row <?php echo $category_slug ? 'justify-content-center' : ''; ?>">

      <!-- Tastemaker -->
      <?php
      $tastemaker = $wpdb->get_row("SELECT l.id, l.title, l.slug, l.description, l.image_url, l.frequency, sub_count FROM {$wpdb->prefix}observer_lists l WHERE l.id = '48' ");
      if ($tastemaker) :
      ?>
        <div class="col-lg-2 col-md-4 col-6 my-4 px-2 topic topic-tastemakers">
          <div class="text-center d-flex flex-column justify-content-between h-100 topic-inner">
            <?php if ($tastemaker->slug) : ?>
              <a href="<?php echo home_url('/observer/' . $tastemaker->slug . '/'); ?>" class="text-dark">
              <?php endif; ?>
              <div class="d-inline text-center text-white text-uppercase bg-danger p-2" style="font-size: 125%; white-space: nowrap; position: absolute; top: -1rem; left: 50%; transform: translateX(-50%); z-index: 1;">&#9733; VIP &#9733;</div>
              <div class="list-info">
                <figure class="img-wrap">
                  <img alt="<?php echo $tastemaker->title; ?>" src="<?php echo $tastemaker->image_url; ?>">
                </figure>
                <h3 class="text-white"><?php echo trim($tastemaker->title); ?></h3>
                <div class="text-white"><?php echo wpautop($tastemaker->description); ?></div>
              </div>
              <?php if ($tastemaker->slug) : ?>
              </a>
            <?php endif; ?>
            <div class="list-subscription-action">
              <?php if (in_array($tastemaker->id, $my_sub_lists)) :
                $share_url = $tastemaker->slug ? home_url('/observer/' . $tastemaker->slug . '/') : home_url('/observer/');
              ?>
                <a class="btn btn-default btn-primary btn-share mx-1" href="http://www.facebook.com/share.php?u=<?php echo $share_url; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>

                <a class="btn btn-default btn-info btn-share mx-1" href="https://twitter.com/share?url=<?php echo $share_url; ?>&amp;text=<?php echo urlencode($tastemaker->title); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
              <?php else : ?>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="flex-fill observer-action-wrap">
                    <?php if (is_user_logged_in()) : ?>
                      <button type="button" class="btn btn-dark rounded btn-block btn-subscribe-observer-l d-flex justify-content-between py-2" data-target="#subscribeobserverModal" data-topic="<?php echo $tastemaker->title; ?>" data-list="<?php echo $tastemaker->id; ?>" data-desc="<?php echo $tastemaker->description; ?>" data-apple="<?php $apple_signin_state = base64_encode(serialize(['list_id' => $tastemaker->id, 'code' => md5(time() . 'tbm')]));
                                                                                                                                                                                                                                                                                                                                                echo $apple_signin_state; ?>">
                        <div><i class="fa fa-envelope mr-2 d-none d-xl-inline"></i> <span class="btn-text">Subscribe</span></div>
                      </button>
                    <?php else : ?>
                      <a href="<?php echo wp_login_url(); ?>" class="btn btn-dark rounded btn-block d-flex justify-content-between py-2" target="_blank">Subscribe</a>
                    <?php endif; ?>
                    <div class="loading" style="display: none;">
                      <div class="spinner">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                      </div>
                    </div>
                  </div>

                  <?php if ($tastemaker->slug) : ?>
                    <a href="<?php echo home_url('/observer/' . $tastemaker->slug . '/'); ?>" target="_blank" class="btn text-white px-2 l-list-info">
                      <span class="ico"><i class="fas fa-info-circle"></i></span>
                    </a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; // If $tastemaker 
      ?>
      <?php
      $len_lists = count($lists);
      foreach ($lists as $index => $list) :
        // $list_image_url = $list->image_url;
        if (!$list->image_url || '' == $list->image_url) :
          $list_image_url = null;
        else :
          $list_image_url = $list->image_url; //  bo_img_resize( $list->image_url, 400, 400 );
        endif; // If image url is not set
      ?>
        <?php if (48 != $list->id) : // Exclude Tone Deaf Tastemaker 
        ?>
          <div class="col-lg-2 col-md-4 col-6 my-4 px-2 topic">
            <div class="text-center d-flex flex-column justify-content-between h-100 topic-inner">
              <?php if ($list->slug) : ?>
                <a href="<?php echo home_url('/observer/' . $list->slug . '/'); ?>" class="text-dark">
                <?php endif; ?>
                <div class="list-info">
                  <figure class="img-wrap rounded-circle2">
                    <?php if (!is_null($list_image_url)) : ?>
                      <img alt="<?php echo $list->title; ?>" src="<?php echo $list_image_url; ?>">
                    <?php endif; ?>
                    <div class="tags text-center text-white bg-danger text-uppercase"><?php echo $list->frequency ?: 'Breaking News'; ?></div>
                  </figure>
                  <h3><?php
                      echo !in_array($list->id, [4,]) ? trim(str_ireplace('Observer', '', $list->title)) : trim($list->title);
                      ?></h3>
                  <?php echo wpautop($list->description); ?>
                </div>
                <?php if ($list->slug) : ?>
                </a>
              <?php endif; ?>
              <div class="list-subscription-action">
                <?php if (in_array($list->id, $my_sub_lists)) :
                  $share_url = $list->slug ? home_url('/observer/' . $list->slug . '/') : home_url('/observer/');
                ?>
                  <a class="btn btn-default btn-outline-primary btn-share mx-1" href="http://www.facebook.com/share.php?u=<?php echo $share_url; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>

                  <a class="btn btn-default btn-outline-info btn-share mx-1" href="https://twitter.com/share?url=<?php echo $share_url; ?>&amp;text=<?php echo urlencode($list->title); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                <?php else : ?>

                  <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-fill observer-action-wrap">

                      <?php if (is_user_logged_in()) : ?>
                        <button type="button" class="btn btn-dark rounded btn-block btn-subscribe-observer<?php echo is_user_logged_in() ? '-l' : ''; ?> d-flex justify-content-between py-2" data-target="#subscribeobserverModal" data-topic="<?php echo $list->title; ?>" data-list="<?php echo $list->id; ?>" data-desc="<?php echo $list->description; ?>" data-apple="<?php $apple_signin_state = base64_encode(serialize(['list_id' => $list->id, 'code' => md5(time() . 'tbm')]));
                                                                                                                                                                                                                                                                                                                                                                            echo $apple_signin_state; ?>">
                          <div><i class="fa fa-envelope mr-2 d-none d-xl-inline"></i> <span class="btn-text">Subscribe</span></div>
                          <!-- <div><i class="fa fa-caret-right d-xs-none"></i></div> -->
                        </button>
                      <?php else : ?>
                        <a href="<?php echo wp_login_url($list->slug ? home_url('/observer/' . $list->slug . '/') : home_url('/observer/')); ?>" class="btn btn-dark rounded btn-block d-flex justify-content-between py-2" target="_blank">Subscribe</a>
                      <?php endif; ?>
                      <div class="loading" style="display: none;">
                        <div class="spinner">
                          <div class="double-bounce1"></div>
                          <div class="double-bounce2"></div>
                        </div>
                      </div>
                    </div>

                    <?php if ($list->slug) : ?>
                      <a href="<?php echo home_url('/observer/' . $list->slug . '/'); ?>" target="_blank" class="btn text-dark px-2 l-list-info">
                        <span class="ico"><i class="fas fa-info-circle"></i></span>
                      </a>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>

          </div>
        <?php endif; ?>
      <?php
      endforeach; // For Each List
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

  <?php if ($coming_soon_lists) : ?>
    <div class="my-5 text-center" id="coming-soon">
      <h1 class="text-center">Coming soon</h1>
      <h4 class="col-md-8 offset-md-2" style="line-height: 1.5">If these newsletters reach their target, we'll bring on expert writers and launch them for you. Vote for all your favourites below:</h4>
    </div>

    <div class="row">
      <div class="col-12">
        <ul class="nav nav-pills justify-content-end">
          <?php foreach ($sort_orders as $sort_order => $sort_order_title) :
            if ('frequency' == $sort_order)
              continue;
          ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $query_sort_order == $sort_order ? ' text-white active bg-dark' : 'text-dark'; ?>" href="<?php echo add_query_arg('sort', $sort_order); ?>"><?php echo $sort_order_title; ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="row <?php echo $category_slug ? 'justify-content-center' : ''; ?>">
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
                <a class="btn btn-default btn-outline-primary btn-share mx-1" href="http://www.facebook.com/share.php?u=<?php echo $share_url; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>

                <a class="btn btn-default btn-outline-info btn-share mx-1" href="https://twitter.com/share?url=<?php echo $share_url; ?>&amp;text=<?php echo urlencode($list->title); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
              <?php else : ?>
                <?php if (is_user_logged_in()) : ?>
                  <button type="button" class="btn btn-dark rounded btn-block btn-vote-observer<?php echo is_user_logged_in() ? '-l' : ''; ?> d-flex justify-content-between py-2" data-target="#voteobserverModal" data-topic="<?php echo $list->title; ?>" data-list="<?php echo $list->id; ?>" data-desc="<?php echo $list->description; ?>" data-votes="<?php echo $list->votes_count; ?>">
                    <div><i class="fa fa-thumbs-up mr-2"></i> <span class="btn-text">Vote</span></div>
                    <div><i class="fa fa-caret-right"></i></div>
                  </button>
                <?php else : ?>
                  <a href="<?php echo wp_login_url(); ?>" class="btn btn-dark rounded btn-block d-flex justify-content-between py-2" target="_blank">Vote</a>
                <?php endif; ?>
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
    </div>
  <?php endif; // If $coming_soon_lists 
  ?>
  <div class="row <?php echo $category_slug ? 'justify-content-center' : ''; ?>">
    <div class="col-lg-2 col-md-4 col-6 my-4 px-2 topic">
      <div class="text-center bg-light text-dark pt-2 pb-3 px-4 rounded" style="border: 1px solid #333;">
        <h3 class="text-center text-dark">Have an idea for a newsletter?</h3>
        <figure class="col-xs-12 text-center">
          <img src="https://cdn.thebrag.com/observer/images/marketing.svg" alt="" style="width: 90%">
        </figure>
        <a class="btn btn-danger rounded" href="/propose-a-newsletter">Propose a newsletter</a>
      </div>
    </div>
  </div>


<?php
}

/*
function bo_img_resize($imageURL, $width, $height, $crop = false) {
	$imageBase = str_replace(basename($imageURL), '', $imageURL);
	$imageURLParts = parse_url($imageURL);
	$imagePath = $_SERVER['DOCUMENT_ROOT'] . $imageURLParts['path'];

	if (!is_file($imagePath))
		return false;

	$originalSize = getimagesize($imagePath);

	if ($originalSize[0] <= $width)
		return $imageURL;

	if (!$height)
		$height = round($originalSize[1] / $originalSize[0] * $width);

	$pathInfo = pathinfo($imagePath);
	$resizedImageFileName = $pathInfo['filename'] . '-' . $width . 'x' . $height . '.' . $pathInfo['extension'];
	$resizedImageURL = $imageBase . $resizedImageFileName;
	$resizedImagePath = $pathInfo['dirname'] . '/' . $resizedImageFileName;

	if (is_file($resizedImagePath))
		return $resizedImageURL;

	$editor = wp_get_image_editor($imagePath);

	if (is_wp_error($editor))
		return false;

	$editor->resize($width, $height, $crop ? array('center', 'center') : false);
	$editor->save($resizedImagePath);

	return $resizedImageURL;
}
*/

// Add custom footer for Observer
get_template_part('page-templates/brag-observer/footer');
