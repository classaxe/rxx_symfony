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