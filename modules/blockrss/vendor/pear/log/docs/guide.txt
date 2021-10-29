=================
 The Log Package
=================

--------------------
 User Documentation
--------------------

:Author:        Jon Parise
:Contact:       jon@php.net

.. contents:: Contents
.. section-numbering::

Using Log Handlers
==================

The Log package is implemented as a framework that supports the notion of
backend-specific log handlers.  The base logging object (defined by the `Log
class`_) is primarily an abstract interface to the currently configured
handler.

A wide variety of handlers are distributed with the Log package, and, should
none of them fit your application's needs, it's easy to `write your own`__.

.. _Log class: http://cvs.php.net/viewvc.cgi/pear/Log/Log.php
__ `Custom Handlers`_

Creating a Log Object
---------------------
There are three ways to create Log objects:

    - Using the ``Log::factory()`` method
    - Using the ``Log::singleton()`` method
    - Direct instantiation

The Factory Method
~~~~~~~~~~~~~~~~~~
The ``Log::factory()`` method implements the `Factory Pattern`_.  It allows
for the parameterized construction of concrete Log instances at runtime.  The
first parameter to the ``Log::factory()`` method indicates the name of the
concrete handler to create.  The rest of the parameters will be passed on to
the handler's constructor (see `Configuring a Handler`_ below).

The new ``Log`` instance is returned by reference.

::

    require_once 'Log.php';

    $console = Log::factory('console', '', 'TEST');
    $console->log('Logging to the console.');

    $file = Log::factory('file', 'out.log', 'TEST');
    $file->log('Logging to out.log.');

.. _Factory Pattern: http://wikipedia.org/wiki/Factory_method_pattern

The Singleton Method
~~~~~~~~~~~~~~~~~~~~
The ``Log::singleton()`` method implements the `Singleton Pattern`_.  The
singleton pattern ensures that only a single instance of a given log type and
configuration is ever created.  This has two benefits: first, it prevents
duplicate ``Log`` instances from being constructed, and, second, it gives all
of your code access to the same ``Log`` instance.  The latter is especially
important when logging to files because only a single file handler will need
to be managed.

The ``Log::singleton()`` method's parameters match the ``Log::factory()``
method.  The new ``Log`` instance is returned by reference.

::

    require_once 'Log.php';

    /* Same construction parameters */
    $a = Log::singleton('console', '', 'TEST');
    $b = Log::singleton('console', '', 'TEST');

    if ($a === $b) {
        echo '$a and $b point to the same Log instance.' . "\n";
    }

    /* Different construction parameters */
    $c = Log::singleton('console', '', 'TEST1');
    $d = Log::singleton('console', '', 'TEST2');

    if ($c !== $d) {
        echo '$c and $d point to different Log instances.' . "\n";
    }

.. _Singleton Pattern: http://wikipedia.org/wiki/Singleton_pattern

Direct Instantiation
~~~~~~~~~~~~~~~~~~~~
It is also possible to directly instantiate concrete ``Log`` handler
instances.  However, this method is **not recommended** because it creates a
tighter coupling between your application code and the Log package than is
necessary.  Use of `the factory method`_ or `the singleton method`_ is
preferred.


Configuring a Handler
---------------------
A log handler's configuration is determined by the arguments used in its
construction.  Here's an overview of those parameters::

    /* Using the factory method ... */
    Log::factory($handler, $name, $ident, $conf, $maxLevel);

    /* Using the singleton method ... */
    Log::singleton($handler, $name, $ident, $conf, $maxLevel);

    /* Using direct instantiation ... */
    new Log_handler($name, $ident, $conf, $maxLevel);

+---------------+-----------+-----------------------------------------------+
| Parameter     | Type      | Description                                   |
+===============+===========+===============================================+
| ``$handler``  | String    | The type of Log handler to construct.  This   |
|               |           | parameter is only available when `the factory |
|               |           | method`_ or `the singleton method`_ are used. |
+---------------+-----------+-----------------------------------------------+
| ``$name``     | String    | The name of the log resource to which the     |
|               |           | events will be logged.  The use of this value |
|               |           | is determined by the handler's implementation.|
|               |           | It defaults to an empty string.               |
+---------------+-----------+-----------------------------------------------+
| ``$ident``    | String    | An identification string that will be included|
|               |           | in all log events logged by this handler.     |
|               |           | This value defaults to an empty string and can|
|               |           | be changed at runtime using the ``setIdent()``|
|               |           | method.                                       |
+---------------+-----------+-----------------------------------------------+
| ``$conf``     | Array     | Associative array of key-value pairs that are |
|               |           | used to specify any handler-specific settings.|
+---------------+-----------+-----------------------------------------------+
| ``$level``    | Integer   | Log messages up to and including this level.  |
|               |           | This value defaults to ``PEAR_LOG_DEBUG``.    |
|               |           | See `Log Levels`_ and `Log Level Masks`_.     |
+---------------+-----------+-----------------------------------------------+


Logging an Event
----------------
Events are logged using the ``log()`` method::

    $logger->log('Message', PEAR_LOG_NOTICE);

The first argument contains the log event's message.  Even though the event is
always logged as a string, it is possible to pass an object to the ``log()``
method.  If the object implements a ``getString()`` method, a ``toString()``
method or Zend Engine 2's special ``__toString()`` casting method, it will be
used to determine the object's string representation.  Otherwise, the
`serialized`_ form of the object will be logged.

The second, optional argument specifies the log event's priority.  See the
`Log Levels`_ table for the complete list of priorities.  The default priority
is PEAR_LOG_INFO.

The ``log()`` method will return ``true`` if the event was successfully
logged.

"Shortcut" methods are also available for logging an event at a specific log
level.  See the `Log Levels`_ table for the complete list.

.. _serialized: http://www.php.net/serialize


Log Levels
----------
This table is ordered by highest priority (``PEAR_LOG_EMERG``) to lowest
priority (``PEAR_LOG_DEBUG``).

