Netgen's extra bits for Ibexa CMS Admin UI
==========================================

[![Downloads](https://img.shields.io/packagist/dt/netgen/ibexa-admin-ui-extra.svg)](https://packagist.org/packages/netgen/ibexa-admin-ui-extra)
[![Latest stable](https://img.shields.io/packagist/v/netgen/ibexa-admin-ui-extra.svg)](https://packagist.org/packages/netgen/ibexa-admin-ui-extra)
[![Ibexa](https://img.shields.io/badge/Ibexa-%E2%89%A5%204.6-orange.svg)](https://www.ibexa.co)

Netgen's extra Admin UI bits for Ibexa CMS implements an enhanced administration
UI for Ibexa DXP that adds some missing features we loved from eZ Publish Legacy
administration interface.

Installation & license
----------------------

Install the package with `composer require netgen/ibexa-admin-ui-extra`. This
will automatically enable the bundle, but will not enable the new interface in
Ibexa Admin UI. To enable the interface, you need to set the design of
`admin_group` siteaccess group to `ngadmin`, e.g.:

```yaml
ibexa:
    system:
        admin_group:
            design: ngadmin
```

Next, import the routes into your project:

```yaml
netgen_ibexa_admin_ui_extra:
    resource: '@NetgenIbexaAdminUIExtraBundle/Resources/config/routing.yaml'
```

Content URLs by Siteaccess
--------------------------

This package enhances the visibility of Content URLs by Siteaccess. URLs can be viewed in the administration interface under the **URL** tab within the Content view.

The package distinguishes between two types of URLs:

1. **Siteaccess URLs** that reside within the configured Siteaccess Content tree.
2. **Siteaccess URLs** that exist outside the configured Siteaccess Content tree.

By default, the overview of URLs outside the configured Content tree is disabled.
To display these URLs, you need to enable this option in your configuration:

```yaml
netgen_ibexa_admin_ui_extra:
    show_siteaccess_urls_outside_configured_content_tree_root: true
```

Queues Module
=============

The extra Admin UI also introduces a **Queues module**, which provides a dedicated interface for viewing the number of pending messages in each configured Symfony Messenger transport.

With this module, you can:

* View all available Messenger transports
* See the count of pending messages per transport
* Restrict access to the queues via Ibexa policies

---

Configuration
-------------

You can customize the queues feature in your configuration:

```yaml
netgen_ibexa_admin_ui_extra:
    queues:
        enabled: true
        transports:
            - transport1
            - transport2
```

If Symfony Messenger is **not installed**, the Queues module is automatically disabled and won’t appear in the menu or routes.

---

Permissions
-----------

Access to the Queues module is **restricted by Ibexa policies**. To access the module, the user’s role must include the following policy:

* Module: ``queues``
* Function: ``read``

Users without this policy will not see the Queues menu item and will receive an an **Access Denied** exception if they try to access the page directly.

---

Licensed under [GPLv2](LICENSE)
