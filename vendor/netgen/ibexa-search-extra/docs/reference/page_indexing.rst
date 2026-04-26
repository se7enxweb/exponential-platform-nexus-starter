Page indexing
=============

This feature implements indexing of Content by scraping the corresponding web page and indexing its content into the
configured search engine. It's implemented for ``solr`` and ``elastic`` search engines.

Configuration
-------------

To enable this feature, set up the page indexing configuration:

.. code-block:: yaml

    netgen_ibexa_search_extra:
        page_indexing:
            enabled: true
            sites:
                site1:
                    tree_root_location_id: '%site1.locations.tree_root.id%'
                    language_siteaccess_map:
                        cro-HR: cro
                        eng-GB: eng
                    fields:
                        level_1:
                            - h1
                        level_2:
                            - h2
                            - h3
                            - div.short
                        level_3:
                            - div.item-short
                    allowed_content_types:
                        - ng_article
                        - ng_frontpage
                    host: "%env(PAGE_INDEXING_HOST)%"
                site2:
                    tree_root_location_id: '%site2.locations.tree_root.id%'
                    language_siteaccess_map:
                        cro-HR: cro
                        eng-GB: eng
                    fields:
                        level_1:
                            - h1
                        level_2:
                            - h2
                            - h3
                            - div.short
                        level_3:
                            - div.item-short
                    allowed_content_types:
                        - ng_landing_page
                    host: "%env(PAGE_INDEXING_HOST)%"

To activate the feature, set the ``enabled`` parameter to true. Define the individual page sites under the ``sites``
array parameter. The available options are:

* ``tree_root_location_id`` **required**

  This is an integer defining the root Location of the site you are configuring.

* ``language_siteaccess_map`` **optional**

  Defines a map of language codes to siteaccess name. When indexing a Content item in a specific language, the URL for
  scraping will be generated for the configured siteaccess.

* ``fields`` **optional**

  Defines a map of indexed fields. Each field contains an array of simplified CSS selectors that will be used to map the
  text content of the scraped page to the given field.

* ``allowed_content_types`` **optional**

  Defines a list ContentType identifiers for Content items that will be indexed as pages. This is a whitelist, meaning
  if you don't configure anything here, nothing will be indexed as page.

* ``host`` **optional**

  In case when it's needed to scrape the page from host that is different from the one configured for the siteaccess,
  you can use this parameter.

For each Content item and language, the configuration will be processed by site sequentially until a match is found.
That means you can use different configuration for a particular language under the same tree root.

.. note::

   Note that source from which the text will be extracted must be delimited by XML comments
   ``<!--begin page content-->`` and ``<!--end page content-->``.

Indexing
--------

This feature is intended for Content items whose pages are built not only from the Content item itself, but from other
Content items as well. Typically, these are landing pages of various kind. That means there is not mechanism to reindex
the page when one of the participating Content item updates. For that reason, you should to set up periodic reindexing
of the pages using the provided command.

Since scraping might not be performant of successful in all cases, you should consider using asynchronous indexing
feature also provided by ``netgen/ibexa-search-extra``.

Command
-------

A command ``netgen-search-extra:index-pages`` is provided to reindex all Content items configured for page indexing.

The command is used to execute initial page indexing when the feature is first added to the project. It goes through
all content types specified in the configuration (``allowed_content_types``) and indexes all existing Content items
of the specified types through their pages:

.. code-block:: console

    bin/console netgen-search-extra:index-pages


The command also has an option ``content-ids``, used to reindex only the given Content items by their IDs:

.. code-block:: console

    bin/console netgen-search-extra:index-pages --content-ids=24,42