+-----------------------+---------------+-----------------------------------+
| Level                 | Shortcut      | Description                       |
+=======================+===============+===================================+
| ``PEAR_LOG_EMERG``    | ``emerg()``   | System is unusable                |
+-----------------------+---------------+-----------------------------------+
| ``PEAR_LOG_ALERT``    | ``alert()``   | Immediate action required         |
+-----------------------+---------------+-----------------------------------+
| ``PEAR_LOG_CRIT``     | ``crit()``    | Critical conditions               |
+-----------------------+---------------+-----------------------------------+
| ``PEAR_LOG_ERR``      | ``err()``     | Error conditions                  |
+-----------------------+---------------+-----------------------------------+
| ``PEAR_LOG_WARNING``  | ``warning()`` | Warning conditions                |
+-----------------------+---------------+-----------------------------------+
| ``PEAR_LOG_NOTICE``   | ``notice()``  | Normal but significant            |
+-----------------------+---------------+-----------------------------------+
| ``PEAR_LOG_INFO``     | ``info()``    | Informational                     |
+-----------------------+---------------+-----------------------------------+
| ``PEAR_LOG_DEBUG``    | ``debug()``   | Debug-level messages              |
+-----------------------+---------------+-----------------------------------+


Log Level Masks
---------------
Defining a log level mask allows you to include and/or exclude specific levels
of events from being logged.  The ``$level`` construction parameter (see
`Configuring a Handler`_) uses this mechanism to exclude log events below a
certain priority, and it's possible to define more complex masks once the Log
object has been constructed.

Each priority has a specific mask associated with it.  To compute a priority's
mask, use the static ``Log::MASK()`` method::

    $mask = Log::MASK(PEAR_LOG_INFO);

To compute the mask for all priorities up to, and including, a certain level,
use the ``Log::MAX()`` static method::

    $mask = Log::MAX(PEAR_LOG_INFO);

To compute the mask for all priorities greater than or equal to a certain
level, use the ``Log::MIN()`` static method::

    $mask = Log::MIN(PEAR_LOG_INFO);

The apply the mask, use the ``setMask()`` method::

    $logger->setMask($mask);

Masks can be be combined using bitwise operations.  To restrict logging to
only those events marked as ``PEAR_LOG_NOTICE`` or ``PEAR_LOG_DEBUG``::

    $mask = Log::MASK(PEAR_LOG_NOTICE) | Log::MASK(PEAR_LOG_DEBUG);
    $logger->setMask($mask);

For convenience, two special masks are predefined: ``PEAR_LOG_NONE`` and
``PEAR_LOG_ALL``.  ``PEAR_LOG_ALL`` is especially useful for excluding only
specific priorities::

    $mask = PEAR_LOG_ALL ^ Log::MASK(PEAR_LOG_NOTICE);
    $logger->setMask($mask);

It is also possible to retrieve and modify a Log object's existing mask::

    $mask = $logger->getMask() | Log::MASK(PEAR_LOG_INFO);
    $logger->setMask($mask);


Log Line Format
---------------
Most log handlers support configurable line formats.  The following is a list
of special tokens that will be expanded at runtime with contextual information
related to the log event.  Each token has an alternate shorthand notation, as
well.

+------------------+-----------+--------------------------------------------+
| Token            | Alternate | Description                                |
+==================+===========+============================================+
| ``%{timestamp}`` | ``%1$s``  | Timestamp.  This is often configurable.    |
+------------------+-----------+--------------------------------------------+
| ``%{ident}``     | ``%2$s``  | The log handler's identification string.   |
+------------------+-----------+--------------------------------------------+
| ``%{priority}``  | ``%3$s``  | The log event's priority.                  |
+------------------+-----------+--------------------------------------------+
| ``%{message}``   | ``%4$s``  | The log event's message text.              |
+------------------+-----------+--------------------------------------------+
| ``%{file}``      | ``%5$s``  | The full filename of the logging file.     |
+------------------+-----------+--------------------------------------------+
| ``%{line}``      | ``%6$s``  | The line number on which the event occured.|
+------------------+-----------+--------------------------------------------+
| ``%{function}``  | ``%7$s``  | The function from which the event occurred.|
+------------------+-----------+--------------------------------------------+
| ``%{class}``     | ``%8$s``  | The class in which the event occurred.     |
+------------------+-----------+--------------------------------------------+


Flushing Log Events
-------------------
Some log handlers (such as `the console handler`_) support explicit
"buffering".  When buffering is enabled, log events won't actually be written
to the output stream until the handler is closed.  Other handlers (such as
`the file handler`_) support implicit buffering because they use the operating
system's IO routines, which may buffer the output.

It's possible to force these handlers to flush their output, however, by
calling their ``flush()`` method::

    $conf = array('buffering' => true);
    $logger = Log::singleton('console', '', 'test', $conf);

    for ($i = 0; $i < 10; $i++) {
        $logger->log('This event will be buffered.');
    }

    /* Flush all of the buffered log events. */
    $logger->flush();

    for ($i = 0; $i < 10; $i++) {
        $logger->log('This event will be buffered.');
    }

    /* Implicitly flush the buffered events on close. */
    $logger->close();

At this time, the ``flush()`` method is only implemented by `the console
handler`_, `the file handler`_, `the Firebug handler`_, and `the mail
handler`_.


Standard Log Handlers
=====================

The Console Handler
-------------------
The Console handler outputs log events directly to the console.  It supports
output buffering and configurable string formats.

Configuration
~~~~~~~~~~~~~
+-------------------+-----------+---------------+---------------------------+
| Parameter         | Type      | Default       | Description               |
+===================+===========+===============+===========================+
| ``stream``        | File      | STDOUT_       | The output stream to use. |
+-------------------+-----------+---------------+---------------------------+
| ``buffering``     | Boolean   | False         | Should the output be      |
|                   |           |               | buffered until shutdown?  |
+-------------------+-----------+---------------+---------------------------+
| ``lineFormat``    | String    | ``%1$s %2$s   | `Log line format`_        |
|                   |           | [%3$s] %4$s`` | specification.            |
+-------------------+-----------+---------------+---------------------------+
| ``timeFormat``    | String    | ``%b %d       | Time stamp format         |
|                   |           | %H:%M:%S``    | (for strftime_).          |
+-------------------+-----------+---------------+---------------------------+

