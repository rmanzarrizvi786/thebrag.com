<?php
/*
$ads_after_articles = isset( $newsletter->details->ads ) ? wp_list_pluck( $newsletter->details->ads, 'article' ) : [];
foreach( $ads_after_articles as $k => $ads_after_article ) {
  if ( '' == $ads_after_articles[$k] ) {
    unset( $ads_after_articles[$k] );
    continue;
  }

  // $ads_after_articles[$k] = $ads_after_article % 2 == 0 ? (int) $ads_after_article : (int) ( $ads_after_article + 1 );

  if ( $ads_after_articles[$k] > ( count( $post_ids ) ) ) {
    $ads_after_articles[$k] = count( $post_ids );
  }
}
*/
$ads_after_articles = [];
$ads = isset($newsletter->details->ads) ? wp_list_pluck($newsletter->details->ads, 'image') : [];

foreach ($ads as $k => $ad) {
  if ('' == $ads[$k]) {
    unset($ads[$k]);
    continue;
  }
}
if (count($ads) > 0) {
  array_push($ads_after_articles, 3);
}
if (count($ads) > 1) {
  array_push($ads_after_articles, 6);
}
if (count($ads) > 2) {
  array_push($ads_after_articles, 10);
}
if (count($ads) > 3) {
  array_push($ads_after_articles, 14);
}
// var_dump( $ads_after_articles ); exit;
// $ads_after_articles = [ 3, 6, 10, 14 ];
// var_dump( $ads_after_articles ); exit;

if (isset($post_ids) && is_array($post_ids) && count($post_ids) > 0) :
?>
  <tr>
    <td valign="top" class="templateLowerColumns">
      <?php
      // First article
      if (isset($post_ids[0])) :
        print_article($newsletter, $post_ids[0], 'full');
      endif;
      ?>

      <!-- 2, 3 {{ -->
      <table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;" width="700">
        <tbody>
          <tr style="padding:0;text-align:center;vertical-align:top">
            <?php
            foreach ([1, 2] as $i) :
              if (isset($post_ids[$i])) : ?>
                <td class="small-12" style="color:#0a0a0a;border-bottom:2px solid #EAEAEA;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:20px;padding-top:20px;text-align:center;width:320px;vertical-align:top">
                  <?php print_article($newsletter, $post_ids[$i], 'half'); ?>
                </td>
            <?php
              endif;
            endforeach;
            ?>
          </tr>
        </tbody>
      </table>
      <!-- }} 2, 3 -->

      <?php
      // Ad after article 3
      if (in_array(3, $ads_after_articles)) :
        print_ad(3, $ads_after_articles, $newsletter, $container_width);
      endif;
      ?>

      <!-- 4, 5 {{ -->
      <table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;" width="700">
        <tbody>
          <tr style="padding:0;text-align:center;vertical-align:top">
            <?php
            foreach ([3, 4] as $i) :
              if (isset($post_ids[$i])) : ?>
                <td class="small-12" style="color:#0a0a0a;border-bottom:2px solid #EAEAEA;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:20px;padding-top:20px;text-align:center;width:320px;vertical-align:top">
                  <?php print_article($newsletter, $post_ids[$i], 'half'); ?>
                </td>
            <?php
              endif;
            endforeach;
            ?>
          </tr>
        </tbody>
      </table>
      <!-- }} 4, 5 -->

      <?php
      // Article #6, index 5
      if (isset($post_ids[5])) :
        print_article($newsletter, $post_ids[5], 'full');
      endif;
      ?>

      <?php
      // Ad after article 6
      if (in_array(6, $ads_after_articles)) :
        print_ad(6, $ads_after_articles, $newsletter, $container_width);
      endif;
      ?>

      <!-- 7, 8 {{ -->
      <table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;" width="700">
        <tbody>
          <tr style="padding:0;text-align:center;vertical-align:top">
            <?php
            foreach ([6, 7] as $i) :
              if (isset($post_ids[$i])) : ?>
                <td class="small-12" style="color:#0a0a0a;border-bottom:2px solid #EAEAEA;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:20px;padding-top:20px;text-align:center;width:320px;vertical-align:top">
                  <?php print_article($newsletter, $post_ids[$i], 'half'); ?>
                </td>
            <?php
              endif;
            endforeach;
            ?>
          </tr>
        </tbody>
      </table>
      <!-- }} 7, 8 -->

      <!-- 9, 10 {{ -->
      <table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;" width="700">
        <tbody>
          <tr style="padding:0;text-align:center;vertical-align:top">
            <?php
            foreach ([8, 9] as $i) :
              if (isset($post_ids[$i])) : ?>
                <td class="small-12" style="color:#0a0a0a;border-bottom:2px solid #EAEAEA;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:20px;padding-top:20px;text-align:center;width:320px;vertical-align:top">
                  <?php print_article($newsletter, $post_ids[$i], 'half'); ?>
                </td>
            <?php
              endif;
            endforeach;
            ?>
          </tr>
        </tbody>
      </table>
      <!-- }} 9, 10 -->

      <?php
      // Ad after article 10
      if (in_array(10, $ads_after_articles)) :
        print_ad(10, $ads_after_articles, $newsletter, $container_width);
      endif;
      ?>

      <?php for ($j = 10; $j < count($post_ids); $j += 2) : ?>
        <table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;" width="700">
          <tbody>
            <tr style="padding:0;text-align:center;vertical-align:top">
              <?php
              foreach ([$j, $j + 1] as $i) :
                if (isset($post_ids[$i])) : ?>
                  <td class="small-12" style="color:#0a0a0a;border-bottom:2px solid #EAEAEA;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:20px;padding-top:20px;text-align:center;width:320px;vertical-align:top">
                    <?php print_article($newsletter, $post_ids[$i], 'half'); ?>
                  </td>
              <?php
                endif;
              endforeach;
              ?>
            </tr>
          </tbody>
        </table>
      <?php endfor; ?>

    </td>
  </tr>
