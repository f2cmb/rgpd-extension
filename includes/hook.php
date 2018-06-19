<?php

/**
 * Hooks et traductions
 *
 * @link        http://example.me
 * @since      1.1
 *
 * @package    gdpr-data-request-form
 * @subpackage gdpr-data-request-form/public
 * @prefix     gdrf_
 */
/**
 * Add extra options on admin
 *
 * @package    noyau
 * @subpackage noyau/admin
 * @subpackage noyau/admin/configurations.php
 * 
 */
function f2cmb_rgpd_mail_option() {
	register_setting( 'f2cmb_option_group_client', 'refrgpd_form_gf' );
}
add_action( 'f2cmb_configuration_clients_nouvelle_option', 'f2cmb_rgpd_mail_option' );

function f2cmb_rgpd_mail_html(){ ?>
	<fieldset class="formulairegestion-refrgpd">
		<label for="nom_form_gf">Qui doit réceptionner les demandes liées au RGPD ?</label>
		<input type="text" name="refrgpd_form_gf" id="refrgpd_form_gf" value="<?php echo get_option( 'refrgpd_form_gf' ); ?>" placeholder="exemple1@courriel.fr, exemple2@courriel.fr" />

	</fieldset>
<?php }
add_action( 'f2cmb_configuration_clients_html_moderation', 'f2cmb_rgpd_mail_html', 46 );

/**
 * Modify confirm page 
 *
 * 
 */

function is_login_page() {
	return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}
function my_confirmation_redirect(){
	if( is_login_page() ){
		if ( $_GET['action'] == 'confirmaction' ) {

			header( "Location: '  .site_url() . 'systeme/confirmation-de-votre-demande/" );

		}
	}
}

add_action( 'init', 'my_confirmation_redirect' );

/**
 * Modify header to auto mail
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

add_filter( 'wp_mail', 'change_default_wp_mail_headers', 10, 1 );

function change_default_wp_mail_headers( $args ) {

	$mymail = get_option( 'email_form_gf' );
	$myname = get_option( 'nom_form_gf' );


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
 * Gravity form exporter hook
 *
 * @package    f2cmb-data-request-form
 * @subpackage f2cmb-data-request-form/includes
 * @subpackage f2cmb-data-request-form/includes/data-request.php
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
 * @package    f2cmb-data-request-form
 * @subpackage f2cmb-data-request-form/includes
 * @subpackage f2cmb-data-request-form/includes/data-request.php
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
 * Mailpoet exporter hook
 *
 * @package    f2cmb-data-request-form
 * @subpackage f2cmb-data-request-form/includes
 * @subpackage f2cmb-data-request-form/includes/data-request.php
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
 * @package    f2cmb-data-request-form
 * @subpackage f2cmb-data-request-form/includes
 * @subpackage f2cmb-data-request-form/includes/data-request.php
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
