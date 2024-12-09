jQuery(document).ready(function ($) {
    $('#add-included-field').on('click', function () {
        const index = $('#included-field-group .included-field-item').length;

        $('#add-included-field').before(
            '<div class="included-field-item"  style="display: flex;align-items:end;gap:15px;margin-bottom:10px;">' +
            '<div style="flex: 0 0 80%;">' +
            '<div><label for="custom_included_fields[' + index + '][title]">Titre</label></div>' +
            '<div><input type="text" name="custom_included_fields[' + index + '][title]" value="" placeholder="Titre" style="width: 100%;" /></div>' +
            '</div>' +

            '<div style="flex: 0 0 15%;">' +
            '<button type="button" class="remove-included-field button">Supprimer</button>' +
            '</div>' +
            '</div>'
        );
    });

    $(document).on('click', '.remove-included-field', function () {
        $(this).closest('.included-field-item').remove();
    });

});