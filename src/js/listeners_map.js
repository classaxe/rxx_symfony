// Used here: http://rxx.classaxe.com/en/rna/listeners/56/map
function initListenersMap() {
    var markerGroups;
    // Global vars:
    //     google.maps
    //     box, center, gridColor, gridOpacity, layers, map
    TxtOverlay =    initMapsTxtOverlay();

    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: center.lat, lng: center.lon },
        scaleControl: true,
        zoomControl: true,
        zoom: 2
    });

    map.fitBounds(
        new google.maps.LatLngBounds(
            new google.maps.LatLng( box[0].lat, box[0].lon), //sw
            new google.maps.LatLng( box[1].lat, box[1].lon) //ne
        )
    );

    function showMarkers() {
        var html, i, icon_highlight, icon_primary, icon_secondary, marker;
        if (!listeners) {
            return;
        }
        markerGroups=new google.maps.MVCObject();
        markerGroups.set('primary', map);
        markerGroups.set('secondary', map);
        markerGroups.set('highlight', map);
        icon_primary = {
            url: base_image + "/map_point3.gif",
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(5, 5)
        };
        icon_secondary = {
            url: base_image + "/map_point4.gif",
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(5, 5)
        };
        icon_highlight = {
            url: base_image + "/map_point_here.gif",
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(6, 6)
        };
        html = '';
        for (i in listeners) {
            l = listeners[i];
            html +=
                '<tr data-gmap="' + l.lat + '|' + l.lon + '">' +
                '<td>' + (l.pri ? '<strong>' : '&nbsp; &nbsp; ') +
                    '<a href="' + base_url + 'listeners/' + l.id + '" data-popup="1">' + l.name + '</a>' +
                    (l.pri ? '</strong>' : '') +
                '</td>' +
                '<td>' + l.qth +'</td>' +
                '<td>' + l.sp +'</td>' +
                '<td>' + l.itu +'</td>' +
                '</tr>';

            marker=new google.maps.Marker({
                position: new google.maps.LatLng(l.lat, l.lon),
                title: (decodeHtmlEntities(l.name) + ': ' + decodeHtmlEntities(l.qth) + (l.sp ? ', ' + l.sp : '') + ', ' + l.itu),
                icon: (l.pri ? icon_primary : icon_secondary)
            });
            marker.bindTo('map', markerGroups, (l.pri ? 'primary' : 'secondary'));
        }
        $('.results tbody').append(html);
        $('.no-results').hide();
        $('.results').show();

        setExternalLinks();
        setClippedCellTitles();

        $('tr[data-gmap]')
            .mouseover(function() {
                var coords = $(this).data('gmap').split('|');
                highlight = new google.maps.Marker({
                    position: new google.maps.LatLng(coords[0], coords[1]),
                    map: map,
                    icon: icon_highlight
                });
            })
            .mouseout(function() {
                highlight.setMap(null);
            });
    }

    function toggle(layer) {
        var active;
        if (!Array.isArray(layers[layer])) {
            active = (layers[layer].getMap() !== null);
            layers[layer].setMap(active ? null : map);
            return;
        }
        active = (layers[layer][0].getMap() !== null);
        for (var i in layers[layer]) {
            layers[layer][i].setMap(active ? null : map);
        }
    }

    function toggleGrid() {
        toggle('grid');
    }

    function togglePrimary() {
        markerGroups.set('primary', $('#layer_primary').prop('checked') ? map : null);
    }

    function toggleSecondary() {
        markerGroups.set('secondary', $('#layer_secondary').prop('checked') ? map : null);
    }

    google.maps.event.addDomListener(document.getElementById('layer_grid'), 'click', function() {
        toggleGrid();
    });

    google.maps.event.addDomListener(document.getElementById('layer_primary'), 'click', function() {
        togglePrimary();
    });

    google.maps.event.addDomListener(document.getElementById('layer_secondary'), 'click', function() {
        toggleSecondary();
    });

    showGrid(map, layers, 'gridLabel');
    showMarkers();
}