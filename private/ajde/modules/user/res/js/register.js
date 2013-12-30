;
$(document).ready(function() {
	
	var complexityClass = {
		81: 'Perfect',
		61: 'Strong',
		41: 'Average',
		1: 'Too weak',
		0: 'Unsafe password'		
	};

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

    AC.Form.Ajax.registerCallback(function() {

	    $('#registerform').on('before', function() {
            if (!$('#provider').val() && !$(this).data('valid')) {
                $('body').removeClass('loading');
                $('.give-status').addClass('error');
                $('.status-text').text('Please provide a more complex password');
                return false;
            }
        });

        $('#registerform').on('result', function(event, data) {
            if (data.success === false) {
                $('.give-status').addClass('error');
                $('.status-text').text(data.message);
            } else {
                if (data.returnto !== false) {
                    window.location.href = data.returnto;
                } else {
                    window.location.href = 'user';
                }
            }
        });

        $('#registerform').on('error', function(event) {
            $('.give-status').addClass('error');
            $('.status-text').text('Something went wrong');
        });

        $('#registerform').on('submit', function(event) {
//		$('.give-status').removeClass('error');
//		$('.status-text').text('Registering...');
            return true;
        });

    });
	
	$('#registerform :input').on('keydown', function() {
		$('.status-text').text('');
	});
	
});
