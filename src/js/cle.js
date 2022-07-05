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