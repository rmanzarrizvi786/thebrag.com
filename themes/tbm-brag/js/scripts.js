jQuery(document).ready(function ($) {
  /* $("#toggle-top-search").on("click", function(e) {
    $("#searchform-wrap").toggleClass("expanded");
    if ($("#searchform-wrap").hasClass("expanded")) {
      $(this)
        .find("i")
        .removeClass("fa-search")
        .addClass("fa-times");
      $("#searchform-wrap")
        .find("input.search-field")
        .focus();
    } else {
      $(this)
        .find("i")
        .removeClass("fa-times")
        .addClass("fa-search");
    }
  }); */

  /* $("ul li.menu-item-has-children a[href=\\#]").on("click", function(e) {
    e.preventDefault();
    $(this)
      .parent()
      .find("ul")
      .toggle();
    $(this).toggleClass("expanded");
  });
 */

  $('.btn-toggle-slidedown').on('click', function () {
    if ($(this).data('target')) {
      var target = $('#' + $(this).data('target'));
      if (target.hasClass('slidedown-active')) {
        target.removeClass('slidedown-active');
        $('#overlay').addClass('d-none');
        $('body').removeClass('modal-open');
        target.slideUp();
      } else {
        $('.slidedown-active').removeClass('slidedown-active').hide();
        target.slideDown().addClass('slidedown-active');
        $('#overlay').removeClass('d-none');
        $('body').addClass('modal-open');
      }
    }
  });

  $('.btn-toggle-network-mobile').on('click', function () {
    $(this).toggleClass('active');
    $('#network-mobile').slideToggle();
    $('#search-nav-wrap').slideToggle();
  });

  $('#overlay').on('click', function () {
    $(this).addClass('d-none');
    $('body').removeClass('modal-open');
    $('.slidedown-active').removeClass('slidedown-active').hide(); // .slideUp();
    // $('.nav-wrap').addClass('d-none');
    $('.nav-wrap').removeClass('active');
    $('.modal').hide();
  })

  $('.btn-toggle-menu').on('click', function () {
    $('.nav-wrap').addClass('active');
    $('#overlay').removeClass('d-none');
    $('body').addClass('modal-open');
  });

  $('.btn-close-menu').on('click', function () {
    $('.nav-wrap').removeClass('active');
    $('#overlay').addClass('d-none');
    $('body').removeClass('modal-open');
  });

  $('[data-toggle="modal"]').on('click', function (e) {
    e.preventDefault();
    var target = $($(this).data('target'));
    $('body').addClass('modal-open');
    $('#overlay').removeClass('d-none');
    target.fadeIn();
  });
  $('.modal .close').on('click', function (e) {
    e.preventDefault();
    var target = $(this).closest('.modal');
    target.fadeOut();
    $('#overlay').addClass('d-none');
    $('body').removeClass('modal-open');
  });

  $('.btn-open-top-search').on('click', function () {
    $('#top-search-wrap').addClass('active');
  });
  $('.btn-close-top-search').on('click', function () {
    $('#top-search-wrap').removeClass('active');
  });



  $('.observer-list .topics-active a').on('click', function (e) {
    e.preventDefault();
    var btn = $(this);

    if (!btn.hasClass('subscribed')) {
      var status = 'subscribed';
    } else {
      var status = 'unsubscribed';
    }

    var list = $(this).data('list');

    var data = {
      action: 'subscribe_observer',
      formData: 'list=' + list + '&status=' + status
    };
    $.post(brag_observer.url, data, function (res) {
      if (res.success) {
        // btn.toggleClass('subscribed');
        $('a[data-list=' + list + ']').toggleClass('subscribed');
      } else {
        // btn.prop('disabled', false);
      }
    });
  });

  $("body").on("click", ".yt-lazy-load", function () {
    var video_id = $(this).data("id");
    var player_id = $(this).prop("id");
    var player_height = $(this).height();

    var player;
    player = new YT.Player(player_id, {
      height: player_height,
      videoId: video_id,
      events: {
        onReady: onPlayerReady,
      },
    });
    function onPlayerReady(event) {
      event.target.playVideo();
    }
  });

  $(".l_video").on("click", function (e) {
    e.preventDefault();
    var yt = $(this).data("youtube");
    $("#tb-video-modal .modal-body").html("");
    var i =
      '<iframe width="560" height="349" src="http://www.youtube.com/embed/' +
      yt +
      '?rel=0&autoplay=1&color=white" frameborder="0" allowfullscreen ></iframe>';
    $("#tb-video-modal .modal-body").html(i),
      $("#tb-video-modal").modal("show");
  });
  $("#tb-video-modal").on("hidden.bs.modal", function () {
    $("#tb-video-modal .modal-body").html("");
  });
  $(".datepicker").length &&
    $(".datepicker").datepicker({
      format: "dd M yyyy",
    });
  $("body").on("click", ".social-share-link", function (o) {
    return (
      o.preventDefault(),
      window.open(
        $(this).attr("href"),
        $(this).data("type"),
        "height=450, width=550, top=" +
        ($(window).height() / 2 - 225) +
        ", left=" +
        ($(window).width() / 2 - 275) +
        ", toolbar=0, location=0, menubar=0, directories=0, scrollbars=0"
      ),
      !1
    );
  });

  if ($("#articles-wrap").length) {
    $("#articles-wrap").append(
      '<div class="load-more">Loading...</div>'
    );
    var button = $("#articles-wrap .load-more");
    var loading = false;
    var scrollHandling = {
      allow: true,
      reallow: function () {
        scrollHandling.allow = true;
      },
      delay: 400,
    };
    var count_articles = 2;
  }

  var progress_top = 0;

  var winTop = $(window).scrollTop();
  var page_title = document.title;
  var page_url =
    document.location.protocol +
    "//" +
    document.location.host +
    document.location.pathname;

  var $news_stories = $(".single-article");
  var top_news_story = $.grep($news_stories, function (item) {
    return $(item).position().top <= winTop + 10;
  });
  var visible_news_story = $.grep($news_stories, function (item) {
    return (
      $(item).position().top <=
      winTop + $(window).height() - $("#header").outerHeight()
    );
  });

  var winHeight = $(window).height();

  $(window).scroll(function () {
    winTop = $(this).scrollTop();

    /* if (winTop >= $('header').outerHeight()) {
      $('#skin').addClass('fixed');
    } else {
      $('#skin').removeClass('fixed');
    } */

    var mainTop = $('main').length ? $('main').offset().top : 0;
    var billboardHeight = $('.ad-billboard').length ? $('.ad-billboard').height() : 0;
    if (winTop >= mainTop + billboardHeight) {
      $('.sticky-ad-bottom').show();
    } else {
      $('.sticky-ad-bottom').hide();
    }

    if ($(".single").length) {
      if ($("#articles-wrap").length && count_articles < 4) {
        if (!loading && scrollHandling.allow) {
          scrollHandling.allow = false;
          setTimeout(scrollHandling.reallow, scrollHandling.delay);
          var offset =
            $(button).offset().top -
            $(window).scrollTop() -
            $(window).outerHeight();

          if (winHeight * 1.5 > offset) {
            loading = true;
            var data = {
              action: "tbm_ajax_load_next_post",
              exclude_posts: tbm_load_next_post.exclude_posts,
              id: tbm_load_next_post.current_post,
              count_articles: count_articles,
            };
            $.post(tbm_load_next_post.url, data, function (res) {
              if (res.success) {
                if (res.data.page_title != "undefined") {
                  count_articles++;
                }

                tbm_load_next_post.current_post = res.data.loaded_post;
                tbm_load_next_post.exclude_posts += "," + res.data.loaded_post;

                $("#articles-wrap").append(res.data.content);
                $("#articles-wrap").append(button);

                fusetag.setTargeting("fuse_category", [
                  "'" + res.data.category + "'",
                ]);

                var v = fusetag.getAdSlotsById('22339226185')[0].getResponseInformation();
                if (typeof v.lineItemId != "undefined" && v.lineItemId == "5709731975") {
                  fusetag.setTargeting("pos", [
                    "1",
                  ]);
                } else {
                  fusetag.setTargeting("pagepath", [
                    "'" + res.data.pagepath + "'",
                  ]);
                }

                loading = false;
              } else {
                button.remove();
              }
            }).fail(function (xhr, textStatus, e) { });
          }
        }
      } else {
        if (typeof button !== "undefined") {
          button.remove();
        }
      } // If $('#articles-wrap').length

      if (typeof button !== "undefined") {
        $news_stories = $(".single-article");
        top_news_story = $.grep($news_stories, function (item) {
          return $(item).position().top <= winTop + 100;
        });
        visible_news_story = $.grep($news_stories, function (item) {
          return $(item).position().top <= winTop + $(window).height() / 2; // + $('#header').outerHeight() - 30;
        });
        if (
          $(visible_news_story)
            .last()
            .prop("id") != ""
        ) {
          progress_top =
            $(visible_news_story)
              .last()
              .offset().top +
            $(visible_news_story)
              .last()
              .outerHeight() -
            30; // - $(window).height() / 2;

          var progress =
            1 -
            (progress_top - winTop - $(window).height()) /
            $(visible_news_story)
              .last()
              .outerHeight();
          progress < 0 ? (progress = 0) : progress > 1 && (progress = 1);
        }
        if (
          $(visible_news_story)
            .last()
            .find("h1")
            .text() != "" &&
          page_url !=
          $(visible_news_story)
            .last()
            .find("h1")
            .data("href")
        ) {
          page_title_html = $(visible_news_story)
            .last()
            .find("h1")
            .data("title");
          page_title = $("<textarea />")
            .html(page_title_html)
            .text();
          page_url = $(visible_news_story)
            .last()
            .find("h1")
            .data("href");

          var author = $(visible_news_story)
            .last()
            .find(".author")
            .data("author");
          var cats = $(visible_news_story)
            .last()
            .find(".cats")
            .data("category");
          var tags = $(visible_news_story)
            .last()
            .find(".cats")
            .data("tags");
          var pubdate = $(visible_news_story)
            .last()
            .find("time")
            .data("pubdate");
          window.dataLayer = window.dataLayer || [];
          window.dataLayer.push({
            AuthorCD: author,
            CategoryCD: cats,
            TagsCD: tags,
            PubdateCD: pubdate,
          });

          document.title = page_title;
          window.history.pushState(null, page_title, page_url);

          article_number = $(visible_news_story)
            .last()
            .find("h1")
            .data("article-number");
        } // If visible_news_story.last().find('h1')
      } // If button exists

      if (
        $(visible_news_story)
          .last()
          .find(".observer-sub-form").length
      ) {
        var elemSubForm = $(visible_news_story)
          .last()
          .find(".observer-sub-form");

        if (elemSubForm.closest("blockquote").length > 0) {
          elemSubForm.detach();
        }
        var top_of_form = elemSubForm.offset().top;
        var bottom_of_form =
          elemSubForm.offset().top + elemSubForm.outerHeight();
        var bottom_of_screen = $(window).scrollTop() + $(window).innerHeight();
        var top_of_screen = $(window).scrollTop();

        if (
          top_of_screen < bottom_of_form - $(window).height() / 2 &&
          bottom_of_screen > top_of_form + $(window).height() / 2
        ) {
          elemSubForm
            .closest(".single-article")
            .find(".overlay")
            .first()
            .fadeIn();
        } else {
          elemSubForm
            .closest(".single-article")
            .find(".overlay")
            .first()
            .fadeOut();
        }
      }
      if ($(".single-article .overlay").length) {
        $(".single-article .overlay").on("click", function () {
          $(this).remove();
        });
      }
    } // If $('.single').length
  });

  if ($('.btn-join').length) {
    $(document).on('click', '.btn-join', function () {
      if ($(window).width() < 768) {
        $(this)
          .closest(".observer-sub-form")
          .find(".img-wrap")
          .first()
          .hide();
      }
      var theForm = $(this).next('form.observer-subscribe-form');
      theForm.removeClass('d-none');
      theForm.find('input[name="email"]').focus();
      $(this).remove();
    })
  }
  if ($('.observer-subscribe-form').length) {
    $(document).on('submit', '.observer-subscribe-form', function (e) {
      e.preventDefault();
      var theForm = $(this);

      const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

      if (theForm.find('input[name="email"]').length &&
        (
          theForm.find('input[name="email"]').val() == '' ||
          !re.test(String(theForm.find('input[name="email"]').val().toLowerCase()))
        )) {
        theForm.parent().find('.js-errors-subscribe').html('Please enter a valid email address.').removeClass('d-none');
        return false;
      }

      var formData = $(this).serialize();
      var loadingElem = $(this).find('.loading');
      var button = $(this).find('.button');

      var the_url = theForm.closest('.single_story').find('h1:first').data('href');
      formData += '&source=' + the_url;

      $('.js-errors-subscribe,.js-msg-subscribe').html('').addClass('d-none');
      loadingElem.show();
      button.hide();
      var data = {
        action: 'subscribe_observer_category',
        formData: formData
      };
      $.post(tbm_load_next_post.url, data, function (res) {
        if (res.success) {
          theForm.parent().find('.js-msg-subscribe').html(res.data.message).removeClass('d-none');
          theForm.hide();
        } else {
          theForm.parent().find('.js-errors-subscribe').html(res.data.error.message).removeClass('d-none');
          button.show();
        }
        loadingElem.hide();
      }).error(function () {
        theForm.parent().find('.js-errors-subscribe').html('Something went wrong, please try again later').removeClass('d-none');
        loadingElem.hide();
        button.show();
      });
    });
  }
});
