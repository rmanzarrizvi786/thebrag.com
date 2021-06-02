<?php get_header(); ?>

<div class="ad-billboard ad-billboard-top container py-1 py-md-2">
    <div class="mx-auto text-center">
        <?php render_ad_tag('leaderboard'); ?>
    </div>
</div>

<div class="container bg-yellow rounded-top p-5">
    <h1 class="text-center"><?php _e('Page Not Found', 'twentyfourteen'); ?></h1>
    <div class="text-center">
        <p><?php _e('It looks like nothing was found at this location.', 'twentyfourteen'); ?></p>
    </div>
    <!--Search Box-->
    <div class="search_wide">
        <form role="search" method="get" id="searchform" class="searchform" action="<?php echo site_url(); ?>">
            <label class="screen-reader-text" for="s">Looking For Something?&nbsp;&nbsp;</label>
            <div class="d-flex flex-column align-items-start">
                <input type="text" value="" name="s" id="s" class="form-control mt-1" placeholder="Search..." style="padding: .75rem;">
                <input type="submit" id="searchsubmit" value="Search" class="btn btn-dark mt-1">
            </div>
        </form>
    </div>
    <!--End Search Box-->
</div>

<?php
get_footer();
