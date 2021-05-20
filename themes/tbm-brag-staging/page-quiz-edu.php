<?php /* Template Name: Quiz (Edu) */ ?>
<?php get_template_part( 'header-quiz-edu' );
wp_enqueue_script( 'quiz-edu', get_template_directory_uri() . '/js/quiz-edu.js', array ( 'jquery' ), '20180820', true);
wp_enqueue_script( 'quiz-edu-cricle-progress', get_template_directory_uri() . '/js/circle-progress/circle-progress.min.js', array ( 'jquery' ), '20180815', true);
?>

<?php if ( ! post_password_required() ) : ?>

<div class="quiz-story-edu<?php echo isset( $_POST ) ? ' result' : ''; ?>">
    <?php
    /*
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
    endif;
    */
?>
    <?php // $featured_img_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'cover-story' ); ?>
    <!--<img src="<?php // echo $featured_img_src[0]; ?>" style="display: block; margin: auto;">-->
    
<?php
    $cachebuster = time();
    $results = array(
        array(
            'title' => 'Arts',
            'text' => '<p>As one of the most versatile qualifications on offer, Arts covers a range of humanities subjects such as religion, cultures and languages, and is the key to kick-starting your degree in teaching, psychology, law, literature, politics and more.</p>
            <p class="buttons-bottom"><a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236067383;dc_trk_aid=433608270;dc_trk_cid=110081426;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a></p>',
            'link' => 'https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236067383;dc_trk_aid=433608270;dc_trk_cid=110081426;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=', // 'https://www.westernsydney.edu.au/thecollege/courses_and_pathways/arts',
            'imp_tag' => '<IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.236067383;dc_trk_aid=433608270;dc_trk_cid=110081426;ord=' . $cachebuster . ';dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="Advertisement">',
        ),
        array(
            'title' => 'Communication Arts &amp; Design',
            'text' => '<p>Forging a career in the media industry means staying ahead of the latest technology trends and forms of communication, and these courses are designed to help you thrive in this environment by preparing you for a career in journalism, public relations, advertising, media production and more.</p>
            <p class="buttons-bottom"><a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236363931;dc_trk_aid=433579640;dc_trk_cid=110083607;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a></p>',
            'link' => 'https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236363931;dc_trk_aid=433579640;dc_trk_cid=110083607;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=', // 'https://www.westernsydney.edu.au/thecollege/courses_and_pathways/communication_arts_and_design',
            'imp_tag' => '<IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.236363931;dc_trk_aid=433579640;dc_trk_cid=110083607;ord=' . $cachebuster . ';dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="Advertisement">',
        ),
        array(
            'title' => 'Health Science &amp; Nursing',
            'text' => '<p>Bringing together a comprehensive introduction of health science, personal development, nursing fundamentals and interpersonal skills, and Health Science and Nursing courses are designed to get you on-track to your career in the health sector.</p>
            <p class="buttons-bottom"><a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236067131;dc_trk_aid=433581950;dc_trk_cid=109978821;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a></p>',
            'link' => 'https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236067131;dc_trk_aid=433581950;dc_trk_cid=109978821;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=', // 'https://www.westernsydney.edu.au/thecollege/courses_and_pathways/health_science_and_nursing',
            'imp_tag' => '<IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.236067131;dc_trk_aid=433581950;dc_trk_cid=109978821;ord=' . $cachebuster . ';dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="Advertisement">',
        ),
        array(
            'title' => 'Science',
            'text' => '<p>With a host of experienced teachers and state-of-the-art facilities, Western Sydney University\'s Science courses combine advanced academic knowledge with practical training to prepare you not only for your degree, but your future career.</p>
            <p class="buttons-bottom"><a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236067161;dc_trk_aid=433579652;dc_trk_cid=110083382;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a></p>',
            'link' => 'https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236067161;dc_trk_aid=433579652;dc_trk_cid=110083382;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=', // 'https://www.westernsydney.edu.au/thecollege/courses_and_pathways/science',
            'imp_tag' => '<IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.236067161;dc_trk_aid=433579652;dc_trk_cid=110083382;ord=' . $cachebuster . ';dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="Advertisement">',
        ),
        array(
            'title' => 'Social Science, Policing &amp; Criminal Justice',
            'text' => '<p>These courses are designed to expand your knowledge of the culturally diverse social environment, preparing you for a degree with a focus in sociology, tourism and more. Or, for a career in criminal justice or policing, courses can provide practical, real-world experience to prepare you for the NSW Police Force Academy or a criminal and community justice degree.</p>
            <p class="buttons-bottom"><a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236363934;dc_trk_aid=433663213;dc_trk_cid=109978824;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a></p>',
            'link' => 'https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236363934;dc_trk_aid=433663213;dc_trk_cid=109978824;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=', // 'https://www.westernsydney.edu.au/thecollege/courses_and_pathways/social_science_policing_and_criminal_justice',
            'imp_tag' => '<IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.236363934;dc_trk_aid=433663213;dc_trk_cid=109978824;ord=' . $cachebuster . ';dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="Advertisement">',
        ),
        array(
            'title' => 'Construction &amp; Engineering',
            'text' => '<p>Combined with hands-on experience in Construction Management, Building Design Management and Engineering, these courses are designed to provide you with the skills and abilities to work in civil, electrical or construction engineering, robotics and mechatronics.</p>
            <p class="buttons-bottom"><a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236064605;dc_trk_aid=433581941;dc_trk_cid=110083379;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a></p>',
            'link' => 'https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236064605;dc_trk_aid=433581941;dc_trk_cid=110083379;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=', // 'https://www.westernsydney.edu.au/thecollege/courses_and_pathways/construction_and_engineering'
            'imp_tag' => '<IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.236064605;dc_trk_aid=433581941;dc_trk_cid=110083379;ord=' . $cachebuster . ';dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="Advertisement">',
        ),
        array(
            'title' => 'Information &amp; Communications Technology',
            'text' => '<p>These courses will provide you with the skills and knowledge base to work confidently in programming, database development and IT support, and solve a range of technical problems within ICT.</p>
            <p class="buttons-bottom"><a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236064608;dc_trk_aid=433662028;dc_trk_cid=110083388;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a></p>',
            'link' => 'https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236064608;dc_trk_aid=433662028;dc_trk_cid=110083388;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=', // 'https://www.westernsydney.edu.au/thecollege/courses_and_pathways/information_and_communications_technology',
            'imp_tag' => '<IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.236064608;dc_trk_aid=433662028;dc_trk_cid=110083388;ord=' . $cachebuster . ';dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="Advertisement">',
        ),
        array(
            'title' => 'Business',
            'text' => '<p>Whether you dream of being the CEO of your own company or becoming a senior executive in a global enterprise, a Business course will equip you with the knowledge, experience and contacts to get you on your way.</p>
            <p class="buttons-bottom"><a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236067386;dc_trk_aid=433662016;dc_trk_cid=110084933;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a></p>',
            'link' => 'https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236067386;dc_trk_aid=433662016;dc_trk_cid=110084933;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=', // 'https://www.westernsydney.edu.au/thecollege/courses_and_pathways/business',
            'imp_tag' => '<IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.236067386;dc_trk_aid=433662016;dc_trk_cid=110084933;ord=' . $cachebuster . ';dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="Advertisement">',
        ),
    );
    
    $questions = array(
        
        1 => 
        array(
            'question_no' => 1,
            'question' => 'What were your favourite things to do in school?',
            'answers' => array(
                'Leading the debate team' => 0,
                'Writing or learning languages' => 1,
                'Sketching or Photoshopping' => 1,
                'Playing sport' => 2,
                'Biology class' => 2,
                'Mixing things in the chemistry lab' => 3,
                'Learning about society and culture' => 4,
                'Brushing up on the legal system' => 4,
                'Working with tools' => 5,
                'Maths and problem solving' => 5,
                'Tinkering with computers and code' => 6,
                'Dreaming up business ideas' => 7
            ),
        ),

        2 => 
        array(
            'question_no' => 2,
            'question' => 'What do you see yourself doing in your career?',
            'answers' => array(
                'Understanding other perspectives' => 0,
                'Speaking to the public' => 1,
                'Creating beautiful art' => 1,
                'Helping people stay healthy' => 2,
                'Taking care of people' => 2,
                'Learning more about the world' => 3,
                'Improving the community' => 4,
                'Keeping people safe' => 4,
                'Building something big' => 5,
                'Inventing and innovating' => 5,
                'Working with new technology' => 6,
                'Making ideas profitable' => 7,
            ),
        ),
        
        3 => 
        array(
            'question_no' => 3,
            'question' => 'Which workplaces would you thrive in?',
            'answers' => array(
                'A classroom' => 0,
                'A newsroom' => 1,
                'A design studio' => 1,
                'A gym' => 2,
                'A hospital' => 2,
                'A laboratory' => 3,
                'A community centre' => 4,
                'A legal practice' => 4,
                'A construction site' => 5,
                'A workshop' => 5,
                'A computer lab' => 6,
                'An office' => 7,
            ),
        ),

        4 =>
        array(
            'question_no' => 4,
            'question' => 'Would you like to travel for work?',
            'answers' => array(
                'Yes' => array( 0, 1, 3, 6, 7, ),
                'No' => array( 2, 4, 5, ),
            ),
            'type' => 'radio',
        ),

//        [0] Arts
//        [1] Communication Arts & Design
//        [2] Health Science & Nursing
//        [3] Science
//        [4] Social Science, Policing & Criminal Justice
//        [5] Construction & Engineering
//        [6] Information & Communications Technology
//        [7] Business
        
        5 => 
        array(
            'question_no' => 5,
            'question' => 'What sort of hours would you like to work?',
            'answers' => array(
                'Flexible hours' => array( 0, 1, 6, 7, ),
//                'Flexible hours / Nine-to-five' => array( 1,  ),
//                'Nine-to-five' => 2,
                'Shift work' => array( 2, 4, ),
                'Regular hours' => array( 1, 2, 3, 4, 5, 6, 7, ),
                'I don\'t mind' => NULL,
//                'Shift work / Regular hours' => 4,
//                'Flexible hours / Regular hours' => array( 6, 7, ),
            ),
            'type' => 'radio',
        ),
        
        6 =>
        array(
            'question_no' => 6,
            'question' => 'Who would you like as your mentors?',
            'answers' => array(
                'Louis Theroux' => 0,
                'Lee Lin Chin' => 1,
                'Andy Warhol' => 1,
                'Serena Williams' => 2,
                'Mother Teresa' => 2,
                'Neil deGrasse Tyson' => 3,
                'Oprah Winfrey' => 4,
                'Julia Gillard' => 4,
                'Scotty Cam' => 5,
                'Elon Musk' => 5,
                'Steve Jobs' => 6,
                'Richard Branson' => 7,
            ),
        ),
        
        /*
        array(
            'question' => 'It\'s deadline day at The Brag - which task do you help with first?',
            'answers' => array(
                'A bit of everything!' => 0,
                'Writing a news announcement' => 1,
                'Laying out the pages of the magazine' => 1,
                'Leading the mandatory yoga break' => 2,
                'Managing everyone\'s stress' => 2,
                'Researching a complex topic' => 3,
                'Analysing our social media audience' => 4,
                'Clearing an article with the legal team' => 4,
                'Building our lighting rig' => 5,
                'Troubleshooting our electronics' => 5,
                'Updating the website code' => 6,
                'Making sure the magazine is turning a profit' => 7,
            ),
        ),
        */
        
        7 =>
        array(
            'question_no' => 7,
            'question' => 'What do you want most from your career?',
            'answers' => array(
                'Plenty of options' => array( 0, 1, 4, 7 ),
                'A clear career path' => array( 2, 3, 4, 5, 6, ),
            ),
            'type' => 'radio',
        ),
        

        
        8 =>
        array(
            'question_no' => 8,
            'question' => 'What are you most likely to be doing in your spare time?',
            'answers' => array(
                'Reading a book' => 0,
                'Writing a blog entry' => 1,
                'Creating a piece of art' => 1,
                'Cooking a healthy meal' => 2,
                'Volunteering' => 2,
                'Watching a space doco' => 3,
                'Reading the news' => 4,
                'Listening to a true crime podcast' => 4,
                'Fixing things around the house' => 5,
                'Inventing something in the garage' => 5,
                'Building a website' => 6,
                'Checking the finance pages' => 7,
            ),
        ),
        
        9 =>
        array(
            'question_no' => 9,
            'question' => 'How do you prefer to learn something new?',
            'answers' => array(
                'Visual info like videos, maps and graphs' => NULL,
                'Verbally, through lectures or group chats' => NULL,
                'Reading books and writing essays' => NULL,
                'Hands-on learning and building things' => NULL,
            ),
            'type' => 'radio',
        ),
    );
    
    $brag_question = array(
        'question_no' => 0,
        'question' => 'It\'s deadline day at The Brag - which tasks do you help with?',
        'answers' => array(
            'A bit of everything!' => 0,
            'Writing a news announcement' => 1,
            'Laying out the pages of the magazine' => 1,
            'Leading the mandatory yoga break' => 2,
            'Managing everyone\'s stress' => 2,
            'Researching a complex topic' => 3,
            'Analysing our social media audience' => 4,
            'Clearing an article with the legal team' => 4,
            'Building our lighting rig' => 5,
            'Troubleshooting our electronics' => 5,
            'Updating the website code' => 6,
            'Making sure we turn a profit' => 7,
        ),
    );
    
    shuffle( $questions );
    $questions = array_merge( array( $brag_question ), $questions );
    
