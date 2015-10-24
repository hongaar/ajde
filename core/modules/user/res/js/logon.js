;
$(document).ready(function() {
    AC.Form.Ajax.registerCallback(function() {
        $('#loginform').on('result', function(event, data) {
            if (data.success === false) {
                $('.give-status').addClass('error');
                $('.status-text').text(data.message);
            } else {
                if ($('#returnto').val()) {
                    $('body').addClass('loading');
                    // IE fix (going level up?)
                    window.location.href = $('base').attr('href') + $('#returnto').val();
                } else {
                    $('body').addClass('loading');
                    window.location.reload(true);
                }
            }
        });
        $('#loginform').on('error', function(event) {
            $('.give-status').addClass('error');
            $('.status-text').text(i18n.requestError);
        });
        $('#loginform').on('submit', function(event) {
    //		$('.give-status').removeClass('error');
    //		$('.status-text').text('Logging in...');
            return true;
        });
        $('#loginform :input').on('keydown', function() {
            $('.status-text').text('');
        });
    });
});
