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
function barcode_font_128_encode( $value, $order, $placeholder_clean )
{
	if( empty($order) ) return $value;

	$remaining_string = str_replace('wpo_barcode_128|', '', $placeholder_clean);
	switch ( $remaining_string ) {
		case 'order_number':
			$str = strval($order->get_id());
			break;
	}
	if( $str ) {
		$value = Code128Encoder::encode($str);
	}
	return $value;
}