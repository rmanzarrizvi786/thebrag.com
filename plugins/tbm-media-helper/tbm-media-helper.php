<?php

namespace TBM;

/**
 * Plugin Name: TBM Media Helper
 * Plugin URI: https://thebrag.com/media/
 * Description:
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI:
 */

class MediaHelper
{
    protected $plugin_title;
    protected $plugin_name;
    protected $plugin_slug;

    protected $logo_file_name;
    protected $social_img_width;
    protected $social_img_height;
    protected $logo_height;
    protected $allowed_hosts;

    public function __construct()
    {
        $this->plugin_title = 'TBM Media Helper';
        $this->plugin_name = 'tbm_media_helper';
        $this->plugin_slug = 'tbm-media-helper';

        $this->logo_file_name = 'THEBRAG_socialshare_LOGO';
        $this->social_img_width = 1200;
        $this->social_img_height = 628;
        $this->logo_height = 150;
        $this->allowed_hosts = [
            'images.thebrag.com',
            'au.rollingstone.com',

            'theindustryobserver.thebrag.com',
            'theindustryobserver.the-brag.com',
            'staging.theindustryobserver.thebrag.com',

            'tonedeaf.thebrag.com',
            'staging.tonedeaf.thebrag.com',

            'dontboreus.thebrag.com',
            'staging.dontboreus.thebrag.com',

            'thebrag.com',
            'staging.thebrag.com',
        ];

        add_action('parse_request', [$this, 'parse_request']);

        add_filter('wpseo_opengraph_image', [$this, 'wpseo_opengraph_image'], 99);

        add_filter('wp_head', [$this, 'wp_head']);
    }

    public function parse_request()
    {
        if (strpos($_SERVER["REQUEST_URI"],  '/img-socl/') !== FALSE) {
            global $wp_query;
            $url = isset($_GET['url']) ? trim($_GET['url']) : null;

            if (is_null($url)) {
                $wp_query->set_404();
                status_header(404);
                return;
            }

            $parsed_url = parse_url($url);
            if (!isset($parsed_url['host']) || !in_array($parsed_url['host'], $this->allowed_hosts)) {
                $wp_query->set_404();
                status_header(404);
                return;
            }

            $image =  $this->generate_image($url);
            if (!$image) {
                $wp_query->set_404();
                status_header(404);
                return;
            }

            header('Content-Type:image/jpg');
            imagejpeg($image);
        }
    }

    public function wpseo_opengraph_image($url)
    {
        if (!is_single())
            return $url;

        $type = exif_imagetype($url);

        if ($type != false && ($type == (IMAGETYPE_PNG || IMAGETYPE_JPEG))) {
            return home_url("/img-socl/?url={$url}?nologo=1");
        }

        return $url;
    }

    private function generate_image($url)
    {
        if (!$url)
            return false;

        $url = $this->get_final_url($url);

        $headers = get_headers($url, 1);

        if ($headers[0] != 'HTTP/1.1 200 OK') {
            return false;
        }

        $type = exif_imagetype($url);

        if ($type == (IMAGETYPE_PNG || IMAGETYPE_JPEG)) {
            $logo_url = "https://images.thebrag.com/common/brands/{$this->logo_file_name}.png";

            $x = $this->social_img_width;
            $y = $this->social_img_height;
            $ratio_dest = $x / $y;

            list($xx, $yy) = getimagesize($url);

            $ratio_original = $xx / $yy;

            if ($ratio_original >= $ratio_dest) {
                $yo = $yy;
                $xo = ceil(($yo * $x) / $y);
                $xo_ini = ceil(($xx - $xo) / 2);
                $xy_ini = 0;
            } else {
                $xo = $xx;
                $yo = ceil(($xo * $y) / $x);
                $xy_ini = ceil(($yy - $yo) / 2);
                $xo_ini = 0;
            }

            $dest = imagecreatetruecolor($x, $y);

            // $source = imagecreatefromjpeg($url);

            switch ($type) {
                case IMAGETYPE_JPEG:
                    $source = imagecreatefromjpeg($url);
                    break;
                case IMAGETYPE_PNG:
                    $source = imagecreatefrompng($url);
                    break;
                default:
                    return false;
            }

            imagecopyresampled($dest, $source, 0, 0, $xo_ini, $xy_ini, $x, $y, $xo, $yo);

            if (!isset($_GET['nologo'])) {
                $png_logo = imagecreatefrompng($logo_url);
                list($logo_real_width, $logo_real_height) = getimagesize($logo_url);

                $logo_width = ceil($this->logo_height * $logo_real_width / $logo_real_height);
                imagecopyresampled($dest, $png_logo, $x - $logo_width, $y - $this->logo_height, 0, 0, $logo_width, $this->logo_height, $logo_real_width, $logo_real_height);
            }

            return $dest;
        }
        return false;
    }

    private function get_final_url($url)
    {
        stream_context_set_default([
            'http' => [
                'method' => 'HEAD'
            ]
        ]);
        $headers = get_headers($url, 1);
        if ($headers !== false && isset($headers['Location'])) {
            return is_array($headers['Location']) ? array_pop($headers['Location']) : $headers['Location'];
        }
        return $url;
    }

    /**
     * Add og image tag if featured image is webp, Yoast currently doesn't add webp image
     * 26 July, 2022
     */
    public function wp_head()
    {
        if (!is_single())
            return;
        $post_id = get_the_ID();
        $attachment_id = get_post_thumbnail_id($post_id);
        $image_attributes = wp_get_attachment_image_src($attachment_id, "full");
        $image_src = $image_attributes[0];
        if (!empty($image_src) && get_post_mime_type(get_post_thumbnail_id()) == 'image/webp') { ?>
            <meta property="og:image" content="<?php echo $image_src; ?>">
<?php }
    } // wp_head()
}

new MediaHelper();
