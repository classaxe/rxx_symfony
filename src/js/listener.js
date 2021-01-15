function setListenerActions() {
    $(document).ready(function () {
        $('#form_timezone').selectmenu();
        $('#coords_link').on('click', function(){
            popup(base_url +"tools/coordinates?args=" + $('#form_gsq').val());
        });
        $('#form_save').on('click', function() {
            checkItuSp();
        });
        $('#form_saveClose').on('click', function(){
            checkItuSp();
            $('#form__close').val(1);
        });
        $('#form_itu').on('change', function() {
            checkItuSp();
        });
        $('#form_sp').on('change', function() {
            checkItuSp();
        });
        function checkItuSp() {
            if ($.inArray($('#form_itu').val(), ['AUS', 'CAN', 'USA']) !== -1 && $('#form_sp').val() === '') {
                $('#form_sp')[0].setCustomValidity('State / Prov is required for Australia, Canada and USA');
                return false;
            } else {
                $('#form_sp')[0].setCustomValidity('');
                return true;
            }
        }
    });
}

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
            $('#list').height(($(window).height() / 2) - offset);
            $('#list2').height(($(window).height() / 2) - offset);
            $(window).resize(function () {
                $('#list').height(($(window).height() / 2) - offset);
                $('#list2').height(($(window).height() / 2) - offset);
            });
            $('.logsessions tbody tr').on('click', function () {
                var listenerId = $(this).closest('tr').attr('id').split('_')[2];
                var logSessionId = $(this).closest('tr').attr('id').split('_')[3];
                logSessions.getLogSessionLogs(listenerId, logSessionId)
            });
            $('.logsessions tbody').children('tr:first').trigger('click');
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