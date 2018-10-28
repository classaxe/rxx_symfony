/* [ Set css styles for signal type checkboxes ] */
function setFormTypesStyles() {
    $("div#form_types div input").each(function() {
        $(this).parent().attr('class', $(this).attr('class'))
    });
}

/* [ Ensure that at least one option is checked for signal type checkboxes ] */
function setFormTypesDefault() {
    if ($('div#form_types div :checkbox:checked').length == 0) {
        $('div#form_types div :checkbox[value=type_NDB]').prop('checked', true);
    }
}

/* [ Enable 'All' checkbox to select / unselect all signal types ] */
function setFormTypesAllAction() {
    $('div#form_types div :checkbox[value=type_ALL]').click(function () {
        $('div#form_types div :checkbox').prop('checked', $(this).prop("checked"));
    });
}

/* [ Enable Country change to resubmit form ] */
function setFormCountryAction() {
    $('select#form_country').change(function () {
        $('#form_submit').click();
    });
}

/* [ Enable Region change to resubmit form ] */
function setFormRegionAction() {
    $('select#form_region').change(function () {
        $('#form_submit').click();
    });
}

/* [ Enable sort actions for all sortable columns ] */
function setColumnSortActions() {
    $('table.results thead tr th[id]').each(function() {
        $(this).click(function () {
            var column = this.id.split('_')[0];
            var dir = this.id.split('_')[1];
            if ($(this).hasClass('sorted')) {
                dir = ($('#form_order').val()=='a' ? 'd' : 'a');
            }
            $('#form_sort').val(column);
            $('#form_order').val(dir);
            $('form[name="form"]').submit();
        })
    });
}

/* [ Add title for td cells having class 'clipped' ] */
function setClippedCellTitles() {
    $('td.clipped').each(function() {
        $(this).attr('title', $(this).text().trim());
    });
}

/* [ Indicate which column is sorted by checking hidden fields on form ] */
function setColumnSortedClass() {
    $('table.results thead tr th').each(function() {
        if (this.id.split('_')[0] == $('#form_sort').val()) {
            $(this).append($('#form_order').val() == 'd' ? ' &#9662;' : ' &#9652;');
            $(this).addClass('sorted');
        }
    });
}

/* [ Set links to open in external or popup window if rel attribute is set ] */
function setExternalLinks() {
    $('a[rel="external"]').attr('target', '_blank');
    $('a[data-popup]').click(function() {
        var args = $(this).data('popup').split('|');
        window.open(this.href, args[0], args[1]);
        return false;
    });
}

function setEmailLinks() {
    $('a[data-contact]').each(function() {
        var link = $(this).attr('data-contact').split('').reverse().join('').trim().replace('#','@');
        $(this).attr('href', link);
        $(this).removeAttr('data-contact');
    });
}

