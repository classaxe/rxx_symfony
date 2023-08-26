function initDonationsForm(pagingMsg, resultsCount) {
    $(document).ready( function() {
        COMMON_FORM.setPagingControls();

        setColumnSortActions();
        setColumnSortedClass();
        setExternalLinks();

        COMMON_FORM.setPagingStatus(pagingMsg, resultsCount);
        setDonationActions();
    });
}

function setDonationActions() {
    $('#btn_new').click(function() {
        window.open('./donations/new', 'donation_new', popWinSpecs['donations_[id]']);
        return false;
    });
}
