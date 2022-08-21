current =		window.location.href.match(/current=([0-9]+\.[0-9]+\.[0-9]+)/);
if (current) {
    current = current[1];
}

// ************************************
// * releases data                    *
// ************************************
function RELEASE(version,date,changes) {
    this.version =	version;
    this.date =		date;
    this.changes =	changes;
}

releases = [];
var i = 0;

releases[i++] =
    new RELEASE("1.0.0","9 Jul, 2003",
        "<ul>\n<li>Initial Release</li>\n</ul>");
releases[i++] =
    new RELEASE("1.0.2","21 Jul, 2003",
        "<ul>\n<li>System now supports two formats - <b>Monthly</b> log and a new <b>Lifetime</b> log for people who don't collect monthly day / night statistics.</li>\n"+
        "<li>A single parameter in the config.js file switches logs between the two formats (reversable at any time).</li>\n"+
        "<li>Alternative stats are available for the new Lifetime format. See <b><a href='http://www.classaxe.com/dx/ndb/log/alex/' target='_blank'>Alex Wiecek's</a></b> log by way of example.</li>\n"+
        "<li>New columns show \"First Received Date and Time\" besides station - switchable in user preferences.</li>\n"+
        "<li>System clock used to set default month and year (no longer set in config.js which simplifys setup and maintenance).</li>\n"+
        "</ul>");
releases[i++] =
    new RELEASE("1.0.3","26 Jul, 2003",
        "<ul>\n<li>Colours used in statistics reports to indicate magitudes are now automatically adjusted to provide maximum contrast regardless of actual data values.</li>\n"+
        "<li>Added new statistics for Lifetime logs.</li>\n"+
        "<li>You may now select Nautical Miles as the measurement distance.</li>\n"+
        "</ul>");
releases[i++] =
    new RELEASE("1.0.4","3 Aug, 2003",
        "<ul>\n<li>You can select date format in preferences - use <b>YYYYMMDD</b>, <b>DD/MM/YYYY</b>, <b>MM/DD/YYYY</b> or just <b>DD</b> for all reports.</li>\n"+
        "<li>There is a new Text output function which can be used to prepare CLE (Coordinated Listening Event) conformant logs with all times in UTC. A variety of date output formats is supported. The idea is that for each CLE (spanning perhaps 3 days), you install an NDB WebLog taking only the portion of your log corresponding to that time period.</li>\n"+
        "</ul>");
releases[i++] =
    new RELEASE("1.0.5","7 Aug, 2003",
        "<ul>\n<li>The biggest change - the system now runs from your hard disk - the web server is optional. Column selection is now by means of temporary cookies instead of URI Query string. "+
        "This version DOES run without a web server (I promise!). My sincere applogies to Brian and Alex, both of whom tried out the buggy version locally without success.</li>"+
        "<li>Feedback is now provided in the status bar (bottom of the browser window) saying what each hyperlink does if clicked.</li>\n"+
        "</ul>");
releases[i++] =
    new RELEASE("1.0.6","13 Aug, 2003",
        "<ul>\n<li>This release includes <b>filters for text output</b> - you can specify date and frequency ranges and choose to see all loggings or just the first "+
        "occasion on which each beacon was received in the period and frequency range given</b>. "+
        "The big advantage to this is that <b>you no longer need to maintain separate logs for Co-ordinated Listening Event (CLE) Logs</b> - "+
        "just use your main log and filter the results any way you wish. For this reason, my own separate CLE logs have now been deleted.</li>\n"+
        "<li>The Countries Report now shows you the furthest beacon received for each country logged.</li>\n"+
        "</ul>");
