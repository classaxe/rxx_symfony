function initListenersLogUploadForm() {
    $('#form_format').on('keyup', function(e) {
        $('#form_saveFormat').attr('disabled', $(this).val() === $('#formatOld').text());
    });

    // Detect if we reloaded the page dure to back button being pressed
    if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
        $('#form_format').trigger('keyup');
    }

    $('#form_saveFormat').on('click', function(e) {
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

    $('#form_back').on('click', function(e) {
        window.history.back();
    });

    $(document).on('click', '.trigger', function () {
        $(this).addClass("on");
        $(this).tooltip({
            content: $('#tokensHelp').html(),
            items: '.trigger.on',
            position: {
                my: "left+15 top-20",
                at: "right center",
            },
            tooltipClass: "toolTipDetails",
        });
        $(this).trigger('mouseenter');
    });
    //hide
    $(document).on('click', '.trigger.on', function () {
        $(this).tooltip('close');
        $(this).removeClass("on");
    });
    //prevent mouseout and other related events from firing their handlers
    $(".trigger").on('mouseout', function (e) {
        e.stopImmediatePropagation();
    });
}