jQuery(document).ready(function($) {
  $(".navbar-toggler").on("click", function(o) {
    $("#mobile-menu").toggleClass("expanded");
    $("#overlay").toggleClass("d-none");
    $("body").toggleClass("modal-open");
    $("#content").toggleClass("modal-open");
  });

  $(".l_toggle_menu_network").on("click", function(e) {
    e.preventDefault();
    $("#brands_wrap").height(
      $(window).height() - jQuery("#adm_leaderboard").height() - 24
    );
    $("#content").toggleClass("modal-open");
    $("body").toggleClass("modal-open");
    if ($("body").hasClass("modal-open")) {
      $("#menu-network").slideDown();
      $(this)
        .find("i")
        .removeClass("fa-caret-down")
        .addClass("fa-caret-up");
    } else {
      $("#menu-network").slideUp();

      $(this)
        .find("i")
        .removeClass("fa-caret-up")
        .addClass("fa-caret-down");
    }
  });

  $("#overlay").on("click", function() {
    $("#overlay").toggleClass("d-none");
    $("#menu-network").hide();
    $("body").removeClass("modal-open");
    $("#mobile-menu").removeClass("expanded");
  });

  $("#toggle-top-search").on("click", function(e) {
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
  });

  $("ul li.menu-item-has-children a[href=\\#]").on("click", function(e) {
    e.preventDefault();
    $(this)
      .parent()
      .find("ul")
      .toggle();
    $(this).toggleClass("expanded");
  });

  $("body").on("click", ".yt-lazy-load", function() {
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

  $(".l_video").on("click", function(e) {
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
  $("#tb-video-modal").on("hidden.bs.modal", function() {
    $("#tb-video-modal .modal-body").html("");
  });
  $(".datepicker").length &&
    $(".datepicker").datepicker({
      format: "dd M yyyy",
    });
  $("body").on("click", ".social-share-link", function(o) {
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

  var screen_width = screen.width;
  var body_width = $(body).outerWidth();

  $(window).on("resize", function() {
    // $('#main').css('paddingTop', $('#header').outerHeight() + 15 );
    // $('#main').css('paddingTop', $('#header').outerHeight());
    screen_width = screen.width;
    body_width = $(body).outerWidth();
    googletag.pubads().refresh([adslot_leaderboard]);
  });

  if (body_width <= 768) {
    setInterval(function() {
      googletag
        .pubads()
        .refresh([adslot_leaderboard, adslot_mobile_leaderboard]);
    }, 45000);
  }

  // if ( screen_width <= 576 ) {
  //if ( $(body).outerWidth() <= 576 )
  {
    var didScroll;
    var lastScrollTop = 0;
    var delta = 5;
    var navbarHeight = $("#header").outerHeight();

    setInterval(function() {
      if (didScroll) {
        hasScrolled();
        didScroll = false;
      }
    }, 250);

    function hasScrolled() {
      screen_width = screen.width;
      body_width = $(body).outerWidth();
      var st = $(this).scrollTop();

      // Make sure they scroll more than delta
      if (Math.abs(lastScrollTop - st) <= delta) return;

      // if (st > lastScrollTop && st > navbarHeight){
      if (st <= $("#header-wrap").outerHeight()) {
        $("#header").removeClass("fixed-top");
      } else {
        // Scroll Up
        if (st + $(window).height() < $(document).height()) {
          $("#header").addClass("fixed-top");
        }
      }
      lastScrollTop = st;
    }
  }

  if ($("#articles-wrap").length) {
    $("#articles-wrap").append(
      '<div class="load-more"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>'
    );
    var button = $("#articles-wrap .load-more");
    var loading = false;
    var scrollHandling = {
      allow: true,
      reallow: function() {
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

  var $news_stories = $(".single_story");
  var top_news_story = $.grep($news_stories, function(item) {
    return $(item).position().top <= winTop + 10;
  });
  var visible_news_story = $.grep($news_stories, function(item) {
    return (
      $(item).position().top <=
      winTop + $(window).height() - $("#header").outerHeight()
    );
  });

  var taboola_loaded = [];

  $(window).scroll(function() {
    winTop = $(this).scrollTop();

    didScroll = true;

    if (body_width <= 768) {
      if (winTop >= 600) {
        $(".sticky-ad-bottom").addClass("sticky-bottom");
      } else {
        $("#header").addClass("fixed-top");
        $(".sticky-ad-bottom").removeClass("sticky-bottom");
      }
    }

    if ($(".sticky-ad").length) {
      var stickyAdElem = $(".sticky-ad");
      var stickyAdPos = stickyAdElem.position();

      if ($(window).scrollTop() > stickyAdPos.top - 665) {
        stickyAdElem.addClass("stick");
        stickyAdElem.css("top", $("#header").outerHeight());
      } else {
        stickyAdElem.removeClass("stick");
      }
    }

    if ($(".single").length) {
      if ($("#articles-wrap").length && count_articles < 9) {
        if (!loading && scrollHandling.allow) {
          scrollHandling.allow = false;
          setTimeout(scrollHandling.reallow, scrollHandling.delay);
          var offset =
            $(button).offset().top -
            $(window).scrollTop() -
            $(window).outerHeight();

          if (1000 > offset) {
            loading = true;
            var data = {
              action: "tbm_ajax_load_next_post",
              exclude_posts: tbm_load_next_post.exclude_posts,
              id: tbm_load_next_post.current_post,
              count_articles: count_articles,
            };
            $.post(tbm_load_next_post.url, data, function(res) {
              if (res.success) {
                if (res.data.page_title != "undefined") {
                  count_articles++;
                }

                tbm_load_next_post.current_post = res.data.loaded_post;
                tbm_load_next_post.exclude_posts += "," + res.data.loaded_post;

                $("#articles-wrap").append(res.data.content);
                $("#articles-wrap").append(button);

                if ($(body).outerWidth() <= 768) {
                  googletag.cmd.push(function() {
                    googletag.pubads().enableLazyLoad({
                      fetchMarginPercent: 10,
                      renderMarginPercent: 5,
                    });
                    googletag.pubads().refresh([adslot_leaderboard]);
                    googletag
                      .pubads()
                      .addEventListener("slotRenderEnded", function(event) {
                        if (event.slot == adslot_leaderboard && event.isEmpty) {
                          var newScript = document.createElement("script");
                          var inlineScript = document.createTextNode(
                            "propertag.cmd.push(function() { proper_display('thebrag_sticky_1-" +
                              count_articles +
                              "'); });"
                          );
                          newScript.appendChild(inlineScript);

                          var slot_id = event.slot.getSlotElementId();
                          jQuery("#" + slot_id).show();
                          document.getElementById(slot_id).innerHTML =
                            '<div class="proper-ad-unit"><div id="proper-ad-thebrag_sticky_1-' +
                            count_articles +
                            '"></div></div>';
                          document
                            .getElementById(slot_id)
                            .appendChild(newScript);
                        }
                      });
                  });
                }

                loading = false;
              } else {
                button.remove();
              }
            }).fail(function(xhr, textStatus, e) {});
          }
        }
      } else {
        if (typeof button !== "undefined") {
          button.remove();
        }
      } // If $('#articles-wrap').length

      if (typeof button !== "undefined") {
        $news_stories = $(".single_story");
        top_news_story = $.grep($news_stories, function(item) {
          return $(item).position().top <= winTop + 100;
        });
        visible_news_story = $.grep($news_stories, function(item) {
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
          //                $('.progress-bar').css( { width: ( progress * 100) + "%" } );
        } else {
          //                $('.progress-bar').css( { transform: "scaleX(0)" } );
        }

        if (winTop <= 0) {
          progress = 0;
        }

        $(".progress-bar").css({ width: progress * 100 + "%" });

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

          if (!taboola_loaded[article_number] && article_number > 1) {
            taboola_loaded[article_number] = true;
            var taboola_mode = "alternating-thumbnails-a";
            var taboola_placement = "";
            if (2 == article_number) {
              taboola_mode = "thumbnails-a";
              taboola_placement = " " + article_number;
            }
            window._taboola = window._taboola || [];
            _taboola.push({
              mode: taboola_mode,
              container: "taboola-below-article-thumbnails" + article_number,
              placement: "Below Article Thumbnails" + taboola_placement,
              target_type: "mix",
            });
          }
        } // If visible_news_story.last().find('h1')
      } // If button exists
    } // If $('.single').length
  });

  $("body").on("click", ".btn-toggle-gig-search", function() {
    $(this).toggleClass("expanded");
    $(".gig-search-form").toggleClass("d-none");
  });
});
