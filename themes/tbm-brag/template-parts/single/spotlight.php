<?php extract($args); ?>
<section class="container spotlight">
    <div class="m-md-2">
        <h2 class="text-center text-uppercase p-1 pt-0 mt-3 mb-0 mx-1 h-spotlight">Spotlight</h2>
        <div class="spotlight-stories d-flex <?php echo isset($args['pos']) && 'sidebar' == $args['pos'] ? 'flex-column' : 'flex-row'; ?> align-items-start rounded bg-white mt-2">
            <?php
            while ($spotlight_articles->have_posts()) :
                $spotlight_articles->the_post();
                $categories = get_the_category(get_the_ID());
            ?>
                <a href="<?php the_permalink(); ?>" class="story m-1 m-md-2 pb-0">
                    <div class="d-flex flex-column flex-md-row align-items-start">
                        <div class="img-wrap rounded mr-0 mr-md-2">
                            <?php if ('' !== get_the_post_thumbnail()) :
                                the_post_thumbnail('thumbnail');
                            endif; ?>
                        </div>
                        <div>
                            <div class="mb-1 mt-1 mt-md-0 text-uppercase spotlight-story-category">
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
                        </div>

                    </div>
                </a>
            <?php endwhile;
            wp_reset_postdata();

            ?>
        </div>
    </div>
</section>