.. _STDOUT: http://www.php.net/wrappers.php
.. _strftime: http://www.php.net/strftime

Example
~~~~~~~
::

    $logger = Log::singleton('console', '', 'ident');
    for ($i = 0; $i < 10; $i++) {
        $logger->log("Log entry $i");
    }


The Display Handler
-------------------
The Display handler simply prints the log events back to the browser.  It
respects the ``error_prepend_string`` and ``error_append_string`` `error
handling values`_ and is useful when `logging from standard error handlers`_.

Configuration
~~~~~~~~~~~~~
+-------------------+-----------+---------------+---------------------------+
| Parameter         | Type      | Default       | Description               |
+===================+===========+===============+===========================+
| ``lineFormat``    | String    | ``<b>%3$s</b>:| `Log line format`_        |
|                   |           | %4$s``        | specification.            |
+-------------------+-----------+---------------+---------------------------+
| ``timeFormat``    | String    | ``%b %d       | Time stamp format         |
|                   |           | %H:%M:%S``    | (for strftime_).          |
+-------------------+-----------+---------------+---------------------------+
| ``error_prepend`` | String    | PHP INI value | This string will be       |
|                   |           |               | prepended to the line     |
|                   |           |               | format.                   |
+-------------------+-----------+---------------+---------------------------+
| ``error_append``  | String    | PHP INI value | This string will be       |
|                   |           |               | appended to the line      |
|                   |           |               | format.                   |
+-------------------+-----------+---------------+---------------------------+
| ``linebreak``     | String    | ``<br />\n``  | This string is used to    |
|                   |           |               | represent a line break.   |
+-------------------+-----------+---------------+---------------------------+
| ``rawText``       | Boolean   | False         | Should message text be    |
|                   |           |               | passed directly to the log|
|                   |           |               | system?  Otherwise, it    |
|                   |           |               | will be converted to an   |
|                   |           |               | HTML-safe representation. |
+-------------------+-----------+---------------+---------------------------+

.. _error handling values: http://www.php.net/errorfunc

Example
~~~~~~~
::

    $conf = array('error_prepend' => '<font color="#ff0000"><tt>',
                  'error_append'  => '</tt></font>');
    $logger = Log::singleton('display', '', '', $conf, PEAR_LOG_DEBUG);
    for ($i = 0; $i < 10; $i++) {
        $logger->log("Log entry $i");
    }


The Error_Log Handler
---------------------
The Error_Log handler sends log events to PHP's `error_log()`_ function.

Configuration
~~~~~~~~~~~~~
+-------------------+-----------+---------------+---------------------------+
| Parameter         | Type      | Default       | Description               |
+===================+===========+===============+===========================+
| ``destination``   | String    | '' `(empty)`  | Optional destination value|
|                   |           |               | for `error_log()`_.  See  |
|                   |           |               | `Error_Log Types`_ for    |
|                   |           |               | more details.             |
+-------------------+-----------+---------------+---------------------------+
| ``extra_headers`` | String    | '' `(empty)`  | Additional headers to pass|
|                   |           |               | to the `mail()`_ function |
|                   |           |               | when the                  |
|                   |           |               | ``PEAR_LOG_TYPE_MAIL``    |
|                   |           |               | type is specified.        |
+-------------------+-----------+---------------+---------------------------+
| ``lineFormat``    | String    | ``%2$s: %4$s``| `Log line format`_        |
|                   |           |               | specification.            |
+-------------------+-----------+---------------+---------------------------+
| ``timeFormat``    | String    | ``%b %d       | Time stamp format         |
|                   |           | %H:%M:%S``    | (for strftime_).          |
+-------------------+-----------+---------------+---------------------------+

Error_Log Types
~~~~~~~~~~~~~~~
All of the available log types are detailed in the `error_log()`_ section of
the PHP manual.  For your convenience, the Log package also defines the
following constants that can be used for the ``$name`` handler construction
parameter.

+---------------------------+-----------------------------------------------+
| Constant                  | Description                                   |
+===========================+===============================================+
| ``PEAR_LOG_TYPE_SYSTEM``  | Log events are sent to PHP's system logger,   |
|                           | which uses the operating system's logging     |
|                           | mechanism or a file (depending on the value   |
|                           | of the `error_log configuration directive`_). |
+---------------------------+-----------------------------------------------+
| ``PEAR_LOG_TYPE_MAIL``    | Log events are sent via email to the address  |
|                           | specified in the ``destination`` value.       |
+---------------------------+-----------------------------------------------+
| ``PEAR_LOG_TYPE_DEBUG``   | Log events are sent through PHP's debugging   |
|                           | connection.  This will only work if           |
|                           | `remote debugging`_ has been enabled.  The    |
|                           | ``destination`` value is used to specify the  |
|                           | host name or IP address of the target socket. |
+---------------------------+-----------------------------------------------+
| ``PEAR_LOG_TYPE_FILE``    | Log events will be appended to the file named |
|                           | by the ``destination`` value.                 |
+---------------------------+-----------------------------------------------+
| ``PEAR_LOG_TYPE_SAPI``    | Log events will be sent directly to the SAPI  |
|                           | logging handler.                              |
+---------------------------+-----------------------------------------------+

.. _error_log(): http://www.php.net/error_log
.. _mail(): http://www.php.net/mail
.. _error_log configuration directive: http://www.php.net/errorfunc#ini.error-log
.. _remote debugging: http://www.php.net/install.configure#install.configure.enable-debugger

Example
~~~~~~~
::

    $logger = Log::singleton('error_log', PEAR_LOG_TYPE_SYSTEM, 'ident');
    for ($i = 0; $i < 10; $i++) {
        $logger->log("Log entry $i");
    }


The File Handler
----------------
The File handler writes log events to a text file using configurable string
formats.

