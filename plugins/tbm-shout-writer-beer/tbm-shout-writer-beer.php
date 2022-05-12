<?php

/**
 * Plugin Name: Shout writer a Beer
 * Plugin URI: https://thebrag.media/
 * Description: Shout writer a beer (via PayPal)
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI: http://www.patelsachin.com
 */

class TBM_Shout_Writer_Beer
{

  protected static $instance = null;

  protected $plugin_name;
  protected $plugin_slug;

  protected $enableSandbox = false;

  protected $recaptchaSiteKey = '6Ld6B6UUAAAAAAHeCyZKNvQ3BQHYyudN2IuPMz6m';
  protected $recaptchaSecretKey = '6Ld6B6UUAAAAALmIbHn85ogS8HM1u5nHO7Dd7-mc';

  public function __construct()
  {
    $this->plugin_name = 'tbm_shout_writer_beer';
    $this->plugin_slug = 'tbm-shout-writer-beer';

    add_action('init', array($this, 'shout_writer_beer_init'));

    add_shortcode('shout_writer_beer', array($this, 'shortcode_shout_writer_beer_func'));

    add_action('admin_menu', array($this, 'settings_menu'));
  }

  public function settings_menu()
  {
    add_options_page('Shout writer beer', 'Shout writer beer', 'administrator', 'tbm-shout-writer-beer', array($this, 'settings_page'));
  }

