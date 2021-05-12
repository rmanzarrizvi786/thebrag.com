<?php
/*
   Plugin Name: SSM Compress Images
   Plugin URI: 
   description:
   Version: 1.0
   Author: Sachin Patel
   Author URI:
*/


add_action( 'admin_menu', 'ssm_compress_images_index' );
function ssm_compress_images_index() {
    add_media_page( 'Compression Results', 'Compression Results', 'manage_options', 'ssm-compress-images', 'ssm_compress_images_show_func'); //, 'dashicons-images-alt2', 16  );
//    add_media_page( 'Compression Results 2', 'Compression Results 2', 'manage_options', 'ssm-compress-images2', 'ssm_compress_images_show_func2'); //, 'dashicons-images-alt2', 16  );
//    add_menu_page( 'Compress Images', 'Compress Images (Beta)', 'manage_options', 'ssm-compress-images', 'ssm_compress_images_index_func', 'dashicons-images-alt2', 6  );
//    add_submenu_page( 'ssm-compress-images', 'Show Results', 'Show Results', 'manage_options', 'compress-images-show', 'ssm_compress_images_show_func' );
}

function ssm_compress_images_index_func() {
//    wp_enqueue_script( 'compress-images-script', plugins_url( '/js/compress-images.js', __FILE__ ), array('jquery') );
    echo '<h1>Compress Images</h1>';
    echo '<a href="#" id="start_ssm_compress_images" class="button">Start</a>';
    echo '<div><h2 id="ssm_compress_images_processing">Progress will be shown here.</h2></div>';
    echo '<table id="ssm_compress_images_progress" class="wp-list-table widefat striped"></table>';
}

