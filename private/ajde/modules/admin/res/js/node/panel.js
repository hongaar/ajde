;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Admin === "undefined") {App.Admin = function(){}};
if (typeof App.Admin.Node === "undefined") {App.Admin.Node = function(){}};

App.Admin.Node.Panel = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	return {

		init: function() {		
			$('form.ACCrudList tbody tr.panel td').live('dblclick', function(e) {
				e.stopPropagation();
			});
			
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
							App.Admin.Node.Update.update(id, meta, key, value);
							origVal = newVal;
						}
					}, 10);					
				});
			});
		}
		
	};
}();

$(document).ready(function() {
	App.Admin.Node.Panel.init();
});