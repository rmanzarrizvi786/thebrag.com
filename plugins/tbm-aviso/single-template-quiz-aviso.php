<?php
/*
 * Template Name: Aviso Lead Generation
 * Template Post Type: post
 */
get_header(); ?>

<?php

$post = $wp_query->post;
if ( ! post_password_required( $post ) ) :
  $author_byline = '';
  $author_image = '';
  $activate_shout_coffee = false;
  $author_id = 0;
  if ( get_field('author') || get_field('Author') ) :
      if ( get_field( 'author' ) ) :
          $author_byline = $author_name = get_field( 'author' );
      elseif ( get_field( 'Author' ) ) :
          $author_byline = $author_name = get_field( 'Author' );
      endif;

      $author_img_src = wp_get_attachment_image_src( get_field('author_profile_picture'), 'thumbnail' );
      if ( $author_img_src ) :
          $author_image = '<img src="' . $author_img_src[0] . '" width="72">';
      endif;
  else : // If custom author has not bee set
      if ( '' != get_the_author_meta( 'first_name', $post->post_author ) && '' != get_the_author_meta( 'last_name', $post->post_author ) ) :
          $author_byline = '<a href="' . get_author_posts_url( $post->post_author ) . '">' . get_the_author_meta( 'first_name', $post->post_author ) . ' ' . get_the_author_meta( 'last_name', $post->post_author ) . '</a>';
          $author_name = get_the_author_meta( 'first_name', $post->post_author ) . ' ' . get_the_author_meta( 'last_name', $post->post_author );
      else :
          $author_byline = '<a href="' . get_author_posts_url( $post->post_author ) . '">' . get_the_author_meta( 'display_name', $post->post_author ) . '</a>';
          $author_name = get_the_author_meta( 'display_name', $post->post_author );
      endif;

      $author_image = get_avatar( $post->post_author, 72, 'blank', '', array( 'class' => 'rounded-circle') );
  endif;

  if ( in_array( strtolower( $author_name ), array ('', 'staff', 'staff writer', 'admin', 'nathan jolly', 'phoebe loomes' ) ) ) :
      $activate_shout_coffee = false;
  endif;
