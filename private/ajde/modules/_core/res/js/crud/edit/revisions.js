;$(document).ready(function() {

    $('h3.revisions a.showall').click(function() {
        if ($('table.revisions tr.collapsed').length) {
		    $('table.revisions tr').removeClass('collapsed');
        } else {
            $('table.revisions tr').addClass('collapsed');
        }
	});

    $('h3.revisions a.purge').click(function() {

        var form = $(this).parents('form');
        form.find('input.operation').val('purgeRevisions');

        var get = {
            operation	: 'purgeRevisions',
            crudId		: form.attr('id')
        };
        var url = form.attr('action') + "?" + $.param(get);

        // Add CSRF token
        var data = '_token=' + form.find('input[name=\'_token\']').val();

        $.post(url, data, function(response) {
            if (response.success === true) {
                $('table.revisions tbody tr').fadeOut();
                AC.Core.Alert.flash(response.message);
            }
        }, 'json').error(function(jqXHR, message, exception) {
            $('body').removeClass('loading');
            AC.Core.Alert.error(i18n.requestError + ' (' + exception + ')');
        });

    });
});