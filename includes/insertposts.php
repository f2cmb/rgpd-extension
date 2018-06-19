<?php

function createposts() {
    
    $pageformulaire = array(
        'post_title' => 'Gestion des données personnelles',
        'post_type' => 'page-systeme',
        'post_content' => '[f2cmb-data-request]',
        'post_status' => 'publish',
        'post_author' => 1,
    );

    $post_form_id = wp_insert_post( $pageformulaire, true );
    update_post_meta( $post_form_id,  'introduction', 'Utilisez ce formulaire pour demander une récupération ou une suppression de vos données personnelles.');

    $pageconfirmation = array(
        'post_title' => 'Confirmation de votre demande',
        'post_type' => 'page-systeme',
        'post_content' => 'Merci d’avoir confirmé votre demande de récupération/suppression de vos données personnelles.',
        'post_status' => 'publish',
        'post_author' => 1,
    );

    $post_confirm_id = wp_insert_post( $pageconfirmation, true );
    
    update_post_meta( $post_confirm_id,  'introduction', 'Nous revenons vers vous dans les plus brefs délais.');

    // ajout des templates custom via custom post templae

    $template = 'template-pagesysteme.php';
    update_post_meta( $post_form_id,  'custom_post_template', $template );
    update_post_meta( $post_confirm_id,  'custom_post_template', $template );
    
}


