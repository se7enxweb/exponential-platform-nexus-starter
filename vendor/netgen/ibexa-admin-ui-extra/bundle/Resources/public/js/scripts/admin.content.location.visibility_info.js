(function (global, doc, ibexa) {
    "use strict";
    const SELECTOR_VISIBILITY_ALERT_WRAPPER = '#ng-visibility-alert';
    const handleRefreshError = () => {}
    const handleRefreshResponse = (response) => {
        if (response.status === 204) {
            return { isEmpty: true };
        }

        if (!response.ok) {
            throw new Error(response.statusText);
        }

        return response.text().then((html) => ({ isEmpty: false, html }));
    };
    const updateAlertContent = (wrapper, { isEmpty, html }) => {
        wrapper.innerHTML = isEmpty ? '' : html;
    };
    const refreshAlert = (wrapper, url) => {
        const request = new Request(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        fetch(request)
            .then(handleRefreshResponse)
            .then(updateAlertContent.bind(null, wrapper))
            .catch(handleRefreshError);
    };
    const onReady = () => {
        const wrapper = doc.querySelector(SELECTOR_VISIBILITY_ALERT_WRAPPER);
        if (!wrapper) {
            return;
        }

        const url = wrapper.dataset.url;
        if (!url) {
            return;
        }

        doc.body.addEventListener(
            'ibexa-content-tree-refresh',
            refreshAlert.bind(null, wrapper, url),
            false
        );
    };

    if (doc.readyState === 'loading') {
        doc.addEventListener('DOMContentLoaded', onReady);
    } else {
        onReady();
    }
})(window, window.document, window.ibexa);
