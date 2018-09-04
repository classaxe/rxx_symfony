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
            $('form[name="form"]').submit();
        })
    });
}

/* [ Add title for td cells having class 'clipped' ] */
function setClippedCellTitles() {
    $('td.clipped').each(function() {
        $(this).attr('title', $(this).text().trim());
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
    $('a[data-popup]').click(function() {
        var args = $(this).data('popup').split('|');
        window.open(this.href, args[0], args[1]);
        return false;
    });
}

function setEmailLinks() {
    $('a[data-contact]').each(function() {
        var link = $(this).attr('data-contact').split('').reverse().join('').trim().replace('#','@');
        $(this).attr('href', link);
        $(this).removeAttr('data-contact');
    });
}

function setPagingActions() {
    var select =    $('select#form_page');
    var idx =       select.prop('selectedIndex');
    var options =   $('select#form_page option');

    select.change(function() {
        $('button#form_prev').prop('disabled', 'disabled');
        $('button#form_next').prop('disabled', 'disabled');
        $('form[name="form"]').submit();
    });

    if (idx > 0) {
        $('button#form_prev').click(function () {
            var form = $('form[name="form"]');
            var select = $('select#form_page');
            var options = $('select#form_page option');
            var idx = select.prop('selectedIndex');
            $('button#form_prev').prop('disabled', 'disabled');
            $('button#form_next').prop('disabled', 'disabled');
            options.eq(idx - 1).prop('selected', true);
            select.prop('selectedIndex', idx - 1);
            form.submit();
            return false;
        });
    } else {
        $('button#form_prev').prop('disabled', 'disabled');
    }

    if (idx + 1 < options.length) {
        $('button#form_next').click(function() {
            var form =      $('form[name="form"]');
            var select =    $('select#form_page');
            var options =   $('select#form_page option');
            var idx =       select.prop('selectedIndex');
            $('button#form_prev').prop('disabled', 'disabled');
            $('button#form_next').prop('disabled', 'disabled');
            options.eq(idx + 1).prop('selected', true);
            select.prop('selectedIndex', idx + 1);
            form.submit();
            return false;
        });
    } else {
        $('button#form_next').prop('disabled', 'disabled');
    }
}