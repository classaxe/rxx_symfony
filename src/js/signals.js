function initSignalsForm(pagingMsg, resultsCount) {
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
        setExternalLinks();

        setFormPagingStatus(pagingMsg, resultsCount);
        setSignalActions();
        scrollToResults();

        setFocusOnCall();

        RT.init($('#wide'), $('#narrow'));
    });
}

function setSignalActions() {
    $('#btn_csv_all').click(function () {
        window.location.assign(window.location + '/export/csv');
    });
    $('#btn_txt_all').click(function () {
        window.location.assign(window.location + '/export/txt');
    });
    $('#btn_csv_fil').click(function () {
        var form_show = $('#form_show');
        var show = form_show.val();
        form_show.val('csv');
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
