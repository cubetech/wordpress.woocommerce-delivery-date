<?php
/**
 * Plugin Name: WooCommerce Delivery Date
 * Plugin URI: https://github.com/cubetech/wordpress.woocommerce-delivery-date
 * Description: Adds a delivery date field and a new column to order overview page
 * Version: 1.0
 * Author: cubetech GmbH
 * Author URI: http://www.cubetech.ch
 * Requires at least: 3.5
 * Tested up to: 3.8
 *
 * Text Domain: -
 * Domain Path: -
 *
 */
 
/*
|--------------------------------------------------------------------------
| WooCommerce Order Extra Columns
|--------------------------------------------------------------------------
*/
 
/**
 * Load Custom Order Columns
 *
 * @accesspublic
 * @since 1.0 
 * @return
*/
function woo_order_delivery_date_column($columns)
{
 
	$columns['delivery_date'] = __('Lieferdatum', 'woocommerce');	
	return $columns;
}
add_filter("manage_edit-shop_order_columns", "woo_order_delivery_date_column", 99, 99);
 
 
/**
 * Charge Order Columns Content
 *
 * @accesspublic
 * @since 1.0 
 * @return
*/
function woo_order_delivery_date_column_content($column)
{
	global $post;

	$delivery = get_post_meta($post->ID, 'Lieferdatum', true);	

	switch ($column)
	{
		case "delivery_date":
			if(!$delivery) {
				echo '<em>' . __( 'Kein Datum vorhanden', 'woocommerce' ) . '</em>';
			} else {
				echo '<strong>' . $delivery . '</strong>';
			}
		break;	
		
	}
}
add_action("manage_shop_order_posts_custom_column","woo_order_delivery_date_column_content");


/**
 * Delivery Date Checkout Field
 *
 * @accesspublic
 * @since 1.0 
 * @return
*/
function woo_order_delivery_date_checkout_field( $checkout ) {

	wp_enqueue_script( 'jquery-ui-datepicker' );

	wp_enqueue_style( 'jquery-ui', "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/smoothness/jquery-ui.css" , '', '', false);
	wp_enqueue_style( 'datepicker', plugins_url('/css/datepicker.css', __FILE__) , '', '', false);

	echo '<script language="javascript">jQuery(document).ready(function(){
		jQuery("#e_deliverydate").width("150px");
		var formats = ["dd.mm.yy","dd.mm.yy"];
		jQuery("#e_deliverydate").val("").datepicker({dateFormat: formats[1], minDate:1});
		});</script>';

	echo '<h3>Lieferdatum</h3><div id="delivery_date_checkout_field" style="width: 202%; float: left;">';

	woocommerce_form_field( 'e_deliverydate', array(
		'type'=> 'text',
		'label' => __('Lieferdatum'),
		'required'=> true,
		'placeholder' => __('Lieferdatum'),
	),

	$checkout->get_value( 'e_deliverydate' ));

	echo '</div>';

}
add_action('woocommerce_after_checkout_billing_form', 'woo_order_delivery_date_checkout_field');


/**
 * Delivery Date Checkout Field Validation
 *
 * @accesspublic
 * @since 1.0 
 * @return
*/
function woo_order_delivery_date_checkout_field_process() {

	global $woocommerce;

	// Check if set, if its not set add an error.
	if (!$_POST['e_deliverydate'])
		$woocommerce->add_error( __('Bitte füllen Sie das Lieferdatum aus.') );

}
add_action('woocommerce_checkout_process', 'woo_order_delivery_date_checkout_field_process');


/**
 * Delivery Date Checkout Field Data Save
 *
 * @accesspublic
 * @since 1.0 
 * @return
*/
function woo_order_delivery_date_checkout_field_update_order_meta( $order_id ) {

	if ($_POST['e_deliverydate']) { 
		update_post_meta( $order_id, 'Lieferdatum', esc_attr($_POST['e_deliverydate']));
	}

} 
add_action('woocommerce_checkout_update_order_meta', 'woo_order_delivery_date_checkout_field_update_order_meta'); 


/**
 * Add Delivery Date to Order Mails
 *
 * @accesspublic
 * @since 1.0 
 * @return
*/
function woo_order_delivery_date_checkout_field_order_meta_keys( $keys ) {
	$keys[] = 'Lieferdatum';
	return $keys; 
}
add_filter('woocommerce_email_order_meta_keys', 'woo_order_delivery_date_checkout_field_order_meta_keys');