//    echo '<pre>' . print_r( $questions, true ) . '</pre>';
//    foreach ( $questions as $question ) { echo $question['question'] . ' => ' . count( $question['answers'] ) . '<br>'; } exit;
    
    if ( $_POST ) :
        // Show score and result info
        $all_answers = $_POST['answers'];
        
//        echo '<pre>' . print_r( $all_answers, true );
    
        $scores = $db_answers = $db_results = array();
        
        $total_score = 0;
        foreach ( $all_answers as $question_no => $answers ) :
            $question_key = array_search( $question_no, array_column( $questions, 'question_no' ) );
            $q_answers = $questions[$question_key]['answers'];
            
            $db_answers[] = array(
                'question' => $questions[$question_key]['question'],
                'answers' => $answers,
            );
            
            foreach ( $answers as $answer ) :
                
                $result_score = $q_answers[stripslashes( $answer )];
                
                if ( is_array( $result_score ) ) :
                    
                    foreach( $result_score as $rs ) :
                        $total_score++;
                        if ( isset( $scores[ $rs ] ) ) {
                            $scores[ $rs ]++;
                        } else {
                            $scores[ $rs ]= 1;
                        }
                    endforeach;
                
                elseif ( ! is_null( $result_score ) ) :
                    $total_score++;
                    if ( isset( $scores[ $result_score ] ) ) {
                        $scores[ $result_score ]++;
                    } else {
                        $scores[ $result_score ]= 1;
                    }
                    
                endif;
            endforeach;
        endforeach;
        
        arsort( $scores );
