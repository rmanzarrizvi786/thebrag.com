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
        
//    for( var i = 0; i <= 100; i+= 5 ) {
//        console.log( i + ' ' + parseInt( i / 10 ) );
//    }
        
    function navigate( direction ) {
        if ( 'next' == direction ) {
            questions_answered++;
            target_q++;
            if ( target_q == 1 ) {
                var data = {
                    action: 'ssm_save_quiz_rekorderlig_result',
                    result: 'Started',
                    page_url: "",
                    page_title: "",
                    result_key: 0,
                };
                $.post(quiz_rekorderlig.url, data, function(res) {
                }).fail(function(xhr, textStatus, e) {});
            }
        } else if ( 'prev' == direction ) {
            questions_answered--;
            target_q--;
        }
        
//        console.log( current_q );
        
        $('#quiz-details-' + current_q).find('.quiz-answer-result-correct').fadeIn(400);
//        $('#quiz-details-' + current_q).find('.quiz-answer-result-correct').css( { 'border' : '5px solid green' } );
        
//        return false;
        
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
            800
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
//        $('#results #score').text(score);
        var score_per = Math.round( score / ( questions.length - 1 ) * 100 );
        var result_key = 3;
        result_key = parseInt( score_per / 10 )
        /*
        if ( score_per == 0 ) {
            result_key = 0;
        } else if ( score_per > 0 && score_per <= 10 ) {
            result_key = 1;
        } else if ( score_per > 10 && score_per <= 20 ) {
            result_key = 2;
        } else if ( score_per > 20 && score_per <= 30 ) {
            result_key = 3;
        } else if ( score_per > 30 && score_per <= 40 ) {
            result_key = 4;
        } else if ( score_per > 40 && score_per <= 50 ) {
            result_key = 5;
        } else if ( score_per > 50 && score_per <= 60 ) {
            result_key = 6;
        } else if ( score_per > 60 && score_per <= 70 ) {
            result_key = 7;
        } else if ( score_per > 70 && score_per <= 80 ) {
            result_key = 8;
        } else if ( score_per > 80 && score_per <= 90 ) {
            result_key = 9;
        } else if ( score_per > 90 && score_per <= 100 ) {
            result_key = 10;
        }
        */
//        $('#results #score_text').html( score_text );
        $('.result-images .result-image').hide();
//        alert( results[result_key].title );
        $('.result-images #result-image' + result_key ).show();
        $('.progress, .question-numbers').hide();
        
        var data = {
            action: 'ssm_save_quiz_rekorderlig_result',
            result: results[result_key].title,
            page_url: quiz_rekorderlig.page_url,
            page_title: quiz_rekorderlig.page_title,
            result_key: result_key,
        };
        $.post(quiz_rekorderlig.url, data, function(res) {
            if (res.success) {
//                console.log( res.data.twitter_share_url );
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
//        $('form')[0].submit();
    });
    
    /*
    $('#form-quiz').on('submit', function(e) {        
        if ( $('input[name="answers[' + active_qn +']"]:checked').length < 1 ) {
            $('#error-' + active_qn).text( 'Please select at least one.' ).show();
            e.preventDefault();
            return false;
        } else {
            $(this).submit();
            return true;
        }
    });
    */
    
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
    
    
    var o_h = parseInt($('.share-icons').height());
    var o_w = parseInt($('.share-icons').width());
    var o_b = parseInt($('.share-icons').css('bottom'));
    var o_r = parseInt($('.share-icons').css('right'));
//    pos_social_icons();
    $( window ).resize(function() {
//        pos_social_icons();
    });
    function pos_social_icons() {
        var scale_social_icons = $('.results-wrap').outerWidth() / 580;
        if ( scale_social_icons < 1 )
        {
        var pos_bottom_social_icons = o_b - ( (o_h - o_h * scale_social_icons ) * scale_social_icons );
        var pos_right_social_icons = o_r - ( (o_w - o_w * scale_social_icons ) * scale_social_icons );
//        20-(71-53.75)*.75
        $('.share-icons').css(
            {
                transform: 'scale(' + scale_social_icons + '',
                bottom: pos_bottom_social_icons + 'px',
                right: pos_right_social_icons + 'px'
            }
        );
        }
    }
    
} );