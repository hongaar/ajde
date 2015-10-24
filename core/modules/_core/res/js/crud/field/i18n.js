//;
//if (typeof AC ==="undefined") {AC = function(){}};
//if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};
//if (typeof AC.Crud.Edit ==="undefined") {AC.Crud.Edit = function(){}};
//
//AC.Crud.Edit.I18n = function() {
//		
//	var infoHandler		= AC.Core.Alert.show;
//	var warningHandler	= AC.Core.Alert.warning;
//	var errorHandler	= AC.Core.Alert.error;
//		
//	return {
//		
//		init: function() {
//			$('form.ACCrudEdit div.translations a').on('click', AC.Crud.Edit.I18n.clickHandler);
//		},
//			
//		clickHandler: function(e) {
//			e.preventDefault();
//			
//			var href = $(this).attr('href');
//			var fields = $(this).data('clone');
//			fields = fields.split(',');
//			
//			for(var i in fields) {
//				var val = $(':input[name=' + fields[i] + ']').val();
//				href = href + '&prefill[' + fields[i] + ']=' . val;
//			}
//			
//			document.location.href = href;			
//			return false;
//		}
//		
//	};
//}();
//
//$(document).ready(function() {
//	AC.Crud.Edit.I18n.init();
//});