jQuery(document).ready(function ($) {
    $('#add-field').on('click', function () {
        const index = $('#repeater-fields .repeater-field').length;

        let itineraryOptions = '';
            itineraryData.options.forEach(function(option) {
                itineraryOptions += '<option value="' + option.id + '">' + option.title + '</option>';
            });

        $('#repeater-fields').append(
            '<div class="repeater-field" style="margin-bottom: 20px;">' +
            '<div style="flex: 0 0 80%;width:80%;">' +
            '<div>' +
            '<div><label for="custom_repeater_fields[' + index + '][jour]">Jour</label></div>' +
            '<div><input type="text" name="custom_repeater_fields[' + index + '][jour]" value="" placeholder="Jour" style="width: 100%;" /></div>' +
            '</div>' +
            '<div>' +
            '<div><label for="custom_repeater_fields[' + index + '][titre]">Titre</label></div>' +
            '<div><input type="text" name="custom_repeater_fields[' + index + '][titre]" value="" placeholder="Titre" style="width: 100%;" /></div>' +
            '</div>' +

            '<div>' +
            '<div><label for="custom_repeater_fields[' + index + '][gallery]">Galerie de photos</label></div>' +
            '<input type="hidden" name="custom_repeater_fields[' + index + '][gallery]" value="" class="gallery-urls" /></div>' +
            '<button type="button" class="button select-gallery">Ajouter une image à la galerie</button>' +
            '<div class="gallery-preview" style="margin-top: 10px;"></div>' +

            '<div>' +
            '<div><label for="custom_repeater_fields[' + index + '][description]">Description</label></div>' +
            '<div><textarea id="custom_repeater_fields_' + index + '_description" name="custom_repeater_fields[' + index + '][description]" rows="4" style="width: 100%;"></textarea></div>' +
            '</div>' +

            '<div class="choix_des_content" style="display:flex;justify-content:start;align-items:center;gap:15px;">' +
            '<div style="flex:0 0 30%;gap:15px;">' +
            '<label for="custom_repeater_fields[' + index + '][itineraire]">Contenus Itinéraires</label>' +
            '<select class="content_itineraire_choices" name="custom_repeater_fields[' + index + '][itineraire][]" style="width: 100%;">' +
            '<option value="">Choisir</option>' +
            itineraryOptions +
            '</select>' +
            '</div>' +
            '<div style="flex:0 0 60%;">' +
            '<div><label for="">Les contenus choisis</label></div>' +
            '<div class="fake_textarea" style="border:1px solid #8c8f94;width:100%;height:80px;background:#fff;display:flex;flex-wrap:wrap;justify-content:start;gap:5px;"></div>' +
            '<div><input class="chosen_itineraires" name="custom_repeater_fields[' + index + '][chosen]" type="hidden"></div>' +
            '</div>' +
            '</div>' +

            '</div>' +

            '<div style="flex: 0 0 10%;width:10%;">' +
            '<button type="button" class="remove-field button">Supprimer</button>' +
            '</div>' +
            '</div>'
        );

        wp.editor.initialize('custom_repeater_fields_' + index + '_description', {
            tinymce: true,
            quicktags: true
        });
    });

    $(document).on('change','.content_itineraire_choices',function(){
        let deval = $(this).val();
        let detext = $(this).find("option:selected").text();

        $(this).find("option:selected").attr('disabled','disabled');


        //let texta = $(this).closest('.choix_des_content').find('textarea').val();
        let hidda = $(this).closest('.choix_des_content').find('.chosen_itineraires').val();

        $(this).closest('.choix_des_content').find('.fake_textarea').append(
            '<div id="' + deval + '" class="fake_textarea_item" style="display:flex;height: fit-content;width: fit-content;background: yellow;justify-content: center;padding: 3px 7px;gap: 5px;align-items:start;"><div style="flex: 0 0 72%;">' + detext + '</div><div style="flex: 0 0 15%;height: 10px;align-content: center;align-items: center;justify-content: center;display: flex;gap: 5px;"><img src="'+ClosePath.closeimage+'" class="deselect_contenu" style="width: 10px;10px: inherit;object-fit:cover;cursor: pointer;"></div></div>');

        if (hidda != '') {
            $(this).closest('.choix_des_content').find('.chosen_itineraires').val(hidda + "|" + deval);
        }else{
            $(this).closest('.choix_des_content').find('.chosen_itineraires').val(deval);
        }
    });

    //uncheck itineraire contenu
    $(document).on('click','.deselect_contenu',function(){
        let deoption = $(this).closest('div').prev('div').text();
        $(this).closest('.choix_des_content').find('select.content_itineraire_choices option').filter(function() {
            return $(this).text() === deoption;
        }).attr('disabled', false);

        let idtocheck = $(this).closest('.fake_textarea_item').attr('id');
        let inital_val = $(this).closest('.fake_textarea').next('div').find('.chosen_itineraires').val();


        let updatedValue = inital_val
            .split('|')
            .filter(function(value) {
                return value !== idtocheck;
            })
            .join('|');

        $(this).closest('.fake_textarea').next('div').find('.chosen_itineraires').val(updatedValue);

        $(this).closest('.fake_textarea_item').remove();
    });

    //remove curent iteration
    $(document).on('click', '.remove-field', function () {
        $(this).closest('.repeater-field').remove();
    });

    //remove current gallery image item
    $(document).on('click', '.remove_dis_image', function() {
        var dis = $(this);
        var disimage = dis.closest('.backoffice_image_item').find('.the_image_backoffice_image_item').attr('src');
        var urlists = dis.closest('.repeater-field').find('.gallery-urls').val();

        var imageArray = urlists.split(",");
        var index = imageArray.indexOf(disimage);
        if (index > -1) {
            imageArray.splice(index, 1);
        }
        dis.closest('.repeater-field').find('.gallery-urls').val(imageArray.join(","));
        dis.closest('.backoffice_image_item').remove();
    });

    $(document).on('click', '.select-gallery', function (event) {
        event.preventDefault();
        const button = $(this);
        const galleryInput = button.prev('div').find('.gallery-urls');
        const galleryPreview = button.siblings('.gallery-preview');

        let existingUrls = galleryInput.val().length ? galleryInput.val().split(',') : [];

        let file_frame = wp.media({
            title: 'Choisir des images pour la galerie',
            button: {
                text: 'Utiliser cette image',
            },
            multiple: true
        });

        file_frame.on('select', function () {
            const attachments = file_frame.state().get('selection').map(function (attachment) {
                return attachment.toJSON();
            });

            $.each(attachments, function (i, attachment) {
                if (!existingUrls.includes(attachment.url)) {
                    existingUrls.push(attachment.url);
                    galleryPreview.append('<div class="backoffice_image_item" style="position: relative;"><div style="position: absolute;top: 5px;right: 5px;height: 20px;display: flex;justify-content: flex-end;"><img src="'+ClosePath.closeimage+'" class="remove_dis_image" style="width: 15px;height: 15px;object-fit:cover;cursor: pointer;"></div><img src="' + attachment.url + '" style="max-width: 202px; margin-right: 10px; height: 202px;" /></div>');
                }
            });

            galleryInput.val(existingUrls.join(','));
        });

        file_frame.open();
    });
});