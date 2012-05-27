;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};
if (typeof AC.Crud.Edit ==="undefined") {AC.Crud.Edit = function(){}};

AC.Crud.Edit.Text = function() {
	
	var addAloha = function(elm) {
		
		Aloha.ready( function() {
			elm.aloha();
		});
		
		Aloha.bind('aloha-smart-content-changed', function(e) {
			if (Aloha.getActiveEditable()) {
				var contents = Aloha.getActiveEditable().getContents();
				Aloha.getActiveEditable().originalObj[0].value = contents;
			}
			
		});
		
	};
	
	return {
		
		init: function() {
			//addAloha($('form.ACCrudEdit textarea'));
			addAloha($('form.ACCrudEdit textarea'));
		}
		
	};
}();