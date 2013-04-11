;
$(document).ready(function() {
	
	var complexityClass = {
		81: 'Perfect',
		61: 'Strong',
		41: 'Average',
		1: 'Too weak',
		0: '&nbsp;'		
	};

	$("input[name=password]").complexify({
			minimumChars		: 8,
			strengthScaleFactor	: 0.7,
			bannedPasswords		: complexify.banlist,
			banmode				: 'loose',
			preventSubmit		: false
		}, function (valid, complexity) {
			$(this).closest('form').data('valid', valid);
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
		
	$('#registerform').bind('before', function() {
		if (!$(this).data('valid')) {
			$('dd.status').addClass('error');
			$("dd.status").text('Please provide a more complex password');
			return false;
		}
	});
		
	$('#registerform').bind('result', function(event, data) {
		if (data.success === false) {
			$('dd.status').addClass('error');
			$("dd.status").text(data.message);
		} else {
			if (data.returnto !== false) {
				window.location.href = data.returnto;
			} else {
				window.location.href = 'user';
			}
		}
	});
		
	$('#registerform').bind('error', function(event) {
		$('dd.status').addClass('error');
		$("dd.status").text('Something went wrong');
	});
	
	$('#registerform').bind('submit', function(event) {
		$('dd.status').removeClass('error');
		$("dd.status").text('Registering...');
		return true;
	});
	
});
