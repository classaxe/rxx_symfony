var commonForm = {
    setCountryAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('select#form_country').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_country').off('change');
        }
    },

    setRegionAction : function(enable) {
        enable = typeof enable !== 'undefined' ? enable : true;
        if (enable) {
            $('select#form_region').change(function () {
                formSubmit();
            });
        } else {
            $('select#form_region').off('change');
        }
    },

    /* [ Enable 'All' checkbox to select / unselect all signal types ] */
    setTypesAllAction : function () {
        $('fieldset#form_type div :checkbox[value=ALL]').click(function () {
            $('fieldset#form_type div :checkbox').prop('checked', $(this).prop("checked"));
        });
    },

    /* [ Ensure that at least one option is checked for signal type checkboxes ] */
    setTypesDefault : function() {
        if ($('fieldset#form_type div :checkbox:checked').length === 0) {
            $('fieldset#form_type div :checkbox[value=NDB]').prop('checked', true);
        }
    },

    /* [ Set css styles for signal type checkboxes ] */
    setTypesStyles : function() {
        $("fieldset#form_type div input").each(function() {
            $(this).parent().attr('class', 'type_' + $(this).attr('class'));
        });
    },

}