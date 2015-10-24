/**
 * Lazy JS application
 */

;
(function($) {

    var bootstrap = function() {
        if (jQuery().lazyload) {
            $("img.lazy").lazyload({
                effect : "fadeIn"
            });
        }
    };

    $(document).ready(bootstrap);

})(jQuery);