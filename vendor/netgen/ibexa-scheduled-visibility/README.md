# Netgen's Scheduled Visibility for Ibexa CMS

[![Build Status](https://img.shields.io/github/actions/workflow/status/netgen/ibexa-scheduled-visibility/tests.yaml?branch=master)](https://github.com/netgen/ibexa-scheduled-visibility/actions)
[![Read the Docs](https://img.shields.io/readthedocs/netgens-scheduled-visibility-for-ibexa-cms)](https://netgens-scheduled-visibility-for-ibexa-cms.readthedocs.io/en/latest/)
[![Downloads](https://img.shields.io/packagist/dt/netgen/ibexa-scheduled-visibility.svg)](https://packagist.org/packages/netgen/ibexa-scheduled-visibility)
[![Latest stable](https://img.shields.io/packagist/v/netgen/ibexa-scheduled-visibility.svg)](https://packagist.org/packages/netgen/ibexa-scheduled-visibility)
[![PHP](https://img.shields.io/badge/PHP-8.1+-%238892BF.svg)](https://www.php.net)
[![Ibexa](https://img.shields.io/badge/Ibexa-4.5+-orange.svg)](https://www.ibexa.co)

**Netgen's Ibexa Scheduled Visibility** enables scheduled publishing of content
based on ``publish_from`` and ``publish_to`` fields and further configuration.

See the
[documentation](https://netgens-scheduled-visibility-for-ibexa-cms.readthedocs.io/en/latest/)
for more details.

## Installation

To install Ibexa CMS Scheduled Visibility first add it as a dependency to your project:

```sh
composer require netgen/ibexa-scheduled-visibility:^1.0
```

Once the added dependency is installed, activate the bundle in `config/bundles.php` file by adding it to the returned array, together with other required bundles:

```php
<?php

return [
    //...

    Netgen\Bundle\IbexaScheduledVisibilityBundle\NetgenIbexaScheduledVisibilityBundle::class => ['all' => true],
}
```
