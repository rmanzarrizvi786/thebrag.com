<style>
    #campaign-posts { border-collapse: collapse; }
    #campaign-posts tr td { border-bottom: 3px solid #e5e5e5; margin: 0; padding: 5px; }
</style>
<?php
wp_enqueue_script( 'td-newsletter', plugin_dir_url( __FILE__ ) . '/js/newsletter.js', array( 'jquery' ), '2.2', true );
wp_enqueue_script( 'jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array ( 'jquery' ), 1.0, true);
wp_enqueue_style( 'jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css' );
?>
<form method="post" action="#" id="edm-settings" class="create-campaign">
    <table class="form-table">
    <tr>
        <td>
            <h3>Featured Video</h3>
            <label><input type="checkbox" name="edm_include_video_story" value="1"<?php echo get_option('edm_include_video_story') ? ' checked="checked"' : ''; ?>> Include Featured Video Story in EDMs</label>
            <p><input type="text" id="add-featured-video" size="30" placeholder="Search..."></p>
            <p>OR
            <a href="#" id="add-featured-video-blank">Add blank</a>
            </p>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div id="campaign-posts-wrap">
            <table id="campaign-featured-video">
                <?php if ( get_option('edm_featured_video_title') ) : ?>
                <tr id="featured_video_wrap">
                    <td>
                        <a href="<?php echo get_option('edm_featured_video_link'); ?>" target="_blank">
                            <img src="<?php echo get_option('edm_featured_video_image'); ?>" width="50">
                        </a>
                    <td>
                        <label>Link:<input type="text" name="edm_featured_video_link" value="<?php echo get_option('edm_featured_video_link'); ?>" class="link_remote"></label>
                        <div class="remote_content">
                            <label>Title:<input type="text" name="edm_featured_video_title" value="<?php echo htmlentities( get_option('edm_featured_video_title') ); ?>" class="title"></label><br>
                            <label>Image:<br><input type="text" name="edm_featured_video_image" value="<?php echo get_option('edm_featured_video_image'); ?>" class="image"></label>
                        </div>
                    </td>
                    <td><label class="remove remove-featured-video" data-id="featured_video_wrap">x</label></td>
                </tr>
                <?php endif; ?>
            </table>
            </div>
        </td>
    </tr>
    </table>
    <div class="submit">
        <span id="td-mc-errors" class="hide error" style="color: #ff0000; display: block; padding: 10px 0;"></span>
        <input type="button" name="submit" id="save-edm-settings" class="button button-primary" value="Save">
        <span class="status"></span>
    </div>
    
</form>