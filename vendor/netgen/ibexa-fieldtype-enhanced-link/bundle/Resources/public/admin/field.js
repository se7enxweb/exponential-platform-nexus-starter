(function (global, doc, ibexa, React, ReactDOM, Translator) {
    const CLASS_FIELD_SINGLE = 'ibexa-field-edit--ngenhancedlink';
    const SELECTOR_FIELD = '.ibexa-field-edit--ngenhancedlink';
    const otherValue = {
        internal: 'external',
        external: 'internal'
    };
    const CLASS_REQUIRED_BY_LINK_TYPE = {
        internal: '.internal-required-field',
        external: '.external-required-field',
    }
    const ID_FIELD_SELECTOR = '.internal-link-id';

    const IBEXA_SUBFIELD_WRAPPER = 'ibexa-data-source__field'
    const SELECTOR_BTN_ADD = '.ibexa-relations__table-action--create';
    const SELECTOR_ROW = '.ibexa-relations__item';


    const requiredFieldContainerClass = 'ibexa-field-edit--required';
    const requiredSubFieldContainerClass = 'ibexa-data-source__field--required';

    const INTERNAL_RELATION_FIELD_NAME = 'Related object';

    class NGEnhancedLinkValidator extends ibexa.BaseFieldValidator {
        constructor(props) {
            super(props);

            this.allowedLinkType = this.fieldContainer.querySelector('.link-type-options').dataset.allowedLinkType;
            this.isRequired = this.fieldContainer.classList.contains(requiredFieldContainerClass);

            const fieldName = this.fieldContainer.querySelector('.ibexa-field-edit__label').innerHTML;
            this.validationResult = {
                isError: false,
                errorMessage: ibexa.errors.emptyField.replace('{fieldName}', fieldName),
            };
        }

        validateInput(fieldElement) {
            const isRequiredSubField = fieldElement.required;
            const isEmptySubField = !fieldElement.value.length;

            return !isRequiredSubField || !isEmptySubField;
        }

        validateField(config, event) {
            this.validationResult.isError = false;
            const selectedLinkType = [...this.fieldContainer.querySelectorAll('.link-type-options input[type="radio"]')].find(option => option.checked)?.value;

            if (selectedLinkType !== undefined) {
                const wrappersSelector = `.${selectedLinkType}-link-form > .${requiredSubFieldContainerClass}`;
                const currentTypeFormElements = this.fieldContainer.querySelectorAll(`${wrappersSelector} input, ${wrappersSelector} select`);
                currentTypeFormElements.forEach(element => {
                    const isValidInput = this.validateInput(element);
                    this.validationResult.isError ||= !isValidInput;
                    if (element.id === event.target.id) {
                        this.toggleInputError(isValidInput, element);
                    }
                });

                if (this.allowedLinkType === 'all') {
                    const otherWrappersSelector = `.${otherValue[selectedLinkType]}-link-form > .${requiredSubFieldContainerClass}`;
                    const otherTypeFormElements = this.fieldContainer.querySelectorAll(`${otherWrappersSelector} input, ${wrappersSelector} select`);
                    otherTypeFormElements.forEach(element => {
                        console.log({element});
                        this.toggleInputError(true, element);
                    });
                }
            } else if (this.isRequired) {
                this.validationResult.isError = true;
            }

            this.validationResult.isError &&= this.isRequired;

            this.toggleError(this.validationResult, this.fieldContainer);

            return this.validationResult;
        }

        toggleError(validationResult, inputWrapper) {
            const errorElement = [...inputWrapper.children].find(el => el.classList.contains('ibexa-form-error'));

            if (validationResult.isError) {
                inputWrapper.classList.add(this.classInvalid);
                errorElement.innerText = validationResult.errorMessage;
            } else {
                inputWrapper.classList.remove(this.classInvalid);
                errorElement.innerText = '';
            }
        }

        toggleInputError(isValid, inputElement) {
            const subFieldName = inputElement.name.split('][').slice(-1)[0].slice(0, -1);
            if (!subFieldName) {
                return;
            }

            const subFieldWrapper = this.fieldContainer.querySelector(`.${IBEXA_SUBFIELD_WRAPPER}--${subFieldName}`);

            const validationResult = {isError: !isValid};
            if (validationResult.isError) {
                const label = subFieldWrapper.querySelector('label.ibexa-label')?.innerHTML ?? INTERNAL_RELATION_FIELD_NAME;
                validationResult.errorMessage = ibexa.errors.emptyField.replace('{fieldName}', label);

                inputElement.classList.add(this.classInvalid);
            } else {
                inputElement.classList.remove(this.classInvalid);
            }

            this.toggleError(validationResult, subFieldWrapper);
        }
    }

    [...doc.querySelectorAll(SELECTOR_FIELD)].forEach((fieldContainer) => {
        /** LINK TYPES */
        const typeOptionsWrapper = fieldContainer.querySelector('.link-type-options');
        const allowedLinkType = fieldContainer.querySelector('.link-type-options').dataset.allowedLinkType;
        const linkTypeOptionsInputs = typeOptionsWrapper.querySelectorAll('.form-check input[type="radio"]');

        if (allowedLinkType === 'all') {
            typeOptionsWrapper.classList.remove('hidden');

            const handleLinkTypeChange = (option, container) => {
                if (!option.checked) {
                    return;
                }

                container.querySelector(`.${option.value}-link-form`).classList.remove('hidden');
                container.querySelector(`.${otherValue[option.value]}-link-form`).classList.add('hidden');
            };

            linkTypeOptionsInputs.forEach(linkTypeOption => {
                const parent = linkTypeOption.parentElement;
                const type = linkTypeOption.value;
                parent.appendChild(fieldContainer.querySelector(`.${type}-link-form`));

                linkTypeOption.addEventListener('change', handleLinkTypeChange.bind(null, linkTypeOption, typeOptionsWrapper));
            });
            linkTypeOptionsInputs.forEach(option => handleLinkTypeChange(option, typeOptionsWrapper));
        } else {
            fieldContainer.querySelector(`.${allowedLinkType}-link-form`).classList.remove('hidden');
            typeOptionsWrapper.querySelector(`.form-check input[type="radio"][value="${allowedLinkType}"]`).checked = true;
        }
        /** /LINK TYPES */

        /** VALIDATOR */
        const eventsMap = [];
        if (allowedLinkType !== 'external') {
            eventsMap.push({
                selector: CLASS_REQUIRED_BY_LINK_TYPE.internal,
                eventName: 'blur',
            });
        }
        if (allowedLinkType !== 'internal') {
            eventsMap.push({
                selector: CLASS_REQUIRED_BY_LINK_TYPE.external,
                eventName: 'blur',
            });
        }

        const validator = new NGEnhancedLinkValidator({
            classInvalid: 'is-invalid',
            fieldContainer,
            eventsMap,
        });
        validator.init();
        ibexa.addConfig('fieldTypeValidators', [validator], true);
        /** /VALIDATOR */

        const udwContainer = doc.getElementById('react-udw');
        const relationIdInput = fieldContainer.querySelector(ID_FIELD_SELECTOR);
        const relationsContainer = fieldContainer.querySelector('.ibexa-relations__list');
        const relationsWrapper = fieldContainer.querySelector('.ibexa-relations__wrapper');
        const relationsCTA = fieldContainer.querySelector('.ibexa-relations__cta');
        const addBtn = fieldContainer.querySelector(SELECTOR_BTN_ADD);
        const trashBtn = fieldContainer.querySelector('.ibexa-relations__table-action--remove');
        const isSingle = fieldContainer.classList.contains(CLASS_FIELD_SINGLE);
        const selectedItemsLimit = isSingle ? 1 : parseInt(relationsContainer.dataset.limit, 10);
        const relationsTable = relationsWrapper.querySelector('.ibexa-table');
        const startingLocationId =
            relationsContainer.dataset.defaultLocation !== '0' ? parseInt(relationsContainer.dataset.defaultLocation, 10) : null;
        const closeUDW = () => {
            if (udwRoot) {
                udwRoot.unmount();
                udwRoot = null;
            }
        };
        const renderRows = (items) => {
            items.forEach((item, index) => {
                relationsContainer.insertAdjacentHTML('beforeend', renderRow(item, index));

                const { escapeHTML } = ibexa.helpers.text;
                const itemNodes = relationsContainer.querySelectorAll('.ibexa-relations__item');
                const itemNode = itemNodes[itemNodes.length - 1];

                itemNode.setAttribute('data-content-id', escapeHTML(item.ContentInfo.Content._id));
                itemNode.querySelector('.ibexa-relations__table-action--remove-item').addEventListener('click', removeItem, false);
            });

            ibexa.helpers.tooltips.parse();
        };
        const updateInputValue = (items) => {
            relationIdInput.value = items.join();
            relationIdInput.dispatchEvent(new FocusEvent('blur'));
        };
        const onConfirm = (items) => {
            items = excludeDuplicatedItems(items);

            renderRows(items);
            attachRowsEventHandlers();

            selectedItems = [...selectedItems, ...items.map((item) => item.ContentInfo.Content._id)];

            updateInputValue(selectedItems);
            closeUDW();
            updateFieldState();
            updateAddBtnState();
        };
        const openUDW = (event) => {
            event.preventDefault();

            const config = JSON.parse(event.currentTarget.dataset.udwConfig);
            const limit = parseInt(event.currentTarget.dataset.limit, 10);
            const title =
                limit === 1
                    ? Translator.trans(
                          /*@Desc("Select a Content item")*/ 'ezobjectrelationlist.title.single',
                          {},
                          'universal_discovery_widget',
                      )
                    : Translator.trans(
                          /*@Desc("Select Content item(s)")*/ 'ezobjectrelationlist.title.multi',
                          {},
                          'universal_discovery_widget',
                      );

            udwRoot = window.ReactDOMClient.createRoot(udwContainer);

            udwRoot.render(
                React.createElement(ibexa.modules.UniversalDiscovery, {
                    onConfirm,
                    onCancel: closeUDW,
                    title,
                    startingLocationId,
                    ...config,
                    multiple: isSingle ? false : selectedItemsLimit !== 1,
                    multipleItemsLimit: selectedItemsLimit > 1 ? selectedItemsLimit - selectedItems.length : selectedItemsLimit,
                }),
            );
        };
        const excludeDuplicatedItems = (items) => {
            selectedItemsMap = items.reduce((total, item) => ({
                ...total,
                [item.ContentInfo.Content._id]: item
            }), selectedItemsMap);

            return items.filter((item) => selectedItemsMap[item.ContentInfo.Content._id]);
        };
        const renderRow = (item, index) => {
            const { escapeHTML } = ibexa.helpers.text;
            const { formatShortDateTime } = ibexa.helpers.timezone;
            const contentTypeName = ibexa.helpers.contentType.getContentTypeName(item.ContentInfo.Content.ContentTypeInfo.identifier);
            const contentName = escapeHTML(item.ContentInfo.Content.TranslatedName);
            const { rowTemplate } = relationsWrapper.dataset;

            return rowTemplate
                .replace('{{ content_name }}', contentName)
                .replace('{{ content_type_name }}', contentTypeName)
                .replace('{{ published_date }}', formatShortDateTime(item.ContentInfo.Content.publishedDate))
                .replace('{{ order }}', selectedItems.length + index + 1);
        };
        const updateFieldState = () => {
            const tableHideMethod = selectedItems.length ? 'removeAttribute' : 'setAttribute';
            const ctaHideMethod = selectedItems.length ? 'setAttribute' : 'removeAttribute';

            relationsTable[tableHideMethod]('hidden', true);

            if (trashBtn) {
                trashBtn[tableHideMethod]('hidden', true);
            }

            if (addBtn) {
                addBtn[tableHideMethod]('hidden', true);
            }

            relationsCTA[ctaHideMethod]('hidden', true);
        };
        const updateAddBtnState = () => {
            if (!addBtn) {
                return;
            }

            const methodName = !selectedItemsLimit || selectedItems.length < selectedItemsLimit ? 'removeAttribute' : 'setAttribute';

            addBtn[methodName]('disabled', true);
        };
        const updateTrashBtnState = (event) => {
            if (
                !trashBtn ||
                ((!event.target.hasAttribute('type') || event.target.type !== 'checkbox') && event.currentTarget !== trashBtn)
            ) {
                return;
            }

            const anySelected = findCheckboxes().some((item) => item.checked === true);
            const methodName = anySelected ? 'removeAttribute' : 'setAttribute';

            trashBtn[methodName]('disabled', true);
        };
        const removeItems = (event) => {
            event.preventDefault();

            const removedItems = [];

            relationsContainer.querySelectorAll('input:checked').forEach((input) => {
                removedItems.push(parseInt(input.value, 10));

                input.closest('tr').remove();
            });

            selectedItems = selectedItems.filter((item) => !removedItems.includes(item));

            updateInputValue(selectedItems);
            updateFieldState();
            updateAddBtnState();
        };
        const removeItem = (event) => {
            const row = event.target.closest('.ibexa-relations__item');
            const contentId = parseInt(row.dataset.contentId, 10);

            row.remove();

            selectedItems = selectedItems.filter((item) => contentId !== item);

            updateInputValue(selectedItems);
            updateFieldState();
            updateAddBtnState();
        };
        const findOrderInputs = () => {
            return [...relationsContainer.querySelectorAll('.ibexa-relations__order-input')];
        };
        const findCheckboxes = () => {
            return [...relationsContainer.querySelectorAll('[type="checkbox"]')];
        };
        const attachRowsEventHandlers = () => {
            const isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

            findOrderInputs().forEach((item) => {
                item.addEventListener('blur', updateSelectedItemsOrder, false);

                if (isFirefox) {
                    item.addEventListener('change', focusOnElement, false);
                }
            });
        };
        const focusOnElement = (event) => {
            if (doc.activeElement !== event.target) {
                event.target.focus();
            }
        };
        const emptyRelationsContainer = () => {
            while (relationsContainer.lastChild) {
                relationsContainer.removeChild(relationsContainer.lastChild);
            }
        };
        const updateSelectedItemsOrder = (event) => {
            event.preventDefault();

            const inputs = findOrderInputs().reduce((total, input) => {
                return [
                    ...total,
                    {
                        order: parseInt(input.value, 10),
                        row: input.closest(SELECTOR_ROW),
                    },
                ];
            }, []);

            inputs.sort((a, b) => a.order - b.order);

            const fragment = inputs.reduce((frag, item) => {
                frag.appendChild(item.row);

                return frag;
            }, doc.createDocumentFragment());

            emptyRelationsContainer();
            relationsContainer.appendChild(fragment);
            attachRowsEventHandlers();

            selectedItems = inputs.map((item) => parseInt(item.row.dataset.contentId, 10));
            updateInputValue(selectedItems);
        };
        let selectedItems = [...fieldContainer.querySelectorAll(SELECTOR_ROW)].map((row) => parseInt(row.dataset.contentId, 10));
        let selectedItemsMap = selectedItems.reduce((total, item) => ({...total, [item]: item}), {});
        let udwRoot = null;

        updateAddBtnState();
        attachRowsEventHandlers();

        [...fieldContainer.querySelectorAll(SELECTOR_BTN_ADD), ...fieldContainer.querySelectorAll('.ibexa-relations__cta-btn')].forEach(
            (btn) => btn.addEventListener('click', openUDW, false),
        );

        [...fieldContainer.querySelectorAll('.ibexa-relations__table-action--remove-item')].forEach((btn) =>
            btn.addEventListener('click', removeItem, false),
        );

        if (trashBtn) {
            trashBtn.addEventListener('click', removeItems, false);
            trashBtn.addEventListener('click', updateTrashBtnState, false);
        }

        relationsContainer.addEventListener('change', updateTrashBtnState, false);
    });
})(window, window.document, window.ibexa, window.React, window.ReactDOM, window.Translator);
