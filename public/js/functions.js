/*
 * Project:    RXX - NDB Logging Database
 * Homepage:   https://rxx.classaxe.com
 * Version:    2.44.6
 * Date:       2022-07-23
 * Licence:    LGPL
 * Copyright:  2022 Martin Francis
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
    'admin_users_[id]' :                    'width=420,height=320,status=1,scrollbars=1,resizable=1',
    'admin_users_new' :                     'width=420,height=320,status=1,scrollbars=1,resizable=1',
    'countries_*' :                         'width=860,height=630,resizable=1',
    'countries_af' :                        'width=640,height=630,resizable=1',
    'countries_as' :                        'width=780,height=590,resizable=1',
    'countries_eu' :                        'width=680,height=590,resizable=1',
    'countries_na' :                        'width=640,height=220,resizable=1',
    'countries_oc' :                        'width=680,height=500,resizable=1',
    'countries_sa' :                        'width=320,height=600,resizable=1',
    'listeners_[id]' :                      'width=1120,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_logs' :                 'width=1120,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_logsessions' :          'width=1120,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_upload' :               'width=1120,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_signals' :              'width=1120,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_map' :                  'width=1120,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_locatormap' :           'width=1120,height=800,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_remotelogs' :           'width=1120,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_remotelogsessions' :    'width=1120,height=760,status=1,scrollbars=1,resizable=1',
    '[id]_signals_map' :                    'width=1120,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_ndbweblog' :            'status=1,scrollbars=1,resizable=1',
    'logs_[id]' :                           'width=640,height=540,status=1,scrollbars=1,resizable=1',
    'maps_af' :                             'width=646,height=652,resizable=1',
    'maps_alaska' :                         'width=600,height=620,resizable=1',
    'maps_as' :                             'width=856,height=645,resizable=1',
    'maps_au' :                             'width=511,height=545,resizable=1',
    'maps_eu' :                             'width=704,height=760,resizable=1',
    'maps_japan' :                          'width=517,height=740,resizable=1',
    'maps_na' :                             'width=669,height=720,resizable=1',
    'maps_pacific' :                        'width=600,height=750,resizable=1',
    'maps_polynesia' :                      'width=500,height=525,resizable=1',
    'maps_sa' :                             'width=490,height=745,resizable=1',
    'signals_new' :                         'width=820,height=400,status=1,scrollbars=1,resizable=1',
    'signals_[id]' :                        'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_logs' :                   'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_listeners' :              'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_map' :                    'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_weather' :                'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'states_*' :                            'width=720,height=780,resizable=1',
    'states_aus' :                          'width=720,height=240,resizable=1',
    'states_can_usa' :                      'width=680,height=710,resizable=1',
    'tools_coordinates' :                   'width=900,height=195,resizable=1',
    'tools_dgps' :                          'width=720,height=345,resizable=1',
    'tools_navtex' :                        'width=420,height=580,resizable=1',
    'tools_negativeKeyer' :                 'width=800,height=560,resizable=1',
    'tools_references' :                    'width=520,height=130,resizable=1',
    'tools_sunrise' :                       'width=520,height=385,resizable=1',
    'weather_aurora_n' :                    'width=520,height=580,resizable=1',
    'weather_aurora_s' :                    'width=520,height=580,resizable=1',
    'weather_lightning' :                   'width=620,height=620,resizable=1',
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

var cle = {
    init: function() {
        $('#toggle_editor')
            .css({ 'cursor' : 'pointer' })
            .prop('title', msg.show_hide)
            .click(
                function() {
                    $('#' + this.id.replace('toggle_','')).toggle();
                    $(this).find('span').toggle();
                }
            )
            .find('span').css({'font-size': '120%'});
        $.datepicker.setDefaults({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '2010:+1'
        });
        $('.js-datepicker').datepicker({ });
        tinymce.init({
            selector: 'textarea',
            height: 150,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor textcolor colorpicker',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste code wordcount'
            ],
            toolbar: 'insert | undo redo | formatselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code | help',
        });

        $('td input[type="checkbox"]').click(
            function(){
                var types = [];
                var field = $(this).parent().parent().find('input:hidden');
                $(this).parent().parent().find('input:checkbox:checked').each(function(){
                    var type = 'type_' + $(this).parent().prop('className').split('_')[1].toUpperCase() + '=1';
                    types.push(type);
                })
                field.val(types.join('&amp;'));
            }
        );
        cle.setTypes();
        setExternalLinks();
    },
    setTypes: function() {
        var i, types, typeArray, value;
        types = ['#form_worldRange1Type', '#form_worldRange2Type', '#form_europeRange1Type', '#form_europeRange2Type'];
        for (i in types) {
            value = $(types[i]).val();
            if (typeof value !== 'undefined') {
                typeArray = value.split('&amp;');
                $(types[i]).parent().find('input:checkbox').each(function () {
                    var j, type;
                    $(this).prop('checked', false);
                    for (j = 0; j < typeArray.length; j++) {
                        type = 'type_' + typeArray[j].split('_')[1].split('=')[0].toLowerCase();
                        if ($(this).parent().prop('className') === type) {
                            $(this).prop('checked', 'checked');
                        }
                    }
                })
            }
        }
    }
}

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
        COMMON_FORM.setSectionToggleWithCookie('section_credits')
    },

    setTipsHideShowActions: function() {
        COMMON_FORM.setSectionToggleWithCookie('section_tips')
    },

    setPagingControls: function() {
        var filter =    $('#form_filter');
        var prev =      $('#form_prev');
        var next =      $('#form_next');
        var prevbot =   $('#form_prevbottom');
        var nextbot =   $('#form_nextbottom');
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
            if (prevbot.length) {
                prevbot.show();
                nextbot.show();
            }
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
                    if (prevbot.length) {
                        prevbot.prop('disabled', 'disabled');
                        nextbot.prop('disabled', 'disabled');
                        prevbot.show();
                        nextbot.show();
                    }
                } else {
                    page.hide();
                    prev.hide();
                    next.hide();
                    if (prevbot.length) {
                        prevbot.hide();
                        nextbot.hide();
                    }
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
                    var prevbot =   $('#form_prevbottom');
                    var nextbot =   $('#form_nextbottom');
                    prev.prop('disabled', 'disabled');
                    next.prop('disabled', 'disabled');
                    options.eq(paging.page - 1).prop('selected', true);
                    page.prop('selectedIndex', paging.page - 1);
                    if (prevbot.length) {
                        prevbot.prop('disabled', 'disabled');
                        nextbot.prop('disabled', 'disabled');
                    }
                    form.submit();
                    return false;
                }
            );
        } else {
            prev.prop('disabled', 'disabled');
            if (prevbot.length) {
                prevbot.prop('disabled', 'disabled');
            }
        }

        if (paging.page + 1 < options.length) {
            next.prop('disabled', false);
            nextbot.prop('disabled', false);
            next.click(
                function() {
                    var form =      $('form[name="form"]');
                    var page =      $('#form_page');
                    var options =   $('#form_page option');
                    var prev =      $('#form_prev');
                    var next =      $('#form_next');
                    var prevbot =   $('#form_prevbottom');
                    var nextbot =   $('#form_nextbottom');
                    prev.prop('disabled', 'disabled');
                    next.prop('disabled', 'disabled');
                    options.eq(paging.page + 1).prop('selected', true);
                    page.prop('selectedIndex', paging.page + 1);
                    if (prevbot.length) {
                        prevbot.prop('disabled', 'disabled');
                        nextbot.prop('disabled', 'disabled');
                    }
                    form.submit();
                    return false;
                }
            );
        } else {
            next.prop('disabled', 'disabled');
            if (prevbot.length) {
                nextbot.prop('disabled', 'disabled');
            }
        }

        page.change(
            function() {
                var form =      $('form[name="form"]');
                var prev =      $('#form_prev');
                var next =      $('#form_next');
                var prevbot =   $('#form_prev');
                var nextbot =   $('#form_next');
                prev.prop('disabled', 'disabled');
                next.prop('disabled', 'disabled');
                if (prevbot.length) {
                    prevbot.prop('disabled', 'disabled');
                    nextbot.prop('disabled', 'disabled');
                }
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
                var prevbot =   $('#form_prevbottom');
                var nextbot =   $('#form_nextbottom');
                options.eq(0).prop('selected', true);
                page.prop('selectedIndex', 0);
                prev.prop('disabled', 'disabled');
                next.prop('disabled', 'disabled');
                if (prevbot.length) {
                    prevbot.prop('disabled', 'disabled');
                    nextbot.prop('disabled', 'disabled');
                }
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

    setSectionToggleWithCookie: function(id) {
        var container = $('#' + id);
        var hide = $('#' + id + '_hide');
        var show = $('#' + id + '_show');
        show.on('click', function(){
            COOKIE.set(id + '_hide', 'no');
            $('#' + id).show();
            $('#' + id + '_hide').show();
            $(this).hide();
            $(window).trigger('resize');
        });
        hide.on('click', function(){
            COOKIE.set(id + '_hide', 'yes');
            $('#' + id).hide();
            $('#' + id + '_show').show();
            $(this).hide();
            $(window).trigger('resize');
        });
        if (COOKIE.get(id + '_hide') === 'yes') {
            container.hide();
            hide.hide();
            show.show();
        } else {
            container.show();
            hide.show();
            show.hide();
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

Number.prototype.numberFormat = function(decimals, dec_point, thousands_sep) {
    var parts
    dec_point = typeof dec_point !== 'undefined' ? dec_point : '.';
    thousands_sep = typeof thousands_sep !== 'undefined' ? thousands_sep : ',';
    parts = this.toFixed(decimals).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);
    return parts.join(dec_point);
}

function changeShowMode(mode) {
    $('#form_show').val(mode);
    formSubmit();
}

function decodeHtmlEntities(value) {
    return $("<div/>").html(value).text();
}

function encodeMorse(value) {
    var chars, i, morse, out;
    morse = {
        '0': '-----',
        '1': '.----',
        '2': '..---',
        '3': '...--',
        '4': '....-',
        '5': '.....',
        '6': '-....',
        '7': '--...',
        '8': '---..',
        '9': '----.',
        'a': '.-',
        'b': '-...',
        'c': '-.-.',
        'd': '-..',
        'e': '.',
        'f': '..-.',
        'g': '--.',
        'h': '....',
        'i': '..',
        'j': '.---',
        'k': '-.-',
        'l': '.-..',
        'm': '--',
        'n': '-.',
        'o': '---',
        'p': '.--.',
        'q': '--.-',
        'r': '.-.',
        's': '...',
        't': '-',
        'u': '..-',
        'v': '...-',
        'w': '.--',
        'x': '-..-',
        'y': '-.--',
        'z': '--..',
        '.': '.-.-.-',
        ',': '--..--',
        '?': '..--..',
        '!': '-.-.--',
        '-': '-....-',
        '/': '-..-.',
        '@': '.--.-.',
        '(': '-.--.',
        ')': '-.--.-',
        ' ': ' ',
    };
    chars = value.toLowerCase().split('');
    out = [];
    for (i=0; i<chars.length; i++) {
        out.push(typeof morse[chars[i]] !== 'undefined' ? morse[chars[i]] : '?');
    }
    return out.join('/');
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
    if (systems.indexOf(pattern[2]) !== -1) {
        pattern.pop();
    }
    pattern.reverse();
    mode = pattern.join('_').replace(',', '_').split('?')[0];

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

function copyToClipboard(text) {
    var temp = $("<textarea>");
    $("body").append(temp);
    temp.val(text).select();
    document.execCommand("copy");
    temp.remove();
}

function formSubmit() {
    $('#form_clear').prop('disabled', 'disabled');
    $('#form_save').prop('disabled', 'disabled');
    $('#form_submit')
        .click()
        .prop('disabled', 'disabled');
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

function lead(num, size) {
    var s = String(num);
    while (s.length < (size || 2)) {
        s = "0" + s;
    }
    return s;
}

function pad(txt, size, padStr) {
    var s = String(txt);
    var r = (s + ('                                        ')).substr(0, size);
    if ('string' === typeof padStr) {
        return r.replace(/ /g, padStr);
    }
    return r;
}

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
        $('#btn_generate').on('click', function() {
            $('#form_wwsuKey').val($('#form_rxx_id').val() + '-' + Math.random().toString(36).substr(2, 10));
        })
        $('#btn_copy').on('click', function() {
            var key = $('#form_wwsuKey').val();
            if (key.length) {
                copyToClipboard(key);
                alert('SUCCESS\nCopied key to clipboard.\nNow please SAVE this listener profile to make the key live.');
            } else {
                alert('ERROR\nPlease generate a key first.')
            }
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
            var footerOffset = (COOKIE.get('credits_hide') !== 'yes' ? 74 : 0);
            $('#list').height(($(window).height() / 2) - offset - footerOffset);
            $('#list2').height(($(window).height() / 2) - offset - footerOffset);
            $(window).on('resize', function () {
                var footerOffset = (COOKIE.get('credits_hide') !== 'yes' ? 74 : 0);
                $('#list').height(($(window).height() / 2) - offset - footerOffset);
                $('#list2').height(($(window).height() / 2) - offset - footerOffset);
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
        $('#btn_rxxid_go').click(function() {
            window.open('./listeners/' + $('#rxxid').val() + '/upload', '_blank', popWinSpecs['listeners_[id]_upload']);
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
            if (!confirm(msg.reset + "\n" + msg.cookie.reset)) {
                return false;
            }
            COOKIE.clear('listenersForm', '/');
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

// Used here: http://rxx.classaxe.com/en/rna/listeners/323/locatormap
var LocatorMap = {
    init : function(xpos, ypos) {
        var rx_map = $('#rx_map');
        if (!rx_map.height()) {
            return window.setTimeout(function(){ LocatorMap.init(xpos, ypos); }, 100);
        }
        rx_map.on('click', function (e) {
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

function initListenersLogUploadForm() {
    var std_formats = {
        'pskov' : '  DD-MM-YYYY  hhmm  KHZ    ID    LSB    USB    sec            X',
        'wwsu'  : 'YYYY-MM-DD hh:mm  KHZ     ID        X     QTH',
        'yand'  : 'YYYYMMDD hhmm KHZ ID   X          QTH           X',
        'rxx'   : 'YYYY-MM-DD hh:mm KHZ      ID         X      X  LSB   USB    sec      fmt    PWR    X     X     GSQ    X        X        X SP ITU QTH'
    }
    var formFormat = $('#form_format');
    formFormat.on('keyup', function() {
        $('#form_saveFormat').attr('disabled', $(this).val() === $('#formatOld').text());
    });

    // Detect if we reloaded the page dure to back button being pressed
    if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
        formFormat.trigger('keyup');
    }
    for (var i in std_formats) {
        (function(i) {
            $('#format_' + i).on('click', function() {
                $('#form_format').val(std_formats[i]);
            });
        })(i);
    }

    $('#form_saveFormat').on('click', function() {
        if (confirm(msg.log_upload.prompt.a) === false) {
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
            [ '#form_format', 3, 4 ],
            [ '#form_logs',   5, 6 ],
            [ '#form_YYYY',   7, 8 ],
            [ '#form_MM',     9, 10 ],
            [ '#form_DD',    11, 12 ]
        ];
        logsRemoveBlankLines($('#form_logs'));
        for (f in fields) {
            field = $(fields[f][0]);
            if (!field.is(':visible')) {
                continue;
            }
            if (field.val() === '' || field.val() === msg['log_upload_' + fields[f][2]]) {
                e.preventDefault();
                field.val(msg['log_upload_' + fields[f][2]]);
                alert(msg.error.toUpperCase() + "\n\n" + msg['log_upload_' + fields[f][1]]);
                field.focus().select();
                return false;
            }
        }
        $('#form_selected').val('UNSET');
        $('#form_step').val(2);
    });

    $('#form_back').on('click', function() {
        $('#form_step').val(1);
        $('#form_selected').val('UNSET');
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
            alert(msg.copied_x.replace('%s', txt));
        }).attr('title', msg.copy_token);
        $('.tokensHelp #tokensHelpClose').on('click', function() {
            $('.tokensHelpLink')
                .removeClass('on')
                .tooltip('close');
            return false;
        });
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

    $('table.parse').on('click', 'tr td:gt(1)', function(event) {
        event.stopImmediatePropagation();
        var ctl = $(this).parent().find('input:checkbox');
        ctl.prop('checked', !ctl.prop('checked'));
        ctl.trigger('change');
    });

    $('table.parse input:checkbox').change(function() {
        $('input[data-idx="' + $(this).data()['idx'] + '"]').not(this).prop('checked', false);
        logsShowRemainder();
    });

    $('#form_submitLog').on('click', function(e) {
        var m = msg.log_upload.confirm;
        var remaining_val = $('#remainder_logs').val();
        var remaining = 0;
        var remaining_lines = [];
        if (remaining_val !== '') {
            remaining_lines = remaining_val.split("\n");
            for (i in remaining_lines) {
                if (remaining_lines[i] === '') {
                    continue;
                }
                if (remaining_lines[i].substr(0,2) === '* ') {
                    continue;
                }
                remaining++;
            }
        }
        var message = (remaining ? m[1] + "\n" + m[2].replace('COUNT', remaining) + "\n\n" + m[3] : m[1]);
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
        $('#form_step').val(3);
    });

    $('#copyDetails').on('click', function() {
        var data = ($('#remainder_format').val() + '\n' + $('#remainder_logs').val()).split('\n');
        len = 1 + data.sort(function(a,b){return b.length - a.length})[0].length;
        var txt =
            $('#logEmail').val() + '\n' +
            "-".repeat(len) + '\n' +
            $('#remainder_format').val() + '\n' +
            "-".repeat(len) + '\n' +
            $('#remainder_logs').val().trimEnd() + '\n' +
            "-".repeat(len) + '\n\n';
        copyToClipboard(txt);
        alert(msg.log_upload.copy_remaining);
        return false;
    })

    $('#copyEmail').on('click', function() {
        var data = ($('#remainder_format').val() + '\n' + $('#remainder_logs').val()).split('\n');
        len = 1 + data.sort(function(a,b){return b.length - a.length})[0].length;
        var txt =
            'To:       ' + $('#logEmail').val() + '\n' +
            'Subject:  Issues seen for log upload for ' + $('#logOwner').val() + '\n\n\n' +
            'Dear Listener,\n\n' +
            'Some potential issues were encountered when attempting to upload a submitted log.\n' +
            'Would you please check the following log entries?\n\n' +
            '-' . repeat(len) + '\n' +
            $('#remainder_format').val() + '\n' +
            '-' . repeat(len) + '\n' +
            $('#remainder_logs').val().trimEnd() + '\n' +
            '-' . repeat(len) + '\n\n\n\n' +
            'Sincerely,\n\n\n' +
            $('#userName').val();
        copyToClipboard(txt);
        alert(msg.log_upload.prepare_email);
        return false;
    })

    $('.jump .up').on('click', function() {
        var id = parseInt($(this).parent().attr('id').split('_')[1]);
        var row_id = $('#jump_' + (id - 1)).parent().attr('id').split('_')[1];
        document.getElementById('row_' + (row_id-1)).scrollIntoView({behavior: 'smooth', block: 'start'});
    });

    $('.jump .down').on('click', function() {
        var id = parseInt($(this).parent().attr('id').split('_')[1]);
        var jump = $('#jump_' + (id + 1));
        if (jump.length) {
            var row_id =jump.parent().attr('id').split('_')[1];
            document.getElementById('row_' + (row_id - 1)).scrollIntoView({behavior: 'smooth', block: 'start'});
        } else {
            alert(msg.log_upload.last_item)
        }
    });
    $('#check_good').on('click', function() {
        $('table.parse .good input:checkbox').each(function() {
            $(this).prop('checked', true);
        });
        logsShowRemainder();
        return false;
    })
    $('#check_warning').on('click', function() {
        $('table.parse .warning input:checkbox').each(function() {
            $(this).prop('checked', true);
        });
        logsShowRemainder();
        return false;
    })
    $('#check_choice').on('click', function() {
        var choices, i, path, rows;
        choices = $('table.parse .choice input:checkbox');
        rows = [];
        for (i=0; i<choices.length; i++) {
            var idx = $(choices[i]).data('idx');
            if ('undefined' === typeof rows[idx]) {
                rows[idx] = 0;
            }
            if (!$(choices[i]).parent().parent().hasClass('inactive')) {
                rows[idx]++;
            }
        }
        for (i=0; i<rows.length; i++) {
            if (rows[i] === 1) {
                path = 'tr:not(.inactive) input[type=checkbox][data-idx='+i+']';
                $(path).prop('checked', 'checked');
            }
        }
        logsShowRemainder();
        return false;
    });
    $('#uncheck_warning').on('click', function() {
        $('table.parse .warning input:checkbox').each(function() {
            $(this).prop('checked', false);
        });
        logsShowRemainder();
        return false;
    });
    $('#uncheck_choice').on('click', function() {
        $('table.parse .choice input:checkbox').each(function() {
            $(this).prop('checked', false);
        });
        logsShowRemainder();
        return false;
    });
    $('#uncheck_all').on('click', function() {
        $('table.parse input:checkbox').each(function() {
            $(this).prop('checked', false);
        });
        logsShowRemainder();
        return false;
    });
}
function logsRemoveBlankLines(element) {
    var i, logs, logs_filtered;
    logs = element.val().split("\n");
    logs_filtered = [];
    for (i=0; i < logs.length; i++) {
        if (logs[i].trim() !== '') {
            logs_filtered.push(logs[i]);
        }
    }
    element.val(logs_filtered.join("\n"));

}

function logsShowRemainder() {
    var logs = $('#form_logs').val().split("\n");
    var i;
    var issues;
    var idx;
    var checked = [];
    var selected = [];
    var remainder = [];
    $('table.parse input:checkbox').each(function() {
        if ($(this).is(':checked')) {
            selected.push($(this).val());
            idx = $(this).val().split('|')[0];
            checked[idx] = idx;
        }
    });
    for (i in checked) {
        if (checked.hasOwnProperty(i)) {
            logs[i] = '';
        }
    }
    for (i in logs) {
        if (logs.hasOwnProperty(i)) {
            if (logs[i] === '') {
                continue;
            }
            if (logs[i].substr(0,2) === '* ') {
                continue;
            }
            issues = $('#row_' + i).data('issues');
            remainder.push(logs[i] + (issues ? '\n* ISSUES: ' + issues + '\n' : ''));
        }
    }
    $('#remainder_format').val($('#form_format').val());
    $('#remainder_logs').val(remainder.join("\r\n"));
    $('#form_selected').val(selected.join(','));
    $('#issueCount').text(remainder.length);
}

var LOG_EDIT = {
    init: function() {
        $('#form_save').on('click', function(){
            $('#form_reload').val(1);
        })
        $('#form_saveClose').on('click', function(){
            $('#form_reload').val(1);
            $('#form__close').val(1);
        })
        LOG_EDIT.initListenersSelector(listeners);
        LOG_EDIT.initSignalsSelector(signals);
        LOG_EDIT.initTimeControl();
        COMMON_FORM.setDatePickerActions();
    },

    initListenersSelector: function(data) {
        var element, i, out, r, s;
        element = $('#form_listenerId');
        s  = element.val();
        out = "<select id=\"form_listenerId\" name=\"form[listenerId]\" required=\"required\" size=\"10\">\n";
        for (i in data) {
            r = data[i].split('|');
            out +=
                "<option value='" + r[0] + "'" +
                " data-gsq='" + r[3] + "'" +
                " data-tz='" + r[8] + "'" +
                " class='" + (r[4] === 'Y' ? 'primaryQth' : 'secondaryQth') + "'" +
                (r[0] === s ? " selected='selected'" : '') +
                ">" +
                pad(r[1] + ", " + r[5] + (r[2] ? ' ' + r[2] : ''), (r[4] === '1' ? 60 : 58), '&nbsp;') +
                (r[6] ? ' ' + r[6] : '&nbsp; &nbsp;') +
                ' ' + r[7] +
                "</option>";
        }
        out += "</select>";
        element.replaceWith(out);
        $('#form_' + 'listenerId')
            .on('change', function(){
                LOG_EDIT.getDx();
                LOG_EDIT.getDaytime();
            });
    },

    initSignalsSelector: function(data) {
        var element, i, out, r, s;
        element = $('#form_signalId');
        s  = element.val();
        out = "<select id=\"form_signalId\" name=\"form[signalId]\" required=\"required\" size=\"10\">\n";
        for (i in data) {
            r = data[i].split('|');
            out +=
                "<option value='" + r[0] + "'" +
                (r[5] === '0' ? " title='" + msg.inactive + "'" : '') +
                " class='type_" +r[3] + (r[5] === '0' ? ' inactive' : '') + "'" +
                " data-gsq='" + r[4] + "'" +
                (r[0] === s ? " selected='selected'" : '') +
                ">" +
                pad(parseFloat(r[2]), 10, '&nbsp;') +
                pad(r[1], 10, '&nbsp;') +
                pad(r[6], 41, '&nbsp;') +
                pad(r[7], 3, '&nbsp;') +
                r[8] + ' ' +
                "</option>";
        }
        out += "</select>";
        element.replaceWith(out);
        $('#form_' + 'signalId')
            .on('change', function(){
                LOG_EDIT.getDx();
            })
    },

    initTimeControl: function() {
        element = $('#form_time');
        element
            .on('change', function(){
                LOG_EDIT.getDaytime();
            })
    },

    getDx: function() {
        var dx, qth, qth_element, sig, sig_element;
        qth_element = document.getElementById('form_listenerId');
        qth = qth_element.options[qth_element.selectedIndex].getAttribute('data-gsq');

        sig_element = document.getElementById('form_signalId');
        sig = sig_element.options[sig_element.selectedIndex].getAttribute('data-gsq');

        if (qth === '' || sig === '') {
            return false;
        }
        dx = CONVERT.gsq_gsq_dx(qth, sig);
        $('#form_dxKm').val(dx ? dx.dx_km : '');
        $('#form_dxMiles').val(dx ? dx.dx_miles : '');
    },

    getDaytime: function() {
        var hhmm, isDaytime, tz, tz_element;
        tz_element = document.getElementById('form_listenerId');
        tz = tz_element.options[tz_element.selectedIndex].getAttribute('data-tz');
        hhmm = $('#form_time').val();
        if (hhmm.length !== 4) {
            isDaytime = 0;
        } else {
            isDaytime = (parseInt(hhmm) + 2400 >= (tz * -100) + 3400 && parseInt(hhmm) + 2400 <  (tz * -100) + 3800) ? 1 : 0;
        }
        $('#form_daytime').val(isDaytime);
    }
}


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
                colspan++;
            }
        }
        i = 0;
        this.source.find('tbody tr').each(function () {
            var classname, ele = $(this), html, j;

            html = '<table style="width:100%">';
            for (j in RT.fields) {
                if (!RT.rows[i][RT.fields[j].idx]) {
                    continue;
                }
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
                if (!this.rows[i][this.fields[j].idx]) {
                    continue;
                }
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
            if (!header.hasClass('hidden')) {
                RT.fields.push({
                    idx: idx++,
                    html: header.html().trim().split('<br>')[0],
                    l2: header.hasClass('l2'),
                    rowspan2: header.hasClass('rowspan2'),
                    type: (header.hasClass('th') ? 'th' : 'td')
                });
                RT.classes.push(header.prop('title').trim());
                RT.titles.push(header.prop('title').trim());
            }
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
        return window.location.protocol + '//' + window.location.host + base_url + mode;
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
            ('undefined' !== typeof f2.val() && null !== f2.val() && 0 !== parseInt(f2.val()) ? '&page=' + f2.val() : '');
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
        var types = [], url;
        $("fieldset#form_type div input").each(function() {
            if ($(this).is(':checked') && 'ALL' !== $(this).prop('value')) {
                types.push($(this).prop('value'));
            }
        });
        if (0 === types.length) {
            types = ['NDB'];
        }
        if (7 === types.length) {
            types = ['ALL'];
        }
        url = '&types=' + $.uniqueSort(types).join(',');
        return (url === '&types=NDB' ? '' : url);
    },
    listenersUrl: function(suffix) {
        var base = this.getBaseUrl('listeners');
        var url =
            this.getFromTypes() +
            this.getFromField('q') +
            this.getFromField('region') +
            this.getFromField('country') +
            this.getFromField('rxx_id') +
            this.getFromField('has_logs', [ 'N', 'Y' ], 'A') +
            this.getFromField('has_map_pos', [ 'N', 'Y' ], 'A') +
            (this.getFromField('timezone') !== '&timezone=ALL' ? this.getFromField('timezone') : '') +
            this.getFromField('status', [ 'N', 'Y', '30D', '3M', '6M', '1Y', '2Y', '5Y' ], 'A') +
            this.getFromField('equipment') +
            this.getFromField('notes') +
            this.getFromPagingControls(500) +
            this.getFromSortingControls('name', 'a') +
            (typeof suffix !== 'undefined' ? suffix : '');

        return base + (url.substring(0,1) === '&' ? '?' + url.substring(1) : url);
    },
    signalsUrl: function(suffix) {
        var base = this.getBaseUrl('signals');
        var url =
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
            this.getFromField('recently') +
            this.getFromField('within') +
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

            this.getFromRadioGroup('hidenotes') +
            this.getFromRadioGroup('morse') +
            this.getFromRadioGroup('offsets') +

            this.getFromField('range_gsq') +
            this.getFromField('range_min') +
            this.getFromField('range_max') +
            (this.getFromField('range_gsq') ? this.getFromRadioGroup('range_units') : '') +

            this.getFromRadioGroup('paper', [ 'a4', 'a4_l', 'lgl', 'lgl_l', 'ltr', 'ltr_l' ]) +
            this.getFromField('admin_mode') +
            (typeof suffix !== 'undefined' ? suffix : '');

        return base + (url.substring(0,1) === '&' ? '?' + url.substring(1) : url);
    }
};

function shareListeners() {
    var url = shareableLink.listenersUrl();
    var dialog = $('#dialog');
    dialog
        .html(
            '<p>' + msg.share.listeners.text1 +'<br>' + msg.share.listeners.text2 +'</p>' +
            '<ul>' +
            '<li><a style="color:#0000ff" href="' + url + '">' + msg.share.listeners.links.list + '</a></li>' +
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
            '<p style="margin:0">' + msg.share.signals.text1 +'<br>' + msg.share.signals.text2 +'</p>' +
            '<ul>' +
            '<li><a style="color:#0000ff" href="' + url + '">' + msg.share.signals.links.list + '</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=seeklist">' + msg.share.signals.links.seeklist + '</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=map">' + msg.share.signals.links.map + '</a></li>' +
            '</ul>' +
            '<p style="margin:0"><strong>' + msg.share.signals.links.export + '</strong></p>' +
            '<ul style="margin-bottom:0">' +
            '<li><a style="color:#0000ff" href="' + url + '&show=csv">signals.csv</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=kml">signals.kml</a></li>' +
            '<li><a style="color:#0000ff" href="' + url + '&show=txt">signals.txt</a></li>' +
            '</ul>')
        .dialog({
            buttons: [{
                text: msg.close,
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

var SIGNALS_FORM = {
    init : function(resultsCount) {
        $(document).ready( function() {
            var c = COMMON_FORM;
            var s = SIGNALS_FORM;
            s.setPersonaliseAction();
            s.setNotesAction();
            s.setMorseAction();
            s.setOffsetsAction();
            s.setRangeAction();
            s.setRangeUnitsDefault();
            s.setHeardIn();
            s.setSortByAction();
            s.setSortZaAction();
            s.setPaperSizeAction();

            s.setRwwFocusAction();
            c.setTypesStyles();
            c.setTypesDefault();
            c.setTypesAllAction();
            s.setStatesLabelLink();
            s.setCountriesLabelLink();
            c.setRegionAction();
            s.setAdminAction();

            s.setListenerInvertDefault();
            s.setHeardInModDefault();
            s.setListenerOptionsStyle();
            s.setCollapsableSections();
            s.setSaveAction();
            s.setResetAction();

            c.setDatePickerActions();
            if (resultsCount !== false) {
                c.setPagingControls();
                c.setPagingStatus(msg.paging_s, resultsCount);
            }

            s.setActions();
            s.setIdentTip();
            s.setFocusOnCall();
            s.showStats();

        });
    },

    ituSps: {
        AUS : 'AT NN NW QD SA TA VI WE',
        CAN : 'AB BC MB NB NL NS NT NU ON PE QC SK YT',
        USA : 'AL AR AZ CA CO CT DC DE FL GA IA ID IL IN KS KY LA MA MD ME MI MN MO MS MT NC ND NE NH NJ NM NV NY OH OK OR PA RI SC SD TN TX UT VA VT WA WI WV WY'
    },

    setActions : function() {
        $('#btn_csv_all').click(function () {
            if (confirm(
                msg.export
                    .replace(':system', system.toUpperCase())
                    .replace(':format', '.csv') +
                "\n" + msg.export2
            )) {
                window.location.assign(window.location + '/export/csv' + shareableLink.getFromTypes());
            }
        });
        $('#btn_kml_all').click(function () {
            if (confirm(
                msg.export
                    .replace(':system', system.toUpperCase())
                    .replace(':format', '.kml') +
                "\n" + msg.export2
            )) {
                window.location.assign(window.location + '/export/kml' + shareableLink.getFromTypes());
            }
        });
        $('#btn_txt_all').click(function () {
            shareableLink.getFromTypes()
            if (confirm(
                msg.export
                    .replace(':system', system.toUpperCase())
                    .replace(':format', '.txt') +
                "\n" + msg.export2
            )) {
                window.location.assign(window.location + '/export/txt' + shareableLink.getFromTypes());
            }
        });
        $('#btn_psk_all').click(function () {
            if (confirm(
                msg.export
                    .replace(':system', system.toUpperCase())
                    .replace(':format', 'PSKOV') +
                "\n" + msg.export2 +
                (system.toUpperCase() === 'RWW' ? '' : "\n\n" + msg.export3)
            )) {
                window.location.assign(window.location + '/export/xls' + shareableLink.getFromTypes());
            }
        });
        $('#btn_csv_fil').click(function () {
            var filename = prompt('Filename', system + '_signals.csv');
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
        $('#btn_kml_fil').click(function () {
            var filename = prompt('Filename', system + '_signals.kml');
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
        $('#btn_txt_fil').click(function () {
            var filename = prompt('Filename', system + '_signals.txt');
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
        $('form[name="form"]').on('submit', function() {
            $('#form_call').val($('#form_call').val().toUpperCase());
        });
    },

    setAdminAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('select#form_admin_mode').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_admin_mode').off('change');
        }
    },

    setCollapsableSections : function() {
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
    },

    setCountriesLabelLink : function() {
        var ele = $('label[for="form_countries"]');
        ele.html('<a href="countries/*" data-popup="1">' + ele.html() + '</a>');
    },

    setFocusOnCall : function() {
        var f = $('#form_call');
        f.focus();
        f.select();
    },

    setIdentTip : function() {
        var frmCall = $('#form_call');
        if (frmCall.val() !== '') {
            var tip = $('#exact');
            tip.html(tip.html().replace('%s', "<b>'" + frmCall.val() + "'</b>"))
            tip.show();
        }
    },

    setHeardIn : function() {
        $('#form_heard_in').on('keyup', function(){
            $('#form_heard_in').val(function (_, val) {
                return val.toUpperCase();
            });
            $.each(SIGNALS_FORM.ituSps, function(itu, sps){
                var field = $('#form_heard_in')
                var heardIn = field.val();
                if (heardIn.indexOf(itu) >= 0) {
                    alert('Country code ' + itu + ' will be expanded to show all states, provinces and territories');
                    field.val(field.val().replace(itu, sps));
                }
            })
        });
    },

    setHeardInModDefault : function() {
        if ($('fieldset#form_heard_in_mod div :radio:checked').length === 0) {
            $('fieldset#form_heard_in_mod div :radio[value="any"]').prop('checked', true);
        }
    },

    setListenerInvertDefault : function() {
        if ($('fieldset#form_listener_invert div :radio:checked').length === 0) {
            $('fieldset#form_listener_invert div :radio[value=0]').prop('checked', true);
        }
    },

    setListenerOptionsStyle: function() {
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
    },

    setMorseAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('#form_morse').change(function () {
                formSubmit();
            });
        } else {
            $('#form_morse').off('change');
        }
    },

    setNotesAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('#form_hidenotes').change(function () {
                formSubmit();
            });
        } else {
            $('#form_hidenotes').off('change');
        }
    },

    setOffsetsAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('#form_offsets').change(function () {
                formSubmit();
            });
        } else {
            $('#form_offsets').off('change');
        }
    },

    setPersonaliseAction : function(enable) {
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
    },

    setRangeAction : function() {
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
    },

    setRangeUnitsDefault : function() {
        if ($('fieldset#form_range_units div :radio:checked').length === 0) {
            $('fieldset#form_range_units div :radio[value=km]').prop('checked', true);
        }
    },

    setRwwFocusAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('select#form_rww_focus').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_rww_focus').off('change');
        }
    },

    setResetAction : function() {
        $('button[type="reset"]').click(function () {
            if (!confirm(msg.reset + "\n" + msg.cookie.reset)) {
                return false;
            }
            COOKIE.clear('signalsForm', '/');
            var c = COMMON_FORM;
            var s = SIGNALS_FORM;
            var form_range_gsq = $('#form_range_gsq');
            var form_range_min = $('#form_range_min');
            s.setAdminAction(false);
            c.setRegionAction(false);
            s.setRwwFocusAction(false);
            s.setNotesAction(false);
            s.setMorseAction(false);
            s.setOffsetsAction(false);
            s.setPersonaliseAction(false);

            $('#form_show').val('');
            $('fieldset#form_type div :checkbox').prop('checked', false);
            $('fieldset#form_type div :checkbox[value=NDB]').prop('checked', true);
            $('#form_call').val('');
            $('#form_khz_1').val('');
            $('#form_khz_2').val('');
            $('#form_channels').prop('selectedIndex', 0);
            $('#form_active').prop('selectedIndex', 0);
            $('#form_recently').prop('selectedIndex', 0);
            $('#form_within').prop('selectedIndex', 0);
            $('#form_personalise').prop('selectedIndex', 0);
            $('#form_morse_0').prop('checked', 1);
            $('#form_hidenotes_1').prop('checked', 1);
            $('#form_offsets_0').prop('checked', 1);

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

            s.setPersonaliseAction(true);
            s.setMorseAction(true);
            s.setNotesAction(true);
            s.setOffsetsAction(true);
            s.setAdminAction(true);
            c.setRegionAction(true);
            s.setRwwFocusAction(true);
            formSubmit();
            return false;
        });
    },

    setSaveAction: function() {
        $('#form_save').click(function(){
            if (confirm(msg.cookie.save + "\n" + msg.cookie.usesCookie)) {
                var value = shareableLink.signalsUrl().split('?')[1];
                COOKIE.set('signalsForm', value, '/');
                alert(msg.cookie.saved);
            }
        });
    },

    setPaperSizeAction: function() {
        $('#seeklist_paper')
            .change(function() {
                $('#form_paper').val($('#seeklist_paper option:selected').val());
                formSubmit();
            });
    },

    setSortByAction: function() {
        $('select#form_sortby').change(function() {
            var val = $("#form_sortby option:selected").val();
            $('#form_sort').val(val.split('|')[0]);
            $('#form_order').val(val.split('|')[1]);
            $('#form_za').prop('checked', val.split('|')[1] === 'd');
            formSubmit();
        });
    },

    setSortZaAction : function() {
        $('input#form_za').change(function () {
            $('#form_order').val($('input#form_za').prop('checked') ? 'd' : 'a');
            formSubmit();
        });
    },

    setStatesLabelLink : function() {
        var ele = $('label[for="form_states"]');
        ele.html('<a href="states/*" data-popup="1">' + ele.html() + '</a>');
    },

    showStats : function() {
        var rwwFocus = $('#form_rww_focus')
        url = base_url +
            'signals/stats' +
            (typeof rwwFocus.val() !== 'undefined' ? '?rww_focus=' + rwwFocus.val() : '');
        $.get(url, function(data) {
            $.each(data.signals, function(key, value){
                $('#stats_' + key).text(value.numberFormat());
            })
            $('#stats_focus').text(data.listeners.focus);
            $('#stats_locations').text(data.listeners.locations.numberFormat());
            $('#stats_logs').text(data.listeners.logs.numberFormat());
            $('#stats_first').text(data.listeners.first);
            $('#stats_last').text(data.listeners.last);
            $('#seeklist_last').text(data.listeners.last);
        });
    }
}

var SIGNALS = {
    loadList: function(args) {
        var url = shareableLink.signalsUrl('&show=list');
        console.log(url);
        console.log(args);
        $.get(url, function(data) {
            var c, cols, html, i, id, j, key, row, s, tde, tds, title, value;
            html = [];
            paging = data.results;  // Global object set in paging.html.twig
            cols = data.columns;

            SIGNALS.setHeadingTitle(data);
            SIGNALS.setHeadingPersonalise(data);
            COMMON_FORM.setPagingControls();
            COMMON_FORM.setPagingStatus(msg.paging_s, paging.total);
            $('#signalDetails').addClass('line')
            $('#paging').show();
            html.push('<tr>');
            if (data.personalise.id) {
                html.push('<th class="txt_vertical nosort rowspan2 th"><div>Logged</div></th>');
            }
            for (j = 0; j < cols.length; j++) {
                c = cols[j];
                if (c.key === 'morse' && args.morse !== 1) {
                    console.log(args)
                    continue;
                }
                if (c.key === 'notes' && args.hidenotes !== 0) {
                    console.log(args)
                    continue;
                }
                html.push(
                    '  <th' +
                    (c.key && c.order ? ' id="' + c.key + '|' + c.order + '"' : '') +
                    (c.th_class || c.sort ? ' class="' + c.th_class  + (c.sort ? ' sort' : '') + '"' : '') +
                    (c.tooltip ? ' title="' + c.tooltip + '"' : '') + '>' +
                    (c.th_class === 'txt_vertical' ? '<div>' + c.label + '<\/div>' : c.label) +
                    '</th>'
                );
            }
            html.push('<\/tr>\n');
            $('.signal.results thead').html(html.join('\n'));
            html = [];
            for (i = 0; i<data.types.length; i++) {
                $('#ref_type_' + data.types[i]).show();
            }
            if (data.signals.length === 0) {
                $( "#signals_list").html(
                    "<tr><th class='no-results' colspan='" + cols.length + "'>" + "No signals found matching your criteria" + "</th></tr>"
                );
                return;
            }
            for (i = 0; i<data.signals.length; i++) {
                s = data.signals[i];
                row = '<tr class="' +
                    (s.active === '0' ? 'inactive ' : '') + args.types[s.type].classname +
                    (data.personalise.id ? (s.personalise === '0' ? '' : 'un') + 'logged' : '') + '"' +
                    ' title="' + args.types[s.type].title + (s.active === '0' ? ' (' + msg.inactive + ')' : '' ) + '">' +
                    (data.personalise.id ?
                            '<th title="' + (s.personalise === '0' ? msg.unlogged_by : msg.logged_by) + '" class="rowspan2">' +
                            (s.personalise === '1' ? '&#x2714;' : '&nbsp;') +
                            '</th>'
                            :
                            ''
                    );
                for (j = 0; j < cols.length; j++) {
                    c = cols[j];

                    value = s[c.key];
                    id = s['ID'];
                    tds = '<td' + (c.td_class ? ' class="' + c.td_class + '"' : '') + '>';
                    tde = '</td>';
                    switch (c.key) {
                        case 'call':
                            row += tds + '<a href="' + args.urls.profile.replace('*', id) + '" data-popup="1">' + value + '</a>' + tde;
                            break;
                        case 'delete':
                            row += tds + '<a href="' + args.urls.delete.replace('*', id) + '" class="delete" onclick="return confirm(msg.del_signal)">X</a>' + tde;
                            break;
                        case 'GSQ':
                            row += tds + (value !=='' ? '<a data-gsq="' + id + '">' + value + '</a>' : '') + tde;
                            break;
                        case 'first_heard':
                        case 'last_heard':
                            row += tds + (value !== null ? value : '') + tde;
                            break;
                        case 'heard_in':
                            row += tds + s['heard_in_html'] + tde;
                            break;
                        case 'ITU':
                            row += tds + (value !=='' ? '<a data-set="itu">' + value + '</a>' : '') + tde;
                            break;
                        case 'khz':
                            row += tds + parseFloat(value) + tde;
                            break;
                        case 'listeners':
                            row += tds + (value !=='0' ? '<a href="' + args.urls.listeners.replace('*', id) + '" data-popup="1">' : '') + value + '</a>' + tde;
                            break;
                        case 'logs':
                            row += tds + (value !=='0' ? '<a href="' + args.urls.logs.replace('*', id) + '" data-popup="1">' : '') + value + '</a>' + tde;
                            break;
                        case 'LSB':
                        case 'USB':
                            row += tds + (parseFloat(value) ? (data.args.offsets ? parseFloat(value).toFixed(3) : value) : '') + tde;
                            break;
                        case 'merge':
                            row += tds + '<a href="' + args.urls.merge.replace('*', id) + '" class="merge">M</a>' + tde;
                            break;
                        case 'morse':
                            if (args.morse === 1) {
                                row += tds + (s.morse !=='' ? encodeMorse(s.morse) : '?') + tde;
                            }
                            break;
                        case 'notes':
                            if (args.hidenotes !== 1) {
                                row += tds + s.notes + tde;
                            }
                            break;
                        case 'pwr':
                            row += tds + (value !== '0'  ? value : '') + tde;
                            break;
                        case 'SP':
                            row += tds + (value !=='' ? '<a data-set="sp">' + value + '</a>' : '') + tde;
                            break;
                        case 'type':
                            break;
                        default:
                            row += tds + value + tde;
                            break;
                    }
                }
                row += "</tr>";
                html.push(row);
            }
            $('#signals_list').html( html.join('\n'));
            setColumnSortActions();
            setColumnSortedClass();
            setExternalLinks();
            scrollToResults()
            RT.init($('#wide'), $('#narrow'));
        });
    },
    loadMap: function() {
        $('#signalDetails').addClass('line')
        $('#paging').show();
    },
    loadSeeklist: function() {
        $('#signalDetails').removeClass('line')
        $('#paging').hide();
    },
    setHeadingPersonalise: function(data) {
        if (!data.personalise.name) {
            return;
        }
        $('#signals_personalise').html(
            msg.signals.personalise.replace(
                '%s',
                '<a href="' + args.urls.listeners.replace('*', data.personalise.id) + '" data-popup="1">' + data.personalise.name + "<\/a>"
            )
        );
    },
    setHeadingTitle: function(data) {
        switch(data.title) {
            case 1:
                title = msg.signals.title.unlogged;
                break;
            case 2:
                title = msg.signals.title.both;
                break;
            default:
                title = msg.signals.title.normal.replace('%s', system.toUpperCase());
                break;
        }
        $('#signals_title').html(title);
    }
}

var SIGNAL_MERGE = {
    init: function() {
        $('#form_save').on('click', function(){
            $('#form_reload').val(1);
        })
        $('#form_saveClose').on('click', function(){
            $('#form_reload').val(1);
            $('#form__close').val(1);
        })
        SIGNAL_MERGE.initSignalsSelector(signals);
    },

    initSignalsSelector: function(data) {
        var element, i, out, r, s;
        element = $('#form_signalId');
        s  = element.val();
        out = '<select id="form_signalId" name="form[signalId]" required="required" size="10">\n';
        for (i in data) {
            r = data[i].split('|');
            out +=
                "<option value='" + r[0] + "'" +
                (r[5] === '0' ? " title='" + msg.inactive + "'" : '') +
                " class='type_" +r[3] + (r[5] === '0' ? ' inactive' : '') + "'" +
                " data-gsq='" + r[4] + "'" +
                (r[0] === s ? " selected='selected'" : '') +
                ">" +
                pad(parseFloat(r[2]), 10, '&nbsp;') +
                pad(r[1], 10, '&nbsp;') +
                pad(r[6], 41, '&nbsp;') +
                pad(r[7], 3, '&nbsp;') +
                r[8] + ' ' +
                "</option>";
        }
        out += "</select>";
        element.replaceWith(out);
        $('#form_' + 'signalId')
            .on('change', function(){
                LOG_EDIT.getDx();
            })
    },
}


var DGPS = {
    init: function() {
        $('#frm_dgps').on('submit', function() {
            $('#dgps_details').val(DGPS.lookup($('#dgps_ref').val()));
            return false;
        });
        $('a[data-dgps]').on('click', function() {
            $('#dgps_ref').val($(this).data('dgps'));
            $('#dgps_go').trigger('click');
            return false;
        });
        $('#dgps_ref').on('focus', function() {
            $(this).select();
        });
        $('#close').on('click', function(){
            window.close();
        })
    },
    a: function(id1, id2, ref, khz, bps, qth, sta, cnt, act) {
        if (typeof this.entries[id1] == 'undefined') {
            this.entries[id1] =		[];
        }
        this.entries[id1].push([ ref, khz, bps, qth, sta, cnt, id1, id2, act ]);
        if (typeof this.entries[id2] == 'undefined') {
            this.entries[id2] =		[];
        }
        this.entries[id2].push([ ref, khz, bps, qth, sta, cnt, id1, id2, act ]);

    },
    entries: [],
    lookup: function(id) {
        var out = [];
        if (id === '') {
            return '';
        }
        if (typeof this.entries[parseFloat(id)] === 'undefined') {
            return msg.tools.dgps.nomatch;
        }
        id = parseFloat(id);
        for (var i=0; i < this.entries[id].length; i++) {
            var a =	this.entries[id][i];
            out.push(
                '  Station ' + a[0] + (a[8] === 0 ? ' ' + msg.tools.dgps.inactive : '') + "\n" +
                '  ' + a[1] + 'KHz ' + a[2] + 'bps' + "\n" +
                '  ' + a[3] + ' ' + a[4] + ' ' + a[5] + "\n" +
                '  Reference ID(s): ' + a[6] + (a[6] !== a[7] ? ', ' + a[7] : '')
            );
        }
        if (i>1) {
            return msg.tools.dgps.multiple + ' (' + i + "):\n"+out.join("\n\n");
        }
        return out.join("");
    }
}
var CONVERT = {
    deg_dms: function(lat_dec, lon_dec) {
        var lat_abs, lat_dd, lat_h, lat_mm, lat_ss, lon_abs, lon_dd, lon_h, lon_mm, lon_ss;
        if (!VALIDATE.dec_lat(lat_dec) || !VALIDATE.dec_lon(lon_dec)) {
            return false;
        }
        lat_h =     (lat_dec > 0 ? 'N' : 'S')
        lat_abs =   Math.abs(lat_dec);
        lat_dd =    Math.floor(lat_abs);
        lat_mm =    lead(Math.floor(60*(lat_abs%1)));
        lat_ss =    lead(Math.floor((lat_abs-lat_dd-(lat_mm/60))*3600));
        lon_h =     (lon_dec > 0 ? 'E' : 'W')
        lon_abs =   Math.abs(lon_dec);
        lon_dd =    Math.floor(lon_abs);
        lon_mm =    lead(Math.floor(60*(lon_abs%1)));
        lon_ss =    lead(Math.floor((lon_abs-lon_dd-(lon_mm/60))*3600));
        return {
            lat_dd_mm_ss: lat_dd + '.' + lat_mm + '.' + lat_ss + '.' + lat_h,
            lon_dd_mm_ss: lon_dd + '.' + lon_mm + '.' + lon_ss + '.' + lon_h
        };
    },
    deg_gsq: function(lat_dec, lon_dec) {
        var lat, lat_a, lat_b, lat_c, lon, lon_a, lon_b, lon_c;
        var letters = "abcdefghijklmnopqrstuvwxyz";
        if (!VALIDATE.dec_lat(lat_dec) || !VALIDATE.dec_lon(lon_dec)) {
            return false;
        }
        lat =   parseFloat(lat_dec) + 90;
        lat_a = letters.charAt(Math.floor(lat / 10)).toUpperCase();
        lat_b = Math.floor(lat % 10);
        lat_c = letters.charAt(Math.floor(24 * (lat % 1)))
        lon =   (parseFloat(lon_dec) + 180) / 2;
        lon_a = letters.charAt(Math.floor(lon / 10)).toUpperCase();
        lon_b = Math.floor(lon % 10);
        lon_c = letters.charAt(Math.floor(24 * (lon % 1)))
        return (lon_a + lat_a + lon_b + lat_b + lon_c + lat_c);
    },
    dms_deg: function(lat_dd_mm_ss, lon_dd_mm_ss) {
        var a, dec_lat, dec_lon, deg, hem, min, rexp_lat, rexp_lon, result, sec

        rexp_lat =  /([0-9]+)[^0-9]([0-9]+)[^0-9]([0-9]+)[^0-9]([NS])/i;
        rexp_lon =  /([0-9]+)[^0-9]([0-9]+)[^0-9]([0-9]+)[^0-9]([EW])/i;

        a =         lat_dd_mm_ss.match(rexp_lat);
        deg =       parseFloat(a[1]);
        min =       parseFloat(a[2]);
        sec =       (a[3] !== '' ? parseFloat(a[3]) : 0);
        hem =       (typeof a[4] === 'undefined' ? 1 : (a[4] === 'N' || a[4] === 'n' ? 1 : -1));
        dec_lat =   hem * (deg + (Math.round(((sec / 3600) + (min / 60)) * 10000)) / 10000);

        a =         lon_dd_mm_ss.match(rexp_lon);
        deg =       parseFloat(a[1]);
        min =       parseFloat(a[2]);
        sec =       (a[3] !== '' ? parseFloat(a[3]) : 0);
        hem =       (typeof a[4] === 'undefined' ? 1 : (a[4] === 'E' || a[4] === 'e' ? 1 : -1));
        dec_lon =   hem * (deg + (Math.round(((sec / 3600) + (min / 60)) * 10000)) / 10000);

        return { lat: dec_lat, lon: dec_lon };
    },
    gsq_deg: function(GSQ) {
        var lat, lat_d, lat_m, lat_s, lon, lon_d, lon_m, lon_s, offset;
        if (!VALIDATE.gsq(GSQ)) {
            return false;
        }
        GSQ = GSQ.toUpperCase();
        offset = (GSQ.length === 6 ? 1/48 : 0);
        GSQ = GSQ + (GSQ.length === 4 ? 'MM' : '');
        lon_d = GSQ.charCodeAt(0)-65;
        lon_m = parseFloat(GSQ.substr(2,1));
        lon_s = GSQ.charCodeAt(4)-65;
        lat_d = GSQ.charCodeAt(1)-65;
        lat_m = parseFloat(GSQ.substr(3,1));
        lat_s = GSQ.charCodeAt(5)-65;
        lon = Math.round((2 * (lon_d * 10 + lon_m + lon_s / 24 + offset) - 180) * 10000) / 10000;
        lat = Math.round((lat_d * 10 + lat_m + lat_s / 24 + offset - 90) * 10000) / 10000;
        return { lat: lat, lon: lon };
    },
    gsq_gsq_dx: function(gsq1, gsq2) {
        var out = { 'dx_km': null, 'dx_miles': null };
        var deg2rad, lat1, lat2, lon1, lon2
        gsq1 = CONVERT.gsq_deg(gsq1);
        gsq2 = CONVERT.gsq_deg(gsq2);

        if (!gsq1 || !gsq2) {
            return false;
        }

        lat1 = parseFloat(gsq1.lat);
        lon1 = parseFloat(gsq1.lon);

        lat2 = parseFloat(gsq2.lat);
        lon2 = parseFloat(gsq2.lon);

        deg2rad = Math.PI / 180;
        lat1 *= deg2rad;
        lon1 *= deg2rad;
        lat2 *= deg2rad;
        lon2 *= deg2rad;
        var diam = 12742; // Diameter of the earth in km (2 * 6371)
        var dLat = lat2 - lat1;
        var dLon = lon2 - lon1;
        var a = (
            (1 - Math.cos(dLat)) +
            (1 - Math.cos(dLon)) * Math.cos(lat1) * Math.cos(lat2)
        ) / 2;

        return {
            'dx_km' :       Math.round(12742 * Math.asin(Math.sqrt(a))),
            'dx_miles' :    Math.round(7917.5 * Math.asin(Math.sqrt(a))),
        }
    }
}
var VALIDATE = {
    gsq: function(value) {
        return value.match(/^([a-rA-R]{2})([0-9]{2})([a-xA-X]{2})?$/i);
    },
    dec_lat: function(value) {
        return (!(isNaN(value) || value >= 90 || value <= -90));
    },
    dec_lon: function(value) {
        return (!(isNaN(value) || value >= 180 || value <= -180));
    },
    dms_lat: function(value) {
        return value.match(/^([0-9]{1,2})[^0-9]([0-5][0-9])[^0-9]([0-5][0-9])[^0-9]?([NS])?$/i);
    },
    dms_lon: function(value) {
        return value.match(/^([0-9]{1,3})[^0-9]([0-5][0-9])[^0-9]([0-5][0-9])[^0-9]?([EW])?$/i);
    },
    float: function(value, min, max, field) {
        var msg = "Please enter a number"
        var checkVal = parseFloat(value)

        if ( isNaN(checkVal) ) {
            alert(field + ":\n" + msg);
            return false;
        }
        if (checkVal < min) {
            alert(field + ":\n" + msg + " >= " + min)
            return false;
        }
        if (checkVal > max) {
            alert(field + ":\n" + msg + " <= " + max)
            return false;
        }
        return true
    },
    int: function(value, min, max, field) {
        var checkVal = parseInt(value)
        var msg = "Please enter a number"

        if ( isNaN(checkVal) ) {
            alert(field + ":\n" + msg);
            return false;
        }
        if (checkVal < min) {
            alert(field + ":\n" + msg + " >= " + min);
            return false
        }
        if (checkVal > max) {
            alert(field + ":\n" + msg + " <= " + max);
            return false;
        }
        return true;
    },
}

var COORDS = {
    init: function() {
        var cmd_1, cmd_2, cmd_3, idx, modes;
        modes = [
            ['dms_deg', 'deg_gsq', 'copy_dms'],
            ['deg_dms', 'deg_gsq', 'copy_deg'],
            ['gsq_deg', 'deg_dms', 'copy_gsq']
        ];
        for (idx = 0; idx < modes.length; idx++) {
            cmd_1 = 'COORDS.' + modes[idx][0] + '();';
            cmd_2 = 'COORDS.' + modes[idx][1] + '();';
            cmd_3 = 'COORDS.' + modes[idx][2] + '();';
            (function (i, c1, c2, c3) {
                $('#go_' + i).on('click', function () {
                    if (eval(c1)) {
                        eval(c2);
                    }
                    return false;
                });
                $('#map_' + i).on('click', function () {
                    if (eval(c1)) {
                        eval(c2);
                        COORDS.map('map');
                    }
                    return false;
                });
                $('#photo_' + i).on('click', function () {
                    if (eval(c1)) {
                        eval(c2);
                        COORDS.map('photo');
                    }
                    return false;
                });
                $('#copy_' + i).on('click', function() {
                    if (eval(c1)) {
                        eval(c2);
                        eval(c3);
                    }
                    return false;
                })

            })(idx + 1, cmd_1, cmd_2, cmd_3);
        }
        $('#close').on('click', function(){
            window.close();
        })
    },
    map: function(mode) {
        var field_lat, field_lon, hd, lat, lon, url;
        field_lat = $('#lat_dddd');
        field_lon = $('#lon_dddd');
        field_lat.val(field_lat.val().trim());
        field_lon.val(field_lon.val().trim());
        lat = field_lat.val();
        lon = field_lon.val();
        if (lat === '' || lon === '') {
            return;
        }
        url = base_url + "maps/coords/" + lat + "/" + lon + "/" + mode;
        hd = '_' + lat + '_' + lon;
        window.open(url, hd, 'scrollbars=1,resizable=1,location=1,width=860,height=630');
    },
    copy_dms: function(){
        copyToClipboard($('#lat_dd_mm_ss').val() + ', ' + $('#lon_dd_mm_ss').val());
        alert(msg.copied_x.replace('%s', 'DMS Coordinates'));
    },
    copy_deg: function(){
        copyToClipboard($('#lat_dddd').val() + ', ' + $('#lon_dddd').val());
        alert(msg.copied_x.replace('%s', 'Decimal Coordinates'));
    },
    copy_gsq: function(){
        copyToClipboard($('#gsq').val());
        alert(msg.copied_x.replace('%s', 'GSQ'));
    },
    deg_gsq: function(){
        var field_lat, field_lon, lat, lon;
        field_lat = $('#lat_dddd');
        field_lon = $('#lon_dddd');
        field_lat.val(field_lat.val().trim());
        field_lon.val(field_lon.val().trim());
        lat = field_lat.val();
        lon = field_lon.val();
        if (!VALIDATE.dec_lat(lat)) {
            alert(msg.tools.coords.lat_dec);
            return false;
        }
        if (!VALIDATE.dec_lon(lon)) {
            alert(msg.tools.coords.lon_dec);
            return false;
        }
        $('#gsq').val(CONVERT.deg_gsq(lat, lon));
    },
    gsq_deg: function() {
        var field_gsq, gsq, result;
        field_gsq = $('#gsq');
        field_gsq.val(field_gsq.val().trim());
        gsq = field_gsq.val().trim();
        if (!VALIDATE.gsq(gsq)) {
            alert(msg.tools.coords.gsq_format);
            return false;
        }
        result = CONVERT.gsq_deg(gsq);
        $('#lat_dddd').val(result.lat);
        $('#lon_dddd').val(result.lon);
        return true;
    },
    deg_dms: function() {
        var dec_lat, dec_lon, result;
        dec_lat =	parseFloat($('#lat_dddd').val());
        dec_lon =	parseFloat($('#lon_dddd').val());
        if (!VALIDATE.dec_lat(dec_lat)) {
            alert(msg.tools.coords.lat_dec);
            return false;
        }
        if (!VALIDATE.dec_lon(dec_lon)) {
            alert(msg.tools.coords.lon_dec);
            return false;
        }
        result = CONVERT.deg_dms(dec_lat, dec_lon);
        $('#lat_dd_mm_ss').val(result.lat_dd_mm_ss);
        $('#lon_dd_mm_ss').val(result.lon_dd_mm_ss);
        return true;
    },
    dms_deg: function() {
        var result, lat_dd_mm_ss, lon_dd_mm_ss;
        lat_dd_mm_ss = $('#lat_dd_mm_ss').val();
        lon_dd_mm_ss = $('#lon_dd_mm_ss').val();
        if (!VALIDATE.dms_lat(lat_dd_mm_ss)) {
            alert(
                msg.tools.coords.lat_dms_1 +
                "\n  DD\xB0MM'SS\"H\n  DD.MM.SS.H\n  DD MM SS H\n  DD\xB0MM.H\n  DD.MM.H\n  DD MM H\n\n" +
                msg.tools.coords.lat_dms_2
            );
            return false;
        }
        if (!VALIDATE.dms_lon(lon_dd_mm_ss)) {
            alert(
                msg.tools.coords.lon_dms_1 +
                "\n  DDD\xB0MM'SS\"H\n  DDD.MM.SS.H\n  DDD MM SS H\n  DDD.MM.H\n  DDD\xB0MM.H\n  DDD MM H\n\n" +
                msg.tools.coords.lon_dms_2
            );
            return false;
        }
        result = CONVERT.dms_deg(lat_dd_mm_ss, lon_dd_mm_ss);
        $('#lat_dddd').val(result.lat);
        $('#lon_dddd').val(result.lon);
        return true;
    }
}

var NAVTEX = {
    init: function() {
        $('#frm_navtex').on('submit', function(){
            return false;
        });
        $('#translateMumbo').on('click', function() {
            $('#navtex2').val(NAVTEX.mumboToText($('#navtex1').val()));
        });
        $('#clearoutMumbo').on('click', function() {
            $('#navtex1').val('');
        });
        $('#clearoutText').on('click', function() {
            $('#navtex2').val('');
        });
        $('#clearoutAll').on('click', function() {
            $('#navtex1').val('');
            $('#navtex2').val('');
        });
        $('#translateText').on('click', function() {
            $('#navtex1').val(NAVTEX.textToMumbo($('#navtex2').val()));
        });
        $('#close').on('click', function(){
            window.close();
        })
    },
    mumboChars: "-?:$3!&#8*().,9014'57=2/6+",
    textChars:  "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
    mumboChar: function(chars) {
        var pos = NAVTEX.textChars.indexOf(chars);
        if (pos > -1) {
            return NAVTEX.mumboChars.charAt(pos);
        }
        return chars;
    },
    textChar: function(chars) {
        var pos = NAVTEX.mumboChars.indexOf(chars);
        if (pos > -1) {
            return NAVTEX.textChars.charAt(pos);
        }
        return chars;
    },
    textToMumbo: function(input) {
        var i, output = '';
        for (i=0; i<input.length; i++) {
            output += NAVTEX.mumboChar(input.charAt(i).toUpperCase());
        }
        return output;
    },
    mumboToText: function(input) {
        var i, output = '';
        for (i = 0; i < input.length; i++) {
            output += NAVTEX.textChar(input.charAt(i));
        }
        return output;
    }
}
var COOKIE = {
    clear: function(which, path) {
        document.cookie =
            which +
            '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=' +
            ('string' === typeof path ? path : '/');
    },
    get: function(which) {
        var cookies =		document.cookie;
        var pos =		cookies.indexOf(which+"=");
        if (pos === -1) {
            return false;
        }
        var start =	pos + which.length+1;
        var end =	cookies.indexOf(";",start);
        if (end === -1) {
            end =	cookies.length;
        }
        return unescape(cookies.substring(start, end));
    },
    set: function(which, value, path) {
        var nextYear =	new Date();
        nextYear.setFullYear(nextYear.getFullYear()+1);
        document.cookie =
            which +
            '=' + value + ';expires=' + nextYear.toGMTString() + '; path=' +
            ('string' === typeof path ? path : '/');
    },
}
var SUNRISE = {
    // Refactored from code created by Gene Davis - Computer Support Group: Dec 6 1994 - Oct 8 2001
    init: function() {
        var now = new Date();
        var sunrise_clear = $('#sunrise_clear');
        var sunrise_go =    $('#sunrise_go');
        var sunrise_gsq =   $('#sunrise_gsq');
        var sunrise_lat =   $('#sunrise_lat');
        var sunrise_lon =   $('#sunrise_lon');
        var sunrise_DD =    $('#sunrise_DD');
        var sunrise_MM =    $('#sunrise_MM');
        var sunrise_YYYY =  $('#sunrise_YYYY');

        $('#close').on('click', function(){
            window.close();
        })
        sunrise_YYYY
            .on('change', function() {
                VALIDATE.int(this, 1901, 2099, 'Year')
            })
            .val(now.getUTCFullYear());
        sunrise_MM
            .on('change', function() {
                VALIDATE.int(this, 1, 12, 'Month');
            })
            .val(now.getUTCMonth()+1);
        sunrise_DD
            .on('change', function() {
                VALIDATE.int(this, 1, 31, 'Date')
            })
            .val(now.getUTCDate());
        sunrise_gsq.on('change', function() {
            if (!VALIDATE.gsq($(this).val())) {
                alert(msg.tools.coords.gsq_format);
            } else {
                SUNRISE.gsq_deg($(this).val());
            }
        });
        sunrise_lat.on('change', function() {
            VALIDATE.float($(this).val(), -90.0, 90.0, 'Latitude')
        });
        sunrise_lon.on('change', function() {
            VALIDATE.float($(this).val(), -180.0, 180.0, 'Longitude');
        });
        sunrise_go.on('click', function(){
            $('#sunrise_result').val(SUNRISE.formValues());
            SUNRISE.cookie_set();
            return false;
        });
        sunrise_clear.on('click', function(){
            SUNRISE.cookie_clear();
        });
        if (SUNRISE.cookie_get()) {
            var result = SUNRISE.cookie_get("sunrise").split("|");
            sunrise_gsq.val(result[0]);
            sunrise_lat.val(result[1]);
            sunrise_lon.val(result[2]);
        }
    },
    cookie_clear: function() {
        COOKIE.clear('sunrise');
    },
    cookie_get: function() {
        return COOKIE.get('sunrise');
    },
    cookie_set: function() {
        var value = $('#sunrise_gsq').val() + '|' + $('#sunrise_lat').val() + '|' + $('#sunrise_lon').val();
        COOKIE.set('sunrise', value);
    },
    formValues: function() {
        var latText =   "Latitude"
        var lonText =   "Longitude"
        var yearText =  "Year"
        var monthText = "Month"
        var dayText =   "Date"

        var lat =   parseFloat($('#sunrise_lat').val());
        var lon =   parseFloat($('#sunrise_lon').val());
        var year =  parseInt($('#sunrise_YYYY').val());
        var month = parseInt($('#sunrise_MM').val());
        var day =   parseInt($('#sunrise_DD').val());

        if (!VALIDATE.float(lat, -90.0, 90.0, latText)) {
            return;
        }
        if (!VALIDATE.float(lon, -180.0, 180.0, lonText)) {
            return;
        }
        if (!VALIDATE.int(year, 1901, 2099, yearText)) {
            return;
        }
        if (!VALIDATE.int(month, 1, 12, monthText) ) {
            return;
        }
        var absLat = Math.abs(lat);
        var absLon = Math.abs(lon);

        var monthMax;
        if (month === 2 && (year % 4) === 0) {
            monthMax = 29;
        } else if (month === 2) {
            monthMax = 28;
        } else if ((month === 4) || (month === 6) || (month === 9) || (month === 11)) {
            monthMax = 30;
        } else {
            monthMax = 31
        }
        if (!VALIDATE.int(day, 1, monthMax, dayText)) {
            return
        }
        sunRiseSet(year, month, day, lat, lon);
        civTwilight(year, month, day, lat, lon);

        var twsText =   "Twilight starts: ";
        var tweText =   "Twilight ends:   ";
        var twAllText = "Twilight all night";
        var twNoText =  "No twilight this day";

        var srText = "Sunrise:         ";
        var ssText = "Sunset:          ";
        var sAllText = "Sun is up 24 hrs";
        var sNoText = "Sun is down 24 hrs";

        var resultText =
            "On " + year + "-" + (month < 10 ? '0' : '') + month + "-" + (day < 10 ? '0' : '') +
            day + "\nAt " + absLat + (lat < 0 ? "S" : "N") +
            "  / " + absLon + (lon < 0 ? 'W' : 'E') + "\n" +
            "-----------------------\n" +
            "Sunrise / Sunset UTC\n" +
            "-----------------------\n";

        var twst_h = Math.floor(twStartT)
        var twst_m = Math.floor((twStartT - twst_h)*60)

        var sris_h = Math.floor(sRiseT)
        var sris_m = Math.floor((sRiseT - sris_h)*60)

        var sset_h = Math.floor(sSetT)
        var sset_m = Math.floor((sSetT - sset_h)*60)

        var twen_h = Math.floor(twEndT)
        var twen_m = Math.floor((twEndT - twen_h)*60)

        if (twStatus == 0) {
            resultText += twsText;
            if (twst_h < 10) {
                resultText += "0";
            }
            resultText += twst_h + "."
            if (twst_m < 10) {
                resultText += "0";
            }
            resultText += twst_m + "\n";
        } else if(twStatus > 0 && srStatus <= 0) {
            resultText += twAllText + "\n";
        } else {
            resultText += twNoText + "\n";
        }

        if (srStatus == 0) {
            resultText += srText;
            if (sris_h < 10) {
                resultText += "0";
            }
            resultText += sris_h + ".";
            if (sris_m < 10) {
                resultText += "0";
            }
            resultText += sris_m + "\n";
            resultText += ssText;
            if (sset_h < 10) {
                resultText += "0"
            }
            resultText += sset_h + ".";
            if (sset_m < 10) {
                resultText += "0";
            }
            resultText += sset_m + "\n";
        } else if(srStatus > 0) {
            resultText += sAllText + "\n";
        } else {
            resultText += sNoText + "\n";
        }

        if (twStatus == 0) {
            resultText += tweText;
            if (twen_h < 10) {
                resultText += "0";
            }
            resultText += twen_h + ".";
            if (twen_m < 10) {
                resultText += "0";
            }
            resultText += twen_m
        }
        resultText += "\n-----------------------";
        resultText += "\n(From "+document.title.substr(0,3)+")";
        return resultText;
    },

    gsq_deg: function() {
        var gsq, result;
        gsq = $('#sunrise_gsq').val();
        if (!VALIDATE.gsq(gsq)) {
            alert(msg.tools.coords.gsq_format);
            return false;
        }
        result = CONVERT.gsq_deg(gsq);
        $('#sunrise_lat').val(result.lat);
        $('#sunrise_lon').val(result.lon);
        return true;
    },
}




// Global variables:
var twAngle = -6.0;    // For civil twilight, set to -12.0 for
                       // nautical, and -18.0 for astr. twilight

var srAngle = -35.0/60.0;    // For sunrise/sunset

var sRiseT;        // For sunrise/sunset times
var sSetT;
var srStatus;

var twStartT;      // For twilight times
var twEndT;
var twStatus;

var sDIST;     // Solar distance, astronomical units
var sRA;       // Sun's Right Ascension
var sDEC;      // Sun's declination

var sLON;      // True solar longitude


//-------------------------------------------------------------
// A function to compute the number of days elapsed since
// 2000 Jan 0.0  (which is equal to 1999 Dec 31, 0h UT)
//-------------------------------------------------------------
function dayDiff2000(y,m,d){
    return (367.0*(y)-((7*((y)+(((m)+9)/12)))/4)+((275*(m))/9)+(d)-730530.0);
}


var RADEG = 180.0 / Math.PI
var DEGRAD = Math.PI / 180.0


//-------------------------------------------------------------
// The trigonometric functions in degrees

function sind(x) { return Math.sin((x)*DEGRAD); }
function cosd(x) { return Math.cos((x)*DEGRAD); }
function acosd(x) { return (RADEG*Math.acos(x)); }

function atan2d(y,x) {
    var at2 = (RADEG*Math.atan(y/x));
    if (x < 0 && y < 0) {
        at2 -= 180;
        return at2;
    }
    if (x < 0 && y > 0) {
        at2 += 180;
    }
    return at2;
}

//-------------------------------------------------------------
// This function computes times for sunrise/sunset.
// Sunrise/sunset is considered to occur when the Sun's upper
// limb is 35 arc minutes below the horizon (this accounts for
// the refraction of the Earth's atmosphere).
//-------------------------------------------------------------
function sunRiseSet(year, month, day, lat, lon) {
    return sunTimes( year, month, day, lat, lon, srAngle, 1);
}

//-------------------------------------------------------------
// This function computes the start and end times of civil
// twilight. Civil twilight starts/ends when the Sun's center
// is 6 degrees below the horizon.
//-------------------------------------------------------------
function civTwilight(year,month,day,lat,lon) {
    return ( sunTimes( year, month, day, lat, lon, twAngle, 0) )
}

//-------------------------------------------------------------
// The main function for sun rise/set times
//
// year,month,date = calendar date, 1801-2099 only.
// Eastern longitude positive, Western longitude negative
// Northern latitude positive, Southern latitude negative
//
// altit = the altitude which the Sun should cross. Set to
//         -35/60 degrees for rise/set, -6 degrees for civil,
//         -12 degrees for nautical and -18 degrees for
//         astronomical twilight.
//
// sUppLimb: non-zero -> upper limb, zero -> center. Set to
//           non-zero (e.g. 1) when computing rise/set times,
//           and to zero when computing start/end of twilight.
//
// Status:  0 = sun rises/sets this day, times stored in Global
//              variables
//          1 = sun above the specified "horizon" 24 hours.
//              Rising set to time when the sun is at south,
//              minus 12 hours while Setting is set to the
//              south time plus 12 hours.
//         -1 = sun is below the specified "horizon" 24 hours.
//              Rising and Setting are both set to the time
//              when the sun is at south.
//-------------------------------------------------------------
function sunTimes(year, month, day, lat, lon, altit, sUppLimb) {
    var dayDiff    // Days since 2000 Jan 0.0 (negative before)
    var sRadius    // Sun's apparent radius
    var diuArc     // Diurnal arc
    var sSouthT    // Time when Sun is at south
    var locSidT    // Local sidereal time

    var stCode = 0     // Status code from function - usually 0
    dayDiff = dayDiff2000(year,month,day) + 0.5 - lon/360.0;   // Compute dayDiff of 12h local mean solar time
    locSidT = revolution( GMST0(dayDiff) + 180.0 + lon );   // Compute local sideral time of this moment
    sunRaDec( dayDiff );                                       // Compute Sun's RA + Decl at this moment
    sSouthT = 12.0 - rev180(locSidT - sRA)/15.0;			   // Compute time when Sun is at south - in hours UT
    sRadius = 0.2666 / sDIST;                                  // Compute the Sun's apparent radius, degrees
    if ( sUppLimb != 0) {                                      // Do correction to upper limb, if necessary
        altit -= sRadius;
    }
    /* Compute the diurnal arc that the Sun traverses */
    /* to reach the specified altitude altit:         */
    var cost;
    cost = ( sind(altit) - sind(lat) * sind(sDEC) ) / ( cosd(lat) * cosd(sDEC) );

    if ( cost >= 1.0 ) {
        stCode = -1;
        diuArc = 0.0;       // Sun always below altit
    } else if ( cost <= -1.0 ) {
        stCode = 1;
        diuArc = 12.0;      // Sun always above altit
    } else {
        diuArc = acosd(cost) / 15.0;   // The diurnal arc, hours
    }

    /* Store rise and set times - in hours UT */
    if ( sUppLimb != 0) {     // For sunrise/sunset
        sRiseT = sSouthT - diuArc;
        if (sRiseT < 0) {       // Sunrise day before
            sRiseT += 24;
        }
        sSetT  = sSouthT + diuArc;
        if(sSetT > 24) {        // Sunset next day
            sSetT -= 24;
        }
        srStatus = stCode;
    }
    else {                     // For twilight times
        twStartT = sSouthT - diuArc;
        if (twStartT < 0) {
            twStartT += 24;
        }
        twEndT  = sSouthT + diuArc;
        if(twEndT > 24) {
            twEndT -= 24;
        }
        twStatus = stCode;
    }
}
//================== sunTimes() =====================


