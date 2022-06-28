Deprecated Features
===================

This document lists all deprecated features in Twig. Deprecated features are
kept for backward compatibility and removed in the next major release (a
feature that was deprecated in Twig 1.x is removed in Twig 2.0).

Deprecation Notices
-------------------

As of Twig 1.21, Twig generates deprecation notices when a template uses
deprecated features. See :ref:`deprecation-notices` for more information.

Macros
------

As of Twig 2.0, macros imported in a file are not available in child templates
anymore (via an ``include`` call for instance). You need to import macros
explicitly in each file where you are using them.

Token Parsers
-------------

* As of Twig 1.x, the token parser broker sub-system is deprecated. The
  following class and interface will be removed in 2.0:

  * ``Twig_TokenParserBrokerInterface``
  * ``Twig_TokenParserBroker``

* As of Twig 1.27, ``\Twig\Parser::getFilename()`` is deprecated. From a token
  parser, use ``$this->parser->getStream()->getSourceContext()->getPath()`` instead.

* As of Twig 1.27, ``\Twig\Parser::getEnvironment()`` is deprecated.

Extensions
----------

* As of Twig 1.x, the ability to remove an extension is deprecated and the
  ``\Twig\Environment::removeExtension()`` method will be removed in 2.0.

* As of Twig 1.23, the ``\Twig\Extension\ExtensionInterface::initRuntime()`` method is
  deprecated. You have two options to avoid the deprecation notice: if you
  implement this method to store the environment for your custom filters,
  functions, or tests, use the ``needs_environment`` option instead; if you
  have more complex needs, explicitly implement
  ``\Twig\Extension\InitRuntimeInterface`` (not recommended).

* As of Twig 1.23, the ``\Twig\Extension\ExtensionInterface::getGlobals()`` method is
  deprecated. Implement ``\Twig\Extension\GlobalsInterface`` to avoid
  deprecation notices.

* As of Twig 1.26, the ``\Twig\Extension\ExtensionInterface::getName()`` method is
  deprecated and it is not used internally anymore.

PEAR
----

PEAR support has been discontinued in Twig 1.15.1, and no PEAR packages are
provided anymore. Use Composer instead.

Filters
-------

* As of Twig 1.x, use ``\Twig\TwigFilter`` to add a filter. The following
  classes and interfaces will be removed in 2.0:

  * ``Twig_FilterInterface``
  * ``Twig_FilterCallableInterface``
  * ``Twig_Filter``
  * ``Twig_Filter_Function``
  * ``Twig_Filter_Method``
  * ``Twig_Filter_Node``

* As of Twig 2.x, the ``Twig_SimpleFilter`` class is deprecated and will be
  removed in Twig 3.x (use ``\Twig\TwigFilter`` instead). In Twig 2.x,
  ``Twig_SimpleFilter`` is just an alias for ``\Twig\TwigFilter``.

Functions
---------

* As of Twig 1.x, use ``\Twig\TwigFunction`` to add a function. The following
  classes and interfaces will be removed in 2.0:

  * ``Twig_FunctionInterface``
  * ``Twig_FunctionCallableInterface``
  * ``Twig_Function``
  * ``Twig_Function_Function``
  * ``Twig_Function_Method``
  * ``Twig_Function_Node``

* As of Twig 2.x, the ``Twig_SimpleFunction`` class is deprecated and will be
  removed in Twig 3.x (use ``\Twig\TwigFunction`` instead). In Twig 2.x,
  ``Twig_SimpleFunction`` is just an alias for ``\Twig\TwigFunction``.

Tests
-----

* As of Twig 1.x, use ``\Twig\TwigTest`` to add a test. The following classes
  and interfaces will be removed in 2.0:

  * ``Twig_TestInterface``
  * ``Twig_TestCallableInterface``
  * ``Twig_Test``
  * ``Twig_Test_Function``
  * ``Twig_Test_Method``
  * ``Twig_Test_Node``

* As of Twig 2.x, the ``Twig_SimpleTest`` class is deprecated and will be
  removed in Twig 3.x (use ``\Twig\TwigTest`` instead). In Twig 2.x,
  ``Twig_SimpleTest`` is just an alias for ``\Twig\TwigTest``.

* The ``sameas`` and ``divisibleby`` tests are deprecated in favor of ``same
  as`` and ``divisible by`` respectively.

Tags
----

* As of Twig 1.x, the ``raw`` tag is deprecated. You should use ``verbatim``
  instead.

Nodes
-----

* As of Twig 1.x, ``Node::toXml()`` is deprecated and will be removed in Twig
  2.0.

* As of Twig 1.26, ``Node::$nodes`` should only contains ``\Twig\Node\Node``
  instances, storing a ``null`` value is deprecated and won't be possible in
  Twig 2.x.

