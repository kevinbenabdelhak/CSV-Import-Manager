<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function gestionnaire_import_csv_page_admin() {
    ?>



    <div class="wrap">
        <h1><?php esc_html_e('CSV Import Manager', 'csv-import-manager'); ?></h1>
        <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>" id="import_form">
            <input type="hidden" name="action" value="importer_csv">
            <?php wp_nonce_field('gestionnaire_import_csv_nonce', 'gestionnaire_import_csv_nonce_field'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="fichier_csv"><?php esc_html_e('Fichier CSV', 'csv-import-manager'); ?></label></th>
                    <td><input type="file" name="fichier_csv" id="fichier_csv" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="type_contenu"><?php esc_html_e('Type de contenu', 'csv-import-manager'); ?></label></th>
                    <td>
                        <select name="type_contenu" id="type_contenu">
                            <?php
                            $post_types = get_post_types(['public' => true], 'objects');
                            foreach ($post_types as $post_type) {
                                echo '<option value="' . esc_attr($post_type->name) . '">' . esc_html($post_type->labels->singular_name) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
            <h2 id="mapping_colonnes_title" style="display:none;"><?php esc_html_e('Lier les colonnes CSV', 'csv-import-manager'); ?></h2>
            <div id="mapping_colonnes_csv" style="display:none;"></div>
            <button type="submit" class="button button-primary" id="import_button" style="display:none;"><?php esc_html_e('Importer', 'csv-import-manager'); ?></button>
        </form>
  
    <?php
}