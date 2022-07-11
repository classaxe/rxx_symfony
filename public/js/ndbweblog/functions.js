// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Web Log                  *
// * Filename:  functions.js                 *
// * Created:   2022-07-11 (MF)              *
// *******************************************
version = "1.1.30";
// ###########################################
// # Inline code:                            #
// ###########################################
// Notes:
// In this file, the order of code shown is as follows:
// 1) Inline code
// 2) Object constructors in alphabetical order of object name
// 3) Functions in alphabetical order of function name


// ++++++++++++++++++++++++++++++++++
// + Initialse data container arrays+
// ++++++++++++++++++++++++++++++++++
stats = [];	                    // Global variable hold stats data.
station = [];	                // Global variable to hold station data
logbook = [];	                // Used when printing out text listing
unregistered_countries = [];    // Used if countries are logged but not defined in countries.js
unregistered_stations = [];	    // Used if stations are logged but not defined in stations.js
cnt_arr = [];	                // Used to hold details on countries
rgn_arr = [];	                // Used to hold details on regions
sta_arr = [];	                // Holds data on states (Australia, US and Canada only)

cookie = [];
var now = new Date();
var cur_yyyy = now.getFullYear();				// Default value for year on entry
var cur_mm = ("0" + (now.getMonth() + 1));		// Default value for month (requires a leading zero)
cur_mm = cur_mm.substr((cur_mm.length - 2), 2)
var txt_options, i;

var list_selected = false


// Add (ID) to mark station as OTA
css = (document.location.protocol == 'file:' ? '' : '../../../../css/ndbweblog/') + 'style.css';

if (!get_cookie('popup_warning_given') || eval(get_cookie('popup_warning_given')) > 0) {
    var expires = new Date();
    expires.setFullYear(expires.getFullYear() + 1)
    if (!get_cookie('popup_warning_given')) {
        document.cookie = "popup_warning_given=3;expires=" + expires.toGMTString();
    }
    if (!get_cookie('popup_warning_given')) {
        alert("COOKIES\n\nNDB WebLog uses Cookies to change modes\nand remember settings.\n\nPlease allow cookies for this site to\nuse NDB WebLog.");
    }
    else {
        if (eval(get_cookie('popup_warning_given')) > 0) {
            document.cookie = "popup_warning_given=" + (eval(get_cookie('popup_warning_given') - 1)) + ";expires=" + expires.toGMTString();
            var out = "POPUP BLOCKERS\n\nNDB WebLog uses popups to display data.\nIf you have a popup blocker installed, you   \nshould disable it for this system.\n\n";
            switch (get_cookie('popup_warning_given')) {
                case "2":
                    out += "This message will be shown TWO more times\n(Sorry if it doesn't apply to you).";
                    break;
                case "1":
                    out += "This message will be shown ONE more time.";
                    break;
                case "0":
                    out += "This message will not be shown again\n(unless you delete your cookies)";
                    break;
            }
            alert(out);
        }
    }
}

progress();

// ++++++++++++++++++++++++++++++++++
// + Find values for user selection +
// ++++++++++++++++++++++++++++++++++
if (get_cookie('list_selected')) {
    txt_options = get_cookie("list_selected").split("|");
    i = 0;
    cookie['sel_mm'] = txt_options[i++];
    cookie['sel_sort'] = txt_options[i++];
    cookie['sel_yyyy'] = txt_options[i++];
}

sel_mm = (cookie['sel_mm'] ? cookie['sel_mm'] : cur_mm);
sel_sort = (cookie['sel_sort'] ? cookie['sel_sort'] : "khz");
sel_yyyy = (cookie['sel_yyyy'] ? cookie['sel_yyyy'] : cur_yyyy);

var list_options = false;
if (get_cookie("list_options")) {
    txt_options = get_cookie("list_options").split("|");
    i = 0;
    cookie['format'] = txt_options[i++];
    cookie['h_dxw'] = txt_options[i++];
    cookie['h_gsq'] = txt_options[i++];
    cookie['h_ident'] = txt_options[i++];
    cookie['h_lifelist'] = txt_options[i++];
    cookie['h_notes'] = txt_options[i++];
    cookie['map_zoom'] = txt_options[i++];
    cookie['units'] = txt_options[i++];
    // Add new options in chronological order for forward compatability
    cookie['mod_abs'] = txt_options[i++];
    cookie['h_logs'] = txt_options[i++];
    cookie['language'] = txt_options[i++];
}

cookie['format'] = (cookie["format"] ? cookie["format"] : "yyyymmdd");
cookie['units'] = (cookie["units"] ? cookie["units"] : "km");
cookie['map_zoom'] = (cookie["map_zoom"] ? cookie["map_zoom"] : "5");


// ++++++++++++++++++++++++++++++++++
// + Establish daylight for location+
// ++++++++++++++++++++++++++++++++++
utc_daylight = 10 + utc_offset;
utc_daylight_array = [];

for (i = utc_daylight; i < utc_daylight + 4; i++) {
    utc_daylight_array[i - utc_daylight] = lead(i);
}

function isLocalDaylight(hours, offset) {
    var hour_start = 10;
    var hour_end = 14;
    var dt = (24 + hours + offset) % 24;
    return (dt >= hour_start && dt <= hour_end);
}

// ++++++++++++++++++++++++++++++++++
// + Initialise month name arrays   +
// ++++++++++++++++++++++++++++++++++

