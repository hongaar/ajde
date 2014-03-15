;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};
if (typeof AC.Crud.Edit ==="undefined") {AC.Crud.Edit = function(){}};

AC.Crud.Edit.Picker = function() {
		
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	var currentCrudAction;
		
	return {
		
		init: function() {
			$('form.ACCrudEdit div.picker a.choose').click(AC.Crud.Edit.Picker.chooseHandler);
			$('form.ACCrudEdit div.picker a.setnull').click(AC.Crud.Edit.Picker.setnullHandler);
		},
			
		chooseHandler: function(e) {
			currentCrudAction = $(this).parents('div.picker');

			var listRoute = $(this).parents('div.picker:eq(0)').attr('data-list-route');
			var listMultiple = $(this).parents('div.picker:eq(0)').attr('data-list-multiple');
			
			if (listRoute.indexOf('?') > -1) {
				listRoute += '&';
			} else {
				listRoute += '?';
			}

			$.fancybox.open({
				href: listRoute + 'multiple=' + listMultiple,
				type: 'iframe',
				autoSize: false,
				width: '100%',
				height: '100%',
				closeBtn: false
			});
		},
		
		setnullHandler: function(e) {
			$(this).parents('div.picker').trigger('chosen', false);
		},
			
		chosen: function(data) {
			currentCrudAction.trigger('chosen', [data]);
			$.fancybox.close();
		}
		
	};
}();

$(document).ready(function() {
	AC.Crud.Edit.Picker.init();
});