//        echo '<pre>' . print_r( $scores, true ) . '</pre>'; echo $total_score;
        
        $suggested_results = array();
        
?>
    <div class="results">
        <div id="brag-logo">
            <a href="<?php echo site_url(); ?>" target="_blank">
                <img src="<?php echo get_template_directory_uri(); ?>/images/brag_logo_300x80.png" alt="Brag Magazine" width="175" />
            </a>
        </div>
        <div class="quiz-header">
            <div class="left">
                My<br>
                Future<br>
                Finder.<br>
            </div>
            <div class="right">
                <img src="<?php echo get_template_directory_uri(); ?>/images/Afifa_1_results_TheCollegeLOGO2.jpg">
            </div>
            <div class="clear"></div>
        </div>
        
        <h2 style="text-align: center; margin-top: 40px;" class="result-title">Your results</h2>
        
        <div style="padding: 0 20px; width: 560px; margin: auto; max-width: 100%; font-size: 14px; font-family: 'GothamNarrow-Light', 'Open Sans', Arial; box-sizing: border-box;">
            <p style="font-size: 24px; line-height: 30px">
                Didn't get the ATAR you were hoping for, or didn't get one at all? Don't worry, a number doesn't decide your career - you do.
            </p>
            <p style="font-family: 'GothamNarrow-Bold', 'Open Sans', Arial;">
                Even without an ATAR, you can apply now for courses at The College and start your path to success.
            </p>
            <p>
                Based on your career goals, study preferences and things you enjoy, these are three areas of study that could be perfect for you, and take you straight to the bachelor degree you really want.
            </p>
            <p>
                As the official pathways provider to Western Sydney University, The College offers a wide range of Higher Education Diploma programs to pave the way into your chosen university degree.
            </p>
            <p style="font-family: 'GothamNarrow-Bold', 'Open Sans', Arial;">
                And with flexible entry requirements, there's no need for an ATAR.
            </p>
            
        </div>
