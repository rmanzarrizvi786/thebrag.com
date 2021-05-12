<?php
    $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
    get_header();
?>

<?php $exclude_posts = []; ?>

<div class="container author">
    
    <div class="d-flex">
    
        <div>
            <div class="author-avatar"><?php echo get_avatar( $curauth->ID, 96, 'blank', '', array( 'class' => 'rounded-circle' ) ); ?></div>
        </div>
        <div class="ml-5">
            <h1><?php echo $curauth->display_name; ?></h1>
            <ul class="nav">
                <?php if ( $curauth->twitter != '' ) : ?>
                    <li class="nav-item"><a href="<?php echo $curauth->twitter; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-twitter-square fa-lg" aria-hidden="true"></i></a></li>
                <?php endif; ?>
                <?php if ( $curauth->facebook != '' ) : ?>
                    <li class="nav-item"><a href="<?php echo $curauth->facebook; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-facebook-square fa-lg" aria-hidden="true"></i></a></li>
                <?php endif; ?>
                <?php if ( $curauth->linkedin != '' ) : ?>
                    <li class="nav-item"><a href="<?php echo $curauth->linkedin; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-linkedin-square fa-lg" aria-hidden="true"></i></a></li>
                <?php endif; ?>
                <?php if ( $curauth->instagram != '' ) : ?>
                    <li class="nav-item"><a href="<?php echo $curauth->instagram; ?>" target="_blank" class="nav-link px-1 text-dark"><i class="fa fa-instagram fa-lg" aria-hidden="true"></i></a></li>
                <?php endif; ?>
            </ul>
        </div>
        
    </div>
    
    <p><?php echo nl2br( $curauth->user_description ); ?></p>
    
    <div class="row">
        <div class="col-12 cats mt-2 mb-4">
            <div style="border-top: 2px solid #000;"></div>
        </div>
        <h2 class="col-12 archive-title mb-4">All posts by <?php echo $curauth->display_name; ?></h2>
    </div>
    
    <div class="row posts">
    <?php    
        $count = 1;
        $show_cats = true;
        while ( have_posts() ) :
            the_post();

            if ( get_field('author') || get_field('Author')) {
                continue;
            }

            $exclude_posts[] = get_the_ID();
            include( get_template_directory() . '/partials/post-tile.php' );
            if ( $count == 2 ) :
                echo '<div class="col-lg-4 col-md-6 col-12 p-0">';
                get_fuse_tag( 'mrec_1' );
                echo '</div>';
            endif;
            
            if ( $count == 8 ) :
                echo '</div><div class="row"><div class="col-lg-8 posts"><div class="row">';
            endif;
            
            if ( $count >= 8 ) :
                $no_of_columns = 2;
            endif;
            
            $count++;
        endwhile;
        $show_cats = false;
        
        wp_reset_postdata();
        echo '</div></div>';
        echo '<div class="col-lg-4 col-md-6 col-12 p-0">';
        get_fuse_tag( 'mrec_2' );
        echo '</div>';
    ?>
    </div>
    
    <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3 page-nav">
            <div class="d-flex justify-content-center my-4">
                <div class="m-3"><?php previous_posts_link( 'Newer' ); ?></div>
                <div class="m-3"><?php next_posts_link( 'Older', '' ); ?></div>
            </div>
        </div>
    </div>
</div>

<?php get_footer();