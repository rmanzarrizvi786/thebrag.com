jQuery(document).ready(function($) {
    
    $('body').on('click', '#start_ssm_compress_images', function(e) {
        e.preventDefault();
        $('#ssm_compress_images_progress').append(
                '<tr>' + 
                '<th>Result</td>' +
                '<th>Original Size</td>' +
                '<th>Compressed Size</td>' +
                '<th>Change</td>' +
                '<th>File Type</td>' +
                '</tr>'
            );
        $(this).prop( 'disabled', true );
        ssm_compress_images_next(ssm_compress_images_object);
        
    });
    
    function ssm_compress_images_next(ssm_compress_images_object) {
        var data = {
            'action': 'ssm_compress_images_get_next_image',
            'last_attachment_id': ssm_compress_images_object.last_image_id
        };
        $.post(ssm_compress_images_object.ajax_url, data, function(response) {
            if ( response ) {
//                console.clear(); console.log( response ); return;
                response = $.parseJSON( response );
                if ( response.next_image_id ) {
                    ssm_compress_images_object.next_image_id = response.next_image_id;
                    $('#ssm_compress_images_processing').text('Processing ' + response.next_image_filepath);
                    ssm_compress_images_run(ssm_compress_images_object);
                } else {
                    $('#ssm_compress_images_processing').text('******** Finished ********');
                }
            }
        });
    }
    
    function ssm_compress_images_run(ssm_compress_images_object) {
        var data = {
            'action': 'ssm_compress_image',
            'next_image_id': ssm_compress_images_object.next_image_id
        };
        $.post(ssm_compress_images_object.ajax_url, data, function(response) {
            if ( response ) {
                response = $.parseJSON( response );
                show_results( response );
                ssm_compress_images_next(ssm_compress_images_object);
            } else {
                console.log( response );
            }
        });
    }
    
    function show_results( response ) {
//        $('#ssm_compress_images_processing').detach();
        $('#ssm_compress_images_processing').text('Getting Next Image');
        $('#ssm_compress_images_progress').prepend(
                '<tr>' + 
                '<td>' + response.result + '</td>' +
                '<td>' + response.original_filesize + '</td>' +
                '<td>' + response.compressed_filesize + '</td>' +
                '<td>' + response.change + '</td>' +
                '<td>' + response.filetype + '<br>(' + response.ext + ')</td>' +
                '</tr>'
            );
    }
});