/*
 * Project:    RXX - NDB Logging Database
 * Homepage:   https://rxx.classaxe.com
 * Version:    2.8.24
 * Date:       2020-05-07
 * Licence:    LGPL
 * Copyright:  2020 Martin Francis
 */
var gridColor = "#808080";
var gridOpacity = 0.5;
var highlight;
var layers = {grid: []};
var map;
var markers = [];
var all_sections = [];
var award = {};
var cart = [];

var popWinSpecs = {
    'countries_*' :                 'width=860,height=630,resizable=1',
    'countries_af' :                'width=640,height=630,resizable=1',
    'countries_as' :                'width=780,height=590,resizable=1',
    'countries_eu' :                'width=680,height=590,resizable=1',
    'countries_na' :                'width=640,height=220,resizable=1',
    'countries_oc' :                'width=680,height=500,resizable=1',
    'countries_sa' :                'width=320,height=600,resizable=1',
    'listeners_[id]' :              'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_logs' :         'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_upload' :       'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_signals' :      'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_map' :          'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_locatormap' :   'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    '[id]_signals_map' :            'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_ndbweblog' :    'status=1,scrollbars=1,resizable=1',
    'maps_af' :                     'width=646,height=652,resizable=1',
    'maps_alaska' :                 'width=600,height=620,resizable=1',
    'maps_as' :                     'width=856,height=645,resizable=1',
    'maps_au' :                     'width=511,height=545,resizable=1',
    'maps_eu' :                     'width=704,height=760,resizable=1',
    'maps_japan' :                  'width=517,height=740,resizable=1',
    'maps_na' :                     'width=669,height=720,resizable=1',
    'maps_pacific' :                'width=600,height=750,resizable=1',
    'maps_polynesia' :              'width=500,height=525,resizable=1',
    'maps_sa' :                     'width=490,height=745,resizable=1',
    'signals_[id]' :                'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_logs' :           'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_listeners' :      'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_map' :            'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_weather' :        'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'states_*' :                    'width=720,height=760,resizable=1',
    'states_aus' :                  'width=720,height=240,resizable=1',
    'states_can_usa' :              'width=680,height=690,resizable=1',
};

var awards = {
    all_sections : [],
    init : function(sections) {
        var i, zone;
        awards.all_sections = sections;
        for (i in awards.all_sections) {
            zone = awards.all_sections[i];
            $('#toggle_' + zone)
                .css({ 'cursor' : 'pointer' })
                .prop('title', msg.show_hide)
                .click(
                    function() {
                        $('#' + this.id.replace('toggle_','')).toggle();
                        $(this).find('span').toggle();
                    }
                )
                .find('span').css({'font-size': '120%'});
        }
        $('#all_0').click(function() { awards.toggleSections(0); return false; });
        $('#all_1').click(function() { awards.toggleSections(1); return false; });
        $('#form_email').change(function() {
            if (isValidEmail($('#form_email').val())) {
                $('#form_submit').removeAttr('disabled');
            } else {
                $('#form_submit').attr('disabled', 'disabled');
            }
        });
        $('#form_done').click(function() {
            location.replace(location.protocol + '//' + location.host + location.pathname );
        });
        $('#form_body').val(msg.cart_none);
        $('.cart').each(function() {
            $(this).html(
                '<span>' +
                '<img src="' + base_image + '/icon_cart.gif" alt="' + msg.no + '" title=""/>' +
                '<img style="display: none" src="' + base_image + '/icon_cart_added.gif" alt="' + msg.yes + '" />' +
                '</span>'
            );
        });
        $('.cart span').click(function() {
            var p = $(this).parent();
            var id = p.attr('id');
            awards.toggleAward(id);
            p.find('img').toggle();
        });
        $('#form_submit').click(function() {
            var message = msg.cart_conf_1 + '\n' + msg.cart_conf_2 + '\n\n' + msg.cart_conf_3 + '\n' + msg.cart_conf_4;
            if (!confirm(message)) {
                alert(msg.cancelled);
                return false;
            }
        });
    },
    toggleAward : function(id) {
        var awards, i, idx, len, message;
        len = 8;
        idx = $.inArray(id, cart);
        if (idx === -1) {
            cart.push(id);
        } else {
            cart.splice(idx, 1)
        }
        cart = cart.sort();
        message = msg.cart_none;
        if (cart.length) {
            awards = [];
            for (i in cart) {
                awards.push(cart[i].split('-')[0]);
            }
            awards = $.grep(awards, function(v, k){
                return $.inArray(v ,awards) === k;
            });
            message =
                msg.cart_1 + '\n' +
                msg.cart_2.padEnd(len, ' ') + award.admin + '\n' +
                msg.cart_3.padEnd(len, ' ') + award.from + '\n' +
                msg.cart_4.padEnd(len, ' ') + award.url + '/' + awards.join(',') + '\n' +
                '\n' +
                msg.cart_5 + '\n' +
                msg.cart_6 + '\n\n' +
                ' * ' + cart.join('\n * ') + '\n\n' +
                msg.cart_7 + '\n' +
                award.name;
        }
        $('#form_awards').val(cart.join(','));
        $('#form_filter').val(awards.join(','));
        $('#form_body').val(message);
        if (cart.length && isValidEmail($('#form_email').val())) {
            $('#form_submit').removeAttr('disabled');
        } else {
            $('#form_submit').attr('disabled', 'disabled');
        }
    },
    toggleSections : function(show) {
        var i, section, sectionToggle;
        for (i in awards.all_sections) {
            section = $('#' + awards.all_sections[i]);
            sectionToggle = $('#toggle_' + awards.all_sections[i]);
            if (show) {
                section.show();
                sectionToggle.find('span:eq(0)').hide();
                sectionToggle.find('span:eq(1)').show();
            } else {
                section.hide();
                $sectionToggle.find('span:eq(0)').show();
                sectionToggle.find('span:eq(1)').hide();
            }
        }
    }
};

