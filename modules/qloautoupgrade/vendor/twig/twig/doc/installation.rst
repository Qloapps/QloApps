Installation
============

You have multiple ways to install Twig.

Installing the Twig PHP package
-------------------------------

Install `Composer`_ and run the following command:

.. code-block:: bash

    composer require "twig/twig:^1.0"

Installing the C extension
--------------------------

.. versionadded:: 1.4
    The C extension was added in Twig 1.4.

Twig comes with an **optional** C extension that improves the performance of the
Twig runtime engine.

Note that this extension does not replace the PHP code but only provides an
optimized version of the ``\Twig\Template::getAttribute()`` method; you must
still install the regular PHP code

The C extension is only compatible and useful for **PHP5**.

Install it like any other PHP extensions:

.. code-block:: bash

    cd ext/twig
    phpize
    ./configure
    make
    make install

For Windows:

1. Setup the build environment following the `PHP documentation`_
2. Put Twig's C extension source code into ``C:\php-sdk\phpdev\vcXX\x86\php-source-directory\ext\twig``
3. Use the ``configure --disable-all --enable-cli --enable-twig=shared`` command instead of step 14
4. ``nmake``
5. Copy the ``C:\php-sdk\phpdev\vcXX\x86\php-source-directory\Release_TS\php_twig.dll`` file to your PHP setup.

.. tip::

    For Windows ZendServer, ZTS is not enabled as mentioned in `Zend Server
    FAQ`_.

    You have to use ``configure --disable-all --disable-zts --enable-cli
    --enable-twig=shared`` to be able to build the twig C extension for
    ZendServer.

    The built DLL will be available in
    ``C:\\php-sdk\\phpdev\\vcXX\\x86\\php-source-directory\\Release``

Finally, enable the extension in your ``php.ini`` configuration file:

.. code-block:: ini

    extension=twig.so # For Unix systems
    extension=php_twig.dll # For Windows systems

And from now on, Twig will automatically compile your templates to take
advantage of the C extension.

.. _`download page`:     https://github.com/twigphp/Twig/tags
.. _`Composer`:          https://getcomposer.org/download/
.. _`PHP documentation`: https://wiki.php.net/internals/windows/stepbystepbuild
.. _`Zend Server FAQ`:   https://www.zend.com/en/products/server/faq#faqD6