Configuration
~~~~~~~~~~~~~
+-------------------+-----------+---------------+---------------------------+
| Parameter         | Type      | Default       | Description               |
+===================+===========+===============+===========================+
| ``append``        | Boolean   | True          | Should new log entries be |
|                   |           |               | append to an existing log |
|                   |           |               | file, or should the a new |
|                   |           |               | log file overwrite an     |
|                   |           |               | existing one?             |
+-------------------+-----------+---------------+---------------------------+
| ``locking``       | Boolean   | False         | Should advisory file      |
|                   |           |               | locking (using flock_) be |
|                   |           |               | used?                     |
+-------------------+-----------+---------------+---------------------------+
| ``mode``          | Integer   | 0644          | Octal representation of   |
|                   |           |               | the log file's permissions|
|                   |           |               | mode.                     |
+-------------------+-----------+---------------+---------------------------+
| ``dirmode``       | Integer   | 0755          | Octal representation of   |
|                   |           |               | the file permission mode  |
|                   |           |               | that will be used when    |
|                   |           |               | creating directories that |
|                   |           |               | do not already exist.     |
+-------------------+-----------+---------------+---------------------------+
| ``eol``           | String    | OS default    | The end-of-line character |
|                   |           |               | sequence.                 |
+-------------------+-----------+---------------+---------------------------+
| ``lineFormat``    | String    | ``%1$s %2$s   | `Log line format`_        |
|                   |           | [%3$s] %4$s`` | specification.            |
+-------------------+-----------+---------------+---------------------------+
| ``timeFormat``    | String    | ``%b %d       | Time stamp format         |
|                   |           | %H:%M:%S``    | (for strftime_).          |
+-------------------+-----------+---------------+---------------------------+

.. _flock: http://www.php.net/flock
.. _strftime: http://www.php.net/strftime

The file handler will only attempt to set the ``mode`` value if it was
responsible for creating the file.

Example
~~~~~~~
::

    $conf = array('mode' => 0600, 'timeFormat' => '%X %x');
    $logger = Log::singleton('file', 'out.log', 'ident', $conf);
    for ($i = 0; $i < 10; $i++) {
        $logger->log("Log entry $i");
    }


The Firebug Handler
-------------------
The Firebug handler outputs log events to the Firebug_ console. It supports
output buffering and configurable string formats.

Configuration
~~~~~~~~~~~~~
+-------------------+-----------+---------------+---------------------------+
| Parameter         | Type      | Default       | Description               |
+===================+===========+===============+===========================+
| ``buffering``     | Boolean   | False         | Should the output be      |
|                   |           |               | buffered until shutdown?  |
+-------------------+-----------+---------------+---------------------------+
| ``lineFormat``    | String    | ``%2$s [%3$s] | `Log line format`_        |
|                   |           | %4$s``        | specification.            |
+-------------------+-----------+---------------+---------------------------+
| ``timeFormat``    | String    | ``%b %d       | Time stamp format         |
|                   |           | %H:%M:%S``    | (for strftime_).          |
+-------------------+-----------+---------------+---------------------------+

.. _Firebug: http://www.getfirebug.com/
.. _strftime: http://www.php.net/strftime

Example
~~~~~~~
::

    $logger = Log::singleton('firebug', '', 'ident');
    for ($i = 0; $i < 10; $i++) {
        $logger->log("Log entry $i");
    }


The Mail Handler
----------------

The Mail handler aggregates a session's log events and sends them in the body
of an email message using either the `PEAR Mail`_ package or PHP's native
`mail()`_ function.

If an empty ``mailBackend`` value is specified, the `mail()`_ function will be
used instead of the `PEAR Mail`_ package.

Multiple recipients can be specified by separating their email addresses with
commas in the ``$name`` construction parameter.

Configuration
~~~~~~~~~~~~~
+-------------------+-----------+---------------+---------------------------+
| Parameter         | Type      | Default       | Description               |
+===================+===========+===============+===========================+
| ``from``          | String    | sendmail_from | Value for the message's   |
|                   |           | INI value     | ``From:`` header.         |
+-------------------+-----------+---------------+---------------------------+
| ``subject``       | String    | ``[Log_mail]  | Value for the message's   |
|                   |           | Log message`` | ``Subject:`` header.      |
+-------------------+-----------+---------------+---------------------------+
| ``preamble``      | String    | `` `(empty)`  | Preamble for the message. |
+-------------------+-----------+---------------+---------------------------+
| ``lineFormat``    | String    | ``%1$s %2$s   | `Log line format`_        |
|                   |           | [%3$s] %4$s`` | specification.            |
+-------------------+-----------+---------------+---------------------------+
| ``timeFormat``    | String    | ``%b %d       | Time stamp format         |
|                   |           | %H:%M:%S``    | (for strftime_).          |
+-------------------+-----------+---------------+---------------------------+
| ``mailBackend``   | String    | `` `(empty)`  | Name of the Mail package  |
|                   |           |               | backend to use.           |
+-------------------+-----------+---------------+---------------------------+
| ``mailParams``    | Array     | `(empty)`     | Array of parameters that  |
|                   |           |               | will be passed to the     |
|                   |           |               | Mail package backend.     |
+-------------------+-----------+---------------+---------------------------+

.. _PEAR Mail: http://pear.php.net/package/Mail
.. _mail(): http://www.php.net/mail

Example
~~~~~~~
::

    $conf = array('subject' => 'Important Log Events');
    $logger = Log::singleton('mail', 'webmaster@example.com', 'ident', $conf);
    for ($i = 0; $i < 10; $i++) {
        $logger->log("Log entry $i");
    }


The MDB2 Handler
----------------
The MDB2 handler is similar to `the SQL (DB) handler`_, but instead of using
the PEAR DB package, it uses the `MDB2 database abstraction package`_.

