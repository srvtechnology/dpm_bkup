jQuery(document).ready(function () {
    jQuery("a#media-popup").on('click', function () {
        var url = BASE_URL + '/back-admin/media/popup';

        var media_input = jQuery(this).attr('media-input') ? jQuery(this).attr('media-input') : 'media_id';
        var media_destination = jQuery(this).attr('media-destination') ? jQuery(this).attr('media-destination') : 'media-dest';

        jQuery.ajax({
            url: url,
            method: 'post',
            data: '_token=' + CSRF_TOKEN + '&media_input=' + media_input + '&media_destination=' + media_destination,
            success: function (html) {
                jQuery("#response-block").html(html);
            }

        });
        return false;
    });
});

