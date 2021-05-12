<?php
register_nav_menus(array(
    'get_in_touch' => __('Get In Touch', 'thebrag'),
    'top' => __('Top Menu', 'thebrag'),
    'shitshow' => __('Sh!tShow', 'thebrag'),
    'top-what-you-love' => __('Top What You Love', 'thebrag'),
    'top-check-this-out' => __('Top Check This Out', 'thebrag'),
));

function render_ad_tag($tag, $slot_no = 1)
{
    if (!file_exists(WP_PLUGIN_DIR . '/tbm-adm/tbm-adm.php'))
        return;
    require_once WP_PLUGIN_DIR . '/tbm-adm/tbm-adm.php';
    $ads = TBMAds::get_instance();
    echo $ads->get_ad($tag, $slot_no, get_the_ID());
    return;
}
