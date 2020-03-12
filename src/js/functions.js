function changeShowMode(mode) {
    $('#form_show').val(mode);
    formSubmit();
}

function decodeHtmlEntities(value) {
    return $("<div/>").html(value).text();
}

function exportSignallistExcel() {
    if (!confirm(
            (msg.export + "\n\n" + msg.time + "\n\n" + msg.options + "\n\n" + msg.continue)
            . replace('\:system', system.toUpperCase())
            . replace('\:format', msg.excel)
        )
    ) {
        return;
    }
    alert('OK');
}

function exportSignallistILG() {
    if (!confirm(
        (msg.export + "\n\n" + msg.nooptions + "\n\n" + msg.continue)
            . replace('\:system', system.toUpperCase())
            . replace('\:format', msg.ilg)
    )
    ) {
        return;
    }
    alert('OK');
}
function exportSignallistPDF() {
    if (!confirm(
        (msg.export + "\n\n" + msg.time + "\n\n" + msg.options + "\n\n" + msg.continue)
            . replace('\:system', system.toUpperCase())
            . replace('\:format', msg.pdf)
    )
    ) {
        return;
    }
    alert('OK');
}

function getLimitOptions(max, value, defaultLimit) {
    var values = [10, 20, 50, 100, 200, 500, 1000, 2000, 5000, 100000, 20000, 50000, 100000];
    var out = "";
    for (var i in values) {
        if (values[i] > max && values[i] > defaultLimit) {
            continue;
        }
        out +=
            "<option value=\"" + values[i] + "\"" +
            (parseInt(value) === values[i] ? " selected=\"selected\"" : "") +
            ">" +
            values[i] + ' results' +
            "</option>";
    }
    out +=
        "<option value=\"" + (max > values[0] ? -1 : defaultLimit) + "\"" +
        (parseInt(value) === -1 ? " selected=\"selected\"" : "") +
        ">All results</option>";
    return out;
}

function getMetar(decoded) {
    window.open('https://www.aviationweather.gov/metar/data' +
        '?ids='+$('#form_icao').val() +
        '&format=' +(decoded ? 'decoded' : 'raw') +
        '&taf=on' +
        '&layout=off' +
        '&hours='+$('#form_hours').val(),
        'popMETAR'+decoded,
        'scrollbars=1,resizable=1,location=1'
    );
}

function getPagingOptions(total, limit, page) {
    var out = "";
    pages = total/limit;
    for (var i=0; i < pages; i++) {
        out +=
            "<option value=\"" + i + "\"" +
            (parseInt(page) === i ? " selected=\"selected\"" : "") +
            ">" +
            (1 + (i*limit)) +
            '-' +
            (((i+1) * limit) > total ? total : ((i+1) * limit)) +
            "</option>";
    }
    return out;
}

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
        scrollToResults();
    });
}

function isValidEmail(text) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test(text);
}

function popup(url) {
    var hd, i, id, mode, name, path;
    var pattern = [];
    var systems = [ 'rna', 'reu', 'rww' ];

    path = url.split('/').reverse();
    for(i = 0; i <= 2; i++) {
        if ($.isNumeric(path[i])) {
            id = path[i];
            pattern.push('[id]');
        } else {
            pattern.push(path[i]);
        }
    }
    if (systems.includes(pattern[2])) {
        pattern.pop();
    }
    pattern.reverse();
    mode = pattern.join('_').replace(',', '_');

    if ('undefined' === typeof popWinSpecs[mode]) {
        alert('Unhandled mode ' + mode);
        return false;
    }
    name = mode.replace('[id]', id);
    hd = window.open(url, name, popWinSpecs[mode]);
    if (!hd){
        alert(
            'ERROR:\n\n'+
            'This site tried to open a popup window\n'+
            'but was prevented from doing so.\n\n'+
            'Please disable any popup blockers you may\n'+
            'have enabled for this site.'
        );
        return false;
    }
    hd.focus();
    return false;
}

