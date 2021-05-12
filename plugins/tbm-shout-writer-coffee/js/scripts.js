jQuery(document).ready(function($) {
    $('body').on('click', '.btn-shout-writer-coffee', function() {
        var form_elem = $(this).parent().find('.' + $(this).data('toggle') );
        form_elem.toggleClass('d-none');
        if ( ! form_elem.hasClass('d-none') ) {
            form_elem.find('input[name="amount"]').focus();
        }
    });
    
    if ( $('#thankYouShoutCoffeeModal').length ) {
        $('#thankYouShoutCoffeeModal').modal('show');
    }
} );