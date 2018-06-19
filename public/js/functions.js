function setFormTypesStyles() {
    $("div#form_types div input").each(function() {
        $(this).parent().attr('class', $(this).attr('class'))
    });
}
function setFormTypesAllAction() {
    $('div#form_types div :checkbox[value=type_ALL]').click(function () {
        $('div#form_types div :checkbox').prop('checked', $(this).prop("checked"));
    });
}