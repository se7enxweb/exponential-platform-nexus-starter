const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    /** Content type editing */
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-type-edit-js',
        newItems: [path.resolve(__dirname, '../public/admin/field_definition.js')],
    });
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-layout-css',
        newItems: [path.resolve(__dirname, '../public/admin/field_definition.scss')],
    });

    /** Content editing */
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-edit-parts-js',
        newItems: [path.resolve(__dirname, '../public/admin/field.js')],
    });
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-edit-parts-css',
        newItems: [path.resolve(__dirname, '../public/admin/field.scss')],
    });
};
