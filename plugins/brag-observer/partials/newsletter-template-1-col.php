<?php
/*
$ads_after_articles = isset( $newsletter->details->ads ) ? wp_list_pluck( $newsletter->details->ads, 'article' ) : [];
foreach( $ads_after_articles as $k => $ads_after_article ) {
  if ( '' == trim( $ads_after_article ) ) {
    unset( $ads_after_articles[$k] );
    continue;
  }
}
*/
// $ads_after_articles = [ 2, 4, 6 ];

$ads_after_articles = [];
$ads = isset($newsletter->details->ads) ? wp_list_pluck($newsletter->details->ads, 'image') : [];

foreach ($ads as $k => $ad) {
  if ('' == $ads[$k]) {
    unset($ads[$k]);
    continue;
  }
}
if (count($ads) > 0) {
  array_push($ads_after_articles, 2);
}
if (count($ads) > 1) {
  array_push($ads_after_articles, 4);
}
if (count($ads) > 2) {
  array_push($ads_after_articles, 6);
}
if (count($ads) > 3) {
  array_push($ads_after_articles, 8);
}
?>
<tr>
  <td valign="top" class="templateLowerColumns">
    <?php
    if (isset($post_ids) && is_array($post_ids) && count($post_ids) > 0) {
      foreach ($post_ids as $post_index => $post_id) {
        if (in_array($post_index, $ads_after_articles)) {
    ?>
          <!--[if gte mso 9]>
        <table align="center" border="0" cellspacing="0" cellpadding="0" width="<?php echo $container_width; ?>" style="width:<?php echo $container_width; ?>px;">
        <tr><td align="center" valign="top" width="<?php echo $container_width; ?>" style="width:<?php echo $container_width; ?>px;"><![endif]-->
          <?php print_ad($post_index, $ads_after_articles, $newsletter, $container_width); ?>
          <!--[if gte mso 9]></td></tr>
        </table>
        <![endif]-->
        <?php
        }
        ?>
        <!--[if gte mso 9]>
        <table align="center" border="0" cellspacing="0" cellpadding="0" width="<?php echo $container_width; ?>" style="width:<?php echo $container_width; ?>px;">
        <tr><td align="center" valign="top" width="<?php echo $container_width; ?>" style="width:<?php echo $container_width; ?>px;"><![endif]-->
        <?php print_article($newsletter, $post_id, $container_width); ?>
        <!--[if gte mso 9]></td></tr>
        </table>
        <![endif]-->
      <?php
      } // For Each $post_ids
    } // If $post_ids

    foreach ($ads_after_articles as $ads_after_article) {
      if ($ads_after_article > (count($post_ids) - 1)) {
      ?>
        <!--[if gte mso 9]>
        <table align="center" border="0" cellspacing="0" cellpadding="0" width="<?php echo $container_width; ?>" style="width:<?php echo $container_width; ?>px;">
        <tr><td align="center" valign="top" width="<?php echo $container_width; ?>" style="width:<?php echo $container_width; ?>px;"><![endif]-->
        <?php print_ad($ads_after_article, $ads_after_articles, $newsletter); ?>
        <!--[if gte mso 9]></td></tr>
        </table>
        <![endif]-->
    <?php
      }
    } // For Each $ads_after_articles
    ?>
  </td>
</tr>

<?php
if (4 == $list->id) {
  print_jobs_tio($newsletter);

  print_tio_tweet($newsletter);

  print_tio_birthday_shoutout($newsletter);
} // if list is TIO 
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

<?php
/**
 * Featured Video / Audio
 */
print_video_record_of_week($newsletter);

function print_article($newsletter, $article_number, $container_width = 700)
{

  $post_links = ($newsletter->details->post_links);
  $post_images = ($newsletter->details->post_images);
  $post_titles = ($newsletter->details->post_titles);
  $post_excerpts = ($newsletter->details->post_excerpts);

  if (!is_null($article_number)) :
    $pub_logo = get_pub_logo($newsletter->details->post_links[$article_number]);
?>
    <table align="left" border="0" cellpadding="0" cellspacing="0" width="<?php echo $container_width; ?>" class="columnWrapper">
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
                          <td class="pub">
                            <img src="<?php echo $pub_logo['url']; ?>" class="pub-icon" title="<?php echo $pub_logo['title']; ?>" alt="<?php echo $pub_logo['title']; ?>">
                            <strong><?php echo $pub_logo['title']; ?></strong>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  <?php endif; // If $pub_logo is NOT null 
                  ?>
                  <table align="right" border="0" cellpadding="0" cellspacing="0" class="mcnImageCardBottomContent" width="100%" style="background-color: #000000;">
                    <tbody>
                      <tr>
                        <td class="mcnImageCardBottomImageContent" align="left" valign="top" class="p-0">
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
                                <?php echo strtoupper($newsletter->details->post_titles[$article_number]); ?>
                              </a>
                            </font>
                          </h1>
                          <div style="margin-top: 9px;text-align: left; font-size:16px; color:#FFFFFF; font-family:arial,helvetica neue,helvetica,sans-serif">
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
} // print_article

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
        <table border="0" cellpadding="0" cellspacing="0" width="300" class="mcnImageCardBlock" align="center">
          <tbody class="mcnImageCardBlockOuter">
            <tr>
              <td class="mcnImageCardBlockInner" valign="top" style="padding-top:9px; padding-right:18px; padding-bottom:9px; padding-left:18px;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" class="mcnImageCardBottomContent" width="300" style="background-color: #ffffff;">
                  <tbody>
                    <tr>
                      <td class="mcnImageCardBottomImageContent" align="center" valign="top" style="padding-top:0px; padding-right:0px; padding-bottom:0; padding-left:0px; text-align: center;">
                        <a href="<?php echo isset($newsletter->details->ads[$array_key]->image) && $newsletter->details->ads[$array_key]->link != '' ? $newsletter->details->ads[$array_key]->link : '#'; ?>" target="_blank">
                          <?php // $ad_size = getimagesize( $newsletter->details->ads[$array_key]->image ); 
                          ?>
                          <img align="center" alt="" src="<?php echo $newsletter->details->ads[$array_key]->image; ?>" width="300" height="250" style="width: 300px; height: 250px; max-width: 300px; max-height: 250px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
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
