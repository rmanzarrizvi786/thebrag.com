<?php
/*
 * Template Name: Instagram Survey Oct 2019
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
      * Process the submitted form
      */
      if ( isset( $_POST['submit_instagram_survey'] ) ) :
          if ( isset( $_POST['nonce_insta_survey_oct19'] ) && wp_verify_nonce( $_POST['nonce_insta_survey_oct19'], 'action_insta_survey_oct19' ) ) :
        $form_errors = array();
        $responses = array();


          $required_fields = array(
            'user_email',
            'user_instagram_handle',
            'music_artists_discovered',
            'link_to_discovery',
            'why_love_it',
            'how_did_you_find',
            'consent_tcp'
          );

          /*
          * Validate fields
          */
          foreach( $required_fields as $required_field ) :
            if ( isset( $_POST[$required_field]) ) :
                if ( 'why_love_it' != $required_field ) :
                    $responses[$required_field] = sanitize_text_field( $_POST[$required_field] );
                else :
                    $responses[$required_field] = sanitize_textarea_field( $_POST[$required_field] );
                endif;
            else :
              $form_errors = array( 'All fields are mandatory.' );
            endif;
          endforeach;

          if (filter_var($responses['link_to_discovery'], FILTER_VALIDATE_URL) === FALSE) {
                $form_errors[] = 'Please input valid URL for what you found.';
            }

          $responses['how_did_you_find_hashtag'] = sanitize_text_field( $_POST['how_did_you_find_hashtag'] );
          $responses['how_did_you_find_account'] = sanitize_text_field( $_POST['how_did_you_find_account'] );

          if ( isset( $responses['how_did_you_find'] ) ) :
              if ( 'Hash tag' == $responses['how_did_you_find'] && ( ! isset( $responses['how_did_you_find_hashtag'] ) || '' == $responses['how_did_you_find_hashtag'] ) ) {
                  $form_errors[] = 'Please input the hashtag how you found music or artists.';
              } else if ( 'Following account' == $responses['how_did_you_find'] && ( ! isset( $responses['how_did_you_find_account'] ) || '' == $responses['how_did_you_find_account'] ) ) {
                  $form_errors[] = 'Please input the account how you found music or artists.';
              }
          endif;

          /*
          * Basic email validation
          */
          if ( ! isset( $responses['user_email'] ) || ! is_email( $responses['user_email'] ) ) :
            $form_errors[] = 'Please enter correct email address.';
          endif;

          if ( ! isset( $responses['consent_tcp'] ) ) :
            $form_errors[] = 'You need to agree to the Terms and Conditions and Privacy Policy.';
          endif;

          if ( count( $form_errors ) == 0 ) :
            /*
            * No errors
            */

            /*
            * Add to DBU MailChimp DB
            */
            require_once( get_template_directory() . '/MailChimp.php');
            $api_key = '727643e6b14470301125c15a490425a8-us1';
            $MailChimp = new \DrewM\MailChimp\MailChimp( $api_key );
            $data = array(
                'email_address' => $responses['user_email'],
                'status' => 'subscribed',
            );
            $subscribe = $MailChimp->post( "lists/2a48cd9086/members", $data );

            /*
            Insert in to database
            */
            $wpdb->insert(
              $wpdb->prefix . 'insta_survey_responses_oct19',
              array(
                'user_email' => $responses['user_email'],
                'user_instagram_handle' => $responses['user_instagram_handle'],
                'music_artists_discovered' => $responses['music_artists_discovered'],
                'link_to_discovery' => $responses['link_to_discovery'],
                'why_love_it' => $responses['why_love_it'],
                'how_did_you_find' => $responses['how_did_you_find'],
                'how_did_you_find_hashtag' => $responses['how_did_you_find_hashtag'],
                'how_did_you_find_account' => $responses['how_did_you_find_account'],
                'created_at' => current_time( 'mysql' ),
                'updated_at' => current_time( 'mysql' ),
              )
            );
            $lead_id = $wpdb->insert_id;

            wp_safe_redirect( add_query_arg( array(
                'success' => '1',
            ), get_the_permalink() ) );
            exit;

            echo '<div class="col-12 mb-2"><div class="alert alert-success text-center">Thank you!</div></div>';

            unset( $responses );
          else :
            echo '<div class="col-12 mb-2"><ul class="alert alert-danger">';
            foreach( $form_errors as $error ) :
              echo '<li>' . $error . '</li>';
            endforeach;
            echo '</ul></div>';
          endif; // If $form_errors is empty
      else :
          echo '<div class="col-12 mb-2"><ul class="alert alert-danger">';
        echo '<li>Please try again.</li>';
          echo '</ul></div>';
      endif; // If Nonce is set and  verified
      endif; // If the form is submitted
      ?>

      <?php if ( isset( $_GET['success'] ) && 1 == $_GET['success'] ) : ?>
          <script>
          fbq('trackCustom', 'CompletedInstaSurvey', {site: 'dbu'});
          </script>
          <div class="col-12 mb-2"><div class="alert alert-success text-center">Thank you!</div></div>
      <?php endif; ?>

        <?php $title = get_post_meta(get_the_ID(), '_yoast_wpseo_title', true) ? get_post_meta(get_the_ID(), '_yoast_wpseo_title', true) : get_the_title(); ?>
        <div class="col-12 pb-4 mt-2">
            <h1 id="story_title" class="mb-2" data-href="<?php the_permalink(); ?>" data-title="<?php echo htmlentities( $title );?>" data-share-title="<?php echo urlencode( $title ); ?>" data-share-url="<?php echo urlencode(get_permalink()); ?>"><?php the_title(); ?></h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 left-col-has-ad d-none d-lg-block">
            <div class="cats">
                <?php
                $categories = get_the_category(get_the_ID());
                if ( $categories ) :
                    foreach( $categories as $category ) :
                    if ( 'Evergreen' == $category->cat_name ) :
                        continue;
                    endif; // If category name is Evergreen
                ?>
                <a class="d-inline-block small text-uppercase cat" href="<?php echo get_category_link( $category->term_id ); ?>"><?php echo $category->cat_name; ?></a>
                <?php
                    endforeach; // For Each Category
                endif; // If there are categories for the post ?>
            </div>
            <div class="d-flex flex-column" style="height: 100%;">
                <div class="post-meta d-flex justify-content-between mb-3">
                    <div class="author align-self-center" data-author="<?php echo $author_name; ?>">
                        By <?php echo $author_byline; ?>
                        <br>
                        <time datetime="<?php echo date( 'Y-m-d\TH:i:s+10:00', get_the_time( 'U' ) ); ?>"><?php echo get_the_time('M d, Y'); ?></time>
                    </div>
                    <div class="text-right"><?php echo $author_image; ?></div>
                </div>

                <?php
                if ( shortcode_exists( 'shout_writer_coffee' ) ) :
                    echo do_shortcode( '[shout_writer_coffee author="' . $author_name . '"]' );
                endif; // If shout writer a coffee shortcode exists
                ?>

                <?php do_action( 'ssm_social_sharing_buttons', 'row', false ); ?>

                <div class="align-self-center mt-3">
                    <?php get_fuse_tag( 'mrec_1', 'single', $count_articles > 1 ? 'refresh' : NULL ); ?>
                </div>


            </div>
        </div><!-- Left Pane - Cats, Author, Coffee and Share buttons - for desktop devices -->



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

            ?>
            <form action="<?php echo get_the_permalink(); ?>" method="post" id="instagram-survey-form-oct19">
                <?php wp_nonce_field( 'action_insta_survey_oct19', 'nonce_insta_survey_oct19' ); ?>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h4>What music or artists have you discovered on instagram?</h4>
                        <input type="text" name="music_artists_discovered" value="<?php echo isset( $responses['music_artists_discovered'] ) ? $responses['music_artists_discovered'] : ''; ?>" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h4>What did you find?</h4>
                        <input type="url" name="link_to_discovery" value="<?php echo isset( $responses['link_to_discovery'] ) ? $responses['link_to_discovery'] : ''; ?>" class="form-control" required placeholder="https://">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h4>Why do you love it?</h4>
                        <textarea name="why_love_it" class="form-control" rows="7" required><?php echo isset( $responses['why_love_it'] ) ? $responses['why_love_it'] : ''; ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h4>How did you find it?</h4>
                        <div class="label-group">
                            <label>
                              <input type="radio" value="Hash tag" name="how_did_you_find" <?php echo isset( $responses['how_did_you_find'] ) && 'Hash tag' == stripslashes_deep( $responses['how_did_you_find'] ) ? ' checked="checked"' : ''; ?> required>
                              <span class="label-text">Hash tag</span>
                              <div class="label-sub-field hide"><input type="text" name="how_did_you_find_hashtag" value="<?php echo isset( $responses['how_did_you_find_hashtag'] ) ? $responses['how_did_you_find_hashtag'] : ''; ?>" class="form-control" placeholder="#"></div>
                            </label>

                            <label>
                              <input type="radio" value="Explore portal" name="how_did_you_find" <?php echo isset( $responses['how_did_you_find'] ) && 'Explore portal' == stripslashes_deep( $responses['how_did_you_find'] ) ? ' checked="checked"' : ''; ?> required>
                              <span class="label-text">Explore portal</span>
                            </label>

                            <label>
                              <input type="radio" value="Following account" name="how_did_you_find" <?php echo isset( $responses['how_did_you_find'] ) && 'Following account' == stripslashes_deep( $responses['how_did_you_find'] ) ? ' checked="checked"' : ''; ?> required>
                              <span class="label-text">Following account</span>
                              <div class="label-sub-field hide"><input type="text" name="how_did_you_find_account" value="<?php echo isset( $responses['how_did_you_find_account'] ) ? $responses['how_did_you_find_account'] : ''; ?>" class="form-control" placeholder="@"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h4>What’s your Instagram handle?</h4>
                        <input type="text" name="user_instagram_handle" value="<?php echo isset( $responses['user_instagram_handle'] ) ? $responses['user_instagram_handle'] : ''; ?>" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h4>What's your email?</h4>
                        <input type="email" name="user_email" value="<?php echo isset( $responses['user_email'] ) ? $responses['user_email'] : ''; ?>" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                  <div class="col-12 mt-5">
                    <label>
                      <input type="checkbox" name="consent_tcp" checked> I agree to the <a href="https://thebrag.media/terms-and-conditions/" target="_blank">Terms and Conditions</a> and <a href="https://thebrag.media/privacy-policy/" target="_blank">Privacy Policy</a>
                    </label>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12 mt-4 mb-5">
                    <input type="submit" name="submit_instagram_survey" class="btn-dark" value="Submit">
                  </div>
                </div>
            </form>
            <?php

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

<style>
#instagram-survey-form-oct19 .label-group .label-sub-field {
    margin-left: 17px;
}
#instagram-survey-form-oct19 .label-group .label-sub-field.hide {
    display: none;
}
#instagram-survey-form-oct19 .label-group label {
    margin-right: 10px;
    display: block;
}
#instagram-survey-form-oct19 .label-group .label-text {
}
</style>

<script>
jQuery(document).ready(function($){
    // $('.label-sub-field').addClass('hide').find('input').val('');

    $('#instagram-survey-form-oct19 input[type=radio][name="how_did_you_find"]').each(function() {
        if ( $(this).prop('checked') ) {
            if ( $(this).parent().find('.label-sub-field').length ) {
                $(this).parent().find('.label-sub-field').removeClass('hide');
            }
        }
    });

    $('#instagram-survey-form-oct19 input[type=radio][name="how_did_you_find"]').on('change', function() {
        $('.label-sub-field').addClass('hide').find('input').val('');
        if ( $(this).prop('checked') ) {
            if ( $(this).parent().find('.label-sub-field').length ) {
                $(this).parent().find('.label-sub-field').removeClass('hide').find('input').focus();
            }
        }
    });
});
</script>

<?php get_footer();
