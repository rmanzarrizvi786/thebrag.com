<?php

/**
 * Plugin Name: Theme Options
 * Plugin URI: https://thebrag.media/
 * Description: Theme Options
 * Version: 1.0.0
 * Author: Sachin Patel
 * Author URI: http://www.patelsachin.com
 */

add_action('admin_menu', 'tbm_theme_options_plugin_menu');
function tbm_theme_options_plugin_menu()
{
    add_menu_page('Theme Options', 'Theme Options', 'edit_pages', 'tbm_theme_options', 'tbm_theme_options');
}

function tbm_theme_options()
{
    wp_enqueue_script('bs', get_template_directory_uri() . '/bs/js/bootstrap.bundle.min.js', array('jquery'), '20190424', true);
    wp_enqueue_style('bs', get_template_directory_uri() . '/bs/css/bootstrap.min.css');
    wp_enqueue_style('edm-mailchimp', plugin_dir_url(__FILE__) . '/css/style.css');

    wp_enqueue_script('td-jquery-autocomplete', get_template_directory_uri() . '/js/jquery.auto-complete.min.js', array('jquery'), NULL, true);
    wp_enqueue_script('td-options-ajax-search', get_template_directory_uri() . '/js/scripts-admin.js', array('jquery'), NULL, true);

    wp_enqueue_script('tbm-theme-options', plugin_dir_url(__FILE__) . '/js/scripts.js', array('jquery'), '20190429', true);

    $curl_metadata = array(
        'auth_key' => 'cacU1r_3wUpusw9cadltIratL8+glt*s',
        'data' => array()
    );

    // $current_blog_id = get_current_blog_id();

    // $sites = get_sites();
    /*
     * Save options
     */
    if (isset($_POST) && count($_POST) > 0) :
        if (isset($_POST['tbm_featured_infinite_ID'])) :
            // $tbm_featured_infinite_ID = absint($_POST['tbm_featured_infinite_ID']);
            $tbm_featured_infinite_IDs = trim($_POST['tbm_featured_infinite_ID']);
            if ($tbm_featured_infinite_IDs != '') :
                update_option('tbm_featured_infinite_ID', $tbm_featured_infinite_IDs);
            else :
                update_option('tbm_featured_infinite_ID', '');
            endif;
        endif; // tbm_featured_infinite_ID

        if (isset($_POST['force_most_viewed'])) :
            $force_most_viewed = absint($_POST['force_most_viewed']);
            if ($force_most_viewed > 0) :
                update_option('force_most_viewed', absint($_POST['force_most_viewed']));
                update_option('most_viewed_yesterday', absint($_POST['force_most_viewed']));
            else :
                update_option('force_most_viewed', '');
            endif;
        endif; // force_most_viewed


        foreach ($_POST as $key => $value) :
            if (strpos($key, 'tbm_') !== false && $key != 'tbm_featured_infinite_ID') :
                /* if (is_main_site($current_blog_id)) :
                    foreach ($sites as $site) :
                        if (is_main_site((int) $site->blog_id)) :
                            continue;
                        endif;
                        update_blog_option($site->blog_id, $key, sanitize_text_field($value));
                    endforeach;
                endif; */
                update_option($key, sanitize_text_field($value));
                $curl_metadata['data'][$key] = sanitize_text_field($value);
            endif;
        endforeach;

        // if (is_main_site($current_blog_id)) :

        if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
            curl_post('https://the-industry-observer.com.au/wp-json/api/v1/update_theme_options', 'POST', $curl_metadata);
            curl_post('https://tone-deaf.com.au/wp-json/api/v1/update_theme_options/', 'POST', $curl_metadata);
            curl_post('https://rs-au.localhost/wp-json/api/v1/update_theme_options/', 'POST', $curl_metadata);
        } else {
            curl_post('https://theindustryobserver.thebrag.com/wp-json/api/v1/update_theme_options', 'POST', $curl_metadata);
            curl_post('https://dontboreus.thebrag.com/wp-json/api/v1/update_theme_options', 'POST', $curl_metadata);
            curl_post('https://tonedeaf.thebrag.com/wp-json/api/v1/update_theme_options/', 'POST', $curl_metadata);
            curl_post('https://au.rollingstone.com/wp-json/api/v1/update_theme_options/', 'POST', $curl_metadata);
        }
        // endif;

        echo '<div class="alert alert-success">Options have been saved!</div>';
    endif;
