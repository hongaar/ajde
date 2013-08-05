;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Report === "undefined") {App.Report = function(){}};

App.Report.Work = function() {

//	var btnWorkHandler = function(e) {
//		e.stopPropagation();
//		e.preventDefault();
//		var row = $(this).parents('tr');
//		var id = row.find('input[type=checkbox]').attr('value');
//		App.Timer.Widget.work(id);
//		return false;
//	};

	return {

		init: function() {			
			$('.work').each(function() {
				$(this).popover({
					content: $($(this).data('element')).html()
				});
			});
		}

	};
}();

$(document).ready(function() {
	App.Report.Work.init();
});