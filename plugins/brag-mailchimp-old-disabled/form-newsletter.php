<style>
    #campaign-posts { border-collapse: collapse; }
    #campaign-posts tr td { border-bottom: 3px solid #e5e5e5; margin: 0; padding: 5px; }
</style>
<?php
wp_enqueue_script( 'td-newsletter', plugin_dir_url( __FILE__ ) . '/js/newsletter.js', array( 'jquery' ), '2.2', true );
wp_enqueue_script( 'jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array ( 'jquery' ), 1.0, true);
wp_enqueue_style( 'jquery-ui', get_template_directory_uri() . '/css/jquery-ui.css' );
$blog_id = get_current_blog_id();
$subject_field = array(
    'The Brag',
    'The Brag Dad',
    "Don't Bore Us"
);
?>
<form method="post" action="#" class="create-campaign">
    <?php
    if ( isset($newsletter) ):
    ?>
    <input type="hidden" name="id" id="newsletter-id" value="<?php echo $newsletter->id; ?>">
    <h1>Edit "<?php echo $newsletter->details->subject; ?>"</h1>
    <?php
    else:
    ?>
    <h1>Create New Newsletter</h1>
    <?php
    endif;
    ?>
        <table class="form-table">
            <tr>
                <td colspan="2" align="right">
                    <a href="<?php echo plugin_dir_url( __FILE__ ); ?>template-guide-v2.1.png" class="button button-primary" target="_blank">Template Guide</a>
                </td>
            </tr>
            <tr>
                <td style="border-right: 1px solid #ddd;">
                    <table class="form-table">
                        <!-- MailChimp Campaign Details -->
                        <tr>
                            <th colspan="2">Campaign Details <small>(for MailChimp)</small></th>
                        </tr>
                        <tr>
                            <td style="width: 150px;">Date<br></td>
                            <td><input type="text" name="date_for" class="datepicker" readonly value="<?php echo isset($newsletter) && isset($newsletter->details->date_for) ? date('j F Y', strtotime($newsletter->details->date_for)) : date('j F Y'); ?>"></td>
                        </tr>
                        <tr>
                            <td>Title</td>
                            <td><input type="text" name="title" value="<?php echo isset($newsletter) && isset($newsletter->details->title) ? htmlentities( $newsletter->details->title ) : '#'; ?>"></td>
                        </tr>
                        <tr>
                            <td>Subject</td>
                            <td><input type="text" name="subject" value="<?php echo isset($newsletter) && isset($newsletter->details->subject) ? htmlentities( $newsletter->details->subject ) : $subject_field[ $blog_id - 1 ]; ?>"></td>
                        </tr>
                        <tr>
                            <td>Reply to <small>Email Address</small></td>
                            <td><input type="text" name="reply_to" value="<?php echo isset($newsletter) && isset($newsletter->details->reply_to) ? $newsletter->details->reply_to : 'noreply@seventhstreet.media'; ?>"></td>
                        </tr>
                        <tr>
                            <td>From Name</td>
                            <td><input type="text" name="from_name" value="<?php echo isset($newsletter) && isset($newsletter->details->from_name) ? $newsletter->details->from_name : $subject_field[ $blog_id - 1 ]; ?>"></td>
                        </tr>
                        <!-- MailChimp Campaign Details -->
                        
                        <tr><th colspan="2"><hr></th></tr>
                        
                        <tr>
                            <th colspan="2">Search the post and select to add to the list on the right.</th>
                        </tr>
                        
                        <!-- Add Post (Cover Story) (AJAX) -->
                        <tr>
                            <th>Cover Story</th>
                            <td>
                                <input type="text" id="add-cover-story" size="30" placeholder="Search...">
                                <a href="#" id="add-cover-story-blank">Add blank</a>
                            </td>
                        </tr>
                        <!-- Add Post (Cover Story) (AJAX) -->
                        
                        <tr><th colspan="2"><hr></th></tr>
                        
                        <?php for ( $i = 1; $i <= 2; $i++ ) : ?>
                        <!-- Add Post (Featured Story <?php echo $i; ?>) (AJAX) -->
                        <tr>
                            <th>Featured Story <?php echo $i; ?></th>
                            <td>
                                <input type="text" id="add-featured-story-<?php echo $i; ?>" size="30" placeholder="Search...">
                                <a href="#" id="add-featured-story-blank-<?php echo $i; ?>">Add blank</a>
                            </td>
                        </tr>
                        <!-- Add Post (Featured Story <?php echo $i; ?>) (AJAX) -->
                        <?php endfor; ?>
                        
                        <tr><th colspan="2"><hr></th></tr>
                        
                        <!-- Add Post (AJAX) -->
                        <tr>
                            <th>
                                Add Article
                                <small id="total-posts">
                                    <br>
                                    # of articles: <span class="total">0</span>
                                </small>
                            </th>
                            <td>
                                <input type="text" id="add-post" size="30" placeholder="Search...">
                                <a href="#" id="add-post-blank">Add blank</a>
                                <div class="error hide"></div>
                            </td>
                        </tr>
                        <!-- Add Post (AJAX) -->
                        
                        <tr><th colspan="2"><hr></th></tr>
                        
                        
                        
                        <!-- Middle Ad1 -->
                        <tr>
                            <th colspan="2">Ad 1 <small>(Middle)</small></th>
                        </tr>
                        <tr>
                            <td>Target Link</td>
                            <td><input type="text" name="ad_middle_1_link" value="<?php echo isset($newsletter) && isset($newsletter->details->ad_middle_1_link) ? $newsletter->details->ad_middle_1_link : ''; ?>"></td>
                        </tr>
                        <tr>
                            <td>Image URL<br><small>(width: 580px)</small></td>
                            <td><input type="text" name="ad_middle_1_image" value="<?php echo isset($newsletter) && isset($newsletter->details->ad_middle_1_image) ? $newsletter->details->ad_middle_1_image : ''; ?>"></td>
                        </tr>
                        <!-- Middle Ad1 -->
                        
                        <!-- Middle Ad2 -->
                        <tr>
                            <th colspan="2">Ad 2 <small>(Middle)</small></th>
                        </tr>
                        <tr>
                            <td>Target Link</td>
                            <td><input type="text" name="ad_middle_2_link" value="<?php echo isset($newsletter) && isset($newsletter->details->ad_middle_2_link) ? $newsletter->details->ad_middle_2_link : ''; ?>"></td>
                        </tr>
                        <tr>
                            <td>Image URL<br><small>(width: 580px)</small></td>
                            <td><input type="text" name="ad_middle_2_image" value="<?php echo isset($newsletter) && isset($newsletter->details->ad_middle_2_image) ? $newsletter->details->ad_middle_2_image : ''; ?>"></td>
                        </tr>
                        <!-- Middle Ad2 -->
                        
                        
                    </table>
                </td>
                
                <td width="50%">
                    <div id="campaign-posts-wrap">
                        <h3>Cover Story</h3>
                        <table id="campaign-cover-story">
                        <?php if ( isset( $newsletter->details->cover_story_title ) ) : ?>
                            <tr id="cover_story_wrap">
                                <td>
                                    <a href="<?php echo $newsletter->details->cover_story_link; ?>" target="_blank">
                                        <img src="<?php echo $newsletter->details->cover_story_image; ?>" width="50">
                                    </a>
                                <td>
                                    <label>Link:<input type="text" name="cover_story_link" value="<?php echo $newsletter->details->cover_story_link; ?>" class="link_remote"></label>
                                    <div class="remote_content">
                                        <label>Title:<input type="text" name="cover_story_title" value="<?php echo htmlentities( $newsletter->details->cover_story_title ); ?>" class="title"></label><br>
                                        <label>Blurb:<br><textarea name="cover_story_excerpt" class="excerpt"><?php echo htmlentities( $newsletter->details->cover_story_excerpt ); ?></textarea></label><br>
                                        <label>Image:<br><input type="text" name="cover_story_image" value="<?php echo $newsletter->details->cover_story_image; ?>" class="image"></label>
                                    </div>
                                </td>
                                <td><label class="remove remove-cover-story" data-id="cover_story_wrap">x</label></td>
                            </tr>
                        <?php endif; ?>
                        </table>
                        <hr>
                        
                        <h3>Featured Story 1</h3>
                        <table id="campaign-featured-story-1">
                            <?php if ( isset( $newsletter->details->featured_story_title_1 ) ) : ?>
                            <tr id="featured_story_wrap_1">
                                <td>
                                    <a href="<?php echo $newsletter->details->featured_story_link_1; ?>" target="_blank">
                                        <img src="<?php echo $newsletter->details->featured_story_image_1; ?>" width="50">
                                    </a>
                                <td>
                                    <label>Link:<input type="text" name="featured_story_link_1" value="<?php echo $newsletter->details->featured_story_link_1; ?>" class="link_remote"></label>
                                    <div class="remote_content">
                                        <label>Title:<input type="text" name="featured_story_title_1" value="<?php echo htmlentities( $newsletter->details->featured_story_title_1 ); ?>" class="title"></label><br>
                                        <label>Blurb:<br><textarea name="featured_story_excerpt_1" class="excerpt"><?php echo htmlentities( $newsletter->details->featured_story_excerpt_1 ); ?></textarea></label><br>
                                        <label>Image:<br><input type="text" name="featured_story_image_1" value="<?php echo $newsletter->details->featured_story_image_1; ?>" class="image"></label>
                                    </div>
                                </td>
                                <td><label class="remove remove-featured-story-1" data-id="featured_story_wrap_1">x</label></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                        
                        <h3>Featured Story 2</h3>
                        <table id="campaign-featured-story-2">
                            <?php if ( isset( $newsletter->details->featured_story_title_2 ) ) : ?>
                            <tr id="featured_story_wrap_2">
                                <td>
                                    <a href="<?php echo $newsletter->details->featured_story_link_2; ?>" target="_blank">
                                        <img src="<?php echo $newsletter->details->featured_story_image_2; ?>" width="50">
                                    </a>
                                <td>
                                    <label>Link:<input type="text" name="featured_story_link_2" value="<?php echo $newsletter->details->featured_story_link_2; ?>" class="link_remote"></label>
                                    <div class="remote_content">
                                        <label>Title:<input type="text" name="featured_story_title_2" value="<?php echo htmlentities( $newsletter->details->featured_story_title_2 ); ?>" class="title"></label><br>
                                        <label>Blurb:<br><textarea name="featured_story_excerpt_2" class="excerpt"><?php echo htmlentities( $newsletter->details->featured_story_excerpt_2 ); ?></textarea></label><br>
                                        <label>Image:<br><input type="text" name="featured_story_image_2" value="<?php echo $newsletter->details->featured_story_image_2; ?>" class="image"></label>
                                    </div>
                                </td>
                                <td><label class="remove remove-featured-story-2" data-id="featured_story_wrap_2">x</label></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                        
                        <hr>
                        
                        <h3 style="float: left; margin: 0;">Articles</h3>
                        <!--<span class="remove-all-posts" data-id="campaign-posts">Remove all</span>-->
                        <br class="clear">
                        <table id="campaign-posts">
                            <tr>
                                <th>Order</th>
                                <th colspan="2">Post</th>
                            </tr>
                            <?php
                            if ( isset( $newsletter ) && isset( $newsletter->details->posts ) ):
                            $i = 1;
                            foreach ( $newsletter->details->posts as $post_id => $order):
                            ?>
                            <tr class="campaign-post" id="campaign-post-<?php echo $post_id; ?>">
                                <td>
                                    <input type="number" maxlength="2" min="1" class="campaign-posts" data-id="<?php echo $post_id; ?>" name="posts[<?php echo $post_id; ?>]" value="<?php echo $i; ?>" size="2">
                                    <?php if ( $newsletter->details->post_images->{$post_id} != '' ) : ?>
                                    <a href="<?php echo $newsletter->details->post_links->{$post_id}; ?>" target="_blank">
                                        <img src="<?php echo $newsletter->details->post_images->{$post_id}; ?>" width="50">
                                    </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <label>Link:<input type="text" name="post_links[<?php echo $post_id; ?>]" value="<?php echo $newsletter->details->post_links->{$post_id}; ?>" class="link_remote"></label>
                                    <div class="remote_content">
                                        <label>Title:<input type="text" name="post_titles[<?php echo $post_id; ?>]" value="<?php echo htmlentities( $newsletter->details->post_titles->{$post_id} ); ?>" class="title"></label><br>
                                        <label>Blurb:<textarea name="post_excerpts[<?php echo $post_id; ?>]" class="excerpt"><?php echo htmlentities( $newsletter->details->post_excerpts->{$post_id} ); ?></textarea></label><br>
                                        <label>Image:<input type="text" name="post_images[<?php echo $post_id; ?>]" value="<?php echo $newsletter->details->post_images->{$post_id}; ?>" class="image"></label>
                                    </div>
                                </td>
                                <td><label class="remove remove-campaign-post" data-id="<?php echo $post_id; ?>">x</label></td>
                            </tr>
                            <?php
                            $i++;
                            endforeach;
                            endif;
                            ?>
                        </table>
                        
                        <hr>
                    </div>
                </td>
            </tr>
        </table>
            <?php // submit_button(); ?>
        
    <div>
        <div class="submit">
            <span id="td-mc-errors" class="hide error" style="color: #ff0000; display: block; padding: 10px 0;"></span>
            <input type="button" name="submit" id="submit-campaign" class="button button-primary" value="Save">
            <span class="status"></span>
        </div>
        
    </div>
</form>