jQuery(document).ready(function($) {
    var campaign_posts = [];
    var total_posts = 0;
    var max_post_id = 0;
    if ( $('.campaign-posts').length ) {
        total_posts = $('.campaign-posts').length;
        $('.campaign-posts').each(function() {
          var value = parseFloat($(this).data('id'));
          max_post_id = (value > max_post_id) ? value : max_post_id;
        });
    }
    $('#total-posts').find('.total').text(total_posts);
    
    // Prevent Submit form
    $('.create-campaign').on('submit', function(e) {
        e.preventDefault();
    });
    // Save Campaign
    $('.create-campaign #submit-campaign').on('click', function() {
        $(this).attr('disabled', true).parent().find('.status').html(' Saving...');
        var data = $('.create-campaign').serialize();
        $('#td-mc-errors').addClass('hide').html('');
        $.post(
            ajaxurl,
            {
                action: 'save_campaign',
                data: data
            },
            function(response){
                res = $.parseJSON(response);
                if (res.success) {
                    window.location = '?page=brag-mailchimp/brag-mailchimp.php';
                    $('.create-campaign #submit-campaign').attr('disabled', false).parent().find('.status').html(' Saved.');
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
    $('.create-campaign #add-post').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: 'action=td_ajax_search&type=any&term='+name,
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
                    'data-excerpt="' + item[6] + '"' +
                    'data-thumbnail="' + item[5] + '"' +
                    'data-val="' + search + '">' +
                    item[1].replace(re, "<b>$1</b>") +
                    '</div>';
        },
        onSelect: function(e, term, item){
            var post_id = parseInt( item.data('id') );
            $('.create-campaign #add-post').next('div.error').text('').addClass('hide');
//            if ( $.inArray( post_id, campaign_posts ) === -1 )
            {
                campaign_posts.push( post_id );
                var post_title = item.data('title');
                var post_date = item.data('date');
                var post_excerpt = item.data('excerpt');
                var post_thumbnail = item.data('thumbnail');
                if ( post_thumbnail == 'null' || post_thumbnail == null )
                    post_thumbnail = '';
                var post_link = item.data('link');
                total_posts++;
                max_post_id++;
                $('#campaign-posts').append(
                        '<tr id="campaign-post-' + max_post_id + '">' +
                        '<td>' + 
                        '<input type="number" maxlength="2" min="1" class="campaign-posts" name="posts[' + max_post_id + ']" value="' + total_posts + '" size="2"><br>' +
                        '<a href="' + post_link + '" target="_blank"><img src="' + post_thumbnail + '" width="50"></a>' + 
                        '</td>' +
                        '<td>' + 
                        '<label>Link:<input type="text" name="post_links[' + max_post_id + ']" value="' + post_link + '"></label>' + 
                        '<label>Title:<input type="text" name="post_titles[' + max_post_id + ']" value="' + post_title + '"></label>' +
                        '<label>Blurb:<br><textarea name="post_excerpts[' + max_post_id + ']">' + post_excerpt + '</textarea></label>' +
                        '<label>Image:<br><input type="text" name="post_images[' + max_post_id + ']" value="' + post_thumbnail + '"></label>' +
                        '</td>' +
                        '<td><label class="remove remove-campaign-post" data-id="' + max_post_id + '">x</label></td>' +
                        '</tr>'
                        );
                $('#total-posts').find('.total').text(total_posts);
                $('.create-campaign #add-post').val('');
            }
//            else {
//                $('.create-campaign #add-post').next('div.error').removeClass('hide').text('Already in the list.');
//            }
        },
        minChars: 1
    });
    $('#add-post-blank').on('click', function(e) {
        e.preventDefault();
        total_posts++;
        max_post_id++;
        $('#campaign-posts').append(
                '<tr id="campaign-post-' + max_post_id + '">' +
                '<td>' + 
                '<input type="number" maxlength="2" min="1" class="campaign-posts" name="posts[' + max_post_id + ']" value="' + total_posts + '" size="2"><br>' +
                '<td>' + 
                '<label>Link:<input type="text" name="post_links[' + max_post_id + ']" id="post_' + max_post_id + '" value="" class="link_remote"></label>' + 
                '<div class="hide remote_content">' + 
                '<label>Title:<input type="text" name="post_titles[' + max_post_id + ']" class="title" value=""></label><br>' +
                '<label>Blurb:<br><textarea name="post_excerpts[' + max_post_id + ']" class="excerpt"></textarea></label><br>' +
                '<label>Image:<br><input type="text" name="post_images[' + max_post_id + ']" class="image" value=""></label>' +
                '</div>' + 
                '</td>' +
                '<td><label class="remove remove-campaign-post" data-id="' + max_post_id + '">x</label></td>' +
                '</tr>'
                );
        $('#post_' + max_post_id).focus();
        $('#total-posts').find('.total').text(total_posts);
    });
    $(document).on('click', '.remove-campaign-post' , function() {
        var post_id = parseInt( $(this).data('id') );
        campaign_posts = jQuery.grep(campaign_posts, function(value) {
            return value !== post_id;
        });
        $('#campaign-post-' + post_id).detach();
        total_posts--;
        $('#total-posts').find('.total').text(total_posts);
    });
    
    // AJAX Search for Campaign Cover Story
    $('.create-campaign #add-cover-story').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: 'action=td_ajax_search&type=any&term='+name,
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
                    'data-excerpt="' + item[6] + '"' +
                    'data-thumbnail="' + item[5] + '"' +
                    'data-val="' + search + '">' +
                    item[1].replace(re, "<b>$1</b>") +
                    '</div>';
        },
        onSelect: function(e, term, item){
            if ( $('.remove-cover-story').length) {
                alert ('You can add only one Cover Story, please remove the other one first.');
                return false;
            }
            var post_id = parseInt( item.data('id') );
//            if ( $.inArray( post_id, campaign_posts ) === -1 )
            {
                campaign_posts.push( post_id );
                var post_title = item.data('title');
                var post_date = item.data('date');
                var post_excerpt = item.data('excerpt');
                var post_thumbnail = item.data('thumbnail');
                var post_link = item.data('link');
                $('#campaign-cover-story').append(
                        '<tr id="cover_story_wrap">' +
                        '<td width="50"><a href="' + post_link + '" target="_blank"><img src="' + post_thumbnail + '" width="50"></a></td>' +
                        '<td>' +
                        '<label>Link:<input type="text" name="cover_story_link" value="' + post_link + '" class="link_remote"></label>' + 
                        '<div class="remote_content">' + 
                        '<label>Title:<input type="text" name="cover_story_title" value="' + post_title + '" class="title"></label><br>' +
                        '<label>Blurb:<br><textarea name="cover_story_excerpt" class="excerpt">' + post_excerpt + '</textarea></label><br>' +
                        '<label>Image:<br><input type="text" name="cover_story_image" value="' + post_thumbnail + '" class="image"></label>' +
                        '</div>' + 
                        '</td>' +
                        '<td><label class="remove remove-cover-story" data-id="cover_story_wrap">x</label></td>' +
                        '</tr>'
                        );
                $('.create-campaign #add-cover-story').val('');
            }
        },
        minChars: 1
    });
    $('#add-cover-story-blank').on('click', function(e) {
        e.preventDefault();
        if ( $('.remove-cover-story').length) {
            alert ('You can add only one Cover Story, please remove the other one first.');
            return false;
        }
        $('#campaign-cover-story').append(
            '<tr id="cover_story_wrap">' +
            '<td width="50">&nbsp;</td>' +
            '<td>' +
            '<label>Link:<input type="text" name="cover_story_link" id="cover_story_link" value="" class="link_remote"></label>' + 
            '<div class="hide remote_content">' + 
            '<label>Title:<input type="text" name="cover_story_title" value="" class="title"></label><br>' +
            '<label>Blurb:<br><textarea name="cover_story_excerpt" class="excerpt"></textarea></label><br>' +
            '<label>Image:<br><input type="text" name="cover_story_image" class="image" value=""></label>' +
            '</div>' + 
            '</td>' +
            '<td><label class="remove remove-cover-story" data-id="cover_story_wrap">x</label></td>' +
            '</tr>'
            );
        $('#cover_story_link').focus();
    });
    
    $('.create-campaign #add-featured-story-1').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: 'action=td_ajax_search&type=any&term='+name,
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
                    'data-excerpt="' + item[6] + '"' +
                    'data-thumbnail="' + item[5] + '"' +
                    'data-val="' + search + '">' +
                    item[1].replace(re, "<b>$1</b>") +
                    '</div>';
        },
        onSelect: function(e, term, item){
            if ( $('.remove-featured-story-1').length) {
                alert ('You can add only one Featured Story 1, please remove the other one first.');
                return false;
            }
            var post_id = parseInt( item.data('id') );
//            if ( $.inArray( post_id, campaign_posts ) === -1 )
            {
                campaign_posts.push( post_id );
                var post_title = item.data('title');
                var post_date = item.data('date');
                var post_excerpt = item.data('excerpt');
                var post_thumbnail = item.data('thumbnail');
                var post_link = item.data('link');
                $('#campaign-featured-story-1').append(
                        '<tr id="featured_story_wrap_1">' +
                        '<td width="50"><a href="' + post_link + '" target="_blank"><img src="' + post_thumbnail + '" width="50"></a></td>' +
                        '<td>' +
                        '<label>Link:<input type="text" name="featured_story_link_1" value="' + post_link + '" class="link_remote"></label>' + 
                        '<div class="remote_content">' + 
                        '<label>Title:<input type="text" name="featured_story_title_1" value="' + post_title + '" class="title"></label><br>' +
                        '<label>Blurb:<br><textarea name="featured_story_excerpt_1" class="excerpt">' + post_excerpt + '</textarea></label><br>' +
                        '<label>Image:<br><input type="text" name="featured_story_image_1" value="' + post_thumbnail + '" class="image"></label>' +
                        '</div>' + 
                        '</td>' +
                        '<td><label class="remove remove-featured-story-1" data-id="featured_story_wrap_1">x</label></td>' +
                        '</tr>'
                        );
                $('.create-campaign #add-featured-story-1').val('');
            }
        },
        minChars: 1
    });
    $('#add-featured-story-blank-1').on('click', function(e) {
        e.preventDefault();
        if ( $('.remove-featured-story-1').length) {
            alert ('You can add only one Featured Story 1, please remove the other one first.');
            return false;
        }
        $('#campaign-featured-story-1').append(
            '<tr id="featured_story_wrap_1">' +
            '<td width="50">&nbsp;</td>' +
            '<td class="data_remote">' +
            '<label>Link:<input type="text" name="featured_story_link_1" id="featured_story_link_1" value="" class="link_remote"></label>' + 
            '<div class="hide remote_content">' + 
            '<label>Title:<input type="text" name="featured_story_title_1" value="" class="title"></label><br>' +
            '<label>Blurb:<br><textarea name="featured_story_excerpt_1" class="excerpt"></textarea></label><br>' +
            '<label>Image:<br><input type="text" name="featured_story_image_1" value="" class="image"></label>' +
            '</div>' + 
            '</td>' +
            '<td><label class="remove remove-featured-story-1" data-id="featured_story_wrap_1">x</label></td>' +
            '</tr>'
            );
        $('#featured_story_link_1').focus();
    });
    
    $('.create-campaign #add-featured-story-2').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: 'action=td_ajax_search&type=any&term='+name,
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
                    'data-excerpt="' + item[6] + '"' +
                    'data-thumbnail="' + item[5] + '"' +
                    'data-val="' + search + '">' +
                    item[1].replace(re, "<b>$1</b>") +
                    '</div>';
        },
        onSelect: function(e, term, item){
            if ( $('.remove-featured-story-2').length) {
                alert ('You can add only one Featured Story 2, please remove the other one first.');
                return false;
            }
            var post_id = parseInt( item.data('id') );
//            if ( $.inArray( post_id, campaign_posts ) === -1 )
            {
                campaign_posts.push( post_id );
                var post_title = item.data('title');
                var post_date = item.data('date');
                var post_excerpt = item.data('excerpt');
                var post_thumbnail = item.data('thumbnail');
                var post_link = item.data('link');
                $('#campaign-featured-story-2').append(
                        '<tr id="featured_story_wrap_2">' +
                        '<td width="50"><a href="' + post_link + '" target="_blank"><img src="' + post_thumbnail + '" width="50"></a></td>' +
                        '<td>' +
                        '<label>Link:<input type="text" name="featured_story_link_2" value="' + post_link + '" class="link_remote"></label>' + 
                        '<div class="remote_content">' + 
                        '<label>Title:<input type="text" name="featured_story_title_2" value="' + post_title + '" class="title"></label><br>' +
                        '<label>Blurb:<br><textarea name="featured_story_excerpt_2" class="excerpt">' + post_excerpt + '</textarea></label><br>' +
                        '<label>Image:<br><input type="text" name="featured_story_image_2" value="' + post_thumbnail + '" class="image"></label>' +
                        '</div>' + 
                        '</td>' +
                        '<td><label class="remove remove-featured-story-2" data-id="featured_story_wrap_2">x</label></td>' +
                        '</tr>'
                        );
                $('.create-campaign #add-featured-story-2').val('');
            }
        },
        minChars: 1
    });
    $('#add-featured-story-blank-2').on('click', function(e) {
        e.preventDefault();
        if ( $('.remove-featured-story-2').length) {
            alert ('You can add only one Featured Story 2, please remove the other one first.');
            return false;
        }
        $('#campaign-featured-story-2').append(
            '<tr id="featured_story_wrap_2">' +
            '<td width="50">&nbsp;</td>' +
            '<td>' +
            '<label>Link:<input type="text" name="featured_story_link_2" id="featured_story_link_2" value="" class="link_remote"></label>' + 
            '<div class="hide remote_content">' + 
            '<label>Title:<input type="text" name="featured_story_title_2" class="title" value=""></label><br>' +
            '<label>Blurb:<br><textarea name="featured_story_excerpt_2" class="excerpt"></textarea></label><br>' +
            '<label>Image:<br><input type="text" name="featured_story_image_2" class="image" value=""></label>' +
            '</div>' + 
            '</td>' +
            '<td><label class="remove remove-featured-story-2" data-id="featured_story_wrap_2">x</label></td>' +
            '</tr>'
            );
        $('#featured_story_link_2').focus();
    });
    
    $('.create-campaign #add-featured-video').autoComplete({
        source: function(name, response) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: 'action=td_ajax_search&type=any&term=' + name + '&after=-2 weeks',
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
                    'data-title="' + item[1].replace(/&/g,'&amp;').replace(/>/g,'&gt;').replace(/</g,'&lt;').replace(/"/g,'&quot;') + '" ' +
                    'data-link="' + item[2] + '" ' +
                    'data-date="' + item[3] + '" ' +
//                    'data-excerpt="' + item[6].replace(/&/g,'&amp;').replace(/>/g,'&gt;').replace(/</g,'&lt;').replace(/"/g,'&quot;') + '" ' +
                    'data-thumbnail="' + item[5] + '"' +
                    'data-val="' + search + '">' +
                    item[1].replace(re, "<b>$1</b>") +
                    '</div>';
        },
        onSelect: function(e, term, item){
            if ( $('.remove-featured-video').length) {
                alert ('You can add only one Featured Video, please remove the other one first.');
                return false;
            }
            var post_id = parseInt( item.data('id') );
//            if ( $.inArray( post_id, campaign_posts ) === -1 )
            {
                campaign_posts.push( post_id );
                var post_title = item.data('title');
                var post_date = item.data('date');
                var post_excerpt = item.data('excerpt');
                var post_thumbnail = item.data('thumbnail');
                var post_link = item.data('link');
                $('#campaign-featured-video').append(
                        '<tr id="featured_video_wrap">' +
                        '<td width="50"><a href="' + post_link + '" target="_blank"><img src="' + post_thumbnail + '" width="50"></a></td>' +
                        '<td>' +
                        '<label>Link:<input type="text" name="edm_featured_video_link" value="' + post_link + '" class="link_remote"></label>' + 
                        '<div class="remote_content">' + 
                        '<label>Title:<input type="text" name="edm_featured_video_title" value="' + post_title + '" class="title"></label><br>' +
//                        '<label>Blurb:<br><textarea name="edm_featured_video_excerpt">' + post_excerpt + '</textarea></label>' +
                        '<label>Image:<br><input type="text" name="edm_featured_video_image" value="' + post_thumbnail + '" class="image"></label>' +
                        '</div>' + 
                        '</td>' +
                        '<td><label class="remove remove-featured-video" data-id="featured_video_wrap">x</label></td>' +
                        '</tr>'
                        );
                $('.create-campaign #add-featured-video').val('');
            }
        },
        minChars: 1
    });
    $('#add-featured-video-blank').on('click', function(e) {
        e.preventDefault();
        if ( $('.remove-featured-video').length) {
            alert ('You can add only one Featured Video, please remove the other one first.');
            return false;
        }
        $('#campaign-featured-video').append(
            '<tr id="featured_video_wrap">' +
            '<td width="50">&nbsp;</td>' +
            '<td>' +
            '<label>Link:<input type="text" name="edm_featured_video_link" id="edm_featured_video_link" value="" class="link_remote"></label>' + 
            '<div class="hide remote_content">' + 
            '<label>Title:<input type="text" name="edm_featured_video_title" value="" class="title"></label><br>' +
            '<label>Image:<br><input type="text" name="edm_featured_video_image" value="" class="image"></label>' +
            '</div>' + 
            '</td>' +
            '<td><label class="remove remove-featured-video" data-id="featured_video_wrap">x</label></td>' +
            '</tr>'
            );
        $('#edm_featured_video_link').focus();
    });
    
    $(document).on('click', '.remove-cover-story, .remove-featured-story-1, .remove-featured-story-2, .remove-featured-video' , function() {
        $( '#' + $(this).data('id') ).detach();
    });
    
    $(document).on('click', '.remove-all-posts', function() {
        var section = $(this).data('id');
        $('#' + section).find('.campaign-post').detach();
    });
    
    if ( $('.datepicker').length ) {
        $('.datepicker').datepicker( { dateFormat: 'dd M yy' } );
    }
    
    $(document).on('paste', '.link_remote', function(e) {
        var element = this;
        $(element).parent().append('<div id="wait_msg" style="padding: 5px 10px; background: #333; color: #fff;">Please wait...</div>');
        $(element).parent().next('.remote_content').addClass('hide');
        $(element).parent().next('.remote_content').find('.title, .excerpt, .image').val('');
        setTimeout(function () {
            var data = 'url=' + $(element).val();
            $.post(
                ajaxurl,
                {
                    action: 'get_remote_data',
                    data: data
                },
                function(response){
                    res = $.parseJSON(response);
                    if (res.success) {
                        $(element).parent().next('.remote_content').find('.title').val( res.title );
                        $(element).parent().next('.remote_content').find('.excerpt').val( res.description );
                        $(element).parent().next('.remote_content').find('.image').val( res.image );
                    } else {
                    }
                    $(element).parent().next('.remote_content').removeClass('hide');
                    $('#wait_msg').detach();
                }
            );
        }, 100);
    });
    
    // Save Campaign
    $('#save-edm-settings').on('click', function() {
        $(this).attr('disabled', true).parent().find('.status').html(' Saving...');
        var data = $('.create-campaign').serialize();
        $('#td-mc-errors').addClass('hide').html('');
        $.post(
            ajaxurl,
            {
                action: 'save_edm_settings',
                data: data
            },
            function(response){
                res = $.parseJSON(response);
                console.log( res );
                if (res.success) {
//                    window.location = '?page=brag-mailchimp/brag-mailchimp.php';
                    $('#save-edm-settings').attr('disabled', false).parent().find('.status').html(' Saved.');
                } else if (res.errors) {
                    var errors = '';
                    $.each(res.errors, function(index, error) {
                        errors += error + "\n";
                    });
                    $('#td-mc-errors').removeClass('hide').html(errors);
                    $('#save-edm-settings').attr('disabled', false).parent().find('.status').html(' Error Saving, please check the errors and try again.');
                }
            }
        );
    });
} );