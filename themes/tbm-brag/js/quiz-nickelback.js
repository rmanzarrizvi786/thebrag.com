jQuery(document).ready(function($) {
    
    $('.quiz-header').on('click', function() {
        $("html, body").animate({
            scrollTop: $('#questions-wrap').offset().top - 50
        }, 500);
    });
    
    var current_q = -1,
        target_q = -1,
        active_qn = -1,
        questions_answered = -1;
        
    function navigate( direction ) {
        if ( 'next' == direction ) {
            questions_answered++;
            target_q++;
            if ( target_q == 1 ) {
                var data = {
                    action: 'ssm_save_quiz_nickelback_result',
                    result: 'Started',
                    page_url: "",
                    page_title: "",
                    result_key: 0,
                };
                $.post(quiz_nickelback.url, data, function(res) {
                }).fail(function(xhr, textStatus, e) {});
            }
        } else if ( 'prev' == direction ) {
            questions_answered--;
            target_q--;
        }
                
        $('#quiz-details-' + current_q).find('.quiz-answer-result-correct').fadeIn(400);
        
        setTimeout(
            function() {
                $('#quiz-details-' + current_q).slideUp();

                var progress = ( target_q + 1 ) * 100  / $('.quiz-details').length;
                $('.progress .progress-indicator').css( { 'width' : progress + '%' } );

                if ( $('#quiz-details-' + target_q).length ) {
                    $('#quiz-details-' + target_q).slideDown();
                    active_qn = $('#quiz-details-' + target_q).data('qn');
                }

                current_q = target_q;
                if ( current_q >= total_questions )
                    current_q = total_questions - 1;

                $('.progress').show();
                if ( questions_answered == total_questions ) {
                    calculate();
                }
            },
            1000
        );
    }
    function calculate() {
        var answers = [],
            score = 0;
        $('.answer_choice:checked').each(function() {
            var this_qn = $(this).parents('.quiz-details').data('qn');
            answers[this_qn] = $(this).val();
        });
        for( i = 1; i < answers.length; i++ ) {
            if ( answers[i] == questions[i] )
                score++;
        }
        $('.quiz-details-wrap').fadeOut();
        $('#results').fadeIn(2500);
        var score_per = Math.round( score / ( questions.length - 1 ) * 100 );
        var result_key = 3;
        result_key = parseInt( score_per / 10 )
        
        $('.result-images .result-image').hide();
        $('.result-images #result-image' + result_key ).show();
        $('.progress, .question-numbers').hide();
        
//        console.log( result_key );
        var data = {
            action: 'ssm_save_quiz_nickelback_result',
            result: results[result_key].title,
            page_url: quiz_nickelback.page_url,
            page_title: quiz_nickelback.page_title,
            result_key: result_key,
        };
        $.post(quiz_nickelback.url, data, function(res) {
            if (res.success) {
                $('#social-share-quiz-facebook').prop( 'href', res.data.fb_share_url );
                $('#social-share-quiz-twitter').prop( 'href', res.data.twitter_share_url );
            }
        }).fail(function(xhr, textStatus, e) {});
    }
    
    $('#restart-quiz').on('click', function() {
        $('#results').slideUp();
        current_q = -1;
        target_q = -1;
        active_qn = -1;
        questions_answered = -1;
        $('.answer_choice').prop('checked', false);
        $('#questions-wrap').show(); navigate( 'next' );
    });
    
    $('#questions-wrap').show();
    navigate( 'next' );
    
    $('.btn-next').on('click', function() {
        var this_qn = $(this).parents('.quiz-details').data('qn');
        if ( $('input[name="answers[' + this_qn +']"]:checked').length < 1 ) {
            $('#error-' + this_qn).text( 'Please select the answer.' ).show();
        } else {
            navigate( 'next' );
        }
    });
    
    $('.btn-prev').on('click', function() {
        navigate( 'prev' );
    });
    
    $('#btn-submit').on('click', function() {
        var this_qn = $(this).parents('.quiz-details').data('qn');
        if ( $('input[name="answers[' + this_qn +']"]:checked').length < 1 ) {
            $('#error-' + this_qn).text( 'Please select the answer.' ).show();
        } else {
            $('form')[0].submit();
        }
    });
    
    $('.answer_choice').on('click', function() {
        var this_qn = $(this).parents('.quiz-details').data('qn');
        $('#error-' + this_qn).text( '' ).hide();
        $(this).parent().find('.quiz-answer-result').show();
        if ( $(this).parent().find('.quiz-answer-result').hasClass('quiz-answer-result-correct') ) {
            $('.progress-dots #dot-' + current_q).addClass('correct');
        } else {
            $('.progress-dots #dot-' + current_q).addClass('incorrect');
        }
        navigate( 'next' );
    });
    
} );