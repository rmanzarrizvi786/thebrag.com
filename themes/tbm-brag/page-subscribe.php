<?php /* Template Name: Subscribe to Multiple Pubs */ ?>
<?php
wp_redirect( 'https://thebrag.com/observer/' ); exit;
use \DrewM\MailChimp\MailChimp;
if ( isset( $_POST ) && count( $_POST ) > 0 ) :
    $form_posts = stripslashes_deep($_POST);
    require_once( get_template_directory() . '/MailChimp.php');
    $api_key = '727643e6b14470301125c15a490425a8-us1';
    $MailChimp = new MailChimp( $api_key );

    foreach( $form_posts['list'] as $list_id ) :
        $data = array(
            'email_address' => $form_posts['email'],
            'status' => 'subscribed',
        );
        $subscribe = $MailChimp->post( "lists/$list_id/members", $data );
        header( 'Location: thank-you-subscribe' );
    endforeach;
    exit;
endif;
?>
<?php get_header(); ?>

<div class="container subscribe">
    <div class="row">
        <div class="col-12 cats mt-2 mb-4">
            <div style="border-top: 2px solid #000;"></div>
        </div>
        <div class="col-12 mb-3">
            <h1>Trending Topics:</h1>
            <p>Choose your interests and get the latest news &amp; free stuff from your favourite publications.</p>
        </div>
    </div>
    <div class="row mb-5">
        <div class="col-md-8">
            <form method="post">
                <div class="subscribe-form">
                    <div class="input-group">
                        <input type="text" required="required" class="email" placeholder="Email Address*" name="email"><button type="submit" class="btn-dark">Subscribe</button>
                    </div>
                    <div class="pubs row">
                        <div class="col-md-6">
                            <label class="my-2 p-2">
                                <input type="checkbox" checked="checked" name="list[]" value="b6f823df63">
                                <span class="checkbox"><i class="fa fa-check-square"></i></span>
                                <div class="img"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs/tonedeaf-logo-300px.png"></div>
                                <div class="tagline"><span>Music News And Free Tickets</span></div>
                                <div class="desc"><span>Tone Deaf is Australian's premiere online publication dedicated to music lovers. Find out the latest music news, new releases and upcoming tours and festivals.</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="my-2 p-2">
                                <input type="checkbox" checked="checked" name="list[]" value="c9114493ef">
                                <span class="checkbox"><i class="fa fa-check-square"></i></span>
                                <div class="img"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs/brag-logo-300px.png"></div>
                                <div class="tagline"><span>Arts, Music, Comedy And Food</span></div>
                                <div class="desc"><span>The BRAG covers music, arts, pop culture, theatre, comedy, food, current affairs, and more - focusing nationally and beyond.</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="my-2 p-2">
                                <input type="checkbox" checked="checked" name="list[]" value="4a1cd6d6a6">
                                <span class="checkbox"><i class="fa fa-check-square"></i></span>
                                <div class="img"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs/tio-logo-300x133.png"></div>
                                <div class="tagline"><span>Music Business News &amp; Insight</span></div>
                                <div class="desc"><span>At its core, The Industry Observer is illuminating, informative and at times, argumentative - but in a non-provocative way.</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="my-2 p-2">
                                <input type="checkbox" checked="checked" name="list[]" value="2a48cd9086">
                                <span class="checkbox"><i class="fa fa-check-square"></i></span>
                                <div class="img"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs/dbu-logo-300px.png"></div>
                                <div class="tagline"><span>All things pop, punk and emo</span></div>
                                <div class="desc"><span>DBU has a penchant for all things pop and alternative music. While new to the scene, Don’t Bore Us is easily found, right in the middle of the dance floor.</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="my-2 p-2">
                                <input type="checkbox" checked="checked" name="list[]" value="f0eedde184">
                                <span class="checkbox"><i class="fa fa-check-square"></i></span>
                                <div class="img"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs/brag-dad-logo-300px.png"></div>
                                <div class="tagline"><span>Making your dad life your best life</span></div>
                                <div class="desc"><span>A dedicated publication just for Australian dads, coming soon. Making your dad life, your best life.</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="my-2 p-2">
                                <input type="checkbox" checked="checked" name="list[]" value="bee79a59dc">
                                <span class="checkbox"><i class="fa fa-check-square"></i></span>
                                <div class="img"><img src="<?php echo get_template_directory_uri(); ?>/images/pubs/brag-gaming-logo-200px.png"></div>
                                <div class="tagline"><span>Gaming news and giveaways</span></div>
                                <div class="desc"><span>The Brag Gaming puts an Australian spin on the biggest gaming and esports news, viewing gaming through a pop culture lense that brings a broad appeal to every story.</span></div>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-4 col-md-6 col-12 p-0">
            <?php get_fuse_tag( 'mrec_1', 'search' ); ?>
            <?php get_fuse_tag( 'mrec_2', 'search' ); ?>
        </div>
    </div>
</div>


<div class="pt-0 pb-2"><?php get_fuse_tag( 'hrec_2' ); ?></div>

<?php get_footer(); ?>
