var popWinSpecs = {
    'countries_*' :                 'width=860,height=630,resizable=1',
    'countries_af' :                'width=640,height=630,resizable=1',
    'countries_as' :                'width=780,height=590,resizable=1',
    'countries_eu' :                'width=680,height=590,resizable=1',
    'countries_na' :                'width=640,height=220,resizable=1',
    'countries_oc' :                'width=680,height=500,resizable=1',
    'countries_sa' :                'width=320,height=600,resizable=1',
    'listeners_[id]' :              'width=800,height=680,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_logs' :         'width=800,height=680,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_signals' :      'width=800,height=680,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_signalmap' :    'width=800,height=680,status=1,scrollbars=1,resizable=1,location=1',
    'listeners_[id]_ndbweblog' :    'status=1,scrollbars=1,resizable=1',
    'map_af' :                      'width=646,height=652,resizable=1',
    'map_alaska' :                  'width=600,height=620,resizable=1',
    'map_as' :                      'width=856,height=645,resizable=1',
    'map_au' :                      'width=511,height=545,resizable=1',
    'map_eu' :                      'width=704,height=760,resizable=1',
    'map_japan' :                   'width=517,height=740,resizable=1',
    'map_na' :                      'width=669,height=720,resizable=1',
    'map_pacific' :                 'width=600,height=750,resizable=1',
    'map_polynesia' :               'width=500,height=525,resizable=1',
    'map_sa' :                      'width=490,height=745,resizable=1',
    'signals_[id]' :                'width=800,height=680,status=1,scrollbars=1,resizable=1',
    'signals_[id]_logs' :           'width=800,height=680,status=1,scrollbars=1,resizable=1',
    'signals_[id]_listeners' :      'width=800,height=680,status=1,scrollbars=1,resizable=1',
    'signals_[id]_weather' :        'width=800,height=680,status=1,scrollbars=1,resizable=1',
    'states_*' :                    'width=720,height=760,resizable=1',
    'states_aus' :                  'width=720,height=240,resizable=1',
    'states_can_usa' :              'width=680,height=690,resizable=1',
};

