jQuery(document).ready(function($) {
    function get_acf_fields(post_type) {
        return $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'get_acf_fields',
                post_type: post_type
            }
        });
    }

    $('#fichier_csv').on('change', function() {
        var file = this.files[0];
        var post_type = $('#type_contenu').val();

        if (file && post_type) {
            get_acf_fields(post_type).done(function(response) {
                var acfFields = [];
                try {
                    acfFields = JSON.parse(response);
                } catch (e) {
                    console.error('Erreur de parsing JSON', e);
                }

                var reader = new FileReader();
                reader.onload = function(e) {
                    var contents = e.target.result;
                    var lines = contents.split("\n");
                    if (lines.length > 0) {
                        var headers = lines[0].split(",");

                        // Filtrer les colonnes vides ou contenant uniquement des espaces
                        headers = headers.filter(header => header.trim() !== "");

                        var mappingHtml = '<table class="form-table"><tbody>';
                        for (var i = 0; i < headers.length; i++) {
                            if (headers[i].trim() === "") continue;

                            mappingHtml += '<tr>';
                            mappingHtml += '<th>' + headers[i].trim() + '</th>';
                            mappingHtml += '<td><select name="mapping[' + headers[i].trim() + ']">';
                            mappingHtml += '<option value="post_title">' + csvImportManagerTranslations.titre + '</option>';
                            mappingHtml += '<option value="post_content">' + csvImportManagerTranslations.contenu + '</option>';
                            mappingHtml += '<option value="post_name">' + csvImportManagerTranslations.slug + '</option>';

                            // Ajout de champs ACF
                            for (var field of acfFields) {
                                mappingHtml += '<option value="' + field.name + '">' + field.label + '</option>';

                                // Ajouter l'option pour le champ répéteur uniquement si elle n'a pas été ajoutée
                                if (field.name === 'repeteur' && !mappingHtml.includes('value="repeteur"')) {
                                    mappingHtml += '<option value="repeteur">' + field.label + ' (Répéteur)' + '</option>';
                                }
                            }

                            mappingHtml += '</select></td>';
                            mappingHtml += '</tr>';
                        }
                        mappingHtml += '</tbody></table>';

                        $('#mapping_colonnes_csv').html(mappingHtml).show();
                        $('#mapping_colonnes_title').show();
                        $('#import_button').show();
                    }
                };
                reader.readAsText(file);
            });
        } else {
            $('#mapping_colonnes_csv').hide();
            $('#mapping_colonnes_title').hide();
            $('#import_button').hide();
        }
    });

    $('#type_contenu').on('change', function() {
        $('#fichier_csv').trigger('change');
    });
});