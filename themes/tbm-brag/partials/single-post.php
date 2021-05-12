<?php
$the_post_id = get_the_ID();
$count_articles = isset( $_POST['count_articles'] ) ? (int) $_POST['count_articles'] : 1;
if ( ! post_password_required( $post ) ) :
    $author_byline = '';
    $author_image = '';
    $author_id = 0;

    if ( get_field('author') || get_field('Author') ) :
        if ( get_field( 'author' ) ) :
            $author_byline = $author_name = get_field( 'author' );
        elseif ( get_field( 'Author' ) ) :
            $author_byline = $author_name = get_field( 'Author' );
        endif; // If custom author is set

        $author_img_src = wp_get_attachment_image_src( get_field('author_profile_picture'), 'thumbnail' );
        if ( $author_img_src ) :
            $author_image = '<img src="' . $author_img_src[0] . '" width="72" class="rounded-circle">';
        else :
          $author_image = '<img src="' . get_template_directory_uri() . '/images/default-avatar.png" width="72" class="rounded-circle">';
        endif; // If custom author image is set
    else : // If custom author has not bee set
        if ( '' != get_the_author_meta( 'first_name', $post->post_author ) && '' != get_the_author_meta( 'last_name', $post->post_author ) ) :
            $author_byline = '<a href="' . get_author_posts_url( $post->post_author ) . '">' . get_the_author_meta( 'first_name', $post->post_author ) . ' ' . get_the_author_meta( 'last_name', $post->post_author ) . '</a>';
            $author_name = get_the_author_meta( 'first_name', $post->post_author ) . ' ' . get_the_author_meta( 'last_name', $post->post_author );
        else :
            $author_byline = '<a href="' . get_author_posts_url( $post->post_author ) . '">' . get_the_author_meta( 'display_name', $post->post_author ) . '</a>';
            $author_name = get_the_author_meta( 'display_name', $post->post_author );
        endif; // If Post Author's first and last name not empty

        $author_image = get_avatar( $post->post_author, 72, get_template_directory_uri() . '/images/default-avatar.png', '', array( 'class' => 'rounded-circle') );
    endif; // If custom author is set
?>