?>
    <h1 class="mt-3">Theme Options</h1>
    <?php // if (1 === $current_blog_id) : 
    ?>
    <h4 class="p-4 h5" style="background: gold;">Updating VOTW will also update VOTW for sites in network. You can overwrite these settings for each site individually too from their respective Wordpress theme options page.</h4>
    <?php // endif; 
    ?>
    <form method="post" class="form">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-12">
                        <h3>Video of the week</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Link URL</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_video_link" id="tbm_featured_video_link" type="text" value="<?php echo stripslashes(get_option('tbm_featured_video_link')); ?>" placeholder="https://" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>YouTube URL</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_video" id="tbm_featured_video" type="text" value="<?php echo stripslashes(get_option('tbm_featured_video')); ?>" placeholder="https://" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Featured Video Artist Title</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_video_artist" id="tbm_featured_video_artist" type="text" value="<?php echo stripslashes(get_option('tbm_featured_video_artist')); ?>" placeholder="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Featured Video Song Title</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_video_song" id="tbm_featured_video_song" type="text" value="<?php echo stripslashes(get_option('tbm_featured_video_song')); ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div><!-- Video of the week -->
            </div>

            <!-- <div class="col-md-6">
                <div class="row">
                    <div class="col-12">
                        <h3>Record of the week</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Artist</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_album_artist" id="tbm_featured_album_title" type="text" value="<?php // echo stripslashes(get_option('tbm_featured_album_artist')); 
                                                                                                                        ?>" placeholder="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Title</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_album_title" id="tbm_featured_album_title" type="text" value="<?php // echo stripslashes(get_option('tbm_featured_album_title')); 
                                                                                                                    ?>" placeholder="" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Image URL</label>
                            <label class="reset">x</label>
                            <input type="text" name="tbm_featured_album_image_url" id="tbm_featured_album_image_url" class="form-control" value="<?php // echo get_option('tbm_featured_album_image_url') != '' ? get_option('tbm_featured_album_image_url')  : ''; 
                                                                                                                                                    ?>">
                            <?php
                            // if (function_exists('wp_enqueue_media')) {
                            //     wp_enqueue_media();
                            // } else {
                            //     wp_enqueue_style('thickbox');
                            //     wp_enqueue_script('media-upload');
                            //     wp_enqueue_script('thickbox');
                            // }
                            ?>
                            <?php // if (get_option('tbm_featured_album_image_url') != '') : 
                            ?>
                                <img src="<?php // echo get_option('tbm_featured_album_image_url'); 
                                            ?>" width="100" id="tbm_featured_album_image" class="img-fluid d-block">
                            <?php // endif; 
                            ?>
                            <button id="btn-featured-album-image" type="button" class="button">Upload / Select from Library</button>
                        </div>

                        <div class="form-group">
                            <label>Link</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_album_link" id="tbm_featured_album_link" type="text" value="<?php // echo stripslashes(get_option('tbm_featured_album_link')); 
                                                                                                                    ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- Record of the week -->

            <div class="col-md-6">
                <div class="row">
                    <div class="col-12">
                        <h3>Featured Article for Infinite Scroll</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Post ID</label>
                            <label class="reset">x</label>
                            <input name="tbm_featured_infinite_ID" id="tbm_featured_infinite_ID" type="text" value="<?php echo stripslashes(get_option('tbm_featured_infinite_ID')); ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div><!-- Featured Article for Infinite Scroll ID -->

                <div class="row">
                    <div class="col-12">
                        <h3>Force Trending on Home page</h3>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Post ID</label>
                            <label class="reset">x</label>
                            <input name="force_most_viewed" id="force_most_viewed" type="number" value="<?php echo stripslashes(get_option('force_most_viewed')); ?>" placeholder="" class="form-control">
                        </div>
                    </div>
                </div><!-- Force Trending on Home page -->
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

function curl_post($post_url, $method = 'POST', $curl_post = array())
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

