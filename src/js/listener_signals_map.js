// Used here: http://rxx.classaxe.com/en/rna/listeners/56/map
var LSMap = {
    init : function() {
        var qthInfo, signalType, type;
        // Global vars:
        //     listener[source, logLatest, lat, lng, name, qth, types]
        //     google.maps
        //     gridColor, gridOpacity, layers, map

        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: listener.lat, lng: listener.lng },
            scaleControl: true,
            zoomControl: true,
            zoom: 2
        });

        layers.qth = new google.maps.Marker({
            position: { lat: listener.lat, lng: listener.lng },
            map: map,
            icon: {
                scaledSize: new google.maps.Size(30,30),
                url: "//maps.google.com/mapfiles/kml/pushpin/red-pushpin.png"
            },
            title: listener.name
        });

        qthInfo = new google.maps.InfoWindow({
            content:
                "<h2>" + listener.name + "</h2>" +
                "<p>" + listener.qth + "</p>"
        });

        layers.qth.addListener('click', function() {
            qthInfo.open(map, layers.qth);
        });

        LSMap.drawGrid();

        // Signal Types overlays
        for (type in listener.types) {
            signalType = listener.types[type];
            layers[signalType + '_0'] = new google.maps.KmlLayer({
                url: listener.source + '/' + signalType + '/0?v=a' +
                    listener.logLatest + '_' + new Date().toJSON().substring(0,10),
                preserveViewport: true,
                map: map
            });
            layers[signalType + '_1'] = new google.maps.KmlLayer({
                url: listener.source + '/' + signalType + '/1?v=a' + listener.logLatest,
                preserveViewport: true,
                map: map
            });
        }

        function toggleInactive() {
            for (type in listener.types) {
                signalType = listener.types[type];
                if (document.getElementById('layer_' + signalType).checked) {
                    if (document.getElementById('layer_inactive').checked) {
                        layers[signalType + '_0'].setMap(map);
                    } else {
                        layers[signalType + '_0'].setMap(null);
                    }
                }
            }
        }

        function toggleLayer(type) {
            if (layers[type + '_1'].getMap() == null) {
                layers[type + '_1'].setMap(map);
                if (document.getElementById('layer_inactive').checked) {
                    layers[type + '_0'].setMap(map);
                } else {
                    layers[type + '_0'].setMap(null);
                }
            } else {
                layers[type + '_0'].setMap(null);
                layers[type + '_1'].setMap(null);
            }
        }

        $('#layer_inactive').click(function() {
            toggleInactive();
        });

        listener.types.forEach(function(type){
            $('#layer_' + type).click(function(){
                toggleLayer( type );
            });
        });

        LSMap.setActions();
    },

    drawGrid : function() {
        return drawGrid(map, layers);
    },

    setActions : function() {
        $('#layer_qth').click(function() {
            layers['qth'].setMap($('#layer_qth').prop('checked') ? map : null);
        });

        $('#layer_grid').click(function() {
            var active, i;
            active = $('#layer_grid').prop('checked');
            for (i in layers.grid) {
                layers.grid[i].setMap(active ? map : null);
            }
        });

        // $('#layer_inactive').click(function() {
        //     LMap.toggleInactive();
        // });
    }
};