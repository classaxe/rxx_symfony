var signalsMap = {
    map: null,
    icons: {},
    infoWindow: null,
    items: [],
    options: {},

    init: function() {
        TxtOverlay =    initMapsTxtOverlay();
        signalsMap.items = signals;
        var icons = [ 'dgps', 'dsc', 'hambcn', 'navtex', 'ndb', 'time', 'other' ];
        var states = [ 0, 1 ];
        for (var i in icons) {
            for (var j in states) {
                var pin = base_image + '/pins/' + icons[i] + '_' + states[j] + '.png';
                signalsMap.icons[icons[i] + '_' + states[j]] =
                    new google.maps.MarkerImage(pin, new google.maps.Size(12, 20));
            }
        }
        signalsMap.options = {
            'zoom': 2,
            'center': new google.maps.LatLng(20, 0),
            'mapTypeId': google.maps.MapTypeId.ROADMAP
        };
        signalsMap.map = new google.maps.Map(document.getElementById('map'), signalsMap.options);
        signalsMap.infoWindow = new google.maps.InfoWindow();
        signalsMap.showMarkers();

        showGrid(signalsMap.map, layers, 'gridLabel');

    },

    markerClickFunction: function(s, latlng) {
        return function(e) {
            e.cancelBubble = true;
            e.returnValue = false;
            if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            }
            var title = s.khz+' '+s.call+' ';
            var infoHtml =
                '<div class="map_info">' +
                '  <h3><a href="./rna/signal_info/' + s.id + '" target="_blank">' + title + '</a></h3>' +
                '  <table class="info-body">' +
                (typeof s.logged !== 'undefined' ? '    <tr><th>Received</th><td><b>' + (s.logged ? 'Yes' : 'No') + '</b></td></tr>' : '') +
                '    <tr><th>ID</th><td>'+s.call + '</td></tr>' +
                '    <tr><th>KHz</th><td>'+s.khz + '</td></tr>' +
                '    <tr><th>Type</th><td>'+s.type + '</td></tr>' +
                (s.pwr !== '0' ? '    <tr><th>Power</th><td>'+s.pwr + 'W</td></tr>' : '') +
                '    <tr><th>\'Name\' / QTH</th><td>'+s.qth + (s.sp ? ', ' + s.sp : '') + ', ' + s.itu + '</td></tr>' +
                (s.gsq ? '    <tr><th>GSQ</th><td><a href="." onclick="popup_map('+s.id+','+s.lat+','+s.lon+');return false;" title="Show map (accuracy limited to nearest Grid Square)">'+s.gsq+'</a></td></tr>' : '') +
                '    <tr><th>Lat / Lon</th><td>' + s.lat + ', ' + s.lon + '</td></tr>' +
                (s.usb || s.lsb ? '    <tr><th>Sidebands</th><td>' + (s.lsb ? 'LSB: ' + s.lsb : '') + (s.usb ? (s.lsb ? ', ' : '') + ' USB: ' + s.usb : '') + '</td></tr>' : '') +
                (s.sec || s.fmt ? '    <tr><th>Secs / Format</th><td>' + (s.sec ? s.sec + ' sec' : '') + (s.sec && s.fmt ? ', ' : '') + s.fmt + '</td></tr>' : '') +
                '    <tr><th>Last Logged</th><td>' + s.heard + '</td></tr>' +
                '    <tr><th>Heard In</th><td>' + s.heard_in + '</td></tr>' +
                '  </table>' +
                '</div>';
            signalsMap.infoWindow.setContent(infoHtml);
            signalsMap.infoWindow.setPosition(latlng);
            signalsMap.infoWindow.open(signalsMap.map);
        };
    },

    showMarkers: function() {
        var fn, i, item, latLng, marker, panel, s, title, titleText;
        signalsMap.markers = [];
        panel = document.getElementById('markerlist');
        if (signalsMap.items.length === 0) {
            return;
        }
        panel.innerHTML = '';

        for (i = 0; i < signalsMap.items.length; i++) {
            s = signalsMap.items[i];
            titleText =
                '<b>' + (typeof s.logged !== 'undefined' ? (s.logged ? '&#9745;' : '&#9744;') + ' ' : '') + s.khz+' '+s.call + '</b> ' +
                s.qth + (s.sp ? ', ' + s.sp : '') + ', ' + s.itu;
            item = document.createElement('DIV');
            title = document.createElement('A');
            title.href = '#';
            title.className = 'title type_' + s.className;
            title.innerHTML = titleText;
            if (typeof s.logged !== 'undefined' && s.logged) {
                item.className = 'logged';
                item.title = 'Received';
            }
            item.appendChild(title);
            panel.appendChild(item);
            latLng = new google.maps.LatLng(s.lat, s.lon);
            marker = new google.maps.Marker({
                'title' :  strip_tags(s.khz + ' ' + s.call),
                'position': latLng,
                'icon': signalsMap.icons[s.icon + '_' + (s.active ? 1 : 0)]
            });
            fn = signalsMap.markerClickFunction(s, latLng);
            google.maps.event.addListener(marker, 'click', fn);
            google.maps.event.addDomListener(title, 'click', fn);
            marker.setMap(signalsMap.map);
        }
    }
};