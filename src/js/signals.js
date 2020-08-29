function initSignalsForm(pagingMsg, resultsCount, forAjax) {
    $(document).ready( function() {
        setFormPagingActions();

        setFormPersonaliseAction();
        setFormOffsetsAction();
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
        setFormCollapseSections();

        setFormResetAction('signals');
        setColumnSortActions();
        setColumnSortedClass();

        setFormPagingStatus(pagingMsg, resultsCount);
        setSignalActions();

        setFocusOnCall();

        if (forAjax) {
            return;
        }

        setExternalLinks();
        scrollToResults();
        RT.init($('#wide'), $('#narrow'));

    });
}

function setSignalActions() {
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
}