switch (cookie['language']) {
    case "French":
        months = ['Janvier', 'F�vrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Ao�t', 'Septembre', 'Octobre', 'Novembre', 'D�cembre'];
        break;
    case "German":
        months = ['Januar', 'Februar', 'M�rz', 'April', 'Mag', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
        break;
    case "Italian":
        months = ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Pu�', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'];
        break;
    case "Portuguese":
        months = ['Janeiro', 'Fevereiro', 'Mar�o', 'Abril', 'Pode', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        break;
    case "Spanish":
        months = ['Enero', 'Febrero', 'Marcha', 'Abril', 'Puede', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        break;
    default:
        months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        break
}

mm_arr = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];


// ###########################################
// # Object Constructors:                    #
// ###########################################
// ************************************
// * COUNTRY Constructor:             *
// ************************************
function COUNTRY(cnt, name, rgn) {
    var a = [];
    a.name = name;
    a.rgn = rgn;
    cnt_arr[cnt] = a;
}


// ************************************
// * LOG Constructor:                 *
// ************************************
function LOG(khz, call, yyyymmdd, hhmm, notes) {

    if (!station) {
        return
    }

    // ++++++++++++++++++++++++++++++++++
    // + Extract variables              +
    // ++++++++++++++++++++++++++++++++++
    var id = khz + "-" + call;
    if (!station[id]) {
        unregistered_stations[unregistered_stations.length] = id;
        return;
    }

    stats.last_date = yyyymmdd;		// Assume last entry in log is the last entry
    stats.last_time = hhmm;
    var yyyy = yyyymmdd.substr(0, 4);
    var mm = yyyymmdd.substr(4, 2);
    var dd = yyyymmdd.substr(6, 2);
    var hh = hhmm.substr(0, 2);


    // ++++++++++++++++++++++++++++++++++
    // + Prepare stats counts:          +
    // ++++++++++++++++++++++++++++++++++
    // Stats counters used in Statistics output
    // Prepare for global stats counts:
    if (!stats.all) {
        stats.all = [];	// Tally Stations (lifetime log)
        stats.year = [];	// Tally Stations (monthly log)
    }

    // Prepare for annual stats counts:
    if (!stats.year[yyyy]) {
        stats.year[yyyy] = [];	// Initialise year
        stats.year[yyyy].id = [];	// Tally stations
        stats.year[yyyy].cnt = [];	// Tally countries (with states)
        stats.year[yyyy].rgn = [];	// Tally countries (with states)
        for (i in rgn_arr) {
            stats.year[yyyy].rgn[i] = 0;	// Count
        }
        stats.year[yyyy].n60 = 0;			// Count Stations North of 60
        stats.year[yyyy].new_stations = 0;			// Count new stations
        stats.year[yyyy].new_stations_ever = 0;		// Count new stations
        stats.year[yyyy].max_day = 0;			// Log furthest day-time DX
        stats.year[yyyy].max_night = 0;			// Log furthest night-time DX
        stats.year[yyyy].max_dxw = 0;			// Log furthest DX/W
    }

    // Prepare for monthly stats counts
    if (!stats.year[yyyy][mm]) {
        stats.year[yyyy][mm] = [];	// Prepare structure
        stats.year[yyyy][mm].id = [];	// Tally stations
        stats.year[yyyy][mm].cnt = [];	// Tally countries (with states)
        stats.year[yyyy][mm].rgn = [];	// Tally countries (with states)
        for (i in rgn_arr) {
            stats.year[yyyy][mm].rgn[i] = 0;	// Count
        }
        stats.year[yyyy][mm].dx_d = [];
        stats.year[yyyy][mm].dx_n = [];
        stats.year[yyyy][mm].dx_x = [];
        stats.year[yyyy][mm].new_stations = 0;		// Count new stations
        stats.year[yyyy][mm].new_stations_ever = 0;		// Count new stations
        stats.year[yyyy][mm].n60 = 0;		// Count stations North of 60
        stats.year[yyyy][mm].max_day = 0;		// Log furthest day-time DX
        stats.year[yyyy][mm].max_night = 0;		// Log furthest night-time DX
        stats.year[yyyy][mm].max_dxw = 0;		// Log furthest DX/W
    }

    // ++++++++++++++++++++++++++++++++++
    // + Station registered?            +
    // ++++++++++++++++++++++++++++++++++
    // logbook used for text output and popup_details modes
    if (!logbook[id]) {	// Then station never logged before
        logbook[id] = [];
        logbook[id]['entry'] = [];
    }
    var entry = [];
    entry['yyyymmddhhmm'] = yyyymmdd + "" + hhmm;
    entry['notes'] = notes;
    logbook[id]['entry'][logbook[id]['entry'].length] = entry;


    // ++++++++++++++++++++++++++++++++++
    // + Prepare station[id].log counts +
    // ++++++++++++++++++++++++++++++++++
    // station[id].log used for main listing output
    if (!station[id].all_date) {
        station[id].all_yyyymmddhhmm = yyyymmdd + "" + hhmm;	// Used in sorting by date
        switch (cookie['format']) {
            case "dd.mm.yyyy":
                station[id].all_date = dd + "." + mm + "." + yyyy;
                break;
            case "ddmmyyyy":
                station[id].all_date = dd + "/" + mm + "/" + yyyy;
                break;
            case "mmddyyyy":
                station[id].all_date = mm + "/" + dd + "/" + yyyy;
                break;
            default:
                station[id].all_date = yyyy + "" + mm + "" + dd;
                break;
        }
        station[id].all_time = hhmm;
        station[id].all_notes = [];
        if (!stats.all[id]) {
            stats.year[yyyy][mm].new_stations_ever++
            stats.year[yyyy].new_stations_ever++
            stats.all[id] = true;	// used in lifetime log stats
        }
    }
    if (!station[id].log[yyyy]) {
        station[id].log[yyyy] = [];
        station[id].log[yyyy].rx = [];
    }
    if (!station[id].log[yyyy][mm]) {
        station[id].log[yyyy][mm] = [];
        station[id].log[yyyy][mm].notes = [];
    }

    if (!stats.year[yyyy].id[id]) {
        stats.year[yyyy].new_stations++;			// and add it to the new stations count
        stats.year[yyyy][mm].new_stations++;		// and add it to the new stations count, and add it to this month's total also.
    }

    // ++++++++++++++++++++++++++++++++++
    // + Input station[id].log counts   +
    // ++++++++++++++++++++++++++++++++++
    // Input latest logging for the month
    // Daytime or nighttime logging?
    if (isLocalDaylight(hh, -1 * utc_offset)) {
        station[id].log[yyyy][mm].day_hhmm = hhmm;
        station[id].log[yyyy][mm].day_dd = dd;
        station[id].log[yyyy].day_dd = dd;				// Put a daytime value in if you have one - doesn't matter which one
        notes = ((notes) ? (((monthly) ? ("D:") : ("")) + notes) : (""))
        if (station[id].dx > stats.year[yyyy].max_day) {
            stats.year[yyyy].max_day = station[id].dx;
        }
        if (station[id].dx > stats.year[yyyy][mm].max_day) {
            stats.year[yyyy][mm].max_day = station[id].dx;
        }
    }
    else {
        station[id].log[yyyy][mm].night_hhmm = hhmm;
        station[id].log[yyyy][mm].night_dd = dd;
        station[id].log[yyyy].night_dd = dd;	// Put a nighttime value in if you have one - doesn't matter which one
        notes = ((notes) ? (((monthly) ? ("N:") : ("")) + notes) : (""))
        if (station[id].dx > stats.year[yyyy].max_night) {
            stats.year[yyyy].max_night = station[id].dx;
        }
        if (station[id].dx > stats.year[yyyy][mm].max_night) {
            stats.year[yyyy][mm].max_night = station[id].dx;
        }
    }


    // Don't count unknowns in country reports
    if (!(station[id].cnt == "?" || station[id].sta == "?")) {
        var cnt = station[id].cnt + "_" + station[id].sta;

        // ++++++++++++++++++++++++++++++++++
        // + Check yearly totals            +
        // ++++++++++++++++++++++++++++++++++
        if (!stats.year[yyyy].id[id]) {			// Has station been logged this year?
            stats.year[yyyy].id[id] = 1;			// No, so log it now
            if (station[id].dxw > stats.year[yyyy].max_dxw) {
                stats.year[yyyy].max_dxw = station[id].dxw;
            }
            if (station[id].lat >= 60) {				// If North of 60 degrees,
                stats.year[yyyy].n60++;				// Add it to N of 60 count.
            }
            if (!stats.year[yyyy].cnt[cnt]) {			// ... and check if the country has been logged
                stats.year[yyyy].cnt[cnt] = [];
                stats.year[yyyy].cnt[cnt].count = 1;	// No, Record the country
                stats.year[yyyy].cnt[cnt].best_dx_ndb = 0;
                stats.year[yyyy].cnt[cnt].best_dx_dgps = 0;
                stats.year[yyyy].cnt[cnt].best_dx_navtex = 0;
                switch (station[id].call.substr(0, 1)) {
                    case "#":
                        stats.year[yyyy].cnt[cnt].best_dx_dgps = station[id].dx;	// Record DX as best DX from this country
                        stats.year[yyyy].cnt[cnt].best_id_dgps = id;	// Record the ID as the best DX from this country
                        break;
                    case "$":
                        stats.year[yyyy].cnt[cnt].best_dx_navtex = station[id].dx;	// Record DX as best DX from this country
                        stats.year[yyyy].cnt[cnt].best_id_navtex = id;	// Record the ID as the best DX from this country
                        break;
                    default:
                        stats.year[yyyy].cnt[cnt].best_dx_ndb = station[id].dx;	// Record DX as best DX from this country
                        stats.year[yyyy].cnt[cnt].best_id_ndb = id;	// Record the ID as the best DX from this country
                        break;
                }
                if (station[id].rgn) {
                    stats.year[yyyy].rgn[station[id].rgn]++;			// And increment the counter for that region
                }
                else {
                    alert("NDB WEBLOG ERROR\n\nStation " + id + " is not defined as belonging to any country.\n\n" +
                        "Please check the stations data file and enter a valid ITU\ncountry code.");
                }
            }
            else {											// Else country has already been logged
                stats.year[yyyy].cnt[cnt].count++;					// Add another station to it
                switch (station[id].call.substr(0, 1)) {
                    case "#":
                        if (station[id].dx > stats.year[yyyy].cnt[cnt].best_dx_dgps) {
                            stats.year[yyyy].cnt[cnt].best_dx_dgps = station[id].dx;	// Record DX as best DX from this country
                            stats.year[yyyy].cnt[cnt].best_id_dgps = id;				// Record the ID as the best DX from this country
                        }
                        break;
                    case "$":
                        if (station[id].dx > stats.year[yyyy].cnt[cnt].best_dx_navtex) {
                            stats.year[yyyy].cnt[cnt].best_dx_navtex = station[id].dx;	// Record DX as best DX from this country
                            stats.year[yyyy].cnt[cnt].best_id_navtex = id;				// Record the ID as the best DX from this country
                        }
                        break;
                    default:
                        if (station[id].dx > stats.year[yyyy].cnt[cnt].best_dx_ndb) {
                            stats.year[yyyy].cnt[cnt].best_dx_ndb = station[id].dx;	// Record DX as best DX from this country
                            stats.year[yyyy].cnt[cnt].best_id_ndb = id;				// Record the ID as the best DX from this country
                        }
                        break;
                }
            }
        }

        // ++++++++++++++++++++++++++++++++++
        // + Check monthly totals           +
        // ++++++++++++++++++++++++++++++++++
        if (!stats.year[yyyy][mm].id[id]) {			// Has station been logged this month?
            stats.year[yyyy][mm].id[id] = 1;		// No, so log it now
            if (station[id].dxw > stats.year[yyyy][mm].max_dxw) {
                stats.year[yyyy][mm].max_dxw = station[id].dxw;
            }
            if (station[id].lat >= 60) {				// If North of 60 degrees,
                stats.year[yyyy][mm].n60++				// Add it to N of 60 count.
            }
            if (!stats.year[yyyy][mm].cnt[cnt]) {		// ... and check if the country has been logged
                stats.year[yyyy][mm].cnt[cnt] = 1;		// No, Record the country
                stats.year[yyyy][mm].rgn[station[id].rgn]++			// And increment the counter for that region
            }
            else {								// Else country has already been logged
                stats.year[yyyy][mm].cnt[cnt]++			// Add another station to it
            }
        }
    }

    // Used in stats output and main listing
    if (station[id].log[yyyy][mm].night_dd && station[id].log[yyyy][mm].day_dd) {
        station[id].log[yyyy].rx[mm] = "X";
    }
    else if (station[id].log[yyyy][mm].night_dd) {
        station[id].log[yyyy].rx[mm] = "N";
    }
    else if (station[id].log[yyyy][mm].day_dd) {
        station[id].log[yyyy].rx[mm] = "D";
    }

    // Used in stats report
    if (station[id].log[yyyy].night_dd && station[id].log[yyyy].day_dd) {
        station[id].log[yyyy].rx_x = "X";
    }
    else if (station[id].log[yyyy].night_dd) {
        station[id].log[yyyy].rx_n = "N";
    }
    else if (station[id].log[yyyy].day_dd) {
        station[id].log[yyyy].rx_d = "D";
    }

    if (notes != "") {
        station[id].all_notes[station[id].all_notes.length] = notes;
        station[id].log[yyyy][mm].notes[station[id].log[yyyy][mm].notes.length] = notes;
    }
}


// ************************************
// * REGION Constructor:              *
// ************************************
function REGION(rgn, name) {
    rgn_arr[rgn] = name;
}


// ************************************
// * STATE Constructor:               *
// ************************************
function STATE(sta, name, cnt) {
    if (!sta_arr[cnt]) {
        sta_arr[cnt] = [];
    }
    sta_arr[cnt][sta] = name;
}

// ************************************
// * STATION Constructor:             *
// ************************************
function STATION(khz, call, qth, sta, cnt, cyc, daid, lsb, usb, pwr, lat, lon, notes, rww) {
    if (typeof(cnt_arr[cnt]) == "undefined") {
        unregistered_countries[unregistered_countries.length] = khz + "-" + call + " - code given was " + cnt;
        return;
    }
    var a = []
    a.id = khz + "-" + call;
    a.khz = khz;
    a.call = call;
    a.display = call.split(";")[0];
    a.qth = qth;
    a.sta = trim(sta);
    a.cnt = cnt;
    a.rgn = cnt_arr[cnt].rgn
    a.cyc = (cyc != " " && cyc != "?" ? cyc : "");
    a.daid = daid;
    lsb = (lsb && lsb != " " && lsb != "?" ? Math.abs(parseInt(lsb)) : 0);
    usb = (usb && usb != " " && usb != "?" ? Math.abs(parseInt(usb)) : 0);
    pwr = (pwr && pwr != " " ? (isNaN(parseInt(pwr)) ? 0 : parseInt(pwr)) : 0);

    if (cookie['mod_abs'] == '1') {
        a.lsb = (lsb ? format_3sf(parseInt(khz) - (lsb / 1000)) : 0)
        a.usb = (usb ? format_3sf(parseInt(khz) + (usb / 1000)) : 0)
    }
    else {
        a.lsb = lsb;
        a.usb = usb;
    }

    a.pwr = pwr;
    a.lat = lat;
    a.lon = lon;
    a.ident = get_ident(a.display);
    a.notes = notes;
    if (lat + lon) {
        var n = get_range_bearing(qth_lat, qth_lon, lat, lon, cookie['units'])
        a.dir = n[0];
        a.dx = n[1];
        a.dxw = a.pwr ? Math.round(a.dx * 10 / a.pwr) / 10 : 0;
        a.gsq = get_gsq(a.lat, a.lon);
    }
    else {
        a.dir = -1;
        a.dx = -1;
        a.dxw = 0;
        a.gsq = "";
    }
    a.log = [];
    a.rww = (arguments.length > 13 ? rww : "");

    station[a.id] = a;
}


// ************************************
// * TEXT Constructor:                *
// ************************************
// Used in text output
function TEXT(yyyymmddhhmm, date, hhmm, khz, call, display, gsq, lsb, usb, cyc, pwr, dx, dxw, nu, qth, sta, cnt) {
    this.yyyymmddhhmm = yyyymmddhhmm;
    this.date = date;
    this.hhmm = hhmm;
    this.khz = khz;
    this.call = call;
    this.display = display;
    this.lsb = lsb;
    this.usb = usb;
    this.gsq = gsq;
    this.cyc = cyc;
    this.pwr = pwr;
    this.dx = dx;
    this.dxw = dxw;
    this.nu = nu;	// Can't say 'new' as this is a reserved word
    this.qth = qth;
    this.sta = sta;
    this.cnt = cnt;
}


// ###########################################
// # Functions:                              #
// ###########################################
// Code to allow scrolling to named anchors in JS generated documents:
// From Martin Honnen - see this page:
// http://www.faqts.com/knowledge_base/view.phtml/aid/13648/fid/189
// Note that NS 4.8 works fine with the technique but still stuggles with it in this application about 1 time in every 2.

function checkScrollNecessary(window_hd, link) {
    if (document.layers || (!document.all && document.getElementById)) {
        var coords = getAnchorPosition(window_hd, link.hash.substring(1));
        eval(window_hd).scrollTo(coords.x, coords.y);
        return false;
    }
    return true;
}


function getAnchorPosition(window_hd, anchorName) {
    if (document.layers) {
        var anchor = eval(window_hd).document.anchors[anchorName];
        return {x: anchor.x, y: anchor.y};
    }
    if (document.getElementById) {
        var anchor = eval(window_hd).document.anchors[anchorName];
        var coords = {x: 0, y: 0};
        while (anchor) {
            coords.x += anchor.offsetLeft;
            coords.y += anchor.offsetTop - 10;
            anchor = anchor.offsetParent;
        }
        return coords;
    }
}

// ************************************
// * format_3sf()                     *
// ************************************
function format_3sf(X) {
    // Returns decimals formatted to three significant figures
    // Based on work by J.Stockton - http://www.merlyn.demon.co.uk/js-round.htm#OGC
    return String((Math.round(X * 1000) + (X < 0 ? -0.1 : +0.1)) / 1000).replace(/(.*\.\d\d\d)\d*/, '$1');
}


// ************************************
// * format_date()                    *
// ************************************
function format_date(yyyymmdd) {
    var yyyy = yyyymmdd.substr(0, 4);
    var mm = yyyymmdd.substr(4, 2);
    var dd = yyyymmdd.substr(6, 2);
    switch (cookie['format']) {
        case "dd.mm.yyyy":
            return dd + "." + mm + "." + yyyy;	// For German users (suggested by Udo Deutscher)
        case "ddmmyyyy":
            return dd + "/" + mm + "/" + yyyy;
        case "mmddyyyy":
            return mm + "/" + dd + "/" + yyyy;
        case "yyyy-mm-dd":
            return yyyy + "-" + mm + "-" + dd;
        default:
            return yyyymmdd;
    }
}


///////////////////////
// Testing functions //
///////////////////////
// By Dave Thomas

function fnTimerStart(strIdentifier) {
    if (typeof(arrTimer) == "undefined")
        arrTimer = []
    if (typeof(arrTimer[strIdentifier]) == "undefined")
        arrTimer[strIdentifier] = [(new Date()).getTime()]
    else
        arrTimer[strIdentifier][0] = (new Date()).getTime()
}

function fnTimerEnd(strIdentifier, numMetric) {
    if (typeof(arrTimer[strIdentifier]) == "undefined")
        alert("Function fnTimerEnd: unknown identifier - " + strIdentifier)
    if (typeof(numMetric) == "undefined")
        numMetric = Number.NaN
    arrTimer[strIdentifier][1] = (new Date()).getTime()
    if (typeof(arrTimer[strIdentifier][2]) == "undefined")
        arrTimer[strIdentifier][2] = arrTimer[strIdentifier][1] - arrTimer[strIdentifier][0]
    else
        arrTimer[strIdentifier][2] += arrTimer[strIdentifier][1] - arrTimer[strIdentifier][0]
    arrTimer[strIdentifier][3] = numMetric
}

// ************************************
// * get_cookie()                     *
// ************************************
function get_cookie(which) {
    var cookies = document.cookie;
    var pos = cookies.indexOf(which + "=");
    if (pos == -1) {
        return false;
    }
    var start = pos + which.length + 1;
    var end = cookies.indexOf(";", start);
    if (end == -1) {
        end = cookies.length;
    }
    var result = unescape(cookies.substring(start, end))
    return result;
}


// ************************************
// * get_graph_color:                 *
// ************************************
function get_graph_color(number, max) {
    var chars = "0123456789abcdef";
    var value = 255 - ((number / max) * 255);
    var lsb = value % 16;
    var msb = (value - lsb) / 16;
    var hex = "" + chars.charAt(msb) + chars.charAt(lsb);
    return "#ff" + hex + hex;
}


// ************************************
// * get_gsq(lat,lon)                 *
// ************************************
function get_gsq(lat, lon) {
    var letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var gsq_lon = (parseFloat(lon) + 180) / 20;
    var gsq_lon_deg = parseInt(gsq_lon);
    var gsq_lon_mmss = (gsq_lon - gsq_lon_deg) * 10;
    var gsq_1 = letters.charAt(gsq_lon_deg);
    var gsq_3 = Math.floor(gsq_lon_mmss);
    var gsq_5 = letters.charAt(parseInt((gsq_lon_mmss - gsq_3) * 24));
    var gsq_lat = (parseFloat(lat) + 90) / 10;
    var gsq_lat_deg = parseInt(gsq_lat);
    var gsq_lat_mmss = (gsq_lat - gsq_lat_deg) * 10;
    var gsq_2 = letters.charAt(gsq_lat_deg);
    var gsq_4 = parseInt(gsq_lat_mmss);
    var gsq_6 = letters.charAt(parseInt((gsq_lat_mmss - gsq_4) * 24));
    return (gsq_1 + gsq_2 + gsq_3 + gsq_4 + gsq_5.toLowerCase() + gsq_6.toLowerCase());
}


// ************************************
// * get_ident()                      *
// ************************************
function get_ident(cal) {
    if (cal.substr(0, 1) == "#") {		// Detects DGPS Idents
        return "DGPS " + cal;
    }
    if (cal.substr(0, 1) == "$") {		// Detects DGPS Idents
        return "Navtex " + cal.substr(cal.length - 1, 1);
    }
    morse = [];
    morse['A'] = ".-";
    morse['�'] = ".-.-";  // German
    morse['�'] = ".-.-";  // Scandanavian
    morse['�'] = ".--.-"; // Scandanavian or Spanish
    morse['�'] = ".--.-"; // Scandanavian or Spanish
    morse['B'] = "-...";
    morse['C'] = "-.-.";
    morse['D'] = "-..";
    morse['E'] = ".";
    morse['�'] = "..-.."; // Finish or French
    morse['F'] = "..-.";
    morse['G'] = "--.";
    morse['H'] = "....";
    morse['I'] = "..";
    morse['J'] = ".---";
    morse['K'] = "-.-";
    morse['L'] = ".-..";
    morse['M'] = "--";
    morse['N'] = "-.";
    morse['�'] = "--.--";	// Spanish
    morse['O'] = "---";
    morse['�'] = "---.";  // German or Scandanavian
    morse['�'] = "---.";  // Scandanavian
    morse['P'] = ".--.";
    morse['Q'] = "--.-";
    morse['R'] = ".-.";
    morse['S'] = "...";
    morse['T'] = "-";
    morse['U'] = "..-";
    morse['�'] = "..--";  // Finish or German
    morse['V'] = "...-";
    morse['W'] = ".--";
    morse['X'] = "-..-";
    morse['Y'] = "-.--";
    morse['Z'] = "--..";
    morse['1'] = ".----";
    morse['2'] = "..---";
    morse['3'] = "...--";
    morse['4'] = "....-";
    morse['5'] = ".....";
    morse['6'] = "-....";
    morse['7'] = "--...";
    morse['8'] = "---..";
    morse['9'] = "----.";
    morse['0'] = "-----";

    var out = [];
    var n = 0;
    for (var a = 0; a < cal.length; a++) {
        out[n++] = morse[cal.substr(a, 1)];
    }
    return (out.join("/") + "&nbsp;");	// fixes letter spacing problem in IE6 - otherwise last set of dashes are not spaced.
}


// ************************************
// * get_range_bearing()              *
// ************************************
function get_range_bearing(qth_lat, qth_lon, dx_lat, dx_lon, units) {
// Check for same point:
    if (qth_lat == dx_lat && qth_lon == dx_lon) {
        return new Array(0, 0);
    }
    var dlon = (dx_lon - qth_lon)
    if (Math.abs(dlon) > 180) {
        dlon = (360 - Math.abs(dlon)) * (0 - (dlon / Math.abs(dlon)));
    }
    var rinlat = qth_lat * 0.01745;	// convert to radians
    var rinlon = qth_lon * 0.01745;
    var rfnlat = dx_lat * 0.01745;
    var rdlon = dlon * 0.01745;
    var rgcdist = Math.acos(Math.sin(rinlat) * Math.sin(rfnlat) + Math.cos(rinlat) * Math.cos(rfnlat) * Math.cos(rdlon));

    var rincourse = (Math.sin(rfnlat) - Math.cos(rgcdist) * Math.sin(rinlat)) / (Math.sin(rgcdist) * Math.cos(rinlat));
    rincourse = Math.acos(rincourse);
    incourse = rincourse * 57.3;
    if (dlon < 0) {
        incourse = 360 - incourse;
    }
    switch (units) {
        case "mi":
            return [Math.round(incourse), Math.round(Math.abs(rgcdist) * 3958.284)];
        case "nm":
            return [Math.round(incourse), Math.round(Math.abs(rgcdist) * 3439.719)];
        default:
            return [Math.round(incourse), Math.round(Math.abs(rgcdist) * 6370.614)];
    }
}


// ************************************
// * get_region(country)              *
// ************************************
// See countries.js


// ************************************
// * get_units()                      *
// ************************************
function get_units() {
    switch (cookie['units']) {
        case "mi":
            return "Miles";
            break;
        case "nm":
            return "Naut.M";
            break;
        default:
            return "KM";
            break;
    }
}


// ************************************
// * goto_page()                      *
// ************************************
function goto_page(yyyy, mm, sort) {
    document.cookie = "list_selected=" + (mm ? mm : sel_mm) + "|" + (sort ? sort : sel_sort) + "|" + (yyyy ? yyyy : sel_yyyy);
    sel_yyyy = yyyy;
    sel_mm = mm;
    sel_sort = sort;
    progress();
    list();
}


// ************************************
// * keydown()                        *
// ************************************
function keydown(win, e) {
    switch (win) {
        case "main":
            if (e.ctrlKey && e.keyCode == 49) {	// CTRL+1
                popup_stats();
                return false;
            }
            if (e.ctrlKey && e.keyCode == 50) {	// CTRL+2
                popup_prefs();
                return false;
            }
            if (e.ctrlKey && e.keyCode == 51) {	// CTRL+3
                popup_search();
                return false;
            }
            if (e.ctrlKey && e.keyCode == 52) {	// CTRL+4
                popup_text_options();
                return false;
            }
            if (e.ctrlKey && e.keyCode == 53) {	// CTRL+5
                popup_help();
                return false;
            }
            if (e.ctrlKey && e.keyCode == 54) {	// CTRL+6
                popup_home();
                return false;
            }
            break;
        case "popup_details":
            if (e.keyCode == 27) {
                details_h.close();
                return false;
            }
            break;
        case "popup_prefs":
            if (e.keyCode == 27) {
                pref_h.close();
                return false;
            }
            break;
        case "popup_help":
            if (e.keyCode == 27) {
                help_h.close();
                return false;
            }
            break;
        case "popup_home":
            if (e.keyCode == 27) {
                home_h.close();
                return false;
            }
            break;
        case "popup_search":
            if (e.keyCode == 27) {
                search_h.close();
                return false;
            }
            break;
        case "popup_stats":
            if (e.keyCode == 27) {
                stat_h.close();
                return false;
            }
            break;
        case "search":
            if (e.keyCode == 27) {
                result_h.close();
                return false;
            }
            break;
        case "popup_text_options":
            if (e.keyCode == 27) {
                text_options_h.close();
                return false;
            }
            break;
    }
    return true
}


// ************************************
// * lead()                           *
// ************************************
function lead(num) {
    return (num.toString().length == 1 ? "0" + num : num)
}


// ************************************
// * pad()                            *
// ************************************
function pad(txt, len) {
    return ((txt + "        ").substr(0, len));
}


// ************************************
// * popup_details()                  *
// ************************************
function popup_details(id) {
    var km = get_range_bearing(qth_lat, qth_lon, station[id].lat, station[id].lon, 'km');
    var miles = get_range_bearing(qth_lat, qth_lon, station[id].lat, station[id].lon, 'mi');
    if (cookie['mod_abs'] == '1') {
        var lsb = Math.round((station[id].khz - station[id].lsb) * 1000);
        var usb = Math.round((station[id].usb - station[id].khz) * 1000);
    }
    else {
        var lsb = station[id].lsb;
        var usb = station[id].usb;
    }
    var gsq = ((station[id].lat + station[id].lon != "") ?
        ("<a href='#' onclick='window.opener.top.popup_map(\"" + station[id].lat + "\",\"" + station[id].lon + "\",\"" + station[id].id + "\");return false;' title='Click to show a map of this location'>" + station[id].gsq + "</a>") :
        ("&nbsp;"));
    var out =
        "<html><title>NDB WebLog > Details > " + station[id].display + "</title>\n" +
        "<link TITLE='new' REL='stylesheet' href=\"" + css + "\" type='text/css'>\n" +
        "</head>" +
        "<body bgcolor='#ffffff' onload=\"document.body.focus();\" onkeydown=\"window.opener.keydown('popup_details',event)\">" +
        "<h3>Details for " + station[id].display + "</h3>\n" +
        "<table border='" + ((document.all) ? ("0") : ("1")) + "' cellspacing='0' cellpadding='1' width='100%'>\n" +
        "<tr>\n" +
        "  <th class='l_edge'>KHz</th>\n" +
        "  <th>Call</th>\n" +
        (station[id].rww != '' ? "<th>More...</th>\n  " : "") +
        "  <th>Morse / DGPS ID</th>\n" +
        "  <th>DAID</th>\n" +
        "  <th>Cycle</th>\n" +
        "  <th>LSB</th>\n" +
        "  <th>USB</th>\n" +
        "  <th>Pwr</th>\n" +
        "</tr>\n" +
        "<tr>\n" +
        "  <td class='l_edge' align='center'>" + station[id].khz + "</td>\n" +
        "  <td class='c'>" + station[id].display + "</td>\n" +
        (station[id].rww != '' ? "  <td><a href='#' onclick='window.opener.top.rww(" + station[id].rww + ");return false;'>RWW</a></td>" : "") +
        "  <td nowrap class='c'>" + station[id].ident + "</td>\n" +
        "  <td class='c'>" + (station[id].daid ? station[id].daid : "&nbsp;") + "</td>\n" +
        "  <td class='c'>" + (station[id].cyc ? station[id].cyc : "&nbsp;") + "</td>\n" +
        "  <td class='c'>" + (lsb ? lsb : "&nbsp;") + "</td>\n" +
        "  <td class='c'>" + (usb ? usb : "&nbsp;") + "</td>\n" +
        "  <td class='c'>" + (station[id].pwr ? station[id].pwr : "&nbsp;") + "</td>\n" +
        "</tr>\n" +
        (trim(station[id].notes) != "" ? "<tr>\n  <td colspan='" + (station[id].rww != '' ? '9' : '8') + "' class='l_edge'>" + station[id].notes + "</td>\n</tr>\n" : "") +
        "</table><br>\n" +
        "<table border='" + ((document.all) ? ("0") : ("1")) + "' cellspacing='0' cellpadding='1' width='100%'>\n" +
        "<tr>\n" +
        "  <th class='l_edge'>Location</th>\n" +
        "  <th>Lat</th>\n" +
        "  <th>Lon</th>\n" +
        "  <th>GSQ</th>\n" +
        "</tr>\n" +
        "<tr>\n" +
        "  <td class='l_edge'>" + station[id].qth + (station[id].sta ? ", " + sta_arr[station[id].cnt][station[id].sta] : "") + ", " + cnt_arr[station[id].cnt].name + "</td>\n" +
        "  <td nowrap>" + station[id].lat + "</td>\n" +
        "  <td nowrap>" + station[id].lon + "</td>\n" +
        "  <td>" + gsq + "</td>\n" +
        "</tr>\n" +
        "</table><br>\n" +
        "<table border='" + ((document.all) ? ("0") : ("1")) + "' cellspacing='0' cellpadding='1' width='100%'>\n" +
        "<tr>\n" +
        "  <th rowspan='2' class='l_edge' width='100'>DX<br>Details</th>\n" +
        "  <th>Deg</th>\n" +
        "  <th>KM</th>\n" +
        "  <th>Miles</th>\n" +
        (station[id].pwr ? "  <th>KM DX/W</th>\n  <th>Miles DX/W</th>\n" : "") +
        "</tr>\n" +
        "<tr>\n" +
        "  <td class='c'>" + station[id].dir + "</td>\n" +
        "  <td class='c'>" + km[1] + "</td>\n" +
        "  <td class='c'>" + miles[1] + "</td>\n" +
        (station[id].pwr ? "  <td class='c'>" + Math.round(km[1] * 100 / station[id].pwr) / 100 + "</td>\n  <td class='c'>" + Math.round(miles[1] * 100 / station[id].pwr) / 100 + "</td>\n" : "") +
        "</tr>\n" +
        "</table><br>\n";

    if (logbook[id]) {
        out += "<h3>Reception Details for " + station[id].display + "</h3><table border='" + ((document.all) ? ("0") : ("1")) + "' cellspacing='0' cellpadding='1'>\n";
        out += "<tr>\n  <th class='l_edge'>" + show_date_heading() + "</th>\n  <th>UTC</th>\n  <th>Day</th>\n  <th>Notes</th>\n</tr>"
        for (var b in logbook[id]['entry']) {
            var log_yyyymmdd = logbook[id]['entry'][b]['yyyymmddhhmm'].substr(0, 8);
            var log_HH = logbook[id]['entry'][b]['yyyymmddhhmm'].substr(8, 2);
            var log_MM = logbook[id]['entry'][b]['yyyymmddhhmm'].substr(10, 2);
            var log_notes = logbook[id]['entry'][b]['notes']
            var log_isDay = (utc_daylight_array[0] == log_HH || utc_daylight_array[1] == log_HH || utc_daylight_array[2] == log_HH || utc_daylight_array[3] == log_HH);
            out += "<tr>\n  <td class='l_edge'>" + format_date(log_yyyymmdd) + "</td>\n  <td>" + log_HH + ":" + log_MM + "</td>\n  <td class='c'>" + (log_isDay ? "Y" : "&nbsp;") + "</td>\n  <td>" + (log_notes != "" ? log_notes : "&nbsp;") + "</td>\n</tr>"
        }
        out += "</table>";
    }
    out += "</body></html>";
    details_h = window.open('', '', 'width=600,height=500,status=1,resizable=1,menubar=0,location=0,toolbar=1,scrollbars=1');
    details_h.focus();
    details_h.document.write(out);
    details_h.document.close();
}


// ************************************
// * popup_help()                     *
// ************************************
function popup_help() {
    var h = window.open('https://classaxe.com/dx/ndb/log/help.html', 'helpViewer', 'width=800,height=400,status=1,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
    h.focus();
}

// ************************************
// * popup_home()                     *
// ************************************
function popup_home() {
    var h = window.open(qth_home, 'homePage', 'width=800,height=400,status=1,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
    h.focus();
}

// ************************************
// * popup_map()                      *
// ************************************
function popup_map() {
    var h = window.open('map.html', 'mapViewer', 'width=800,height=400,status=1,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
    h.focus();
}

// ************************************
// * popup_prefs()                    *
// ************************************
function popup_prefs() {
    var out = '';
    out =
        "<html><head><title>NDB WebLog > Preferences</title>\n" +
        "<link TITLE=\"new\" REL=\"stylesheet\" href=\"" + css + "\" type=\"text/css\">\n" +
        "<script type='text/javascript'>\n" +
        "function set_cookies(form) {\n" +
        "  var format =	form.format.options[form.format.selectedIndex].value;\n" +
        "  var h_dxw =	form.h_dxw.options[form.h_dxw.selectedIndex].value;\n" +
        "  var h_gsq =	form.h_gsq.options[form.h_gsq.selectedIndex].value;\n" +
        "  var h_ident =	form.h_ident.options[form.h_ident.selectedIndex].value;\n" +
        "  var h_lifelist =	form.h_lifelist.options[form.h_lifelist.selectedIndex].value;\n" +
        "  var h_notes =	form.h_notes.options[form.h_notes.selectedIndex].value;\n" +
        "  var map_zoom =	form.map_zoom.options[form.map_zoom.selectedIndex].value;\n" +
        "  var units =	form.units.options[form.units.selectedIndex].value;\n" +
        "  var mod_abs =	form.mod_abs.options[form.mod_abs.selectedIndex].value;\n" +
        "  var h_logs =	form.h_logs.options[form.h_logs.selectedIndex].value;\n" +
        "  var lang =	form.lang.options[form.lang.selectedIndex].value;\n" +
        "  var expires =	new Date();\n" +
        "  expires.setFullYear(expires.getFullYear()+1);\n" +
        "  expires =		expires.toGMTString();\n" +
        "  var D =		\"|\"\n" +
        "  var cookies = \"list_options=\"+format+D+h_dxw+D+h_gsq+D+h_ident+D+h_lifelist+D+" +
        "                h_notes+D+map_zoom+D+units+D+mod_abs+D+h_logs+D+lang+\";expires=\"+expires;\n" +
        "  document.cookie = cookies;\n" +
        "}\n" +
        "</script>\n" +
        "</head>\n" +
        "<body onload=\"document.body.focus();\" onkeydown=\"window.opener.keydown('popup_prefs',event)\">" +
        "<form name='form'>" +
        "<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n" +
        "  <tr>\n" +
        "    <td class='plain'><table cellpadding='1' cellspacing='0' border='0' class='r' width='100%'>\n" +
        "      <tr>\n" +
        "        <th colspan='2' class='l_edge'>Preferences > Options</th>" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td class='l_edge'>Dates&nbsp;</td>\n" +
        "        <td><select name='format'>\n" +
        "    <option value='ddmmyyyy'" + ((cookie['format'] == 'ddmmyyyy') ? (" selected") : ("")) + ">DD/MM/YYYY</option>\n" +
        "    <option value='dd.mm.yyyy'" + ((cookie['format'] == 'dd.mm.yyyy') ? (" selected") : ("")) + ">DD.MM.YYYY</option>\n" +
        "    <option value='mmddyyyy'" + ((cookie['format'] == 'mmddyyyy') ? (" selected") : ("")) + ">MM/DD/YYYY</option>\n" +
        "    <option value='yyyy-mm-dd'" + ((cookie['format'] == 'yyyy-mm-dd') ? (" selected") : ("")) + ">YYYY-MM-DD</option>\n" +
        "    <option value='yyyymmdd'" + ((cookie['format'] == 'yyyymmdd') ? (" selected") : ("")) + ">YYYYMMDD</option>\n" +
        "    </select>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td class='l_edge'>Map Zoom&nbsp;</td>\n" +
        "        <td><select name='map_zoom'>\n";
    for (var i = 0; i < 10; i++) {
        out += "    <option value='" + i + "'" + ((cookie['map_zoom'] == i) ? (" selected") : ("")) + ">" + (1 + i) + "</option>\n";
    }
    out +=
        "    </select>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td class='l_edge'>Distances&nbsp;</td>\n" +
        "        <td><select name='units'>\n" +
        "    <option value='km'" + ((cookie['units'] == 'km') ? (" selected") : ("")) + ">KM</option>\n" +
        "    <option value='nm'" + ((cookie['units'] == 'nm') ? (" selected") : ("")) + ">Naut.Mi.</option>\n" +
        "    <option value='mi'" + ((cookie['units'] == 'mi') ? (" selected") : ("")) + ">Miles</option>\n" +
        "    </select>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td class='l_edge'>Language&nbsp;</td>\n" +
        "        <td><select name='lang'>\n" +
        "    <option value='English'" + ((cookie['language'] == 'English') ? (" selected") : ("")) + ">English</option>\n" +
        "    <option value='French'" + ((cookie['language'] == 'French') ? (" selected") : ("")) + ">French</option>\n" +
        "    <option value='German'" + ((cookie['language'] == 'German') ? (" selected") : ("")) + ">German</option>\n" +
        "    <option value='Portuguese'" + ((cookie['language'] == 'Portuguese') ? (" selected") : ("")) + ">Portuguese</option>\n" +
        "    <option value='Spanish'" + ((cookie['language'] == 'Spanish') ? (" selected") : ("")) + ">Spanish</option>\n" +
        "    </select>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td class='l_edge'>Mod Values&nbsp;</td>\n" +
        "        <td><select name='mod_abs'>\n" +
        "    <option value='0'" + ((cookie['mod_abs'] == '0') ? (" selected") : ("")) + ">Relative</option>\n" +
        "    <option value='1'" + ((cookie['mod_abs'] == '1') ? (" selected") : ("")) + ">Absolute</option>\n" +
        "    </select>\n" +
        "      </tr>\n" +
        "    </table></td>\n" +
        "  </tr>\n" +
        "  <tr>\n" +
        "    <td class='plain'>&nbsp;</td>\n" +
        "  </tr>\n" +
        "  <tr>\n" +
        "    <td class='plain'><table cellpadding='1' cellspacing='0' border='0' class='r' width='100%'>\n" +
        "      <tr>\n" +
        "        <th colspan='2' class='l_edge'>Preferences > Columns</th>" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td class='l_edge'>Morse / DGPS ID&nbsp;</td>" +
        "        <td><select name='h_ident'>" +
        "    <option value='0'" + ((cookie['h_ident'] == '1') ? ("") : (" selected")) + ">Show</option>\n" +
        "    <option value='1'" + ((cookie['h_ident'] == '1') ? (" selected") : ("")) + ">Hide</option>\n" +
        "    </select>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td class='l_edge'>GSQ&nbsp;</td>" +
        "        <td><select name='h_gsq'>" +
        "    <option value='0'" + ((cookie['h_gsq'] == '1') ? ("") : (" selected")) + ">Show</option>\n" +
        "    <option value='1'" + ((cookie['h_gsq'] == '1') ? (" selected") : ("")) + ">Hide</option>\n" +
        "    </select>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td class='l_edge'>DX per Watt&nbsp;</td>" +
        "        <td><select name='h_dxw'>" +
        "    <option value='0'" + ((cookie['h_dxw'] == '1') ? ("") : (" selected")) + ">Show</option>\n" +
        "    <option value='1'" + ((cookie['h_dxw'] == '1') ? (" selected") : ("")) + ">Hide</option>\n" +
        "    </select>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td class='l_edge'>First Received&nbsp;</td>" +
        "        <td><select name='h_lifelist'>" +
        "    <option value='0'" + ((cookie['h_lifelist'] == '1') ? ("") : (" selected")) + ">Show</option>\n" +
        "    <option value='1'" + ((cookie['h_lifelist'] == '1') ? (" selected") : ("")) + ">Hide</option>\n" +
        "    </select>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td class='l_edge'>Log Count</td>" +
        "        <td><select name='h_logs'>" +
        "    <option value='0'" + ((cookie['h_logs'] == '1') ? ("") : (" selected")) + ">Show</option>\n" +
        "    <option value='1'" + ((cookie['h_logs'] == '1') ? (" selected") : ("")) + ">Hide</option>\n" +
        "    </select>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td class='l_edge'>Notes&nbsp;</td>" +
        "        <td><select name='h_notes'>" +
        "    <option value='0'" + ((cookie['h_notes'] == '1') ? ("") : (" selected")) + ">Show</option>\n" +
        "    <option value='1'" + ((cookie['h_notes'] == '1') ? (" selected") : ("")) + ">Hide</option>\n" +
        "    </select>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <th colspan='2' class='l_edge'><input type='button' value='Submit' onclick='set_cookies(document.form);window.opener.location.reload(1);window.close()'>" +
        "     <input type='button' value='Cancel' onclick='window.close()'></th>" +
        "      </tr>\n" +
        "    </table></td>\n" +
        "  </tr>\n" +
        "</table>\n" +
        "</form>\n" +
        "</body></html>\n";
    pref_h = window.open('', 'prefsPage', 'width=250,height=390,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=0');
    pref_h.focus();
    pref_h.document.write(out);
    pref_h.document.close();
}

function popup_search() {
    var out =
        "<html><head><title>NDB WebLog > Search</title>\n" +
        "<link TITLE=\"new\" REL=\"stylesheet\" href=\"" + css + "\" type=\"text/css\">\n" +
        "</head>\n" +
        "<body onload='document.form.term.focus()' onkeydown=\"window.opener.keydown('popup_search',event)\">\n" +
        "<form name='form' onsubmit='window.opener.search(document.form);return false'>" +
        "<table cellpadding='1' cellspacing='0' border='0' class='r' align='center'>\n" +
        "  <tr>\n" +
        "    <th colspan='2' class='l_edge'>Search</th>" +
        "  </tr>\n" +
        "  <tr>\n" +
        "    <td nowrap class='l_edge'>KHz / Call&nbsp;</td>" +
        "    <td><input name='term'>\n" +
        "  </tr>\n" +
        "  <tr>\n" +
        "    <th colspan='2' class='l_edge'><input type='button' value='Submit' onclick='window.opener.search(document.form);'>" +
        "     <input type='button' value='Cancel' onclick='window.close()'></th>" +
        "  </tr>\n" +
        "</table>" +
        "</form>" +
        "</body></html>\n";
    search_h = window.open('', 'searchPage', 'width=280,height=100,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=0');
    search_h.focus();
    search_h.document.write(out);
    search_h.document.close();
}


// ************************************
// * popup_stats()                    *
// ************************************
function popup_stats() {
    progress();
    var units_long = get_units();
    var dx_step = (cookie['units'] == "mi" ? 100 : 200)

    var max_dx = 20000;	// Maximum possible DX in KM (smallest unit)
    var rexp_country = /([A-Z\?]*)\_([A-Z\?]*)/
    var yyyy = 0

    var n = 0;
    var out =
        "<!doctype html public '-//W3C//DTD HTML 4.01 Transitional//EN'>\n" +
        "<html>\n" +
        "<head>\n\n" +
        "<title>NDB WebLog for " + qth_name + " > Statistics</title>\n" +
        "<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=windows-1252'>\n" +
        "<link TITLE=\"new\" REL=\"stylesheet\" href=\"" + css + "\" type=\"text/css\">\n\n" +
        "</head>\n" +
        "<body onload='document.body.focus()' onkeydown=\"window.opener.keydown('popup_stats',event)\">\n" +
        "<h2 align='center'><a name='top'></a><u>Statistics and Awards Qualifications Page</u></h2>\n";

    if (monthly) {
        // +++++++++++++++++++
        // + Monthly Report  +
        // +++++++++++++++++++
        var max_count = [];		// Used to calculate contrast settings for colour graduated boxes
        max_count['br'] = 0;
        max_count['dx'] = 0;
        max_count['cnt'] = 0;
        max_count['new_stations'] = 0;
        max_count['new_stations_ever'] = 0;
        max_count['rgn'] = 0;
        max_count['n60'] = 0;

        // +++++++++++++++++++
        // + links:          +
        // +++++++++++++++++++
        var link_top = " <span class='links'><small>[ <a href='#top' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>Top</a> ]</small></span>"
        // Repeat for each available year:
        var years = [];
        for (yyyy in stats.year) {
            yyyy = "" + yyyy
            // Quick links:
            var quicklinks = [];
            var qk_i = 0;
            quicklinks[qk_i++] = "<a href='#" + yyyy + "br' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>Stations</a>";
            if (stats.year[yyyy].new_stations) {
                quicklinks[qk_i++] = "<a href='#" + yyyy + "new' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>New Stations</a>";
            }
            quicklinks[qk_i++] = "<a href='#" + yyyy + "dx_d' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>Day-time DX</a>";
            quicklinks[qk_i++] = "<a href='#" + yyyy + "dx_n' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>Night-time DX</a>";
            quicklinks[qk_i++] = "<a href='#" + yyyy + "dx_w' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>DX per Watt</a>";
            quicklinks[qk_i++] = "<a href='#" + yyyy + "cr' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>Countries</a>";
            quicklinks[qk_i++] = "<a href='#" + yyyy + "rr' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>Regions</a>";
            if (stats.year[yyyy].n60) {
                quicklinks[qk_i++] = "<a href='#" + yyyy + "n60' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>North of 60</a>";
            }
            out += "<p align='center' class='links'><small><a href='#year" + yyyy + "' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'><b>Reports for " + yyyy + "</b></a><br>[ " + quicklinks.join(" | ") + " ]</small></p>\n";
        }
        out += "<p align='center' class='links'><small>[ <a href='#awards' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>About NDB Awards...</a> ]</small></p>\n";

        out += "<hr width='75%' align='center'>\n\n"

        // +++++++++++++++++++
        // + clear old stats:+
        // +++++++++++++++++++
        for (yyyy in stats.year) {
            stats.year[yyyy].rx_x = 0;	// Clear out old stats
            stats.year[yyyy].rx_d = 0;
            stats.year[yyyy].rx_n = 0;
            stats.year[yyyy].dx_d = [];
            stats.year[yyyy].dx_n = [];
            stats.year[yyyy].dx_x = [];

            for (var dx = 0; dx < (max_dx / dx_step); dx++) {
                stats.year[yyyy].dx_d[dx] = 0;
                stats.year[yyyy].dx_n[dx] = 0;
                stats.year[yyyy].dx_x[dx] = 0;
            }

            var count = 0;
            var dx_max_day = 0;
            var dx_max_night = 0;
            // +++++++++++++++++++
            // + Count for year: +
            // +++++++++++++++++++
            for (c in station) {
                for (var b = 0; b < 12; b++) {
                    var mm = mm_arr[b];
                    if (stats.year[yyyy][mm]) {
                        stats.year[yyyy][mm].rx_n = 0;		// Clear the monthly stat counts
                        stats.year[yyyy][mm].rx_d = 0;		// Clear the monthly stat counts
                        stats.year[yyyy][mm].rx_x = 0;		// Clear the monthly stat counts
                        for (dx = 0; dx < (max_dx / dx_step); dx++) {
                            stats.year[yyyy][mm].dx_d[dx] = 0;
                            stats.year[yyyy][mm].dx_n[dx] = 0;
                            stats.year[yyyy][mm].dx_x[dx] = 0;
                        }
                    }
                }

                if (station[c].log[yyyy]) {
                    count++;					// Now count how many stations were logged this year
                    var dx_range = Math.floor(station[c].dx / dx_step)
                    if (station[c].log[yyyy].rx_x) {
                        if (station[c].dx > dx_max_day) {
                            dx_max_day = station[c].dx;
                        }
                        if (station[c].dx > dx_max_night) {
                            dx_max_night = station[c].dx;
                        }
                        stats.year[yyyy].dx_x[dx_range]++;
                        stats.year[yyyy].rx_x++;
                    }
                    else if (station[c].log[yyyy].rx_n) {
                        if (station[c].dx > dx_max_night) {
                            dx_max_night = station[c].dx;
                        }
                        stats.year[yyyy].dx_n[dx_range]++;
                        stats.year[yyyy].rx_n++;
                    }
                    else if (station[c].log[yyyy].rx_d) {
                        if (station[c].dx > dx_max_day) {
                            dx_max_day = station[c].dx;
                        }
                        stats.year[yyyy].dx_d[dx_range]++;
                        stats.year[yyyy].rx_d++;
                    }
                }
            }

            // +++++++++++++++++++
            // + Count for month:+
            // +++++++++++++++++++

            for (var b = 0; b < 12; b++) {
                var mm = mm_arr[b];
                for (c in station) {
                    if (station[c].log[yyyy] && station[c].log[yyyy].rx[mm]) {
                        var dx_range = Math.floor(station[c].dx / dx_step)
                        switch (station[c].log[yyyy].rx[mm]) {
                            case "N":
                                stats.year[yyyy][mm].rx_n++;
                                stats.year[yyyy][mm].dx_n[dx_range]++;
                                break;
                            case "D":
                                stats.year[yyyy][mm].rx_d++;
                                stats.year[yyyy][mm].dx_d[dx_range]++;
                                break;
                            case "X":
                                stats.year[yyyy][mm].rx_x++;
                                stats.year[yyyy][mm].dx_x[dx_range]++;
                                break;
                        }

                        // has max_count['br'] been exceeded?
                        if (stats.year[yyyy][mm].rx_n > max_count['br']) {
                            max_count['br'] = stats.year[yyyy][mm].rx_n;
                        }
                        if (stats.year[yyyy][mm].rx_d > max_count['br']) {
                            max_count['br'] = stats.year[yyyy][mm].rx_d;
                        }
                        if (stats.year[yyyy][mm].rx_x > max_count['br']) {
                            max_count['br'] = stats.year[yyyy][mm].rx_x;
                        }

                        // has max_count['dx'] been exceeded?
                        if (stats.year[yyyy][mm].dx_d[dx_range] + stats.year[yyyy][mm].dx_x[dx_range] > max_count['dx']) {
                            max_count['dx'] = stats.year[yyyy][mm].dx_d[dx_range] + stats.year[yyyy][mm].dx_x[dx_range];
                        }
                        if (stats.year[yyyy][mm].dx_n[dx_range] + stats.year[yyyy][mm].dx_x[dx_range] > max_count['dx']) {
                            max_count['dx'] = stats.year[yyyy][mm].dx_n[dx_range] + stats.year[yyyy][mm].dx_x[dx_range];
                        }
                    }
                }
            }

            out += "<h2 align='center'><u><a name='year" + yyyy + "'></a>Reports for " + yyyy + "</u></h2>\n";

            var month_columns = "<th width='30'>" + months[0].substr(0, 3) + "</th>\n    <th width='30'>" + months[1].substr(0, 3) + "</th>\n    <th width='30'>" + months[2].substr(0, 3) + "</th>\n    <th width='30'>" + months[3].substr(0, 3) + "</th>\n    <th width='30'>" + months[4].substr(0, 3) + "</th>\n    <th width='30'>" + months[5].substr(0, 3) + "</th>\n    <th width='30'>" + months[6].substr(0, 3) + "</th>\n    <th width='30'>" + months[7].substr(0, 3) + "</th>\n    <th width='30'>" + months[8].substr(0, 3) + "</th>\n    <th width='30'>" + months[9].substr(0, 3) + "</th>\n    <th width='30'>" + months[10].substr(0, 3) + "</th>\n    <th width='30'>" + months[11].substr(0, 3) + "</th>\n    <th>" + yyyy + "</th>\n";
            // +++++++++++++++++++
            // + stations Report: +
            // +++++++++++++++++++
            // Day + Night stations report:
            out += "<big><a name='" + yyyy + "br'></a>" + yyyy + " All Stations Report (with UNIDs)" + link_top + "</big><br>\n<small>Daytime: " + lead(utc_daylight) + ":00-" + lead((utc_daylight + 3) % 24) + ":59, Night: " + lead((utc_daylight + 4) % 24) + ":00-" + lead((utc_daylight - 1) % 24) + ":59</small>\n\n";
            out += "<table cellpadding='1' cellspacing='0' border='0'>\n  <tr>\n    <th width='120' class='l_edge'>&nbsp;</th>\n" + month_columns + "  </tr>\n";


            // +++++++++++++++++++
            // + Day:            +
            // +++++++++++++++++++
            out += "  <tr>\n    <th nowrap class='l_edge_l'>Day only</th>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["01"] ? stats.year[yyyy]["01"].rx_d : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["01"] ? stats.year[yyyy]["01"].rx_d : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["02"] ? stats.year[yyyy]["02"].rx_d : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["02"] ? stats.year[yyyy]["02"].rx_d : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["03"] ? stats.year[yyyy]["03"].rx_d : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["03"] ? stats.year[yyyy]["03"].rx_d : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["04"] ? stats.year[yyyy]["04"].rx_d : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["04"] ? stats.year[yyyy]["04"].rx_d : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["05"] ? stats.year[yyyy]["05"].rx_d : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["05"] ? stats.year[yyyy]["05"].rx_d : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["06"] ? stats.year[yyyy]["06"].rx_d : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["06"] ? stats.year[yyyy]["06"].rx_d : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["07"] ? stats.year[yyyy]["07"].rx_d : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["07"] ? stats.year[yyyy]["07"].rx_d : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["08"] ? stats.year[yyyy]["08"].rx_d : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["08"] ? stats.year[yyyy]["08"].rx_d : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["09"] ? stats.year[yyyy]["09"].rx_d : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["09"] ? stats.year[yyyy]["09"].rx_d : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["10"] ? stats.year[yyyy]["10"].rx_d : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["10"] ? stats.year[yyyy]["10"].rx_d : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["11"] ? stats.year[yyyy]["11"].rx_d : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["11"] ? stats.year[yyyy]["11"].rx_d : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["12"] ? stats.year[yyyy]["12"].rx_d : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["12"] ? stats.year[yyyy]["12"].rx_d : "&nbsp;") + "</td>\n" +
                "    <th class='r'>" + stats.year[yyyy].rx_d + "</th>\n" +
                "  </tr>\n";

            // +++++++++++++++++++
            // + Day / Night:    +
            // +++++++++++++++++++
            out += "  <tr>\n    <th nowrap class='l_edge_l'>Day + Night</th>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["01"] ? stats.year[yyyy]["01"].rx_x : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["01"] ? stats.year[yyyy]["01"].rx_x : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["02"] ? stats.year[yyyy]["02"].rx_x : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["02"] ? stats.year[yyyy]["02"].rx_x : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["03"] ? stats.year[yyyy]["03"].rx_x : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["03"] ? stats.year[yyyy]["03"].rx_x : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["04"] ? stats.year[yyyy]["04"].rx_x : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["04"] ? stats.year[yyyy]["04"].rx_x : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["05"] ? stats.year[yyyy]["05"].rx_x : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["05"] ? stats.year[yyyy]["05"].rx_x : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["06"] ? stats.year[yyyy]["06"].rx_x : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["06"] ? stats.year[yyyy]["06"].rx_x : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["07"] ? stats.year[yyyy]["07"].rx_x : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["07"] ? stats.year[yyyy]["07"].rx_x : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["08"] ? stats.year[yyyy]["08"].rx_x : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["08"] ? stats.year[yyyy]["08"].rx_x : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["09"] ? stats.year[yyyy]["09"].rx_x : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["09"] ? stats.year[yyyy]["09"].rx_x : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["10"] ? stats.year[yyyy]["10"].rx_x : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["10"] ? stats.year[yyyy]["10"].rx_x : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["11"] ? stats.year[yyyy]["11"].rx_x : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["11"] ? stats.year[yyyy]["11"].rx_x : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["12"] ? stats.year[yyyy]["12"].rx_x : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["12"] ? stats.year[yyyy]["12"].rx_x : "&nbsp;") + "</td>\n" +
                "    <th class='r'>" + stats.year[yyyy].rx_d + "</th>\n" +
                "  </tr>\n";

            // +++++++++++++++++++
            // + Night:          +
            // +++++++++++++++++++
            out += "  <tr>\n    <th nowrap class='l_edge_l'>Night only</th>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["01"] ? stats.year[yyyy]["01"].rx_n : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["01"] ? stats.year[yyyy]["01"].rx_n : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["02"] ? stats.year[yyyy]["02"].rx_n : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["02"] ? stats.year[yyyy]["02"].rx_n : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["03"] ? stats.year[yyyy]["03"].rx_n : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["03"] ? stats.year[yyyy]["03"].rx_n : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["04"] ? stats.year[yyyy]["04"].rx_n : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["04"] ? stats.year[yyyy]["04"].rx_n : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["05"] ? stats.year[yyyy]["05"].rx_n : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["05"] ? stats.year[yyyy]["05"].rx_n : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["06"] ? stats.year[yyyy]["06"].rx_n : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["06"] ? stats.year[yyyy]["06"].rx_n : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["07"] ? stats.year[yyyy]["07"].rx_n : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["07"] ? stats.year[yyyy]["07"].rx_n : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["08"] ? stats.year[yyyy]["08"].rx_n : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["08"] ? stats.year[yyyy]["08"].rx_n : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["09"] ? stats.year[yyyy]["09"].rx_n : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["09"] ? stats.year[yyyy]["09"].rx_n : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["10"] ? stats.year[yyyy]["10"].rx_n : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["10"] ? stats.year[yyyy]["10"].rx_n : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["11"] ? stats.year[yyyy]["11"].rx_n : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["11"] ? stats.year[yyyy]["11"].rx_n : "&nbsp;") + "</td>\n" +
                "    <td bgcolor='" + get_graph_color((stats.year[yyyy]["12"] ? stats.year[yyyy]["12"].rx_n : 0), max_count['br']) + "' class='r'>" + (stats.year[yyyy]["12"] ? stats.year[yyyy]["12"].rx_n : "&nbsp;") + "</td>\n" +
                "    <th class='r'>" + stats.year[yyyy].rx_d + "</th>\n" +
                "  </tr>\n";

            // +++++++++++++++++++
            // + Total results:  +
            // +++++++++++++++++++
            out += "  <tr>\n" +
                "    <th class='l_edge_l'>Total</th>\n" +
                "    <th class='r'>" + ((stats.year[yyyy]["01"]) ? (stats.year[yyyy]["01"].rx_d + stats.year[yyyy]["01"].rx_x + stats.year[yyyy]["01"].rx_n) : ("&nbsp;")) + "</th>\n" +
                "    <th class='r'>" + ((stats.year[yyyy]["02"]) ? (stats.year[yyyy]["02"].rx_d + stats.year[yyyy]["02"].rx_x + stats.year[yyyy]["02"].rx_n) : ("&nbsp;")) + "</th>\n" +
                "    <th class='r'>" + ((stats.year[yyyy]["03"]) ? (stats.year[yyyy]["03"].rx_d + stats.year[yyyy]["03"].rx_x + stats.year[yyyy]["03"].rx_n) : ("&nbsp;")) + "</th>\n" +
                "    <th class='r'>" + ((stats.year[yyyy]["04"]) ? (stats.year[yyyy]["04"].rx_d + stats.year[yyyy]["04"].rx_x + stats.year[yyyy]["04"].rx_n) : ("&nbsp;")) + "</th>\n" +
                "    <th class='r'>" + ((stats.year[yyyy]["05"]) ? (stats.year[yyyy]["05"].rx_d + stats.year[yyyy]["05"].rx_x + stats.year[yyyy]["05"].rx_n) : ("&nbsp;")) + "</th>\n" +
                "    <th class='r'>" + ((stats.year[yyyy]["06"]) ? (stats.year[yyyy]["06"].rx_d + stats.year[yyyy]["06"].rx_x + stats.year[yyyy]["06"].rx_n) : ("&nbsp;")) + "</th>\n" +
                "    <th class='r'>" + ((stats.year[yyyy]["07"]) ? (stats.year[yyyy]["07"].rx_d + stats.year[yyyy]["07"].rx_x + stats.year[yyyy]["07"].rx_n) : ("&nbsp;")) + "</th>\n" +
                "    <th class='r'>" + ((stats.year[yyyy]["08"]) ? (stats.year[yyyy]["08"].rx_d + stats.year[yyyy]["08"].rx_x + stats.year[yyyy]["08"].rx_n) : ("&nbsp;")) + "</th>\n" +
                "    <th class='r'>" + ((stats.year[yyyy]["09"]) ? (stats.year[yyyy]["09"].rx_d + stats.year[yyyy]["09"].rx_x + stats.year[yyyy]["09"].rx_n) : ("&nbsp;")) + "</th>\n" +
                "    <th class='r'>" + ((stats.year[yyyy]["10"]) ? (stats.year[yyyy]["10"].rx_d + stats.year[yyyy]["10"].rx_x + stats.year[yyyy]["10"].rx_n) : ("&nbsp;")) + "</th>\n" +
                "    <th class='r'>" + ((stats.year[yyyy]["11"]) ? (stats.year[yyyy]["11"].rx_d + stats.year[yyyy]["11"].rx_x + stats.year[yyyy]["11"].rx_n) : ("&nbsp;")) + "</th>\n" +
                "    <th class='r'>" + ((stats.year[yyyy]["12"]) ? (stats.year[yyyy]["12"].rx_d + stats.year[yyyy]["12"].rx_x + stats.year[yyyy]["12"].rx_n) : ("&nbsp;")) + "</th>\n" +
                "    <th class='r'>" + count + "</th>\n" +
                "  </tr>\n" +
                "</table>\n<p> </p>\n\n";

            // +++++++++++++++++++++++
            // + New Stations Report: +
            // +++++++++++++++++++++++
            if (stats.year[yyyy].new_stations) {
                var new_stations = [];
                var new_stations_ever = [];

                // +++++++++++++++++++
                // + Count for month:+
                // +++++++++++++++++++
                for (var c = 0; c < 12; c++) {
                    var mm = mm_arr[c];
                    var val = ((stats.year[yyyy][mm] && stats.year[yyyy][mm].new_stations) ? (stats.year[yyyy][mm].new_stations) : (0))
                    new_stations[c] = val
                    if (val > max_count['new_stations']) {
                        max_count['new_stations'] = val;
                    }
                    val = ((stats.year[yyyy][mm] && stats.year[yyyy][mm].new_stations_ever) ? (stats.year[yyyy][mm].new_stations_ever) : (0))
                    new_stations_ever[c] = val
                    if (val > max_count['new_stations_ever']) {
                        max_count['new_stations_ever'] = val;
                    }
                }

                // +++++++++++++++++++
                // + Show Results:   +
                // +++++++++++++++++++
                var total_ever = 0;
                for (var c in station) {
                    if (station[c].all_date) {
                        total_ever++;
                    }
                }
                out += "<big><a name='" + yyyy + "new'></a>" + yyyy + " New Stations Report (with UNIDs)" + link_top + "</big><br>\n";
                out += "<small>Total for all time: " + total_ever + "</small>\n"
                out += "<table cellpadding='1' cellspacing='0' border='0'>\n  <tr>\n    <th width='120' class='l_edge'>&nbsp;</th>\n" + month_columns + "  </tr>\n";
                out += "  <tr>\n"
                out += "    <th class='l_edge_l'>New (All time)</th>\n";
                for (var c = 0; c < 12; c++) {
                    var mm = mm_arr[c];
                    out += "    <td bgcolor='" + get_graph_color(new_stations_ever[c], max_count['new_stations_ever']) + "' class='r'>" + ((new_stations_ever[c]) ? (new_stations_ever[c]) : ("&nbsp;")) + "</td>\n";
                }
                out += "    <th class='r'>" + stats.year[yyyy].new_stations_ever + "</th>\n";
                out += "  </tr>\n";

                out += "  <tr>\n"
                out += "    <th class='l_edge_l'>New for " + yyyy + "</th>\n";
                for (var c = 0; c < 12; c++) {
                    var mm = mm_arr[c];
                    out += "    <td bgcolor='" + get_graph_color(new_stations[c], max_count['new_stations']) + "' class='r'>" + ((new_stations[c]) ? (new_stations[c]) : ("&nbsp;")) + "</td>\n";
                }
                out += "    <th class='r'>" + stats.year[yyyy].new_stations + "</th>\n";
                out += "  </tr>\n";
                out += "</table>\n<p> </p>\n"
            }

            // +++++++++++++++++++++++++++++++++
            // + Daytime DX Stations Report:    +
            // +++++++++++++++++++++++++++++++++
            out += "<big><a name='" + yyyy + "dx_d'></a>" + yyyy + " Day-time Distances Report (" + units_long + ") " + link_top + "</big><br>\n"
            out += "<small>Daytime: " + lead(utc_daylight) + ":00-" + lead((utc_daylight + 3) % 24) + ":59</small>"

            out += "<table cellpadding='1' cellspacing='0' border='0'>\n  <tr>\n    <th width='120' class='l_edge_r'>" + units_long + "</th>\n" + month_columns + "  </tr>\n";

            for (var dx = 0; dx < (dx_max_day / dx_step); dx++) {
                out += "  <tr>\n"
                out += "    <th nowrap class='l_edge_r'>" + (dx * dx_step) + " - " + ((dx_step * (dx + 1)) - 1) + "</th>\n";
                for (var b = 0; b < 12; b++) {
                    var mm = mm_arr[b]
                    var val = 0;
                    if (stats.year[yyyy][mm]) {
                        val = (stats.year[yyyy][mm].dx_d[dx] ? stats.year[yyyy][mm].dx_d[dx] : 0) +
                            (stats.year[yyyy][mm].dx_x[dx] ? stats.year[yyyy][mm].dx_x[dx] : 0);
                    }
                    out += "    <td bgcolor='" + get_graph_color(val, max_count['dx']) + "' class='r'>" + ((val) ? (val) : ("&nbsp;")) + "</td>\n";
                }
                dx_d = (stats.year[yyyy].dx_d[dx] ? stats.year[yyyy].dx_d[dx] : 0) +
                    (stats.year[yyyy].dx_x[dx] ? stats.year[yyyy].dx_x[dx] : 0);
                out += "<th class='r'>" + ((dx_d) ? (dx_d) : ("&nbsp;")) + "</th>\n";
                out += "  </tr>\n"
            }
            out += "  <tr>\n"
            out += "    <th class='l_edge_r'>Max " + units_long + "</th>\n"
            for (var b = 0; b < 12; b++) {
                var mm = mm_arr[b]
                out += "    <th class='l_edge_r'>" + ((stats.year[yyyy] && stats.year[yyyy][mm] && stats.year[yyyy][mm].max_day) ? (stats.year[yyyy][mm].max_day) : ("&nbsp;")) + "</th>\n";
            }
            out += "    <th>" + ((stats.year[yyyy] && stats.year[yyyy].max_day) ? (stats.year[yyyy].max_day) : ("&nbsp;")) + "</th>\n"
            out += "  </tr>\n"
            out += "</table>\n<p> </p>\n\n"

            // +++++++++++++++++++++++++++++++++
            // + Nighttime DX Stations Report: +
            // +++++++++++++++++++++++++++++++++
            out += "<big><a name='" + yyyy + "dx_n'></a>" + yyyy + " Night-time Distances Report (" + units_long + ") " + link_top + "</big><br>"
            out += "<small>Night: " + lead((utc_daylight + 4) % 24) + ":00-" + lead((utc_daylight - 1) % 24) + ":59</small>"

            out += "<table cellpadding='1' cellspacing='0' border='0'>\n  <tr>\n    <th width='120' class='l_edge_r'>" + units_long + "</th>\n" + month_columns + "  </tr>\n";

            for (var dx = 0; dx < (dx_max_night / dx_step); dx++) {
                out += "  <tr>\n"
                out += "  <th nowrap class='l_edge_r'>" + (dx * dx_step) + " - " + ((dx_step * (dx + 1)) - 1) + "</th>";
                for (var b = 0; b < 12; b++) {
                    var mm = mm_arr[b]
                    var val = 0;
                    if (stats.year[yyyy][mm]) {
                        val = (stats.year[yyyy][mm].dx_n[dx] ? stats.year[yyyy][mm].dx_n[dx] : 0) +
                            (stats.year[yyyy][mm].dx_x[dx] ? stats.year[yyyy][mm].dx_x[dx] : 0);
                    }
                    out += "<td bgcolor='" + get_graph_color(val, max_count['dx']) + "' class='r'>" + ((val) ? (val) : ("&nbsp;")) + "</td>";
                }
                dx_n = (stats.year[yyyy].dx_n[dx] ? stats.year[yyyy].dx_n[dx] : 0) +
                    (stats.year[yyyy].dx_x[dx] ? stats.year[yyyy].dx_x[dx] : 0);
                out += "<th class='r'>" + ((dx_n) ? (dx_n) : ("&nbsp;")) + "</td>\n";
                out += "  </tr>\n"
            }
            out += "  </tr>\n"

            out += "  <tr><th class='l_edge_r'>Max " + units_long + "</th>\n"
            for (var b = 0; b < 12; b++) {
                var mm = mm_arr[b]
                out += "<th>" + ((stats.year[yyyy] && stats.year[yyyy][mm] && stats.year[yyyy][mm].max_night) ? (stats.year[yyyy][mm].max_night) : ("&nbsp;")) + "</th>";
            }
            out += "<th>" + ((stats.year[yyyy] && stats.year[yyyy].max_night) ? (stats.year[yyyy].max_night) : ("&nbsp;")) + "</th>"
            out += "  </tr>\n"
            out += "</table>\n<p> </p>\n\n"

            // +++++++++++++++++
            // + DX/W Report:  +
            // +++++++++++++++++
            var dx_w = [];
            max_count['dxw'] = 0
            for (var c = 0; c < 12; c++) {
                var mm = mm_arr[c];
                var val = ((stats.year[yyyy][mm] && stats.year[yyyy][mm].max_dxw) ? (stats.year[yyyy][mm].max_dxw) : (0))
                dx_w[c] = val;
                if (val > max_count['dxw']) {
                    max_count['dxw'] = val;
                }
            }
            out += "<big><a name='" + yyyy + "dx_w'></a>" + yyyy + " DX per Watt " + units_long + link_top + "</big></br>\n";

            out += "<table cellpadding='1' cellspacing='0' border='0'>\n  <tr>\n    <th width='120' class='l_edge'>&nbsp;</th>\n" + month_columns + "  </tr>\n";

            out += "<tr><th class='l_edge_l'>Stations</th>";
            for (var c = 0; c < 12; c++) {
                val = dx_w[c]
                out += "<td bgcolor='" + get_graph_color(val, max_count['dxw']) + "'>" + ((val) ? (val) : ("&nbsp;")) + "</td>";
            }
            out += "<th class='l_edge_r'>" + stats.year[yyyy].max_dxw + "</th>";
            out += "</tr>\n";
            out += "</table>\n<p> </p>\n\n"

            // ++++++++++++++++++++++
            // + Countries Report:  +
            // ++++++++++++++++++++++
            var sorted_cnt = [];
            var s_c = 0;
            for (b in stats.year[yyyy].cnt) {
                sorted_cnt[s_c++] = b;
            }
            sorted_cnt.sort();

            var year_count = 0;
            var year_best_dx_dgps = 0;
            var year_best_dx_navtex = 0;
            var year_best_dx_ndb = 0;
            var year_best_id_dgps = "";
            var year_best_id_navtex = "";
            var year_best_id_ndb = "";
            var countries = []
            for (b = 0; b < sorted_cnt.length; b++) {
                countries[b] = []
                var name = sorted_cnt[b].match(rexp_country);
                cnt_name = (cnt_arr[name[1]] ? cnt_arr[name[1]].name : sorted[a].cnt);
                sta_name = (sta_arr[name[1]] && sta_arr[name[1]][name[2]] ? sta_arr[name[1]][name[2]] : name[2]);

                countries[b].full = cnt_arr[name[1]].name + ((sta_name) ? (" (" + sta_name + ")") : (""));

                countries[b].qth = name[1] + ((name[2]) ? (" (" + name[2] + ")") : (""))
                countries[b].mm = []
                for (var c = 0; c < 12; c++) {
                    var mm = mm_arr[c];
                    countries[b].mm[c] = ((stats.year[yyyy][mm] && stats.year[yyyy][mm].cnt[sorted_cnt[b]]) ? (stats.year[yyyy][mm].cnt[sorted_cnt[b]]) : (0))
                    if (countries[b].mm[c] > max_count['cnt']) {
                        max_count['cnt'] = countries[b].mm[c]
                    }
                }
            }

            out +=
                "<big><a name='" + yyyy + "cr'></a>" + yyyy + " Countries Report" + link_top + "</big></br>\n" +
                "<table cellpadding='1' cellspacing='0' border='0'>\n" +
                "  <tr>\n" +
                "    <th width='120' class='l_edge'>Country (Sta)</th>\n" +
                month_columns +
                "<th colspan='2'>Best NDB<br>(" + units_long + ")</th>\n" +
                "<th colspan='2'>Best DGPS<br>(" + units_long + ")</th>\n" +
                "<th colspan='2'>Best Navtex<br>(" + units_long + ")</th>\n" +
                "</tr>\n";

            for (b = 0; b < countries.length; b++) {
                out +=
                    "<tr>\n" +
                    "<th class='l_edge_l'><a class='info' href='javascript:void 0' " +
                    "onmouseover='window.status=\"" + countries[b].full + "\";return true;' onmouseout='window.status=\"\";return true;' " +
                    "title='" + countries[b].full + "'>" + countries[b].qth + "</th>\n" +
                    "<td bgcolor='" + get_graph_color(countries[b].mm[0], max_count['cnt']) + "' class='r'>" + (countries[b].mm[0] ? countries[b].mm[0] : "&nbsp;") + "</td>" +
                    "<td bgcolor='" + get_graph_color(countries[b].mm[1], max_count['cnt']) + "' class='r'>" + (countries[b].mm[1] ? countries[b].mm[1] : "&nbsp;") + "</td>" +
                    "<td bgcolor='" + get_graph_color(countries[b].mm[2], max_count['cnt']) + "' class='r'>" + (countries[b].mm[2] ? countries[b].mm[2] : "&nbsp;") + "</td>" +
                    "<td bgcolor='" + get_graph_color(countries[b].mm[3], max_count['cnt']) + "' class='r'>" + (countries[b].mm[3] ? countries[b].mm[3] : "&nbsp;") + "</td>" +
                    "<td bgcolor='" + get_graph_color(countries[b].mm[4], max_count['cnt']) + "' class='r'>" + (countries[b].mm[4] ? countries[b].mm[4] : "&nbsp;") + "</td>" +
                    "<td bgcolor='" + get_graph_color(countries[b].mm[5], max_count['cnt']) + "' class='r'>" + (countries[b].mm[5] ? countries[b].mm[5] : "&nbsp;") + "</td>" +
                    "<td bgcolor='" + get_graph_color(countries[b].mm[6], max_count['cnt']) + "' class='r'>" + (countries[b].mm[6] ? countries[b].mm[6] : "&nbsp;") + "</td>" +
                    "<td bgcolor='" + get_graph_color(countries[b].mm[7], max_count['cnt']) + "' class='r'>" + (countries[b].mm[7] ? countries[b].mm[7] : "&nbsp;") + "</td>" +
                    "<td bgcolor='" + get_graph_color(countries[b].mm[8], max_count['cnt']) + "' class='r'>" + (countries[b].mm[8] ? countries[b].mm[8] : "&nbsp;") + "</td>" +
                    "<td bgcolor='" + get_graph_color(countries[b].mm[9], max_count['cnt']) + "' class='r'>" + (countries[b].mm[9] ? countries[b].mm[9] : "&nbsp;") + "</td>" +
                    "<td bgcolor='" + get_graph_color(countries[b].mm[10], max_count['cnt']) + "' class='r'>" + (countries[b].mm[10] ? countries[b].mm[10] : "&nbsp;") + "</td>" +
                    "<td bgcolor='" + get_graph_color(countries[b].mm[11], max_count['cnt']) + "' class='r'>" + (countries[b].mm[11] ? countries[b].mm[11] : "&nbsp;") + "</td>";

                out += "<th class='r'>"
                if (stats.year[yyyy] && stats.year[yyyy].cnt[sorted_cnt[b]]) {
                    year_count += stats.year[yyyy].cnt[sorted_cnt[b]].count;
                    out +=
                        stats.year[yyyy].cnt[sorted_cnt[b]].count +
                        "</th>" +
                        "<td nowrap>" +
                        (typeof stats.year[yyyy].cnt[sorted_cnt[b]].best_id_ndb != "undefined" ? stats.year[yyyy].cnt[sorted_cnt[b]].best_id_ndb : "&nbsp;") +
                        "</td>" +
                        "<td>";
                    if (!stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_ndb) {
                        out += "&nbsp;";
                    }
                    else {
                        out += ((stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_ndb != -1) ? (stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_ndb) : ("?"))
                        if (stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_ndb > year_best_dx_ndb) {
                            year_best_dx_ndb = stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_ndb;
                            year_best_id_ndb = stats.year[yyyy].cnt[sorted_cnt[b]].best_id_ndb;
                        }
                    }
                    out +=
                        "</td>" +
                        "<td nowrap>" +
                        (typeof stats.year[yyyy].cnt[sorted_cnt[b]].best_id_dgps != "undefined" ? stats.year[yyyy].cnt[sorted_cnt[b]].best_id_dgps : "&nbsp;") +
                        "</td>" +
                        "<td>";
                    if (!stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_dgps) {
                        out += "&nbsp;";
                    }
                    else {
                        out += ((stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_dgps != -1) ? (stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_dgps) : ("?"));
                        if (stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_dgps > year_best_dx_dgps) {
                            year_best_dx_dgps = stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_dgps;
                            year_best_id_dgps = stats.year[yyyy].cnt[sorted_cnt[b]].best_id_dgps;
                        }
                    }
                    out +=
                        "</td>" +
                        "<td nowrap>" +
                        (typeof stats.year[yyyy].cnt[sorted_cnt[b]].best_id_navtex != "undefined" ? stats.year[yyyy].cnt[sorted_cnt[b]].best_id_navtex : "&nbsp;") +
                        "</td>" +
                        "<td>";
                    if (!stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_navtex) {
                        out += "&nbsp;";
                    }
                    else {
                        out += ((stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_navtex != -1) ? (stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_navtex) : ("?"));
                        if (stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_navtex > year_best_dx_navtex) {
                            year_best_dx_navtex = stats.year[yyyy].cnt[sorted_cnt[b]].best_dx_navtex;
                            year_best_id_navtex = stats.year[yyyy].cnt[sorted_cnt[b]].best_id_navtex;
                        }
                    }
                    out += "</td>";
                }
                else {
                    out += "&nbsp;</th><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"
                }
                out += "</td></tr>\n"
            }
            out += "<tr>\n"
            out += "<th class='l_edge_l'>Countries:</th>\n"
            for (var c = 0; c < 12; c++) {
                var mm = mm_arr[c];
                var month_count = 0;
                for (b = 0; b < sorted_cnt.length; b++) {
                    if (stats.year[yyyy][mm] && stats.year[yyyy][mm].cnt[sorted_cnt[b]]) {
                        month_count++;
                    }
                }
                out += "<th class='r'>" + ((month_count) ? (month_count) : ("&nbsp;")) + "</th>";
            }
            out +=
                "<th class='r'>" +
                sorted_cnt.length +
                "</th>\n" +
                "<th class='l'>" + (year_best_id_ndb ? year_best_id_ndb : "&nbsp;") + "</th>\n" +
                "<th class='r'>" + (year_best_dx_ndb ? year_best_dx_ndb : "&nbsp;") + "</th>\n" +
                "<th class='l'>" + (year_best_id_dgps ? year_best_id_dgps : "&nbsp;") + "</th>\n" +
                "<th class='r'>" + (year_best_dx_dgps ? year_best_dx_dgps : "&nbsp;") + "</th>\n" +
                "<th class='l'>" + (year_best_id_navtex ? year_best_id_navtex : "&nbsp;") + "</th>\n" +
                "<th class='r'>" + (year_best_dx_navtex ? year_best_dx_navtex : "&nbsp;") + "</th>\n" +
                "</tr>\n" +
                "</table>\n" +
                "<p> </p>\n\n";

            // ++++++++++++++++++++++
            // + Regions Report:    +
            // ++++++++++++++++++++++
            var regions = []
            for (c in rgn_arr) {
                if (stats.year[yyyy].rgn[c]) {
                    regions[c] = [];
                    regions[c].name = rgn_arr[c];
                    regions[c].mm = [];
                    for (var b = 0; b < 12; b++) {
                        var mm = mm_arr[b];
                        var val = ((stats.year[yyyy][mm] && stats.year[yyyy][mm].rgn[c]) ? (stats.year[yyyy][mm].rgn[c]) : (0))
                        if (val > max_count['rgn']) {
                            max_count['rgn'] = val;
                        }
                        regions[c].mm[b] = val
                    }
                }
            }
            out += "<big><a name='" + yyyy + "rr'></a>" + yyyy + " Regions Report" + link_top + "</big></br>\n";
            out += "<table cellpadding='1' cellspacing='0' border='0'>\n  <tr>\n    <th width='120' class='l_edge'>&nbsp;</th>\n" + month_columns + "  </tr>\n";

            for (c in regions) {
                if (stats.year[yyyy].rgn[c]) {

                    out += "<tr><th class='l_edge_l'>" + regions[c].name + "</th>";
                    for (var b = 0; b < 12; b++) {
                        var val = regions[c].mm[b]
                        out += "<td bgcolor='" + get_graph_color(val, max_count['rgn']) + "' class='r'>" + ((val) ? (val) : ("&nbsp;")) + "</td>";
                    }
                    out += "<th class='r'>" + stats.year[yyyy].rgn[c] + "</th>";
                    out += "</tr>\n";
                }
            }
            out += "</table>\n<p> </p>\n\n"

            // ++++++++++++++++++++++
            // + North of 60 Report:+
            // ++++++++++++++++++++++
            if (stats.year[yyyy].n60) {
                n60 = []
                for (var c = 0; c < 12; c++) {
                    var mm = mm_arr[c];
                    var val = ((stats.year[yyyy][mm] && stats.year[yyyy][mm].n60) ? (stats.year[yyyy][mm].n60) : (0))
                    n60[c] = val;
                    if (val > max_count['n60']) {
                        max_count['n60'] = val;
                    }
                }
                out += "<big><a name='" + yyyy + "n60'></a>" + yyyy + " North of 60 Degrees Report" + link_top + "</big></br>\n";

                out += "<table cellpadding='1' cellspacing='0' border='0'>\n  <tr>\n    <th width='120' class='l_edge'>&nbsp;</th>\n" + month_columns + "  </tr>\n";
                out += "<tr><th class='l_edge_l'>Stations</th>";
                for (var c = 0; c < 12; c++) {
                    var mm = mm_arr[c];
                    var val = n60[c];
                    out += "<td bgcolor='" + get_graph_color(val, max_count['n60']) + "'>" + ((val) ? (val) : ("&nbsp;")) + "</td>";
                }
                out += "<th class='r'>" + stats.year[yyyy].n60 + "</th>";
                out += "</tr>\n";
                out += "</table>\n<p> </p>\n\n"
            }
            out += "<hr width='75%' align='center'>\n"
        }
    }
    else {
        // +++++++++++++++++++
        // + Lifetime Report +
        // +++++++++++++++++++

        // +++++++++++++++++++
        // + links:          +
        // +++++++++++++++++++
        if (document.all) {	// for some reason NS 4.7 doesn't set bookmarks correctly when contents dynamically generated.
            var link_top = " <span class='links'>[ <a href='#top'>Top</a> ]</span>"
            out += "<p align='center'><span class='links'>[ "
            out += "<a href='#summary'>Summary</a> | ";
            out += "<a href='#country'>Countries</a> | ";
            out += "<a href='#region'>Regions</a> ";
            out += "]</span></p>";
        }
        else {
            var link_top = "";
        }

        var max_count = [];		// Used to calculate contrast settings for colour graduated boxes
        max_count['cnt'] = 0;
        var all_stations = 0;
        var all_n60 = 0;
        var all_n60_names = [];
        var all_dxw = 0;
        var all_dxw_name = "";
        var all_countries = [];
        var all_rgn = [];
        var all_dx_max = 0;
        for (var id in stats.all) {
            var cnt = station[id].cnt + "_" + station[id].sta;
            all_stations++;
            if (station[id].lat >= 60) {
                all_n60++;
                all_n60_names[all_n60_names.length] = station[id].id
            }
            if (station[id].dx > all_dx_max) {
                all_dx_max = station[id].dx;
                all_dx_name = station[id].id;
            }
            if (station[id].dxw > all_dxw) {
                all_dxw = station[id].dxw;
                all_dxw_name = station[id].id;
            }
            if (!all_countries[cnt]) {		// Country not yet defined
                all_countries[cnt] = 0;		// Initialise count
                var rgn = station[id].rgn;
                if (!all_rgn[rgn]) {
                    all_rgn[rgn] = [];
                    all_rgn[rgn].cnt = 0;
                    all_rgn[rgn].rgn = rgn;
                }
                all_rgn[station[id].rgn].cnt++;
            }
            all_countries[cnt]++
        }


        var rgn_sorted = [];
        var i = 0
        max_count['rgn'] = 0;
        for (var rgn in all_rgn) {
            rgn_sorted[i] = []
            rgn_sorted[i].rgn = rgn;
            rgn_sorted[i].cnt = all_rgn[rgn];
            if (rgn_sorted[i].cnt > max_count['rgn']) {
                max_count['rgn'] = rgn_sorted[i].rgn;
            }
        }

        var all_countries_sorted = [];
        var i = 0
        for (var cnt in all_countries) {
            all_countries_sorted[i] = []
            all_countries_sorted[i].cnt = cnt;
            all_countries_sorted[i].stations = all_countries[cnt]
            if (all_countries_sorted[i].stations > max_count['cnt']) {
                max_count['cnt'] = all_countries_sorted[i].stations;
            }
            i++
        }

        all_countries_sorted.sort(sortBy_life_country);

        var rgn_sorted = [];
        var i = 0;
        for (c in rgn_arr) {
            if (all_rgn[c]) {
                all_rgn[rgn]
                rgn_sorted[i] = [];
                rgn_sorted[i].rgn = rgn_arr[c];
                rgn_sorted[i].cnt = all_rgn[c].cnt;
                if (rgn_sorted[i].cnt > max_count['rgn']) {
                    max_count['rgn'] = rgn_sorted[i].cnt;
                }
                i++;
            }
        }
        rgn_sorted.sort(sortBy_life_rgn);


        out += "<big><a name='summary'></a>Summary" + link_top + "</big></br>\n"
        out += "<table cellpadding='1' cellspacing='0' border='1' class='r'>";
        out += "  <tr>\n";
        out += "    <th class='l'>Statistics</th>\n";
        out += "    <th colspan='2' class='l'>Value</th>\n";
        out += "  </tr>\n";
        out += "  <tr>\n";
        out += "    <th class='l'>Total Stations received:</th>\n"
        out += "    <td colspan='2' class='l'>" + all_stations + "</td>\n"
        out += "  </tr>\n";
        out += "  <tr>\n";
        out += "    <th class='l'>Stations North of 60 Degrees:</th>\n"
        out += "    <td class='l'>" + all_n60 + "</td>"
        out += "    <td class='l'><nobr>" + all_n60_names.join("</nobr>, <nobr>") + "</nobr></td>"
        out += "  </tr>\n";
        out += "  <tr>\n";
        out += "    <th class='l'>Maximum DX:</th>\n"
        out += "    <td class='l' nowrap>" + all_dx_max + " " + units_long + "</td>"
        out += "    <td class='l'>" + all_dx_name + "</td>"
        out += "  </tr>\n";
        out += "  <tr>\n";
        out += "    <th class='l'>Maximum DX/W:</th>\n"
        out += "    <td class='l' nowrap>" + all_dxw + " " + units_long + "</td>"
        out += "    <td class='l'>" + all_dxw_name + "</td>"
        out += "  </tr>\n";
        out += "</table>\n<p> </p>\n"

        // ++++++++++++++++++++++
        // + Countries Report:  +
        // ++++++++++++++++++++++
        out += "<big><a name='country'></a>Countries Report" + link_top + "</big></br>\n"
        out += "<table cellpadding='1' cellspacing='0' border='1' class='r'>\n";
        out += "  <tr>\n";
        out += "    <th class='l'>Country</th>\n";
        out += "    <th class='l'>Stations</th>\n";
        out += "  </tr>\n";

        for (var i = 0; i < all_countries_sorted.length; i++) {
            var name = all_countries_sorted[i].cnt.match(rexp_country);
            var cnt = name[1];
            var sta = ((name[2]) ? (" (" + name[2] + ")") : (""));
            full = cnt_arr[name[1]] + ((name[2]) ? (" (" + sta_arr[name[1]][name[2]] + ")") : (""));
            var val = all_countries_sorted[i].stations
            out += "  <tr>\n";
            out += "<th class='l'><a class='info' href='javascript:void 0' "
            out += "onmouseover='window.status=\"" + full + "\";return true;' onmouseout='window.status=\"\";return true;' "
            out += "title='" + full + "'>" + cnt + sta + "</th>\n"
            out += "    <td class='r' bgcolor='" + get_graph_color(val, max_count['cnt']) + "'>" + val + "</td>\n"
            out += "  </tr>\n";
        }
        out += "  <tr>\n";
        out += "    <th class='l'>Countries:</th>\n"
        out += "    <th class='r'>" + all_countries_sorted.length + "</th>\n"
        out += "  </tr>\n";
        out += "</table>\n<p> </p>\n\n"


        // ++++++++++++++++++++++
        // + Regions Report:    +
        // ++++++++++++++++++++++
        out += "<big><a name='region'></a>Regions Report" + link_top + "</big></br>\n"
        out += "<table cellpadding='1' cellspacing='0' border='1' class='r'>";
        out += "  <tr>\n";
        out += "    <th class='l'>Region</th>\n";
        out += "    <th class='l'>Stations</th>\n";
        out += "  </tr>\n";
        for (var i = 0; i < rgn_sorted.length; i++) {
            out += "  <tr>\n";
            out += "    <th class='l'>" + rgn_sorted[i].rgn + "</th>\n"
            out += "    <td class='r' bgcolor='" + get_graph_color(rgn_sorted[i].cnt, max_count['rgn']) + "'>" + rgn_sorted[i].cnt + "</td>\n"
            out += "  </tr>\n";
        }
        out += "  <tr>\n";
        out += "    <th class='l'>Regions:</th>\n"
        out += "    <th class='r'>" + rgn_sorted.length + "</th>\n"
        out += "  </tr>\n";
        out += "</table>\n<p> </p>\n\n"
    }

    out += "<p><a name='awards'></a><small><b>See <a href='http://www.beaconworld.org.uk' target='_blank'>http://www.beaconworld.org.uk</a> for details on how to join the NDB List and qualify for awards</b></small></p>";
    out += "</body></html>\n"
    stat_h = window.open('', 'statsViewer', 'width=700,height=480,status=1,resizable=1,menubar=1,location=0,toolbar=0,scrollbars=1');
    stat_h.focus();
    stat_h.document.write(out);
    stat_h.document.close();
    if (progress_hd) {
        progress_hd.close();
    }
}


// ************************************
// * popup_text()                     *
// ************************************
function popup_text() {
    var txt_options = false;
    if (get_cookie("txt_options")) {
        var txt_options = get_cookie("txt_options").split("|");
        var i = 0;
        cookie['txt_date1'] = txt_options[i++];
        cookie['txt_date2'] = txt_options[i++];
        cookie['txt_dayNight'] = txt_options[i++];
        cookie['txt_dx1'] = txt_options[i++];
        cookie['txt_dx2'] = txt_options[i++];
        cookie['txt_format'] = txt_options[i++];
        cookie['txt_freq1'] = txt_options[i++];
        cookie['txt_freq2'] = txt_options[i++];
        cookie['txt_h_dxw'] = txt_options[i++];
        cookie['txt_h_new'] = txt_options[i++];
        cookie['txt_h_mod'] = txt_options[i++];
        cookie['txt_showAll'] = txt_options[i++];
        cookie['txt_sortBy'] = txt_options[i++];
        cookie['txt_type'] = txt_options[i++];
        cookie['txt_lat1'] = txt_options[i++];
        cookie['txt_lat2'] = txt_options[i++];
        cookie['txt_lon1'] = txt_options[i++];
        cookie['txt_lon2'] = txt_options[i++];
        cookie['txt_h_cyc'] = txt_options[i++];
        cookie['txt_h_gsq'] = txt_options[i++];
        cookie['txt_h_pwr'] = txt_options[i++];
        cookie['txt_h_dx'] = txt_options[i++];
        // cookie['mod_abs'] is set in main prefs
    }
    // Make frequency span conform:
    if (cookie['txt_freq1'].toLowerCase() == "all") cookie['txt_freq1'] = "0";
    if (cookie['txt_freq2'].toLowerCase() == "all") cookie['txt_freq2'] = "100000";
    start_khz = parseFloat(cookie['txt_freq1']);
    end_khz = parseFloat(cookie['txt_freq2']);

    // Make dx span conform:
    if (cookie['txt_dx1'].toLowerCase() == "all") cookie['txt_dx1'] = "0";
    if (cookie['txt_dx2'].toLowerCase() == "all") cookie['txt_dx2'] = "100000";
    dx_min = parseFloat(cookie['txt_dx1']);
    dx_max = parseFloat(cookie['txt_dx2']);

    // Make lat span conform:
    if (cookie['txt_lat1'].toLowerCase() == "all") cookie['txt_lat1'] = "-90";
    if (cookie['txt_lat2'].toLowerCase() == "all") cookie['txt_lat2'] = "90";
    lat_min = parseFloat(cookie['txt_lat1']);
    lat_max = parseFloat(cookie['txt_lat2']);

    // Make lon span conform:
    if (cookie['txt_lon1'].toLowerCase() == "all") cookie['txt_lon1'] = "-180";
    if (cookie['txt_lon2'].toLowerCase() == "all") cookie['txt_lon2'] = "180";
    lon_min = parseFloat(cookie['txt_lon1']);
    lon_max = parseFloat(cookie['txt_lon2']);

    // Make date range conform:
    if (cookie['txt_date1'].toLowerCase() == "all") cookie['txt_date1'] = "19000001";
    if (cookie['txt_date2'].toLowerCase() == "all") cookie['txt_date2'] = "21000001";


    var sorted = [];
    var i = 0;
    for (a in station) {
        if ((parseFloat(station[a].khz) >= start_khz && parseFloat(station[a].khz) <= end_khz) &&
            ((cookie['txt_type'] == 'all') ||
                (cookie['txt_type'] == 'dgps' && station[a].call.substr(0, 1) == "#") ||
                (cookie['txt_type'] == 'navtex' && station[a].call.substr(0, 1) == "$") ||
                (cookie['txt_type'] == 'ndb' && station[a].call.substr(0, 1) != "#" && station[a].call.substr(0, 1) != "$"))) {
            var id = station[a].id;
            if (logbook[id]) {
                logbook[id]['shown'] = false;
                logbook[id]['old'] = false;
            }
            sorted[i++] = station[a];
        }
    }
    // Output data
    var total = 0;

    var start_dd = lead(cookie['txt_date1'].substr(6, 2));
    var start_mm = lead(cookie['txt_date1'].substr(4, 2) - 1);	// Months begin at 0
    var start_yyyy = cookie['txt_date1'].substr(0, 4);
    var end_dd = lead(cookie['txt_date2'].substr(6, 2));
    var end_mm = lead(cookie['txt_date2'].substr(4, 2) - 1);	// Months begin at 0
    var end_yyyy = cookie['txt_date2'].substr(0, 4);

    var out = "Log showing " + ((cookie['txt_showAll'] == '1') ? ("all receptions") : ("first reception")) + " of each "
    switch (cookie['txt_type']) {
        case "dgps":
            out += "DGPS station ";
            break;
        case "ndb":
            out += "NDB ";
            break;
        case "navtex":
            out += "Naxtex station ";
            break;
        case "all":
            out += "signal "
    }

    if (cookie['txt_date1'] == cookie['txt_date2']) {
        out += "on day " + start_dd;
    }
    else {
        if (cookie['txt_date1'] != "19000001" && cookie['txt_date2'] != "21000001") {
            switch (cookie['txt_format']) {
                case "dd":
                    out += "between day " + start_dd + " and " + end_dd;
                    break
                case "ddmmyyyy":
                    out += "between " + start_dd + "/" + start_mm + "/" + start_yyyy + " and " + end_dd + "/" + end_mm + "/" + end_yyyy;
                    break
                case "dd.mm.yyyy":
                    out += "between " + start_dd + "." + start_mm + "." + start_yyyy + " and " + end_dd + "." + end_mm + "." + end_yyyy;
                    break
                case "mmddyyyy":
                    out += "between " + start_mm + "/" + start_dd + "/" + start_yyyy + " and " + end_mm + "/" + end_dd + "/" + end_yyyy;
                    break
                case "yyyy-mm-dd":
                    out += "between " + start_yyyy + "-" + start_mm + "-" + start_dd + " and " + end_yyyy + "." + end_mm + "." + end_dd;
                    break
                default:
                    out += "between " + cookie['txt_date1'] + " and " + cookie['txt_date2'];
                    break
            }
        }
        if (cookie['txt_date1'] != "19000001" && cookie['txt_date2'] == "21000001") {
            switch (cookie['txt_format']) {
                case "dd":
                    out += "from day " + start_dd + " onwards";
                    break
                case "ddmmyyyy":
                    out += "from " + start_dd + "/" + start_mm + "/" + start_yyyy + " onwards";
                    break
                case "dd.mm.yyyy":
                    out += "from " + start_dd + "." + start_mm + "." + start_yyyy + " onwards";
                    break
                case "mmddyyyy":
                    out += "from " + start_mm + "/" + start_dd + "/" + start_yyyy + " onwards";
                    break
                case "yyyy-mm-dd":
                    out += "from " + start_yyyy + "-" + start_mm + "-" + start_dd + " onwards";
                    break
                default:
                    out += "from " + cookie['txt_date1'] + " onwards.";
                    break
            }
        }
        if (cookie['txt_date1'] == "19000001" && cookie['txt_date2'] != "21000001") {
            switch (cookie['txt_format']) {
                case "dd":
                    out += "until day " + end_dd + "\n\n";
                    break
                case "ddmmyyyy":
                    out += "until " + end_dd + "/" + end_mm + "/" + end_yyyy;
                    break
                case "dd.mm.yyyy":
                    out += "until " + end_dd + "." + end_mm + "." + end_yyyy;
                    break
                case "mmddyyyy":
                    out += "until " + end_mm + "/" + end_dd + "/" + end_yyyy;
                    break
                case "yyyy-mm-dd":
                    out += "until " + end_yyyy + "-" + end_mm + "-" + end_dd;
                    break
                default:
                    out += "until " + cookie['txt_date2'] + " onwards.";
                    break
            }
        }
    }

    if (!(start_khz == 0 && end_khz == 100000)) {
        out += "\nAll frequencies"
        if (start_khz != 0) {
            out += " from " + start_khz + "KHz";
        }
        if (end_khz != 100000) {
            out += " to " + end_khz + "KHz";
        }
    }

    if (!(cookie['txt_dx1'] == 0 && cookie['txt_dx2'] == 100000)) {
        out += "\nAll distances"
        if (cookie['txt_dx1'] != 0) {
            out += " from " + cookie['txt_dx1'] + " " + get_units();
        }
        if (cookie['txt_dx2'] != 100000) {
            out += " to " + cookie['txt_dx2'] + " " + get_units();
        }
    }

    if (!(cookie['txt_lat1'] == -90 && cookie['txt_lat2'] == 90)) {
        out += "\nAll Latitudes"
        if (cookie['txt_lat11'] != -90) {
            out += " from " + cookie['txt_lat1'];
        }
        if (cookie['txt_lat2'] != 90) {
            out += " to " + cookie['txt_lat2'];
        }
    }

    if (!(cookie['txt_lon1'] == -180 && cookie['txt_lon2'] == 180)) {
        out += "\nAll Longitudes"
        if (cookie['txt_lon1'] != -180) {
            out += " from " + cookie['txt_lon1'];
        }
        if (cookie['txt_lon2'] != 180) {
            out += " to " + cookie['txt_lon2'];
        }
    }

    if (cookie['txt_dayNight'] != "x") {
        if (cookie['txt_dayNight'] == "d") {
            out += "\nDaytime loggings only";
        }
        else {
            out += "\nNight-time loggings only";
        }
    }

    out += "\nDaytime: " + lead(utc_daylight) + ":00-" + lead((utc_daylight + 4) % 24) + ":59, ";
    out += "Night: " + lead((utc_daylight + 5) % 24) + ":00-" + lead((utc_daylight - 1) % 24) + ":59\n";

    out += "\nOutput sorted by ";
    switch (cookie['txt_sortBy']) {
        case "call":
            out += "callsign";
            break;
        case "cnt":
            out += "country";
            break;
        case "yyyymmddhhmm":
            out += "date";
            break;
        case "dx":
            out += "distance";
            break;
        case "dxw":
            out += "distance per watt";
            break;
        case "gsq":
            out += "Grid Square";
            break;
        case "lsb":
            out += "LSB Mod Offset";
            break;
        case "pwr":
            out += "transmitter power";
            break;
        case "qth":
            out += "town";
            break;
        case "sta":
            out += "state / province";
            break;
        case "hhmm":
            out += "time";
            break;
        case "usb":
            out += "USB Mod Offset";
            break;
        default:
            out += "Frequency";
            break;
    }
    out += "\n";


    out += "----------------------------------------------------------------------\n";
    switch (cookie['txt_format']) {
        case "dd":
            out += "DD ";
            break
        case "ddmmyyyy":
            out += "DD/MM/YYYY ";
            break
        case "dd.mm.yyyy":
            out += "DD.MM.YYYY ";
            break
        case "mmddyyyy":
            out += "MM/DD/YYYY ";
            break
        case "yyyymmdd":
            out += "YYYYMMDD ";
            break
        case "yyyy-mm-dd":
            out += "YYYY-MM-DD ";
            break
        case "no_date" :
            break;
    }
    out += (cookie['txt_format'] == 'no_date' ? "" : "UTC   ");
    out += "kHz   Call  ";
    out += (cookie['txt_h_mod'] != '1' && cookie['mod_abs'] != '1' ? "LSB  USB  " : "");
    out += (cookie['txt_h_mod'] != '1' && cookie['mod_abs'] == '1' ? "LSB     USB     " : "");
    out += (cookie['txt_h_cyc'] != '1' ? "Cyc.  " : "");
    out += (cookie['txt_h_pwr'] != '1' ? "Pwr. " : "");
    out += (cookie['txt_h_dx'] != '1' ? pad(get_units(), 6) : "");
    out += (cookie['txt_h_dxw'] != '1' ? "dx/w " : "");
    out += (cookie['txt_h_new'] != '1' ? "+ " : "");
    out += (cookie['txt_h_gsq'] != '1' ? "GSQ    " : "");
    out += "Location\n";
    out += "----------------------------------------------------------------------\n";

    var temp_shown = 0;
    var temp_new = 0;

// We now join station and log together and then flatten the table


//new TEXT(date,hhmm,khz,call,lsb,usb,pwr,dx,dxw,nu,qth,sta,cnt) {
    var temp_date = 0;
    var temp_hhmm = 0;
    var temp_khz = 0;
    var temp_call = 0;
    var temp_lsb = 0;
    var temp_usb = 0;
    var temp_pwr = 0;
    var temp_dx = 0;
    var temp_dxw = 0;
    var temp_nu = 0;
    var temp_qth = 0;
    var temp_sta = 0;
    var temp_cnt = 0;

    var start = new Date(start_yyyy, start_mm, start_dd)
    var end = new Date(end_yyyy, end_mm, end_dd, 23, 59, 59, 0)

    var text_array = [];
    var k = 0;
    for (var a = 0; a < sorted.length; a++) {
        var id = sorted[a].id
        if (logbook[id]) {
            for (var b = 0; b < logbook[id]['entry'].length; b++) {
                total++
                var log_yyyy = logbook[id]['entry'][b]['yyyymmddhhmm'].substr(0, 4);
                var log_mm = logbook[id]['entry'][b]['yyyymmddhhmm'].substr(4, 2) - 1;	// Months begin at 0
                var log_dd = logbook[id]['entry'][b]['yyyymmddhhmm'].substr(6, 2);
                var log_HH = logbook[id]['entry'][b]['yyyymmddhhmm'].substr(8, 2);
                var log_MM = logbook[id]['entry'][b]['yyyymmddhhmm'].substr(10, 2);
                var log_isDay = (utc_daylight_array[0] == log_HH || utc_daylight_array[1] == log_HH || utc_daylight_array[2] == log_HH || utc_daylight_array[3] == log_HH);

                var log_date = new Date(log_yyyy, log_mm, log_dd, log_HH, log_MM, 0, 0)


                if (log_date >= start && log_date <= end &&
                    ((station[id].dx >= dx_min && station[id].dx <= dx_max) || (cookie['txt_dx1'] == 0 && cookie['txt_dx2'] == 100000)) &&
                    station[id].lat >= lat_min && station[id].lat <= lat_max &&
                    station[id].lon >= lon_min && station[id].lon <= lon_max &&
                    (cookie['txt_dayNight'] == 'x' || (cookie['txt_dayNight'] == 'd' && log_isDay) || (cookie['txt_dayNight'] == 'n' && !log_isDay))) {
                    var DD = lead(log_date.getDate());
                    var MM = lead(log_date.getMonth() + 1);	// Months begin at 0
                    var YYYY = log_date.getFullYear();
                    var hh = lead(log_date.getHours())
                    var mm = lead(log_date.getMinutes())
                    temp_yyyymmddhhmm = YYYY + "" + MM + "" + DD + "" + hh + "" + mm
                    switch (cookie['txt_format']) {
                        case "dd":
                            temp_date = DD + " ";
                            break;
                        case "ddmmyyyy":
                            temp_date = DD + "/" + MM + "/" + YYYY + " ";
                            break;
                        case "dd.mm.yyyy":
                            temp_date = DD + "." + MM + "." + YYYY + " ";
                            break;
                        case "mmddyyyy":
                            temp_date = MM + "/" + DD + "/" + YYYY + " ";
                            break;
                        case "yyyymmdd":
                            temp_date = YYYY + "" + MM + "" + DD + " ";
                            break;
                        case "yyyy-mm-dd":
                            temp_date = YYYY + "-" + MM + "-" + DD + " ";
                            break;
                        case "no_date" :
                            temp_date = "";
                            break;
                    }
                    if (cookie['txt_showAll'] == '1' || !logbook[id]['shown']) {
                        if (!logbook[id]['shown']) {
                            temp_shown++
                            logbook[id]['shown'] = true;
                        }
                        temp_hhmm = (cookie['txt_format'] == "no_date" ? "" : "" + hh + ":" + mm + " ");
                        temp_khz = sorted[a].khz;
                        temp_call = sorted[a].call;
                        temp_display = sorted[a].display;
                        temp_lsb = sorted[a].lsb;
                        temp_usb = sorted[a].usb;
                        temp_pwr = sorted[a].pwr;
                        temp_cyc = sorted[a].cyc;
                        temp_dx = sorted[a].dx;
                        temp_dxw = sorted[a].dxw;
                        temp_nu = !logbook[id]['old'];
                        temp_qth = sorted[a].qth;
                        temp_sta = sorted[a].sta;
                        temp_cnt = sorted[a].cnt;
                        temp_gsq = sorted[a].gsq;
                        if (!logbook[id]['old']) {
                            temp_new++;
                            logbook[id]['old'] = true;
                        }
                        text_array[k++] = new TEXT(temp_yyyymmddhhmm, temp_date, temp_hhmm, temp_khz, temp_call, temp_display, temp_gsq, temp_lsb, temp_usb, temp_cyc, temp_pwr, temp_dx, temp_dxw, temp_nu, temp_qth, temp_sta, temp_cnt);
                        logbook[id]['shown'] = true;
                    }
                }
                else {
                    logbook[id]['old'] = true;
                }
            }
        }
    }

    text_array.sort(sortBy_call).sort(sortBy_khz).sort(eval("sortBy_" + cookie['txt_sortBy']));

    for (var k = 0; k < text_array.length; k++) {
        out +=
            text_array[k].date +
            text_array[k].hhmm +
            pad(text_array[k].khz, 5) + " " +
            pad(text_array[k].display, 5) + " ";
        if (cookie['txt_h_mod'] != '1' && cookie['mod_abs'] != '1') {
            out +=
                (text_array[k].lsb ? pad(text_array[k].lsb, 4) + " " : "     ") +
                (text_array[k].usb ? pad(text_array[k].usb, 4) + " " : "     ");
        }
        if (cookie['txt_h_mod'] != '1' && cookie['mod_abs'] == '1') {
            out +=
                (text_array[k].lsb ? pad(text_array[k].lsb, 7) + " " : "        ") +
                (text_array[k].usb ? pad(text_array[k].usb, 7) + " " : "        ");
        }
        out +=
            (cookie['txt_h_cyc'] != '1' ? pad(text_array[k].cyc, 5) + " " : "") +
            (cookie['txt_h_pwr'] != '1' ? pad(text_array[k].pwr, 4) + " " : "") +
            (cookie['txt_h_dx'] != '1' ? pad((text_array[k].dx != -1 ? text_array[k].dx : ""), 5) + " " : "");
        if (cookie['txt_h_dxw'] != '1') {
            out += ((text_array[k].dxw) ? (pad(text_array[k].dxw, 4) + " ") : ("     "));
        }
        if (cookie['txt_h_new'] != '1') {
            out += (text_array[k].nu ? "Y" : " ") + " ";
        }
        out +=
            (cookie['txt_h_gsq'] != '1' ? (text_array[k].gsq ? text_array[k].gsq : "      ") + " " : "") +
            text_array[k].qth + ", " +
            (text_array[k].sta != "" ? text_array[k].sta + ", " : "") +
            text_array[k].cnt + "\n";
    }

    out +=
        "----------------------------------------------------------------------\n" +
        temp_shown + " stations shown listed" +
        ((temp_new && cookie['txt_h_new'] != '1') ? (", including " + temp_new + " stations new to log (shown in + column)") : ("")) + ".\n" +
        "(Output generated by NDB WebLog " + version + " - looks best in courier font)"
    list_h = window.open('', 'textOutput', 'width=800,height=600,status=1,resizable=1,menubar=1,location=0,toolbar=1,scrollbars=1');
    list_h.focus();
    list_h.document.open("text/plain");
    if (document.all) {
        list_h.document.write(out);
    } else {
        list_h.document.write("<pre>" + out + "</pre>");
    }
    list_h.document.close();
}


// ************************************
// * popup_text_options()             *
// ************************************
function popup_text_options() {
    var txt_options = false;
    if (get_cookie("txt_options")) {
        var txt_options = get_cookie("txt_options").split("|");
        var i = 0;
        cookie['txt_date1'] = txt_options[i++];
        cookie['txt_date2'] = txt_options[i++];
        cookie['txt_dayNight'] = txt_options[i++];
        cookie['txt_dx1'] = txt_options[i++];
        cookie['txt_dx2'] = txt_options[i++];
        cookie['txt_format'] = txt_options[i++];
        cookie['txt_freq1'] = txt_options[i++];
        cookie['txt_freq2'] = txt_options[i++];
        cookie['txt_h_dxw'] = txt_options[i++];
        cookie['txt_h_new'] = txt_options[i++];
        cookie['txt_h_mod'] = txt_options[i++];
        cookie['txt_showAll'] = txt_options[i++];
        cookie['txt_sortBy'] = txt_options[i++];
        cookie['txt_type'] = txt_options[i++];
        cookie['txt_lat1'] = txt_options[i++];
        cookie['txt_lat2'] = txt_options[i++];
        cookie['txt_lon1'] = txt_options[i++];
        cookie['txt_lon2'] = txt_options[i++];
        cookie['txt_h_cyc'] = txt_options[i++];
        cookie['txt_h_gsq'] = txt_options[i++];
        cookie['txt_h_pwr'] = txt_options[i++];
        cookie['txt_h_dx'] = txt_options[i++];
    }
    if (!cookie['txt_format']) {
        cookie['txt_format'] = 'yyyymmdd';
    }
    if (!cookie['txt_sortBy']) {
        cookie['txt_sortBy'] = 'khz';
    }

    var out = "<!doctype html public '-//W3C//DTD HTML 4.01 Transitional//EN'>\n" +
        "<html><head><title>NDB WebLog > Text List Setup</title>\n" +
        "<meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=windows-1252\">\n" +
        "<link TITLE=\"new\" REL=\"stylesheet\" href=\"" + css + "\" type=\"text/css\">\n" +
        "<script language='javascript' type='text/javascript'>\n" +
        "function check_params(form) {\n" +
        "  var d1 =  form.txt_date1.value;\n" +
        "  var d2 =  form.txt_date2.value;\n" +
        "  var fmt = document.form.txt_format[document.form.txt_format.selectedIndex].value;\n" +
        "  if (fmt!='dd') {\n" +
        "    return true;\n" +
        "  }\n" +
        "  if (d1!='All' && d1!='' && d2!='All' && d2!='') {\n" +
        "    if (d1.substr(0,6)==d2.substr(0,6)) {\n" +
        "      return true;\n" +
        "    }\n" +
        "  }\n" +
        "  alert('Error - your date range spans more than one month - please choose another Date Format');\n" +
        "  return false;\n" +
        "}\n" +
        "function go() {\n" +
        "  var txt_date1 =    (document.form.txt_date1.value?document.form.txt_date1.value:'All');\n" +
        "  var txt_date2 =    (document.form.txt_date2.value?document.form.txt_date2.value:'All');\n" +
        "  var txt_dayNight = (document.form.txt_dayNight.value?document.form.txt_dayNight.value:'All');\n" +
        "  var txt_dx1 =      (document.form.txt_dx1.value?document.form.txt_dx1.value:'All');\n" +
        "  var txt_dx2 =      (document.form.txt_dx2.value?document.form.txt_dx2.value:'All');\n" +
        "  var txt_format =   document.form.txt_format[document.form.txt_format.selectedIndex].value;\n" +
        "  var txt_freq1 =    (document.form.txt_freq1.value?document.form.txt_freq1.value:'All');\n" +
        "  var txt_freq2 =    (document.form.txt_freq2.value?document.form.txt_freq2.value:'All');\n" +
        "  var txt_h_cyc =    parseInt(document.form.txt_h_cyc[document.form.txt_h_cyc.selectedIndex].value);\n" +
        "  var txt_h_dx =     parseInt(document.form.txt_h_dx[document.form.txt_h_dx.selectedIndex].value);\n" +
        "  var txt_h_dxw =    parseInt(document.form.txt_h_dxw[document.form.txt_h_dxw.selectedIndex].value);\n" +
        "  var txt_h_gsq =    parseInt(document.form.txt_h_gsq[document.form.txt_h_gsq.selectedIndex].value);\n" +
        "  var txt_h_mod =    parseInt(document.form.txt_h_mod[document.form.txt_h_mod.selectedIndex].value);\n" +
        "  var txt_h_new =    parseInt(document.form.txt_h_new[document.form.txt_h_new.selectedIndex].value);\n" +
        "  var txt_h_pwr =    parseInt(document.form.txt_h_pwr[document.form.txt_h_pwr.selectedIndex].value);\n" +
        "  var txt_lat1 =     (document.form.txt_lat1.value?document.form.txt_lat1.value:'All');\n" +
        "  var txt_lat2 =     (document.form.txt_lat2.value?document.form.txt_lat2.value:'All');\n" +
        "  var txt_lon1 =     (document.form.txt_lon1.value?document.form.txt_lon1.value:'All');\n" +
        "  var txt_lon2 =     (document.form.txt_lon2.value?document.form.txt_lon2.value:'All');\n" +
        "  var txt_showAll =  parseInt(document.form.txt_showAll[document.form.txt_showAll.selectedIndex].value);\n" +
        "  var txt_sortBy =   (document.form.txt_sortBy[document.form.txt_sortBy.selectedIndex].value);\n" +
        "  var txt_type =     (document.form.txt_type[document.form.txt_type.selectedIndex].value);\n" +
        "  var expires =	new Date();\n" +
        "  expires.setFullYear(expires.getFullYear()+1);\n" +
        "  expires =		expires.toGMTString();\n" +
        "  var D =		\"|\"\n" +
        "  var cookies = \"txt_options=\"+txt_date1+D+txt_date2+D+txt_dayNight+D+\n" +
        "                txt_dx1+D+txt_dx2+D+txt_format+D+txt_freq1+D+txt_freq2+D+\n" +
        "                txt_h_dxw+D+txt_h_new+D+txt_h_mod+D+txt_showAll+D+txt_sortBy+D+\n" +
        "                txt_type+D+txt_lat1+D+txt_lat2+D+txt_lon1+D+txt_lon2+D+\n" +
        "                txt_h_cyc+D+txt_h_gsq+D+txt_h_pwr+D+txt_h_dx+\";expires=\"+expires;\n" +
        "  document.cookie = cookies;\n" +
        "  window.opener.popup_text();\n" +
        "  window.close();\n" +
        "}\n" +
        "</script>\n" +
        "</head>\n" +
        "<body onload='document.body.focus()' onkeydown=\"window.opener.keydown('popup_text_options',event)\"><form name='form' action='./'>\n" +
        "<table cellpadding='0' cellspacing='0' border='0' class='noline'>\n" +
        "  <tr>\n" +
        "    <td class='plain'><table cellpadding='0' cellspacing='0' border='0' class='r' width='100%'>\n" +
        "      <tr>\n" +
        "        <th colspan='2' class='l_edge'>Text List > Output Options</th>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap width='50%' class='l_edge'>Date Format&nbsp;</td>\n" +
        "        <td nowrap width='50%'><select name='txt_format'>\n" +
        "<option value='dd'" + (cookie['txt_format'] == 'dd' ? " selected" : "") + ">DD (for CLEs)</option>\n" +
        "<option value='ddmmyyyy'" + (cookie['txt_format'] == 'ddmmyyyy' ? " selected" : "") + ">DD/MM/YYYY</option>\n" +
        "<option value='dd.mm.yyyy'" + (cookie['txt_format'] == 'dd.mm.yyyy' ? " selected" : "") + ">DD.MM.YYYY</option>\n" +
        "<option value='mmddyyyy'" + (cookie['txt_format'] == 'mmddyyyy' ? " selected" : "") + ">MM/DD/YYYY</option>\n" +
        "<option value='yyyy-mm-dd'" + (cookie['txt_format'] == 'yyyy-mm-dd' ? " selected" : "") + ">YYYY-MM-DD</option>\n" +
        "<option value='yyyymmdd'" + (cookie['txt_format'] == 'yyyymmdd' ? " selected" : "") + ">YYYYMMDD</option>\n" +
        "<option value='no_date'" + (cookie['txt_format'] == 'no_date' ? " selected" : "") + ">(No Time or date)</option>\n" +
        "</select></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap class='l_edge'>For each station&nbsp;</td>\n" +
        "        <td nowrap><select name='txt_showAll'>\n" +
        "<option value='0'" + (cookie['txt_showAll'] == '0' ? " selected" : "") + ">Show first logging</option>\n" +
        "<option value='1'" + (cookie['txt_showAll'] == '1' ? " selected" : "") + ">Show all loggings</option>\n\n" +
        "</select></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap class='l_edge'>Sort By&nbsp;</td>\n" +
        "        <td nowrap><select name='txt_sortBy'>\n" +
        "<option value='call'" + (cookie['txt_sortBy'] == 'call' ? " selected" : "") + ">Call</option>\n" +
        "<option value='cyc'" + (cookie['txt_sortBy'] == 'cyc' ? " selected" : "") + ">Cycle</option>\n" +
        "<option value='cnt'" + (cookie['txt_sortBy'] == 'cnt' ? " selected" : "") + ">Country</option>\n" +
        "<option value='yyyymmddhhmm'" + (cookie['txt_sortBy'] == 'yyyymmddhhmm' ? " selected" : "") + ">Date</option>\n" +
        "<option value='dx'" + (cookie['txt_sortBy'] == 'dx' ? " selected" : "") + ">Distance</option>\n" +
        "<option value='dxw'" + (cookie['txt_sortBy'] == 'dxw' ? " selected" : "") + ">DX per Watt</option>\n" +
        "<option value='gsq'" + (cookie['txt_sortBy'] == 'gsq' ? " selected" : "") + ">Grid Square</option>\n" +
        "<option value='khz'" + (cookie['txt_sortBy'] == 'khz' ? " selected" : "") + ">KHz</option>\n" +
        "<option value='lsb'" + (cookie['txt_sortBy'] == 'lsb' ? " selected" : "") + ">Modulation (LSB)</option>\n" +
        "<option value='usb'" + (cookie['txt_sortBy'] == 'usb' ? " selected" : "") + ">Modulation (USB)</option>\n" +
        "<option value='pwr'" + (cookie['txt_sortBy'] == 'pwr' ? " selected" : "") + ">TX power</option>\n" +
        "<option value='qth'" + (cookie['txt_sortBy'] == 'qth' ? " selected" : "") + ">Location</option>\n" +
        "<option value='sta'" + (cookie['txt_sortBy'] == 'sta' ? " selected" : "") + ">State / Province</option>\n" +
        "<option value='hhmm'" + (cookie['txt_sortBy'] == 'hhmm' ? " selected" : "") + ">Time</option>\n" +
        "</select></td>\n" +
        "      </tr>\n" +
        "    </table></td>\n" +
        "  </tr>\n" +
        "  <tr>\n" +
        "    <td class='plain'>&nbsp;</td>\n" +
        "  </tr>\n" +
        "  <tr>\n" +
        "    <td class='plain'><table cellpadding='1' cellspacing='0' border='0' class='r' width='100%'>\n" +
        "      <tr>\n" +
        "        <th colspan='2' class='l_edge'>Text List > Filters</th>" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap width='50%' class='l_edge'>Day / Night&nbsp;</td>\n" +
        "        <td nowrap width='50%'><select name='txt_dayNight'>\n" +
        "<option value='x'" + (cookie['txt_dayNight'] == 'x' ? " selected" : "") + ">All times</option>\n" +
        "<option value='d'" + (cookie['txt_dayNight'] == 'd' ? " selected" : "") + ">Daytime only</option>\n" +
        "<option value='n'" + (cookie['txt_dayNight'] == 'n' ? " selected" : "") + ">Night only</option>\n" +
        "</select></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap class='l_edge'>Signal Types&nbsp;</td>\n" +
        "        <td nowrap><select name='txt_type'>\n" +
        "<option value='all'" + (cookie['txt_type'] == 'all' ? " selected" : "") + ">All Types</option>\n" +
        "<option value='ndb'" + (cookie['txt_type'] == 'ndb' ? " selected" : "") + ">NDB only</option>\n" +
        "<option value='dgps'" + (cookie['txt_type'] == 'dgps' ? " selected" : "") + ">DGPS only</option>\n" +
        "<option value='navtex'" + (cookie['txt_type'] == 'navtex' ? " selected" : "") + ">NAVTEX only</option>\n" +
        "</select></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap class='l_edge'>Dates (YYYYMMDD)&nbsp;</td>\n" +
        "        <td nowrap><input title='Minimum value' name='txt_date1' size='8' maxlength='8' value='" + (cookie['txt_date1'] ? cookie['txt_date1'] : "All") + "' " +
        "onblur='if (document.form.txt_date1.value==\"\") document.form.txt_date1.value=\"All\"; return true;'> - " +
        "<input title='Maximum value' name='txt_date2' size='8' maxlength='8' value='" + (cookie['txt_date2'] ? cookie['txt_date2'] : "All") + "' " +
        "onblur='if (document.form.txt_date2.value==\"\") document.form.txt_date2.value=\"All\"; return true;'></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap class='l_edge'>KHz&nbsp;</td>\n" +
        "        <td nowrap><input title='Minimum value' name='txt_freq1' size='8' maxlength='6' value='" + (cookie['txt_freq1'] ? cookie['txt_freq1'] : "All") + "' " +
        "onblur='if (document.form.txt_freq1.value==\"\") document.form.txt_freq1.value=\"All\"; return true;'> - " +
        "<input title='Maximum value' name='txt_freq2' size='8' maxlength='6' value='" + (cookie['txt_freq2'] ? cookie['txt_freq2'] : "All") + "' " +
        "onblur='if (document.form.txt_freq2.value==\"\") document.form.txt_freq2.value=\"All\"; return true;'></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap class='l_edge'>DX (" + get_units() + ")&nbsp;</td>\n" +
        "        <td nowrap><input title='Minimum value' name='txt_dx1' size='8' maxlength='6' value='" + (cookie['txt_dx1'] ? cookie['txt_dx1'] : "All") + "' " +
        "onblur='if (document.form.txt_dx1.value==\"\") document.form.txt_dx1.value=\"All\"; return true;'> - " +
        "<input title='Maximum value' name='txt_dx2' size='8' maxlength='6' value='" + (cookie['txt_dx2'] ? cookie['txt_dx2'] : "All") + "' " +
        "onblur='if (document.form.txt_dx2.value==\"\") document.form.txt_dx2.value=\"All\"; return true;'></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap title='Latitude (in decimal degrees from -90 to 90)\n" +
        "Hint: for a North of 60 Report use 60 and All' class='l_edge'>Lat. (Decimal)&nbsp;</td>\n" +
        "        <td nowrap><input title='Minimum value' name='txt_lat1' size='8' maxlength='6' value='" + (cookie['txt_lat1'] ? cookie['txt_lat1'] : "All") + "' " +
        "onblur='if (document.form.txt_lat1.value==\"\") document.form.txt_lat1.value=\"All\"; return true;'> - " +
        "<input title='Maximum value' name='txt_lat2' size='8' maxlength='6' value='" + (cookie['txt_lat2'] ? cookie['txt_lat2'] : "All") + "' " +
        "onblur='if (document.form.txt_lat2.value==\"\") document.form.txt_lat2.value=\"All\"; return true;'></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap title='Longitude (in decimal degrees from -180 to 180)\n" +
        "Hint: to show stations West of 100, use All and -100' class='l_edge'>Lon. (Decimal)&nbsp;</td>\n" +
        "        <td nowrap><input title='Minimum value' name='txt_lon1' size='8' maxlength='6' value='" + (cookie['txt_lon1'] ? cookie['txt_lon1'] : "All") + "' " +
        "onblur='if (document.form.txt_lon1.value==\"\") document.form.txt_lon1.value=\"All\"; return true;'> - " +
        "<input title='Maximum value' name='txt_lon2' size='8' maxlength='6' value='" + (cookie['txt_lon2'] ? cookie['txt_lon2'] : "All") + "' " +
        "onblur='if (document.form.txt_lon2.value==\"\") document.form.txt_lon2.value=\"All\"; return true;'></td>\n" +
        "      </tr>\n" +
        "    </table></td>\n" +
        "  </tr>\n" +
        "  <tr>\n" +
        "    <td class='plain'>&nbsp;</td>\n" +
        "  </tr>\n" +
        "  <tr>\n" +
        "    <td class='plain'><table cellpadding='1' cellspacing='0' border='0' class='r' width='100%'>\n" +
        "      <tr>\n" +
        "        <th colspan='2' class='l_edge'>Text List > Columns</th>" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap class='l_edge'>Cycle&nbsp;</td>\n" +
        "        <td nowrap><select name='txt_h_cyc'>\n" +
        "<option value='0'" + (cookie['txt_h_cyc'] != '1' ? " selected" : "") + ">Show</option>\n" +
        "<option value='1'" + (cookie['txt_h_cyc'] != '1' ? "" : " selected") + ">Hide</option>\n\n" +
        "</select></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap class='l_edge'>DX&nbsp;</td>\n" +
        "        <td nowrap><select name='txt_h_dx'>\n" +
        "<option value='0'" + (cookie['txt_h_dx'] != '1' ? " selected" : "") + ">Show</option>\n" +
        "<option value='1'" + (cookie['txt_h_dx'] != '1' ? "" : " selected") + ">Hide</option>\n\n" +
        "</select></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap class='l_edge'>DX per Watt&nbsp;</td>\n" +
        "        <td nowrap><select name='txt_h_dxw'>\n" +
        "<option value='0'" + (cookie['txt_h_dxw'] != '1' ? " selected" : "") + ">Show</option>\n" +
        "<option value='1'" + (cookie['txt_h_dxw'] != '1' ? "" : " selected") + ">Hide</option>\n\n" +
        "</select></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap class='l_edge'>Grid Square&nbsp;</td>\n" +
        "        <td nowrap><select name='txt_h_gsq'>\n" +
        "<option value='0'" + (cookie['txt_h_gsq'] != '1' ? " selected" : "") + ">Show</option>\n" +
        "<option value='1'" + (cookie['txt_h_gsq'] != '1' ? "" : " selected") + ">Hide</option>\n\n" +
        "</select></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap class='l_edge'>Mod Offsets&nbsp;</td>\n" +
        "        <td nowrap><select name='txt_h_mod'>\n" +
        "<option value='0'" + (cookie['txt_h_mod'] != '1' ? " selected" : "") + ">Show</option>\n" +
        "<option value='1'" + (cookie['txt_h_mod'] != '1' ? "" : " selected") + ">Hide</option>\n\n" +
        "</select></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap width='50%' class='l_edge'>'New' loggings&nbsp;</td>\n" +
        "        <td nowrap width='50%'><select name='txt_h_new'>\n" +
        "<option value='0'" + (cookie['txt_h_new'] != '1' ? " selected" : "") + ">Show</option>\n" +
        "<option value='1'" + (cookie['txt_h_new'] != '1' ? "" : " selected") + ">Hide</option>\n\n" +
        "</select></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <td nowrap width='50%' class='l_edge'>Power&nbsp;</td>\n" +
        "        <td nowrap width='50%'><select name='txt_h_pwr'>\n" +
        "<option value='0'" + (cookie['txt_h_pwr'] != '1' ? " selected" : "") + ">Show</option>\n" +
        "<option value='1'" + (cookie['txt_h_pwr'] != '1' ? "" : " selected") + ">Hide</option>\n\n" +
        "</select></td>\n" +
        "      </tr>\n" +
        "      <tr>\n" +
        "        <th colspan='2' class='l_edge'><input type='button' value='Submit' onclick=\"if (check_params(document.form)) { this.value='Wait';this.disabled=1;go();}\"><input type='button' value='Cancel' onclick='window.close()'></th>" +
        "      </tr>\n" +
        "    </table></td>\n" +
        "  </tr>\n" +
        "</table>\n" +
        "</form>\n";
    text_options_h = window.open('', 'zoneSelector', 'width=360,height=570,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=0');
    text_options_h.focus();
    text_options_h.document.write(out);
    text_options_h.document.close();
}


// ************************************
// * popWin()                         *
// ************************************
function popWin(theURL, winName, features, windowx, windowy, centre) {
    if (centre == "centre") {
        var availx;
        var availy;
        var posx;
        var posy;
        availx = screen.availWidth;
        availy = screen.availHeight;
        posx = (availx - windowx) / 2;
        posy = (availy - windowy) / 2;
        var theWin = window.open(theURL, winName, features + ',width=' + windowx + ',height=' + windowy + ',left=' + posx + ',top=' + posy);
    } else {
        var theWin = window.open(theURL, winName, features + ',width=' + windowx + ',height=' + windowy + ',left=25,top=25');
    }
    theWin.focus();
}


// ++++++++++++++++++++++++++++++++++
// + Open Progress Indicator window +
// ++++++++++++++++++++++++++++++++++
function progress() {
    progress_hd = window.open('', 'progress', 'width=260,height=100,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=0');
    progress_hd.focus();
    progress_hd.document.write("<html><head><title>NDB WebLog " + version + "</title><style type='text/css'>h2 { font-family: Arial, sans-serif; }</style></head><body bgcolor='#ffffd8'><h2>Working...<br><small>Please wait</small></h2></body></html>");
    progress_hd.document.close();
}


function rww(ID) {
    popWin('http://www.classaxe.com/dx/ndb/rww/signal_info/' + ID, 'popsignal', 'scrollbars=0,resizable=1', 640, 380, 'centre');
}


// ************************************
// * search()                         *
// ************************************
function search(form) {
    var term = form.term.value.toUpperCase();
    var result = []
    if (term) {
        for (var i in station) {
            if (station[i].khz == term) {
                result[result.length] = i
            }
        }
        for (var i in station) {
            if (station[i].display == term) {
                result[result.length] = i
            }
        }
        if (!result.length) {
            alert("No stations matched entered criteria")
            search_h.focus()
        }
        else {
            var out =
                "<!doctype html public '-//W3C//DTD HTML 4.01 Transitional//EN'>\n" +
                "<html><head><title>NDB WebLog > Search > Results</title>\n" +
                "<meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=windows-1252\">\n" +
                "<link TITLE=\"new\" REL=\"stylesheet\" href=\"" + css + "\" type=\"text/css\">\n" +
                "</head>\n" +
                "<body onload='document.body.focus()' onkeydown=\"window.opener.keydown('search',event)\">" +
                "<h1>Results</h1>\n" +
                "<table cellspacing='0' cellpadding='0' border='0'>\n" +
                "  <tr>\n" +
                "    <th class='l_edge'>KHz</th>" +
                "<th>Call</th><th>Location</th>" +
                "<th>DX(" + get_units() + ")</th>" +
                "<th>LSB</th>" +
                "<th>USB</th>" +
                "<th colspan='2'>Last RX</th>" +
                "<th>Logs</th>\n"
            out += "  </tr>\n"
            for (var k in result) {
                var id = result[k];
                out +=
                    "  <tr title='Notes: " + station[id].notes + "'>\n" +
                    "    <td class='l_edge'>" + station[id].khz + "</td>\n" +
                    "    <td><a href='javascript:void window.opener.popup_details(\"" + id + "\");'>" + station[id].display + "</a></td>\n" +
                    "    <td>" + station[id].qth + (station[id].sta ? ", " + sta_arr[station[id].cnt][station[id].sta] : "") + ", " + cnt_arr[station[id].cnt].name + "</td>\n" +
                    "    <td>" + (station[id].dx != -1 ? station[id].dx : "&nbsp;") + "</td>\n" +
                    "    <td>" + (station[id].lsb ? station[id].lsb : "&nbsp;") + "</td>\n" +
                    "    <td>" + (station[id].usb ? station[id].usb : "&nbsp;") + "</td>\n";

                if (logbook[id]) {
                    var log_yyyymmdd = logbook[id]['entry'][logbook[id]['entry'].length - 1]['yyyymmddhhmm'].substr(0, 8);
                    var log_HH = logbook[id]['entry'][logbook[id]['entry'].length - 1]['yyyymmddhhmm'].substr(8, 2);
                    var log_MM = logbook[id]['entry'][logbook[id]['entry'].length - 1]['yyyymmddhhmm'].substr(10, 2);

                    out +=
                        "  <td>" + format_date(log_yyyymmdd) + "</td>\n" +
                        "  <td>" + log_HH + ":" + log_MM + "</td>\n" +
                        "  <td>" + logbook[id]['entry'].length + "</td>\n";
                }
                else {
                    out +=
                        "    <td>&nbsp;</th>\n" +
                        "    <td>&nbsp;</th>\n" +
                        "    <td>&nbsp;</th>\n";
                }
                out += "  </tr>\n"
            }
            out += "</table></body></html>"
            result_h = window.open('', 'resultSelector', 'width=600,height=300,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
            result_h.focus();
            result_h.document.write(out);
            result_h.document.close();
        }
    }
}


// ************************************
// * show_date_heading()              *
// ************************************
function show_date_heading() {
    switch (cookie['format']) {
        case "dd.mm.yyyy":
            return "DD.MM.YYYY";
            break
        case "ddmmyyyy":
            return "DD/MM/YYYY";
            break
        case "mmddyyyy":
            return "MM/DD/YYYY";
            break
        case "yyyy-mm-dd":
            return "YYYY-MM-DD";
            break
        default:
            return "YYYYMMDD";
            break
    }
}


// ************************************
// * show_time()                      *
// ************************************
function show_time() {
    var now =       new Date()
    var hhmmss =    lead(now.getUTCHours()) + ":" + lead(now.getUTCMinutes()) + ":" + lead(now.getUTCSeconds());
    var date =      format_date(now.getUTCFullYear()  + lead(now.getUTCMonth() + 1) + lead(now.getUTCDate()));
    top.main.document.getElementById('clock').innerHTML = date + ' ' + hhmmss + '<br>' +
        (isLocalDaylight(now.getUTCHours(), utc_offset) ?
            '<b>Daytime</b> (10am - 2pm Local Standard time)' : '<b>Nighttime</b> (2pm - 10am Local Standard time)'
        );
    setTimeout("top.show_time()", 1000)
}


// ************************************
// * sort functions:                  *
// ************************************
function sortBy_all_date(a, b) {
    anew = (a.all_yyyymmddhhmm ? a.all_yyyymmddhhmm : "999999999999");
    bnew = (b.all_yyyymmddhhmm ? b.all_yyyymmddhhmm : "999999999999");
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_all_date_d(a, b) {
    anew = (a.all_yyyymmddhhmm ? a.all_yyyymmddhhmm : "000000000000");
    bnew = (b.all_yyyymmddhhmm ? b.all_yyyymmddhhmm : "000000000000");
    if (anew > bnew) return -1;
    if (anew < bnew) return 1;
    return 0;
}

function sortBy_all_time(a, b) {
    anew = (a.all_time ? a.all_time : "9999");
    bnew = (b.all_time ? b.all_time : "9999");
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_all_time_d(a, b) {
    anew = (a.all_time ? a.all_time : "0000");
    bnew = (b.all_time ? b.all_time : "0000");
    if (anew > bnew) return -1;
    if (anew < bnew) return 1;
    return 0;
}

function sortBy_call(a, b) {
    if (a.call < b.call) return -1;
    if (a.call > b.call) return 1;
    return 0;
}

function sortBy_call_d(a, b) {
    if (a.call > b.call) return -1;
    if (a.call < b.call) return 1;
    return 0;
}

function sortBy_cnt(a, b) {
    if (a.cnt < b.cnt) return -1;
    if (a.cnt > b.cnt) return 1;
    return 0;
}

function sortBy_cnt_d(a, b) {
    if (a.cnt > b.cnt) return -1;
    if (a.cnt < b.cnt) return 1;
    return 0;
}

function sortBy_cyc(a, b) {	// Processed as text - because European NDBers describe cycles '2xID' etc
    anew = (a.cyc ? a.cyc : "ZZZZZZZZ");
    bnew = (b.cyc ? b.cyc : "ZZZZZZZZ");
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_cyc_d(a, b) {
    anew = (a.cyc ? a.cyc : " ");
    bnew = (b.cyc ? b.cyc : " ");
    if (anew > bnew) return -1;
    if (anew < bnew) return 1;
    return 0;
}

function sortBy_daid(a, b) {
    if (a.daid < b.daid) return -1;
    if (a.daid > b.daid) return 1;
    return 0;
}

function sortBy_daid_d(a, b) {
    if (a.daid > b.daid) return -1;
    if (a.daid < b.daid) return 1;
    return 0;
}

function sortBy_dir(a, b) {
    anew = (parseInt(a.dir) ? parseInt(a.dir) : 9999);
    bnew = (parseInt(b.dir) ? parseInt(b.dir) : 9999);
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_dir_d(a, b) {
    anew = (parseInt(a.dir) ? parseInt(a.dir) : 0);
    bnew = (parseInt(b.dir) ? parseInt(b.dir) : 0);
    if (anew > bnew) return -1;
    if (anew < bnew) return 1;
    return 0;
}

function sortBy_dx(a, b) {
    anew = (parseInt(a.dx) ? parseInt(a.dx) : 9999);
    bnew = (parseInt(b.dx) ? parseInt(b.dx) : 9999);
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_dx_d(a, b) {
    anew = (parseInt(a.dx) ? parseInt(a.dx) : 0);
    bnew = (parseInt(b.dx) ? parseInt(b.dx) : 0);
    if (anew > bnew) return -1;
    if (anew < bnew) return 1;
    return 0;
}

function sortBy_dxw(a, b) {
    anew = (parseFloat(a.dxw) ? parseFloat(a.dxw) : 9999);
    bnew = (parseFloat(b.dxw) ? parseFloat(b.dxw) : 9999);
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_dxw_d(a, b) {
    anew = (parseFloat(a.dxw) ? parseFloat(a.dxw) : 0);
    bnew = (parseFloat(b.dxw) ? parseFloat(b.dxw) : 0);
    if (anew > bnew) return -1;
    if (anew < bnew) return 1;
    return 0;
}

function sortBy_hhmm(a, b) {	// only used in text output
    if (a.hhmm < b.hhmm) return -1;
    if (a.hhmm > b.hhmm) return 1;
    return 0;
}


function sortBy_khz(a, b) {
    anew = parseFloat(a.khz);
    bnew = parseFloat(b.khz);
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_khz_d(a, b) {
    anew = parseFloat(a.khz);
    bnew = parseFloat(b.khz);
    if (anew > bnew) return -1;
    if (anew < bnew) return 1;
    return 0;
}

function sortBy_life_country(a, b) {
    anew = (a.cnt ? a.cnt : "ZZZ_ZZZ");
    bnew = (b.cnt ? b.cnt : "ZZZ_ZZZ");
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_life_rgn(a, b) {
    anew = a.rgn;
    bnew = b.rgn;
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_logs(a, b) {
    anew = logbook[a.id].length;
    bnew = logbook[b.id].length;
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_lsb(a, b) {
    anew = (parseFloat(a.lsb) ? parseFloat(a.lsb) : 9999);
    bnew = (parseFloat(b.lsb) ? parseFloat(b.lsb) : 9999);
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_lsb_d(a, b) {
    anew = (parseFloat(a.lsb) ? parseFloat(a.lsb) : 0);
    bnew = (parseFloat(b.lsb) ? parseFloat(b.lsb) : 0);
    if (anew > bnew) return -1;
    if (anew < bnew) return 1;
    return 0;
}

function sortBy_mm(a, b) {
    anew = (a.temp_mm ? a.temp_mm : "Z");
    bnew = (b.temp_mm ? b.temp_mm : "Z");
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_mm_d(a, b) {
    anew = (a.temp_mm ? a.temp_mm : "0");
    bnew = (b.temp_mm ? b.temp_mm : "0");
    if (anew > bnew) return -1;
    if (anew < bnew) return 1;
    return 0;
}

function sortBy_pwr(a, b) {
    anew = (parseInt(a.pwr) ? parseInt(a.pwr) : 9999);
    bnew = (parseInt(b.pwr) ? parseInt(b.pwr) : 9999);
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_pwr_d(a, b) {
    anew = (parseInt(a.pwr) ? parseInt(a.pwr) : 0);
    bnew = (parseInt(b.pwr) ? parseInt(b.pwr) : 0);
    if (anew > bnew) return -1;
    if (anew < bnew) return 1;
    return 0;
}

function sortBy_gsq(a, b) {
    if (a.gsq < b.gsq) return -1;
    if (a.gsq > b.gsq) return 1;
    return 0;
}

function sortBy_gsq_d(a, b) {
    if (a.gsq > b.gsq) return -1;
    if (a.gsq < b.gsq) return 1;
    return 0;
}

function sortBy_qth(a, b) {
    if (a.qth < b.qth) return -1;
    if (a.qth > b.qth) return 1;
    return 0;
}

function sortBy_qth_d(a, b) {
    if (a.qth > b.qth) return -1;
    if (a.qth < b.qth) return 1;
    return 0;
}

function sortBy_sta(a, b) {
    if (a.sta < b.sta) return -1;
    if (a.sta > b.sta) return 1;
    return 0;
}

function sortBy_sta_d(a, b) {
    if (a.sta > b.sta) return -1;
    if (a.sta < b.sta) return 1;
    return 0;
}

function sortBy_temp(a, b) {
    anew = (parseInt(a.temp) ? parseInt(a.temp) : 9999);
    bnew = (parseInt(b.temp) ? parseInt(b.temp) : 9999);
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_temp_d(a, b) {
    anew = (parseInt(a.temp) ? parseInt(a.temp) : 0);
    bnew = (parseInt(b.temp) ? parseInt(b.temp) : 0);
    if (anew > bnew) return -1;
    if (anew < bnew) return 1;
    return 0;
}

function sortBy_usb(a, b) {
    anew = (parseFloat(a.usb) ? parseFloat(a.usb) : 9999);
    bnew = (parseFloat(b.usb) ? parseFloat(b.usb) : 9999);
    if (anew < bnew) return -1;
    if (anew > bnew) return 1;
    return 0;
}

function sortBy_usb_d(a, b) {
    anew = (parseFloat(a.usb) ? parseFloat(a.usb) : 0);
    bnew = (parseFloat(b.usb) ? parseFloat(b.usb) : 0);
    if (anew > bnew) return -1;
    if (anew < bnew) return 1;
    return 0;
}

function sortBy_yyyymmddhhmm(a, b) {	// only used in text output
    if (a.yyyymmddhhmm < b.yyyymmddhhmm) return -1;
    if (a.yyyymmddhhmm > b.yyyymmddhhmm) return 1;
    return 0;
}


// ************************************
// * status_msg()                     *
// ************************************
function status_msg(what) {
    return " onmouseover='window.status=window.defaultStatus+\"      | " + what + "\";return true;' onmouseout='window.status=\"\";return true;'";
}

function trim(what) {
    return what.replace(" ", "");
}

function version_check() {
    ver_h = window.open('http://www.classaxe.com/dx/ndb/log/changelog/?current=' + version, 'versionPage', 'width=280,height=220,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
    ver_h.focus();
}

// ************************************
// * list()                           *
// ************************************
function list(yyyy, mm) {
    var out = '';

    // Check that system has been correctly set up:
    // First check config.js variables are set:

    if ((typeof qth_lat == "undefined") || (typeof qth_lon == "undefined") || (typeof qth_name == "undefined") ||
        (typeof qth_home == "undefined") ||
        (typeof qth_email == "undefined") || (typeof monthly == "undefined") || (typeof utc_offset == "undefined")) {
        if (progress_hd) {
            progress_hd.close();
        }
        return (
            "<h1>Error</h1><p>The <b>config.js</b> file should reside in the same directory as your other NDB WebLog files.<br>" +
            "It must include these definitions in order for this system to function correctly:</p><ul>" +
            "<li><b>qtl_lat</b> - decimal value for latitude.</li>" +
            "<li><b>qtl_lon</b> - decimal value for longitude.</li>" +
            "<li><b>qtl_name</b> - a \"string\" containing the log owner's name.</li>" +
            "<li><b>qtl_home</b> - a \"string\" containing URL of the log owner's web site, or \"\" to disable.</li>" +
            "<li><b>qtl_email</b> - a \"string\" containing email address of the log owner's web site, or \"\" to disable.</li>" +
            "<li><b>monthly</b> - 1 if you want to use the montly log format, 0 if you want a lifetime log format.</li>" +
            "<li><b>utc_offset</b> - the number of hours that standard time in your time zone (winter time) differs from UTC." +
            "<br>For example, for <b>EST</b> this value is <b>5</b>, for <b>GMT</b> this value is <b>0</b></li>");
    }

    // Now check stations.js:
    var i = 0;
    for (a in station) {
        i++
    }
    if (!i) {
        if (progress_hd) {
            progress_hd.close();
        }
        return ("<h1>Error</h1><p>There is a problem reading data in the <b>stations.js</b> file. This file should reside in the same directory as your other NDB WebLog files.<br>" +
            "It should contain at least one station, and contain no errors (e.g. strings not enclosed between \"quotes\") in order for this system to function correctly</p>");
    }
    // Now check log.js:
    i = 0;
    for (a in logbook) {
        i++
    }
    if (!i) {
        if (progress_hd) {
            progress_hd.close();
        }
        return ("<h1>Error</h1><p>There is a problem reading data in the <b>log.js</b> file. This file should reside in the same directory as your other NDB WebLog files.<br>" +
            "It should contain at least one log entry, and contain no errors (e.g. strings not enclosed between \"quotes\") in order for this system to function correctly.</p>");
    }


    var msg = status_msg("Sort by this column (ascending)")
    var msg_d = status_msg("Sort by this column (descending)")
    var msg_s = status_msg("Show details for this month");
    var msg_m = status_msg("Show map for this location");
    var msg_e = status_msg("Send an email");
    var msg_dl = status_msg("Download your copy today!");
    var msg_utc = status_msg("View World Times");

    // If monthly = 1 then monthly columns are available, otherwise you get first date and time received only
    // (for people who don't keep such detailed logs)

    if ((cookie['h_gsq'] == '1' && sel_sort == "gsq") || (cookie['h_ident'] == '1' && sel_sort == "ident") ||
        (cookie['h_notes'] == '1' && sel_sort == "notes") || (cookie['h_notes'] == '1' && sel_sort == "dxw")) {
        sel_sort = "khz";  // If user just hid the sorted column, change order back to default.
    }

    out +=
        "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>\n" +
        "<head><link TITLE='new' REL='stylesheet' href=\"" + css + "\" type='text/css'>\n" +
        "<style type='text/css'>\n" +
        ".01 { border-right: 1px solid RGB(128,128,128); background-color: RGB(" + (sel_mm == "01" ? "200,200,128" : "236,236,236") + ");}\n" +
        ".02 { border-right: 1px solid RGB(128,128,128); background-color: RGB(" + (sel_mm == "02" ? "200,200,128" : "255,255,255") + ");}\n" +
        ".03 { border-right: 1px solid RGB(128,128,128); background-color: RGB(" + (sel_mm == "03" ? "200,200,128" : "236,236,236") + ");}\n" +
        ".04 { border-right: 1px solid RGB(128,128,128); background-color: RGB(" + (sel_mm == "04" ? "200,200,128" : "255,255,255") + ");}\n" +
        ".05 { border-right: 1px solid RGB(128,128,128); background-color: RGB(" + (sel_mm == "05" ? "200,200,128" : "236,236,236") + ");}\n" +
        ".06 { border-right: 1px solid RGB(128,128,128); background-color: RGB(" + (sel_mm == "06" ? "200,200,128" : "255,255,255") + ");}\n" +
        ".07 { border-right: 1px solid RGB(128,128,128); background-color: RGB(" + (sel_mm == "07" ? "200,200,128" : "236,236,236") + ");}\n" +
        ".08 { border-right: 1px solid RGB(128,128,128); background-color: RGB(" + (sel_mm == "08" ? "200,200,128" : "255,255,255") + ");}\n" +
        ".09 { border-right: 1px solid RGB(128,128,128); background-color: RGB(" + (sel_mm == "09" ? "200,200,128" : "236,236,236") + ");}\n" +
        ".10 { border-right: 1px solid RGB(128,128,128); background-color: RGB(" + (sel_mm == "10" ? "200,200,128" : "255,255,255") + ");}\n" +
        ".11 { border-right: 1px solid RGB(128,128,128); background-color: RGB(" + (sel_mm == "11" ? "200,200,128" : "236,236,236") + ");}\n" +
        ".12 { border-right: 1px solid RGB(0,0,0);       background-color: RGB(" + (sel_mm == "12" ? "200,200,128" : "255,255,255") + ");}\n" +
        "</style>\n" +
        "<title>NDB WebLog " + version + " for " + qth_name + " > Main Listing > " + months[sel_mm - 1] + " " + sel_yyyy + "</title>\n" +
        "</head>\n" +
        "<body link='#0000ff' vlink='#800080' onload='' onkeydown='top.keydown(\"main\",event)'>\n" +
        "<form name='form' action='./'>\n" +
        "<p><b>" + ((qth_email) ? ("<a href=\"mailto:" + qth_email + "?subject=" + qth_name + " NDB%20WebLog\"" + msg_e + " title='Send me an email!'>" + qth_name + "</a>") : (qth_name)) +
        ((monthly) ? (" - " + months[sel_mm - 1] + " " + sel_yyyy) : ("")) + "<br>\n" +
        "<a onclick='popup_map(top.qth_lat,top.qth_lon,top.qth_name)'" + msg_m + " title='Click to see a map of this location'>Location</a> " + qth_lat + "," + qth_lon + " (" + get_gsq(qth_lat, qth_lon) + ")</b><br>\r\n" +
        "<small>NDB WebLog Software: " + version + " by\r\n" +
        "<a href='mailto:martin@classaxe.com' title='Bug reports? Suggestions? Contact the programmer'><b>Martin Francis</b></a>, &copy; 2003-2013<br>\r\n" +
        "This <a href='http://www.classaxe.com/dx' target='_blank'" + msg_dl + " title='NDB WebLog is free - get it here!'><b>NDB WebLog</b></a>\r\n" +
        "is configured as a <b>" + ((monthly) ? ("Monthly") : ("Lifetime")) + " list</b>,\r\n" +
        "all <b>times</b> are in <a href='http://www.timeanddate.com/worldclock' target='_blank'" + msg_utc + " title='View world clock'><b>UTC</b></a>,\r\n" +
        "and the <b>last log entry</b> was recorded on <b>" + format_date(stats.last_date) + " at " + stats.last_time + " UTC</b></small></p>\r\n" +
        "<p><small><b>UTC Time</b> <span id='clock'></span></small></p>\r\n" +
        "<p><small>Download Files: [ <a href='" + urls.package + "' target='_blank'>NDBWebLog</a> | <a href='" + urls.config + "'>config.js</a> | " +
        "<a href='" + urls.logs + "'>logs.js</a> | <a href='" + urls.stations + "'>stations.js</a> ]</small></p>"
    ;

    // Add year selection buttons
    if (monthly) {	// Find out if more than 1 years log results are recorded - if so, put year selector links on page:
        var years = [];
        var i = 0;
        for (link_yyyy in stats.year) {
            years[i++] = link_yyyy;
        }
        years.sort();
        var years_out = [];
        for (var i = 0; i < years.length; i++) {
            years_out[i] = (years[i] == sel_yyyy ? "<font color='red'>" + years[i] + "</font>" : "<a href='javascript:top.goto_page(\"" + years[i] + "\",\"" + sel_mm + "\",\"" + sel_sort + "\")'>" + years[i] + "</a>");
        }
        if (years.length > 1 || (years.length == 1 && years[0] != sel_yyyy)) {
            out += "<p align='center'><b>Year: [ " + years_out.join(" | ") + " ]</b></p>\r\n";
        }
    }
    out +=
        "<p align='center'><input type='button' class='120px' value='Statistics' onclick='void top.popup_stats()' title='[CTRL]+1'>" +
        "<input type='button' class='120px' value='Preferences' onclick='void top.popup_prefs()' title='[CTRL]+2'>" +
        "<input type='button' class='120px' value='Search' onclick='void top.popup_search()' title='[CTRL]+3'>" +
        "<input type='button' class='120px' value='Text List' onclick='void top.popup_text_options()' title='[CTRL]+4'>" +
        "<input type='button' class='120px' value='Help' onclick='top.popup_help(); return true;' title='[CTRL]+5'>" +
        ((qth_home) ? ("<input type='button' class='120px' value='Home Page' onclick='void top.popup_home()' title='[CTRL]+6'>") : ("")) +
        "</p>\n\n";

    rows = ((monthly) ? "'3'" : ((cookie['h_lifelist'] == '1') ? "'1'" : "'2'"))

    var href = ((monthly) ? ("href='javascript:top.goto_page(\"" + sel_yyyy + "\",\"" + sel_mm + "\",\"") : ("href='javascript:top.goto_page(\"\",\"\",\""))

    // Begin main output (apply borders if NS is in use)
    out += "<table border='" + ((document.all) ? ("0") : ("1")) + "' cellspacing='0' cellpadding='0'>\n";

    // Table Headings:
    out +=
        "<tr>\n" +
        "<th rowspan=" + rows + (sel_sort == "khz" || sel_sort == "khz_d" ? " class='khz_sort" + (sel_sort == "khz_d" ? "_d" : "") + "'" : " class='khz'") + ">" +
        "<a " + href + "khz" + (sel_sort == "khz" ? "_d" : "") + "\")'" + (sel_sort == "khz" ? msg_d : msg) + " title='Frequency in Kilohertz'>KHz</a></th>\n" +
        "<th rowspan=" + rows + (sel_sort == "call" || sel_sort == "call_d" ? " class='sort" + (sel_sort == "call_d" ? "_d" : "") + "'" : "") + ">" +
        "<a " + href + "call" + (sel_sort == "call" ? "_d" : "") + "\")'" + (sel_sort == "call" ? msg_d : msg) + " title='Callsign or DGPS Station Number'>Call</a></th>\n" +
        "<th rowspan=" + rows + (sel_sort == "qth" || sel_sort == "qth_d" ? " class='sort" + (sel_sort == "qth_d" ? "_d" : "") + "'" : "") + ">" +
        "<a " + href + "qth" + (sel_sort == "qth" ? "_d" : "") + "\")'" + (sel_sort == "qth" ? msg_d : msg) + " title='Location (where known)'>Location</a></th>\n" +
        "<th rowspan=" + rows + (sel_sort == "sta" || sel_sort == "sta_d" ? " class='sort" + (sel_sort == "sta_d" ? "_d" : "") + "'" : "") + ">" +
        "<a " + href + "sta" + (sel_sort == "sta" ? "_d" : "") + "\")'" + (sel_sort == "sta" ? msg_d : msg) + " title='State or province (for USA, Canada and Australia)'>S<br>t<br>a<br>t<br>e</a></th>\n" +
        "<th rowspan=" + rows + (sel_sort == "cnt" || sel_sort == "cnt_d" + (sel_sort == "cnt_d" ? "_d" : "") + "'" ? " class='sort" : "") + ">" +
        "<a " + href + "cnt" + (sel_sort == "cnt" ? "_d" : "") + "\")'" + (sel_sort == "cnt" ? msg_d : msg) + " title='NDB List approved Country Code'>C<br>o<br>u<br>n<br>t<br>r<br>y</a></th>\n";

    if (cookie['h_gsq'] != '1') {
        out +=
            "<th rowspan=" + rows + (sel_sort == "gsq" || sel_sort == "gsq_d" ? " class='sort" + (sel_sort == "gsq_d" ? "_d" : "") + "'" : "") + ">" +
            "<a " + href + "gsq" + (sel_sort == "gsq" ? "_d" : "") + "\")'" + (sel_sort == "gsq" ? msg_d : msg) + " title='ITU Grid Square locator'>GSQ</a></th>\n";
    }
    if (cookie['h_ident'] != '1') {
        out += "<th rowspan=" + rows + ">Morse / DGPS ID</th>\n";
    }
    out +=
        "<th rowspan=" + rows + (sel_sort == "daid" || sel_sort == "daid_d" ? " class='sort" + (sel_sort == "daid_d" ? "_d" : "") + "'" : "") + ">" +
        "<a " + href + "daid" + (sel_sort == "daid" ? "_d" : "") + "\")'" + (sel_sort == "daid" ? msg_d : msg) + " title='Dash After Identification?'>D<br>A<br>I<br>D</a></th>\n" +
        "<th rowspan=" + rows + (sel_sort == "cyc" || sel_sort == "cyc_d" ? " class='sort" + (sel_sort == "cyc_d" ? "_d" : "") + "'" : "") + ">" +
        "<a " + href + "cyc" + (sel_sort == "cyc" ? "_d" : "") + "\")'" + (sel_sort == "cyc" ? msg_d : msg) + " title='Period in seconds OR format of repetitions'>Cycle</a></th>\n";

    if (cookie['mod_abs'] == '1') {
        out +=
            "<th rowspan=" + rows + (sel_sort == "lsb" || sel_sort == "lsb_d" ? " class='sort" : "") + (sel_sort == "lsb_d" ? "_d" : "") + "'" + ">" +
            "<a " + href + "lsb" + (sel_sort == "lsb" ? "_d" : "") + "\")'" + (sel_sort == "lsb" ? msg_d : msg) + " title='Negative Mod Offset'>LSB<br>KHz<br>(Abs)</a></th>\n" +
            "<th rowspan=" + rows + (sel_sort == "usb" || sel_sort == "usb_d" ? " class='sort" + (sel_sort == "usb_d" ? "_d" : "") + "'" : "") + ">" +
            "<a " + href + "usb" + (sel_sort == "usb" ? "_d" : "") + "\")'" + (sel_sort == "usb" ? msg_d : msg) + " title='Positive Mod Offset'>USB<br>KHz<br>(Abs)</a></th>\n";
    }
    else {
        out +=
            "<th rowspan=" + rows + (sel_sort == "lsb" || sel_sort == "lsb_d" ? " class='sort" + (sel_sort == "lsb_d" ? "_d" : "") + "'" : "") + ">" +
            "<a " + href + "lsb" + (sel_sort == "lsb" ? "_d" : "") + "\")'" + (sel_sort == "lsb" ? msg_d : msg) + " title='Negative Mod Frequency'>LSB<br>Hz<br>(Rel)</a></th>\n" +
            "<th rowspan=" + rows + (sel_sort == "usb" || sel_sort == "usb_d" ? " class='sort" + (sel_sort == "usb_d" ? "_d" : "") + "'" : "") + ">" +
            "<a " + href + "usb" + (sel_sort == "usb" ? "_d" : "") + "\")'" + (sel_sort == "usb" ? msg_d : msg) + " title='Positive Mod Frequency'>USB<br>Hz<br>(Rel)</a></th>\n";
    }
    out +=
        "<th rowspan=" + rows + (sel_sort == "pwr" || sel_sort == "pwr_d" ? " class='sort" + (sel_sort == "pwr_d" ? "_d" : "") + "'" : "") + ">" +
        "<a " + href + "pwr" + (sel_sort == "pwr" ? "_d" : "") + "\")'" + (sel_sort == "pwr" ? msg_d : msg) + " title='Power in Watts (where known)'>Pwr</a></th>\n" +
        "<th rowspan=" + rows + (sel_sort == "dir" || sel_sort == "dir_d" ? " class='sort" + (sel_sort == "dir_d" ? "_d" : "") + "'" : "") + ">" +
        "<a " + href + "dir" + (sel_sort == "dir" ? "_d" : "") + "\")'" + (sel_sort == "dir" ? msg_d : msg) + " title='Bearing in degrees from reception location'>Deg</a></th>\n" +
        "<th rowspan=" + rows + (sel_sort == "dx" || sel_sort == "dx_d" ? " class='sort" + (sel_sort == "dx_d" ? "_d" : "") + "'" : "") + ">" +
        "<a " + href + "dx" + (sel_sort == "dx" ? "_d" : "") + "\")'" + (sel_sort == "dx" ? msg_d : msg) + " title='Distance to station from reception location'>" +
        get_units() + "</a></th>\n";

    if (cookie['h_dxw'] != '1') {
        out +=
            "<th rowspan=" + rows + (sel_sort == "dxw" || sel_sort == "dxw_d" ? " class='sort" + (sel_sort == "dxw_d" ? "_d" : "") + "'" : "") + ">" +
            "<a " + href + "dxw" + (sel_sort == "dxw" ? "_d" : "") + "\")'" + (sel_sort == "dxw" ? msg_d : msg) + " title='Distance covered by a single Watt (where DX and power are both known)'>" +
            get_units() + "<br>per<br>W</a></th>\n";
    }
    if (monthly) {
        out +=
            "<th class='sel' rowspan='1' colspan='12'>&nbsp;" + sel_yyyy + " Summary<br>&nbsp; N&#9;Nighttime<br>&nbsp; D&#9;Daytime<br>&nbsp; X&#9;Both</th>\n" +
            "<th colspan='4' class='sel'>Details for<br>" + months[sel_mm - 1] + " " + sel_yyyy + "<br><br>(UTC)<br></th>\n";
    }
    if (cookie['h_lifelist'] != '1' && monthly) {
        out += "<th rowspan='2' colspan='2' class='all_log'>First<br>\nReceived</th>\n";
    }

    if (cookie['h_lifelist'] != '1' && !monthly) {
        out += "<th rowspan='1' colspan='2' class='all_log'>First RXed</th>\n";
    }
    if (cookie['h_logs'] != '1') {
        out += "<th rowspan=" + rows + " class='notes'>L<br>o<br>g<br>s</th>\n";
    }
    if (cookie['h_notes'] != '1') {
        out += "<th rowspan=" + rows + " class='notes'>Notes" + (monthly ? "<br><span class='normal'>(D:Day, N: Night)</span>" : "") + "</th>\n";
    }
    out += "</tr>\n\n";


    if (monthly) {

        // Month summary headings:
        // If none of the months is already selected:
        //   classes of each month = mm_arr[i] except for mmclass=((mm==mm_arr[i])?(selected):(months[i].subst(0,2)))
        //   click changes month but leaves sort order as it is.
        // If month already selected and sort order is NOT set to the month
        //   class=((mm==mm_arr[i])?(sort):(mm_abr[i]))
        //   click changes sort order to month
        // If month already selected and sort order IS set to the month
        //   class=((mm==mm_arr[i])?(sorted_d):(mm_abr[i]))
        //   click reverses sort order in month

        out += "<TR>\n";
        var sorting_months = false;
        for (var i = 0; i < 12; i++) {
            if (sel_sort == mm_arr[i] || sel_sort == mm_arr[i] + "_d") {
                sorting_months = true;
            }
        }

        for (var i = 0; i < 12; i++) {
            if (!sorting_months) {
                out += "<th rowspan='2'><a href='javascript:top.goto_page(\"" + sel_yyyy + "\",\"" + mm_arr[i] + "\",\"" + (sel_mm == mm_arr[i] ? mm_arr[i] : sel_sort) + "\")' " + (mm_arr[i] != sel_mm ? msg_s : msg) + " title='" + months[i] + "'>";
            }
            else {
                if (sel_mm == mm_arr[i]) {		// The current month is selected already
                    if (sel_sort == sel_mm) {	// Sorting on this month already, so next time sort decending:
                        out += "<th rowspan='2' class='sort'><a href='javascript:top.goto_page(\"" + sel_yyyy + "\",\"" + mm_arr[i] + "\",\"" + mm_arr[i] + "_d\")'" + msg_d + " title='" + months[i] + "'>";
                    }
                    else {					// Not sorting on this month already, so next time sort ascending:
                        out += "<th rowspan='2' class='sort_d'><a href='javascript:top.goto_page(\"" + sel_yyyy + "\",\"" + mm_arr[i] + "\",\"" + mm_arr[i] + "\")'" + msg + " title='" + months[i] + "'>";
                    }
                }
                else {
                    out += "<th rowspan='2' class='" + mm_arr[i] + "'><a href='javascript:top.goto_page(\"" + sel_yyyy + "\",\"" + mm_arr[i] + "\",\"" + mm_arr[i] + "\")'" + (mm_arr[i] != sel_mm ? msg_s : msg) + " title='" + months[i] + "'>";
                }
            }
            out += months[i].substr(0, 1) + "</a></th>\n";
        }
        out +=
            "<th COLSPAN=2>Night<br>" + lead((utc_daylight + 4) % 24) + ":00-<br>" + lead((utc_daylight - 1) % 24) + ":59</th>\n<th colspan='2'>Day<br>" + lead(utc_daylight) + ":00-<br>" + lead((utc_daylight + 3) % 24) + ":59</th>\n</TR>\n\n" +
            "<tr>\n<th" + ((sel_sort == "ndd") ? (" class='sort'") : ("")) + ((sel_sort == "ndd_d") ? (" class='sort_d'") : ("")) + "><a " + href + "ndd" + ((sel_sort == "ndd") ? ("_d") : ("")) + "\")'" + ((sel_sort == "ndd") ? (msg_d) : (msg)) + " title='Day of Month'>D</a></th>\n" +
            "<th" + ((sel_sort == "nhhmm") ? (" class='sort'") : ("")) + ((sel_sort == "nhhmm_d") ? (" class='sort_d'") : ("")) + "><a " + href + "nhhmm" + ((sel_sort == "nhhmm") ? ("_d") : ("")) + "\")'" + ((sel_sort == "nhhmm") ? (msg_d) : (msg)) + " title='Time (UTC)'>UTC</a></th>\n" +
            "<th" + ((sel_sort == "ddd") ? (" class='sort'") : ("")) + ((sel_sort == "ddd_d") ? (" class='sort_d'") : ("")) + "><a " + href + "ddd" + ((sel_sort == "ddd") ? ("_d") : ("")) + "\")'" + ((sel_sort == "ddd") ? (msg_d) : (msg)) + " title='Day of Month'>D</a></th>\n" +
            "<th" + ((sel_sort == "dhhmm") ? (" class='sort'") : ("")) + ((sel_sort == "dhhmm_d") ? (" class='sort_d'") : ("")) + "><a " + href + "dhhmm" + ((sel_sort == "dhhmm") ? ("_d") : ("")) + "\")'" + ((sel_sort == "dhhmm") ? (msg_d) : (msg)) + " title='Time (UTC)'>UTC</a></th>\n";
    }

    if (cookie['h_lifelist'] != '1') {
        out +=
            "<th " + ((sel_sort == "all_date") ? (" class='sort'") : ("")) + ((sel_sort == "all_date_d") ? (" class='sort_d'") : ("")) + "><a " + href + "all_date" + ((sel_sort == "all_date") ? ("_d") : ("")) + "\")'" + ((sel_sort == "all_date") ? (msg_d) : (msg)) + " title='Date (in format shown in column)'>" + show_date_heading() + "</a></th>\r\n" +
            "<th " + ((sel_sort == "all_time") ? (" class='sort'") : ("")) + ((sel_sort == "all_time_d") ? (" class='sort_d'") : ("")) + "><a " + href + "all_time" + ((sel_sort == "all_time") ? ("_d") : ("")) + "\")'" + ((sel_sort == "all_time") ? (msg_d) : (msg)) + " title='Time (UTC)'>UTC</a></th>\n";
    }

    out += "</tr>\n\n";

    // Process data - convert from associative array into linear to allow sorting by column headings:
    var sorted = [];
    var i = 0;

    if (sel_sort) {
        switch (sel_sort) {
            case "01":
            case "02":
            case "03":
            case "04":
            case "05":
            case "06":
            case "07":
            case "08":
            case "09":
            case "10":
            case "11":
            case "12":
            case "01_d":
            case "02_d":
            case "03_d":
            case "04_d":
            case "05_d":
            case "06_d":
            case "07_d":
            case "08_d":
            case "09_d":
            case "10_d":
            case "11_d":
            case "12_d":
                for (var a in station) {
                    if (station[a].all_date) {
                        sorted[i] = station[a];
                        sorted[i].temp_mm = "";	// For sorting of time or date columns
                        if (station[a].log[sel_yyyy] && station[a].log[sel_yyyy][sel_mm]) {
                            sorted[i].temp_mm = station[a].log[sel_yyyy].rx[sel_mm];
                        }
                        i++;
                    }
                }
                break;
            case "ddd":
            case "ddd_d":
                for (var a in station) {
                    if (station[a].all_date) {
                        sorted[i] = station[a];
                        sorted[i].temp = "";	// For sorting of XND columns
                        if (station[a].log[sel_yyyy] && station[a].log[sel_yyyy][sel_mm] && station[a].log[sel_yyyy][sel_mm].day_dd) {
                            sorted[i].temp = station[a].log[sel_yyyy][sel_mm].day_dd;
                        }
                        i++;
                    }
                }
                break;
            case "dhhmm":
            case "dhhmm_d":
                for (var a in station) {
                    if (station[a].all_date) {
                        sorted[i] = station[a];
                        sorted[i].temp = "";	// For sorting of XND columns
                        if (station[a].log[sel_yyyy] && station[a].log[sel_yyyy][sel_mm] && station[a].log[sel_yyyy][sel_mm].day_dd) {
                            sorted[i].temp = station[a].log[sel_yyyy][sel_mm].day_hhmm;
                        }
                        i++;
                    }
                }
                break;
            case "ndd":
            case "ndd_d":
                for (var a in station) {
                    if (station[a].all_date) {
                        sorted[i] = station[a];
                        sorted[i].temp = "";	// For sorting of XND columns
                        if (station[a].log[sel_yyyy] && station[a].log[sel_yyyy][sel_mm] && station[a].log[sel_yyyy][sel_mm].night_dd) {
                            sorted[i].temp = station[a].log[sel_yyyy][sel_mm].night_dd;
                        }
                        i++;
                    }
                }
                break;
            case "nhhmm":
            case "nhhmm_d":
                for (var a in station) {
                    if (station[a].all_date) {
                        sorted[i] = station[a];
                        sorted[i].temp = "";	// For sorting of XND columns
                        if (station[a].log[sel_yyyy] && station[a].log[sel_yyyy][sel_mm] && station[a].log[sel_yyyy][sel_mm].night_dd) {
                            sorted[i].temp = station[a].log[sel_yyyy][sel_mm].night_hhmm;
                        }
                        i++;
                    }
                }
                break;
            default:
                for (var a in station) {
                    if (station[a].all_date) sorted[i++] = station[a];
                }
                break;
        }

        switch (sel_sort) {
            case "khz":
                sorted.sort(sortBy_khz);
                break;
            case "call":
                sorted.sort(sortBy_call);
                break;
            case "qth":
                sorted.sort(sortBy_qth);
                break;
            case "sta":
                sorted.sort(sortBy_cnt).sort(sortBy_sta);
                break;
            case "cnt":
                sorted.sort(sortBy_sta).sort(sortBy_cnt);
                break;
            case "01":
            case "02":
            case "03":
            case "04":
            case "05":
            case "06":
            case "07":
            case "08":
            case "09":
            case "10":
            case "11":
            case "12":
                sorted.sort(sortBy_mm);
                break;
            case "01_d":
            case "02_d":
            case "03_d":
            case "04_d":
            case "05_d":
            case "06_d":
            case "07_d":
            case "08_d":
            case "09_d":
            case "10_d":
            case "11_d":
            case "12_d":
                sorted.sort(sortBy_mm_d);
                break;
            case "ddd":
            case "dhhmm":
            case "ndd":
            case "nhhmm":
                sorted.sort(sortBy_temp);
                break;
            case "ddd_d":
            case "dhhmm_d":
            case "ndd_d":
            case "nhhmm_d":
                sorted.sort(sortBy_temp_d);
                break;
            default:
                sorted.sort(eval("sortBy_" + sel_sort));
                break;
        }
    }

//  fnTimerStart("List_0");	// Benchmark timer function

    // Output data
    var count_NDB_ever = 0;
    var count_DGPS_ever = 0;
    var count_NAVTEX_ever = 0;
    var count_UNID_ever = 0;


    var total_year = 0;
    var total_month = 0;

    for (var a = 0; a < sorted.length; a++) {
        if (!sorted[a].gsq) {
            count_UNID_ever++;
        }
        else {
            switch (sorted[a].call.substr(0, 1)) {
                case "#":
                    count_DGPS_ever++;
                    break;
                case "$":
                    count_NAVTEX_ever++;
                    break;
                default:
                    count_NDB_ever++;
                    break;
            }
        }
        cnt_name = (cnt_arr[sorted[a].cnt] ? cnt_arr[sorted[a].cnt].name : sorted[a].cnt);
        sta_name = (sta_arr[sorted[a].cnt] ? sta_arr[sorted[a].cnt][sorted[a].sta] : "?");
        out +=
            "<tr>\n" +
            "<td class='khz'><a name='" + sorted[a].id + "'></a>" + sorted[a].khz + "</td>\n" +
            "<td class='call'><a title='Display details for this station' href='javascript:void top.popup_details(\"" + sorted[a].id + "\")'>" + sorted[a].display + "</a></td>\n" +
            "<td class='qth'>" + (sorted[a].qth ? sorted[a].qth : "&nbsp;") + "</td>\n" +
            "<td class='sta'>" + (sorted[a].sta ? "<a class='info' href='javascript:void 0' " + status_msg("State = " + sta_name) + " title='" + sta_name + "'>" + sorted[a].sta + "</a>" : "&nbsp;") + "</td>\n" +
            "<td class='cnt'>" + (sorted[a].cnt ? "<a class='info' href='javascript:void 0' " + status_msg("Country = " + cnt_name) + " title='" + cnt_name + "'>" + sorted[a].cnt + "</a>" : "&nbsp;") + "</td>\n";

        if (cookie['h_gsq'] != '1') {
            out += "<td class='gsq'>" + (sorted[a].gsq ? "<a href='javascript:top.popup_map(\"" + sorted[a].lat + "\",\"" + sorted[a].lon + "\",\"" + sorted[a].id + "\")'" + msg_m + " title='Click to show a map of this location'>" + sorted[a].gsq + "</a>" : "&nbsp;") + "</td>\n";
        }
        if (cookie['h_ident'] != '1') {
            out += "<td class='ident'>" + sorted[a].ident + "</td>\n";
        }
        out += "<td class='daid'>" + (sorted[a].daid != "" ? sorted[a].daid : "&nbsp;") + "</td>\n<td class='cyc'>" + (sorted[a].cyc != "" ? sorted[a].cyc : "&nbsp;") + "</td>\n<td class='lsb'>" + (sorted[a].lsb != "" ? sorted[a].lsb : "&nbsp;") + "</td>\n<td class='usb'>" + (sorted[a].usb != "" ? sorted[a].usb : "&nbsp;") + "</td>\n<td class='pwr'>" + (sorted[a].pwr != "" ? sorted[a].pwr : "&nbsp;") + "</td>\n<td class='dir'>" + (sorted[a].dir != -1 ? sorted[a].dir : "&nbsp;") + "</td>\n<td class='dx'>" + (sorted[a].dx != -1 ? sorted[a].dx : "&nbsp;") + "</td>\n";
        if (cookie['h_dxw'] != '1') {
            out += "<td class='dxw'>" + (sorted[a].dxw != "" ? sorted[a].dxw : "&nbsp;") + "</td>\n";
        }
        if (monthly) {
            if (sorted[a].log[sel_yyyy]) {
                total_year++;
                out += "<td class='01'>" + (sorted[a].log[sel_yyyy].rx["01"] ? sorted[a].log[sel_yyyy].rx["01"] : "&nbsp;") + "</td>\n<td class='02'>" + (sorted[a].log[sel_yyyy].rx["02"] ? sorted[a].log[sel_yyyy].rx["02"] : "&nbsp;") + "</td>\n<td class='03'>" + (sorted[a].log[sel_yyyy].rx["03"] ? sorted[a].log[sel_yyyy].rx["03"] : "&nbsp;") + "</td>\n<td class='04'>" + (sorted[a].log[sel_yyyy].rx["04"] ? sorted[a].log[sel_yyyy].rx["04"] : "&nbsp;") + "</td>\n<td class='05'>" + (sorted[a].log[sel_yyyy].rx["05"] ? sorted[a].log[sel_yyyy].rx["05"] : "&nbsp;") + "</td>\n<td class='06'>" + (sorted[a].log[sel_yyyy].rx["06"] ? sorted[a].log[sel_yyyy].rx["06"] : "&nbsp;") + "</td>\n<td class='07'>" + (sorted[a].log[sel_yyyy].rx["07"] ? sorted[a].log[sel_yyyy].rx["07"] : "&nbsp;") + "</td>\n<td class='08'>" + (sorted[a].log[sel_yyyy].rx["08"] ? sorted[a].log[sel_yyyy].rx["08"] : "&nbsp;") + "</td>\n<td class='09'>" + (sorted[a].log[sel_yyyy].rx["09"] ? sorted[a].log[sel_yyyy].rx["09"] : "&nbsp;") + "</td>\n<td class='10'>" + (sorted[a].log[sel_yyyy].rx["10"] ? sorted[a].log[sel_yyyy].rx["10"] : "&nbsp;") + "</td>\n<td class='11'>" + (sorted[a].log[sel_yyyy].rx["11"] ? sorted[a].log[sel_yyyy].rx["11"] : "&nbsp;") + "</td>\n<td class='12'>" + (sorted[a].log[sel_yyyy].rx["12"] ? sorted[a].log[sel_yyyy].rx["12"] : "&nbsp;") + "</td>\n";
            }
            else {
                out += "<td class='01'>&nbsp;</td>\n<td class='02'>&nbsp;</td>\n<td class='03'>&nbsp;</td>\n<td class='04'>&nbsp;</td>\n<td class='05'>&nbsp;</td>\n<td class='06'>&nbsp;</td>\n<td class='07'>&nbsp;</td>\n<td class='08'>&nbsp;</td>\n<td class='09'>&nbsp;</td>\n<td class='10'>&nbsp;</td>\n<td class='11'>&nbsp;</td>\n<td class='12'>&nbsp;</td>\n";
            }
            if (sorted[a].log[sel_yyyy] && sorted[a].log[sel_yyyy].rx[sel_mm]) {
                total_month++;
            }
            if (sorted[a].log[sel_yyyy] && sorted[a].log[sel_yyyy][sel_mm]) {
                out += "<td class='n_yymmdd'>" + ((sorted[a].log[sel_yyyy][sel_mm].night_dd) ? (parseFloat(sorted[a].log[sel_yyyy][sel_mm].night_dd)) : ("&nbsp;")) + "</td>\n<td class='n_hhmm'>" + ((sorted[a].log[sel_yyyy][sel_mm].night_hhmm) ? (sorted[a].log[sel_yyyy][sel_mm].night_hhmm) : ("&nbsp;")) + "</td>\n<td class='d_yymmdd'>" + ((sorted[a].log[sel_yyyy][sel_mm].day_dd) ? (parseFloat(sorted[a].log[sel_yyyy][sel_mm].day_dd)) : ("&nbsp;")) + "</td>\n<td class='d_hhmm'>" + ((sorted[a].log[sel_yyyy][sel_mm].day_hhmm) ? (sorted[a].log[sel_yyyy][sel_mm].day_hhmm) : ("&nbsp;")) + "</td>\n";
            }
            else {
                out += "<td class='n_yymmdd'>&nbsp;</td>\n<td class='n_hhmm'>&nbsp;</td>\n<td class='d_yymmdd'>&nbsp;</td>\n<td class='d_hhmm'>&nbsp;</td>\n";
            }
        }
        if (cookie['h_lifelist'] != '1') {
            out += "<td class='all_log'>" + ((sorted[a].all_date) ? (sorted[a].all_date) : ("&nbsp;")) + "</td>\n<td class='all_log'>" + ((sorted[a].all_time) ? (sorted[a].all_time) : ("&nbsp;")) + "</td>\n";
        }
        if (cookie['h_logs'] != '1') {
            out += "<td class='logs'>" + ((logbook[sorted[a].khz + '-' + sorted[a].call]) ? (logbook[sorted[a].khz + '-' + sorted[a].call]['entry'].length) : ("&nbsp;")) + "</td>\n";
        }
        if (cookie['h_notes'] != '1') {
            out += "<td class='notes'>" + ((sorted[a].notes) ? sorted[a].notes : '');
            if (monthly) {
                out += (sorted[a].log && sorted[a].log[sel_yyyy] && sorted[a].log[sel_yyyy][sel_mm] && sorted[a].log[sel_yyyy][sel_mm].notes && sorted[a].log[sel_yyyy][sel_mm].notes.length ? sorted[a].log[sel_yyyy][sel_mm].notes.join(", ") : "&nbsp;");
            }
            else {
                out += (sorted[a].all_notes.length ? sorted[a].all_notes : "&nbsp;");
            }
            out += "</td>\n";
        }
        out += "</tr>\n\n";
    }
    out += "</table>\n";
    out += "<b><a name='bottom'></a>ALL TIME: " + count_NDB_ever + " NDBs, " + count_DGPS_ever + " DGPS, " + count_NAVTEX_ever + " NAVTEX stations and " + count_UNID_ever + " UNIDS";
    if (monthly) {
        out += " with a total of " + total_year + " stations received in " + sel_yyyy + (top.stats.year[sel_yyyy] ? " (including " + top.stats.year[sel_yyyy]['new_stations_ever'] + " new)" : "");
        out += " and " + total_month + " in " + months[sel_mm - 1] + " " + sel_yyyy + (top.stats.year[sel_yyyy] && top.stats.year[sel_yyyy][sel_mm] && top.stats.year[sel_yyyy][sel_mm]['new_stations_ever'] ? " (including " + top.stats.year[sel_yyyy][sel_mm]['new_stations_ever'] + " new)" : "");
    }
    out += ".</b><br><br>\n<script>if(top.show_time) { top.show_time()}</script></form></body></html>"
//  fnTimerEnd("List_0",0);


    if (unregistered_stations.length) {
        var error_page = "<html><head><title>NDB WebLog > Error > Unregistered Stations</title></head><body><h1>Unregistered Stations</h1>" +
            "<p>The following " + unregistered_stations.length + " stations were logged but do not appear in the stations.js file:</p>" +
            "<ol><li>" + unregistered_stations.join("</li>\n<li>") + "</li></ol></body></html>";

        error_h1 = window.open('', 'errorViewer', 'width=350,height=600,status=1,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
        error_h1.focus();
        error_h1.document.write(error_page);
        error_h1.document.close();
    }
    if (unregistered_countries.length) {
        var error_page = "<html><head><title>NDB WebLog > Error > Unregistered Countries</title></head><body><h1>Unregistered Countries</h1>" +
            "<p>The following " + unregistered_countries.length + " stations have country codes which do not appear in the countries.js file:</p>" +
            "<ol><li>" + unregistered_countries.join("</li>\n<li>") + "</li></ol></body></html>";

        error_h2 = window.open('', 'errorViewer', 'width=350,height=600,status=1,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
        error_h2.focus();
        error_h2.document.write(error_page);
        error_h2.document.close();
    }


    if (progress_hd) {
        progress_hd.close();
    }
    top.main.document.write(out);
    top.main.document.close();
}
