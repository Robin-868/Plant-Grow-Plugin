(function($) {
    'use strict';

    $(document).ready(function() {
        // Image upload functionality
        $('.plant-grow-upload-image').on('click', function(e) {
            e.preventDefault();
            
            const stage = $(this).data('stage');
            const imageInput = $('#plant-grow-image-' + stage);
            
            // Create media uploader
            const mediaUploader = wp.media({
                title: 'Choose Plant Stage ' + stage + ' Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });
            
            // When image is selected
            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                imageInput.val(attachment.url);
                
                // Show preview
                const preview = imageInput.closest('.plant-grow-image-upload').find('.plant-grow-image-preview');
                if (preview.length) {
                    preview.html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto;">');
                } else {
                    imageInput.closest('.plant-grow-image-upload').append(
                        '<div class="plant-grow-image-preview" style="margin-top: 10px;">' +
                        '<img src="' + attachment.url + '" style="max-width: 150px; height: auto;">' +
                        '</div>'
                    );
                }
            });
            
            // Open media uploader
            mediaUploader.open();
        });
        
        // Remove image functionality
        $('.plant-grow-remove-image').on('click', function(e) {
            e.preventDefault();
            
            const stage = $(this).data('stage');
            const imageInput = $('#plant-grow-image-' + stage);
            
            imageInput.val('');
            imageInput.closest('.plant-grow-image-upload').find('.plant-grow-image-preview').remove();
        });
    });
})(jQuery);