<?php endif; // if there are $post_ids 
?>

<?php if (4 == $list->id) { ?>
  <tr>
    <td style="padding-top:20px;padding-bottom:20px;background-color:#ffffff;">
      <?php print_jobs_tio(); ?>
    </td>
  </tr>

  <?php if (isset($newsletter->details->top_i_tweet_image) && $newsletter->details->top_i_tweet_image != '') { ?>
    <tr>
      <td style="padding-top:20px;border-bottom:2px solid #EAEAEA;padding-bottom:20px;background-color:#ffffff;">
        <?php print_tio_tweet($newsletter); ?>
      </td>
    </tr>
  <?php } // If top_i_tweet_image 
  ?>

  <?php if (isset($newsletter->details->birthday_shoutout_image) && $newsletter->details->birthday_shoutout_image != '') { ?>
    <tr>
      <td style="padding-top:20px;border-bottom:2px solid #EAEAEA;padding-bottom:20px;background-color:#ffffff;">
        <?php print_tio_birthday_shoutout($newsletter); ?>
      </td>
    </tr>
  <?php } // If birthday_shoutout_image 
  ?>

<?php } // if list is TIO 
?>

<?php if (56 == $list->id) { // 56 = Christian Hull list ID  
  if (isset($newsletter->details->christian_hull_top_five) && $newsletter->details->christian_hull_top_five != '') { ?>
    <tr>
      <td style="padding-top:20px;border-bottom:2px solid #EAEAEA;padding-bottom:20px;background-color:#ffffff;">
        <?php print_christian_hull_top_five($newsletter); ?>
      </td>
    </tr>
<?php } // If birthday_shoutout_image 
} // If Christian Hull list 
?>

<!-- Featured Video / Audio -->
<tr>
  <td style="background-color:#ffffff;">
    <?php print_video_record_of_week($this, $container_width); ?>
  </td>
</tr>
<!-- Featured Video / Audio -->

