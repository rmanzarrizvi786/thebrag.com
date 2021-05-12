<?php /* Template Name: Submit Gig (Front) */ ?>

<?php get_header(); ?>
<?php
wp_enqueue_script( 'td-jquery-autocomplete', get_template_directory_uri() . '/js/jquery.auto-complete.min.js', array( 'jquery' ), '1.0', true );
wp_enqueue_script( 'bs-dp', get_template_directory_uri() . '/bs/dp/js/bootstrap-datepicker.min.js', array ( 'jquery' ), NULL, true);
?>
<link rel="stylesheet" id="bs-dp-css" href="<?php echo get_template_directory_uri(); ?>/bs/dp/css/bootstrap-datepicker.min.css" type="text/css" media="all" />
<script>
    jQuery(document).ready(function($) {
        $('#gig-date').datepicker( { format: 'dd-mm-yyyy' } );
    } );
</script>
<style>
    #selected_venues { margin: 0; padding: 0; }
    #selected_venues li { list-style: none; display: inline-block; border: 1px solid #ededed; padding: 10px; }
    .remove_venue { cursor: pointer; background: #ededed; padding: 2px 5px; }
    .fa-pencil:before {
  content: "\f040";
}
</style>
<?php
use \DrewM\MailChimp\MailChimp;
wp_enqueue_script( 'ssm-gig', get_template_directory_uri() . '/js/gig.js', array ( 'jquery' ), 1.0, true);
if ( ! is_admin() ) {
    include ABSPATH . 'wp-admin/includes/template.php';
}
if ( isset( $_POST ) && count( $_POST ) > 0 ) :
    require_once "recaptchalib.php";

    $form_posts = stripslashes_deep($_POST);
    $errors = $messages = array();

    $required_fields = array(
        'post_title',
        'gig_artist',
        'user_name',
        'user_email',
    );

    if ( isset( $form_posts['gig_repeat'] ) && $form_posts['gig_repeat'] == '1' ) :
        array_push( $required_fields, 'gig_repeat_freq', 'gig_repeat_count' );
        if ( $form_posts['gig_repeat_freq'] == 'DAILY' ) :
            array_push( $required_fields, 'gig_repeat_daily_byday' );
        endif;
        if ( $form_posts['gig_repeat_freq'] == 'WEEKLY' ) :
            array_push( $required_fields, 'gig_repeat_weekly_interval' );
        endif;

        $required_numeric = array(
            'gig_repeat_daily_interval',
            'gig_repeat_weekly_interval',
            'gig_repeat_count',
        );
        foreach ( $required_numeric as $field ) :
            if ( isset( $form_posts[$field] ) && trim( $form_posts[$field] ) != '' ) :
                if ( filter_var( $form_posts[$field], FILTER_VALIDATE_INT, array('min_range' => 0) ) == FALSE ) :
                    $errors[] = 'Repeat Interval and Occurances must be positive, whole number.';
                    break;
                endif;
            endif;
        endforeach;
    endif; // If Gig Repeat is set

    foreach ( $required_fields as $required_field ):
        if ( trim( $form_posts[$required_field] ) == '' ):
            $errors[] = 'Please input all compulsory fields.';
            break;
        endif;
    endforeach;

    if ( ! isset( $form_posts['tax_input']['gig-type'] ) || ! isset( $form_posts['tax_input']['gig-genre'] )) {
        $errors[] = 'Please select Gig Type and Genre.';
    }

    $gig_date = DateTime::createFromFormat( 'd-m-Y', $form_posts['gig_date'] );
    if ( ! $gig_date || $gig_date->format('d-m-Y') != $form_posts['gig_date'] || strtotime( $gig_date->format('Y-m-d') ) < strtotime( date('Y-m-d') ) ) {
        $errors[] = 'Please make sure Gig Date is in proper format and is a future date.';
    }
    $gig_time = DateTime::createFromFormat( 'H:i', $form_posts['gig_time'] );
    if ( ! $gig_time ) { // || $gig_time->format('H:i') != $form_posts['gig_time'] ) {
        $errors[] = 'Please make sure Gig Time is in proper format.';
    }

    // Recaptcha
    $secret = "6LcajB8UAAAAAPjdEzFm01TGxsZ0p1wu5V5BbZGq";
    $response = null;
    $reCaptcha = new ReCaptcha($secret);

    if ($_POST["g-recaptcha-response"]) {
        $response = $reCaptcha->verifyResponse(
            $_SERVER["REMOTE_ADDR"],
            $_POST["g-recaptcha-response"]
        );
        if ( is_null( $response != null ) || ! $response->success ) {
//            $errors[] = 'Please complete human verification.';
        }
    } else {
//        $errors[] = 'Please complete human verification.';
    }

    if ( count( $errors ) === 0 ):
        $new_post = array(
            'post_title' => $form_posts['post_title'],
            'post_content' => $form_posts['post_content'],
            'post_status' => 'pending',
            'post_date' => date('Y-m-d H:i:s'),
            'post_author' => 51096, // Gig Guide
            'post_type' => 'gig',
            'post_category' => array(0),
            'meta_input' => array(
                'price' => $form_posts['price'],
                'ticket_link_url_1' => $form_posts['ticket_link_url_1'],
                'submitted_by' => $form_posts['user_name'] . "\n" . $form_posts['user_email'],
            )
        );

        $post_id = wp_insert_post($new_post);

        require_once( ABSPATH . 'wp-admin/includes/admin.php' );
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        // Insert Gig Types Taxonomies
        $gig_types = array_map( 'intval', $form_posts['tax_input']['gig-type'] );
        $gig_types = array_unique( $gig_types );
        wp_set_object_terms($post_id, $gig_types, 'gig-type');

        // Insert Gig Genre Taxonomies
        $gig_genres = array_map( 'intval', $form_posts['tax_input']['gig-genre'] );
        $gig_genres = array_unique( $gig_genres );
        wp_set_object_terms($post_id, $gig_genres, 'gig-genre');

        // Insert Gig Artists Taxonomies
        wp_set_post_terms($post_id, $form_posts['gig_artist'], 'gig-artist');

        // Insert Gig Support Taxonomies
        wp_set_post_terms($post_id, $form_posts['gig_support'], 'gig-support');

        // Insert Gig Venues (P2P Plugin)
        if ( isset( $form_posts['gig_venue'] ) && is_int( (int)$form_posts['gig_venue'] ) ) :
            $wpdb->insert( $wpdb->prefix . 'p2p',
                array(
                    'p2p_from' => $post_id,
                    'p2p_to' => $form_posts['gig_venue'],
                    'p2p_type' => 'gig_to_venue',
                ),
                array(
                    '%d',
                    '%d',
                    '%s',
                )
            );
        endif;

        $start = date( 'Y-m-d', strtotime( $form_posts['gig_date'] ) );
        $repeat_rule = '';

        if ( isset( $form_posts['gig_repeat'] ) && $form_posts['gig_repeat'] == '1' ) :
            $repeat_rule = 'RRULE:FREQ=' . $form_posts['gig_repeat_freq'] . ';';
            switch( $form_posts['gig_repeat_freq'] ):
                case 'DAILY':
                    $daily_interval = $form_posts['gig_repeat_daily_interval'];
                    if ( $form_posts['gig_repeat_daily_byday'] == 'weekday' ):
                        $byday = 'MO,TU,WE,TH,FR';
                        $count = $form_posts['gig_repeat_count'];
                        $i = 0; $j = 1;
                        while ( $j <= $count ):
                            $date_to_add = date('Y-m-d', strtotime($i . 'days', strtotime($start)));
                            if ( in_array(date('D', strtotime($date_to_add)), array('Mon', 'Tue', 'Wed', 'Thu', 'Fri')) ) {
                                $dates[] = $date_to_add;
                                $j++;
                            }
                            $i++;
                        endwhile;
                    elseif( $form_posts['gig_repeat_daily_byday'] == 'mo_we_fr' ):
                        $byday = 'MO,WE,FR';
                        $count = $form_posts['gig_repeat_count'];
                        $i = 0; $j = 1;
                        while ( $j <= $count ):
                            $date_to_add = date('Y-m-d', strtotime($i . 'days', strtotime($start)));
                            if ( in_array(date('D', strtotime($date_to_add)), array('Mon', 'Wed', 'Fri')) ) {
                                $dates[] = $date_to_add;
                                $j++;
                            }
                            $i++;
                        endwhile;
                    elseif( $form_posts['gig_repeat_daily_byday'] == 'tu_th' ):
                        $byday = 'TU,TH';
                        $count = $form_posts['gig_repeat_count'];
                        $i = 0; $j = 1;
                        while ( $j <= $count ):
                            $date_to_add = date('Y-m-d', strtotime($i . 'days', strtotime($start)));
                            if ( in_array(date('D', strtotime($date_to_add)), array('Tue', 'Thu')) ) {
                                $dates[] = $date_to_add;
                                $j++;
                            }
                            $i++;
                        endwhile;
                    endif;

                    if ( ! isset($byday) ):
                        $count = $form_posts['gig_repeat_count'];
                        $i = 0; $j = 1;
                        $date_to_add = $start;
                        $dates[] = $date_to_add;
                        while ( $j < $count ):
                            $date_to_add = date('Y-m-d', strtotime($daily_interval . 'days', strtotime($date_to_add)));
                            $dates[] = $date_to_add;
                            $j++;
                        endwhile;
                        $repeat_rule .= 'INTERVAL=' . $daily_interval . ';';
                    else:
                        $repeat_rule .= 'INTERVAL=1;';
                        $repeat_rule .= 'BYDAY=' . $byday . ';';
                    endif;

                    break; // Case DAILY
                case 'WEEKLY':
                    $dates[] = $start;
                    $count = $form_posts['gig_repeat_count'];
                    $weekly_interval = $form_posts['gig_repeat_weekly_interval'];
                    $repeat_rule .= 'INTERVAL=' . $weekly_interval . ';';
                    $i = 0;
                    while ( $i < ($count-1) ):
                        $date_to_add = date('Y-m-d', strtotime(($i + $weekly_interval) . 'weeks', strtotime($start) ));
                        $dates[] = $date_to_add;
                        $i++;
                    endwhile;
                    break; // Case WEEKLY
                default:
                    break;
            endswitch;
            $repeat_rule .= 'COUNT=' . $form_posts['gig_repeat_count'] . ';';
        else:
            $dates[] = $start;
        endif;

        $wpdb->insert( $wpdb->prefix . "gig_details", array(
            'post_id' => $post_id,
            'gig_datetime' => $dates[0] . ' ' . $form_posts['gig_time'],
            'repeat_rule' => $repeat_rule,
            )
        );

        if ( isset( $form_posts['subscribe'] ) && $form_posts['subscribe'] == '1' ) :
            require_once( get_template_directory() . '/MailChimp.php');
            $api_key = '727643e6b14470301125c15a490425a8-us1';
            $MailChimp = new MailChimp( $api_key );

            $list_id = 'c9114493ef';
            $data = array(
                'email_address' => $form_posts['user_email'],
                'status' => 'subscribed',
                'merge_fields' => array(
                    'FNAME' => $form_posts['user_name'],
                )
            );
            $subscribe = $MailChimp->post( "lists/$list_id/members", $data );
        endif;

        // Slack Notification to Channel
        /*
        $slack_post_url = 'https://hooks.slack.com/services/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';
        $slack_text = '';
        $slack_text .= "*" . get_bloginfo( 'name' ) . "*\n";
        $slack_text .= "New Gig has been submitted.\n";
        $slack_text .= '<http://thebrag.com/wp-admin/post.php?post=' . $post_id . '&action=edit|Click here> to Edit/Publish (you need to be logged in before visiting this link)';

        $curl_post = json_encode(
            array(
                'text' => $slack_text,
                "mrkdwn"=> true,
            )
        );
        $ch = curl_init( $slack_post_url );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curl_output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        */

        $email_body = 'Link: http://thebrag.com/wp-admin/post.php?post=' . $post_id . '&action=edit to Edit/Publish (you need to be logged in before visiting this link)';
        $headers[] = 'From: The BRAG Gig Guide <gigguide@thebrag.media>';
        wp_mail( 'intern@thebrag.media', 'The Brag - New Gig has been submitted', $email_body, $headers );

        unset( $form_posts );
        echo '<div class="alert alert-success text-center my-3 p-3">Thank you, your gig has been successfully submitted.</div>';
    endif;
endif; // If Form is submitted
?>

<div class="container">


    <div class="row">
        <div class="col-lg-8">
            <div class="row">
        <h1 id="story_title" class="col-12"><?php the_title(); ?></h1>
    </div>

            <?php
if ( isset ( $errors ) && count( $errors ) > 0 ):
    echo '<ul class="alert alert-danger">';
    foreach ( $errors as $error ):
         echo '<li>' . $error . '</li>';
    endforeach;
    echo '</ul>';
endif;
if ( isset( $_GET['success']  ) && $_GET['success'] == '1' ) :
    echo '<div class="success">Thank you, your job has been successfully submitted.</div>';
endif;

the_content();

$content_editor = '';
$editor_id = 'post_content';
$settings =   array(
    'tinymce' => array(
        'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,fullscreen',
        'toolbar2' => '',
        'toolbar3' => '',
        'toolbar4' => '',
        'height' => 50
    ),
    'wpautop' => false,
    'media_buttons' => false,
    'quicktags' => false,
);
?>
    <form enctype="multipart/form-data" action="" method="post" class="form">
        <div class="row">
            <div class="col-12">
                <h3>Basics</h3>
            </div>
            <div class="col-12">
                <label>Title <span class="required">*</span></label>
                <input type="text" name="post_title" class="form-control" value="<?php echo isset( $form_posts['post_title'] ) ? $form_posts['post_title'] : ''; ?>">
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label>Description</label>
                        <?php if ( isset( $form_posts['post_content'] ) ) $content_editor = $form_posts['post_content']; ?>
                    <textarea name="post_content" class="form-control" style="height: 150px;"><?php echo $content_editor; ?></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                    <div>
                        <label>Gig Type <span class="required">*</span></label>
                        <ul class="categorychecklist" style="list-style: none; padding: 0;">
                            <?php
                            $gig_types = get_terms( array( 'taxonomy' => 'gig-type' ) );
                            foreach ( $gig_types as $gig_type ) :
                            ?>
                                <li id='gig-type-<?php echo $gig_type->term_id; ?>' class="popular-category">
                                    <label class="selectit">
                                        <input value="<?php echo $gig_type->term_id; ?>" type="checkbox" name="tax_input[gig-type][]" id="in-gig-type-<?php echo $gig_type->term_id; ?>">
                                        <?php echo $gig_type->name; ?>
                                    </label>
                                </li>
                            <?php
                            endforeach;
                            ?>
                        </ul>

                        <label>Gig Genre <span class="required">*</span></label>
                        <ul class="categorychecklist" style="list-style: none; padding: 0;">
                            <?php
                            $gig_genres = get_terms( array( 'taxonomy' => 'gig-genre' ) );
                            foreach ( $gig_genres as $gig_genre ) :
                            ?>
                                <li id='gig-type-<?php echo $gig_genre->term_id; ?>' class="popular-category">
                                    <label class="selectit">
                                        <input value="<?php echo $gig_genre->term_id; ?>" type="checkbox" name="tax_input[gig-genre][]" id="in-gig-type-<?php echo $gig_genre->term_id; ?>">
                                        <?php echo $gig_genre->name; ?>
                                    </label>
                                </li>
                            <?php
                            endforeach;
                            ?>
                        </ul>
                    </div>
            </div>
            <div class="col-md-6">
                <label>Gig Artist <small>(Separate multiple by comma)</small> <span class="required">*</span></label>
                <input type="text" name="gig_artist" id="gig_artist" class="form-control" value="<?php echo isset( $form_posts['gig_artist'] ) ? $form_posts['gig_artist'] : ''; ?>">

                <br>

                <label>Gig Support <small>(Separate multiple by comma)</small></label>
                <input type="text" name="gig_support" id="gig_support" class="form-control" value="<?php echo isset( $form_posts['gig_support'] ) ? $form_posts['gig_support'] : ''; ?>">

                <br>

                <?php
                if ( isset( $form_posts['gig_venue'] ) ) :
                    $venue = get_post( $form_posts['gig_venue'] );
                endif;
                ?>
                <label>Venue <small>(Start typing to search for the Venue)</small></label>

                <div id="gig_venues_wrap">
                    <?php if ( isset( $venue ) ) : ?>
                        <input type="hidden" name="gig_venue" class="gig_venue<?php echo $venue->id; ?>" value="<?php echo $venue->id; ?>">
                    <?php endif; ?>
                </div>
                <ul id="selected_venues">
                    <?php if ( isset( $venue ) ) : ?>
                        <li class="gig_venue<?php echo $venue->id; ?>"><?php echo $venue->post_title; ?> <span data-venue="<?php echo $venue->id; ?>" class="remove_venue"><i class="fa fa-pencil" aria-hidden="true"></i></span></li>
                    <?php endif; ?>
                </ul>
                <label id="gig_venue_select_wrap" style="<?php echo isset( $venue ) && ! is_null( $venue ) ? 'display: none;' : ''; ?>">
                    <input type="text" name="gig_venue_select" id="gig_venue_select" class="form-control">
                    <small>If you cannot find the Venue, please include it in the description.</small>
                </label>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <h3>Ticket Information</h3>
            </div>
            <div class="col-md-4">
                <div class="form-wrap">
                    <label>Price</label>
                    <input type="text" name="price" class="form-control" value="<?php echo isset( $form_posts['price'] ) ? $form_posts['price'] : ''; ?>">
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-wrap">
                    <label>Ticket Link URL</label>
                    <input type="text" name="ticket_link_url_1" class="form-control" value="<?php echo isset( $form_posts['ticket_link_url_1'] ) ? $form_posts['ticket_link_url_1'] : ''; ?>">
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h3>Gig Date and Time</h3>
            </div>

            <div class="col-md-6">
                <label>Date <small>(dd-mm-yyyy format)</small> <span class="required">*</span></label>
                <input type="text" id="gig-date" name="gig_date" class="form-control" value="<?php echo isset( $form_posts['gig_date'] ) ? $form_posts['gig_date'] : ''; ?>" size="20" maxlength="30" readonly>
                <small>e.g. 31-12-<?php echo date('Y'); ?></small>

            </div>

            <div class="col-md-4">
                <label id="gig-time-wrap">Time <small>(24hr format)</small> <span class="required">*</span></label>
                    <input class="" type="text" id="gig-time" name="gig_time" class="form-control" value="<?php echo isset( $form_posts['gig_time'] ) ? $form_posts['gig_time'] : ''; ?>" size="15" maxlength="10">
                    <small>e.g. 21:05 (HH:MM)</small>
            </div>

            <div class="col-md-2">
                <label>
                    <input type="checkbox" id="gig-repeat" name="gig_repeat" value="1" <?php echo isset( $form_posts['gig_repeat'] ) ? 'checked="checked"' : '';?> style="width: auto; display: inline;">
                    Repeat ?
                </label>
            </div>

            <div class="col-md-12">
                <div class="mt-3">
                    <div class="repeat_rules_wrap <?php echo isset( $form_posts['gig_repeat'] ) ? '' : 'hide'; ?>">
                        <div class="sub">
                            <label>
                                Repeats <span class="required">*</span>
                                    <?php $freqs = array( 'DAILY', 'WEEKLY', ); ?>
                                <select id="gig-repeat-freq" name="gig_repeat_freq" class="form-control">
                                    <?php foreach ( $freqs as $freq ): ?>
                                    <option value="<?php echo $freq; ?>" <?php echo isset( $form_posts['gig_repeat_freq'] ) && $freq == $form_posts['gig_repeat_freq'] ? ' selected="selected"': '';?>>
                                        <?php echo ucfirst(strtolower($freq)); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        </div>
                        <div class="repeat_rule daily sub <?php echo isset( $form_posts['gig_repeat_freq'] ) && $form_posts['gig_repeat_freq'] == 'DAILY' ? '' : 'hide'; ?>">
                            <label>Repeats every <span class="required">*</span> </label>
                            <div class="sub" style="padding-left: 10px;">
                                <div class="container-inline interval">
                                    <input type="radio" id="gig-repeat-daily-byday-interval" name="gig_repeat_daily_byday" value="INTERVAL" <?php echo isset( $form_posts['gig_repeat_daily_byday'] ) && $form_posts['gig_repeat_daily_byday'] == 'INTERVAL' ? 'checked="checked"' : ''; ?> style="width: auto; display: inline;">
                                    <label>
                                        <input type="text" id="gig-repeat-daily-interval" name="gig_repeat_daily_interval" value="<?php echo isset( $form_posts['gig_repeat_daily_interval'] ) ? $form_posts['gig_repeat_daily_interval'] : '1'; ?>" size="3" maxlength="3" class="form-text" style="width: auto; display: inline; padding: 0 4px;"> days
                                    </label>
                                </div>
                                <div>
                                    <label>
                                        <input type="radio" id="gig-repeat-daily-byday-every-weekday" name="gig_repeat_daily_byday" value="weekday" <?php echo isset( $form_posts['gig_repeat_daily_byday'] ) && $form_posts['gig_repeat_daily_byday'] == 'weekday' ? 'checked="checked"' : ''; ?> style="width: auto; display: inline;"> Every weekday
                                    </label>
                                </div>
                                <div>
                                    <label>
                                        <input type="radio" id="gig-repeat-daily-byday-every-mo-we-fr" name="gig_repeat_daily_byday" value="mo_we_fr" <?php echo isset( $form_posts['gig_repeat_daily_byday'] ) && $form_posts['gig_repeat_daily_byday'] == 'mo_we_fr' ? 'checked="checked"' : ''; ?> style="width: auto; display: inline;"> Every Mon, Wed, Fri
                                    </label>
                                </div>
                                <div>
                                    <label>
                                        <input type="radio" id="gig-repeat-daily-byday-every-tu-th" name="gig_repeat_daily_byday" value="tu_th" <?php echo isset( $form_posts['gig_repeat_daily_byday'] ) && $form_posts['gig_repeat_daily_byday'] == 'tu_th' ? 'checked="checked"' : ''; ?> style="width: auto; display: inline;"> Every Tue, Thu
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="repeat_rule weekly sub <?php echo isset( $form_posts['gig_repeat_freq'] ) && $form_posts['gig_repeat_freq'] == 'WEEKLY' ? '' : 'hide'; ?>">
                            <label style="width: auto;">
                                Repeats every <span class="required">*</span>
                                <input type="text" id="gig-repeat-weekly-interval" name="gig_repeat_weekly_interval" value="<?php echo isset( $form_posts['gig_repeat_weekly_interval'] ) ? $form_posts['gig_repeat_weekly_interval'] : ''; ?>" size="3" maxlength="3" style="width: auto; display: inline; padding: 0 4px;">
                                weeks
                            </label>
                        </div>

                        <div class="sub">
                            <label>
                                Stop repeating after
                                <input type="text" id="gig-repeat-count" name="gig_repeat_count" value="<?php echo isset( $form_posts['gig_repeat_count'] ) ? $form_posts['gig_repeat_count'] : '2'; ?>" size="3" maxlength="3" class="form-text" style="width: auto; display: inline; padding: 0 4px;">
                                occurrences <span class="required">*</span>
                            </label>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-12">
                        <h3>Your Details</h3>
                    </div>
                    <div class="col-md-6">
                        <label>Your Name <span class="required">*</span></label>
                        <input type="text" name="user_name" class="form-control" value="<?php echo isset( $form_posts['user_name'] ) ? $form_posts['user_name'] : ''; ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Your Email <span class="required">*</span></label>
                        <input type="email" name="user_email" class="form-control" value="<?php echo isset( $form_posts['user_email'] ) ? $form_posts['user_email'] : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="col-md-6">

            </div>
            <div class="col-md-6">

            </div>

            <div class="col-12 mt-2">
                <label>
                    <input type="checkbox" id="subscribe" name="subscribe" value="1" <?php echo isset( $form_posts ) && !isset( $form_posts['subscribe'] ) ? '' : 'checked="checked"';?> style="width: auto; display: inline;">
                    I would like a free The Brag newsletter subscription
                </label>
            </div>


            <div class="col-12 py-2">
                <div class="g-recaptcha" data-sitekey="6LcajB8UAAAAAJYOH-bN1EdyGLmS3RcY0L8gugZe"></div>
                <script src='https://www.google.com/recaptcha/api.js'></script>
            </div>

            <div class="col-12 my-2">
        <input type="submit" value="submit" class="btn btn-dark">
            </div>
        </div>
    </form>
    </div>

    <div class="col-lg-4 p-0" style="min-width: 320px;">
        <div class="gig-search-wrap px-3 py-1 mb-3 bg-dark">
            <h2 class="text-center text-uppercase mt-3 text-white">Gig Search</h2>
            <?php $in_sidebar = true; include( get_template_directory() . '/gig-search-form.php' ); ?>
        </div>
        <?php get_fuse_tag( 'mrec_1' ); ?>
        <?php get_fuse_tag( 'mrec_2' ); ?>
    </div>
</div>
</div>
<?php get_footer();
