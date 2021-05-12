<?php
// $post = $wp_query->post;
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
            $author_image = '<img src="' . $author_img_src[0] . '" width="72" class="rounded-circle" style="height: 100%;
    max-width: none;
    width: auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);">';
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

  $categories = get_the_category(get_the_ID());
  $CategoryCD = '';
  if ( $categories ) :
      foreach( $categories as $category ) :
          $CategoryCD .= $category->slug . ' ';
      endforeach; // For Each Category
  endif; // If there are categories for the post

  $tags = get_the_tags(get_the_ID());
  $TagsCD = '';
  if ( $tags ) :
      foreach( $tags as $tag ) :
          $TagsCD .= $tag->slug . ' ';
      endforeach; // For Each Tag
  endif; // If there are tags for the post
?>
<?php if ( '' !== get_the_post_thumbnail() ) : ?>
<div class="post-thumbnail post-thumbnail-featured mb-3">
    <?php
    $alt_text = get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true );
    if ( $alt_text == '' ) :
        $alt_text = trim(strip_tags(get_the_title()));
    endif; // If Alt text for featured image is empty

    echo get_the_post_thumbnail( null, 'full', array(
        'alt' => $alt_text,
        'title' => $alt_text,
        'class' => 'img-fluid',
    ));

    if ( get_field( 'image_credit' ) ) :
        echo '<p class="image-credit text-right" style="position: absolute; bottom: 4rem; right: 0; background: #fff; color: #333; padding-left: .5rem; padding-right: .5rem; margin: 0;">Image: ' . get_field( 'image_credit' ) . '</p>';
    elseif ( (get_field('Image Credit')) ) :
        echo '<p class="image-credit text-right" style="position: absolute; bottom: 4rem; right: 0; background: #fff; color: #333; padding-left: .5rem; padding-right: .5rem; margin: 0;">Image: ' . get_field('Image Credit', '') . '</p>';
    endif; // If custom field - image credit - is set
    ?>
    <!-- <div class="c-featured-hero__text-layer">
      <h1 style="color: <?php // echo get_field('heading_colour') ? get_field('heading_colour') : '#333'; ?>; font-size: <?php //echo get_field('heading_font_size') ? get_field('heading_font_size') . 'px' : '2.5rem'; ?>"><?php // the_title(); ?></h1>
    </div> -->
</div><!-- .post-thumbnail -->
<?php endif; // If post has thumbnail ?>
<div class="container single single_story single-featured py-3" id="<?php the_ID(); ?>">
  <header class="c-featured-article__header">
    <div class="c-featured-article__breadcrumbs t-semibold t-semibold--upper">
      <div class="cats mb-3" data-category="<?php echo $CategoryCD; ?>" data-tags="<?php echo $TagsCD; ?>">
      <?php
        if ( isset( $categories ) ) :
            foreach( $categories as $category ) :
              if( 'Instagram Explore' == $category->cat_name ) :
        ?>
              <span class="cat mr-2"><i class="fa fa-instagram"></i> Instagram Explore</span>
        <?php
              elseif ( 'Evergreen' == $category->cat_name ) :
                    continue;
                    else :

        ?>
        <a class="d-inline-block small text-uppercase cat" href="<?php echo get_category_link( $category->term_id ); ?>"><?php echo $category->cat_name; ?></a>
        <?php
        endif; // If category name is Evergreen
            endforeach; // For Each Category
        endif; // If there are categories for the post ?>
      </div>
			</div>

      <?php
      $title = get_post_meta(get_the_ID(), '_yoast_wpseo_title', true) ? get_post_meta(get_the_ID(), '_yoast_wpseo_title', true) : get_the_title();
      if ( strpos( $title, '%%title%%' ) !== FALSE ) {
        $title = get_the_title();
      }
      ?>

      <h1 id="story_title<?php echo get_the_ID(); ?>" class="mb-3 d-none2" data-href="<?php the_permalink(); ?>" data-title="<?php echo htmlentities( $title );?>" data-share-title="<?php echo urlencode( $title ); ?>" data-share-url="<?php echo urlencode(get_permalink()); ?>" data-article-number="<?php echo $count_articles; ?>"><?php the_title(); ?></h1>

			<!-- <h2 class="c-featured-article__subtitle t-bold"> -->
				<?php // the_excerpt(); ?>
      <!-- </h2><!-- /.c-featured-article__title -->

      <?php // if ( ! in_category( 'competitions' ) ) : ?>
      <div class="c-featured-article__meta">
        <div class="c-featured-article__avatar">
          <div class="rounded-circle" style="width: 72px;
