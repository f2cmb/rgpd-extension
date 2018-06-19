<?php

/**
 * Form shortcode.
 *
 * @link        http://example.me
 * @since      1.0
 *
 * @package    gdpr-data-request-form
 * @subpackage gdpr-data-request-form/public
 * @prefix     gdrf_
 */

function gdrf_shortcode_init() {
	function gdrf_shortcode_data_request( $atts ) {

		// Enqueue CSS/JS
		wp_enqueue_script( 'gdrf-public-scripts' );
		wp_enqueue_style( 'gdrf-public-styles' );

		// Display the form
		ob_start();
		?>
			<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="gdrf-form">
				<input type="hidden" name="action" value="gdrf_data_request">
				<input type="hidden" name="gdrf_data_nonce" id="gdrf_data_nonce" value="<?php echo wp_create_nonce( 'gdrf_nonce' ); ?>" />
				<ul class="form form-outer">
					<li class="form__item form__title">
						<h2>Sélectionnez votre demande :</h2>
					</li>
					<li class="form__item">
						<input id="gdrf-data-type-export" class="gdrf-data-type-input" type="radio" name="gdrf_data_type" value="export_personal_data"> 
						<label for="gdrf-data-type-export" class="gdrf-data-type-label">Je souhaite demander la récupération de mes données personnelles</label>
					</li>
					<li class="form__item">
						<input id="gdrf-data-type-remove" class="gdrf-data-type-input" type="radio" name="gdrf_data_type" value="remove_personal_data"> 
						<label for="gdrf-data-type-remove" class="gdrf-data-type-label">Je souhaite demander la suppression de mes données personnelles</label>
					</li>
					<li class="form__item">
						<label for="gdrf_data_email">
							Indiquez votre adresse de messagerie * :
						</label>
						<input type="email" id="gdrf_data_email" name="gdrf_data_email" />
					</li>
					<li class="form__item">
						<label for="gdrf_data_human">
							Vérification antispam : 3 + 5 = ?
						</label>
						<input type="text" id="gdrf_data_human" name="gdrf_data_human" />
					</li>
					<li class="form__item">
						<input id="gdrf-submit-button" class="submit-btn" type="submit" value="Envoyer la demande" />
					</li>
				<ul>
			</form>
		<?php
		return ob_get_clean();
	}
	add_shortcode( 'f2cmb-data-request', 'gdrf_shortcode_data_request' );
}
add_action('init', 'gdrf_shortcode_init');