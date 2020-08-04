function initUsersForm(pagingMsg, resultsCount) {
    $(document).ready( function() {
        setFormPagingActions();

        setColumnSortActions();
        setColumnSortedClass();
        setExternalLinks();

        setFormPagingStatus(pagingMsg, resultsCount);
        setUserActions();
    });
}

function setUserActions() {
    $('#btn_new').click(function() {
        window.open('./users/new', 'user_new', popWinSpecs['users_[id]']);
        return false;
    });
}
