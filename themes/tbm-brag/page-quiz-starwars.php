<?php /* Template Name: Quiz (Star Wars) */ ?>
<?php get_template_part( 'header-quiz-starwars' );
$page_url = (get_permalink());
$page_title = get_the_title();
wp_enqueue_script( 'quiz-starwars', get_template_directory_uri() . '/js/quiz-starwars.js', array ( 'jquery' ), '20181010', true);
$args = array(
    'url'   => admin_url( 'admin-ajax.php' ),
    'page_url' => $page_url,
    'page_title' => $page_title,
);
wp_localize_script( 'quiz-starwars', 'quiz_starwars', $args );
?>

<?php if ( ! post_password_required() ) : ?>

<div class="quiz-story" style="position: relative;">
<!--    <div style="position: fixed; margin-left: -200px">
        <img src="<?php echo get_template_directory_uri(); ?>/images/quiz-starwars/quiz-bg.png" style="max-width: none;">
    </div>-->
<?php
    $results = array(
        array(
            'title' => 'Youngling',
            'text' => 'You have a sensitivity to the Force, and show some potential... but you\'ve got a long way to go.',
            'img' => get_template_directory_uri() . '/images/quiz-starwars/Result1_youngling.png',
        ),
        array(
            'title' => 'Padawan',
            'text' => 'You\'re beginning to learn the ways of the Force, but have a little way to go before you pass your trials.',
            'img' => get_template_directory_uri() . '/images/quiz-starwars/Result2_padwan.png',
        ),
        array(
            'title' => 'Jedi Knight',
            'text' => 'You\'ve passed your trials and become part of the Jedi Order - with some more experience, mastery awaits.',
            'img' => get_template_directory_uri() . '/images/quiz-starwars/Result3_JediKnight.png',
        ),
        array(
            'title' => 'Jedi Master',
            'text' => 'You\'re wise beyond your years, and a master of the Force - a seat on the Jedi Council may one day be yours.',
            'img' => get_template_directory_uri() . '/images/quiz-starwars/Result4_JediMaster.png',
        ),
        array(
            'title' => 'Jedi Council',
            'text' => 'With your knowledge, you\'re wise enough to take a seat on the Jedi Council alongside the greatest of your order.',
            'img' => get_template_directory_uri() . '/images/quiz-starwars/Result5_JediCouncil.png',
            
        ),
        array(
            'title' => 'Grand Master',
            'text' => 'There\'s little about the Force you don\'t know, and you\'ve attained the highest rank there is in the Jedi Order.',
            'img' => get_template_directory_uri() . '/images/quiz-starwars/Result6_GrandJedi.png',
        )
    );
    
    /*
    $questions = array(
        array(
            'question_no' => 1,
            'question' => 'Not counting his death scream, how many lines of dialogue does Boba Fett have in the original trilogy?',
            'answers' => array(
                '12',
                '6',
                '2',
                '4'
            ),
            'correct_answer' => '4'
        ),
        array(
            'question_no' => 2,
            'question' => 'What is Grand Moff Tarkin\'s first name?',
            'answers' => array(
                'Sheev',
                'Jones',
                'Wilhuff',
                'Armond'
            ),
            'correct_answer' => 'Wilhuff'
        ),
        array(
            'question_no' => 3,
            'question' => 'What was the original working title of Star Wars: Return Of The Jedi?',
            'answers' => array(
                'Starkiller',
                'Blue Harvest',
                'It\'s A Trap',
                'Endless Space'
            ),
            'correct_answer' => 'Blue Harvest'
        ),
        array(
            'question_no' => 4,
            'question' => 'Morris Bush played the role of which bounty hunter in The Empire Strikes Back?',
            'answers' => array(
                'Greedo',
                'IG-88',
                'Dengar',
                '4-Lom'
            ),
            'correct_answer' => 'Dengar'
        ),
        array(
            'question_no' => 5,
            'question' => 'What was Lando's official title in Cloud City?',
            'answers' => array(
                'President',
                'Baron Administrator',
                'Lieutenant',
                'Grand Moff'
            ),
            'correct_answer' => 'Baron Administrator'
        ),
        array(
            'question_no' => 6,
            'question' => 'How old is Chewbacca in The Force Awakens?',
            'answers' => array(
                '40',
                '65',
                '152',
                '234'
            ),
            'correct_answer' => '234'
        ),
        array(
            'question_no' => 7,
            'question' => 'What was the name of the officer Darth Vader forced choked for his lack of faith?',
            'answers' => array(
                'Armitage Hux',
                'Ronald Piett',
                'Conan Antonio Motti',
                'Charles Ozzel'
            ),
            'correct_answer' => 'Conan Antonio Motti'
        ),
        array(
            'question_no' => 8,
            'question' => 'Tibanna gas mines can be found on which planet?',
            'answers' => array(
                'Tatooine',
                'Bespin',
                'Endor',
                'Hoth'
            ),
            'correct_answer' => 'Bespin'
        ),
        array(
            'question_no' => 9,
            'question' => 'What is the exact running time of A New Hope?',
            'answers' => array(
                '121 minutes',
                '98 minutes',
                '152 minutes',
                '100 minutes'
            ),
            'correct_answer' => '121 minutes'
        ),
        array(
            'question_no' => 10,
            'question' => 'Which avant-garde director was given the opportunity to direct The Empire Strikes Back, but turned it down because the thought of the film “gave him a headache”? ',
            'answers' => array(
                'Alejandro Jodorowsky',
                'Ken Russell',
                'David Cronenberg',
                'David Lynch'
            ),
            'correct_answer' => 'David Lynch'
        ),
    );
    */
    
    $questions = array(
        array(
            'question_no' => 1,
            'question' => 'What planet did Princess Leia grow up on?',
            'answers' => array(
                'Earth',
                'Coruscant',
                'Alderaan',
            ),
            'correct_answer' => 'Alderaan'
        ),
        array(
            'question_no' => 2,
            'question' => 'Who was revered as a god by the Ewoks?',
            'answers' => array(
                'R2-D2',
                'Luke',
                'C-3PO',
            ),
            'correct_answer' => 'C-3PO'
        ),
        array(
            'question_no' => 3,
            'question' => 'Which character was introduced as a "protocol Droid"?',
            'answers' => array(
                'R2-D2',
                'Lando',
                'C-3PO',
            ),
            'correct_answer' => 'C-3PO'
        ),
        array(
            'question_no' => 4,
            'question' => 'How many suns does Tatooine have?',
            'answers' => array(
                'One',
                'Two',
                'Three',
            ),
            'correct_answer' => 'Two'
        ),
        array(
            'question_no' => 5,
            'question' => 'Which of Luke\'s hands gets cut off by Vader?',
            'answers' => array(
                'Right',
                'Left',
                'Vader didn\'t do it',
            ),
            'correct_answer' => 'Right'
        ),
        array(
            'question_no' => 6,
            'question' => 'How long did Amidala reign as Queen of Naboo?',
            'answers' => array(
                '10 years',
                '12 years',
                '8 years',
            ),
            'correct_answer' => '8 years'
        ),
        array(
            'question_no' => 7,
            'question' => 'Who ends up serving drinks on Jabba\'s barge?',
            'answers' => array(
                'Luke',
                'R2-D2',
                'Leia',
            ),
            'correct_answer' => 'R2-D2'
        ),
        array(
            'question_no' => 8,
            'question' => 'The palace of the Queen of Naboo is called:',
            'answers' => array(
                'Coruscant',
                'Theed Palace',
                'Yavin',
            ),
            'correct_answer' => 'Theed Palace'
        ),
        array(
            'question_no' => 9,
            'question' => 'What kind of Droid is R2-D2?',
            'answers' => array(
                'Protocol',
                'Astromech',
                'Destroyer',
            ),
            'correct_answer' => 'Astromech'
        ),
        array(
            'question_no' => 10,
            'question' => 'What is the term for a Jedi Master\'s pupil?',
            'answers' => array(
                'Padawan',
                'Learner',
                'Youngling',
            ),
            'correct_answer' => 'Padawan'
        ),
        array(
            'question_no' => 11,
            'question' => 'What is Luke\'s Uncle Owen?',
            'answers' => array(
                'A Droid repairman',
                'A moisture farmer',
                'A soldier',
            ),
            'correct_answer' => 'A moisture farmer'
        ),
        array(
            'question_no' => 12,
            'question' => 'Who gets shot in the shoulder at the shield generator on Endor?',
            'answers' => array(
                'Luke',
                'Leia',
                'Han',
            ),
            'correct_answer' => 'Leia'
        ),
        array(
            'question_no' => 13,
            'question' => 'What colour of light signals the start of the Podrace?',
            'answers' => array(
                'Green',
                'Yellow',
                'Blue',
            ),
            'correct_answer' => 'Green'
        ),
        array(
            'question_no' => 14,
            'question' => 'Who advises Anakin, "Don\'t try to grow up too fast"?',
            'answers' => array(
                'Obi-Wan',
                'Yoda',
                'Padmé',
            ),
            'correct_answer' => 'Padmé'
        ),
        array(
            'question_no' => 15,
            'question' => 'Who confirms that Darth Vader is indeed Luke\'s father?',
            'answers' => array(
                'Obi-Wan',
                'Leia',
                'Yoda',
            ),
            'correct_answer' => 'Yoda'
        ),
    );
    
