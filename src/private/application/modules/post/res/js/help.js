;
Application.Help = function() {

	return {

		init: function() {

			$('#help').click(function() {
				$(this).fadeOut(function() {
					$(this).remove();
				});
			});
		}

	}
}();

$(document).ready(function() {
	Application.Help.init();
});