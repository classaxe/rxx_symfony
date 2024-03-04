function initClePlannerForm(pagingMsg, resultsCount) {
    $(document).ready( function() {
        var c = COMMON_FORM;
        c.setPagingControls();

        setColumnSortActions();
        setColumnSortedClass();
        setExternalLinks();

        c.setTypesStyles();
        c.setTypesDefault();
        c.setTypesAllAction();
        c.setDatePickerActions();


        c.setPagingStatus(pagingMsg, resultsCount);
    });
}