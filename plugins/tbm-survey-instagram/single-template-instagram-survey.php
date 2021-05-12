<?php
/*
 * Template Name: Instagram Survey Sep 2019
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
          if ( isset( $_POST['nonce_insta_survey_sep19'] ) && wp_verify_nonce( $_POST['nonce_insta_survey_sep19'], 'action_insta_survey_sep19' ) ) :
        $form_errors = array();
        $responses = array();


          $required_fields = array(
            'user_email',
            'looked_at_explore',
            'discovered_new_artist',
            'discovered_new_hashtags',
            'user_country',
            'age',
            'consent_tcp'
          );

          /*
          * Validate fields
          */
          foreach( $required_fields as $required_field ) :
            if ( isset( $_POST[$required_field]) ) :
              $responses[$required_field] = sanitize_text_field( $_POST[$required_field] );
            else :
              $form_errors = array( 'All fields are mandatory.' );
            endif;
          endforeach;

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
              $wpdb->prefix . 'insta_survey_responses',
              array(
                'user_email' => $responses['user_email'],
                'looked_at_explore' => $responses['looked_at_explore'],
                'discovered_new_artist' => $responses['discovered_new_artist'],
                'discovered_new_hashtags' => $responses['discovered_new_hashtags'],
                'age' => $responses['age'],
                'user_country' => $responses['user_country'],
                'created_at' => current_time( 'mysql' ),
                'updated_at' => current_time( 'mysql' ),
              )
            );
            $lead_id = $wpdb->insert_id;

            wp_safe_redirect( add_query_arg( array(
                'success' => '1',
            ), get_the_permalink() ) );
            exit;

            echo '<div class="col-12 mb-2"><div class="alert alert-success text-center">Thanks! You\'re in the draw!</div></div>';

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
          <div class="col-12 mb-2"><div class="alert alert-success text-center">Thanks! You're in the draw!</div></div>
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

                <div class="left-col-has-ad mt-1">
                    <div class="sticky-ad"><?php get_fuse_tag( 'mrec_2', 'single' ); ?></div>
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
            <form action="<?php echo get_the_permalink(); ?>" method="post" id="instagram-survey-form" novalidate>
                <?php wp_nonce_field( 'action_insta_survey_sep19', 'nonce_insta_survey_sep19' ); ?>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h4>Have you looked at your Instagram Explore page in the last 7 days?</h4>
                        <div class="label-group">
                            <label class="">
                              <input type="radio" value="Yes" name="looked_at_explore" <?php echo isset( $responses['looked_at_explore'] ) && 'Yes' == stripslashes_deep( $responses['looked_at_explore'] ) ? ' checked="checked"' : ''; ?> required>
                              <span class="img-wrap"><img src="https://media.giphy.com/media/l4q83E0RjRSGLXBLO/giphy.gif" width="100"></span>
                              <span class="label-text">Yes</span>
                            </label>

                            <label class="">
                              <input type="radio" value="No" name="looked_at_explore" <?php echo isset( $responses['looked_at_explore'] ) && 'No' == stripslashes_deep( $responses['looked_at_explore'] ) ? ' checked="checked"' : ''; ?> required>
                              <span class="img-wrap"><img src="https://media.giphy.com/media/26DOzMVt4i9GzzxyE/giphy.gif" width="100"></span>
                              <span class="label-text">No</span>
                            </label>

                            <label class="">
                              <input type="radio" value="Whats that" name="looked_at_explore" <?php echo isset( $responses['looked_at_explore'] ) && 'Whats that' == stripslashes_deep( $responses['looked_at_explore'] ) ? ' checked="checked"' : ''; ?> required>
                              <span class="img-wrap"><img src="https://media.giphy.com/media/ybcMkow8xLIrK/200w_d.gif" width="100"></span>
                              <span class="label-text">What's that?</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h4>Have you discovered a new artist on Instagram over the last 7 weeks?</h4>
                        <div class="label-group">
                            <?php
                            $arr_discovered_new_artists = array(
                                'No' => 'http://giphygifs.s3.amazonaws.com/media/Az1CJ2MEjmsp2/giphy.gif',
                                '1 artist' => 'https://media.giphy.com/media/l0HUdWwVhf1xsGvyU/giphy.gif',
                                '2 artists' => 'https://media.giphy.com/media/dYQ9x1sX1u1dIuLXG8/giphy.gif',
                                '3 artists' => 'https://media.giphy.com/media/B2zaaRsfBxS5tGSNyj/giphy.gif',
                                '4+ artists' => 'https://media.giphy.com/media/26DMXAEYxdwI37KKI/giphy.gif',
                            );
                            foreach( $arr_discovered_new_artists as $item_discovered_new_artists => $image_discovered_new_artists ) :
                            ?>
                            <label class="">
                              <input type="radio" value="<?php echo $item_discovered_new_artists; ?>" name="discovered_new_artist" <?php echo isset( $responses['discovered_new_artist'] ) && $item_discovered_new_artists == stripslashes_deep( $responses['discovered_new_artist'] ) ? ' checked="checked"' : ''; ?> required>
                              <span class="img-wrap"><img src="<?php echo $image_discovered_new_artists; ?>" width="100"></span>
                              <span class="label-text"><?php echo $item_discovered_new_artists; ?></span>
                            </label>
                            <?php
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h4>Have you discovered new hashtags to follow in the last 7 weeks?</h4>
                        <div class="label-group">
                            <?php
                            $arr_discovered_new_hashtags = array(
                                'None' => 'http://giphygifs.s3.amazonaws.com/media/Az1CJ2MEjmsp2/giphy.gif',
                                '1 hashtag' => 'https://media.giphy.com/media/xT39CSmSwZnQVhOZd6/giphy.gif',
                                '2 hashtags' => 'https://media.giphy.com/media/xTiN0h0Kh5gH7yQYUw/giphy.gif',
                                '3 hashtags' => 'https://media.giphy.com/media/26AHIbtfGWCi2Q2C4/giphy.gif',
                                '4+ hashtags' => 'https://media.giphy.com/media/HjheuybfwDGnu/giphy.gif',
                            );
                            foreach( $arr_discovered_new_hashtags as $item_discovered_new_hashtags => $image_discovered_new_hashtags ) :
                            ?>
                            <label class="">
                              <input type="radio" value="<?php echo $item_discovered_new_hashtags; ?>" name="discovered_new_hashtags" <?php echo isset( $responses['discovered_new_hashtags'] ) && $item_discovered_new_hashtags == stripslashes_deep( $responses['discovered_new_hashtags'] ) ? ' checked="checked"' : ''; ?> required>
                              <span class="img-wrap"><img src="<?php echo $image_discovered_new_hashtags; ?>" width="100"></span>
                              <span class="label-text"><?php echo $item_discovered_new_hashtags; ?></span>
                            </label>
                            <?php
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h4>What country do you live in?</h4>
                        <div class="label-group">
                            <?php
                            $arr_country = array(
                                'Australia' => 'https://media.giphy.com/media/7P6eKzjpriYV2/giphy.gif',
                                'Other' => 'https://media.giphy.com/media/l3V0megwbBeETMgZa/giphy.gif'
                            );
                            foreach( $arr_country as $item_country => $image_country ) :
                            ?>
                            <label class="">
                              <input type="radio" value="<?php echo $item_country; ?>" name="user_country" <?php echo isset( $responses['user_country'] ) && $item_country == stripslashes_deep( $responses['user_country'] ) ? ' checked="checked"' : ''; ?> required>
                              <span class="img-wrap"><img src="<?php echo $image_country; ?>" width="100"></span>
                              <span class="label-text"><?php echo $item_country; ?></span>
                            </label>
                            <?php
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h4>What's your email?</h4>
                        <input type="text" name="user_email" value="<?php echo isset( $responses['user_email'] ) ? $responses['user_email'] : ''; ?>" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h4>What's your age?</h4>
                        <div class="label-group">
                            <?php
                            $arr_age = array(
                                'Younger than 22' =>
                                    array(
                                        'text' => '22 and under',
                                        'image' => 'https://media.giphy.com/media/XweOsBl72PFcc/giphy.gif',
                                    ),
                                '23-38' =>
                                    array(
                                        'text' => '23-38',
                                        'image' => 'https://media.giphy.com/media/sT8Oe6KirjpWU/giphy.gif',
                                    ),
                                '39+' =>
                                    array(
                                        'text' => '23-38',
                                        'image' => 'https://media.giphy.com/media/l4KibWpBGWchSqCRy/giphy.gif'
                                    ),
                            );
                            foreach( $arr_age as $item_age => $data_age ) :
                            ?>
                            <label class="">
                              <input type="radio" value="<?php echo $item_age; ?>" name="age" <?php echo isset( $responses['age'] ) && $item_age == stripslashes_deep( $responses['age'] ) ? ' checked="checked"' : ''; ?> required>
                              <span class="img-wrap"><img src="<?php echo $data_age['image']; ?>" width="100"></span>
                              <span class="label-text"><?php echo $data_age['text']; ?></span>
                            </label>
                            <?php
                            endforeach;
                            ?>
                        </div>
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
#instagram-survey-form [type=radio] {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

