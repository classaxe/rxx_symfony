var LOGSESSION_EDIT = {
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
    },
}
