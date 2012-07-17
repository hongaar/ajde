;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Core ==="undefined") {AC.Core = function(){}};

AC.Core.Cookielaw = function() {
	
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
		
	return {
		
		init: function() {
			EU.CookieManager.init({
				expires:			365, // Define the expiry time for the cookie
				cookie_prefix:		'EU_', // prefix for the cookie
				optin_cookie_name:	'OPTIN', // cookie name
				test:				false, 
				idle:				0, // Time in seconds of you want the initial popup to close automatically if user dont intract with it. 
				link:				'main/cookielaw.html', // Linkf or the page if you want to include a page for cookie information for user. 
				message:			'The cookie settings on this website are set to \'<strong>allow all cookies</strong>\' to give you the very best experience.' +
									' If you continue without changing these settings, you consent to this - but if you want, you can change your settings' +
									' now by clicking on the Change settings link.',
				functionalList:		{ // Functionality list for all three levels. 
										'strict': {
											'will' : ['Remember what is in your shopping basket', 'Remember cookie access level.'],
											'willnot': ['Send information to other websites so that advertising is more relevant to you', 'Remember your log-in details', 'Improve overall performance of the website', 'Provide you with live, online chat support']
										},
										'functional': {
											'will' : ['Remember what is in your shopping basket', 'Remember cookie access level.','Remember your log-in details','Make sure the website looks consistent','Offer live chat support'],
											'willnot': ['Allow you to share pages with social networks like Facebook', 'Allow you to comment on blogs', 'Send information to other websites so that advertising is more relevant to you']
										},
										'targeting': {
											'will' : ['Remember what is in your shopping basket', 'Remember cookie access level.','Remember your log-in details','Make sure the website looks consistent','Offer live chat support','Send information to other websites so that advertising is more relevant to you'],
											'willnot': []
										}
				}
			});
		}
		
	}
	
}();

$(document).ready(function() {
	AC.Core.Cookielaw.init();
});