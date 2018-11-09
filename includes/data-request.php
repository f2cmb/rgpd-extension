<?php
/**
 * Data Request Handler.
 *
 * @link       https://example.me
 * @since      1.0
 *
 * @package    gdpr-data-request-form
 * @subpackage gdpr-data-request-form/includes
 * @prefix     gdrf_
 */


function gdrf_data_request() {

	$gdrf_error = array();
	$gdrf_type  = sanitize_key( $_POST['gdrf_data_type'] );
	$gdrf_email = sanitize_email( $_POST['gdrf_data_email'] );
	$gdrf_human = absint( filter_input( INPUT_POST, 'gdrf_data_human', FILTER_SANITIZE_NUMBER_INT ) );
	$gdrf_nonce = esc_html( filter_input( INPUT_POST, 'gdrf_data_nonce', FILTER_SANITIZE_STRING ) );
	
	if ( ! function_exists( 'wp_create_user_request' ) ) {
		wp_send_json_success( 'La demande a échoué, veuillez vérifier la version de WordPress.' );
		die();
	}

	if ( ! empty( $gdrf_email ) && ! empty( $gdrf_human ) ) {
		if ( ! wp_verify_nonce( $gdrf_nonce, 'gdrf_nonce' ) ) {
			$gdrf_error[] =  'La vérification de sécurité à échoué. Veuillez rafraîchir cette page.';
			
		} else {

			if ( ! is_email( $gdrf_email ) ) {
				$gdrf_error[] = 'adresse e-mail invalide.';
			}
			if ( 8 !== $gdrf_human ) {
				$gdrf_error[] = 'La vérification de sécurité à échoué.';
			}
			if ( ! in_array( $gdrf_type, array( 'export_personal_data', 'remove_personal_data' ), true ) ) {
				$gdrf_error[] = 'Type de demande invalide. Veuillez rafraîchir cette page.';
			}
		}
	} else {
		$gdrf_error[] = 'Tous les champs sont requis';
	}
	if ( empty( $gdrf_error ) ) {

		$request_id = wp_create_user_request( $gdrf_email, $gdrf_type );

		if ( is_wp_error( $request_id ) ) {
			wp_send_json_success( $request_id->get_error_message() );
		} elseif ( ! $request_id ) {
			wp_send_json_success( 'Impossible d\'effecuter la requête. Contactez un administrateur.' );
		} else {

			$send_request = wp_send_user_request( $request_id );

			wp_send_json_success( 'success' );
			
		}
		
	} else {
		wp_send_json_success( join( '<br />', $gdrf_error ) );
	}
	
	die();

}
/**
 * Custom role mail on request
 *
 * @package    gdpr-data-request-form
 * @subpackage gdpr-data-request-form/includes
 * 
 */
function send_webmestre_email_on_request( $request_id ){

	global $wpdb;
	//get post by request id
	$userrequest_table = $wpdb->prefix."posts";
	$userrequest_results = $wpdb->get_results( "SELECT * FROM {$userrequest_table} WHERE post_type='user_request' AND ID='{$request_id}'" );
	//get data for email
	$gdrf_email = $userrequest_results[0]->post_title;
	$gdrf_type  = $userrequest_results[0]->post_name;
	//get responsable email from wp_options
	$resp_email = get_option( 'refrgpd_form_gf' );
	//if remove request
	if ( $gdrf_type == 'remove_personal_data' ){

		$resp_remove_subject = "Demande de suppression de données personnelles";
		$resp_remove_content = "Bonjour, \r\n\n";
		$resp_remove_content .= "Un internaute ($gdrf_email) vient de faire une demande de suppression de ses données personnelles. \r\n\n";
		$resp_remove_content .= "Pour procéder à la suppression, rendez-vous dans votre interface d'administration.\r\n\n";
		$resp_remove_content .= "Cordialement,";

		return wp_mail( $resp_email, $resp_remove_subject, $resp_remove_content );

	}
	//if export request
	if ( $gdrf_type == 'export_personal_data' ){
		
		$resp_export_subject = "Demande de récupération de données personnelles";
		$resp_export_content = "Bonjour, \r\n\n";
		$resp_export_content .= "Un internaute ($gdrf_email) vient de faire une demande de récupération de ses données personnelles. \r\n\n";
		$resp_export_content .= "Pour procéder à la récupération, rendez-vous dans votre interface d'administration.\r\n\n";
		$resp_export_content .= "Cordialement,";

		return wp_mail( $resp_email, $resp_export_subject, $resp_export_content );
	}
}
/**
 * Gravity form exporter
 *
 * @package    gdpr-data-request-form
 * @subpackage gdpr-data-request-form/includes
 * 
 */
