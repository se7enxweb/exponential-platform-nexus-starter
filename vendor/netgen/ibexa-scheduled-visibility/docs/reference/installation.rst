Installation
============

To install Ibexa CMS Scheduled Visibility first add it as a dependency to your project:

.. code-block:: shell

    $ composer require netgen/ibexa-scheduled-visibility:^1.0

Once the added dependency is installed, activate the bundle in ``config/bundles.php`` file by adding it to the returned array, together with other required bundles:

.. code-block:: php

    <?php

    return [
        //...

        Netgen\Bundle\IbexaScheduledVisibilityBundle\NetgenIbexaScheduledVisibilityBundle::class => ['all' => true],
    }
