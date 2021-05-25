jQuery(document).ready(function($) {
    $('.quiz-section .quiz .quiz-img.clickable, .quiz-section .quiz h2.clickable').on('click', function() {
        var quiz_id = $(this).data('qz');
        var $quiz = $(this).parent('.quiz');
        var $quiz_details = $('#quiz-details-' + quiz_id);
        $('.quiz-section .quiz-details').hide();
        
        $('.quiz.active').removeClass('active');
        $quiz.addClass('active');
        
        $quiz_details.insertAfter($quiz).show();
        
        $.each( $('.quiz-section .quiz'), function() {
            if ( $(this).parent().find('.quiz-audio').length )
                $(this).parent().find('.quiz-audio audio').get(0).pause();
        });
        if ( $quiz_details.find('.quiz-audio').length )
            $quiz_details.find('.quiz-audio audio').get(0).play();
        
//        $('#btn-submit').show();
        
        var target_offset = $quiz.offset() ? $quiz.offset().top : 0;
        var customoffset = 5;
        $('html, body').animate({scrollTop:target_offset - customoffset}, 500);
    });
    
    $('.answer_choice').on('change', function() {
        var this_qz = $(this).parents('.quiz-details').data('qz');
        var next_qz = this_qz + 1;
        if ( $('.quiz-img.clickable[data-qz="' + next_qz + '"]').length ) {
            setTimeout( function() {
                $('.quiz-img.clickable[data-qz="' + next_qz + '"]').click();
            }, 700);
        } else {
            setTimeout( function() {
                $('.quiz-section .quiz-details').hide();
                $('#btn-submit').addClass('active');
                var target_offset = $('#btn-submit').offset() ? $('#btn-submit').offset().top : 0;
                var customoffset = 5;
                $('html, body').animate({scrollTop:target_offset - customoffset}, 500);
            }, 700);
        }
    });
} );