/* add_action('wp_head', function () {
    if (is_single()) {
        $current_blog_id = get_current_blog_id();
        $publisher_id = 1 === $current_blog_id ? 'GC_fd6c75ab568de8a0eac4f0bf23eff6aa87928ac2' : 'GC_ee6c4bd9831aaa81f108ff638f6555d120c09541';
    ?>
        <script>
            (function(c, e, n, o, i, r, s, t, u, a, h, f, l, d, p) {
                s = "querySelector";
                a = new Date;
                d = 0;
                c["GotChosenObject"] = o;
                c[o] = c[o] || function() {
                    (c[o].q = c[o].q || []).push(arguments);
                    r = r || c[o].q.filter(function(t) {
                        return t[0] === "init"
                    })[0][1];
                    p = function() {
                        try {
                            try {
                                h = [];
                                c[o].q[0][2].widgets.autoinstall.forEach(function(t) {
                                    h.push(t.selector)
                                });
                                h = h.join()
                            } catch (t) {
                                h = ".gcwp-carousel"
                            }
                            if (d < 6e4 && !e[s]("#" + r)) {
                                if (e[s](h)) {
                                    f = e.createElement(n);
                                    f.id = r;
                                    f.async = 1;
                                    f.src = i + "/gcjs/" + r + "/gc.js?cb=" + a.toJSON().slice(0, 13);
                                    e.head.appendChild(f)
                                } else {
                                    setTimeout(p, 100)
                                }
                                d += 100
                            }
                        } catch (t) {
                            throw new Error(t)
                        }
                    };
                    if (r) {
                        p()
                    }
                }
            })(window, document, "script", "gc", "https://cdn.gotchosen.com");
            gc("init", "<?php echo $publisher_id; ?>", {
                widgets: {
                    autoinstall: [{
                        selector: '.gcwp-carousel',
                        insertion: 'into'
                    }]
                }
            });
        </script>
<?php
    }
}); */

add_action('edit_form_after_title', function ($post) {
    $screen = get_current_screen();
    if ($screen->id != 'post') {
        return;
    }
    if ($post && ('post' != $post->post_type || 'publish' == $post->post_status)) {
        return;
    }
?>
    <div style="background-color: lightyellow; padding: 0.25rem 0.5rem">
        <h3>Checklist:</h3>
        <ol>
            <li>Does this article adhere EXACTLY to the Crib Notes for this publication?</li>
            <li>If it’s a news piece: Was it first published by another publication within the last hour?</li>
            <li>If it was first published over an hour ago, do you have an original & exclusive angle?!</li>
            <li>Are there any opps to add in a link to a relevant Observer newsletter?</li>
        </ol>
    </div>
<?php
});

// Coil - Monetize content
add_action('wp_head', function () {
    echo '<meta name="monetization" content="$ilp.uphold.com/68Q7DryfNX4d">';
});

class TBM_WP_HTML_Compression
{
    // Settings
    protected $compress_css = true;
    protected $compress_js = false;
    protected $info_comment = false;
    protected $remove_comments = true;