function scrollToResults() {
    if ($('#form_show').val() !== '') {
        $([document.documentElement, document.body]).animate({
            scrollTop: $("#tabs").offset().top - 20
        }, 500);
    }
}

/* [ Add title for td cells having class 'clipped' ] */
function setClippedCellTitles() {
    $('td.clipped').each(function() {
        $(this).attr('title', $(this).text().trim());
    });
}

/* [ Enable sort actions for all sortable columns ] */
function setColumnSortActions() {
    $('table.results thead tr th[id]').each(function() {
        $(this).click(function () {
            var column = this.id.split('|')[0];
            var dir = this.id.split('|')[1];
            if ($(this).hasClass('sorted')) {
                dir = ($('#form_order').val() === 'a' ? 'd' : 'a');
            }
            $('#form_sort').val(column);
            $('#form_order').val(dir);
            $('form[name="form"]').submit();
        });
    });
}

/* [ Indicate which column is sorted by checking hidden fields on form ] */
function setColumnSortedClass() {
    $('table.results thead tr th').each(function() {
        if (this.id.split('|')[0] === $('#form_sort').val()) {
            $(this).append($('#form_order').val() === 'd' ? ' &#9662;' : ' &#9652;');
            $(this).addClass('sorted');
        }
    });
}

function setEmailLinks() {
    $('a[data-contact]').each(function() {
        var link = $(this).attr('data-contact').split('').reverse().join('').trim().replace('#','@');
        $(this).attr('href', link);
        $(this).removeAttr('data-contact');
    });
}

function setEqualHeight(a, b) {
    if (!$(a).height()) {
        return window.setTimeout(function(){ setEqualHeight(a, b); }, 100);
    }
    $(b).height($(a).height());
    $(b).show();
    $(b).height($(a).height());
}

/* [ Set links to open in external or popup window if rel attribute is set ] */
function setExternalLinks() {
    $('a[rel="external"]').attr('target', '_blank');
    $('a[data-popup]').click(function() {
        if (1 === $(this).data('popup')) {
            return popup(this.href);
        }
        var args = $(this).data('popup').split('|');
        window.open(this.href, args[0], args[1]);
        return false;
    });
    $('a[data-append]')
        .click(function() {
            var abbr, div, field, items;
            abbr = $(this).find('span').html();
            div = ('itu' === $(this).data('append') ? '#form_countries' : '#form_states');
            field = window.opener.$(div);
            items = field.val().split(' ');
            if ($.inArray(abbr, items) !== -1) {
                items = items.filter(function(elem){ return elem != abbr; });
            } else {
                items.push(abbr);
            }
            field.val(Array.from(new Set(items)).sort().join(' ').trim());
            return false;
        })
        .attr('title', msg.data_append);
    $('a[data-set]')
        .click(function() {
            var abbr, divs, field, i;
            abbr = $(this).text();
            switch ($(this).data('set')) {
                case 'khz':
                    divs = ['#form_khz_1', '#form_khz_2'];
                    break;
                case 'itu':
                    divs = ['#form_countries'];
                    break;
                case 'sp':
                    divs = ['#form_states'];
                    break;
            }
            for (i in divs) {
                field = $(divs[i]);
                if (field.val() === abbr) {
                    field.val('');
                } else {
                    field.val(abbr);
                }
            }
            $('form[name="form"]').submit();
            return false;
        })
        .attr('title', msg.data_set);

    $('a[data-gsq]')
        .click(function() {
            var target = 'map_' + $(this).data('gsq');
            var features = 'scrollbars=1,resizable=1,width=1024,height=800';
            window.open(this.href, target, features);
            return false;
        })
        .each(function() {
            $(this).attr('href', './signals/'  + $(this).data('gsq') + '/map');
        })
        .attr('title', msg.s_map_eu)
        .attr('class', 'hover');

    $('a[data-signal-map-eu]')
        .click(function() {
            var target = 'map_' + $(this).data('signal-map-eu');
            var features = 'scrollbars=1,resizable=1,width=1040,height=800';
            window.open(this.href, target, features);
            return false;
        })
        .each(function() {
            $(this).attr('href', './signals/'  + $(this).data('signal-map-eu') + '/map/eu');
        })
        .attr('title', msg.s_map_eu)
        .attr('class', 'hover');

    $('a[data-signal-map-na]')
        .click(function() {
            var target = 'map_' + $(this).data('signal-map-na');
            var features = 'scrollbars=1,resizable=1,width=1040,height=800';
            window.open(this.href, target, features);
            return false;
        })
        .each(function() {
            $(this).attr('href', './signals/' + $(this).data('signal-map-na') + '/map/na');
        })
        .attr('title', msg.s_map_na)
        .attr('class', 'hover');

    $('area[data-map]')
        .attr('shape', 'circle')
        .mouseover(function() {
            $('#listener_' + $(this).data('map'))
                .css({backgroundColor: '#ffff00'})
                .trigger('mouseenter');
        })
        .mouseout(function() {
            $('#listener_' + $(this).data('map'))
                .css({backgroundColor: ''})
                .trigger('mouseleave');
        })
        .each(function() {
            $(this).attr('title', $(this).attr('alt'));
        });

    $('tr[data-map]')
        .mouseover(function() {
            var coords = $(this).data('map').split('|');
            var scale = $('#rx_map').width() / $('#rx_map')[0].naturalWidth;
            $('#point_here')
                .show()
                .css({left: ((coords[0] * scale) - 5) + 'px', top: ((coords[1] * scale) - 5) + 'px'});
        })
        .mouseout(function() {
            $('#point_here').hide();
        });

    $('tr[data-map] a')
        .click(function() {
            var target = 'listeners_' + $(this).data('map');
            window.open(this.href, target, popWinSpecs["listeners_[id]"]);
            return false;
        });

}

