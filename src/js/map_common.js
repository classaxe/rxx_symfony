function showGrid(map, layers, overlayClass) {
    var i, la, lo;
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
    }
    for (lo=0; lo<360; lo+=20) {
        layers.grid.push(
            new google.maps.Polyline({
                path: [{lat:85.05, lng: lo}, {lat:-85.05, lng: lo}],
                geodesic: false,
                strokeColor: gridColor,
                strokeOpacity: gridOpacity,
                strokeWeight: 0.5
            })
        );
    }
    for (la=10; la<170; la+=10) {
        for (lo = 0; lo < 360; lo += 20) {
            layers.grid.push(
                new TxtOverlay(
                    new google.maps.LatLng(la -90 +5,lo -180 + 10),
                    String.fromCharCode((lo/20) +65) + String.fromCharCode((la/10) +65),
                    overlayClass,
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

function showListenerMapLocatorForm() {
    if (!$('#rx_map').height()) {
        return window.setTimeout(function(){ showListenerMapLocatorForm(); }, 100);
    }
    $('#form').show();
}

function setListenerMapLocatorPos(xpos, ypos) {
    if (xpos === 0 && ypos === 0) {
        return;
    }
    $('#cursor').css({
        left : (xpos - 10) + 'px',
        top : (ypos - 10) + 'px',
        display: 'block'
    });
}

function setListenerMapLocatorFormActions() {
    $('#form_mapX').change(function(e) {
        xpos = parseInt($('#form_mapX').val());
        setListenerMapLocatorPos(xpos, ypos);
    });

    $('#form_mapY').change(function(e) {
        ypos = parseInt($('#form_mapY').val());
        setListenerMapLocatorPos(xpos, ypos);
    });

    $('#x_sub').click(function(e) {
        var val = parseInt($('#form_mapX').val());
        if (val > 0) {
            $('#form_mapX')
                .val(val - 1)
                .trigger('change');
        }
    });

    $('#x_add').click(function(e) {
        var val = parseInt($('#form_mapX').val());
        $('#form_mapX')
            .val(val + 1)
            .trigger('change');
    });

    $('#y_sub').click(function(e) {
        var val = parseInt($('#form_mapY').val());
        if (val > 0) {
            $('#form_mapY')
                .val(val - 1)
                .trigger('change');
        }
    });

    $('#y_add').click(function(e) {
        var val = parseInt($('#form_mapY').val());
        $('#form_mapY')
            .val(val + 1)
            .trigger('change');
    });

    $('#form_reset').click(function(e) {
        e.preventDefault();
        form = e.toElement.form;
        form.reset();
        xpos = $('#form_mapX').val();
        ypos = $('#form_mapY').val();
        setListenerMapLocatorPos(xpos, ypos);
    });
}