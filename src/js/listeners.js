function initListenersForm(pagingMsg, resultsCount) {
    $(document).ready(function () {
        setFormPagingActions();
        setFormTypesStyles();
        setFormTypesDefault();
        $('#form_timezone').selectmenu();
        setFormTypesAllAction();
        setFormCountryAction();
        setFormRegionAction();
        setFormHasLogsAction();
        setFormHasLogsAction();
        setFormHasMapPosAction();
        setFormTimezoneAction();
        setFormResetAction('listeners');
        setColumnSortActions();
        setColumnSortedClass();
        setFocusOnSearch();
        setExternalLinks();
        setFormPagingStatus(pagingMsg, resultsCount);
        setListenersActions();
        scrollToResults();
        RT.init($('#wide'), $('#narrow'));
    })
}

function setFocusOnSearch() {
    var f = $('#form_q');
    f.focus();
    f.select();
}
function setListenersActions() {
    $('#btn_prt').click(function () {
        window.print();
        return false;
    });
    $('#btn_share').click(function() {
        shareListeners();
        return false;
    });
    $('#btn_new').click(function() {
        window.open('./listeners/new', 'listener_new', popWinSpecs['listeners_[id]']);
        return false;
    });
}