height: 72px;
overflow: hidden;
position: relative;"><?php echo $author_image; ?></div>
        </div><!-- /.c-featured-article__avatar -->
					<div class="c-featured-article__byline">

<div class="c-byline c-byline--small">
	<div class="c-byline__authors">

		<div class="c-byline__author">
      <em class="c-byline__by">By</em>
			<?php echo $author_byline; ?>
		</div><!-- .c-byline__author -->
	</div><!-- .c-byline__authors -->
</div><!-- .c-byline -->

					</div><!-- /.c-featured-article__byline -->
								<time datetime="<?php echo date( 'Y-m-d\TH:i:s+10:00', get_the_time( 'U' ) ); ?>" data-pubdate="<?php echo get_the_time('M d, Y'); ?>"><?php echo get_the_time('M d, Y'); ?></time><!-- .l-article-header__block--time -->
							</div>
            <?php //endif; ?>
		</header>
    <div class="row">
        <div class="col-12">
            <?php
            if( get_field( 'promoted_text' ) && '' != get_field( 'promoted_text' ) ) :
                if ( get_field( 'promoted_text_link' ) && '' != get_field( 'promoted_text_link' ) ) :
            ?>
                <a href="<?php echo get_field('promoted_text_link'); ?>" target="_blank" class="text-dark">
                <?php endif; // If promoted_text_link ?>
                    <div class="p-3 mb-3 d-flex align-items-center" style="border: 1px solid #b2b2b2; font-size: 90%; max-width: 39.6875rem; margin-left: auto; margin-right: auto;">
                        <div><?php echo get_field('promoted_text'); ?></div>
                        <?php if( get_field( 'promoted_logo' ) && '' != get_field( 'promoted_logo' ) ) : ?>
                          <div><img src="<?php echo get_field( 'promoted_logo' ); ?>" style="width: 100px;"></div>
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
            if ( ! get_field('paid_content', get_the_ID()) ) :
                // the_content();
                $content = apply_filters( 'the_content', $post->post_content );
                $closing_p = '</p>';
                $paragraphs = explode( $closing_p, $content );

                if ( $paragraphs[0] ) :
                ?>
                <h2 class="c-featured-article__subtitle t-bold">
          				<?php echo $paragraphs[0]; ?>
                </h2>
                <?php
                unset( $paragraphs[0] );
                endif;

                foreach ($paragraphs as $index => $paragraph) {
                  echo $paragraph;
                }
                // echo $content;
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

            <?php if ( in_category( 'Op-Ed/Comment' ) ) : ?>
              <div class="mt-5 py-3" style="border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
                  <div class="d-flex flex-column">
                      <div class="post-meta row">
                          <div class="col-2 text-right">
                            <div class="ml-3 rounded-circle" style="width: 72px;
                height: 72px;
                overflow: hidden;
                position: relative;"><?php echo $author_image; ?></div>
                          </div>
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

            <div class="mb-3 d-inline-block">
            <?php
            if ( shortcode_exists( 'shout_writer_beer' ) ) :
                echo do_shortcode( '[shout_writer_beer author="' . $author_name . '"]' );
            elseif ( shortcode_exists( 'shout_writer_coffee' ) ) :
                echo do_shortcode( '[shout_writer_coffee author="' . $author_name . '"]' );
            endif; // If shout writer shortcode exists
            ?>
            </div>

        </div><!-- Left panel - content, etc. -->
    </div><!-- Row 2 -->
</div><!-- .container .single_story -->

<?php
// echo '<hr><h1 class="text-center">' . $count_articles . '</h1><hr>';
$ads_after_article_number = 3;
$max_articles = 9;
if ( 0 == $count_articles % $ads_after_article_number ): ?>
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
<?php endif; // If it's a 3rd article in infinite scroll ?>

<?php elseif( $count_articles == 1 ) :
    echo '<style>.load-more{display:none;}</style>';
    echo get_the_password_form();
endif;
