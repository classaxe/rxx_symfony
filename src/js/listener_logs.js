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
            [ '#form_format', 3, 4 ],
            [ '#form_logs',   5, 6 ],
            [ '#form_YYYY',   7, 8 ],
            [ '#form_MM',     9, 10 ],
            [ '#form_DD',    11, 12 ]
        ];
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
        $('#form_step').val(2);
    });

    $('#form_back').on('click', function() {
        $('#form_step').val(1);
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