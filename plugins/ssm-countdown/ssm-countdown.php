<?php
/*
   Plugin Name: SSM Countdown
   Plugin URI: 
   description: <strong>Format</strong>: <code>[ssm_countdown id="{Unique Number for each counter}" start="yyyy-mm-dd hh:mm:ss" end="yyyy-mm-dd hh:mm:ss" img_before="{Image URL}" img_after="{Image URL}" timer_bg="#{HEX Colour Code}" link="{Link for the images}"][/ssm_countdown]</code>
   Version: 1.0
   Author: Sachin Patel
   Author URI:
*/
function ssm_enqueue_countdown_scripts() {
    global $post;
    if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'ssm_countdown') ) {
        wp_enqueue_script( 'FlipClock-countdown', plugin_dir_url( __FILE__ ) . '/FlipClock/flipclock.min.js', array( 'jquery' ), '20181020', false );
        wp_enqueue_style( 'FlipClock-countdown', plugin_dir_url( __FILE__ ) . '/FlipClock/flipclock.css', array(), '20181023-4' );
    }
}
add_action( 'wp_enqueue_scripts', 'ssm_enqueue_countdown_scripts' );

/*
 * Shortcode - SSM Countdown
 */
function ssm_shortcode_countdown_init(){
    add_shortcode( 'ssm_countdown', 'ssm_shortcode_countdown' );
}
add_action('init', 'ssm_shortcode_countdown_init');
function ssm_shortcode_countdown( $atts, $content = null, $tag = '' ){
    // normalize attribute keys, lowercase
    $atts = array_change_key_case( (array)$atts, CASE_LOWER );

    extract( shortcode_atts(
        array(
            'start' => '',
            'end'		=> '',
            'endtext'		=> '',
            'id' => '',
            'img_before' => '',
            'img_after' => '',
            'timer_bg' => '',
            'link' => '',
        ),
        $atts,
        'ssm_countdown'
    ) );
    
    $timer_id = $atts['id'];

    date_default_timezone_set( 'Australia/Sydney' );
    $diff = strtotime( $atts['end'] ) - time();
    
    $output = '';
    
    if ( time() >= strtotime( $atts['start'] ) && $diff > 0 ) :
        
        $timer_bg = isset( $atts['timer_bg'] ) && $atts['timer_bg'] != '' ? '#' . str_replace( '#', '', $atts['timer_bg'] ) : '';
    
        $output .= '<div class="countdown-wrap">';
        
        $output .= '<div class="img-before">';
        if ( isset( $atts['link'] ) && !is_null( $atts['link'] ) && '' != $atts['link'] ) :
            $output .= '<a href="' . $atts['link'] . '" target="_blank">';
        endif;
        $output .= '<img src="' . $atts['img_before'] . '">';
        if ( isset( $atts['link'] ) && !is_null( $atts['link'] ) && '' != $atts['link'] ) :
            $output .= '</a>';
        endif;
        $output .= '</div>';

        $output .= '<div class="timer-wrap" style="background: ' . $timer_bg . '"><span id="timer' . $timer_id . '" style="background: ' . $timer_bg . ';"></span></div>';
        
        $output .= '<div class="img-after">';
        if ( isset( $atts['link'] ) && !is_null( $atts['link'] ) && '' != $atts['link'] ) :
            $output .= '<a href="' . $atts['link'] . '" target="_blank">';
        endif;
        $output .= '<img src="' . $atts['img_after'] . '">';
        if ( isset( $atts['link'] ) && !is_null( $atts['link'] ) && '' != $atts['link'] ) :
            $output .= '</a>';
        endif;
        $output .= '</div>';
        
        $output .= '</div>';

        $script = '<script>
            jQuery(document).ready(function($){
                var clock' . $timer_id . ';

                clock' . $timer_id . ' = $(\'#timer' . $timer_id . '\').FlipClock({
                    clockFace: \'DailyCounter\',
                    countdown: true,
                    autoStart: false,
                    callbacks: {
                        stop: function() {
                            window.location.reload();
                        }
                    }
                });

                clock' . $timer_id . '.setTime(' . $diff . ');
                clock' . $timer_id . '.start();
            });
        </script>';
        $output .= $script;
        
        $output .= $content;
    elseif ( time() >= strtotime( $atts['start'] ) ) :
        $output .= $content;
    endif;
    
    return $output;
}