add_action( 'admin_enqueue_scripts', 'ssm_enqueue_compress_images' );
function ssm_enqueue_compress_images($hook) {
    if ( 'toplevel_page_ssm-compress-images' != $hook )
        return;
    // Add script to the page
    wp_enqueue_script( 'compress-images-script', plugins_url( '/js/compress-images.js', __FILE__ ), array('jquery') );
    // in JavaScript, object properties are accessed as ssm_compress_images_object.ajax_url, ssm_compress_images_object.we_value
    wp_localize_script( 'compress-images-script', 'ssm_compress_images_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'last_attachment_id' => NULL, 'last_image_id' => NULL ) );
}

add_action( 'wp_ajax_ssm_compress_images_get_next_image', 'ssm_compress_images_get_next_image' );
function ssm_compress_images_get_next_image() {
    global $wpdb;
    $query = "SELECT * FROM {$wpdb->prefix}ssm_compressed_images WHERE status = 'Pending' LIMIT 1";
    $image = $wpdb->get_row( $query );
    
    $return = array();
    if ( $image ) {
        $return['next_image_id'] = $image->id;
        $return['next_image_filepath'] = $image->old_image_path;
    } else {
        if ( ssm_compress_images_get_next_attachment() ) {
            ssm_compress_images_get_next_image();
        }
    }
    echo json_encode( $return );
    wp_die();
}

add_action( 'wp_ajax_ssm_compress_images_get_next_attachment', 'ssm_compress_images_get_next_attachment' );
function ssm_compress_images_get_next_attachment() {
    global $wpdb;
    $query = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' ";
    $last_compressed_attachment = $wpdb->get_row( "SELECT attachment_id FROM {$wpdb->prefix}ssm_compressed_images ORDER BY id DESC LIMIT 1" );
    if ( $last_compressed_attachment ) {
        $query .= " AND ID < '{$last_compressed_attachment->attachment_id}' ";
    }
    $query .= " ORDER BY ID DESC LIMIT 1";
    $attachment = $wpdb->get_row( $query );
    
    if ( $attachment ) {
        $files = array();
        
        $fileurl = wp_get_attachment_url( $attachment->ID ); // Get File URL
        $filetype = wp_check_filetype( $fileurl ); // Get File Type
        
        $filepath = get_attached_file( $attachment->ID ); // Get File Path
        
        array_push( $files, array(
            'path' => $filepath,
            'attachment_id' => $attachment->ID,
            'size' => 'full',
            'old_image_url' => $fileurl,
        ));
        
        // Get all the available thumbnail sizes
        $sizes = get_intermediate_image_sizes();
        foreach ( $sizes as $size ) {
            $info_thumbnail = image_get_intermediate_size( $attachment->ID, $size );
            $filepath_thumbnail = realpath( str_replace( wp_basename( $filepath ), $info_thumbnail['file'], $filepath ) );
            array_push( $files, array(
                'path' => $filepath_thumbnail,
                'attachment_id' => $attachment->ID,
                'size' => $size,
                'old_image_url' => $info_thumbnail['url'],
            ));
        }

        foreach( $files as $file ) {
            $filepath = $file['path'];
            if ( is_file( $filepath ) ) {
                $original_filesize = filesize( $filepath );
                $wpdb->insert( $wpdb->prefix . 'ssm_compressed_images',
                    array(
                        'attachment_id' => $file['attachment_id'],
                        'size' => $file['size'],
                        'old_image_path' => $filepath,
                        'new_image_path' => '',
                        'old_image_url' => $file['old_image_url'],
                        'new_image_url' => '',
                        'original_size' => $original_filesize,
                        'compressed_size' => $original_filesize,
                        'status' => 'Pending',
                        'created_at' => current_time('mysql', 1),
                        'compressed_at' => current_time('mysql', 1)
                    )
                );
            }
        }
        return true;
    } else {
        return false;
    }
}

add_action( 'wp_ajax_ssm_compress_image', 'ssm_compress_image' );
function ssm_compress_image() {
    // before saving post
    remove_filter('content_save_pre', 'wp_filter_post_kses');
    remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
    
    ini_set('max_execution_time', 900);
    global $wpdb;
    $next_image_id = ( $_POST['next_image_id'] ) && $_POST['next_image_id'] > 0 ? $_POST['next_image_id'] : NULL;
    $query = "SELECT * FROM {$wpdb->prefix}ssm_compressed_images WHERE id = {$next_image_id} LIMIT 1";
    $image = $wpdb->get_row( $query );
    $return = array();
    if ( $image ) {
        $attachment_id = $image->attachment_id;
        
        $attachment = get_post( $attachment_id );
        
        $fileurl = $image->old_image_url; // Get File URL
        $filetype = wp_check_filetype( $fileurl ); // Get File Type
        
        $filepath = $image->old_image_path; // Get File Path
        $filename = basename( $filepath ); // Get File Name
        
        $ext = $filetype['ext'];
        
        $size_limit = 1000;
        $compress_quality = 90;
        
        $return['filetype'] = $filetype['type'];
        $return['ext'] = $ext;
        
        $return['original_filesize'] = $return['compressed_filesize'] = $return['change'] = '';
        
        if ( ! in_array( $filetype['type'], array( 'image/png', 'image/jpeg' ) ) ) { // Check if file type is valid
            $return['result'] = '<span style="color: #ff0000;">Skipped ' . $filepath . '</span>';
            $wpdb->udpate( $wpdb->prefix . 'ssm_compressed_images', 
                array( 'status' => 'Skipped' ),
                array( 'id' => $image->id )
            );
        } else {
            require_once( plugin_dir_path( __FILE__ ) . '/php-image-resize/lib/ImageResize.php' );
            
            $i = 0;
            if ( is_file( $filepath ) ) {
                $i++;
                $fileinfo = pathinfo( $filename );
                $compress_filename = $fileinfo['filename'] . '.jpg';
                
                $compress_fileurl = str_replace( $filename, $compress_filename, $fileurl );
                
                $img_size = getimagesize( $filepath ); // Get Images Sizes
                $resize_filepath = $filepath;
                if ( $img_size[0] > $size_limit || $img_size[1] > $size_limit ) { // If Image width or height is higher than the limit
                    $image = new \Gumlet\ImageResize( $filepath ); // ImageResize instance
                    $image->resizeToBestFit( $size_limit, $size_limit ); // Resize the image
                    $image->save( $resize_filepath, null, 100 ); // Save the resized image
                }

                // Compress and save new file
                $compress_filepath = str_replace( '.' . $ext, '_C.jpg', $filepath );
                if ( '127.0.0.1' === $_SERVER['REMOTE_ADDR'] ) {
                    shell_exec('/usr/local/bin/guetzli --quality ' . $compress_quality . ' ' . $resize_filepath . ' ' . $compress_filepath);
                } else {
                    shell_exec('/usr/bin/guetzli --quality ' . $compress_quality . ' ' . $resize_filepath . ' ' . $compress_filepath);
                }
                
                if ( is_file( $compress_filepath ) ) {
                    // Get Image File Size to store in DB
                    $original_filesize = filesize( $filepath );
                    $compressed_filesize = filesize( $compress_filepath );
                    
                    $compressed_image_size = getimagesize( $compress_filepath );
                    
                    $compressed_mime_type = mime_content_type( $compress_filepath );

                    rename( $compress_filepath, str_replace( '_C.jpg', '.jpg', $compress_filepath ) );

                    // Change filename in affected posts
                    $affected_posts_ids = array();
                    $affected_posts = $wpdb->get_results( "SELECT ID FROM {$wpdb->prefix}posts WHERE post_content LIKE '%{$fileurl}%' AND post_status = 'publish' " );
                    if ( $affected_posts ) {
                        foreach ( $affected_posts as $affected_post ) {
                            array_push( $affected_posts_ids, $affected_post->ID );
                            $update_posts_query = "UPDATE {$wpdb->prefix}posts
                                    SET post_content = REPLACE( post_content, '{$fileurl}', '{$compress_fileurl}' )
                                WHERE ID = {$affected_post->ID } AND post_content LIKE '%{$fileurl}%' ";
                            $wpdb->query( $update_posts_query );
                            
                            // Update Post (containg the attachment image) to set Current Date as Modified Date
                            wp_update_post(
                                array(
                                    'ID' => $affected_post->ID,
                                    'post_modified' => current_time('mysql', 1),
                                )
                            );
                        }
                    }

                    // Update Attachment Metadata
                    $metadata = wp_get_attachment_metadata( $attachment_id );
                    if ( 'full' == $image->size ) {
                        $metadata['width'] = $compressed_image_size[0];
                        $metadata['height'] = $compressed_image_size[1];
                        $metadata['file'] = str_replace( $filename, $compress_filename, $metadata['file'] );

                        // Update attachment meta
                        update_attached_file( $attachment_id, str_replace( '_C.jpg', '.jpg', $compress_filepath ) );

                        // Update guid in posts table
                        $wpdb->query(
                            "UPDATE {$wpdb->prefix}posts
                                SET guid = REPLACE( guid, '{$fileurl}', '{$compress_fileurl}' )
                                WHERE ID = {$attachment_id}
                                LIMIT 1 "
                        );
                                
                        // Update Post (Attachment) to set Current Date as Modified Date
                        wp_update_post(
                            array(
                                'ID' => $attachment_id,
                                'post_modified' => current_time('mysql', 1),
                            )
                        );
                        
                        // Update Post (having the attachment image as featured) to set Current Date as Modified Date
                        wp_update_post(
                            array(
                                'ID' => $attachment->post_parent,
                                'post_modified' => current_time('mysql', 1),
                            )
                        );
                    } else {
                        $metadata['sizes'][$image->size] = array(
                            'file' => str_replace( $filename, $compress_filename, $metadata['sizes'][$image->size]['file'] ),
                            'width' => $compressed_image_size[0],
                            'height' => $compressed_image_size[1],
                            'mime-type' => $compressed_mime_type,
                        );
                    }

                    // Update database for processed (compressed) images, so it doesn't get processed in next run
                    $wpdb->update( $wpdb->prefix . 'ssm_compressed_images',
                        array(
                            'new_image_path' => str_replace( '_C.jpg', '.jpg', $compress_filepath ),
                            'new_image_url' => $compress_fileurl,
                            'compressed_size' => $compressed_filesize,
                            'compressed_at' => current_time('mysql', 1),
                            'status' => 'Compressed'
                        ),
                        array ( 'id' => $image->id )
                    );
                    $return['result'] = '<span style="color: #00cc00;">Compressed ' . $filepath . '</span>';
                    $return['original_filesize'] = formatBytes( $original_filesize );
                    $return['compressed_filesize'] = formatBytes( $compressed_filesize );

                    $reduction = round( ( ( $compressed_filesize - $original_filesize ) / $original_filesize ) * 100, 2 );
                    
                    $return['change'] .= $reduction . '%';
                
                    // Delete old file is the extension was not jpg
                    if ( 'jpg' != $ext ) {
                        unlink( $resize_filepath );
                    }
                } else { // If Compressed file is NOT saved
                    $return['result'] .= '<span style="color: #ff0000;">Unable to save compressed file ' . $filepath . '</span>';
                    $wpdb->update( $wpdb->prefix . 'ssm_compressed_images',
                        array(
                            'status' => 'Failed'
                        ),
                        array ( 'id' => $image->id )
                    );
                }
            } else { // If File is found at filepath
                $wpdb->insert( $wpdb->prefix . 'ssm_missing_images',
                    array(
                        'attachment_id' => $attachment_id,
                        'file_path' => $filepath,
                        'created_at' => current_time('mysql', 1),
                    ));
            }
            
            // Update attachment and thumbnails postmeta
            if ( isset ( $metadata ) ) {
                wp_update_attachment_metadata( $attachment_id, $metadata );
            }
        }
    } else {
        $return['result'] = 'done';
    }
    
    // after saving post
    add_filter('content_save_pre', 'wp_filter_post_kses');
    add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
    
    echo json_encode( $return );
    
//    echo $attachment->ID;
    wp_die();
}

function ssm_compress_images_show_func() {
    global $wpdb;
    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
    
    $limit = 20;
    $offset = ( $pagenum - 1 ) * $limit;
    $total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$wpdb->prefix}ssm_compressed_images" );
    $num_of_pages = ceil( $total / $limit );
    $images = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ssm_compressed_images ORDER BY id DESC LIMIT {$offset}, {$limit}" );
    
    $image_width = 100;
    
    $page_links = paginate_links( array(
        'base' => add_query_arg( 'pagenum', '%#%' ),
        'format' => '',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'total' => $num_of_pages,
        'current' => $pagenum,
    ) );
    
    if ( $page_links ) {
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">Total: ' . $total . ' items ' . $page_links . '</div></div>';
    }
    
    echo '<h1>Compression Results</h1>';
    
    $compression = $wpdb->get_row( "SELECT SUM(original_size) AS total_original_size, SUM(compressed_size) AS total_compressed_size FROM {$wpdb->prefix}ssm_compressed_images WHERE status = 'Compressed' " );
    $overall_change = round( ( ( $compression->total_compressed_size - $compression->total_original_size ) / $compression->total_original_size ) * 100, 2 );
    
    $compressed_attachments = $wpdb->get_row( "SELECT COUNT(*) AS total FROM (SELECT DISTINCT attachment_id FROM {$wpdb->prefix}ssm_compressed_images WHERE status = 'Compressed') src" );
    $skipped_attachments = $wpdb->get_row( "SELECT COUNT(*) AS total FROM (SELECT DISTINCT attachment_id FROM {$wpdb->prefix}ssm_compressed_images WHERE status = 'Skipped') src" );
    $failed_attachments = $wpdb->get_row( "SELECT COUNT(*) AS total FROM (SELECT DISTINCT attachment_id FROM {$wpdb->prefix}ssm_compressed_images WHERE status = 'Failed') src" );
    $total_attachments = $wpdb->get_row( "SELECT COUNT(*) AS total FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' " );
    
    echo '<h4>Original size: ' . formatBytes( $compression->total_original_size ) .
    ' | Compressd size: ' . formatBytes( $compression->total_compressed_size ) .
    ' | Change: ' . $overall_change . '%' .
//    ' | Processed: ' . $processed_attachments->total . ' out of ' . $total_attachments->total . ' (' . round( $processed_attachments->total / $total_attachments->total * 100, 2 ) . '%)' .
    '</h4>';
    
    echo '
        <div style="width: 100%; height: 40px; border: 1px solid #ccc; background: #999">
        <div style="float: left; width: ' . round( $compressed_attachments->total / $total_attachments->total * 100,3 ) . '%; height: 100%; background: #00cc00; text-align: center; color: #fff;">' . 
            $compressed_attachments->total . '<br>(' . round( $compressed_attachments->total / $total_attachments->total * 100,3 ) . '%)</div>
        <div style="float: left; width: ' . round( $skipped_attachments->total / $total_attachments->total * 100,3 ) . '%; height: 100%; background: #ff6600; text-align: center; color: #fff;">' . 
            $skipped_attachments->total . '<br>(' . round( $skipped_attachments->total / $total_attachments->total * 100,3 ) . '%)</div>
        <div style="float: left; width: ' . round( $failed_attachments->total / $total_attachments->total * 100,3 ) . '%; height: 100%; background: #cc0000; text-align: center; color: #fff;">' . 
            $failed_attachments->total . '<br>(' . round( $failed_attachments->total / $total_attachments->total * 100,3 ) . '%)</div>
        <div style="float: left; width: ' . ( 100 - round( ( $compressed_attachments->total + $skipped_attachments->total + $failed_attachments->total ) / $total_attachments->total * 100,3 ) ) . '%; height: 100%; text-align: center; color: #fff;">' . 
            ( $total_attachments->total - $compressed_attachments->total - $skipped_attachments->total - $failed_attachments->total ) . '<br>(' . ( 100 - round( ( $compressed_attachments->total + $skipped_attachments->total + $failed_attachments->total ) / $total_attachments->total * 100,3 ) ) . '%)</div>
        </div>
    ';
    
    echo '<table class="widefat striped" cellspacing="0" cellpadding="5">';
    echo '<tr>
        <th>ID</th>
        <th>Image</th>
        <th>Size</th>
        <th nowrap>Original Size</th>
        <th nowrap>Compressed Size</th>
        <th>Change</th>
        <th>Status</th>
    </tr>';
    
    foreach ( $images as $image ) :
//        $img = wp_get_attachment_image( $image->attachment_id, 'thumbnail', false, array('style' => 'width: ' . $image_width . 'px; height: auto;') );
        $img_url = $image->new_image_url != '' ? $image->new_image_url : $image->old_image_url;
        $change = round( ( ( $image->compressed_size - $image->original_size ) / $image->original_size ) * 100, 2 );
        $change_style_color = $change < 0 ? '00cc00' : 'ff0000';
        echo '<tr>';
        echo '<td>' . $image->attachment_id . '</td>'
        . '<td><a href="' . $img_url . '" target="_blank"><img src="' . $img_url . '" width="' . $image_width . '"></a></td>'
        . '<td>' . $image->size . '</td>'
//        . '<td>' . $image->new_image_url . '</td>'
        . '<td>' . formatBytes( $image->original_size ) . '</td>'
        . '<td>' . formatBytes( $image->compressed_size ) . '</td>'
        . '<td style="color: #' . $change_style_color . '">' . $change . '%</td>'
        . '<td>' . $image->status . '</td>'
        ;
        echo '</tr>';
    endforeach;
    echo '</table>';
    
    if ( $page_links ) {
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">Total: ' . $total . ' items ' . $page_links . '</div></div>';
    }
}

