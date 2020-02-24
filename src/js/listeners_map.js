// Used here: http://rxx.classaxe.com/en/rna/listeners/56/map
function initListenersMap() {
    var markerGroups;
    // Global vars:
    //     google.maps
    //     gridColor, gridOpacity, layers, map
    TxtOverlay =    initMapsTxtOverlay();

    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 30, lng: 0 },
        scaleControl: true,
        zoomControl: true,
        zoom: 2
    });

    function showMarkers() {
        var html, i, marker;
        if (!listeners) {
            return;
        }
        markerGroups=new google.maps.MVCObject();
        markerGroups.set('primary', map);
        markerGroups.set('secondary', map);
        var icon_primary = {
            url: base_image + "/map_point3.gif", // url
            origin: new google.maps.Point(0,0), // origin
            anchor: new google.maps.Point(0, 0) // anchor
        };
        var icon_secondary = {
            url: base_image + "/map_point4.gif", // url
            origin: new google.maps.Point(0,0), // origin
            anchor: new google.maps.Point(0, 0) // anchor
        };
        html = '';
        for (i in listeners) {
            l = listeners[i];
            html +=
                '<tr>' +
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