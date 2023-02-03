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
            l.setMultiopAction();
            l.setLoctypeAction();
            l.setStatusAction();
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
        $('#btn_csv_fil').click(function () {
            var filename = prompt('Filename', system + '_listeners.csv');
            if (filename === null) {
                return;
            }
            var form_show = $('#form_show');
            var form_filename = $('#form_filename');
            var show = form_show.val();
            form_show.val('csv');
            form_filename.val(filename);
            $('#form_submit').click();
            form_show.val(show);
        });
        $('#btn_txt_fil').click(function () {
            var filename = prompt('Filename', system + '_listeners.txt');
            if (filename === null) {
                return;
            }
            var form_show = $('#form_show');
            var form_filename = $('#form_filename');
            var show = form_show.val();
            form_show.val('txt');
            form_filename.val(filename);
            $('#form_submit').click();
            form_show.val(show);
        });
        $('#btn_kml_fil').click(function () {
            var filename = prompt('Filename', system + '_listeners.kml');
            if (filename === null) {
                return;
            }
            var form_show = $('#form_show');
            var form_filename = $('#form_filename');
            var show = form_show.val();
            form_show.val('kml');
            form_filename.val(filename);
            $('#form_submit').click();
            form_show.val(show);
        });
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
        $('#frm_rxxid').on('submit', function(e){
            e.preventDefault();
            $('#btn_rxxid_go').trigger('click');
            return false;
        })
        $('#btn_rxxid_go').click(function() {
            window.open('./listeners/' + $('#rxxid').val() + '/upload', '_blank', popWinSpecs['listeners_[id]_upload']);
            $('#rxxid').val('');
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
            $('input[type=radio][name=\'form[has_logs]\']').change(function () {
                formSubmit();
            });
        } else {
            $('input[type=radio][name=\'form[has_logs]\']').off('change');
        }
    },

    setHasMapPosAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('input[type=radio][name=\'form[has_map_pos]\']').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_has_map_pos').off('change');
        }
    },

    setLoctypeAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('input[type=radio][name=\'form[loctype]\']').change(function () {
                formSubmit();
            });
        } else {
            $('input[type=radio][name=\'form[loctype]\']').off('change');
        }
    },

    setMultiopAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('input[type=radio][name=\'form[multiop]\']').change(function () {
                formSubmit();
            });
        } else {
            $('input[type=radio][name=\'form[multiop]\']').off('change');
        }
    },

    setResetAction : function() {
        $('button[type="reset"]').click(function () {
            if (COOKIE.get('listenersForm') && confirm(msg.cookie.reset)) {
                COOKIE.clear('listenersForm', '/');
            }
            var c = COMMON_FORM;
            var l = LISTENERS_FORM;
            $('fieldset#form_type div :checkbox').prop('checked', false);
            $('fieldset#form_type div :checkbox[value=NDB]').prop('checked', true);
            $('#form_q').val('');
            $('#form_rxx_id').val('');
            c.setCountryAction(false);
            c.setRegionAction(false);
            l.setHasLogsAction(false);
            l.setHasMapPosAction(false);
            l.setTimezoneAction(false);
            l.setStatusAction(false);
            l.setMultiopAction(false);
            l.setLoctypeAction(false);
            $('#form_equipment').val('');
            $('#form_notes').val('');
            $('#form_active').removeClass('inactive')
            $('select#form_region').prop('selectedIndex', 0);
            $('select#form_country').prop('selectedIndex', 0);
            $('input[type=radio][name=\'form[has_map_pos]\'][value=\'\']').prop('checked', true);
            $('input[type=radio][name=\'form[has_logs]\'][value=\'\']').prop('checked', true);
            $('select#form_timezone').val('ALL').selectmenu('refresh');
            $('select#form_status').prop('selectedIndex', 0);
            $('input[type=radio][name=\'form[multiop]\'][value=\'\']').prop('checked', true);
            $('input[type=radio][name=\'form[loctype]\'][value=\'\']').prop('checked', true);
            c.setCountryAction(true);
            c.setRegionAction(true);
            l.setHasLogsAction(true);
            l.setHasMapPosAction(true);
            l.setTimezoneAction(true);
            l.setStatusAction(true);
            l.setMultiopAction(true);
            l.setLoctypeAction(true);
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

    setStatusAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('select#form_status').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_status').off('change');
        }
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