//    for( $i = 1; $i < 10; $i++ ) unset( $questions[$i] );
    
    shuffle( $questions );
    ?>
<script>
    var total_questions = <?php echo count( $questions ); ?>;
    var questions = [];
    <?php foreach ( $questions as $question ) : ?>
    questions[<?php echo $question['question_no']; ?>] = '<?php echo $question['correct_answer']; ?>';
    <?php endforeach; ?>
        
    var results= [];
    <?php foreach ( $results as $key =>  $result ) : ?>
    results[<?php echo $key; ?>] = <?php echo json_encode( $result ); ?>;
    <?php endforeach; ?>
</script>

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="form-quiz">
        <section class="quiz-section">
        <div class="quiz-skin-left" style="">
            <a href="https://maas.museum/event/star-wars-identities-the-exhibition/?utm_campaign=Star_Wars_Identities_MAAS&utm_medium=Direct%20Buy&utm_source=Seventh%20Street%20Media%20&utm_content=Interactive_Quiz_UTM%20-%201x1" target="_blank" style="width: 100%; height: 100%; display: block;">
            <img src="<?php echo get_template_directory_uri(); ?>/images/quiz-starwars/skin-left300px.png" style="height: 100%; width: auto; max-width: none;">
            </a>
        </div>
        <div class="quiz-skin-right" style="">
            <a href="https://maas.museum/event/star-wars-identities-the-exhibition/?utm_campaign=Star_Wars_Identities_MAAS&utm_medium=Direct%20Buy&utm_source=Seventh%20Street%20Media%20&utm_content=Interactive_Quiz_UTM%20-%201x1" target="_blank" style="width: 100%; height: 100%; display: block;">
            <img src="<?php echo get_template_directory_uri(); ?>/images/quiz-starwars/skin-right300px.png" style="height: 100%; widht: auto; max-width: none;">
            </a>
        </div>
        <?php if ( $questions ) : ?>
        <div class="questions-wrap" id="questions-wrap">
            <div class="quiz-header">
                <img src="<?php echo get_template_directory_uri(); ?>/images/StarWarsIdentities_header.png">
                <br>
                <p style="text-align: right; font-size: .8rem; color: #999; margin: 0; padding: 0;">&copy; &amp; TM 2018 Lucasfilm Ltd. All Rights Reserved. Used Under Authorization.</p>
            </div>
            <div>
                <p>With the incredible Star Wars: Identities exhibition heading to Australia for the first time in November, there's no better time to test your knowledge on everything <span style="font-style: italic;">Star Wars</span>.</p>
                <p>See if you can answer all 15 questions below and take your place as a Grand Master, then challenge your friends to do the same. <strong>May the Force be with you!</strong></p>
                <p>&nbsp;</p>
            </div>
            <div class="quiz-details-wrap">
        <?php $i = 0; foreach ( $questions as $question ) : ?>
            <div class="quiz-details" id="quiz-details-<?php echo $i; ?>" data-qz="<?php echo $i; ?>" data-qn="<?php echo $question['question_no']; ?>" style="<?php echo $i > 0 ? 'display: none;' : ''; ?>">
                <div class="quiz-question">
                    <div class="question-number">
                        <span><?php echo $i + 1; ?></span>
                    </div>
                    <div class="question-text">
                        <span><?php echo $question['question']; ?></span>
                    </div>
                </div>
                
                <div class="quiz-answers">
                    <?php
                    if ( $question['answers'] ) :
                        $question['answers'] = shuffle_assoc( $question['answers'] );
                        $j = 0;
                        foreach ( $question['answers'] as $answer_text ) :
                    ?>
                   <div class="quiz-answer">
                        <input type="radio" class="radio answer_choice<?php echo $i == 0 ? ' answer_choice-starwars-start' : ''; echo ( $i + 1 ) == count( $questions ) ? ' answer_choice-starwars-last': ''; ?>" name="answers[<?php echo $question['question_no']; ?>]" id="answer_<?php echo $i . '_' . $j; ?>" value="<?php echo $answer_text; ?>">
                        <label for="answer_<?php echo $i . '_' . $j; ?>" class="answer_choice_label answer_choice_label-starwars"><span><?php echo $answer_text; ?></span></label>
                        <div class="quiz-answer-result <?php echo $answer_text == $question['correct_answer'] ? 'quiz-answer-result-correct' : 'quiz-answer-result-incorrect'; ?>"></div>
                    </div>
                    <?php $j++; 
                        endforeach; // For Each Answer of Question
                    endif; ?>
                </div>
                <div class="clear"></div>
            </div>
        <?php $i++; endforeach; // For Each $question in $questions ?>
            <div class="clear"></div>

            <div style="padding: 10px;">
                <div class="progress">
                    <div class="progress-indicator" style="width: <?php echo 100 / count( $questions ); ?>%;"></div>
                </div>
            </div>
            <div class="question-numbers">Question <span id="current_q">1</span> / <?php echo count( $questions ); ?></div>
            </div>

            <div id="results" style="display: none;">
                <div class="results-wrap">
                <p style="text-transform: uppercase; font-size: 1.2rem;">You scored <span id="score"></span>/<?php echo count( $questions ); ?>, and achieved the rank of:</p>
                <div class="result-images">
                    <?php foreach( $results as $key => $result ): ?>
                        <div class="result-image" id="result-image<?php echo $key; ?>">
                            <img src="<?php echo $result['img']; ?>">
                            <p style="font-size: 1.5rem; line-height: 1.8rem; padding-left: 12px;"><?php echo $result['text']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="padding: 0 20px; width: 560px; margin: 40px auto auto auto; max-width: 100%; box-sizing: border-box; text-align: center;">
                    
                <div style="text-transform: uppercase; font-size: 1.2rem; padding: 10px;">Share your results, and challenge your friends:</div>
                <?php
                $page_url = urlencode(get_permalink());
                $page_title = str_replace( ' ', '%20', get_the_title());
                
                $facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . $page_url;
                $twitterURL = 'https://twitter.com/intent/tweet?text=' . $page_title . '&amp;url=' . $page_url;

                $content = '<div class="social-share-buttons">';
                $content .= '<a class="social-share-link social-share-quiz-facebook" id="social-share-quiz-facebook" href="' . $facebookURL . '" target="_blank" data-type="share-fb"><i class="fa fa-facebook"></i>&nbsp; Share</a>';
                $content .= '<a class="social-share-link social-share-quiz-twitter" id="social-share-quiz-twitter" href="' . $twitterURL . '" target="_blank" data-type="share-twitter"><i class="fa fa-twitter"></i> Tweet</a>';
                $content .= '</div>';
                echo $content;
                ?>
                
                </div>
                </div>
            </div>
        </div>
        <?php endif; // If there are questions ?>
        <div>
            <p>&nbsp;</p>
            <p>You know a bit about <span style="font-style: italic;">Star Wars</span>, but do you know who you might be be if you were a character living in that galaxy far, far away?</p>
            <p>Well, you can find out as you build your own personal <span style="font-style: italic;">Star Wars</span> hero in an  interactive exhibition featuring 200 original <span style="font-style: italic;">Star Wars</span> props, costumes and more, heading to Australia for the very first time.</p>
            <p>Designed for visitors of all ages, you'll be able to explore your own identity and learn about the forces that shape you through a series of interactive stations dotted throughout the exhibition, and each answer you give will define the traits of your unique <span style="font-style: italic;">Star Wars</span> character, who you'll even get to meet at the end of the exhibition.</p>
            <p>Along the way, you'll discover rare treasures from the Lucasfilm archives and see original costumes, props, models and artworks up close as you go behind the scenes of the movie-making process. </p>
            <p>There's R2-D2 & BB-8, the Millenium Falcon, Yoda from <span style="font-style: italic;">The Empire Strikes Back</span>, Darth Vader's suit from <span style="font-style: italic;">Return of the Jedi</span>, and plenty more to see.</p>
            <p>Exclusive to the Powerhouse Museum in Sydney, this is a once-in-a-lifetime chance to get to know Luke, Han, Leia, Yoda, and more on a whole new level, and even get to know a little more about how you'd fit in to the <span style="font-style: italic;">Star Wars</span> universe, too.</p>
            <p>You can find out more below.</p>
            <p style="text-align: center; margin-top: 40px;">
                <a href="https://maas.museum/event/star-wars-identities-the-exhibition/?utm_campaign=Star_Wars_Identities_MAAS&utm_medium=Direct%20Buy&utm_source=Seventh%20Street%20Media%20&utm_content=Interactive_Quiz_UTM%20-%201x1" target="_blank" class="link-find-out-more" style="/*border-top: 5px solid #d41f26; border-bottom: 5px solid #d41f26; */ font-size: 2rem; color: #000; padding: 25px 0px 20px; display: inline-block; text-decoration: none;">
                    FIND OUT MORE
                </a>
            </p>
        </div>
        
    </section>
    </form>
    
    

</div>

<?php else: ?>
<div class="quiz-story">
    <form method="post" action="/wp-login.php?action=postpass">
    <p>This content is password protected. To view it please enter your password below:</p>
    <p>Password:<br/>
    <input type="password" size="20" id="pwbox-<?php echo get_the_ID(); ?>" name="post_password"/><br/>
    <input type="submit" value="Enter" name="Submit"/></p>
    </form>
</div>
<?php endif; ?>

<?php get_template_part( 'footer-quiz-starwars' );

function shuffle_assoc($list) { 
    if (!is_array($list)) return $list; 
    $keys = array_keys($list); 
    shuffle($keys); 
    $random = array(); 
    foreach ($keys as $key) 
        $random[$key] = $list[$key]; 
    return $random; 
}