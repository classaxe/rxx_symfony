function initListenersLogUploadForm() {
    var std_formats = {
        'pskov': '  DD.MM.YYYY  hhmm  KHZ    ID    LSB    USB    sec            X',
        'wwsu':  'YYYY.MM.DD hh:mm  KHZ     ID        X     QTH',
        'yand':  'YYYYMMDD hhmm KHZ ID   X          QTH           X'
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