function changeShowMode(mode) {
    $('#form_show').val(mode);
    $('#form_submit').click();
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

function getLimitOptions(max, value, defaultLimit) {
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

function getPagingOptions(total, limit, page) {
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

    google.maps.event.addDomListener(document.getElementById('layer_grid'), 'click', function() {
        toggleGrid();
    });

    google.maps.event.addDomListener(document.getElementById('layer_inactive'), 'click', function() {
        toggleInactive();
    });

    google.maps.event.addDomListener(document.getElementById('layer_qth'), 'click', function() {
        toggleQth();
    });

    listener.types.forEach(function(type){
        google.maps.event.addDomListener(document.getElementById('layer_' + type), 'click', function () {
            toggleLayer( type );
        });
    });
}

function initMapsShowGrid() {
    function showGrid(map, overlayClass) {
        console.log('showGrid called');
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

function initSignalsForm(pagingMsg, resultsCount) {
    $(document).ready( function() {
        setFormPagingActions();

        setFormPersonaliseAction();
        setFormRangeAction();
        setFormRangeUnitsDefault();
        setFormSortbyAction();
        setFormSortZaAction();
        setFormShowModeAction();

        setFormTypesStyles();
        setFormTypesDefault();
        setFormTypesAllAction();
        setFormCountryAction();
        setFormAdminAction();
        setFormRegionAction();
        setFormRwwFocusAction();
        setFormStatesLabelLink();
        setFormCountriesLabelLink();

        setFormListenerInvertDefault();
        setFormHeardInModDefault();
        setFormListenerOptionsStyle();
        setFormDatePickers();

        setFormResetAction('signals');
        setColumnSortActions();
        setColumnSortedClass();
        setExternalLinks();

        setFormPagingStatus(pagingMsg, resultsCount);
        scrollToResults();
    });
}

function popup(url) {

    var hd, i, id, mode, name, path;
    var pattern = [];
    var systems = [ 'rna', 'reu', 'rww' ];

    path = url.split('/').reverse();
    for(i = 0; i <= 2; i++) {
        if ($.isNumeric(path[i])) {
            id = path[i];
            pattern.push('[id]');
        } else {
            pattern.push(path[i]);
        }
    }
    if (systems.includes(pattern[2])) {
        pattern.pop();
    }
    pattern.reverse();
    mode = pattern.join('_').replace(',', '_');

    if ('undefined' === typeof popWinSpecs[mode]) {
        alert('Unhandled mode ' + mode);
        return false;
    }
    name = mode.replace('[id]', id);
    hd = window.open(url, name, popWinSpecs[mode]);
    if (!hd){
        alert(
            'ERROR:\n\n'+
            'This site tried to open a popup window\n'+
            'but was prevented from doing so.\n\n'+
            'Please disable any popup blockers you may\n'+
            'have enabled for this site.'
        );
        return false;
    }
    hd.focus();
    return false;
}

function scrollToResults() {
    if ($('#form_show').val() !== '') {
        $([document.documentElement, document.body]).animate({
            scrollTop: $("#tabs").offset().top - 20
        }, 500);
    }
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
                dir = ($('#form_order').val() === 'a' ? 'd' : 'a');
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
        if (this.id.split('|')[0] === $('#form_sort').val()) {
            $(this).append($('#form_order').val() === 'd' ? ' &#9662;' : ' &#9652;');
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

function setEqualHeight(a, b) {
    if (!$(a).height()) {
        return window.setTimeout(function(){ setEqualHeight(a, b); }, 100);
    }
    $(b).height($(a).height());
    $(b).show();
    $(b).height($(a).height());
}

/* [ Set links to open in external or popup window if rel attribute is set ] */
function setExternalLinks() {
    $('a[rel="external"]').attr('target', '_blank');
    $('a[data-popup]').click(function() {
        if (1 === $(this).data('popup')) {
            return popup(this.href);
        }
        var args = $(this).data('popup').split('|');
        window.open(this.href, args[0], args[1]);
        return false;
    });
    $('a[data-append]')
        .click(function() {
            var abbr, div, field, items;
            abbr = $(this).find('span').html();
            div = ('itu' === $(this).data('append') ? '#form_countries' : '#form_states');
            field = window.opener.$(div);
            items = field.val().split(' ');
            if ($.inArray(abbr, items) !== -1) {
                items = items.filter(function(elem){ return elem != abbr; });
            } else {
                items.push(abbr);
            }
            field.val(Array.from(new Set(items)).sort().join(' ').trim());
            return false;
        })
        .attr('title', msg.data_append);
    $('a[data-set]')
        .click(function() {
            var abbr, divs, field, i;
            abbr = $(this).text();
            switch ($(this).data('set')) {
                case 'khz':
                    divs = ['#form_khz_1', '#form_khz_2'];
                    break;
                case 'itu':
                    divs = ['#form_countries'];
                    break;
                case 'sp':
                    divs = ['#form_states'];
                    break;
            }
            for (i in divs) {
                field = $(divs[i]);
                if (field.val() === abbr) {
                    field.val('');
                } else {
                    field.val(abbr);
                }
            }
            $('form[name="form"]').submit();
            return false;
        })
        .attr('title', msg.data_set);

    $('a[data-gsq]')
        .click(function() {
            var target = 'map_' + $(this).data('gsq');
            var features = 'scrollbars=1,resizable=1,width=1024,height=800';
            window.open(this.href, target, features);
            return false;
        })
        .each(function() {
            $(this).attr('href', './signals/'  + $(this).data('gsq') + '/map');
        })
        .attr('title', msg.s_map_eu)
        .attr('class', 'hover');

    $('a[data-signal-map-eu]')
        .click(function() {
            var target = 'map_' + $(this).data('signal-map-eu');
            var features = 'scrollbars=1,resizable=1,width=1024,height=800';
            window.open(this.href, target, features);
            return false;
        })
        .each(function() {
            $(this).attr('href', './signals/'  + $(this).data('signal-map-eu') + '/map/eu');
        })
        .attr('title', msg.s_map_eu)
        .attr('class', 'hover');

    $('a[data-signal-map-na]')
        .click(function() {
            var target = 'map_' + $(this).data('signal-map-na');
            var features = 'scrollbars=1,resizable=1,width=1024,height=800';
            window.open(this.href, target, features);
            return false;
        })
        .each(function() {
            $(this).attr('href', './signals/' + $(this).data('signal-map-na') + '/map/na');
        })
        .attr('title', msg.s_map_na)
        .attr('class', 'hover');

    $('area[data-map]')
        .attr('shape', 'circle')
        .mouseover(function() {
            $('#listener_' + $(this).data('map'))
                .css({backgroundColor: '#ffff00'})
                .trigger('mouseenter');
        })
        .mouseout(function() {
            $('#listener_' + $(this).data('map'))
                .css({backgroundColor: ''})
                .trigger('mouseleave');
        })
        .each(function() {
            $(this).attr('title', $(this).attr('alt'));
        });

    $('tr[data-map]')
        .click(function() {
            var target = 'listeners_' + $(this).data('map');
            alert(target);
            return false;
        })
        .mouseover(function() {
            var coords = $(this).data('map').split('|');
            $('#point_here')
                .show()
                .css({left: (coords[0] - 5)+'px', top: (coords[1]-5) + 'px'});
        })
        .mouseout(function() {
            $('#point_here').hide();
        });

    $('tr[data-map] a')
        .click(function() {
            var target = 'listeners_' + $(this).data('map');
            window.open(this.href, target, popWinSpecs["listeners_[id]"]);
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

function setFormCountriesLabelLink() {
    var ele = $('label[for="form_countries"]');
    ele.html('<a href="countries/*" data-popup="1">' + ele.html() + '</a>');
}

function setFormStatesLabelLink() {
    var ele = $('label[for="form_states"]');
    ele.html('<a href="states/*" data-popup="1">' + ele.html() + '</a>');
}

function setFormDatePickers() {
    $.datepicker.setDefaults({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        yearRange: '1980:+0'
    });
    $('.js-datepicker').datepicker({
    });
}

function setFormHeardInModDefault() {
    if ($('fieldset#form_heard_in_mod div :radio:checked').length === 0) {
        $('fieldset#form_heard_in_mod div :radio[value="any"]').prop('checked', true);
    }
}

function setFormListenerInvertDefault() {
    if ($('fieldset#form_listener_invert div :radio:checked').length === 0) {
        $('fieldset#form_listener_invert div :radio[value=0]').prop('checked', true);
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

function setFormPagingActions() {
    var filter =    $('#form_filter');
    var prev =      $('#form_prev');
    var next =      $('#form_next');
    var limit =     $('#form_limit');
    if (limit.length) {
        limit[0].outerHTML =
            "<select id=\"form_limit\" name=\"form[limit]\" required=\"required\">" +
            getLimitOptions(paging.total, limit.val(), paging.limit) +
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

function setFormPagingStatus(string, value) {
    $('#form_paging_status').html(
        string.replace('%s', value.toLocaleString())
    );
}

function setFormPersonaliseAction() {
    $('#form_personalise').change(function() {
        var lbl = $('#form_personalise option:selected').text();
        var gsq = (lbl.split('|').length === 2 ? lbl.split('|')[1] : '').trim();
        $('#form_range_gsq').val(gsq);
        $('#form_range_gsq').trigger('keyup');
    });
}

function setFormRangeAction() {
    $('#form_range_gsq').on('keyup', function() {
        var disabled = ($('#form_range_gsq').val().length < 6);
        $('#form_range_min').attr('disabled', disabled);
        $('#form_range_max').attr('disabled', disabled);
    });
    $('#form_range_min').on('keyup', function() {
        var disabled = ($('#form_range_min').val().length === 0 && $('#form_range_max').val().length === 0);
        $('#form_range_units').attr('disabled', disabled);
    });
    $('#form_dx_max').on('keyup', function() {
        var disabled = ($('#form_range_min').val().length === 0 && $('#form_range_max').val().length === 0);
        $('#form_range_units').attr('disabled', disabled);
    });
    $('#form_range_gsq').trigger('keyup');
    $('#form_range_min').trigger('keyup');
}

function setFormRangeUnitsDefault() {
    if ($('fieldset#form_range_units div :radio:checked').length === 0) {
        $('fieldset#form_range_units div :radio[value=km]').prop('checked', true);
    }
}

/* [ Enable Admin Mode change to resubmit form ] */
function setFormAdminAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('select#form_admin_mode').change(function () {
            $('#form_submit').click();
        });
    } else {
        $('select#form_admin_mode').off('change');
    }
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

function setFormRwwFocusAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('select#form_rww_focus').change(function () {
            $('#form_submit').click();
        });
    } else {
        $('select#form_rww_focus').off('change');
    }
}

function setFormResetAction(form) {
    switch (form) {
        case 'signals':
            $('button[type="reset"]').click(function () {
                setFormAdminAction(false);
                setFormRegionAction(false);
                setFormRwwFocusAction(false);

                $('#form_show').val('');
                $('#form_types div :checkbox').prop('checked', false);
                $('#form_types div :checkbox[value=type_NDB]').prop('checked', true);
                $('#form_call').val('');
                $('#form_khz_1').val('');
                $('#form_khz_2').val('');
                $('#form_channels').prop('selectedIndex', 0);
                $('#form_active').prop('selectedIndex', 0);
                $('#form_personalise').prop('selectedIndex', 0);
                $('#form_offsets').prop('selectedIndex', 0);

                $('#form_states').val('');
                $('#form_sp_itu_clause').prop('selectedIndex', 0);
                $('#form_countries').val('');
                $('#form_region').prop('selectedIndex', 0);
                $('#form_rww_focus').prop('selectedIndex', 0);
                $('#form_gsq').val('');
                $('#form_range_gsq').val('');
                $('#form_range_min').val('');
                $('#form_range_max').val('');
                $('#form_range_units_0').prop('checked', 1);
                $('#form_range_gsq').trigger('keyup');
                $('#form_range_min').trigger('keyup');

                $('#form_listener').val([]);
                $('#form_listener_invert_0').prop('checked', 1);
                $('#form_heard_in').val('');
                $('#form_heard_in_mod_0').prop('checked', 1);
                $('#form_logged_date_1').val('');
                $('#form_logged_date_2').val('');
                $('#form_logged_first_1').val('');
                $('#form_logged_first_2').val('');
                $('#form_logged_last_1').val('');
                $('#form_logged_last_2').val('');

                $('#form_admin_mode').prop('selectedIndex', 0);

                setFormAdminAction(true);
                setFormRegionAction(true);
                setFormRwwFocusAction(true);
                $('#form_submit').click();
                return false;
            });
            break;
        case 'listeners':
            $('button[type="reset"]').click(function () {
                $('fieldset#form_types div :checkbox').prop('checked', false);
                $('fieldset#form_types div :checkbox[value=type_NDB]').prop('checked', true);
                $('#form_filter').val('');
                setFormCountryAction(false);
                setFormRegionAction(false);
                setFormRwwFocusAction(false);
                $('select#form_region').prop('selectedIndex', 0);
                $('select#form_country').prop('selectedIndex', 0);
                setFormCountryAction(true);
                setFormRegionAction(true);
                setFormRwwFocusAction(true);
                return false;
            });
            break;
    }
}

function setFormShowModeAction() {
    $('#seeklist_paper')
        .change(function() {
            $('#form_paper').val($('#seeklist_paper option:selected').val());
            $('#form_submit').click();
        });
}

function setFormSortbyAction() {
    $('select#form_sortby').change(function() {
        var val = $("#form_sortby option:selected").val();
        $('#form_sort').val(val.split('|')[0]);
        $('#form_order').val(val.split('|')[1]);
        $('#form_za').prop('checked', val.split('|')[1] === 'd');
        $('#form_submit').click();
    });
}

function setFormSortZaAction() {
    $('input#form_za').change(function () {
        $('#form_order').val($('input#form_za').prop('checked') ? 'd' : 'a');
        $('#form_submit').click();
    });
}

/* [ Set css styles for signal type checkboxes ] */
function setFormTypesStyles() {
    $("fieldset#form_types div input").each(function() {
        $(this).parent().attr('class', $(this).attr('class'));
    });
}

/* [ Ensure that at least one option is checked for signal type checkboxes ] */
function setFormTypesDefault() {
    if ($('fieldset#form_types div :checkbox:checked').length === 0) {
        $('fieldset#form_types div :checkbox[value=type_NDB]').prop('checked', true);
    }
}

/* [ Enable 'All' checkbox to select / unselect all signal types ] */
function setFormTypesAllAction() {
    $('fieldset#form_types div :checkbox[value=type_ALL]').click(function () {
        $('fieldset#form_types div :checkbox').prop('checked', $(this).prop("checked"));
    });
}

var signalsMap = {
    map: null,
    icons: {},
    infoWindow: null,
    items: [],
    options: {},

    init: function() {
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

function strip_tags(input, allowed) {
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
    var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    allowed = (((allowed || '') + '')
        .toLowerCase()
        .match(/<[a-z][a-z0-9]*>/g) || [])
        .join('');
    return input.replace(commentsAndPhpTags, '')
        .replace(tags, function($0, $1) {
                return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
            }
        );
}

var gridColor, gridOpacity, layers = [], map;

layers.grid = [];
gridColor="#808080";
gridOpacity=0.5;

