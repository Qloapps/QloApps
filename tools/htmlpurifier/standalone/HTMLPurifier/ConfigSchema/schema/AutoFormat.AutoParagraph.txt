AutoFormat.AutoParagraph
TYPE: bool
VERSION: 2.0.1
DEFAULT: false
--DESCRIPTION--

<p>
  This directive turns on auto-paragraphing, where double newlines are
  converted in to paragraphs whenever possible. Auto-paragraphing:
</p>
<ul>
  <li>Always applies to inline elements or text in the root node,</li>
  <li>Applies to inline elements or text with double newlines in nodes
      that allow paragraph tags,</li>
  <li>Applies to double newlines in paragraph tags</li>
</ul>
<p>
  <code>p</code> tags must be allowed for this directive to take effect.
  We do not use <code>br</code> tags for paragraphing, as that is
  semantically incorrect.
</p>
<p>
  To prevent auto-paragraphing as a content-producer, refrain from using
  double-newlines except to specify a new paragraph or in contexts where
  it has special meaning (whitespace usually has no meaning except in
  tags like <code>pre</code>, so this should not be difficult.) To prevent
  the paragraphing of inline text adjacent to block elements, wrap them
  in <code>div</code> tags (the behavior is slightly different outside of
  the root node.)
</p>
--# vim: et sw=4 sts=4