/* IMAGE STYLES */
#instagram-survey-form [type=radio] + span.img-wrap {
  cursor: pointer;
  padding: 4px;
  display: block;
  text-align: center;
  height: 100px;
  width: 100px;
  max-width: 100%;
  overflow: hidden;
  position: relative;;
  /* opacity: 0.7; */
}

/* CHECKED STYLES */
#instagram-survey-form [type=radio]:checked + span.img-wrap {
  /* outline: 2px solid #4184ae; */
  outline: 4px solid #000;
  opacity: 1;
}

#instagram-survey-form [type=radio]:checked + span.img-wrap:after {
    background: rgba(0,0,0,.5);
    content: "";
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
}

#instagram-survey-form [type=radio] + span.img-wrap img {
    height: 100px;
    width: auto;
    max-width: none;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
}

#instagram-survey-form [type=radio] + span.img-wrap span {
    display: block;
    position: absolute;
    text-align: center;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
}

/* #instagram-survey-form label {
    display: inline-block;
} */

#instagram-survey-form .label-group {
    display: flex;
    flex-wrap: wrap;
}
#instagram-survey-form .label-group label {
    width: 100px;
    height: 120px;
    margin-right: 10px;
}
#instagram-survey-form .label-group .label-text {
    display: block;
    magin-top: 10px;
    text-align: center;
}

</style>

<?php get_footer();
