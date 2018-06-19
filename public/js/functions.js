function setFormTypesStyles() {
    $("div#form_types div input").each(function() {
        $(this).parent().attr('class', $(this).attr('class'))
    });
}

function setFormTypesDefault() {
    if ($('div#form_types div :checkbox:checked').length == 0) {
        $('div#form_types div :checkbox[value=type_NDB]').prop('checked', true);
    }
}

function setFormTypesAllAction() {
    $('div#form_types div :checkbox[value=type_ALL]').click(function () {
        $('div#form_types div :checkbox').prop('checked', $(this).prop("checked"));
    });
}

function setColumnSortActions() {
    $('table.results thead tr th').each(function() {
        $(this).click(function () {
            $('#form_sort').val(this.id);
            $('#form_submit').click();
        })
    });
}

function setColumnSortedClass() {
    $('table.results thead tr th').each(function() {
        if (this.id == $('#form_sort').val()) {
            $(this).attr('class', $(this).attr('class')+' sorted');
        }
    });
}