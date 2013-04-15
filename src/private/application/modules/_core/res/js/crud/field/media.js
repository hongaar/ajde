;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};
if (typeof AC.Crud.Edit ==="undefined") {AC.Crud.Edit = function(){}};

AC.Crud.Edit.Media = function() {
		
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
		
	return {
		
		init: function() {
			
            $('input[name=_embed]').on('change')
		}
		
	};
}();

$(document).ready(function() {
	AC.Crud.Edit.Media.init();
});