function ssm_compress_images_show_func2() {
    global $wpdb;
    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
    
    $limit = 20;
    $offset = ( $pagenum - 1 ) * $limit;
    $total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$wpdb->prefix}ssm_compressed_images2" );
    $num_of_pages = ceil( $total / $limit );
    $images = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ssm_compressed_images2 ORDER BY id DESC LIMIT {$offset}, {$limit}" );
    
    $image_width = 100;
    
    $page_links = paginate_links( array(
        'base' => add_query_arg( 'pagenum', '%#%' ),
        'format' => '',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'total' => $num_of_pages,
        'current' => $pagenum,
    ) );
    
    if ( $page_links ) {
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">Total: ' . $total . ' items ' . $page_links . '</div></div>';
    }
    
    echo '<h1>Compression Results</h1>';
    
    $compression = $wpdb->get_row( "SELECT SUM(original_size) AS total_original_size, SUM(compressed_size) AS total_compressed_size FROM {$wpdb->prefix}ssm_compressed_images2 WHERE status = 'Compressed' " );
    $overall_change = round( ( ( $compression->total_compressed_size - $compression->total_original_size ) / $compression->total_original_size ) * 100, 2 );
    
    $compressed_attachments = $wpdb->get_row( "SELECT COUNT(*) AS total FROM (SELECT DISTINCT attachment_id FROM {$wpdb->prefix}ssm_compressed_images2 WHERE status = 'Compressed') src" );
    $skipped_attachments = $wpdb->get_row( "SELECT COUNT(*) AS total FROM (SELECT DISTINCT attachment_id FROM {$wpdb->prefix}ssm_compressed_images2 WHERE status = 'Skipped') src" );
    $failed_attachments = $wpdb->get_row( "SELECT COUNT(*) AS total FROM (SELECT DISTINCT attachment_id FROM {$wpdb->prefix}ssm_compressed_images2 WHERE status = 'Failed') src" );
    $total_attachments = $wpdb->get_row( "SELECT COUNT(*) AS total FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' " );
    
    echo '<h4>Original size: ' . formatBytes( $compression->total_original_size ) .
    ' | Compressd size: ' . formatBytes( $compression->total_compressed_size ) .
    ' | Change: ' . $overall_change . '%' .