releases[i++] =
    new RELEASE("1.0.7","22 Aug, 2003",
        "<ul>\n<li>This release includes <b>Selectable Map Zoom</b> - you can specify the zoom level for the mapquest maps which appear when you click on a location. "+
        "Thanks to T.S.Bauge for the suggestion.</li>\n"+
        "<li>Any 'new' stations received in a given period are now identified as such in the Text Output listing.</li>\n"+
        "<li>There are more descriptive messages associated with errors in country code definitions in the stations.js file.</li>\n"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.0","27 Aug, 2003",
        "<ul>\n<li>Following a simplification of the 'Daytime DX' definition which now locks this period to a fixed range of UTC hours "+
        "that remains constant throughout the year, this release now displays all times and dates in <b>UTC format</b>. simplifying preparation of "+
        "CLE logs using text output.<br>"+
        "<b>This requirement means that all times are to be logged using UTC</b>. "+
        "A conversion utility is available from the author for anyone with existing local-time based log data already entered into the system.<br>"+
        "In addition, there is a <b>new parameter in the config.js file</b> which must be provided during setup to specify the number of hours offset "+
        "between Local Standard Time and UTC. See the new config.js file for more details.</li>"+
        "<li><b>Settings used in the Text Output dialog are slightly simplified</b> (there is no 'Time Zone' field any longer) and <b>remembered between sessions</b>.</li>"+
        "<li>There is a <b>UTC 'real time clock'</b> displayed at all times in the main screen and the browser status bar - please note, this requires your system clock to be set correctly.</li>\n"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.1","4 Sep, 2003",
        "<ul>\n<li>The <b>date of the last log entry recorded</b> (assuming log.js is arranged in Chronological order) <b>is shown</b> in the configuration details line for the main listing.</li>"+
        "<li>Added extended morse characters for <b>German</b>, <b>Spanish</b> and <b>Scandanavian languages</b>."+
        "Please note that the Russian alphabet uses some latin characters but assigns different codes to them - for example,"+
        "<b>B in English is -...</b> but <b>B in Russian is .--</b> since it is pronounced W. This means it isn't possible to support"+
        "Russian at the same time as Latin Alphabets - at least not yet.<br>"+
        "This may be possible if we assume that beacons in russia use the alternative character set.<br>"+
        "<b>If you want this feature - let me know!</b></li>"+
        "<li><b>5-letter Callsigns</b> (used by ships - see example <a href='http://home.online.no/~tjabauge/radio/ndblog/' target='_blank'><b>here</b></a>)"+
        "are now correctly displayed in text list output format - these were previously cropped to 4 characters.</li>"+
        "<li>The <b>ITU QTH</b> column heading has been shortened to the stanadard <b>GSQ</b> abbreviation.</li>\n"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.2","6 Sep, 2003",
        "<ul><li><b>Tooltips are now provided</b> (for users of Internet Explorer on Windows only) when user moves mouse over each column heading. This is in addition to information provided in the Status Bar.</li>"+
        "<li>The <b>First Received HHMM</b> column has been renamed to simply <b>Time</b> to conform with the other headings.</li>"+
        "<li>A link to <b>Beaconworld</b> is provided.</li>"+
        "<li>The (still very primative) <b>search form</b> has been tidied up somewhat.</li>\n"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.3","13 Sep, 2003",
        "<ul><li><b>Help</b> is now provided in a pop-up window to reduce clutter on the main screen.</li>"+
        "<li>Full <b>State</b> and <b>Country</b> names are now given when the mouse is moved over "+
        "<a href='http://www.beaconworld.org.uk/files/countrylist.pdf' target='_blank' title='See the NDB List approved country and state codes'>abbreviations</a></li>"+
        "<li><b>Day / Night indication</b> given on the real-time UTC clock in the status bar.</li>"+
        "<li><b>Pwr</b> column added to text output.</li>"+
        "<li>Bug when sorting <b>KHz</b> column with frequencies higher than 999KHz now fixed.</li>"+
        "<li>Bug when displaying <b>DAID</b> entries which contain now value now corrected.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.4","22 Sep, 2003",
        "<ul><li><b>Sort Order</b> may now be defined in text output mode.</li>"+
        "<li><b>Number of Beacons</b>, plus <b>Number of New Beacons</b> shown in Text Output.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.5","26 Sep, 2003",
        "<ul><li><b>New Sort Orders in text output mode</b> (very tricky to do believe it or not!).</li>"+
        "<li><b>Selectable Columns in text output mode</b>.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.6","18 Oct, 2003",
        "<ul><li><b>Max DX Day and Night tables</b> now adjust to accommodate the maximum DX which needs to be displayed</li>"+
        "<li>New features to facilitate the handling logging of <b>DGPS stations</b>.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.7","23 Oct, 2003",
        "<ul><li><b>Two versions now available: UPGRADE AND FULL</b> - This should simplify install process and reduce risk of data loss.</li>"+
        "<li><b>Serious date bug fixed</b> - dates in December were shown as January the following year.<br>Because <b>Michael Silvers</b> spotted this serious issue, this release is dedicated to him!</li>"+
        "<li><b>Hyperlinks now highlighted when mouse over them</b> (newer browsers only)</li>"+
        "<li><b>Improved layout and MUCH BETTER formatting for Netscape</b> - also font size may be changed using "+
        "web browser font size settings</li>"+
        "<li><b>Speed improvements</b> (only slight I'm afraid!)</li>"+
        "<li><b>Bug when handling multiple note entries for a single month corrected</b></li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.8","28 Oct, 2003",
        "<ul><li><b>Progress window popup when loading</b></li>"+
        "<li><b>Range Filtering in Text Output</b> - You can now specify DX range conditions for Text Output.</li>"+
        "<li><b>Improved stats for Lifetime Logs</b> - now includes regions report (previously added for Monthly Logs).</li>"+
        "<li><b>Version Check</b> - Click on help then press 'Upgrade Check' to see if a log is running the latest version.</li>"+
        "<li><b>Speed improvements</b> - mainly relating to sorting of data in columns (now 25% faster).</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.9","28 Oct, 2003",
        "<ul>"+
        "<li><b>Better detection and reporting of system setup errors</b> - thanks to <b>David Towers</b> for inspiring these changes.</b></li>"+
        "<li><b>Progress window popup when producing statistics</b></li>"+
        "<li><b>Day / Night Filtering in Text Output</b> - limit to day, night or both.</li>"+
        "<li><b>Text Output tweek</b> Removed 'including N beacons new to log' when 'New' column is hidden.</li>"+
        "<li><b>Internal changes to country / state and region data</b> to make it easier to use with future improved edition of Station Editor.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.10","10 Nov, 2003",
        "<ul>"+
        "<li><b>Better detection and reporting of system setup errors</b> - multiple stations which have invalid country codes are now shown as a single "+
        "error report after loading instead of multiple error messages.</li>"+
        "<li><b>DD.MM.YYYY date format added</b> - for use by German speaking users (Thanks Udo for the idea).</li>"+
        "<li><b>DGPS / NDB filter in text output</b> - also tidied up the text output options form to make it clearer.</li>"+
        "<li><b>Default values provided in text output</b> - if you tab out of any of the range fields and leave them blank, the value 'All' is automatically inserted for you.</li>"+
        "<li><b>Bug fix in remembering preferences</b> - browsers impose a limit of 20 cookies per domain - changes in this version combine the 24(!!) cookies used into 3 multiplexed cookies, so setting are not lost between sessions even if you have several logs held on one server.</li>"+
        "<li><b>Bug fix in lifelog statistics</b> - following changes in the previous release, regions statistics in lifelogs failed to work correctly.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.11","14 Nov, 2003",
        "<ul>"+
        "<li><b>New Station Details popup window</b> (click on Callsign in Station Listing). Displays all available details about a station, including a list of all loggings for the station. (Suggested by Udo Deutscher.)</li>"+
        "<li><b>Hide Time and Date columns in text output</b>.</li>"+
        "<li><b>Absolute or Relative offset selection</b> in list and text output for people who tune to beacons in CW mode using narrow filters.<br>\n"+
        "<b>Tip: if you use CW mode and require a list of absolute offset to tune to, here's how you can create your own 'Daytime seek list':</b><ol>\n"+
        "  <li>In <b>Preferences > Options</b> set <b>Mod Values = Absolute</b> (new feature in this version)</li>\n"+
        "  <li>In <b>Text List > Output Options</b> set <b>Date Format = (No Time or Date)</b> (new feature in this version)</li>\n"+
        "  <li>In <b>Text List > Output Options</b> set <b>For Each Beacon = Show First Logging</b></li>\n"+
        "  <li>In <b>Text List > Output Options</b> set <b>Sort By = Modulation (USB)</b> or <b>(LSB)</b></li>\n"+
        "  <li>In <b>Text List > Filters</b> set <b>Day / Night = Daytime Only</b> (New feature in 1.1.10)</li>\n"+
        "  <li>Now press <b>Submit</b> to generate your daytime 'seek list'</li>\n"+
        "</li></ol></li>"+
        "<li><b>Improved Sort Order in List View</b> for sorting by Country (records are sorted by State within each Country) and by State (records are sorted by country first so countries with no states appear in correct order).</li>"+
        "<li><b>Bug fix in text output</b> - following changes in 1.1.0 text output was showing all receptions when 'Show first logging' was selected.</li>"+
        "<li><b>Bug fix highlighting sorted columns</b> - I'm not sure when this error crept in but it was a simple stylesheet issue.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.12","8 Dec, 2003",
        "<ul>"+
        "<li><b>Total number of stations received in selected month shown at bottom of list output with monthly logs - saves having to open stats for this info.</li>"+
        "<li><b>Help Page</b> - Now very much more detailed and contained as a separate file.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.13","4 Jan, 2004",
        "<ul>"+
        "<li><b>Bug fix - year selection</b>: if all stations were only received the previous year, the year selector previously failed to display, thereby preventing the listener from selecting that year. This is now fixed.</li>"+
        "<li><b>Search</b> - The search function now compiles matches for stations where you enter either callsign or frequency, complete with details on when station was last received.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.14","9 Jan 2004",
        "<ul>"+
        "<li><b>Quick-Links in stats now enabled for Netscape users</b> - a lot harder than it sounds</li>"+
        "<li><b>All-time Total beacon-count shown in list window</b> - along with the total for the selected year and month.</li>"+
        "<li><b>Improved stats</b> - in Country report best DGPS and best NDB shown in separate columns in listing.</li>"+
        "<li><b>YYYY-MM-DD date format added</b> - as per ISO 8601 (Thanks to Tjaerand for the suggestion).</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.15","18 Jan 2004",
        "<ul>"+
        "<li><b>All previously heard stations now listed</b> - irresepective of when they were heard. This means you can use last years loggings to point you towards being able to repeat loggings list year. One of those things that didn't occur to me until I was faced with looking at a blank logbook screen in Jan of 2004, with no idea what I heard last year. A big usability gain I think.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.16","12 Feb 2004",
        "<ul>"+
        "<li><b>Filter text output by Latitude and Longitude</b> - Ideal for producing 'North of 60' log submissions (or 'West of 100' for that matter!). Thanks to Tjaerand once again for the suggestion.</li>"+
        "<li><b>Keyboard Shortcuts for all reports</b> - Tooltips on buttons show which shortcut operates which function. You can also hit 'Escape' to close all popup windows. My most used is CTRL+3 for the Search function.</li>"+
        "<li><b>Search Results now show notes for each listed station</b> - this information is provided as a tool tip and is handy when perusing a list of DGPS stations on a channel and seeing at a glance which ones operate at 100 and which at 200BPS.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.17","28 Feb 2004",
        "<ul>"+
        "<li><b>Speed increase</b> - the system now runs between 10% and 30% faster when sorting columns depending on browser used. Also, after the initial loading, no further accesses are made to web server when system is running remotely. This speed increase is made possible by hiding the code in a frameset behind the main results screen.</li>"+
        "<li><b>Search results</b> - minor bug fix, window is now closable by pressing escape.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.18","11 Mar 2004",
        "<ul>"+
        "<li><b>Statistics now show new beacons for year and for all time by month</b> - One of those things I didn't realise I needed until the first year had passed.</li>"+
        "<li><b>Log Count column</b> - Shows how many times each station has been logged. Hideable in Preferences.</li>"+
        "<li><b>Minor formatting improvements</b> - designed to make reports look better in any browser.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.19","7 Apr 2004",
        "<ul>"+
        "<li><b>TX Pwr column hideable in Text Output.</b></li>"+
        "<li><b>New GSQ column available in Text Output</b></li>"+
        "<li><b>New Cycle column available in Text Output</b> (only use this if your Cycle entries consist of a straight decimal time in seconds for the ident cycle - less useful for more complex entries such as those used by many European listeners)</li>"+
        "<li><b>Various speed improvements - now runs about 25% faster in IE6</b> (although the code looks a little uglier!). Thanks to David Thomas for the use of his Benchmarking routines and advice to make this possible.<br><b>Test case: PIII 733MHz Windows XP machine running IE6</b><ul><li><b>Before:</b> 42.5 sec</li><li><b>After:</b> 34.1 sec - 25% faster.</li></ul>Mozilla and Netscape users however won't notice a significant speed increase.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.20","2 Jun 2004",
        "<ul>"+
        "<li><b>Bug fix - clicking on QTH location for listener caused JS error and no map.</b></li>"+
        "<li><b>DX column now hidable in Text Output</b></li>"+
        "<li><b>Bug fix - GSQ should be formatted AAnnaa, it was AAnnAA. This is now fixed.</b></li>"+
        "<li><b>Bug fix - stations with unknown coordinates were NOT appearing in the text output, even when the DX filtering was disabled.</b></li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.21","Not publically released",
        "<ul>"+
        "<li><b>Bug fix - unknown country / states caused crash and problems in stats.</b></li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.22","12 Dec 2004",
        "<ul>"+
        "<li><b>New feature - because many users now experience problems with Windows XP SP2 blocking popups</b> (causing NDB WebLog to fail) <b>this version includes warnings that popups must be enabled in order to use the system.</b></li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.23","19 Oct 2005",
        "<ul>"+
        "<li>Following suggestions from <b>Tjaerand</b> and the introduction of an NDB WebLog Export feature for <a href='http://www.ve3gop.com/' target='_blank'><b>WWSU 6.1</b></a>, changes have been made for the first time in almost a year.</li>"+
        "<li><b>Stats now break out Navtex stations (prefixed with a $) from NDBs</b></li>"+
        "<li><b>You can now filter text output to show only Navtex stations.</b></li>"+
        "<li><b>Bug fixes for WWSU export</b> - WWSU 6.1 inserts a space for LSB, USB and Power when it doesn't have a value - this previously caused problems for NDB WebLog with values like 'Infinity' and 'NaN' (Not A Number) being displayed for some columns.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.24","01 Nov 2005",
        "<ul>"+
        "<li>Following a bug notification by <b>Francesco</b> primarily relating to output generated by <a href='http://www.ve3gop.com/' target='_blank'><b>WWSU 6.1</b></a>, there are some bug fixes and a couple of additional minor refinements for this edition.</li>"+
        "<li><b>Bug fix 1:</b> WWSU sometimes exports a State or Province as \" \" where \"\" is expected (e.g. for European countries) - this caused problems with NDB WebLog trying to find a state called \" \" which doesn't exist.</li>"+
        "<li><b>Bug fix 2:</b> LSB, USB and Cycle Time values of ? are now screened out to prevent display of NaN in columns</li>"+
        "<li><b>Bug fix 3:</b> Power values are sometimes exported from WWSU with a trailing w e.g. 100w - this caused problems that are now corrected for.</li>"+
        "<li><b>Bug fix 4:</b> Power values are sometimes exported from WWSU as non-numerical values e.g. U - this caused problems that are now corrected for.</li>"+
        "<li><b>New refinement: GSQ is shown for listener's location along with lat / lon.</b></li>"+
        "<li><b>New refinement: Text List doesn't allow DD to be used as a date format where output potentially spans more than one month.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.25","30 July 2006",
        "<ul>"+
        "<li>WWSU 6.2 introduced a new mechanism to support two stations with same ID on same channel (e.g. DB-341 which exists in Canada and USA):<br>where duplicates exist, it appends ';n' to the end of both - e.g. OSA;2<br>\nNDB Weblog now uses this to support duplicate IDs but displays throughout without the ;n appendage.</li>"+
        "<li>Navtex stations now completely split from NDBs in Statistics report (begun in 1.1.23) under Countries table.</li>"+
        "<li>Navtex stations now no longer incorrectly show morse equivalents for their callsigns.</li>"+
        "<li>Made some subtle text changes to change 'Beacons' and 'Signals' to 'Stations' where these were not split into distinct station types.</li>"+
        "<li>Now includes additional support for an optional 13th entry in stations.js file - the ID of the coresponding signal in RWW. So far the only system supporting this is RWW itself, though it is hoped WWSU will eventually offer functionality in this area too. Where the field is available, users with internet access can click a 'More...' link in Station Details to see RWW data for the station.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.26","Date unknown",
        "<ul>"+
        "<li>Includes new country codes for MNE and SRB.</li>"+
        "<li>Now includes additional support for an optional 14th entry in stations.js file - flag indicating whether or not the station is still active.</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.27","2013-12-21",
        "<ul>"+
        "<li>Now autocorrects inclusion path for css file</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.28","2018-08-18",
        "<ul>"+
        "<li>Minor changes for new symfony-based RXX system</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.29","2020-03-25",
        "<ul>"+
        "<li>Change to download files link to reference new RXX system</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.30","2022-07-11",
        "<ul>"+
        "<li>Bit of a refresh of functions.js to tidy it up a bit</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.30b","2022-07-11",
        "<ul>"+
        "<li>More code style updates (No version name update)</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.30c","2022-07-11",
        "<ul>"+
        "<li>Tweak for Daytime detection</li>"+
        "</ul>");
