<?php
$id = isset($_GET['id']) ? $_GET['id'] : null;
if (is_null($id)) :
	return;
endif;
$newsletter = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_newsletters WHERE id = {$id} LIMIT 1");
if (is_null($newsletter)) :
	return;
endif;
$newsletter->details = json_decode($newsletter->details);
foreach ($newsletter->details as $k => $v) {
	if (is_object($v)) {
		$v = (array) $v;
		// $v = array_values( $v );
		$newsletter->details->{$k} = $v;
	}
}
$exclude_posts = array();
$posts = isset($newsletter->details->posts) ? $newsletter->details->posts : [];
$post_ids = array_keys($posts);

$list = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_lists WHERE id = {$newsletter->list_id}");

$logo = [];
$logo['url'] = $list && $list->email_header_image_url && '' != $list->email_header_image_url ? $list->email_header_image_url : 'https://cdn.thebrag.com/tb/The-Brag_combo-300px.png';
$logo['width'] = $list && $list->email_header_image_url && '' != $list->email_header_image_url ? 660 : 300;

$media_logo = 'https://cdn.thebrag.com/observer/images/email-footer-media-logo.jpg';

$container_width = 700;
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
		p {
			margin: 10px 0;
			padding: 0;
		}

		table {
			border-collapse: collapse;
		}

		h1,
		h2,
		h3,
		h4,
		h5,
		h6 {
			display: block;
			margin: 0;
			padding: 0;
		}

		img,
		a img {
			border: 0;
			height: auto;
			outline: none;
			text-decoration: none;
		}

		body,
		#bodyTable,
		#bodyCell {
			height: 100%;
			margin: 0;
			padding: 0;
			width: 100%;
		}

		#outlook a {
			padding: 0;
		}

		img {
			-ms-interpolation-mode: bicubic;
		}

		table {
			mso-table-lspace: 0pt;
			mso-table-rspace: 0pt;
		}

		.ReadMsgBody {
			width: 100%;
		}

		.ExternalClass {
			width: 100%;
		}

		p,
		a,
		li,
		td,
		blockquote {
			mso-line-height-rule: exactly;
		}

		a[href^=tel],
		a[href^=sms] {
			color: inherit;
			cursor: default;
			text-decoration: none;
		}

		p,
		a,
		li,
		td,
		body,
		table,
		blockquote {
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%;
		}

		.ExternalClass,
		.ExternalClass p,
		.ExternalClass td,
		.ExternalClass div,
		.ExternalClass span,
		.ExternalClass font {
			line-height: 100%;
		}

		a[x-apple-data-detectors] {
			color: inherit !important;
			text-decoration: none !important;
			font-size: inherit !important;
			font-family: inherit !important;
			font-weight: inherit !important;
			line-height: inherit !important;
		}

		#bodyCell {
			padding: 10px;
		}

		.templateContainer {
			max-width: <?php echo $container_width; ?>px !important;
		}

		a.mcnButton {
			display: block;
		}

		.mcnImage {
			vertical-align: bottom;
		}

		.mcnTextContent {
			word-break: break-word;
		}

		.mcnTextContent img {
			height: auto !important;
		}

		.mcnDividerBlock {
			table-layout: fixed !important;
		}

		body,
		#bodyTable {
			background-color: #FAFAFA;
		}

		#bodyCell {
			border-top: 0;
		}

		.templateContainer {
			border: 0;
		}

		h1 {
			color: #202020;
			font-family: Helvetica;
			font-size: 26px;
			font-style: normal;
			font-weight: bold;
			line-height: 125%;
			letter-spacing: normal;
			text-align: left;
		}

		h2 {
			color: #202020;
			font-family: Helvetica;
			font-size: 22px;
			font-style: normal;
			font-weight: bold;
			line-height: 125%;
			letter-spacing: normal;
			text-align: left;
		}

		h3 {
			color: #202020;
			font-family: Helvetica;
			font-size: 20px;
			font-style: normal;
			font-weight: bold;
			line-height: 125%;
			letter-spacing: normal;
			text-align: left;
		}

		h4 {
			color: #202020;
			font-family: Helvetica;
			font-size: 18px;
			font-style: normal;
			font-weight: bold;
			line-height: 125%;
			letter-spacing: normal;
			text-align: left;
		}

		.mcnPreviewText {
			display: none !important;
		}

		#templatePreheader {
			background-color: #fafafa;
			background-image: none;
			background-repeat: no-repeat;
			background-position: center;
			background-size: cover;
			border-top: 0;
			border-bottom: 0;
			padding-top: 9px;
			padding-bottom: 9px;
		}

		#templatePreheader .mcnTextContent,
		#templatePreheader .mcnTextContent p {
			color: #656565;
			font-family: Helvetica;
			font-size: 12px;
			line-height: 150%;
			text-align: left;
		}

		#templatePreheader .mcnTextContent a,
		#templatePreheader .mcnTextContent p a {
			color: #656565;
			font-weight: normal;
			text-decoration: none;
		}

		#templateHeader {
			background-color: #ffffff;
			background-image: none;
			background-repeat: no-repeat;
			background-position: center;
			background-size: cover;
			border-top: 0;
			border-bottom: 0;
			padding-top: 0;
			padding-bottom: 0;
		}

		#templateHeader .mcnTextContent,
		#templateHeader .mcnTextContent p {
			color: #202020;
			font-family: Helvetica;
			font-size: 16px;
			line-height: 150%;
			text-align: left;
		}

		#templateHeader .mcnTextContent a,
		#templateHeader .mcnTextContent p a {
			color: #007bff;
			font-weight: normal;
			text-decoration: none;
		}

		#templateBody {
			background-color: #ffffff;
			background-image: none;
			background-repeat: no-repeat;
			background-position: center;
			background-size: cover;
			border-top: 0;
			border-bottom: 0;
			padding-top: 0;
			padding-bottom: 0;
		}

		#templateBody .mcnTextContent,
		#templateBody .mcnTextContent p {
			color: #202020;
			font-family: Helvetica;
			font-size: 16px;
			line-height: 150%;
			text-align: left;
		}

		#templateBody .mcnTextContent a,
		#templateBody .mcnTextContent p a {
			color: #000000;
			font-weight: bold;
			text-decoration: none;
		}

		#templateUpperColumns {
			background-color: #ffffff;
			background-image: none;
			background-repeat: no-repeat;
			background-position: center;
			background-size: cover;
			border-top: 0;
			border-bottom: 0;
			padding-top: 0;
			padding-bottom: 0;
		}

		#templateUpperColumns .columnContainer .mcnTextContent,
		#templateUpperColumns .columnContainer .mcnTextContent p {
			color: #202020;
			font-family: Helvetica;
			font-size: 16px;
			line-height: 150%;
			text-align: left;
		}

		#templateUpperColumns .columnContainer .mcnTextContent a,
		#templateUpperColumns .columnContainer .mcnTextContent p a {
			color: #000000;
			font-weight: bold;
			text-decoration: none;
		}

		.templateLowerColumns {
			background-color: #ffffff;
			background-image: none;
			background-repeat: no-repeat;
			background-position: center;
			background-size: cover;
			border-top: 0;
			/*			border-bottom:2px solid #EAEAEA;*/
			padding-top: 9px;
			padding-bottom: 9px;
		}

		.templateLowerColumns .columnContainer .mcnTextContent,
		.templateLowerColumns .columnContainer .mcnTextContent p {
			color: #202020;
			font-family: Helvetica;
			font-size: 16px;
			line-height: 150%;
			text-align: left;
		}

		.templateLowerColumns .columnContainer .mcnTextContent a,
		.templateLowerColumns .columnContainer .mcnTextContent p a {
			color: #000000;
			font-weight: normal;
			text-decoration: none;
		}

		.templateFooter {
			background-color: #ffffff;
			background-image: none;
			background-repeat: no-repeat;
			background-position: center;
			background-size: cover;
			border-top: 0;
			border-bottom: 0;
			padding-top: 9px;
			padding-bottom: 9px;
		}

		.templateFooter .mcnTextContent,
		.templateFooter .mcnTextContent p {
			color: #ffffff;
			font-family: Helvetica;
			font-size: 14px;
			line-height: 150%;
			text-align: center;
		}

		.templateFooter .mcnTextContent a,
		.templateFooter .mcnTextContent p a {
			color: #ffffff;
			font-size: 12px;
			font-weight: normal;
			text-decoration: underline;
		}

		.small-12-inner {
			padding-left: 20px;
			padding-right: 20px;
		}

		@media only screen and (min-width:768px) {
			.templateContainer {
				width: <?php echo $container_width; ?>px !important;
			}
		}

		@media only screen and (max-width: 480px) {
			.small-12 {
				width: 100% !important;
				max-width: 100% !important;
				display: inline-block !important;
				padding: 20px 0 !important;
			}

			.small-12-inner {
				padding-left: 0 !important;
				padding-right: 0 !important;
			}
		}

		@media only screen and (max-width: 480px) {

			body,
			table,
			td,
			p,
			a,
			li,
			blockquote {
				-webkit-text-size-adjust: none !important;
			}

			body {
				width: 100% !important;
				min-width: 100% !important;
			}

			#bodyCell {
				padding-top: 10px !important;
			}

			.columnWrapper {
				max-width: 100% !important;
				width: 100% !important;
			}

			.mcnImage {
				width: 100% !important;
			}

			.mcnCartContainer,
			.mcnCaptionTopContent,
			.mcnRecContentContainer,
			.mcnCaptionBottomContent,
			.mcnTextContentContainer,
			.mcnBoxedTextContentContainer,
			.mcnImageGroupContentContainer,
			.mcnCaptionLeftTextContentContainer,
			.mcnCaptionRightTextContentContainer,
			.mcnCaptionLeftImageContentContainer,
			.mcnCaptionRightImageContentContainer,
			.mcnImageCardLeftTextContentContainer,
			.mcnImageCardRightTextContentContainer {
				max-width: 100% !important;
				width: 100% !important;
			}

			.mcnBoxedTextContentContainer {
				min-width: 100% !important;
			}

			.mcnImageGroupContent {
				padding: 9px !important;
			}

			.mcnCaptionLeftContentOuter .mcnTextContent,
			.mcnCaptionRightContentOuter .mcnTextContent {
				padding-top: 9px !important;
			}

			.mcnImageCardTopImageContent,
			.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent {
				padding-top: 18px !important;
			}

			.mcnImageCardBottomImageContent {
				padding-bottom: 9px !important;
			}

			.mcnImageGroupBlockInner {
				padding-top: 0 !important;
				padding-bottom: 0 !important;
			}

			.mcnImageGroupBlockOuter {
				padding-top: 9px !important;
				padding-bottom: 9px !important;
			}

			.mcnTextContent,
			.mcnBoxedTextContentColumn {
				padding-right: 18px !important;
				padding-left: 18px !important;
			}

			.mcnImageCardLeftImageContent,
			.mcnImageCardRightImageContent {
				padding-right: 18px !important;
				padding-bottom: 0 !important;
				padding-left: 18px !important;
			}

			.mcpreview-image-uploader {
				display: none !important;
				width: 100% !important;
			}

			h1 {
				font-size: 16px !important;
				line-height: 125% !important;
				font-weight: bold !important;
			}

			h2 {
				font-size: 14px !important;
				line-height: 125% !important;
			}

			h3 {
				font-size: 14px !important;
				line-height: 125% !important;
			}

			h4 {
				font-size: 14px !important;
				line-height: 150% !important;
			}

			.mcnBoxedTextContentContainer .mcnTextContent,
			.mcnBoxedTextContentContainer .mcnTextContent p {
				font-size: 14px !important;
				line-height: 150% !important;
			}

			#templatePreheader {
				display: block !important;
			}

			#templatePreheader .mcnTextContent,
			#templatePreheader .mcnTextContent p {
				font-size: 14px !important;
				line-height: 150% !important;
			}

			#templateHeader .mcnTextContent,
			#templateHeader .mcnTextContent p {
				font-size: 16px !important;
				line-height: 125% !important;
			}

			#templateBody .mcnTextContent,
			#templateBody .mcnTextContent p {
				font-size: 12px !important;
				line-height: 125% !important;
			}

			#templateUpperColumns .columnContainer .mcnTextContent,
			#templateUpperColumns .columnContainer .mcnTextContent p {
				font-size: 12px !important;
				line-height: 125% !important;
			}

			.templateLowerColumns .columnContainer .mcnTextContent,
			.templateLowerColumns .columnContainer .mcnTextContent p {
				font-size: 12px !important;
				line-height: 125% !important;
			}

			.templateFooter .mcnTextContent,
			.templateFooter .mcnTextContent p {
				font-size: 10px !important;
				line-height: 100% !important;
			}
		}

		.list-christian_hull_top_five ul li {
			list-style-type: "★";
			color: #419df2;
			margin-left: 16px;
			padding-left: 16px;
		}
	</style>