function setFormCollapseSections() {
    $('#section_loggings legend').click(
        function() {
            $(this).parent().find('fieldset').toggle();
            $(this).find('span').toggle();
        }
    );
    $('#section_customise legend').click(
        function() {
            $(this).parent().find('fieldset').toggle();
            $(this).find('span').toggle();
        }
    );
}

function setFormCountryAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('select#form_country').change(function () {
            formSubmit();
        });
    } else {
        $('select#form_country').off('change');
    }
}

function setFormCountriesLabelLink() {
    var ele = $('label[for="form_countries"]');
    ele.html('<a href="countries/*" data-popup="1">' + ele.html() + '</a>');
}

function setFormStatesLabelLink() {
    var ele = $('label[for="form_states"]');
    ele.html('<a href="states/*" data-popup="1">' + ele.html() + '</a>');
}

function setFormDatePickers() {
    $.datepicker.setDefaults({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        yearRange: '1980:+0'
    });
    $('.js-datepicker').datepicker({
    });
}

function setFormHeardInModDefault() {
    if ($('fieldset#form_heard_in_mod div :radio:checked').length === 0) {
        $('fieldset#form_heard_in_mod div :radio[value="any"]').prop('checked', true);
    }
}

function setFormListenerInvertDefault() {
    if ($('fieldset#form_listener_invert div :radio:checked').length === 0) {
        $('fieldset#form_listener_invert div :radio[value=0]').prop('checked', true);
    }
}

function setFormListenerOptionsStyle() {
    var listener = $('#form_listener');
    listener.children().each(function() {
        if ($(this).val() === '') {
            $(this).addClass('all');
        } else if ($(this).text().substr(0,2) === '. ') {
            $(this).text('\xa0' + $(this).text().substr(1));
            $(this).addClass('secondaryQth');
        } else {
            $(this).addClass('primaryQth');
        }
    });
}

