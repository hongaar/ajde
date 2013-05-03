/**
 * Main JS application
 */

;
(function($) {
	
	var bootstrap = function() {	
		
		// Fastclick
		new FastClick(document.body);
        
        // Chosen
		setTimeout(function() {
			$("select.chosen").chosen({
				allow_single_deselect: true
			});
		}, 100);
		
		// Picker
		$("input[type=radio], input[type=checkbox]").not('.nopicker').picker();
		
		// Form validation with Twitter Bootstrap
		$('form').attr('novalidate', 'novalidate');
		$('input, select, textarea').not('[type=submit]').jqBootstrapValidation({
			autoAdd: {
				helpBlocks: false
			}
		});
	
		// Toggled menu
		$('.menu-toggle').on('click', function() {
			if ($('.row-fluid > div:eq(0)').hasClass('span2')) {
				$.cookie('collapsed-menu', 1);
				$('.row-fluid > div:eq(0)').removeClass('span2').addClass('span0');
				$('.row-fluid > div:eq(1)').removeClass('span10').addClass('span11');
			} else {
				$.cookie('collapsed-menu', 0);
				$('.row-fluid > div:eq(0)').removeClass('span0 no-animation').addClass('span2');
				$('.row-fluid > div:eq(1)').removeClass('span11 no-animation').addClass('span10');
			}
		});
		if ($.cookie('collapsed-menu') == 1) {
			$('.row-fluid > div').addClass('no-animation');
			$('.menu-toggle').trigger('click');			
		}
	};

	$(document).ready(bootstrap);

})(jQuery);