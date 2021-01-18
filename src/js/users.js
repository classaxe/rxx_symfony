function initUsersForm(pagingMsg, resultsCount) {
    $(document).ready( function() {
        COMMON_FORM.setPagingControls();

        setColumnSortActions();
        setColumnSortedClass();
        setExternalLinks();

        COMMON_FORM.setPagingStatus(pagingMsg, resultsCount);
        setUserActions();
    });
}

function setUserActions() {
    $('#btn_new').click(function() {
        window.open('./users/new', 'user_new', popWinSpecs['users_[id]']);
        return false;
    });
}
