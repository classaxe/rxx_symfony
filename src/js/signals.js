var SIGNALS_FORM = {
    init : function(resultsCount) {
        $(document).ready( function() {
            var c = COMMON_FORM;
            var s = SIGNALS_FORM;
            s.setPersonaliseAction();
            s.setIdentAction();
            s.setKhzAction();
            s.setListenerFilterAction();
            s.setShowNotesAction();
            s.setShowMorseAction();
            s.setShowOffsetsAction();
            s.setRangeAction();
            s.setRangeUnitsDefault();
            s.setHeardIn();
            s.setSortByAction();
            s.setSortZaAction();
            s.setPaperSizeAction();

            s.setRwwFocusAction();
            c.setStatusDefault();
            c.setTypesStyles();
            c.setTypesDefault();
            c.setTypesAllAction();
            s.setStatesLabelLink();
            s.setCountriesLabelLink();
            c.setRegionAction();
            s.setAdminAction();

            s.setListenerInvertDefault();
            s.setHeardInModDefault();
            s.setListenerOptionsStyle();
            s.setCollapsableSections();
            s.setSaveAction();
            s.setResetAction();

            c.setDatePickerActions();
            if (resultsCount !== false) {
                c.setPagingControls();
                c.setPagingStatus(msg.paging_s, resultsCount);
            }

            s.setActions();
            s.setIdentTip();
            s.setFocusOnCall();
            s.showStats();

        });
    },

    ituSps: {
        AUS : 'AT NN NW QD SA TA VI WE',
        CAN : 'AB BC MB NB NL NS NT NU ON PE QC SK YT',
        NZL : 'CI NI SI',
        USA : 'AL AR AZ CA CO CT DC DE FL GA IA ID IL IN KS KY LA MA MD ME MI MN MO MS MT NC ND NE NH NJ NM NV NY OH OK OR PA RI SC SD TN TX UT VA VT WA WI WV WY'
    },

    getExportAllArgs: function() {
        var args = [];
        var types = shareableLink.getFromTypes()
        var admin_mode = shareableLink.getFromField('admin_mode', ['0', '1', '2']);
        if (types) {
            args.push(types.substring(1));
        }
        if (admin_mode) {
            args.push(admin_mode.substring(1));
        }
        if (args.length) {
            return '?' + args.join('&');
        }
        return '';
    },

    setActions : function() {
        $('#btn_csv_all').click(function () {
            if (confirm(
                msg.export
                    .replace(':system', system.toUpperCase())
                    .replace(':format', '.csv') +
                "\n" + msg.export2
            )) {
                window.location.assign(window.location + '/export/csv' + SIGNALS_FORM.getExportAllArgs());
            }
        });
        $('#btn_kml_all').click(function () {
            if (confirm(
                msg.export
                    .replace(':system', system.toUpperCase())
                    .replace(':format', '.kml') +
                "\n" + msg.export2
            )) {
                window.location.assign(window.location + '/export/kml' + SIGNALS_FORM.getExportAllArgs());
            }
        });
        $('#btn_txt_all').click(function () {
            if (confirm(
                msg.export
                    .replace(':system', system.toUpperCase())
                    .replace(':format', '.txt') +
                "\n" + msg.export2
            )) {
                window.location.assign(window.location + '/export/txt' + SIGNALS_FORM.getExportAllArgs());
            }
        });
        $('#btn_psk_all').click(function () {
            if (confirm(
                msg.export
                    .replace(':system', system.toUpperCase())
                    .replace(':format', 'PSKOV') +
                "\n" + msg.export2 +
                (system.toUpperCase() === 'RWW' ? '' : "\n\n" + msg.export3)
            )) {
                window.location.assign(window.location + '/export/xls' + SIGNALS_FORM.getExportAllArgs());
            }
        });
        $('#btn_csv_fil').click(function () {
            var filename = prompt('Filename', system + '_signals.csv');
            if (filename === null) {
                return;
            }
            var form_show = $('#form_show');
            var form_filename = $('#form_filename');
            var show = form_show.val();
            form_show.val('csv');
            form_filename.val(filename);
            $('#form_submit').click();
            form_show.val(show);
        });
        $('#btn_txt_fil').click(function () {
            var filename = prompt('Filename', system + '_signals.txt');
            if (filename === null) {
                return;
            }
            var form_show = $('#form_show');
            var form_filename = $('#form_filename');
            var show = form_show.val();
            form_show.val('txt');
            form_filename.val(filename);
            $('#form_submit').click();
            form_show.val(show);
        });
        $('#btn_kml_fil').click(function () {
            var filename = prompt('Filename', system + '_signals.kml');
            if (filename === null) {
                return;
            }
            var form_show = $('#form_show');
            var form_filename = $('#form_filename');
            var show = form_show.val();
            form_show.val('kml');
            form_filename.val(filename);
            $('#form_submit').click();
            form_show.val(show);
        });
        $('#btn_prt').click(function () {
            window.print();
            return false;
        });
        $('#btn_share').click(function() {
            shareSignals();
            return false;
        });
        $('#btn_new').click(function() {
            window.open('./signals/new', 'signal_new', popWinSpecs['signals_[id]']);
            return false;
        });
        $('form[name="form"]').on('submit', function() {
            $('#form_call').val($('#form_call').val().toUpperCase());
        });
    },

    setAdminAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('select#form_admin_mode').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_admin_mode').off('change');
        }
    },

    setCollapsableSections : function() {
        $('#section_loggings legend').click(
            function() {
                $(this).parent().find('fieldset').toggle();
                $(this).parent().find('fieldset fieldset').toggle();
                $(this).find('span').toggle();
            }
        );
        $('#section_customise legend').click(
            function() {
                $(this).parent().find('fieldset').toggle();
                $(this).parent().find('fieldset fieldset').toggle();
                $(this).find('span').toggle();
            }
        );
    },

    setCountriesLabelLink : function() {
        var ele = $('label[for="form_countries"]');
        ele.html('<a href="countries/*" data-popup="1">' + ele.html() + '</a>');
    },

    setFocusOnCall : function() {
        var f = $('#form_call');
        f.focus();
        f.select();
    },

    setHeardIn : function() {
        $('#form_heard_in').on('keyup', function(){
            $('#form_heard_in').val(function (_, val) {
                return val.toUpperCase();
            });
            $.each(SIGNALS_FORM.ituSps, function(itu, sps){
                var field = $('#form_heard_in')
                var heardIn = field.val();
                if (heardIn.indexOf(itu) >= 0) {
                    alert('Country code ' + itu + ' will be expanded to show all states, provinces and territories');
                    field.val(field.val().replace(itu, sps));
                }
            })
        });
    },

    setHeardInModDefault : function() {
        if ($('fieldset#form_heard_in_mod div :radio:checked').length === 0) {
            $('fieldset#form_heard_in_mod div :radio[value=""]').prop('checked', true);
        }
    },

    setIdentAction : function() {
        $('#form_call').on('blur', function(){
            var params = $(this).val().split('-');
            if (params.length === 2){
                if (!isNaN(params[0]) && isNaN(params[1])) {
                    params.reverse();
                }
                if (isNaN(params[0]) && !isNaN(params[1])) {
                    $('#form_khz_1, #form_khz_2').val(params[1]);
                    $(this).val(params[0]);
                }
            }
        }).on('keypress', function(e) {
            if (e.which === 13) {
                $('#form_call').trigger('blur');
            }
        });
    },

    setIdentTip : function() {
        var frmCall = $('#form_call');
        if (frmCall.val() === '') {
            return;
        }
        var tip = $('#exact');
        tip.html(tip.html().replace('%s', "<b>'" + frmCall.val() + "'</b>"))
        tip.show();
    },

    setKhzAction: function() {
        $('#form_khz_1, #form_khz_2').on('blur', function(){
            $(this).val($(this).val().replace(',', '.'))
        })
    },

    setListenerInvertDefault : function() {
        if ($('fieldset#form_listener_invert div :radio:checked').length === 0) {
            $('fieldset#form_listener_invert div :radio[value=0]').prop('checked', true);
        }
    },

    setListenerOptionsStyle: function() {
        var listener = $('#form_listener');
        listener.children().each(function() {
            if ($(this).val() === '') {
                $(this).addClass('all');
            } else if ($(this).text().substr(0,2) === '. ') {
                $(this)
                    .text('\xa0' + $(this).text().substr(1))
                    .addClass('secondaryQth');
            } else {
                $(this).addClass('primaryQth');
            }
            if ($(this).text().substr(0,2) === 'R|') {
                $(this)
                    .text($(this).text().substr(2))
                    .addClass('remote');
            }
            if ($(this).text().slice(-2) === '|N') {
                $(this)
                    .text($(this).text().slice(0, -2))
                    .addClass('inactive')
                    .attr('title', '(Inactive)')
            }
            if ($('#form_listener_filter div :radio:checked').val() === 'N') {
                if ($(this).hasClass('remote')) {
                    $(this).hide();
                }
            }
            if ($('#form_listener_filter div :radio:checked').val() === 'Y') {
                if (!$(this).hasClass('remote')) {
                    $(this).hide();
                }
            }
        });
    },

    setListenerFilterAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('#form_listener_filter').change(function () {
                var value = $('#form_listener_filter div :radio:checked').val();
                var listener = $('#form_listener');
                listener.children().each(function() {
                    switch (value){
                        case 'N':
                            if ($(this).hasClass('remote')) {
                                $(this).hide();
                            } else {
                                $(this).show();
                            }
                            break;
                        case 'Y':
                            if (!$(this).hasClass('remote')) {
                                $(this).hide();
                            } else {
                                $(this).show();
                            }
                            break;
                        default:
                            $(this).show();
                            break;
                    }
                });
            });
        } else {
            $('#form_listener_filter').off('change');
        }
    },

    setPersonaliseAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('#form_personalise').change(function () {
                var lbl = $('#form_personalise option:selected').text();
                var gsq = (lbl.split('|').length === 3 ? lbl.split('|')[1] : '').trim();
                var form_range_gsq = $('#form_range_gsq');
                form_range_gsq.val(gsq);
                form_range_gsq.trigger('keyup');
                $('form[name="form"]').submit();
            });
        } else {
            $('#form_personalise').off('change');
        }
    },

    setRangeAction : function() {
        var form_range_gsq = $('#form_range_gsq');
        var form_range_min = $('#form_range_min');
        form_range_gsq.on('keyup', function() {
            var disabled = ($('#form_range_gsq').val().length < 6);
            $('#form_range_min').attr('disabled', disabled);
            $('#form_range_max').attr('disabled', disabled);
        });
        form_range_min.on('keyup', function() {
            var disabled = ($('#form_range_min').val().length === 0 && $('#form_range_max').val().length === 0);
            $('#form_range_units').attr('disabled', disabled);
        });
        $('#form_dx_max').on('keyup', function() {
            var disabled = ($('#form_range_min').val().length === 0 && $('#form_range_max').val().length === 0);
            $('#form_range_units').attr('disabled', disabled);
        });
        form_range_gsq.trigger('keyup');
        form_range_min.trigger('keyup');
    },

    setRangeUnitsDefault : function() {
        if ($('fieldset#form_range_units div :radio:checked').length === 0) {
            $('fieldset#form_range_units div :radio[value=km]').prop('checked', true);
        }
    },

    setRwwFocusAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('select#form_rww_focus').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_rww_focus').off('change');
        }
    },

    setResetAction : function() {
        $('button[type="reset"]').click(function () {
            if (COOKIE.get('signalsForm') && confirm(msg.cookie.reset)) {
                COOKIE.clear('signalsForm', '/');
            }
            var c = COMMON_FORM;
            var s = SIGNALS_FORM;
            var form_range_gsq = $('#form_range_gsq');
            var form_range_min = $('#form_range_min');
            s.setAdminAction(false);
            c.setRegionAction(false);
            s.setRwwFocusAction(false);
            s.setListenerFilterAction(false);
            s.setShowNotesAction(false);
            s.setShowMorseAction(false);
            s.setShowOffsetsAction(false);
            s.setPersonaliseAction(false);

            $('#form_show').val('');
            $('fieldset#form_type div :checkbox').prop('checked', false);
            $('fieldset#form_type div :checkbox[value=NDB]').prop('checked', true);
            $('fieldset#form_status div :checkbox').prop('checked', false);
            $('fieldset#form_status div :checkbox[value=1]').prop('checked', true);
            $('#form_call').val('');
            $('#form_khz_1').val('');
            $('#form_khz_2').val('');
            $('#form_channels').prop('selectedIndex', 0);
            $('#form_recently').prop('selectedIndex', 0);
            $('#form_within').prop('selectedIndex', 0);
            $('#form_personalise').prop('selectedIndex', 0);
            $('#form_morse_0').prop('checked', 1);
            $('#form_hidenotes_1').prop('checked', 1);
            $('#form_offsets_0').prop('checked', 1);
            $('#form_notes').val('');
            $('#form_states').val('');
            $('#form_sp_itu_clause').prop('selectedIndex', 0);
            $('#form_countries').val('');
            $('#form_region').prop('selectedIndex', 0);
            $('#form_rww_focus').prop('selectedIndex', 0);
            $('#form_gsq').val('');
            form_range_gsq.val('');
            form_range_min.val('');
            $('#form_range_max').val('');
            $('#form_range_units_0').prop('checked', 1);
            form_range_gsq.trigger('keyup');
            form_range_min.trigger('keyup');

            $('#form_listener').val([]);
            $('#form_listener_invert_0').prop('checked', 1);
            $('#form_listener_filter_0').prop('checked', 1);
            $('#form_heard_in').val('');
            $('#form_heard_in_mod_0').prop('checked', 1);
            $('#form_logged_date_1').val('');
            $('#form_logged_date_2').val('');
            $('#form_logged_first_1').val('');
            $('#form_logged_first_2').val('');
            $('#form_logged_last_1').val('');
            $('#form_logged_last_2').val('');

            $('#form_admin_mode').prop('selectedIndex', 0);

            s.setPersonaliseAction(true);
            s.setShowMorseAction(true);
            s.setShowNotesAction(true);
            s.setShowOffsetsAction(true);
            s.setAdminAction(true);
            c.setRegionAction(true);
            s.setListenerFilterAction(true);
            s.setRwwFocusAction(true);
            formSubmit();
            return false;
        });
    },

    setSaveAction: function() {
        $('#form_save').click(function(){
            if (confirm(msg.cookie.save + "\n" + msg.cookie.usesCookie)) {
                var value = shareableLink.signalsUrl().split('?')[1];
                COOKIE.set('signalsForm', value, '/');
                alert(msg.cookie.saved);
            }
        });
    },

    setShowMorseAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('#form_morse').change(function () {
                formSubmit();
            });
        } else {
            $('#form_morse').off('change');
        }
    },

    setShowNotesAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('#form_hidenotes').change(function () {
                formSubmit();
            });
        } else {
            $('#form_hidenotes').off('change');
        }
    },

    setShowOffsetsAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('#form_offsets').change(function () {
                formSubmit();
            });
        } else {
            $('#form_offsets').off('change');
        }
    },

    setPaperSizeAction: function() {
        $('#seeklist_paper')
            .change(function() {
                $('#form_paper').val($('#seeklist_paper option:selected').val());
                formSubmit();
            });
    },

    setSortByAction: function() {
        $('select#form_sortby').change(function() {
            var val = $("#form_sortby option:selected").val();
            $('#form_sort').val(val.split('|')[0]);
            $('#form_order').val(val.split('|')[1]);
            $('#form_za').prop('checked', val.split('|')[1] === 'd');
            formSubmit();
        });
    },

    setSortZaAction : function() {
        $('input#form_za').change(function () {
            $('#form_order').val($('input#form_za').prop('checked') ? 'd' : 'a');
            formSubmit();
        });
    },

    setStatesLabelLink : function() {
        var ele = $('label[for="form_states"]');
        ele.html('<a href="states/*" data-popup="1">' + ele.html() + '</a>');
    },

    showStats : function() {
        var rwwFocus = $('#form_rww_focus')
        url = base_url +
            'signals/stats' +
            (typeof rwwFocus.val() !== 'undefined' ? '?rww_focus=' + rwwFocus.val() : '');
        $.get(url, function(data) {
            $.each(data.signals, function(key, value){
                $('#stats_' + key).text(value.numberFormat());
            })
            $('#stats_focus').text(data.listeners.focus);
            $('#stats_locations').text(data.listeners.locations.numberFormat());
            $('#stats_logs').text(data.listeners.logs.numberFormat());
            $('#stats_first').text(data.listeners.first);
            $('#stats_last').text(data.listeners.last);
            $('#seeklist_last').text(data.listeners.last);
        });
    }
}