//-------------------------------------------------------------
// This function computes the sun's spherical coordinates
//-------------------------------------------------------------
function sunRaDec(dayDiff)
{
    var eclObl;   // Obliquity of ecliptic
                  // (inclination of Earth's axis)
    var x;
    var y;
    var z;

    /* Compute Sun's ecliptical coordinates */

    sunPos( dayDiff );

    /* Compute ecliptic rectangular coordinates (z=0) */

    x = sDIST * cosd(sLON);

    y = sDIST * sind(sLON);

    /* Compute obliquity of ecliptic */
    /* (inclination of Earth's axis) */

//  eclObl = 23.4393 - 3.563E-7 * dayDiff;

    eclObl = 23.4393 - 3.563/10000000 * dayDiff;  // for Opera

    /* Convert to equatorial rectangular coordinates */
    /* - x is unchanged                               */

    z = y * sind(eclObl);
    y = y * cosd(eclObl);

    /* Convert to spherical coordinates */

    sRA = atan2d( y, x );
    sDEC = atan2d( z, Math.sqrt(x*x + y*y) );

} //================= sunRaDec() =======================



//-------------------------------------------------------------
// Computes the Sun's ecliptic longitude and distance
// at an instant given in dayDiff, number of days since
// 2000 Jan 0.0.  The Sun's ecliptic latitude is not
// computed, since it's always very near 0.
//-------------------------------------------------------------
function sunPos(dayDiff)
{
    var M;       // Mean anomaly of the Sun
    var w;       // Mean longitude of perihelion
                 // Note: Sun's mean longitude = M + w
    var e;       // Eccentricity of Earth's orbit
    var eAN;     // Eccentric anomaly
    var x;       // x, y coordinates in orbit
    var y;
    var v;       // True anomaly

    /* Compute mean elements */

    M = revolution( 356.0470 + 0.9856002585 * dayDiff );

//  w = 282.9404 + 4.70935E-5 * dayDiff;
//  e = 0.016709 - 1.151E-9 * dayDiff;

    w = 282.9404 + 4.70935/100000 * dayDiff;    // for Opera
    e = 0.016709 - 1.151/1000000000 * dayDiff;  // for Opera

    /* Compute true longitude and radius vector */

    eAN = M + e * RADEG * sind(M) * ( 1.0 + e * cosd(M) );

    x = cosd(eAN) - e;
    y = Math.sqrt( 1.0 - e*e ) * sind(eAN);

    sDIST = Math.sqrt( x*x + y*y );    // Solar distance

    v = atan2d( y, x );                // True anomaly

    sLON = v + w;                      // True solar longitude

    if ( sLON >= 360.0 )
        sLON -= 360.0;                   // Make it 0..360 degrees

} //=================== sunPos() =============================


