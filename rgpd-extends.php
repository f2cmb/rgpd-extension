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
 * Version:           1.2
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
require_once plugin_dir_path( __FILE__ ) . '/includes/activation.php';
require_once plugin_dir_path( __FILE__ ) . '/includes/roles.php';


/**
 * Inserts confirm page and form on activation / register policy post if it exist
 *
 * @package    noyau
 * @subpackage noyau/admin
 * @subpackage noyau/admin/configurations.php
 * 
 */
register_activation_hook( __FILE__, 'insert_posts' );
register_activation_hook( __FILE__, 'register_policy_post' );
register_activation_hook( __FILE__, 'default_mail_rgpd_referer' );