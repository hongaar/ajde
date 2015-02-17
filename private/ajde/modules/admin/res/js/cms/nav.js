/**
 * Main JS application
 */

;
(function($) {
	
	var bootstrap = function() {	
		
		var url = "admin/cms:nav.json";
		var tree = $('#nav-tree');

		tree.tree({
			data: [],
			dragAndDrop: false,
			autoOpen: false,
			dataUrl: url
		});

		var refreshTree = function() {

			$.get(url, function(data) {

				tree.tree('loadData', data);

				if ($('div[data-node-id]').length) {
					var nodeId = $('div[data-node-id]').data('node-id');
					var node = tree.tree('getNodeById', nodeId);
					tree.tree('selectNode', node);
					tree.tree('openNode', node, false);
				}

			}, 'json');

		};
		refreshTree();

		$('a.refresh-nav').click(refreshTree);

		tree.bind(
			'tree.click',
			function(event) {
				// The clicked node is 'event.node'
				var node = event.node;
				window.location.href = 'admin/node:view?edit=' + node.id;
			}
		);
		
	};

	$(document).ready(bootstrap);

})(jQuery);