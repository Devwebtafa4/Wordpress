jQuery(document).ready(function ($) {
    let selectIndex = $('#post-type-selects .post-type-select').length;

    $('#add-post-type-select').on('click', function () {
        const postTypes = customRepeaterData.postTypes; 

        let selectHtml = '<div class="post-type-select" style="margin-bottom: 20px;">' +
            '<label for="custom_repeater_post_types[' + selectIndex + ']">Post Type</label>' +
            '<select name="custom_repeater_post_types[' + selectIndex + ']">';

        $.each(postTypes, function (key, value) {
            selectHtml += '<option value="' + key + '">' + value.label + '</option>';
        });

        selectHtml += '</select><button type="button" class="remove-select button">Remove</button></div>';
        
        $('#post-type-selects').append(selectHtml);
        selectIndex++;
    });

    // Supprimer un select
    $(document).on('click', '.remove-select', function () {
        $(this).closest('.post-type-select').remove();
    });
});