<?php
function print_ad_old($value, $ads_after_articles, $newsletter)
{
  $array_key = array_search($value, $ads_after_articles);

  if (FALSE === $array_key)
    return;
?>
  <a href="<?php echo isset($newsletter->details->ads[$array_key]->image) && $newsletter->details->ads[$array_key]->link != '' ? $newsletter->details->ads[$array_key]->link : '#'; ?>" target="_blank">
    <?php // $ad_size = getimagesize( $newsletter->details->ads[$array_key]->image ); 
    ?>
    <img align="center" alt="" src="<?php echo $newsletter->details->ads[$array_key]->image; ?>" width="300" height="250" style="max-width: 300px; max-height: 250px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
  </a>
<?php
}

function print_ad($value, $ads_after_articles, $newsletter, $container_width = 700)
{
  $array_key = array_search($value, $ads_after_articles);
  // var_dump( $array_key );
  if (FALSE === $array_key)
    return;
?>
  <table align="center" border="0" cellpadding="0" cellspacing="0" width="<?php echo $container_width; ?>" class="columnWrapper">
    <tr>
      <td style="background-color: #ffffff; border-bottom:2px solid #EAEAEA;" class="templateLowerColumns">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageCardBlock">
          <tbody class="mcnImageCardBlockOuter">
            <tr>
              <td class="mcnImageCardBlockInner" valign="top" style="padding-top:9px; padding-right:18px; padding-bottom:9px; padding-left:18px;">
                <table align="right" border="0" cellpadding="0" cellspacing="0" class="mcnImageCardBottomContent" width="100%" style="background-color: #ffffff;">
                  <tbody>
                    <tr>
                      <td class="mcnImageCardBottomImageContent" align="center" valign="top" style="padding-top:0px; padding-right:0px; padding-bottom:0; padding-left:0px; text-align: center;">
                        <a href="<?php echo isset($newsletter->details->ads[$array_key]->image) && $newsletter->details->ads[$array_key]->link != '' ? $newsletter->details->ads[$array_key]->link : '#'; ?>" target="_blank">
                          <?php // $ad_size = getimagesize( $newsletter->details->ads[$array_key]->image ); 
                          ?>
                          <img align="center" alt="" src="<?php echo $newsletter->details->ads[$array_key]->image; ?>" width="300" height="250" style="max-width: 300px; max-height: 250px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </table>
  <?php
}

