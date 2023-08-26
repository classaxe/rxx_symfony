function initDonorsForm(pagingMsg, resultsCount) {
    $(document).ready( function() {
        COMMON_FORM.setPagingControls();

        setColumnSortActions();
        setColumnSortedClass();
        setExternalLinks();

        COMMON_FORM.setPagingStatus(pagingMsg, resultsCount);
        setDonorActions();
    });
}

function setDonorActions() {
    $('#btn_new').click(function() {
        window.open('./donors/new', 'donor_new', popWinSpecs['donors_[id]']);
        return false;
    });
}
