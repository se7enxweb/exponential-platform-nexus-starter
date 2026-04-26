Configuration
=============

.. contents::
    :depth: 1
    :local:

Default configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: yaml

    netgen_ibexa_scheduled_visibility:
        enabled: false
        handler: 'content'
        content_types:
            all: false
            allowed: []
        sections:
            visible:
                section_id: 0
            hidden:
                section_id: 0
        object_states:
            object_state_group_id: 0
            visible:
                object_state_id: 0
            hidden:
                object_state_id: 0

Enabling scheduled visibility
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In order to use Ibexa scheduled visibility, you have to enable it:

.. code-block:: yaml

    netgen_ibexa_scheduled_visibility:
        enabled: true

Changing visibility handlers:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In order to change :doc:`visibility handler <visibility_handling>`,
you need to set it to one of supported ones:
**'content'**, **'location'**, **'content_location'**, **'section'**, **'object_state'**
or create your own :ref:`custom handler<custom_handler>`:

.. code-block:: yaml

    netgen_ibexa_scheduled_visibility:
        handler: 'section'

Content type limitations
~~~~~~~~~~~~~~~~~~~~~~~~

In order to include all content types in scheduled visibility mechanism you need to enable it:

.. code-block:: yaml

    netgen_ibexa_scheduled_visibility:
        content_types:
            all: true

In order to limit content types you need to disable previously mentioned setting for all content types
and enter content types to be included in scheduled visibility mechanism:

.. code-block:: yaml

    netgen_ibexa_scheduled_visibility:
        content_types:
            all: false
            allowed: ['content_type_1', 'content_type_2']

.. _section_configuration:

Section handler configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If :ref:`'section'<section_handler>` has been chosen as preferred visibility handler,
ids of sections used for visible and hidden content need to be configured:

.. code-block:: yaml

    netgen_ibexa_scheduled_visibility:
        handler: 'section'
        sections:
            visible:
                section_id: 1
            hidden:
                section_id: 2

.. _object_state_configuration:

Object state handler configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If :ref:`'object_state'<object_state_handler>` has been chosen as preferred visibility handler,
ids of object states used for visible and hidden content need to be configured,
as well as object state group id in which both of these states are:

.. code-block:: yaml

    netgen_ibexa_scheduled_visibility:
        handler: 'object_state'
        object_states:
            object_state_group_id: 1
            visible:
                object_state_id: 1
            hidden:
                object_state_id: 2

.. note::

    Both object states must be in the configured object state group.