?>
<div class="container single single_story" id="<?php the_ID(); ?>">
    <div class="row">
      <?php
      /*
      * Verify the email using the code in the URL
      */
      if ( isset( $_GET['action'] ) ) :
        $action = isset( $_GET['action'] ) ? $_GET['action'] : NULL;
        $code = isset( $_GET['code'] ) ? $_GET['code'] : NULL;

        if ( ! is_null( $action) && ! is_null( $code ) ) :
          $lead = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}aviso_leads WHERE verification_code = %s LIMIT 1",
            $code
          ) );
          if( ! is_null( $lead ) ) :
            $lead_id = $lead->id;
            $wpdb->update( $wpdb->prefix . 'aviso_leads',
              array(
                'verified' => '1',
                'updated_at' => current_time( 'mysql' ),
              ),
              array(
                'id' => $lead_id,
              )
            );

            $lead_response = $wpdb->get_row( $wpdb->prepare(
              "SELECT * FROM {$wpdb->prefix}aviso_lead_responses WHERE lead_id = %d AND `question` = 'will_hook_up' LIMIT 1",
              $lead_id
            ));

            /*
            *  Send details to Aviso Group (LPL) - contactability
            */
            if( stripos( $lead_response->response, 'Definitely' ) !== FALSE ) :
              $post_data = array(
                'email' => ( $lead->email ),
                'firstname' => urlencode( $lead->firstname ),
                'lastname' => urlencode( $lead->lastname ),
                'postcode' => urlencode( $lead->postcode ),
                'phone1' => ( $lead->phone ),
                'state' => urlencode( $lead->state ),
                'product' => get_field( 'product', $post->ID ),
              );
              $lead_response_ride = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}aviso_lead_responses WHERE lead_id = %d AND `question` = 'your_ride' LIMIT 1",
                $lead_id
              ));
              if ( isset( $lead_response_ride->response ) ) :
                  $post_data['car_type'] = urlencode( $lead_response_ride->response );
              endif;

              $lead_response_number_cars = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}aviso_lead_responses WHERE lead_id = %d AND `question` = 'how_many_cars' LIMIT 1",
                $lead_id
              ));
              if ( isset( $lead_response_number_cars->response ) ) :
                  $post_data['number_of_cars'] = urlencode( $lead_response_number_cars->response );
              endif;

              $lead_response_cover = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}aviso_lead_responses WHERE lead_id = %d AND `question` = 'current_cover' LIMIT 1",
                $lead_id
              ));
              if ( isset( $lead_response_cover->response ) ) :
                  $post_data['level_of_cover'] = urlencode( $lead_response_cover->response );
              endif;
              // $post_data = array_map( 'urlencode', $post_data );

              // echo '<pre>'; print_r( $post_data ); echo '</pre>'; exit;

              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, "https://contactability.leadbyte.co.uk/api/submit.php?campid=AVISO-GROUP-LPL&sid=BR&returnjson=yes");
              curl_setopt($ch, CURLOPT_POST, 1);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $post_data ));
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              $curl_output = curl_exec($ch);
              curl_close ($ch);

              $wpdb->insert( $wpdb->prefix . 'aviso_lead_results',
                array(
                  'lead_id' => $lead_id,
                  'response' => $curl_output,
                )
              );

              // echo '<pre>'; var_dump( json_decode( $curl_output ) ); echo '</pre>';
            endif; // If user is interested in a better deal

            echo '<div class="col-12 mb-2"><div class="alert alert-success text-center">Thank you, your email address is verified.</div></div>';
          else :
              echo '<div class="col-12 mb-2"><div class="alert alert-danger text-center">Invalid link</div></div>';
          endif; // If the lead is found
        endif; // If action and code are set
      endif; // If there is an action in the $_GET variable

      $states = array(
        'NSW',
        'QLD',
        'VIC',
        'ACT',
        'NT',
        'SA',
        'TAS',
        'WA',
      );

      /*
      * Process the submitted form
      */
      if ( isset( $_POST['submit_aviso_lead'] ) ) :
        $errors = $responses = array();
        $questions = explode( "\n", get_field( 'questions' ) );
        if ( $questions && is_array( $questions ) ) :

          $user_fields = array(
            'firstname', 'lastname', 'phone', 'email', 'state', 'postcode', 'consent',
          );

          /*
          * User fields
          */
          foreach( $user_fields as $user_field ) :
            if ( isset( $_POST[$user_field]) ) :
              $responses[$user_field] = sanitize_text_field( $_POST[$user_field] );
            else :
              $errors = array( 'All fields are mandatory.' );
            endif;
          endforeach;

          /*
          * Responses to Questions
          */
          foreach( $questions as $question ) :
            $question_details = array_map( 'trim', explode( '|', $question ) );
            if ( isset( $question_details[0] ) && isset( $question_details[2] ) ) :

              $question_id = $question_details[0];
              $question_type = $question_details[2];
              $question_text = $question_details[1];

              if ( isset( $_POST['questions'][$question_id]) ) :
                if ( 'textarea' == $question_type ) :
                  $responses['questions'][$question_id] = sanitize_textarea_field( $_POST['questions'][$question_id] );
                else :
                  $responses['questions'][$question_id] = sanitize_text_field( $_POST['questions'][$question_id] );
                endif;
              else : // Response has not been submitted
                $errors = array( 'All fields are mandatory.' );
              endif; // If an answer to question is posted
            endif;
          endforeach;

          /*
          * Basic email validation
          */
          if ( ! isset( $responses['email'] ) || ! is_email( $responses['email'] ) ) :
            $errors[] = 'Please enter correct email address.';
          endif;

          if ( isset( $responses['state'] ) && ! in_array( $responses['state'], $states ) ) :
            $errors[] = 'Please select correct state.';
          endif;

          if ( isset( $responses['postcode'] ) ) :
            $regex_postcode = '/^(?:(?:[2-8]\d|9[0-7]|0?[28]|0?9(?=09))(?:\d{2}))$/';
            if ( ! preg_match( $regex_postcode, $responses['postcode'] ) ) :
              $errors[] = 'Please enter valid Australian postcode.';
            endif;
          endif;

          if ( isset( $responses['phone'] ) ) :
            $regex_phone = '\(?(?:\+?61|0)(?:(?:2\)?[ -]?(?:3[ -]?[38]|[46-9][ -]?[0-9]|5[ -]?[0-35-9])|3\)?(?:4[ -]?[0-57-9]|[57-9][ -]?[0-9]|6[ -]?[1-67])|7\)?[ -]?(?:[2-4][ -]?[0-9]|5[ -]?[2-7]|7[ -]?6)|8\)?[ -]?(?:5[ -]?[1-4]|6[ -]?[0-8]|[7-9][ -]?[0-9]))(?:[ -]?[0-9]){6}|4\)?[ -]?(?:(?:[01][ -]?[0-9]|2[ -]?[0-57-9]|3[ -]?[1-9]|4[ -]?[7-9]|5[ -]?[018])[ -]?[0-9]|3[ -]?0[ -]?[0-5])(?:[ -]?[0-9]){5})';

            if ( ! preg_match( "/^" . $regex_phone . "$/", $responses['phone'] ) ) :
              $errors[] = 'Please enter valid Australian phone number.';
            endif;
          endif;

          if ( ! isset( $responses['consent'] ) ) :
            $errors[] = 'You need to agree to the Terms and Conditions and Privacy Policy.';
          endif;


          if ( count( $errors ) === 0 ) :
            /*
            * No errors, insert in to database and send verification emails
            */

            /*
            * Add to The Brag MailChimp DB
            */
            require_once( get_template_directory() . '/MailChimp.php');
            $api_key = '727643e6b14470301125c15a490425a8-us1';
            $MailChimp = new \DrewM\MailChimp\MailChimp( $api_key );
            $data = array(
                'email_address' => $responses['email'],
                'status' => 'subscribed',
            );
            $subscribe = $MailChimp->post( "lists/c9114493ef/members", $data );

            $verificationCode = md5( uniqid( $responses['email'], true ) );
            // $verificationLink = get_permalink() . '?action=verify&code=' . $verificationCode;
            $verificationLink = esc_url( add_query_arg( array( 'action' => 'verify', 'code' => $verificationCode ), get_permalink() ) );

            $wpdb->insert(
              $wpdb->prefix . 'aviso_leads',
              array(
                'firstname' => $responses['firstname'],
                'lastname' => $responses['lastname'],
                'email' => $responses['email'],
                'phone' => $responses['phone'],
                'state' => $responses['state'],
                'postcode' => $responses['postcode'],
                // 'responses' => json_encode( $responses ),
                'verified' => '0',
                'verification_code' => $verificationCode,
                'quiz_post' => $post->ID,
                'product' => get_field( 'product', $post->ID ),
                'created_at' => current_time( 'mysql' ),
                'updated_at' => current_time( 'mysql' ),
              )
            );
            $lead_id = $wpdb->insert_id;

            foreach( $responses['questions'] as $k => $v ) :
              $wpdb->insert(
                $wpdb->prefix . 'aviso_lead_responses',
                array(
                  'lead_id' => $lead_id,
                  'question' => $k,
                  'response' => $v,
                )
              );
            endforeach;

            ob_start();
            include( get_template_directory() . '/email-templates/header.php' );
            ?>
            <p><strong>Dear <?php echo $responses['firstname']; ?>,</strong></p>
            <?php if ( 'Home and Contents Insurance' == get_field( 'product', $post->ID ) ) : ?>
                <p>Your chance to have your home sparkly and clean is just one click away.</p>
                <p>We just need you to verify your email address!</p>
            <?php elseif ( 'Car Insurance' == get_field( 'product', $post->ID ) ) : ?>
                <p>You're only one click away from the chance to win a month's worth of petrol.</p>
                <p>We just need you to verify your email address!</p>
            <?php endif; ?>
            <p><a href="<?php echo $verificationLink; ?>" target="_blank" style="font-weight:bold; color: #4834d4;">VERIFY EMAIL</a></p>
            <p>&nbsp;</p>
            <p style="color: #999;">Regards,<br><strong>The Brag Media</strong></p>
            <?php
            include( get_template_directory() . '/email-templates/footer.php' );

            $body = ob_get_contents();
            ob_end_clean();

            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            wp_mail( $responses['email'], 'Verify your email address', $body, $headers );

            echo '<div class="col-12 mb-2"><div class="alert alert-info text-center">You are very close to entering the competition! Check your inbox to verify your email.</div></div>';

            unset( $responses );
          else :
            echo '<div class="col-12 mb-2"><ul class="alert alert-danger">';
            foreach( $errors as $error ) :
              echo '<li>' . $error . '</li>';
            endforeach;
            echo '</ul></div>';
          endif; // If $errors is empty
        endif; // If there are $questions using ACF
      endif; // If the form is submitted
      ?>

        <?php $title = get_post_meta(get_the_ID(), '_yoast_wpseo_title', true) ? get_post_meta(get_the_ID(), '_yoast_wpseo_title', true) : get_the_title(); ?>
        <div class="col-12 pb-4 mt-2">
            <h1 id="story_title" class="mb-2" data-href="<?php the_permalink(); ?>" data-title="<?php echo htmlentities( $title );?>" data-share-title="<?php echo urlencode( $title ); ?>" data-share-url="<?php echo urlencode(get_permalink()); ?>"><?php the_title(); ?></h1>
        </div>
    </div>
    <div class="row">
      <div class="col-lg-4 left-col-has-ad d-none d-lg-block">
          <div class="d-flex flex-column" style="height: 100%;">
              <div class="post-meta d-flex justify-content-between mb-3">
                  <div class="author align-self-center" data-author="<?php echo $author_name; ?>">
                      <div class="cats">
                          <?php if ( 'dad' != get_post_type() ) : ?>
                          <?php $categories = get_the_category(get_the_ID()); if ( $categories ) : foreach( $categories as $category ) : ?>
                          <a class="d-inline-block small text-uppercase cat mr-2" href="<?php echo get_category_link( $category->term_id ); ?>"><?php echo $category->cat_name; ?></a>
                          <?php endforeach; endif; ?>
                          <?php else :?>
                          <?php $categories = get_the_terms(get_the_ID(), 'dad-category'); if ( $categories ) : foreach( $categories as $category ) : if ( 'Uncategorised' == $category->name ) continue;  ?>
                          <a class="d-inline-block small text-uppercase cat mr-2" href="<?php echo get_term_link( $category, 'dad-category' ); ?>"><?php echo $category->name; ?></a>
                          <?php endforeach; endif; ?>
                          <?php endif; // If Post Type != Dad ?>
                      </div>
                      By <?php echo $author_byline; ?>
                      <br>
                      <time datetime="<?php echo date( 'Y-m-d\TH:i:s+10:00', get_the_time( 'U' ) ); ?>"><?php echo get_the_time('M d, Y'); ?></time>
                  </div>
                  <div class="text-right"><?php echo $author_image; ?></div>
              </div>

              <?php
              if ( $activate_shout_coffee && shortcode_exists( 'shout_writer_coffee' ) ) :
                  echo do_shortcode( '[shout_writer_coffee author="' . $author_name . '"]' );
              endif;
              ?>

              <div class="align-self-center mt-0">
                  <?php get_fuse_tag( 'mrec_1', 'single' ); ?>
              </div>
              <?php do_action( 'ssm_social_sharing_buttons', 'row', false ); ?>
              <div class="left-col-has-ad mt-2">
                  <div class="sticky-ad"><?php get_fuse_tag( 'mrec_2', 'single' ); ?></div>
              </div>
          </div>
      </div>

        <div class="col-lg-8 post-content">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) :
                    the_post();
                if ( ! post_password_required( $post ) ) : ?>
    <?php if ( '' !== get_the_post_thumbnail() ) : ?>
    <div class="post-thumbnail mb-3">
        <?php
        $alt_text = get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true );
        if ( $alt_text == '' ) {
            $alt_text = trim(strip_tags(get_the_title()));
        }
        echo get_the_post_thumbnail( null, 'large', array(
            'alt' => $alt_text,
            'title' => $alt_text,
            'class' => 'img-fluid',
            )); ?>
        <?php
            if ( get_field( 'image_credit' ) ) {
                echo '<p class="image-credit text-right">Image: ' . get_field( 'image_credit' ) . '</p>';
            } else if ( (get_field('Image Credit')) ) {
                echo '<p class="image-credit text-right">Image: ' . get_field('Image Credit', '') . '</p>';
            }
            ?>
    </div><!-- .post-thumbnail -->
    <?php endif; ?>

    <?php if ( in_category( 'Op-Ed/Comment' ) ) : ?>
    <div style="padding: 10px 0; font-weight: bold;">COMMENT</div>
    <?php endif; ?>

    <?php if ( in_category( 'Fiction' ) ) : ?>
    <div style="padding: 10px 0; font-weight: bold;">FICTION</div>
    <?php endif; ?>

    <?php
    if( get_field( 'promoted_text' ) && '' != get_field( 'promoted_text' ) ) :
        if ( get_field( 'promoted_text_link' ) && '' != get_field( 'promoted_text_link' ) ) :
    ?>
    <a href="<?php echo get_field('promoted_text_link'); ?>" target="_blank" class="text-dark">
    <?php
        endif; // If promoted_text_link
    ?>
    <div class="p-3 mb-3 d-flex align-items-center" style="border: 1px solid #b2b2b2; font-size: 110%">
        <div><?php echo get_field('promoted_text'); ?></div>
        <?php if( get_field( 'promoted_logo' ) && '' != get_field( 'promoted_logo' ) ) : ?>
        <img src="<?php echo get_field( 'promoted_logo' ); ?>" style="width: 100px;">
        <?php endif; // If promoted_logo ?>
    </div>
    <?php
    if ( get_field( 'promoted_text_link' ) && '' != get_field( 'promoted_text_link' ) ) :
    ?>
        </a>
    <?php
        endif; // If promoted_text_link
    endif; // If promoted_text

            $content = apply_filters( 'the_content', $post->post_content );
            echo $content;
            if ( ! get_field('paid_content', get_the_ID()) ) :
                $args = array (
                    'before'            => '<div class="page-links">',
                    'after'             => '</div>',
                    'link_before'       => '<span class="page-link">',
                    'link_after'        => '</span>',
                    'next_or_number'    => 'next',
                    'separator'         => ' ',
                    'nextpagelink'      => 'Next &raquo',
                    'previouspagelink'  => '&laquo Previous',
                );

                wp_link_pages( $args );
            endif;

            if( get_field('generate_leads_for_aviso_group_lpl') ) :
            ?>
            <form action="<?php echo get_the_permalink(); ?>" method="post">
            <?php
              if ( get_field( 'questions' ) ) :
                $questions = explode( "\n", get_field( 'questions' ) );
                if ( $questions && is_array( $questions ) ) :
                  foreach( $questions as $question ) :
                    $question_details = array_map( 'trim', explode( '|', $question ) );
                    if ( isset( $question_details[0] ) && isset( $question_details[2] ) ) :
                      $question_id = $question_details[0];
                      $question_type = $question_details[2];
                      $question_text = $question_details[1];

                      // $question_id = sanitize_title( $question_text );

                      echo '<div class="mb-4"><label class="h5">'. $question_text . '</label>';
                      switch( $question_type ) :
                        case 'text' :
                        ?>
                          <input type="text" name="questions[<?php echo $question_id; ?>]" value="<?php echo isset( $responses['questions'][$question_id] ) ? $responses['questions'][$question_id] : ''; ?>" placeholder="" class="form-control" required>
                        <?php
                          break;
                        case 'textarea' :
                        ?>
                          <textarea name="questions[<?php echo $question_id; ?>]" placeholder="" class="form-control" required><?php echo isset( $responses['questions'][$question_id] ) ? $responses['questions'][$question_id] : ''; ?></textarea>
                        <?php
                          break;
                        case 'radio' :
                          if ( isset( $question_details[3] ) ) :
                            $choices = array_map( 'trim', explode( '%%', $question_details[3] ) );
                            echo '<div class="">';
                            foreach( $choices as $i_choice => $choice ) :
                              ?>
                              <label class="btn btn-secondary mr-2">
                                <input type="radio" value="<?php echo $choice; ?>" name="questions[<?php echo $question_id; ?>]"<?php echo isset( $responses['questions'][$question_id] ) && $choice == stripslashes_deep( $responses['questions'][$question_id] ) ? ' checked="checked"' : ''; ?> required>
                                <span><?php echo $choice; ?></span>
                              </label>
                              <?php
                            endforeach; // For Each $choices
                            echo '</div>';
                          endif; // If there are choices
                          break;
                        default:
                          break;
                      endswitch;
                      echo '</div>';
                    endif; // If qustion type is set
                  endforeach; // For Each $questions
                endif; // If there are $questions
              endif; // If the questions field is set
            ?>

            <div class="row">
              <div class="col-md-12">
                <h5>Your Details</h5>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mt-3">
                <label>First name</label>
                <input type="text" name="firstname" value="<?php echo isset( $responses['firstname'] ) ? $responses['firstname'] : ''; ?>" class="form-control" required>
              </div>

              <div class="col-md-6 mt-3">
                <label>Last name</label>
                <input type="text" name="lastname" value="<?php echo isset( $responses['lastname'] ) ? $responses['lastname'] : ''; ?>" class="form-control" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mt-3">
                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo isset( $responses['phone'] ) ? $responses['phone'] : ''; ?>" class="form-control" required>
              </div>

              <div class="col-md-6 mt-3">
                <label>Email address</label>
                <input type="text" name="email" value="<?php echo isset( $responses['email'] ) ? $responses['email'] : ''; ?>" class="form-control" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mt-3">
                <label>State</label>
                <select name="state" class="form-control" required>
                  <?php foreach( $states as $state ) : ?>
                    <option value="<?php echo $state; ?>"<?php echo isset( $responses['state'] ) && $state == $responses['state'] ? ' selected="selected"' : ''; ?>><?php echo $state; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6 mt-3">
                <label>Postcode</label>
                <input type="text" name="postcode" value="<?php echo isset( $responses['postcode'] ) ? $responses['postcode'] : ''; ?>" size="4" maxlength="4" class="form-control" required>
              </div>
            </div>

            <div class="row">
              <div class="col-12 mt-4">
                <label>
                  <input type="checkbox" name="consent" checked> I agree to the <a href="https://thebrag.media/terms-and-conditions/" target="_blank">Terms and Conditions</a> and <a href="https://thebrag.media/privacy-policy/" target="_blank">Privacy Policy</a>
                </label>
              </div>
            </div>

            <div class="row">
              <div class="col-12 mt-2 mb-4">
                <input type="submit" name="submit_aviso_lead" class="btn-dark" value="Submit">
              </div>
            </div>

            </form>
            <?php
            endif; // If lead generation is ON

            if ( get_field( 'impression_tag' ) ) :
                echo str_replace( '[timestamp]', time(), get_field( 'impression_tag' ) );
            endif;
            ?>
            <div class="mt-2 d-flex justify-content-around"><?php do_action( 'ssm_social_sharing_buttons', 'row' ); ?></div>
<!-- Story End -->
<?php endif;

                endwhile;
            endif;
            wp_reset_query();
            ?>
        </div>
    </div>
</div>

<?php else :
    echo get_the_password_form();
endif;
?>

<?php get_footer();
