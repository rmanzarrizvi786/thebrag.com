<?php $photos = new Attachments( 'snaps_attachments' ); ?>
<div class="photo-gallery-single">
    <div class="story single_story">
        <h2 id="story_title">
            <?php the_title(); ?>
            <small>
                (<?php echo count( $photos->get_attachments() ); ?>
                photos)
            </small>
        </h2>
    <div class="clear"></div>

<?php
$photo = isset( $_GET['photo'] ) ? $_GET['photo'] : 0;
if( $photos->exist() ) :
    $i = 1;
    while( $photo = $photos->get() ) :
        $img_src = wp_get_attachment_image_src( $photo->id, 'medium_large'); ?>
        <div><amp-img src="<?php echo $img_src[0]; ?>" width="<?php echo $img_src[1]; ?>" height="<?php echo $img_src[2]; ?>" layout="responsive"></amp-img><br></div>
        <?php if ( $i % 4 == 0 ) :  ?>
        <div style="text-align: center; margin: 10px auto;">
            <!-- <amp-ad width=300 height=250
                    type="doubleclick"
                data-slot="/71161633/SSM_thebrag/tb_article_hrec_2">
            </amp-ad> -->
            <amp-ad width=300 height=250
              type="doubleclick"
              data-slot="/5376056/thebrag_amp_content_1">
            </amp-ad>
        </div>
        <?php endif; ?>
<?php
    $i++;
    endwhile;
endif; ?>

<?php wp_reset_query(); ?>

<div class="clear"></div>
<p class="story-meta">
        <?php
            if ( (get_field('photographer')) ) {
                echo 'Photographed by ' . get_field('photographer', '');
            }
            if ( (get_field('gallery_date')) ) {
                echo ' on ' . date( 'M j, Y', strtotime( get_field('gallery_date', '') ) );
            }
        ?>
    </p>
</div>
</div>
