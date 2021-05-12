<?php
    if(function_exists( 'wp_enqueue_media' )){
        wp_enqueue_media();
    }else{
        wp_enqueue_style('thickbox');
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
    }
?>
<style>
    .form-table tr td { padding: 5px; }
    .form-table tr td input[type=text], .form-table tr td textarea { padding: 5px; width: 100%; }
    .form-table tr td textarea { height: 100px; }
    .errors { color: #ff0000; }
</style>
<form method="post" action="<?php echo $plugin_url; ?>">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="form" value="ssm-artist-urls">
    <h1>
        <?php if ( isset( $artist ) ) : ?>
            Edit <?php echo $artist->artist_name; ?>
            <input type="hidden" name="artist_ID" value="<?php echo $artist->artist_ID; ?>">
        <?php else : ?>
            Add Artist
        <?php endif; ?>
    </h1>
    <?php
        if ( isset( $_SESSION['ssm_errors'] ) ) :
            echo '<ul class="errors">';
            foreach ( $_SESSION['ssm_errors'] as $error ) :
                echo '<li>' . $error . '</li>';
            endforeach;
            echo '</ul>';
        endif;
        
        if ( isset( $_SESSION['form_posts'] ) ) :
            $form_posts = $_SESSION['form_posts'];
        endif;
        
        unset( $_SESSION['ssm_errors'], $_SESSION['form_posts'] );
    ?>
    <table class="form-table">
        <tr>
            <td>
                <label>Title
                <input type="text" name="artist_name" value="<?php echo isset( $form_posts ) ? $form_posts['artist_name'] : (isset( $artist ) ? $artist->artist_name : ''); ?>">
                </label>
            </td>
            <td>
                URL Slug
                    <br>
                    <?php
                        $url_slugs = array_unique( array( 'comedian' ) );
                    ?>
                    <select name="url_slug">
                        <?php foreach ( $url_slugs as $url_slug ) : ?>
                        <option value="<?php echo $url_slug; ?>"
                                <?php echo isset( $form_posts ) && $form_posts['url_slug'] == $url_slug ? 'selected="selected"' : ( isset( $artist ) && $artist->url_slug == $url_slug  ? 'selected="selected"' : '' ); ?>
                                >
                            <?php echo $url_slug; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    /
                    <input type="text" name="artist_slug" value="<?php echo isset( $form_posts ) ? $form_posts['artist_slug'] : (isset( $artist ) ? $artist->artist_slug : ''); ?>" style="width: 45%;">
                
            </td>
        
            <td>
                <label>Header Image</label>
                <div id="artist-header-wrap">
                    <input type="hidden" name="image_id" id="image_id" value="<?php echo isset( $form_posts ) && isset( $form_posts['image_id']) ? $form_posts['image_id'] : (isset( $artist ) && isset( $artist->image_id ) ? $artist->image_id : ''); ?>">
                    <a id="btn-artist-header" class="button">Upload / Select from Library</a>
                    <?php
                    if ( isset( $form_posts ) && isset( $form_posts['image_id'] ) && $form_posts['image_id'] > 0 ) :
                        $artist_img_src = wp_get_attachment_image_src( $form_posts['image_id'], 'thumbnail' );
                    elseif ( isset( $artist ) && isset( $artist->image_id ) &&  $artist->image_id > 0 ):
                        $artist_img_src = wp_get_attachment_image_src( $artist->image_id, 'thumbnail' );
                    endif;
                    
                    if ( isset( $artist_img_src ) ) :
                    ?>
                    <img src="<?php echo $artist_img_src[0]; ?>" width="100" id="artist-header-src" style="display:block;">
                    <a href="#" id="remove-image">Remove</a>
                    <?php endif; ?>
                </div>
            </td>
            </tr>
        <tr>
            <td colspan="3">
                <label>Intro Paragraph
                    <textarea name="intro_para" id="intro_para"><?php echo isset( $form_posts ) ? $form_posts['intro_para'] : (isset( $artist ) ? $artist->intro_para : ''); ?></textarea>
                </label>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <label>Meta Desc
                    <textarea name="metadesc" id="metadesc"><?php echo isset( $form_posts ) ? $form_posts['metadesc'] : (isset( $artist ) ? $artist->metadesc : ''); ?></textarea>
                </label>
            </td>
        </tr>
    </table>
    
    <h4>Social Media Links</h4>
    
    <table class="form-table">
        <tr>
            <td>
                <label>Facebook
                    <input type="text" name="facebook" value="<?php echo isset( $form_posts ) ? $form_posts['facebook'] : (isset( $artist ) ? $artist->facebook : ''); ?>">
                </label>
            </td>
            <td>
                <label>Twitter
                    <input type="text" name="twitter" value="<?php echo isset( $form_posts ) ? $form_posts['twitter'] : (isset( $artist ) ? $artist->twitter : ''); ?>">
                </label>
            </td>
            <td>
                <label>Instagram
                    <input type="text" name="instagram" value="<?php echo isset( $form_posts ) ? $form_posts['instagram'] : (isset( $artist ) ? $artist->instagram : ''); ?>">
                </label>
            </td>
        </tr>
        <tr>
            <td><input type="submit" class="add-artist-url button button-primary" value="Save"></td>
        </tr>
    </table>
</form>