function print_article($newsletter, $article_number, $style = 'full', $container_width = 700)
{
  $post_links = ($newsletter->details->post_links);
  $post_images = ($newsletter->details->post_images);
  $post_titles = ($newsletter->details->post_titles);
  $post_excerpts = ($newsletter->details->post_excerpts);

  if (!is_null($article_number)) :
    $pub_logo = get_pub_logo($newsletter->details->post_links[$article_number]);
    if ('full' == $style) : // Full
  ?>
      <table align="center" border="0" cellpadding="0" cellspacing="0" width="<?php echo $container_width; ?>" class="columnWrapper">
        <tr>
          <td style="background-color: #ffffff; border-bottom:2px solid #EAEAEA;" class="templateLowerColumns">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageCardBlock">
              <tbody class="mcnImageCardBlockOuter">
                <tr>
                  <td class="mcnImageCardBlockInner" valign="top" style="padding-top:9px; padding-right:20px; padding-bottom:9px; padding-left:20px;">
                    <?php if (!is_null($pub_logo)) : ?>
                      <table align="right" border="0" cellpadding="0" cellspacing="0" class="mcnImageCardBottomContent" width="100%">
                        <tbody>
                          <tr>
                            <td class="mcnImageCardBottomImageContent" align="left" valign="top" style="padding-top:0px; padding-right:0px; padding-bottom:9px; padding-left:0px;">
                              <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td>
                                    <img src="<?php echo $pub_logo['url']; ?>" style="width: <?php echo $pub_logo['width']; ?>px; max-width: 100%; height: auto;" title="<?php echo $pub_logo['title']; ?>" alt="<?php echo $pub_logo['title']; ?>">
                                  </td>
                                  <td style="padding-left: 8px; color: #0a0a0a; font-size: 14px; font-family: Helvetica;">
                                    <strong><?php echo $pub_logo['title']; ?></strong>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    <?php endif; // If $pub_logo is NOT null 
                    ?>
                    <table align="right" border="0" cellpadding="0" cellspacing="0" class="mcnImageCardBottomContent" width="100%" style="background-color: #000000;">
                      <tbody>
                        <tr>
                          <td class="mcnImageCardBottomImageContent" align="left" valign="top" style="padding-top:0px; padding-right:0px; padding-bottom:0; padding-left:0px;">
                            <?php if (isset($newsletter->details->post_images[$article_number]) && $newsletter->details->post_images[$article_number] != '') : ?>
                              <a href="<?php echo $newsletter->details->post_links[$article_number]; ?>" target="_blank">
                                <img align="center" alt="<?php echo $newsletter->details->post_titles[$article_number]; ?>" src="<?php echo $newsletter->details->post_images[$article_number]; ?>" width="<?php echo $container_width - 40; ?>" style="max-width:<?php echo $container_width - 40; ?>px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                              </a>
                            <?php endif; ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="mcnTextContent" valign="top" style="padding: 9px 18px;color: #F2F2F2;font-family: Helvetica;font-size: 14px;font-style: normal;font-weight: normal;line-height: 150%;text-align: center;" width="546">
                            <h1>
                              <font color="#ffffff">
                                <a href="<?php echo $newsletter->details->post_links[$article_number]; ?>" target="_blank" style="color: #ffffff; text-decoration: none;">
                                  <?php echo $newsletter->details->post_titles[$article_number]; ?>
                                </a>
                              </font>
                            </h1>
                            <div style="text-align: left; font-size:16px; color:#FFFFFF; font-family:arial,helvetica neue,helvetica,sans-serif">
                              <?php echo $newsletter->details->post_excerpts[$article_number]; ?>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </table>
    <?php
    else : // Half
    ?>
      <table align="center" border="0" cellpadding="0" cellspacing="0" width="<?php echo $container_width / 2; ?>" class="columnWrapper">
        <tr>
          <td style="background-color: #ffffff;" class="templateLowerColumns">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageCardBlock">
              <tbody class="mcnImageCardBlockOuter">
                <tr>
                  <td class="mcnImageCardBlockInner" valign="top" style="padding-right: 18px; padding-left: 18px;">
                    <?php if (!is_null($pub_logo)) : ?>
                      <table align="right" border="0" cellpadding="0" cellspacing="0" class="mcnImageCardBottomContent" width="100%">
                        <tbody>
                          <tr>
                            <td class="mcnImageCardBottomImageContent" align="left" valign="top" style="padding-top:0px; padding-right:0px; padding-bottom:9px; padding-left:0px;">
                              <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td>
                                    <img src="<?php echo $pub_logo['url']; ?>" style="width: <?php echo $pub_logo['width']; ?>px; max-width: 100%; height: auto;" title="<?php echo $pub_logo['title']; ?>" alt="<?php echo $pub_logo['title']; ?>">
                                  </td>
                                  <td style="padding-left: 8px; color: #0a0a0a; font-size: 14px; font-family: Helvetica;">
                                    <strong><?php echo $pub_logo['title']; ?></strong>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    <?php endif; // If $pub_logo is NOT null 
                    ?>
                    <table align="right" border="0" cellpadding="0" cellspacing="0" class="mcnImageCardBottomContent" width="100%" style="background-color: #ffffff;">
                      <tbody>
                        <tr>
                          <td class="mcnImageCardBottomImageContent" align="left" valign="top" style="padding-top:0px; padding-right:0px; padding-bottom:0; padding-left:0px;">
                            <?php if (isset($newsletter->details->post_images[$article_number]) && $newsletter->details->post_images[$article_number] != '') : ?>
                              <a href="<?php echo $newsletter->details->post_links[$article_number]; ?>" target="_blank">
                                <img align="center" alt="<?php echo $newsletter->details->post_titles[$article_number]; ?>" src="<?php echo $newsletter->details->post_images[$article_number]; ?>" width="310" style="max-width:100%; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                              </a>
                            <?php endif; ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="mcnTextContent" valign="top" style="padding: 9px 0;color: #000000;font-family: Helvetica;font-size: 14px;font-style: normal;font-weight: normal;line-height: 150%;text-align: center;">
                            <h2>
                              <font color="#ffffff">
                                <a href="<?php echo $newsletter->details->post_links[$article_number]; ?>" target="_blank" style="color: #000000; text-decoration: none;">
                                  <?php echo $newsletter->details->post_titles[$article_number]; ?>
                                </a>
                              </font>
                            </h2>
                            <div style="text-align: left; font-size:16px; color:#000000; font-family:arial,helvetica neue,helvetica,sans-serif">
                              <?php echo $newsletter->details->post_excerpts[$article_number]; ?>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </table>
    <?php
    endif;
  endif;
}

