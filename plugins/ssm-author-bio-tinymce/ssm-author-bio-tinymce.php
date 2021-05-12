<?php
/*
   Plugin Name: SSM Aauthor Bio tinymce
   Plugin URI: 
   description:
   Version: 1.0
   Author: Sachin Patel
   Author URI:
*/
/*******************************************
* TinyMCE EDITOR "Biographical Info" USER PROFILE
*******************************************/
function biographical_info_tinymce() {
    if ( isset( $user ) && isset( $user->ID ) ) :
    if ( basename($_SERVER['PHP_SELF']) == 'profile.php' || basename($_SERVER['PHP_SELF']) == 'user-edit.php' && function_exists('wp_tiny_mce') ) {
        echo "<script>jQuery(document).ready(function($){ $('#description').remove();});</script>";
        $settings = array(
            'tinymce' => array(
                'toolbar1' => 'bold,italic,underline,blockquote,strikethrough,bullist,numlist,alignleft,aligncenter,alignright,undo,redo,link,unlink,fullscreen',
                'toolbar2' => '',
                'toolbar3' => '',
                'toolbar4' => '',
            ),
            'wpautop' => true,
            'media_buttons' => false,
            'quicktags' => false,
        );
        $description = get_user_meta( $user->ID, 'description', true);
        wp_editor( $description, 'description', $settings );
    }
    endif;
}
add_action('admin_head', 'biographical_info_tinymce');
remove_filter('pre_user_description', 'wp_filter_kses');
add_filter( 'pre_user_description', 'wp_filter_post_kses' );