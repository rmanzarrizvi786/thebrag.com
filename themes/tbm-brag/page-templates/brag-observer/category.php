<?php
$category = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}observer_categories WHERE slug = '{$category_slug}' LIMIT 1");
if (!$category)
  return;
?>

<div class="row content mb-3 pt-2 pb-0 px-0 bg-danger" style="position: relative; overflow: hidden;">
  <div class="my-3 col-12 col-md-8 offset-md-2 text-white" style="/*position: absolute; top: 50%; transform: translateY(-50%)*/ height: 100%;">
    <h1 class="text-center text-white"><?php echo $category->title; ?></h1>
    <div class="text-center"><?php echo wpautop($category->description); ?></div>
  </div>

  <div class="menu-network nav-network mx-auto mb-2">
    <ul class="nav d-flex align-items-center justify-content-center">
      <li class="nav-item"><a href="https://au.rollingstone.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/pubs-white/rolling-stone-australia.png" alt="Rolling Stone Australia" class="lazyload" style="width: 120px"></a></li><!-- Rolling Stone Australia -->

      <li class="nav-item"><a href="https://variety.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/pubs-white/variety.png" alt="Variety" class="lazyload"></a></li><!-- Variety -->

      <li class="nav-item"><a href="https://tonedeaf.thebrag.com/" target="_blank" class="nav-link"><img src="https://cdn.thebrag.com/td/Tone-Deaf-light.png" alt="Tone Deaf" class="lazyload" style="width: 60px"></a></li><!-- Tone Deaf -->

      <li class="nav-item"><a href="https://dontboreus.thebrag.com/" target="_blank" class="nav-link"><img src="https://images.thebrag.com/common/pubs-white/dbu.png" alt="Don't Bore Us" class="lazyload" style="width: 150px;"></a></li><!-- Don't Bore Us -->

      <li class="nav-item"><a href="https://thebrag.com/" target="_blank" class="nav-link"><img src="https://cdn.thebrag.com/tb/The-Brag-light.png" alt="The Brag" style="width: 120px;"></a></li>

      <li class="nav-item"><a href="https://theindustryobserver.thebrag.com/" target="_blank" class="nav-link"><img src="https://cdn.thebrag.com/tio/The-Industry-Observer-light.png" alt="The Industry Observer" class="lazyload" style="width: 100px;"></a></li><!-- The Industry Observer -->
    </ul>
  </div>
</div>