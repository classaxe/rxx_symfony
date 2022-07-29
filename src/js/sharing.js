var shareableLink = {
    getBaseUrl: function(mode) {
        return window.location.protocol + '//' + window.location.host + base_url + mode;
    },
    getFromField: function(field, options, letterCase) {
        var value = $('#form_' + field).val();
        if ('undefined' === typeof value || '' === value) {
            return '';
        }
        if ('string' === typeof letterCase && -1 !== $.inArray(letterCase, [ 'a', 'A' ])) {
            value = ('a' === letterCase ? value.toLowerCase() : value.toUpperCase());
        }
        if ('undefined' === typeof options || -1 !== $.inArray(value, options)) {
            return '&' + field + '=' + encodeURI(value);
        }
        return '';
    },
    getFromListeners: function() {
        var f1 = $('#form_listener');
        if ('undefined' === typeof f1.val() || '' === encodeURI(f1.val())) {
            return '';
        }
        return '&listeners=' + encodeURI(f1.val());
    },
    getFromPagingControls: function(defaultLimit) {
        var f1 = $('#form_limit');
        var f2 = $('#form_page');
        return (defaultLimit !== parseInt(f1.val()) ? '&limit=' + f1.val() : '') +
            ('undefined' !== typeof f2.val() && null !== f2.val() && 0 !== parseInt(f2.val()) ? '&page=' + f2.val() : '');
    },
    getFromPair: function(field) {
        var f1 = $('#form_' + field + '_1');
        var f2 = $('#form_' + field + '_2');
        return (f1.val() || f2.val() ?
                '&' + field + '=' + encodeURI(f1.val()) +
                (f1.val() !== f2.val() ? ',' + encodeURI(f2.val()) : '')
                : ''
        );
    },
    getFromRadioGroup: function(field, options) {
        var f1 = $("input[name='form[" + field + "]']:checked");
        if ('undefined' === typeof f1.val() || '' === f1.val()) {
            return '';
        }
        if ('undefined' === typeof options || -1 !== $.inArray(f1.val(), options)) {
            return '&' + field + '=' + encodeURI(f1.val());
        }
        return '';
    },
    getFromSortingControls: function(defaultSorting, defaultOrder) {
        var f1 = $('#form_sort');
        var f2 = $('#form_order');
        return (defaultSorting !== f1.val() ? '&sort=' + f1.val() : '') +
            (defaultOrder !== f2.val() ? '&order=' + f2.val() : '');
    },
    getFromTypes: function() {
        var types = [], url;
        $("fieldset#form_type div input").each(function() {
            if ($(this).is(':checked') && 'ALL' !== $(this).prop('value')) {
                types.push($(this).prop('value'));
            }
        });
        if (0 === types.length) {
            types = ['NDB'];
        }
        if (7 === types.length) {
            types = ['ALL'];
        }
        url = '&types=' + $.uniqueSort(types).join(',');
        return (url === '&types=NDB' ? '' : url);
    },
    listenersUrl: function(suffix) {
        var base = this.getBaseUrl('listeners');
        var url =
            this.getFromTypes() +
            this.getFromField('q') +
            this.getFromField('region') +
            this.getFromField('country') +
            this.getFromField('rxx_id') +
            this.getFromRadioGroup('has_logs', [ 'N', 'Y' ]) +
            this.getFromRadioGroup('has_map_pos', [ 'N', 'Y' ]) +
            (this.getFromField('timezone') !== '&timezone=ALL' ? this.getFromField('timezone') : '') +
            this.getFromField('status', [ 'N', 'Y', '30D', '3M', '6M', '1Y', '2Y', '5Y' ], 'A') +
            this.getFromField('equipment') +
            this.getFromField('notes') +
            this.getFromPagingControls(500) +
            this.getFromSortingControls('name', 'a') +
            (typeof suffix !== 'undefined' ? suffix : '');

        return base + (url.substring(0,1) === '&' ? '?' + url.substring(1) : url);
    },
    signalsUrl: function(suffix) {
        var base = this.getBaseUrl('signals');
        var url =
            this.getFromTypes() +
            this.getFromField('rww_focus') +
            this.getFromField('call') +
            this.getFromPair('khz') +
            this.getFromField('channels') +
            this.getFromField('states') +
            this.getFromField('sp_itu_clause', [ 'or' ]) +
            this.getFromField('countries') +
            this.getFromField('region') +
            this.getFromField('gsq') +
            this.getFromField('recently') +
            this.getFromField('within') +
            this.getFromField('active') +

            this.getFromListeners() +
            this.getFromRadioGroup('listener_invert', [ '1' ]) +
            this.getFromField('heard_in') +
            this.getFromRadioGroup('heard_in_mod', [ 'all' ]) +
            this.getFromPair('logged_date') +
            this.getFromPair('logged_first') +
            this.getFromPair('logged_last') +

            this.getFromPagingControls(50) +
            this.getFromSortingControls('khz', 'a') +
            this.getFromField('personalise') +

            this.getFromRadioGroup('hidenotes') +
            this.getFromRadioGroup('morse') +
            this.getFromRadioGroup('offsets') +

            this.getFromField('range_gsq') +
            this.getFromField('range_min') +
            this.getFromField('range_max') +
            (this.getFromField('range_gsq') ? this.getFromRadioGroup('range_units') : '') +

            this.getFromRadioGroup('paper', [ 'a4', 'a4_l', 'lgl', 'lgl_l', 'ltr', 'ltr_l' ]) +
            this.getFromField('admin_mode') +
            (typeof suffix !== 'undefined' ? suffix : '');

        return base + (url.substring(0,1) === '&' ? '?' + url.substring(1) : url);
    }
};

function shareListeners() {
    var url = shareableLink.listenersUrl();
    var dialog = $('#dialog');
    dialog
        .html(
            '<p>' + msg.share.listeners.text1 +'<br>' + msg.share.listeners.text2 +'</p>' +
            '<ul>' +
            '<li><a style="color:#0000ff" href="' + url + '">' + msg.share.listeners.links.list + '</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=map">' + msg.share.listeners.links.map + '</a></li>' +
            '</ul>')
        .dialog({
            buttons: [{
                text: msg.close ,
                click: function() {
                    $( this ).dialog( "close" );
                }
            }],
            open: function() {
                $('.ui-dialog-buttonpane button').focus();
            },
            modal: true,
            title: msg.share.listeners.title
        });
//    alert(url);
//    copyToClipboard(url);
}

function shareSignals() {
    var url = shareableLink.signalsUrl();
    var dialog = $('#dialog');
    dialog
        .html(
            '<p style="margin:0">' + msg.share.signals.text1 +'<br>' + msg.share.signals.text2 +'</p>' +
            '<ul>' +
            '<li><a style="color:#0000ff" href="' + url + '">' + msg.share.signals.links.list + '</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=seeklist">' + msg.share.signals.links.seeklist + '</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=map">' + msg.share.signals.links.map + '</a></li>' +
            '</ul>' +
            '<p style="margin:0"><strong>' + msg.share.signals.links.export + '</strong></p>' +
            '<ul style="margin-bottom:0">' +
            '<li><a style="color:#0000ff" href="' + url + '&show=csv">signals.csv</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=kml">signals.kml</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=txt">signals.txt</a></li>' +
            '</ul>')
        .dialog({
            buttons: [{
                text: msg.close,
                click: function() {
                    $( this ).dialog( "close" );
                }
            }],
            open: function() {
                $('.ui-dialog-buttonpane button').focus();
            },
            modal: true,
            title: msg.share.signals.title
        });
//    alert(url);
//    copyToClipboard(url);
}
