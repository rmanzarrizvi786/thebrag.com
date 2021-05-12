<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.png" />
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        
        <meta name="google-site-verification" content="Tf8gbZdF2WOW_R5JIuceGcMuqUNy7TAvdrYKaeoLP5I" />
        <meta name="msvalidate.01" content="E0857D4C8CDAF55341D0839493BA8129" />
        <meta name="bitly-verification" content=""/>
        
        <meta property="fb:app_id" content="1950298011866227" />
        <meta property="fb:pages" content="145692175443937" />
        
        <meta name="theme-color" content="#2982b3">
        
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
  font-family: 'ConduitITCStd-Bold';
  src: url('<?php echo get_template_directory_uri(); ?>/fonts/ConduitITCStd-Bold.otf')  format('opentype');
}
@font-face {
  font-family: 'ConduitITCStd-Light';
  src: url('<?php echo get_template_directory_uri(); ?>/fonts/ConduitITCStd-Light.otf')  format('opentype');
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
    font-family: 'ConduitITCStd-Bold', Helvetica,Arial,Verdana,Sans-serif;
    color: #e20026;
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
.link-find-out-more {
    position: relative;
}
.link-find-out-more:after, .link-find-out-more:before {
    content: '';
    display: block;
    width: 100%;
    height: 5px;
    background: #d41f26;
    transition: .3s all;
    position: absolute;
}
.link-find-out-more:after {
    margin-top: 20px;
    right: 0;
}
.link-find-out-more:before {
    top: -5px;
    left: 0;
}
.link-find-out-more:hover::after, .link-find-out-more:hover::before {
    width: 0%;
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

/*.quiz-story { background-color: #fff !important;}*/
.quiz-section {
    padding: 0 15px;
/*    background: none repeat scroll 0 0 #fff !important;*/
}
body {
    margin:0;
    padding:0;
/*    background-color: #fff;*/
    font-size: 1rem;
    line-height: 1.2rem;
    /*letter-spacing: 1px;*/
    font-family: 'ConduitITCStd-Light', Helvetica,Arial,Verdana,Sans-serif;
    background-image: url(<?php echo get_template_directory_uri(); ?>/images/SW_Official_Background_Grey.jpg);
    background-attachment: fixed;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center top;
}

.quiz-story{
    width: 600px;
    
/*    background:#fff;*/
    margin:auto;
    max-width:100%;
    padding: 0px;
    box-sizing:border-box;
    min-height: 100%;
    padding-bottom: 20px;
}
            
.quiz-story .story-title{text-align:center}
.quiz-story.results{width:100%}
.quiz-story .featured-img{width:100%;height:200px;background-size:cover;background-repeat:no-repeat;margin:0 0 20px 0;background-position:center}

.quiz-section .quiz{margin:0;display:inline-block;width:100%;padding-top:100%;position:relative}
.quiz-section .quiz .quiz-img{background-repeat:no-repeat;background-size:cover;position:absolute;top:0;left:0;right:0;bottom:0;background-position:center}
.quiz.active .quiz-img:before,.quiz-section .quiz:hover .quiz-img:before{opacity:0}
.quiz-section .quiz.result{width:20%;padding-top:20%;margin-bottom:-7px}
.quiz-section .quiz{margin-bottom:-7px}
.quiz-section .quiz .quiz-img.clickable,.quiz-section .quiz h2.clickable{cursor:pointer}
.quiz-section .quiz .quiz-img.clickable{opacity:.8}
.quiz.active .quiz-img.clickable{opacity:1}
.quiz-section .quiz-details{width:100%;cursor:default;margin:10px auto;padding:10px;box-sizing:border-box}
.quiz-section .quiz-details.result{padding:0}
.quiz-section .quiz-details .quiz-audio{text-align:center}
.quiz h2{text-transform:uppercase}
.quiz.active h2{color:#f9b317;background:rgba(0,0,0,.4)}
.quiz-section .quiz.result h2{font-size:.8rem;line-height:1.2rem;font-weight:normal}
.quiz h2{position:absolute;bottom:0;text-align:center;width:100%;padding:5px 0;margin:0;box-sizing:border-box}
.answer-img{width:40%;padding-top:40%;background-repeat:no-repeat;background-size:cover;background-position:center;float:left}
.answer-details{width:60%;float:left}
.quiz-story .intro-outro{margin:50px 0 60px 0}
.quiz-story .related-articles h1{text-align:center}
.quiz-story .related-articles .article-wrap{width:33.33%;float:left}
.quiz-story .related-articles .article-wrap .article{padding:10px}
.quiz-story .related-articles .article-wrap .article .img{width:100%;padding-top:50%;background-size:cover;background-repeat:no-repeat;background-position:center}
.quiz-story .related-articles .article-wrap .article .img img{height:100%;width:auto;max-width:none;position:absolute;top:50%;left:50%;transform:translateX(-50%) translateY(-50%)}
.quiz-story .related-articles .article-wrap .article .title{font-size:14px;font-weight:bold}
.quiz-story .related-articles .article-wrap .article .title a{text-decoration:none;color:inherit}
.quiz-story label{width:100%;font-size:1rem;font-weight:bold}
.quiz-story input.radio:empty{margin-left: -1000%;display:none;}

.quiz-story input.radio:empty ~ label{
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
    /*border: 2px solid #d41f26;*/
    color:#d41f26;
    transition: 0.25s all linear;
    text-align: center;
    height: 100%;
    box-shadow: 0px 1px 3px rgba(0,0,0,.3);
}
.quiz-story input.radio:empty ~ label span {
    display: block;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
    font-size: 1.4rem;
    line-height: 1.6rem;
    width: 90%;
}
.quiz-story input.radio:checked ~ label{background-color: #c6e9f7; border-color: #e20026;}
.quiz-story input.radio.disabled ~ label, .quiz-story input.radio.disabled:hover ~ label { background-color: #C4C2C3; color: #fff; border-color: #C4C2C3; }

.quiz-question{ display: flex;  margin-bottom: 20px; margin-top: 10px; line-height: 2rem; background: #efefef; }
.quiz-question small{ font-size: 1rem; display: block; color: #e20026; margin-top: 5px; }

.quiz-answers {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}
.quiz-answers .quiz-answer {
    display: inline-block;
    width: 170px;
    height: 170px;
    margin: 5px;
    position: relative;
}
.quiz-answers .quiz-answer .quiz-answer-result {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: none;
    background-size: cover;
}
.quiz-answers .quiz-answer .quiz-answer-result-correct {
    background-image: url(/wp-content/themes/ssm/images/quiz-starwars/sw-correct2.png);
}
.quiz-answers .quiz-answer .quiz-answer-result-incorrect {
    background-image: url(/wp-content/themes/ssm/images/quiz-starwars/sw-incorrect2.png);
}
/*.quiz-answers .quiz-answer:nth-child(1) {
    margin-left: 0;
}
.quiz-answers .quiz-answer:nth-child(4) {
    margin-right: 0;
}*/
.question-number {
    background: #d41f26;
    box-sizing: border-box;
    color: #fff;
    font-size: 2.6rem;
    line-height: 36px;
    width: 70px;
    min-height: 30px;
    flex: 1 0 auto;
    text-align: center;
    max-width: 70px;
    font-family: 'ConduitITCStd-Light', Helvetica,Arial,Verdana,Sans-serif;
}
.question-text {
    font-size: 1.5rem;
    font-family: 'ConduitITCStd-Light', Helvetica,Arial,Verdana,Sans-serif;
}
.question-number, .question-text {
    position: relative;
    padding: 20px;
}
.question-number span {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
}

.progress {
    width: 100%;
    height: 7px;
    margin: auto;
    position: relative;
    box-sizing: border-box;
    background-color: #C4C2C3;
    border-right: 0px;
    overflow: hidden;
}
.progress .progress-indicator {
    background-color: #d41f26;
    height: 7px;
    width: 0;
    position: absolute;
    top: 0px;
    left: 0;
}
.question-numbers{
    text-align: right;
    padding: 10px;
    text-transform: uppercase;
}

.error{ color: #d41f26; text-align: center; }

.results { margin: auto; width: 600px; max-width: 100%; }
.results .result{ border-bottom: 1px solid #F2F2F2; padding: 20px 20px; }
.results .result .result-circle-score { position: relative; float: left; text-align: center; margin: 10px 10px 0px 10px; }
.results .result .result-circle-score .score { position: absolute; width: 100%;
margin: auto;
  position: absolute;
  top: 50%; left: 0; transform: translateY(-50%);
  font-size: 40px;
}
.results .result .result-text { margin: 10px 10px 10px 190px; }
.results .result .result-text .title { font-size: 24px; font-family: 'ConduitITCStd-Light', Helvetica,Arial,Verdana,Sans-serif; }

.start-wrap-mobile {
    padding: 20px;
    box-sizing: border-box;
    color: #fff;
    background-color: #101010;
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
    color: #e20026;
    font-family: 'ConduitITCStd-Light', Helvetica,Arial,Verdana,Sans-serif;
    text-transform: uppercase;
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
    font-family: 'ConduitITCStd-Light', Helvetica,Arial,Verdana,Sans-serif;
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
.btn-start, .btn-restart, .btn-next, .btn-prev, .quiz-story input.button {
    font-family: 'ConduitITCStd-Light', Helvetica,Arial,Verdana,Sans-serif;
    display: inline-block;
    margin: auto;
    width: auto;
    text-align: center;
    padding: .875rem 1rem;
    box-sizing:border-box;
    background: #fff;
    color: #e20026;
    border: 2px solid #e20026;
    text-transform:uppercase;
    font-size: 2rem;
    line-height: 1.15rem;
    margin-top: 10px;
    height: auto;
    cursor:pointer;
}
.btn-restart {
    display: block;
    border: none;
}
.quiz-story input.button{
    display:block; margin:auto; width: auto; text-align:center; box-sizing:border-box; cursor: pointer;
    float: right;
}
.btn-next {
    float: right;
}
.btn-prev{
    padding: .875rem 1rem;
    margin-top: 10px;
    float: left;
    cursor: pointer;
}
.btn-start, .btn-next, .btn-prev, .quiz-story input.button{
    background: #d41f26;
    color: #fff;
    border: 2px solid #d41f26;
    transition: 0.15s all linear;
}
.btn-start {
    background-color: #fff;
    color: #d41f26;
}
.btn-start:hover, .btn-next:hover, .btn-prev:hover, .quiz-story input.button:hover {
    background:#fff;
    color: #FF6666;
    border-color: #FF6666;
    padding-right: 10px;
}
.btn-restart:hover {
    background:#fff;
    color: #ff142b;
}
.btn-prev:hover {
    padding-right: 15px;
}
.quiz-story input.button:hover {
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
    font-family: 'ConduitITCStd-Light', Helvetica,Arial,Verdana,Sans-serif;
    color: #3A3537;
    max-width: 600px;
    margin: auto;
}
.results-other tr td {
    padding: 10px 5px;
    font-weight: bold;
    font-family: 'ConduitITCStd-Light', Helvetica,Arial,Verdana,Sans-serif;
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
    /*min-height: 100vh;*/
}

.quiz-skin-left, .quiz-skin-right {
    position: fixed;
    z-index: -1;
    width: 300px;
    /*height: 100vh;*/
}
.quiz-skin-left {
    margin-left: -315px;
    text-align: right;
}
.quiz-skin-right {
    margin-left: 585px;
    text-align: left;
}
@media only screen and (max-height: 1200px) {
    .quiz-skin-left, .quiz-skin-right {
        height: 100vh;
        z-index: 3;
    }
}
.progress, .quiz-details {
    z-index: 2;
}

#main {
    min-height: 100%;
    max-width: 100%;
    overflow: hidden;
}
.quiz-header {
    text-align: center;
    padding: 10px;
}
.quiz-header img {
    height: auto;
    max-width: 100%;
}
.quiz-details-wrap, .results-wrap {
    background: #fff;
    border-top: 5px solid #333;
}
.results-wrap {
    padding: 10px;
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
    font-family: 'ConduitITCStd-Light', Helvetica,Arial,Verdana,Sans-serif;
    display: inline-block;
    margin: auto;
    width: auto;
    text-align: center;
    padding: .875rem 1rem;
    box-sizing:border-box;
    background: #fff;
    color: #e20026;
    border: 2px solid #e20026;
    text-transform:uppercase;
    font-size: 1rem;
    line-height: 1.15rem;
    margin-top: 10px;
    height: auto;
    cursor:pointer;
    text-decoration: none;
    background: #d41f26;
    color: #fff;
    border: 2px solid #d41f26;
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

#site-logo {
    width: 80%;
    max-width: 600px;
    margin: auto;
    padding: 20px;
    box-sizing: border-box;
    text-align: center;
}

.results-other .link { text-align: right; }
.results-other .link a {
    font-family: 'ConduitITCStd-Light', Helvetica,Arial,Verdana,Sans-serif;
    
    text-decoration: none;
    background: none; padding: 0; color: #d41f26; font-size: 1rem; border: none;
}

.social-share-buttons{
    display: block;
    text-align: center;
}
.social-share-buttons a{
    font-family: 'ConduitITCStd-Light', Helvetica,Arial,Verdana,Sans-serif;
    display: inline-block;
    margin: auto;
    width: auto;
    text-align: center;
    padding: .875rem 1rem;
    box-sizing:border-box;
    text-transform:uppercase;
    font-size: 1rem;
    line-height: 1.15rem;
    margin-top: 10px;
    height: auto;
    cursor:pointer;
    text-decoration: none;
    margin: 0 10px;
    background: #fff;
    color: #d41f26;
    transition: 0.15s all linear;
    float: none;
}
.social-share-quiz-facebook{color: #2d5f9a !important}
.social-share-quiz-twitter{color: #00c3f3 !important}

#score_text{
    color: #e20026;
    font-size: 2rem;
    line-height: 2.5rem;
}
#score_text span.title {
    background: #e20026;
    color: #fff;
    display: inline-block;
    padding: 5px;
}
.result-images .result-image {
    display: none;
}

@media only screen and (min-width:1201px) {
    .results { width: 100%; }
    .quiz-story input.radio:hover ~ label{background-color:#c6e9f7; border-color: #e20026;}
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
    #site-logo {
        width: 100%;
    }
    
    h2.result-title {
        text-align: left !important;
        margin-left: 20px;
    }

}

@media only screen and (min-width:320px) and (max-width:600px){
    .quiz-story, .start-wrap-desktop { width: 100% !important; height: 100%; }
    
    .quiz-answers .quiz-answer{
        width: 30%;
        padding-top: 30%;
        height: auto;
        position: relative;
    }
    .quiz-story input.radio:empty { display: none; }
    
    .quiz-story input.radio:empty ~ label{
        min-height: auto;
        height: 100%;
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
    }
    .quiz-story input.radio:empty ~ label span {
        font-size: 1.1rem;
        line-height: 1.3rem;
    }
    .results .result .result-circle-score .circle canvas { width: 100px !important; height: 100px !important; }
    .results .result .result-text { margin-left: 130px; }
    
    .quiz-header .left { font-size: 30px; line-height: 28px; }
    .quiz-header .right { width: 100%; margin: 0 0 10px 0; padding: 10px; box-sizing: border-box; height: 180px; overflow: hidden; }
    
    .results .result .result-text .title{ font-size: 16px; }
}
@media only screen and (max-width:320px) {
    .quiz-header .left { font-size: 20px; line-height: 18px; }
    .quiz-header .right { width: 100%; margin: 0 0 10px 0; padding: 10px; box-sizing: border-box; height: 180px; overflow: hidden; }
}
</style>

<script>
var header_images = [
    
];
</script>

    </head>
    
    <body <?php body_class(); ?>>
        <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQC6WRH" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div id="main">
        