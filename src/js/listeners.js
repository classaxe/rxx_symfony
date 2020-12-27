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

var logSessions = {
    baseUrl : '',
    init: function (baseUrl, matched) {
        logSessions.baseUrl = baseUrl;
        $(document).ready(function () {
            setExternalLinks();
            setFormPagingActions();
            setColumnSortActions();
            setColumnSortedClass();
            setClippedCellTitles();
            $('#form_paging_status').html(matched);
            $('#list').height($(window).height() - 90);
            $(window).resize(function () {
                $('#list').height($(window).height() - 90);
            });
            $('.logsessions tbody tr').on('click', function () {
                var id = $(this).closest('tr').attr('id').split('_')[2];
                logSessions.getLogSessionLogs(id)
            });
            $('.logsessions tbody').children('tr:first').trigger('click');
        });
    },
    getLogSessionLogs: function (id) {
        $('.logsessions tbody tr').removeClass('selected');
        $('#list2').html("<div class='logsession_loader'><h2>" + msg.loading + "</h2></div>");
        $('.logsessions tbody tr#log_session_' + id).addClass('selected');
        var url = logSessions.baseUrl.replace('XXX', id);
        $('#list2').load(url);
        return false;
    }
};

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
