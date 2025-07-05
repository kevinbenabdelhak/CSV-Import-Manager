<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function gestionnaire_import_csv_ajouter_scripts_admin() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('csv-import-manager-script', plugin_dir_url(__FILE__) . '../assets/js/admin.js', ['jquery'], null, true);

    // Passer les traductions à JavaScript
    wp_localize_script('csv-import-manager-script', 'csvImportManagerTranslations', [
        'titre' => __('Titre', 'csv-import-manager'),
        'contenu' => __('Contenu', 'csv-import-manager'),
        'slug' => __('Slug', 'csv-import-manager'),
    ]);
}
add_action('admin_enqueue_scripts', 'gestionnaire_import_csv_ajouter_scripts_admin');