// Used here: http://rxx.classaxe.com/en/rna/listeners/56/map
// Global vars:
//     google.maps
//     box, center, gridColor, gridOpacity, highlight, layers, map, markers

var LMap = {
    markerGroups : null,
    init : function() {
        map = new google.maps.Map($('#map').get(0), {
            center: { lat: center.lat, lng: center.lon },
            scaleControl: true,
            zoomControl: true,
            zoom: 7
        });

        if (box[0].lat !== box[1].lat || box[0].lon !== box[1].lon) {
            map.fitBounds(
                new google.maps.LatLngBounds(
                    new google.maps.LatLng(box[0].lat, box[0].lon), //sw
                    new google.maps.LatLng(box[1].lat, box[1].lon) //ne
                )
            );
        }
        LMap.drawGrid();
        LMap.drawMarkers();
        LMap.setActions();
        setExternalLinks();
        setClippedCellTitles();
        nite.init(map);
        setInterval(function() { nite.refresh() }, 10000); // every 10s
    },

    drawGrid : function() {
        return drawGrid(map, layers);
    },

    drawMarkers : function() {
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
                '<tr id="listener_' + l.id + '" class="qth_' + (l.pri ? 'pri' : 'sec') + '" data-gmap="' + l.lat + '|' + l.lon + '">' +
                '<td class="text-nowrap" data-val="' + l.name + '">' +
                '<img style="display:block;float: left" src="' + base_image + '/map_point' + (l.pri ? 3 : 4) + '.gif" alt="' + (l.pri ? msg.qth_pri : msg.qth_sec) + '" />' +
                '<a href="' + base_url + 'listeners/' + l.id + '" class="' + (l.pri ? 'pri' : 'sec') + '" data-popup="1">' +
                l.name +
                '</a></td>' +
                '<td data-val="' + l.qth + '">' + l.qth + '</td>' +
                '<td data-val="' + l.sp + '">' + l.sp + '</td>' +
                '<td data-val="' + l.itu + '">' + l.itu + '</td>' +
                '</tr>';
            marker = new google.maps.Marker({
                id: 'point_' + l.id,
                icon: (l.pri ? icon_primary : icon_secondary),
                position: new google.maps.LatLng(l.lat, l.lon),
                title: (decodeHtmlEntities(l.name) + ': ' + decodeHtmlEntities(l.qth) + (l.sp ? ', ' + l.sp : '') + ', ' + l.itu)
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

        $('#markerlist tbody').append(html);
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
        $('#markerlist').show();
    },

    setActions : function() {
        $('#layer_grid').click(function() {
            var active, i;
            active = $('#layer_grid').prop('checked');
            for (i in layers.grid) {
                layers.grid[i].setMap(active ? map : null);
            }
        });

        $('#layer_night').click(function () {
            if ($('#layer_night').prop('checked')) {
                nite.show()
            } else {
                nite.hide();
            }
        });

        $('#layer_primary').click(function() {
            var layer_primary = $('#layer_primary');
            LMap.markerGroups.set('primary', layer_primary.prop('checked') ? map : null);
            if (layer_primary.prop('checked')) {
                $('#markerlist .qth_pri').show();
            } else {
                $('#markerlist .qth_pri').hide();
            }
        });

        $('#layer_secondary').click(function() {
            var layer_secondary = $('#layer_secondary');
            LMap.markerGroups.set('secondary', layer_secondary.prop('checked') ? map : null);
            if (layer_secondary.prop('checked')) {
                $('#markerlist .qth_sec').show();
            } else {
                $('#markerlist .qth_sec').hide();
            }
        });
        mapMarkerColSetActions();
    }
};