var SIGNALS = {
    loadList: function(args) {
        var url = shareableLink.signalsUrl('&show=list');
        console.log(url);
        console.log(args);
        $.get(url, function(data) {
            var c, cols, html, i, id, j, key, row, s, tde, tds, title, value;
            html = [];
            paging = data.results;  // Global object set in paging.html.twig
            cols = data.columns;

            SIGNALS.setHeadingTitle(data);
            SIGNALS.setHeadingPersonalise(data);
            COMMON_FORM.setPagingControls();
            COMMON_FORM.setPagingStatus(msg.paging_s, paging.total);
            $('#signalDetails').addClass('line')
            $('#paging').show();
            html.push('<tr>');
            if (data.personalise.id) {
                html.push('<th class="txt_vertical nosort rowspan2 th"><div>Logged</div></th>');
            }
            for (j = 0; j < cols.length; j++) {
                c = cols[j];
                if (c.key === 'morse' && args.morse !== 1) {
                    console.log(args)
                    continue;
                }
                if (c.key === 'notes' && args.hidenotes !== 0) {
                    console.log(args)
                    continue;
                }
                html.push(
                    '  <th' +
                    (c.key && c.order ? ' id="' + c.key + '|' + c.order + '"' : '') +
                    (c.th_class || c.sort ? ' class="' + c.th_class  + (c.sort ? ' sort' : '') + '"' : '') +
                    (c.tooltip ? ' title="' + c.tooltip + '"' : '') + '>' +
                    (c.th_class === 'txt_vertical' ? '<div>' + c.label + '<\/div>' : c.label) +
                    '</th>'
                );
            }
            html.push('<\/tr>\n');
            $('.signal.results thead').html(html.join('\n'));
            html = [];
            for (i = 0; i<data.types.length; i++) {
                $('#ref_type_' + data.types[i]).show();
            }
            if (data.signals.length === 0) {
                $( "#signals_list").html(
                    "<tr><th class='no-results' colspan='" + cols.length + "'>" + "No signals found matching your criteria" + "</th></tr>"
                );
                return;
            }
            for (i = 0; i<data.signals.length; i++) {
                s = data.signals[i];
                row = '<tr class="' +
                    (parseInt(s.decommissioned) ? 'decommissioned ' : '') +
                    (!parseInt(s.active) ? 'inactive ' : '') +
                    args.types[s.type].classname +
                    (data.personalise.id ? (parseInt(s.personalise) ? '' : 'un') + 'logged' : '') + '"' +
                    ' title="' +
                    args.types[s.type].title +
                    (!parseInt(s.active) && !parseInt(s.decommissioned) ? ' (' + msg.inactive + ')' : '' ) +
                    (parseInt(s.decommissioned)  ? ' (' + msg.decommissioned + ')' : '' ) +
                    '">' +
                    (data.personalise.id ?
                            '<th title="' + (!parseInt(s.personalise) ? msg.unlogged_by : msg.logged_by) + '" class="rowspan2">' +
                            (parseInt(s.personalise) ? '&#x2714;' : '&nbsp;') +
                            '</th>'
                            :
                            ''
                    );
                for (j = 0; j < cols.length; j++) {
                    c = cols[j];

                    value = s[c.key];
                    id = s['ID'];
                    tds = '<td' + (c.td_class ? ' class="' + c.td_class + '"' : '') + '>';
                    tde = '</td>';
                    switch (c.key) {
                        case 'call':
                            row += tds + '<a href="' + args.urls.profile.replace('*', id) + '" data-popup="1">' + value + '</a>' + tde;
                            break;
                        case 'delete':
                            row += tds + '<a href="' + args.urls.delete.replace('*', id) + '" class="delete" onclick="return confirm(msg.del_signal)">X</a>' + tde;
                            break;
                        case 'GSQ':
                            row += tds + (value !=='' ? '<a data-gsq="' + id + '">' + value + '</a>' : '') + tde;
                            break;
                        case 'first_heard':
                        case 'last_heard':
                            row += tds + (value !== null ? value : '') + tde;
                            break;
                        case 'heard_in':
                            row += tds + s['heard_in_html'] + tde;
                            break;
                        case 'ITU':
                            row += tds + (value !=='' ? '<a data-set="itu">' + value + '</a>' : '') + tde;
                            break;
                        case 'khz':
                            row += tds + parseFloat(value) + tde;
                            break;
                        case 'listeners':
                            row += tds + (value !=='0' ? '<a href="' + args.urls.listeners.replace('*', id) + '" data-popup="1">' : '') + value + '</a>' + tde;
                            break;
                        case 'logs':
                            row += tds + (value !=='0' ? '<a href="' + args.urls.logs.replace('*', id) + '" data-popup="1">' : '') + value + '</a>' + tde;
                            break;
                        case 'LSB':
                        case 'USB':
                            row += tds + (parseFloat(value) ? (data.args.offsets ? parseFloat(value).toFixed(3) : value) : '') + tde;
                            break;
                        case 'merge':
                            row += tds + '<a href="' + args.urls.merge.replace('*', id) + '" class="merge">M</a>' + tde;
                            break;
                        case 'morse':
                            if (args.morse === 1) {
                                row += tds + (s.morse !=='' ? encodeMorse(s.morse) : '?') + tde;
                            }
                            break;
                        case 'notes':
                            if (args.hidenotes !== 1) {
                                row += tds + s.notes + tde;
                            }
                            break;
                        case 'pwr':
                            row += tds + (value !== '0'  ? value : '') + tde;
                            break;
                        case 'SP':
                            row += tds + (value !=='' ? '<a data-set="sp">' + value + '</a>' : '') + tde;
                            break;
                        case 'type':
                            break;
                        default:
                            row += tds + (!!value ? value : '') + tde;
                            break;
                    }
                }
                row += "</tr>";
                html.push(row);
            }
            $('#signals_list').html( html.join('\n'));
            setColumnSortActions();
            setColumnSortedClass();
            setExternalLinks();
            scrollToResults()
            RT.init($('#wide'), $('#narrow'));
        });
    },
    loadMap: function() {
        $('#signalDetails').addClass('line')
        $('#paging').show();
    },
    loadSeeklist: function() {
        $('#signalDetails').removeClass('line')
        $('#paging').hide();
    },
    setHeadingPersonalise: function(data) {
        if (!data.personalise.name) {
            return;
        }
        $('#signals_personalise').html(
            msg.signals.personalise.replace(
                '%s',
                '<a href="' + args.urls.listeners.replace('*', data.personalise.id) + '" data-popup="1">' + data.personalise.desc + "<\/a>"
            )
        );
    },
    setHeadingTitle: function(data) {
        switch(data.title) {
            case 1:
                title = msg.signals.title.unlogged;
                break;
            case 2:
                title = msg.signals.title.both;
                break;
            default:
                title = msg.signals.title.normal.replace('%s', system.toUpperCase());
                break;
        }
        $('#signals_title').html(title);
    }
}

