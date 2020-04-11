function initListenersForm(pagingMsg, resultsCount) {
    $(document).ready(function () {
        setFormPagingActions();
        setFormTypesStyles();
        setFormTypesDefault();
        setFormTypesAllAction();
        setFormCountryAction();
        setFormRegionAction();
        setFormHasLogsAction();
        setFormHasLogsAction();
        setFormHasMapPosAction();
        setFormResetAction('listeners');
        setColumnSortActions();
        setColumnSortedClass();
        setExternalLinks();
        setFormPagingStatus(pagingMsg, resultsCount);
        setListenerActions();
        scrollToResults();
    })
}

function setListenerActions() {
    $('#btn_prt').click(function () {
        window.print();
        return false;
    });
    $('#btn_share').click(function() {
        shareListeners();
        return false;
    });
    $('#btn_new').click(function() {
        window.open('./listeners/new', 'listener_new', popWinSpecs['listeners_[id]']);
        return false;
    });
}
