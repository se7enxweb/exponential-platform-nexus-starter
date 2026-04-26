Visibility handling
===========================

Scheduled visibility comes with several handlers you can use for updating content visibility using
different services from `Ibexa PHP API <https://doc.ibexa.co/en/latest/api/php_api/php_api/>`_:

.. contents::
    :depth: 1
    :local:

Content
-------

This handler uses
`hideContent() <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-ContentService.html#method_hideContent>`_ and
`revealContent() <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-ContentService.html#method_revealContent>`_ methods from
`ContentService <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-ContentService.html>`_.
It hides Content by making all the Locations appear hidden but does not persist hidden state on Location object/s.

Location
--------

This handler uses
`hideLocation() <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-LocationService.html#method_hideLocation>`_ and
`unhideLocation() <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-LocationService.html#method_unhideLocation>`_ methods from
`LocationService <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-LocationService.html>`_.
It hides the Location and marks invisible all descendants of Location.

Content and Location
--------------------

This handler uses
`hideContent() <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-ContentService.html#method_hideContent>`_ and
`revealContent() <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-ContentService.html#method_revealContent>`_ methods from
`ContentService <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-ContentService.html>`_ and
`hideLocation() <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-LocationService.html#method_hideLocation>`_ and
`unhideLocation() <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-LocationService.html#method_unhideLocation>`_ methods from
`LocationService <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-LocationService.html>`_.
It does combined actions of Content and Location handlers mentioned above.

.. _section_handler:

Section
-------

This handler uses `SectionService <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-SectionService.html>`_
and its method `assignSection() <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-SectionService.html#method_assignSection>`_.
Content that is supposed to become hidden or visible will be assigned to :ref:`configured<section_configuration>` sections.

.. _object_state_handler:

Object state
------------

This handler uses `ObjectStateService <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-ObjectStateService.html>`_
and its method `setContentState() <https://doc.ibexa.co/en/latest/api/php_api/php_api_reference/classes/Ibexa-Contracts-Core-Repository-ObjectStateService.html#method_setContentState>`_.
Content that is supposed to be hidden or visible will be assigned to :ref:`configured<object_state_configuration>` object states in object state group.

.. note::

    Both object states must be in the configured object state group.

.. _custom_handler:

Custom handler
------------

Custom handler can be created. It needs to be registered and tagged with:

.. code-block:: yaml

        tags:
            - { name: netgen.ibexa_scheduled_visibility.handler, identifier: custom_identifier }
