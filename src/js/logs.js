var LOG_EDIT = {
    init: function() {
        $('#form_save').on('click', function(){
            $('#form_reload').val(1);
            setTimeout(function(){
                $('#form_save, #form_saveClose, #form_close').attr('disabled', 'disabled');
            }, 1);
        })
        $('#form_saveClose').on('click', function(){
            $('#form_reload').val(1);
            $('#form__close').val(1);
            setTimeout(function(){
                $('#form_save, #form_saveClose, #form_close').attr('disabled', 'disabled');
            }, 1);
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
                " data-gsq='" + r[4] + "'" +
                " data-tz='" + r[9] + "'" +
                " class='" + (r[5] === 'Y' ? 'primaryQth' : 'secondaryQth') + "'" +
                (r[0] === s ? " selected='selected'" : '') +
                ">" +
                leadNbsp(r[0],4) + ' ' + pad(r[2] + ', ' + r[6], (r[5] === 'Y' ? 55 : 52), '&nbsp;') +
                (r[7] ? ' ' + r[7] : '&nbsp; &nbsp;') +
                ' ' + r[8] +
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
