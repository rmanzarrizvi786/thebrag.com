<?php
if ( 'post' == $this->post->post_type ) :
$author = 'Written by ';
$post_author = $this->get( 'post_author' );
if ( get_field( 'author', $this->get('post_id') ) ) {
    $author .= get_field( 'author', $this->get('post_id') ); }
else if ( get_field( 'Author', $this->get('post_id') ) ) {
    $author .= get_field( 'Author', $this->get('post_id') ); }
else if ( $post_author ) :
    $author .= $post_author->display_name;
endif;
?>
<div class="amp-wp-meta amp-wp-byline">
    <span class="amp-wp-author author vcard"><?php echo esc_html( $author ); ?> on <?php echo date('M d, Y', strtotime( $this->get('post')->post_date ) ); ?></span>
</div>
<?php endif; ?>