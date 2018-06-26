/* [ Set css styles for signal type checkboxes ] */
function setFormTypesStyles() {
    $("div#form_types div input").each(function() {
        $(this).parent().attr('class', $(this).attr('class'))
    });
}

/* [ Ensure that at least one option is checked for signal type checkboxes ] */
function setFormTypesDefault() {
    if ($('div#form_types div :checkbox:checked').length == 0) {
        $('div#form_types div :checkbox[value=type_NDB]').prop('checked', true);
    }
}

/* [ Enable 'All' checkbox to select / unselect all signal types ] */
function setFormTypesAllAction() {
    $('div#form_types div :checkbox[value=type_ALL]').click(function () {
        $('div#form_types div :checkbox').prop('checked', $(this).prop("checked"));
    });
}

/* [ Enable Country change to resubmit form ] */
function setFormCountryAction() {
    $('select#form_country').change(function () {
        $('#form_submit').click();
    });
}

/* [ Enable Region change to resubmit form ] */
function setFormRegionAction() {
    $('select#form_region').change(function () {
        $('#form_submit').click();
    });
}

/* [ Enable sort actions for all sortable columns ] */
function setColumnSortActions() {
    $('table.results thead tr th[id]').each(function() {
        $(this).click(function () {
            var column = this.id.split('_')[0];
            var dir = this.id.split('_')[1];
            if ($(this).hasClass('sorted')) {
                dir = ($('#form_order').val()=='a' ? 'd' : 'a');
            }
            $('#form_sort').val(column);
            $('#form_order').val(dir);
            $('#form_submit').click();
        })
    });
}

/* [ Indicate which column is sorted by checking hidden fields on form ] */
function setColumnSortedClass() {
    $('table.results thead tr th').each(function() {
        if (this.id.split('_')[0] == $('#form_sort').val()) {
            $(this).append($('#form_order').val() == 'd' ? ' &#9662;' : ' &#9652;');
            $(this).addClass('sorted');
        }
    });
}

/* [ Set links to open in external or popup window if rel attribute is set ] */
function setExternalLinks() {
    $('a[rel="external"]').attr('target', '_blank');
    $('a[rel^="popup|"]').click(function() {
        var args = this.rel.split('|')[1];
        window.open(this.href,this.rel.split('|')[1], this.rel.split('|')[2]);
        return false;
    });
}