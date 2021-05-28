</div><!-- /#content -->

</div><!-- /#main.wrap -->

<div id="footer" class="pb-3 pt-3 mt-5 container-fluid">
  <div class="row align-items-center">
    <div id="footer-frequency-details" class="col-md-6">
      <div class="mt-3 mb-3 text-left">
        <div class="d-flex flex-column">
          <div class="my-2"><strong class="p-1 bg-danger">Breaking news</strong> as it happens, in real time</div>
          <div class="my-2"><strong class="p-1 bg-danger">Daily</strong> every day of the week</div>
          <div class="my-2"><strong class="p-1 bg-danger">Weekly</strong> once a week</div>
          <div class="my-2"><strong class="p-1 bg-danger">Fortnightly</strong> once every two weeks</div>
        </div>
      </div>
    </div>

    <div id="footer-copyright" class="col-md-6 mt-3 text-md-right text-center">
      <p>
        <a href="https://thebrag.com/media/" target="_blank">
          <img src="https://thebrag.com/media/wp-content/themes/bragm/images/TheBragMedia_LOGO_v7_white@100x.png" alt="The Brag" class="img-fluid" style="max-width: 300px;">
        </a>
      </p>
      <p>&copy; Copyright <?php echo date("Y"); ?><br />All Rights Reserved.</p>
      <ul id="footer-socials" class="nav flex-row justify-content-center justify-content-md-end">
        <li class="nav-item"><a target="_blank" href="https://www.facebook.com/thebragmag" class="nav-link px-2 text-light"><i class="fab fa-facebook-f fa-lg" aria-hidden=true></i></a></li>
        <li class="nav-item"><a target="_blank" href="https://twitter.com/TheBrag" class="nav-link px-2 text-light"><i class="fab fa-twitter fa-lg" aria-hidden=true></i></a></li>
        <li class="nav-item"><a target="_blank" href="https://www.instagram.com/thebragmag/" class="nav-link px-2 text-light"><i class="fab fa-instagram fa-lg" aria-hidden=true></i></a></li>
        <li class="nav-item"><a target="_blank" href="https://www.youtube.com/channel/UCcZMmtU74qKN_w4Dd8ZkV6g" class="nav-link px-2 text-light"><i class="fab fa-youtube fa-lg" aria-hidden=true></i></a></li>
      </ul>

      <ul class="nav flex-column flex-md-row justify-content-end">
        <li class="nav-item">
          <a target="_blank" title="Privacy policy" href="https://thebrag.com/media/privacy-policy/" class="nav-link text-light">Privacy policy</a>
        </li>
        <li class="nav-item">
          <a target="_blank" title="Terms and conditions" href="https://thebrag.com/media/terms-and-conditions/" target="_blank" class="nav-link text-light">Terms &amp; conditions</a>
        </li>
        <li class="nav-item">
          <a target="_blank" title="Contact us" href="mailto:observer@thebrag.media" target="_blank" class="nav-link text-light">Contact us</a>
        </li>
        <li class="nav-item">
          <a target="_blank" title="Advertise with us" href="https://thebrag.com/media/" target="_blank" class="nav-link text-light pr-md-2">Advertise with us</a>
        </li>
      </ul>
    </div>


  </div>
</div>

</div><!-- #page -->

<noscript id="deferred-styles">
  <link href="https://fonts.googleapis.com/css?family=Poppins|Roboto:400,700" rel="stylesheet">
  <?php if (!is_page_template('page-quiz.php')) : ?>
    <!-- <link rel="stylesheet" id="bs-css" href="<?php echo get_template_directory_uri(); ?>/bs/css/bootstrap.min.css" 
    type="text/css" media="all" /> -->
    <link rel="stylesheet" id="bs-css" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" id="fontawesome" href="<?php echo get_template_directory_uri(); ?>/fontawesome/css/all.min.css?v=20201126" type="text/css" media="all" />
    <!-- <link rel="stylesheet" id="site-css" href="<?php echo get_template_directory_uri(); ?>/css/style.min.css?v=20201126" type="text/css" media="all" /> -->
  <?php endif; ?>
