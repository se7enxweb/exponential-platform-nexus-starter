Asynchronous indexing
=====================

``Asynchronous indexing`` implements asynchronous indexing mechanism for Repository PHP API through Symfony's Messenger
component. This solves a number of use cases where standard synchronous indexing mechanism fails, because it tries to
execute indexing as part of the PHP API call. For example, hiding a large subtree though the Admin UI will fail with
standard synchronous indexing implementation, while with asynchronous indexing enabled it will be processed in the
background, without blocking the UI or causing timeouts.

This also enables sane implementation of various custom use cases, for example indexing file's content, which might also
require more time to execute and hence block the UI or cause timeouts.

In order to enable asynchronous indexing, use the following configuration:

.. code-block:: yaml

    netgen_ibexa_search_extra:
        use_asynchronous_indexing: true

`Default Messenger component configuration <https://github.com/netgen/ibexa-search-extra/blob/master/bundle/Resources/config/messenger.yaml>`_
will be automatically loaded. In case you want to use a different configuration, you will have to override it in the
project code. Note that it would be recommended to configure the bus used with ``doctrine_ping_connection`` middleware,
since the asynchronous indexing consumer will typically be a long running process, which could otherwise lose the
connection to the database. This setting is not included in the default configuration as it's a global setting and you
might not want to have it enabled by default. You can enable it in the ``buses`` part of the Messenger configuration
with:

.. code-block:: yaml

    buses:
        messenger.bus.default:
            middleware:
                - doctrine_ping_connection

Additionally, you will need to start the consumer to process the queue. For the default configuration, you can do that
with:

.. code-block:: console

    bin/console messenger:consume netgen_ibexa_search_extra_asynchronous_indexing --time-limit=1800 --limit=4096

.. note::

    Event subscribers use a Messenger message bus service named ``netgen.ibexa_search_extra.asynchronous_indexing.messenger.bus``,
    which is an alias to the default ``messenger.default_bus`` service. This enables override of the message bus used in
    event listeners when needed.
