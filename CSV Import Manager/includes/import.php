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
    $posts = []; 

    if (($handle = fopen($fichier_csv, 'r')) !== FALSE) {
     
        $header = fgetcsv($handle);
        $header = array_filter($header, function($value) {
            return !is_null($value) && trim($value) !== '';
        });

        if (empty($header)) {
            fclose($handle);
            return; 
        }

        $last_post_title = '';
        $last_post_content = '';
        $last_post_name = '';

        while (($data = fgetcsv($handle)) !== FALSE) {
          
            if (count($data) < count($header)) {
                continue; 
            }

            $post_title = !empty($data[array_search('Titre', $header)]) ? $data[array_search('Titre', $header)] : $last_post_title;
            $post_content = !empty($data[array_search('Contenu', $header)]) ? $data[array_search('Contenu', $header)] : $last_post_content;
            $post_name = !empty($data[array_search('Slug', $header)]) ? $data[array_search('Slug', $header)] : $last_post_name;

            $last_post_title = $post_title;
            $last_post_content = $post_content;
            $last_post_name = $post_name;

            if (!isset($posts[$post_title])) {
                $posts[$post_title] = [
                    'post_type' => $type_contenu,
                    'post_status' => 'publish',
                    'post_title' => $post_title,
                    'post_content' => $post_content,
                    'post_name' => $post_name,
                    'repeteur' => [] 
                ];
            }

            $posts[$post_title]['repeteur'][] = [
                'souschamp1' => $data[array_search('souschamp1', $header)],
                'souschamp2' => $data[array_search('souschamp2', $header)],
            ];
        }
        fclose($handle);
    }

    foreach ($posts as $post_data) {
        $post_id = wp_insert_post([
            'post_type' => $post_data['post_type'],
            'post_status' => $post_data['post_status'],
            'post_title' => $post_data['post_title'],
            'post_content' => $post_data['post_content'],
            'post_name' => $post_data['post_name'],
        ]);

       
        if ($post_id && function_exists('update_field')) {
            update_field('repeteur', $post_data['repeteur'], $post_id);
        }
    }
}