//    ' | Processed: ' . $processed_attachments->total . ' out of ' . $total_attachments->total . ' (' . round( $processed_attachments->total / $total_attachments->total * 100, 2 ) . '%)' .
    '</h4>';
    
    echo '
        <div style="width: 100%; height: 40px; border: 1px solid #ccc; background: #999">
        <div style="float: left; width: ' . round( $compressed_attachments->total / $total_attachments->total * 100,3 ) . '%; height: 100%; background: #00cc00; text-align: center; color: #fff;">' . 
            $compressed_attachments->total . '<br>(' . round( $compressed_attachments->total / $total_attachments->total * 100,3 ) . '%)</div>
        <div style="float: left; width: ' . round( $skipped_attachments->total / $total_attachments->total * 100,3 ) . '%; height: 100%; background: #ff6600; text-align: center; color: #fff;">' . 
            $skipped_attachments->total . '<br>(' . round( $skipped_attachments->total / $total_attachments->total * 100,3 ) . '%)</div>
        <div style="float: left; width: ' . round( $failed_attachments->total / $total_attachments->total * 100,3 ) . '%; height: 100%; background: #cc0000; text-align: center; color: #fff;">' . 
            $failed_attachments->total . '<br>(' . round( $failed_attachments->total / $total_attachments->total * 100,3 ) . '%)</div>
        <div style="float: left; width: ' . ( 100 - round( ( $compressed_attachments->total + $skipped_attachments->total + $failed_attachments->total ) / $total_attachments->total * 100,3 ) ) . '%; height: 100%; text-align: center; color: #fff;">' . 
            ( $total_attachments->total - $compressed_attachments->total - $skipped_attachments->total - $failed_attachments->total ) . '<br>(' . ( 100 - round( ( $compressed_attachments->total + $skipped_attachments->total + $failed_attachments->total ) / $total_attachments->total * 100,3 ) ) . '%)</div>
        </div>
    ';
    
    echo '<table class="widefat striped" cellspacing="0" cellpadding="5">';
    echo '<tr>
        <th>ID</th>
        <th>Image</th>
        <th>Size</th>
        <th nowrap>Original Size</th>
        <th nowrap>Compressed Size</th>
        <th>Change</th>
        <th>Status</th>
    </tr>';
    
    foreach ( $images as $image ) :
        $img_url = $image->new_image_url != '' ? $image->new_image_url : $image->old_image_url;
        $change = round( ( ( $image->compressed_size - $image->original_size ) / $image->original_size ) * 100, 2 );
        $change_style_color = $change < 0 ? '00cc00' : 'ff0000';
        echo '<tr>';
        echo '<td>' . $image->attachment_id . '</td>'
        . '<td><a href="' . $img_url . '" target="_blank"><img src="' . $img_url . '" width="' . $image_width . '"></a></td>'
        . '<td>' . $image->size . '</td>'
        . '<td>' . formatBytes( $image->original_size ) . '</td>'
        . '<td>' . formatBytes( $image->compressed_size ) . '</td>'
        . '<td style="color: #' . $change_style_color . '">' . $change . '%</td>'
        . '<td>' . $image->status . '</td>'
        ;
        echo '</tr>';
    endforeach;
    echo '</table>';
    
    if ( $page_links ) {
        echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">Total: ' . $total . ' items ' . $page_links . '</div></div>';
    }
}