</head>

<body>
	<!--*|IF:MC_PREVIEW_TEXT|*-->
	<!--[if !gte mso 9]><!----><span class="mcnPreview Text" style="display:none; font-size:0px; line-height:0px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; visibility:hidden; mso-hide:all;">*|MC_PREVIEW_TEXT|*</span>
	<!--<![endif]-->
	<!--*|END:IF|*-->
	<center>
		<table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
			<tr>
				<td align="center" valign="top" id="bodyCell">
					<!-- BEGIN TEMPLATE // -->
					<!--[if gte mso 9]>
        <table align="center" border="0" cellspacing="0" cellpadding="0" width="<?php echo $container_width; ?>" style="width:<?php echo $container_width; ?>px;">
        <tr>
        <td align="center" valign="top" width="<?php echo $container_width; ?>" style="width:<?php echo $container_width; ?>px;">
        <![endif]-->
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
						<tr>
							<td valign="top" id="templateHeader">
								<table border="0" cellpadding="0" cellspacing="0" width="700" class="mcnImageBlock">
									<tbody class="mcnImageBlockOuter">
										<tr>
											<td valign="top" style="background: #ffffff;" class="mcnImageBlockInner">
												<table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer">
													<tbody>
														<tr>
															<td colspan="2" class="mcnImageContent" valign="top" style="padding-right: 20px; padding-left: 20px; padding-top: 10px; padding-bottom: 0px; text-align:center; background: #ffffff;">
																<a href="<?php echo home_url('observer'); ?>" title="" class="" target="_blank">
																	<?php if (!is_null($logo['url'])) : ?>
																		<img align="center" alt="<?php echo trim(str_ireplace('Observer', '', $list->title)) . ' Observer'; ?>" src="<?php echo $logo['url']; ?>" width="<?php echo $logo['width']; ?>" style="width: <?php echo $logo['width']; ?>px; max-width: 100%; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
																	<?php endif; ?>
																</a>
															</td>
														</tr>
														<tr>
															<td valign="top" align="left" class="mcnTextContent" style="padding-top: 5px; padding-right: 9px; padding-bottom: 0px; padding-left: 20px; color: #777777; font-size: 13px;">
																<!--[if gte mso 9]>
        											<table align="left" border="0" cellspacing="0" cellpadding="0" width="330" style="width:330px;">
        											<tr>
        											<td align="left" valign="top" width="330" style="width:330px;">
        											<![endif]-->
																*|DATE:l, j F Y|*
																<!--[if gte mso 9]>
        											</td>
        											</tr>
        											</table>
        											<![endif]-->
															</td>

															<td valign="middle" align="right" class="mcnTextContent" style="padding-top: 5px; padding-right: 20px; padding-bottom: 0px; padding-left: 9px; color: #777777; text-align: right; font-size: 13px;">
																<!--[if gte mso 9]>
        											<table align="right" border="0" cellspacing="0" cellpadding="0" width="330" style="width:330px;">
        											<tr>
        											<td align="right" valign="top" width="330" style="width:330px;">
        											<![endif]-->
																*|IF:STRENGTH|*
																*|IF:STRENGTH < 100|* Your Profile Strength <img src="https://thebrag.com/wp-content/uploads/edm/profile-strength-bar-*|STRENGTH|*.jpg" alt="*|STRENGTH|*% complete" title="*|STRENGTH|*% complete" style="vertical-align: middle;">
																	<div style="font-size: 11px;"><a target="_blank" href="https://thebrag.com/verify/?oc=*|OC_TOKEN|*&amp;returnTo=https://thebrag.com/profile/" style="color: #007bff;">Boost</a> your profile to receive emails more tailored to you!</a></div>
																	*|END:IF|*
																	*|ELSE:|*
																	Your Profile Strength <img src="https://thebrag.com/wp-content/uploads/edm/profile-strength-bar-0.jpg" alt="0% complete" title="0% complete" style="vertical-align: middle;">
																	<div style="font-size: 11px;"><a target="_blank" href="https://thebrag.com/verify/?oc=*|OC_TOKEN|*&amp;returnTo=https://thebrag.com/profile/" style="color: #007bff;">Boost</a> your profile to receive emails more tailored to you!</a></div>
																	*|END:IF|*
																	<!--[if gte mso 9]>
        											</td>
        											</tr>
        											</table>
        											<![endif]-->
															</td>
														</tr>
														<?php if (isset($newsletter->details->intro_content) && $newsletter->details->intro_content != '') { ?>
															<tr>
																<td colspan="2" valign="top" class="mcnTextContent" style="padding-top: 5px; padding-right: 20px; padding-left: 20px;color:#000000;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;">
																	<table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;" width="660">
																		<tbody>
																			<tr style="padding:0;text-align:center;vertical-align:top">
																				<td style="padding: 15px; border-radius: 20px; background-color: #f3f3f3;">
																					<?php echo wpautop($newsletter->details->intro_content); ?>
																				</td>
																			</tr>
																		</tbody>
																	</table>
																</td>
															</tr>
														<?php } // If intro_content 
														?>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<?php
						if (count($post_ids) <= 6) {
							include __DIR__ . '/../partials/newsletter-template-1-col.php';
						} else {
							// ob_start();
							include __DIR__ . '/../partials/newsletter-template-2-col.php';
							// $t = ob_get_clean();
							// ob_end_flush();
							// echo htmlentities($t);
						}
						?>

						<?php
						$random_lists = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}observer_lists WHERE id != {$list->id} AND status  = 'active' ORDER BY RAND() LIMIT 3");
						if ($random_lists && !isset($newsletter->details->hide_observer_recommendations)) :
						?>
							<tr>
								<td valign="top" class="templateFooter" style="background: #ffffff; text-align: center; padding: 10px 10px;">

									<div style="border-bottom: 1px solid rgb(249, 249, 249); border-radius: 20px; margin-bottom: 7px;">
										<div style="border-bottom: 1px solid rgb(245, 245, 245); border-radius: 19px;">
											<div style="border-right: 1px solid rgb(245, 245, 245); border-bottom: 1px solid rgb(242, 242, 242); border-radius: 18px;">
												<div style="border-right: 1px solid rgb(242, 242, 242); border-bottom: 1px solid rgb(240, 240, 240); border-radius: 17px;">
													<div style="border-right: 1px solid rgb(238, 238, 238); border-bottom: 1px solid rgb(238, 238, 238); border-radius: 16px;">
														<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
															<tbody>
																<tr>
																	<th style="text-align: left; font-weight: 400; font-family: Helvetica, Arial, sans-serif; font-size: 16px; color: rgb(51, 51, 51); display: block; background-color: rgb(255, 255, 255); border-radius: 15px; border: 1px solid rgb(230, 230, 230); border-collapse: collapse;">
																		<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
																			<tbody>
																				<tr>
																					<td style="padding: 15px;">
																						<h3 style="font-family: Helvetica, Arial, sans-serif; font-size: 16px; color: #007bff; font-weight: 700; margin-top: 0px; margin-bottom: 0px;">Did you like the <?php echo trim(str_ireplace(['the', 'Observer'], ['', ''], $list->title)) . ' Observer'; ?>?</h3>
																						<p>If you enjoyed that, perhaps you'll enjoy some of our other Observers:</p>
																					</td>
																				</tr>
																			</tbody>
																		</table>
																		<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
																			<tbody>
																				<tr>
																					<td style="padding: 0px 15px 15px; font-size: 16px;">
																						<table style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;width:100%">
																							<tbody>
																								<tr style="padding:0;text-align:center;vertical-align:top">
																									<?php foreach ($random_lists as $random_list_counter => $random_list) : ?>
																										<td class="small-12.2" style="width: 33%; max-width: 100%; padding: 0 5px;">
																											<a href="<?php echo home_url('verify'); ?>/?oc=*|OC_TOKEN|*&amp;fl=0&amp;returnTo=<?php echo urlencode(home_url('observer/' . $random_list->slug)); ?>" title="<?php echo $random_list->title; ?>" class="" target="_blank" style="color: #000; text-decoration: none; display: block; line-height: 150%;">
																												<img width="205" height="205" src="<?php echo $random_list->image_url; ?>" style="max-width: 100%; border-radius: 9px;"><br>
																												<?php
																												echo !in_array($random_list->id, [4, 48]) ? trim(str_ireplace('Observer', '', $random_list->title)) : trim($random_list->title);
																												?>
																											</a>
																										</td>
																									<?php endforeach; ?>
																								</tr>
																							</tbody>
																						</table>
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</th>
																</tr>
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
						<?php endif; // If $random_lists

						if (!isset($newsletter->details->hide_observer_rewards)) :
						?>
							<tr>
								<td valign="top" class="templateFooter" style="background: #ffffff; text-align: center; padding: 10px 10px;">
									<table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;" width="660">
										<tbody>
											<tr style="padding:0;text-align:center;vertical-align:top">
												<td>
													<div style="border-bottom: 1px solid rgb(249, 249, 249); border-radius: 20px; margin-bottom: 7px;">
														<div style="border-bottom: 1px solid rgb(245, 245, 245); border-radius: 19px;">
															<div style="border-right: 1px solid rgb(245, 245, 245); border-bottom: 1px solid rgb(242, 242, 242); border-radius: 18px;">
																<div style="border-right: 1px solid rgb(242, 242, 242); border-bottom: 1px solid rgb(240, 240, 240); border-radius: 17px;">
																	<div style="border-right: 1px solid rgb(238, 238, 238); border-bottom: 1px solid rgb(238, 238, 238); border-radius: 16px;">
																		<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
																			<tbody>
																				<tr>
																					<th style="text-align: left; font-weight: 400; font-family: Helvetica, Arial, sans-serif; font-size: 16px; color: rgb(51, 51, 51); display: block; background-color: rgb(255, 255, 255); border-radius: 15px; border: 1px solid rgb(230, 230, 230); border-collapse: collapse;">
																						<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
																							<tbody>
																								<tr>
																									<td style="padding: 15px;">
																										<h3 style="font-family: Helvetica, Arial, sans-serif; font-size: 16px; color: #007bff; font-weight: 700; margin-top: 0px; margin-bottom: 0px;">Share The Observer for free stuff!</h3>
																									</td>
																								</tr>
																							</tbody>
																						</table>
																						<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
																							<tbody>
																								<tr>
																									<td style="padding: 0px 15px 15px; font-size: 16px;">

																										*|IF:REFERRALS|*

																										*|IF:REFERRALS < 3|* <p style="margin-top: 15px; margin-bottom: 15px; line-height: 22px; font-size: 16px;">
																											When you share The Brag Observer with your network, you earn free magazine's, merch and more! You only need 3 referrals to get 10% OFF Magazine Subscription.
																											</p>

																											*|ELSEIF:REFERRALS < 10|* <p style="margin-top: 15px; margin-bottom: 15px; line-height: 22px; font-size: 16px;">When you share The Brag Observer with your network, you earn free stuff. You only need 10 referrals to get the next issue of Rolling Stone Australia Magazine.
																												<br><br>
																												Are you one of those people who likes to stay in the know? Then you probably need Rolling Stone in your life, it's the premier music &amp; entertainment magazine in Australia.</p>

																												*|ELSEIF:REFERRALS < 20|* <p style="margin-top: 15px; margin-bottom: 15px; line-height: 22px; font-size: 16px;">When you share The Brag Observer with your network, you earn free stuff. You only need 20 referrals to win a FREE annual subscription to Rolling Stone Australia Magazine.</p>

																													*|ELSEIF:REFERRALS < 30|* <p style="margin-top: 15px; margin-bottom: 15px; line-height: 22px; font-size: 16px;">When you share The Brag Observer with your network, you earn free swag like our Rolling Stone Beach Towel.
																														<br><br>
																														It's Australia, so heading to the beach is always an option. Might as well be with this bad boy with the Rolling Stone logo plastered across it.</p>

																														*|ELSEIF:REFERRALS < 50|* <p style="margin-top: 15px; margin-bottom: 15px; line-height: 22px; font-size: 16px;">When you share The Brag Observer with your network, you earn free swag like our Rolling Stone Bathrobe.
																															<br><br>
																															Are you one of those people who like to luxe it up when you stay in? Then you probably need a bathrobe. Might as well be this bad boy with the Rolling Stone logo plastered across it.</p>

																															*|END:IF|*

																															*|ELSE:|*
																															<p style="margin-top: 15px; margin-bottom: 15px; line-height: 22px; font-size: 16px;">When you share The Brag Observer with your network, you earn free magazine's, merch and more! You only need 3 referrals to get 10% OFF Magazine Subscription.</p>

																															*|END:IF|*

																															<p style="text-align: center;"><img width="660" src="https://cdn.thebrag.com/observer/images/refer-a-friend/ReferAfriend_600px.gif" style="max-width: 100%; height: auto;" alt="Share The Observer for free stuff!" title="Share The Observer for free stuff!"></p>

																															<p style="margin-top: 15px; margin-bottom: 15px; line-height: 22px; font-size: 16px;">Hit the button below to learn more and access your rewards hub.</p>
																															<a href="https://thebrag.com/verify/?oc=*|OC_TOKEN|*&amp;returnTo=https://thebrag.com/refer-a-friend/" style="width: 100%; max-width: 150px; font-family: Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; font-weight: 900; text-decoration: none; text-align: center; background-color: #000000; padding: 15px; border-radius: 4px; display: block; margin: inherit;" target="_blank">Click to Share</a>

																															*|IF:REFER_CODE|*
																															<p style="margin-top: 15px; margin-bottom: 15px; line-height: 22px;font-size: 16px; word-break: break-word; -ms-hyphens: auto; -moz-hyphens: auto; -webkit-hyphens: auto; hyphens: auto;">Or copy &amp; paste your referral link to others:
																																<br>
																																<?php $list_referrer_link = 'https://thebrag.com/refer/?rc=*|REFER_CODE|*&l=' . $list->id; ?>
																																<a href="<?php echo $list_referrer_link; ?>" style="border-bottom: 2px solid #007bff; text-decoration: none; color: #262626;" target="_blank"><?php echo $list_referrer_link; ?></a>
																															</p>
																															*|END:IF|*
																									</td>
																								</tr>
																							</tbody>
																						</table>
																					</th>
																				</tr>
																			</tbody>
																		</table>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						<?php endif; // If not hiding Observer rewards (share)
						?>

						<tr>
							<td valign="top" class="templateFooter" style="background: #ffffff; text-align: center; padding: 10px 0 10px 0;">

								<div style="padding-top: 30px; padding-bottom: 30px;">
									<a title="Advertise with us" href="https://thebrag.com/media/" target="_blank" style="text-decoration: none;">
										<img alt="The Brag Media" src="<?php echo $media_logo; ?>" width="300" style="width: 300px; max-width:100%; padding-bottom: 0; display: inline !important; vertical-align: bottom;">
									</a>
								</div>

								<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
									<tbody class="mcnTextBlockOuter">
										<tr>
											<td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
												<table align="center" border="0" cellpadding="0" cellspacing="0" style="max-width:100%;" width="100%" class="mcnTextContentContainer">
													<tbody>
														<tr>
															<td valign="top" class="mcnTextContent" style="padding-top: 0px; padding-bottom: 30px; text-align: center;">
																<?php $this->print_social_icons(); ?>
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

					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">

						<tr>
							<td valign="top" class="templateFooter" align="center" style="text-align: center; padding: 5px; background: #333333; font-size: 10px; color: #ffffff;">
								<div class="mcnTextContent">
									<br>
									Copyright &copy; <?php echo date('Y'); ?><br>
									*|HTML:LIST_ADDRESS_HTML|*
									<br>
								</div>
							</td>
						</tr>

						<tr>
							<td valign="top" class="templateFooter" style="padding-top:0; padding: 10px 0; color: #dedede; text-align: center; font-size: 10px; background: #333333; ">
								<div class="mcnTextContent">
									<a target="_blank" href="https://thebrag.com/verify/?a=unsub&oc=*|OC_TOKEN|*" style="color: #aaaaaa !important;text-decoration: none;font-size: 10px !important;">Unsubscribe</a>
									&nbsp;
									<span style="font-size: 10px !important; color: #666666;">|</span>
									&nbsp;
									<a target="_blank" title="Advertise with us" href="https://thebrag.com/media/" target="_blank" style="color: #aaaaaa; text-decoration: none;font-size: 10px !important;">Advertise with us</a>
									&nbsp;
									<span style="font-size: 10px !important; color: #666666;">|</span>
									&nbsp;
									<a target="_blank" title="Contact us" href="mailto:observer@thebrag.media" target="_blank" style="color: #aaaaaa; text-decoration: none;font-size: 10px !important;">Contact us</a>
								</div>
							</td>
						</tr>

						<tr>
							<td valign="top" class="templateFooter" align="center" style="text-align: right; padding: 0; background: #333333; font-size: 10px; color: #ffffff;">
								<a href="*|UNSUB|*" style="color: #666666 !important;text-decoration: none;">.</a>
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

