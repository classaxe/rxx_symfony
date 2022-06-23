var LISTENERS_FORM = {
    init : function(resultsCount) {
        $(document).ready(function () {
            var c = COMMON_FORM;
            var l = LISTENERS_FORM;
            c.setPagingControls();
            c.setTypesStyles();
            c.setTypesDefault();
            $('#form_timezone').selectmenu();
            c.setTypesAllAction();
            c.setCountryAction();
            c.setRegionAction();
            l.setHasLogsAction();
            l.setHasMapPosAction();
            l.setTimezoneAction();
            l.setActiveAction();
            l.setSearchforAction();
            l.setSaveAction();
            l.setResetAction();
            l.setFocusOnSearch();
            l.setActions();

            setColumnSortActions();
            setColumnSortedClass();
            setExternalLinks();
            c.setPagingStatus(msg.paging_l, resultsCount);
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

    setActiveAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('select#form_active').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_active').off('change');
        }
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
            if (!confirm(msg.reset + "\n" + msg.cookie.reset)) {
                return false;
            }
            COOKIE.clear('listenersForm', '/');
            var c = COMMON_FORM;
            var l = LISTENERS_FORM;
            $('fieldset#form_type div :checkbox').prop('checked', false);
            $('fieldset#form_type div :checkbox[value=NDB]').prop('checked', true);
            $('#form_q').val('');
            c.setCountryAction(false);
            c.setRegionAction(false);
            l.setHasLogsAction(false);
            l.setHasMapPosAction(false);
            l.setTimezoneAction(false);
            l.setActiveAction(false);
            $('#form_equipment').val('');
            $('#form_active').removeClass('inactive')
            $('select#form_region').prop('selectedIndex', 0);
            $('select#form_country').prop('selectedIndex', 0);
            $('select#form_has_map_pos').prop('selectedIndex', 0);
            $('select#form_timezone').val('ALL').selectmenu('refresh');
            $('select#form_active').prop('selectedIndex', 0);
            c.setCountryAction(true);
            c.setRegionAction(true);
            l.setHasLogsAction(true);
            l.setHasMapPosAction(true);
            l.setTimezoneAction(true);
            l.setActiveAction(true);
            formSubmit();
            return false;
        })
    },

    setSaveAction: function() {
        $('#form_save').click(function(){
            if (confirm(msg.cookie.save + "\n" + msg.cookie.usesCookie)) {
                var value = shareableLink.listenersUrl(false).split('?')[1];
                COOKIE.set('listenersForm', value, '/');
                alert(msg.cookie.saved);
            }
        });
    },
    setSearchforAction : function(enable) {
        var form_q = $('#form_q');
        $('#form_active').addClass(!! form_q.val() ? 'inactive' : '');
        form_q.on('keyup', function () {
            if (!! form_q.val()) {
                $('#form_active').addClass('inactive');
            } else {
                $('#form_active').removeClass('inactive');
            }
        });
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