<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function gestionnaire_import_csv_creer_menu_admin() {
    add_menu_page(
        'CSV Import Manager',
        'CSV Import Manager',
        'manage_options',
        'gestionnaire-import-csv',
        'gestionnaire_import_csv_page_admin',
        'dashicons-upload'
    );
}
add_action('admin_menu', 'gestionnaire_import_csv_creer_menu_admin');