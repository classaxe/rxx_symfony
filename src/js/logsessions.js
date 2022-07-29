var logSessions = {
    baseUrl : '',
    init: function (baseUrl, matched, offset) {
        logSessions.baseUrl = baseUrl;
        $(document).ready(function () {
            setExternalLinks();
            COMMON_FORM.setPagingControls();
            setColumnSortActions();
            setColumnSortedClass();
            setClippedCellTitles();
            $('#form_paging_status').html(matched);
            $(window).on('resize', function () {
                var footerOffset = (COOKIE.get('credits_hide') !== 'yes' ? 74 : 0);
                $('#list').height(($(window).height() / 2) - offset - footerOffset);
                $('#list2').height(($(window).height() / 2) - offset - footerOffset);
            });
            $(window).trigger('resize');
            $('.logsessions tbody tr').on('click', function () {
                var listenerId = $(this).closest('tr').attr('id').split('_')[2];
                var logSessionId = $(this).closest('tr').attr('id').split('_')[3];
                logSessions.getLogSessionLogs(listenerId, logSessionId)
            });
            $('.logsessions tbody').children('tr:first').trigger('click');
        });
    },
    initFS: function (baseUrl, matched, offset) {
        logSessions.baseUrl = baseUrl;
        $(document).ready(function () {
            setExternalLinks();
            COMMON_FORM.setPagingControls();
            setColumnSortActions();
            setColumnSortedClass();
            setClippedCellTitles();
            $('#form_paging_status').html(matched);
            $(window).on('resize', function () {
                var footerHeight =   (COOKIE.get('credits_hide') !== 'yes' ? 74 : 0);
                var sessionsHeight = (COOKIE.get('logsessionlogs_hide') !== 'yes' ? ($(window).height() / 2) : $(window).height() - 115);
                var logsHeight =     (COOKIE.get('logsessionlogs_hide') !== 'yes' ? ($(window).height() / 2) : 30);
                $('#list').height(sessionsHeight - offset - footerHeight);
                $('#list2').height(logsHeight - offset - footerHeight);
            });
            $(window).trigger('resize');
            $('.logsessions tbody tr').on('click', function () {
                var listenerId = $(this).closest('tr').attr('id').split('_')[2];
                var logSessionId = $(this).closest('tr').attr('id').split('_')[3];
                logSessions.getLogSessionLogs(listenerId, logSessionId)
            });
            $('.logsessions tbody').children('tr:first').trigger('click');
            COMMON_FORM.setLogSessionLogsActions();
        });
    },
    getLogSessionLogs: function (listenerId, logSessionId) {
        $('.logsessions tbody tr').removeClass('selected');
        $('#list2').html("<div class='logsession_loader'><h2>" + msg.loading + "</h2></div>");
        $('.logsessions tbody tr#log_session_' + listenerId + '_' + logSessionId).addClass('selected');
        var url = logSessions.baseUrl.replace('XXX', listenerId).replace('YYY', logSessionId);
        $('#list2').load(url);
        return false;
    }
};