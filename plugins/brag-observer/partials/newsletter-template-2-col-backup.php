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
$ads = isset( $newsletter->details->ads ) ? wp_list_pluck( $newsletter->details->ads, 'image' ) : [];

foreach( $ads as $k => $ad ) {
  if ( '' == $ads[$k] ) {
    unset( $ads[$k] );
    continue;
  }
}
if ( count( $ads ) > 0 ) {
  array_push( $ads_after_articles, 3 );
}
if ( count( $ads ) > 1 ) {
  array_push( $ads_after_articles, 6 );
}
if ( count( $ads ) > 2 ) {
  array_push( $ads_after_articles, 10 );
}
if ( count( $ads ) > 3 ) {
  array_push( $ads_after_articles, 14 );
}
// var_dump( $ads_after_articles ); exit;
// $ads_after_articles = [ 3, 6, 10, 14 ];
// var_dump( $ads_after_articles ); exit;
?>
<tr>
  <td valign="top" class="templateLowerColumns">
    <table style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;width:100%" class="columnWrapper">
      <tbody class="mcnImageCardBlockOuter">
        <?php if ( in_array( 0, $ads_after_articles ) ) { ?>
        </tr>
        <tr style="padding:0;text-align:center;vertical-align:top" >
          <td colspan="2" class="small-12" style="Margin:0 auto;color:#0a0a0a;border-bottom:2px solid #EAEAEA;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:20px;padding-left:0;padding-right:0;padding-top:20px;text-align:center" >
            <?php print_ad( 0, $ads_after_articles, $newsletter ); ?>
          </td>
        </tr>
        <tr style="padding:0;text-align:center;vertical-align:top" >
          <?php
        } // Ad placement ?>
        <tr style="padding:0;text-align:center;vertical-align:top" >
          <td class="small-12" style="Margin:0 auto;border-bottom:2px solid #EAEAEA;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:20px;padding-top:20px;text-align:center;width:640px" >
            <?php
            if ( isset( $post_ids ) && is_array( $post_ids ) && count( $post_ids ) > 0 ) {
              print_article( $newsletter, $post_ids[0], 'full' );
            }
            unset( $post_ids[0] );
            ?>
          </td>
        </tr>
      </tbody>
    </table>
    <?php if ( isset( $post_ids ) && is_array( $post_ids ) && count( $post_ids ) > 0 ) { ?>
      <!-- Two Column side by side stories -->
      <table style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;width:100%" >
        <tbody>
          <tr style="padding:0;text-align:center;vertical-align:top" >
          <?php
          $counter_posts = 1;
          foreach ( $post_ids as $post_index => $post_id ) {
            $counter_posts++;

            if ( $counter_posts == 6 ) {
          ?>
            </tr>
            <tr style="padding:0;text-align:center;vertical-align:top" >
              <td colspan="2" class="small-12" style="color:#0a0a0a;border-bottom:2px solid #EAEAEA;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:20px;padding-top:20px;text-align:center;width:320px;vertical-align:top" >
                <?php print_article( $newsletter, $post_id, 'full' ); ?>
              </td>
            </tr>
            <tr style="padding:0;text-align:center;vertical-align:top" >
          <?php
          // $counter_posts = 6; // Increase counter becuase spanning 2 cols above
          // continue;
          } else { ?>
            <td class="small-12" style="color:#0a0a0a;border-bottom:2px solid #EAEAEA;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:20px;padding-left:<?php echo ( $counter_posts < 6 && ( $counter_posts  ) % 2 == 0 )
            ||
            ( $counter_posts > 6 && ( $counter_posts + 1 ) % 2 == 0 ) ? '20px' : '10px'; ?>;padding-right:<?php echo ( $counter_posts < 6 && ( $counter_posts ) % 2 == 0 )
            ||
            ( $counter_posts > 6 && ( $counter_posts + 1 ) % 2 == 0 ) ? '10px' : '20px'; ?>;padding-top:20px;text-align:center;width:320px;vertical-align:top" >
              <?php print_article( $newsletter, $post_id, 'half' ); ?>
            </td>
          <?php
          // $counter_posts++;
          } // Colspan to 2 if featured article

          if ( in_array( $counter_posts, $ads_after_articles ) ) {
          ?>
            </tr>
            <tr style="padding:0;text-align:center;vertical-align:top" >
              <td colspan="2" class="small-12" style="Margin:0 auto;color:#0a0a0a;border-bottom:2px solid #EAEAEA;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:20px;padding-left:0;padding-right:0;padding-top:20px;text-align:center" >
                <?php print_ad( $counter_posts, $ads_after_articles, $newsletter ); ?>
              </td>
            </tr>
            <tr style="padding:0;text-align:center;vertical-align:top" >
          <?php
          } // Ad placement
          else if (
            ( $counter_posts < 6 && ( $counter_posts + 1 ) % 2 == 0 )
            ||
            ( $counter_posts > 6 && ( $counter_posts ) % 2 == 0 )
          ) {
          ?>
            </tr>
            <tr style="padding:0;text-align:center;vertical-align:top" >
          <?php
          } // If adding a new row

            // $counter_posts++;
          } // For Each $post_ids
          ?>
          </tr>

          <?php
          foreach( $ads_after_articles as $ads_after_article ) {
            if ( $ads_after_article > ( count( $post_ids ) + 1 ) ) {
          ?>
        </tr>
        <tr style="padding:0;text-align:center;vertical-align:top" >
          <td colspan="2" class="small-12" style="Margin:0 auto;color:#0a0a0a;border-bottom:2px solid #EAEAEA;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:20px;padding-left:0;padding-right:0;padding-top:20px;text-align:center" >
          <?php print_ad( $ads_after_article, $ads_after_articles, $newsletter ); ?>
          </td>
      </tr>
      <tr style="padding:0;text-align:center;vertical-align:top" >
        <?php
            }
          } // For Each $ads_after_articles
          ?>
        </tbody>
      </table>
      <!-- Two Column side by side stories -->
      <?php
    } // If $post_ids
    ?>
  </td>