function revolution( x ) {
    return (x - 360.0 * Math.floor( x * (1.0 / 360.0) ));
}

function rev180( x ) {
    return ( x - 360.0 * Math.floor( x * (1.0 / 360.0) + 0.5 ) );
}


//-------------------------------------------------------------
// This function computes GMST0, the Greenwhich Mean Sidereal
// Time at 0h UT (i.e. the sidereal time at the Greenwhich
// meridian at 0h UT).
// GMST is then the sidereal time at Greenwich at any time of
// the day.  GMST0 is generalized as well, and is defined as:
//
//  GMST0 = GMST - UT
//
// This allows GMST0 to be computed at other times than 0h UT
// as well.  While this sounds somewhat contradictory, it is
// very practical:
// Instead of computing GMST like:
//
//  GMST = (GMST0) + UT * (366.2422/365.2422)
//
// where (GMST0) is the GMST last time UT was 0 hours, one simply
// computes:
//
//  GMST = GMST0 + UT
//
// where GMST0 is the GMST "at 0h UT" but at the current moment!
// Defined in this way, GMST0 will increase with about 4 min a
// day.  It also happens that GMST0 (in degrees, 1 hr = 15 degr)
// is equal to the Sun's mean longitude plus/minus 180 degrees!
// (if we neglect aberration, which amounts to 20 seconds of arc
// or 1.33 seconds of time)
//-------------------------------------------------------------
function GMST0( dayDiff ) {
    var const1 = 180.0 + 356.0470 + 282.9404;
    var const2 = 0.9856002585 + 4.70935E-5;
    return ( revolution( const1 + const2 * dayDiff ) );
}


