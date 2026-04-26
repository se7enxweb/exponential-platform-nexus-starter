Usage
=====

.. contents::
    :depth: 1
    :local:

Content types
-------------

For content to be accepted by scheduled visibility mechanism,
its content type must contain two fields that are either ``ibexa_date`` or ``ibexa_datetime``.
Identifiers of these fields must be ``publish_from`` and ``published_to``.

.. warning::
 ``publish_from`` and ``published_to`` fields must not be translatable.

``publish_from``
~~~~~~~~~~~~~~~~~~~~
It represents time from which content becomes visible.
When set to null, content does not have that limit.

``publish_to``
~~~~~~~~~~~~~~~~~~~~
It represents time until content is visible.
When set to null, content does not have that limit.

When both fields are set to null, no action will be taken.
Naturally, if value of ``publish_from`` field is greater that value of ``publish_to``,
content will not be accepted by the mechanism and no action will be taken.

Command
-------

Bundle contains ``ScheduledVisibilityUpdateCommand``
that searches through configured content and, if necessary, updates its visibility.
Command has a ``limit`` option that represents number of content objects to process in a single iteration.
By default it is set to 1024.
The command also has a ``since`` option that limits processing to content items modified since the given number of days.
By default it is not set, which means all items will be processed.
Command can be executed with: ``bin/console ngscheduledvisibility:update``.
For this mechanism to work how it was intended, this command should be set as cron job with ``--no-interaction`` option.