<div class="container single single_story py-3" id="<?php the_ID(); ?>">
    <div class="row">
      <div class="col-lg-8">
          <?php
          $title = get_post_meta(get_the_ID(), '_yoast_wpseo_title', true) ? get_post_meta(get_the_ID(), '_yoast_wpseo_title', true) : get_the_title();
          if ( strpos( $title, '%%title%%' ) !== FALSE ) {
            $title = get_the_title();
          }
          ?>
          <h1 id="story_title<?php echo get_the_ID(); ?>" class="mb-3" data-href="<?php the_permalink(); ?>" data-title="<?php echo htmlentities( $title );?>" data-share-title="<?php echo urlencode( $title ); ?>" data-share-url="<?php echo urlencode(get_permalink()); ?>" data-article-number="<?php echo $count_articles; ?>"><?php the_title(); ?></h1>

          <?php

          $tags = get_the_tags(get_the_ID());
          $TagsCD = '';
          if ( $tags ) :
              foreach( $tags as $tag ) :
                  $TagsCD .= $tag->slug . ' ';
              endforeach; // For Each Tag
          endif; // If there are tags for the post

          if ( 'dad' != get_post_type() ) :
            $categories = get_the_category(get_the_ID());
            $CategoryCD = '';
            if ( $categories ) :
                foreach( $categories as $category ) :
                    $CategoryCD .= $category->slug . ' ';
                endforeach; // For Each Category
            endif; // If there are categories for the post
            ?>
            <div class="cats mb-3" data-category="<?php echo $CategoryCD; ?>" data-tags="<?php echo $TagsCD; ?>">
              <?php
                if ( isset( $categories ) ) :
                  foreach( $categories as $category ) :
                    if ( 'Evergreen' == $category->cat_name ) :
                        continue;
                    endif; // If category name is Evergreen
                ?>
                <a class="d-inline-block small text-uppercase cat" href="<?php echo get_category_link( $category->term_id ); ?>"><?php echo $category->cat_name; ?></a>
                <?php
                    endforeach; // For Each Category
                endif; // If there are categories for the post ?>
            </div><!-- Cats -->
          <?php else : // Post type = Dad
            $categories = get_the_terms(get_the_ID(), 'dad-category');
            $CategoryCD = '';
            if ( $categories ) :
                foreach( $categories as $category ) :
                    $CategoryCD .= $category->slug . ' ';
                endforeach; // For Each Category
            endif; // If there are categories for the post
            ?>
            <div class="cats mb-3" data-category="<?php echo $CategoryCD; ?>" data-tags="<?php echo $TagsCD; ?>">
              <?php
                if ( $categories ) :
                  foreach( $categories as $category ) :
                    if ( 'Uncategorised' == $category->cat_name ) :
                        continue;
                    endif; // If category name is Uncategorised
                ?>
                <a class="d-inline-block small text-uppercase cat" href="<?php echo get_term_link( $category, 'dad-category' ); ?>"><?php echo $category->cat_name; ?></a>
                <?php
                    endforeach; // For Each Category
                endif; // If there are categories for the post ?>
            </div><!-- Cats -->
          <?php endif; // If Post Type != Dad ?>

            <?php if ( '' !== get_the_post_thumbnail() && 'issue' != get_post_type() ) : ?>
            <div class="post-thumbnail mb-3">
                <?php
                $alt_text = get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true );
                if ( $alt_text == '' ) :
                    $alt_text = trim(strip_tags(get_the_title()));
                endif; // If Alt text for featured image is empty

                echo get_the_post_thumbnail( null, 'medium_large', array(
                    'alt' => $alt_text,
                    'title' => $alt_text,
                    'class' => 'img-fluid',
                ));

                if ( get_field( 'image_credit' ) ) :
                    echo '<p class="image-credit text-right">Image: ' . get_field( 'image_credit' ) . '</p>';
                elseif ( (get_field('Image Credit')) ) :
                    echo '<p class="image-credit text-right">Image: ' . get_field('Image Credit', '') . '</p>';
                endif; // If custom field - image credit - is set
                ?>
            </div><!-- .post-thumbnail -->
            <?php endif; // If post has thumbnail AND post type is not issue ?>

            <div class="post-meta align-items-start justify-content-between d-block d-md-flex mb-3">
              <div class="align-items-center d-flex mb-3 mb-md-0">

                <div class="author" data-author="<?php echo $author_name; ?>">
                    By <?php echo $author_byline; ?>
                    <br>
                    <time datetime="<?php echo date( 'Y-m-d\TH:i:s+10:00', get_the_time( 'U' ) ); ?>" data-pubdate="<?php echo get_the_time('M d, Y'); ?>"><?php echo get_the_time('M d, Y'); ?></time>
                </div>
                <div class="ml-3"><?php echo $author_image; ?></div>

              </div>

              <div>
              <?php
              if ( shortcode_exists( 'shout_writer_beer' ) ) :
                  echo do_shortcode( '[shout_writer_beer author="' . $author_name . '"]' );
              elseif ( shortcode_exists( 'shout_writer_coffee' ) ) :
                  echo do_shortcode( '[shout_writer_coffee author="' . $author_name . '"]' );
              endif; // If shout writer shortcode exists
              ?>
              </div>

              <!-- <div class="d-none d-md-block">
                <?php // do_action( 'ssm_social_sharing_buttons', 'row' ); ?>
              </div> -->
            </div><!-- Author, Coffee and Share buttons -->

            <?php if ( in_category( 'Op-Ed/Comment' ) ) : ?>
                <div style="padding: 10px 0; font-weight: bold;">COMMENT</div>
            <?php endif; // If the post has a category Op-Ed/Comment ?>

            <?php if ( in_category( 'Fiction' ) ) : ?>
                <div style="padding: 10px 0; font-weight: bold;">FICTION</div>
            <?php endif; // If the post has a category Fiction ?>

            <?php
            if ( shortcode_exists( 'observer_subscribe_category' ) ) :
              echo do_shortcode( '[observer_subscribe_category id="' . get_the_ID() . '"]' );
            endif;
            ?>

