<?php
/**
 * Plugin Name: WooCommerce PDF Invoices & Packing Slips Barcode 39/128 Font
 * Plugin URI: http://www.wpovernight.com
 * Description: Adds the barcode font Libre Barcode 39/128 to WooCommerce PDF Invoices & Packing Slips
 * Version: 1.0
 * Author: Ewout Fernhout
 * Author URI: http://www.wpovernight.com
 * License: GPLv2 or later
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */


require dirname(__FILE__).'/vendor/autoload.php';

use Hbgl\Barcode\Code128Encoder;


add_action( 'wpo_wcpdf_custom_styles', 'wpo_wcpdf_barcode_font', 10, 2 );
function wpo_wcpdf_barcode_font ( $document_type, $document = null ) {
	if ( apply_filters('wpo_wcpdf_use_path', true) ) {
		$location = dirname(__FILE__);
	} else {
		$location = untrailingslashit( plugin_dir_url( __FILE__ ) );
	}
	?>
	@font-face {
		font-family: 'Barcode39Text';
		font-style: normal;
		font-weight: normal;
		src: local('Barcode39Text'), local('Barcode39Text'), url(<?php echo $location; ?>/fonts/LibreBarcode39Text-Regular.ttf) format('truetype');
	}
	@font-face {
		font-family: 'Barcode39';
		font-style: normal;
		font-weight: normal;
		src: local('Barcode39'), local('Barcode39'), url(<?php echo $location; ?>/fonts/LibreBarcode39-Regular.ttf) format('truetype');
	}
	@font-face {
		font-family: 'Barcode128Text';
		font-style: normal;
		font-weight: normal;
		src: local('Barcode128Text'), local('Barcode128Text'), url(<?php echo $location; ?>/fonts/LibreBarcode128Text-Regular.ttf) format('truetype');
	}
	@font-face {
		font-family: 'Barcode128';
		font-style: normal;
		font-weight: normal;
		src: local('Barcode128'), local('Barcode128'), url(<?php echo $location; ?>/fonts/LibreBarcode128-Regular.ttf) format('truetype');
	}


	<?php
	// /* To override default order number: */
	// .order-number td {
	// 	font-family: 'Barcode39Text';
	// 	font-size: 32pt;
	// 	line-height: 32pt;
	// }
	// /* insert end/start characters */
	// .order-number td:before, .order-number td:after {
	// 	content: '*';
	// }

}

if (!defined('DOMPDF_ENABLE_FONTSUBSETTING')) {
	define('DOMPDF_ENABLE_FONTSUBSETTING', true);
}


add_filter( 'wpo_wcpdf_templates_replace_wpo_barcode_128', 'barcode_font_128_encode', 10, 3 );
function barcode_font_128_encode( $value, $order, $placeholder_clean = null ) {
	if( empty($order) || empty($placeholder_clean) || !function_exists('wcpdf_get_document') ) {
		return $value;
	}

	$remaining_string = str_replace('wpo_barcode_128|', '', $placeholder_clean);
	switch ( $remaining_string ) {
		case 'order_number': // {{wpo_barcode_128|order_number}}
			$str = is_callable(array($order,'get_order_number')) ? strval($order->get_order_number()) : strval($order->get_id());
			break;
		case 'invoice_number': // {{wpo_barcode_128|invoice_number}}
			$document = wcpdf_get_document('invoice', $order);
			if ($document && $document->exists()) {
				$str = strval($document->get_number());
			}
			break;
		case 'packing-slip_number': // {{wpo_barcode_128|packing-slip_number}}
			$document = wcpdf_get_document('packing-slip', $order);
			if ($document && $document->exists()) {
				$str = strval($document->get_number());
			}
			break;
	}

	if ( emty($str) && $meta = $order->get_meta($remaining_string) ) {
		$str = $meta;
	}
	
	if( $str ) {
		$value = sprintf( '<div class="wpo-barcode-128" style="font-family:\'Barcode128\'; font-size:32pt; line-height:32pt;">%s</div>', Code128Encoder::encode($str) );
	}
	return $value;
}