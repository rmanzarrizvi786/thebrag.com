<?php
/*
   Plugin Name: SSM WSU Quiz Results
   Plugin URI: 
   description:
   Version: 1.0
   Author: Sachin Patel
   Author URI:
*/

add_action('admin_menu', 'ssm_wsu_quiz_results');
function ssm_wsu_quiz_results() {
    add_management_page('WSU Quiz Results', 'WSU Quiz Results', 'edit_posts', 'ssm-wsu-quiz-results', 'wsu_quiz_results');
    add_management_page('WSU Quiz 2 Results', 'WSU Quiz 2 Results', 'edit_posts', 'ssm-wsu-quiz-2-results', 'wsu_quiz_2_results');
}

function wsu_quiz_results () {
    
    echo '<h1>WSU Quiz Results</h1>';
    
    global $wpdb;
    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
    
    $limit = 20;
    $offset = ( $pagenum - 1 ) * $limit;
    $total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$wpdb->prefix}wsu_quiz_results" );
    $num_of_pages = ceil( $total / $limit );
    $quiz_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wsu_quiz_results ORDER BY id DESC LIMIT {$offset}, {$limit}" );
    
    $page_links = paginate_links( array(
        'base' => add_query_arg( 'pagenum', '%#%' ),
        'format' => '',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'total' => $num_of_pages,
        'current' => $pagenum,
    ) );
    
    if ( $page_links ) {
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">Total: ' . $total . ' items ' . $page_links . '</div></div>';
    }
    ?>
    <table class="widefat striped" cellspacing="0" cellpadding="5">
    <tr>
        <th>IP Address / DateTime</th>
        <th>Answers</th>
        <th>Results</th>
    </tr>
    
    <?php foreach ( $quiz_results as $quiz_result ) : ?>
    
        <tr>
            <td>
                <?php echo $quiz_result->ip_address; ?>
                <br>
                <?php echo date( 'd-m-Y h:ia', strtotime( $quiz_result->created_at ) ); ?>
            </td>
            <td nowrap>
                <ol>
                <?php
                    $question_answers = json_decode( $quiz_result->answers );
                    foreach ( $question_answers as $question_answer ) :
                        echo '<li><strong>' . $question_answer->question . '</strong>';
                        echo '<ul>';
                        foreach ( $question_answer->answers as $answer ) :
                            echo '<li>' . $answer . '</li>';
                        endforeach;
                        echo '</ul>';
                        echo '</li>';
                    endforeach;
                ?>
                </ol>
            </td>
            <td>
                <?php
                    $results = json_decode( $quiz_result->results );
                    echo '<ol>';
                    foreach ( $results as $result ) :
                        echo '<li>' . $result . '</li>';
                    endforeach;
                    echo '</ol>';
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
    <?php
    
    if ( $page_links ) {
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">Total: ' . $total . ' items ' . $page_links . '</div></div>';
    }
}

