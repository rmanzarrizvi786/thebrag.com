<article class="home-article <?php echo $count % 2 == 0 ? 'even' : 'odd'; ?>">
    <?php
    $photos = new Attachments( 'snaps_attachments' );
    if( $photos->exist() ) :
        if ( $photo = $photos->get_single( 0 ) ) :
    ?>
        <div class="post-thumbnail">
            <div>
                <a href="<?php the_permalink(); ?>">
                <img src="<?php echo $photos->src( 'medium_large', 0 ); ?>">
                </a>
            </div>
        </div>
    <?php 
        endif;
    endif;
    ?>

    <div class="post-content">
        <h2>
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
        <p>
        <?php
            if ( (get_field('photographer')) ) {
                echo 'Photographed by ' . get_field('photographer', ''); 
            }
            if ( (get_field('gallery_date')) ) { 
                echo ' on ' . date( 'M j, Y', strtotime( get_field('gallery_date', '') ) ); 
            }
        ?>
        </p>
        <p class="post-excerpt"><?php echo string_limit_words(get_the_excerpt(), 50); ?></p>
        <p class="post-time"><time datetime="<?php echo date( 'Y-m-d\TH:i:s+10:00', get_the_time( 'U' ) ); ?>"><?php the_time('M d, Y'); ?></time></p>
    </div>
    <div class="clear"></div>
</article>