function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('', 'K', 'M', 'G', 'T');   

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}


// Creating tables for all blogs in a WordPress Multisite installation
function on_activate( $network_wide ) {
    global $wpdb;
    if ( is_multisite() && $network_wide ) {
        // Get all blogs in the network and activate plugin on each one
        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
        foreach ( $blog_ids as $blog_id ) {
            switch_to_blog( $blog_id );
            create_table();
            restore_current_blog();
        }
    } else {
        create_table();
    }
}
register_activation_hook( __FILE__, 'on_activate' );

// Create tables in Single site installations
function create_table() {
    global $wpdb;
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    $table_name_compressed_images = $wpdb->prefix . 'ssm_compressed_images';
    $table_name_missing_images = $wpdb->prefix . 'ssm_missing_images';
    
    if( $wpdb->get_var( "show tables like '{$table_name_compressed_images}'" ) != $table_name_compressed_images ) {
        $sql_compressed_images = "CREATE TABLE " . $table_name_compressed_images . " (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `attachment_id` int(11) NOT NULL,
            `size` varchar(100) NOT NULL,
            `old_image_path` varchar(1000) NOT NULL,
            `new_image_path` varchar(1000) NOT NULL,
            `old_image_url` varchar(1000) NOT NULL,
            `new_image_url` varchar(1000) NOT NULL,
            `original_size` int(11) NOT NULL,
            `compressed_size` int(11) NOT NULL,
            `status` varchar(10) NOT NULL,
            `created_at` datetime NOT NULL,
            `compressed_at` datetime NOT NULL,
            PRIMARY KEY  (id)
        );";
        dbDelta( $sql_compressed_images );
    }
    
    if( $wpdb->get_var( "show tables like '{$table_name_missing_images}'" ) != $table_name_missing_images ) {
        $sql_missing_images = "CREATE TABLE " . $table_name_missing_images . " (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `attachment_id` int(11) NOT NULL,
            `file_path` varchar(1000) NOT NULL,
            `created_at` datetime NOT NULL,
            PRIMARY KEY  (id)
        );";
        dbDelta( $sql_missing_images );
