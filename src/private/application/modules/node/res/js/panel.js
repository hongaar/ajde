;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Node === "undefined") {App.Node = function(){}};

App.Node.IssueRow = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	return {

		init: function() {			
			$('table.crud tr.panel :input').each(function() {
				var origVal = $(this).val();
				$(this).live('focus', function() {
					origVal = $(this).val();
				});
				$(this).live('blur', function() {
					var $self = $(this);
					setTimeout(function() {
						// allow other operations
						var newVal = $self.val();
						if (newVal != origVal) {
							var row = $self.parents('tr');
							var id = row.prev().find('input[type=checkbox]').attr('value');
							var meta = ($self.data('meta') == 1) ? 1 : 0;
							var key = row.find(':input[name=' + $self.data('input') + ']').attr('name');
							var value = row.find(':input[name=' + $self.data('input') + ']').val();
							App.Issue.Update.update(id, meta, key, value);
						}
					}, 10);					
				});
			});
		}
		
	};
}();

$(document).ready(function() {
	App.Node.IssueRow.init();
});