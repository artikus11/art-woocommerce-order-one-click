<?php
/**
 * Email Template
 *
 * This template can be overridden by copying it to yourtheme/art-woocommerce-order-one-click/email.php.
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/templates
 * @version 3.0.0
 *
 * @global $args
 */

$letter_data  = $args['letter_data'];
$product_data = $args['product_data'];
$letter_meta  = $args['letter_meta'];

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
	<title><?php echo esc_html( get_bloginfo( 'name', 'display' ) ); ?></title>
	<style>
		/* -------------------------------------
			GLOBAL RESETS
		------------------------------------- */

		/*All the styling goes here*/

		img {
			border: none;
			-ms-interpolation-mode: bicubic;
			max-width: 100%;
		}

		body {
			background-color: #f6f6f6;
			font-family: sans-serif;
			-webkit-font-smoothing: antialiased;
			font-size: 14px;
			line-height: 1.4;
			margin: 0;
			padding: 0;
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%;
		}

		table {
			border-collapse: separate;
			mso-table-lspace: 0pt;
			mso-table-rspace: 0pt;
			width: 100%;
		}

		table td {
			font-family: sans-serif;
			font-size: 14px;
			vertical-align: top;
			padding-bottom: 10px;
		}

		/* -------------------------------------
			BODY & CONTAINER
		------------------------------------- */

		.body {
			background-color: #f6f6f6;
			width: 100%;
		}

		/* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
		.container {
			display: block;
			margin: 40px auto !important;
			/* makes it centered */
			max-width: 580px;
			padding: 10px;
			width: 580px;
		}

		/* This should also be a block element, so that it will fill 100% of the .container */
		.content {
			box-sizing: border-box;
			display: block;
			margin: 0 auto;
			max-width: 580px;
			padding: 10px;
		}

		/* -------------------------------------
			HEADER, FOOTER, MAIN
		------------------------------------- */
		.main {
			background: #ffffff;
			border-radius: 3px;
			width: 100%;
		}

		.wrapper {
			box-sizing: border-box;
			padding: 20px;
		}

		.content-block .product-list > tbody > tr > td {
			padding-bottom: 6px;
		}

		.content-block {
			padding-bottom: 10px;
			padding-top: 10px;
		}

		.footer {
			clear: both;
			margin-top: 10px;
			text-align: center;
			width: 100%;
		}

		.footer td,
		.footer p,
		.footer span,
		.footer a {
			color: #999999;
			font-size: 12px;
			text-align: center;
		}

		/* -------------------------------------
			TYPOGRAPHY
		------------------------------------- */
		h1,
		h2,
		h3,
		h4 {
			color: #000000;
			font-family: sans-serif;
			font-weight: bold;
			line-height: 1.4;
			margin: 0;
		}

		h1 {
			margin-bottom: 30px;
		}

		h2 {
			margin-bottom: 24px;
		}

		h3 {
			margin-bottom: 18px;
		}

		h4 {
			margin-bottom: 14px;
		}

		h1 {
			font-size: 35px;
			font-weight: 300;
			text-align: center;
			text-transform: capitalize;
		}

		p,
		ul,
		ol {
			font-family: sans-serif;
			font-size: 14px;
			font-weight: normal;
			margin: 0;
			margin-bottom: 15px;
		}

		p li,
		ul li,
		ol li {
			list-style-position: inside;
			margin-left: 5px;
		}

		a {
			color: #3498db;
			text-decoration: underline;
		}

		/* -------------------------------------
			BUTTONS
		------------------------------------- */
		.btn {
			box-sizing: border-box;
			width: 100%;
		}

		.btn > tbody > tr > td {
			padding-bottom: 15px;
		}

		.btn table {
			width: auto;
		}

		.btn table td {
			background-color: #ffffff;
			border-radius: 5px;
			text-align: center;
		}

		.btn a {
			background-color: #ffffff;
			border: solid 1px #3498db;
			border-radius: 5px;
			box-sizing: border-box;
			color: #3498db;
			cursor: pointer;
			display: inline-block;
			font-size: 14px;
			font-weight: bold;
			margin: 0;
			padding: 12px 25px;
			text-decoration: none;
			text-transform: capitalize;
		}

		.btn-primary table td {
			background-color: #3498db;
		}

		.btn-primary a {
			background-color: #3498db;
			border-color: #3498db;
			color: #ffffff;
		}

		/* -------------------------------------
			OTHER STYLES THAT MIGHT BE USEFUL
		------------------------------------- */
		.last {
			margin-bottom: 0;
		}

		.first {
			margin-top: 0;
		}

		.align-center {
			text-align: center;
		}

		.align-right {
			text-align: right;
		}

		.align-left {
			text-align: left;
		}

		.clear {
			clear: both;
		}

		.mt0 {
			margin-top: 0;
		}

		.mb0 {
			margin-bottom: 0;
		}

		.preheader {
			color: transparent;
			display: none;
			height: 0;
			max-height: 0;
			max-width: 0;
			opacity: 0;
			overflow: hidden;
			mso-hide: all;
			visibility: hidden;
			width: 0;
		}

		.powered-by a {
			text-decoration: none;
		}

		hr {
			border: 0;
			border-bottom: 1px solid #f6f6f6;
			margin: 20px 0;
		}

		/* -------------------------------------
			RESPONSIVE AND MOBILE FRIENDLY STYLES
		------------------------------------- */
		@media only screen and (max-width: 620px) {
			table.body h1 {
				font-size: 28px !important;
				margin-bottom: 10px !important;
			}

			table.body p,
			table.body ul,
			table.body ol,
			table.body td,
			table.body span,
			table.body a {
				font-size: 16px !important;
			}

			table.body .wrapper,
			table.body .article {
				padding: 10px !important;
			}

			table.body .content {
				padding: 0 !important;
			}

			table.body .container {
				padding: 0 !important;
				width: 100% !important;
			}

			table.body .main {
				border-left-width: 0 !important;
				border-radius: 0 !important;
				border-right-width: 0 !important;
			}

			table.body .btn table {
				width: 100% !important;
			}

			table.body .btn a {
				width: 100% !important;
			}

			table.body .img-responsive {
				height: auto !important;
				max-width: 100% !important;
				width: auto !important;
			}
		}

		/* -------------------------------------
			PRESERVE THESE STYLES IN THE HEAD
		------------------------------------- */
		@media all {
			.ExternalClass {
				width: 100%;
			}

			.ExternalClass,
			.ExternalClass p,
			.ExternalClass span,
			.ExternalClass font,
			.ExternalClass td,
			.ExternalClass div {
				line-height: 100%;
			}

			.apple-link a {
				color: inherit !important;
				font-family: inherit !important;
				font-size: inherit !important;
				font-weight: inherit !important;
				line-height: inherit !important;
				text-decoration: none !important;
			}

			#MessageViewBody a {
				color: inherit;
				text-decoration: none;
				font-size: inherit;
				font-family: inherit;
				font-weight: inherit;
				line-height: inherit;
			}

			.btn-primary table td:hover {
				background-color: #34495e !important;
			}

			.btn-primary a:hover {
				background-color: #34495e !important;
				border-color: #34495e !important;
			}
		}

	</style>
