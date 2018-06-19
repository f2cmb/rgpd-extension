<?php

/**
 * @link              https://example.me
 * @since             1.0
 * @package           rgpd-form
 *
 * @wordpress-plugin
 * Plugin Name:       Créasit formulaire GRPD
 * Plugin URI:        https://example.me
 * Description:       Permet de saisir des demandes de données relatives aux norms GRPD / créer un post de formulaire - FORK de https://jeanbaptisteaudras.com/gdpr-data-request-form
 *
 * Version:           1.1
 * Author:            https://github.com/f2cmb/
 * Author URI:        https://github.com/f2cmb/
 * Text Domain:       rgpd-form
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Public
 */
require_once plugin_dir_path( __FILE__ ) . '/includes/data-request.php';
require_once plugin_dir_path( __FILE__ ) . '/public/public.php';
require_once plugin_dir_path( __FILE__ ) . '/public/shortcode.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/hook.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/insertposts.php';

register_activation_hook( __FILE__, 'createposts' );
add_action( 'user_request_action_confirmed' , 'send_webmestre_email_on_request', 10, 1 );