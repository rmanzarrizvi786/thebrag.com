<?php
$table = $wpdb->base_prefix . "td_newsletters";
$newsletter = $wpdb->get_row( "SELECT * FROM $table WHERE id = {$id}" );
if( is_null( $newsletter ) ):
    die( 'Newsletter not found.' );
endif;
$newsletter->details = json_decode( $newsletter->details );
$exclude_posts = array();
$posts = (array) $newsletter->details->posts;
//add seperte logo for all multisite
$blog_id = get_current_blog_id();
$bm_logo = array(
	'https://gallery.mailchimp.com/a9d74bfce08ba307bfa8b9c78/images/c261b79b-2b6a-4440-bee7-e7460aa1ac70.png',
	'https://dad.thebrag.com/wp-content/themes/tbd/images/brag-dad_logo_300x80.png',
	'https://dontboreus.thebrag.com/wp-content/themes/dbu/images/dont-bore-us-black_600x92.png' 
);
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
	<head>
		<!-- NAME: 1:3:2 COLUMN -->
		<!--[if gte mso 15]>
        <xml>
            <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
        <![endif]-->
		<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>*|MC:SUBJECT|*</title>
        
    <style type="text/css">
		p{
			margin:10px 0;
			padding:0;
		}
		table{
			border-collapse:collapse;
		}
		h1,h2,h3,h4,h5,h6{
			display:block;
			margin:0;
			padding:0;
		}
		img,a img{
			border:0;
			height:auto;
			outline:none;
			text-decoration:none;
		}
		body,#bodyTable,#bodyCell{
			height:100%;
			margin:0;
			padding:0;
			width:100%;
		}
		#outlook a{
			padding:0;
		}
		img{
			-ms-interpolation-mode:bicubic;
		}
		table{
			mso-table-lspace:0pt;
			mso-table-rspace:0pt;
		}
		.ReadMsgBody{
			width:100%;
		}
		.ExternalClass{
			width:100%;
		}
		p,a,li,td,blockquote{
			mso-line-height-rule:exactly;
		}
		a[href^=tel],a[href^=sms]{
			color:inherit;
			cursor:default;
			text-decoration:none;
		}
		p,a,li,td,body,table,blockquote{
			-ms-text-size-adjust:100%;
			-webkit-text-size-adjust:100%;
		}
		.ExternalClass,.ExternalClass p,.ExternalClass td,.ExternalClass div,.ExternalClass span,.ExternalClass font{
			line-height:100%;
		}
		a[x-apple-data-detectors]{
			color:inherit !important;
			text-decoration:none !important;
			font-size:inherit !important;
			font-family:inherit !important;
			font-weight:inherit !important;
			line-height:inherit !important;
		}
		#bodyCell{
			padding:10px;
		}
		.templateContainer{
			max-width:600px !important;
		}
		a.mcnButton{
			display:block;
		}
		.mcnImage{
			vertical-align:bottom;
		}
		.mcnTextContent{
			word-break:break-word;
		}
		.mcnTextContent img{
			height:auto !important;
		}
		.mcnDividerBlock{
			table-layout:fixed !important;
		}
		body,#bodyTable{
			background-color:#FAFAFA;
		}
		#bodyCell{
			border-top:0;
		}
		.templateContainer{
			border:0;
		}
		h1{
			color:#202020;
			font-family:Helvetica;
			font-size:26px;
			font-style:normal;
			font-weight:bold;
			line-height:125%;
			letter-spacing:normal;
			text-align:left;
		}
		h2{
			color:#202020;
			font-family:Helvetica;
			font-size:22px;
			font-style:normal;
			font-weight:bold;
			line-height:125%;
			letter-spacing:normal;
			text-align:left;
		}
		h3{
			color:#202020;
			font-family:Helvetica;
			font-size:20px;
			font-style:normal;
			font-weight:bold;
			line-height:125%;
			letter-spacing:normal;
			text-align:left;
		}
		h4{
			color:#202020;
			font-family:Helvetica;
			font-size:18px;
			font-style:normal;
			font-weight:bold;
			line-height:125%;
			letter-spacing:normal;
			text-align:left;
		}
		#templatePreheader{
			background-color:#fafafa;
			background-image:none;
			background-repeat:no-repeat;
			background-position:center;
			background-size:cover;
			border-top:0;
			border-bottom:0;
			padding-top:9px;
			padding-bottom:9px;
		}
		#templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
			color:#656565;
			font-family:Helvetica;
			font-size:12px;
			line-height:150%;
			text-align:left;
		}
		#templatePreheader .mcnTextContent a,#templatePreheader .mcnTextContent p a{
			color:#656565;
			font-weight:normal;
			text-decoration:none;
		}
		#templateHeader{
			background-color:#ffffff;
			background-image:none;
			background-repeat:no-repeat;
			background-position:center;
			background-size:cover;
			border-top:0;
			border-bottom:0;
			padding-top:0;
			padding-bottom:0;
		}
		#templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
			color:#202020;
			font-family:Helvetica;
			font-size:16px;
			line-height:150%;
			text-align:left;
		}
		#templateHeader .mcnTextContent a,#templateHeader .mcnTextContent p a{
			color:#000000;
			font-weight:normal;
			text-decoration:none;
		}
		#templateBody{
			background-color:#ffffff;
			background-image:none;
			background-repeat:no-repeat;
			background-position:center;
			background-size:cover;
			border-top:0;
			border-bottom:0;
			padding-top:0;
			padding-bottom:0;
		}
		#templateBody .mcnTextContent,#templateBody .mcnTextContent p{
			color:#202020;
			font-family:Helvetica;
			font-size:16px;
			line-height:150%;
			text-align:left;
		}
		#templateBody .mcnTextContent a,#templateBody .mcnTextContent p a{
			color:#000000;
			font-weight:bold;
			text-decoration:none;
		}
		#templateUpperColumns{
			background-color:#ffffff;
			background-image:none;
			background-repeat:no-repeat;
			background-position:center;
			background-size:cover;
			border-top:0;
			border-bottom:0;
			padding-top:0;
			padding-bottom:0;
		}
		#templateUpperColumns .columnContainer .mcnTextContent,#templateUpperColumns .columnContainer .mcnTextContent p{
			color:#202020;
			font-family:Helvetica;
			font-size:16px;
			line-height:150%;
			text-align:left;
		}
		#templateUpperColumns .columnContainer .mcnTextContent a,#templateUpperColumns .columnContainer .mcnTextContent p a{
			color:#000000;
			font-weight:bold;
			text-decoration:none;
		}
		.templateLowerColumns{
			background-color:#ffffff;
			background-image:none;
			background-repeat:no-repeat;
			background-position:center;
			background-size:cover;
			border-top:0;
			border-bottom:2px solid #EAEAEA;
			padding-top: 9px;
			padding-bottom:9px;
		}
		.templateLowerColumns .columnContainer .mcnTextContent,.templateLowerColumns .columnContainer .mcnTextContent p{
			color:#202020;
			font-family:Helvetica;
			font-size:16px;
			line-height:150%;
			text-align:left;
		}
		.templateLowerColumns .columnContainer .mcnTextContent a,.templateLowerColumns .columnContainer .mcnTextContent p a{
			color:#000000;
			font-weight:normal;
			text-decoration:none;
		}
		.templateFooter{
			background-color:#ffffff;
			background-image:none;
			background-repeat:no-repeat;
			background-position:center;
			background-size:cover;
			border-top:0;
			border-bottom:0;
			padding-top:9px;
			padding-bottom:9px;
		}
		.templateFooter .mcnTextContent,.templateFooter .mcnTextContent p{
			color:#656565;
			font-family:Helvetica;
			font-size:12px;
			line-height:150%;
			text-align:center;
		}
		.templateFooter .mcnTextContent a,.templateFooter .mcnTextContent p a{
			color:#656565;
			font-weight:normal;
			text-decoration:underline;
		}
	@media only screen and (min-width:768px){
		.templateContainer{
			width:600px !important;
		}

}	@media only screen and (max-width: 480px){
		body,table,td,p,a,li,blockquote{
			-webkit-text-size-adjust:none !important;
		}

}	@media only screen and (max-width: 480px){
		body{
			width:100% !important;
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		#bodyCell{
			padding-top:10px !important;
		}

}	@media only screen and (max-width: 480px){
		.columnWrapper{
			max-width:100% !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImage{
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer{
			max-width:100% !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnBoxedTextContentContainer{
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupContent{
			padding:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCaptionLeftContentOuter .mcnTextContent,.mcnCaptionRightContentOuter .mcnTextContent{
			padding-top:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardTopImageContent,.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{
			padding-top:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardBottomImageContent{
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockInner{
			padding-top:0 !important;
			padding-bottom:0 !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockOuter{
			padding-top:9px !important;
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnTextContent,.mcnBoxedTextContentColumn{
			padding-right:18px !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{
			padding-right:18px !important;
			padding-bottom:0 !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcpreview-image-uploader{
			display:none !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		h1{
			font-size:16px !important;
			line-height:125% !important;
                        font-weight:bold !important;
		}

}	@media only screen and (max-width: 480px){
		h2{
			font-size:14px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		h3{
			font-size:14px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		h4{
			font-size:14px !important;
			line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnBoxedTextContentContainer .mcnTextContent,.mcnBoxedTextContentContainer .mcnTextContent p{
			font-size:14px !important;
			line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
		#templatePreheader{
			display:block !important;
		}

}	@media only screen and (max-width: 480px){
		#templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
			font-size:14px !important;
			line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
		#templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
			font-size:16px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		#templateBody .mcnTextContent,#templateBody .mcnTextContent p{
			font-size:12px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		#templateUpperColumns .columnContainer .mcnTextContent,#templateUpperColumns .columnContainer .mcnTextContent p{
			font-size:12px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		.templateLowerColumns .columnContainer .mcnTextContent,.templateLowerColumns .columnContainer .mcnTextContent p{
			font-size:12px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		.templateFooter .mcnTextContent,.templateFooter .mcnTextContent p{
			font-size:10px !important;
			line-height:100% !important;
		}

}</style></head>
<body>
    <center>
        <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
            <tr>
                <td align="center" valign="top" id="bodyCell">
                    <!-- BEGIN TEMPLATE // -->
                    <!--[if gte mso 9]>
                    <table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
                    <tr>
                    <td align="center" valign="top" width="600" style="width:600px;">
                    <![endif]-->
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
                        <tr>
                            <td valign="top" id="templatePreheader">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
                                    <tbody class="mcnTextBlockOuter">
                                        <tr>
                                            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
                                                <!--[if mso]>
                                                <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                                                <tr>
                                                <![endif]-->
                                                <!--[if mso]>
                                                <td valign="top" width="390" style="width:390px;">
                                                <![endif]-->
                                                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:390px;" width="100%" class="mcnTextContentContainer">
                                                    <tbody>
                                                        <tr>
                                                            <td valign="top" class="mcnTextContent" style="padding-top:0; padding-left: 5px; padding-bottom:9px; padding-right:18px;">The latest in music, comedy and more from thebrag.com</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <!--[if mso]>
                                                </td>
                                                <![endif]-->
                                                <!--[if mso]>
                                                <td valign="top" width="210" style="width:210px;">
                                                <![endif]-->
                                                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:210px;" width="100%" class="mcnTextContentContainer">
                                                    <tbody>
                                                        <tr>
                                                            <td valign="top" class="mcnTextContent" style="padding-top:0; padding-left:18px; padding-bottom:9px; padding-right:5px; text-align: right;"><a href="*|ARCHIVE|*" target="_blank">View email in browser</a></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <!--[if mso]>
                                                </td>
                                                <![endif]-->
                                                <!--[if mso]>
                                                </tr>
                                                </table>
                                                <![endif]-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" id="templateHeader">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width:100%;">
                                    <tbody class="mcnImageBlockOuter">
                                        <tr>
                                            <td valign="top" style="padding:9px; background: #ffffff;" class="mcnImageBlockInner">
                                                <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="mcnImageContent" valign="top" style="padding-right: 9px; padding-left: 9px; padding-top: 10px; padding-bottom: 10px; text-align:center; background: #ffffff;">
                                                                <a href="http://www.thebrag.com" title="" class="" target="_blank">
                                                                    <img align="center" alt="" src="<?php echo esc_url( $bm_logo[$blog_id - 1 ] ); ?>" width="152" style="max-width:152px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnFollowBlock" style="min-width:100%;">
                                    <tbody class="mcnFollowBlockOuter">
                                        <tr>
                                            <td align="center" valign="top" style="padding:9px" class="mcnFollowBlockInner">
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnFollowContentContainer" style="min-width:100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td align="center" style="padding-left:9px;padding-right:9px;">
                                                                <table border="0" cellpadding="0" cellspacing="0" class="mcnFollowContent">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td align="center" valign="top" style="padding-top:9px; padding-right:9px; padding-left:9px;">
                                                                                <?php print_social_icons(); ?>
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
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" id="templateBody">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width:100%;">
                                    <tbody class="mcnImageBlockOuter">
                                        <tr>
                                            <td valign="top" style="padding:9px" class="mcnImageBlockInner">
                                                <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="mcnImageContent" valign="top" style="padding-right: 9px; padding-left: 9px; padding-top: 0; padding-bottom: 0; text-align:center;">
                                <?php if ( isset( $newsletter->details->cover_story_image ) && $newsletter->details->cover_story_image != '' ) : ?>
                                                                <a href="<?php echo $newsletter->details->cover_story_link; ?>" target="_blank">
                                                                    <img align="center" alt="" src="<?php echo $newsletter->details->cover_story_image; ?>" width="564" style="max-width:625px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                                                                </a>
                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
                                    <tbody class="mcnTextBlockOuter">
                                        <tr>
                                            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
                                                <!--[if mso]>
                                                <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                                                <tr>
                                                <![endif]-->
                                                <!--[if mso]>
                                                <td valign="top" width="600" style="width:600px;">
                                                <![endif]-->
                                                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                                                    <tbody>
                                                        <tr>
                                                            <td valign="top" class="mcnTextContent" style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;">
                                                                <h1><a href="<?php echo $newsletter->details->cover_story_link; ?>" target="_blank" style="color: #000000;"><?php echo $newsletter->details->cover_story_title; ?></a></h1>
                                                                <p><?php echo $newsletter->details->cover_story_excerpt; ?></p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <!--[if mso]>
                                                </td>
                                                <![endif]-->
                                                <!--[if mso]>
                                                </tr>
                                                </table>
                                                <![endif]-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" id="templateUpperColumns">
                                <!--[if gte mso 9]>
                                <table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
                                <tr>
                                <td align="center" valign="top" width="200" style="width:200px;">
                                <![endif]-->
                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="200" class="columnWrapper">
                                    <tr>
                                        <td valign="top" class="columnContainer"></td>
                                    </tr>
                                </table>
                                <!--[if gte mso 9]>
                                </td>
                                <td align="center" valign="top" width="200" style="width:200px;">
                                <![endif]-->
                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="200" class="columnWrapper">
                                    <tr>
                                        <td valign="top" class="columnContainer"></td>
                                    </tr>
                                </table>
                                <!--[if gte mso 9]>
                                </td>
                                <td align="center" valign="top" width="200" style="width:200px;">
                                <![endif]-->
                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="200" class="columnWrapper">
                                    <tr>
                                        <td valign="top" class="columnContainer"></td>
                                    </tr>
                                </table>
                                <!--[if gte mso 9]>
                                </td>
                                </tr>
                                </table>
                                <![endif]-->
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" class="templateLowerColumns">
                                <!--[if gte mso 9]>
                                <table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
                                <tr>[endif]-->
<?php
print_article( $newsletter, array_keys( array_slice( $posts, 0, 1 ) )[0] ); // First Article
print_article( $newsletter, array_keys( array_slice( $posts, 1, 1 ) )[0] ); // Second Article
?>                           
                                <!--[if gte mso 9]></tr>
                                </table>
                                <![endif]-->
                            </td>
                        </tr>
                        
<?php if ( isset( $newsletter->details->ad_middle_1_image ) && $newsletter->details->ad_middle_1_image != '' ): ?>
                        <tr>
                            <td style="background: #ffffff;" class="templateLowerColumns">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width:100%;">
                                    <tbody class="mcnImageBlockOuter">
                                        <tr>
                                            <td valign="top" style="padding:9px" class="mcnImageBlockInner">
                                                <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="mcnImageContent" valign="top" style="padding-right: 9px; padding-left: 9px; padding-top: 0; padding-bottom: 0; text-align:center;">
                                                                <a href="<?php echo isset( $newsletter->details->ad_middle_1_link ) && $newsletter->details->ad_middle_1_link != '' ? $newsletter->details->ad_middle_1_link : '#'; ?>" title="" class="" target="_blank">
                                                                    <img align="center" alt="" src="<?php echo $newsletter->details->ad_middle_1_image; ?>" width="564" style="max-width:728px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
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
<?php endif; // If Ad 1 is available ?>
                        
<?php
if( isset( $newsletter->details->featured_story_title_1 ) && $newsletter->details->featured_story_title_1 != '' ):
?>
                        <tr>
                            <td style="background-color: #ffffff;" class="templateLowerColumns">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageCardBlock">
                                    <tbody class="mcnImageCardBlockOuter">
                                        <tr>
                                            <td class="mcnImageCardBlockInner" valign="top" style="padding-top:9px; padding-right:18px; padding-bottom:9px; padding-left:18px;">
                                                <table align="right" border="0" cellpadding="0" cellspacing="0" class="mcnImageCardBottomContent" width="100%" style="background-color: #000000;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="mcnImageCardBottomImageContent" align="left" valign="top" style="padding-top:0px; padding-right:0px; padding-bottom:0; padding-left:0px;">
                                                                <?php if ( isset( $newsletter->details->featured_story_image_1 ) && $newsletter->details->featured_story_image_1 != '' ) : ?>
                                                                <a href="<?php echo $newsletter->details->featured_story_link_1; ?>" target="_blank">
                                                                    <img align="center" alt="" src="<?php echo $newsletter->details->featured_story_image_1; ?>" width="564" style="max-width:625px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                                                                </a>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="mcnTextContent" valign="top" style="padding: 9px 18px;color: #F2F2F2;font-family: Helvetica;font-size: 14px;font-style: normal;font-weight: normal;line-height: 150%;text-align: center;" width="546">
                                                                <h1 class="null"><font color="#ffffff"><?php echo $newsletter->details->featured_story_title_1; ?></font></h1>
                                                                <div style="text-align: left;"><span style="font-size:16px"><span style="color:#FFFFFF"><span style="font-family:arial,helvetica neue,helvetica,sans-serif"><?php echo $newsletter->details->featured_story_excerpt_1; ?></span></span></span></div>
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
<?php
endif; // If Featured Article 1 is available
?>
                        
                        <tr>
                            <td valign="top" class="templateLowerColumns">
                                <!--[if gte mso 9]>
                                <table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
                                <tr>[endif]-->
<?php
print_article( $newsletter, array_keys( array_slice( $posts, 2, 1 ) )[0] ); // Third Article
print_article( $newsletter, array_keys( array_slice( $posts, 3, 1 ) )[0] ); // Fourth Article
?>                              
                                <!--[if gte mso 9]></tr>
                                </table>
                                <![endif]-->
                            </td>
                        </tr>
                        
<?php if ( isset( $newsletter->details->ad_middle_2_image ) && $newsletter->details->ad_middle_2_image != '' ): ?>
                        <tr>
                            <td style="background: #ffffff;" class="templateLowerColumns">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width:100%;">
                                    <tbody class="mcnImageBlockOuter">
                                        <tr>
                                            <td valign="top" style="padding:9px" class="mcnImageBlockInner">
                                                <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="mcnImageContent" valign="top" style="padding-right: 9px; padding-left: 9px; padding-top: 0; padding-bottom: 0; text-align:center;">
                                                                <a href="<?php echo isset( $newsletter->details->ad_middle_2_link ) && $newsletter->details->ad_middle_2_link != '' ? $newsletter->details->ad_middle_2_link : '#'; ?>" title="" class="" target="_blank">
                                                                    <img align="center" alt="" src="<?php echo $newsletter->details->ad_middle_2_image; ?>" width="564" style="max-width:728px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
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
<?php endif; // If Ad 1 is available ?>

<?php
if( isset( $newsletter->details->featured_story_title_2 ) && $newsletter->details->featured_story_title_2 != '' ):
?>
                        <tr>
                            <td style="background-color: #ffffff;" class="templateLowerColumns">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageCardBlock">
                                    <tbody class="mcnImageCardBlockOuter">
                                        <tr>
                                            <td class="mcnImageCardBlockInner" valign="top" style="padding-top:9px; padding-right:18px; padding-bottom:9px; padding-left:18px;">
                                                <table align="right" border="0" cellpadding="0" cellspacing="0" class="mcnImageCardBottomContent" width="100%" style="background-color: #000000;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="mcnImageCardBottomImageContent" align="left" valign="top" style="padding-top:0px; padding-right:0px; padding-bottom:0; padding-left:0px;">
                                                                <?php if ( isset( $newsletter->details->featured_story_image_2 ) && $newsletter->details->featured_story_image_2 != '' ) : ?>
                                                                <a href="<?php echo $newsletter->details->featured_story_link_2; ?>" target="_blank">
                                                                    <img align="center" alt="" src="<?php echo $newsletter->details->featured_story_image_2; ?>" width="564" style="max-width:625px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                                                                </a>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="mcnTextContent" valign="top" style="padding: 9px 18px;color: #F2F2F2;font-family: Helvetica;font-size: 14px;font-style: normal;font-weight: normal;line-height: 150%;text-align: center;" width="546">
                                                                <h1 class="null"><font color="#ffffff"><?php echo $newsletter->details->featured_story_title_2; ?></font></h1>
                                                                <div style="text-align: left;"><span style="font-size:16px"><span style="color:#FFFFFF"><span style="font-family:arial,helvetica neue,helvetica,sans-serif"><?php echo $newsletter->details->featured_story_excerpt_2; ?></span></span></span></div>
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
<?php
endif; // If Featured Article 1 is available
?>

<?php
for( $i = 4; $i <= count( $posts ); $i+=2 ):
    $remaining_posts = array_slice( $posts, $i, 2 );
if ( count( $remaining_posts ) > 0 ) :
?>
                        <tr>
                            <td valign="top" class="templateLowerColumns">
                                <!--[if gte mso 9]>
                                <table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
                                <tr>[endif]-->
<?php
foreach ( $remaining_posts as $p_id => $rp ) :
    print_article( $newsletter, $p_id );
endforeach;
?>
                                <!--[if gte mso 9]></tr>
                                </table>
                                <![endif]-->
                            </td>
                        </tr>
<?php endif; endfor; ?>

                        
<?php
//$votw = file_get_contents( 'http://tone-deaf.com.au/?feed=common_details&type=votw' );
//$votw = json_decode( $votw );
if( get_option('edm_include_video_story') == '1' ):
?>
                        <tr>
                            <td style="background-color: #ffffff;" class="templateLowerColumns">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageCardBlock">
                                    <tbody class="mcnImageCardBlockOuter">
                                        <tr>
                                            <td class="mcnImageCardBlockInner" valign="top" style="padding-top:9px; padding-right:18px; padding-bottom:9px; padding-left:18px;">
                                                <table align="right" border="0" cellpadding="0" cellspacing="0" class="mcnImageCardBottomContent" width="100%" style="background-color: #000000;">
                                                    <tbody>
                                                        <tr>
                                                            <td class="mcnTextContent" valign="top" style="padding: 9px 18px;color: #ffffff;font-family: Helvetica;font-size: 14px;font-style: normal;font-weight: normal;line-height: 150%;text-align: center;" width="546">
                                                                <h1 class="null" style="text-align: center; padding: 0; margin: 0;"><font color="#ffffff">VIDEO OF THE WEEK</font></h1>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="mcnImageCardBottomImageContent" align="left" valign="top" style="padding-top:0px; padding-right:0px; padding-bottom:0; padding-left:0px; text-align: center;">
                                                                <?php if ( get_option('edm_featured_video_image') != '' ) : ?>
                                                                <a href="<?php echo get_option('edm_featured_video_link'); ?>" target="_blank">
                                                                    <img align="center" alt="" src="<?php echo get_option('edm_featured_video_image'); ?>" width="564" style="max-width:625px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                                                                </a>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="mcnTextContent" valign="top" style="padding: 9px 18px;color: #ffffff;font-family: Helvetica;font-size: 14px;font-style: normal;font-weight: normal;line-height: 150%;text-align: center;" width="546">
                                                                <h1 class="null" style="text-align: center; padding: 0; margin: 0;">
                                                                    <a href="<?php echo get_option('edm_featured_video_link'); ?>" target="_blank" style="text-decoration: none;">
                                                                        <font color="#ffffff"><?php echo get_option('edm_featured_video_title'); ?></font>
                                                                    </a>
                                                                </h1>
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
<?php
endif; // If Featured Video is available
?>
                            
                        <tr>
                            <td valign="top" class="templateFooter">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
                                    <tbody class="mcnTextBlockOuter">
                                        <tr>
                                            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
                                                <!--[if mso]>
                                                <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                                                <tr>
                                                <![endif]-->
                                                <!--[if mso]>
                                                <td valign="top" width="600" style="width:600px;">
                                                <![endif]-->
                                                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                                                    <tbody>
                                                        <tr>
                                                            <td valign="top" class="mcnTextContent" style="padding-top:0; padding: 20px 0; color: #dedede; text-align: center;">
                                                                <a href="*|UPDATE_PROFILE|*" style="color: #333333 !important;text-decoration: none;">Update my details</a>
                                                                &nbsp;
                                                                |
                                                                &nbsp;
                                                                <a href="*|UNSUB|*" style="color: #333333 !important;text-decoration: none;">Unsubscribe</a>
                                                                &nbsp;
                                                                |
                                                                &nbsp;
                                                                <a target="_blank" title="View email in web browser" href="*|ARCHIVE|*" style="color: #333333;text-decoration: none;">Web version</a>
                                                                &nbsp;
                                                                |
                                                                &nbsp;
                                                                <a title="Advertise with us" href="http://seventhstreetmedia.com.au" target="_blank" style="color: #333333;text-decoration: none;">Advertise with us</a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <!--[if mso]>
                                                </td>
                                                <![endif]-->
                                                <!--[if mso]>
                                                </tr>
                                                </table>
                                                <![endif]-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        
                        <tr>
                            <td valign="top" class="templateFooter" style="background: #ffffff; text-align: center; padding: 10px 0;">
                                <img src="<?php echo esc_url( $bm_logo[$blog_id - 1 ] ); ?>" width="152" style="max-width:100%; padding-bottom: 0; display: inline !important; vertical-align: bottom;">
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" class="templateFooter" align="right" style="text-align: right; padding: 5px; background: #333333; font-size: 10px; color: #AAAAAA;">
                                ...part of <a href="http://seventhstreetmedia.com.au" target="_blank" style="color: #ffffff;text-decoration: none; font-size: 10px;">Seventh Street Media</a>
                            </td>
                        </tr>
                    </table>
                    <!--[if gte mso 9]>
                    </td>
                    </tr>
                    </table>
                    <![endif]-->
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
<?php
function print_article( $newsletter, $article_number ) {

    if ( !is_null( $article_number ) ) :
?>
    <!--[if gte mso 9]<td align="center" valign="top" width="300" style="width:300px;">
    <![endif]-->
    <table align="left" border="0" cellpadding="0" cellspacing="0" width="300" class="columnWrapper">
        <tr>
            <td valign="top" class="columnContainer">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnCaptionBlock">
                    <tbody class="mcnCaptionBlockOuter">
                        <tr>
                            <td class="mcnCaptionBlockInner" valign="top" style="padding:9px;">
                                <table align="left" border="0" cellpadding="0" cellspacing="0" class="mcnCaptionBottomContent" width="false">
                                    <tbody>
<?php if ( isset( $newsletter->details->post_images->{$article_number} ) && $newsletter->details->post_images->{$article_number} != '' ) : ?>
                                        <tr>
                                            <td class="mcnCaptionBottomImageContent" align="center" valign="top" style="padding:0 9px 9px 9px;">
                                                <?php if ( isset( $newsletter->details->post_links->{$article_number} ) && $newsletter->details->post_links->{$article_number} != '' ) : ?>
                                                <a href="<?php echo $newsletter->details->post_links->{$article_number}; ?>" title="" class="" target="_blank">
                                                    <img alt="" src="<?php echo  $newsletter->details->post_images->{$article_number}; ?>" width="264" style="max-width:625px;" class="mcnImage">
                                                </a>
                                                <?php else: ?>
                                                    <img alt="" src="<?php echo  $newsletter->details->post_images->{$article_number}; ?>" width="264" style="max-width:625px;" class="mcnImage">
                                                <?php endif; ?>
                                            </td>
                                        </tr>
<?php endif; // If Image Field Exists and Not Empty ?>
                                        <tr>
                                            <td class="mcnTextContent" valign="top" style="padding:0 9px 0 9px;" width="264">
                                                <?php if ( isset( $newsletter->details->post_links->{$article_number} ) && $newsletter->details->post_links->{$article_number} != '' ) : ?>
                                                <a href="<?php echo $newsletter->details->post_links->{$article_number}; ?>" target="_blank">
                                                    <span style="font-size:14px"><strong><?php echo $newsletter->details->post_titles->{$article_number}; ?></strong></span>
                                                </a>
                                                <?php else: ?>
                                                    <span style="font-size:14px"><strong><?php echo $newsletter->details->post_titles->{$article_number}; ?></strong></span>
                                                <?php endif; ?>
                                                <br>
                                                <span style="font-size:14px"><?php echo $newsletter->details->post_excerpts->{$article_number}; ?></span>
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
    <!--[if gte mso 9]>
    </td>[endif]-->
<?php
    endif;
}

function print_social_icons() {

	/**
	* added seperate social links and logo for all multisite
	*/
	$blog_id = get_current_blog_id();
	$bm_social_links = array(
		'facebook' => array(
			'http://www.facebook.com/thebragsydney',
			'https://www.facebook.com/The-Brag-Dad-2014698975428870',
			'https://www.facebook.com/dontboreus'

		),
		'twitter' => array(
			'https://twitter.com/thebrag',
			'https://twitter.com/thebragdad',
			'https://twitter.com/dontboreus'
		),
		'instagram' => array(
			'http://instagram.com/thebragmag',
			'https://www.instagram.com/thebragdad/',
			'https://www.instagram.com/dontboreus/'
		),
		'link' => array(
			'http://www.thebrag.com',
			'https://dad.thebrag.com/',
			'https://dontboreus.thebrag.com/'
		)
	);
?>
    <table align="center" border="0" cellpadding="0" cellspacing="0">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td align="center" valign="top">
                                                                                                <!--[if mso]>
                                                                                                <table align="center" border="0" cellspacing="0" cellpadding="0">
                                                                                                <tr>
                                                                                                <![endif]-->
                                                                                                
                                                                                                <!--[if mso]>
                                                                                                <td align="center" valign="top">
                                                                                                <![endif]-->
                                                                                                <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;">
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <td valign="top" style="padding-right:5px; padding-bottom:9px;" class="mcnFollowContentItemContainer">
                                                                                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnFollowContentItem">
                                                                                                                    <tbody>
                                                                                                                        <tr>
                                                                                                                            <td align="left" valign="middle" style="padding-top:5px; padding-right:10px; padding-bottom:5px; padding-left:9px;">
                                                                                                                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="">
                                                                                                                                    <tbody>
                                                                                                                                        <tr>
                                                                                                                                            <td align="center" valign="middle" width="24" class="mcnFollowIconContent">
                                                                                                                                                <a href="<?php echo esc_url( $bm_social_links[ 'facebook' ][$blog_id - 1 ] ); ?>" target="_blank"><img src="https://cdn-images.mailchimp.com/icons/social-block-v2/color-facebook-48.png" style="display:block;" height="24" width="24" class=""></a>
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
                                                                                                    </tbody>
                                                                                                </table>
                                                                                                <!--[if mso]>
                                                                                                </td>
                                                                                                <![endif]-->
                                                                                                
                                                                                                <!--[if mso]>
                                                                                                <td align="center" valign="top">
                                                                                                <![endif]-->
                                                                                                <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;">
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <td valign="top" style="padding-right:5px; padding-bottom:9px;" class="mcnFollowContentItemContainer">
                                                                                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnFollowContentItem">
                                                                                                                    <tbody>
                                                                                                                        <tr>
                                                                                                                            <td align="left" valign="middle" style="padding-top:5px; padding-right:10px; padding-bottom:5px; padding-left:9px;">
                                                                                                                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="">
                                                                                                                                    <tbody>
                                                                                                                                        <tr>
                                                                                                                                            <td align="center" valign="middle" width="24" class="mcnFollowIconContent">
                                                                                                                                                <a href="<?php echo esc_url( $bm_social_links[ 'twitter' ][$blog_id - 1 ] ); ?>" target="_blank"><img src="https://cdn-images.mailchimp.com/icons/social-block-v2/color-twitter-48.png" style="display:block;" height="24" width="24" class=""></a>
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
                                                                                                    </tbody>
                                                                                                </table>
                                                                                                <!--[if mso]>
                                                                                                </td>
                                                                                                <![endif]-->
                                                                                                
                                                                                                <!--[if mso]>
                                                                                                <td align="center" valign="top">
                                                                                                <![endif]-->
                                                                                                <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;">
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <td valign="top" style="padding-right:5px; padding-bottom:9px;" class="mcnFollowContentItemContainer">
                                                                                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnFollowContentItem">
                                                                                                                    <tbody>
                                                                                                                        <tr>
                                                                                                                            <td align="left" valign="middle" style="padding-top:5px; padding-right:10px; padding-bottom:5px; padding-left:9px;">
                                                                                                                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="">
                                                                                                                                    <tbody>
                                                                                                                                        <tr>
                                                                                                                                            <td align="center" valign="middle" width="24" class="mcnFollowIconContent">
                                                                                                                                                <a href="<?php echo esc_url( $bm_social_links[ 'facebook' ][$blog_id - 1 ] ); ?>" target="_blank"><img src="https://cdn-images.mailchimp.com/icons/social-block-v2/color-instagram-48.png" style="display:block;" height="24" width="24" class=""></a>
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
                                                                                                    </tbody>
                                                                                                </table>
                                                                                                <!--[if mso]>
                                                                                                </td>
                                                                                                <![endif]-->
                                                                                                
                                                                                                <!--[if mso]>
                                                                                                <td align="center" valign="top">
                                                                                                <![endif]-->
                                                                                                <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;">
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <td valign="top" style="padding-right:0; padding-bottom:9px;" class="mcnFollowContentItemContainer">
                                                                                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnFollowContentItem">
                                                                                                                    <tbody>
                                                                                                                        <tr>
                                                                                                                            <td align="left" valign="middle" style="padding-top:5px; padding-right:10px; padding-bottom:5px; padding-left:9px;">
                                                                                                                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="">
                                                                                                                                    <tbody>
                                                                                                                                        <tr>
                                                                                                                                            <td align="center" valign="middle" width="24" class="mcnFollowIconContent">
                                                                                                                                                <a href="<?php echo esc_url( $bm_social_links[ 'link' ][$blog_id - 1 ] ); ?>" target="_blank"><img src="https://cdn-images.mailchimp.com/icons/social-block-v2/color-link-48.png" style="display:block;" height="24" width="24" class=""></a>
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
                                                                                                    </tbody>
                                                                                                </table>
                                                                                                <!--[if mso]>
                                                                                                </td>
                                                                                                <![endif]-->
                                                                                                
                                                                                                <!--[if mso]>
                                                                                                </tr>
                                                                                                </table>
                                                                                                <![endif]-->
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
<?php
}