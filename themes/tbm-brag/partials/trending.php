<div class="trending mb-2 mt-4">
  <?php
  global $wpdb;
  $trending_article_ids = $wpdb->get_results(
    "SELECT post_id FROM (
      SELECT post_id FROM `{$wpdb->prefix}tbm_trending`
      ORDER BY `created_at` DESC LIMIT 10
    ) AS temptable
    ORDER BY RAND()
    LIMIT 3"
  );
  if ($trending_article_ids && count($trending_article_ids) > 0) :
    $trending_articles_args = array(
      'post_status' => 'publish',
      'post_type' => array('post', 'country'),
      'ignore_sticky_posts' => 1,
      'post__in' => wp_list_pluck($trending_article_ids, 'post_id'),
    );
    $trending_articles = new WP_Query($trending_articles_args);
    if ($trending_articles->have_posts()) :
  ?>
      <h2 class="text-uppercase text-center trending-heading">Trending</h2>
      <?php
      while ($trending_articles->have_posts()) :
        $trending_articles->the_post();
        $categories = get_the_category(get_the_ID());

        if ('' !== get_the_post_thumbnail()) :
          $alt_text = get_post_meta(get_post_thumbnail_id(get_the_ID()), '_wp_attachment_image_alt', true);
          if ($alt_text == '') {
            $alt_text = trim(strip_tags(get_the_title()));
          }
          $img_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'thumbnail');
        endif;
      ?>
        <a href="<?php the_permalink(); ?>" class="d-block pt-3 text-dark trending-story">
          <div class="d-flex flex-row align-items-start">
            <div class="col-3 pr-0 pl-2">
              <div class="img-wrap">
                <?php if ($img_src && $img_src[0]) : ?>
                  <img data-src="<?php echo $img_src[0]; ?>" alt="<?php echo $alt_text; ?>" title="<?php echo $alt_text; ?>" class="lazyload img-fluid">
                <?php endif; ?>
              </div>
            </div>
            <div class="px-3 pb-2">
              <div class="mb-2 text-uppercase trending-story-category">
                <?php
                if (isset($categories)) :
                  foreach ($categories as $category) :
                    if (in_array($category->cat_name, ['Instagram Explore', 'Evergreen'])) :
                      continue;
                    else :
                      echo $category->cat_name;
                      break;
                    endif; // If category name is Evergreen
                  endforeach; // For Each Category
                endif; // If there are categories for the post 
                ?>
              </div><!-- Cats -->
              <h3 class="h6"><?php the_title(); ?></h3>
              <?php
              /* $author_name = get_field( 'author' ) ? get_field( 'author' ) : ( get_field( 'Author' ) ? get_field( 'Author' ): get_the_author_meta( 'first_name', $post->post_author ) . ' ' . get_the_author_meta( 'last_name', $post->post_author ) );
              echo $author_name; */
              ?>
            </div>
          </div>
        </a>
  <?php endwhile;
      wp_reset_postdata();
    endif; // If there are trending articles
  endif;
  ?>
</div>