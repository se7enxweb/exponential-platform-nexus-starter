Fulltext Search Boosting
========================

The Fulltext Search Boost functionality allows fine-tuning of search results by applying configurable boost values to
specific content types, raw fields, and meta-fields. It comes in three parts:

1. **Boosting configuration**

   Boosting configuration is applied during querying the search backend

2. **Meta fields indexing configuration**

   Indexing configuration defines indexing for meta fields that are used in boosting

3. ``Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\FullText`` **criterion**

   A custom criterion implementation that that can define boosting rules per ContentType, raw search engine fields and
   meta fields.

The criterion is currently implemented for ``Solr`` search engine only.

.. note::

    Boosting through the custom ``FullText`` criterion utilizes ``exists`` Solr function, from which it achieves
    **linear** increase of search hit score by the given factor. In contrast to alternative implementations, that makes
    it *controllable* even when using multiple boost rules.

Boosting configuration
----------------------

The boosting configuration is defined under the ``netgen_ibexa_search_extra.fulltext.boost`` key in your project's
configuration files. This structure allows you to define multiple named configurations for different use cases. Each
configuration specifies boost values for content types, raw fields, and meta-fields.

The configuration is structured as follows:

.. code-block:: yaml

    netgen_ibexa_search_extra:
        fulltext:
            boost:
                <name>:
                    content_types:
                        <content_type_identifier>: <boost_value>
                    raw_fields:
                        <raw_field_name>: <boost_value>
                    meta_fields:
                        <meta_field_name>: <boost_value>

- ``<name>``: A unique identifier for the configuration. You can define multiple configurations for different scenarios
  (e.g., ``default``, ``custom``, etc.).
- ``content_types``: Specifies boost values for specific content types. The key is the content type identifier, and the
  value is the boost factor.
- ``raw_fields``: Specifies boost values for raw Solr fields. The key is the field name, and the value is the boost
  factor.
- ``meta_fields``: Specifies boost values for meta-fields. The key is the meta-field name, and the value is the boost
  factor.

Below is an example configuration with a name ``default``:

.. code-block:: yaml

    netgen_ibexa_search_extra:
        fulltext:
            boost:
                default:
                    content_types:
                        article: 2.5
                        blog_post: 1.8
                    raw_fields:
                        meta_content__name_t: 2.1
                    meta_fields:
                        title: 3.14

Meta-fields indexing configuration
----------------------------------

Meta-fields are mapped during indexing, from one or multiple Content Fields. The configuration is defined on the same
level as ``boost``. It allows indexing meta-fields from specific ContentType fields or globally, from all ContentTypes.
There are two ways to define the indexed fields:

1. **Per ContentType**

   Specify the mapping with ContentType identifiers and field names. For example:

   .. code-block:: yaml

    netgen_ibexa_search_extra:
        fulltext:
            meta_fields:
                title:
                    - 'article/title'
                    - 'blog_post/title'

   In this example the ``title`` meta-field is mapped from the ``title`` field of the ``article`` and ``blog_post``
   ContentTypes.

2. **For all ContentTypes**

   Specify just the field name. In this case, the field applies to all ContentTypes. For
   example:

   .. code-block:: yaml

    netgen_ibexa_search_extra:
        fulltext:
              meta_fields:
                  title:
                    - 'title'

   In this example the ``title`` meta-field indexes the ``title`` field from any ContentType.

This flexibility allows you to configure meta-fields either specifically for certain content types or globally across
all content types.

Creating a Criterion
--------------------

The ``ConfiguredFulltextCriterionFactory`` class is responsible for creating ``FullText`` criterion with the specified
boost configuration. When creating a criterion, you can specify the name of the configuration to use. If no name is
provided, the factory defaults to the ``default`` configuration.

To create a ``FullText`` criterion, call the ``create`` method with the search term and the name of the configuration to
use. For example:

.. code-block:: php

    $searchText = trim($request->query->get('searchText', ''));
    $criterion = $configuredFulltextCriterionFactory->create($searchText, 'default');

In this example:

- ``$searchText`` is the user-provided search term.
- ``default`` is the name of the boost configuration to apply.

If the specified configuration name does not exist, an exception will be thrown.

You can also instantiate ``FullText`` criterion manually and set the boosting rules how you see fit:


.. code-block:: php

    use Netgen\IbexaSearchExtra\API\Values\Content\Query\Criterion\FullText;

    $criterion = new FullText();

    $criterion->contentTypeBoost = [
        'article' => 2,
    ];