function check_plugin_gf() {
	if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {

		function gravity_exporter( $email_address, $page = 1 ) {

			$number = 500; // Limit us to avoid timing out
			$page = (int) $page;
		
			$gformExport_items = array();
		
			$search_criteria['field_filters'][] = array( 'value' => $email_address );
			$entries = GFAPI::get_entries( 0, $search_criteria );
			error_log(print_r($entries,true));

			foreach ( $entries as $entry ) {

				$entry_id = $entry['id'];
				$entry_email = $entry['1'];
				$entry_ip = $entry['ip'];
				$entry_source = $entry['source_url'];
				$entry_user_agent = $entry['user_agent'];
				$entry_date = $entry['date_created'];


				$gformData = array(
					array(
						'name' => "Adresse soumise au formulaire : $entry_source ",
						'value' => $entry_email
					),
					array(
						'name' => "Source",
						'value' => $entry_source
					),
					array(
						'name' => 'Date de soumission du formulaire',
						'value' => $entry_date
						),
					array(
						'name' => 'User Agent depuis lequel le formulaire à été soumis',
						'value' => $entry_user_agent
						),
					array(
						'name' => 'IP enregistrée à la soumission du formulaire',
						'value' => $entry_ip
						),		
				);
					

				$group_id = 'gf_entry';
				$group_label = 'Soumissions de formulaire';
			
				$gformExport_items[] = array(
					'group_id' => $group_id,
					'group_label' => $group_label,
					'item_id' => $entry_id,
					'data' => $gformData,
				);
			}
		
			// Tell core if we have more entries
			$done = count( $entries ) < $number;
			
			return array(
			'data' => $gformExport_items,
			'done' => $done,
			);
		}

	/**
	 * Gravity forms entry eraser
	 *
	 * @package    gdpr-data-request-form
	 * @subpackage gdpr-data-request-form/includes
	 * 
	 */

		function gravity_eraser( $email_address, $page = 1 ) {
			$number = 500; // Limit us to avoid timing out
			$page = (int) $page;
		
			$search_criteria['field_filters'][] = array( 'value' => $email_address );
			$entries = GFAPI::get_entries( 0, $search_criteria );
		
			$items_removed = false;

			foreach ( $entries as $entry ) {

				$entry_id = $entry['id'];
				GFAPI::delete_entry( $entry_id );
				$items_removed = true;
			
			}
		
			// Tell core if we have more entries to work on still
			$done = count( $entries ) < $number; return array( 'items_removed' => $items_removed,
			'items_retained' => false, // always false in this example
			'messages' => array(), // no messages in this example
			'done' => $done,
			);
		}
	}
}

/**
 * Mailpoet 2 exporter
 *
 * @package    gdpr-data-request-form
 * @subpackage gdpr-data-request-form/includes
 * 
 */
function check_plugin_mp() {
	if ( is_plugin_active( 'wysija-newsletters/index.php' ) ) {

		function mailpoet_exporter( $email_address, $page = 1 ) {

			global $wpdb;

			$number = 500; // Limit us to avoid timing out
			$page = (int) $page;
			$mailpoetExport_items = array();
			
			$subscribers_table = $wpdb->prefix."wysija_user";
			$subscribers_results = $wpdb->get_results( "SELECT * FROM {$subscribers_table} WHERE email='{$email_address}'" );

			foreach ( $subscribers_results as $subscriber ) {

				$subscriber_id = $subscriber->user_id;
				$subscriber_email = $subscriber->email;
				$subscriber_ip = $subscriber->ip;

				$userlist_table = $wpdb->prefix."wysija_user_list";
				$lists_table = $wpdb->prefix."wysija_list";

				$userlist_results = $wpdb->get_results( "SELECT * FROM {$userlist_table} WHERE user_id='{$subscriber_id}'" );

				$name_list = array();

				foreach ( $userlist_results as $userlist_result ){

					$id_list = $userlist_result->list_id;
					
					$lists_table = $wpdb->prefix."wysija_list";
					$list_results = $wpdb->get_results( "SELECT * FROM {$lists_table} WHERE list_id='{$id_list}'" );

						foreach ( $list_results as $list_result ){

							$name_list[] = $list_results[0]->name;

						}
				}
				
				$listcleaned = implode(', ', $name_list);

				$mailpoetData = array(
					array(
						'name' => "Adresse enregistrée dans les abonnements",
						'value' => $subscriber_email
					),
					array(
						'name' => 'IP enregistrée lors de l\'abonnement',
						'value' => $subscriber_ip
						),
					array(
						'name' => 'Listes souscrites',
						'value' => $listcleaned
						)	
				);
					
				$group_id = 'subscribers';
				$group_label = 'Abonnements de newsletter';
			
				$mailpoetExport_items[] = array(
					'group_id' => $group_id,
					'group_label' => $group_label,
					'item_id' => $subscriber_id,
					'data' => $mailpoetData,
				);
			}
		
			// // Tell core if we have more comments to work on still
			$done = count( $subscribers_results ) < $number;
			
			return array(
			'data' => $mailpoetExport_items,
			'done' => $done,
			);
		}


		function mailpoet_eraser( $email_address, $page = 1 ) {

			$number = 500; // Limit us to avoid timing out
			$page = (int) $page;

			global $wpdb;

			$subscribers_table = $wpdb->prefix."wysija_user";
			$subscribers_results = $wpdb->get_results( "SELECT * FROM {$subscribers_table} WHERE email='{$email_address}'" );
		
			$items_removed = false;
		
			foreach ( $subscribers_results as $subscriber ) {

				$subscriber_id = $subscriber->user_id;
				$userlist_table = $wpdb->prefix."wysija_user";
				$wpdb->delete( $userlist_table, array( 'user_id' => $subscriber_id ) );
				$items_removed = true;
			
			}
		
			// Tell core if we have more comments to work on still
			$done = count( $subscribers_results ) < $number; return array( 'items_removed' => $items_removed,
			'items_retained' => false, // always false in this example
			'messages' => array(), // no messages in this example
			'done' => $done,
			);
		}
	}
}
add_action( 'admin_init', 'check_plugin_gf' );
add_action( 'admin_init', 'check_plugin_mp' );

add_action( 'wp_ajax_gdrf_data_request', 'gdrf_data_request' );
add_action( 'wp_ajax_nopriv_gdrf_data_request', 'gdrf_data_request' );