function setFormPagingActions() {
    var filter =    $('#form_filter');
    var prev =      $('#form_prev');
    var next =      $('#form_next');
    var limit =     $('#form_limit');
    if (limit.length) {
        limit[0].outerHTML =
            "<select id=\"form_limit\" name=\"form[limit]\" required=\"required\">" +
            getLimitOptions(paging.total, limit.val(), paging.limit) +
            "</select>";
        limit =     $('#form_limit');
    }

    var page =  $('#form_page');
    if (page.length) {
        page[0].outerHTML =
            "<label class=\"sr-only\" for=\"form_page\">Page Control</label>\n" +
            "<select id=\"form_page\" name=\"form[page\]\" style=\"display:none\">" +
            getPagingOptions(paging.total, limit.val(), paging.page) +
            "</select>";
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
}

function setFormPagingStatus(string, value) {
    $('#form_paging_status').html(
        string.replace('%s', value.toLocaleString())
    );
}

function setFormPersonaliseAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('#form_personalise').change(function () {
            var lbl = $('#form_personalise option:selected').text();
            var gsq = (lbl.split('|').length === 2 ? lbl.split('|')[1] : '').trim();
            $('#form_range_gsq').val(gsq);
            $('#form_range_gsq').trigger('keyup');
            $('form[name="form"]').submit();
        });
    } else {
        $('#form_personalise').off('change');
    }
}

function setFormOffsetsAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('#form_offsets').change(function () {
            formSubmit();
        });
    } else {
        $('#form_offsets').off('change');
    }
}

function setFormRangeAction() {
    $('#form_range_gsq').on('keyup', function() {
        var disabled = ($('#form_range_gsq').val().length < 6);
        $('#form_range_min').attr('disabled', disabled);
        $('#form_range_max').attr('disabled', disabled);
    });
    $('#form_range_min').on('keyup', function() {
        var disabled = ($('#form_range_min').val().length === 0 && $('#form_range_max').val().length === 0);
        $('#form_range_units').attr('disabled', disabled);
    });
    $('#form_dx_max').on('keyup', function() {
        var disabled = ($('#form_range_min').val().length === 0 && $('#form_range_max').val().length === 0);
        $('#form_range_units').attr('disabled', disabled);
    });
    $('#form_range_gsq').trigger('keyup');
    $('#form_range_min').trigger('keyup');
}

function setFormRangeUnitsDefault() {
    if ($('fieldset#form_range_units div :radio:checked').length === 0) {
        $('fieldset#form_range_units div :radio[value=km]').prop('checked', true);
    }
}

function setFormAdminAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('select#form_admin_mode').change(function () {
            formSubmit();
        });
    } else {
        $('select#form_admin_mode').off('change');
    }
}

function setFormHasMapPosAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('select#form_has_map_pos').change(function () {
            formSubmit();
        });
    } else {
        $('select#form_has_map_pos').off('change');
    }
}

function setFormRegionAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('select#form_region').change(function () {
            formSubmit();
        });
    } else {
        $('select#form_region').off('change');
    }
}

function setFormRwwFocusAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('select#form_rww_focus').change(function () {
            formSubmit();
        });
    } else {
        $('select#form_rww_focus').off('change');
    }
}

