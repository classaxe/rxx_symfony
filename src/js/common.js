var COMMON_FORM = {
    setCountryAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('select#form_country').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_country').off('change');
        }
    },

    setDatePickerActions: function() {
        $.datepicker.setDefaults({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '1970:+0'
        });
        $('.js-datepicker').datepicker({ });
    },

    setPagingStatus: function(string, value) {
        $('#form_paging_status').html(
            string.replace('%s', value.toLocaleString())
        );
    },

    setCreditsHideShowActions: function() {
        var credits = $('#section_credits');
        var hide = $('#section_credits_hide');
        var show = $('#section_credits_show');
        show.on('click', function(){
            COOKIE.set('credits_hide', 'no');
            $('#section_credits').show();
            $('#section_credits_hide').show();
            $(this).hide();
            $(window).trigger('resize');
        });
        hide.on('click', function(){
            COOKIE.set('credits_hide', 'yes');
            $('#section_credits').hide();
            $('#section_credits_show').show();
            $(this).hide();
            $(window).trigger('resize');
        });
        if (COOKIE.get('credits_hide') === 'yes') {
            credits.hide();
            hide.hide();
            show.show();
        } else {
            credits.show();
            hide.show();
            show.hide();
        }
    },

    setPagingControls: function() {
        var filter =    $('#form_filter');
        var prev =      $('#form_prev');
        var next =      $('#form_next');
        var limit =     $('#form_limit');
        var page =      $('#form_page');
        if (limit.length) {
            limit[0].outerHTML =
                "<select id=\"form_limit\" name=\"form[limit]\" required=\"required\">" +
                getLimitOptions(paging.total, limit.val(), paging.limit) +
                "</select>";
            limit =     $('#form_limit');
        }

        if (page.length) {
            page[0].outerHTML =
                '<label class="sr-only" for="form_page">Page Control</label>\n' +
                '<select id="form_page" name="form[page]" style="display:none">' +
                getPagingOptions(paging.total, limit.val(), paging.page) +
                '</select>';
            page =  $('#form_page');
        }

        var options =   $('#form_page option');

        if (limit.val() !== '-1') {
            prev.show();
            next.show();
            page.show();
        }

        limit.change(
            function() {
                var form =      $('form[name="form"]');
                var limit =     $('#form_limit');
                var options =   $('#form_page option');
                var prev =      $('#form_prev');
                var next =      $('#form_next');
                options.eq(0).prop('selected', true);
                page.prop('selectedIndex', 0);
                if (limit.val() !== "-1") {
                    prev.show();
                    next.show();
                    page.show();
                    options.eq(0).prop('text', '1-' + limit.val());
                    prev.prop('disabled', 'disabled');
                    next.prop('disabled', 'disabled');
                } else {
                    page.hide();
                    prev.hide();
                    next.hide();
                }
                page.prop('selectedIndex', 0);
                form.submit();
            }
        );
        if (paging.page > 0) {
            prev.prop('disabled', false);
            prev.click(
                function () {
                    var form =      $('form[name="form"]');
                    var page =      $('#form_page');
                    var options =   $('#form_page option');
                    var prev =      $('#form_prev');
                    var next =      $('#form_next');
                    prev.prop('disabled', 'disabled');
                    next.prop('disabled', 'disabled');
                    options.eq(paging.page - 1).prop('selected', true);
                    page.prop('selectedIndex', paging.page - 1);
                    form.submit();
                    return false;
                }
            );
        } else {
            prev.prop('disabled', 'disabled');
        }

        if (paging.page + 1 < options.length) {
            next.prop('disabled', false);
            next.click(
                function() {
                    var form =      $('form[name="form"]');
                    var page =      $('#form_page');
                    var options =   $('#form_page option');
                    var prev =      $('#form_prev');
                    var next =      $('#form_next');
                    prev.prop('disabled', 'disabled');
                    next.prop('disabled', 'disabled');
                    options.eq(paging.page + 1).prop('selected', true);
                    page.prop('selectedIndex', paging.page + 1);
                    form.submit();
                    return false;
                }
            );
        } else {
            next.prop('disabled', 'disabled');
        }

        page.change(
            function() {
                var form =      $('form[name="form"]');
                var prev =      $('#form_prev');
                var next =      $('#form_next');
                prev.prop('disabled', 'disabled');
                next.prop('disabled', 'disabled');
                form.submit();
            }
        );

        filter.change(
            function() {
                var form =      $('form[name="form"]');
                var page =      $('#form_page');
                var options =   $('#form_page option');
                var prev =      $('#form_prev');
                var next =      $('#form_next');
                options.eq(0).prop('selected', true);
                page.prop('selectedIndex', 0);
                prev.prop('disabled', 'disabled');
                next.prop('disabled', 'disabled');
                form.submit();
            }
        );
    },

    setRegionAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('select#form_region').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_region').off('change');
        }
    },

    /* [ Enable 'All' checkbox to select / unselect all signal types ] */
    setTypesAllAction : function () {
        $('fieldset#form_type div :checkbox[value=ALL]').click(function () {
            $('fieldset#form_type div :checkbox').prop('checked', $(this).prop("checked"));
        });
    },

    /* [ Ensure that at least one option is checked for signal type checkboxes ] */
    setTypesDefault : function() {
        if ($('fieldset#form_type div :checkbox:checked').length === 0) {
            $('fieldset#form_type div :checkbox[value=NDB]').prop('checked', true);
        }
    },

    /* [ Set css styles for signal type checkboxes ] */
    setTypesStyles : function() {
        $("fieldset#form_type div input").each(function() {
            $(this).parent().attr('class', 'type_' + $(this).attr('class'));
        });
    },

}