</noscript>

<script src="https://www.youtube.com/iframe_api" defer></script>
<script src="<?php echo get_template_directory_uri(); ?>/bs/js/bootstrap.min.js" defer></script>

<script>
  var BASE = "<?php echo home_url() ?>";
  var SITE_NAME = "<?php echo html_entity_decode(get_bloginfo('name'), ENT_QUOTES); ?>";
  var window_width = jQuery(window).width();
  var window_height = jQuery(window).height();

  var loadDeferredStyles = function() {
    var addStylesNode = document.getElementById("deferred-styles");
    var replacement = document.createElement("div");
    replacement.innerHTML = addStylesNode.textContent;
    document.body.appendChild(replacement);
    addStylesNode.parentElement.removeChild(addStylesNode);
  };
  var raf = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
    window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
  if (raf) raf(function() {
    window.setTimeout(loadDeferredStyles, 0);
  });
  else window.addEventListener('load', loadDeferredStyles);

  jQuery(document).ready(function($) {
    $(".navbar-toggler").on("click", function(o) {
      $("#mobile-menu").toggleClass("expanded");
      $("#overlay").toggleClass("d-none");
      $("body").toggleClass("modal-open");
      $("#content").toggleClass("modal-open");
    });

    $("#overlay").on("click", function() {
      $("#overlay").toggleClass("d-none");
      $("#menu-network").hide();
      $("body").removeClass("modal-open");
      $("#mobile-menu").removeClass("expanded");
    });
  });
</script>

<?php wp_footer(); ?>

<div id="overlay" class="d-none"></div>

