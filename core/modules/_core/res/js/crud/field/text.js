;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};
if (typeof AC.Crud.Edit ==="undefined") {AC.Crud.Edit = function(){}};

AC.Crud.Edit.Text = function() {
		
	return {
		
		init: function() {
			/* ... */
		}
		
	};
}();

(function($) {	
	// BOOTSTRAP
	$(function() {
		AC.Crud.Edit.Text.init();
	});
})(jQuery);