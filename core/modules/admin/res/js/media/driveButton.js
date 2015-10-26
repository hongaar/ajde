;
if (typeof App === "undefined") {
    App = function () {
    }
}
if (typeof App.Admin === "undefined") {
    App.Admin = function () {
    }
}
if (typeof App.Admin.Media === "undefined") {
    App.Admin.Media = function () {
    }
}
App.Admin.Media.DriveBtn = function() {

    var infoHandler		= AC.Core.Alert.show;
    var warningHandler	= AC.Core.Alert.warning;
    var errorHandler	= AC.Core.Alert.error;

    var node;

    var openDialog = function(e) {
        node = $(this);
        DriveCreatePicker();
    };

    var onPicked = function(download_url, filename, oauthToken) {
        var form = $('form.ACCrudList');
        var mediatype = form.find('select[name="view[filter][mediatype]"]').val();
        var url = 'admin/media:upload.json';
        var data = {
            _token: form.find('input[name=\'_token\']').val(),
            filename: download_url,
            name: filename,
            oauthToken: oauthToken,
            mediatype: mediatype
        };

        node.addClass('loading');
        $.post(url, data, onCallback, 'json').error(function(jqXHR, message, exception) {
            $('body').removeClass('loading');
            node.removeClass('loading');
            errorHandler(i18n.requestError + ' (' + exception + ')');
        });
    };

    var onCallback = function(data) {
        if (!data.success) {
            errorHandler('Something went wrong when saving the media file');
        } else {
            AC.Crud.List.updateView(node);
        }
    };

    return {

        init: function() {
            // event bind
            $('form.ACCrudList thead a.btn.drive').live('click', openDialog);

            // on result
            DriveResultHandler(onPicked);
        }

    };
}();

$(document).ready(function() {
    App.Admin.Media.DriveBtn.init();
});
