<?php
global $wpdb;

$action = isset($_GET['action']) ? $_GET['action'] : null;

if (!is_null($action) && 'edit' != $action) {
  switch ($action) {
    case 'preview':
      include __DIR__ . '/../partials/newsletter-template-braze.php';
      break;
    case 'create-on-mc':
      include __DIR__ . '/../partials/create-newsletter-mc.php';
      break;
    case 'show-html':
      include __DIR__ . '/../partials/show-newsletter-html.php';
      break;
    case 'copy':
      include __DIR__ . '/../partials/copy-newsletter.php';
      break;
    default:
      break;
  }
  return;
} // If $action is set

wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css');

$articles_recent_results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}observer_newsletter_articles WHERE DATE(created_at) BETWEEN '" . date('Y-m-d', strtotime('-4 weeks')) . "' AND '" . date('Y-m-d') . "'");
$articles_recent = wp_list_pluck($articles_recent_results, 'article_url');

$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!is_null($id)) :
  $newsletter = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_newsletters WHERE id = {$id} LIMIT 1");
  if (!$newsletter) {
    echo '<div class="alert alert-danger">Newsletter not found.</div>';
    return;
  }
  $newsletter->details = json_decode($newsletter->details);

  foreach ($newsletter->details as $k => $v) {
    if (is_object($v)) {
      $v = (array) $v;
      // $v = array_values( $v );
      $newsletter->details->{$k} = $v;
    }
  }

  $post_links = isset($newsletter->details->post_links) ? $newsletter->details->post_links : [];

  // echo '<pre>'; print_r( $newsletter->details->ads ); exit;
  $list_id = $newsletter->list_id;
else :
  $list_id = isset($_GET['list_id']) ? $_GET['list_id'] : null;
  if (is_null($list_id)) {
    $lists = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}observer_lists WHERE status = 'active' ORDER BY title ASC");
    if ($lists) {
?>
      <h1>Click the list you want to create newsletter for;</h1>
      <div class="container-fluid">
        <div class="row">
          <?php foreach ($lists as $list) { ?>
            <a href="<?php echo add_query_arg('list_id', $list->id); ?>" class="btn btn-default col-md-2 m-2" style="height: 150px; overflow: hidden;">
              <img src="<?php echo $list->image_url; ?>" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 100%; height: auto; opacity: .7">
              <span class="text-white p-2 d-inline-block" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,.7)"><?php echo $list->title; ?></span>
            </a>
          <?php } ?>
        </div>
      </div>
<?php  }
    return;
  }
endif;

$list = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}observer_lists WHERE id = {$list_id}");
if (!$list)
  return;

$keywords = $list->keywords && '' != $list->keywords ? $list->keywords : $list->slug;

$urls = [
  'https://tonedeaf.thebrag.com/',
  'https://thebrag.com/',
  'https://dontboreus.thebrag.com/',
  // 'https://theindustryobserver.thebrag.com/',
  'https://themusicnetwork.com/',
  'https://au.rollingstone.com/'
];

$articles = [];
$after = date('Y-m-d', strtotime('-5 days'));
$before = date('Y-m-d', strtotime('+1 days'));

foreach ($urls as $url) {
  $call_url = $url . 'wp-json/api/v1/observer/articles/?topic[]=' . $keywords . '&after=' . $after . '&before=' . $before;
  $articles_json = self::callAPI('GET', $call_url);
  $articles_url = json_decode($articles_json);
  if (is_array($articles_url)) {
    $articles = array_merge($articles, $articles_url);
  }
}

/* if (4 == $list->id) {
  $call_url = 'https://themusicnetwork.com/wp-json/api/v1/observer/articles/?topic[]=biz&topic[]=music&topic[]=observer&after=' . $after . '&before=' . $before;
  $articles_json = self::callAPI('GET', $call_url);
  $articles_url = json_decode($articles_json);
  // echo '<pre>'; print_r( $call_url ); exit;
  if (is_array($articles_url)) {
    $articles = array_merge($articles, $articles_url);
  }
} */

// echo '<pre>'; print_r( $articles ); exit;
?>
<style>
  /* #campaign-posts { border-collapse: collapse; }
  #campaign-posts tr td, #campaign-posts tr th { border-bottom: 3px solid #e5e5e5; margin: 0; padding: 1rem; } */
  .input-group-text,
  #campaign-posts input,
  #campaign-posts textarea {
    font-size: .8rem !important;
  }

  #campaign-posts label {
    display: block;
  }

  .form-control {
    border: 1px solid #ced4da !important;
  }
