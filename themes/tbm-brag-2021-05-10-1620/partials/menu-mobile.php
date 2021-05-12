<?php
$logo_link = isset($logo_link) ? $logo_link : site_url(); // ! isset( $logo_link ) ? site_url() : $logo_link;
$logo_alt = isset($logo_alt) ? $logo_alt : 'The Brag';
?>
<div id="mobile-menu" class="d-flex flex-column">
    <div style="position: absolute; top: 0; left: 0;">
        <button class="navbar-toggler" type="button" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="outline: none;">
            <div class="navbar-button-bars text-white"><i class="fas fa-times"></i></div>
        </button>
    </div>
    <div class="brand my-3">
        <a class="header-logo" href="<?php echo $logo_link; ?>">
            <img src="<?php echo $logo_url; ?>" alt="<?php echo $logo_alt; ?>" class="img-fluid" style="max-width: 150px;">
        </a>
    </div>
    <nav aria-label="Menu Social" class="mb-3">
        <ul class="nav flex-row align-items-center justify-content-center">
            <li class="nav-item"><a target="_blank" href="https://www.facebook.com/thebragmag" class="nav-link px-2 text-white" aria-label="Facebook" rel="noopener"><i class="fab fa-facebook-f fa-lg" aria-hidden=true></i></a></li>
            <li class="nav-item"><a target="_blank" href="https://twitter.com/TheBrag" class="nav-link px-2 text-white" aria-label="Twitter" rel="noopener"><i class="fab fa-twitter fa-lg" aria-hidden=true></i></a></li>
            <li class="nav-item"><a target="_blank" href="https://www.instagram.com/thebragmag/" class="nav-link px-2 text-white" aria-label="Instagram" rel="noopener"><i class="fab fa-instagram fa-lg" aria-hidden=true></i></a></li>
            <li class="nav-item"><a target="_blank" href="https://www.youtube.com/channel/UCcZMmtU74qKN_w4Dd8ZkV6g" class="nav-link px-2 text-white" aria-label="YouTube" rel="noopener"><i class="fab fa-youtube fa-lg" aria-hidden=true></i></a></li>
            <li class="nav-item"><a target="_blank" href="<?php echo home_url('/observer/'); ?>" class="nav-link px-2 text-white l-ico-observer" aria-label="Subscribe" rel="noopener"><i class="fas fa-envelope fa-lg" aria-hidden=true></i></a></li>
        </ul>
    </nav>
    <form role="search" method="get" id="searchform-mobile" class="searchform form-inline justify-content-center my-1" action="<?php echo esc_url(home_url('/')); ?>" style="">
        <div><input type="text" name="s" class="search-field form-control" placeholder="Search..." autocomplete="off" aria-label="Search"></div>
        <button type="submit" class="btn" aria-label="Search"><i class="fa fa-search" aria-hidden="true"></i></button>
    </form>
    <div id="menu" class="mt-3" style="width: 300px; margin: auto;">
        <h4 class="mt-2 text-white menu-genres-header pb-2" style="margin-left: .7rem;">What do you love?</h4>
        <?php
        wp_nav_menu(array(
            'theme_location' => 'top-what-you-love',
            'menu_id'        => 'menu_main-love',
            'menu_class' => 'nav flex-column',
            'fallback_cb'   => false,
            'add_li_class'  => 'nav-item',
            'link_class'   => 'nav-link',
            'container' => 'nav',
        ));
        ?>
    </div>
    <div id="menu-genres" class="my-3" style="width: 300px; margin: auto;">
        <h4 class="mt-2 text-white menu-genres-header pb-2" style="margin-left: .7rem;">Check this out</h4>
        <?php
        wp_nav_menu(array(
            'theme_location' => 'top-check-this-out',
            'menu_id'        => 'menu_main-checkout',
            'menu_class' => 'nav flex-column',
            'fallback_cb'   => false,
            'add_li_class'  => 'nav-item',
            'link_class'   => 'nav-link',
            'container' => 'nav',
        ));
        ?>
    </div>

    <div class="mb-3 pt-3" style="width: 300px; margin: auto; border-top: 1px solid #555;">
        <p class="text-white">Never miss what you care about, become an Observer today.</p>
        <a href="https://thebrag.com/observer/" target="_blank" rel="noopener"><img data-src="https://thebrag.com/wp-content/themes/tbm-brag/images/observer/TBOLogowhite.svg" alt="The Brag Observer" class="lazyload img-fluid" width="200" style="max-width: 200px;"></a>
    </div>
</div>