    // Variables
    protected $html;
    public function __construct($html)
    {
        if (!empty($html)) {
            $this->parseHTML($html);
        }
    }
    public function __toString()
    {
        return $this->html;
    }
    protected function bottomComment($raw, $compressed)
    {
        $raw = strlen($raw);
        $compressed = strlen($compressed);

        $savings = ($raw - $compressed) / $raw * 100;

        $savings = round($savings, 2);

        return '<!--HTML compressed, size saved ' . $savings . '%. From ' . $raw . ' bytes, now ' . $compressed . ' bytes-->';
    }
    protected function minifyHTML($html)
    {
        $pattern = '/<(?<script>script).*?<\/script\s*>|<(?<style>style).*?<\/style\s*>|<!(?<comment>--).*?-->|<(?<tag>[\/\w.:-]*)(?:".*?"|\'.*?\'|[^\'">]+)*>|(?<text>((<[^!\/\w.:-])?[^<]*)+)|/si';
        preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);
        $overriding = false;
        $raw_tag = false;
        // Variable reused for output
        $html = '';
        foreach ($matches as $token) {
            $tag = (isset($token['tag'])) ? strtolower($token['tag']) : null;

            $content = $token[0];

            if (is_null($tag)) {
                if (!empty($token['script'])) {
                    $strip = $this->compress_js;
                } else if (!empty($token['style'])) {
                    $strip = $this->compress_css;
                } else if ($content == '<!--wp-html-compression no compression-->') {
                    $overriding = !$overriding;

                    // Don't print the comment
                    continue;
                } else if ($this->remove_comments) {
                    if (!$overriding && $raw_tag != 'textarea') {
                        // Remove any HTML comments, except MSIE conditional comments
                        $content = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $content);
                    }
                }
            } else {
                if ($tag == 'pre' || $tag == 'textarea') {
                    $raw_tag = $tag;
                } else if ($tag == '/pre' || $tag == '/textarea') {
                    $raw_tag = false;
                } else {
                    if ($raw_tag || $overriding) {
                        $strip = false;
                    } else {
                        $strip = true;

                        // Remove any empty attributes, except:
                        // action, alt, content, src
                        $content = preg_replace('/(\s+)(\w++(?<!\baction|\balt|\bcontent|\bsrc)="")/', '$1', $content);

                        // Remove any space before the end of self-closing XHTML tags
                        // JavaScript excluded
                        $content = str_replace(' />', '/>', $content);
                    }
                }
            }

            if ($strip) {
                $content = $this->removeWhiteSpace($content);
            }

            $html .= $content;
        }

        return $html;
    }

    public function parseHTML($html)
    {
        $this->html = $this->minifyHTML($html);

        if ($this->info_comment) {
            $this->html .= "\n" . $this->bottomComment($html, $this->html);
        }
    }

    protected function removeWhiteSpace($str)
    {
        $str = str_replace("\t", ' ', $str);
        $str = str_replace("\n",  '', $str);
        $str = str_replace("\r",  '', $str);

        while (stristr($str, '  ')) {
            $str = str_replace('  ', ' ', $str);
        }

        return $str;
    }
}

function tbm_wp_html_compression_finish($html)
{
    return new TBM_WP_HTML_Compression($html);
}

function tbm_wp_html_compression_start()
{
    ob_start('tbm_wp_html_compression_finish');
}
add_action('get_header', 'tbm_wp_html_compression_start');



/*
add_action( 'admin_enqueue_scripts', 'tbm_theme_options_enqueue_admin_scripts' );

function tbm_theme_options_enqueue_admin_scripts( $screen ) {
    $screen = get_current_screen();

    if ( 'post' === $screen->base || 'edit' === $screen->base ) {
        wp_enqueue_script( 'tbm-to-admin-script', plugins_url( 'js/scripts.js', __FILE__ ), array( 'jquery' ), NULL, true );
        wp_localize_script( 'tbm-to-admin-script', 'tbm_theme_options', tbm_theme_options_get_plugin_settings() );
    }
}

function tbm_theme_options_get_plugin_settings() {
    $screen = get_current_screen();

    return array(
        'placeholder' => esc_html__( 'Filter %s', 'admin-category-filter' ),
        'screenName'  => $screen->base
    );
}

add_action( 'wp_ajax_tbm_theme_options_get_cat_count', 'tbm_theme_options_get_cat_count' );
add_action( 'wp_ajax_nopriv_tbm_theme_options_get_cat_count', 'tbm_theme_options_get_cat_count' );

function tbm_theme_options_get_cat_count() {
    $term = get_term( $_REQUEST['tax_id'], 'category' );
    $tax_name = $_REQUEST['tax_name'];
    if ( $term ) {
        wp_send_json_success( array( 'tax_name' => $tax_name,  'count' => $term->count ) );
//        echo json_encode(  );
    } else {
        wp_send_json_success( array( 'tax_name' => $tax_name,  'count' => 0 ) );
    }
    wp_die();
}
 *
 */


