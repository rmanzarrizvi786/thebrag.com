jQuery(document).ready(function($) {
    $( '.reset' ).on( 'click', function() {
        elem = $(this).next();
        elem.val('');
        if ( $('#tbm_featured_album_image_url').val() == '' )
            $('#tbm_featured_album_image').detach();
    });
    
    $('#btn-featured-album-image').click(function(e) {
        e.preventDefault();

        var custom_uploader = wp.media({
            title: 'Select Featured Album Image',
            button: {
                text: 'Select'
            },
            multiple: false  // Set this to true to allow multiple files to be selected
        })
        .on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            if ( $('#tbm_featured_album_image').length )
                $('#tbm_featured_album_image').detach();
            console.log( attachment );
            $('#tbm_featured_album_image_url').val(attachment.url);
            $('#btn-featured-album-image').before('<img src="' + attachment.sizes.thumbnail.url + '" width="100" id="tbm_featured_album_image" class="img-fluid d-block">');
        })
        .open();
    });
} );