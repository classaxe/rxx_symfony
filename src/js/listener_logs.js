function initListenersLogUploadForm() {
    var std_formats = {
        'wwsu': 'YYYY-MM-DD UTC    KHZ     ID         X       QTH',
        'yand': 'YYYYMMDD hhmm KHZ ID   X          QTH           X'
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
        var remaining = remaining_val === '' ? 0 : remaining_val.split("\n").length;
        var message = (remaining_val.trim() ? m[1] + "\n" + m[2].replace('COUNT', remaining) + "\n\n" + m[3] : m[1]);
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
        $('#form_step').val(3);
    });

    $('#copyRemainder').on('click', function() {
        var txt = $('#remainder_logs').val();
        copyToClipboard(txt);
        alert(msg.log_upload.copy_remaining);
        return false;
    })

    $('.jump .up').on('click', function() {
        var id = parseInt($(this).parent().attr('id').split('_')[1]);
        var row_id = $('#jump_' + (id - 1)).parent().attr('id').split('_')[1];
        document.getElementById('row_' + (row_id-1)).scrollIntoView({behavior: 'smooth', block: 'start'});
    });

    $('.jump .down').on('click', function() {
        var id = parseInt($(this).parent().attr('id').split('_')[1]);
        if ($('#jump_' + (id + 1)).length) {
            var row_id = $('#jump_' + (id + 1)).parent().attr('id').split('_')[1];
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
            if (logs[i] !== '') {
                remainder.push(logs[i]);
            }
        }
    }
    $('#remainder_format').val($('#form_format').val());
    $('#remainder_logs').val(remainder.join("\r\n"));
    $('#form_selected').val(selected.join(','));
    $('#issueCount').text(remainder.length);
}