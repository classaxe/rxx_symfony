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

function initSignalsSelector(data) {
    var element, i, out = '', r, s;
    element = $('#form_signalId');
    s  = element.val();
    out = "<select id=\"form_signalId\" name=\"form[signalId]\" required=\"required\" size=\"6\">\n";
    for (i in data) {
        r = data[i].split('|');
        out +=
            "<option value='" + r[0] + "'" +
            (r[5] === '0' ? " title='" + msg.tools : '') + "'" +
            " class='type_" +r[3] + (r[5] === '0' ? ' inactive' : '') + "'" +
            (r[0] === s ? " selected='selected'" : '') +
            " data-gsq='" + r[4] + "'" +
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
}

function initListenersSelector(data) {
    var element, i, out = '', r, s;
    element = $('#form_listenerId');
    s  = element.val();
    out = "<select id=\"form_listenerId\" name=\"form[listenerId]\" required=\"required\" size=\"6\">\n";
    for (i in data) {
        r = data[i].split('|');
        out +=
            "<option value='" + r[0] + "'" +
            (r[0] === s ? " selected='selected'" : '') +
            " data-gsq='" + r[3] + "'" +
            " class='" + (r[4] === '1' ? 'primaryQth' : 'secondaryQth') + "'" +
            ">" +
            pad(r[1] + ", " + r[5] + (r[2] ? ' ' + r[2] : ''), (r[4] === '1' ? 60 : 58), '&nbsp;') +
            (r[6] ? ' ' + r[6] : '&nbsp; &nbsp;') +
            ' ' + r[7] +
            "</option>";
    }
    out += "</select>";
    element.replaceWith(out);
}

