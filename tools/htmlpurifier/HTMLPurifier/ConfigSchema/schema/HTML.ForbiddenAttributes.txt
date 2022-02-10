HTML.ForbiddenAttributes
TYPE: lookup
VERSION: 3.1.0
DEFAULT: array()
--DESCRIPTION--
<p>
    While this directive is similar to %HTML.AllowedAttributes, for
    forwards-compatibility with XML, this attribute has a different syntax. Instead of
    <code>tag.attr</code>, use <code>tag@attr</code>. To disallow <code>href</code>
    attributes in <code>a</code> tags, set this directive to
    <code>a@href</code>. You can also disallow an attribute globally with
    <code>attr</code> or <code>*@attr</code> (either syntax is fine; the latter
    is provided for consistency with %HTML.AllowedAttributes).
</p>
<p>
    <strong>Warning:</strong> This directive complements %HTML.ForbiddenElements,
    accordingly, check
    out that directive for a discussion of why you
    should think twice before using this directive.
</p>
--# vim: et sw=4 sts=4
