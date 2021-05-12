<?php
class Shortcode extends BragObserver
{

  public function __construct()
  {

    // parent::__construct();

    // Term metas for Category
    add_action('category_add_form_fields', [$this, 'add_observer_topic_field'], 10, 2);
    add_action('created_category', [$this, 'save_category_meta'], 10, 2);
    add_action('category_edit_form_fields', [$this, 'edit_category_field'], 10, 2);
    add_action('edited_category', [$this, 'update_category_meta'], 10, 2);
    add_filter('manage_edit-category_columns', [$this, 'add_category_column']);
    add_filter('manage_category_custom_column', [$this, 'add_category_column_content'], 10, 3);

    // Shortcodes
    add_shortcode('observer_subscribe_category', [$this, 'shortcode_subscribe_category_form']);

    // AJAX
    add_action('wp_ajax_subscribe_observer_category', [$this, 'subscribe_observer_category']);
    add_action('wp_ajax_nopriv_subscribe_observer_category', [$this, 'subscribe_observer_category']);
  }

  /*
  * Add a field to New Category for term meta
  */
  public function add_observer_topic_field($post)
  {
    $topics = wp_list_pluck($this->get_observer_topics(), 'title', 'id');
    asort($topics);

    $value = 0;
    if (isset($post) && isset($post->ID)) {
      $value = get_post_meta($post->ID, 'observer-topic', true);
    }
?>
    <div class="form-field term-group">
      <label for="observer-topic">Observer topic</label>
      <select class="postform" id="observer-topic" name="observer-topic">
        <option value="">None</option>
        <?php foreach ($topics as $id => $title) : ?>
          <option value="<?php echo $id; ?>" <?php selected($value, $id); ?>><?php echo $title; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  <?php
  }

  /*
  * Save term meta for New Category
  */
  public function save_category_meta($term_id, $tt_id)
  {
    if (isset($_POST['observer-topic']) && '' !== $_POST['observer-topic']) {
      $topic = sanitize_title($_POST['observer-topic']);
      add_term_meta($term_id, 'observer-topic', $topic, true);
    }
  }

  /*
  * Add a field to Edit Category for term meta
  */
  public function edit_category_field($term, $taxonomy)
  {
    $topics = wp_list_pluck($this->get_observer_topics(), 'title', 'id');
    asort($topics);

    // get current topic
    $topic = get_term_meta($term->term_id, 'observer-topic', true);
  ?>
    <tr class="form-field term-group-wrap">
      <th scope="row">
        <label for="observer-topic">Observer topic</label>
      </th>
      <td><select class="postform" id="observer-topic" name="observer-topic">
          <option value="">None</option>
          <?php foreach ($topics as $id => $title) : ?>
            <option value="<?php echo $id; ?>" <?php selected($topic, $id); ?>><?php echo $title; ?></option>
          <?php endforeach; ?>
        </select></td>
    </tr>
    <?php
  }

  /*
  * Save term meta for Existing Category
  */
  public function update_category_meta($term_id, $tt_id)
  {
    if (isset($_POST['observer-topic']) && '' !== $_POST['observer-topic']) {
      $topic = sanitize_title($_POST['observer-topic']);
      update_term_meta($term_id, 'observer-topic', $topic);
    } else {
      delete_term_meta($term_id, 'observer-topic');
    }
  }

  /*
  * Add column to Category
  */
  public function add_category_column($columns)
  {
    $columns['observer_topic'] = 'Observer topic';
    return $columns;
  }

  function add_category_column_content($content, $column_name, $term_id)
  {
    if ($column_name !== 'observer_topic') {
      return $content;
    }

    $topics = wp_list_pluck($this->get_observer_topics(), 'title', 'id');
    asort($topics);

    $term_id = absint($term_id);
    $topic = get_term_meta($term_id, 'observer-topic', true);

    if (!empty($topic)) {
      $content .= esc_attr($topics[$topic]);
    }
    return $content;
  }

  /*
  * Get Observer topics
  */
  public function get_observer_topics()
  {
    global $wpdb;
    $lists_query = "SELECT id, title, slug, image_url, frequency, description FROM {$wpdb->prefix}observer_lists WHERE status = 'active' ORDER BY sub_count DESC";
    $lists = $wpdb->get_results($lists_query);

    $return = [];

    foreach ($lists as $list) {
      $return[] = [
        'id' => $list->id,
        'title' => $list->title,
        'link' => home_url('/observer/' . $list->slug . '/'),
        'image_url' => $list->image_url,
        'description' => $list->description,
        'frequency' => $list->frequency,
      ];
    }

    return $return;
  }