releases[i++] =
    new RELEASE("1.1.31","2022-08-21",
        "<ul>"+
        "<li>Signal Details additional info link now points to new RXX version</li>"+
        "</ul>");

// ************************************
// * changelog()                      *
// ************************************
function changelog(current){
    rexp_version =	/([0-9]+)\.([0-9]+)\.([0-9]+)/;		// Regular expression to split version codes
    if (current) {
        var latest =	releases[releases.length-1].version
        document.write("<body bgcolor='" + ( current == latest ? "#e8ffe8": "#ffd0do") + "'>\n");
        document.write("<h3>NDB WebLog Version Check</h3>\n");
        document.write("<p><ul><li><b>Your version: <font color='red'>"+current+"</font></b></li>");
        document.write("<li><b>Latest version: <font color='red'>"+latest+"</font></b></li>");
        if (current == latest) {
            document.write("<li><b><font color='#206020'>This system is up to date</font></b></li>");
        }
        else {
            document.write("<li><b><a href='../upgrade.exe'>Get Upgrade</a> (NDB WebLog Administrator only)</b></li></ul>");
            document.write("<hr align='center'><h3>What's new?</h3>\n");
        }
        document.write("</ul></p>");
        var cur =		current.match(rexp_version);
        var cur_1 =		parseInt(cur[1]);
        var cur_2 =		parseInt(cur[2]);
        var cur_3 =		parseInt(cur[3]);
    }
    else {
        document.write("<h3>NDB WebLog Release History</h3>");
    }
    document.write("<table>\n");
    for (var i=releases.length-1; i>=0; i--) {
        var ver =		releases[i].version.match(rexp_version);
        var ver_1 =		parseInt(ver[1]);
        var ver_2 =		parseInt(ver[2]);
        var ver_3 =		parseInt(ver[3]);
        if (!current || (cur_1<ver_1) || (cur_1==ver_1 && cur_2<ver_2) || (cur_1==ver_1 && cur_2==ver_2 && cur_3<ver_3)) {
            document.write("<tr><td><b>"+releases[i].version+"</b> Released: "+releases[i].date+"</td></tr>\n");
            document.write("<tr><td>"+releases[i].changes+"</th></tr>\n");
        }
    }
    document.write("</table>\n");
}