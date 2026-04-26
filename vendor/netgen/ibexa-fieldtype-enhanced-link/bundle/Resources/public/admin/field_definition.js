(function (global, doc, ibexa, React, ReactDOM) {
    const SELECTOR_RESET_STARTING_LOCATION_BTN = '.ibexa-tag__remove-btn';
    const defaultLocationContainers = doc.querySelectorAll('.ibexa-default-location');
    const udwContainer = doc.getElementById('react-udw');
    let udwRoot = null;
    const closeUDW = () => udwRoot.unmount();
    const renderTagItem = (container, [item]) => {
        container.innerHTML = container.dataset.template.replaceAll('{{ content }}', item.name);

        const deleteBtn = container.querySelector(SELECTOR_RESET_STARTING_LOCATION_BTN);

        ibexa.helpers.ellipsis.middle.parseAll();

        deleteBtn.addEventListener('click', resetStartingLocation, false);
    };
    const onConfirm = (btn, items) => {
        closeUDW();

        const locationId = items[0].id;
        const container = btn.closest('.ibexa-default-location');
        const pathSelector = container.querySelector('.ibexa-default-location__path-selector');

        container.querySelector(btn.dataset.relationRootInputSelector).value = locationId;

        pathSelector.classList.add('ibexa-default-location__path-selector--filled');

        ibexa.helpers.tagViewSelect.buildItemsFromUDWResponse(
            items,
            (item) => item.pathString,
            renderTagItem.bind(null, container.querySelector('.ibexa-default-location__selected-path')),
        );
    };
    const onCancel = () => closeUDW();
    const openUDW = (event) => {
        event.preventDefault();

        const config = JSON.parse(event.currentTarget.dataset.udwConfig);

        udwRoot = ReactDOM.createRoot(udwContainer);
        udwRoot.render(
            React.createElement(ibexa.modules.UniversalDiscovery, {
                onConfirm: onConfirm.bind(null, event.currentTarget),
                onCancel,
                title: event.currentTarget.dataset.universaldiscoveryTitle,
                multiple: false,
                ...config,
            }),
        );
    };
    const resetStartingLocation = ({ currentTarget }) => {
        const container = currentTarget.closest('.ibexa-default-location');
        const udwBtn = container.querySelector('.ibexa-btn--udw-relation-default-location');
        const pathSelector = container.querySelector('.ibexa-default-location__path-selector');
        const { relationRootInputSelector } = udwBtn.dataset;

        container.querySelector(relationRootInputSelector).value = '';
        container.querySelector('.ibexa-default-location__selected-path').innerHTML = '';
        pathSelector.classList.remove('ibexa-default-location__path-selector--filled');
    };

    const ALLOWED_TARGETS_QUERY_SELECTOR = {
        internal: '.allowed-targets-internal',
        external: '.allowed-targets-external',
    };
    const attachEvents = (container) => {
        const udwBtn = container.querySelector('.ibexa-btn--udw-relation-default-location');
        udwBtn.addEventListener('click', openUDW, false);

        const deleteBtn = container.querySelector(SELECTOR_RESET_STARTING_LOCATION_BTN);
        deleteBtn?.addEventListener('click', resetStartingLocation, false);

        const choices = container.querySelectorAll('input[type="radio"]');
        choices.forEach((choice) => choice.addEventListener('change', toggleDisabledState.bind(null, container), false));

        setupAllowedLinkTypeChangeHandling(container);

        addAllowedTargetsEventHandler(ALLOWED_TARGETS_QUERY_SELECTOR.internal, container);
        addAllowedTargetsEventHandler(ALLOWED_TARGETS_QUERY_SELECTOR.external, container);
    };

    const setupAllowedLinkTypeChangeHandling = (container) => {
        const allowedLinkTypeChoices = container.querySelectorAll('.allowed-link-type input[type="radio"]');
        allowedLinkTypeChoices.forEach((choice) => {
            choice.addEventListener('change', handleAllowedLinkChange.bind(null, choice, container))
            if (choice.checked) {
                handleAllowedLinkChange(choice, container);
            }
        });
    };

    const addAllowedTargetsEventHandler = (querySelector, container) => {
        container = container.querySelector(querySelector);
        const options = container.querySelectorAll('input[type="checkbox"]');
        options.forEach(option => option.addEventListener('change', handleAllowedTargetsChange.bind(null, option, options)));
    };

    const ALLOWED_LINK_TYPE_VALUE = {
        all: 'all',
        internal: 'internal',
        external: 'external',
    };
    const OPTION_CONTAINER_QUERY_SELECTOR = {
        internal: '.internal-options',
        external: '.external-options',
    };
    const show = (element) => {
        element.classList.remove('hidden');
    };
    const hide = (element) => {
        element.classList.add('hidden');
    };
    const handleAllowedLinkChange = (choice, container) => {
        if (!choice.checked) {
            return;
        }

        const internalOptions = container.querySelector(OPTION_CONTAINER_QUERY_SELECTOR.internal);
        const externalOptions = container.querySelector(OPTION_CONTAINER_QUERY_SELECTOR.external);

        switch (choice.value) {
            case ALLOWED_LINK_TYPE_VALUE.all:
                show(internalOptions);
                show(externalOptions);
                break;

            case ALLOWED_LINK_TYPE_VALUE.internal:
                show(internalOptions);
                hide(externalOptions);
                break;
                
            case ALLOWED_LINK_TYPE_VALUE.external:
                hide(internalOptions);
                show(externalOptions);
                break;

            default:
                break;
        }
    };

    const handleAllowedTargetsChange = (option, allOptions) => {
        let checkedCounter = 0;
        allOptions.forEach(option => {
            if (option.checked) {
                checkedCounter += 1;
            }
        });

        if (!option.checked && checkedCounter === 0) {
            option.checked = true;
        }
    };
    
    const toggleDisabledState = (container) => {
        const locationBtn = container.querySelector('.ibexa-btn--udw-relation-default-location');
        const deleteBtn = container.querySelector(SELECTOR_RESET_STARTING_LOCATION_BTN);
        const isDisabled = !container.querySelector('input[value="1"]').checked;

        locationBtn.classList.toggle('disabled', isDisabled);
        deleteBtn?.classList.toggle('disabled', isDisabled);
    };

    doc.body.addEventListener(
        'ibexa-drop-field-definition',
        (event) => {
            const { nodes } = event.detail;

            nodes.forEach((node) => {
                const defaultLocationContainer = node.querySelector('.ibexa-default-location');

                if (!defaultLocationContainer) {
                    return;
                }

                attachEvents(defaultLocationContainer);
                toggleDisabledState(defaultLocationContainer);
            });
        },
        false,
    );

    defaultLocationContainers.forEach((defaultLocationContainer) => {
        attachEvents(defaultLocationContainer);
        toggleDisabledState(defaultLocationContainer);
        ibexa.helpers.ellipsis.middle.parseAll();
    });
})(window, window.document, window.ibexa, window.React, window.ReactDOM);
