<?php

/**
 * The public-specific functionality of the plugin.
 *
 * @link       http://example.me
 * @since      1.0
 *
 * @package    gdpr-data-request-form
 * @subpackage gdpr-data-request-form/public
 * @prefix     gdrf_
 */

add_action( 'wp_enqueue_scripts', 'enqueue_styles_gdrf_public' );
function enqueue_styles_gdrf_public() {
	wp_register_style( 'gdrf-public-styles', plugin_dir_url( __FILE__ ) . 'css/public.css', array(), '', 'all' );
	wp_enqueue_style( 'gdrf-public-styles', plugin_dir_url( __FILE__ ) . 'css/public.css', array(), '', 'all' );

}

add_action( 'wp_enqueue_scripts', 'enqueue_scripts_gdrf_public' );
function enqueue_scripts_gdrf_public() {
	wp_register_script( 'gdrf-public-scripts', plugin_dir_url( __FILE__ ) . 'js/gdrf-public.js', array( 'jquery' ), '', false );
	wp_enqueue_script( 'gdrf-public-scripts', plugin_dir_url( __FILE__ ) . 'js/gdrf-public.js', array( 'jquery' ), '', false );

	$translations = array(
		'gdrf_ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
		'gdrf_success'  => 'Votre demande a bien été prise en compte, vérifiez votre messagerie pour la confirmer..',
		'gdrf_errors'   => 'Des erreurs ont été détectées :',
	);
	wp_localize_script( 'gdrf-public-scripts', 'gdrf_localize', $translations );
}
