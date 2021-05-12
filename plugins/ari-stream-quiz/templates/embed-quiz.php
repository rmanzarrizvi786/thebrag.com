<?php
$post = get_post( get_the_ID() );
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $post->post_title; ?></title>
    <?php wp_head(); ?>
</head>
<body>
<?php echo do_shortcode( $post->post_content ); ?>
<?php wp_footer(); ?>
</body>
</html>