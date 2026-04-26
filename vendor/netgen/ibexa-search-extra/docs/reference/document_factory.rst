Extensible DocumentFactory
==========================

DocumentFactory is an implementation of field mappers for Elasticsearch modeled after the Solr implementation using the
template method pattern. It implements the Elasticsearch ``DocumentFactoryInterface`` and its methods ``fromContent()``
and ``fromLocation()`` add fields to document. These methods index the fields from the suitable field mappers.

``DocumentFactory`` depends on the elasticsearch service ``DocumentFactoryInterface`` , so it is registered in a
Compiler Pass since elasticsearch is not available directly from the bundle.

To use this feature, ensure you have elasticsearch bundle added to your project.

The ``DocumentFactory`` service uses all base field mapper services to index content into the correct document
(content, location, or translation-dependent document):

* ``ContentFieldMapper``

* ``LocationFieldMapper``

* ``ContentTranslationFieldMapper``

* ``LocationTranslationFieldMapper``

* ``BlockFieldMapper``

* ``BlockTranslationFieldMapper``

These services are abstract classes containing methods ``accept()`` and ``mapFields()`` which are implemented by new
field mappers as needed.

To add a new field mapper, create a class that extends one of the base field mappers above, implements its methods, and
registers the service with one of the following tags, depending on the base field mapper:

* ``netgen.ibexa_search_extra.elasticsearch.field_mapper.content``

* ``netgen.ibexa_search_extra.elasticsearch.field_mapper.location``

* ``netgen.ibexa_search_extra.elasticsearch.field_mapper.content_translation``

* ``netgen.ibexa_search_extra.elasticsearch.field_mapper.location_translation``

* ``netgen.ibexa_search_extra.elasticsearch.field_mapper.block_translation``
