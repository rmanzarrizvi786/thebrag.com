<h1>
    Custom URLs
    <?php echo isset( $term ) ? '<small>[Search term: "' . $term . '"]</small>' : ''; ?>
    <?php echo isset( $ch ) ? '<small>[Starts with: "' . $ch . '"]</small>' : ''; ?>
</h1>
<div>
    <a href="<?php echo $plugin_url; ?>&action=add" class="button button-primary" id="btn-add-artist-url" style="float: left;">Add</a>
    <form method="get" action="">
        <input type="hidden" name="page" value="ssm-artist-urls">
        <input type="hidden" name="action" value="search">
        <p class="search-box">
            <label class="screen-reader-text" for="post-search-input">Search:</label>
            <input type="search" id="post-search-input" name="term" value="<?php echo isset( $term ) ? $term : ''; ?>" style="float: left; height: 28px; margin: 0 4px 0 0;">
            <input type="submit" id="search-submit" class="button" value="Search">
        </p>
    </form>
</div>

<div class="clear"></div>

<div style="text-align: center; margin: 10px 0;">
    <a href="<?php echo $plugin_url; ?>" style="padding: 4px 6px; border: 1px solid #ddd; text-decoration: none;">Show All</a>
    <?php
    foreach ( array_merge( range( '0', '9' ), range('A', 'Z') ) as $character ) :
        $q = "SELECT artist_ID FROM " . $wpdb->prefix . "td_artist_urls WHERE artist_name LIKE '{$character}%' OR artist_slug LIKE '{$character}%' LIMIT 1";
        $r = $wpdb->get_var( $q );
        if ( !is_null( $r ) ) :
    ?>
    <a href="<?php echo $plugin_url; ?>&action=search_a&ch=<?php echo $character; ?>" style="padding: 4px 6px; border: 1px solid #ddd; text-decoration: none; background: #fff;"><?php echo $character; ?></a>
    <?php endif; endforeach; ?>
</div>

<table class="wp-list-table widefat striped posts" id="artist_url_list">
    <thead>
        <tr>
            <th>Image</th>
            <th>Artist Name</th>
            <th>URL Slug</th>
            <th>Intro Para</th>
            <th>Meta Desc</th>
            <th>Socials</th>
            <th>Actions</th>
        </tr>
    </thead>
<?php if ( count( $artists ) > 0 ) : foreach ( $artists as $artist ) : ?>
    <tr class="artist_<?php echo $artist->artist_ID; ?>">
        <td>
            <?php
            if ( isset( $artist->image_id ) && $artist->image_id > 0 ):
                $artist_img_src = wp_get_attachment_image_src( $artist->image_id, 'thumbnail' );
            ?>
            <img src="<?php echo $artist_img_src[0]; ?>" width="100" id="artist-header-src" style="display:block;">
            <?php endif; ?>
        </td>
        <td><?php echo $artist->artist_name; ?></td>
        <td>
            <a href="/<?php echo $artist->url_slug; ?>/<?php echo $artist->artist_slug; ?>" target="_blank">
                <?php echo $artist->url_slug; ?>/<?php echo $artist->artist_slug; ?>
            </a>
        </td>
        <td><?php echo wpautop($artist->intro_para); ?></td>
        <td><?php echo $artist->metadesc; ?></td>
        <td>
            <ul>
                <?php if ( $artist->facebook ) : ?>
                    <li><a href="<?php echo addhttp( $artist->facebook ); ?>" target="_blank">Facebook</a></li>
                <?php endif; ?>
                <?php if ( $artist->twitter ) : ?>
                    <li><a href="<?php echo addhttp( $artist->twitter ); ?>" target="_blank">Twitter</a></li>
                <?php endif; ?>
                <?php if ( $artist->instagram ) : ?>
                    <li><a href="<?php echo addhttp( $artist->instagram ); ?>" target="_blank">Instagram</a></li>
                <?php endif; ?>
            </ul>
        </td>
        <td>
            <a href="<?php echo $plugin_url; ?>&action=edit&id=<?php echo $artist->artist_ID; ?>" class="button button-primary" data-id="<?php echo $artist->artist_ID; ?>">Edit</a>
            <a href="<?php echo $plugin_url; ?>&action=delete_custom_url&id=<?php echo $artist->artist_ID; ?>" class="btn-delete button" data-id="<?php echo $artist->artist_ID; ?>">Delete</a>
        </td>
    </tr>
<?php endforeach; endif; ?>
</table>