/**
 * Adds the current bootstrap media-query class to the body element and keeps it updated.
 * Attaches empty spans with bootstrap responsive utitlity classes and checks their visibility.
 * Updates the classes on window resize.
 *
 * examples:
 * var bsc = $('body').bsClasses(); // classes available and up-to-date from now on
 * $('body').bsClasses('deactivate'); // event listeners removed, span elements removed
 * bsc.activate(); event listeners back on, spans attached again
 *
 * @see http://getbootstrap.com/css/#responsive-utilities
 * @see http://getbootstrap.com/css/#grid-media-queries
 */

$.fn.bsClasses = function() {

    var pluginName = 'bsClasses',
        args = Array.prototype.slice.call(arguments),
        element = $(this),

        // these are the "modes" we check for.
        modes = [
            {className: 'phone', element: $('<span class="visible-xs"></span>')},
            {className: 'tablet', element: $('<span class="visible-sm"></span>')},
            {className: 'desktop', element: $('<span class="visible-md"></span>')},
            {className: 'large', element: $('<span class="visible-lg"></span>')}
        ],
        plugin = null,
        fn = null
        ;


    function Plugin() {

        this.update = function() {
            $.each(modes, function(i) {
                element[modes[i].element.is(':visible') ? 'addClass' : 'removeClass'](modes[i].className);
            });
        };

        this.activate = function() {
            $(window).bind('resize.' + pluginName, this.update);
            $.each(modes, function(i) {
                element.append(modes[i].element);
            });
            this.update();
        };

        this.deactivate = function() {
            $(window).unbind('resize.' + pluginName);
            $.each(modes, function(i) {
                element.removeClass(modes[i].className);
                modes[i].element.remove();
            });
        };

        this.activate();
    }





    // if there already is an instance for this element, try to call functions on the instance and bail out.
    if (element.data(pluginName)) {
        plugin = element.data(pluginName);
        fn = args.shift();
        if (plugin[fn] instanceof Function ) {
            plugin[fn].apply(plugin, args);
        }
        else {
            window.console.warn('no such method', fn);
        }
        return plugin;
    }


    // otherwise, create a new instance and return it
    plugin = new Plugin(element);
    element.data(pluginName, plugin);
    return plugin;


};