function initUsersForm(pagingMsg, resultsCount) {
    $(document).ready( function() {
        COMMON_FORM.setPagingControls();

        setColumnSortActions();
        setColumnSortedClass();
        setExternalLinks();

        COMMON_FORM.setPagingStatus(pagingMsg, resultsCount);
        setUserActions();
    });
}

function setUserActions() {
    $('#btn_new').click(function() {
        window.open('./users/new', 'user_new', popWinSpecs['users_[id]']);
        return false;
    });
}


var LIGHTNING = {
    init: function() {
        var lightning_clear =   $('#lightning_clear');
        var lightning_go =      $('#lightning_go');
        var lightning_gsq =     $('#lightning_gsq');
        var lightning_lat =     $('#lightning_lat');
        var lightning_lon =     $('#lightning_lon');
        var lightning_zoom =    $('#lightning_zoom');
        var lightning_map =     $('#lightning_map');
        var lightning_slider =  $("#slider-range-max");

        var element = $('#lightning_zoom');
        lightning_slider.slider({
            range: "max",
            min: 1,
            max: 10,
            value: element.val(),
            slide: function (event, ui) {
                lightning_zoom.val(ui.value);
                if (lightning_lat.val() && lightning_lon.val()) {
                    lightning_go.trigger('click');
                }
            }
        });

        $('#close').on('click', function() {
            window.close();
        })

        lightning_gsq.on('change, blur', function() {
            if (!$(this).val()) {
                return;
            }
            if (!VALIDATE.gsq($(this).val())) {
                alert(msg.tools.coords.gsq_format);
                return;
            }
            LIGHTNING.gsq_deg($(this).val());
            lightning_go.trigger('click');
            LIGHTNING.cookie_set();
        });

        lightning_lat.on('change', function() {
            VALIDATE.float($(this).val(), -90.0, 90.0, 'Latitude')
        });

        lightning_lon.on('change', function() {
            VALIDATE.float($(this).val(), -180.0, 180.0, 'Longitude');
        });

        lightning_go.on('click', function() {
            var lat = $('#lightning_lat').val();
            var lon = $('#lightning_lon').val();
            var zoom = $('#lightning_zoom').val();
            if (!VALIDATE.float(lat, -90.0, 90.0, 'Latitude')) {
                return false;
            }
            if (!VALIDATE.float(lon, -180.0, 180.0, 'Longitude')) {
                return false;
            }
            LIGHTNING.map_show(lat, lon, zoom);
        });
        lightning_clear.on('click', function() {
            LIGHTNING.cookie_clear();
            lightning_gsq.val('');
            lightning_lat.val('');
            lightning_lon.val('');
            lightning_map.prop('src', '');
            lightning_zoom.val(5);
            lightning_slider.slider('option', 'value', 5);
        });
        if (LIGHTNING.cookie_get('lightning')) {
            var result = LIGHTNING.cookie_get("lightning").split("|");
            lightning_gsq.val(result[0]);
            lightning_lat.val(result[1]);
            lightning_lon.val(result[2]);
            lightning_zoom.val(result[3]);
            lightning_slider.slider('option', 'value', result[3]);
        }
        $('a[data-coords]')
            .click(function() {
                var args, lat, lon, zoom;
                result = $(this).data('coords').split('|');
                lightning_gsq.val('');
                lightning_lat.val(result[0]);
                lightning_lon.val(result[1]);
                lightning_zoom.val(result[2]);
                lightning_slider.slider('option', 'value', result[2]);
                LIGHTNING.map_show(result[0], result[1], result[2]);
                LIGHTNING.cookie_clear();
                return false;
            });
    },
    cookie_clear: function() {
        COOKIE.clear('lightning');
    },
    cookie_get: function() {
        return COOKIE.get('lightning');
    },
    cookie_set: function() {
        var value = $('#lightning_gsq').val() + '|' + $('#lightning_lat').val() + '|' + $('#lightning_lon').val() + '|' + $('#lightning_zoom').val();
        COOKIE.set('lightning', value);
    },
    gsq_deg: function(gsq) {
        var result = CONVERT.gsq_deg(gsq);
        $('#lightning_lat').val(result.lat);
        $('#lightning_lon').val(result.lon);
        return true;
    },
    map_show: function(lat, lon, zoom) {
        var url =
            'https://map.blitzortung.org?' +
            'interactive=1' +
            '&NavigationControl=0' +
            '&FullScreenControl=1' +
            '&Cookies=0' +
            '&InfoDiv=0' +
            '&MenuButtonDiv=1' +
            '&ScaleControl=1' +
            '&CountingRangeValue=3' +
            '&CountingCheckboxChecked=1' +
            '&DetectorsCheckboxChecked=0' +
            '&DetectorsRange=0' +
            '&AudioCheckboxChecked=0' +
            '&LinksCheckboxChecked=0' +
            '&LinksRangeValue=0' +
            '&MapStyle=0' +
            '&MapStyleRangeValue=0' +
            '&Advertisment=0' +
            '#' + zoom + '/' + lat + '/' + lon;
        $('#lightning_map').prop('src', url);
    }
}