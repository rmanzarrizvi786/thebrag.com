<?php

/**
 * Plugin Name: TBM Ads Manager
 * Plugin URI: https://thebrag.media/
 * Description:
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI:
 */

class TBMAds
{

  protected $plugin_title;
  protected $plugin_name;
  protected $plugin_slug;

  protected static $_instance;

  public function __construct()
  {

    $this->plugin_title = 'TBM Ads';
    $this->plugin_name = 'tbm_ads';
    $this->plugin_slug = 'tbm-ads';

    add_action('wp_enqueue_scripts', [$this, 'action_wp_enqueue_scripts']);
    add_action('wp_head', [$this, 'action_wp_head']);
  }

  /*
  * Enqueue JS
  */
  public function action_wp_enqueue_scripts()
  {
    wp_enqueue_script('adm-fuse', 'https://cdn.fuseplatform.net/publift/tags/2/2355/fuse.js', [], '1');
  }

  /*
  * WP Head
  */
  public function action_wp_head()
  {
    // if (!is_home() && !is_front_page()) 
    {
?>
      <script type="text/javascript">
        const fusetag = window.fusetag || (window.fusetag = {
          que: []
        });

        fusetag.que.push(function() {
          googletag.pubads().enableSingleRequest();
          googletag.enableServices();
        });
      </script>
<?php
    }
  }

  /*
  * Singleton
  */
  public static function get_instance()
  {
    if (!isset(static::$_instance)) {
      static::$_instance = new TBMAds();
    }
    return static::$_instance;
  }

  /*
  * Get Ad Tag
  */
  public function get_ad($ad_location = '', $slot_no = 0, $post_id = null, $device = '', $ad_width = '')
  {
    if ('' == $ad_location)
      return;

    $html = '';
    $fuse_tags = self::fuse_tags();

    if (isset($_GET['screenshot'])) {
      $pagepath = 'screenshot';
    } else if (isset($_GET['dfp_key'])) {
      $pagepath = $_GET['dfp_key'];
    } else if (is_home() || is_front_page()) {
      $pagepath = 'homepage';
    } else {
      $pagepath_uri = substr(str_replace('/', '', $_SERVER['REQUEST_URI']), 0, 40);
      $pagepath_e = explode('?', $pagepath_uri);
      $pagepath = $pagepath_e[0];
    }

    if (function_exists('amp_is_request') && amp_is_request()) {
      if (isset($fuse_tags['amp'][$ad_location]['sticky']) && $fuse_tags['amp'][$ad_location]['sticky']) {
        $html .= '<amp-sticky-ad layout="nodisplay">';
      }
      $html .= '<amp-ad
        width=' . $fuse_tags['amp'][$ad_location]['width']
        . ' height=' . $fuse_tags['amp'][$ad_location]['height']
        . ' type="doubleclick"'
        . ' data-slot="' . $fuse_tags['amp']['network_id'] . $fuse_tags['amp'][$ad_location]['slot'] . '"'
        . '></amp-ad>';
      if (isset($fuse_tags['amp'][$ad_location]['sticky']) && $fuse_tags['amp'][$ad_location]['sticky']) {
        $html .= '</amp-sticky-ad>';
      }
      return $html;
    } else {

      if (in_array($ad_location, ['mrec1', 'mrec_1'])) {
        $ad_location = 'rail1';
      } elseif (in_array($ad_location, ['mrec2', 'mrec_2'])) {
        $ad_location = 'rail2';
      }

      $fuse_id = null;

      $post_type = get_post_type(get_the_ID());

      $section = 'homepage';
      if (is_home() || is_front_page()) {
        $section = 'homepage';
      } elseif (is_category()) {
        $term = get_queried_object();
        if ($term) {
          $category_parent_id = $term->category_parent;
          if ($category_parent_id != 0) {
            $category_parent = get_term($category_parent_id, 'category');
            $category = $category_parent->slug;
          } else {
            $category = $term->slug;
          }
        }
        $section = 'category';
      } elseif (is_archive()) {
        $section = 'category';
      } elseif (in_array($post_type, ['post', 'snaps', 'photo_gallery'])) {
        $section = 'article';
        /* if ($slot_no == 2) {
          $section = 'second_article';
        } */

        $categories = get_the_category($post_id);
        if ($categories) {
          foreach ($categories as $category_obj) :
            $category = $category_obj->slug;
            break;
          endforeach;
        }
      }

      if (!is_null($post_id)) {
        // $the_post = get_post($post_id);
        // $pagepath = get_the_permalink($post_id);
      }

      if (isset($section)) {
        if (!isset($fuse_tags[$section][$ad_location])) {
          return;
        }
        $fuse_id = $fuse_tags[$section][$ad_location];
      } else {
        $fuse_id = $fuse_tags[$ad_location];
      }
      $html .= '<!--' . $post_id . ' | '  . $section . ' | ' . $ad_location . ' | ' . $slot_no . '-->';
      $html .= '<div data-fuse="' . $fuse_id . '"></div>';

      if ($slot_no > 1) {
        $html .= '<script>
      fusetag.que.push(function(){
        fusetag.loadSlotById("' . $fuse_id . '");
       });
       </script>';
      } else {
        $html .= '<script type="text/javascript">';
        if (isset($category)) {
          $html .= 'fusetag.setTargeting("fuse_category", ["' . $category . '"]);';
        }
        if (isset($pagepath)) {
          $html .= 'fusetag.setTargeting("pagepath", ["' . $pagepath . '"]);';
        }
        $html .= '</script>';
      }

      if (isset($category)) {
        /* $html .= '<script type="text/javascript">
      fusetag.que.push(function() {
         fusetag.setTargeting("fuse_category", ["' . $category . '"]);
      });
      </script>'; */
      }

      return $html;
    }
  }

