function drawGrid(map, layers, squares) {
    TxtOverlay =    initMapsTxtOverlay();
    var i, la, laf, lo, lof;
    for (la=0; la<180; la+=10) {
        layers.grid.push(
            new google.maps.Polyline({
                path: [{lat: (la-90), lng: -180}, {lat:(la-90), lng: 0}, {lat: (la-90), lng: 180}],
                geodesic: false,
                strokeColor: gridColor,
                strokeOpacity: gridOpacity,
                strokeWeight: 0.5
            })
        );
        if (typeof squares !== 'undefined' && squares) {
            for(laf=0; laf<10; laf++) {
                layers.grid.push(
                    new google.maps.Polyline({
                        path: [{lat: laf + (la-90), lng: -180}, {lat: laf + (la-90), lng: 0}, {lat: laf +  (la-90), lng: 180}],
                        geodesic: false,
                        strokeColor: gridColor,
                        strokeOpacity: gridOpacity,
                        strokeWeight: 0.25
                    })
                );
            }
        }
    }
    for (lo=0; lo<360; lo+=20) {
        layers.grid.push(
            new google.maps.Polyline({
                path: [{lat: 85.05, lng: lo}, {lat: -85.05, lng: lo}],
                geodesic: false,
                strokeColor: gridColor,
                strokeOpacity: gridOpacity,
                strokeWeight: 0.5
            })
        );
        if (typeof squares !== 'undefined' && squares) {
            for (lof = 0; lof < 20; lof += 2) {
                layers.grid.push(
                    new google.maps.Polyline({
                        path: [{lat: 85.05, lng: lo + lof}, {lat: -85.05, lng: lo + lof}],
                        geodesic: false,
                        strokeColor: gridColor,
                        strokeOpacity: gridOpacity,
                        strokeWeight: 0.25
                    })
                );

            }
        }
    }
    for (la=10; la<170; la+=10) {
        for (lo = 0; lo < 360; lo += 20) {
            layers.grid.push(
                new TxtOverlay(
                    new google.maps.LatLng(la -90 + 5.17,lo -180 + 9.625),
                    String.fromCharCode((lo/20) +65) + String.fromCharCode((la/10) +65),
                    'gridLabel',
                    map
                )
            );
        }
    }
    for (i in layers.grid) {
        layers.grid[i].setMap(map);
    }
}

function initMapsTxtOverlay() {
    // Thanks to Michal, 'UX Lead at Alphero' for this custom text overlay code
    // Ref: https://stackoverflow.com/a/3955258/815790

    function TxtOverlay(pos, txt, cls, map) {
        this.pos = pos;
        this.txt_ = txt;
        this.cls_ = cls;
        this.map_ = map;
        this.div_ = null;
        this.setMap(map);
    }

    TxtOverlay.prototype = new google.maps.OverlayView();

    TxtOverlay.prototype.onAdd = function() {
        var div, overlayProjection, panes, position;
        div = document.createElement('DIV');
        div.className = this.cls_;
        div.innerHTML = this.txt_;
        this.div_ = div;
        overlayProjection = this.getProjection();
        position = overlayProjection.fromLatLngToDivPixel(this.pos);
        div.style.left = position.x + 'px';
        div.style.top = position.y + 'px';
        panes = this.getPanes();
        panes.floatPane.appendChild(div);
    };

    TxtOverlay.prototype.draw = function() {
        var div, position, overlayProjection;
        overlayProjection = this.getProjection();
        position = overlayProjection.fromLatLngToDivPixel(this.pos);
        div = this.div_;
        div.style.left = position.x + 'px';
        div.style.top = position.y + 'px';
    };

    TxtOverlay.prototype.onRemove = function() {
        this.div_.parentNode.removeChild(this.div_);
        this.div_ = null;
    };

    return TxtOverlay;
}

function mapMarkerColSetActions() {
    $('#markerlist thead th.sort').on('click', function(){
        var i, initial, me, sortBy, sortOrder, sortType;
        me = $(this);
        i = me.attr('id').split('|');
        sortBy = i[0];
        sortOrder = i[1];
        sortType = me.data('type');
        if (sortBy === SMap.sortBy) {
            sortOrder = (SMap.sortOrder === 'a' ? 'd' : 'a');
            me.attr('id', sortBy + '|' + SMap.sortOrder);
        } else {
            me.attr('id', sortBy + '|a');
        }
        SMap.sortBy = sortBy;
        SMap.sortOrder = sortOrder;
        console.log('idx ' + sortBy + ' order ' + sortOrder + ' of type ' + sortType);
        mapMarkerColSort(sortBy, sortOrder, sortType);
    });
}

function mapMarkerColSort(idx, dir, type) {
    var cols =  $('#markerlist thead tr th');
    var col =   $('#markerlist thead tr th:eq(' + idx + ')')
    var tbody = $('#markerlist tbody');

    cols.removeClass('sorted');
    col.addClass('sorted');

    tbody.find('tr').sort(function (a, b) {
        var tda = $(a).find('td:eq(' + idx +')').data('val');
        var tdb = $(b).find('td:eq(' + idx +')').data('val');
        if (type === 'number') {
            tda = parseFloat(tda);
            tda = parseFloat(tda);
        }
        switch(dir) {
            case 'a':
                return (tda > tdb ? 1 : (tda < tdb ? -1 : 0));
            case 'd':
                return (tdb > tda ? 1 : (tdb < tda ? -1 : 0));
        }
    }).appendTo(tbody);
}