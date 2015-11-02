Attr.AllowedFrameTargets
TYPE: lookup
DEFAULT: array()
--DESCRIPTION--
Lookup table of all allowed link frame targets.  Some commonly used link
targets include _blank, _self, _parent and _top. Values should be
lowercase, as validation will be done in a case-sensitive manner despite
W3C's recommendation. XHTML 1.0 Strict does not permit the target attribute
so this directive will have no effect in that doctype. XHTML 1.1 does not
enable the Target module by default, you will have to manually enable it
(see the module documentation for more details.)
--# vim: et sw=4 sts=4
