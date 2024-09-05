<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function gestionnaire_import_csv_traiter_import_csv() {
    if (isset($_POST['gestionnaire_import_csv_nonce_field']) && wp_verify_nonce($_POST['gestionnaire_import_csv_nonce_field'], 'gestionnaire_import_csv_nonce')) {
        if (isset($_FILES['fichier_csv']) && !empty($_FILES['fichier_csv']['tmp_name'])) {
            $type_contenu = sanitize_text_field($_POST['type_contenu']);
            $mapping = isset($_POST['mapping']) ? $_POST['mapping'] : [];
            $fichier_csv = $_FILES['fichier_csv']['tmp_name'];
            gestionnaire_import_csv_processus_csv($fichier_csv, $type_contenu, $mapping);
        }
    }
    wp_redirect(admin_url('admin.php?page=gestionnaire-import-csv&import_status=success'));
    exit;
}
add_action('admin_post_importer_csv', 'gestionnaire_import_csv_traiter_import_csv');

function gestionnaire_import_csv_processus_csv($fichier_csv, $type_contenu, $mapping) {
    if (($handle = fopen($fichier_csv, 'r')) !== FALSE) {
       
        $header = fgetcsv($handle);
        

        $header = array_filter($header, function($value) {
            return !is_null($value) && trim($value) !== '';
        });

      
        if (empty($header)) {
            fclose($handle);
            return; 
        }

        while (($data = fgetcsv($handle)) !== FALSE) {
          
            $data = array_filter($data, function($value) {
                return !is_null($value) && trim($value) !== '';
            });

           
            if (count($data) < count($header)) {
                continue; 
            }

            $post_data = [
                'post_type'   => $type_contenu,
                'post_status' => 'publish',
                'post_title'  => '', 
                'post_content' => '', 
            ];

            $post_name = '';

           
            foreach ($header as $i => $col_name) {
                if (isset($data[$i])) {
                    switch ($mapping[$col_name]) {
                        case 'post_title':
                            $post_data['post_title'] = $data[$i];
                            break;
                        case 'post_content':
                            $post_data['post_content'] = $data[$i];
                            break;
                        case 'post_name':
                            $post_name = $data[$i];
                            break;
                    }
                }
            }

           
            $post_id = wp_insert_post($post_data);

            if ($post_id) {
                
                if (!empty($post_name)) {
                    $updated_post_data = [
                        'ID' => $post_id,
                        'post_name' => sanitize_title($post_name),
                    ];
                    wp_update_post($updated_post_data);
                }

               
                if (function_exists('update_field')) {
                    foreach ($header as $i => $col_name) {
                        if (strpos($mapping[$col_name], 'post_') === false) {
                            update_field($mapping[$col_name], $data[$i], $post_id);
                        }
                    }
                }
            }
        }
        fclose($handle);
    }
}