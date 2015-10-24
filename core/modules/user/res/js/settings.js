;
$(document).ready(function() {
	
	var complexityClass = {
		81: 'Perfect',
		61: 'Strong',
		41: 'Average',
		1: 'Too weak',
		0: 'Unsafe password'		
	};

    try {
        $("input[name=password]").complexify({
                minimumChars		: 8,
                strengthScaleFactor	: 0.5,
                bannedPasswords		: complexify.banlist,
                banmode				: 'loose',
                preventSubmit		: false
            }, function (valid, complexity) {
                $(this).closest('form').data('valid', valid);

                if (!$(this).val().length) return;

                $('div.complexity .progress').css({'width':complexity + '%'});
                if (!valid) {
                    $('div.complexity').removeClass('valid').addClass('invalid');
                } else {
                    $('div.complexity').removeClass('invalid').addClass('valid');
                }
                for (var i in complexityClass) {
                    if (complexity >= i) {
                        $('div.complexity .class').html(complexityClass[i]);
                    }
                }
            }
        );
    } catch (e) {};

    AC.Form.Ajax.registerCallback(function() {
		
        $('#settingsform').bind('before', function() {
            if ($("input[name=password]").val() && !$(this).data('valid')) {
                $('.give-status').addClass('error');
                $('.status-text').text('Please provide a more complex password');
                return false;
            }
        });

        $('#settingsform').bind('result', function(event, data) {
            if (data.success === false) {
                $('.give-status').addClass('error');
                $('.status-text').text(data.message);
            } else {
                if (data.returnto !== false) {
                    // IE fix (going level up?)
                    window.location.href = $('base').attr('href') + data.returnto;
                } else {
                    window.location.href = 'user';
                }
            }
        });

        $('#settingsform').bind('error', function(event) {
            $('.give-status').addClass('error');
            $('.status-text').text('Something went wrong');
        });

        $('#settingsform').bind('submit', function(event) {
    //		$('.give-status').removeClass('error');
    //		$('.status-text').text('Registering...');
            return true;
        });

    });

    $('#settingsform :input').on('keydown', function() {
        $('.status-text').text('');
        $('.give-status').removeClass('error');
    });

});
