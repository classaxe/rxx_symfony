function changeShowMode(mode) {
    switch (mode) {
        case 'list':
            if ($('#form_show').prop('selectedIndex') !== 0) {
                $('#form_show').prop('selectedIndex', 0);
                $('#form_submit').click();
            }
            break;
        case 'map':
            if ($('#form_show').prop('selectedIndex') !== 1) {
                $('#form_show').prop('selectedIndex', 1);
                $('#form_submit').click();
            }
            break;
    }
}

function getlimitOptions(max, value, defaultLimit)
{
    var values = [10, 20, 50, 100, 200, 500, 1000, 2000, 5000, 100000, 20000, 50000, 100000];
    var out = "";
    for (var i in values) {
        if (values[i] > max) {
            continue;
        }
        out +=
            "<option value=\"" + values[i] + "\"" +
            (parseInt(value) === values[i] ? " selected=\"selected\"" : "") +
            ">" +
            values[i] + ' results' +
            "</option>";
    }
    out +=
        "<option value=\"" + (max > values[0] ? -1 : defaultLimit) + "\"" +
        (parseInt(value) === -1 ? " selected=\"selected\"" : "") +
        ">All results</option>";
    return out;
}

function exportSignallistExcel() {
    if (!confirm(
            (msg.export + "\n\n" + msg.time + "\n\n" + msg.options + "\n\n" + msg.continue)
            . replace('\:system', system.toUpperCase())
            . replace('\:format', msg.excel)
        )
    ) {
        return;
    }
    alert('OK');
}

function exportSignallistILG() {
    if (!confirm(
        (msg.export + "\n\n" + msg.nooptions + "\n\n" + msg.continue)
            . replace('\:system', system.toUpperCase())
            . replace('\:format', msg.ilg)
    )
    ) {
        return;
    }
    alert('OK');
}
function exportSignallistPDF() {
    if (!confirm(
        (msg.export + "\n\n" + msg.time + "\n\n" + msg.options + "\n\n" + msg.continue)
            . replace('\:system', system.toUpperCase())
            . replace('\:format', msg.pdf)
    )
    ) {
        return;
    }
    alert('OK');
}

function getMetar(decoded) {
    window.open('https://www.aviationweather.gov/metar/data' +
        '?ids='+$('#form_icao').val() +
        '&format=' +(decoded ? 'decoded' : 'raw') +
        '&taf=on' +
        '&layout=off' +
        '&hours='+$('#form_hours').val(),
        'popMETAR'+decoded,
        'scrollbars=1,resizable=1,location=1'
    );
}

