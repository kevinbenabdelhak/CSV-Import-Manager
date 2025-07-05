<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function gestionnaire_import_csv_get_acf_fields_callback() {
    $post_type = sanitize_text_field($_POST['post_type']);
    $fields = [];

    if (function_exists('acf_get_field_groups')) {
        $field_groups = acf_get_field_groups(['post_type' => $post_type]);

        foreach ($field_groups as $group) {
            $group_fields = acf_get_fields($group['ID']);
            if ($group_fields) {
                foreach ($group_fields as $field) {
                    $fields[] = [
                        'key' => $field['key'],
                        'label' => $field['label']
                    ];
                }
            }
        }
    }

    echo json_encode($fields);
    wp_die();
}
add_action('wp_ajax_get_acf_fields', 'gestionnaire_import_csv_get_acf_fields_callback');