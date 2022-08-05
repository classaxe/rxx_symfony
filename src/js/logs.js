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
        COMMON_FORM.initListenersSelector('form_listenerId', 'form[listenerId]', true, listeners, 10);
        COMMON_FORM.initListenersSelector('form_operatorId', 'form[operatorId]', false, operators, 10);
        COMMON_FORM.initSignalsSelector(signals);
        COMMON_FORM.initTimeControl();
        COMMON_FORM.setDatePickerActions();
        setExternalLinks();
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
