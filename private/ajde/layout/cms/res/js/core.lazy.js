/**
 * Lazy JS application
 */

;
(function($) {

    var bootstrap = function() {
        $("img.lazy").lazyload({
            effect : "fadeIn"
        });
    };

    $(document).ready(bootstrap);

})(jQuery);