function changeShowMode(mode) {
    $('#form_show').val(mode);
    formSubmit();
}

function decodeHtmlEntities(value) {
    return $("<div/>").html(value).text();
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

function isValidEmail(text) {
    var emailReg = /^([\w-.]+@([\w-]+\.)+[\w-]{2,4})?$/;
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
            var form_order = $('#form_order');
            if ($(this).hasClass('sorted')) {
                dir = (form_order.val() === 'a' ? 'd' : 'a');
            }
            $('#form_sort').val(column);
            form_order.val(dir);
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
                items = items.filter(function(elem){ return elem !== abbr; });
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

    $('a.close')
        .click(function() {
            $(this).parent().hide();
        });

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
}

function setFormCollapseSections() {
    $('#section_loggings legend').click(
        function() {
            $(this).parent().find('fieldset').toggle();
            $(this).parent().find('fieldset fieldset').toggle();
            $(this).find('span').toggle();
        }
    );
    $('#section_customise legend').click(
        function() {
            $(this).parent().find('fieldset').toggle();
            $(this).parent().find('fieldset fieldset').toggle();
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

function copyToClipboard(text) {
    var temp = $("<input>");
    $("body").append(temp);
    temp.val(text).select();
    document.execCommand("copy");
    temp.remove();
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
            var form_range_gsq = $('#form_range_gsq');
            form_range_gsq.val(gsq);
            form_range_gsq.trigger('keyup');
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
    var form_range_gsq = $('#form_range_gsq');
    var form_range_min = $('#form_range_min');
    form_range_gsq.on('keyup', function() {
        var disabled = ($('#form_range_gsq').val().length < 6);
        $('#form_range_min').attr('disabled', disabled);
        $('#form_range_max').attr('disabled', disabled);
    });
    form_range_min.on('keyup', function() {
        var disabled = ($('#form_range_min').val().length === 0 && $('#form_range_max').val().length === 0);
        $('#form_range_units').attr('disabled', disabled);
    });
    $('#form_dx_max').on('keyup', function() {
        var disabled = ($('#form_range_min').val().length === 0 && $('#form_range_max').val().length === 0);
        $('#form_range_units').attr('disabled', disabled);
    });
    form_range_gsq.trigger('keyup');
    form_range_min.trigger('keyup');
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

function setFormHasLogsAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('select#form_has_logs').change(function () {
            formSubmit();
        });
    } else {
        $('select#form_has_logs').off('change');
    }
}

function setFormTimezoneAction(enable) {
    enable = typeof enable !== 'undefined' ? enable : true;
    if (enable) {
        $('#form_timezone').on('selectmenuchange', function () {
            formSubmit();
        });
    } else {
        $('#form_timezone').off('selectmenuchange');
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
    var button_reset = $('button[type="reset"]');
    switch (form) {
        case 'signals':
            button_reset.click(function () {
                var form_range_gsq = $('#form_range_gsq');
                var form_range_min = $('#form_range_min');
                setFormAdminAction(false);
                setFormRegionAction(false);
                setFormRwwFocusAction(false);
                setFormOffsetsAction(false);
                setFormPersonaliseAction(false);

                $('#form_show').val('');
                $('#form_types div :checkbox').prop('checked', false);
                $('#form_types div :checkbox[value=NDB]').prop('checked', true);
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
                form_range_gsq.val('');
                form_range_min.val('');
                $('#form_range_max').val('');
                $('#form_range_units_0').prop('checked', 1);
                form_range_gsq.trigger('keyup');
                form_range_min.trigger('keyup');

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
            button_reset.click(function () {
                $('fieldset#form_types div :checkbox').prop('checked', false);
                $('fieldset#form_types div :checkbox[value=NDB]').prop('checked', true);
                $('#form_filter').val('');
                setFormCountryAction(false);
                setFormRegionAction(false);
                setFormRwwFocusAction(false);
                setFormHasLogsAction(false);
                setFormHasMapPosAction(false);
                setFormTimezoneAction(false);
                $('select#form_region').prop('selectedIndex', 0);
                $('select#form_country').prop('selectedIndex', 0);
                $('select#form_has_map_pos').prop('selectedIndex', 0);
                $('select#form_timezone').val('').selectmenu('refresh');
                setFormCountryAction(true);
                setFormRegionAction(true);
                setFormRwwFocusAction(true);
                setFormHasLogsAction(true);
                setFormHasMapPosAction(true);
                setFormTimezoneAction(true);
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
    $("fieldset#form_type div input").each(function() {
        $(this).parent().attr('class', 'type_' + $(this).attr('class'));
    });
}

/* [ Ensure that at least one option is checked for signal type checkboxes ] */
function setFormTypesDefault() {
    if ($('fieldset#form_type div :checkbox:checked').length === 0) {
        $('fieldset#form_type div :checkbox[value=NDB]').prop('checked', true);
    }
}

/* [ Enable 'All' checkbox to select / unselect all signal types ] */
function setFormTypesAllAction() {
    $('fieldset#form_type div :checkbox[value=ALL]').click(function () {
        $('fieldset#form_type div :checkbox').prop('checked', $(this).prop("checked"));
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

function initListenersLogUploadForm() {
    var formFormat = $('#form_format');
    formFormat.on('keyup', function() {
        $('#form_saveFormat').attr('disabled', $(this).val() === $('#formatOld').text());
    });

    // Detect if we reloaded the page dure to back button being pressed
    if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
        formFormat.trigger('keyup');
    }

    $('#form_saveFormat').on('click', function() {
        if (confirm(msg.log_upload_1) === false) {
            e.preventDefault();
            return;
        }
        $('#form_step').val('1b');
    });

    $('#form_tabs2spaces').on('click', function() {
        var logs = $('#form_logs');
        logs.val(logs.val().replace(/\t/g, '     '));
    });

    $('#form_lineUp').on('click', function() {
        var i, idx, line, log_arr, logs, max_words, word, word_num, word_len_arr, words;
        format = $('#form_format');
        logs = $('#form_logs');
        log_arr = logs.val().split('\n');
        max_words = 0;
        word_len_arr = [];
        for (idx in log_arr) {
            line = log_arr[idx].replace(/^\s+|\s+$/g,'').replace(/\s+/g,' ');
            words = line.split(' ').length;
            if (words > max_words) {
                max_words = words;
            }
            log_arr[idx] = line;
        }
        for (i = 0; i < max_words; i++) {
            word_len_arr[i] = 0;
        }
        for (idx in log_arr) {
            line = log_arr[idx];
            words = line.split(' ');
            for (word_num in words) {
                word = words[word_num];
                if (word.length > word_len_arr[word_num]) {
                    word_len_arr[word_num] = word.length;
                }
            }
        }
        for (idx in log_arr) {
            line = log_arr[idx];
            words = line.split(' ');
            for (word_num in words) {
                word = words[word_num];
                words[word_num] = word.padEnd(word_len_arr[word_num]+1, ' ');
            }
            log_arr[idx] = words.join('');
        }
        logs.val(log_arr.join('\r\n'));
    });

    $('#form_parseLog').on('click', function(e) {
        var f, field, fields;
        fields = [
            [ '#form_format', msg.log_upload_3, msg.log_upload_4 ],
            [ '#form_logs',   msg.log_upload_5, msg.log_upload_6 ]
        ];
        for (f in fields) {
            field = $(fields[f][0]);
            if (field.val() === '' || field.val() === fields[f][2]) {
                e.preventDefault();
                field.val(fields[f][2]);
                alert(msg.error.toUpperCase() + "\n\n" + fields[f][1]);
                field.focus().select();
                return false;
            }
        }
        $('#form_step').val(2);
    });

    $('#form_back').on('click', function() {
        window.history.back();
    });

    $(document).on('click', '.tokensHelpLink', function() {
        $(this).addClass('on');
        $(this).tooltip({
            content: $('#tokensHelp').html(),
            items: '.tokensHelpLink.on',
            position: {
                my: 'left+15 top-20',
                at: 'right center',
            },
            tooltipClass: 'toolTipDetails',
        });
        $(this).trigger('mouseenter');
        $('.tokensHelp b').on('click', function() {
            var txt = $(this).text();
            copyToClipboard(txt);
            alert(msg.copied.replace('%s', txt));
        }).attr('title', msg.copy_token);
        return false;
    });
    //hide
    $(document).on('click', '.tokensHelpLink.on', function () {
        $(this).removeClass('on');
        $(this).tooltip('close');
        return false;
    });
    //prevent mouseout and other related events from firing their handlers
    $('.tokensHelpLink').on('mouseout', function (e) {
        e.stopImmediatePropagation();
    });
}

// Used here: http://rxx.classaxe.com/en/rna/listeners/56/map
// Global vars:
//     google.maps
//     box, center, gridColor, gridOpacity, highlight, layers, map, markers

var LMap = {
    markerGroups : null,
    init : function() {
        map = new google.maps.Map($('#map').get(0), {
            center: { lat: center.lat, lng: center.lon },
            scaleControl: true,
            zoomControl: true,
            zoom: 7
        });

        if (box[0].lat !== box[1].lat || box[0].lon !== box[1].lon) {
            map.fitBounds(
                new google.maps.LatLngBounds(
                    new google.maps.LatLng(box[0].lat, box[0].lon), //sw
                    new google.maps.LatLng(box[1].lat, box[1].lon) //ne
                )
            );
        }
        LMap.drawGrid();
        LMap.drawMarkers();
        LMap.setActions();
        setExternalLinks();
        setClippedCellTitles();
    },

    drawGrid : function() {
        return drawGrid(map, layers);
    },

    drawMarkers : function() {
        var html, i, icon_highlight, icon_primary, icon_secondary, marker;
        if (!listeners) {
            return;
        }
        LMap.markerGroups=new google.maps.MVCObject();
        LMap.markerGroups.set('primary', map);
        LMap.markerGroups.set('secondary', map);
        LMap.markerGroups.set('highlight', map);
        icon_primary = {
            url: base_image + '/map_point3.gif',
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(5, 5)
        };
        icon_secondary = {
            url: base_image + '/map_point4.gif',
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(5, 5)
        };
        icon_highlight = {
            url: base_image + '/map_point_here.gif',
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(6, 6)
        };
        html = '';
        for (i in listeners) {
            l = listeners[i];
            html +=
                '<tr id="listener_' + l.id + '" class="qth_' + (l.pri ? 'pri' : 'sec') + '" data-gmap="' + l.lat + '|' + l.lon + '">' +
                '<td class="text-nowrap">' +
                '<img style="display:block;float: left" src="' + base_image + '/map_point' + (l.pri ? 3 : 4) + '.gif" alt="' + (l.pri ? msg.qth_pri : msg.qth_sec) + '" />' +
                '<a href="' + base_url + 'listeners/' + l.id + '" class="' + (l.pri ? 'pri' : 'sec') + '" data-popup="1">' +
                l.name +
                '</a></td>' +
                '<td>' + l.qth + '</td>' +
                '<td>' + l.sp + '</td>' +
                '<td>' + l.itu + '</td>' +
                '</tr>';
            marker = new google.maps.Marker({
                id: 'point_' + l.id,
                icon: (l.pri ? icon_primary : icon_secondary),
                position: new google.maps.LatLng(l.lat, l.lon),
                title: (decodeHtmlEntities(l.name) + ': ' + decodeHtmlEntities(l.qth) + (l.sp ? ', ' + l.sp : '') + ', ' + l.itu)
            });
            marker.bindTo('map', LMap.markerGroups, (l.pri ? 'primary' : 'secondary'));
            markers.push(marker);
        }

        for (i in markers) {
            markers[i].addListener('mouseover', function() {
                $('#listener_' + this.id.split('_')[1]).css('background', '#ffff00');
            });
            markers[i].addListener('mouseout', function () {
                $('#listener_' + this.id.split('_')[1]).css('background', '');
            });
            markers[i].addListener('click', function () {
                $('#listener_' + this.id.split('_')[1]).find('a').trigger('click');
            });
        }

        $('.results tbody').append(html);
        $('tr[data-gmap]')
            .mouseover(function() {
                var coords = $(this).data('gmap').split('|');
                highlight = new google.maps.Marker({
                    position: new google.maps.LatLng(coords[0], coords[1]),
                    map: map,
                    icon: icon_highlight
                });
            })
            .mouseout(function() {
                highlight.setMap(null);
            });
        $('.no-results').hide();
        $('.results').show();
    },

    setActions : function() {
        $('#layer_grid').click(function() {
            var active, i;
            active = $('#layer_grid').prop('checked');
            for (i in layers.grid) {
                layers.grid[i].setMap(active ? map : null);
            }
        });

        $('#layer_primary').click(function() {
            var layer_primary = $('#layer_primary');
            LMap.markerGroups.set('primary', layer_primary.prop('checked') ? map : null);
            if (layer_primary.prop('checked')) {
                $('#markerlist .qth_pri').show();
            } else {
                $('#markerlist .qth_pri').hide();
            }
        });

        $('#layer_secondary').click(function() {
            var layer_secondary = $('#layer_secondary');
            LMap.markerGroups.set('secondary', layer_secondary.prop('checked') ? map : null);
            if (layer_secondary.prop('checked')) {
                $('#markerlist .qth_sec').show();
            } else {
                $('#markerlist .qth_sec').hide();
            }
        });
    }
};

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
        setExternalLinks();
        setFormPagingStatus(pagingMsg, resultsCount);
        setListenerActions();
        scrollToResults();
        RT.init($('#wide'), $('#narrow'));
    })
}

function setListenerActions() {
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


// Used here: http://rxx.classaxe.com/en/rna/listeners/323/locatormap
var LocatorMap = {
    init : function(xpos, ypos) {
        var rx_map = $('#rx_map');
        if (!rx_map.height()) {
            return window.setTimeout(function(){ LocatorMap.init(xpos, ypos); }, 100);
        }
        rx_map.on('click', function () {
            var x = parseInt(e.pageX - $(this).offset().left);
            var y = parseInt(e.pageY - $(this).offset().top);
            LocatorMap.setPos(x, y);
            $('#form_mapX').val(x);
            $('#form_mapY').val(y);
        });
        $('#form_mapX').change(function() {
            xpos = parseInt($('#form_mapX').val());
            ypos = parseInt($('#form_mapY').val());
            LocatorMap.setPos(xpos, ypos);
        });
        $('#form_mapY').change(function() {
            xpos = parseInt($('#form_mapX').val());
            ypos = parseInt($('#form_mapY').val());
            LocatorMap.setPos(xpos, ypos);
        });
        $('#x_sub').click(function() {
            var form_mapX = $('#form_mapX');
            var val = parseInt(form_mapX.val());
            if (val > 0) {
                form_mapX
                    .val(val - 1)
                    .trigger('change');
            }
        });
        $('#x_add').click(function() {
            var form_mapX = $('#form_mapX');
            var val = parseInt(form_mapX.val());
            form_mapX
                .val(val + 1)
                .trigger('change');
        });
        $('#y_sub').click(function() {
            var form_mapY = $('#form_mapY');
            var val = parseInt(form_mapY.val());
            if (val > 0) {
                form_mapY
                    .val(val - 1)
                    .trigger('change');
            }
        });
        $('#y_add').click(function() {
            var form_mapY = $('#form_mapY');
            var val = parseInt(form_mapY.val());
            form_mapY
                .val(val + 1)
                .trigger('change');
        });
        $('#form_reset').click(function(e) {
            e.preventDefault();
            form = e.toElement.form;
            form.reset();
            xpos = $('#form_mapX').val();
            ypos = $('#form_mapY').val();
            LocatorMap.setPos(xpos, ypos);
        });
        LocatorMap.setPos(xpos, ypos);
        $('#form').show();

    },
    setPos : function(xpos, ypos) {
        if (xpos === 0 && ypos === 0) {
            return;
        }
        $('#cursor').css({
            left : (xpos - 10) + 'px',
            top : (ypos - 10) + 'px',
            display: 'block'
        });
    }
};

function drawGrid(map, layers) {
    TxtOverlay =    initMapsTxtOverlay();
    var i, la, lo;
    for (la=0; la<180; la+=10) {
        layers.grid.push(
            new google.maps.Polyline({
                path: [{lat: (la-90), lng: -180}, {lat:(la-90), lng: 0}, {lat: (la-90), lng: 180}],
                geodesic: false,
                strokeColor: gridColor,
                strokeOpacity: gridOpacity,
                strokeWeight: 0.5
            })
        );
    }
    for (lo=0; lo<360; lo+=20) {
        layers.grid.push(
            new google.maps.Polyline({
                path: [{lat:85.05, lng: lo}, {lat:-85.05, lng: lo}],
                geodesic: false,
                strokeColor: gridColor,
                strokeOpacity: gridOpacity,
                strokeWeight: 0.5
            })
        );
    }
    for (la=10; la<170; la+=10) {
        for (lo = 0; lo < 360; lo += 20) {
            layers.grid.push(
                new TxtOverlay(
                    new google.maps.LatLng(la -90 +5,lo -180 + 10),
                    String.fromCharCode((lo/20) +65) + String.fromCharCode((la/10) +65),
                    'gridLabel',
                    map
                )
            );
        }
    }
    for (i in layers.grid) {
        layers.grid[i].setMap(map);
    }
}

function initMapsTxtOverlay() {
    // Thanks to Michal, 'UX Lead at Alphero' for this custom text overlay code
    // Ref: https://stackoverflow.com/a/3955258/815790

    function TxtOverlay(pos, txt, cls, map) {
        this.pos = pos;
        this.txt_ = txt;
        this.cls_ = cls;
        this.map_ = map;
        this.div_ = null;
        this.setMap(map);
    }

    TxtOverlay.prototype = new google.maps.OverlayView();

    TxtOverlay.prototype.onAdd = function() {
        var div, overlayProjection, panes, position;
        div = document.createElement('DIV');
        div.className = this.cls_;
        div.innerHTML = this.txt_;
        this.div_ = div;
        overlayProjection = this.getProjection();
        position = overlayProjection.fromLatLngToDivPixel(this.pos);
        div.style.left = position.x + 'px';
        div.style.top = position.y + 'px';
        panes = this.getPanes();
        panes.floatPane.appendChild(div);
    };

    TxtOverlay.prototype.draw = function() {
        var div, position, overlayProjection;
        overlayProjection = this.getProjection();
        position = overlayProjection.fromLatLngToDivPixel(this.pos);
        div = this.div_;
        div.style.left = position.x + 'px';
        div.style.top = position.y + 'px';
    };

    TxtOverlay.prototype.onRemove = function() {
        this.div_.parentNode.removeChild(this.div_);
        this.div_ = null;
    };

    return TxtOverlay;
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



var RT = {
    classes: [],
    fields: [],
    preamble: '',
    rows: [],
    titles: [],
    init: function (source, destination) {
        this.source = source;
        this.destination = destination;
        this.readSource();
        this.drawMedium();
        this.drawNarrow();
    },
    drawMedium: function() {
        var colspan = 0, i;
        for (i in RT.fields) {
            if (RT.fields[i].rowspan2) {
                RT.preamble += ('th' === RT.fields[i].type ? '<th></th>' : '<td></td>');
            } else {
                colspan ++;
            }
        }
        i = 0;
        this.source.find('tbody tr').each(function () {
            var classname, ele = $(this), html;

            html = '<table>';
            for (j in RT.fields) {
                if (!RT.rows[i][RT.fields[j].idx].l2) {
                    continue;
                }
                if (!RT.rows[i][RT.fields[j].idx].html) {
                    continue;
                }
                classname = RT.rows[i][RT.fields[j].idx].class.replace(/( )*l2/ig, '');
                html += '<tr><th>' + RT.fields[j].html + ':</th><td class="' + classname + '">' + RT.rows[i][RT.fields[j].idx].html + '</td></tr>';
            }
            html += '</table>';

            ele.after(
                '<tr class="' + ele.prop('class') + ' l2_alt">' +
                RT.preamble + '<td colspan="' + colspan + '">' + html + '</td>' +
                '</tr>'
            );
            i++;
        })
    },
    drawNarrow: function() {
        var html, i, j;
        html = '';
        for (i in this.rows) {
            html += '<table class="responsive"><tbody>\n';
            for (j in this.fields) {
                if ('' === this.rows[i][this.fields[j].idx].html) {
                    continue;
                }
                html +=
                    '<tr title="' + this.titles[i] + '">' +
                    '<th>' + this.fields[j].html + '</th>' +
                    '<td class="' + this.rows[i][this.fields[j].idx].class + '">' + this.rows[i][this.fields[j].idx].html + '</td>' +
                    '</tr>\n';
            }
            html += '</tbody></table>\n\n';
        }
        this.destination.append(html);
    },
    readSource: function () {
        var idx = 0;
        this.source.find('thead tr th').each(function () {
            var header = $(this);
            RT.fields.push({
                idx: idx++,
                html: header.html().trim().split('<br>')[0],
                l2: header.hasClass('l2'),
                rowspan2: header.hasClass('rowspan2'),
                type: (header.hasClass('th') ? 'th' : 'td')
            });
            RT.classes.push(header.prop('title').trim());
            RT.titles.push(header.prop('title').trim());
        });
        this.source.find('tbody tr').each(function () {
            var ele = $(this);
            var row = {
//                class: ele.prop('class'),
                title: ele.prop('title')
            };
            var i = 0;
            ele.find('th,td').each(function () {
                var ele = $(this);
                row[RT.fields[i++].idx] = {
                    'class' : ele.prop('class'),
                    'l2' : ele.hasClass('l2'),
                    'html' : ele.html().trim()
                };
            });
            RT.rows.push(row);
        });
    }
};

var shareableLink = {
    getBaseUrl: function(mode) {
        return base_host + base_url + mode;
    },
    getFromField: function(field, options, letterCase) {
        var value = $('#form_' + field).val();
        if ('undefined' === typeof value || '' === value) {
            return '';
        }
        if ('string' === typeof letterCase && -1 !== $.inArray(letterCase, [ 'a', 'A' ])) {
            value = ('a' === letterCase ? value.toLowerCase() : value.toUpperCase());
        }
        if ('undefined' === typeof options || -1 !== $.inArray(value, options)) {
            return '&' + field + '=' + encodeURI(value);
        }
        return '';
    },
    getFromListeners: function() {
        var f1 = $('#form_listener');
        if ('undefined' === typeof f1.val() || '' === encodeURI(f1.val())) {
            return '';
        }
        return '&listeners=' + encodeURI(f1.val());
    },
    getFromPagingControls: function(defaultLimit) {
        var f1 = $('#form_limit');
        var f2 = $('#form_page');
        return (defaultLimit !== parseInt(f1.val()) ? '&limit=' + f1.val() : '') +
            ('undefined' !== typeof f2.val() && 0 !== parseInt(f2.val()) ? '&page=' + f2.val() : '');
    },
    getFromPair: function(field) {
        var f1 = $('#form_' + field + '_1');
        var f2 = $('#form_' + field + '_2');
        return (f1.val() || f2.val() ?
                '&' + field + '=' + encodeURI(f1.val()) +
                (f1.val() !== f2.val() ? ',' + encodeURI(f2.val()) : '')
                : ''
        );
    },
    getFromRadioGroup: function(field, options) {
        var f1 = $("input[name='form[" + field + "]']:checked");
        if ('undefined' === typeof f1.val() || '' === f1.val()) {
            return '';
        }
        if ('undefined' === typeof options || -1 !== $.inArray(f1.val(), options)) {
            return '&' + field + '=' + encodeURI(f1.val());
        }
        return '';
    },
    getFromSortingControls: function(defaultSorting, defaultOrder) {
        var f1 = $('#form_sort');
        var f2 = $('#form_order');
        return (defaultSorting !== f1.val() ? '&sort=' + f1.val() : '') +
            (defaultOrder !== f2.val() ? '&order=' + f2.val() : '');
    },
    getFromTypes: function() {
        var types = [];
        $("fieldset#form_type div input").each(function() {
            if ($(this).is(':checked') && 'ALL' !== $(this).prop('value')) {
                types.push($(this).prop('value'));
            }
            if (0 === types.length) {
                types = ['NDB'];
            }
            if (7 === types.length) {
                types = ['ALL'];
            }
        });
        return '?types=' + $.uniqueSort(types).join(',');
    },
    listenersUrl: function() {
        return this.getBaseUrl('listeners') +
            this.getFromTypes() +
            this.getFromField('q') +
            this.getFromField('region') +
            this.getFromField('country') +
            this.getFromField('has_logs', [ 'N', 'Y' ], 'A') +
            this.getFromField('has_map_pos', [ 'N', 'Y' ], 'A') +
            this.getFromField('timezone') +
            this.getFromPagingControls(100) +
            this.getFromSortingControls('name', 'a');
    },
    signalsUrl: function() {
        return this.getBaseUrl('signals') +
            this.getFromTypes() +
            this.getFromField('rww_focus') +
            this.getFromField('call') +
            this.getFromPair('khz') +
            this.getFromField('channels') +
            this.getFromField('states') +
            this.getFromField('sp_itu_clause', [ 'or' ]) +
            this.getFromField('countries') +
            this.getFromField('region') +
            this.getFromField('gsq') +
            this.getFromField('active') +

            this.getFromListeners() +
            this.getFromRadioGroup('listener_invert', [ '1' ]) +
            this.getFromField('heard_in') +
            this.getFromRadioGroup('heard_in_mod', [ 'all' ]) +
            this.getFromPair('logged_date') +
            this.getFromPair('logged_first') +
            this.getFromPair('logged_last') +

            this.getFromPagingControls(50) +
            this.getFromSortingControls('khz', 'a') +
            this.getFromField('personalise') +
            this.getFromField('offsets', ['1']) +
            this.getFromField('range_gsq') +
            this.getFromField('range_min') +
            this.getFromField('range_max') +
            (this.getFromField('range_gsq') ? this.getFromRadioGroup('range_units') : '') +

            this.getFromRadioGroup('paper', [ 'a4', 'a4_l', 'lgl', 'lgl_l', 'ltr', 'ltr_l' ]);
    }
};

function shareListeners() {
    var url = shareableLink.listenersUrl();
    var dialog = $('#dialog');
    dialog
        .html(
            '<p>' + msg.share.listeners.text1 +'<br>' + msg.share.listeners.text2 +'</p>' +
            '<ul>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=list">' + msg.share.listeners.links.list + '</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=map">' + msg.share.listeners.links.map + '</a></li>' +
            '</ul>')
        .dialog({
            buttons: [{
                text: msg.close ,
                click: function() {
                    $( this ).dialog( "close" );
                }
            }],
            open: function() {
                $('.ui-dialog-buttonpane button').focus();
            },
            modal: true,
            title: msg.share.listeners.title
        });
//    alert(url);
//    copyToClipboard(url);
}

function shareSignals() {
    var url = shareableLink.signalsUrl();
    var dialog = $('#dialog');
    dialog
        .html(
            '<p>' + msg.share.signals.text1 +'<br>' + msg.share.signals.text2 +'</p>' +
            '<ul>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=list">' + msg.share.signals.links.list + '</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=seeklist">' + msg.share.signals.links.seeklist + '</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=map">' + msg.share.signals.links.map + '</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=csv">' + msg.share.signals.links.export + '</a></li>' +
            '</ul>')
        .dialog({
            buttons: [{
                text: msg.close ,
                click: function() {
                    $( this ).dialog( "close" );
                }
            }],
            open: function() {
                $('.ui-dialog-buttonpane button').focus();
            },
            modal: true,
            title: msg.share.signals.title
        });
//    alert(url);
//    copyToClipboard(url);
}


var SLMap = {
    init : function() {
        var html = '', i, imgmap = '', l;
        for (i in listeners) {
            l = listeners[i];
            html +=
                '<tr id="listener_' + l.id + '" data-map="' + l.x + '|' + l.y + '|' + l.id +'"' + (l.dt ? ' title="' + msg.daytime + '"' : '') +'>\n' +
                '<td>\n' +
                '<img src="' + base_image + '/map_point' + (l.pri ? 1 : 2) +'.gif" alt="' + (l.pri ? msg.qth_pri : msg.qth_sec) + '" />\n' +
                '<a href="' + base_url + 'listeners/' + l.id + '" class="' + (l.pri ? 'pri' : 'sec') + '">' + l.name + '</a>\n' +
                '</td>\n' +
                '<td>' + l.sp + '</td>\n' +
                '<td>' + l.itu + '</td>\n' +
                '<td>' + (l.dt ? '<b>' + l.km + '</b>' : l.km ) + '</td>\n' +
                '<td>' + (l.dt ? '<b>' + l.mi + '</b>' : l.mi ) + '</td>\n' +
                '</tr>\n';
            imgmap +=
                '<area alt="' + l.name + '" title="' + l.name + '" shape="circle" href="' + base_url + 'listeners/' + l.id + '" coords="' + l.x + ',' + l.y + ',4" data-map="' + l.id + '" />\n';
        }
        $('.results tbody').html(html);
        $('#imgmap').html(imgmap);
        SLMap.setActions();
    },

    setActions : function() {
        $('area[data-map]')
            .mouseover(function() {
                $('#listener_' + $(this).data('map'))
                    .css({backgroundColor: '#ffff00'})
                    .trigger('mouseenter');
            })
            .mouseout(function() {
                $('#listener_' + $(this).data('map'))
                    .css({backgroundColor: ''})
                    .trigger('mouseleave');
            });

        $('tr[data-map]')
            .mouseover(function() {
                var coords = $(this).data('map').split('|');
                var rx_map = $('#rx_map');
                var scale = rx_map.width() / rx_map[0].naturalWidth;
                $('#point_here')
                    .show()
                    .css({left: ((coords[0] * scale) - 5) + 'px', top: ((coords[1] * scale) - 5) + 'px'})
                    .unbind()
                    .click(function(e) {
                        e.preventDefault();
                        $('#listener_' + coords[2] + ' a').trigger('click');
                        return false;
                    });
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
};


// Globals: signals, types
var SMap = {
    map : null,
    icons : {},
    infoWindow : null,
    markers : [],
    options : {},

    init: function() {
        var icons = [ 'dgps', 'dsc', 'hambcn', 'navtex', 'ndb', 'time', 'other' ];
        var states = [ 0, 1 ];
        for (var i in icons) {
            for (var j in states) {
                var pin = base_image + '/pins/' + icons[i] + '_' + states[j] + '.png';
                SMap.icons[icons[i] + '_' + states[j]] =
                    new google.maps.MarkerImage(pin, new google.maps.Size(12, 20));
            }
        }
        SMap.options = {
            'zoom': 7,
            'center': new google.maps.LatLng(center.lat, center.lon),
            'mapTypeId': google.maps.MapTypeId.ROADMAP
        };
        SMap.map = new google.maps.Map($('#map').get(0), SMap.options);
        if (box[0].lat !== box[1].lat || box[0].lon !== box[1].lon) {
            SMap.map.fitBounds(
                new google.maps.LatLngBounds(
                    new google.maps.LatLng(box[0].lat, box[0].lon), //sw
                    new google.maps.LatLng(box[1].lat, box[1].lon) //ne
                )
            );
        }

        SMap.infoWindow = new google.maps.InfoWindow();
        SMap.drawGrid();
        SMap.drawMarkers();
        SMap.drawQTH();
        SMap.setActions();
        setExternalLinks();
        setClippedCellTitles();
    },

    drawGrid : function() {
        return drawGrid(SMap.map, layers);
    },

    drawMarkers : function() {
        var html, i, icon_highlight, marker, mode;
        if (!signals) {
            return;
        }
        mode = (typeof listener === 'undefined' ? 'S' : 'LS');
        SMap.markerGroups=new google.maps.MVCObject();
        for(i in types) {
            SMap.markerGroups.set('type_' + types[i] + '_0', SMap.map);
            SMap.markerGroups.set('type_' + types[i] + '_1', SMap.map);
        }
        SMap.markerGroups.set('highlight', SMap.map);

        icon_highlight = {
            url: base_image + '/map_point_here.gif',
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(6, 7)
        };

        html = '';
        for (i in signals) {
            s = signals[i];
            html +=
                '<tr' +
                ' class="type_' + s.typeId + ' type_' + s.className + (typeof s.logged !== 'undefined' ? (s.logged ? ' logged' : ' unlogged') : '') + '"' +
                ' id="signal_' + s.id + '"' +
                ' data-gmap="' + s.lat + '|' + s.lon + '"' +
                '>' +
                (typeof s.logged !== 'undefined' ? '<th>' + (s.logged ? '&#x2714;' : '&nbsp;') + '</th>' : '') +
                '<td>' + s.khz + '</td>' +
                '<td class="text-nowrap">' +
                '<a href="' + base_url + 'signals/' + s.id + '" class="' + (s.active ? '' : 'inactive') + '" data-popup="1">' + s.call + '</a>' +
                '</td>' +
                '<td class="clipped">' + s.qth + '</td>' +
                '<td>' + s.sp + '</td>' +
                '<td>' + s.itu + '</td>' +
                ('LS' === mode ? '<td class="num">' + s.km + '</td>' : '') +
                ('LS' === mode ? '<td class="num">' + s.mi + '</td>' : '') +
                '</tr>';

            marker = new google.maps.Marker({
                id : 'point_' + s.id,
                icon : SMap.icons[s.icon + '_' + (s.active ? 1 : 0)],
                position : new google.maps.LatLng(s.lat, s.lon),
                title :  strip_tags(s.khz + ' ' + s.call)
            });
            google.maps.event.addListener(marker, 'click', SMap.markerClickFunction(s));
            marker.bindTo('map', SMap.markerGroups, 'type_' + s.typeId + '_' + (s.active ? '1' : '0'));
            markers.push(marker);
        }

        $('.results tbody').append(html);

        $('tr[data-gmap]')
            .mouseover(function() {
                var coords = $(this).data('gmap').split('|');
                highlight = new google.maps.Marker({
                    position: new google.maps.LatLng(coords[0], coords[1]),
                    map: SMap.map,
                    icon: icon_highlight
                });
            })
            .mouseout(function() {
                highlight.setMap(null);
            });

        $('.no-results').hide();
        $('.results').show();
    },

    drawQTH : function() {
        if (typeof listener === 'undefined') {
            return;
        }
        layers.qth = new google.maps.Marker({
            position: { lat: listener.lat, lng: listener.lng },
            map: SMap.map,
            icon: {
                scaledSize: new google.maps.Size(30,30),
                url: "//maps.google.com/mapfiles/kml/pushpin/red-pushpin.png"
            },
            title: listener.name,
            zIndex: 100
        });

        qthInfo = new google.maps.InfoWindow({
            content:
                "<h2>" + listener.name + "</h2>" +
                "<p>" + listener.qth + "</p>"
        });

        layers.qth.addListener('click', function() {
            qthInfo.open(SMap.map, layers.qth);
        });
    },

    markerClickFunction: function(s) {
        return function(e) {
            e.cancelBubble = true;
            e.returnValue = false;
            if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            }
            var infoHtml =
                '<div class="map_info">' +
                '  <h3><a href="' + base_url + 'signals/' + s.id + '" onclick="return popup(this.href);">' + s.khz + ' ' + s.call + '</a></h3>' +
                '  <table class="info-body">' +
                (typeof s.logged !== 'undefined' ? '    <tr><th>' + msg.logged +'</th><td>' + (s.logged ? msg.yes : msg.no) + '</td></tr>' : '') +
                '    <tr><th>' + msg.id + '</th><td>'+s.call + '</td></tr>' +
                '    <tr><th>' + msg.khz + '</th><td>'+s.khz + '</td></tr>' +
                '    <tr><th>' + msg.type + '</th><td>'+s.type + '</td></tr>' +
                (s.pwr !== '0' ? '    <tr><th>' + msg.power + '</th><td>'+s.pwr + 'W</td></tr>' : '') +
                '    <tr><th>' + msg.name_qth + '</th><td>'+s.qth + (s.sp ? ', ' + s.sp : '') + ', ' + s.itu + '</td></tr>' +
                (s.gsq ? '    <tr><th>' + msg.gsq + '</th><td><a href="' + base_url + 'signals/' + s.id + '/map" onclick="return popup(this.href);" title="Show map (accuracy limited to nearest Grid Square)">'+s.gsq+'</a></td></tr>' : '') +
                '    <tr><th>' + msg.lat_lon + '</th><td>' + s.lat + ', ' + s.lon + '</td></tr>' +
                (s.usb || s.lsb ? '    <tr><th>' + msg.sidebands + '</th><td>' + (s.lsb ? 'LSB: ' + s.lsb : '') + (s.usb ? (s.lsb ? ', ' : '') + ' USB: ' + s.usb : '') + '</td></tr>' : '') +
                (s.sec || s.fmt ? '    <tr><th>' + msg.sec_format + '</th><td>' + (s.sec ? s.sec + ' sec' : '') + (s.sec && s.fmt ? ', ' : '') + s.fmt + '</td></tr>' : '') +
                '    <tr><th>' + msg.last_logged + '</th><td>' + s.heard + '</td></tr>' +
                '    <tr><th>' + msg.heard_in + '</th><td>' + s.heard_in + '</td></tr>' +
                '  </table>' +
                '</div>';
            SMap.infoWindow.setContent(infoHtml);
            SMap.infoWindow.setPosition(new google.maps.LatLng(s.lat, s.lon));
            SMap.infoWindow.open(SMap.map);
        };
    },

    setActions : function() {
        $('#layer_grid').click(function() {
            var active, i;
            active = $('#layer_grid').prop('checked');
            for (i in layers.grid) {
                layers.grid[i].setMap(active ? SMap.map : null);
            }
        });

        $('#layer_qth').click(function() {
            layers['qth'].setMap($('#layer_qth').prop('checked') ? SMap.map : null);
        });

        $('#layer_active').click(function() {
            var i, layer_active, layer_type, type;
            for (i in types) {
                type = types[i];
                layer_active = $('#layer_active');
                layer_type = $('#layer_' + type);
                SMap.markerGroups.set(
                    'type_' + type + '_1',
                    layer_active.prop('checked') && layer_type.prop('checked') ? SMap.map : null
                );
                if (layer_type.prop('checked')) {
                    if (layer_active.prop('checked')) {
                        $('.results tbody .type_' + type + '.active').show();
                    } else {
                        $('.results tbody .type_' + type + '.active').hide();
                    }
                } else {
                    $('.results tbody .type_' + type + '.active').hide();
                }
            }
        });
        $('#layer_inactive').click(function() {
            var i, layer_inactive, layer_type, type;
            for (i in types) {
                type = types[i];
                layer_inactive = $('#layer_inactive');
                layer_type = $('#layer_' + type);
                SMap.markerGroups.set(
                    'type_' + type + '_0',
                    layer_inactive.prop('checked') && layer_type.prop('checked') ? SMap.map : null
                );
                if (layer_type.prop('checked')) {
                    if (layer_inactive.prop('checked')) {
                        $('.results tbody .type_' + type + '.inactive').show();
                    } else {
                        $('.results tbody .type_' + type + '.inactive').hide();
                    }
                } else {
                    $('.results tbody .type_' + type + '.inactive').hide();
                }
            }
        });
        types.forEach(function(type){
            $('#layer_' + type).click(function() {
                var layer_type = $('#layer_' + type);
                SMap.markerGroups.set(
                    'type_' + type + '_0',
                    $('#layer_inactive').prop('checked') && layer_type.prop('checked') ? SMap.map : null
                );
                SMap.markerGroups.set(
                    'type_' + type + '_1',
                    $('#layer_active').prop('checked') && layer_type.prop('checked') ? SMap.map : null
                );
                if (layer_type.prop('checked')) {
                    if ($('#layer_inactive').prop('checked')) {
                        $('.results tbody .type_' + type +'.inactive').show();
                    } else {
                        $('.results tbody .type_' + type +'.inactive').hide();
                    }
                    if ($('#layer_active').prop('checked')) {
                        $('.results tbody .type_' + type +'.active').show();
                    } else {
                        $('.results tbody .type_' + type +'.active').hide();
                    }
                } else {
                    $('.results tbody .type_' + type).hide();
                }
            });
        });

    }
};

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
