<?php
/**
 * Header bar template part.
 *
 * @package AMP
 */

?>
<header id="top" class="amp-wp-header">
    <div class="amp-wp-header-inner">
        <?php if ( 'dad' != get_post_type() ) : ?>
            <a href="<?php echo esc_url( $this->get( 'home_url' ) ); ?>">
                <?php $site_icon_url = $this->get( 'site_icon_url' ); ?>
                <?php if ( $site_icon_url ) : ?>
                        <amp-img src="<?php echo esc_url( $site_icon_url ); ?>" width="32" height="32" class="amp-wp-site-icon"></amp-img>
                <?php endif; ?>
                <span class="amp-site-title">
                        <?php echo esc_html( wptexturize( $this->get( 'blog_name' ) ) ); ?>
                </span>
            </a>
        <?php else :
            $category_link = get_post_type_archive_link( 'dad' );
            ?>
            <a href="<?php echo esc_url( $category_link ); ?>">
                <span class="amp-site-title">Brag Dad</span>
            </a>
        <?php endif; ?>
	</div>
</header>
