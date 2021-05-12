jQuery(document).ready(function($) {
  $('body').on('click', '.btn-shout-writer-beer', function(e) {

    $(this).parent().find('.form-shout-writer-beer, .logo-beer').toggle();
    $(this).parent().find('.form-shout-writer-beer').find('input[name="amount"]').focus();

    $(this).toggleClass('active');

  });
  if ( $('#thankYouShoutBeerModal').length ) {
    $('#thankYouShoutBeerModal').modal('show');
  }
} );
