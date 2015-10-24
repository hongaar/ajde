;
if (typeof AC ==="undefined") {AC = function(){}}
if (typeof AC.Core ==="undefined") {AC.Core = function(){}}

AC.Core.Shortcut = function() {

    var popup;
    var opener;

    var onClick = function(e) {
        var target = opener || popup || false;
        var href, windowName;

        if (target) {
            href = target.location.href.replace('#', '') + '#';
            $(this).attr('href', href);
        } else {
            href = $(this).attr('href');
            windowName = $(this).attr('target');
            popup = window.open('', windowName);

            if (popup.location.href == 'about:blank') {
                popup.location.href = href;
            }

            e.preventDefault();
        }
    };

    return {

        init: function() {
            opener = window.opener || false;

            $('a.ajde-shortcut').on('click', onClick);
        }

    };
}();

$(document).ready(function() {
    AC.Core.Shortcut.init();
});