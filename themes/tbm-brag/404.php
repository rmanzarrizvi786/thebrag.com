<?php get_header(); ?>
<style>
.container_404 {
    text-align: center;
    margin: 0;
    padding: 100px 0px;
}
</style>
        <div class="container_404">
            <header class="page-header">
                <h1 class="page-title"><?php _e( 'Page Not Found', 'twentyfourteen' ); ?></h1>
            </header>
            <div class="page-content">
                <p><?php _e( 'It looks like nothing was found at this location.', 'twentyfourteen' ); ?></p>
            </div>
            <!--Search Box-->
            <div class="search_wide" style="width: 300px; margin: auto;">
            <form role="search" method="get" id="searchform" class="searchform" action="<?php echo site_url(); ?>">
                    <div>
                    <label class="screen-reader-text" for="s">Looking For Something?&nbsp;&nbsp;</label>
                    <input type="text" value="" name="s" id="s">
                    <input type="submit" id="searchsubmit" value="Search">
                    </div>
            </form>
            </div>
            <!--End Search Box-->
        </div>
        
<?php
get_footer();