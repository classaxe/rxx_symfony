var DGPS = {
    init: function() {
        $('a[data-dgps]').on('click', function() {
            $('#dgps_ref').val($(this).data('dgps'));
            $('#dgps_go').trigger('click');
            return false;
        });
        $('#dgps_ref').on('focus', function() {
            $(this).select();
        });
        $('#dgps_lookup').on('submit', function() {
            $('#dgps_details').val(DGPS.lookup($('#dgps_ref').val()));
            return false;
        });
        $('#dgps_close').on('click', function(){
            window.close();
        })
    },
    a: function(id1, id2, ref, khz, bps, qth, sta, cnt, act) {
        if (typeof this.entries[id1] == 'undefined') {
            this.entries[id1] =		[];
        }
        this.entries[id1].push([ ref, khz, bps, qth, sta, cnt, id1, id2, act ]);
        if (typeof this.entries[id2] == 'undefined') {
            this.entries[id2] =		[];
        }
        this.entries[id2].push([ ref, khz, bps, qth, sta, cnt, id1, id2, act ]);

    },
    entries: [],
    lookup: function(id) {
        var out = [];
        if (id === '') {
            return '';
        }
        if (typeof this.entries[parseFloat(id)] === 'undefined') {
            return msg.tools.dgps.nomatch;
        }
        id = parseFloat(id);
        for (var i=0; i < this.entries[id].length; i++) {
            var a =	this.entries[id][i];
            out.push(
                '  Station ' + a[0] + (a[8] === 0 ? ' ' + msg.tools.dgps.inactive : '') + "\n" +
                '  ' + a[1] + 'KHz ' + a[2] + 'bps' + "\n" +
                '  ' + a[3] + ' ' + a[4] + ' ' + a[5] + "\n" +
                '  Reference ID(s): ' + a[6] + (a[6] !== a[7] ? ', ' + a[7] : '')
            );
        }
        if (i>1) {
            return msg.tools.dgps.multiple + ' (' + i + "):\n"+out.join("\n\n");
        }
        return out.join("");
    }
}

