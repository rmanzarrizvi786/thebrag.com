<section class="container video-record mb-4">
    <div class="py-4 px-2 d-block d-md-flex align-items-start">
        <?php
        //Featured Video (YouTube)
        // $featured_yt_vid_id = NULL;
        // $featured_video = get_option('tbm_featured_video');
        // $tbm_featured_video_link = get_option('tbm_featured_video_link');
        $votw_response = wp_remote_get('https://thebrag.com/wp-json/tbm/votw?v='  . rand(11111, 9999999));

        if (is_array($votw_response) && !is_wp_error($votw_response) && wp_remote_retrieve_response_code($votw_response) == 200) :
            $votw = json_decode($votw_response['body']);
            $featured_video_link = esc_html(stripslashes($votw->link));
            $featured_video_alt = esc_html(stripslashes($votw->artist)) . ' - ' .  esc_html(stripslashes($votw->song));
            $featured_video_img =  BragObserver::resize_image($votw->image, 660, 370, null, '/edm/featured/', 'featured-vid-' . date('Y\wW') . '-n.jpg');
        ?>
            <div class="video px-2">
                <h3 class="heading mb-2">Video <span>of the week</span></h3>
                <?php
                parse_str(parse_url($featured_video_link, PHP_URL_QUERY), $featured_video_vars);
                $featured_yt_vid_id = isset($featured_video_vars['v']) ? $featured_video_vars['v'] : NULL;
                // $featured_video_img = !is_null($featured_yt_vid_id) ? 'https://i.ytimg.com/vi/' . $featured_yt_vid_id . '/0.jpg' : NULL;
                if ($featured_video_link) :
                    // $tbm_featured_video_link_html = file_get_contents($tbm_featured_video_link);
                    // $tbm_featured_video_link_html_dom = new DOMDocument();
                    // @$tbm_featured_video_link_html_dom->loadHTML($tbm_featured_video_link_html);
                    // $meta_og_img_tbm_featured_video_link = null;
                    // foreach ($tbm_featured_video_link_html_dom->getElementsByTagName('meta') as $meta) {
                    //     if ($meta->getAttribute('property') == 'og:image') {
                    //         $featured_video_img = $meta->getAttribute('content');
                    //         break;
                    //     }
                    // }
                ?>
                    <a class="p-r d-block overflow-hidden player-wrap" href="<?php echo $featured_video_link; ?>" target="_blank" rel="noreferrer">
                    <?php else : // $tbm_featured_video_link is not set, so display video   
                    ?>
                        <div class="p-r overflow-hidden" style="cursor:pointer;" title="Click to play video" class="yt-lazy-load" data-id="<?php echo $featured_yt_vid_id; ?>" id="<?php echo $featured_yt_vid_id; ?>">
                        <?php endif; // If $tbm_featured_video_link  
                        ?>
                        <div class="rounded youtube-player player-wrap" data-id="<?php echo $featured_yt_vid_id; ?>" style="background-image:url(<?php echo $featured_video_img; ?>);">
                            <img class="p-a-center play-button" src="<?php echo ICONS_URL; ?>controller-play.svg" alt="Play" title="Play" loading="lazy">
                        </div>
                        <?php if ($featured_video_link) : ?>
                    </a>
                <?php else : // $tbm_featured_video_link is not set, so display video 
                ?>
            </div>
        <?php endif; // If $tbm_featured_video_link 
        ?>
        <h4 class="mt-0 mt-md-3 text-center">
            <?php
            //add artist title and songs title
            if (get_option('tbm_featured_video_artist')) {
                echo '' . esc_html(stripslashes(get_option('tbm_featured_video_artist')));
            }
            if (get_option('tbm_featured_video_song')) {
                echo ' - \'' . esc_html(stripslashes(get_option('tbm_featured_video_song'))) . '\'';
            }
            ?>
        </h4>
    <?php endif; // If Featured Video is set 
    ?>
    </div>

    <div class="record px-2 mt-3 mt-md-0">
        <h3 class="heading mb-2">Record <span>of the week</span></h3>
        <a href="<?php echo get_option('tbm_featured_album_link'); ?>" target="_blank" class="p-r d-block overflow-hidden player-wrap rounded" style="background-image: url(<?php echo get_option('tbm_featured_album_image_url'); ?>);" rel="noreferrer">
            <?php echo esc_html(stripslashes(get_option('tbm_featured_album_artist'))); ?>
            -
            <?php echo esc_html(stripslashes(get_option('tbm_featured_album_title'))); ?>
        </a>
        <h4 class="mt-0 mt-md-3 text-center">
            <?php  echo esc_html(stripslashes(get_option('tbm_featured_album_artist'))); ?>
            -
            <em><?php echo esc_html(stripslashes(get_option('tbm_featured_album_title'))); ?></em>
        </h4>
    </div>
    </div>
</section>