</head>
<body>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
	<tr>
		<td>&nbsp;</td>
		<td class="container">
			<div class="content">

				<!-- START CENTERED WHITE CONTAINER -->
				<table role="presentation" class="main">

					<!-- START MAIN CONTENT AREA -->
					<tr>
						<td class="wrapper">
							<table role="presentation" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<h2><?php esc_html_e( 'Order One Click', 'art-woocommerce-order-one-click' ); ?></h2>
										<p><?php esc_html_e( 'A quick one-click order has arrived', 'art-woocommerce-order-one-click' ); ?></p>
										<hr>
										<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="">
											<tbody>
												<?php foreach ( $letter_data as $key => $value ) : ?>

													<?php

													$label = ucfirst( $key );

													if ( 'phone' === $label ) :
														$phone_url = preg_replace( '/\D/', '', $value );

														$value = sprintf( '<a href="%s">%s</a>', $phone_url, $value );
													endif;
													?>

													<tr style="margin-bottom: 10px">
														<td align="left" style="width: 80px">
															<strong>

																<?php
																//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
																esc_html_e( $label, 'art-woocommerce-order-one-click' );
																?>
															</strong>
														</td>
														<td align="left">
															<?php echo wp_kses_post( $value ); ?>
														</td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
										<hr>
										<h3><?php esc_html_e( 'Information about the selected product', 'art-woocommerce-order-one-click' ); ?></h3>
										<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="product-list">
											<tbody>
												<?php foreach ( $product_data as $key => $value ) : ?>
													<tr>
														<td align="left">
															<strong><em><?php echo esc_html( $key ); ?>:</em></strong> <em><?php echo esc_html( $value ); ?></em>
														</td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<!-- END MAIN CONTENT AREA -->
				</table>
				<!-- END CENTERED WHITE CONTAINER -->

				<!-- START FOOTER -->
				<div class="footer">
					<table role="presentation" border="0" cellpadding="0" cellspacing="0">
						<?php
						$footer_meta = [];

						foreach ( $letter_meta as $key => $meta ) :
							if ( 'time' === $key ) :
								$meta['value'] = wp_date( 'd.m.Y H:i:s', $meta['value'] );
							endif;

							if ( 'url' === $key ) :
								$meta['value'] = wp_parse_url( $meta['value'], PHP_URL_HOST );
							endif;

							$footer_meta[] = esc_html( $meta['label'] ) . ': ' . esc_html( $meta['value'] );
							?>
						<?php endforeach; ?>
						<tr>
							<td class="content-block powered-by">
								<?php echo esc_html( implode( ' &bull; ', $footer_meta ) ); ?>
							</td>
						</tr>

					</table>
				</div>
				<!-- END FOOTER -->

			</div>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
</body>
</html>