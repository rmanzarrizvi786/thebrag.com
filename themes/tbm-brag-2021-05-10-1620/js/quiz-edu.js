jQuery(document).ready(function($) {
    /*
    $('.btn-start').on('click', function() {
        $(this).slideUp('fast', function() {
            $('#quiz-details-0').slideDown();
            
            var progress = (1) * 100  / $('.quiz-details').length;
            $('.progress .progress-indicator').css( { 'width' : progress + '%' } );
            
        });
    });
    */
    
    var current_q = -1,
        target_q = -1,
        active_qn = -1;
    function navigate( direction ) {
        
        $('.progress').show();
        
        if ( 'next' == direction ) {
            target_q++;
        } else if ( 'prev' == direction ) {
            target_q--;
        }
        
        var progress = ( target_q + 1 ) * 100  / $('.quiz-details').length;
        $('.progress .progress-indicator').css( { 'width' : progress + '%' } );
        
        if ( $('#quiz-details-' + target_q).length ) {
            
            $('#hero-image').prop('src', header_images[target_q]);
            
            $('#quiz-details-' + current_q).slideUp();
            $('#quiz-details-' + target_q).slideDown();
            
            active_qn = $('#quiz-details-' + target_q).data('qn');
            
//            var target_offset = $('#quiz-details-' + current_q).offset() ? $('#quiz-details-' + current_q).offset().top : 0;
            
            if ( target_q > 0 ) {
                var target_offset = $('.progress').offset() ? $('.progress').offset().top : 0;
                var customoffset = 5;
                $('html, body').animate({scrollTop:target_offset - customoffset}, 500);
                $('#quiz-details-' + target_q).find('.btn-prev').show();
            }   
        }
        
        current_q = target_q;
        $('#current_q').text( current_q + 1 );
        
    }
    
//    $('#start-wrap').slideUp('fast', function() { $('#questions-wrap').show(); navigate( 'next' ); });
    
    $('.btn-start').on('click', function() {
//        $(this).slideUp('fast', function() {
        $('#start-wrap').slideUp('fast', function() {
            $('#questions-wrap').show();
            navigate( 'next' );
        });
    });
    
    $('.btn-next').on('click', function() {
//        var this_qz = $(this).parents('.quiz-details').data('qz');
        var this_qn = $(this).parents('.quiz-details').data('qn');
        if ( $('input[name="answers[' + this_qn +'][]"]:checked').length < 1 ) {
            $('#error-' + this_qn).text( 'Please select at least one.' ).show();
        } else {
            navigate( 'next' );
        }
    });
    
    $('.btn-prev').on('click', function() {
        navigate( 'prev' );
    });
    
    $('#btn-submit').on('click', function() {
        $('form')[0].submit();
    });
    
    $('#form-quiz').on('submit', function(e) {
        if ( $('input[name="answers[' + active_qn +'][]"]:checked').length < 1 ) {
            $('#error-' + active_qn).text( 'Please select at least one.' ).show();
            e.preventDefault();
            return false;
        } else {
            $(this).submit();
            return true;
        }
        
    });
    
//    alert( $('.quiz-details').length );
    
    /*
    $('.btn-next').on('click change', function() {
        
        var this_qz = $(this).parents('.quiz-details').data('qz');
        var next_qz = this_qz + 1;
        
        var progress = (next_qz + 1) * 100  / $('.quiz-details').length;
        $('.progress .progress-indicator').css( { 'width' : progress + '%' } );
        
        if ( $('#quiz-details-' + next_qz).length ) {
            $('#quiz-details-' + this_qz).slideUp();
            $('#quiz-details-' + next_qz).slideDown();
            $('#quiz-details-' + next_qz).find('.btn-prev').show();
        } else {
            $('form').submit();
            $('#btn-submit').click();
        }
        
//        if ( next_qz >= 1 ) {
//            $('.btn-prev').show();
//        }
    });
    
    $('.btn-prev').on('click change', function() {
        var this_qz = $(this).parents('.quiz-details').data('qz');
        var prev_qz = this_qz - 1;
        
        if ( prev_qz == 0 ) {
            $('.btn-prev').hide();
        }
        
        var progress = (prev_qz + 1) * 100  / $('.quiz-details').length;
        $('.progress .progress-indicator').css( { 'width' : progress + '%' } );
        
        if ( $('#quiz-details-' + prev_qz).length ) {
            $('#quiz-details-' + this_qz).slideUp();
            $('#quiz-details-' + prev_qz).slideDown();
        }
    });
    */
    
    var max_choices = 3;
    $('.answer_choice,.answer_choice_label').on('click change', function() {
        var this_qz = $(this).parents('.quiz-details').data('qz');
        var this_qn = $(this).parents('.quiz-details').data('qn');
        
        $('#error-' + this_qn).text( '' ).hide();
        
        if ( $('input[name="answers[' + this_qn +'][]"]:checked').length == max_choices) {
            $('input[name="answers[' + this_qn +'][]"]:not(:checked)').prop( 'disabled' , true ).addClass('disabled');
            
            var target_offset = $(this).parents('.quiz-details').find('.navigate').offset() ? $(this).parents('.quiz-details').find('.navigate').offset().top : 0;
            var customoffset = 5;
            $('html, body').animate({scrollTop:target_offset - customoffset}, 500);
            
        } else if ( $('input[name="answers[' + this_qn +'][]"]:checked').length < max_choices) {
            $('input[name="answers[' + this_qn +'][]"]:not(:checked)').prop( 'disabled' , false ).removeClass('disabled');
        }
    });
    
    $('.answer_choice,.answer_choice_label').each(function() {
        var this_qz = $(this).parents('.quiz-details').data('qz');
        var this_qn = $(this).parents('.quiz-details').data('qn');
        if ( $('input[name="answers[' + this_qn +'][]"]:checked').length == max_choices) {
            $('input[name="answers[' + this_qn +'][]"]:not(:checked)').prop( 'disabled' , true ).addClass('disabled');
        } else if ( $('input[name="answers[' + this_qn +'][]"]:checked').length < max_choices) {
            $('input[name="answers[' + this_qn +'][]"]:not(:checked)').prop( 'disabled' , false ).removeClass('disabled');
        }
    });
    
    /*
    $('.answer_choice,.answer_choice_label').on('click change', function() {
        var this_qz = $(this).parents('.quiz-details').data('qz');
        var next_qz = this_qz + 1;
        
        if ( $('#quiz-details-' + next_qz).length ) {
            $('#quiz-details-' + this_qz).slideUp();
            $('#quiz-details-' + next_qz).slideDown();
        } else {
            $('form').submit();
            $('#btn-submit').click();
        }
    });
    */
} );