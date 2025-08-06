``macro``
=========

.. versionadded:: 1.12

    The possibility to define default values for arguments in the macro
    signature was added in Twig 1.12.

Macros are comparable with functions in regular programming languages. They
are useful to reuse template fragments to not repeat yourself.

Macros are defined in regular templates.

Imagine having a generic helper template that define how to render HTML forms
via macros (called ``forms.html``):

.. code-block:: twig

    {% macro input(name, value, type = "text", size = 20) %}
        <input type="{{ type }}" name="{{ name }}" value="{{ value|e }}" size="{{ size }}" />
    {% endmacro %}

    {% macro textarea(name, value, rows = 10, cols = 40) %}
        <textarea name="{{ name }}" rows="{{ rows }}" cols="{{ cols }}">{{ value|e }}</textarea>
    {% endmacro %}

Each macro argument can have a default value (here ``text`` is the default value
for ``type`` if not provided in the call).

.. note::

    Before Twig 1.12, defining default argument values was done via the
    ``default`` filter in the macro body:

    .. code-block:: twig

        {% macro input(name, value, type, size) %}
            <input type="{{ type|default('text') }}" name="{{ name }}" value="{{ value|e }}" size="{{ size|default(20) }}" />
        {% endmacro %}

Macros differ from native PHP functions in a few ways:

* Arguments of a macro are always optional.

* If extra positional arguments are passed to a macro, they end up in the
  special ``varargs`` variable as a list of values.

But as with PHP functions, macros don't have access to the current template
variables.

.. tip::

    You can pass the whole context as an argument by using the special
    ``_context`` variable.

Import
------

There are two ways to import macros. You can import the complete template
containing the macros into a local variable (via the ``import`` tag) or only
import specific macros from the template (via the ``from`` tag).

To import all macros from a template into a local variable, use the ``import``
tag:

.. code-block:: twig

    {% import "forms.html" as forms %}

The above ``import`` call imports the ``forms.html`` file (which can contain
only macros, or a template and some macros), and import the macros as items of
the ``forms`` local variable.

The macros can then be called at will in the *current* template:

.. code-block:: twig

    <p>{{ forms.input('username') }}</p>
    <p>{{ forms.input('password', null, 'password') }}</p>

When you want to use a macro in another macro from the same file, you need to
import it locally:

.. code-block:: twig

    {% macro input(name, value, type, size) %}
        <input type="{{ type|default('text') }}" name="{{ name }}" value="{{ value|e }}" size="{{ size|default(20) }}" />
    {% endmacro %}

    {% macro wrapped_input(name, value, type, size) %}
        {% import _self as forms %}

        <div class="field">
            {{ forms.input(name, value, type, size) }}
        </div>
    {% endmacro %}

Alternatively you can import names from the template into the current namespace
via the ``from`` tag:

.. code-block:: twig

    {% from 'forms.html' import input as input_field, textarea %}

    <p>{{ input_field('password', '', 'password') }}</p>
    <p>{{ textarea('comment') }}</p>

.. note::

    Importing macros using ``import`` or ``from`` is **local** to the current
    file. The imported macros are not available in included templates or child
    templates; you need to explicitly re-import macros in each file.

.. tip::

    To import macros from the current file, use the special ``_self`` variable:

    .. code-block:: twig

        {% import _self as forms %}

        <p>{{ forms.input('username') }}</p>

    When you define a macro in the template where you are going to use it, you
    might be tempted to call the macro directly via ``_self.input()`` instead of
    importing it; even if it seems to work, this is just a side-effect of the
    current implementation and it won't work anymore in Twig 2.x.

Named Macro End-Tags
--------------------

Twig allows you to put the name of the macro after the end tag for better
readability (the name after the ``endmacro`` word must match the macro name):

.. code-block:: twig

    {% macro input() %}
        ...
    {% endmacro input %}
