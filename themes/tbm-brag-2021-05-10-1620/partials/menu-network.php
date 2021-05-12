<div style="text-align: center; background: #000; padding: 0; position: relative; z-index: 2;">
  <a href="#" class="l_toggle_menu_network">
    <img data-src="<?php echo get_template_directory_uri(); ?>/images/TheBragMedia_white.png" alt="The BRAG Media" class="lazyload img-fluid" style="height: 15px;">
    <i class="fa fa-caret-down" style="font-size: 12px"></i>
  </a>
</div>
<div class="brands__sub-menu is-open" id="menu-network" style="display: none;">
  <div class="d-flex flex-column" id="brands_wrap">
    <div class="brands__grid brands__wrap our-brands" style="min-height: 130px;">
      <?php foreach (brands() as $brand => $brand_details) : ?>
        <div class="brands-box">
          <a href="<?php echo $brand_details['link']; ?>" title="<?php echo $brand_details['title']; ?>" target="_blank">
            <img src="https://images.thebrag.com/common/pubs/<?php echo $brand_details['logo_name']; ?>.jpg" alt="<?php echo $brand_details['title']; ?>">
          </a>
        </div>
      <?php endforeach; ?>
    </div><!-- .our-brands -->

    <div class="text-center text-white py-3 my-0 text-network-pmc" style="border-bottom: 1px solid #2b2b2b; font-size: 1.5rem;">AUSTRALIAN NETWORK IN PARTNERSHIP WITH <a href="https://pmc.com/" target="_blank"><img src="https://images.thebrag.com/common/pubs-white/pmc.svg" alt="PMC" title="PMC" width="100" style="height: 1.1rem; margin-top: -5px; margin-left: 5px;"></a></div>

    <div class="brands__grid brands__wrap network-brands flex-fill">
      <?php foreach (brands_network() as $brand => $brand_details) : ?>
        <div class="brands-box">
          <a href="<?php echo $brand_details['link']; ?>" title="<?php echo $brand_details['title']; ?>" target="_blank">
            <img src="https://images.thebrag.com/common/pubs/<?php echo $brand_details['logo_name']; ?>.jpg" alt="<?php echo $brand_details['title']; ?>">
          </a>
        </div>
      <?php endforeach; ?>
    </div><!-- .network-brands -->
  </div>
</div>
<?php if (0) : ?>
  <div style="text-align: center; background: #000; padding: 0; position: relative; z-index: 2;">
    <a href="#" class="l_toggle_menu_network">
      <img data-src="<?php echo get_template_directory_uri(); ?>/images/TheBragMedia_white.png" alt="The BRAG Media" class="lazyload img-fluid" style="height: 12px;">
      <i class="fa fa-caret-down" style="font-size: 12px"></i>
    </a>
    <div class="menu-network nav-network" id="menu-network" style="display: none;">
      <div class="nav d-flex align-items-center justify-content-center">

        <div class="d-flex flex-fill">

          <div class="col text-center px-0"><a href="https://au.rollingstone.com/" target="_blank" class="nav-link" rel="noopener"><img data-src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/rollingstone-australia.png" alt="Rolling Stone Australia" class="lazyload" style="width: 110px"></a></div><!-- Rolling Stone Australia -->

          <div class="col text-center px-0"><a href="https://tonedeaf.thebrag.com/" target="_blank" class="nav-link" rel="noopener"><img data-src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/tone-deaf.png" alt="Tone Deaf" class="lazyload" style="width: 90px"></a></div><!-- Tone Deaf -->

          <div class="col text-center px-0"><a href="https://dontboreus.thebrag.com/" target="_blank" class="nav-link" rel="noopener"><img data-src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/dbu.png" alt="Don't Bore Us" class="lazyload" style="width: 90px;"></a></div><!-- Don't Bore Us -->

          <div class="col text-center px-0"><a href="https://thebrag.com/" target="_blank" class="nav-link" rel="noopener"><svg style="width: 70px; height: 25px; fill: #fff;">
                <use xlink:href="#svg-brag-logo"></use>
              </svg></a></div>
        </div>
        <div class="d-flex flex-fill">
          <div class="col text-center px-0"><a href="https://theindustryobserver.thebrag.com/" target="_blank" class="nav-link" rel="noopener"><img data-src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/tio.png" class="lazyload" alt="The Industry Observer" style="width: 60px;"></a></div><!-- The Industry Observer -->

          <div class="col text-center px-0"><a href="https://thebrag.com/observer/" target="_blank" class="nav-link" rel="noopener"><img data-src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/TBOLogowhite.svg" class="lazyload" alt="The Brag Observer" style="width: 55px;"></a></div><!-- The Brag Observer -->

          <div class="col text-center px-0"><a href="https://thebrag.com/jobs" target="_blank" class="nav-link" rel="noopener"><img data-src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/brag-jobs.png" class="lazyload" alt="The Brag Jobs" style="width: 45px;"></a></div>

          <div class="col text-center px-0"><a href="https://variety.com/" target="_blank" class="nav-link" rel="noopener"><img data-src="<?php echo get_template_directory_uri(); ?>/images/pubs-white/variety.png" alt="Variety" class="lazyload" style="width: 80px;"></a></div><!-- Variety -->
        </div>

      </div>
    </div>
  </div>
<?php endif; ?>