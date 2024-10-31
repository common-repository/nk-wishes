/**
 * Send using AJAX
 */
(function($) {
    if(!NK_WISHES) {
        return;
    }

    var busy;
    $(document).on('submit', '.nk-wishes-form', function(e) {
        e.preventDefault();

        if(busy) {
            return;
        }
        busy = 1;

        // form
        var $form = $(this);

        // result block
        var $result = $form.find('.nk-wishes-form-result').html('');

        // show spinner
        var $spinner = $form.find('.nk-wishes-form-spinner').css('visibility', 'visible');

        // submit
        var $submit = $form.find('[type=submit]').attr('disabled', 'disabled');

        // event
        $(window).trigger('nk-wishes-form-before-submit', $form);

        // send AJAX request
        $.ajax({
            type: 'POST',
            url:  NK_WISHES.WP.siteurl + '/wp-admin/admin-ajax.php',
            data: $(this).serialize(),
            success:function(data) {
                var $data = $(data);
                var status = $data.attr('data-status');
                
                if(status) {
                    $result.html($data.html());

                    if(status == 'success') {
                        $form[0].reset();
                    }
                }

                // hide spinner
                $spinner.css('visibility', 'hidden');

                // submit
                $submit.removeAttr('disabled');

                busy = 0;

                // event
                $(window).trigger('nk-wishes-form-after-submit', $form, status);
            }
        });

    });
}(jQuery));