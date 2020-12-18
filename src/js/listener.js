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