``flush``
=========

.. versionadded:: 1.5
    The flush tag was added in Twig 1.5.

The ``flush`` tag tells Twig to flush the output buffer:

.. code-block:: twig

    {% flush %}

.. note::

    Internally, Twig uses the PHP `flush`_ function.

.. _`flush`: https://secure.php.net/flush
