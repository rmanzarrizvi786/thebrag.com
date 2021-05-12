jQuery(function($){
    var scrollable_items = [
        { element: $('.post-listing'), action: 'td_ajax_load_more' },
        { element: $('.cat-post-listing'), action: 'td_ajax_load_more_cat_posts' },
        { element: $('.freeshit-post-listing'), action: 'td_ajax_load_more_freeshit_posts' },
        { element: $('.venue-blocks'), action: 'td_ajax_load_more_venues' },
        { element: $('#post-artist-listing'), action: 'ssm_ajax_load_more_artist_posts' }
    ];
    var scrollable_element, scrollable_element_action;
    var base_page_url = document.location.href;
    var current_page = base_page_url.match( new RegExp("\/page\/([0-9]+)"), "" );
    var page_no = current_page != null ? current_page[1] : 0;
    var venue_letter = base_page_url.match(new RegExp("\/l\/(.*)"), "");
    var v_l = venue_letter != null ? venue_letter[1] : '_';
    base_page_url = base_page_url.replace(new RegExp("\/page\/([0-9]+)"), "");
    var more_posts = true;
    
    if(more_posts) {
        for(var i = 0; i < scrollable_items.length; i++) {
            scrollable_element = scrollable_items[i].element;
            scrollable_element_action = scrollable_items[i].action;
            if(scrollable_element.length) {
                scrollable_element.append('<div class="load-more"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>');
                var button = scrollable_element.find('.load-more');
                var page = parseInt(page_no) + 1;
                var loading = false;
                var scrollHandling = {
                    allow:true,
                    reallow:function() {
                        scrollHandling.allow = true;
                    },
                    delay: 400
                };
                break;
            }
        }
    }
    $(window).scroll(function() {
        if(scrollable_element.length && !loading&&scrollHandling.allow ) {
            scrollHandling.allow = false;
            setTimeout(scrollHandling.reallow,scrollHandling.delay);
            var offset = $(button).offset().top - $(window).scrollTop();
            if( 2000 > offset) {
                loading = true;
                var data = {
                    action: scrollable_element_action,
                    page: page,
                    l: v_l,
                    query: tdloadmore.query,
                    exclude_posts: tdloadmore.exclude_posts,
                    post_type: 'post',
                    is_home: tdloadmore.is_home,
                    is_cat: tdloadmore.is_cat
                };
                $.post(
                    tdloadmore.url,
                    data,
                    function(res) {
                        if(res.success && res.data) {
                            scrollable_element.append(res.data);
                            scrollable_element.append(button);
                            page_url = base_page_url + 'page/' + page + '/';
                            page_title = document.title + ' - Page ' + page;
                            window.history.pushState(null, page_title, page_url);
                            page = page + 1;
                            loading = false;
                        } else {
                            more_posts = false;
                            button.remove();
                        }
                    })
                    .fail(function(xhr,textStatus,e) {
                        console.log(xhr.responseText);
                    });
            }
        }
    });
    
    
    if( scrollable_element.length && $('.col-right-sticky').length && scrollable_element.height() > $('.col-right-sticky').height() ) {
        
        
        var colRightAbsTop = window_height - $('.col-right-sticky').outerHeight();
        
        $(window).scroll(function() {
            
            if ( window_width >= 768 ) {
               var heightTopElements =
                       $('#header').outerHeight() + 
                       $('#ad-leaderboard').outerHeight() + 
                       $('#section-hero').outerHeight() + 
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
    }
    
    
    
});