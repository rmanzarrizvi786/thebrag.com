<div id="network" class="w-100 pt-1" style="display: none;">
    <div class="container bg-dark">
        <div class="btn btn-media-top">
            <span class="brag-media-top"><img src="<?php echo ICONS_URL; ?>TheBMedia_web.svg"></span>
            <span class="arrow-down rotate180"><img src="<?php echo ICONS_URL; ?>triangle-down-color.svg"></span>
        </div>
        <div class="d-flex flex-column" id="brands_wrap" style="padding: .5rem 1rem;">
            <div class="d-flex flex-row">
                <?php foreach (brands() as $brand => $brand_details) : ?>
                    <div class="brand-box-o col-2 d-flex">
                        <a href="<?php echo $brand_details['link']; ?>" title="<?php echo $brand_details['title']; ?>" target="_blank" class="d-block p-2">
                            <!-- <img src="https://images.thebrag.com/common/pubs/<?php echo $brand_details['logo_name']; ?>.jpg" alt="<?php echo $brand_details['title']; ?>"> -->
                            <img src="https://images.thebrag.com/common/brands/<?php echo $brand_details['logo_name']; ?>-light.<?php echo isset($brand_details['ext']) ? $brand_details['ext'] : 'jpg'; ?>" alt="<?php echo $brand_details['title']; ?>" style="<?php echo isset( $brand_details['width'] ) ? 'width: ' . $brand_details['width'] . 'px;' : ''; ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div><!-- .our-brands -->

            <div class="text-center py-3" style="color: #3b3b3b; font-size: 1.5rem;">Australian Network in Partnership With <a href="https://pmc.com/" target="_blank"><img src="https://images.thebrag.com/common/pubs-white/pmc.svg" alt="PMC" title="PMC" width="100" style="height: 1.1rem; margin-top: -5px; margin-left: 5px;"></a></div>

            <div class="d-flex flex-wrap justify-content-start bg-white other-brands">
                <?php foreach (brands_network() as $brand => $brand_details) : ?>
                    <div class="brand-box col-2 d-flex">
                        <a href="<?php echo $brand_details['link']; ?>" title="<?php echo $brand_details['title']; ?>" target="_blank" class="d-block p-2">
                            <img src="https://images.thebrag.com/common/pubs/<?php echo $brand_details['logo_name']; ?>.jpg" alt="<?php echo $brand_details['title']; ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div><!-- .network-brands -->
        </div>
    </div>
</div>