function print_article_old($newsletter, $article_number, $style = 'full')
{

  $tableWidth = 'full' == $style ? 700 : 300;
  $bgColor = 'full' == $style ? '#000000' : '#ffffff';
  $textColor = 'full' == $style ? '#F2F2F2' : '#000000';
  $paddingX = 'full' == $style ? 18 : 0;

  $post_links = ($newsletter->details->post_links);
  $post_images = ($newsletter->details->post_images);
  $post_titles = ($newsletter->details->post_titles);
  $post_excerpts = ($newsletter->details->post_excerpts);

  if (!is_null($article_number)) : ?>

    <?php if ('full' == $style) : ?>
      <table style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;width:100%" class="">
        <tbody class="">
          <tr style="padding:0;text-align:center;vertical-align:top">
            <td style="text-align:center;" class="small-12-inner">
            <?php endif; ?>

            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:center;vertical-align:top;">
              <tr style="padding:0;text-align:center;vertical-align:top">
                <td style="color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0;padding-top:0;padding-bottom:18px;text-align:center;background-color: <?php echo $bgColor; ?>;padding-left:0;padding-right:0;">
                  <?php if (isset($post_images[$article_number]) && $post_images[$article_number] != '') : ?>
                    <a href="<?php echo $post_links[$article_number]; ?>" target="_blank" style="color:<?php echo $textColor; ?>;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.4;margin:0;padding:0;text-align:center;text-decoration:none"><img src="<?php echo  $post_images[$article_number]; ?>" alt="<?php echo $post_titles[$article_number]; ?>" title="<?php echo $post_titles[$article_number]; ?>" width="304" height="170" style="-ms-interpolation-mode:bicubic;border:none;clear:both;display:block;height:auto;margin-bottom:10px;max-width:100%;outline:0;text-decoration:none;width:100%;color:<?php echo $textColor; ?>;">
                    </a>
                  <?php endif; ?>
                  <div style="padding-left:<?php echo $paddingX; ?>px;padding-right:<?php echo $paddingX; ?>px">
                    <h2 style="Margin-bottom:5px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:400;line-height:1.4;margin:0;margin-bottom:5px;margin-top:5px;padding:0;word-wrap:normal"><a class="headline" href="<?php echo $post_links[$article_number]; ?>" target="_blank" title="<?php echo $post_titles[$article_number]; ?>" style="Margin:0;color:<?php echo $textColor; ?>!important;font-family:Helvetica,Arial,sans-serif;font-size:23px;font-weight:700;line-height:28px;margin:0;padding:0;text-align:center;text-decoration:none"><?php echo $post_titles[$article_number]; ?></a></h2>
                    <div style="text-align: left; font-size:16px;color:<?php echo $textColor; ?>;font-family:arial,helvetica neue,helvetica,sans-serif">
                      <?php echo $newsletter->details->post_excerpts[$article_number]; ?>
                    </div>
                  </div>
                </td>
              </tr>
            </table>

            <?php if ('full' == $style) : ?>
            </td>
          </tr>
        </tbody>
      </table>
    <?php endif; ?>

<?php endif;
} // function print_articles
