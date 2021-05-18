<!-- <article class="my-3">
    <div class="mb-4 mx-0 mx-md-3">
        <a href="<?php the_permalink(); ?>" class="d-flex flex-row flex-md-column align-items-start">
            <div class="post-thumbnail col-5 p-r">
                <?php
                if ('' !== get_the_post_thumbnail()) :
                    $alt_text = get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true);
                    if ($alt_text == '') {
                        $alt_text = trim(strip_tags(get_the_title()));
                    }
                    $img_src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail');
                ?>
                    <img src="<?php echo $img_src[0]; ?>" alt="<?php echo $alt_text; ?>" title="<?php echo $alt_text; ?>" loading="lazy">
                <?php endif; ?>
            </div>
            <div class="pl-2 post-content align-self-start col-7">
                <h3 class="my-2"><?php the_title(); ?></h3>
                <p class="excerpt d-none d-md-block">
                    <?php
                    $author_name =
                        get_field('photographer') ? get_field('photographer') : (get_field('author') ? get_field('author') : (get_field('Author') ? get_field('Author') : get_the_author_meta('first_name', $post->post_author) . ' ' . get_the_author_meta('last_name', $post->post_author)));
                    if ('snaps' == $post->post_type) :
                        echo 'Relive all the highlights (or check out what you missed) with our full photo gallery by ' . $author_name . '.';
                    else :
                        $metadesc = get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);
                        $excerpt = trim($metadesc) != '' ? $metadesc : string_limit_words(get_the_excerpt(), 25);
                        echo $excerpt;
                    endif;
                    ?>
                </p>
            </div>
        </a>
    </div>
</article> -->
<?php extract($args); ?>
<article class="my-3">
    <div class="mb-4 mx-0 mx-md-3">
        <a href="<?php the_permalink(); ?>" class="d-flex flex-row flex-md-column align-items-start">
            <?php if (isset($category)) : ?>
                <div class="mb-2 text-uppercase cat d-none d-md-block">
                    <?php echo $category; ?>
                </div>
            <?php endif; ?>
            <div class="post-thumbnail col-5 p-r">
                <?php
                if ('' !== get_the_post_thumbnail()) :
                    $alt_text = get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true);
                    if ($alt_text == '') {
                        $alt_text = trim(strip_tags(get_the_title()));
                    }
                    $img_src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail');
                ?>
                    <img src="<?php echo $img_src[0]; ?>" alt="<?php echo $alt_text; ?>" title="<?php echo $alt_text; ?>" loading="lazy">
                <?php endif; ?>
            </div>
            <div class="pl-2 post-content align-self-start col-7">
                <div class="mb-2 text-uppercase cat d-block d-md-none">
                    <?php echo $category; ?>
                </div>
                <h3 class="my-2"><?php the_title(); ?></h3>
                <p class="excerpt d-none d-md-block">
                    <?php
                    $author_name =
                        get_field('photographer') ? get_field('photographer') : (get_field('author') ? get_field('author') : (get_field('Author') ? get_field('Author') : get_the_author_meta('first_name', $post->post_author) . ' ' . get_the_author_meta('last_name', $post->post_author)));
                    if ('snaps' == $post->post_type) :
                        echo 'Relive all the highlights (or check out what you missed) with our full photo gallery by ' . $author_name . '.';
                    else :
                        $metadesc = get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);
                        $excerpt = trim($metadesc) != '' ? $metadesc : string_limit_words(get_the_excerpt(), 25);
                        echo $excerpt;
                    endif;
                    ?>
                </p>
            </div>
        </a>
    </div>
</article>