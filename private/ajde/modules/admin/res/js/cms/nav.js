/**
 * Main JS application
 */

;
(function($) {
	
	var bootstrap = function() {	
		
		var url = "admin/cms:nav.json";
		
		$.get(url, function(data) {
			
			$('#nav-tree').tree({
		        data: data,
		        dragAndDrop: true,
		        autoOpen: 0,
		        dataUrl: url
		    });	
			
		}, 'json');
		
		
		
	};

	$(document).ready(bootstrap);

})(jQuery);