//        add_option( EmailLog::DB_OPTION_NAME, EmailLog::DB_VERSION );
    }
    
}

// Creating table whenever a new blog is created
function on_create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    if ( is_plugin_active_for_network( 'plugin-name/plugin-name.php' ) ) {
        switch_to_blog( $blog_id );
        create_table();
        restore_current_blog();
    }
}
add_action( 'wpmu_new_blog', 'on_create_blog', 10, 6 );

// Deleting the table whenever a blog is deleted
function on_delete_blog( $tables ) {
    global $wpdb;
    $tables[] = $wpdb->prefix . 'table_name';
    return $tables;
}
add_filter( 'wpmu_drop_tables', 'on_delete_blog' );

// Attachments
function ssm_attachment_json_func($data) {
    $return = array();
    $attachment_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : NULL;
    if ( is_null( $attachment_id ) )
        return $return;
    $return['fileurl'] = $fileurl = wp_get_attachment_url( $attachment_id ); // Get File URL
    $return['filetype'] = $filetype = wp_check_filetype( $fileurl ); // Get File Type
    $return['filepath'] = $filepath = get_attached_file( $attachment_id ); // Get File Path
    $return['filesize'] = filesize( $filepath );
    return $return;
}
add_action( 'rest_api_init', function () {
    register_rest_route( 'api/v1', '/attachment', array(
        'methods' => 'GET',
        'callback' => 'ssm_attachment_json_func',
    ) );
} );