<div hidden>
  <svg id="svg-rs-logo" viewBox="0 0 203 37" xmlns="http://www.w3.org/2000/svg">
    <path d="M23.65 8.12c0-2.9-1.98-4.73-4.59-4.73-.61 0-2.16.02-2.7.08l-1.81 11.22c.33.03.9 0 1.2 0 4.3 0 7.9-2.8 7.9-6.57zm32.1-6.93l-3.61 22.03c-.15.9-.16 1.26 1.55 1.26l-.43 2.82h-2.97c1.63.75 3.22 1.92 3.22 3.46 0 2.95-2.9 5.3-10.5 5.3-14.07 0-23.36-7.18-26.55-17.97h-2.49l-1.08 6.79c-.2 1.3-.18 1.72 2.13 1.87l1.59.11-.65 3.32c-2.3-.14-4.84-.26-7.37-.25-2.3 0-4.72.12-7 .25l.54-3.32c.6-.03 1.01-.03 1.4-.07a4.57 4.57 0 0 0 1.06-.22c.64-.2 1.16-.61 1.3-1.44L9.32 4.44a15.1 15.1 0 0 0-2.86.8c-.76.3-1.59.99-1.59 1.55 0 .24.05.42.15.58.13.2.29.38.29.65 0 .19-.08.3-.18.43-.5.6-1.62 1.08-2.67 1.08A2.46 2.46 0 0 1 0 7.04c0-3.87 4.84-5.42 7.94-6.03A62.1 62.1 0 0 1 18.5 0c7.15 0 12.34 2.23 12.34 7.87 0 4.64-3.52 7.64-7.29 8.92 2.87 8.95 9.88 15.67 18.88 15.67 3.42 0 4.62-1.46 4.62-2.89a2.2 2.2 0 0 0-2.23-2.27H43.4l.54-2.82c1 0 2.1-.09 2.27-1.12l2.57-15.89c.04-.28-.07-.4-.36-.4h-2.39l.47-2.52L55.46.9l.29.3zm129.5 23.29l-.54 2.82h-9.64l.47-2.82c1.48 0 1.69-.13 1.88-1.4l1.05-6.94c.18-1.19-.54-1.59-1.23-1.59-1.4 0-2.85 1.46-3.1 3.14l-.87 5.63c-.13.8 0 1.16 1.66 1.16l-.44 2.82h-9.78l.5-2.82c1.77 0 2.07-.26 2.24-1.34l1.16-7.15c.08-.5 0-.57-.4-.57h-2.13l.44-2.46 8.73-3.75.26.25-.4 2.2c1.6-1.42 3.37-2.42 5.38-2.42 2.51 0 4.19 2.36 4.19 4.88 0 .59-.05 1.07-.18 1.91l-1.16 7.04c-.2 1.18.01 1.4 1.92 1.4zm-35.34-14.4l-1.66 3.53a5.5 5.5 0 0 0-1.99-.4h-1.98l-1.34 8.2c-.24 1.47.04 1.73.8 1.73.82 0 1.66-.42 3.17-1.8l1.3 2.56c-1.79 1.9-4.07 3.9-6.97 3.9-3.08 0-4.44-1.76-4.44-4.55 0-.59.1-1.39.18-1.95l1.3-8.09-2.27.04.54-3.1 2.16.03.51-3.28 5.96-2.42.4.4-.84 5.23 5.17-.04zm-11.88-8.85a73.34 73.34 0 0 0-1.84 8.05h-3.47c.32-1.15.42-1.87.43-2.5.03-1.5-1.68-2.45-4.22-2.45-2.6 0-4.34 1.33-4.37 3-.03 1.58.57 2.24 2.57 3.65l3.46 2.45c3.22 2.28 5.16 4.41 5.1 8.09-.11 6.11-5.29 9.2-11.92 9.2-1.55 0-2.84-.25-3.9-.5-.9-.22-1.77-.5-2.71-.5a4.4 4.4 0 0 0-2.02.54l-.54-.3a138.7 138.7 0 0 0-1.6-8.84l3.62-.87c.95 4.48 3.03 6.54 7.54 6.54 2.96 0 4.55-1.44 4.59-3.75.04-2.22-1.11-3.14-3.21-4.52-.94-.6-1.87-1.2-2.75-1.8a16.4 16.4 0 0 1-2.45-1.95 8.13 8.13 0 0 1-2.31-6.14c.08-4.8 4.38-8.2 10.21-8.2 2.44 0 4.34.61 6.54.73.73.03 1.96-.13 2.74-.33l.5.4zM75.64 4.15c0 2.27-2.46 4.26-5.02 4.26-1.3 0-2.81-.55-2.81-2.38 0-2.72 2.76-4.55 5.05-4.55 1.51 0 2.78 1.01 2.78 2.67zm-.58 5.31l-2.2 13.36c-.25 1.5-.28 1.66 1.88 1.66l-.44 2.82H64.23l.43-2.82c1.66 0 2.14-.4 2.28-1.3l1.12-7.3c.06-.42.07-.46-.36-.46h-2.31l.43-2.46 8.99-3.75.25.25zm-8.95-8.38l-3.54 21.63c-.26 1.61-.09 1.77 1.52 1.77l-.44 2.81h-9.82l.44-2.81c1.72 0 2.15-.33 2.34-1.55l2.46-15.28c.08-.5.02-.57-.36-.57h-2.28l.44-2.5L65.86.84l.25.25zM196.7 14.73c-.05-1.6-.7-2.06-1.63-2.06-1.06 0-2.57 1.3-3.14 4.2l4.77-2.14zm5.74.87l-10.98 5.05c.23 1.88 1.28 3.14 3 3.14 1.8 0 2.42-1.01 2.42-1.48a.97.97 0 0 0-.18-.57c-.14-.2-.25-.32-.25-.58a.63.63 0 0 1 .14-.44 3.31 3.31 0 0 1 2.46-1.04c1.28 0 2.42.96 2.42 2.42 0 3.17-3.11 5.74-7.98 5.74-4.8 0-8.23-2.91-8.23-8.42 0-4.93 4.4-10.25 9.89-10.25 4.6 0 6.6 2.31 7.29 6.43zm-42.9-.04c0-1.99-.65-2.89-1.83-2.89-1.72 0-3.58 3.64-3.58 8.81 0 1.98.67 2.85 1.84 2.85 1.82 0 3.58-4.2 3.58-8.77m6.24 1.45c0 5.42-3.9 10.86-10.1 10.86-4.72 0-7.73-2.75-7.73-8.23 0-5.59 4.22-10.54 10.04-10.54 4.53 0 7.8 2.86 7.8 7.9m-60.77 13.08c0-.41-.38-.69-1.26-.69-1.01 0-2.03.14-3.07.14a9.95 9.95 0 0 1-4.59-.93c-.7.55-1.12 1.18-1.12 2.09 0 1.17 1.53 2.13 3.22 2.13 4.47 0 6.82-1.63 6.82-2.74m1.01-15.78c0-1-.39-1.88-1.2-1.88-1.84 0-2.63 3.41-2.63 5.45 0 1.42.3 2.02 1.27 2.02 1.62 0 2.56-3.72 2.56-5.6m11.27-2.95c0 1.15-1.02 2.56-2 2.56-.32 0-.43-.23-.6-.47a.87.87 0 0 0-.44-.32 1.25 1.25 0 0 0-.43-.08c-1.05 0-1.8 1.19-1.8 2.24l.03.72a6.3 6.3 0 0 1-.94 3.22 8.74 8.74 0 0 1-7.33 4.04 9.37 9.37 0 0 1-3.28-.47 1.15 1.15 0 0 0-.3.72c0 .74.59.9 1.49.9 1.87 0 3.54-.28 5.27-.28 2.96 0 5.09 1.46 5.09 4.19 0 4.85-7.4 7.8-14.3 7.8-4.1 0-8.05-1.18-8.05-4.66 0-1.72 1-3.06 3.25-4.15h-8.16l.47-2.82c1.64-.04 1.8-.16 1.95-1.2l1.08-7.21c.14-.89-.46-1.52-1.22-1.52-1.42 0-2.87 1.6-3.07 2.89l-.94 5.96c-.12.74.04 1.08 1.59 1.08l-.44 2.82h-9.35l.47-2.82c1.62 0 1.67-.24 1.8-1.08l1.2-7.44c.08-.5 0-.54-.36-.54h-2.32l.47-2.5 8.96-3.71.25.25-.36 2.17c1.8-1.56 3.64-2.42 5.27-2.42 2.86 0 4.26 2.67 4.26 5.12 0 .27-.07.94-.1 1.2l-1.23 7.69c-.15.93.5 1.2 1.4 1.26.45-1.71 1.72-2.6 3.22-3.21a6 6 0 0 1-1.7-4.4c0-4.87 4.39-7.84 8.85-7.84 2.03 0 4.55.68 5.95 2.81 1.01-1.77 2.46-2.81 3.97-2.81 1.35 0 2.42.86 2.42 2.3m-77.66 4.23c0-1.99-.66-2.89-1.84-2.89-1.72 0-3.57 3.64-3.57 8.81 0 1.98.67 2.86 1.84 2.86 1.82 0 3.57-4.2 3.57-8.78m6.25 1.45c0 5.42-3.9 10.86-10.11 10.86-4.71 0-7.73-2.75-7.73-8.23 0-5.59 4.22-10.54 10.04-10.54 4.54 0 7.8 2.86 7.8 7.9" />
  </svg>
  <svg version="1.1" id="svg-brag-logo" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 839.3 206.5" style="enable-background:new 0 0 839.3 206.5; width: 140px; max-width: 100%;" xml:space="preserve">
    <style type="text/css">
      .st-white {
        fill: #FFFFFF;
      }
    </style>
    <path class="st-white" d="M179.7,5.2c22.2,0,47.1,0.3,62.3,19.7c9.1,11.6,10,23.8,10,29.4s-1.1,19.7-13,31c-5.3,5-10,7.5-12.5,8.6
    c1.4,0.6,9.1,3.3,14.4,6.9c8,5,20.5,17.5,20.5,41c0,17.7-6.1,28.3-10.2,34.1c-19.1,26.6-53.7,24.9-61.8,24.9H88.9V5.2H179.7z
     M139,81.9h36.3c8.3,0,17.2-0.3,23-7.2c1.7-1.9,4.2-5.3,4.2-11.4c0-1.9-0.3-7.2-3.6-11.6c-6.1-7.8-15.8-7.8-24.4-7.8H139V81.9z
     M139,160.3h40.4c15.8,0,23.5-2.8,27.4-10.2c2.2-3.9,3-8,3-12.2c0-3.3-0.8-13-10-18.3c-7.2-4.2-13.6-3.9-21.6-4.2h-39.3v44.9H139z" />
    <path class="st-white" d="M376.9,5.2c4.4,0,8.9,0,13.3,0.3c27.7,1.7,41,12.5,48.5,22.7c10.2,13.8,10.8,26.9,10.8,33.5
    c0,2.8,0,7.5-1.7,13.8c-1.7,6.6-8.3,26-28.5,34.1c14.4,4.2,21.1,15.2,23.3,19.7c6.6,13.8,6.6,34.9,8.3,49.3
    c1.7,13,1.9,15.2,6.1,22.2h-52.6c-0.8-1.7-1.7-3.3-2.2-6.4c-1.4-5.3-2.5-19.4-2.8-24.7c-0.3-4.2-0.6-8.3-1.4-12.5
    c-1.1-8.6-3.9-14.7-5.5-17.5c-6.9-11.4-19.1-11.6-30.7-11.9h-29.4v72.8h-51V5.2H376.9z M332.3,91.6h36.3
    c10.5-0.3,21.9-0.6,27.7-11.1c2.8-5.3,2.8-10.5,2.8-12.7c0-5-0.8-9.1-3.6-13c-6.6-9.7-17.7-10-28-10.2h-35.2V91.6z" />
    <path class="st-white" d="M659.9,200.7h-52.4L595.3,165H528l-12.5,35.7h-51.7L536.4,5.1h51.2L659.9,200.7z M562.1,60.3l-22.4,67.9h44
    L562.1,60.3z" />
    <path class="st-white" d="M839.3,200.7h-32.7l-3-21.1c-3,3.3-4.4,5.3-7.8,8c-7.8,6.6-24.4,17.7-50.7,17.7c-8.9,0-33.8-1.7-55.1-18.3
    c-15-11.9-36.3-37.7-36.3-83.9c0-23.3,6.4-47.1,20.5-65.9C685,22.8,697.5,15,703.3,11.7c4.4-2.2,12.5-6.1,24.1-8.9
    C739.3,0.3,746.8,0,752.3,0c9.1,0,29.1,0.8,49.6,14.7C823,28.8,831,47.9,833.8,56.8c1.1,2.8,2.2,7.5,2.8,14.4H787
    c-0.8-4.2-3.9-16.1-15.8-22.7c-3.3-1.9-10.8-5-21.1-5c-4.2,0-13.6,0.6-23,7.2c-11.9,8-21.6,22.7-21.6,52.6c0,5.3-1.1,36.3,19.4,52.1
    c7.8,6.4,18,8.9,27.7,8.9c16.3,0,26-7.2,30.5-11.9c3-3.3,8-10.2,9.7-21.6h-34.9V94h81.4L839.3,200.7L839.3,200.7z" />
    <g>
      <path class="st-white" d="M15.8,140.4v20.9h54.7v18.6H15.8v20.9H0v-60.3h15.8V140.4z" />
      <path class="st-white" d="M70.5,73v18.5H41.3v23.1h29.2v18.6H0v-18.6h25.2V91.5H0V73H70.5z" />
      <path class="st-white" d="M14.9,6.1v37.2H27v-34h14.3v34h13.8V5.2h15.5v56.4H0.1V6.1H14.9z" />
    </g>
  </svg>
</div>
</body>

</html>