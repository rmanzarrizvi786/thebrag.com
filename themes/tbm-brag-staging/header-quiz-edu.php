<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
    <head>
        <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.png" />
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        
        <meta name="google-site-verification" content="Tf8gbZdF2WOW_R5JIuceGcMuqUNy7TAvdrYKaeoLP5I" />
        <meta name="msvalidate.01" content="E0857D4C8CDAF55341D0839493BA8129" />
        <meta name="bitly-verification" content=""/>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta property="fb:app_id" content="1950298011866227" />
        <meta property="fb:pages" content="145692175443937" />
        
        <meta name="theme-color" content="#990033">
        
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-TQC6WRH');</script>
        <!-- End Google Tag Manager -->
        
        <?php wp_head(); ?>
        
        <style>
@font-face {
  font-family: 'GothamNarrow-Book';
  src: url('<?php echo get_template_directory_uri(); ?>/fonts/GothamNarrow-Book.otf')  format('opentype');
}
@font-face {
  font-family: 'GothamNarrow-Bold';
  src: url('<?php echo get_template_directory_uri(); ?>/fonts/GothamNarrow-Bold.otf')  format('opentype');
}
@font-face {
  font-family: 'GothamNarrow-Light';
  src: url('<?php echo get_template_directory_uri(); ?>/fonts/GothamNarrow-Light.otf')  format('opentype');
}