Configuration
~~~~~~~~~~~~~
+-------------------+-----------+---------------+---------------------------+
| Parameter         | Type      | Default       | Description               |
+===================+===========+===============+===========================+
| ``dsn``           | Mixed     | '' `(empty)`  | A `Data Source Name`_.    |
|                   |           |               | |required|                |
+-------------------+-----------+---------------+---------------------------+
| ``options``       | Array     | ``persistent``| An array of `MDB2`_       |
|                   |           |               | options.                  |
+-------------------+-----------+---------------+---------------------------+
| ``db``            | Object    | NULL          | An existing `MDB2`_       |
|                   |           |               | object.  If specified,    |
|                   |           |               | this object will be used, |
|                   |           |               | and ``dsn`` will be       |
|                   |           |               | ignored.                  |
+-------------------+-----------+---------------+---------------------------+
| ``sequence``      | String    | ``log_id``    | The name of the sequence  |
|                   |           |               | to use when generating    |
|                   |           |               | unique event IDs.   Under |
|                   |           |               | many databases, this will |
|                   |           |               | be used as the name of    |
|                   |           |               | the sequence table.       |
+-------------------+-----------+---------------+---------------------------+
| ``identLimit``    | Integer   | 16            | The maximum length of the |
|                   |           |               | ``ident`` string.         |
|                   |           |               | **Changing this value may |
|                   |           |               | require updates to the SQL|
|                   |           |               | schema, as well.**        |
+-------------------+-----------+---------------+---------------------------+
| ``singleton``     | Boolean   | false         | Is true, use a singleton  |
|                   |           |               | database object using     |
|                   |           |               | `MDB2::singleton()`_.     |
+-------------------+-----------+---------------+---------------------------+

.. _MDB2: http://pear.php.net/package/MDB2
.. _MDB2 database abstraction package: MDB2_
.. _MDB2::singleton(): http://pear.php.net/package/MDB2/docs/latest/MDB2/MDB2.html#methodsingleton

The Null Handler
----------------
The Null handler simply consumes log events (akin to sending them to
``/dev/null``).  `Log level masks`_ are respected, and the event will still be
sent to any registered `log observers`_.

Example
~~~~~~~
::

    $logger = Log::singleton('null');
    for ($i = 0; $i < 10; $i++) {
        $logger->log("Log entry $i");
    }


The SQL (DB) Handler
--------------------

The SQL handler sends log events to a database using `PEAR's DB abstraction
layer`_.

**Note:** Due to the constraints of the default database schema, the SQL
handler limits the length of the ``$ident`` string to sixteen (16) characters.
This limit can be adjusted using the ``identLimit`` configuration parameter.

The Log Table
~~~~~~~~~~~~~
The default SQL table used by this handler looks like this::

    CREATE TABLE log_table (
        id          INT NOT NULL,
        logtime     TIMESTAMP NOT NULL,
        ident       CHAR(16) NOT NULL,
        priority    INT NOT NULL,
        message     VARCHAR(200),
        PRIMARY KEY (id)
    );

This is the "lowest common denominator" that should work across all SQL
compliant database.  You may want to make database- or site-specific changes
to this schema to support your specific needs, however.  For example,
`PostgreSQL`_ users may prefer to use a ``TEXT`` type for the ``message``
field.

.. _PostgreSQL: http://www.postgresql.org/

Configuration
~~~~~~~~~~~~~
+-------------------+-----------+---------------+---------------------------+
| Parameter         | Type      | Default       | Description               |
+===================+===========+===============+===========================+
| ``dsn``           | Mixed     | '' `(empty)`  | A `Data Source Name`_.    |
|                   |           |               | |required|                |
+-------------------+-----------+---------------+---------------------------+
| ``sql``           | String    | |sql-default| | SQL insertion statement.  |
+-------------------+-----------+---------------+---------------------------+
| ``options``       | Array     | ``persistent``| An array of `DB`_ options.|
+-------------------+-----------+---------------+---------------------------+
| ``db``            | Object    | NULL          | An existing `DB`_ object. |
|                   |           |               | If specified, this object |
|                   |           |               | will be used, and ``dsn`` |
|                   |           |               | will be ignored.          |
+-------------------+-----------+---------------+---------------------------+
| ``sequence``      | String    | ``log_id``    | The name of the sequence  |
|                   |           |               | to use when generating    |
|                   |           |               | unique event IDs.   Under |
|                   |           |               | many databases, this will |
|                   |           |               | be used as the name of    |
|                   |           |               | the sequence table.       |
+-------------------+-----------+---------------+---------------------------+
| ``identLimit``    | Integer   | 16            | The maximum length of the |
|                   |           |               | ``ident`` string.         |
|                   |           |               | **Changing this value may |
|                   |           |               | require updates to the SQL|
|                   |           |               | schema, as well.**        |
+-------------------+-----------+---------------+---------------------------+

The name of the database table to which the log entries will be written is
specified using the ``$name`` construction parameter (see `Configuring a
Handler`_).

.. |sql-default| replace:: ``INSERT INTO $table (id, logtime, ident, priority, message) VALUES(?, CURRENT_TIMESTAMP, ?, ?, ?)``

.. _DB: http://pear.php.net/package/DB
.. _PEAR's DB abstraction layer: DB_
.. _Data Source Name: http://pear.php.net/manual/en/package.database.db.intro-dsn.php

Examples
~~~~~~~~
Using a `Data Source Name`_ to create a new database connection::

    $conf = array('dsn' => 'pgsql://jon@localhost+unix/logs');
    $logger = Log::singleton('sql', 'log_table', 'ident', $conf);
    for ($i = 0; $i < 10; $i++) {
        $logger->log("Log entry $i");
    }

Using an existing `DB`_ object::

    require_once 'DB.php';
    $db = &DB::connect('pgsql://jon@localhost+unix/logs');

    $conf['db'] = $db;
    $logger = Log::singleton('sql', 'log_table', 'ident', $conf);
    for ($i = 0; $i < 10; $i++) {
        $logger->log("Log entry $i");
    }


The Sqlite Handler
------------------
:Author:        Bertrand Mansion

The Sqlite handler sends log events to an Sqlite database using the `native
PHP sqlite functions`_.

