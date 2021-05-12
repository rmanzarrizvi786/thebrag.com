jQuery(function($) {
    if ($('#posts_wrap').length) {
        $('#posts_wrap').append('<div class="load-more"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>');
        var button = $('#posts_wrap .load-more');
        var loading = false;
        var scrollHandling = {
            allow: true,
            reallow: function() {
                scrollHandling.allow = true;
            },
            delay: 400
        };
    }
    var page_title = document.title;
    var page_url = document.location.protocol + '//' + document.location.host + document.location.pathname; //document.location.href;
    var window_width = $(window).width();
    
    var winTop = $(this).scrollTop();
    var $news_stories = $('.single_story');
    var top_news_story = $.grep($news_stories, function(item) {
        return $(item).position().top <= winTop + 10;
    });
    var visible_news_story = $.grep($news_stories, function(item) {
        return $(item).position().top <= winTop + $(window).height() - $('#header').outerHeight();
    });
    
    var progress_top = 0;
    
    $(window).scroll(function() {
        if ($('#posts_wrap').length) {
            if (!loading && scrollHandling.allow) {
                scrollHandling.allow = false;
                setTimeout(scrollHandling.reallow, scrollHandling.delay);
                var offset = $(button).offset().top - $(window).scrollTop() - $(window).outerHeight();
                if (3000 > offset) {
                    loading = true;
                    var data = {
                        action: 'td_ajax_load_next_post',
                        exclude_posts: ssm_load_next_post.exclude_posts,
                        id: ssm_load_next_post.current_post
                    };
                    $.post(ssm_load_next_post.url, data, function(res) {
                        if (res.success) {
                            if (res.data.exclude_post) {
                                ssm_load_next_post.exclude_posts += ',' + res.data.exclude_post;
                            } else {
                                ssm_load_next_post.current_post = res.data.loaded_post;
                            }
                            $('#posts_wrap').append(res.data.content);
                            $('#posts_wrap').append(button);
                            loading = false;
                        } else {
                            button.remove();
                        }
                    }).fail(function(xhr, textStatus, e) {});
                }
            }
            winTop = $(this).scrollTop();
            
            $news_stories = $('.single_story');
            top_news_story = $.grep($news_stories, function(item) {
                return $(item).position().top <= winTop + 100;
            });
            visible_news_story = $.grep($news_stories, function(item) {
                return $(item).position().top <= winTop + $(window).height() / 2;  // + $('#header').outerHeight() - 30;
            });
            
            
            if ( $(visible_news_story).last().prop('id') != '' ) {
                progress_top = $(visible_news_story).last().offset().top + $(visible_news_story).last().outerHeight() - 30; // - $(window).height() / 2;
                
                var progress = 1 - ( ( progress_top - winTop - $(window).height() ) / $(visible_news_story).last().outerHeight() );
                progress < 0 ? progress = 0 : progress > 1 && ( progress = 1 );
                $('.progress-bar').css( { transform: "scaleX(" + progress + ")" } );
            } else {
                $('.progress-bar').css( { transform: "scaleX(0)" } );
            }
            
            if ($(top_news_story).last().find('h1').text() != '' && page_url != $(top_news_story).last().find('h1').data('href')) {
                /*
                var data_share_count = {
                    action: 'ssm_get_share_count',
                    post_id: $(top_news_story).last().prop('id')
                };
                $.post(ssm_load_next_post.url, data_share_count, function(res_share_count) {
                    if (res_share_count.success) {
                        console.log( res_share_count.data );
                    }
                });
                */
                
                page_title = $(top_news_story).last().find('h1').data('title');
                page_url = $(top_news_story).last().find('h1').data('href');
                
                var share_title = $(top_news_story).last().find('h1').data('share-title');
                var share_url = $(top_news_story).last().find('h1').data('share-url');
                
                $('#social-share-facebook-stacked').prop( 'href' , 'https://www.facebook.com/sharer/sharer.php?u=' + share_url );
                $('#social-share-twitter-stacked').prop( 'href' , 'https://twitter.com/intent/tweet?text=' + share_title + '&amp;url=' + share_url );
                $('#social-share-google-stacked').prop( 'href' , 'https://plus.google.com/share?url=' + share_url + '&text=' + share_title + '&hl=en_AU' );
                $('#social-share-reddit-stacked').prop( 'href' , 'https://reddit.com/submit?url=' + share_url + '&title=' + share_title );
                $('#social-share-whatsapp-stacked').prop( 'href' , 'https://wa.me/?text=' + share_title + ' ' + share_url );
                
                var author = $(top_news_story).last().find('.author').data('author');
                
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                    'AuthorCD': author
                });
                
                document.title = page_title;
                window.history.pushState(null, page_title, page_url);
            }
        }
        
        if ( window_width >= 768 ) {
            var heightTopElements =
                    $('#header').outerHeight() + 
                    $('#ad-leaderboard').outerHeight() + 
                    60 - 
                    $(window).height();
             var pageWidth = $('#main').outerWidth();
             var windowWidth = $(window).outerWidth();
             var marginRight = (windowWidth - pageWidth) / 2;
             var fixRight = heightTopElements + $('.col-right-sticky').outerHeight() + 40;
             var currentScroll = $(window).scrollTop();
             if ( currentScroll >= fixRight ) {
                 $('.col-right-sticky').addClass('stick');
             } else {
                 $('.col-right-sticky').removeClass('stick');
             }
         }
    });
    window.onpopstate = function(event) {
        if ($('#posts_wrap').length) {
            $('html, body').animate({
                scrollTop: $('a[href="' + document.location + '"]:first').offset().top
            }, 10);
        }
    };
});