  private static function fuse_tags()
  {
    return [
      'amp' => [
        'network_id' => '/22071836792/SSM_thebragcomprem/',
        'header' => [
          'width' => 320,
          'height' => 50,
          'slot' => 'AMP_Header',
        ],
        'mrec_1' => [
          'width' => 300,
          'height' => 250,
          'slot' => 'AMP_mrec_1',
        ],
        'mrec_2' => [
          'width' => 300,
          'height' => 250,
          'slot' => 'AMP_mrec_2',
        ],
        'sticky_footer' => [
          'width' => 320,
          'height' => 50,
          'slot' => 'AMP_sticky_footer',
          'sticky' => true
        ]
      ],
      'article' => [
        'skin' =>   '22339066346',
        'leaderboard' =>   '22339226185',

        'mrec' =>   '22339066349',
        'rail1' =>   '22339066349',

        'vrec' =>   '22339066343',
        'rail2' =>   '22339066343',

        'incontent_1' =>   '22339066340',
        'inbody1' =>   '22339066340',

        'incontent_2' =>   '22339066352',
        'desktop_sticky' =>   '22339066355',
        'mob_sticky' =>   '22339066361',
      ],
      'second_article' => [
        'skin' =>   '22343103560',
        'leaderboard' =>   '22343103416',

        'mrec' =>   '22343103563',
        'rail1' =>   '22343103563',

        'vrec' =>   '22343103419',
        'rail2' =>   '22343103419',

        'incontent_1' =>   '22343238609',
        'inbody1' =>   '22343238609',

        'incontent_2' =>   '22343103422',
      ],
      'category' => [
        'skin' =>   '22339066337',

        'leaderboard' =>   '22339066331',

        'mrec' =>   '22339226182',
        'rail1' =>   '22339226182',
        'vrec_1' =>   '22339226182',

        'vrec' =>   '22339066334',
        'rail2' =>   '22339066334',
        'vrec_2' =>   '22339066334',

        'desktop_sticky' =>   '22339226188',
        'mob_sticky' =>   '22339066358',
      ],
      'homepage' => [
        // 'header' =>   '22339234371',
        'desktop_sticky' =>   '22339066286',

        'vrec_1' =>   '22339066289',
        'rail1' =>   '22339066289',

        'vrec_2' =>   '22339226176',
        'rail2' =>   '22339226176',

        'vrec_3' =>   '22339066292',
        'vrec_4' =>   '22339066304',
        'vrec_5' =>   '22339066307',
        'vrec_7' =>   '22339226179',
        'vrec_6' =>   '22339066310',

        'header' =>   '22339066295',
        'leaderboard' =>   '22339066295',

        'skin' =>   '22339066298',

        'incontent_1' =>   '22339066301',
        'inbody1' =>   '22339066301',

        'incontent_2' =>   '22339066319',
        'inbody2' =>   '22339066319',

        'incontent_3' =>   '22339066322',

        'incontent_4' =>   '22339066316',
        'incontent_5' =>   '22339066313',

        'incontent_6' =>   '22339066325',

        'mob_sticky' =>   '22339066328',
      ]
    ];
  }
}

TBMAds::get_instance();
