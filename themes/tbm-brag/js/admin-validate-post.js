jQuery(document).ready(function($) {

    jQuery('[id$="-all"] > ul.categorychecklist').each(function() {
        var $list = jQuery(this);
        var $firstChecked = $list.find(':checkbox:checked').first();

        if ( !$firstChecked.length )
            return;

        var pos_first = $list.find(':checkbox').position().top;
        var pos_checked = $firstChecked.position().top;

        $list.closest('.tabs-panel').scrollTop(pos_checked - pos_first + 5);
    });

    $('#publish, #save-post').on('click', function (e) {

        var validate_post_error = false;

        /*
        $('#taxonomy-category input[type=checkbox]').each(function () {
            // var selected_cat_id = $('#taxonomy-category input:checked').val();
            if ( this.checked ) {
                var selected_cat_id = $(this).val();
                var cat_parent = $('#category-' + selected_cat_id );
                if( cat_parent.find('.children').length ){
                    if (cat_parent.find('.children').find('input:checked').length == 0) {
                        var cat_text = $.trim( $(this).parent('label').text() );
                        alert('Please select a subcategory of ' + cat_text + ' before publishing this post.');
                        e.stopImmediatePropagation();
                        validate_post_error = true;
                        return false;
                    }
                }
            }
        });
        */

        if ( $( '#focus-keyword-input-metabox' ).length ) {
            if ( $.trim( $( '#focus-keyword-input-metabox' ).val() ).length <= 0 ) {
                alert( 'Please enter Focus keyphrase' );
                $( '#focus-keyword-input-metabox' ).focus();
                e.stopImmediatePropagation();
                validate_post_error = true;
            }
        }
        if ( $( '#focus-keyword-input' ).length ) {
            if ( $.trim( $( '#focus-keyword-input' ).val() ).length <= 0 ) {
                alert( 'Please enter Focus keyphrase' );
                $( '#focus-keyword-input' ).focus();
                e.stopImmediatePropagation();
                validate_post_error = true;
            }
        }

        if ( ! validate_post_error ) {
            return true;
        } else {
            return false;
        }

    });
} );
