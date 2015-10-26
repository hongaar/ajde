;
if (typeof AC === "undefined") {
    AC = function () {
    }
}
if (typeof AC.Crud === "undefined") {
    AC.Crud = function () {
    }
}
if (typeof AC.Crud.Edit === "undefined") {
    AC.Crud.Edit = function () {
    }
}
AC.Crud.Edit.Timespan = function() {

	var span = {
		week: 144000,
		day: 28800,
		hour: 3600,
		minute: 60
	};

	return {

		parseTimespan: function(string) {
			if (!isNaN(parseFloat(string)) && isFinite(string)) {
				return string * 60;
			}
			var fragments = string.split(' ');
			var seconds = 0;
			for(var i = 0; i < fragments.length; i++) {
				var fragment = fragments[i].trim();
				var indicator = fragment.substr(-1).toLowerCase();
				var value = fragment.replace(indicator, '') * 1;
				switch (indicator) {
					case 'w':
						seconds += (value * span.week);
						break;
					case 'd':
						seconds += (value * span.day);
						break;
					case 'h':
						seconds += (value * span.hour);
						break;
					case 'm':
						seconds += (value * span.minute);
						break;
					case 's':
						seconds += (value);
						break;
				}
			}
			return seconds;
		},

		formatTimespan: function(seconds) {
			var weeks = Math.floor(seconds / span.week);
			seconds = seconds - (weeks * span.week);

			var days = Math.floor(seconds / span.day);
			seconds = seconds - (days * span.day);

			var hours = Math.floor(seconds / span.hour);
			seconds = seconds - (hours * span.hour);

			var minutes = Math.floor(seconds / span.minute);
			seconds = seconds - (minutes * span.minute);

			var output = '';
			if (weeks) {
				output += weeks + 'w ';
			}
			if (days) {
				output += days + 'd ';
			}
			if (hours) {
				output += hours + 'h ';
			}
			if (minutes) {
				output += minutes + 'm ';
			}
			if (seconds) {
				output += seconds + 's ';
			}
			return output.trim();
		},

		init: function() {
			$('input.timespan').each(function() {
				$(this).val(AC.Crud.Edit.Timespan.formatTimespan( $(this).prev().val() ));
				$(this).change(function() {
					var seconds = AC.Crud.Edit.Timespan.parseTimespan( $(this).val() );
					$(this).val(AC.Crud.Edit.Timespan.formatTimespan( seconds ));
					$(this).prev().val(seconds);
				});
			});
		}

	};
}();

(function($) {
	$(function() {
		AC.Crud.Edit.Timespan.init();
	});
})(jQuery);
