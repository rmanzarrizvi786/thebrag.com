jQuery(document).ready(function($){

  // if ( $("#loginModal").length )
  //   $("#loginModal").modal( 'show' );

  $('.btn-login').on('click', function() {
    $('.modal').modal('hide');
    var btn = $(this);
    $("#loginModal").modal( 'show' );

    var apple_signin_elem = $("#loginModal").find('.wp-social-login-provider-apple');
    var apple_signin_url = apple_signin_elem.prop('href');
    apple_signin_elem.prop('href', apple_signin_url + '&state=' + $(btn).data('apple') );
  });

  $('#modal-form-login').on('submit', function(e) {
    e.preventDefault();
    var theForm = $(this);
    var formData = $(this).serialize();
    var loadingElem = $(this).find('.loading');
    var button = $(this).find('.btn-submit');
    $('.js-errors-login,.js-success-login,.js-msg-login').html('').addClass('d-none');
    loadingElem.show();
    button.hide();
    var data = {
      action: 'ajax_login',
      'username': theForm.find('input[name="username"]').val(),
      'password': theForm.find('input[name="password"]').val(),
      'security': theForm.find('#security').val()
    };
    $.post( brag_observer.url, data, function(res) {
      if( res.success ) {
        if ( 'success' == res.data.status) {
          theForm.find('.js-success-login').html('Login successful, redirecting...').removeClass('d-none');
          window.location.reload();
        } else if ( 'require_activation' == res.data.status ) {
          theForm.find('.js-msg-login').html(res.data.message).removeClass('d-none');
        }
        theForm.find('input[name="username"]').val('');
      } else {
        theForm.find('.js-errors-login').html(res.data).removeClass('d-none');
      }
      theForm.find('input[name="password"]').val('');
      loadingElem.hide();
      button.show();
    });
  });


  $('.btn-subscribe-observer').on('click', function() {
    var btn = $(this);
    $("#subscribeObserverModal").modal( 'show' );
    $("#subscribeObserverModal").find('#modal-title-subscribe').text( $(btn).data('topic') );
    $("#subscribeObserverModal").find('#modal-desc-subscribe').text( $(btn).data('desc') );
    $("#subscribeObserverModal").find('#modal-list-subscribe').val( $(btn).data('list') );

    var apple_signin_elem = $("#subscribeObserverModal").find('.wp-social-login-provider-apple');
    var apple_signin_url = apple_signin_elem.prop('href');
    apple_signin_elem.prop('href', apple_signin_url + '&state=' + $(btn).data('apple') );

    var data = {
      action: 'set_subscribe_to_list_pending',
      formData: 'list_id=' + $(btn).data('list')
    };
    $.post( brag_observer.url, data, function(res) {});
  });

  $('.btn-vote-observer').on('click', function() {
    var btn = $(this);
    $("#voteObserverModal").modal( 'show' );
    $("#voteObserverModal").find('#modal-title-vote').text( $(btn).data('topic') );
    $("#voteObserverModal").find('#modal-desc-vote').text( $(btn).data('desc') );
    $("#voteObserverModal").find('#modal-list-vote').val( $(btn).data('list') );
    // alert( $(btn).data('votes') );
    $("#voteObserverModal").find('.progress-bar').css( 'width', parseInt( $(btn).data('votes') ) * 100 / 1000 + '%' );
    $("#voteObserverModal").find('#votes_count').text( $(btn).data('votes') );

    var apple_signin_elem = $("#voteObserverModal").find('.wp-social-login-provider-apple');
    var apple_signin_url = apple_signin_elem.prop('href');
    apple_signin_elem.prop('href', apple_signin_url + '&state=' + $(btn).data('apple') );

    var data = {
      action: 'set_apple_redirect_url',
      formData: 'list_id=' + $(btn).data('list')
    };
    $.post( brag_observer.url, data, function(res) {});

  });

  var btn_actions = [
    {
      elem: $('.btn-subscribe-observer-l'),
      action: 'subscribe_observer',
      success_text: 'Subscribed',
      redirect: false
    },
    {
      elem: $('.btn-vote-observer-l'),
      action: 'vote_observer',
      success_text: 'Voted',
      redirect: false
    }
  ];

  $.each( btn_actions, function(i, v) {
    $(v.elem).on('click', function(e) {
      e.preventDefault();
      var btn = $(this);
      btn.removeClass('d-flex').addClass('d-none');
      btn.next('.loading').show();
      btn.prop( 'disabled', true );
      var data = {
        action: v.action,
        formData: 'list=' + $(this).data('list')
      };
      $.post( brag_observer.url, data, function(res) {
        btn.addClass('d-flex').removeClass('d-none');
        btn.next('.loading').hide();
        if( res.success ) {
          btn.removeClass('btn-subscribe-observer-l').addClass('btn-default');
          btn.find('.btn-text').text(v.success_text);
          if ( v.redirect ) {
            window.location.href = "/observer-subscriptions/";
          }
          if ( 'vote_observer' == v.action ) {
            var votes_count = parseInt( btn.parent().find('.votes_count').text() ) + 1;
            btn.parent().find('.votes_count').text(votes_count);
            var votes_target = parseInt( btn.parent().find('.votes_target').text() );
            btn.parent().find('.progress-bar').css( 'width', votes_count * 100 / votes_target + '%' );
          }
        } else {
          btn.prop( 'disabled', false );
        }
      });
    });
  });

  $("#subscribeObserverModal").on('show.bs.modal', function(e){
    var btn = e.relatedTarget;
  });

  $('#observer-subscribe-form,#observer-subscribe-form2').on('submit', function(e) {
    e.preventDefault();
    var theForm = $(this);
    var formData = $(this).serialize();
    var loadingElem = $(this).find('.loading');
    var button = $(this).find('.button');
    $('.js-errors-subscribe,.js-msg-subscribe').html('').addClass('d-none');
    loadingElem.show();
    button.hide();
    var data = {
      action: 'subscribe_observer',
      nonce: $('#brag-observer-nonce').val(),
      formData: formData
    };
    $.post( brag_observer.url, data, function(res) {
      if( res.success ) {
        theForm.find('.js-msg-subscribe').html('You will need to confirm your email address in order to activate your account. An email containing the activation link has been sent to your email address. If the email does not arrive within a few minutes, check your spam folder.').removeClass('d-none');
        // window.location.reload();
      } else {
        if ( 'email_invalid' == res.data ) {
          theForm.find('.js-errors-subscribe').html('Please input valid email address.').removeClass('d-none');
        } else if ( 'email_exists' == res.data ) {
          theForm.find('.js-errors-subscribe').html('User account already exists with the provided email address, please <a href="/observer/?login">login</a> to continue').removeClass('d-none');
        } else if ( 'birthday_invalid' == res.data ) {
          theForm.find('.js-errors-subscribe').html('Please input valid birthday.').removeClass('d-none');
        } else if ( 'state_invalid' == res.data ) {
          theForm.find('.js-errors-subscribe').html('Please select your state.').removeClass('d-none');
        } else {
          theForm.find('.js-errors-subscribe').html(res.data).removeClass('d-none');
        }
      }
      loadingElem.hide();
      button.show();
    });
  });

  $('#observer-vote-form,#observer-vote-form2').on('submit', function(e) {
    e.preventDefault();
    var theForm = $(this);
    var formData = $(this).serialize();
    var loadingElem = $(this).find('.loading');
    var button = $(this).find('.button');
    $('.js-errors-vote,.js-msg-vote').html('').addClass('d-none');
    loadingElem.show();
    button.hide();
    var data = {
      action: 'vote_observer',
      nonce: $('#brag-observer-nonce').val(),
      formData: formData
    };
    $.post( brag_observer.url, data, function(res) {
      if( res.success ) {
        theForm.find('.js-msg-vote').html('You will need to confirm your email address in order to activate your account. An email containing the activation link has been sent to your email address. If the email does not arrive within a few minutes, check your spam folder.').removeClass('d-none');
        // window.location.reload();
      } else {
        if ( 'email_invalid' == res.data ) {
          theForm.find('.js-errors-vote').html('Please input valid email address.').removeClass('d-none');
        } else if ( 'email_exists' == res.data ) {
          theForm.find('.js-errors-vote').html('User account already exists with the provided email address, please <a href="/observer/?login">login</a> to continue').removeClass('d-none');
        } else if ( 'birthday_invalid' == res.data ) {
          theForm.find('.js-errors-vote').html('Please input valid birthday.').removeClass('d-none');
        } else if ( 'state_invalid' == res.data ) {
          theForm.find('.js-errors-vote').html('Please select your state.').removeClass('d-none');
        }
      }
      loadingElem.hide();
      button.show();
    });
  });

  $('.btn-share').on('click', function(e) {
    e.preventDefault();
    popupCenter( { url: $(this).prop('href'), title: '', w: 500, h: 500 })
  });

  $('.checkbox-list').on('change', function() {
    if ( $(this).prop('checked' ) ) {
      var status = 'subscribed';
      $(this).closest('.sub-unsub').removeClass('unsubscribed').addClass('subscribed');
    } else {
      var status = 'unsubscribed';
      $(this).closest('.sub-unsub').removeClass('subscribed').addClass('unsubscribed');
    }
    var checkbox = $(this);
    checkbox.addClass('d-none');
    checkbox.next('.loading').show();
    checkbox.prop( 'disabled', true );
    var data = {
      action: 'subscribe_observer',
      formData: 'list=' + $(this).val() + '&status=' + status
    };
    $.post( brag_observer.url, data, function(res) {
      checkbox.removeClass('d-none');
      checkbox.next('.loading').hide();
      checkbox.prop( 'disabled', false );
    });
  });

  // Tastemakers
  $(document).on('submit', '.tastemaker-form', function(e) {
      e.preventDefault();
      var this_form = $(this);
      var btn = $(this).find('.tastemaker-submit');

      var elem_loading = this_form.find('.loading');
      var elem_js_errors = this_form.find('.js-errors');
      var elem_js_success = this_form.find('.js-success');
      var elem_tastemaker_wrap = this_form.find('.tastemaker-wrap');
      elem_loading.show();
      elem_js_errors.addClass('d-none').html('');
      btn.attr('disabled', true).hide();

      var formData = this_form.serialize();
      var the_url = this_form.closest('.single_story').find('h1:first').data('href');
      formData += '&source=' + the_url;

      js_errors = '';
      if ( this_form.find('input[name="rating"]:checked').length == 0 ) {
        js_errors += 'Please select a valid rating from 1 to 5 stars.<br>';
      }
      if ( this_form.find('input[name="email"]').length && this_form.find('input[name="email"]').val() == '' ) {
        js_errors += 'Please enter a valid email address.';
      }
      if( '' != js_errors ){
        btn.attr('disabled', false).show();
        elem_loading.hide();
        elem_js_errors.removeClass('d-none').addClass('alert alert-danger').html( js_errors );
        return false;
      }

      $.post(
        brag_observer.url,
        {
          action: 'save_tastemaker_review',
          formData: formData
        },
        function(res){
          elem_loading.hide();
          if (res.success) {
            elem_tastemaker_wrap.hide();
            if (res.data.verified ) {
              elem_js_success.addClass('alert alert-success').html(res.data.message);
            } else {
              elem_js_success.addClass('alert alert-info').html(res.data.message);
            }
          } else {
            var errors = '';
            $.each(res.data, function(index, error) {
              errors += '<div>' + error + '</div>';
            });
            btn.attr('disabled', false).show();
            elem_js_errors.removeClass('d-none').addClass('alert alert-danger').html(errors);
          }
        }
      ).fail(function(e) {
        btn.attr('disabled', false).show();
        elem_loading.hide();
        elem_js_errors.removeClass('d-none').addClass('alert alert-danger').html( 'Failed to save.');
      }).error(function(e) {
        btn.attr('disabled', false).show();
        elem_loading.hide();
        elem_js_errors.removeClass('d-none').addClass('alert alert-danger').html( 'Error while saving.');
      });
    });

  // Lead Generation
  $(document).on('submit', '.lead_generator-form', function(e) {
    e.preventDefault();
    var this_form = $(this);
    var btn = $(this).find('.lead_generator-submit');

    var elem_loading = this_form.find('.loading');
    var elem_js_errors = this_form.find('.js-errors');
    var elem_js_success = this_form.find('.js-success');
    var elem_lead_generator_wrap = this_form.find('.lead_generator-wrap');
    elem_loading.show();
    elem_js_errors.addClass('d-none').html('');
    btn.attr('disabled', true).hide();

    var formData = this_form.serialize();
    var the_url = this_form.closest('.single_story').find('h1:first').data('href');
    formData += '&source=' + the_url;

    js_errors = '';
    if ( this_form.find('input[name="email"]').length && this_form.find('input[name="email"]').val() == '' ) {
      js_errors += 'Please enter a valid email address.';
    }
    if( '' != js_errors ){
      btn.attr('disabled', false).show();
      elem_loading.hide();
      elem_js_errors.removeClass('d-none').addClass('alert alert-danger').html( js_errors );
      return false;
    }

    $.post(
      brag_observer.url,
      {
        action: 'save_lead_generator_review',
        formData: formData
      },
      function(res){
        elem_loading.hide();
        if (res.success) {
          elem_lead_generator_wrap.hide();
          if (res.data.verified ) {
            elem_js_success.addClass('alert alert-success').html(res.data.message);
          } else {
            elem_js_success.addClass('alert alert-info').html(res.data.message);
          }
        } else {
          var errors = '';
          $.each(res.data, function(index, error) {
            errors += '<div>' + error + '</div>';
          });
          btn.attr('disabled', false).show();
          elem_js_errors.removeClass('d-none').addClass('alert alert-danger').html(errors);
        }
      }
    ).fail(function(e) {
      btn.attr('disabled', false).show();
      elem_loading.hide();
      elem_js_errors.removeClass('d-none').addClass('alert alert-danger').html( 'Failed to save.');
    }).error(function(e) {
      btn.attr('disabled', false).show();
      elem_loading.hide();
      elem_js_errors.removeClass('d-none').addClass('alert alert-danger').html( 'Error while saving.');
    });
  });

  
});


const popupCenter = ({url, title, w, h}) => {
    // Fixes dual-screen position                             Most browsers      Firefox
    const dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
    const dualScreenTop = window.screenTop !==  undefined   ? window.screenTop  : window.screenY;

    const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    const systemZoom = width / window.screen.availWidth;
    const left = (width - w) / 2 / systemZoom + dualScreenLeft
    const top = (height - h) / 2 / systemZoom + dualScreenTop
    const newWindow = window.open(url, title,
      `
      scrollbars=yes,
      width=${w / systemZoom},
      height=${h / systemZoom},
      top=${top},
      left=${left}
      `
    )

    if (window.focus) newWindow.focus();
}
