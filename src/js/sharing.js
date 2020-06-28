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
            ('undefined' !== typeof f2.val() && 0 !== parseInt(f2.val()) ? '&page=' + f2.val() : '');
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
        var types = [];
        $("fieldset#form_type div input").each(function() {
            if ($(this).is(':checked') && 'ALL' !== $(this).prop('value')) {
                types.push($(this).prop('value'));
            }
            if (0 === types.length) {
                types = ['NDB'];
            }
            if (7 === types.length) {
                types = ['ALL'];
            }
        });
        return '?types=' + $.uniqueSort(types).join(',');
    },
    listenersUrl: function() {
        return this.getBaseUrl('listeners') +
            this.getFromTypes() +
            this.getFromField('q') +
            this.getFromField('region') +
            this.getFromField('country') +
            this.getFromField('has_logs', [ 'N', 'Y' ], 'A') +
            this.getFromField('has_map_pos', [ 'N', 'Y' ], 'A') +
            this.getFromField('timezone') +
            this.getFromPagingControls(100) +
            this.getFromSortingControls('name', 'a');
    },
    signalsUrl: function() {
        return this.getBaseUrl('signals') +
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
            this.getFromField('offsets', ['1']) +
            this.getFromField('range_gsq') +
            this.getFromField('range_min') +
            this.getFromField('range_max') +
            (this.getFromField('range_gsq') ? this.getFromRadioGroup('range_units') : '') +

            this.getFromRadioGroup('paper', [ 'a4', 'a4_l', 'lgl', 'lgl_l', 'ltr', 'ltr_l' ]);
    }
};

function shareListeners() {
    var url = shareableLink.listenersUrl();
    var dialog = $('#dialog');
    dialog
        .html(
            '<p>' + msg.share.listeners.text1 +'<br>' + msg.share.listeners.text2 +'</p>' +
            '<ul>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=list">' + msg.share.listeners.links.list + '</a></li>' +
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
            '<p>' + msg.share.signals.text1 +'<br>' + msg.share.signals.text2 +'</p>' +
            '<ul>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=list">' + msg.share.signals.links.list + '</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=seeklist">' + msg.share.signals.links.seeklist + '</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=map">' + msg.share.signals.links.map + '</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=csv">' + msg.share.signals.links.export + '</a></li>' +
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
            title: msg.share.signals.title
        });
//    alert(url);
//    copyToClipboard(url);
}