function print_video_record_of_week($obj, $newsletter)
{
	if (isset($newsletter->details->hide_video_record))
		return;
	$featured_video = get_option('tbm_featured_video');
	$featured_video_link = get_option('tbm_featured_video_link');
	// $featured_video_img_src = 'https://i.ytimg.com/vi/' . $featured_video . '/0.jpg';
	if (!$featured_video_link || '' == $featured_video_link) {
		$featured_video_link = $featured_video;
	} {
		$tbm_featured_video_link_html = file_get_contents($featured_video_link);
		$tbm_featured_video_link_html_dom = new DOMDocument();
		@$tbm_featured_video_link_html_dom->loadHTML($tbm_featured_video_link_html);
		// $meta_og_img_tbm_featured_video_link = null;
		foreach ($tbm_featured_video_link_html_dom->getElementsByTagName('meta') as $meta) {
			if ($meta->getAttribute('property') == 'og:image') {
				$featured_video_img_src = $meta->getAttribute('content');
				$featured_video_img_src = str_ireplace('/img-socl/?url=', '', substr($featured_video_img_src, strpos($featured_video_img_src, '/img-socl/?url=')));
				break;
			}
		}
	}
	if (!is_null($featured_video) && $featured_video != '') :
		parse_str(parse_url($featured_video, PHP_URL_QUERY), $featured_video_vars);
		// $featured_yt_vid_id = $featured_video_vars['v'];
		$featured_video_alt = '';
		if (get_option('tbm_featured_video_artist')) {
			$featured_video_alt .= esc_html(stripslashes(get_option('tbm_featured_video_artist')));
		}
		if (get_option('tbm_featured_video_song')) {
			$featured_video_alt .= ' - \'' . esc_html(stripslashes(get_option('tbm_featured_video_song'))) . '\'';
		}

		$featured_video_img =  $obj->resize_image($featured_video_img_src, 660, 370, null, '/edm/featured/', 'featured-vid-' . date('Y\wW') . '-n.jpg');
?>
		<tr>
			<td style="background-color:#ffffff;">
				<!--[if gte mso 9 ]>
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="350" style="max-width:350px">
            <tr>
            <td align="center" valign="top" width="350" style="max-width:350px">
            <![endif]-->
				<table align="left" class="small-12" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;width:100%;max-width:340px;margin-top:10px;margin-bottom:10px;margin-left: 5px; margin-right: 5px;">
					<tbody>
						<tr style="padding:0;text-align:center;vertical-align:top">
							<th style="color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:0px;padding-left:0px;padding-right:0px;padding-top:0px;text-align:center;">
								<table bgcolor="#000000" border="0" cellpadding="0" cellspacing="0" style="width:100%; border:none!important;background-color: #000000;">
									<tr>
										<td class="mcnTextContent" valign="top" style="padding: 9px 9px 9px 9px;color: #ffffff;font-family: Helvetica;font-size: 14px;font-style: normal;font-weight: normal;line-height: 150%;text-align: center;" width="546">
											<h1 class="null" style="text-align: center; padding: 0; margin: 0;">
												<font color="#ffffff" size="4">VIDEO OF THE WEEK</font>
											</h1>
										</td>
									</tr>
									<tr>
										<td colspan="2" style="display:table-cell!important; line-height:0!important; height:auto!important;">
											<a target="_blank" style="display: block;" href="<?php echo $featured_video_link; ?>" rel="nofollow">
												<img src="<?php echo $featured_video_img; ?>?v=<?php echo time(); ?>" alt="<?php echo $featured_video_alt; ?>" title="<?php echo $featured_video_alt; ?>" border="0" style="width:100%">
											</a>
										</td>
									</tr>
									<tr>
										<td class="mcnTextContent" valign="top" style="padding: 9px 9px 9px 9px;color: #ffffff;font-family: Helvetica;font-size: 12px;font-style: normal;font-weight: normal;line-height: 150%;text-align: center;height:110px;" width="546">
											<h1 class="null" style="text-align: center; padding: 0; margin: 0;">
												<font color="#ffffff" size="4"><?php if (get_option('tbm_featured_video_artist')) {
																					echo '' . esc_html(stripslashes(get_option('tbm_featured_video_artist')));
																				}
																				if (get_option('tbm_featured_video_song')) {
																					echo '<br><em>\'' . esc_html(stripslashes(get_option('tbm_featured_video_song'))) . '\'</em>';
																				} ?></font>
											</h1>
										</td>
									</tr>
								</table>
							</th>
						</tr>
					</tbody>
				</table>
				<!--[if gte mso 9 ]>
            </td>
					</tr>
				</table>
				<table align="center" border="0" cellspacing="0" cellpadding="0" width="350" style="max-width:350px">
				<tr>
				<td align="center" valign="top" width="350" style="max-width:350px">
            <![endif]-->
			<?php
		endif; // If Featured Video is available

		$rotw_response = wp_remote_get('https://dontboreus.thebrag.com/wp-json/tbm_dbu/v1/rotw?v=' . date('Ymd'));
		$featured_record_alt = '';
		if (is_array($rotw_response) && !is_wp_error($rotw_response)) {
			$rotw = json_decode($rotw_response['body']);
			$featured_record_alt .= esc_html(stripslashes($rotw->artist));
			$featured_record_alt .= ' - ' . esc_html(stripslashes($rotw->name));
			$featured_record_img =  $obj->resize_image($rotw->image, 660, 370, null, '/edm/featured/', 'featured-record-' . date('Y\wW') . '.jpg');
		}
		/* $featured_record_alt .= esc_html(stripslashes(get_option('tbm_featured_album_artist')));
		$featured_record_alt .= ' - ' . esc_html(stripslashes(get_option('tbm_featured_album_title')));
		$featured_record_img =  $obj->resize_image(get_option('tbm_featured_album_image_url'), 660, 370, null, '/edm/featured/', 'featured-record-' . date('Y\wW') . '.jpg'); */
			?>
			<table align="left" class="small-12" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;width:100%;max-width:340px;margin-top:10px;margin-bottom:10px;margin-left: 5px; margin-right: 5px;">
				<tbody>
					<tr style="padding:0;text-align:center;vertical-align:top">
						<th style="Margin:0 auto;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.4;margin:0 auto;padding:0;padding-bottom:0px;padding-left:0px;padding-right:0px;padding-top:0px;text-align:center;">
							<table bgcolor="#000000" border="0" cellpadding="0" cellspacing="0" style="width:100%; border:none!important;background-color: #000000;">
								<tr>
									<td class="mcnTextContent" valign="top" style="padding: 9px 9px 9px 9px;color: #ffffff;font-family: Helvetica;font-size: 14px;font-style: normal;font-weight: normal;line-height: 150%;text-align: center;" width="546">
										<h1 class="null" style="text-align: center; padding: 0; margin: 0;">
											<font color="#ffffff" size="4">RECORD OF THE WEEK</font>
										</h1>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="display:table-cell!important; line-height:0!important; height:auto!important;">
										<a target="_blank" style="display: block;" href="<?php echo $rotw->link; ?>" rel="nofollow">
											<img src="<?php echo $featured_record_img; ?>?v=<?php echo time(); ?>" alt="<?php echo $featured_record_alt; ?>" title="<?php echo $featured_record_alt; ?>" border="0" style="width:100%">
										</a>
									</td>
								</tr>
								<tr>
									<td class="mcnTextContent" valign="top" style="padding: 9px 9px 9px 9px;color: #ffffff;font-family: Helvetica;font-size: 12px;font-style: normal;font-weight: normal;line-height: 150%;text-align: center;height:110px;" width="546">
										<h1 class="null" style="text-align: center; padding: 0; margin: 0;">
											<font color="#ffffff" size="4"><?php
																			if ($rotw->artist) {
																				echo '' . esc_html(stripslashes($rotw->artist));
																			}
																			if ($rotw->name) {
																				echo '<br><em>\'' . esc_html(stripslashes($rotw->name)) . '\'</em>';
																			}
																			?></font>
										</h1>
									</td>
								</tr>
							</table>
						</th>
					</tr>
				</tbody>
			</table>
			<!--[if gte mso 9 ]>
						</td>
            </tr>
            </table>
            <![endif]-->
			</td>
		</tr>
		<!-- END Featured Video / Audio -->
		<?php
	}

	function print_tio_tweet($newsletter = null)
	{
		if (is_null($newsletter))
			return;
		if (isset($newsletter->details->hide_top_industry_tweet))
			return;
		// If Top Industry Tweet is added
		if (isset($newsletter->details->top_i_tweet_image) && $newsletter->details->top_i_tweet_image != '') {
			if (isset($newsletter->details->top_i_tweet_link) && $newsletter->details->top_i_tweet_link != '') {
				$top_i_tweet_link = $newsletter->details->top_i_tweet_link;
			} else {
				$top_i_tweet_link = 'https://theindustryobserver.thebrag.com/';
			}
		?>

			<tr>
				<td style="padding-top:20px;border-bottom:2px solid #EAEAEA;padding-bottom:20px;background-color:#ffffff;">
					<table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;" width="700">
						<tbody>
							<tr style="padding:0;text-align:center;vertical-align:top">
								<td>
									<!--[if gte mso 9]>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="580" style="width:580px;">
<tr>
<td align="center" valign="top" width="580" style="width:580px;">
<![endif]-->
									<table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;" width="580">
										<tbody>
											<tr style="padding:0;text-align:center;vertical-align:top">
												<td style="border: 3px solid #3298d3; width: 580px;">
													<a href="<?php echo $top_i_tweet_link; ?>" target="_blank" style="color: #231f20;text-decoration: none;">
														<img align="none" class="" width="580" alt="Top Industry Post" src="<?php echo content_url(); ?>/uploads/edm/Top-Industry-Post.jpg" style="outline: none;-ms-interpolation-mode: bicubic;max-width: 100%;border: none; width: auto; max-width: 580px;">
													</a>
													<br>
													<a href="<?php echo $top_i_tweet_link; ?>" target="_blank" style="color: #231f20;text-decoration: none;">
														<img align="none" class="" width="580" alt="#" src="<?php echo $newsletter->details->top_i_tweet_image; ?>" style="outline: none;-ms-interpolation-mode: bicubic;max-width: 100%;border: none; width: auto; max-width: 580px;">
													</a>
												</td>
											</tr>
										</tbody>
									</table>
									<!--[if gte mso 9]>
    </td></tr></table><![endif]-->
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		<?php }
	} // print_tio_tweet()

	function print_tio_birthday_shoutout($newsletter = null)
	{
		if (is_null($newsletter))
			return;
		if (isset($newsletter->details->hide_birthday_shoutout))
			return;
		// If Top Birthday Shoutout is added
		if (isset($newsletter->details->birthday_shoutout_image) && $newsletter->details->birthday_shoutout_image != '') {
			if (isset($newsletter->details->birthday_shoutout_link) && $newsletter->details->birthday_shoutout_link != '') {
				$birthday_shoutout_link = $newsletter->details->birthday_shoutout_link;
			} else {
				$birthday_shoutout_link = 'https://theindustryobserver.thebrag.com/';
			}
		?>
			<tr>
				<td style="padding-top:20px;border-bottom:2px solid #EAEAEA;padding-bottom:20px;background-color:#ffffff;">
					<table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;" width="700">
						<tbody>
							<tr style="padding:0;text-align:center;vertical-align:top">
								<td>
									<!--[if gte mso 9]>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="580" style="width:580px;">
<tr>
<td align="center" valign="top" width="580" style="width:580px;">
<![endif]-->
									<table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;" width="580">
										<tbody>
											<tr style="padding:0;text-align:center;vertical-align:top">
												<td style="border: 3px solid #3298d3; width: 580px;">
													<a href="<?php echo $birthday_shoutout_link; ?>" target="_blank" style="color: #231f20;text-decoration: none;">
														<img align="none" class="" width="580" alt="Top Industry Tweet" src="<?php echo content_url(); ?>/uploads/edm/Birthday-Shout-Out.jpg" style="outline: none;-ms-interpolation-mode: bicubic;max-width: 580px;border: none; width: auto;">
													</a>
													<br>
													<div style="text-align: center;">
														<a href="<?php echo $birthday_shoutout_link; ?>" target="_blank" style="color: #231f20;text-decoration: none;">
															<img align="none" class="" width="580" alt="#" src="<?php echo $newsletter->details->birthday_shoutout_image; ?>" style="outline: none;-ms-interpolation-mode: bicubic;max-width: 580px;border: none; width: auto;">
														</a>
														<?php if (isset($newsletter->details->birthday_shoutout_blurb) && '' != $newsletter->details->birthday_shoutout_blurb) { ?>
															<p style="text-decoration: none; font-weight: bold; color: #231f20; font-size: 16px;"><?php echo $newsletter->details->birthday_shoutout_blurb; ?></p>
														<?php } ?>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
									<!--[if gte mso 9]>
    </td></tr></table><![endif]-->
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		<?php }
	} // print_tio_birthday_shoutout()

	function print_christian_hull_top_five($newsletter = null)
	{
		if (is_null($newsletter))
			return;
		// If Christian Hull's Top Five is added
		if (isset($newsletter->details->christian_hull_top_five) && $newsletter->details->christian_hull_top_five != '') {
		?>
			<!--[if gte mso 9]>
<table align="center" border="0" cellspacing="0" cellpadding="0" width="580" style="width:580px;">
<tr>
<td align="center" valign="top" width="580" style="width:580px;">
<![endif]-->
			<div style="border: 3px solid #419df2; width: 550px; margin: auto; padding: 12px;">
				<img align="none" class="" width="550" alt="Christian Hull's Top Five" src="<?php echo content_url(); ?>/uploads/edm/ChristianHullTopFive.jpg" style="outline: none;-ms-interpolation-mode: bicubic;max-width: 100%;border: none; width: auto;">
				<br>
				<div>
					<div style="text-decoration: none; font-weight: bold; color: #231f20; font-size: 16px; padding: 5px 12px 0 12px;" class="list-christian_hull_top_five">
						<?php echo $newsletter->details->christian_hull_top_five; ?>
					</div>
				</div>
			</div>
			<!--[if gte mso 9]>
    </td></tr></table><![endif]-->
		<?php }
	} // print_tio_birthday_shoutout()

	function print_jobs_tio($newsletter)
	{
		if (isset($newsletter->details->hide_jobs))
			return;
		?>
		<tr>
			<td style="padding-top:20px;padding-bottom:20px;background-color:#ffffff;">
				<table align="center" style="background:#fff;border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:center;vertical-align:top;" width="700">
					<tbody>
						<tr style="padding:0;text-align:center;vertical-align:top">
							<td>
								<?php
								// Articles from TheBrag.com
								$ch_brag_jobs = curl_init();
								curl_setopt($ch_brag_jobs, CURLOPT_URL, "https://thebrag.com/jobs/wp-json/api/v1/jobs?keywords=music&industry=music&size=10");
								curl_setopt($ch_brag_jobs, CURLOPT_RETURNTRANSFER, 1);
								$output_brag_jobs = curl_exec($ch_brag_jobs);
								curl_close($ch_brag_jobs);
								$jobs = json_decode($output_brag_jobs);
								if (count($jobs) > 0) :
									$counter_jobs = 0;
								?>
									<h1 style="text-align: center;color: #333333;font-family: 'Helvetica', 'Arial', sans-serif; margin: 10px auto; font-weight: bold; word-break: normal; font-size: 26px;">BEST JOBS IN MUSIC</h1>
									<?php foreach ($jobs as $key => $job) :
										if (is_null($job->image)) :
											continue;
										endif;
										$counter_jobs++;
										if ($counter_jobs > 4) :
											break;
										endif;
									?>
										<table style="width: 100%;  max-width: 100%; <?php echo $key < (count($jobs) - 1) ? ' border-bottom: 1px solid #dddddd;' : ''; ?>" align="center" width="600">

											<tr>
												<td class="td-block" style="padding: 10px; width: 160px; text-align: center; vertical-align: middle;">
													<?php if (!is_null($job->image)) : ?>
														<a href="<?php echo $job->link; ?>" style="text-decoration: none; font-weight: bold; color: #231f20;">
															<img src="<?php echo $job->image; ?>" alt="<?php echo $job->title; ?>" width="150" style="max-width: 150px; outline: 0; border: 0;">
														</a>
													<?php endif; ?>
												</td>
												<td align="left" class="td-block" style="padding: 10px; width: 400px; vertical-align: middle;">
													<h5 style="font-family: Helvetica, Arial, sans-serif; margin: 0 0 5px 0; font-weight:900; font-size: 17px;vertical-align: top;">
														<?php if (isset($job->link) && $job->link != '') : ?>
															<a href="<?php echo $job->link; ?>" style="text-decoration: none; font-weight: bold; color: #231f20;">
																<?php echo $job->title; ?>
															</a>
														<?php else : ?>
															<?php echo $job->title; ?>
														<?php endif; ?>
													</h5>
													<p style="font-family: Helvetica, Arial, sans-serif;font-weight: normal; font-size:14px; line-height:1.4; margin: 0;">
														<?php echo $job->description; ?>
													</p>
												</td>
											</tr>

										</table>
									<?php endforeach; // For Each Job 
									?>
								<?php endif; // If there are jobs 
								?>
								<table style="width: 100%; max-width: 100%; border-bottom: 1px solid #dddddd;" align="center" width="600">
									<tr>
										<td>
											<p align="center" style="text-align: center;margin-top: 20px;margin-bottom: 10px;color: #333333;font-family: 'Helvetica', 'Arial', sans-serif;font-weight: normal;padding: 0;line-height: 19px;font-size: 14px;">
												<a href="https://thebrag.com/jobs/" style="color: #231f20 !important;display: inline-block;width: auto !important;text-align: center;border: 1px solid #231f20;padding: 8px 20px;font-size: 16px;font-weight: bold;border-radius: 6px;text-decoration: none;">
													MORE JOBS
												</a><br>
												<br>
											</p>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	<?php
	}

	function get_pub_logo($url = '')
	{
		return get_pub_logo_new($url);
		if ('' == $url)
			return null;

		$pub_logo = null;

		$pubs_base_url = 'https://images.thebrag.com/common/pubs/';

		$parse = parse_url($url);

		$parsed_host = $parse['host'];
		if (substr($parsed_host, 0, 4) == 'www.') {
			$parsed_host = substr($parsed_host, 4); // str_replace('www.', '', $parse['host']);
		}
		switch (strtolower($parsed_host)):
			case 'theindustryobserver.thebrag.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'The-Industry-Observer-n.jpg',
						'title' => 'The Industry Observer',
						'width' => 75,
						'height' => 25,
					];
				break;
			case 'thebrag.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'The-Brag-n.jpg',
						'title' => 'The Brag',
						'width' => 139,
						'height' => 25,
					];
				break;
			case 'tonedeaf.thebrag.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'Tone-Deaf-n.jpg',
						'title' => 'Tone Deaf',
						'width' => 45,
						'height' => 25,
					];
				break;
			case 'au.rollingstone.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'Rolling-Stone-Australia.jpg',
						'title' => 'Rolling Stone Australia',
						'width' => 135,
						'height' => 25,
					];
				break;
			case 'www.rollingstone.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'Rolling-Stone.jpg',
						'title' => 'Rolling Stone Australia',
						'width' => 135,
						'height' => 25,
					];
				break;
			case 'dontboreus.thebrag.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'Dont-Bore-Us.jpg',
						'title' => 'Don\'t Bore Us',
						'width' => 175,
						'height' => 25,
					];
				break;
			case 'variety.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'Variety.jpg',
						'title' => 'Variety',
						'width' => 84,
						'height' => 25,
					];
				break;
			case 'artnews.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ARTnews.jpg',
						'title' => 'ARTnews',
						'width' => 143,
						'height' => 25,
					];
				break;
			case 'bgr.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'bgr.jpg',
						'title' => 'BGR',
						'width' => 62,
						'height' => 25,
					];
				break;
			case 'billboard.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'billboard.jpg',
						'title' => 'billboard',
						'width' => 104,
						'height' => 25,
					];
				break;
			case 'deadline.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'DEADLINE.jpg',
						'title' => 'DEADLINE',
						'width' => 173,
						'height' => 25,
					];
				break;
			case 'dirt.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'Dirt.jpg',
						'title' => 'dirt',
						'width' => 54,
						'height' => 25,
					];
				break;
			case 'footwearnews.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'FootwearNews.jpg',
						'title' => 'Footwear News',
						'width' => 43,
						'height' => 25,
					];
				break;
			case 'goldderby.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'GoldDerby.jpg',
						'title' => 'Gold Derby',
						'width' => 72,
						'height' => 25,
					];
				break;
			case 'indiewire.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'IndieWire.jpg',
						'title' => 'IndieWire',
						'width' => 129,
						'height' => 25,
					];
				break;
			case 'sheknows.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'SheKnows.jpg',
						'title' => 'SheKnows',
						'width' => 125,
						'height' => 25,
					];
				break;
			case 'sourcingjournal.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'SourcingJournal.jpg',
						'title' => 'Sourcing Journal',
						'width' => 79,
						'height' => 25,
					];
				break;
			case 'sportico.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'Sportico.jpg',
						'title' => 'Sportico',
						'width' => 98,
						'height' => 25,
					];
				break;
			case 'spy.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'Spy.jpg',
						'title' => 'Spy',
						'width' => 45,
						'height' => 25,
					];
				break;
			case 'stylecaster.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'Stylecaster.jpg',
						'title' => 'Stylecaster',
						'width' => 210,
						'height' => 25,
					];
				break;
			case 'hollywoodreporter.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'The-Hollywood-Reporter.jpg',
						'title' => 'The Hollywood Reporter',
						'width' => 100,
						'height' => 25,
					];
				break;
			case 'tvline.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'TVLine.jpg',
						'title' => 'TVLine',
						'width' => 102,
						'height' => 25,
					];
				break;
			case 'vibe.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'Vibe.jpg',
						'title' => 'Vibe',
						'width' => 90,
						'height' => 25,
					];
				break;
			default:
				$pub_logo = NULL;
				break;
		endswitch;

		return $pub_logo;
	}

	function get_pub_logo_new($url = '')
	{
		if ('' == $url)
			return null;

		$pub_logo = null;

		$pubs_base_url = 'https://images.thebrag.com/common/pubs/';

		$parse = parse_url($url);

		$parsed_host = $parse['host'];
		if (substr($parsed_host, 0, 4) == 'www.') {
			$parsed_host = substr($parsed_host, 4); // str_replace('www.', '', $parse['host']);
		}
		switch (strtolower($parsed_host)):
			case 'theindustryobserver.thebrag.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_The-Industry-Observer-32x32.png',
						'title' => 'The Industry Observer',
					];
				break;
			case 'themusicnetwork.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_The-Music-Network-32x32.png',
						'title' => 'The Music Network',
					];
				break;
			case 'thebrag.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_The-Brag-32x32.png',
						'title' => 'The Brag',
					];
				break;
			case 'tonedeaf.thebrag.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_Tone-Deaf-32x32.png',
						'title' => 'Tone Deaf',
					];
				break;
			case 'au.rollingstone.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_Rolling-Stone-32x32.png',
						'title' => 'Rolling Stone Australia',
					];
				break;
			case 'rollingstone.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_Rolling-Stone-32x32.png',
						'title' => 'Rolling Stone',
					];
				break;
			case 'dontboreus.thebrag.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_Dont-Bore-Us-32x32.png',
						'title' => 'Don\'t Bore Us',
					];
				break;
			case 'variety.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_Variety-32x32.png',
						'title' => 'Variety',
					];
				break;
			case 'artnews.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_ARTnews-32x32.png',
						'title' => 'ARTnews',
					];
				break;
			case 'bgr.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_bgr-32x32.png',
						'title' => 'BGR',
					];
				break;
			case 'billboard.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_billboard-32x32.png',
						'title' => 'billboard',
					];
				break;
			case 'deadline.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_DEADLINE-32x32.png',
						'title' => 'DEADLINE',
					];
				break;
			case 'dirt.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_Dirt-32x32.png',
						'title' => 'dirt',
					];
				break;
			case 'footwearnews.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_FootwearNews-32x32.png',
						'title' => 'Footwear News',
					];
				break;
			case 'goldderby.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_GoldDerby-32x32.png',
						'title' => 'Gold Derby',
					];
				break;
			case 'indiewire.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_IndieWire-32x32.png',
						'title' => 'IndieWire',
					];
				break;
			case 'sheknows.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_SheKnows-32x32.png',
						'title' => 'SheKnows',
					];
				break;
			case 'sourcingjournal.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_SourcingJournal-32x32.png',
						'title' => 'Sourcing Journal',
					];
				break;
			case 'sportico.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_Sportico-32x32.png',
						'title' => 'Sportico',
					];
				break;
			case 'spy.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_Spy-32x32.png',
						'title' => 'Spy',
					];
				break;
			case 'stylecaster.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_Stylecaster-32x32.png',
						'title' => 'Stylecaster',
					];
				break;
			case 'hollywoodreporter.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_The-Hollywood-Reporter-32x32.png',
						'title' => 'The Hollywood Reporter',
					];
				break;
			case 'tvline.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_TVLine-32x32.png',
						'title' => 'TVLine',
					];
				break;
			case 'vibe.com':
				$pub_logo =
					[
						'url' => $pubs_base_url .= 'ico_Vibe-32x32.png',
						'title' => 'Vibe',
					];
				break;
			default:
				$pub_logo = NULL;
				break;
		endswitch;

		$pub_logo['width'] = $pub_logo['height'] = 32;

		return $pub_logo;
	}