// Get attachment sizes
function ssm_attachment_sizes_json_func($data) {
    return get_intermediate_image_sizes();
}
add_action( 'rest_api_init', function () {
    register_rest_route( 'api/v1', '/attachment/sizes', array(
        'methods' => 'GET',
        'callback' => 'ssm_attachment_sizes_json_func',
    ) );
} );

function ssm_attachment_intermediate_size_json_func($data) {
    global $wpdb;
    $return = array();
    $attachment_id = isset($_GET['id']) ? (int) $_GET['id'] : NULL;
    $size = isset($_GET['size']) ? trim( $_GET['size'] ) : NULL;
    if ( is_null( $attachment_id ) || is_null( $size ) )
        return $return;
    $intermediate_size = image_get_intermediate_size( $attachment_id, $size );
    if ( is_array( $intermediate_size ) && count( $intermediate_size ) > 0 ) {
        $filepath = get_attached_file( $attachment_id ); // Get File Path
        $filepath_thumbnail = realpath( str_replace( wp_basename( $filepath ), $intermediate_size['file'], $filepath ) );
        
        $intermediate_size['filepath_thumbnail'] = $filepath_thumbnail;
        
        if ( is_file( $filepath_thumbnail ) ) {
            $intermediate_size['filesize'] = filesize( $filepath_thumbnail );
            return $intermediate_size;
        } else {
            $wpdb->insert( $wpdb->prefix . 'ssm_missing_images',
                array(
                    'attachment_id' => $attachment_id,
                    'file_path' => $filepath_thumbnail,
                    'created_at' => current_time('mysql', 1),
            ) );
        }
    }
    return false; // $intermediate_size;
}
add_action( 'rest_api_init', function () {
    register_rest_route( 'api/v1', '/attachment/size', array(
        'methods' => 'GET',
        'callback' => 'ssm_attachment_intermediate_size_json_func',
    ) );
} );

