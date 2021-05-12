<?php
$image = $data['image'];
$lazy_load = $data['lazy_load'];
?>
<img <?php if ( $lazy_load ): ?>src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="<?php echo $image->url; ?>" class="lazy-load"<?php else: ?>src="<?php echo $image->url; ?>"<?php endif; ?> alt="" />
<?php
    if ( $image->description ):
?>
<div class="quiz-image-credit"><?php echo $image->description; ?></div>
<?php
    endif;
?>