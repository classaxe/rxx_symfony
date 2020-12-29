var signalsForm = {
    init : function(pagingMsg, resultsCount, forAjax) {
        $(document).ready( function() {
            var c = commonForm;
            var s = signalsForm;
            setFormPagingActions();
            s.setPersonaliseAction();
            s.setOffsetsAction();
            s.setRangeAction();
            s.setRangeUnitsDefault();
            s.setSortByAction();
            s.setSortZaAction();
            s.setShowModeAction();

            s.setRwwFocusAction();
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
            s.setResetAction();

            setFormDatePickers();
            setColumnSortActions();
            setColumnSortedClass();
            setFormPagingStatus(pagingMsg, resultsCount);

            s.setActions();
            s.setFocusOnCall();
            s.showStats();

            if (forAjax) {
                return;
            }

            setExternalLinks();
            scrollToResults();
            RT.init($('#wide'), $('#narrow'));

        });
    },
    setActions : function() {
        $('#btn_csv_all').click(function () {
            if (confirm(
                msg.export
                    .replace(':system', system.toUpperCase())
                    .replace(':format', '.csv') +
                "\n" + msg.export2
            )) {
                window.location.assign(window.location + '/export/csv' + shareableLink.getFromTypes());
            }
        });
        $('#btn_kml_all').click(function () {
            if (confirm(
                msg.export
                    .replace(':system', system.toUpperCase())
                    .replace(':format', '.kml') +
                "\n" + msg.export2
            )) {
                window.location.assign(window.location + '/export/kml' + shareableLink.getFromTypes());
            }
        });
        $('#btn_txt_all').click(function () {
            shareableLink.getFromTypes()
            if (confirm(
                msg.export
                    .replace(':system', system.toUpperCase())
                    .replace(':format', '.txt') +
                "\n" + msg.export2
            )) {
                window.location.assign(window.location + '/export/txt' + shareableLink.getFromTypes());
            }
        });
        $('#btn_xls_all').click(function () {
            if (confirm(
                msg.export
                    .replace(':system', system.toUpperCase())
                    .replace(':format', 'PSKOV') +
                "\n" + msg.export2
            )) {
                window.location.assign(window.location + '/export/xls' + shareableLink.getFromTypes());
            }
        });
        $('#btn_csv_fil').click(function () {
            var form_show = $('#form_show');
            var show = form_show.val();
            form_show.val('csv');
            $('#form_submit').click();
            form_show.val(show);
        });
        $('#btn_kml_fil').click(function () {
            var form_show = $('#form_show');
            var show = form_show.val();
            form_show.val('kml');
            $('#form_submit').click();
            form_show.val(show);
        });
        $('#btn_txt_fil').click(function () {
            var form_show = $('#form_show');
            var show = form_show.val();
            form_show.val('txt');
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

    setHeardInModDefault : function() {
        if ($('fieldset#form_heard_in_mod div :radio:checked').length === 0) {
            $('fieldset#form_heard_in_mod div :radio[value="any"]').prop('checked', true);
        }
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
                $(this).text('\xa0' + $(this).text().substr(1));
                $(this).addClass('secondaryQth');
            } else {
                $(this).addClass('primaryQth');
            }
        });
    },

    setOffsetsAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('#form_offsets').change(function () {
                formSubmit();
            });
        } else {
            $('#form_offsets').off('change');
        }
    },

    setPersonaliseAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('#form_personalise').change(function () {
                var lbl = $('#form_personalise option:selected').text();
                var gsq = (lbl.split('|').length === 2 ? lbl.split('|')[1] : '').trim();
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
            var c = commonForm;
            var s = signalsForm;
            var form_range_gsq = $('#form_range_gsq');
            var form_range_min = $('#form_range_min');
            s.setAdminAction(false);
            c.setRegionAction(false);
            s.setRwwFocusAction(false);
            s.setOffsetsAction(false);
            s.setPersonaliseAction(false);

            $('#form_show').val('');
            $('fieldset#form_type div :checkbox').prop('checked', false);
            $('fieldset#form_type div :checkbox[value=NDB]').prop('checked', true);
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
            form_range_gsq.val('');
            form_range_min.val('');
            $('#form_range_max').val('');
            $('#form_range_units_0').prop('checked', 1);
            form_range_gsq.trigger('keyup');
            form_range_min.trigger('keyup');

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

            s.setPersonaliseAction(true);
            s.setOffsetsAction(true);
            s.setAdminAction(true);
            c.setRegionAction(true);
            s.setRwwFocusAction(true);
            formSubmit();
            return false;
        });
    },
    setShowModeAction: function() {
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
        url = base_url + 'signals/stats?rww_focus=' + $('#form_rww_focus').val();
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

