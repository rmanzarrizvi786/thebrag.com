<footer class="container footer mb-1 p-2 p-md-5">
    <div class="d-flex justify-content-between flex-column flex-md-row px-0 px-md-2 py-3 mt-3">
        <div class="left px-3">
            <a href="<?php echo site_url(); ?>"><img src="<?php echo get_template_directory_uri() . '/images/The-Brag_combo-white.svg'; ?>" width="200" height="36" alt="The Brag" title="The Brag"></a>
        </div>
        <div class="right px-3 text-right d-flex mt-3 mt-md-0 justify-content-end">
            <span class="mr-1 text-color-primary font-primary" style="font-size: .9rem; font-weight: 500">CULTURE BY</span>
            <span><img src="<?php echo get_template_directory_uri() . '/images/The-Brag-Media-150px-light.png'; ?>" width="130" height="13" alt="The Brag Media" title="The Brag Media"></span>
        </div>
    </div>
    <div class="footer-menu pt-5 border-top mt-5 px-2">
        <div class="d-flex flex-column flex-md-row align-items-start">
            <div class="left px-2 px-md-3">
                <nav>
                    <ul>
                        <li><a href="https://thebrag.media/" target="_blank" rel="noreferrer">Advertise</a></li>
                        <li><a target="_blank" rel="noopener" href="https://thebrag.media/submit-a-tip/">Submit Tip</a></li>
                        <li><a target="_blank" rel="noopener" href="https://thebrag.media/how-to-submit-an-op-ed-essay/">Submit Op-Ed</a></li>
                        <li><a target="_blank" rel="noopener" href="https://thebrag.media/submit/">Submit Video</a></li>
                    </ul>
                </nav>
            </div>
            <div class="right mt-3 mt-md-0">
                <?php get_template_part('template-parts/observer-list', null, ['show_container' => true, 'container_id' => 'observer-list-footer']); ?>
            </div>
        </div>
    </div>
</footer>
<div class="container footer-menu-2 pb-2">
    <nav>
        <ul class="d-flex flex-row justify-content-end">
            <li><a href="https://thebrag.media/terms-and-conditions/" target="_blank" rel="noreferrer">Competition Ts &amp; Cs</a></li>
            <li><a href="https://thebrag.com/media/terms-of-use/" target="_blank" rel="noreferrer">Terms of use</a></li>
            <li><a href="https://thebrag.media/privacy-policy/" target="_blank" rel="noreferrer">Privacy</a></li>
        </ul>
    </nav>
</div>