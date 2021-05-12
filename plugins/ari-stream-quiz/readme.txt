=== ARI Stream Quiz Pro - Viral Quiz Builder for WordPress ===
Contributors: arisoft
Donate link: http://wp-quiz.ari-soft.com/
Tags: quiz, viral quiz, buzzfeed quiz, quizzes, trivia quiz, personality quiz
Requires at least: 4.0
Tested up to: 5.7.0
Stable tag: 1.5.53
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy to use WordPress Viral Quiz plugin which charges with powerful features. Create quizzes like BuzzFeed does and increase audience of your site.

== Description ==

Create quizzes like on BuzzFeed in WordPress. Interesting quizzes will attract new visitors to your site. We create the plugin with easy to use interface and charge it with the powerful features, create quizzes with pleasure. No need any extra knowledge, it requires only a couple of minutes to install "ARI Stream Quiz" and create a quiz.

The plugin can be extended with the following features which helps to do quizzes more viral and increase audience of your site: data capturing, integration with popular mail services (MailChimp, MailerLite, Drip, AWeber, Zapier, ConstantContact GetResponse and others), trivia and personality quizzes, share quizzes and results via social networks.

More information can be found in [user guide](http://www.ari-soft.com/docs/wordpress/ari-stream-quiz-pro/v1/en/index.html).


== Changelog ==

= 1.5.53 =
* Add French translation

= 1.5.52 =
* Fix bug: conflict with Yoast SEO plugin. Not possible to save quizzes 

= 1.5.51 =
* Fix bug: predefined variables are replaced with empty values

= 1.5.50 =
* Fix problem with undefined locale

= 1.5.49 =
* Fix PHP warning in auto-update script

= 1.5.48 =
* Change DB type to longtext for question explanation field

= 1.5.47 =
* Fix integration with MailerLite

= 1.5.46 =
* Add particular CSS class to quiz container depends on quiz type

= 1.5.45 =
* Improve LinkedIn integration

= 1.5.44 =
* Add "Ask confirmation -> Enable by default" option

= 1.5.43 =
* Improve integration with Facebook

= 1.5.42 =
* Fix bug: "Ask confirmation" works incorrectly

= 1.5.41 =
* Add ability to wrap templates on client side with a custom tag instead of <script> to avoid conflicts with some 3rd party plugins

= 1.5.40 =
* Fix bug: "Disable scroll on load" option doesn't work

= 1.5.39 =
* Fix bug: sometimes quiz is not working when inline_scripts mode is used
* Improve quiz results sharing via Facebook

= 1.5.38 =
* Fix bug: results are not shown on some multi-site installation

= 1.5.37 =
* Fix bug: progress bar shows incorrect status when page reload option is enabled

= 1.5.36 =
* Add "Facebook description limit" parameter

= 1.5.35 =
* Add asq_zapier_data filter

= 1.5.34 =
* Fix bug: questions in personality tests are always shown in random order despite of quiz settings

= 1.5.33 =
* Fix bug: OPTIN doesn't show correctly in quiz statistics

= 1.5.32 =
* Supports {$user:name} predefined variable in mail template

= 1.5.31 =
* Update root_url algorithm

= 1.5.30 =
* Fix bug: sometimes quiz gets stuck on the same question when browser's reload is enabled

= 1.5.29 =
* Add "Disable scroll on load" option to plugin settings to disable smart scroll when quiz is configured to start immediately
* Improve asq_session_user_data hook

= 1.5.28 =
* root_url is calculated based on get_site_url() call

= 1.5.27 =
* Add asq_session_user_data hook

= 1.5.26 =
* Fix bug: post titles were shown incorrectly in preview mode

= 1.5.25 =
* Add confirmation checkbox to lead forms. GDPR compliance. See "Settings -> Collect users' data -> Ask confirmation" parameter on quiz settings page.

= 1.5.24 =
* Improve AWeber API loader

= 1.5.23 =
* Fix bug: sometimes answers are shown in an incorrect order in Google Chrome browser

= 1.5.22 =
* Fix bug: Mailchimp shows only 10 lists

= 1.5.21 =
* Fix bug: clear data from `asq_result_questions` table

= 1.5.20 =
* Add "asq_personality_score" custom filter

= 1.5.19 =
* Fix bug: images are shown incorrectly in answers in IE/Edge browsers

= 1.5.18 =
* Fix bug: return back question-result:stream_quiz JS event

= 1.5.17 =
* Fix calculation for {{totalPercentUserScore}} variable

= 1.5.16 =
* Add new points calculation for personality tests

= 1.5.15 =
* Fix custom theme loader

= 1.5.14 =
* Load custom themes from "ari-stream-quiz-themes" sub-folder of "upload" folder

= 1.5.13 =
* Fix bug: Facebook sharing works incorrectly when using plain permalinks

= 1.5.12 =
* Add ability to execute custom JS code

= 1.5.11 =
* Fix bug: quiz results page are shared incorrectly on Facebook 

= 1.5.10 =
* Fix bug: quiz edit/create page sometimes frezees with loading icon

= 1.5.9 =
* Better compatibility with PHP 7.1+

= 1.5.8 =
* Add "Content before lead form" and "Content after lead form" parameters to quiz settings

= 1.5.7 =
* Add {$summarySecondary} predefined variable to mail template for personality quiz

= 1.5.6 =
* Add {$summary} predefined variable to mail template for personality quiz

= 1.5.5 =
* Configure default e-mail in plugin settings
* Fix bug: send emails in HTML format correctly

= 1.5.4 =
* Possible to send results by email

= 1.5.3 =
* Fix bug: answers order is incorrect on quiz edit page

= 1.5.2 =
* Fix bug: questions order is incorrect on quiz edit page

= 1.5.1 =
* Add Dutch translation. Thank you to Andre Sorg
* Fix bug: add missed text domain

= 1.5.0 =
* Improve sharing functionality. Show result text and image when share on Facebook.

= 1.4.21 =
* Fix bug: subscribers are not added to the selected AWeber lists
* Remove short PHP tag in page_questions.php template

= 1.4.20 =
* Add "Disable script optimization" parameter

= 1.4.19 =
* Supports "inline_scripts" shortcode's attribute

= 1.4.18 =
* Add "Show content only for main personality" parameter for personality tests

= 1.4.17 =
* Better compatibility with jQuery 3.x

= 1.4.16 =
* Add Open Graph and Twitter tags

= 1.4.15 =
* Supports new Facebok API (v. 2.9)
* Supports shortcodes in questions and answers

= 1.4.14 =
* Integration with ConstantContact service

= 1.4.13 =
* Integration with MailerLite service

= 1.4.12 =
* Add "Content before result area" and "Content after result area" parameters to quiz settings

= 1.4.11 =
* Better compatibility with 3rd party plugins

= 1.4.10 =
* Style improvements and bug fixing

= 1.4.9 =
* Navigate to explanation instead of results section when last question is answered and "Show questions at the end" parameter is disabled
* Installer improved: if utf8mb4 charset is not supported by database, use utf8

= 1.4.8 =
* Navigate to explanation instead of results section when last question is answered
* Fix bug: "Play again" something works incorrectly on https sites
* Add filter: asq_prepare_quiz_data
* Possible to use a CSS selector as value for "Scroll offset" parameter

= 1.4.7 =
* Fix bug: sometimes a quiz is not started and "Trying to get property of non-object" error occurs

= 1.4.6 =
* Add 100 and -100 options to score drop-down for personality tests
* Add "Show image in description" parameter to quiz settings
* Add hooks:asq_ui_quiz_settings_top, asq_ui_quiz_settings_bottom

= 1.4.5 =
* Possible to use [embed] shortcode in quiz description and results 

= 1.4.4 =
* Add "Support shortcodes" parameter to quiz settings. It adds ability to use shortcodes in quiz description and results
* Add "Lockout single answers" parameter to plugin settings
* Add {{userScorePercent}} and {{maxScore}} predefined variables for personality tests
* Add hooks: asq_quiz_after_save, asq_admin_quiz_page_load, asq_ui_question_options

= 1.4.3 =
* Fix bug: "0" is not saved as question or answer

= 1.4.2 =
* Add Buzzfeed theme

= 1.4.1 =
* Fix bug: answers are overlapped by checkboxes in some themes

= 1.4.0 =
* Add support of multiple answers selection
* Improve usability and performance of quiz builder

= 1.3.13 =
* Add {{userScorePercent}} predefined variable for trivia quiz templates

= 1.3.12 =
* Fix bug: show title which is defined in plugin settings on quiz result page for personality quizzes

= 1.3.11 =
* Possible to define default values on "Settings" screen for [streamquiz] shortcode parameters
* Add animation when question page is changed
* Fix bug: shortcode shows quizzes before post content
* Fix bug: automatic update is not available immediately when API key is entered

= 1.3.10 =
* Fix bug: the plugin doesn't work properly in WordPress version less than 4.5

= 1.3.9 =
* Add missed Turkish translation for WYSIWYG editor

= 1.3.8 =
* Add Russian translation

= 1.3.7 =
* Add translations for WYSIWYG editor
* Fix bug: incorrect percent values are shown on diagram for personality quiz results

= 1.3.6 =
* Add "Show several personalities" parameter to personality quiz settings. Use it to show several personalities at the end of quiz

= 1.3.5 =
* Show name and email for registered users on "Results" page
* Changed size of WYSIWYG editor
* Fix bug: "Shuffle answers" is working incorrectly when "Start immediately" option is disabled 

= 1.3.4 =
* Fix bug with "Is the correct answer" and "Add explanation to question" checkboxes in trivia quiz. It was saved incorrectly

= 1.3.3 =
* Show image credits. Description from media gallery is used as image credit
* Add "Advanced -> Prefetch quiz session" parameter to plugin settings
* Turkish translation is added. Thank you to Gunay Say aka xtrabit

= 1.3.2 =
* Add "Email" to share buttons
* Preload images for next question page / result page

= 1.3.1 =
* Bug fixing

= 1.3.0 =
* Show detailed results with questions and answers on backend
* Show statistics for each quiz
* Export results to CSV

= 1.2.4 =
* Multisite support

= 1.2.3 =
* Improve installer. Check PHP and WordPress versions before activation.

= 1.2.2 =
* Adding integration with ActiveCampaign and Drip services
* Bug fixing and code improvements

= 1.2.1 =
* Adding integration with GetResponse service

= 1.2.0 =
* Possible to reload browser when quiz page is changed
* Possible to show warning when leave quiz page
* Adding "Play Again" button
* Improving form validation on frontend

= 1.1.1 =
* Images lazy loading

= 1.1.0 =
* Adding results page

= 1.0.2 =
* Adding Zapier integration

= 1.0.1 =
* Adding loading icon to backend pages

= 1.0.0 =
* Initial release