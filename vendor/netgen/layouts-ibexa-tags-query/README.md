# Netgen Layouts query based on Netgen Tags for Ibexa CMS

This bundle provides Netgen Layouts query that makes it possible to add items to
block via Tags field type available in any content in Ibexa CMS CMS.

## Installation instructions

Run the following from your installation root to install the package:

```bash
$ composer require netgen/layouts-ibexa-tags-query
```

Symfony Flex will automatically enable the bundle.

Due to how prepending configuration of other bundles works in Symfony, to make
this query type display after the existing Ibexa CMS query type, you need
to add the bundle BEFORE `NetgenLayoutsIbexaBundle` in the list of activated
bundles.

