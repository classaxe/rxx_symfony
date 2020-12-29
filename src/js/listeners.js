var listenersForm = {
    init : function(pagingMsg, resultsCount) {
        $(document).ready(function () {
            var c = commonForm;
            var l = listenersForm;
            setFormPagingActions();
            c.setTypesStyles();
            c.setTypesDefault();
            $('#form_timezone').selectmenu();
            c.setTypesAllAction();
            c.setCountryAction();
            c.setRegionAction();
            l.setHasLogsAction();
            l.setHasMapPosAction();
            l.setTimezoneAction();
            l.setResetAction();
            l.setFocusOnSearch();
            l.setActions();

            setColumnSortActions();
            setColumnSortedClass();
            setExternalLinks();
            setFormPagingStatus(pagingMsg, resultsCount);
            scrollToResults();
            RT.init($('#wide'), $('#narrow'));
        });
    },

    setActions : function() {
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
    },

    setFocusOnSearch : function() {
        var f = $('#form_q');
        f.focus();
        f.select();
    },

    setHasLogsAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('select#form_has_logs').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_has_logs').off('change');
        }
    },

    setHasMapPosAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('select#form_has_map_pos').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_has_map_pos').off('change');
        }
    },

    setResetAction : function() {
        $('button[type="reset"]').click(function () {
            var c = commonForm;
            var l = listenersForm;
            $('fieldset#form_type div :checkbox').prop('checked', false);
            $('fieldset#form_type div :checkbox[value=NDB]').prop('checked', true);
            $('#form_q').val('');
            c.setCountryAction(false);
            c.setRegionAction(false);
            l.setHasLogsAction(false);
            l.setHasMapPosAction(false);
            l.setTimezoneAction(false);
            $('select#form_region').prop('selectedIndex', 0);
            $('select#form_country').prop('selectedIndex', 0);
            $('select#form_has_map_pos').prop('selectedIndex', 0);
            $('select#form_timezone').val('').selectmenu('refresh');
            c.setCountryAction(true);
            c.setRegionAction(true);
            l.setHasLogsAction(true);
            l.setHasMapPosAction(true);
            l.setTimezoneAction(true);
            formSubmit();
            return false;
        })
    },

    setTimezoneAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('#form_timezone').on('selectmenuchange', function () {
                formSubmit();
            });
        } else {
            $('#form_timezone').off('selectmenuchange');
        }
    }
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