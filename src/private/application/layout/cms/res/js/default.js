/**
 * Main JS application
 */

;
(function($) {
	
	var bootstrap = function() {		
		
		// Form validation with Twitter Bootstrap
		$('form').attr('novalidate', 'novalidate');
		$('input, select, textarea').not('[type=submit]').jqBootstrapValidation({
			autoAdd: {
				helpBlocks: false
			}
		});
	};

	$(document).ready(bootstrap);

})(jQuery);