function setPagingActions() {
    var limit =     $('select#form_limit');
    var page_ctl =  $('#form_page_ctl');
    var options =   $('#form_page_ctl option');
    var idx =       page_ctl.prop('selectedIndex');
    var prev =      $('#form_prev');
    var next =      $('#form_next');

    if (limit.val() === '-1') {
        prev.hide();
        next.hide();
        page_ctl.hide();
    }

    limit.change(
        function() {
            var form =      $('form[name="form"]');
            var limit =     $('select#form_limit');
            var page =      $('#form_page');
            var page_ctl =  $('select#form_page_ctl');
            var options =   $('select#form_page_ctl option');
            var idx =       page_ctl.prop('selectedIndex');
            var prev =      $('#form_prev');
            var next =      $('#form_next');
            options.eq(0).prop('selected', true);
            page_ctl.prop('selectedIndex', 0);
            if (limit.val() !== "-1") {
                options.eq(0).prop('text', '1-' + limit.val());
                prev.prop('disabled', 'disabled');
                next.prop('disabled', 'disabled');
            } else {
                page_ctl.hide();
                prev.hide();
                next.hide();
            }
            page_ctl.prop('selectedIndex', 0);
            page.val(0);
            page_ctl.removeAttr('name');
            form.submit();
        }
    );
    if (idx > 0) {
        prev.click(
            function () {
                var form =      $('form[name="form"]');
                var page =      $('#form_page');
                var page_ctl =  $('#form_page_ctl');
                var options =   $('#form_page_ctl option');
                var idx =       page_ctl.prop('selectedIndex');
                var prev =      $('#form_prev');
                var next =      $('#form_next');
                prev.prop('disabled', 'disabled');
                next.prop('disabled', 'disabled');
                options.eq(idx - 1).prop('selected', true);
                page_ctl.prop('selectedIndex', idx - 1);
                page.val(idx - 1);
                page_ctl.removeAttr('name');
                form.submit();
                return false;
            }
        );
    } else {
        prev.prop('disabled', 'disabled');
    }

    if (idx + 1 < options.length) {
        next.click(
            function() {
                var form =      $('form[name="form"]');
                var page =      $('#form_page');
                var page_ctl =  $('select#form_page_ctl');
                var options =   $('select#form_page_ctl option');
                var idx =       page_ctl.prop('selectedIndex');
                var prev =      $('#form_prev');
                var next =      $('#form_next');
                prev.prop('disabled', 'disabled');
                next.prop('disabled', 'disabled');
                options.eq(idx + 1).prop('selected', true);
                page_ctl.prop('selectedIndex', idx + 1);
                page.val(idx + 1);
                page_ctl.removeAttr('name');
                form.submit();
                return false;
            }
        );
    } else {
        next.prop('disabled', 'disabled');
    }

    page_ctl.change(
        function() {
            var form =      $('form[name="form"]');
            var page =      $('#form_page');
            var page_ctl =  $('#form_page_ctl');
            var idx =       page_ctl.prop('selectedIndex');
            var prev =      $('#form_prev');
            var next =      $('#form_next');
            prev.prop('disabled', 'disabled');
            next.prop('disabled', 'disabled');
            page.val(idx);
            page_ctl.removeAttr('name');
            form.submit();
        }
    );
}

var gridColor, gridOpacity, layers = [], map;

layers['grid'] = [];
gridColor="#808080";
gridOpacity=0.5;

function initListenerSignalsMap() {
    var qthInfo, signalType, type;
    // Global vars:
    //     listener[source, logLatest, lat, lng, name, qth, types]
    //     google.maps
    //     gridColor, gridOpacity, layers, map

    TxtOverlay =    initMapsTxtOverlay();
    ShowGrid =      initMapsShowGrid();

    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: listener.lat, lng: listener.lng },
        scaleControl: true,
        zoomControl: true,
        zoom: 2
    });

    layers['qth'] = new google.maps.Marker({
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

    layers['qth'].addListener('click', function() {
        qthInfo.open(map, layers['qth']);
    });

    ShowGrid(map, 'gridLabel');

    // Signal Types overlays
    for (type in listener.types) {
        signalType = listener.types[type];
        layers[signalType + '_0'] = new google.maps.KmlLayer({
            url: listener.source + '/' + signalType + '/0?v=a' + listener.logLatest,
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

    function toggleQth() {
        if (layers['qth'].getMap() == null) {
            layers['qth'].setMap(map);
        } else {
            layers['qth'].setMap(null);
        }
    }

    function toggleGrid() {
        var i;
        if (layers['grid'][0].getMap() == null) {
            for(i in layers['grid']) {
                layers['grid'][i].setMap(map)
            }
        } else {
            for(i in layers['grid']) {
                layers['grid'][i].setMap(null)
            }
        }
    }
    google.maps.event.addDomListener(document.getElementById('layer_grid'), 'click', function(evt) {
        toggleGrid();
    });
    google.maps.event.addDomListener(document.getElementById('layer_inactive'), 'click', function(evt) {
        toggleInactive();
    });
    google.maps.event.addDomListener(document.getElementById('layer_qth'), 'click', function(evt) {
        toggleQth();
    });
    listener.types.forEach(function(type){
        google.maps.event.addDomListener(document.getElementById('layer_' + type), 'click', function (evt) {
            toggleLayer( type );
        });
    });
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

function initMapsShowGrid() {
    function showGrid(map, overlayClass) {
        var i, la, lo;
        for (la=0; la<180; la+=10) {
            layers['grid'].push(
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
            layers['grid'].push(
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
                layers['grid'].push(
                    new TxtOverlay(
                        new google.maps.LatLng(la -90 +5,lo -180 + 10),
                        String.fromCharCode((lo/20) +65) + String.fromCharCode((la/10) +65),
                        overlayClass,
                        map
                    )
                );
            }
        }
        for (i in layers['grid']) {
            layers['grid'][i].setMap(map);
        }
    }

    return showGrid;
}