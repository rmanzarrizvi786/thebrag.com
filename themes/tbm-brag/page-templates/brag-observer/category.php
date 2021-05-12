<?php
$category = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}observer_categories WHERE slug = '{$category_slug}' LIMIT 1" );
if ( ! $category )
  return;
?>

<div class="row content mb-3 pt-2 pb-0 px-0" style="position: relative; overflow: hidden; background-image: url(<?php echo $featured_img_src[0]; ?>); background-size: cover; background-position: center; min-height: 350px;">
  <div class="mt-md-5 mt-3 mb-3 pt-md-3 col-12 col-md-8 offset-md-2 text-white" style="/*position: absolute; top: 50%; transform: translateY(-50%)*/ height: 100%;">
    <h1 class="text-center text-white"><?php echo $category->title; ?></h1>
    <div class="text-center"><?php echo wpautop( $category->description ); ?></div>
  </div>

  <div class="menu-network nav-network mx-auto mb-2">
    <ul class="nav d-flex align-items-center justify-content-center">
      <li class="nav-item"><a href="https://au.rollingstone.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/pubs-white/rolling-stone-australia.png" alt="Rolling Stone Australia" class="lazyload" style="width: 120px"></a></li><!-- Rolling Stone Australia -->

      <li class="nav-item"><a href="https://variety.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/pubs-white/variety.png" alt="Variety" class="lazyload"></a></li><!-- Variety -->

      <li class="nav-item"><a href="https://tonedeaf.thebrag.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/pubs-white/tone-deaf.png" alt="Tone Deaf" class="lazyload" style="width: 80px"></a></li><!-- Tone Deaf -->

      <li class="nav-item"><a href="https://dontboreus.thebrag.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/pubs-white/dbu.png" alt="Don't Bore Us" class="lazyload" style="width: 100px;"></a></li><!-- Don't Bore Us -->

      <li class="nav-item"><a href="https://thebrag.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/pubs-white/the-brag.png" alt="The Brag"></a></li>

      <li class="nav-item"><a href="https://theindustryobserver.thebrag.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/pubs-white/tio.png" alt="The Industry Observer" class="lazyload"></a></li><!-- The Industry Observer -->
    </ul>
  </div>
</div>
