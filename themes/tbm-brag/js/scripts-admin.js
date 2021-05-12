jQuery(document).ready(function($) {
    /*
    $('#tb_music_gig_title').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl, //'/wp-admin/admin-ajax.php',
                data: 'action=get_listing_gigs&name='+name,
                success: function(data) {
                    response(data);
                }
            });
        },
        renderItem: function (item, search){
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
            return '<div class="autocomplete-suggestion" data-id="'+item[0]+'" data-title="'+item[1]+'" data-val="'+search+'">' +
                    item[1].replace(re, "<b>$1</b>")+'</div>';
        },
        onSelect: function(e, term, item){
            $('#tb_music_gig_title').val( item.data('title') );
            $('#tb_music_gig_ID').val( item.data('id') );
        },
        minChars: 1
    });
    
    $('#tb_cover_story_title').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl, //'/wp-admin/admin-ajax.php',
                data: 'action=get_listing_posts&name='+name,
                success: function(data) {
                    response(data);
                }
            });
        },
        renderItem: function (item, search){
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
            return '<div class="autocomplete-suggestion" data-id="'+item[0]+'" data-title="'+item[1]+'" data-val="'+search+'">' +
                    item[1].replace(re, "<b>$1</b>")+'</div>';
        },
        onSelect: function(e, term, item){
            $('#tb_cover_story_title').val( item.data('title') );
            $('#tb_cover_story_ID').val( item.data('id') );
        },
        minChars: 1
    });
    
    $('#td_featured_infinite_title').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl, //'/wp-admin/admin-ajax.php',
                data: 'action=get_listing_news&name='+name,
                success: function(data) {
                    response(data);
                }
            });
        },
        renderItem: function (item, search){
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
            return '<div class="autocomplete-suggestion" data-id="'+item[0]+'" data-title="'+item[1]+'" data-val="'+search+'">' +
                    item[1].replace(re, "<b>$1</b>")+'</div>';
        },
        onSelect: function(e, term, item){
            $('#td_featured_infinite_title').val( item.data('title') );
            $('#td_featured_infinite_ID').val( item.data('id') );
        },
        minChars: 1
    });
    
    $('#tb_comedy_gig_title').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl, //'/wp-admin/admin-ajax.php',
                data: 'action=get_listing_gigs&name='+name,
                success: function(data) {
                    response(data);
                }
            });
        },
        renderItem: function (item, search){
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
            return '<div class="autocomplete-suggestion" data-id="'+item[0]+'" data-title="'+item[1]+'" data-val="'+search+'">' +
                    item[1].replace(re, "<b>$1</b>")+'</div>';
        },
        onSelect: function(e, term, item){
            $('#tb_comedy_gig_title').val( item.data('title') );
            $('#tb_comedy_gig_ID').val( item.data('id') );
        },
        minChars: 1
    });
    
    $( '.reset' ).on( 'click', function() {
        elem = $(this).next();
        elem.val('');
        if ( elem.prop( 'id' ) == 'tb_music_gig_title' ) {
            $('#tb_music_gig_ID').val('');
        } else if ( elem.prop( 'id' ) == 'tb_music_gig_ID' ) {
            $('#tb_music_gig_title').val('');
        } else if ( elem.prop( 'id' ) == 'tb_comedy_gig_title' ) {
            $('#tb_comedy_gig_ID').val('');
        } else if ( elem.prop( 'id' ) == 'tb_comedy_gig_ID' ) {
            $('#tb_comedy_gig_title').val('');
        } else if ( elem.prop( 'id' ) == 'tb_cover_story_title' ) {
            $('#tb_cover_story_ID').val('');
        } else if ( elem.prop( 'id' ) == 'tb_cover_story_ID' ) {
            $('#tb_cover_story_title').val('');
        }
    });
    
    $('.delete').on('click', function(){
        return confirm( 'Are you sure you want to delete this? It cannot be reversed.');
    });
    
    $( '.reset' ).on( 'click', function() {
        elem = $(this).next();
        elem.val('');
    });
    
    var campaign_posts = [];
    var total_posts_section1 = total_posts_section2 = 0;
    if ( $('.campaign-posts-section1').length )
        total_posts_section1 = $('.campaign-posts-section1').length;
    $('#total-section1').find('.total').text(total_posts_section1);
    
    if ( $('.campaign-posts-section2').length )
        total_posts_section2 = $('.campaign-posts-section2').length;
    $('#total-section2').find('.total').text(total_posts_section2);
    
    // Prevent Submit form
    $('.create-campaign').on('submit', function(e) {
        e.preventDefault();
    });
    // Save Campaign
    $('.create-campaign #submit-campaign').on('click', function() {
        $(this).attr('disabled', true).parent().find('.status').html(' Saving...');
        var data = $('.create-campaign').serialize();
        $.post(
            ajaxurl,
            {
                action: 'save_campaign',
                data: data
            },
            function(response){
                res = $.parseJSON(response);
                $('#td-mc-errors').html('');
                if (res.success) {
                    window.location = '?page=ssm-mailchimp/ssm-mailchimp.php';
                } else if (res.errors) {
                    var errors = '';
                    $.each(res.errors, function(index, error) {
                        errors += error + "\n";
                        
                    });
                    $('.create-campaign #submit-campaign').parent().find('#td-mc-errors').removeClass('hide').html(errors);
                    $('.create-campaign #submit-campaign').attr('disabled', false).parent().find('.status').html(' Error Saving, please check the errors and try again.');
                }
            }
        );
    });
    
    // AJAX Search for Campaign Section 1 posts
    $('.create-campaign #add-post-section1').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: 'action=td_ajax_search&type=post&term='+name,
                success: function(data) {
                    response(data);
                }
            });
        },
        renderItem: function (item, search){
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
            return '<div class="autocomplete-suggestion" ' + 
                    'data-id="' + item[0] + '" ' +
                    'data-title="' + item[1]+ '" ' +
                    'data-link="' + item[2] + '" ' +
                    'data-date="' + item[3] + '" ' +
                    'data-excerpt="' + item[4] + '"' +
                    'data-thumbnail="' + item[5] + '"' +
                    'data-val="' + search + '">' +
                    item[1].replace(re, "<b>$1</b>") +
                    '</div>';
        },
        onSelect: function(e, term, item){
            var post_id = parseInt( item.data('id') );
            $('.create-campaign #add-post-section1').next('div.error').text('').addClass('hide');
            if ( $.inArray( post_id, campaign_posts ) === -1 ) {
                campaign_posts.push( post_id );
                var post_title = item.data('title');
                var post_date = item.data('date');
                var post_excerpt = item.data('excerpt');
                var post_thumbnail = item.data('thumbnail');
                var post_link = item.data('link');
                total_posts_section1++;
                $('#campaign-posts-section1').append(
                        '<tr id="campaign-post-' + post_id + '">' +
                        '<td><input type="number" maxlength="2" min="1" class="campaign-posts-section1" name="posts_section1[' + post_id + ']" value="' + total_posts_section1 + '" size="2"></td>' +
                        '<td><a href="' + post_link + '" target="_blank"><img src="' + post_thumbnail + '" width="50"></a></td>' +
                        '<td><input type="text" name="titles_section1[' + post_id + ']" value="' + post_title + '"><br><textarea name="excerpts_section1[' + post_id + ']">' + post_excerpt + '</textarea><br><small>(' + post_date + ')</small></td>' +
                        '<td><label class="remove remove-campaign-post1" data-id="' + post_id + '">x</label></td>' +
                        '</tr>'
                        );
                $('#total-section1').find('.total').text(total_posts_section1);
                $('.create-campaign #add-post-section1').val('');
            } else {
                $('.create-campaign #add-post-section1').next('div.error').removeClass('hide').text('Already in the list.');
            }
        },
        minChars: 1
    });
    $(document).on('click', '.remove-campaign-post1' , function() {
        var post_id = parseInt( $(this).data('id') );
        campaign_posts = jQuery.grep(campaign_posts, function(value) {
            return value !== post_id;
        });
        $('#campaign-post-' + post_id).detach();
        total_posts_section1--;
        $('#total-section1').find('.total').text(total_posts_section1);
    });
    
    // AJAX Search for Campaign Section 2 posts
    $('.create-campaign #add-post-section2').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: 'action=td_ajax_search&type=post&term='+name,
                success: function(data) {
                    response(data);
                }
            });
        },
        renderItem: function (item, search){
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
            return '<div class="autocomplete-suggestion" ' + 
                    'data-id="' + item[0] + '" ' +
                    'data-title="' + item[1]+ '" ' +
                    'data-link="' + item[2] + '" ' +
                    'data-date="' + item[3] + '" ' +
                    'data-excerpt="' + item[4] + '"' +
                    'data-thumbnail="' + item[5] + '"' +
                    'data-val="' + search + '">' +
                    item[1].replace(re, "<b>$1</b>") +
                    '</div>';
        },
        onSelect: function(e, term, item){
            var post_id = parseInt( item.data('id') );
            $('.create-campaign #add-post-section2').next('div.error').text('').addClass('hide');
            if ( $.inArray( post_id, campaign_posts ) === -1 ) {
                campaign_posts.push( post_id );
                var post_title = item.data('title');
                var post_date = item.data('date');
                var post_excerpt = item.data('excerpt');
                var post_thumbnail = item.data('thumbnail');
                var post_link = item.data('link');
                total_posts_section2++;
                $('#campaign-posts-section2').append(
                        '<tr id="campaign-post-' + post_id + '">' +
                        '<td><input type="number" maxlength="2" min="1" class="campaign-posts-section2" name="posts_section2[' + post_id + ']" value="' + total_posts_section2 + '" size="2"></td>' +
                        '<td><a href="' + post_link + '" target="_blank"><img src="' + post_thumbnail + '" width="50"></a></td>' +
                        '<td><input type="text" name="titles_section2[' + post_id + ']" value="' + post_title + '"><br><textarea name="excerpts_section2[' + post_id + ']">' + post_excerpt + '</textarea><br><small>(' + post_date + ')</small></td>' +
                        '<td><label class="remove remove-campaign-post2" data-id="' + post_id + '">x</label></td>' +
                        '</tr>'
                        );
                $('#total-section2').find('.total').text(total_posts_section2);
                $('.create-campaign #add-post-section2').val('');
            } else {
                $('.create-campaign #add-post-section2').next('div.error').removeClass('hide').text('Already in the list.');
            }
        },
        minChars: 1
    });
    $(document).on('click', '.remove-campaign-post2' , function() {
        var post_id = parseInt( $(this).data('id') );
        campaign_posts = jQuery.grep(campaign_posts, function(value) {
            return value !== post_id;
        });
        $('#campaign-post-' + post_id).detach();
        total_posts_section2--;
        $('#total-section2').find('.total').text(total_posts_section2);
    });
    
    // AJAX Search for Campaign Featured Hero
    $('.create-campaign #add-featured-hero').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: 'action=td_ajax_search&type=post&term='+name,
                success: function(data) {
                    response(data);
                }
            });
        },
        renderItem: function (item, search){
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
            return '<div class="autocomplete-suggestion" ' + 
                    'data-id="' + item[0] + '" ' +
                    'data-title="' + item[1]+ '" ' +
                    'data-link="' + item[2] + '" ' +
                    'data-date="' + item[3] + '" ' +
                    'data-excerpt="' + item[4] + '"' +
                    'data-thumbnail="' + item[5] + '"' +
                    'data-val="' + search + '">' +
                    item[1].replace(re, "<b>$1</b>") +
                    '</div>';
        },
        onSelect: function(e, term, item){
            if ( $('.remove-featured-hero').length) {
                alert ('You can add only one featured hero, please remove the other one first.');
                return false;
            }
            var post_id = parseInt( item.data('id') );
            if ( $.inArray( post_id, campaign_posts ) === -1 ) {
                campaign_posts.push( post_id );
                var post_title = item.data('title');
                var post_date = item.data('date');
                var post_excerpt = item.data('excerpt');
                var post_thumbnail = item.data('thumbnail');
                var post_link = item.data('link');
                $('#campaign-featured-hero').append(
                        '<tr id="campaign-post-' + post_id + '">' +
                        '<td><a href="' + post_link + '" target="_blank"><img src="' + post_thumbnail + '" width="70"></a></td>' +
                        '<td>' +
                        '<input type="hidden" class="campaign-featured-hero" name="featured_hero" value="' + post_id + '">' +
                        '<input type="text" name="featured_hero_title" value="' + post_title + '"><br>' + 
                        '<textarea name="featured_hero_excerpt">' + post_excerpt + '</textarea><br>' + 
                        '</td>' +
                        '<td><label class="remove remove-featured-hero" data-id="' + post_id + '">x</label></td>' +
                        '</tr>'
                        );
                $('.create-campaign #add-featured-hero').val('');
            }
        },
        minChars: 1
    });
    $(document).on('click', '.remove-featured-hero' , function() {
        var post_id = parseInt( $(this).data('id') );
        campaign_posts = jQuery.grep(campaign_posts, function(value) {
            return value !== post_id;
        });
        $('#campaign-post-' + post_id).detach();
    });
    
    // AJAX Search for Campaign Footer Article
    $('.create-campaign #add-footer-article').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: 'action=td_ajax_search&type=post&term='+name,
                success: function(data) {
                    response(data);
                }
            });
        },
        renderItem: function (item, search){
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
            return '<div class="autocomplete-suggestion" ' + 
                    'data-id="' + item[0] + '" ' +
                    'data-title="' + item[1]+ '" ' +
                    'data-link="' + item[2] + '" ' +
                    'data-date="' + item[3] + '" ' +
                    'data-excerpt="' + item[4] + '"' +
                    'data-thumbnail="' + item[5] + '"' +
                    'data-val="' + search + '">' +
                    item[1].replace(re, "<b>$1</b>") +
                    '</div>';
        },
        onSelect: function(e, term, item){
            if ( $('.remove-footer-article').length) {
                alert ('You can add only one Footer Article, please remove the other one first.');
                return false;
            }
            var post_id = parseInt( item.data('id') );
            if ( $.inArray( post_id, campaign_posts ) === -1 ) {
                campaign_posts.push( post_id );
                var post_title = item.data('title');
                var post_date = item.data('date');
                var post_excerpt = item.data('excerpt');
                var post_thumbnail = item.data('thumbnail');
                var post_link = item.data('link');
                $('#campaign-footer-article').append(
                        '<tr id="campaign-post-' + post_id + '">' +
                        '<td><a href="' + post_link + '" target="_blank"><img src="' + post_thumbnail + '" width="70"></a></td>' +
                        '<td>' +
                        '<input type="hidden" class="campaign-footer-article" name="footer_article" value="' + post_id + '">' +
                        '<input type="text" name="footer_article_title" value="' + post_title + '"><br>' + 
                        '<textarea name="footer_article_excerpt">' + post_excerpt + '</textarea><br>' + 
                        '</td>' +
                        '<td><label class="remove remove-footer-article" data-id="' + post_id + '">x</label></td>' +
                        '</tr>'
                        );
                $('.create-campaign #add-footer-article').val('');
            }
        },
        minChars: 1
    });
    $(document).on('click', '.remove-footer-article' , function() {
        var post_id = parseInt( $(this).data('id') );
        campaign_posts = jQuery.grep(campaign_posts, function(value) {
            return value !== post_id;
        });
        $('#campaign-post-' + post_id).detach();
    });
    
    $(document).on('click', '.remove-all-posts', function() {
        var section = $(this).data('id');
        $('#' + section).find('.campaign-post').detach();
    });
    
    if ( $('.datepicker').length ) {
        $('.datepicker').datepicker( { dateFormat: 'dd M yy' } );
    }
    
    
    if ( $('#post').length ) {
        $('#post').submit(function() {
            if ( $('#post #post_type').length ) {
                if( $('#post #post_type').val() == 'gig' ) {
//                    alert( 'Please select the venue.' );
                    if ( $('input[name="p2p_connections[]"').length == 0 ) {
                        $('#p2p-from-gig_to_venue').prepend('<div style="color: #ff0000; padding: 10px;">Please select the venue.</div>');
                        $('#p2p-from-gig_to_venue').css('border', '2px solid #ff0000');
                        $('html, body').animate({scrollTop:$('#p2p-from-gig_to_venue').offset().top - 50}, 500);
                        return false;
                    }
                }
            }
        });
    }
    */
    
} );