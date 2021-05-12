jQuery(document).ready(function($) {
    $('#btn-artist-header').click(function(e) {
        e.preventDefault();

        var custom_uploader = wp.media({
            title: 'Select Image',
            button: {
                text: 'Select'
            },
            multiple: false  // Set this to true to allow multiple files to be selected
        })
        .on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            if ( $('#artist-header-src').length )
                $('#artist-header-src').detach();
            $('#image_id').val(attachment.id);
            $('#artist-header-wrap').append('<img src="' + attachment.sizes.thumbnail.url + '" width="100" id="artist-header-src" style="display:block;"><a href="#" id="remove-image">Remove</a>');
        })
        .open();
    });
    
    $('body').on('click', '.btn-delete', function(e) {
        return confirm( 'Are you sure?' );
    });
    
    $('body').on('click', '#remove-image', function(e) {
        e.preventDefault();
        if ( $('#artist-header-src').length )
            $('#artist-header-src').detach();
        if ( $('#remove-image').length )
            $('#remove-image').detach();
        $('#image_id').val('');
    })
    /*
    $('body').on('click', '.cancel-artist-url, .save-artist-url', function(e) {
        e.preventDefault();
        toggleInputVisibility( $('.artist_' + $(this).data('id')) );
    });
    
    function toggleInputVisibility( container ) {
        container.find('td').find('.text, .input').toggleClass('hide');
    }
    
    $('body').on('click', '.save-artist-url', function(e) {
        e.preventDefault();
        var container = $('.artist_' + $(this).data('id'));
        var artist_id = $(this).data('id');
        var artist_name = container.find('input[name="artist_name"]').val();
        var artist_slug = container.find('input[name="artist_slug"]').val();
        var data = { artist_id: artist_id, artist_name: artist_name, artist_slug: artist_slug };
        $.post(
            ajaxurl,
            {
                action: 'save_artist_url',
                data: data
            },
            function(response){
                res = $.parseJSON(response);
                artist_id = res.artist_ID;
                $('#artist_name_' + artist_id).find('span.text').text( res.artist_name );
                $('#artist_name_' + artist_id).find('span.input input').val( res.artist_name );
                
                $('#artist_slug_' + artist_id).find('span.text').text( res.artist_slug );
                $('#artist_slug_' + artist_id).find('span.input input').val( res.artist_slug );
                
                $('#artist_url_' + artist_id).find('a').prop( 'href', '/artist/' + res.artist_slug );
            }
        );
    });
    
    $('body').on('click', '#btn-add-artist-url', function(e) {
        e.preventDefault();
        $(this).after(
            '<form id="new-artist-url-form" action="#"><input type="hidden" name="page" value="ssm-artist-urls">' +
            '<div id="new-artist-url-container" style="float: left;">' +
            '<input type="text" name="artist_name" value="" placeholder="Artist Name">' +
            '<input type="text" name="artist_slug" value="" placeholder="URL Slug">' +
            '<input type="submit" class="add-artist-url button button-primary" value="Save">' + 
            '<a href="#" class="cancel-add-artist-url button" data-id="<?php echo $artist->artist_ID; ?>">Cancel</a>' +
            '</div>' + 
            '</form>'
        );
        $('input[name="artist_name"]').focus();
        $(this).hide();
    });
    
    $('body').on('submit', '#new-artist-url-form', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        $('#new-artist-url-form').hide();
        $('#artist_url_list').before('<span id="msg-saving">Saving...</span>');
        $.post(
            ajaxurl,
            {
                action: 'add_artist_url',
                data: data
            },
            function(response){
                res = $.parseJSON(response);
                if ( res.success ) {
                    $('#artist_url_list').prepend( res.artist );
                    $('#new-artist-url-form').detach();
                    $('#btn-add-artist-url').show();
                    
                } else {
                    $('#new-artist-url-form').show();
                    $('input[name="artist_name"]').focus();
                    alert( res.errors );
                }
                $('#msg-saving').detach();
            }
        );
    });
    
    $('body').on('click', '.cancel-add-artist-url', function(e) {
        e.preventDefault();
        $('#new-artist-url-form').detach();
        $('#btn-add-artist-url').show();
    });
    */
});