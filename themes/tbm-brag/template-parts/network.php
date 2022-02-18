<div class="bg-dark p-1 p-md-2">
    <div class="container">
        <div class="btn d-none d-md-block btn-media-top btn-toggle-slidedown" data-target="network">
            <span class="brag-media-top"><img src="https://cdn.thebrag.com/tbm/The-Brag-Media-300px-light.png" loading="lazy"></span>
            <span class="arrow-down"><img src="<?php echo ICONS_URL; ?>icon_arrow-down-td.svg" class="rotate180"></span>
        </div>
        <div class="d-flex flex-column" id="brands_wrap">
            <div class="d-flex flex-row flex-wrap justify-content-start">
                <?php foreach (brands() as $brand => $brand_details) : ?>
                    <div class="brand-box col-6 col-md-2 d-flex">
                        <a href="<?php echo $brand_details['link']; ?>" title="<?php echo $brand_details['title']; ?>" target="_blank" class="d-block p-2" rel="noreferrer">
                            <img src="https://images.thebrag.com/common/brands/<?php echo $brand_details['logo_name']; ?>-light.<?php echo isset($brand_details['ext']) ? $brand_details['ext'] : 'jpg'; ?>" alt="<?php echo $brand_details['title']; ?>" style="<?php echo isset($brand_details['width']) ? 'width: ' . $brand_details['width'] . 'px;' : ''; ?>" loading="lazy">
                        </a>
                    </div>
                <?php endforeach;
                $brands_network = brands_network();
                ksort($brands_network);
                foreach ($brands_network as $brand => $brand_details) : ?>
                    <div class="brand-box col-6 col-md-2 d-flex flex-wrap">
                        <a href="<?php echo $brand_details['link']; ?>" title="<?php echo $brand_details['title']; ?>" target="_blank" class="d-block p-2" rel="noreferrer">
                            <img src="https://images.thebrag.com/common/pubs-white/<?php echo str_replace(' ', '-', strtolower($brand_details['title'])); ?>.png" alt="<?php echo $brand_details['title']; ?>" style="<?php echo isset($brand_details['width']) ? 'width: ' . $brand_details['width'] . 'px;' : ''; ?>" loading="lazy">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>