  /*
  * Shortcode: Subscribe to category
  */
  public function shortcode_subscribe_category_form($atts)
  {
    ob_start();
    include __DIR__ . '/../partials/shortcodes/category-form.php';
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
  }


  public function shortcode_subscribe_category_form_old($atts)
  {
    return;

    $category_atts = shortcode_atts(array(
      'id' => NULL,
    ), $atts);

    if (is_null($category_atts['id']))
      return;

    $post_id = $category_atts['id'];

    $topic = get_post_meta($post_id, 'observer-topic', true);

    if (!$topic) {

      $posttags = get_the_tags($post_id);
      if ($posttags) {
        foreach ($posttags as $tag) {
          if (stripos($tag->name, 'vegan') !== false) {
            $topic = 6;
            break;
          }
        }
      }

      $topics = $this->get_observer_topics();
      $topic_titles = wp_list_pluck($topics, 'title', 'id');
      $topic_links = wp_list_pluck($topics, 'link', 'id');

      if (!$topic) {
        $categories = get_the_terms($post_id, 'category');
        if (!$categories) {
          return;
        }

        $primary_category = null;

        foreach ($categories as $category) {
          if (get_post_meta($post_id, '_yoast_wpseo_primary_category', true) == $category->term_id) {
            $primary_category = $category;
            break;
          }
        }

        if (is_null($primary_category)) {
          $primary_category = $categories[0];
        }

        $topic = get_term_meta($category->term_id, 'observer-topic', true);

        if (!in_array($topic, array_keys($topic_titles))) {
          foreach ($categories as $category) {
            if (in_array(get_term_meta($category->term_id, 'observer-topic', true), array_keys($topic_titles))) {
              $topic = get_term_meta($category->term_id, 'observer-topic', true);
              break;
            }
          }
        }
      }
    }

    ob_start();
    if ($topic) {

      if (is_user_logged_in()) {
        global $wpdb;
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $my_sub_lists = [];
        $my_subs = $wpdb->get_results("SELECT list_id FROM {$wpdb->prefix}observer_subs WHERE user_id = '{$user_id}' AND list_id = '{$topic}' AND status = 'subscribed' ");
        $my_sub_lists = wp_list_pluck($my_subs, 'list_id');

        if (in_array($topic, $my_sub_lists)) {
          return;
        }
      }

      $topic_title = trim(str_ireplace('Observer', '', $topic_titles[$topic]));

      if (in_array($topic, [27])) {
        $topic_title .= ' Music';
      }
    ?>
      <style>
        .observer-sub-form {
          padding: .25rem;
          /* background: #dc3545; */
          border-radius: 10px;
          max-width: none;
        }

        .observer-sub-form .observer-sub-email {
          background: #fff;
          border-radius: .25rem;
          padding: 25px 15px;
          border-top-right-radius: 0;
          border-bottom-right-radius: 0;

        }

        .observer-sub-form input[type=email] {
          width: 100%;
          font-size: 16px;
          line-height: 1;
          color: #000;
          border: none;
        }

        .observer-sub-form input[type=submit] {
          padding: 5px 10px;
          font-weight: 300;
          /* background-color: #dc3545; */
          color: #fff;
          border: none;

        }

        <?php
        if (!is_user_logged_in()) {
        ?>.observer-sub-form .submit-wrap,
        .observer-sub-form .submit-wrap input[type=submit] {
          border-top-left-radius: 0 !important;
          border-bottom-left-radius: 0 !important;
        }

        .observer-sub-form .submit-wrap {
          border: 1px solid #fff;
        }

        <?php
        } // If not logged in
        ?>.observer-sub-form .spinner {
          width: 25px !important;
          height: 25px !important;
          margin: 10px auto !important;
        }

        .observer-sub-form .spinner .double-bounce1,
        .observer-sub-form .spinner .double-bounce2 {
          background-color: #fff;
        }
      </style>
      <form action="#" method="post" id="observer-subscribe-form<?php echo $post_id; ?>" name="observer-subscribe-form" class="observer-subscribe-form">
        <div class="observer-sub-form bg-success justify-content-center my-3 p-3">
          <h4 class="text-white">Love <?php echo $topic_title; ?>?</h4>
          <p class="text-white">Get the latest <?php echo $topic_title; ?> news, features, updates and giveaways straight to your inbox</p>
          <div class="d-flex" style="<?php echo !is_user_logged_in() ? 'box-shadow: 0 0 0 1px rgba(0,0,0,.15), 0 2px 3px rgba(0,0,0,.2)' : ''; ?>;">
            <input type="hidden" name="list" value="<?php echo $topic; ?>">
            <?php if (!is_user_logged_in()) { ?>
              <input type="email" name="email" class="form-control observer-sub-email" placeholder="Your email" value="">
            <?php } ?>
            <div class="d-flex submit-wrap rounded">
              <input type="submit" value="Join" name="subscribe" class="button btn btn-dark rounded <?php echo is_user_logged_in() ? 'btn-observer-join' : ''; ?>">
              <div class="loading mx-3" style="display: none;">
                <div class="spinner">
                  <div class="double-bounce1"></div>
                  <div class="double-bounce2"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="alert alert-info d-none js-msg-subscribe mt-2"></div>
          <div class="alert alert-danger d-none js-errors-subscribe mt-2"></div>
        </div>
      </form>

      <script>
        jQuery(document).ready(function($) {
          if ($('.observer-subscribe-form').length) {
            $(document).on('submit', '.observer-subscribe-form', function(e) {
              e.preventDefault();
              var theForm = $(this);

              <?php if (!is_user_logged_in()) { ?>
                const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

                if (theForm.find('input[name="email"]').length &&
                  (
                    theForm.find('input[name="email"]').val() == '' ||
                    !re.test(String(theForm.find('input[name="email"]').val().toLowerCase()))
                  )) {
                  theForm.find('.js-errors-subscribe').html('Please enter a valid email address.').removeClass('d-none');
                  return false;
                }
              <?php } ?>

              var formData = $(this).serialize();
              var loadingElem = $(this).find('.loading');
              var button = $(this).find('.button');

              var the_url = theForm.closest('.single_story').find('h1:first').data('href');
              formData += '&source=' + the_url;

              $('.js-errors-subscribe,.js-msg-subscribe').html('').addClass('d-none');
              loadingElem.show();
              button.hide();
              var data = {
                action: 'subscribe_observer_category',
                formData: formData
              };
              $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(res) {
                if (res.success) {
                  theForm.find('.js-msg-subscribe').html(res.data.message).removeClass('d-none');
                } else {
                  theForm.find('.js-errors-subscribe').html(res.data.error.message).removeClass('d-none');
                }
                loadingElem.hide();
                button.show();
              }).error(function() {
                theForm.find('.js-errors-subscribe').html('Something went wrong, please try again later').removeClass('d-none');
                loadingElem.hide();
                button.show();
              });
            });
          }
        });
      </script>
    <?php
    } else if (isset($primary_category)) { // Topic not set
    ?>
      <a href="https://thebrag.com/observer/" target="_blank">
        <div class="observer-sub-form bg-success rounded justify-content-center my-3 p-3">
          <h4 class="text-white">Newsletters tailored to you</h4>
          <p class="text-white">Get the latest news, features, updates and giveaways straight to your inbox</p>
          <div class="btn btn-outline-light">Click here to join FREE</div>
        </div>
      </a>
<?php
    }
    $html = ob_get_contents();
    ob_end_clean();

    return $html;
  } // shortcode_subscribe_category_form()

  public function subscribe_observer_category()
  {
    if (defined('DOING_AJAX') && DOING_AJAX) :

      parse_str($_POST['formData'], $formData);

      if (!is_numeric($formData['list'])) {
        error_log('Observer List _' . $formData['list'] . '_is not numeric');
        wp_mail('sachin.patel@thebrag.media', 'Observer Error', 'Observer List _' . $formData['list'] . '_is not numeric');
        wp_send_json_error(['error' => ['message' => 'Something went wrong']]);
        wp_die();
      }

      $body = [
        // 'email' => $formData['email'],
        'list' => $formData['list'],
        'source' => $formData['source'],
        'status' => 'subscribed',
      ];
      if (isset($formData['email'])) {
        $body['email'] = $formData['email'];
      }

      $api = new API();
      return $api->sub_unsub($body);
    endif;
  }
}
new Shortcode();