It is faster than `the SQL (DB) handler`_ because requests are made directly
to the database without using an abstraction layer.  It is also interesting to
note that Sqlite database files can be moved, copied, and deleted on your
system just like any other files, which makes log management easier.  Last but
not least, using a database to log your events allows you to use SQL queries
to create reports and statistics.

When using a database and logging a lot of events, it is recommended to split
the database into smaller databases.  This is allowed by Sqlite, and you can
later use the Sqlite `ATTACH`_ statement to query your log database files
globally.

If the database does not exist when the log is opened, sqlite will try to
create it automatically. If the log table does not exist, it will also be
automatically created.  The table creation uses the following SQL request::

    CREATE TABLE log_table (
        id          INTEGER PRIMARY KEY NOT NULL,
        logtime     NOT NULL,
        ident       CHAR(16) NOT NULL,
        priority    INT NOT NULL,
        message
    );

Configuration
~~~~~~~~~~~~~
+-------------------+-----------+---------------+---------------------------+
| Parameter         | Type      | Default       | Description               |
+===================+===========+===============+===========================+
| ``filename``      | String    | '' `(empty)`  | Path to an Sqlite         |
|                   |           |               | database. |required|      |
+-------------------+-----------+---------------+---------------------------+
| ``mode``          | Integer   | 0666          | Octal mode used to open   |
|                   |           |               | the database.             |
+-------------------+-----------+---------------+---------------------------+
| ``persistent``    | Boolean   | false         | Use a persistent          |
|                   |           |               | connection.               |
+-------------------+-----------+---------------+---------------------------+

An already opened database connection can also be passed as parameter instead
of the above configuration.  In this case, closing the database connection is
up to the user.

.. _native PHP sqlite functions: http://www.php.net/sqlite
.. _ATTACH: http://www.sqlite.org/lang.html#attach

Examples
~~~~~~~~
Using a configuration to create a new database connection::

    $conf = array('filename' => 'log.db', 'mode' => 0666, 'persistent' => true);
    $logger = Log::factory('sqlite', 'log_table', 'ident', $conf);
    $logger->log('logging an event', PEAR_LOG_WARNING);

Using an existing connection::

    $db = sqlite_open('log.db', 0666, $error);
    $logger = Log::factory('sqlite', 'log_table', 'ident', $db);
    $logger->log('logging an event', PEAR_LOG_WARNING);
    sqlite_close($db);


The Syslog Handler
------------------
The Syslog handler sends log events to the system logging service (syslog on
Unix-like environments or the Event Log on Windows systems).  The events are
sent using PHP's `syslog()`_ function.

Configuration
~~~~~~~~~~~~~
+-------------------+-----------+---------------+---------------------------+
| Parameter         | Type      | Default       | Description               |
+===================+===========+===============+===========================+
| ``inherit``       | Boolean   | false         | Inherit the current syslog|
|                   |           |               | connection for this       |
|                   |           |               | process, or start a new   |
|                   |           |               | one via `openlog()`_?     |
+-------------------+-----------+---------------+---------------------------+
| ``reopen``        | Boolean   | false         | Reopen the syslog         |
|                   |           |               | connection for each log   |
|                   |           |               | event?                    |
+-------------------+-----------+---------------+---------------------------+
| ``maxLength``     | Integer   | 500           | Maximum message length    |
|                   |           |               | that will be sent to the  |
|                   |           |               | `syslog()`_ function.     |
|                   |           |               | Longer messages will be   |
|                   |           |               | split across multiple     |
|                   |           |               | `syslog()`_ calls.        |
+-------------------+-----------+---------------+---------------------------+
| ``lineFormat``    | String    | ``%4$s``      | `Log line format`_        |
|                   |           |               | specification.            |
+-------------------+-----------+---------------+---------------------------+
| ``timeFormat``    | String    | ``%b %d       | Time stamp format         |
|                   |           | %H:%M:%S``    | (for strftime_).          |
+-------------------+-----------+---------------+---------------------------+

Facilities
~~~~~~~~~~
+-------------------+-------------------------------------------------------+
| Constant          | Category Description                                  |
+===================+=======================================================+
| ``LOG_AUTH``      | Security / authorization messages; ``LOG_AUTHPRIV`` is|
|                   | preferred on systems where it is defined.             |
+-------------------+-------------------------------------------------------+
| ``LOG_AUTHPRIV``  | Private security / authorization messages             |
+-------------------+-------------------------------------------------------+
| ``LOG_CRON``      | Clock daemon (``cron`` and ``at``)                    |
+-------------------+-------------------------------------------------------+
| ``LOG_DAEMON``    | System daemon processes                               |
+-------------------+-------------------------------------------------------+
| ``LOG_KERN``      | Kernel messages                                       |
+-------------------+-------------------------------------------------------+
| ``LOG_LOCAL0`` .. | Reserved for local use; **not** available under       |
| ``LOG_LOCAL7``    | Windows.                                              |
+-------------------+-------------------------------------------------------+
| ``LOG_LPR``       | Printer subsystem                                     |
+-------------------+-------------------------------------------------------+
| ``LOG_MAIL``      | Mail subsystem                                        |
+-------------------+-------------------------------------------------------+
| ``LOG_NEWS``      | USENET news subsystem                                 |
+-------------------+-------------------------------------------------------+
| ``LOG_SYSLOG``    | Internal syslog messages                              |
+-------------------+-------------------------------------------------------+
| ``LOG_USER``      | Generic user-level messages                           |
+-------------------+-------------------------------------------------------+
| ``LOG_UUCP``      | UUCP subsystem                                        |
+-------------------+-------------------------------------------------------+

.. _syslog(): http://www.php.net/syslog
.. _openlog(): http://www.php.net/openlog

Example
~~~~~~~
::

    $logger = Log::singleton('syslog', LOG_LOCAL0, 'ident');
    for ($i = 0; $i < 10; $i++) {
        $logger->log("Log entry $i");
    }


The Window Handler
------------------
The Window handler sends log events to a separate browser window.  The
original idea for this handler was inspired by Craig Davis' Zend.com article
entitled `"JavaScript Power PHP Debugging"`_.

