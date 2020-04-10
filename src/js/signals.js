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
    });
}

function setSignalActions() {
    $('#btn_csv_all').click(function () {
        window.location.assign(window.location + '/export/csv');
    });
    $('#btn_csv_fil').click(function () {
        var form_show = $('#form_show');
        var show = form_show.val();
        form_show.val('csv');
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
    })
}