  public function settings_page()
  {
    if (isset($_POST) && count($_POST) > 0) :
      foreach ($_POST as $key => $value) :
        if (strpos($key, 'tbm_') !== false) :
          update_option($key, sanitize_text_field($value));
        endif;
      endforeach;
      echo '<div class="alert alert-success">Options have been saved!</div>';
    endif;

    // wp_enqueue_script( 'bs', get_template_directory_uri() . '/bs/js/bootstrap.bundle.min.js', array( 'jquery' ), NULL, true );
    wp_enqueue_style('bs', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css');
?>
    <form method="post" class="form">
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="col-12">
              <h3>Shout the writer a beer - settings</h3>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Exclude Authors</label>
                <textarea name="tbm_shout_beer_exclude" id="tbm_shout_beer_exclude" class="form-control"><?php echo get_option('tbm_shout_beer_exclude'); ?></textarea>
              </div>

              <div class="form-group">
                <label>&quot;About this&quot; link URL</label>
                <input name="tbm_shout_beer_about_url" id="tbm_shout_beer_about_url" type="text" value="<?php echo stripslashes(get_option('tbm_shout_beer_about_url')); ?>" placeholder="https://" class="form-control">
              </div>

              <div class="form-group">
                <label>Pledging 100% to ALS</label>
                <textarea name="tbm_shout_beer_pledging" id="tbm_shout_beer_pledging" class="form-control"><?php echo get_option('tbm_shout_beer_pledging'); ?></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <input type="submit" name="submit" id="submit-campaign" class="button button-primary" value="Save">
        </div>
      </div>
    </form>
  <?php
  }

  private function get_paypal_config()
  {
    return array(
      'business' => $this->enableSandbox ? 'accounts-facilitator@seventhstreet.media' : 'accounts@seventhstreet.media',
      'return_url' => site_url() . '/thank-you-payment/',
      'cancel_url' => site_url() . '/?payment=cancelled',
      'notify_url' => 'https://thebrag.com/media/wp-json/api/v1/shout_writer_coffee_notify',
      'paypal_url' => $this->enableSandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr',
    );
  }

  /*
   * Show Thank you message OR
   * Process the form submitted from the posts
   */
  public function shout_writer_beer_init()
  {
    if (isset($_GET['message']) && 'thank-you-shout-beer' == $_GET['message']) :
      add_action('wp_footer', function () {
        echo '<div class="modal fade" id="thankYouShoutBeerModal" tabindex="-1" role="dialog" aria-labelledby="thankYouShoutBeerModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <div class="">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
                  <h2 class="text-center py-3 px-5">Thank you for shouting a beer to the writer!</h2>
                </div>
              </div>
            </div>
          </div>
        </div>';
      });
    endif;

    // If Shout a beer form is submitted
    if (isset($_POST['submit-shout-writer-beer'])) {

      $article_url = esc_url_raw($_SERVER['HTTP_REFERER']); // isset( $_POST['article_url'] ) ? esc_url_raw( $_POST['article_url'] ) : site_url();

      // Build POST request:
      $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
      $curl_recaptcha_data['secret'] = $this->recaptchaSecretKey;
      $curl_recaptcha_data['response'] = $_POST['recaptcha_response'];

      $recaptcha = $this->curl_post($recaptcha_url, 'POST', $curl_recaptcha_data);
      $recaptcha = json_decode($recaptcha);

      if (!$recaptcha->success || $recaptcha->score < 0.5) :
        header('location:' . $article_url);
        exit;
      endif;

      //            echo '<pre>'; print_r( $_POST ); exit;

      $paypal_config = $this->get_paypal_config();
      $author = isset($_POST['author']) ? sanitize_text_field($_POST['author']) : 'the writer';

      $total = isset($_POST['amount']) && $_POST['amount'] > 0 ? $_POST['amount'] : 5;
      $message = sanitize_textarea_field($_POST['message']);

      // Create an invoice on TheBrag.media site
      $curl_metadata = array(
        'auth_key' => 'cacU1r_3wUpusw9cadltIratL8+glt*s',
        'data' => array(
          'author' => $author,
          'article_url' => $article_url,
          'total' => $total,
          'message' => $message,
        )
      );

      $invoice_id = $this->curl_post('https://thebrag.com/media/wp-json/api/v1/shout_writer_coffee', 'POST', $curl_metadata);

      $data_paypal = array(
        'invoice' => $invoice_id,
        'cmd' => '_xclick',
        'business' => $paypal_config['business'],
        'item_name' => 'Shout ' . $author . ' a beer @ The Brag Media',
        'mesasge' => $message,
        'custom' => $message,
        'article' => $article_url,
        'amount' => $total,
        'no_shipping' => 1,
        'lc' => 'AU',
        'currency_code' => 'AUD',
        'return' => $article_url . '?message=thank-you-shout-beer',
        'cancel_return' => $article_url, // esc_url_raw($paypal_config['cancel_url']),
        'notify_url' => esc_url_raw($paypal_config['notify_url']),
        'image_url' => 'https://thebrag.media/brag-media-150x50.png',
      );
      $queryString = http_build_query($data_paypal);

      //            echo $queryString; exit;

      header('location:' . $paypal_config['paypal_url'] . '?' . $queryString);
      exit;
    }
  }

  public function shortcode_shout_writer_beer_func($atts)
  {
    wp_enqueue_script($this->plugin_slug, plugins_url('js/scripts.js', __FILE__), array('jquery'), NULL, true);
    // wp_enqueue_style( $this->plugin_slug, plugins_url( 'css/style.min.css', __FILE__ ) );
    wp_enqueue_style($this->plugin_slug, plugins_url('css/style.css', __FILE__), false, '2022-05-12');
    $a = shortcode_atts(array(
      'author' => '',
    ), $atts);

    $a['author'] = trim($a['author']);

    $exclude_authors = get_option('tbm_shout_beer_exclude');
    $arr_exclude_authors = array_map('trim', array_map('strtolower', explode(',', $exclude_authors)));

    if ('' == $a['author'] || in_array(strtolower($a['author']), $arr_exclude_authors)) :
      return '';
    endif;

    $about_url = stripslashes(get_option('tbm_shout_beer_about_url'));
    ob_start();
  ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $this->recaptchaSiteKey; ?>"></script>
    <script>
      grecaptcha.ready(function() {
        grecaptcha.execute("<?php echo $this->recaptchaSiteKey; ?>", {
          action: "shout_beer"
        }).then(function(token) {
          jQuery(".recaptchaResponse").val(token);
        });
      });
    </script>
    <div style="width: auto; max-width: 100%;">
      <div class="btn-shout-writer-beer" data-toggle="form-shout-writer-beer">
        <div style="display: flex; align-items: center;">
          <a href="https://younghenrys.com/" target="_blank" class="l-logo-beer" rel="noreferrer" style="text-decoration: none; text-indent: -99999px;">
            <span class="logo-beer" style="display: none;">
              <img src="<?php echo plugins_url('images/yh-logo-blk.png', __FILE__); ?>" loading="lazy" alt="Young Henrys" width="85" height="50">
            </span>
            <span style="text-indent: -99999px; display: inline-block;">Young Henrys</span>
          </a>
          <span class="ico-beer"><img src="<?php echo esc_url(plugins_url('images/younghenrys.gif', __FILE__)); ?>" width="35" height="60" loading="lazy"></span>

        </div>
        <span class="text-right">Love this article?<br>Shout <?php echo $a['author']; ?> a beer</span>
      </div>
      <form action="" method="post" name="form-shout-writer-beer" class="form-shout-writer-beer" target="_blank" style="display: none;">
        <textarea name="message" class="shout-writer-coffe-message form-control" placeholder="Your note to <?php echo $a['author']; ?> (optional)"></textarea>
        <div class="input-group d-flex">
          <div class="input-group-prepend" style="background: #ced4da;
    height: 100%;
    padding: .55rem;
    border-radius: .25rem 0 0 .25rem;
    font-weight: 500;
    border: 1px solid #ced4da;
    border-right: 0;">
            <div class="input-group-text">$</div>
          </div>
          <input type="number" name="amount" class="required form-control shout-writer-beer-amount" min="5" value="5" required style="border-left: none !important;
    border-radius: 0 .25rem .25rem 0;">
          <input type="hidden" name="author" value="<?php echo $a['author']; ?>">
          <input type="hidden" name="article_url" value="<?php echo get_the_permalink(); ?>">
          <input type="hidden" name="recaptcha_response" id="recaptchaResponse" class="recaptchaResponse">
          <input type="submit" value="Proceed" name="submit-shout-writer-beer" class="button btn btn-dark submit-shout-writer-beer form-control" style="padding: .5rem;">
        </div>
    <?php
    $html = ob_get_contents();
    ob_end_clean();

    $html .= '<div class="d-flex align-items-center">';
    $pledging_authors = get_option('tbm_shout_beer_pledging');
    $arr_pledging_authors = array_map('trim', array_map('strtolower', explode(',', $pledging_authors)));
    if (in_array(strtolower($a['author']), $arr_pledging_authors)) :
      $html .= '<span class="bg-white text-dark ml-0 mr-2 p-2">I am pledging 100% of my donations to <a href="https://www.als.org.au/" target="_blank" >ALS</a></span>';
    endif;

    if ('' != $about_url) :
      $html .= '<a href="' . $about_url . '" target="_blank" class="text-right bg-primary text-white badge p-2">About this</a>';
    endif;
    $html .= '</div>
      </form>
      </div>';
    return $html;
  }

  private function curl_post($post_url, $method = 'POST', $curl_post = array())
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $post_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if (in_array($method, array('POST', 'PUT'))) {
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($curl_post));
    }
    $curl_output = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return $curl_output;
  }
}

new TBM_Shout_Writer_Beer();