function wsu_quiz_2_results () {
    
    global $wpdb;

    echo '<h1>WSU Quiz 2 Results</h1>';
?>
    <form method="post" action="<?php echo admin_url( 'tools.php' ); ?>">
        <input type="hidden" name="action" value="ssm_export_wsu_quiz_2">
        <?php wp_nonce_field( 'ssm_verify' ); ?>

        <div class=""><?php submit_button( 'Export' ); ?></div>
    </form>
<?php
    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

    $limit = 20;
    $offset = ( $pagenum - 1 ) * $limit;
//    $total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$wpdb->prefix}wsu_quiz_2_results" );

    $total_started = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$wpdb->prefix}wsu_quiz_2_results WHERE results = 'Started'" );
    echo '<h2>Started: ' . $total_started . '</h2>';
    $total_completed = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$wpdb->prefix}wsu_quiz_2_results WHERE results != 'Started'" );
    echo '<h2>Completed: ' . $total_completed . '</h2>';

    $num_of_pages = ceil( $total_completed / $limit );
    $quiz_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wsu_quiz_2_results WHERE results != 'Started' ORDER BY id DESC LIMIT {$offset}, {$limit}" );

    $page_links = paginate_links( array(
        'base' => add_query_arg( 'pagenum', '%#%' ),
        'format' => '',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'total' => $num_of_pages,
        'current' => $pagenum,
    ) );

    if ( $page_links ) {
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">Total: ' . $total_completed . ' items ' . $page_links . '</div></div>';
    }
    ?>
    <table class="widefat striped" cellspacing="0" cellpadding="5">
    <tr>
        <th>IP Address / DateTime</th>
        <th>Answers</th>
        <th>Results</th>
    </tr>

    <?php foreach ( $quiz_results as $quiz_result ) : ?>

        <tr>
            <td>
                <?php echo $quiz_result->ip_address; ?>
                <br>
                <?php echo date( 'd-m-Y h:ia', strtotime( $quiz_result->created_at ) ); ?>
            </td>
            <td nowrap>
                <ol>
                <?php
                    $question_answers = json_decode( $quiz_result->answers );
                    foreach ( $question_answers as $question_answer ) :
                        echo '<li><strong>' . $question_answer->question . '</strong>';
                        echo '<ul>';
                        if ( is_array( $question_answer->answers ) ) :
                            foreach ( $question_answer->answers as $answer ) :
                                echo '<li>' . nl2br( $answer ) . '</li>';
                            endforeach;
                        else :
                            echo nl2br( $question_answer->answers );
                        endif;
                        echo '</ul>';
                        echo '</li>';
                    endforeach;
                ?>
                </ol>
            </td>
            <td>
                <?php
                    $results = json_decode( $quiz_result->results );
                    echo '<ol>';
                    foreach ( $results as $result ) :
                        echo '<li>' . $result . '</li>';
                    endforeach;
                    echo '</ol>';
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
    <?php

    if ( $page_links ) {
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">Total: ' . $total_completed . ' items ' . $page_links . '</div></div>';
    }
}

add_action( 'admin_action_ssm_export_wsu_quiz_2', 'ssm_export_wsu_quiz_2_admin_action' );
function ssm_export_wsu_quiz_2_admin_action() {
    global $wpdb;
    if ( ! current_user_can( 'edit_pages' ) ) {
        wp_die( 'You are not allowed to be on this page.' );
    }
    check_admin_referer( 'ssm_verify' );
    
    $data = array();
        
    $quiz_results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wsu_quiz_2_results WHERE results != 'Started' ORDER BY id DESC" );
    
    header('Content-Encoding: UTF-8');
    header('Content-type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="WSU_Quiz_2_Results.csv";');
    header("Pragma: no-cache");
    header("Expires: 0");

    foreach ( $quiz_results as $quiz_result ) :

        $data_result['ip_address'] = $quiz_result->ip_address;
        $data_result['datetime'] = date( 'd-m-Y h:ia', strtotime( $quiz_result->created_at ) );

        $question_answers = json_decode( $quiz_result->answers );
        foreach ( $question_answers as $question_answer ) :
            $data_result[$question_answer->question] = is_array( $question_answer->answers ) ? $question_answer->answers[0] : $question_answer->answers;
        endforeach;

        $results = json_decode( $quiz_result->results );
        foreach ( $results as $result ) :
            $data_result['result'] = $result;
        endforeach;

        array_push( $data, $data_result );
    endforeach;
    
//    echo '<pre>'; print_r( $data ); exit;

    $csv_content = implode(',', array_keys( $data_result ) ) . "\n";
        foreach ( $data as $key => $d ) :
//            $csv_content .= implode(',', $d ) . "\n";
            foreach ( $d as $d2 ) :
                $csv_content .= '"' . $d2 . '",';
            endforeach;
            $csv_content .= "\n";
        endforeach;
    echo $csv_content;
    exit();
}