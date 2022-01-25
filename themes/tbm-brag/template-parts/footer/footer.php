<footer class="container footer mb-1 py-2">
    <div class="d-flex justify-content-between flex-column flex-md-row py-3 mt-3">
        <div class="left px-3">
            <a href="<?php echo site_url(); ?>"><img src="<?php echo CDN_URL; ?>The-Brag_combo-light.svg" width="200" height="36" alt="The Brag" title="The Brag" loading="lazy"></a>
        </div>
        <div class="right px-3 text-right d-flex mt-3 mt-md-0 justify-content-end">
            <span class="mr-1 text-color-primary font-primary" style="font-size: .9rem; font-weight: 500">CULTURE BY</span>
            <span><img src="https://cdn.thebrag.com/tbm/The-Brag-Media-300px-light.png" width="130" height="13" alt="The Brag Media" title="The Brag Media" loading="lazy"></span>
        </div>
    </div>
    <div class="footer-menu pt-5 border-top mt-5">
        <div class="d-flex flex-column flex-md-row align-items-start">
            <div class="left p-2 pr-md-3 w-100">
                <nav>
                    <ul class="d-flex d-md-block p-0">
                        <li class="col-6 col-md-12"><a href="https://thebrag.media/" target="_blank" rel="noreferrer">Advertise</a></li>
                        <li class="col-6 col-md-12"><a target="_blank" rel="noopener" href="https://thebrag.media/submit-a-tip/">Submit Tip</a></li>
                        <li><a target="_blank" rel="noopener" href="https://thebrag.media/how-to-submit-an-op-ed-essay/">Submit Op-Ed</a></li>
                        <li><a target="_blank" rel="noopener" href="https://thebrag.media/submit/">Submit Video</a></li>
                    </ul>
                </nav>
            </div>
            <div class="right">
                <?php get_template_part('template-parts/observer-list', null, ['show_container' => true, 'container_id' => 'observer-list-footer']); ?>
            </div>
        </div>
    </div>
</footer>
<div class="container footer-menu-2 pb-2" style="padding-bottom: 50px !important;">
    <nav class="bg-white rounded mx-2">
        <ul class="d-flex flex-column flex-md-row justify-content-center p-0">
            <li><a href="https://thebrag.media/terms-and-conditions/" target="_blank" rel="noreferrer" class="py-1">Competition Ts &amp; Cs</a></li>
            <li><a href="https://thebrag.com/media/editorial-code/" target="_blank" rel="noreferrer" class="py-1">Editorial code</a></li>
            <li><a href="https://thebrag.com/media/terms-of-use/" target="_blank" rel="noreferrer" class="py-1">Terms of use</a></li>
            <li><a href="https://thebrag.media/privacy-policy/" target="_blank" rel="noreferrer" class="py-1">Privacy</a></li>
        </ul>
    </nav>
</div>