</style>
<?php
wp_enqueue_script('observer-newsletter', plugin_dir_url(__FILE__) . '/../../js/newsletter.js', array('jquery'), time(), true);

$args = array(
  'ajaxurl'   => admin_url('admin-ajax.php'),
);
wp_localize_script('observer-newsletter', 'observer', $args);

wp_enqueue_script('jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array('jquery'), 1.0, true);
wp_enqueue_style('jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css');
?>


<form method="post" action="#" class="create-campaign">
  <input type="hidden" name="list_id" id="list_id" value="<?php echo $list->id; ?>">
  <?php if (isset($newsletter)) : ?>
    <input type="hidden" name="id" id="newsletter-id" value="<?php echo $newsletter->id; ?>">
    <h1>Edit "<?php echo $newsletter->details->title; ?>"</h1>
  <?php else : ?>

    <h1>Create New Newsletter <small>(<?php echo $list->title; ?>)</small></h1>
  <?php endif; ?>

  <table class="table table-sm table-light">
    <!-- Campaign Details -->
    <tr>
      <th colspan="2">Campaign Details</th>
    </tr>
    <tr>
      <td colspan="4">Subject <small>Max 150 characters</small>
        <input type="text" name="subject" value="<?php echo isset($newsletter) && isset($newsletter->details->subject) ? htmlentities($newsletter->details->subject) : ''; ?>" maxlength="150" class="form-control">
      </td>
    </tr>
    <tr>
      <td colspan="4">Preview Text <small>Max 150 characters</small>
        <input type="text" name="preview_text" value="<?php echo isset($newsletter) && isset($newsletter->details->preview_text) ? htmlentities($newsletter->details->preview_text) : ''; ?>" maxlength="150" class="form-control">
      </td>
    </tr>
    <tr>
      <td>Date<br>
        <input type="text" name="date_for" class="datepicker form-control" readonly value="<?php echo isset($newsletter) && isset($newsletter->details->date_for) ? date('j F Y', strtotime($newsletter->details->date_for)) : date('j F Y'); ?>">
      </td>

      <td>Title<br><input type="text" name="title" readonly value="<?php echo isset($newsletter) && isset($newsletter->details->title) ? htmlentities($newsletter->details->title) : '[' . date('d M, Y') . '] ' . $list->title; ?>" class="form-control"></td>

      <td>Reply to <small>Email Address</small><br>
        <input type="text" name="reply_to" value="<?php echo isset($newsletter) && isset($newsletter->details->reply_to) ? $newsletter->details->reply_to : 'observer@thebrag.media'; ?>" class="form-control">
      </td>

      <td>From Name<br>
        <input type="text" name="from_name" value="<?php echo isset($newsletter) && isset($newsletter->details->from_name) ? $newsletter->details->from_name : trim(str_ireplace('Observer', '', $list->title)) . ' Observer'; ?>" class="form-control">
      </td>
    </tr>


    <!-- Campaign Details -->
  </table>

  <table class="table table-sm table-borderless">
    <tr>
      <td style="border-right: 1px solid #ddd;">
        <table class="">
          <tr>
            <td colspan="2">
              <div style="max-height: 500px; overflow-y: scroll; border-bottom: 1px solid lightgrey;">
                <?php if ($articles) : ?>
                  <input type="text" name="search-articles" id="search-articles" placeholder="Search..." class="form-control">
                  <table class="table table-sm table-hover" id="articles-table">
                    <tr>
                      <th style="width: 1rem;"></th>
                      <td colspan="5" class="text-right"></td>
                    </tr>
                    <?php
                    $counter = isset($newsletter->details->posts) ? count($newsletter->details->posts) : 0;
                    foreach ($articles as $index => $article) : ?>
                      <tr style="<?php echo isset($articles_recent) && is_array($articles_recent) && in_array($article->link, $articles_recent) ? 'color: lightgrey;' : ''; ?>">
                        <td><?php echo $index + 1; ?></td>
                        <td><input type="checkbox" class="select-post" value="<?php echo $counter + 1; ?>" id="article<?php echo $counter + 1; ?>" <?php echo isset($post_links) && is_array($post_links) && in_array($article->link, $post_links) ? 'disabled' : ''; ?> data-url="<?php echo $article->link; ?>"></td>
                        <td>
                          <label for="article<?php echo $counter + 1; ?>">
                            <img src="<?php echo $article->image; ?>" width="40">
                          </label>
                        </td>
                        <td>
                          <label for="article<?php echo $counter + 1; ?>">
                            <?php echo $article->title; ?>
                            <input type="hidden" name="articles[<?php echo $counter + 1; ?>]['link']" value="<?php echo $article->link; ?>">
                            <input type="hidden" name="articles[<?php echo $counter + 1; ?>]['title']" value="<?php echo $article->title; ?>">
                            <input type="hidden" name="articles[<?php echo $counter + 1; ?>]['blurb']" value="<?php echo $article->description; ?>">
                            <input type="hidden" name="articles[<?php echo $counter + 1; ?>]['image']" value="<?php echo $article->image; ?>">
                            <a href="<?php echo $article->link; ?>" target="_blank" class="badge badge-secondary" title="Click to open article"><?php echo date('d M', strtotime($article->publish_date)); ?></a>
                          </label>

                      </tr>

                    <?php $counter++;
                    endforeach; // For Each $articles 
                    ?>
                  </table>
              </div>
            <?php
                endif; // If $articles
            ?>
            </td>
          </tr>
        </table>

        <?php
        // Don't show where passendo ads will be shown
        if (1 || !in_array($list->id, [11, 4, 1, 17, 5, 16, 50, 7, 27, 18])) { ?>
          <table class="table table-condensed table-borderless">
            <!-- Ads -->
            <?php for ($i = 0; $i <= 2; $i++) :
              // echo 'ad_middle_$i_link'; exit;
            ?>
              <tr>
                <td colspan="2">
                  <div><strong>Ad <?php echo $i + 1; ?></strong></div>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text px-1 py-0">Link</div>
                    </div>
                    <input type="text" name="ads[<?php echo $i; ?>][link]" value="<?php echo isset($newsletter) && isset($newsletter->details->ads[$i]->link) ? $newsletter->details->ads[$i]->link : ''; ?>" class="form-control">
                  </div>

                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text px-1 py-0">Image</div>
                    </div>
                    <input type="text" name="ads[<?php echo $i; ?>][image]" value="<?php echo isset($newsletter) && isset($newsletter->details->ads[$i]->image) ? $newsletter->details->ads[$i]->image : ''; ?>" class="form-control">
                  </div>
              </tr>
            <?php endfor; ?>

            <tr>
              <td colspan="2">
                <div><strong><a href="#" id="toggle-ad-4">Ad 4 &#9662;</a></strong></div>
                <div id="ad-4-wrap" class="d-none">
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text px-1 py-0">Link</div>
                    </div>
                    <input type="text" name="ads[3][link]" value="<?php echo isset($newsletter) && isset($newsletter->details->ads[3]->link) ? $newsletter->details->ads[3]->link : ''; ?>" class="form-control">
                  </div>

                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text px-1 py-0">Image</div>
                    </div>
                    <input type="text" name="ads[3][image]" value="<?php echo isset($newsletter) && isset($newsletter->details->ads[3]->image) ? $newsletter->details->ads[3]->image : ''; ?>" class="form-control">
                  </div>
                </div>
            </tr>
            <!-- Ads -->
          </table>
        <?php } ?>

        <?php if (4 == $list->id) { // 4 = TIO list ID 
        ?>
          <!-- Top Industry Tweet -->
          <hr>
          <div><strong>Top Industry Tweet</strong></div>
          <div class="input-group mb-2">
            <div class="input-group-prepend">
              <div class="input-group-text px-1 py-0">Image</div>
            </div>
            <input type="text" name="top_i_tweet_image" value="<?php echo isset($newsletter) && isset($newsletter->details->top_i_tweet_image) ? $newsletter->details->top_i_tweet_image : ''; ?>" class="form-control">
          </div>

          <div class="input-group mb-2">
            <div class="input-group-prepend">
              <div class="input-group-text px-1 py-0">Link</div>
            </div>
            <input type="text" name="top_i_tweet_link" value="<?php echo isset($newsletter) && isset($newsletter->details->top_i_tweet_link) ? $newsletter->details->top_i_tweet_link : ''; ?>" class="form-control">
          </div>

          <!-- Birthday Shout Out -->
          <hr>
          <div><strong>Birthday Shout Out</strong></div>
          <div class="input-group mb-2">
            <div class="input-group-prepend">
              <div class="input-group-text px-1 py-0">Image</div>
            </div>
            <input type="text" name="birthday_shoutout_image" value="<?php echo isset($newsletter) && isset($newsletter->details->birthday_shoutout_image) ? $newsletter->details->birthday_shoutout_image : ''; ?>" class="form-control">
          </div>

          <div class="input-group mb-2">
            <div class="input-group-prepend">
              <div class="input-group-text px-1 py-0">Link</div>
            </div>
            <input type="text" name="birthday_shoutout_link" value="<?php echo isset($newsletter) && isset($newsletter->details->birthday_shoutout_link) ? $newsletter->details->birthday_shoutout_link : ''; ?>" class="form-control">
          </div>

          <div class="input-group mb-2">
            <div class="input-group-prepend">
              <div class="input-group-text px-1 py-0">Blurb</div>
            </div>
            <textarea name="birthday_shoutout_blurb" class="form-control"><?php echo isset($newsletter) && isset($newsletter->details->birthday_shoutout_blurb) ? htmlentities($newsletter->details->birthday_shoutout_blurb) : ''; ?></textarea>
          </div>
          <!-- Top Industry Tweet -->
        <?php } // If TIO list 
        ?>

        <?php
        $wpeditor_settings = array(
          // 'teeny' => true,
          'textarea_rows' => 15,
          'tabindex' => 1,
          'tinymce'       => array(
            'toolbar1' => 'formatselect,bold,italic,underline,separator,bullist,numlist,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo,removeformat',
            'toolbar2'      => '',
            'toolbar3'      => '',
          ),
        );
        ?>

        <?php if (56 == $list->id) { // 56 = Christian Hull list ID 
        ?>
          <!-- Christian Hull's Top Five {{ -->
          <hr>
          <div><strong>Christian Hull's Top Five</strong></div>
          <?php
          wp_editor(isset($newsletter) && isset($newsletter->details->christian_hull_top_five) ? $newsletter->details->christian_hull_top_five : '', 'christian_hull_top_five', $wpeditor_settings);
          ?>
          <!-- }} Christian Hull's Top Five -->
        <?php } // If Christian Hull list 
        ?>

        <hr>

        <div><strong>Intro (optional)</strong></div>
        <?php
        wp_editor(isset($newsletter) && isset($newsletter->details->intro_content) ? $newsletter->details->intro_content : '', 'intro_content', $wpeditor_settings);
        ?>

        <!-- Optional sections choices {{ -->
        <div class="mt-3">
          <div>
            <label><input type="checkbox" name="hide_video_record" id="hide_video_record" value="1" <?php echo isset($newsletter->details->hide_video_record) && '1' == $newsletter->details->hide_video_record ? 'checked' : ''; ?>>
              Hide Video/Record of the week
            </label>
          </div>
          
          <!-- <div>
            <label><input type="checkbox" name="hide_observer_recommendations" id="hide_observer_recommendations" value="1" <?php echo isset($newsletter->details->hide_observer_recommendations) && '1' == $newsletter->details->hide_observer_recommendations ? 'checked' : ''; ?>>
              Hide Observer recommendations
            </label>
          </div> -->

          <!-- <div>
            <label><input type="checkbox" name="hide_observer_rewards" id="hide_observer_rewards" value="1" <?php echo isset($newsletter->details->hide_observer_rewards) && '1' == $newsletter->details->hide_observer_rewards ? 'checked' : ''; ?>>
              Hide Observer rewards (share)
            </label>
          </div> -->

          <?php if (4 == $list->id) { // 4 = TIO list ID 
          ?>
            <div>
              <label><input type="checkbox" name="hide_jobs" id="hide_jobs" value="1" <?php echo isset($newsletter->details->hide_jobs) && '1' == $newsletter->details->hide_jobs ? 'checked' : ''; ?>>
                Hide Jobs
              </label>
            </div>
            <div>
              <label><input type="checkbox" name="hide_top_industry_tweet" id="hide_top_industry_tweet" value="1" <?php echo isset($newsletter->details->hide_top_industry_tweet) && '1' == $newsletter->details->hide_top_industry_tweet ? 'checked' : ''; ?>>
                Hide Top Industry Tweet
              </label>
            </div>
            <div>
              <label><input type="checkbox" name="hide_birthday_shoutout" id="hide_birthday_shoutout" value="1" <?php echo isset($newsletter->details->hide_birthday_shoutout) && '1' == $newsletter->details->hide_birthday_shoutout ? 'checked' : ''; ?>>
                Hide Birthday Shout Out
              </label>
            </div>
          <?php } // If TIO list 
          ?>
        </div>
        <!-- }} Optional sections choices -->
      </td>

      <td width="60%">
        <div id="campaign-posts-wrap">
          <div class="d-flex justify-content-between align-items-center">
            <h3 style="">Selected Articles<span id="total-posts">: <span class="total">0</span></span></h3>
            <a href="#" class="add-post-blank btn btn-sm btn-info" style="float: right;">Add blank</a>
          </div>
          <table id="campaign-posts" class="table table-sm table-striped">
            <tr>
              <th style="width: 50px;">Order</th>
              <th colspan="2">Article</th>
            </tr>
            <?php
            if (isset($newsletter->details) && isset($newsletter->details->posts)) :
              foreach ($newsletter->details->posts as $post_index => $order) :
            ?>
                <tr id="campaign-post-blank-<?php echo $post_index; ?>" data-url="<?php echo $newsletter->details->post_links[$post_index]; ?>">
                  <td>
                    <input type="number" maxlength="2" min="1" class="campaign-posts" name="posts[<?php echo $post_index; ?>]" value="<?php echo $order; ?>" size="2" data-id=<?php echo $post_index + 1; ?>>
                    <button class="remove remove-campaign-post btn btn-sm btn-outline-danger" data-id="<?php echo $post_index; ?>" data-url="<?php echo $newsletter->details->post_links[$post_index]; ?>" tabindex="-1">x</button>
                  </td>
                  <td>
                    <div class="input-group mb-2">
                      <div class="input-group-prepend">
                        <div class="input-group-text px-1 py-0">Link</div>
                      </div>
                      <input type="text" name="post_links[<?php echo $post_index; ?>]" value="<?php echo $newsletter->details->post_links[$post_index]; ?>" class="link_remote form-control">
                    </div>
                    <div class="remote_content mb-4">
                      <div class="input-group mb-2">
                        <div class="input-group-prepend">
                          <div class="input-group-text px-1 py-0">Title</div>
                        </div>
                        <input type="text" name="post_titles[<?php echo $post_index; ?>]" value="<?php echo $newsletter->details->post_titles[$post_index]; ?>" class="title form-control">
                      </div>
                      <div class="input-group mb-2">
                        <div class="input-group-prepend">
                          <div class="input-group-text px-1 py-0">Blurb</div>
                        </div>
                        <textarea name="post_excerpts[<?php echo $post_index; ?>]" class="excerpt form-control"><?php echo $newsletter->details->post_excerpts[$post_index]; ?></textarea>
                      </div>
                      <div class="input-group mb-2">
                        <div class="input-group-prepend">
                          <div class="input-group-text px-1 py-0"><img src="<?php echo $newsletter->details->post_images[$post_index]; ?>" width="50"></div>
                        </div>
                        <input type="text" name="post_images[<?php echo $post_index; ?>]" value="<?php echo $newsletter->details->post_images[$post_index]; ?>" class="image form-control">
                      </div>
                    </div>
                  </td>
                </tr>
            <?php
              endforeach; // For Each $newsletter->details->articles
            endif; // If $newsletter->details->articles
            ?>
          </table>

          <a href="#" class="add-post-blank btn btn-sm btn-info" style="float: right;">Add blank</a>
        </div>
      </td>
    </tr>
  </table>

  <?php // submit_button(); 
  ?>

  <div>
    <div class="submit">
      <div id="js-errors" class="hide alert alert-danger"></div>
      <input type="button" name="submit" id="submit-campaign" class="button button-primary" value="Save">
      <span class="status alert"></span>
    </div>
  </div>
</form>

<script>
  jQuery(document).ready(function($) {
    $("#search-articles").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#articles-table tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });
</script>