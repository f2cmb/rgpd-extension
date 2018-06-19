<?php

function createformpost() {
    
    $pageformulaire = array(
        'post_title' => 'Gestion des données personnelles',
        'post_type' => 'page-systeme',
        'post_content' => '[f2cmb-data-request]',
        'post_status' => 'publish',
        'post_author' => 1,
     );

     $new_post_id = wp_insert_post($pageformulaire, true);
     update_post_meta( $new_post_id,  'introduction', 'Utilisez ce formulaire pour demander une récupération ou une suppression de vos données personnelles.');
}
