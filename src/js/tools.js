var DGPS = {
    init: function() {
        $('#frm_dgps').on('submit', function() {
            $('#dgps_details').val(DGPS.lookup($('#dgps_ref').val()));
            return false;
        });
        $('a[data-dgps]').on('click', function() {
            $('#dgps_ref').val($(this).data('dgps'));
            $('#dgps_go').trigger('click');
            return false;
        });
        $('#dgps_ref').on('focus', function() {
            $(this).select();
        });
        $('#close').on('click', function(){
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
var CONVERT = {
    deg_dms: function(lat_dec, lon_dec) {
        var lat_abs, lat_dd, lat_h, lat_mm, lat_ss, lon_abs, lon_dd, lon_h, lon_mm, lon_ss;
        if (!VALIDATE.dec_lat(lat_dec) || !VALIDATE.dec_lon(lon_dec)) {
            return false;
        }
        lat_h =     (lat_dec > 0 ? 'N' : 'S')
        lat_abs =   Math.abs(lat_dec);
        lat_dd =    Math.floor(lat_abs);
        lat_mm =    lead(Math.floor(60*(lat_abs%1)));
        lat_ss =    lead(Math.floor((lat_abs-lat_dd-(lat_mm/60))*3600));
        lon_h =     (lon_dec > 0 ? 'E' : 'W')
        lon_abs =   Math.abs(lon_dec);
        lon_dd =    Math.floor(lon_abs);
        lon_mm =    lead(Math.floor(60*(lon_abs%1)));
        lon_ss =    lead(Math.floor((lon_abs-lon_dd-(lon_mm/60))*3600));
        return {
            lat_dd_mm_ss: lat_dd + '.' + lat_mm + '.' + lat_ss + '.' + lat_h,
            lon_dd_mm_ss: lon_dd + '.' + lon_mm + '.' + lon_ss + '.' + lon_h
        };
    },
    deg_gsq: function(lat_dec, lon_dec) {
        var lat, lat_a, lat_b, lat_c, lon, lon_a, lon_b, lon_c;
        var letters = "abcdefghijklmnopqrstuvwxyz";
        if (!VALIDATE.dec_lat(lat_dec) || !VALIDATE.dec_lon(lon_dec)) {
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
        return (lon_a + lat_a + lon_b + lat_b + lon_c + lat_c);
    },
    dms_deg: function(lat_dd_mm_ss, lon_dd_mm_ss) {
        var a, dec_lat, dec_lon, deg, hem, min, rexp_lat, rexp_lon, result, sec

        rexp_lat =  /([0-9]+)[� .]*([0-9]+)[' .]*([0-9]+)*[" .]*([NS])*/i;
        rexp_lon =  /([0-9]+)[� .]*([0-9]+)[' .]*([0-9]+)*[" .]*([EW])*/i;

        a =         lat_dd_mm_ss.match(rexp_lat);
        deg =       parseFloat(a[1]);
        min =       parseFloat(a[2]);
        sec =       (a[3] !== '' ? parseFloat(a[3]) : 0);
        hem =       (typeof a[4] === 'undefined' ? 1 : (a[4] === 'N' || a[4] === 'n' ? 1 : -1));
        dec_lat =   hem * (deg + (Math.round(((sec / 3600) + (min / 60)) * 10000)) / 10000);

        a =         lon_dd_mm_ss.match(rexp_lon);
        deg =       parseFloat(a[1]);
        min =       parseFloat(a[2]);
        sec =       (a[3] !== '' ? parseFloat(a[3]) : 0);
        hem =       (typeof a[4] === 'undefined' ? 1 : (a[4] === 'E' || a[4] === 'e' ? 1 : -1));
        dec_lon =   hem * (deg + (Math.round(((sec / 3600) + (min / 60)) * 10000)) / 10000);

        return { lat: dec_lat, lon: dec_lon };
    },
    gsq_deg: function(GSQ) {
        var lat, lat_d, lat_m, lat_s, lon, lon_d, lon_m, lon_s, offset;
        if (!VALIDATE.gsq(GSQ)) {
            return false;
        }
        GSQ = GSQ.toUpperCase();
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
        return { lat: lat, lon: lon };
    },
}
var VALIDATE = {
    gsq: function(value) {
        return value.match(/^([a-rA-R]{2})([0-9]{2})([a-xA-X]{2})?$/i);
    },
    dec_lat: function(value) {
        return (!(isNaN(value) || value >= 90 || value <= -90));
    },
    dec_lon: function(value) {
        return (!(isNaN(value) || value >= 180 || value <= -180));
    },
    dms_lat: function(value) {
        return value.match(/^([0-9]{1,2})[� .]([0-5][0-9])[' .]([0-5][0-9])[" .]?([NS])?$/i);
    },
    dms_lon: function(value) {
        return value.match(/^([0-9]{1,3})[� .]([0-5][0-9])[' .]([0-5][0-9])[" .]?([EW])?$/i);
    },
    float: function(value, min, max, field) {
        var msg = "Please enter a number"
        var checkVal = parseFloat(value)

        if ( isNaN(checkVal) ) {
            alert(field + ":\n" + msg);
            return false;
        }
        if (checkVal < min) {
            alert(field + ":\n" + msg + " >= " + min)
            return false;
        }
        if (checkVal > max) {
            alert(field + ":\n" + msg + " <= " + max)
            return false;
        }
        return true
    },
    int: function(value, min, max, field) {
        var checkVal = parseInt(value)
        var msg = "Please enter a number"

        if ( isNaN(checkVal) ) {
            alert(field + ":\n" + msg);
            return false;
        }
        if (checkVal < min) {
            alert(field + ":\n" + msg + " >= " + min);
            return false
        }
        if (checkVal > max) {
            alert(field + ":\n" + msg + " <= " + max);
            return false;
        }
        return true;
    },
}

var COORDS = {
    init: function() {
        var cmd_1, cmd_2, idx, modes;
        modes = [
            ['dms_deg', 'deg_gsq'],
            ['deg_dms', 'deg_gsq'],
            ['gsq_deg', 'deg_dms']
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
        $('#close').on('click', function(){
            window.close();
        })
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
        var lat, lon;
        lat = $('#lat_dddd').val();
        lon = $('#lon_dddd').val();
        if (!VALIDATE.dec_lat(lat)) {
            alert(msg.tools.coords.lat_dec);
            return false;
        }
        if (!VALIDATE.dec_lon(lon)) {
            alert(msg.tools.coords.lon_dec);
            return false;
        }
        $('#gsq').val(CONVERT.deg_gsq(lat, lon));
    },
    gsq_deg: function() {
        var gsq, result;
        gsq = $('#gsq').val();
        if (!VALIDATE.gsq(gsq)) {
            alert(msg.tools.coords.gsq_format);
            return false;
        }
        result = CONVERT.gsq_deg(gsq);
        $('#lat_dddd').val(result.lat);
        $('#lon_dddd').val(result.lon);
        return true;
    },
    deg_dms: function() {
        var dec_lat, dec_lon, result;
        dec_lat =	parseFloat($('#lat_dddd').val());
        dec_lon =	parseFloat($('#lon_dddd').val());
        if (!VALIDATE.dec_lat(dec_lat)) {
            alert(msg.tools.coords.lat_dec);
            return false;
        }
        if (!VALIDATE.dec_lon(dec_lon)) {
            alert(msg.tools.coords.lon_dec);
            return false;
        }
        result = CONVERT.deg_dms(dec_lat, dec_lon);
        $('#lat_dd_mm_ss').val(result.lat_dd_mm_ss);
        $('#lon_dd_mm_ss').val(result.lon_dd_mm_ss);
        return true;
    },
    dms_deg: function() {
        var result, lat_dd_mm_ss, lon_dd_mm_ss;
        lat_dd_mm_ss = $('#lat_dd_mm_ss').val();
        lon_dd_mm_ss = $('#lon_dd_mm_ss').val();
        if (!VALIDATE.dms_lat(lat_dd_mm_ss)) {
            alert(
                msg.tools.coords.lat_dms_1 +
                "\n  DD\xB0MM'SS\"H\n  DD.MM.SS.H\n  DD MM SS H\n  DD\xB0MM.H\n  DD.MM.H\n  DD MM H\n\n" +
                msg.tools.coords.lat_dms_2
            );
            return false;
        }
        if (!VALIDATE.dms_lon(lon_dd_mm_ss)) {
            alert(
                msg.tools.coords.lon_dms_1 +
                "\n  DDD\xB0MM'SS\"H\n  DDD.MM.SS.H\n  DDD MM SS H\n  DDD.MM.H\n  DDD\xB0MM.H\n  DDD MM H\n\n" +
                msg.tools.coords.lon_dms_2
            );
            return false;
        }
        result = CONVERT.dms_deg(lat_dd_mm_ss, lon_dd_mm_ss);
        $('#lat_dddd').val(result.lat);
        $('#lon_dddd').val(result.lon);
        return true;
    }
}

var NAVTEX = {
    init: function() {
        $('#frm_navtex').on('submit', function(){
            return false;
        });
        $('#translateMumbo').on('click', function() {
            $('#navtex2').val(NAVTEX.mumboToText($('#navtex1').val()));
        });
        $('#clearoutMumbo').on('click', function() {
            $('#navtex1').val('');
        });
        $('#clearoutText').on('click', function() {
            $('#navtex2').val('');
        });
        $('#clearoutAll').on('click', function() {
            $('#navtex1').val('');
            $('#navtex2').val('');
        });
        $('#translateText').on('click', function() {
            $('#navtex1').val(NAVTEX.textToMumbo($('#navtex2').val()));
        });
        $('#close').on('click', function(){
            window.close();
        })
    },
    mumboChars: "-?:$3!&#8*().,9014'57=2/6+",
    textChars:  "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
    mumboChar: function(chars) {
        var pos = NAVTEX.textChars.indexOf(chars);
        if (pos > -1) {
            return NAVTEX.mumboChars.charAt(pos);
        }
        return chars;
    },
    textChar: function(chars) {
        var pos = NAVTEX.mumboChars.indexOf(chars);
        if (pos > -1) {
            return NAVTEX.textChars.charAt(pos);
        }
        return chars;
    },
    textToMumbo: function(input) {
        var i, output = '';
        for (i=0; i<input.length; i++) {
            output += NAVTEX.mumboChar(input.charAt(i).toUpperCase());
        }
        return output;
    },
    mumboToText: function(input) {
        var i, output = '';
        for (i = 0; i < input.length; i++) {
            output += NAVTEX.textChar(input.charAt(i));
        }
        return output;
    }
}

var SUNRISE = {
    // Refactored from code created by Gene Davis - Computer Support Group: Dec 6 1994 - Oct 8 2001
    init: function() {
        var now = new Date();
        var sunrise_clear = $('#sunrise_clear');
        var sunrise_go =    $('#sunrise_go');
        var sunrise_gsq =   $('#sunrise_gsq');
        var sunrise_lat =   $('#sunrise_lat');
        var sunrise_lon =   $('#sunrise_lon');
        var sunrise_DD =    $('#sunrise_DD');
        var sunrise_MM =    $('#sunrise_MM');
        var sunrise_YYYY =  $('#sunrise_YYYY');

        $('#close').on('click', function(){
            window.close();
        })
        sunrise_YYYY
            .on('change', function() {
                VALIDATE.int(this, 1901, 2099, 'Year')
            })
            .val(now.getUTCFullYear());
        sunrise_MM
            .on('change', function() {
                VALIDATE.int(this, 1, 12, 'Month');
            })
            .val(now.getUTCMonth()+1);
        sunrise_DD
            .on('change', function() {
                VALIDATE.int(this, 1, 31, 'Date')
            })
            .val(now.getUTCDate());
        sunrise_gsq.on('change', function() {
            if (!VALIDATE.gsq($(this).val())) {
                alert(msg.tools.coords.gsq_format);
            } else {
                SUNRISE.gsq_deg($(this).val());
            }
        });
        sunrise_lat.on('change', function() {
            VALIDATE.float($(this).val(), -90.0, 90.0, 'Latitude')
        });
        sunrise_lon.on('change', function() {
            VALIDATE.float(this, -180.0, 180.0, 'Longitude');
        });
        sunrise_go.on('click', function(){
            $('#sunrise_result').val(SUNRISE.formValues());
            SUNRISE.cookie_set();
            return false;
        });
        sunrise_clear.on('click', function(){
            SUNRISE.cookie_clear();
        });
        if (SUNRISE.cookie_get('sunrise')) {
            var result = SUNRISE.cookie_get("sunrise").split("|");
            sunrise_gsq.val(result[0]);
            sunrise_lat.val(result[1]);
            sunrise_lon.val(result[2]);
        }
    },
    cookie_clear: function() {
        document.cookie = 'sunrise=||;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/';
    },
    cookie_get: function(which) {
        var cookies =		document.cookie;
        var pos =		cookies.indexOf(which+"=");
        if (pos === -1) {
            return false;
        }
        var start =	pos + which.length+1;
        var end =	cookies.indexOf(";",start);
        if (end === -1) {
            end =	cookies.length;
        }
        return unescape(cookies.substring(start, end));
    },
    cookie_set: function() {
        var nextYear =	new Date();
        nextYear.setFullYear(nextYear.getFullYear()+1);
        document.cookie = 'sunrise=' +
            $('#sunrise_gsq').val() + '|' +
            $('#sunrise_lat').val() + '|' +
            $('#sunrise_lon').val() + ';expires=' + nextYear.toGMTString() + '; path=/';
    },
    formValues: function() {
        var latText =   "Latitude"
        var lonText =   "Longitude"
        var yearText =  "Year"
        var monthText = "Month"
        var dayText =   "Date"

        var lat =   parseFloat($('#sunrise_lat').val());
        var lon =   parseFloat($('#sunrise_lon').val());
        var year =  parseInt($('#sunrise_YYYY').val());
        var month = parseInt($('#sunrise_MM').val());
        var day =   parseInt($('#sunrise_DD').val());

        if (!VALIDATE.float(lat, -90.0, 90.0, latText)) {
            return;
        }
        if (!VALIDATE.float(lon, -180.0, 180.0, lonText)) {
            return;
        }
        if (!VALIDATE.int(year, 1901, 2099, yearText)) {
            return;
        }
        if (!VALIDATE.int(month, 1, 12, monthText) ) {
            return;
        }
        var absLat = Math.abs(lat);
        var absLon = Math.abs(lon);

        var monthMax;
        if (month === 2 && (year % 4) === 0) {
            monthMax = 29;
        } else if (month === 2) {
            monthMax = 28;
        } else if ((month === 4) || (month === 6) || (month === 9) || (month === 11)) {
            monthMax = 30;
        } else {
            monthMax = 31
        }
        if (!VALIDATE.int(day, 1, monthMax, dayText)) {
            return
        }
        sunRiseSet(year, month, day, lat, lon);
        civTwilight(year, month, day, lat, lon);

        var twsText =   "Twilight starts: ";
        var tweText =   "Twilight ends:   ";
        var twAllText = "Twilight all night";
        var twNoText =  "No twilight this day";

        var srText = "Sunrise:         ";
        var ssText = "Sunset:          ";
        var sAllText = "Sun is up 24 hrs";
        var sNoText = "Sun is down 24 hrs";

        var resultText =
            "On " + year + "-" + (month < 10 ? '0' : '') + month + "-" + (day < 10 ? '0' : '') +
            day + "\nAt " + absLat + (lat < 0 ? "S" : "N") +
            "  / " + absLon + (lon < 0 ? 'W' : 'E') + "\n" +
            "-----------------------\n" +
            "Sunrise / Sunset UTC\n" +
            "-----------------------\n";

        var twst_h = Math.floor(twStartT)
        var twst_m = Math.floor((twStartT - twst_h)*60)

        var sris_h = Math.floor(sRiseT)
        var sris_m = Math.floor((sRiseT - sris_h)*60)

        var sset_h = Math.floor(sSetT)
        var sset_m = Math.floor((sSetT - sset_h)*60)

        var twen_h = Math.floor(twEndT)
        var twen_m = Math.floor((twEndT - twen_h)*60)

        if (twStatus == 0) {
            resultText += twsText;
            if (twst_h < 10) {
                resultText += "0";
            }
            resultText += twst_h + "."
            if (twst_m < 10) {
                resultText += "0";
            }
            resultText += twst_m + "\n";
        } else if(twStatus > 0 && srStatus <= 0) {
            resultText += twAllText + "\n";
        } else {
            resultText += twNoText + "\n";
        }

        if (srStatus == 0) {
            resultText += srText;
            if (sris_h < 10) {
                resultText += "0";
            }
            resultText += sris_h + ".";
            if (sris_m < 10) {
                resultText += "0";
            }
            resultText += sris_m + "\n";
            resultText += ssText;
            if (sset_h < 10) {
                resultText += "0"
            }
            resultText += sset_h + ".";
            if (sset_m < 10) {
                resultText += "0";
            }
            resultText += sset_m + "\n";
        } else if(srStatus > 0) {
            resultText += sAllText + "\n";
        } else {
            resultText += sNoText + "\n";
        }

        if (twStatus == 0) {
            resultText += tweText;
            if (twen_h < 10) {
                resultText += "0";
            }
            resultText += twen_h + ".";
            if (twen_m < 10) {
                resultText += "0";
            }
            resultText += twen_m
        }
        resultText += "\n-----------------------";
        resultText += "\n(From "+document.title.substr(0,3)+")";
        return resultText;
    },

    gsq_deg: function() {
        var gsq, result;
        gsq = $('#sunrise_gsq').val();
        if (!VALIDATE.gsq(gsq)) {
            alert(msg.tools.coords.gsq_format);
            return false;
        }
        result = CONVERT.gsq_deg(gsq);
        $('#sunrise_lat').val(result.lat);
        $('#sunrise_lon').val(result.lon);
        return true;
    },
}




// Global variables:
var twAngle = -6.0;    // For civil twilight, set to -12.0 for
                       // nautical, and -18.0 for astr. twilight

var srAngle = -35.0/60.0;    // For sunrise/sunset

var sRiseT;        // For sunrise/sunset times
var sSetT;
var srStatus;

var twStartT;      // For twilight times
var twEndT;
var twStatus;

var sDIST;     // Solar distance, astronomical units
var sRA;       // Sun's Right Ascension
var sDEC;      // Sun's declination

var sLON;      // True solar longitude


//-------------------------------------------------------------
// A function to compute the number of days elapsed since
// 2000 Jan 0.0  (which is equal to 1999 Dec 31, 0h UT)
//-------------------------------------------------------------
function dayDiff2000(y,m,d){
    return (367.0*(y)-((7*((y)+(((m)+9)/12)))/4)+((275*(m))/9)+(d)-730530.0);
}


var RADEG = 180.0 / Math.PI
var DEGRAD = Math.PI / 180.0


//-------------------------------------------------------------
// The trigonometric functions in degrees

function sind(x) { return Math.sin((x)*DEGRAD); }
function cosd(x) { return Math.cos((x)*DEGRAD); }
function acosd(x) { return (RADEG*Math.acos(x)); }

function atan2d(y,x) {
    var at2 = (RADEG*Math.atan(y/x));
    if (x < 0 && y < 0) {
        at2 -= 180;
        return at2;
    }
    if (x < 0 && y > 0) {
        at2 += 180;
    }
    return at2;
}

//-------------------------------------------------------------
// This function computes times for sunrise/sunset.
// Sunrise/sunset is considered to occur when the Sun's upper
// limb is 35 arc minutes below the horizon (this accounts for
// the refraction of the Earth's atmosphere).
//-------------------------------------------------------------
function sunRiseSet(year, month, day, lat, lon) {
    return sunTimes( year, month, day, lat, lon, srAngle, 1);
}

//-------------------------------------------------------------
// This function computes the start and end times of civil
// twilight. Civil twilight starts/ends when the Sun's center
// is 6 degrees below the horizon.
//-------------------------------------------------------------
function civTwilight(year,month,day,lat,lon) {
    return ( sunTimes( year, month, day, lat, lon, twAngle, 0) )
}

//-------------------------------------------------------------
// The main function for sun rise/set times
//
// year,month,date = calendar date, 1801-2099 only.
// Eastern longitude positive, Western longitude negative
// Northern latitude positive, Southern latitude negative
//
// altit = the altitude which the Sun should cross. Set to
//         -35/60 degrees for rise/set, -6 degrees for civil,
//         -12 degrees for nautical and -18 degrees for
//         astronomical twilight.
//
// sUppLimb: non-zero -> upper limb, zero -> center. Set to
//           non-zero (e.g. 1) when computing rise/set times,
//           and to zero when computing start/end of twilight.
//
// Status:  0 = sun rises/sets this day, times stored in Global
//              variables
//          1 = sun above the specified "horizon" 24 hours.
//              Rising set to time when the sun is at south,
//              minus 12 hours while Setting is set to the
//              south time plus 12 hours.
//         -1 = sun is below the specified "horizon" 24 hours.
//              Rising and Setting are both set to the time
//              when the sun is at south.
//-------------------------------------------------------------
function sunTimes(year, month, day, lat, lon, altit, sUppLimb) {
    var dayDiff    // Days since 2000 Jan 0.0 (negative before)
    var sRadius    // Sun's apparent radius
    var diuArc     // Diurnal arc
    var sSouthT    // Time when Sun is at south
    var locSidT    // Local sidereal time

    var stCode = 0     // Status code from function - usually 0
    dayDiff = dayDiff2000(year,month,day) + 0.5 - lon/360.0;   // Compute dayDiff of 12h local mean solar time
    locSidT = revolution( GMST0(dayDiff) + 180.0 + lon );   // Compute local sideral time of this moment
    sunRaDec( dayDiff );                                       // Compute Sun's RA + Decl at this moment
    sSouthT = 12.0 - rev180(locSidT - sRA)/15.0;			   // Compute time when Sun is at south - in hours UT
    sRadius = 0.2666 / sDIST;                                  // Compute the Sun's apparent radius, degrees
    if ( sUppLimb != 0) {                                      // Do correction to upper limb, if necessary
        altit -= sRadius;
    }
    /* Compute the diurnal arc that the Sun traverses */
    /* to reach the specified altitude altit:         */
    var cost;
    cost = ( sind(altit) - sind(lat) * sind(sDEC) ) / ( cosd(lat) * cosd(sDEC) );

    if ( cost >= 1.0 ) {
        stCode = -1;
        diuArc = 0.0;       // Sun always below altit
    } else if ( cost <= -1.0 ) {
        stCode = 1;
        diuArc = 12.0;      // Sun always above altit
    } else {
        diuArc = acosd(cost) / 15.0;   // The diurnal arc, hours
    }

    /* Store rise and set times - in hours UT */
    if ( sUppLimb != 0) {     // For sunrise/sunset
        sRiseT = sSouthT - diuArc;
        if (sRiseT < 0) {       // Sunrise day before
            sRiseT += 24;
        }
        sSetT  = sSouthT + diuArc;
        if(sSetT > 24) {        // Sunset next day
            sSetT -= 24;
        }
        srStatus = stCode;
    }
    else {                     // For twilight times
        twStartT = sSouthT - diuArc;
        if (twStartT < 0) {
            twStartT += 24;
        }
        twEndT  = sSouthT + diuArc;
        if(twEndT > 24) {
            twEndT -= 24;
        }
        twStatus = stCode;
    }
}
//================== sunTimes() =====================


//-------------------------------------------------------------
// This function computes the sun's spherical coordinates
//-------------------------------------------------------------
function sunRaDec(dayDiff)
{
    var eclObl;   // Obliquity of ecliptic
                  // (inclination of Earth's axis)
    var x;
    var y;
    var z;

    /* Compute Sun's ecliptical coordinates */

    sunPos( dayDiff );

    /* Compute ecliptic rectangular coordinates (z=0) */

    x = sDIST * cosd(sLON);

    y = sDIST * sind(sLON);

    /* Compute obliquity of ecliptic */
    /* (inclination of Earth's axis) */

//  eclObl = 23.4393 - 3.563E-7 * dayDiff;

    eclObl = 23.4393 - 3.563/10000000 * dayDiff;  // for Opera

    /* Convert to equatorial rectangular coordinates */
    /* - x is unchanged                               */

    z = y * sind(eclObl);
    y = y * cosd(eclObl);

    /* Convert to spherical coordinates */

    sRA = atan2d( y, x );
    sDEC = atan2d( z, Math.sqrt(x*x + y*y) );

} //================= sunRaDec() =======================



//-------------------------------------------------------------
// Computes the Sun's ecliptic longitude and distance
// at an instant given in dayDiff, number of days since
// 2000 Jan 0.0.  The Sun's ecliptic latitude is not
// computed, since it's always very near 0.
//-------------------------------------------------------------
function sunPos(dayDiff)
{
    var M;       // Mean anomaly of the Sun
    var w;       // Mean longitude of perihelion
                 // Note: Sun's mean longitude = M + w
    var e;       // Eccentricity of Earth's orbit
    var eAN;     // Eccentric anomaly
    var x;       // x, y coordinates in orbit
    var y;
    var v;       // True anomaly

    /* Compute mean elements */

    M = revolution( 356.0470 + 0.9856002585 * dayDiff );

//  w = 282.9404 + 4.70935E-5 * dayDiff;
//  e = 0.016709 - 1.151E-9 * dayDiff;

    w = 282.9404 + 4.70935/100000 * dayDiff;    // for Opera
    e = 0.016709 - 1.151/1000000000 * dayDiff;  // for Opera

    /* Compute true longitude and radius vector */

    eAN = M + e * RADEG * sind(M) * ( 1.0 + e * cosd(M) );

    x = cosd(eAN) - e;
    y = Math.sqrt( 1.0 - e*e ) * sind(eAN);

    sDIST = Math.sqrt( x*x + y*y );    // Solar distance

    v = atan2d( y, x );                // True anomaly

    sLON = v + w;                      // True solar longitude

    if ( sLON >= 360.0 )
        sLON -= 360.0;                   // Make it 0..360 degrees

} //=================== sunPos() =============================


function revolution( x ) {
    return (x - 360.0 * Math.floor( x * (1.0 / 360.0) ));
}

function rev180( x ) {
    return ( x - 360.0 * Math.floor( x * (1.0 / 360.0) + 0.5 ) );
}


//-------------------------------------------------------------
// This function computes GMST0, the Greenwhich Mean Sidereal
// Time at 0h UT (i.e. the sidereal time at the Greenwhich
// meridian at 0h UT).
// GMST is then the sidereal time at Greenwich at any time of
// the day.  GMST0 is generalized as well, and is defined as:
//
//  GMST0 = GMST - UT
//
// This allows GMST0 to be computed at other times than 0h UT
// as well.  While this sounds somewhat contradictory, it is
// very practical:
// Instead of computing GMST like:
//
//  GMST = (GMST0) + UT * (366.2422/365.2422)
//
// where (GMST0) is the GMST last time UT was 0 hours, one simply
// computes:
//
//  GMST = GMST0 + UT
//
// where GMST0 is the GMST "at 0h UT" but at the current moment!
// Defined in this way, GMST0 will increase with about 4 min a
// day.  It also happens that GMST0 (in degrees, 1 hr = 15 degr)
// is equal to the Sun's mean longitude plus/minus 180 degrees!
// (if we neglect aberration, which amounts to 20 seconds of arc
// or 1.33 seconds of time)
//-------------------------------------------------------------
function GMST0( dayDiff ) {
    var const1 = 180.0 + 356.0470 + 282.9404;
    var const2 = 0.9856002585 + 4.70935E-5;
    return ( revolution( const1 + const2 * dayDiff ) );
}
