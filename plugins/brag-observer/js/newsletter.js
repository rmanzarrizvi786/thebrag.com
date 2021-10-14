jQuery(document).ready(function($) {
  var campaign_posts = [];
  var article_links = [];
  var total_posts = 0;
  var max_post_id = 0;
  if ( $('.campaign-posts').length ) {
    total_posts = $('.campaign-posts').length;
    $('.campaign-posts').each(function() {
      var value = parseFloat($(this).data('id'));
      // alert( value );
      max_post_id = (value > max_post_id) ? value : max_post_id;
      article_links.push( $(this).closest('tr').data('url') );
    });
  }
  $('#total-posts').find('.total').text(total_posts);

  // Prevent Submit form
  $('.create-campaign').on('submit', function(e) {
    e.preventDefault();
  });

  // Save Campaign
  $('.create-campaign #submit-campaign').on('click', function() {
    var btn = $(this);
    var data = '';
    $(this).attr('disabled', true).parent().find('.status').addClass('alert-info').html(' Saving...');

    window.tinyMCE.triggerSave();

    var data = $('.create-campaign').serialize();
    $('#js-errors').addClass('hide').html('');



    $.post(
      observer.ajaxurl,
      {
        action: 'save_observer_newsletter',
        data: data
      },
      function(res){
        // console.log( res.success ); return;
        // res = $.parseJSON(response);
        if (res.success) {
          $('.create-campaign #submit-campaign').attr('disabled', false).parent().find('.status').removeClass('alert-danger').addClass('alert-success').html(' Saved, redirecting...');
          window.location = '?page=brag-observer-view-newsletter-list&list_id=' + $('#list_id').val();
        } else {
          var errors = '';
          $.each(res.data, function(index, error) {
            errors += '<div>' + error + '</div>';
          });
          $('.create-campaign #submit-campaign').parent().find('#js-errors').removeClass('hide').html(errors);
          $('.create-campaign #submit-campaign').attr('disabled', false).parent().find('.status').addClass('alert-danger').html(' Error Saving, please check the errors and try again.');
        }
      }
    ).fail(function(e) {
      btn.attr('disabled', false).parent().find('.status').addClass('alert-danger').html(' Failed to save. ' + e.statusText);
    }).error(function(e) {
      btn.attr('disabled', false).parent().find('.status').addClass('alert-success').html(' Error while saving. ' + e.statusText);
    });
  });

  $('.add-post-blank').on('click', function(e) {
    e.preventDefault();
    total_posts++;
    max_post_id++;
    $('#campaign-posts').append(
      '<tr id="campaign-post-blank-' + max_post_id + '" data-url="">' +
      '<td>' +
      '<input type="number" maxlength="2" min="1" class="campaign-posts" name="posts[' + max_post_id + ']" value="' + total_posts + '" size="2" data-id="' + max_post_id + '"><button class="remove remove-campaign-post btn btn-sm btn-outline-danger" data-id="' + max_post_id + '">x</button>' +

      '<td>' +

      '<div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text px-1 py-0">Link</div></div><input type="text" name="post_links[' + max_post_id + ']" id="post_' + max_post_id + '" value="" class="link_remote form-control"></div>' +

      '<div class="remote_content mb-4">' +

      '<div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text px-1 py-0">Title</div></div><input type="text" name="post_titles[' + max_post_id + ']" value="" class="title form-control"></div>' +

      '<div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text px-1 py-0">Blurb</div></div><textarea name="post_excerpts[' + max_post_id + ']" class="excerpt form-control"></textarea></div>' +

      '<div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text px-1 py-0"><img src="" width="50" class="image"></div></div><input type="text" name="post_images[' + max_post_id + ']" value="" class="image form-control"></div>' +
      '</div>' +
      '</td>' +
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

    article_links = jQuery.grep(article_links, function(value) {
      return value !== $('#campaign-post-blank-' + post_id).data('url');
    });
    console.log( article_links );

    $('#campaign-post-blank-' + post_id).detach();

    total_posts--;

    $('#total-posts').find('.total').text(total_posts);

    if ( $(this).data('url') ) {
      $('.select-post[data-url="' + $(this).data('url') + '"]').prop('disabled', false);
      // alert( $(this).data('url') );
    }
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

    var closest_tr = $(this).closest('tr');

    $(element).parent().next('.remote_content').addClass('hide');
    $(element).parent().next('.remote_content').find('.title, .excerpt, .image').val('');
    setTimeout(function () {

      if ( $.inArray( $(element).val(), article_links ) !== -1 ) {
        alert( 'The article is already added.' );
        $('#wait_msg').detach();
        return;
      }

      var data = 'url=' + $(element).val();
      $.post(
        observer.ajaxurl,
        {
          action: 'get_remote_data',
          data: data
        },
        function(response){
          res = $.parseJSON(response);
          if (res.success) {

            $(closest_tr).attr( 'data-url', $(element).val() );

            article_links.push( $(element).val() );

            $(element).parent().next('.remote_content').find('.title').val( res.title );
            $(element).parent().next('.remote_content').find('.excerpt').val( res.description );
            $(element).parent().next('.remote_content').find('input.image').val( res.image );
            $(element).parent().next('.remote_content').find('img.image').prop( 'src', res.image );
            // $(element).parent().parent().parent().find('td:first').find('img').remove();
            // $(element).parent().parent().parent().find('td:first').append('<img src="' + res.image + '" width="50">');
          } else {
            $('#wait_msg').text('Failed to fetch');
          }
          $(element).parent().next('.remote_content').removeClass('hide');
          $('#wait_msg').detach();
        }
      );
    }, 100);
  });

  $('.select-post').on('change', function() {
    if( $(this).prop('checked')){
      var article_index = $(this).val();
      var article_link = $('input[name="articles[' + article_index + '][\'link\']"]').val();
      var article_title = $('input[name="articles[' + article_index + '][\'title\']"]').val();
      var article_blurb = $('input[name="articles[' + article_index + '][\'blurb\']"]').val();
      var article_image = $('input[name="articles[' + article_index + '][\'image\']"]').val();

      if ( $.inArray( article_link, article_links ) !== -1 ) {
        alert( 'The article is already added.' );
        $(this).prop( 'checked', false );
        return;
      }

      article_links.push( article_link );

      total_posts++;
      max_post_id++;
      $('#campaign-posts').append(
        '<tr id="campaign-post-' + max_post_id + '" data-url="' + article_link + '">' +
        '<td>' +
        '<input type="number" maxlength="2" min="1" class="campaign-posts" name="posts[' + max_post_id + ']" value="' + total_posts + '" size="2" data-id="' + max_post_id + '">' +

        '<td>' +

        '<div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text px-1 py-0">Link</div></div><input type="text" name="post_links[' + max_post_id + ']" value="' + article_link + '" class="link_remote form-control"></div>' +

        '<div class="remote_content mb-4">' +

        '<div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text px-1 py-0">Title</div></div><input type="text" name="post_titles[' + max_post_id + ']" value="' + article_title + '" class="title form-control"></div>' +

        '<div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text px-1 py-0">Blurb</div></div><textarea name="post_excerpts[' + max_post_id + ']" class="excerpt form-control">' + article_blurb + '</textarea></div>' +

        '<div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text px-1 py-0"><img src="' + article_image + '" width="50"></div></div><input type="text" name="post_images[' + max_post_id + ']" value="' + article_image + '" class="image form-control"></div>' +
        '</div>' +
        '</td>' +
        '</tr>'
      );

      $('#total-posts').find('.total').text(total_posts);
    } else { // Unchecked
      /*
      var article_index = $(this).val();
      campaign_posts = jQuery.grep(campaign_posts, function(value) {
        return value !== article_index;
      });
      $('#campaign-post-' + article_index).detach();
      total_posts--;
      $('#total-posts').find('.total').text(total_posts);
      */

      if ( $(this).data('url') ) {
        var dataurl = $(this).data('url');
        $('#campaign-posts').find(`[data-url='${dataurl}']`).detach();
        total_posts--;
        $('#total-posts').find('.total').text(total_posts);

        article_links = jQuery.grep(article_links, function(value) {
          return value !== dataurl;
        });
      }
    }
  });

  $('#toggle-ad-4').on('click', function(e) {
    e.preventDefault();
    $('#ad-4-wrap').toggleClass('d-none');
  });

  // Save Solus
  $('.create-solus #submit-solus').on('click', function() {
    var btn = $(this);
    var data = '';
    $(this).attr('disabled', true).parent().find('.status').addClass('alert-info').html(' Saving...');
    var data = $('.create-solus').serialize();
    $('#js-errors').addClass('hide').html('');
    $.post(
      observer.ajaxurl,
      {
        action: 'save_observer_solus',
        data: data
      },
      function(res){
        // console.log( res.success ); return;
        // res = $.parseJSON(response);
        if (res.success) {
          $('.create-solus #submit-solus').attr('disabled', false).parent().find('.status').removeClass('alert-danger').addClass('alert-success').html(' Saved, redirecting...');
          window.location = '?page=brag-observer-view-solus-list&list_id=' + $('#list_id').val();
        } else {
          var errors = '';
          $.each(res.data, function(index, error) {
            errors += '<div>' + error + '</div>';
          });
          $('.create-solus #submit-solus').parent().find('#js-errors').removeClass('hide').html(errors);
          $('.create-solus #submit-solus').attr('disabled', false).parent().find('.status').addClass('alert-danger').html(' Error Saving, please check the errors and try again.');
        }
      }
    ).fail(function(e) {
      btn.attr('disabled', false).parent().find('.status').addClass('alert-danger').html(' Failed to save. ' + e.statusText);
    }).error(function(e) {
      btn.attr('disabled', false).parent().find('.status').addClass('alert-success').html(' Error while saving. ' + e.statusText);
    });
  });

  // Save Tastemaker
  $('.create-tastemaker #submit-tastemaker').on('click', function() {
    var btn = $(this);
    var data = '';
    $(this).attr('disabled', true).parent().find('.status').addClass('alert-info').html(' Saving...');
    var data = $('.create-tastemaker').serialize();
    $('#js-errors').addClass('hide').html('');
    $.post(
      observer.ajaxurl,
      {
        action: 'save_observer_tastemaker',
        data: data
      },
      function(res){
        if (res.success) {
          btn.attr('disabled', false).parent().find('.status').removeClass('alert-danger').addClass('alert-success').html(' Saved, redirecting...');
          window.location = '?page=brag-observer-view-tastemakers-list';
        } else {
          var errors = '';
          $.each(res.data, function(index, error) {
            errors += '<div>' + error + '</div>';
          });
          btn.parent().find('#js-errors').removeClass('hide').html(errors);
          btn.attr('disabled', false).parent().find('.status').addClass('alert-danger').html(' Error Saving, please check the errors and try again.');
        }
      }
    ).fail(function(e) {
      btn.attr('disabled', false).parent().find('.status').addClass('alert-danger').html(' Failed to save. ' + e.statusText);
    }).error(function(e) {
      btn.attr('disabled', false).parent().find('.status').addClass('alert-success').html(' Error while saving. ' + e.statusText);
    });
  });

  // Save Lead Generator
  $('.create-lead_generator #submit-lead_generator').on('click', function() {
    var btn = $(this);
    var data = '';
    $(this).attr('disabled', true).parent().find('.status').addClass('alert-info').html(' Saving...');
    var data = $('.create-lead_generator').serialize();
    $('#js-errors').addClass('hide').html('');
    $.post(
      observer.ajaxurl,
      {
        action: 'save_observer_lead_generator',
        data: data
      },
      function(res){
        if (res.success) {
          btn.attr('disabled', false).parent().find('.status').removeClass('alert-danger').addClass('alert-success').html(' Saved, redirecting...');
          window.location = '?page=brag-observer-view-lead_generators-list';
        } else {
          var errors = '';
          $.each(res.data, function(index, error) {
            errors += '<div>' + error + '</div>';
          });
          btn.parent().find('#js-errors').removeClass('hide').html(errors);
          btn.attr('disabled', false).parent().find('.status').addClass('alert-danger').html(' Error Saving, please check the errors and try again.');
        }
      }
    ).fail(function(e) {
      btn.attr('disabled', false).parent().find('.status').addClass('alert-danger').html(' Failed to save. ' + e.statusText);
    }).error(function(e) {
      btn.attr('disabled', false).parent().find('.status').addClass('alert-success').html(' Error while saving. ' + e.statusText);
    });
  });


});