<?php

$score_keys = array_keys( $scores );

//echo '<pre>' . print_r( ( $score_keys ), true ) . '</pre>';

if ( in_array( 1, array( $score_keys[0], $score_keys[1], $score_keys[2] ) ) || in_array( 4, array( $score_keys[0], $score_keys[1], $score_keys[2] ) ) ) :
    
    if ( ! in_array( 0, array( $score_keys[0], $score_keys[1], $score_keys[2] ) ) ) :
        
        $scores[0] = $scores[ $score_keys[1] ]; // - 1;
    
    endif;
    
endif;

arsort( $scores );

//echo '<pre>' . print_r( ( $scores ), true ) . '</pre>';

$count_suggestions = 0;
foreach ( $scores as $index => $score ) :
    if ( $count_suggestions == 3 )
        break;
        array_push( $suggested_results, $index );
?>
        <div class="result">
            <div class="result-circle-score">
            <div class="circle" id="circle-<?php echo $index; ?>"></div>
            <div class="score">
                <?php
                if ( 0 == $count_suggestions ) :
                    $dummy_score = rand( 93, 97 );
                    $scores[$index] = $dummy_score;
                    echo $dummy_score . '%';
                    echo $results[$index]['imp_tag'];
                elseif ( 1 == $count_suggestions ) :
                    $dummy_score = rand( 89, 92 );
                    $scores[$index] = $dummy_score;
                    echo $dummy_score . '%';
                elseif ( 2 == $count_suggestions ) :
                    $dummy_score = rand( 80, 88 );
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
        <div class="quiz-edu-skin-left"></div>
        <div class="quiz-edu-skin-right"></div>
    </div>
    
    <table class="results-other">
