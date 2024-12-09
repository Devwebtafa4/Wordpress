jQuery(document).ready(function ($) {
    // Ajouter un champ "Description courte"
    $('#add-short-description').on('click', function () {
        $('#static-field-group').html(
            '<div class="static-field-item">' +

            '<div>' +
            '<div>' + '<label for="custom_short_desc_fields[title]">Titre de la description</label>' + '</div>' +
            '<div>' + '<input type="text" name="custom_short_desc_fields[title]" value="" style="width: 100%;" />' + '</div>' +
            '</div>' +

            '<div>' +
            '<div>' + '<label for="custom_short_desc_fields[gallery]">Galerie photos</label>' + '</div>' +
            '<div>' + '<input type="hidden" name="custom_short_desc_fields[gallery]" value="" class="static-field-gallery-urls" />' + '</div>' +
            '<div>' + '<button type="button" class="button select-static-gallery">Ajouter une image à la galerie</button>' + '</div>' +
            '<div class="gallery-preview"></div>' +
            '</div>' +

            '<div>' +
            '<div>' + '<label for="custom_short_desc_fields[description]">Contenu de la description</label>' + '</div>' +
            '<div>' + '<textarea id="custom_static_field_description" name="custom_short_desc_fields[description]" style="width: 100%;" rows="5"></textarea>' + '</div>' +
            '</div>' +

            '</div>'
        );

        wp.editor.initialize('custom_static_field_description', {
            tinymce: true,
            quicktags: true
        });
    });

    $(document).on('click', '.select-static-gallery', function (event) {
        event.preventDefault();
        const button = $(this);
        const galleryInput = button.closest('div').prev('div').find('.static-field-gallery-urls');
        const galleryPreview = button.closest('div').next('.gallery-preview');

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
                    galleryPreview.append('<div class="backoffice_image_item" style="position: relative;"><div style="position: absolute;top: 5px;right: 5px;height: 20px;display: flex;justify-content: flex-end;"><img src="'+ClosePath.closeimage+'" class="remove_dis_static_image" style="width: 15px;height: 15px;object-fit:cover;cursor: pointer;"></div><img class="the_image_backoffice_image_item" src="' + attachment.url + '" style="max-width: 202px; margin-right: 10px; height: 202px;" /></div>');
                }
            });

            galleryInput.val(existingUrls.join(','));
        });

        file_frame.open();
    });

    $(document).on('click', '.remove_dis_static_image', function() {
        var dis = $(this);
        var disimage = dis.closest('.backoffice_image_item').find('.the_image_backoffice_image_item').attr('src');
        var urlists = dis.closest('#static-field-group').find('.static-field-gallery-urls').val();

        var imageArray = urlists.split(",");
        var index = imageArray.indexOf(disimage);
        if (index > -1) {
            imageArray.splice(index, 1);
        }
        dis.closest('#static-field-group').find('.static-field-gallery-urls').val(imageArray.join(","));
        dis.closest('.backoffice_image_item').remove();
    });
});