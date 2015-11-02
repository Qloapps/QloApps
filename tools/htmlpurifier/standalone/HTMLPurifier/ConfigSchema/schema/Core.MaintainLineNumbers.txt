Core.MaintainLineNumbers
TYPE: bool/null
VERSION: 2.0.0
DEFAULT: NULL
--DESCRIPTION--

<p>
  If true, HTML Purifier will add line number information to all tokens.
  This is useful when error reporting is turned on, but can result in
  significant performance degradation and should not be used when
  unnecessary. This directive must be used with the DirectLex lexer,
  as the DOMLex lexer does not (yet) support this functionality.
  If the value is null, an appropriate value will be selected based
  on other configuration.
</p>
--# vim: et sw=4 sts=4