// JS to make BB sticky
add_action('wp_footer', function () {
?>
    <script>
        fusetag.onSlotRenderEnded((e) => {
            if (e.slotId === 'fuse-slot-22339066295-1' || e.slotId === 'fuse-slot-22339226185-1') {
                googletag.pubads().addEventListener('slotRenderEnded', function(event) {
                    const slot = event.slot
                    if (slot.getSlotElementId() === 'fuse-slot-22339066295-1' || slot.getSlotElementId() === 'fuse-slot-22339226185-1') {
                        if (event.creativeId === 138363693172) {
                            const skin = document.getElementById('skin')
                            skin.style.setProperty('display', 'none', 'important')

                            const ad_billboard = parent.document.querySelector('.ad-billboard .mx-auto')

                            ad_billboard.style.position = 'fixed'
                            ad_billboard.style.zIndex = 999
                            ad_billboard.style.bottom = '15px'
                            ad_billboard.style.transform = 'translateX(-50%)';
                            ad_billboard.style.left = '50%';

                            setTimeout(function() {
                                ad_billboard.style.bottom = '0'
                                ad_billboard.style.position = 'relative'
                            }, 6000)
                        }
                    }
                })
            }
        })
    </script>
<?php
});

// Brand lift study
add_action('wp_footer', function () {
    if (is_page_template("page-templates/rs-awards-nominate-2021.php")) {
        return;
    }
?>
    <script>
        jQuery(document).ready(function($) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'json',
                cache: 'false',
                data: {
                    'action': 'tbm_set_cookie',
                    'key': 'tbm_sm_seen',
                    'value': 'true',
                    'duration': '<?php echo 60 * 60 * 24 * 30; ?>'
                }
            });
        }); {
            var tbm_e = document.cookie,
                tbm_t = tbm_e.indexOf("; tbm_sm_seen=");
            if (-1 == tbm_t) {
                {
                    const e = (e, t, n, s) => {
                            var c, o, r;
                            e.SMCX = e.SMCX || [], t.getElementById(s) || (o = (c = t.getElementsByTagName(n))[c.length - 1], (r = t.createElement(n)).type = "text/javascript", r.async = !0, r.id = s, r.src = "https://widget.surveymonkey.com/collect/website/js/tRaiETqnLgj758hTBazgd36CitCEEwoE44pTCPBWttcrfN2mODXNCsr6H61j_2BkMD.js", o.parentNode.insertBefore(r, o))
                        },
                        t = (e, t, n, s) => {
                            var c, o, r;
                            e.SMCX = e.SMCX || [], t.getElementById(s) || (o = (c = t.getElementsByTagName(n))[c.length - 1], (r = t.createElement(n)).type = "text/javascript", r.async = !0, r.id = s, r.src = "https://widget.surveymonkey.com/collect/website/js/tRaiETqnLgj758hTBazgd36CitCEEwoE44pTCPBWtteffxwhXTTNQIUFZGZf1MZH.js", o.parentNode.insertBefore(r, o))
                        };
                    null !== (() => {
                        var e = document.cookie,
                            t = e.indexOf("; tbm_v=");
                        if (-1 == t) {
                            if (0 != (t = e.indexOf("tbm_v="))) return null
                        } else {
                            t += 2;
                            var n = document.cookie.indexOf(";", t); - 1 == n && (n = e.length)
                        }
                        return decodeURI(e.substring(t + "tbm_v=".length, n))
                    })() ? (console.log("cookie"), e(window, document, "script", "smcx-sdk")) : (console.log("no cookie"))
                }
            }
        }
    </script>
    <style>
        .smcx-modal:before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin-top: -100%;
            margin-bottom: -100%;
            background: rgba(0, 0, 0, .75);
            margin-left: -100%;
            margin-right: -100%;
        }

        .smcx-modal-header,
        .smcx-modal>.smcx-modal-content,
        .smcx-widget-footer {
            background-color: #2E073E;
        }

        .smcx-modal-header {
            border-radius: 5px 5px 0 0;
        }

        .smcx-modal>.smcx-modal-content {
            border-radius: 0;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 10px !important;
        }

        @media (max-width: 600px) {
            .smcx-widget.smcx-modal>.smcx-modal-content {
                width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding: 0 2% !important;
            }
        }

        .smcx-widget-footer {
            border-radius: 0 0 5px 5px;
        }
    </style>
<?php
});