<?php
    $count_o = -1;
    foreach ( $scores as $index => $score ) :
        if ( ! in_array( $index, $suggested_results ) ) :
            array_push( $suggested_results, $index );
?>
        <tr>
            <td class="score">
                <?php
                $dummy_score_to = 40 - $count_o * 5;
                $dummy_score_from = $dummy_score_to - 2;
                $dummy_scor_o = rand( $dummy_score_from, $dummy_score_to );
                if ( $dummy_scor_o <= 0 )
                    $dummy_scor_o = 1;
                echo $dummy_scor_o . '%';
                ?>
                <?php // echo round( $score  /  $total_score * 100, 0) . '%'; ?>
            </td>
            <td class="title"><?php echo $results[$index]['title']; ?></td>
            <td class="link" nowrap>
                <a href="<?php echo $results[$index]['link']; ?>" target="_blank">Find out more</a>
            </td>
        </tr>
<?php
        $count_o++;
        endif;
    
    endforeach;
    
    foreach ( $results as $index => $result ) :
        if ( ! in_array( $index, $suggested_results ) ) :
?>
        <tr class="result-light">
            <td class="score">0%</td>
            <td><?php echo $result['title']; ?></td>
            <td class="link" nowrap>
                <a href="<?php echo $results[$index]['link']; ?>" target="_blank">Find out more</a>
            </td>
        </tr>
<?php
        endif;
    endforeach;
    
    foreach ( $suggested_results as $suggested_result ) :
        $db_results[] = $results[$suggested_result]['title'];
    endforeach;
    
    global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'wsu_quiz_results',
            array(
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'answers' => json_encode( $db_answers ),
                'results' => json_encode( $db_results ),
                'created_at' => current_time( 'mysql', 0 )
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
        <a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236067134;dc_trk_aid=433655575;dc_trk_cid=109978827;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Find out more<span>></span></a>
        <a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236067173;dc_trk_aid=433651429;dc_trk_cid=110083610;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Apply now<span>></span></a>
        <a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.236367777;dc_trk_aid=433655596;dc_trk_cid=110083385;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">Talk to The College<span>></span></a>
    </div>
    
    <div style="padding: 0 20px; width: 560px; margin: 70px auto auto auto; max-width: 100%; box-sizing: border-box;">
    <p style="text-align: center;font-family: 'GothamNarrow-Light'; font-size: 24px; line-height: 30px; color: #FF5C5E;">
        Know someone looking for their own pathway to uni?<br>Share the quiz:
    </p>
    
    <?php
    $post_url = urlencode(get_permalink());

    // Get current page title
    $post_title = str_replace( ' ', '%20', get_the_title());

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
        <a href="https://ad.doubleclick.net/ddm/trackclk/N827107.3385141SEVENTHSTMEDIA/B22086547.235840487;dc_trk_aid=433411598;dc_trk_cid=109907617;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=" target="_blank">
            <img src="<?php echo get_template_directory_uri(); ?>/images/WSU_Logo_TheCollege.jpg" alt="Western Sydney University - The College" width="175" />
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
        <?php $count = 0; foreach ( $scores as $index => $score ) : ?>
        $('#circle-<?php echo $index; ?>').circleProgress({
            value: <?php echo $score  /  100; ?>,
            size: 150,
            fill: '<?php echo $colors[ $count ]; ?>'
        });
  <?php $count++; endforeach; ?>
    });
</script>

