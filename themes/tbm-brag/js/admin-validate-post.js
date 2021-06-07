jQuery(document).ready(function($) {
    $('#publish, #save-post').on('click', function (e) {
        if ( $( '#focus-keyword-input-metabox' ).length ) {
            if ( $.trim( $( '#focus-keyword-input-metabox' ).val() ).length <= 0 ) {
                alert( 'Please enter Focus keyphrase' );
                $( '#focus-keyword-input-metabox' ).focus();
                e.stopImmediatePropagation();
                return false;
            } else {
                return true;
            }
        }
        if ( $( '#focus-keyword-input' ).length ) {
            if ( $.trim( $( '#focus-keyword-input' ).val() ).length <= 0 ) {
                alert( 'Please enter Focus keyphrase' );
                $( '#focus-keyword-input' ).focus();
                e.stopImmediatePropagation();
                return false;
            } else {
                return true;
            }
        }
    });
} );