Configuration
~~~~~~~~~~~~~
+-------------------+-----------+---------------+---------------------------+
| Parameter         | Type      | Default       | Description               |
+===================+===========+===============+===========================+
| ``title``         | String    | ``Log Output  | The title of the output   |
|                   |           | Window``      | window.                   |
+-------------------+-----------+---------------+---------------------------+
| ``styles``        | Array     | `ROY G BIV`_  | Mapping of log priorities |
|                   |           | (high to low) | to CSS styles.            |
+-------------------+-----------+---------------+---------------------------+

**Note:** The Window handler may not work reliably when PHP's `output
buffering`_ system is enabled.

.. _"JavaScript Power PHP Debugging": http://www.zend.com/zend/tut/tutorial-DebugLib.php
.. _ROY G BIV: http://www.cis.rit.edu/
.. _output buffering: http://www.php.net/outcontrol

Example
~~~~~~~
::

    $conf = array('title' => 'Sample Log Output');
    $logger = Log::singleton('win', 'LogWindow', 'ident', $conf);
    for ($i = 0; $i < 10; $i++) {
        $logger->log("Log entry $i");
    }


Composite Handlers
==================
It is often useful to log events to multiple handlers.  The Log package
provides a compositing system that marks this task trivial.

Start by creating the individual log handlers::

    $console = Log::factory('console', '', 'TEST');
    $file = Log::factory('file', 'out.log', 'TEST');

Then, construct a composite handler and add the individual handlers as
children of the composite::

    $composite = Log::singleton('composite');
    $composite->addChild($console);
    $composite->addChild($file);

The composite handler implements the standard ``Log`` interface so you can use
it just like any of the other handlers::

    $composite->log('This event will be logged to both handlers.');

Children can be removed from the composite when they're not longer needed::

    $composite->removeChild($file);


Log Observers
=============
Log observers provide an implementation of the `observer pattern`_.  In the
content of the Log package, they provide a mechanism by which you can examine
(i.e. observe) each event as it is logged.  This allows the implementation of
special behavior based on the contents of a log event.  For example, the
observer code could send an alert email if a log event contained the string
``PANIC``.

Creating a log observer involves implementing a subclass of the
``Log_observer`` class.  The subclass must override the base class's
``notify()`` method.  This method is passed a hash containing the event's
priority and event.  The subclass's implementation is free to act upon this
information in any way it likes.

Log observers are attached to ``Log`` instances via the ``attach()`` method::

    $observer = Log_observer::factory('yourType');
    $logger->attach($observer);

Observers can be detached using the ``detach()`` method::

    $logger->detach($observer);

At this time, no concrete ``Log_observer`` implementations are distributed
with the Log package.

.. _observer pattern: http://wikipedia.org/wiki/Observer_pattern


Logging From Standard Error Handlers
====================================

Logging PHP Errors
------------------
PHP's default error handler can be overridden using the `set_error_handler()`_
function.  The custom error handling function can use a global Log instance to
log the PHP errors.

**Note:** Fatal PHP errors cannot be handled by a custom error handler at this
time.

::

    function errorHandler($code, $message, $file, $line)
    {
        global $logger;

        /* Map the PHP error to a Log priority. */
        switch ($code) {
        case E_WARNING:
        case E_USER_WARNING:
            $priority = PEAR_LOG_WARNING;
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $priority = PEAR_LOG_NOTICE;
            break;
        case E_ERROR:
        case E_USER_ERROR:
            $priority = PEAR_LOG_ERR;
            break;
        default:
            $priority = PEAR_LOG_INFO;
        }

        $logger->log($message . ' in ' . $file . ' at line ' . $line,
                     $priority);
    }

    set_error_handler('errorHandler');
    trigger_error('This is an information log message.', E_USER_NOTICE);

.. _set_error_handler(): http://www.php.net/set_error_handler

Logging PHP Assertions
----------------------
PHP allows user-defined `assert()`_ callback handlers.  The assertion callback
is configured using the `assert_options()`_ function.

::

    function assertCallback($file, $line, $message)
    {
        global $logger;

        $logger->log($message . ' in ' . $file . ' at line ' . $line,
                     PEAR_LOG_ALERT);
    }

    assert_options(ASSERT_CALLBACK, 'assertCallback');
    assert(false);

.. _assert(): http://www.php.net/assert
.. _assert_options(): http://www.php.net/assert_options

Logging PHP Exceptions
----------------------
PHP 5 and later support the concept of `exceptions`_.  A custom exception
handler can be assigned using the `set_exception_handler()`_ function.

::

    function exceptionHandler($exception)
    {
        global $logger;

        $logger->log($exception->getMessage(), PEAR_LOG_ALERT);
    }

    set_exception_handler('exceptionHandler');
    throw new Exception('Uncaught Exception');

.. _exceptions: http://www.php.net/exceptions
.. _set_exception_handler(): http://www.php.net/set_exception_handler

Logging PEAR Errors
-------------------
The Log package can be used with `PEAR::setErrorHandling()`_'s
``PEAR_ERROR_CALLBACK`` mechanism by writing an error handling function that
uses a global Log instance.  Here's an example::

    function errorHandler($error)
    {
        global $logger;

        $message = $error->getMessage();

        if (!empty($error->backtrace[1]['file'])) {
            $message .= ' (' . $error->backtrace[1]['file'];
            if (!empty($error->backtrace[1]['line'])) {
                $message .= ' at line ' . $error->backtrace[1]['line'];
            }
            $message .= ')';
        }

        $logger->log($message, $error->code);
    }

    PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'errorHandler');
    PEAR::raiseError('This is an information log message.', PEAR_LOG_INFO);

.. _PEAR::setErrorHandling(): http://pear.php.net/manual/en/core.pear.pear.seterrorhandling.php


Custom Handlers
===============
There are times when the standard handlers aren't a perfect match for your
needs.  In those situations, the solution might be to write a custom handler.

Using a Custom Handler
----------------------
Using a custom Log handler is very simple.  Once written (see `Writing New
Handlers`_ and `Extending Existing Handlers`_ below), you have the choice of
placing the file in your PEAR installation's main ``Log/`` directory (usually
something like ``/usr/local/lib/php/Log`` or ``C:\php\pear\Log``), where it
can be found and use by any PHP application on the system, or placing the file
somewhere in your application's local hierarchy and including it before the
the custom Log object is constructed.

Method 1: Handler in the Standard Location
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
After copying the handler file to your PEAR installation's ``Log/`` directory,
simply treat the handler as if it were part of the standard distributed.  If
your handler is named ``custom`` (and therefore implemented by a class named
``Log_custom``)::

    require_once 'Log.php';

    $logger = Log::factory('custom', '', 'CUSTOM');


Method 2: Handler in a Custom Location
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
If you prefer storing your handler in your application's local hierarchy,
you'll need to include that file before you can create a Log instance based on
it.

::

    require_once 'Log.php';
    require_once 'LocalHandlers/custom.php';

    $logger = Log::factory('custom', '', 'CUSTOM');


Writing New Handlers
--------------------
Writing a new Log handler is as simple as authoring a new class that extends
the ``Log`` class and that implements a relatively small set of standard
methods.

Every handler's class name must start with ``Log_`` in order for it to be
recognized by the Log package.

::

    class Log_custom extends Log

The handler's constructor will be called with four parameters.  These values
are discussed in detail in the `Configuring a Handler`_ section.

::

    Log_custom($name, $ident = '', $conf = array(), $level = PEAR_LOG_DEBUG)

The constructor is responsible for configuring the handler based on these
values.  Handler-specific parameters are passed as part of the ``$conf``
array.  At a minimum, the handler's constructor must set the following values
defined by the ``Log`` base class::

        $this->_id = md5(microtime().rand());
        $this->_name = $name;
        $this->_ident = $ident;
        $this->_mask = Log::UPTO($level);

The `Handler Methods`_ section below details the various standard methods that
can be implemented by a log handler.  The `Utility Methods`_ section describes
some useful utility methods provided by the ``Log`` base class which may be
useful when implementing a log handler.


Extending Existing Handlers
---------------------------
Extending existing handlers is very similar to `writing new handlers`_ with
the exception that, instead of inheriting from the ``Log`` base class
directly, the handler extends an existing handler's class.  This is a useful
way of adding some custom behavior to an existing handler without writing an
entirely new class (in the spirit of object-oriented programming).

For example, `the mail handler`_ could be extended to support sending messages
with MIME-encoded attachments simply by authoring a new ``Log_mail_mime``
class with a compliant constructor and a custom ``log()`` method.  The rest of
the standard methods would fall back on the ``Log_mail`` base class's
implementations.

Obviously, the specific details involved in extending an existing handler
require a good working understanding of that handler's implementation.


Handler Methods
---------------

bool open()
~~~~~~~~~~~
The ``open()`` method is called to open the log resource for output.  Handlers
can call ``open()`` immediately upon construction or lazily at runtime
(perhaps when the first log event is received).

The ``Log`` base class provides a protected ``$_opened`` member variable which
should be set to ``true`` when the log handler is opened and ``false`` when it
is closed.  Handler methods can inspect this value to determine whether or not
the handler is currently open and ready for output.

If the ``open()`` method fails to ready the handler for output, it should
return ``false`` and set ``$this->_opened`` to ``false``.

bool close()
~~~~~~~~~~~~
The ``close()`` method is called to close the log resource.  This method is
the analog of the ``open()`` method.  It should be safe to call ``close()``
even when the handler is not open for output.

If the ``close()`` method fails to close the handler, it should return
``false``.  Otherwise, it should return ``true``.  The ``$this->_opened``
flag should also be updated appropriately.

bool flush()
~~~~~~~~~~~~
The ``flush()`` method flushes any buffered log events, as described in
`Flushing Log Events`_.  The implementation of this method will be largely
handler-specific.  If the handler does not support buffered output,
implementing this method is not necessary; the ``Log`` class's ``flush()``
method will be called instead.

bool log($message, $priority = null)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
The ``log()`` method is the core of every log handler.  It is called whenever
the user wishes to log an event.  The implementation of this method is very
handler-specific.  It should return ``true`` or ``false``, depending on
whether or not the message was successfully logged by the handler.

The ``log()`` implementation should be sure to call `_announce()`__ whenever
an event is successfully logged.

.. __: `void _announce($event)`_

Utility Methods
---------------
These utility methods are provided by the ``Log`` base class and provide
common, useful functionality to handler implementations.

string _extractMessage($message)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
This method returns the string representation of the provided message data.

If ``$message`` is an object, ``_extractMessage()`` will attempt to extract
the message text using a known method (such as a `PEAR_Error`_ object's
`getMessage()`_ method).  If a known method, cannot be found, the serialized
representation of the object will be returned.

If the message data is already a string, it will be returned unchanged.

.. _PEAR_Error: http://pear.php.net/manual/en/core.pear.pear-error.php
.. _getMessage(): http://pear.php.net/manual/en/core.pear.pear-error.getmessage.php

string _format($format, $timestamp, $priority, $message)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
This method produces a formatted log line based on a format string and a set
of `tokens`_ representing the current log record and state.

.. _tokens: `Log Line Format`_

bool _isMasked($priority)
~~~~~~~~~~~~~~~~~~~~~~~~~
This method checks if the given priority is included in the handler's current
level mask.  This is useful for determining whether or not a log event should
be written to the handler's log.

void _announce($event)
~~~~~~~~~~~~~~~~~~~~~~
This method informs any registered `log observers`_ that a new event has been
logged.  ``$event`` is an array containing two named elements::

    array('priority' => $priority, 'message' => $message)

``_announce()`` should be called from a handler's `log()`_ method whenever an
event is successfully logged.  Otherwise, registered observers will never
become aware of the event.

.. _log(): `bool log($message, $priority = null)`_

.. |required| replace:: **[required]**

.. vim: tabstop=4 shiftwidth=4 softtabstop=4 expandtab textwidth=78 ft=rst:
