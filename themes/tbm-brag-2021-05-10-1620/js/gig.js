jQuery(document).ready(function($) {
    $('#gig_venue_select').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: BASE + '/wp-admin/admin-ajax.php',
                data: 'action=td_ajax_search&type=venue&term=' + name,
                success: function(data) {
                    console.log( data );
                    response(data);
                }
            });
        },
        renderItem: function (item, search){
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
            return '<div class="autocomplete-suggestion" data-id="'+item[0]+'" data-title="'+item[1]+'" data-link="'+item[2]+'" data-val="'+search+'">' +
                    item[1].replace(re, "<b>$1</b>") + '</div>';
        },
        onSelect: function(e, term, item){
            var venue_id = item.data('id');
            $('#selected_venues').append( '<li class="gig_venue' + venue_id + '">' + item.data('title') + ' <span data-venue="' + venue_id + '" class="remove_venue"><i class="fa fa-pencil" aria-hidden="true"></i></span></li>');
            $('#gig_venues_wrap').append( '<input type="hidden" name="gig_venue" class="gig_venue' + venue_id + '" value="' + venue_id + '">');
            if ( $('input[name="gig_venue"]').length ) {
                $('#gig_venue_select').prop( 'disabled', true );
                $('#gig_venue_select_wrap').hide();
            }
        },
        minChars: 1
    });
    
    $('body').on('click', '.remove_venue', function() {
        var venue_id = $(this).data('venue');
        $( '.gig_venue' + venue_id ).detach();
        if ( $('input[name="gig_venue"]').length === 0 ) {
            $('#gig_venue_select').prop( 'disabled', false );
            $('#gig_venue_select_wrap').show();
        }
    });
    
    
    $('.repeat_rule').addClass('d-none');
    $('.' + $('select[name="gig_repeat_freq"]').val().toLowerCase()).removeClass('d-none');
    $('select[name="gig_repeat_freq"]').on('change', function() {
        gig_repeat_freq = $(this).val().toLowerCase();
        $('.repeat_rule').addClass('d-none');
        $('.' + gig_repeat_freq).removeClass('d-none');
    });
    
    if ( ! $('input[name="gig_repeat"').prop('checked') ) {
        $('.repeat_rules_wrap').addClass('d-none');
    } else {
        $('.repeat_rules_wrap').removeClass('d-none');
    }
    $('input[name="gig_repeat"').on('change', function() {
        if( ! $(this).prop('checked')) {
            $('.repeat_rules_wrap').addClass('d-none');
        } else {
            $('.repeat_rules_wrap').removeClass('d-none');
        }
    });
    
    $('#gig-repeat-daily-interval').on('focus', function() {
        $('#gig-repeat-daily-byday-interval').prop( 'checked', true );
    });
} );