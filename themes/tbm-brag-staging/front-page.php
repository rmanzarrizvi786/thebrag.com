<?php
/**
 * Template name: homepage
 */
get_header();
?>

<div class="ad-billboard container py-2">
    <?php render_ad_tag('leaderboard'); 
    ?>
    <a href="https://thebrag.media?970x250" target="_blank"><img src="http://placehold.it/970x250/663366/fff"></a>
</div>

<?php
get_footer();