var COORDS = {
    init: function() {
        var cmd_1, cmd_2, idx, modes;
        modes = [
            ['conv_dd_mm_ss', 'deg_gsq'],
            ['conv_dd_dddd', 'deg_gsq'],
            ['gsq_deg', 'conv_dd_dddd']
        ];
        for (idx = 0; idx < modes.length; idx++) {
            cmd_1 = 'COORDS.' + modes[idx][0] + '();';
            cmd_2 = 'COORDS.' + modes[idx][1] + '();';
            (function (i, c1, c2) {
                $('#go_' + i).on('click', function () {
                    if (eval(c1)) {
                        eval(c2)
                    }
                    return false;
                });
                $('#map_' + i).on('click', function () {
                    if (eval(c1)) {
                        eval(c2);
                        COORDS.map('map');
                    }
                    return false;
                });
                $('#photo_' + i).on('click', function () {
                    if (eval(c1)) {
                        eval(c2)
                        COORDS.map('photo');
                    }
                    return false;
                });
            })(idx + 1, cmd_1, cmd_2);
        }
    },
    map: function(mode) {
        var hd, lat, lon, url;
        lat = $('#lat_dddd').val();
        lon = $('#lon_dddd').val();
        if (lat === '' || lon === '') {
            return;
        }
        url = base_url + "maps/coords/" + lat + "/" + lon + "/" + mode;
        hd = '_' + lat + '_' + lon;
        window.open(url, hd, 'scrollbars=1,resizable=1,location=1,width=860,height=630');
    },
    deg_gsq: function(){
        var lat, lat_a, lat_b, lat_c, lat_dec, lon, lon_a, lon_b, lon_c, lon_dec;
        var letters = "abcdefghijklmnopqrstuvwxyz";
        lat_dec =   $('#lat_dddd').val();
        lon_dec =   $('#lon_dddd').val();

        if (lat_dec === '' || lon_dec === '') {
            return false;
        }
        lat =   parseFloat(lat_dec) + 90;
        lat_a = letters.charAt(Math.floor(lat / 10)).toUpperCase();
        lat_b = Math.floor(lat % 10);
        lat_c = letters.charAt(Math.floor(24 * (lat % 1)))
        lon =   (parseFloat(lon_dec) + 180) / 2;
        lon_a = letters.charAt(Math.floor(lon / 10)).toUpperCase();
        lon_b = Math.floor(lon % 10);
        lon_c = letters.charAt(Math.floor(24 * (lon % 1)))
        $('#gsq').val(lon_a + lat_a + lon_b + lat_b + lon_c + lat_c);
    },
    gsq_deg: function() {
        var GSQ, lat, lat_d, lat_m, lat_s, lon, lon_d, lon_m, lon_s, offset;
        GSQ = $('#gsq').val().toUpperCase();
        if (GSQ === '') {
            alert(msg.tools.coords.gsq_format);
            return false;
        }
        offset = (GSQ.length === 6 ? 1/48 : 0);
        GSQ = GSQ + (GSQ.length === 4 ? 'MM' : '');
        lon_d = GSQ.charCodeAt(0)-65;
        lon_m = parseFloat(GSQ.substr(2,1));
        lon_s = GSQ.charCodeAt(4)-65;
        lat_d = GSQ.charCodeAt(1)-65;
        lat_m = parseFloat(GSQ.substr(3,1));
        lat_s = GSQ.charCodeAt(5)-65;
        lon = Math.round((2 * (lon_d * 10 + lon_m + lon_s / 24 + offset) - 180) * 10000) / 10000;
        lat = Math.round((lat_d * 10 + lat_m + lat_s / 24 + offset - 90) * 10000) / 10000;
        $('#lat_dddd').val(lat);
        $('#lon_dddd').val(lon);
        return true;
    },
    conv_dd_dddd: function() {
        var dec_lat, dec_lon, lat_abs, lat_dd, lat_h, lat_mm, lat_ss, lon_abs, lon_dd, lon_h, lon_mm, lon_ss;
        dec_lat =	parseFloat($('#lat_dddd').val());
        dec_lon =	parseFloat($('#lon_dddd').val());
        if (isNaN(dec_lat)) {
            alert(msg.tools.coords.lat_dec);
            return false;
        }
        if (isNaN(dec_lon)) {
            alert(msg.tools.coords.lon_dec);
            return false;
        }
        lat_h =     (dec_lat>0 ? "N" : "S")
        lat_abs =   Math.abs(dec_lat);
        lat_dd =    Math.floor(lat_abs);
        lat_mm =    lead(Math.floor(60*(lat_abs%1)));
        lat_ss =    lead(Math.floor((lat_abs-lat_dd-(lat_mm/60))*3600));
        lon_h =     (dec_lon>0 ? "E" : "W")
        lon_abs =   Math.abs(dec_lon);
        lon_dd =    Math.floor(lon_abs);
        lon_mm =    lead(Math.floor(60*(lon_abs%1)));
        lon_ss =    lead(Math.floor((lon_abs-lon_dd-(lon_mm/60))*3600));
        $('#lat_dd_mm_ss').val(lat_dd + "." + lat_mm + "." + lat_ss + "." + lat_h);
        $('#lon_dd_mm_ss').val(lon_dd + "." + lon_mm + "." + lon_ss + "." + lon_h);
        return true;
    },
    conv_dd_mm_ss: function() {
        var a, dec_lat, dec_lon, deg, hem, min, rexp_lat, rexp_lon, sec
        rexp_lat =  /([0-9]+)[� .]*([0-9]+)[' .]*([0-9]+)*[" .]*([NS])*/i;
        rexp_lon =  /([0-9]+)[� .]*([0-9]+)[' .]*([0-9]+)*[" .]*([EW])*/i;
        a = $('#lat_dd_mm_ss').val().match(rexp_lat);
        if (a === null) {
            alert(
                "ERROR: Latitude must be given in one of these formats:" +
                "\n  DD\xB0MM'SS\"H\n  DD.MM.SS.H\n  DD MM SS H\n  DD\xB0MM.H\n  DD.MM.H\n  DD MM H\n\n" +
                "(H is N or S, but defaults to S if not given)"
            );
            return false;
        }
        deg =       parseFloat(a[1]);
        min =       parseFloat(a[2]);
        sec =       (a[3] !== '' ? parseFloat(a[3]) : 0);
        hem =       (typeof a[4] === 'undefined' ? 1 : (a[4] === 'N' || a[4] === 'n' ? 1 : -1));
        dec_lat =   hem * (deg + (Math.round(((sec / 3600) + (min / 60)) * 10000)) / 10000);
        $('#lat_dddd').val(dec_lat);

        a =         $('#lon_dd_mm_ss').val().match(rexp_lon);
        if (a==null) {
            alert(
                "ERROR: Longitude must be given in one of these formats:" +
                "\n  DDD\xB0MM'SS\"H\n  DDD.MM.SS.H\n  DDD MM SS H\n  DDD.MM.H\n  DDD\xB0MM.H\n  DDD MM H\n\n" +
                "(H is E or W, but defaults to W if not given)"
            );
            return;
        }
        deg =       parseFloat(a[1]);
        min =       parseFloat(a[2]);
        sec =       (a[3] !== '' ? parseFloat(a[3]) : 0);
        hem =       (typeof a[4] === 'undefined' ? 1 : (a[4] === 'E' || a[4] === 'e' ? 1 : -1));
        dec_lon =   hem * (deg + (Math.round(((sec / 3600) + (min / 60)) * 10000)) / 10000);
        $('#lon_dddd').val(dec_lon);
        return true;
    }
}