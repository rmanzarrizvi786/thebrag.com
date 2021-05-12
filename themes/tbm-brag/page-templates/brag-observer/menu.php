<?php
$logo_link = isset( $logo_link ) ? $logo_link : site_url(); // ! isset( $logo_link ) ? site_url() : $logo_link;
$logo_alt = isset( $logo_alt ) ? $logo_alt : 'The Brag';
?>
<div id="mobile-menu" class="d-flex flex-column">
  <div style="position: absolute; top: 0; left: 0;">
  <button class="navbar-toggler" type="button" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="outline: none;">
      <div class="navbar-button-bars text-white"><i class="fa fa-remove"></i></div>
  </button>
</div>
    <div class="brand my-3">
        <a class="header-logo" href="<?php echo $logo_link; ?>">
            <img src="<?php echo $logo_url; ?>" alt="<?php echo $logo_alt; ?>" class="img-fluid" style="max-width: 150px;">
        </a>
    </div>
    <nav aria-label="Menu Social" class="mb-3">
        <ul class="nav flex-row align-items-center justify-content-center">
            <li class="nav-item"><a target="_blank" href="https://www.facebook.com/thebragmag" class="nav-link px-2 text-white" aria-label="Facebook"><i class="fa fa-facebook fa-lg" aria-hidden=true></i></a></li>
            <li class="nav-item"><a target="_blank" href="https://twitter.com/TheBrag" class="nav-link px-2 text-white" aria-label="Twitter"><i class="fa fa-twitter fa-lg" aria-hidden=true></i></a></li>
            <li class="nav-item"><a target="_blank" href="https://www.instagram.com/thebragmag/" class="nav-link px-2 text-white" aria-label="Instagram"><i class="fa fa-instagram fa-lg" aria-hidden=true></i></a></li>
            <li class="nav-item"><a target="_blank" href="https://www.youtube.com/channel/UCcZMmtU74qKN_w4Dd8ZkV6g" class="nav-link px-2 text-white" aria-label="YouTube"><i class="fa fa-youtube fa-lg" aria-hidden=true></i></a></li>
            <li class="nav-item"><a href="<?php echo home_url('/observer/'); ?>" class="nav-link px-2 text-white"><i class="fa fa-envelope fa-lg" aria-hidden=true></i></a></li>
        </ul>
    </nav>
    <form role="search" method="get" id="searchform-mobile" class="searchform form-inline justify-content-center my-1" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="">
        <div><input type="text" name="s" class="search-field form-control" placeholder="Search..." autocomplete="off" aria-label="Search"></div>
        <button type="submit" class="btn" aria-label="Search"><i class="fa fa-search" aria-hidden="true"></i></button>
    </form>
    <div id="menu" class="mt-3" style="width: 300px; margin: auto;">
      <h4 class="mt-2 text-white menu-genres-header pb-2" style="margin-left: .7rem;">What do you love?</h4>
        <?php
        wp_nav_menu( array(
            'theme_location' => 'top-what-you-love',
            'menu_id'        => 'menu_main-love',
            'menu_class' => 'nav flex-column',
            'fallback_cb'   => false,
            'add_li_class'  => 'nav-item',
            'link_class'   => 'nav-link'
            ) );
        ?>
    </div>
    <div id="menu-genres" class="my-3" style="width: 300px; margin: auto;">
      <h4 class="mt-2 text-white menu-genres-header pb-2" style="margin-left: .7rem;">Check this out</h4>
        <?php
        wp_nav_menu( array(
            'theme_location' => 'top-check-this-out',
            'menu_id'        => 'menu_main-checkout',
            'menu_class' => 'nav flex-column',
            'fallback_cb'   => false,
            'add_li_class'  => 'nav-item',
            'link_class'   => 'nav-link'
            ) );
        ?>
    </div>

    <div class="mb-3 pt-3" style="width: 300px; margin: auto; border-top: 1px solid #555;">
      <a href="<?php echo home_url( '/observer/' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/observer/mrec-600px.jpg"></a>
    </div>
</div>
