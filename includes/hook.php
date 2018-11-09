<?php

/**
 * Hooks et traductions
 *
 * @link       https://example.me
 * @since      1.1
 *
 * @package    gdpr-data-request-form
 * @subpackage gdpr-data-request-form/public
 * @prefix     gdrf_
 */

/////// in the original plugin, this is where we create the options fields to give a mail where the requests are sent

/**
 * Modify confirm page 
 *
 * 
 */

function is_login_page() {
	return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}
function my_confirmation_redirect(){
	if (isset($_GET['action'])){
		if( is_login_page() ){
			if ( $_GET['action'] == 'confirmaction' ) {
				///URL of confirm page from the original plugin
				header( "Location: '  .site_url() . 'systeme/confirmation-de-votre-demande/" );
	
			}
		}
	}
}

add_action( 'init', 'my_confirmation_redirect' );

/**
 * Modify header to auto mail for confirmation email
 *
 * @package    wordpress
 * @subpackage wp-includes/user.php
 * 
 */

add_filter( 'user_request_action_email_subject', 'add_user_request_email_flag', 10, 1 );

function add_user_request_email_flag( $subject ){
	$subject .= '###check###';
	return $subject;
}

add_filter( 'wp_mail', 'change_default_request_wp_mail_headers', 10, 1 );

function change_default_request_wp_mail_headers( $args ) {

	$domain = network_home_url();
	$find = array( 'http://', 'https://' );
	$replace = '';
	$domaincleaned = str_replace( $find, $replace, $domain );

	$mymail =  get_option( 'refrgpd_form_gf' );

	if ( empty( $mymail ) ){
		$mymail = "nepasrepondre@$domaincleaned";
	}

	$myname = get_option( 'nom_form_gf' );

	if ( empty( $myname ) ){
		$myname = "Votre référent vie privée et données";
	}

	$subjectwithcheck = $args['subject'];
	$checkstring  = '###check###';
	$result = strpos( $subjectwithcheck, $checkstring );

	if ( $result !== false ){
		$args['headers'] = "from : $myname <$mymail>";
		$cleanedsubject = str_replace( $checkstring, '', $subjectwithcheck );
		$args['subject'] = $cleanedsubject;
	}
	return $args;
}
/**
 * Modify header to auto mail for confirm export email
 *
 * @package    wordpress
 * @subpackage wp-includes/user.php
 * 
 */

add_filter( 'wp_privacy_personal_data_email_content', 'add_data_sending_email_flag', 10, 1 );

function add_data_sending_email_flag( $content ){
	$content .= '###check###';
	return $content;
}
add_filter( 'wp_mail', 'change_default_export_confirm_wp_mail_headers', 10, 1 );

function change_default_export_confirm_wp_mail_headers( $args ) {

	$domain = network_home_url();
	$find = array( 'http://', 'https://' );
	$replace = '';
	$domaincleaned = str_replace( $find, $replace, $domain );

	$mymail =  get_option( 'refrgpd_form_gf' );

	if ( empty( $mymail ) ){
		$mymail = "nepasrepondre@$domaincleaned";
	}

	$myname = get_option( 'nom_form_gf' );

	if ( empty( $myname ) ){
		$myname = "Votre référent vie privée et données";
	}

	$contentwithcheck = $args['message'];
	$checkstring  = '###check###';
	$result = strpos( $contentwithcheck, $checkstring );

	if ( $result !== false ){
		$args['headers'] = "from : $myname <$mymail>";
		$cleanedcontent = str_replace( $checkstring, '', $contentwithcheck );
		$args['message'] = $cleanedcontent;
	}

	return $args;
}

/**
 * Modify header to auto mail for confirm export email
 *
 * @package    wordpress
 * @subpackage wp-includes/user.php
 * 
 */

add_filter( 'user_confirmed_action_email_content', 'add_erasure_confirmed_email_flag', 10, 1 );

function add_erasure_confirmed_email_flag( $content ){
	$content .= '###check###';
	return $content;
}
add_filter( 'wp_mail', 'change_default_erasure_confirm_wp_mail_headers', 10, 1 );

function change_default_erasure_confirm_wp_mail_headers( $args ) {

	$domain = network_home_url();
	$find = array( 'http://', 'https://' );
	$replace = '';
	$domaincleaned = str_replace( $find, $replace, $domain );

	$mymail =  get_option( 'refrgpd_form_gf' );

	if ( empty( $mymail ) ){
		$mymail = "nepasrepondre@$domaincleaned";
	}

	$myname = get_option( 'nom_form_gf' );

	if ( empty( $myname ) ){
		$myname = "Votre référent vie privée et données";
	}

	$contentwithcheck = $args['message'];
	$checkstring  = '###check###';
	$result = strpos( $contentwithcheck, $checkstring );

	if ( $result !== false ){
		$args['headers'] = "from : $myname <$mymail>";
		$cleanedcontent = str_replace( $checkstring, '', $contentwithcheck );
		$args['message'] = $cleanedcontent;
	}
	
	return $args;
}


/**
 * Gravity form exporter hook
 *
 * @package    rgpd-data-request-form
 * @subpackage rgpd-data-request-form/includes
 * @subpackage rgpd-data-request-form/includes/data-request.php
 * 
 */

function register_gravity_exporter( $gformExporters ) {
	$gformExporters['f2cmb-gf-export'] = array(
	'exporter_friendly_name' => 'Exportateur Gravity',
	'callback' => 'gravity_exporter',
	);
	return $gformExporters;
}

add_filter( 'wp_privacy_personal_data_exporters', 'register_gravity_exporter' );

/**
 * Gravity form eraser hook
 *
 * @package    rgpd-data-request-form
 * @subpackage rgpd-data-request-form/includes
 * @subpackage rgpd-data-request-form/includes/data-request.php
 * 
 */

function register_gravity_eraser( $gformErasers ) {
	$gformErasers['f2cmb-gf-erase'] = array(
	'eraser_friendly_name' => 'Effaceur Gravity',
	'callback'             => 'gravity_eraser',
	);
	return $gformErasers;
}

add_filter( 'wp_privacy_personal_data_erasers','register_gravity_eraser');

/**
 * Mailpoet 2 exporter hook
 *
 * @package    rgpd-data-request-form
 * @subpackage rgpd-data-request-form/includes
 * @subpackage rgpd-data-request-form/includes/data-request.php
 * 
 */

function register_mailpoet_exporter( $mailpoetExporters ) {
	$mailpoetExporters['f2cmb-mp-export'] = array(
	'exporter_friendly_name' => 'Exportateur MailPoet',
	'callback' => 'mailpoet_exporter',
	);
	return $mailpoetExporters;
}

add_filter( 'wp_privacy_personal_data_exporters', 'register_mailpoet_exporter' );

/**
 * Gravity form eraser hook
 *
 * @package    rgpd-data-request-form
 * @subpackage rgpd-data-request-form/includes
 * @subpackage rgpd-data-request-form/includes/data-request.php
 * 
 */

function register_mailpoet_eraser( $mailpoetErasers ) {
	$mailpoetErasers['f2cmb-mp-erase'] = array(
	'eraser_friendly_name' => 'Effaceur Mailpoet',
	'callback'             => 'mailpoet_eraser',
	);
	return $mailpoetErasers;
}

add_filter( 'wp_privacy_personal_data_erasers','register_mailpoet_eraser');
