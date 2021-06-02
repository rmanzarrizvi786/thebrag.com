<div class="bg-dark p-1 p-md-2">
    <div class="container">
        <div class="btn d-none d-md-block btn-media-top btn-toggle-slidedown" data-target="network">
            <span class="brag-media-top"><img src="https://cdn.thebrag.com/tbm/The-Brag-Media-300px-light.png" loading="lazy"></span>
            <span class="arrow-down"><img src="<?php echo ICONS_URL; ?>triangle-down-color.svg" class="rotate180"></span>
        </div>
        <div class="d-flex flex-column" id="brands_wrap">
            <div class="d-flex flex-row flex-wrap">
                <?php foreach (brands() as $brand => $brand_details) : ?>
                    <div class="brand-box-o col-4 col-md-2 d-flex">
                        <a href="<?php echo $brand_details['link']; ?>" title="<?php echo $brand_details['title']; ?>" target="_blank" class="d-block p-2" rel="noreferrer">
                            <img src="https://images.thebrag.com/common/brands/<?php echo $brand_details['logo_name']; ?>-light.<?php echo isset($brand_details['ext']) ? $brand_details['ext'] : 'jpg'; ?>" alt="<?php echo $brand_details['title']; ?>" style="<?php echo isset($brand_details['width']) ? 'width: ' . $brand_details['width'] . 'px;' : ''; ?>" loading="lazy">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div><!-- .our-brands -->

            <div class="text-center py-1 py-md-3" style="color: #3b3b3b; font-size: 1.5rem;">Australian Network in Partnership With <a href="https://pmc.com/" target="_blank" rel="noreferrer"><img src="https://images.thebrag.com/common/pubs-white/pmc.svg" alt="PMC" title="PMC" width="100" style="height: 1.1rem; margin-top: -5px; margin-left: 5px;"></a></div>

            <div class="d-flex flex-wrap justify-content-start bg-white other-brands">
                <?php foreach (brands_network() as $brand => $brand_details) : ?>
                    <div class="brand-box col-4 col-md-2 d-flex flex-wrap">
                        <a href="<?php echo $brand_details['link']; ?>" title="<?php echo $brand_details['title']; ?>" target="_blank" class="d-block p-2" rel="noreferrer">
                            <img src="https://images.thebrag.com/common/pubs/<?php echo $brand_details['logo_name']; ?>.jpg" alt="<?php echo $brand_details['title']; ?>" loading="lazy">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div><!-- .network-brands -->
        </div>
    </div>
</div>