</tr>

<?php if ( 4 == $list->id ) { ?>
  <tr>
    <td style="padding-top:20px;padding-bottom:20px;background-color:#ffffff;">
      <?php print_jobs_tio(); ?>
    </td>
  </tr>
  <?php if( isset( $newsletter->details->top_i_tweet_image ) && $newsletter->details->top_i_tweet_image != '' ) { ?>
  <tr>
    <td style="padding-top:20px;border-bottom:2px solid #EAEAEA;padding-bottom:20px;background-color:#ffffff;">
      <?php print_tio_tweet( $newsletter ); ?>
    </td>
  </tr>
  <?php } ?>
<?php } // if list is TIO ?>

<!-- Featured Video / Audio -->
<tr>
  <td>
    <?php print_video_record_of_week($this,$container_width); ?>
  </td>
</tr>
<!-- Featured Video / Audio -->

<?php
function print_ad( $value, $ads_after_articles, $newsletter ) {
  $array_key = array_search( $value, $ads_after_articles );

  if( FALSE === $array_key )
    return;
?>
<a href="<?php echo isset( $newsletter->details->ads[$array_key]->image ) && $newsletter->details->ads[$array_key]->link != '' ? $newsletter->details->ads[$array_key]->link : '#'; ?>" target="_blank">
  <?php // $ad_size = getimagesize( $newsletter->details->ads[$array_key]->image ); ?>
  <img align="center" alt="" src="<?php echo $newsletter->details->ads[$array_key]->image; ?>" width="300" height="250" style="max-width: 300px; max-height: 250px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
</a>
<?php
}

function print_article( $newsletter, $article_number, $style = 'full' ) {

  $tableWidth = 'full' == $style ? 700 : 300;
  $bgColor = 'full' == $style ? '#000000' : '#ffffff';
  $textColor = 'full' == $style ? '#F2F2F2' : '#000000';
  $paddingX = 'full' == $style ? 18 : 0;

    $post_links = ( $newsletter->details->post_links );
    $post_images = ( $newsletter->details->post_images );
    $post_titles = ( $newsletter->details->post_titles );
    $post_excerpts = ( $newsletter->details->post_excerpts );

    if ( ! is_null( $article_number ) ) : ?>

    <?php if ( 'full' == $style ) : ?>
      <table style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;width:100%" class="">
        <tbody class="">
          <tr style="padding:0;text-align:center;vertical-align:top" >
            <td style="text-align:center;" class="small-12-inner">
    <?php endif; ?>

    <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:center;vertical-align:top;">
      <tr style="padding:0;text-align:center;vertical-align:top">
        <td style="color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0;padding-top:0;padding-bottom:18px;text-align:center;background-color: <?php echo $bgColor; ?>;padding-left:0;padding-right:0;">
          <?php if ( isset( $post_images[ $article_number ] ) && $post_images[ $article_number ] != '' ) : ?>
          <a href="<?php echo $post_links[ $article_number ]; ?>" target="_blank" style="color:<?php echo $textColor; ?>;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.4;margin:0;padding:0;text-align:center;text-decoration:none"><img src="<?php echo  $post_images[ $article_number ]; ?>" alt="<?php echo $post_titles[ $article_number ]; ?>" title="<?php echo $post_titles[ $article_number ]; ?>" width="304" height="170" style="-ms-interpolation-mode:bicubic;border:none;clear:both;display:block;height:auto;margin-bottom:10px;max-width:100%;outline:0;text-decoration:none;width:100%;color:<?php echo $textColor; ?>;">
          </a>
          <?php endif; ?>
          <div style="padding-left:<?php echo $paddingX; ?>px;padding-right:<?php echo $paddingX; ?>px">
          <h2 style="Margin-bottom:5px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:400;line-height:1.4;margin:0;margin-bottom:5px;margin-top:5px;padding:0;word-wrap:normal" ><a class="headline" href="<?php echo $post_links[ $article_number ]; ?>" target="_blank" title="<?php echo $post_titles[ $article_number ]; ?>" style="Margin:0;color:<?php echo $textColor; ?>!important;font-family:Helvetica,Arial,sans-serif;font-size:23px;font-weight:700;line-height:28px;margin:0;padding:0;text-align:center;text-decoration:none" ><?php echo $post_titles[ $article_number ]; ?></a></h2>
          <div style="text-align: left; font-size:16px;color:<?php echo $textColor; ?>;font-family:arial,helvetica neue,helvetica,sans-serif">
            <?php echo $newsletter->details->post_excerpts[$article_number]; ?>
          </div>
        </div>
        </td>
      </tr>
    </table>

    <?php if ( 'full' == $style ) : ?>
          </td>
        </tr>
      </tbody>
    </table>
    <?php endif; ?>

<?php endif;
} // function print_articles
