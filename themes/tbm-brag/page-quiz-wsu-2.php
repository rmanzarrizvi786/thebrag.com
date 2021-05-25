<?php /* Template Name: WSU Quiz 2 */ ?>
<?php get_template_part('header-quiz-wsu-2');
wp_enqueue_script('quiz-wsu-2', get_template_directory_uri() . '/js/quiz-wsu-2.js', array('jquery'), time(), true);
wp_enqueue_script('quiz-edu-cricle-progress', get_template_directory_uri() . '/js/circle-progress/circle-progress.min.js', array('jquery'), '20180815', true);

$args = array(
    'url'   => admin_url('admin-ajax.php'),
    // 'page_url' => $page_url,
    // 'page_title' => $page_title,
);
wp_localize_script('quiz-wsu-2', 'quiz_wsu_2', $args);
?>

<?php if (!post_password_required()) : ?>

    <div class="quiz-story-edu<?php echo isset($_POST) ? ' result' : ''; ?>">
        <?php
        $cachebuster = time();
        $results = array(
            array(
                'title' => 'Integrated Diploma/Bachelor ',
                'text' => '<p>If you received an ATAR of 55 or above</p>
            <p class="buttons-bottom"><a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236608973;dc_trk_aid=434041475;dc_trk_cid=110250598;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a></p>',
                'link' => 'https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236608973;dc_trk_aid=434041475;dc_trk_cid=110250598;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=',
                'imp_tag' => '<IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.236608973;dc_trk_aid=434041475;dc_trk_cid=110250598;ord=' . $cachebuster . ';dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="">',
            ),
            array(
                'title' => 'Extended Diploma',
                'text' => '<p>If you received an ATAR below 55 or did not receive one at all</p>
            <p class="buttons-bottom"><a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236608982;dc_trk_aid=434042730;dc_trk_cid=110230830;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a></p>',
                'link' => 'https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236608982;dc_trk_aid=434042730;dc_trk_cid=110230830;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=',
                'imp_tag' => '<IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.236608982;dc_trk_aid=434042730;dc_trk_cid=110230830;ord=' . $cachebuster . ';dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="">',
            ),

            array(
                'title' => 'Other study options',
                'text' => '<p>If you’re contemplating going straight into uni</p>
            <p class="buttons-bottom"><a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236630860;dc_trk_aid=434042273;dc_trk_cid=110342906;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a></p>',
                'link' => 'https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236630860;dc_trk_aid=434042273;dc_trk_cid=110342906;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=',
                'imp_tag' => '<IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.236630860;dc_trk_aid=434042273;dc_trk_cid=110342906;ord=' . $cachebuster . ';dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="">',
            ),
        );

        //    echo '<pre>' . print_r( $results, true ); exit;

        $questions = array(
            array(
                'question_no' => 1,
                'question' => 'On a scale of 1-10 how happy are you with your ATAR?',
                'min' => 1,
                'max' => 10,
                'type' => 'scale',
            ),
            array(
                'question_no' => 2,
                'question' => 'Which subject did you perform your best in?',
                'type' => 'textarea',
            ),
            array(
                'question_no' => 3,
                'question' => 'What was your favourite subject throughout Year 12?',
                'type' => 'textarea',
            ),
            array(
                'question_no' => 4,
                'question' => 'If you could have done one thing differently throughout Year 12, what would it have been?',
                'type' => 'textarea',
            ),
            array(
                'question_no' => 5,
                'question' => 'What are you looking forward to the most in 2019?',
                'type' => 'textarea',
            ),
            array(
                'question_no' => 6,
                'question' => 'Where did your ATAR sit?',
                'answers' => array(
                    'Below 55' => 1,
                    '55 - 75' => 0,
                    '75+' => 2,
                ),
            ),
            array(
                'question_no' => 7,
                'question' => 'How do you want to spend your 2019?',
                'answers' => array(
                    'Gap year' => 'You might be looking to recharge in 2019, but the future beholds a good pathway for further learning.',
                    'Straight to uni' => 'There’s no beating around the bush! You’re eager to keep the momentum up with further study in 2019.',
                    'Working' => 'You’re eager to fill that piggy bank up to your savings goal. Why not take your career prospects to the next step with the right course for you?',
                    'Still do not know!' => 'Life after the HSC can be a big daunting thought. There are plenty options for you!',
                    'Further education of some sort' => 'You’re keen to learn more, but aren’t quite sure where to go.',
                ),
            ),
            array(
                'question_no' => 8,
                'question' => 'In what environment do you learn best?',
                'answers' => array(
                    'Collaborative' => 'The College at Western Sydney University provides a collaborative and immersive learning environment to further your studies.',
                    'By myself in a library' => 'The College at Western Sydney University has world class facilities, including quiet study spaces perfect for boosting productivity.',
                    'At home, online' => 'Western Sydney University provides online study options for potential students looking for more flexible learning.',
                    'On the job' => 'The College at Western Sydney University provide practical and hands-on learning experiences across all its courses.',
                ),
            ),
            array(
                'question_no' => 9,
                'question' => 'What do you think you’ll enjoy most about further study?',
                'answers' => array(
                    'The social life' => 'On top of that the friends you’ll make and the connections you’ll garner will last a lifetime.',
                    'Learning more about the field you’re interested in' => 'On top of that, with the diverse courses available you’ll be sure to find something that speaks to you the most',
                    'Being independent' => 'On top of that there is ample opportunity for you to excel further as a strong individual and make a positive impact on your future.',
                ),
            ),
            array(
                'question_no' => 10,
                'question' => 'How long do you want to study for?',
                'answers' => array(
                    'I do not mind, as long as I’m learning!' => -1,
                    '3 years maximum' => -1,
                    '1 year - I want to start working ASAP' => -1,
                    'I want a lifetime of knowledge' => -1,
                ),
            ),
        );

        //    shuffle( $questions );

        if ($_POST) :
            // Show score and result info
            $all_answers = $_POST['answers'];
            //        echo '<pre>' . print_r( $all_answers, true );

            $scores = $db_answers = $db_results = array();

            $total_score = 0;
            foreach ($all_answers as $question_no => $answers) :
                $question_key = array_search($question_no, array_column($questions, 'question_no'));
                $q_answers = isset($questions[$question_key]['answers']) ? $questions[$question_key]['answers'] : [];

                $db_answers[] = array(
                    'question' => $questions[$question_key]['question'],
                    'answers' => $answers,
                );
                if (is_array($answers)) :
                    foreach ($answers as $answer) :
                        $result_score = $q_answers[stripslashes($answer)];

                        if (is_array($result_score)) :

                            foreach ($result_score as $rs) :
                                $total_score++;
                                if (isset($scores[$rs])) {
                                    $scores[$rs]++;
                                } else {
                                    $scores[$rs] = 1;
                                }
                            endforeach;

                        elseif (!is_null($result_score) && -1 != $result_score && is_numeric($result_score)) :
                            $total_score++;
                            if (isset($scores[$result_score])) {
                                $scores[$result_score]++;
                            } else {
                                $scores[$result_score] = 1;
                            }

                        endif;
                    endforeach;
                endif;
            endforeach;


            arsort($scores);
            //        echo '<pre>' . print_r( $scores, true ) . '</pre>'; echo $total_score;

            $suggested_results = array();

        ?>
            <div class="results">
                <div id="brag-logo">
                    <a href="<?php echo site_url(); ?>" target="_blank">
                        <img src="<?php echo ICONS_URL; ?>The-Brag_combo.svg" alt="Brag Magazine" width="175" />
                    </a>
                </div>
                <div class="quiz-header">
                    <div class="left">
                        My<br>
                        Future<br>
                        Finder.<br>
                    </div>
                    <div class="right">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/wsu-quiz/Afifa_1_results_TheCollegeLOGO2.jpg">
                    </div>
                    <div class="clear"></div>
                </div>

                <h2 style="text-align: center; margin-top: 40px;" class="result-title">Your results</h2>

                <div style="padding: 0 20px; width: 560px; margin: auto; max-width: 100%; font-size: 14px; font-family: 'GothamNarrow-Light', 'Open Sans', Arial; box-sizing: border-box;">
                    <p style="font-size: 24px; line-height: 30px">
                        <?php
                        if ($all_answers && count($all_answers) > 0) :
                            foreach ($all_answers as $key => $answer) :
                                if (isset($answer[0]) && !is_null($answer[0]) && isset($questions[$key - 1]['answers'][$answer[0]]) && !is_numeric($questions[$key - 1]['answers'][$answer[0]])) :
                                    echo $questions[$key - 1]['answers'][$answer[0]] . ' ';
                                endif;
                            endforeach;
                        endif;
                        ?>
                    </p>

                </div>
                <?php

                $score_keys = array_keys($scores);

                //echo '<pre>' . print_r( ( $score_keys ), true ) . '</pre>';

                if (in_array(1, array($score_keys[0], @$score_keys[1], @$score_keys[2])) || in_array(4, array($score_keys[0], @$score_keys[1], @$score_keys[2]))) :

                    if (!in_array(0, array($score_keys[0], $score_keys[1], $score_keys[2]))) :

                        $scores[0] = $scores[$score_keys[1]]; // - 1;

                    endif;

                endif;

                arsort($scores);

                //echo '<pre>' . print_r( ( $scores ), true ) . '</pre>';

                $count_suggestions = 0;
                foreach ($scores as $index => $score) :
                    if ($count_suggestions == 3)
                        break;
                    array_push($suggested_results, $index);
                ?>
                    <div class="result">
                        <div class="result-circle-score">
                            <div class="circle" id="circle-<?php echo $index; ?>"></div>
                            <div class="score">
                                <?php
                                if (0 == $count_suggestions) :
                                    $dummy_score = rand(93, 97);
                                    $scores[$index] = $dummy_score;
                                    echo $dummy_score . '%';
                                    echo $results[$index]['imp_tag'];
                                elseif (1 == $count_suggestions) :
                                    $dummy_score = rand(89, 92);
                                    $scores[$index] = $dummy_score;
                                    echo $dummy_score . '%';
                                elseif (2 == $count_suggestions) :
                                    $dummy_score = rand(80, 88);
                                    $scores[$index] = $dummy_score;
                                    echo $dummy_score . '%';
                                endif;
                                ?>
                                <?php
                                //                    $score_per = round( $score  /  $total_score * 100, 0);
                                //                    echo $score_per . '%';
                                ?>
                            </div>
                        </div>
                        <div class="result-text">
                            <div class="title"><?php echo $results[$index]['title']; ?></div>
                            <div class="description"><?php echo $results[$index]['text']; ?></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                <?php
                    $count_suggestions++;
                endforeach; ?>

                <?php
                $count_o = -1;
                foreach ($results as $index => $result) :
                    if (!in_array($index, $suggested_results)) :
                ?>
                        <div class="result">
                            <div class="result-circle-score">
                                <div class="circle" id="circle-<?php echo $index; ?>"></div>
                                <div class="score">
                                    <?php
                                    $dummy_score_to = 70 - $count_o * 5;
                                    $dummy_score_from = $dummy_score_to - 2;
                                    $dummy_score = rand($dummy_score_from, $dummy_score_to);
                                    if ($dummy_score <= 0)
                                        $dummy_score = 1;
                                    $scores[$index] = $dummy_score;
                                    echo $dummy_score . '%';
                                    ?>
                                </div>
                            </div>
                            <div class="result-text">
                                <div class="title"><?php echo $results[$index]['title']; ?></div>
                                <div class="description"><?php echo $results[$index]['text']; ?></div>
                            </div>
                            <div class="clear"></div>
                        </div>
                <?php
                        $count_o++;
                    endif;
                endforeach;
                ?>
                <div class="quiz-edu-skin-left"></div>
                <div class="quiz-edu-skin-right"></div>
            </div>

            <table class="results-other">
                <?php
                foreach ($suggested_results as $suggested_result) :
                    $db_results[] = $results[$suggested_result]['title'];
                endforeach;

                global $wpdb;
                $wpdb->insert(
                    $wpdb->prefix . 'wsu_quiz_2_results',
                    array(
                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                        'answers' => json_encode($db_answers),
                        'results' => json_encode($db_results),
                        'created_at' => current_time('mysql', 0)
                    )
                );
                ?>
            </table>

            <h2 style="text-align: center; margin-top: 40px;" class="result-title">What's the next step?</h2>

            <div style="padding: 0 20px; width: 560px; margin: auto; max-width: 100%; font-size: 14px; font-family: 'GothamNarrow-Light', 'Open Sans', Arial; box-sizing: border-box;">

                <p style="font-size: 24px; line-height: 30px">
                    Even without an ATAR, you can enrol in courses at The College and start your path to success.
                </p>

                <p>If you didn't qualify for an ATAR, didn't finish school, or don't have any other equivalent study qualification, you can enrol in a 16-month Extended Diploma. Or, with an ATAR of 55 or above, you can finish an Integrated Diploma/Bachelor in just 12 months.</p>
                <p>Whichever course you complete, you'll then receive guaranteed entry into the corresponding bachelor degree at Western Sydney University - jumping straight into your second year.</p>
                <p style="font-family: 'GothamNarrow-Bold', 'Open Sans', Arial; font-size: 14px; color: #990033;">Don't wait</p>
                <p>Starting on the path to your dream career is easy - you can apply directly to Western Sydney University, and there's no application fee. Plus, all Diploma courses include Commonwealth supported places, and all eligible students can apply for interest-free HECS-HELP loans, to study now and pay later.</p>
                <p style="font-family: 'GothamNarrow-Bold', 'Open Sans', Arial;">
                    Better yet, you don't need to wait until next year to take the first steps towards your career.
                </p>

                <p style="margin-top: 40px; font-family: 'GothamNarrow-Bold', 'Open Sans', Arial; font-size: 18px; line-height: 22px; text-align: left; color: #990033; text-transform: uppercase;">
                    Don't let the ATAR define you.<br>Find your dream career now.
                </p>
            </div>

            <div class="buttons-bottom">
                <a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236632063;dc_trk_aid=434128447;dc_trk_cid=110341010;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a>
                <a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236894928;dc_trk_aid=434131216;dc_trk_cid=110342999;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Apply now<span>></span></a>
                <a href="mailto:study@westernsydney.edu.au">Talk to The College<span>></span></a>
            </div>

            <div style="padding: 0 20px; width: 560px; margin: 70px auto auto auto; max-width: 100%; box-sizing: border-box;">
                <p style="text-align: center;font-family: 'GothamNarrow-Light'; font-size: 24px; line-height: 30px; color: #FF5C5E;">
                    Know someone looking for their own pathway to uni?<br>Share the quiz:
                </p>

                <?php
                $post_url = urlencode(get_permalink());

                // Get current page title
                $post_title = str_replace(' ', '%20', get_the_title());

                // Construct sharing URL without using any script
                $twitterURL = 'https://twitter.com/intent/tweet?text=' . $post_title . '&amp;url=' . $post_url;
                $facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . $post_url;

                $content = '<div class="social-share-buttons">';
                $content .= '<a class="social-share-link social-share-quiz-facebook" href="' . $facebookURL . '" target="_blank" data-type="share-fb"><i class="fa fa-facebook"></i>&nbsp; Share</a>';
                $content .= '<a class="social-share-link social-share-quiz-twitter" href="' . $twitterURL . '" target="_blank" data-type="share-twitter"><i class="fa fa-twitter"></i> Tweet</a>';
                $content .= '</div>';
                echo $content;
                //    do_action( 'ssm_social_sharing_buttons' );
                ?>
            </div>

            <div class="clear"></div>

            <div style="margin-top: 70px; text-align: center">
                <a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.235842224;dc_trk_aid=433496458;dc_trk_cid=109884147;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/wsu-quiz/WSU_Logo_TheCollege.jpg" alt="Western Sydney University - The College" width="175" />
                </a>
            </div>

            <?php
            $colors = array(
                '#990033',
                '#ED0033',
                '#FF5C5E',

                '#663399',
                '#006699',

                '#CC99FF',
                '#82B4C8',
                '#3A3537',
            );
            ?>

            <script>
                jQuery(document).ready(function($) {
                    <?php $count = 0;
                    foreach ($scores as $index => $score) : ?>
                        $('#circle-<?php echo $index; ?>').circleProgress({
                            value: <?php echo $score  /  100; ?>,
                            size: 150,
                            fill: '<?php echo $colors[$count]; ?>'
                        });
                    <?php $count++;
                    endforeach; ?>
                });
            </script>

            <!-- Quiz | SSM | Seventh Street Media | Quiz 2 > Personality Test > Landing Page Skins -->
            <IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.235842224;dc_trk_aid=433496458;dc_trk_cid=109884147;ord=<?php echo $cachebuster; ?>;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="">

            <?php else : // Show Quiz
            if ($questions) :
            ?>

                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="form-wsu-quiz-2">
                    <section class="quiz-section-edu">
                        <div id="start-wrap">
                            <div class="start-wrap-mobile" id="start-wrap-mobile">
                                <div class="heading">
                                    My<br>
                                    Future</br>
                                    Finder.
                                </div>
                                <div class="clear"></div>
                                <div class="bottom">
                                    <div class="sub-heading">
                                        YOUR ATAR<br>DOESN'T<br>DETERMINE<br>YOUR FUTURE.<br>YOU DO.
                                    </div>
                                    <p>
                                        Find your pathway to uni<br>in just a few minutes.
                                    </p>
                                    <div class="button btn-start" id="btn-start">
                                        Start the Quiz<span>></span>
                                    </div>
                                </div>
                            </div>

                            <div class="start-wrap-desktop" id="start-wrap-desktop">
                                <div class="start-wrap-desktop-inner">
                                    <div class="heading">
                                        My<br>
                                        Future</br>
                                        Finder.
                                    </div>
                                    <div class="clear"></div>
                                    <div class="sub-heading">
                                        YOUR ATAR DOESN'T DETERMINE YOUR FUTURE.<br>YOU DO.
                                    </div>
                                    <p>
                                        Find your pathway to uni in just a few minutes.
                                    </p>
                                    <div class="button btn-start" id="btn-start">
                                        Start the Quiz<span>></span>
                                    </div>
                                    <div class="quiz-edu-skin-left"></div>
                                    <div class="quiz-edu-skin-right"></div>
                                </div>
                            </div>
                        </div>

                        <div class="questions-wrap" id="questions-wrap" style="display: none;">
                            <div id="brag-logo">
                                <a href="<?php echo site_url(); ?>" target="_blank">
                                    <img src="<?php echo ICONS_URL; ?>The-Brag_combo.svg" alt="Brag Magazine" width="175" />
                                </a>
                            </div>
                            <div class="quiz-header">
                                <div class="left">
                                    My<br>
                                    Future<br>
                                    Finder.<br>
                                </div>
                                <div class="right">
                                    <img src="<?php echo get_template_directory_uri(); ?>/images/Afifa_2_bw2.jpg" id="hero-image">
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="progress" style="display: none;">
                                <div class="progress-indicator"></div>
                                <div class="question-numbers"><span id="current_q">1</span> / <?php echo count($questions); ?></div>
                            </div>
                            <?php $i = 0;
                            foreach ($questions as $question) : ?>
                                <div class="quiz-details" id="quiz-details-<?php echo $i; ?>" data-qz="<?php echo $i; ?>" data-qn="<?php echo $question['question_no']; ?>" style="display: none;">

                                    <div class="quiz-question-edu"><?php echo $question['question']; ?>
                                        <?php if (!isset($question['type']) || !in_array($question['type'], array('textarea', 'scale'))) : ?>
                                            <small>Choose one</small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="quiz-answers">
                                        <?php
                                        if (isset($question['type']) && 'textarea' == $question['type']) :
                                        ?>
                                            <textarea cols="30" rows="6" name="answers[<?php echo $question['question_no']; ?>]" id="answer_<?php echo $i; ?>" class="textarea" autocomplete="off"></textarea>
                                        <?php
                                        elseif (isset($question['type']) && 'scale' == $question['type']) :
                                            $mid = round(array_sum(range($question['min'], $question['max'])) / count(range($question['min'], $question['max'])), 0);
                                        ?>
                                            <div class="slidecontainer" style="position: relative; margin-bottom: 30px;">
                                                <input style="" type="range" min="<?php echo $question['min']; ?>" max="<?php echo $question['max']; ?>" value="<?php echo $mid; ?>" class="slider" name="answers[<?php echo $question['question_no']; ?>]" id="answer_<?php echo $i; ?>">
                                                <span class="slider-value"><?php echo $mid; ?></span>
                                            </div>
                                            <?php
                                        elseif ($question['answers']) :
                                            //                            $question['answers'] = shuffle_assoc( $question['answers'] );
                                            //                        endif;
                                            $j = 0;
                                            foreach ($question['answers'] as $answer_text => $result_score) :
                                            ?>
                                                <?php if (isset($question['type']) && 'radio' == $question['type']) : ?>
                                                    <div class="quiz-answer">
                                                        <input type="radio" class="radio answer_choice" name="answers[<?php echo $question['question_no']; ?>][]" id="answer_<?php echo $i . '_' . $j; ?>" value="<?php echo $answer_text; ?>">
                                                        <label for="answer_<?php echo $i . '_' . $j; ?>" class="answer_choice_label"><?php echo $answer_text; ?></label>
                                                    </div>
                                                <?php else : ?>
                                                    <div class="quiz-answer" style="<?php echo 3 == count($question['answers']) ? 'width: calc( 33% - 10px );' : ''; ?>">
                                                        <input type="checkbox" class="radio answer_choice" name="answers[<?php echo $question['question_no']; ?>][]" id="answer_<?php echo $i . '_' . $j; ?>" value="<?php echo $answer_text; ?>">
                                                        <label for="answer_<?php echo $i . '_' . $j; ?>" class="answer_choice_label"><?php echo $answer_text; ?></label>
                                                    </div>
                                                <?php endif; ?>
                                        <?php $j++;
                                            endforeach; // For Each Answer of Question
                                        endif; // If Question has Answers 
                                        ?>
                                    </div>
                                    <div class="clear"></div>
                                    <div class="navigate">
                                        <div class="error" id="error-<?php echo $question['question_no']; ?>"></div>
                                        <div class="button btn-prev" style="display: none;">
                                            < Back</div>
                                                <?php if ($i < (count($questions) - 1)) : ?>
                                                    <div class="button btn-next">Next<span>></span></div>
                                                <?php else : ?>
                                                    <div class="button btn-next" id="btn-submit">Submit<span>></span></div>
                                                    <!--<input type="submit" name="submit" value="Submit" class="button" id="btn-submit">-->
                                                <?php endif; ?>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                <?php $i++;
                            endforeach; // For Each $question in $questions 
                                ?>
                                <div class="clear"></div>
                                <div class="quiz-edu-skin-left"></div>
                                <div class="quiz-edu-skin-right"></div>
                                </div>
                    </section>
                    <div class="clear"></div>
                </form>
            <?php endif; // If there are questions 
            ?>
        <?php endif; // If $_POST is set 
        ?>
    </div>

<?php else : ?>
    <div class="quiz-story-edu">
        <form method="post" action="/wp-login.php?action=postpass">
            <p>This content is password protected. To view it please enter your password below:</p>
            <p>Password:<br />
                <input type="password" size="20" id="pwbox-<?php echo get_the_ID(); ?>" name="post_password" /><br />
                <input type="submit" value="Enter" name="Submit" />
            </p>
        </form>
    </div>
<?php endif; ?>

<?php get_template_part('footer-quiz-edu');

function shuffle_assoc($list)
{
    if (!is_array($list)) return $list;
    $keys = array_keys($list);
    shuffle($keys);
    $random = array();
    foreach ($keys as $key)
        $random[$key] = $list[$key];
    return $random;
}