<!-- Quiz | SSM | Seventh Street Media | Quiz 1 > Unlimited Future Finder > Landing Page Skins -->
<IMG SRC="https://ad.doubleclick.net/ddm/trackimp/N827107.3385141SEVENTHSTMEDIA/B22086547.235840487;dc_trk_aid=433411598;dc_trk_cid=109907617;ord=<?php echo $cachebuster; ?>;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=?" BORDER="0" HEIGHT="1" WIDTH="1" ALT="Advertisement">

<?php else: // Show Quiz
    if ( $questions ) :
    ?>
    
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="form-quiz">
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
                    <img src="<?php echo get_template_directory_uri(); ?>/images/brag_logo_300x80.png" alt="Brag Magazine" width="175" />
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
            <div class="question-numbers"><span id="current_q">1</span> / <?php echo count( $questions ); ?></div>
        </div>
        <?php $i = 0; foreach ( $questions as $question ) : ?>
            <div class="quiz-details" id="quiz-details-<?php echo $i; ?>" data-qz="<?php echo $i; ?>" data-qn="<?php echo $question['question_no']; ?>" style="display: none;">
                
                <div class="quiz-question-edu"><?php echo $question['question']; ?>
                    <small>
                    <?php
                        if ( isset( $question['type'] ) && 'radio' == $question['type'] ) {
                            echo 'Choose one';
                        } else {
                            echo 'Choose up to three';
                        }
                    ?>
                    </small>
                    </div>
                
                <div class="quiz-answers">
                    <?php
                    if ( $question['answers'] ) :
                        $question['answers'] = shuffle_assoc( $question['answers'] );
                        $j = 0;
                        foreach ( $question['answers'] as $answer_text => $result_score ) :
                    ?>
                    <?php if ( isset( $question['type'] ) && 'radio' == $question['type'] ) : ?>
                        <div class="quiz-answer">
                            <input type="radio" class="radio answer_choice" name="answers[<?php echo $question['question_no']; ?>][]" id="answer_<?php echo $i . '_' . $j; ?>" value="<?php echo $answer_text; ?>">
                            <label for="answer_<?php echo $i . '_' . $j; ?>" class="answer_choice_label"><?php echo $answer_text; ?></label>
                        </div>
                    <?php else : ?>
                        <div class="quiz-answer">
                            <input type="checkbox" class="radio answer_choice" name="answers[<?php echo $question['question_no']; ?>][]" id="answer_<?php echo $i . '_' . $j; ?>" value="<?php echo $answer_text; ?>">
                            <label for="answer_<?php echo $i . '_' . $j; ?>" class="answer_choice_label"><?php echo $answer_text; ?></label>
                        </div>
                    <?php endif; ?>
                    <?php $j++; 
                        endforeach; // For Each Answer of Question
                    endif; ?>
                </div>
                <div class="clear"></div>
                <div class="navigate">
                    <div class="error" id="error-<?php echo $question['question_no']; ?>"></div>
                    <div class="button btn-prev" style="display: none;">< Back</div>
                    <?php if ( $i < ( count( $questions ) - 1 ) ) : ?>
                        <div class="button btn-next">Next<span>></span></div>
                    <?php else : ?>
                        <div class="button btn-next" id="btn-submit">Submit<span>></span></div>
                        <!--<input type="submit" name="submit" value="Submit" class="button" id="btn-submit">-->
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
            </div>
        <?php $i++; endforeach; // For Each $question in $questions ?>
        <div class="clear"></div>
        <div class="quiz-edu-skin-left"></div>
        <div class="quiz-edu-skin-right"></div>
        </div>
    </section>
        <div class="clear"></div>
    </form>
    <?php endif; // If there are questions ?>
<?php endif; // If $_POST is set ?>
</div>

<?php else: ?>
<div class="quiz-story-edu">
    <form method="post" action="/wp-login.php?action=postpass">
    <p>This content is password protected. To view it please enter your password below:</p>
    <p>Password:<br/>
    <input type="password" size="20" id="pwbox-<?php echo get_the_ID(); ?>" name="post_password"/><br/>
    <input type="submit" value="Enter" name="Submit"/></p>
    </form>
</div>
<?php endif; ?>

<?php get_template_part( 'footer-quiz-edu' );

function shuffle_assoc($list) { 
    if (!is_array($list)) return $list; 
    $keys = array_keys($list); 
    shuffle($keys); 
    $random = array(); 
    foreach ($keys as $key) 
        $random[$key] = $list[$key]; 
    return $random; 
}