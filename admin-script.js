jQuery(document).ready(function ($) {
    function toggleFields() {
        var contentType = $('#bcg_content_type').val();
        var isCategory = contentType === 'category';

        $('input[name="bcg_heading"]').closest('h2, p').toggle(!isCategory);
        $('input[name="bcg_url_structure"]').closest('h2, p').toggle(!isCategory);
        $('#wp-bcg_template-wrap').toggle(!isCategory);
        $('input[name="bcg_category_slug"]').closest('h2, p').toggle(isCategory);
        $('input[name="bcg_category_description"]').closest('h2,p').toggle(isCategory);
        $('select[name="bcg_category"]').closest('h2, p').toggle(!isCategory);
    }

    $('#bcg_content_type').change(toggleFields);
    toggleFields();
});