function setFormResetAction(form) {
    switch (form) {
        case 'signals':
            $('button[type="reset"]').click(function () {
                setFormAdminAction(false);
                setFormRegionAction(false);
                setFormRwwFocusAction(false);
                setFormOffsetsAction(false);
                setFormPersonaliseAction(false);

                $('#form_show').val('');
                $('#form_types div :checkbox').prop('checked', false);
                $('#form_types div :checkbox[value=type_NDB]').prop('checked', true);
                $('#form_call').val('');
                $('#form_khz_1').val('');
                $('#form_khz_2').val('');
                $('#form_channels').prop('selectedIndex', 0);
                $('#form_active').prop('selectedIndex', 0);
                $('#form_personalise').prop('selectedIndex', 0);
                $('#form_offsets').prop('selectedIndex', 0);

                $('#form_states').val('');
                $('#form_sp_itu_clause').prop('selectedIndex', 0);
                $('#form_countries').val('');
                $('#form_region').prop('selectedIndex', 0);
                $('#form_rww_focus').prop('selectedIndex', 0);
                $('#form_gsq').val('');
                $('#form_range_gsq').val('');
                $('#form_range_min').val('');
                $('#form_range_max').val('');
                $('#form_range_units_0').prop('checked', 1);
                $('#form_range_gsq').trigger('keyup');
                $('#form_range_min').trigger('keyup');

                $('#form_listener').val([]);
                $('#form_listener_invert_0').prop('checked', 1);
                $('#form_heard_in').val('');
                $('#form_heard_in_mod_0').prop('checked', 1);
                $('#form_logged_date_1').val('');
                $('#form_logged_date_2').val('');
                $('#form_logged_first_1').val('');
                $('#form_logged_first_2').val('');
                $('#form_logged_last_1').val('');
                $('#form_logged_last_2').val('');

                $('#form_admin_mode').prop('selectedIndex', 0);

                setFormPersonaliseAction(true);
                setFormOffsetsAction(true);
                setFormAdminAction(true);
                setFormRegionAction(true);
                setFormRwwFocusAction(true);
                formSubmit();
                return false;
            });
            break;
        case 'listeners':
            $('button[type="reset"]').click(function () {
                $('fieldset#form_types div :checkbox').prop('checked', false);
                $('fieldset#form_types div :checkbox[value=type_NDB]').prop('checked', true);
                $('#form_filter').val('');
                setFormCountryAction(false);
                setFormRegionAction(false);
                setFormRwwFocusAction(false);
                setFormHasMapPosAction(false);
                $('select#form_region').prop('selectedIndex', 0);
                $('select#form_country').prop('selectedIndex', 0);
                $('select#form_has_map_pos').prop('selectedIndex', 0);
                setFormCountryAction(true);
                setFormRegionAction(true);
                setFormRwwFocusAction(true);
                setFormHasMapPosAction(true);
                formSubmit();
                return false;
            });
            break;
    }
}

function formSubmit() {
    $('#form_clear').prop('disabled', 'disabled');
    $('#form_submit')
        .click()
        .prop('disabled', 'disabled');
}
function setFormShowModeAction() {
    $('#seeklist_paper')
        .change(function() {
            $('#form_paper').val($('#seeklist_paper option:selected').val());
            formSubmit();
        });
}

function setFormSortbyAction() {
    $('select#form_sortby').change(function() {
        var val = $("#form_sortby option:selected").val();
        $('#form_sort').val(val.split('|')[0]);
        $('#form_order').val(val.split('|')[1]);
        $('#form_za').prop('checked', val.split('|')[1] === 'd');
        formSubmit();
    });
}

function setFormSortZaAction() {
    $('input#form_za').change(function () {
        $('#form_order').val($('input#form_za').prop('checked') ? 'd' : 'a');
        formSubmit();
    });
}

/* [ Set css styles for signal type checkboxes ] */
function setFormTypesStyles() {
    $("fieldset#form_types div input").each(function() {
        $(this).parent().attr('class', $(this).attr('class'));
    });
}

/* [ Ensure that at least one option is checked for signal type checkboxes ] */
function setFormTypesDefault() {
    if ($('fieldset#form_types div :checkbox:checked').length === 0) {
        $('fieldset#form_types div :checkbox[value=type_NDB]').prop('checked', true);
    }
}

/* [ Enable 'All' checkbox to select / unselect all signal types ] */
function setFormTypesAllAction() {
    $('fieldset#form_types div :checkbox[value=type_ALL]').click(function () {
        $('fieldset#form_types div :checkbox').prop('checked', $(this).prop("checked"));
    });
}

function strip_tags(input, allowed) {
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
    var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    allowed = (((allowed || '') + '')
        .toLowerCase()
        .match(/<[a-z][a-z0-9]*>/g) || [])
        .join('');
    return input.replace(commentsAndPhpTags, '')
        .replace(tags, function($0, $1) {
                return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
            }
        );
}