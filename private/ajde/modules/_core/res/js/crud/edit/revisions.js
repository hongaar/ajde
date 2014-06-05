;$(document).ready(function() {
	$('h3.revisions a').click(function() {
        if ($('table.revisions tr.collapsed').length) {
		    $('table.revisions tr').removeClass('collapsed');
        } else {
            $('table.revisions tr').addClass('collapsed');
        }
	});
});