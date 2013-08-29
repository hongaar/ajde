;$(document).ready(function() {

    function GMapsInitialize() {

        var prepareForSql = function(loc) {
            loc = loc.toString();
            loc = loc.replace('(', '');
            loc = loc.replace(')', '');
            loc = loc.replace(',', '');
            return loc;
        };

        $('div.spatial.gmaps').each(function() {

            var input = $(this).prev();

            var mapOptions = {
                center: new google.maps.LatLng(52.2, 5.6),
                zoom: 6,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(this, mapOptions);

            var marker;
            function placeMarker(loc) {
                if ( marker ) {
                    marker.setPosition(loc);
                } else {
                    marker = new google.maps.Marker({
                        position: loc,
                        map: map
                    });
                }
            }

            google.maps.event.addListener(map, 'click', function(event) {
                input.val(prepareForSql(event.latLng));
                placeMarker(event.latLng);
            });

            if (input.val().trim()) {
                loc = input.val().split(' ');
                loc = new google.maps.LatLng(loc[0],loc[1]);
                map.setCenter(loc);
                map.setZoom(13);
                placeMarker(loc);
            }

        });

    }

    function ImageInitialize() {

        $('div.spatial.image').each(function() {

            var self = this;
			var input = $(this).prev();			
            var $marker = $(this).find('.spatialMarker');
			
            function placeMarker(x, y) {
                $marker.css({                    
                    left: (x * $(self).width()) + 'px',
					top: (y * $(self).height()) + 'px'
                }).show();
            }

            $(this).on('click', function(e) {
                var offset = $(this).offset();
                var x = (e.pageX - offset.left) / $(this).width();
                var y = (e.pageY - offset.top) / $(this).height();
                input.val(x + ' ' + y);
                placeMarker(x, y);
            });

            if (input.val().trim()) {
                loc = input.val().split(' ');
                placeMarker(loc[0], loc[1]);
            }
        });

    }

    if (typeof(google) !== 'undefined' && google.maps) {
        google.maps.event.addDomListener(window, 'load', GMapsInitialize);
    }
    ImageInitialize();

});