<!-- BandsInTown {{ -->
<?php
if ( 0 && ! get_field( 'paid_content', get_the_ID() ) ) : // Disabled by adding 0 to condition, on 18 Sep, 2020
?>
<div id="bandsintown-<?php echo get_the_ID(); ?>"></div>
<script>
/* In-Read - thebrag */
(function() {
	var opts = {
		artist: "",
		song: "",
		detect_artist: true,
		adunit_id: 100001686,
		div_id: "cf_async_" + Math.floor((Math.random() * 999999999))
	};
  var cf_div = document.createElement("div");
  cf_div.setAttribute("id", opts.div_id);
  document.getElementById("bandsintown-<?php echo get_the_ID(); ?>").appendChild(cf_div);
  var c=function(){cf.showAsyncAd(opts)};if(typeof window.cf !== 'undefined')c();else{cf_async=!0;var r=document.createElement("script"),s=document.getElementsByTagName("script")[0];r.async=!0;r.src="//srv.clickfuse.com/showads/showad.js";r.readyState?r.onreadystatechange=function(){if("loaded"==r.readyState||"complete"==r.readyState)r.onreadystatechange=null,c()}:r.onload=c;s.parentNode.insertBefore(r,s)};
})();
</script>
<?php endif; ?>
<!-- }} BandsInTown -->

            <?php
            if( get_field( 'promoted_text' ) && '' != get_field( 'promoted_text' ) ) :
                if ( get_field( 'promoted_text_link' ) && '' != get_field( 'promoted_text_link' ) ) :
            ?>
                <a href="<?php echo get_field('promoted_text_link'); ?>" target="_blank" class="text-dark">
                <?php endif; // If promoted_text_link ?>
                    <div class="p-3 mb-3 d-flex align-items-center" style="border: 1px solid #b2b2b2; font-size: 110%">
                        <div><?php echo get_field('promoted_text'); ?></div>
                        <?php if( get_field( 'promoted_logo' ) && '' != get_field( 'promoted_logo' ) ) : ?>
                            <img src="<?php echo get_field( 'promoted_logo' ); ?>" style="width: 100px;">
                        <?php endif; // If promoted_logo ?>
                    </div>
                <?php if ( get_field( 'promoted_text_link' ) && '' != get_field( 'promoted_text_link' ) ) : ?>
                </a>
            <?php
                endif; // If promoted_text_link
            endif; // If promoted_text
            ?>

            <div class="post-content">
            <?php
            if ( 'issue' == get_post_type() ) :
                if ( get_field('document_id') ):
                    $document_id = get_field('document_id');
            ?>
                <div style="width: 600px; margin: auto; max-width: 100%;">
                    <object classid="clsid:<?php echo $document_id; ?>" style="width: 600px; height:400px; max-width: 100%;" id="<?php echo $document_id; ?>">
                        <param name="movie" value="https://static.issuu.com/webembed/viewers/style1/v2/IssuuReader.swf?mode=mini&amp;backgroundColor=&amp;documentId=<?php echo $document_id; ?>">
                        <param name="allowfullscreen" value="true">
                        <param name="menu" value="false">
                        <param name="wmode" value="transparent">
                        <embed src="https://static.issuu.com/webembed/viewers/style1/v2/IssuuReader.swf" type="application/x-shockwave-flash" allowfullscreen="true" menu="false" wmode="transparent" style="width: 600px; height:400px; max-width: 100%;" flashvars="mode=mini&amp;backgroundColor=&amp;documentId=<?php echo $document_id; ?>">
                    </object>
                </div>
                <?php endif; // If document_id is set for Post type Issue ?>

                <?php if ( get_field('issuu_link') ) : ?>
                    <a href="<?php echo get_field('issuu_link'); ?>" target="_blank">PDF Download Link</a>
                <?php endif; // If issuu_link is set for Post type Issue
            endif; // If the post type is Issue

            if ( ! get_field('paid_content', get_the_ID()) ) :
                $content = apply_filters( 'the_content', $post->post_content );
                echo $content;
                $args = array (
                    'before'            => '<div class="page-links">',
                    'after'             => '</div>',
                    'link_before'       => '',
                    'link_after'        => '',
                    'next_or_number'    => 'next',
                    'separator'         => ' ',
                    'nextpagelink'      => 'Next &raquo',
                    'previouspagelink'  => '&laquo Previous',
                );
                wp_link_pages( $args );
            else :
                $content = apply_filters( 'the_content', $post->post_content );
                echo $content;
            endif; // If it's a paid content
            ?>

            <!-- GotChosen {{ -->
<?php if (0 && ! get_field( 'paid_content', get_the_ID() ) ) : ?>
  <div class="gcwp-carousel"></div>
<?php endif; ?>
            <!-- }} GotChosen -->

            <!-- Apester -->
            <!-- <interaction data-token="5decf535b7735f79ae29b691" data-context="true" data-tags="" data-fallback="true"></interaction> -->
            <?php // if( $count_articles > 1 ) : ?>
              <script>// window.APESTER.reload()</script>
            <?php // endif; ?>

            <?php if ( in_category( 'Op-Ed/Comment' ) ) : ?>
              <div class="mt-5 py-3" style="border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
                  <div class="d-flex flex-column">
                      <div class="post-meta row">
                          <div class="col-2 text-right"><?php echo $author_image; ?></div>
                          <div class="author col-10 align-self-center">
                              <div class="d-flex flex-row justify-content-between">
                                  <h5 data-author="<?php echo $author_name; ?>" class="align-self-center"><?php echo $author_byline; ?></h5>
                                  <?php if ( get_field('author') || get_field('Author') ) :
                                  else :
                                      $author = get_userdata( intval( $post->post_author ) );
                                  ?>
                                  <ul class="nav align-self-center">
                                      <?php if ( $author->twitter != '' ) : ?>
                                          <li class="nav-item"><a href="<?php echo $author->twitter; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-twitter-square fa-lg" aria-hidden="true"></i></a></li>
                                      <?php endif; ?>
                                      <?php if ( $author->facebook != '' ) : ?>
                                          <li class="nav-item"><a href="<?php echo $author->facebook; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-facebook-square fa-lg" aria-hidden="true"></i></a></li>
                                      <?php endif; ?>
                                      <?php if ( $author->linkedin != '' ) : ?>
                                          <li class="nav-item"><a href="<?php echo $author->linkedin; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-linkedin-square fa-lg" aria-hidden="true"></i></a></li>
                                      <?php endif; ?>
                                      <?php if ( $author->instagram != '' ) : ?>
                                          <li class="nav-item"><a href="<?php echo $author->instagram; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-instagram fa-lg" aria-hidden="true"></i></a></li>
                                      <?php endif; ?>
                                  </ul>
                                  <?php
                                  endif;
                                  ?>
                              </div>
                              <div>
                              <?php
                              if ( get_field('author') || get_field('Author') ) :
                                  if ( (get_field('author_bio')) ) :
                                      echo wpautop(get_field('author_bio'));
                                  endif;
                              else : // If custom author has not bee set
                                  echo wpautop( get_the_author_meta( 'description', $post->post_author ) );
                              endif;
                              ?>
                              </div>
                          </div>
                      </div>
                  </div>
              </div><!-- Author details, author socials -->
            <?php endif; ?>

            </div><!-- /.post-content -->
            <?php
            if ( get_field( 'impression_tag' ) ) :
                echo str_replace( '[timestamp]', time(), get_field( 'impression_tag' ) );
            endif; // If custom field - impression tag - is set
            ?>
            <!-- Story End -->
          </div><!-- Left panel - content, etc. -->

          <?php // if ( ! is_mobile() ) : ?>
          <div class="col-lg-4 right-col-has-ad d-none d-lg-block">
              <div class="d-flex flex-column" style="height: 100%;">

                <div class="align-self-center mb-3">
                  <?php render_ad_tag( 'rail1', 'single', $count_articles ); ?>
                </div>

                  <?php
                  include( get_template_directory() . '/partials/trending.php' );
                  ?>
                  <!-- <a href="<?php // echo home_url( '/observer/' ); ?>"><img src="<?php // echo get_template_directory_uri(); ?>/images/observer/mrec-600px.jpg"></a> -->

                  <div class="sticky-ad">
                    <div class="mt-3">
                      <?php
                      // render_ad_tag( 'side_2', 'single', $count_articles );
                      render_ad_tag( 'rail2', 'single', $the_post_id . $count_articles );
                      ?>
                    </div>

                    <!-- <div class="mt-3"> -->
                      <?php // render_ad_tag( 'side_3', 'single', $count_articles ); ?>
                    <!-- </div> -->

                  </div>
              </div>
          </div><!-- Right Pane - Cats, Author, Coffee and Share buttons - for desktop devices -->
          <?php // endif; ?>
      </div><!-- Row 2 -->
  </div><!-- .container .single_story -->