@font-face{font-family:'FontAwesome';src:url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.eot?v=4.7.0');src:url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.eot?#iefix&v=4.7.0') format('embedded-opentype'),url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.woff2?v=4.7.0') format('woff2'),url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.woff?v=4.7.0') format('woff'),url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.ttf?v=4.7.0') format('truetype'),url('<?php echo get_template_directory_uri(); ?>/font-awesome/fonts/fontawesome-webfont.svg?v=4.7.0#fontawesomeregular') format('svg');font-weight:normal;font-style:normal}.fa{display:inline-block;font:normal normal normal 14px/1 FontAwesome;font-size:inherit;text-rendering:auto;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.fa-lg{font-size:1.33333333em;line-height:.75em;vertical-align:-15%}.fa-search:before{content:"\f002"}.fa-twitter:before{content:"\f099"}.fa-facebook:before{content:"\f09a"}.fa-bars:before{content:"\f0c9"}.fa-instagram:before{content:"\f16d"}.fa-youtube:before{content: "\f167"}
.clear{clear:both}
a{color:#2982b3}
img{max-width:100%;height:auto}
iframe{max-width:100%;margin:auto!important}

.social-share-buttons{display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-flex-wrap:wrap;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-align-items:flex-start;-ms-flex-align:start;align-items:flex-start}
.social-share-buttons a{-webkit-flex:1 1 auto;-ms-flex:1 1 auto;flex:1 1 auto;min-width:0;box-shadow:0;padding:10px;margin:5px 2px;float:left;border:0;text-decoration:none;color:#fff}
.social-share-facebook{background:#2d5f9a}
.social-share-twitter{background:#00c3f3}

html, body { margin-top: 0px !important; height: 100%; }
* html body { margin-top: 0px !important; }

h1, h2, h3 {
    font-family: 'GothamNarrow-Bold', 'Open Sans', Arial;
    color: #990033;
    text-transform: uppercase;
}
h1 {
    font-size: 52px;
    line-height: 54px;
    margin: 20px;
}
h2 {
    font-size: 32px;
    line-height: 34px;
}
h3 {
    font-size: 24px;
    line-height: 26px;
}
@media only screen and (max-width:977px) {
    h1 {
        font-size: 30px;
        line-height: 32px;
    }
    h2 {
        font-size: 26px;
        line-height: 28px;
    }
}

.quiz-story-edu { background-color: #fff !important;}
.quiz-section-edu .quiz-details .mejs-container .mejs-controls {
    background: none repeat scroll 0 0 #fff !important;
}
body {
    margin:0;padding:0;
    background-color: #fff;
    font-size: 14px;
    line-height: 21px;
    font-family: 'GothamNarrow-Book', 'Open Sans', Arial;
    /*background-color: #fff !important;*/
    /*background-image: url(<?php echo get_template_directory_uri(); ?>/images/The_College_AOS_2018bw.jpg);*/
    background-repeat: no-repeat;
    /*background-position: center top;*/
}

.quiz-story-edu{
    width: 600px;
    
    background:#fff;
    margin:auto;
    max-width:100%;
    padding: 0px;
    box-sizing:border-box;
/*    background-image: url(<?php echo get_template_directory_uri(); ?>/images/The_College_AOS_2018bw.jpg);
    background-repeat: no-repeat;
    background-size: cover;*/
    min-height: 100%;
    padding-bottom: 20px;
}
            
.quiz-story-edu .story-title{text-align:center}
.quiz-story-edu.results{width:100%}
.quiz-story-edu .featured-img{width:100%;height:200px;background-size:cover;background-repeat:no-repeat;margin:0 0 20px 0;background-position:center}

.quiz-section-edu .quiz{margin:0;display:inline-block;width:100%;padding-top:100%;position:relative}
.quiz-section-edu .quiz-edu .quiz-img{background-repeat:no-repeat;background-size:cover;position:absolute;top:0;left:0;right:0;bottom:0;background-position:center}
.quiz.active .quiz-img:before,.quiz-section-edu .quiz:hover .quiz-img:before{opacity:0}
.quiz-section-edu .quiz.result{width:20%;padding-top:20%;margin-bottom:-7px}
.quiz-section-edu .quiz{margin-bottom:-7px}
.quiz-section-edu .quiz-edu .quiz-img.clickable,.quiz-section-edu .quiz-edu h2.clickable{cursor:pointer}
.quiz-section-edu .quiz-edu .quiz-img.clickable{opacity:.8}
.quiz.active .quiz-img.clickable{opacity:1}
.quiz-section-edu .quiz-details{width:100%;cursor:default;margin:10px auto;padding:10px;box-sizing:border-box}
.quiz-section-edu .quiz-details.result{padding:0}
.quiz-section-edu .quiz-details .quiz-audio{text-align:center}
.quiz-edu h2{text-transform:uppercase}
.quiz.active h2{color:#f9b317;background:rgba(0,0,0,.4)}
.quiz-section-edu .quiz.result h2{font-size:.8rem;line-height:1.2rem;font-weight:normal}
.quiz-edu h2{position:absolute;bottom:0;text-align:center;width:100%;padding:5px 0;margin:0;box-sizing:border-box}
.answer-img{width:40%;padding-top:40%;background-repeat:no-repeat;background-size:cover;background-position:center;float:left}
.answer-details{width:60%;float:left}
.quiz-story-edu .intro-outro{margin:50px 0 60px 0}
.quiz-story-edu .related-articles h1{text-align:center}
.quiz-story-edu .related-articles .article-wrap{width:33.33%;float:left}
.quiz-story-edu .related-articles .article-wrap .article{padding:10px}
.quiz-story-edu .related-articles .article-wrap .article .img{width:100%;padding-top:50%;background-size:cover;background-repeat:no-repeat;background-position:center}
.quiz-story-edu .related-articles .article-wrap .article .img img{height:100%;width:auto;max-width:none;position:absolute;top:50%;left:50%;transform:translateX(-50%) translateY(-50%)}
.quiz-story-edu .related-articles .article-wrap .article .title{font-size:14px;font-weight:bold}
.quiz-story-edu .related-articles .article-wrap .article .title a{text-decoration:none;color:inherit}
.quiz-story-edu label{width:100%;font-size:1rem;font-weight:bold}
.quiz-story-edu input.radio:empty{margin-left:-500%}
/*.quiz-story-edu input.radio:empty ~ label{position:relative;float:left;margin-top:0;cursor:pointer;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;padding:10px; box-sizing: border-box; vertical-align: middle;background-color:#990033;border: 1px solid #990033;color:#fff;transition: 0.25s all linear; text-align: center;}
.quiz-story-edu input.radio:hover ~ label,.quiz-story-edu input.radio:checked ~ label{background-color:#FF5C5E;color:#fff; border-color: #FF5C5E;}
.quiz-story-edu input.radio:checked ~ label:before{ color:#fff;background-color:#FF5C5E; content:'\f00c';font-family:'FontAwesome'; margin-right: 5px; display: inline-block;}
.quiz-story-edu input.radio.disabled ~ label { background-color: #C4C2C3; color: #fff; border-color: #C4C2C3; }*/

.quiz-story-edu input.radio:empty ~ label{
    position:relative;
    float:left;
    margin-top:0;
    cursor:pointer;
    -webkit-user-select:none;
    -moz-user-select:none;
    -ms-user-select:none;
    user-select:none;
    padding:10px;
    box-sizing: border-box;
    vertical-align: middle;
    background-color:#fff;
    border: 2px solid #ED0033;
    border-left: none;
    color:#ED0033;
    transition: 0.25s all linear;
    text-align: center;
}
/*.quiz-story-edu input.radio:hover ~ label,*/
.quiz-story-edu input.radio:checked ~ label{background-color:#FF5C5E;color:#fff; border-color: #FF5C5E;}
.quiz-story-edu input.radio.disabled ~ label, .quiz-story-edu input.radio.disabled:hover ~ label { background-color: #C4C2C3; color: #fff; border-color: #C4C2C3; }

.quiz-question-edu{ font-size: 1.5rem; font-family: 'GothamNarrow-Bold', 'Open Sans', Arial; padding-bottom: 5px; margin-bottom: 20px; margin-top: 10px; }
.quiz-question-edu small{ font-size: 1rem; display: block; color: #FF5C5E; margin-top: 5px; }
.quiz-answers .quiz-answer{ display: inline-block; float: left; width: calc( 50% - 10px ); margin: 5px; }

.progress {
    width: 100%;
    height: 7px;
    margin: auto;
    position: relative;
    box-sizing: border-box;
    background-color: #C4C2C3;
    /*border: 1px solid #ED0033;*/
    border-right: 0px;
    overflow: hidden;
}
.progress .progress-indicator {
    background-color: #ED0033;
    /*background: linear-gradient(to right, transparent, #ED0033);*/
    height: 7px;
    width: 0;
    position: absolute;
    top: 0px;
    left: 0;
}
.progress .question-numbers{
    position: absolute;
    top: 0px;
    right: 0px;
    display: none;
/*    margin-top: -29px;
    width: 40px;
    height: 40px;
    padding: 20px 10px 0 10px;
    border: 1px solid #ED0033;
    text-align: center;
    background: #fff;
    transition: .25s all linear;*/
}

.error{ color: #ED0033; text-align: center; }

.results { margin: auto; width: 600px; max-width: 100%; }
.results .result{ border-bottom: 1px solid #F2F2F2; padding: 20px 20px; }
.results .result .result-circle-score { position: relative; float: left; text-align: center; margin: 10px 10px 0px 10px; }
/*.results .result .result-circle-score .circle canvas { width: 100px !important; height: 100px !important; }*/
.results .result .result-circle-score .score { position: absolute; width: 100%;
margin: auto;
  position: absolute;
  top: 50%; left: 0; transform: translateY(-50%);
  font-size: 40px;
}
.results .result .result-text { margin: 10px 10px 10px 190px; }
.results .result .result-text .title { font-size: 24px; font-family: 'GothamNarrow-Bold', 'Open Sans', Arial; }

.start-wrap-mobile {
    padding: 20px;
    box-sizing: border-box;
    /*color: #3A3537;*/
    color: #fff;
    background-color: #101010;
    background-image: url(<?php echo get_template_directory_uri(); ?>/images/WSU_mobileLandingPage2.jpg);
    background-repeat: no-repeat;
    background-size: contain;
    background-position: right bottom -10px;
    min-height: 100vh;
    width: 100vw;
    position: absolute;
    left: 0;
    top: 0;
}
.start-wrap-desktop {
    padding: 20px;
    box-sizing: border-box;
    min-height: 100vh;
}

.start-wrap-desktop-inner {
    width: 450px;
    max-width: 100%;
    margin: auto;
}

.start-wrap-mobile .heading, .start-wrap-desktop .heading {
    color: #FF5C5E;
    font-family: 'GothamNarrow-Bold', 'Open Sans', Arial;
    text-transform: uppercase;
    border: 2px solid #FF5C5E;
    border-left: none;
    display: inline-block;
    padding: .875rem 1rem;
}
.start-wrap-mobile .heading {
    font-size: 52px;
    line-height: 46px;
}
.start-wrap-desktop .heading {
    font-size: 80px;
    line-height: 70px;
}

.start-wrap-mobile .sub-heading, .start-wrap-desktop .sub-heading {
    color: #262223;
    font-family: 'GothamNarrow-Bold', 'Open Sans', Arial;
    text-transform: uppercase;
    
    margin-top: 100px;
    border-top: 1px solid #898687;
    display: inline-block;
    padding-top: 10px;
}
.start-wrap-mobile .sub-heading {
    font-size: 32px;
    line-height: 34px;
    color: #fff;
}
.start-wrap-desktop .sub-heading {
    font-size: 52px;
    line-height: 54px;
}
.btn-start, .btn-next, .btn-prev, .quiz-story-edu input.button {
    font-family: 'GothamNarrow-Bold', 'Open Sans', Arial;
    display: inline-block;
    margin: auto;
    width: auto;
    text-align: center;
    padding: .875rem 1rem;
    box-sizing:border-box;
    background: #fff;
    color: #FF5C5E;
    border: 2px solid #FF5C5E;
    border-left: none;
    text-transform:uppercase;
    font-size: 1rem;
    line-height: 1.15rem;
    margin-top: 10px;
    height: auto;
    cursor:pointer;
}
.quiz-story-edu input.button{
    display:block; margin:auto; width: auto; text-align:center; box-sizing:border-box; cursor: pointer;
    float: right;
}
/*.quiz-story-edu input.button.active,.quiz-story-edu input.button:hover{background:#fff;border:1px solid #333;color:#333}*/
.btn-next {
    float: right;
}
.btn-prev{
    padding: .875rem 1rem;
    margin-top: 10px;
    float: left;
    cursor: pointer;
}
.btn-start, .btn-next, .btn-prev, .quiz-story-edu input.button{
    background: #ED0033;
    color: #fff;
    border: 2px solid #ED0033;
    border-left: none;
    transition: 0.15s all linear;
}
.btn-start {
    background-color: #fff;
    color: #ED0033;
}
.btn-start:hover, .btn-next:hover, .btn-prev:hover, .quiz-story-edu input.button:hover {
    background:#fff;
    color: #FF6666;
    border-color: #FF6666;
    padding-right: 10px;
}
.btn-prev:hover {
    padding-right: 15px;
}
.quiz-story-edu input.button:hover {
    padding-right: 15px;
}
.btn-next span, .btn-start span {
    margin-left: 10px;
    transition: 0.15s all linear;
}
.btn-next:hover span, .btn-start:hover span {
    margin-left: 15px;
}

.results-other {
    width: 100%;
    box-sizing: border-box;
    font-family: 'GothamNarrow-Light', 'Open Sans', Arial;
    color: #3A3537;
    max-width: 600px;
    margin: auto;
}
.results-other tr td {
    padding: 10px 5px;
    font-weight: bold;
    font-family: 'GothamNarrow-Bold', 'Open Sans', Arial;
    border-bottom: 1px solid #F2F2F2;
}
.results-other tr td.score {
    text-align: right;
}
.results-other tr.result-light td {
    color: #C4C2C3;
}

.results, .questions-wrap {
    position: relative;
    min-height: 100vh;
}

.quiz-edu-skin-left, .quiz-edu-skin-right {
    background-repeat: no-repeat;
    background-size: cover;
    background-attachment: scroll;
    
    min-height: 100vh;
    position: fixed;
    
    top: 0;
    width: 300px;
    z-index: -1;
}
.quiz-edu-skin-left {
    /*margin-left: -300px;*/
    left: 0;
    background-position: left top;
    background-image: url(<?php echo get_template_directory_uri(); ?>/images/WSU_leftbanner3.jpg);
}
.quiz-edu-skin-right {
    /*margin-left: 600px;*/
    right: 0;
    background-position: right top;
    background-image: url(<?php echo get_template_directory_uri(); ?>/images/WSU_rightbanner2.jpg);
}
.progress, .quiz-details {
    z-index: 2;
}

#main {
    min-height: 100%;
    max-width: 100%;
    overflow: hidden;
}

.quiz-header .left {
    font-size: 62px;
    line-height: 62px;
    color: #FF5C5E;
    text-transform: uppercase;
    font-family: 'GothamNarrow-Bold', 'Open Sans', Arial;
    padding: 10px;
    float: left;
    border: 2px solid #FF5C5E;
    border-left: none;
    margin: 10px 5px 10px 10px;
}
.quiz-header .right {
    float: right;
    width: 317px;
    width: calc( 100% - 283px );
    margin: 10px 10px 10px 5px;
    height: 210px;
    overflow: hidden;
    position: relative;
}
.quiz-header .right img {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
    /*width: 100%;*/
    height: 100%;
    max-width: none;
}
@media only screen and (min-width: 1025px) {
    .quiz-header .right img {
        width: 100%;
        height: auto;
        max-width: 100%;
    }
}
@media only screen and (min-width: 1800px) {
    .quiz-header .left {
        font-size: 132px;
        line-height: 132px;
    }
    .quiz-header .right {
        width: 417px;
        width: calc( 100% - 560px );
        height: 420px;
    }
}
div.buttons-bottom {
    text-align: center;
    margin-top: 40px;
}
.buttons-bottom a {
    font-family: 'GothamNarrow-Bold', 'Open Sans', Arial;
    display: inline-block;
    margin: auto;
    width: auto;
    text-align: center;
    padding: .875rem 1rem;
    box-sizing:border-box;
    background: #fff;
    color: #FF5C5E;
    border: 2px solid #FF5C5E;
    border-left: none;
    text-transform:uppercase;
    font-size: 1rem;
    line-height: 1.15rem;
    margin-top: 10px;
    height: auto;
    cursor:pointer;
    text-decoration: none;
/*    margin: 0 10px;*/
    background: #ED0033;
    color: #fff;
    border: 2px solid #ED0033;
    border-left: none;
    transition: 0.15s all linear;
}
div.buttons-bottom a {
    margin: 10px;
}
.buttons-bottom a span {
    margin-left: 10px;
    transition: 0.15s all linear;
}
.buttons-bottom a:hover span {
    margin-left: 15px;
}
.buttons-bottom a:hover {
    background:#fff;
    color: #FF6666;
    border-color: #FF6666;
    padding-right: 10px;
}

#brag-logo {
    width: 80%;
    max-width: 600px;
    margin: auto;
    padding: 20px;
    box-sizing: border-box;
    text-align: center;
}

.results-other .link { text-align: right; }
.results-other .link a {
    font-family: 'GothamNarrow-Bold', 'Open Sans', Arial;
    
    text-decoration: none;
    background: none; padding: 0; color: #ED0033; font-size: 1rem; border: none;
}

.social-share-buttons{
    display: block;
    text-align: center;
}
.social-share-buttons a{
    font-family: 'GothamNarrow-Bold', 'Open Sans', Arial;
    display: inline-block;
    margin: auto;
    width: auto;
    text-align: center;
    padding: .875rem 1rem;
    box-sizing:border-box;
    border-left: none;
    text-transform:uppercase;
    font-size: 1rem;
    line-height: 1.15rem;
    margin-top: 10px;
    height: auto;
    cursor:pointer;
    text-decoration: none;
    margin: 0 10px;
    background: #fff;
    color: #ED0033;
    /*border: 2px solid #ED0033;*/
    border-left: none;
    transition: 0.15s all linear;
    float: none;
}
.social-share-quiz-facebook{color: #2d5f9a !important}
.social-share-quiz-twitter{color: #00c3f3 !important}

@media only screen and (min-width:1201px) {
    .quiz-story-edu { width: calc( 100% - 600px ); }
    .results { width: 100%; }
    .quiz-story-edu input.radio:hover ~ label{background-color:#FF5C5E;color:#fff; border-color: #FF5C5E;}
}
@media only screen and (max-width: 1260px) {
    .quiz-answers .quiz-answer { width: 100%; }
}
@media only screen and (min-width:901px) {
    .start-wrap-mobile { display: none; }
}
@media only screen and (max-width:900px) {
    .start-wrap-desktop { display: none; }
}

@media only screen and (max-width: 420px) {
    .results .result .result-circle-score .score {
        font-size: 30px;
    }
    #brag-logo {
        width: 100%;
    }
    
    h2.result-title {
        text-align: left !important;
        margin-left: 20px;
    }
/*    .start-wrap-mobile .bottom {
        position: fixed;
        bottom: 20px;
    }*/
}

@media only screen and (min-width:320px) and (max-width:600px){
    .quiz-story-edu, .start-wrap-desktop { width: 100% !important; height: 100%; }
    .quiz-answers .quiz-answer{ float: none; width: 100%;margin: 5px auto; min-height: auto; }
    .quiz-story-edu input.radio:empty ~ label{ min-height: auto; }
    .results .result .result-circle-score .circle canvas { width: 100px !important; height: 100px !important; }
    .results .result .result-text { margin-left: 130px; }
    
    .quiz-header .left { font-size: 30px; line-height: 28px; }
    .quiz-header .right { width: 100%; margin: 0 0 10px 0; padding: 10px; box-sizing: border-box; height: 180px; overflow: hidden; }
    
    .results .result .result-text .title{ font-size: 16px; }
    
/*    .results-other .link a { background: none; padding: 0; color: #ED0033; font-size: 0.8rem; border: none; }*/
    /*.results-other .link a span { display: none; }*/
}
@media only screen and (max-width:320px) {
    .quiz-header .left { font-size: 20px; line-height: 18px; }
    .quiz-header .right { width: 100%; margin: 0 0 10px 0; padding: 10px; box-sizing: border-box; height: 180px; overflow: hidden; }
}
</style>

<script>
var header_images = [
    '<?php echo get_template_directory_uri(); ?>/images/Afifa_2_bw2.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/wsu_questionpages.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/Jess_2_bw2.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/WSU_quiz_bw1.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/WSU_quiz_bw2.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/WSU_quiz_bw3.jpg',
    
    '<?php echo get_template_directory_uri(); ?>/images/wsu_questionpages.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/Jess_2_bw2.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/WSU_quiz_bw1.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/WSU_quiz_bw2.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/WSU_quiz_bw3.jpg',
    
    '<?php echo get_template_directory_uri(); ?>/images/wsu_questionpages.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/Jess_2_bw2.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/WSU_quiz_bw1.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/WSU_quiz_bw2.jpg',
    '<?php echo get_template_directory_uri(); ?>/images/WSU_quiz_bw3.jpg',
];
</script>

    </head>
    
    <body <?php body_class(); ?>>
        <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQC6WRH"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div id="main">
        