var SIGNAL_MERGE = {
    init: function() {
        $('#form_save').on('click', function(){
            $('#form_reload').val(1);
        })
        $('#form_saveClose').on('click', function(){
            $('#form_reload').val(1);
            $('#form__close').val(1);
        })
        SIGNAL_MERGE.initSignalsSelector(signals);
    },

    initSignalsSelector: function(data) {
        var element, i, out, r, s;
        element = $('#form_signalId');
        s  = element.val();
        out = '<select id="form_signalId" name="form[signalId]" required="required" size="10">\n';
        for (i in data) {
            r = data[i].split('|');
            out +=
                "<option value='" + r[0] + "'" +
                (!parseInt(r[5]) ? " title='" + msg.inactive + "'" : '') +
                " class='type_" +r[3] + (!parseInt(r[5]) ? ' inactive' : '') + "'" +
                " data-gsq='" + r[4] + "'" +
                (r[0] === s ? " selected='selected'" : '') +
                ">" +
                pad(parseFloat(r[2]), 10, '&nbsp;') +
                pad(r[1], 10, '&nbsp;') +
                pad(r[6], 41, '&nbsp;') +
                pad(r[7], 3, '&nbsp;') +
                r[8] + ' ' +
                "</option>";
        }
        out += "</select>";
        element.replaceWith(out);
        $('#form_' + 'signalId')
            .on('change', function(){
                LOG_EDIT.getDx();
            })
    },
}
