// Used here: http://rxx.classaxe.com/en/rna/listeners/56/map
// Global vars:
//     google.maps
//     box, center, gridColor, gridOpacity, highlight, layers, map, markers

var LMap = {
    markerGroups : null,
    init : function() {
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
        $('#layer_grid').click(function() {
            LMap.toggleGrid();
        });
        $('#layer_primary').click(function() {
            LMap.togglePrimary();
        });
        $('#layer_secondary').click(function() {
            LMap.toggleSecondary();
        });
        LMap.showGrid('gridLabel');
        LMap.showMarkers();
        setExternalLinks();
        setClippedCellTitles();
    },
    showGrid : function(overlayClass) {
        return showGrid(map, layers, overlayClass);
    },
    showMarkers : function() {
        var html, i, icon_highlight, icon_primary, icon_secondary, marker;
        if (!listeners) {
            return;
        }
        LMap.markerGroups=new google.maps.MVCObject();
        LMap.markerGroups.set('primary', map);
        LMap.markerGroups.set('secondary', map);
        LMap.markerGroups.set('highlight', map);
        icon_primary = {
            url: base_image + '/map_point3.gif',
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(5, 5)
        };
        icon_secondary = {
            url: base_image + '/map_point4.gif',
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(5, 5)
        };
        icon_highlight = {
            url: base_image + '/map_point_here.gif',
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(6, 6)
        };
        html = '';
        for (i in listeners) {
            l = listeners[i];
            html +=
                '<tr id="listener_' + l.id + '" data-gmap="' + l.lat + '|' + l.lon + '">' +
                '<td class="text-nowrap">' +
                '<img style="display:block;float: left" src="' + base_image + '/map_point' + (l.pri ? 3 : 4) + '.gif" alt="' + (l.pri ? msg.qth_pri : msg.qth_sec) + '" />' +
                '<a href="' + base_url + 'listeners/' + l.id + '" class="' + (l.pri ? 'pri' : 'sec') + '" data-popup="1">' +
                l.name +
                '</a>' +
                '</td>' +
                '<td>' + l.qth + '</td>' +
                '<td>' + l.sp + '</td>' +
                '<td>' + l.itu + '</td>' +
                '</tr>';
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(l.lat, l.lon),
                id: 'point_' + l.id,
                title: (decodeHtmlEntities(l.name) + ': ' + decodeHtmlEntities(l.qth) + (l.sp ? ', ' + l.sp : '') + ', ' + l.itu),
                icon: (l.pri ? icon_primary : icon_secondary)
            });
            marker.bindTo('map', LMap.markerGroups, (l.pri ? 'primary' : 'secondary'));
            markers.push(marker);
        }

        for (i in markers) {
            markers[i].addListener('mouseover', function() {
                $('#listener_' + this.id.split('_')[1]).css('background', '#ffff00');
            });
            markers[i].addListener('mouseout', function () {
                $('#listener_' + this.id.split('_')[1]).css('background', '');
            });
            markers[i].addListener('click', function () {
                $('#listener_' + this.id.split('_')[1]).find('a').trigger('click');
            });
        }

        $('.results tbody').append(html);
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
        $('.no-results').hide();
        $('.results').show();
    },
    toggle : function(layer) {
        var active, i;
        if (!Array.isArray(layers[layer])) {
            active = (layers[layer].getMap() !== null);
            layers[layer].setMap(active ? null : map);
            return;
        }
        active = (layers[layer][0].getMap() !== null);
        for (i in layers[layer]) {
            layers[layer][i].setMap(active ? null : map);
        }
    },
    toggleGrid : function() {
        LMap.toggle('grid');
    },
    togglePrimary : function() {
        LMap.markerGroups.set('primary', $('#layer_primary').prop('checked') ? map : null);
    },
    toggleSecondary : function() {
        LMap.markerGroups.set('secondary', $('#layer_secondary').prop('checked') ? map : null);
    }
};