* As of Twig 1.27, the ``filename`` attribute on ``\Twig\Node\ModuleNode`` is
  deprecated. Use ``getName()`` instead.

* As of Twig 1.27, the ``\Twig\Node\Node::getFilename()/\Twig\Node\Node::getLine()``
  methods are deprecated, use
  ``\Twig\Node\Node::getTemplateName()/\Twig\Node\Node::getTemplateLine()`` instead.

Interfaces
----------

* As of Twig 2.x, the following interfaces are deprecated and empty (they will
  be removed in Twig 3.0):

* ``Twig_CompilerInterface``     (use ``\Twig\Compiler`` instead)
* ``Twig_LexerInterface``        (use ``\Twig\Lexer`` instead)
* ``Twig_NodeInterface``         (use ``\Twig\Node\Node`` instead)
* ``Twig_ParserInterface``       (use ``\Twig\Parser`` instead)
* ``\Twig\Loader\ExistsLoaderInterface`` (merged with ``\Twig\Loader\LoaderInterface``)
* ``\Twig\Loader\SourceContextLoaderInterface`` (merged with ``\Twig\Loader\LoaderInterface``)
* ``Twig_TemplateInterface``     (use ``\Twig\Template`` instead, and use
  those constants \Twig\Template::ANY_CALL, \Twig\Template::ARRAY_CALL,
  \Twig\Template::METHOD_CALL)

Compiler
--------

* As of Twig 1.26, the ``\Twig\Compiler::getFilename()`` has been deprecated.
  You should not use it anyway as its values is not reliable.

* As of Twig 1.27, the ``\Twig\Compiler::addIndentation()`` has been deprecated.
  Use ``\Twig\Compiler::write('')`` instead.

Loaders
-------

* As of Twig 1.x, ``Twig_Loader_String`` is deprecated and will be removed in
  2.0. You can render a string via ``\Twig\Environment::createTemplate()``.

* As of Twig 1.27, ``\Twig\Loader\LoaderInterface::getSource()`` is deprecated.
  Implement ``\Twig\Loader\SourceContextLoaderInterface`` instead and use
  ``getSourceContext()``.

Node Visitors
-------------

* Because of the removal of ``Twig_NodeInterface`` in 2.0, you need to extend
  ``\Twig\NodeVisitor\AbstractNodeVisitor`` instead of implementing ``\Twig\NodeVisitor\NodeVisitorInterface``
  directly to make your node visitors compatible with both Twig 1.x and 2.x.

Globals
-------

* As of Twig 2.x, the ability to register a global variable after the runtime
  or the extensions have been initialized is not possible anymore (but
  changing the value of an already registered global is possible).

* As of Twig 1.x, using the ``_self`` global variable to get access to the
  current ``\Twig\Template`` instance is deprecated; most usages only need the
  current template name, which will continue to work in Twig 2.0. In Twig 2.0,
  ``_self`` returns the current template name instead of the current
  ``\Twig\Template`` instance. If you are using ``{{ _self.templateName }}``,
  just replace it with ``{{ _self }}``.

Miscellaneous
-------------

* As of Twig 1.x, ``\Twig\Environment::clearTemplateCache()``,
  ``\Twig\Environment::writeCacheFile()``,
  ``\Twig\Environment::clearCacheFiles()``,
  ``\Twig\Environment::getCacheFilename()``,
  ``\Twig\Environment::getTemplateClassPrefix()``,
  ``\Twig\Environment::getLexer()``, ``\Twig\Environment::getParser()``, and
  ``\Twig\Environment::getCompiler()`` are deprecated and will be removed in 2.0.

* As of Twig 1.x, ``\Twig\Template::getEnvironment()`` and
  ``Twig_TemplateInterface::getEnvironment()`` are deprecated and will be
  removed in 2.0.

* As of Twig 1.21, setting the environment option ``autoescape`` to ``true`` is
  deprecated and will be removed in 2.0. Use ``"html"`` instead.

* As of Twig 1.27, ``\Twig\Error\Error::getTemplateFile()`` and
  ``\Twig\Error\Error::setTemplateFile()`` are deprecated. Use
  ``\Twig\Error\Error::getTemplateName()`` and ``\Twig\Error\Error::setTemplateName()``
  instead.

* As of Twig 1.27, ``\Twig\Template::getSource()`` is deprecated. Use
  ``\Twig\Template::getSourceContext()`` instead.

* As of Twig 1.27, ``\Twig\Parser::addHandler()`` and
  ``\Twig\Parser::addNodeVisitor()`` are deprecated and will be removed in 2.0.

* As of Twig 1.29, some classes are marked as being final via the `@final`
  annotation. Those classes will be marked as final in 2.0.