<?php
$ads_after_article_number = 3;
$max_articles = 9;
if ( 0 && 0 == $count_articles % $ads_after_article_number ): // Disabled on 19 Feb, 2021 ?>
<div class="container my-5">
    <?php
    $mode = 'alternating-thumbnails-a';
    if ( 2 == $count_articles / $ads_after_article_number ) :
        $mode = 'thumbnails-c';
    elseif( 1 == $count_articles / $ads_after_article_number ) :
        $mode = 'thumbnails-a';
    endif;
    ?>
    <div class="row">
        <div class="col-12">
            <div id="taboola-below-article-thumbnails<?php echo $count_articles / $ads_after_article_number; ?>" class=""></div>
        </div>
        <?php // if ( 1 == $count_articles / $ads_after_article_number ) : ?>
        <script type="text/javascript">
          window._taboola = window._taboola || [];
          _taboola.push({
            mode: '<?php echo $mode; ?>',
            container: 'taboola-below-article-thumbnails<?php echo $count_articles / $ads_after_article_number; ?>',
            placement: 'Below Article Thumbnails<?php echo $count_articles < $max_articles ? ' ' . $count_articles / $ads_after_article_number : ''; ?>',
            target_type: 'mix'
          });
        </script>
        <?php // endif; ?>
    </div>
</div>
<?php endif; // If it's a multiplication of 3 article in infinite scroll ?>

<?php elseif( $count_articles == 1 ) :
    echo '<style>.load-more{display:none;}</style>';
    echo get_the_password_form();
endif;
