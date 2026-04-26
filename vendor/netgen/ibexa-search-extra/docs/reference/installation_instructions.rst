Installation instructions
=========================

To install Ibexa CMS Search Extra first add it as a dependency to your project:

.. code-block:: shell

    $ composer require netgen/ibexa-search-extra:^3.0

Once the added dependency is installed, activate the bundle in ``config/bundles.php`` file by adding it to the returned array, together with other required bundles:

.. code-block:: php

    <?php

    return [
        //...

        Netgen\Bundle\IbexaSearchExtraBundle\NetgenIbexaSearchExtraBundle::class => ['all' => true],
    }
