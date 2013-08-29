var Aloha = {};

Aloha.settings = {
	/*logLevels: {'error': true, 'warn': true, 'info': true, 'debug': true},
	errorhandling: false,
	ribbon: false,*/
	jQuery: $,
	format: {
		// configure buttons available in the toolbar
		config : [ 'a', 'ol', 'ul', 'b', 'i', 'p', 'sub', 'sup', 'h3', 'h4', 'removeFormat' ],
		// those are the tags that will be cleaned when clicking "remove formatting"
		removeFormats : [ 'strong', 'em', 'b', 'i', 'cite', 'q', 'code', 'abbr', 'del', 'sub', 'sup']
	},
	list: {
		// configure buttons available in the toolbar
		config : [ 'ol', 'ul' ]
	},
	contentHandler: {
		insertHtml: [ 'word', 'generic', 'oembed', 'sanitize' ],
		initEditable: [ 'sanitize' ],
		sanitize: 'relaxed' // relaxed, restricted, basic,
	}
};