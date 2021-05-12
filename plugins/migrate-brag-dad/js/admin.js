(function ( $ ) {

    'use strict';

    $(function () {
        
        var status = 'stopped',
            start_pause = 'start',
            current_post = 0;
        
        $('#start-pause-migration').on('click', function() {
            
            var btn = $(this);
            
            start_pause = status != 'paused' ? 'start' : 'pause';
            
            btn.text( status == 'paused' ? 'Start' : 'Pause' );
            status = status == 'paused' ? 'started' : 'paused';
            
            if ( status == 'paused' ) {            
                migrate();
            } else {
                console.log( 'Paused' );
            }
        });
        
        function migrate() {
            var data = {
                action: 'start_pause_migration',
                nonce: tbm_migrate_brag_dad.nonce,
                start_pause: start_pause,
                current_post: current_post
            };

            $.post( tbm_migrate_brag_dad.url, data, function( response ) {                
                if( response.success ) {
                    current_post = response.data.processed_post;
                    
                    $('#migration-results').append('<div style="padding: 10px;">' + response.data.result + '</div>');                    
                    migrate();
                    
                    console.log( response.data );
                } else {
//                    console.log( response );
                }
            });
        }
    });

}(jQuery));