function getPagingOptions(total, limit, page)
{
    var out = "";
    pages = total/limit;
    for (var i=0; i < pages; i++) {
        out +=
            "<option value=\"" + i + "\"" +
            (parseInt(page) === i ? " selected=\"selected\"" : "") +
            ">" +
            (1 + (i*limit)) +
            '-' +
            (((i+1) * limit) > total ? total : ((i+1) * limit)) +
            "</option>";
    }
    return out;
}

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
        if (layers.qth.getMap() == null) {
            layers.qth.setMap(map);
        } else {
            layers.qth.setMap(null);
        }
    }

    function toggleGrid() {
        var i;
        if (layers.grid[0].getMap() == null) {
            for(i in layers.grid) {
                layers.grid[i].setMap(map);
            }
        } else {
            for(i in layers.grid) {
                layers.grid[i].setMap(null);
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

function initMapsShowGrid() {
    function showGrid(map, overlayClass) {
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
    return showGrid;
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

/* [ Add title for td cells having class 'clipped' ] */
function setClippedCellTitles() {
    $('td.clipped').each(function() {
        $(this).attr('title', $(this).text().trim());
    });
}

/* [ Enable sort actions for all sortable columns ] */
function setColumnSortActions() {
    $('table.results thead tr th[id]').each(function() {
        $(this).click(function () {
            var column = this.id.split('|')[0];
            var dir = this.id.split('|')[1];
            if ($(this).hasClass('sorted')) {
                dir = ($('#form_order').val()=='a' ? 'd' : 'a');
            }
            $('#form_sort').val(column);
            $('#form_order').val(dir);
            $('form[name="form"]').submit();
        });
    });
}

/* [ Indicate which column is sorted by checking hidden fields on form ] */
function setColumnSortedClass() {
    $('table.results thead tr th').each(function() {
        if (this.id.split('|')[0] == $('#form_sort').val()) {
            $(this).append($('#form_order').val() == 'd' ? ' &#9662;' : ' &#9652;');
            $(this).addClass('sorted');
        }
    });
}

function setEmailLinks() {
    $('a[data-contact]').each(function() {
        var link = $(this).attr('data-contact').split('').reverse().join('').trim().replace('#','@');
        $(this).attr('href', link);
        $(this).removeAttr('data-contact');
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

/* [ Enable Country change to resubmit form ] */
function setFormCountryAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('select#form_country').change(function () {
            $('#form_submit').click();
        });
    } else {
        $('select#form_country').off('change');
    }
}

function setFormListenerOptionsStyle() {
    var listener = $('#form_listener');
    listener.children().each(function() {
        if ($(this).val() === '') {
            $(this).addClass('all');
        } else if ($(this).text().substr(0,2) === '. ') {
            $(this).text('\xa0' + $(this).text().substr(1));
            $(this).addClass('secondaryQth');
        } else {
            $(this).addClass('primaryQth');
        }
    });
}

function setFormRangeAction() {
    $('#form_range_gsq').on('keyup', function(e) {
        var disabled = ($('#form_range_gsq').val().length < 6);
        $('#form_range_min').attr('disabled', disabled);
        $('#form_range_max').attr('disabled', disabled);
    });
    $('#form_range_min').on('keyup', function(e) {
        var disabled = ($('#form_range_min').val().length === 0 && $('#form_range_max').val().length === 0);
        $('#form_range_units').attr('disabled', disabled);
    });
    $('#form_dx_max').on('keyup', function(e) {
        var disabled = ($('#form_range_min').val().length === 0 && $('#form_range_max').val().length === 0);
        $('#form_range_units').attr('disabled', disabled);
    });
    $('#form_range_gsq').trigger('keyup');
    $('#form_range_min').trigger('keyup');
}

function setFormRangeUnitsDefault() {
    if ($('fieldset#form_range_units div :radio:checked').length == 0) {
        $('fieldset#form_range_units div :radio[value=km]').prop('checked', true);
    }
}

function setFormListenerInvertDefault() {
    if ($('fieldset#form_listener_invert div :radio:checked').length == 0) {
        $('fieldset#form_listener_invert div :radio[value=0]').prop('checked', true);
    }
}

function setFormHeardInModDefault() {
    if ($('fieldset#form_heard_in_mod div :radio:checked').length == 0) {
        $('fieldset#form_heard_in_mod div :radio[value="any"]').prop('checked', true);
    }
}

function setDatePickers() {
    $.datepicker.setDefaults({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        yearRange: '1980:+0'
    });
    $('.js-datepicker').datepicker({
    });
}
/* [ Enable Region change to resubmit form ] */
function setFormRegionAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('select#form_region').change(function () {
            $('#form_submit').click();
        });
    } else {
        $('select#form_region').off('change');
    }
}

function setFormResetAction(form) {
    switch (form) {
        case 'signals':
            $('button[type="reset"]').click(function () {
                setFormRegionAction(false);

                $('#form_types div :checkbox').prop('checked', false);
                $('#form_types div :checkbox[value=type_NDB]').prop('checked', true);
                $('#form_call').val('');
                $('#form_khz_1').val('');
                $('#form_khz_2').val('');
                $('#form_channels').prop('selectedIndex', 0);
                $('#form_active').prop('selectedIndex', 0);
                $('#form_show').prop('selectedIndex', 0);
                $('#form_offsets').prop('selectedIndex', 0);

                $('#form_states').val('');
                $('#form_sp_itu_clause').prop('selectedIndex', 0);
                $('#form_countries').val('');
                $('#form_region').prop('selectedIndex', 0);
                $('#form_gsq').val('');
                $('#form_range_gsq').val('');
                $('#form_range_min').val('');
                $('#form_range_max').val('');
                $('#form_range_units_0').prop('checked', 1);
                $('#form_range_gsq').trigger('keyup');
                $('#form_range_min').trigger('keyup');

                $('#form_listener').prop('selectedIndex', 0);
                $('#form_listener_invert_0').prop('checked', 1);
                $('#form_heard_in').val('');
                $('#form_heard_in_mod_0').prop('checked', 1);
                $('#form_logged_date_1').val('');
                $('#form_logged_date_2').val('');
                $('#form_logged_first_1').val('');
                $('#form_logged_first_2').val('');
                $('#form_logged_last_1').val('');
                $('#form_logged_last_2').val('');

                setFormRegionAction(true);
                return false;
            });
            break;
        case 'listeners':
            $('button[type="reset"]').click(function () {
                $('fieldset#form_types div :checkbox').prop('checked', false);
                $('fieldset#form_types div :checkbox[value=type_NDB]').prop('checked', true);
                $('#form_filter').val('');
                setFormRegionAction(false);
                setFormCountryAction(false);
                $('select#form_region').prop('selectedIndex', 0);
                $('select#form_country').prop('selectedIndex', 0);
                setFormCountryAction(true);
                setFormRegionAction(true);
                return false;
            });
            break;
    }
}

function setFormShowMode() {
    switch($('#form_show').prop('selectedIndex')) {
        case 0:
            $("#show_list").addClass('is-active');
            break;
        case 1:
            $("#show_map").addClass('is-active');
            break;
    }
}

/* [ Set css styles for signal type checkboxes ] */
function setFormTypesStyles() {
    $("fieldset#form_types div input").each(function() {
        $(this).parent().attr('class', $(this).attr('class'));
    });
}

/* [ Ensure that at least one option is checked for signal type checkboxes ] */
function setFormTypesDefault() {
    if ($('fieldset#form_types div :checkbox:checked').length == 0) {
        $('fieldset#form_types div :checkbox[value=type_NDB]').prop('checked', true);
    }
}

/* [ Enable 'All' checkbox to select / unselect all signal types ] */
function setFormTypesAllAction() {
    $('fieldset#form_types div :checkbox[value=type_ALL]').click(function () {
        $('fieldset#form_types div :checkbox').prop('checked', $(this).prop("checked"));
    });
}

function setPagingActions() {
    var filter =    $('#form_filter');
    var prev =      $('#form_prev');
    var next =      $('#form_next');
    var limit =     $('#form_limit');
    if (limit.length) {
        limit[0].outerHTML =
            "<select id=\"form_limit\" name=\"form[limit]\" required=\"required\">" +
            getlimitOptions(paging.total, limit.val(), paging.limit) +
            "</select>";
        limit =     $('#form_limit');
    }

    var page =  $('#form_page');
    if (page.length) {
        page[0].outerHTML =
            "<label class=\"sr-only\" for=\"form_page\">Page Control</label>\n" +
            "<select id=\"form_page\" name=\"form[page\]\" style=\"display:none\">" +
            getPagingOptions(paging.total, limit.val(), paging.page) +
            "</select>";
        page =  $('#form_page');
    }

    var options =   $('#form_page option');

    if (limit.val() !== '-1') {
        prev.show();
        next.show();
        page.show();
    }

    limit.change(
        function() {
            var form =      $('form[name="form"]');
            var limit =     $('#form_limit');
            var options =   $('#form_page option');
            var prev =      $('#form_prev');
            var next =      $('#form_next');
            options.eq(0).prop('selected', true);
            page.prop('selectedIndex', 0);
            if (limit.val() !== "-1") {
                prev.show();
                next.show();
                page.show();
                options.eq(0).prop('text', '1-' + limit.val());
                prev.prop('disabled', 'disabled');
                next.prop('disabled', 'disabled');
            } else {
                page.hide();
                prev.hide();
                next.hide();
            }
            page.prop('selectedIndex', 0);
            form.submit();
        }
    );
    if (paging.page > 0) {
        prev.click(
            function () {
                var form =      $('form[name="form"]');
                var page =      $('#form_page');
                var options =   $('#form_page option');
                var prev =      $('#form_prev');
                var next =      $('#form_next');
                prev.prop('disabled', 'disabled');
                next.prop('disabled', 'disabled');
                options.eq(paging.page - 1).prop('selected', true);
                page.prop('selectedIndex', paging.page - 1);
                form.submit();
                return false;
            }
        );
    } else {
        prev.prop('disabled', 'disabled');
    }

    if (paging.page + 1 < options.length) {
        next.click(
            function() {
                var form =      $('form[name="form"]');
                var page =      $('#form_page');
                var options =   $('#form_page option');
                var prev =      $('#form_prev');
                var next =      $('#form_next');
                prev.prop('disabled', 'disabled');
                next.prop('disabled', 'disabled');
                options.eq(paging.page + 1).prop('selected', true);
                page.prop('selectedIndex', paging.page + 1);
                form.submit();
                return false;
            }
        );
    } else {
        next.prop('disabled', 'disabled');
    }

    page.change(
        function() {
            var form =      $('form[name="form"]');
            var page =      $('#form_page');
            var prev =      $('#form_prev');
            var next =      $('#form_next');
            prev.prop('disabled', 'disabled');
            next.prop('disabled', 'disabled');
            form.submit();
        }
    );

    filter.change(
        function() {
            var form =      $('form[name="form"]');
            var page =      $('#form_page');
            var options =   $('#form_page option');
            var prev =      $('#form_prev');
            var next =      $('#form_next');
            options.eq(0).prop('selected', true);
            page.prop('selectedIndex', 0);
            prev.prop('disabled', 'disabled');
            next.prop('disabled', 'disabled');
            form.submit();
        }
    );
}

var gridColor, gridOpacity, layers = [], map;

layers.grid = [];
gridColor="#808080";
gridOpacity=0.5;

