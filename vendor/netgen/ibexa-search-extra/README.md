# Netgen's extra bits for Ibexa CMS Search

[![Build Status](https://img.shields.io/github/actions/workflow/status/netgen/ibexa-search-extra/tests.yml?branch=master)](https://github.com/netgen/ibexa-search-extra/actions)
[![Read the Docs](https://img.shields.io/readthedocs/netgens-search-extra-for-ibexa-cms)](https://docs.netgen.io/projects/search-extra)
[![Downloads](https://img.shields.io/packagist/dt/netgen/ibexa-search-extra.svg)](https://packagist.org/packages/netgen/ibexa-search-extra)
[![Latest stable](https://img.shields.io/github/release/netgen/ibexa-search-extra.svg)](https://packagist.org/packages/netgen/ibexa-search-extra)
[![PHP](https://img.shields.io/badge/PHP-%E2%89%A5%208.1-%238892BF.svg)](https://secure.php.net/)
[![Ibexa](https://img.shields.io/badge/Ibexa-%E2%89%A5%204.6-orange.svg)](https://ibexa.co/)

## Features

This only lists all implemented features, see the
[documentation](https://docs.netgen.io/projects/search-extra)
for more details on specific ones.

- [Page indexing](https://docs.netgen.io/projects/search-extra/en/latest/reference/page_indexing.html) (`solr`, `elastic`)

- Custom `FullText` criterion with [configurable boosting](https://docs.netgen.io/projects/search-extra/en/latest/reference/fulltext_search_boosting.html) (`solr`)

- [Extensible DocumentFactory](https://docs.netgen.io/projects/search-extra/en/latest/reference/document_factory.html) (`elastic`)

- Support for [asynchronous indexing](https://docs.netgen.io/projects/search-extra/en/latest/reference/asynchronous_indexing.html) (`solr`, `legacy`)

- [`ContentName`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/API/Values/Content/Query/Criterion/ContentName.php) criterion that works on matched translation's Content name (`solr`, `legacy`)

- [`ContentName`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/API/Values/Content/Query/SortClause/ContentName.php) sort clause that works on matched translation's Content name (`solr`, `legacy`)

- [`ContentId`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/API/Values/Content/Query/Criterion/ContentId.php) and [`LocationId`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/API/Values/Content/Query/Criterion/LocationId.php) criteria with support for range operators (`solr`, `legacy`)

  Supported operators are: `EQ`, `IN`, `GT`, `GTE`, `LT`, `LTE`, `BETWEEN`.

- [`Visible`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/API/Values/Content/Query/Criterion/Visible.php) criterion (`solr`, `elastic`, `legacy`),
  usable in both Content and Location search. The criterion works on compound visibility of Content and Location objects:
  the Content is visible if it's marked as visible; the Location is visible if it's marked as visible, is not hidden by
  one of its ancestor Locations, and its Content is visible.

- [Spellcheck suggestions support](https://docs.netgen.io/projects/search-extra/en/latest/reference/spellcheck_suggestions.html) (`solr`)

- [`CustomField`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/API/Values/Content/Query/SortClause/CustomField.php) sort clause (`solr`)

  Provides a way to sort directly on Solr field by its name.

- Pagerfanta adapters providing access to extra information returned by the search
  engine, aggregations, max score, and execution time (`solr`, `legacy`):

  - [`SearchAdapter`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/Core/Pagination/Pagerfanta/SearchAdapter.php) when using `API` search service
  - [`SearchHandlerAdapter`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/Core/Pagination/Pagerfanta/SearchHandlerAdapter.php) when using `SPI` search handler

- [`ObjectStateIdentifier`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/API/Values/Content/Query/Criterion/ObjectStateIdentifier.php) criterion (`solr`, `legacy`)
- [`SectionIdentifier`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/API/Values/Content/Query/Criterion/SectionIdentifier.php) criterion (`solr`, `legacy`)
- Support for custom Content subdocuments (Solr search engine) (`solr`)

  Provides a way to index custom subdocuments to a Content document and
  [`SubdocumentQuery`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/API/Values/Content/Query/Criterion/SubdocumentQuery.php)
  criterion, available in Content search to define grouped conditions for a custom subdocument.

- [`SubdocumentField`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/API/Values/Content/Query/SortClause/SubdocumentField.php) sort clause (`solr`)

  Provides a way to sort Content by a subdocument field, choosing score calculation mode and optionally limiting with `SubdocumentQuery` criterion.

  **Note:** This will require Solr `6.6` or higher to work correctly with all scoring modes.

- [`LocationQuery`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/API/Values/Content/Query/Criterion/LocationQuery.php) criterion (`solr`, `legacy`)

  Allows grouping of Location criteria so that they apply together on a Location.

- Indexable implementations for [`RichText`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/Core/FieldType/RichText/Indexable.php) (`solr`)

  These implementations shorten text indexed as keyword to 256 characters, which prevents failures
  when the field's content is too big for Solr's string field. They can be controlled with
  semantic configuration (showing defaults):

  ```yaml
  netgen_ibexa_search_extra:
      indexable_field_type:
          ezrichtext:
              enabled: true
              short_text_limit: 256
  ```

- [`Loading`](https://github.com/netgen/ibexa-search-extra/blob/master/lib/Core/Search/Solr/ResultExtractor/LoadingResultExtractor.php) implementation of result extractor (`solr`)

  Loading result extractor gets its value objects
  by loading them from the persistence.
  This prevents:

    - `UnauthorizedException` failures because of the missing `content/versionread` permission
    when Content is updated and incremented current version number is not yet indexed in Solr
    - `NotFoundException` failures when Content/Location is deleted and the corresponding document
    is not yet removed from Solr index

  Usage of loading result extractor can be controlled with semantic configuration (showing defaults):

  ```yaml
  netgen_ibexa_search_extra:
      use_loading_search_result_extractor: true
  ```

## Installation

To install Ibexa Search Extra first add it as a dependency to your project:

```sh
composer require netgen/ibexa-search-extra:^3.0
```

Once the added dependency is installed, activate the bundle in `config/bundles.php` file by adding it to the returned array, together with other required bundles:

```php
<?php

return [
    //...

    Netgen\Bundle\IbexaSearchExtraBundle\NetgenIbexaSearchExtraBundle::class => ['all' => true],
}
```
