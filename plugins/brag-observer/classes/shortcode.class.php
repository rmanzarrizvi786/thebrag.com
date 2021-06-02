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
  public function get_observer_topics($topic_id = null)
  {
    global $wpdb;

    if (!is_null($topic_id)) {
      $id = absint($topic_id);
      return $wpdb->get_row("SELECT id, title, slug, image_url, frequency, description FROM {$wpdb->prefix}observer_lists WHERE id = '{$topic_id}'");
    }

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
