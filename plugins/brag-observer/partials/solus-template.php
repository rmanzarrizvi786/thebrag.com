<?php
$id = isset($_GET['id']) ? absint($_GET['id']) : get_query_var('newsletter_id', null);
if (is_null($id)) :
	return;
endif;

$solus = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_solus WHERE id = {$id}");
if (is_null($solus)) :
	return;
endif;
$solus->details = json_decode($solus->details);
foreach ($solus->details as $k => $v) {
	if (is_object($v)) {
		$v = (array) $v;
		// $v = array_values( $v );
		$solus->details->{$k} = $v;
	}
}
$lists = $wpdb->get_results("SELECT * FROM {$wpdb->base_prefix}observer_lists WHERE id IN( {$solus->lists} )");

$list_id = isset($_GET['list_id']) ? absint($_GET['list_id']) : null;
if (!is_null($list_id)) {
	$list = $wpdb->get_row("SELECT * FROM {$wpdb->base_prefix}observer_lists WHERE id = '{$list_id}' LIMIT 1");
}

$logo = [];
if (isset($list)) {
	$logo['url'] = $list && $list->email_header_image_url && '' != $list->email_header_image_url ? $list->email_header_image_url : 'https://thebrag.com/wp-content/themes/tbm-brag/images/TheBragLOGOblackNOSHIELD.png';
	$logo['width'] = $list && $list->email_header_image_url && '' != $list->email_header_image_url ? 600 : 300;
} else {
	foreach ($lists as $list) {
		$logo['url'] = $list && $list->email_header_image_url && '' != $list->email_header_image_url ? $list->email_header_image_url : 'https://thebrag.com/wp-content/themes/tbm-brag/images/TheBragLOGOblackNOSHIELD.png';
		$logo['width'] = $list && $list->email_header_image_url && '' != $list->email_header_image_url ? 600 : 300;
	}
}

$container_width = 600;
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
	<title><?php echo $solus->details->subject; ?></title>

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
			color: #000000;
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

		@media only screen and (max-width: 728px) {
			.small-12 {
				width: <?php echo $container_width - 60; ?>px !important;
				display: inline-block !important;
				padding: 20px !important;
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
	</style>
</head>

<body>
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
								<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width:100%;">
									<tbody class="mcnImageBlockOuter">
										<tr>
											<td valign="top" style="background: #ffffff;" class="mcnImageBlockInner">
												<table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
													<tbody>
														<tr>
															<td class="mcnImageContent" valign="top" style="padding-right: 20px; padding-left: 20px; padding-top: 10px; padding-bottom: 0px; text-align:center; background: #ffffff;">
																<a href="<?php echo home_url('observer'); ?>" title="" class="" target="_blank">
																	<?php if (!is_null($logo['url'])) : ?>
																		<img align="center" alt="<?php echo trim(str_ireplace('Observer', '', $list->title)) . ' Observer'; ?>" src="<?php echo $logo['url']; ?>" width="<?php echo $logo['width']; ?>" style="width: <?php echo $logo['width']; ?>px; max-width: 100%; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
																	<?php endif; ?>
																</a>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>

								<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
									<tbody>
										<tr>
											<td valign="top" class="mcnTextContent" style="padding-top: 15px; padding-right: 20px; padding-bottom: 15px; padding-left: 20px;">
												<a href="<?php echo $solus->solus_link; ?>" target="_blank"><img src="<?php echo $solus->solus_image_url; ?>" style="border: 0px; width: 600px; height: auto; margin: 0px; max-width: 100%;" width="600"></a>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>

						<tr>
							<td valign="top" class="templateFooter" style="background: #ffffff; text-align: center; padding: 10px 0 10px 0;">

								<div style="padding-bottom: 30px;">
									<a title="Advertise with us" href="https://thebrag.com/media/" target="_blank" style="text-decoration: none;">
										<img alt="The Brag Media" src="https://cdn.thebrag.com/tbm/The-Brag-Media-stacked-200px.jpg" width="152" style="max-width:100%; padding-bottom: 0; display: inline !important; vertical-align: bottom;">
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
																<?php BragObserver::print_social_icons(); ?>
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
							<td valign="top" class="templateFooter" style="padding-top:0; padding: 10px 0; color: #dedede; text-align: center; font-size: 10px; background: #ffffff; ">
								<div class="mcnTextContent">
									<a target="_blank" href="https://thebrag.com/verify/?a=unsub&oc={{custom_attribute.${observer_token}}}" style="color: #333333 !important;text-decoration: none;font-size: 10px !important;">Unsubscribe</a>
									&nbsp;
									<span style="font-size: 10px !important; color: #666666;">|</span>
									&nbsp;
									<a target="_blank" title="Advertise with us" href="https://thebrag.com/media/" target="_blank" style="color: #333333 !important; text-decoration: none;font-size: 10px !important;">Advertise with us</a>
								</div>
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
