<?php
/*
   Plugin Name: SSM Starwars Quiz Results
   Plugin URI: 
   description:
   Version: 1.0
   Author: Sachin Patel
   Author URI:
*/

add_action('admin_menu', 'ssm_starwars_quiz_results');
function ssm_starwars_quiz_results() {
    add_management_page('Starwars Quiz Results', 'Starwars Quiz Results', 'edit_posts', 'ssm-starwars-quiz-results', 'starwars_quiz_results');
}

function starwars_quiz_results () {
    
    echo '<h1>Starwars Quiz Results</h1>';
    
    global $wpdb;
    
    $total_started = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$wpdb->prefix}starwars_quiz_results WHERE result = 'Started'" );
    echo '<h2>Started: ' . $total_started . '</h2>';
    $total_completed = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$wpdb->prefix}starwars_quiz_results WHERE result != 'Started'" );
    echo '<h2>Completed: ' . $total_completed . '</h2>';
}