// Get article (post) by ID
function ssm_article_json_func($data) {
    $return = array();
    $post_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : NULL;
    if ( is_null( $post_id ) )
        return $return;
    return get_post( $post_id );
}
add_action( 'rest_api_init', function () {
    register_rest_route( 'api/v1', '/article', array(
        'methods' => 'GET',
        'callback' => 'ssm_article_json_func',
    ) );
} );

// Update posts modified date to current date/time
function ssm_update_post_json_func($data) {
    // before saving post
    remove_filter('content_save_pre', 'wp_filter_post_kses');
    remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
    
    $return = array();
    $post_id = isset( $_GET['id'] ) ? (int) $_GET['id'] : NULL;
    if ( is_null( $post_id ) )
        return $return;
    $post = get_post( $post_id );
    if ( $post && $post->post_status != 'draft' ) {
        wp_update_post(
            array(
                'ID' => $post_id,
                'post_modified' => current_time('mysql', 1),
            )
        );
    }
    
    // after saving post
    add_filter('content_save_pre', 'wp_filter_post_kses');
    add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
}
add_action( 'rest_api_init', function () {
    register_rest_route( 'api/v1', '/article/update_modified_date', array(
        'methods' => 'GET',
        'callback' => 'ssm_update_post_json_func',
    ) );
} );

// Update attachment metadata
function ssm_update_attachment_metadata_json_func($data) {
    // before saving post
    remove_filter('content_save_pre', 'wp_filter_post_kses');
    remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
    
    global $wpdb;
    $return = array();
    $attachment_id = isset( $_POST['id'] ) ? (int) $_POST['id'] : NULL;
    if ( is_null( $attachment_id ) )
        return $return;
    
    $attachment = get_post( $attachment_id );
    
    $image_size = $_POST['image_size'];
    $compressed_image_size = $_POST['compressed_image_size'];
    $filename = $_POST['filename'];
    $compress_filename = $_POST['compress_filename'];
    $compress_filepath = $_POST['compress_filepath'];
    $fileurl = $_POST['fileurl'];
    $compress_fileurl = $_POST['compress_fileurl'];
    $compressed_mime_type = $_POST['compressed_mime_type'];
    $new_image_filepath = $_POST['new_image_filepath'];
    
    $metadata = wp_get_attachment_metadata( $attachment_id );
    if ( 'full' == $image_size ) {
        $metadata['width'] = $compressed_image_size[0];
        $metadata['height'] = $compressed_image_size[1];
        $metadata['file'] = str_replace( $filename, $compress_filename, $metadata['file'] );

        // Update attachment meta
        update_attached_file( $attachment_id, str_replace( '_C.jpg', '.jpg', $new_image_filepath ) );

        // Update guid in posts table
        $wpdb->query(
            "UPDATE {$wpdb->prefix}posts
                SET guid = REPLACE( guid, '{$fileurl}', '{$compress_fileurl}' )
                WHERE ID = {$attachment_id}
                LIMIT 1 "
        );
    } else {
        $metadata['sizes'][$image_size] = array(
            'file' => str_replace( $filename, $compress_filename, $metadata['sizes'][$image_size]['file'] ),
            'width' => $compressed_image_size[0],
            'height' => $compressed_image_size[1],
            'mime-type' => $compressed_mime_type,
        );
    }

    // Update Post (Attachment) to set Current Date as Modified Date
    wp_update_post(
        array(
            'ID' => $attachment_id,
            'post_modified' => current_time('mysql', 1),
        )
    );

    // Update Post (having the attachment image as featured) to set Current Date as Modified Date
    wp_update_post(
        array(
            'ID' => $attachment->post_parent,
            'post_modified' => current_time('mysql', 1),
        )
    );
    // Update attachment and thumbnails postmeta
    if ( isset ( $metadata ) ) {
        wp_update_attachment_metadata( $attachment_id, $metadata );
    }
    
    // after saving post
    add_filter('content_save_pre', 'wp_filter_post_kses');
    add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
}
add_action( 'rest_api_init', function () {
    register_rest_route( 'api/v1', '/attachment/update_metadata', array(
        'methods' => 'POST',
        'callback' => 'ssm_update_attachment_metadata_json_func',
    ) );
} );