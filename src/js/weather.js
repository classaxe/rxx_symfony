var LIGHTNING = {
    init: function() {
        var lightning_clear =   $('#lightning_clear');
        var lightning_go =      $('#lightning_go');
        var lightning_gsq =     $('#lightning_gsq');
        var lightning_lat =     $('#lightning_lat');
        var lightning_lon =     $('#lightning_lon');
        var lightning_zoom =    $('#lightning_zoom');
        var lightning_map =     $('#lightning_map');
        var lightning_slider =  $("#slider-range-max");

        var element = $('#lightning_zoom');
        lightning_slider.slider({
            range: "max",
            min: 1,
            max: 10,
            value: element.val(),
            slide: function (event, ui) {
                lightning_zoom.val(ui.value);
                if (lightning_lat.val() && lightning_lon.val()) {
                    lightning_go.trigger('click');
                }
            }
        });

        $('#close').on('click', function() {
            window.close();
        })

        lightning_gsq.on('change, blur', function() {
            if (!$(this).val()) {
                return;
            }
            if (!VALIDATE.gsq($(this).val())) {
                alert(msg.tools.coords.gsq_format);
                return;
            }
            LIGHTNING.gsq_deg($(this).val());
            lightning_go.trigger('click');
            LIGHTNING.cookie_set();
        });

        lightning_lat.on('change', function() {
            VALIDATE.float($(this).val(), -90.0, 90.0, 'Latitude')
        });

        lightning_lon.on('change', function() {
            VALIDATE.float($(this).val(), -180.0, 180.0, 'Longitude');
        });

        lightning_go.on('click', function() {
            var lat = $('#lightning_lat').val();
            var lon = $('#lightning_lon').val();
            var zoom = $('#lightning_zoom').val();
            if (!VALIDATE.float(lat, -90.0, 90.0, 'Latitude')) {
                return false;
            }
            if (!VALIDATE.float(lon, -180.0, 180.0, 'Longitude')) {
                return false;
            }
            LIGHTNING.map_show(lat, lon, zoom);
        });
        lightning_clear.on('click', function() {
            LIGHTNING.cookie_clear();
            lightning_gsq.val('');
            lightning_lat.val('');
            lightning_lon.val('');
            lightning_map.prop('src', '');
            lightning_zoom.val(5);
            lightning_slider.slider('option', 'value', 5);
        });
        if (LIGHTNING.cookie_get('lightning')) {
            var result = LIGHTNING.cookie_get("lightning").split("|");
            lightning_gsq.val(result[0]);
            lightning_lat.val(result[1]);
            lightning_lon.val(result[2]);
            lightning_zoom.val(result[3]);
            lightning_slider.slider('option', 'value', result[3]);
        }
        $('a[data-coords]')
            .click(function() {
                var args, lat, lon, zoom;
                result = $(this).data('coords').split('|');
                lightning_gsq.val('');
                lightning_lat.val(result[0]);
                lightning_lon.val(result[1]);
                lightning_zoom.val(result[2]);
                lightning_slider.slider('option', 'value', result[2]);
                LIGHTNING.map_show(result[0], result[1], result[2]);
                LIGHTNING.cookie_clear();
                return false;
            });
    },
    cookie_clear: function() {
        COOKIE.clear('lightning');
    },
    cookie_get: function() {
        return COOKIE.get('lightning');
    },
    cookie_set: function() {
        var value = $('#lightning_gsq').val() + '|' + $('#lightning_lat').val() + '|' + $('#lightning_lon').val() + '|' + $('#lightning_zoom').val();
        COOKIE.set('lightning', value);
    },
    gsq_deg: function(gsq) {
        var result = CONVERT.gsq_deg(gsq);
        $('#lightning_lat').val(result.lat);
        $('#lightning_lon').val(result.lon);
        return true;
    },
    map_show: function(lat, lon, zoom) {
        var url =
            'https://map.blitzortung.org?' +
            'interactive=1' +
            '&NavigationControl=0' +
            '&FullScreenControl=1' +
            '&Cookies=0' +
            '&InfoDiv=0' +
            '&MenuButtonDiv=1' +
            '&ScaleControl=1' +
            '&CountingRangeValue=3' +
            '&CountingCheckboxChecked=1' +
            '&DetectorsCheckboxChecked=0' +
            '&DetectorsRange=0' +
            '&AudioCheckboxChecked=0' +
            '&LinksCheckboxChecked=0' +
            '&LinksRangeValue=0' +
            '&MapStyle=0' +
            '&MapStyleRangeValue=0' +
            '&Advertisment=0' +
            '#' + zoom + '/' + lat + '/' + lon;
        $('#lightning_map').prop('src', url);
    }
}