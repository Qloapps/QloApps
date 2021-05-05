# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [3.1.39] - 2021-02-17

### Security
- Prevent access to `$smarty.template_object` in sandbox mode
- Fixed code injection vulnerability by using illegal function names in `{function name='blah'}{/function}` 

## [3.1.38] - 2021-01-08

### Fixed
- Smarty::SMARTY_VERSION wasn't updated https://github.com/smarty-php/smarty/issues/628

## [3.1.37] - 2021-01-07

### Changed
- Changed error handlers and handling of undefined constants for php8-compatibility (set $errcontext argument optional) https://github.com/smarty-php/smarty/issues/605
- Changed expected error levels in unit tests for php8-compatibility
- Travis unit tests now run for all php versions >= 5.3, including php8
- Travis runs on Xenial where possible

### Fixed
- PHP5.3 compatibility fixes
- Brought lexer source functionally up-to-date with compiled version

## [3.1.36] - 2020-04-14

### Fixed
 - Smarty::SMARTY_VERSION wasn't updated in v3.1.35 https://github.com/smarty-php/smarty/issues/584

## [3.1.35] - 2020-04-14
 - remove whitespaces after comments https://github.com/smarty-php/smarty/issues/447
 - fix foreachelse on arrayiterators https://github.com/smarty-php/smarty/issues/506
 - fix files contained in git export archive for package maintainers https://github.com/smarty-php/smarty/issues/325
 - throw SmartyException when setting caching attributes for cacheable plugin https://github.com/smarty-php/smarty/issues/457
 - fix errors that occured where isset was replaced with null check such as https://github.com/smarty-php/smarty/issues/453
 - unit tests are now in the repository

## 3.1.34 release - 05.11.2019
13.01.2020
 - fix typo in exception message (JercSi)
 - fix typehint warning with callable (bets4breakfast)
 - add travis badge and compatability info to readme (matks)
 - fix stdClass cast when compiling foreach (carpii)
 - fix wrong set/get methods for memcached (IT-Experte)
 - fix pborm assigning value to object variables in smarty_internal_compile_assign (Hunman)
 - exclude error_reporting.ini from git export (glensc)

## 3.1.34-dev-6 -
30.10.2018
 - bugfix a nested subblock in an inheritance child template was not replace by
   outer level block with same name in same child template https://github.com/smarty-php/smarty/issues/500

29.10.2018
 - bugfix Smarty::$php_handling == PHP_PASSTHRU (default) did eat the "\n" (newline) character if it did directly followed
   a PHP tag like "?>" or other https://github.com/smarty-php/smarty/issues/501

14.10.2018
 - bugfix autoloader exit shortcut https://github.com/smarty-php/smarty/issues/467

11.10.2018
 - bugfix {insert} not works when caching is enabled and included template is present
   https://github.com/smarty-php/smarty/issues/496
 - bugfix in date-format modifier; NULL at date string or default_date did not produce correct output
   https://github.com/smarty-php/smarty/pull/458

09.10.2018
 - bugfix fix of 26.8.2017 https://github.com/smarty-php/smarty/issues/327
   modifier is applied to sum expression https://github.com/smarty-php/smarty/issues/491
 - bugfix indexed arrays could not be defined "array(...)""

18.09.2018
  - bugfix large plain text template sections without a Smarty tag > 700kB could
    could fail in version 3.1.32 and 3.1.33 because PHP preg_match() restrictions
    https://github.com/smarty-php/smarty/issues/488

## 3.1.33 release - 12.09.2018
## 3.1.33-dev-12 -
03.09.2018
  - bugfix {foreach} using new style property access like {$item@property} on
    Smarty 2 style named foreach loop could produce errors https://github.com/smarty-php/smarty/issues/484

31.08.2018
  - bugfix some custom left and right delimiters like '{^' '^}' did not work
    https://github.com/smarty-php/smarty/issues/450 https://github.com/smarty-php/smarty/pull/482

  - reformating for PSR-2 coding standards https://github.com/smarty-php/smarty/pull/483

  - bugfix on Windows absolute filepathes did fail if the drive letter was followed by a linux DIRECTORY_SEPARATOR
    like C:/  at Smarty > 3.1.33-dev-5 https://github.com/smarty-php/smarty/issues/451

  - PSR-2 code style fixes for config and template file Lexer/Parser generated with
    the Smarty Lexer/Parser generator from https://github.com/smarty-php/smarty-lexer
    https://github.com/smarty-php/smarty/pull/483

26.08.2018
  - bugfix/enhancement {capture} allow variable as capture block name in Smarty special variable
    like $smarty.capture.$foo https://github.com/smarty-php/smarty/issues/478 https://github.com/smarty-php/smarty/pull/481

## 3.1.33-dev-6 -
19.08.2018
  - fix PSR-2 coding standards and PHPDoc blocks https://github.com/smarty-php/smarty/pull/452
    https://github.com/smarty-php/smarty/pull/475
    https://github.com/smarty-php/smarty/pull/473
  - bugfix PHP5.2 compatibility https://github.com/smarty-php/smarty/pull/472

## 3.1.33-dev-4 -
17.05.2018
 - bugfix strip-block produces different output in Smarty v3.1.32 https://github.com/smarty-php/smarty/issues/436
 - bugfix Smarty::compileAllTemplates ignores `$extension` parameter https://github.com/smarty-php/smarty/issues/437
   https://github.com/smarty-php/smarty/pull/438
 - improvement do not compute total property in {foreach} if not needed https://github.com/smarty-php/smarty/issues/443
 - bugfix  plugins may not be loaded when setMergeCompiledIncludes is true https://github.com/smarty-php/smarty/issues/435

26.04.2018
 - bugfix  regarding Security Vulnerability did not solve the problem under Linux.
   Security issue CVE-2018-16831

## 3.1.32 - (24.04.2018)
24.04.2018
 - bugfix  possible Security Vulnerability in Smarty_Security class.

26.03.2018
 - bugfix plugins may not be loaded if {function} or {block} tags are executed in nocache mode
   https://github.com/smarty-php/smarty/issues/371

26.03.2018
 - new feature {parent} =  {$smarty.block.parent} {child} =  {$smarty.block.child}

23.03.2018
 - bugfix preg_replace could fail on large content resulting in a blank page https://github.com/smarty-php/smarty/issues/417

21.03.2018
 - bugfix {$smarty.section...} used outside {section}{/section} showed incorrect values if {section}{/section} was called inside
   another loop https://github.com/smarty-php/smarty/issues/422
 - bugfix short form of {section} attributes did not work https://github.com/smarty-php/smarty/issues/428

17.03.2018
 - improvement Smarty::compileAllTemplates() exit with a non-zero status code if max errors is reached https://github.com/smarty-php/smarty/pull/402

16.03.2018
 - bugfix extends resource did not work with user defined left/right delimiter https://github.com/smarty-php/smarty/issues/419

22.11.2017
 - bugfix {break} and {continue} could fail if {foreach}{/foreach} did contain other
   looping tags like {for}, {section} and {while} https://github.com/smarty-php/smarty/issues/323

20.11.2017
  - bugfix rework of newline spacing between tag code and template text.
    now again identical with Smarty2 (forum topic 26878)
  - replacement of " by '

05.11.2017
  - lexer/parser optimization
  - code cleanup and optimizations
  - bugfix {$smarty.section.name.loop} used together with {$smarty.section.name.total} could produce
    wrong results (forum topic 27041)

26.10.2017
  - bugfix Smarty version was  not filled in header comment of compiled and cached  files
  - optimization replace internal Smarty::$ds property by DIRECTORY_SEPARATOR
  - deprecate functions Smarty::muteExpectedErrors() and Smarty::unmuteExpectedErrors()
    as Smarty does no longer use error suppression like @filemtime().
    for backward compatibility code is moved from Smarty class to an external class and still can be
    called.
  - correction of PHPDoc blocks
  - minor code cleanup

21.10.2017
  - bugfix custom delimiters could fail since modification of  version 3.1.32-dev-23
    https://github.com/smarty-php/smarty/issues/394

18.10.2017
  - bugfix fix implementation of unclosed block tag in double quoted string of 12.10.2017
    https://github.com/smarty-php/smarty/issues/396 https://github.com/smarty-php/smarty/issues/397
    https://github.com/smarty-php/smarty/issues/391 https://github.com/smarty-php/smarty/issues/392

12.10.2017
  - bugfix $smarty.block.child and $smarty.block.parent could not be used like any
    $smarty special variable https://github.com/smarty-php/smarty/issues/393
  - unclosed block tag in double quoted string must throw compiler exception.
     https://github.com/smarty-php/smarty/issues/391 https://github.com/smarty-php/smarty/issues/392

07.10.2017
  - bugfix modification of 9.8.2017 did fail on some recursive
    tag nesting. https://github.com/smarty-php/smarty/issues/389

26.8.2017
  - bugfix chained modifier failed when last modifier parameter is a signed value
    https://github.com/smarty-php/smarty/issues/327
  - bugfix templates filepath with multibyte characters did not work
    https://github.com/smarty-php/smarty/issues/385
  - bugfix {make_nocache} did display code if the template did not contain other nocache code
    https://github.com/smarty-php/smarty/issues/369

09.8.2017
  - improvement repeated delimiter like {{ and }} will be treated as literal
    https://groups.google.com/forum/#!topic/smarty-developers/h9r82Bx4KZw

05.8.2017
  - bugfix wordwrap modifier could fail if used in nocache code.
    converted plugin file shared.mb_wordwrap.php into modifier.mb_wordwrap.php
  - cleanup of _getSmartyObj()

31.7.2017
  - Call clearstatcache() after mkdir() failure https://github.com/smarty-php/smarty/pull/379

30.7.2017
  - rewrite mkdir() bugfix to retry automatically see https://github.com/smarty-php/smarty/pull/377
    https://github.com/smarty-php/smarty/pull/379

21.7.2017
  - security possible PHP code injection on custom resources at display() or fetch()
    calls if the resource does not sanitize the template name
  - bugfix fix 'mkdir(): File exists' error on create directory from parallel
    processes https://github.com/smarty-php/smarty/pull/377
  - bugfix solve preg_match() hhvm parameter problem https://github.com/smarty-php/smarty/pull/372

27.5.2017
  - bugfix change compiled code for registered function and modifiers to called as callable to allow closures
    https://github.com/smarty-php/smarty/pull/368, https://github.com/smarty-php/smarty/issues/273
  - bugfix https://github.com/smarty-php/smarty/pull/368 did break the default plugin handler
  - improvement replace phpversion() by PHP_VERSION constant.
    https://github.com/smarty-php/smarty/pull/363

21.5.2017
  - performance store flag for already required shared plugin functions in static variable or
    Smarty's $_cache to improve performance when plugins are often called
    https://github.com/smarty-php/smarty/commit/51e0d5cd405d764a4ea257d1bac1fb1205f74528#commitcomment-22280086
  - bugfix remove special treatment of classes implementing ArrayAccess in {foreach}
    https://github.com/smarty-php/smarty/issues/332
  - bugfix remove deleted files by clear_cache() and clear_compiled_template() from
    ACP cache if present, add some is_file() checks to avoid possible warnings on filemtime()
    caused by above functions.
    https://github.com/smarty-php/smarty/issues/341
  - bugfix version 3.1.31 did fail under PHP 5.2
    https://github.com/smarty-php/smarty/issues/365

19.5.2017
  - change properties $accessMap and $obsoleteProperties from private to protected
    https://github.com/smarty-php/smarty/issues/351
  - new feature The named capture buffers can now be accessed also as array
    See NEWS_FEATURES.txt https://github.com/smarty-php/smarty/issues/366
  - improvement check if ini_get() and ini_set() not disabled
    https://github.com/smarty-php/smarty/pull/362

24.4.2017
  - fix spelling https://github.com/smarty-php/smarty/commit/e3eda8a5f5653d8abb960eb1bc47e3eca679b1b4#commitcomment-21803095

17.4.2017
  - correct generated code on empty() and isset() call, observe change PHP behaviour since PHP 5.5
    https://github.com/smarty-php/smarty/issues/347

14.4.2017
  - merge pull requests https://github.com/smarty-php/smarty/pull/349, https://github.com/smarty-php/smarty/pull/322 and    https://github.com/smarty-php/smarty/pull/337 to fix spelling and annotation

13.4.2017
  - bugfix array_merge() parameter should be checked https://github.com/smarty-php/smarty/issues/350

## 3.1.31 - (14.12.2016)
  23.11.2016
   - move template object cache into static variables

  19.11.2016
  - bugfix inheritance root child templates containing nested {block}{/block} could call sub-bock content from parent
    template https://github.com/smarty-php/smarty/issues/317
  - change version checking

 11.11.2016
  - bugfix when Smarty is using a cached template object on Smarty::fetch() or Smarty::isCached() the inheritance data
    must be removed https://github.com/smarty-php/smarty/issues/312
  - smaller speed optimization

 08.11.2016
  - add bootstrap file to load and register Smarty_Autoloader. Change composer.json to make it known to composer

 07.11.2016
  - optimization of lexer speed https://github.com/smarty-php/smarty/issues/311

 27.10.2016
  - bugfix template function definitions array has not been cached between Smarty::fetch() and Smarty::display() calls
    https://github.com/smarty-php/smarty/issues/301

 23.10.2016
  - improvement/bugfix when Smarty::fetch() is called on a template object the inheritance and tplFunctions property
    should be copied to the called template object

 21.10.2016
  - bugfix for compile locking touched timestamp of old compiled file was not restored on compilation error https://github.com/smarty-php/smarty/issues/308

 20.10.2016
  - bugfix nocache code was not removed in cache file when subtemplate did contain PHP short tags in text but no other
    nocache code https://github.com/smarty-php/smarty/issues/300

 19.10.2016
  - bugfix {make_nocache $var} did fail when variable value did contain '\' https://github.com/smarty-php/smarty/issues/305
  - bugfix {make_nocache $var} remove spaces from variable value https://github.com/smarty-php/smarty/issues/304

 12.10.2016
  - bugfix {include} with template names including variable or constants could fail after bugfix from
     28.09.2016 https://github.com/smarty-php/smarty/issues/302

 08.10.2016
  - optimization move runtime extension for template functions into Smarty objects

 29.09.2016
  - improvement new Smarty::$extends_recursion property to disable execution of {extends} in templates called by extends resource
     https://github.com/smarty-php/smarty/issues/296

 28.09.2016
  - bugfix the generated code for calling a subtemplate must pass the template resource name in single quotes https://github.com/smarty-php/smarty/issues/299
  - bugfix nocache hash was not removed for <?xml ?> tags in subtemplates https://github.com/smarty-php/smarty/issues/300

 27.09.2016
  - bugfix when Smarty does use an internally cached template object on Smarty::fetch() calls
           the template and config variables must be cleared https://github.com/smarty-php/smarty/issues/297

 20.09.2016
  - bugfix some $smarty special template variables are no longer accessed as real variable.
    using them on calls like {if isset($smarty.foo)} or {if empty($smarty.foo)} will fail
    http://www.smarty.net/forums/viewtopic.php?t=26222
  - temporary fix for https://github.com/smarty-php/smarty/issues/293 main reason still under investigation
  - improvement new tags {block_parent} {block_child} in template inheritance

 19.09.2016
  - optimization clear compiled and cached folder completely on detected version change
  - cleanup convert cache resource file method clear into runtime extension

 15.09.2016
  - bugfix assigning a variable in if condition by function like {if $value = array_shift($array)} the function got called twice https://github.com/smarty-php/smarty/issues/291
  - bugfix function plugins called with assign attribute like {foo assign='bar'} did not output returned content because
           because assumption was made that it was assigned to a variable https://github.com/smarty-php/smarty/issues/292
  - bugfix calling $smarty->isCached() on a not existing cache file with $smarty->cache_locking = true; could cause a 10 second delay http://www.smarty.net/forums/viewtopic.php?t=26282
  - improvement make Smarty::clearCompiledTemplate() on custom resource independent from changes of templateId computation

 11.09.2016
  - improvement {math} misleading E_USER_WARNING messages when parameter value = null https://github.com/smarty-php/smarty/issues/288
  - improvement move often used code snippets into methods
  - performance Smarty::configLoad() did load unneeded template source object

 09.09.2016
  - bugfix/optimization {foreach} did not execute the {foreachelse} when iterating empty objects https://github.com/smarty-php/smarty/pull/287
  - bugfix {foreach} must keep the @properties when restoring a saved $item variable as the properties might be used outside {foreach} https://github.com/smarty-php/smarty/issues/267
  - improvement {foreach} observe {break n} and {continue n} nesting levels when restoring saved $item and $key variables

 08.09.2016
  - bugfix implement wrapper for removed method getConfigVariable() https://github.com/smarty-php/smarty/issues/286

 07.09.2016
  - bugfix using nocache like attribute with value true like {plugin nocache=true} did not work https://github.com/smarty-php/smarty/issues/285
  - bugfix uppercase TRUE, FALSE and NULL did not work when security was enabled https://github.com/smarty-php/smarty/issues/282
  - bugfix when {foreach} was looping over an object the total property like {$item@total} did always return 1 https://github.com/smarty-php/smarty/issues/281
  - bugfix {capture}{/capture} did add in 3.1.30 unintended additional blank lines https://github.com/smarty-php/smarty/issues/268

 01.09.2016
  - performance require_once should be called only once for shared plugins https://github.com/smarty-php/smarty/issues/280

 26.08.2016
  - bugfix change of 23.08.2016 failed on linux when use_include_path = true

 23.08.2016
  - bugfix remove constant DS as shortcut for DIRECTORY_SEPARATOR as the user may have defined it to something else https://github.com/smarty-php/smarty/issues/277

 20.08-2016
  - bugfix {config_load ... scope="global"} shall not throw an arror but fallback to scope="smarty" https://github.com/smarty-php/smarty/issues/274
  - bugfix {make_nocache} failed when using composer autoloader https://github.com/smarty-php/smarty/issues/275

 14.08.2016
  - bugfix $smarty_>debugging = true; did E_NOTICE messages when {eval} tag was used https://github.com/smarty-php/smarty/issues/266
  - bugfix Class 'Smarty_Internal_Runtime_ValidateCompiled' not found when upgrading from some older Smarty versions with existing
           compiled or cached template files https://github.com/smarty-php/smarty/issues/269
  - optimization remove unneeded call to update acopes when {assign} scope and template scope was local (default)

## 3.1.30 - (07.08.2016)

 07.08.2016
  - bugfix update of 04.08.2016 was incomplete

 05.08.2016
  - bugfix compiling of templates failed when the Smarty delimiter did contain '/' https://github.com/smarty-php/smarty/issues/264
  - updated error checking at template and config default handler

 04.08.2016
  - improvement move template function source parameter into extension

 26.07.2016
  - optimization unneeded loading of compiled resource

 24.07.2016
  - regression this->addPluginsDir('/abs/path/to/dir') adding absolute path without trailing '/' did fail https://github.com/smarty-php/smarty/issues/260

 23.07.2016
  - bugfix setTemplateDir('/') and setTemplateDir('') did create wrong absolute filepath https://github.com/smarty-php/smarty/issues/245
  - optimization of filepath normalization
  - improvement remove double function declaration in plugin shared.escape_special_cars.php https://github.com/smarty-php/smarty/issues/229

 19.07.2016
  - bugfix multiple {include} with relative filepath within {block}{/block} could fail https://github.com/smarty-php/smarty/issues/246
  - bugfix {math} shell injection vulnerability patch provided by Tim Weber

 18.07.2016
  - bugfix {foreach} if key variable and item@key attribute have been used both the key variable was not updated https://github.com/smarty-php/smarty/issues/254
  - bugfix modifier on plugins like {plugin|modifier ... } did fail when the plugin does return an array https://github.com/smarty-php/smarty/issues/228
  - bugfix avoid opcache_invalidate to result in ErrorException when opcache.restrict_api is not empty https://github.com/smarty-php/smarty/pull/244
  - bugfix multiple {include} with relative filepath within {block}{/block} could fail https://github.com/smarty-php/smarty/issues/246

 14.07.2016
  - bugfix wrong parameter on compileAllTemplates() and compileAllConfig() https://github.com/smarty-php/smarty/issues/231

 13.07.2016
  - bugfix PHP 7 compatibility on registered compiler plugins https://github.com/smarty-php/smarty/issues/241
  - update testInstall() https://github.com/smarty-php/smarty/issues/248https://github.com/smarty-php/smarty/issues/248
  - bugfix enable debugging could fail when template objects did already exists https://github.com/smarty-php/smarty/issues/237
  - bugfix template function data should be merged when loading subtemplate https://github.com/smarty-php/smarty/issues/240
  - bugfix wrong parameter on compileAllTemplates() https://github.com/smarty-php/smarty/issues/231

 12.07.2016
  - bugfix {foreach} item variable must be created also on empty from array https://github.com/smarty-php/smarty/issues/238 and https://github.com/smarty-php/smarty/issues/239
  - bugfix enableSecurity() must init cache flags https://github.com/smarty-php/smarty/issues/247

 27.05.2016
  - bugfix/improvement of compileAlltemplates() follow symlinks in template folder (PHP >= 5.3.1) https://github.com/smarty-php/smarty/issues/224
      clear internal cache and expension handler for each template to avoid possible conflicts https://github.com/smarty-php/smarty/issues/231

 16.05.2016
  - optimization {foreach} compiler and processing
  - broken PHP 5.3 and 5.4 compatibility

 15.05.2016
  - optimization and cleanup of resource code

 10.05.2016
  - optimization of inheritance processing

 07.05.2016
  -bugfix Only variables should be assigned by reference https://github.com/smarty-php/smarty/issues/227

 02.05.2016
  - enhancement {block} tag names can now be variable https://github.com/smarty-php/smarty/issues/221

 01.05.2016
  - bugfix same relative filepath at {include} called from template in different folders could display wrong sub-template

 29.04.2016
  - bugfix {strip} remove space on linebreak between html tags https://github.com/smarty-php/smarty/issues/213

 24.04.2016
  - bugfix nested {include} with relative file path could fail when called in {block} ... {/block} https://github.com/smarty-php/smarty/issues/218

 14.04.2016
  - bugfix special variable {$smarty.capture.name} was not case sensitive on name https://github.com/smarty-php/smarty/issues/210
  - bugfix the default template handler must calculate the source uid https://github.com/smarty-php/smarty/issues/205

 13.04.2016
  - bugfix template inheritance status must be saved when calling sub-templates https://github.com/smarty-php/smarty/issues/215

 27.03.2016
  - bugfix change of 11.03.2016 cause again {capture} data could not been seen in other templates with {$smarty.capture.name} https://github.com/smarty-php/smarty/issues/153

 11.03.2016
  - optimization of capture and security handling
  - improvement $smarty->clearCompiledTemplate() should return on recompiled or uncompiled resources

 10.03.2016
  - optimization of resource processing

 09.03.2016
  - improvement rework of 'scope' attribute handling see see NEW_FEATURES.txt https://github.com/smarty-php/smarty/issues/194
    https://github.com/smarty-php/smarty/issues/186 https://github.com/smarty-php/smarty/issues/179
  - bugfix correct Autoloader update of 2.3.2014 https://github.com/smarty-php/smarty/issues/199

 04.03.2016
  - bugfix change from 01.03.2016 will cause $smarty->isCached(..) failure if called multiple time for same template
    (forum topic 25935)

 02.03.2016
  - revert autoloader optimizations because of unexplainable warning when using plugins https://github.com/smarty-php/smarty/issues/199

 01.03.2016
  - bugfix template objects must be cached on $smarty->fetch('foo.tpl) calls incase the template is fetched
    multiple times (forum topic 25909)

 25.02.2016
  - bugfix wrong _realpath with 4 or more parent-directories https://github.com/smarty-php/smarty/issues/190
  - optimization of _realpath
  - bugfix instanceof expression in template code must be treated as value https://github.com/smarty-php/smarty/issues/191

 20.02.2016
  - bugfix {strip} must keep space between hmtl tags. Broken by changes of 10.2.2016 https://github.com/smarty-php/smarty/issues/184
  - new feature/bugfix  {foreach}{section} add 'properties' attribute to force compilation of loop properties
    see NEW_FEATURES.txt https://github.com/smarty-php/smarty/issues/189

 19.02.2016
  - revert output buffer flushing on display, echo content again because possible problems when PHP files had
    characters (newline} after ?> at file end https://github.com/smarty-php/smarty/issues/187

 14.02.2016
  - new tag {make_nocache} read NEW_FEATURES.txt https://github.com/smarty-php/smarty/issues/110
  - optimization of sub-template processing
  - bugfix using extendsall as default resource and {include} inside {block} tags could produce unexpected results https://github.com/smarty-php/smarty/issues/183
  - optimization of tag attribute compiling
  - optimization make compiler tag object cache static for higher compilation speed

 11.02.2016
  - improvement added KnockoutJS comments to trimwhitespace outputfilter https://github.com/smarty-php/smarty/issues/82
    https://github.com/smarty-php/smarty/pull/181

 10.02.2016
  - bugfix {strip} must keep space on output creating smarty tags within html tags https://github.com/smarty-php/smarty/issues/177
  - bugfix wrong precedence on special if conditions like '$foo is ... by $bar' could cause wrong code https://github.com/smarty-php/smarty/issues/178
  - improvement because of ambiguities the inline constant support has been removed from the $foo.bar syntax https://github.com/smarty-php/smarty/issues/149
  - bugfix other {strip} error with output tags between hmtl https://github.com/smarty-php/smarty/issues/180

 09.02.2016
  - move some code from parser into compiler
  - reformat all code for unique style
  - update/bugfix scope attribute handling reworked. Read the newfeatures.txt file

 05.02.2016
  - improvement internal compiler changes

 01.02.2016
  - bugfix {foreach} compilation failed when $smarty->merge_compiled_includes = true and pre-filters are used.

 29.01.2016
  - bugfix implement replacement code for _tag_stack property https://github.com/smarty-php/smarty/issues/151

 28.01.2016
  - bugfix allow windows network filepath or wrapper (forum topic 25876) https://github.com/smarty-php/smarty/issues/170
  - bugfix if fetch('foo.tpl') is called on a template object the $parent parameter should default to the calling template object https://github.com/smarty-php/smarty/issues/152

 27.01.2016
  - revert bugfix compiling {section} did create warning
  - bugfix {$smarty.section.customer.loop} did throw compiler error https://github.com/smarty-php/smarty/issues/161
    update of yesterdays fix
  - bugfix string resource could inject code at {block} or inline subtemplates through PHP comments https://github.com/smarty-php/smarty/issues/157		
  - bugfix output filters did not observe nocache code flhttps://github.com/smarty-php/smarty/issues/154g https://github.com/smarty-php/smarty/issues/160
  - bugfix {extends} with relative file path did not work https://github.com/smarty-php/smarty/issues/154
    https://github.com/smarty-php/smarty/issues/158
  - bugfix {capture} data could not been seen in other templates with {$smarty.capture.name} https://github.com/smarty-php/smarty/issues/153

 26.01.2016
  - improvement observe Smarty::$_CHARSET in debugging console https://github.com/smarty-php/smarty/issues/169
  - bugfix compiling {section} did create warning
  - bugfix {$smarty.section.customer.loop} did throw compiler error https://github.com/smarty-php/smarty/issues/161

 02.01.2016
  - update scope handling
  - optimize block plugin compiler
  - improvement runtime checks if registered block plugins are callable

 01.01.2016
  - remove Smarty::$resource_cache_mode property

 31.12.2015
  - optimization of {assign}, {if} and {while} compiled code

 30.12.2015
  - bugfix plugin names starting with "php" did not compile https://github.com/smarty-php/smarty/issues/147

 29.12.2015
  - bugfix Smarty::error_reporting was not observed when display() or fetch() was called on template objects https://github.com/smarty-php/smarty/issues/145

 28.12.2015
  - optimization of {foreach} code size and processing

 27.12.2015
  - improve inheritance code
  - update external methods
  - code fixes
  - PHPdoc updates

 25.12.2015
  - compile {block} tag code and its processing into classes
  - optimization replace hhvm extension by inline code
  - new feature If ACP is enabled force an apc_compile_file() when compiled or cached template was updated

 24.12.2015
  - new feature Compiler does now observe the template_dir setting and will create separate compiled files if required
  - bugfix post filter did fail on template inheritance https://github.com/smarty-php/smarty/issues/144

 23.12.2015
  - optimization move internal method decodeProperties back into template object
  - optimization move subtemplate processing back into template object
  - new feature Caching does now observe the template_dir setting and will create separate cache files if required

 22.12.2015
  - change $xxx_dir properties from private to protected in case Smarty class gets extended
  - code optimizations

 21.12.2015
  - bugfix a filepath starting with '/' or '\' on windows should normalize to the root dir
    of current working drive https://github.com/smarty-php/smarty/issues/134
  - optimization of filepath normalization
  - bugfix {strip} must remove all blanks between html tags https://github.com/smarty-php/smarty/issues/136

 - 3.1.29 - (21.12.2015)
 21.12.2015
  - optimization improve speed of filetime checks on extends and extendsall resource

 20.12.2015
  - bugfix failure when the default resource type was set to 'extendsall' https://github.com/smarty-php/smarty/issues/123
  - update compilation of Smarty special variables
  - bugfix add addition check for OS type on normalization of file path https://github.com/smarty-php/smarty/issues/134
  - bugfix the source uid of the extendsall resource must contain $template_dir settings https://github.com/smarty-php/smarty/issues/123

 19.12.2015
  - bugfix using $smarty.capture.foo in expressions could fail https://github.com/smarty-php/smarty/pull/138
  - bugfix broken PHP 5.2 compatibility https://github.com/smarty-php/smarty/issues/139
  - remove no longer used code
  - improvement make sure that compiled and cache templates never can contain a trailing '?>?

 18.12.2015
  - bugfix regression when modifier parameter was followed by math https://github.com/smarty-php/smarty/issues/132

 17.12.2015
  - bugfix {$smarty.capture.nameFail} did lowercase capture name https://github.com/smarty-php/smarty/issues/135
  - bugfix using {block append/prepend} on same block in multiple levels of inheritance templates could fail (forum topic 25827)
  - bugfix text content consisting of just a single '0' like in {if true}0{/if} was suppressed (forum topic 25834)

 16.12.2015
  - bugfix {foreach} did fail if from atrribute is a Generator class https://github.com/smarty-php/smarty/issues/128
  - bugfix direct access $smarty->template_dir = 'foo'; should call Smarty::setTemplateDir() https://github.com/smarty-php/smarty/issues/121

 15.12.2015
  - bugfix  {$smarty.cookies.foo} did return the $_COOKIE array not the 'foo' value https://github.com/smarty-php/smarty/issues/122
  - bugfix  a call to clearAllCache() and other should clear all internal template object caches (forum topic 25828)

 14.12.2015
  - bugfix  {$smarty.config.foo} broken in 3.1.28 https://github.com/smarty-php/smarty/issues/120
  - bugfix  multiple calls of {section} with same name droped E_NOTICE error https://github.com/smarty-php/smarty/issues/118

 - 3.1.28 - (13.12.2015)
 13.12.2015
  - bugfix {foreach} and {section} with uppercase characters in name attribute did not work (forum topic 25819)
  - bugfix $smarty->debugging_ctrl = 'URL' did not work (forum topic 25811)
  - bugfix Debug Console could display incorrect data when using subtemplates

 09.12.2015
  - bugfix Smarty did fail under PHP 7.0.0 with use_include_path = true;

 09.12.2015
  - bugfix {strip} should exclude some html tags from stripping, related to fix for https://github.com/smarty-php/smarty/issues/111

 08.12.2015
  - bugfix internal template function data got stored in wrong compiled file https://github.com/smarty-php/smarty/issues/114

 05.12.2015
  -bugfix {strip} should insert a single space https://github.com/smarty-php/smarty/issues/111

 25.11.2015
  -bugfix a left delimter like '[%' did fail on [%$var_[%$variable%]%] (forum topic 25798)

 02.11.2015
  - bugfix {include} with variable file name like {include file="foo_`$bar`.tpl"} did fail in 3.1.28-dev https://github.com/smarty-php/smarty/issues/102

 01.11.2015
  - update config file processing

 31.10.2015
  - bugfix add missing $trusted_dir property to SmartyBC class (forum topic 25751)

 29.10.2015
  - improve template scope handling

 24.10.2015
  - more optimizations of template processing
  - bugfix Error when using {include} within {capture} https://github.com/smarty-php/smarty/issues/100

 21.10.2015
  - move some code into runtime extensions

 18.10.2015
  - optimize filepath normalization
  - rework of template inheritance
  - speed and size optimizations
  - bugfix under HHVM temporary cache file must only be created when caches template was updated
  - fix compiled code for new {block} assign attribute
  - update code generated by template function call handler

 18.09.2015
  - bugfix {if $foo instanceof $bar} failed to compile if 2nd value is a variable https://github.com/smarty-php/smarty/issues/92

 17.09.2015
  - bugfix {foreach} first attribute was not correctly reset since commit 05a8fa2 of 02.08.2015 https://github.com/smarty-php/smarty/issues/90

 16.09.2015
  - update compiler by moving no longer needed properties, code optimizations and other

 14.09.2015
  - optimize autoloader
  - optimize subtemplate handling
  - update template inheritance processing
  - move code of {call} processing back into Smarty_Internal_Template class
  - improvement invalidate OPCACHE for cleared compiled and cached template files (forum topic 25557)
  - bugfix unintended multiple debug windows (forum topic 25699)

 30.08.2015
  - size optimization move some runtime functions into extension
  - optimize inline template processing
  - optimization merge inheritance child and parent templates into one compiled template file

 29.08.2015
  - improvement convert template inheritance into runtime processing
  - bugfix {$smarty.block.parent} did always reference the root parent block https://github.com/smarty-php/smarty/issues/68

 23.08.2015
  - introduce Smarty::$resource_cache_mode and cache template object of {include} inside loop
  - load seldom used Smarty API methods dynamically to reduce memory footprint
  - cache template object of {include} if same template is included several times
  - convert debug console processing to object
  - use output buffers for better performance and less memory usage
  - optimize nocache hash processing
  - remove not really needed properties
  - optimize rendering
  - move caching to Smarty::_cache
  - remove properties with redundant content
  - optimize Smarty::templateExists()
  - optimize use_include_path processing
  - relocate properties for size optimization
  - remove redundant code
  - bugfix compiling super globals like {$smarty.get.foo} did fail in the master branch https://github.com/smarty-php/smarty/issues/77

 06.08.2015
  - avoid possible circular object references caused by parser/lexer objects
  - rewrite compileAll... utility methods
  - commit several  internal improvements
  - bugfix Smarty failed when compile_id did contain "|"

 03.08.2015
  - rework clear cache methods
  - bugfix compileAllConfig() was broken since 3.1.22 because of the changes in config file processing
  - improve getIncludePath() to return directory if no file was given

 02.08.2015
  - optimization and code cleanup of {foreach} and {section} compiler
  - rework {capture} compiler

 01.08.2015
  - update DateTime object can be instance of DateTimeImmutable since PHP5.5 https://github.com/smarty-php/smarty/pull/75
  - improvement show resource type and start of template source instead of uid on eval: and string: resource (forum topic 25630)

 31.07.2015
  - optimize {foreach} and {section} compiler

 29.07.2015
  - optimize {section} compiler for speed and size of compiled code

 28.07.2015
  - update for PHP 7 compatibility

 26.07.2015
  - improvement impement workaround for HHVM PHP incompatibillity https://github.com/facebook/hhvm/issues/4797

 25.07.2015
  - bugfix parser did hang on text starting <?something https://github.com/smarty-php/smarty/issues/74

 20.07.2015
  - bugfix config files got recompiled on each request
  - improvement invalidate PHP 5.5 opcache for recompiled and cached templates  https://github.com/smarty-php/smarty/issues/72

 12.07.2015
  - optimize {extends} compilation

 10.07.2015
  - bugfix force file: resource in demo resource.extendsall.php

 08.07.2015
  - bugfix convert each word of class names to ucfirst in in compiler. (forum topic 25588)

 07.07.2015
  - improvement allow fetch() or display() called on a template object to get output from other template
     like $template->fetch('foo.tpl') https://github.com/smarty-php/smarty/issues/70
  - improvement Added $limit parameter to regex_replace modifier #71
  - new feature multiple indices on file: resource

 06.07.2015
  - optimize {block} compilation
  - optimization get rid of __get and __set in source object

 01.07.2015
  - optimize compile check handling
  - update {foreach} compiler
  - bugfix debugging console did not display string values containing \n, \r or \t correctly https://github.com/smarty-php/smarty/issues/66
  - optimize source resources

 28.06.2015
  - move $smarty->enableSecurity() into Smarty_Security class
  - optimize security isTrustedResourceDir()
  - move auto load filter methods into extension
  - move $smarty->getTemplateVars() into extension
  - move getStreamVariable() into extension
  - move $smarty->append() and $smarty->appendByRef() into extension
  - optimize autoloader
  - optimize file path normalization
  - bugfix PATH_SEPARATOR was replaced by mistake in autoloader
  - remove redundant code

 27.06.2015
  - bugfix resolve naming conflict between custom Smarty delimiter '<%' and PHP ASP tags https://github.com/smarty-php/smarty/issues/64
  - update $smarty->_realpath for relative path not starting with './'
  - update Smarty security with new realpath handling
  - update {include_php} with new realpath handling
  - move $smarty->loadPlugin() into extension
  - minor compiler optimizations
  - bugfix allow function plugins with name ending with 'close' https://github.com/smarty-php/smarty/issues/52
  - rework of $smarty->clearCompiledTemplate() and move it to its own extension

 19.06.2015
  - improvement allow closures as callback at $smarty->registerFilter() https://github.com/smarty-php/smarty/issues/59

 - 3.1.27- (18.06.2015)
 18.06.2015
  - bugfix another update on file path normalization failed on path containing something like "/.foo/" https://github.com/smarty-php/smarty/issues/56

 - 3.1.26- (18.06.2015)
 18.06.2015
  - bugfix file path normalization failed on path containing something like "/.foo/" https://github.com/smarty-php/smarty/issues/56

 17.06.2015
  - bugfix calling a plugin with nocache option but no other attributes like {foo nocache} caused call to undefined function https://github.com/smarty-php/smarty/issues/55

 - 3.1.25- (15.06.2015)
 15.06.2015
  - optimization of smarty_cachereource_keyvaluestore.php code

 14.06.2015
  - bugfix a relative sub template path could fail if template_dir path did contain /../ https://github.com/smarty-php/smarty/issues/50
  - optimization rework of path normalization
  - bugfix an output tag with variable, modifier followed by an operator like {$foo|modifier+1} did fail https://github.com/smarty-php/smarty/issues/53

 13.06.2015
  - bugfix a custom cache resource using smarty_cachereource_keyvaluestore.php did fail if php.ini mbstring.func_overload = 2 (forum topic 25568)

 11.06.2015
  - bugfix the lexer could hang on very large quoted strings (forum topic 25570)

 08.06.2015
  - bugfix using {$foo} as array index like $bar.{$foo} or in double quoted string like "some {$foo} thing" failed https://github.com/smarty-php/smarty/issues/49

 04.06.2015
  - bugfix possible error message on unset() while compiling {block} tags https://github.com/smarty-php/smarty/issues/46

 01.06.2015
  - bugfix <?xml ... ?> including template variables broken  since 3.1.22 https://github.com/smarty-php/smarty/issues/47

 27.05.2015
  - bugfix {include} with variable file name must not create by default individual cache file (since 3.1.22) https://github.com/smarty-php/smarty/issues/43

 24.05.2015
  - bugfix if condition string 'neq' broken due to a typo https://github.com/smarty-php/smarty/issues/42

 - 3.1.24- (23.05.2015)
 23.05.2015
  - improvement on php_handling to allow very large PHP sections, better error handling
  - improvement allow extreme large comment sections (forum 25538)

 21.05.2015
  - bugfix broken PHP 5.2 compatibility when compiling <?php tags https://github.com/smarty-php/smarty/issues/40
  - bugfix named {foreach} comparison like $smarty.foreach.foobar.index > 1 did compile into wrong code https://github.com/smarty-php/smarty/issues/41

 19.05.2015
  - bugfix compiler did overwrite existing variable value when setting the nocache attribute https://github.com/smarty-php/smarty/issues/39
  - bugfix output filter trimwhitespace could run into the pcre.backtrack_limit on large output (code.google issue 220)
  - bugfix compiler could run into the pcre.backtrack_limit on larger comment or {php} tag sections (forum 25538)

 18.05.2015
  - improvement introduce shortcuts in lexer/parser rules for most frequent terms for higher
    compilation speed

 16.05.2015
  - bugfix {php}{/php} did work just for single lines https://github.com/smarty-php/smarty/issues/33
  - improvement remove not needed ?><?php transitions from compiled code
  - improvement reduce number of lexer tokens on operators and if conditions
  - improvement higher compilation speed by modified lexer/parser generator at "smarty/smarty-lexer"

 13.05.2015
  - improvement remove not needed ?><?php transitions from compiled code
  - improvement of debugging:
      - use fresh Smarty object to display the debug console because of possible problems when the Smarty
        was extended or Smarty properties had been modified in the class source
      - display Smarty version number
      - Truncate lenght of Origin display and extend strin value display to 80 character
  - bugfix in Smarty_Security  'nl2br' should be a trusted modifier, not PHP function (code.google issue 223)

 12.05.2015
  - bugfix {$smarty.constant.TEST} did fail  on undefined constant https://github.com/smarty-php/smarty/issues/28
  - bugfix access to undefined config variable like {#undef#} did fail https://github.com/smarty-php/smarty/issues/29
  - bugfix in nested {foreach} saved item attributes got overwritten https://github.com/smarty-php/smarty/issues/33

 - 3.1.23 - (12.05.2015)
 12.05.2015
  - bugfix of smaller performance issue introduce in 3.1.22 when caching is enabled
  - bugfix missig entry for smarty-temmplate-config in autoloader

 - 3.1.22 - tag was deleted because 3.1.22 did fail caused by the missing entry for smarty-temmplate-config in autoloader
 10.05.2015
  - bugfix custom cache resource did not observe compile_id and cache_id when $cache_locking == true
  - bugfix cache lock was not handled correctly after timeout when $cache_locking == true
  - improvement added constants for $debugging

 07.05.2015
  - improvement of the debugging console. Read NEW_FEATURES.txt
  - optimization of resource class loading

 06.05.2015
  - bugfix in 3.1.22-dev cache resource must not be loaded for subtemplates
  - bugfix/improvement in 3.1.22-dev cache locking did not work as expected

 05.05.2015
  - optimization on cache update when main template is modified
  - optimization move <?php ?> handling from parser to new compiler module

 05.05.2015
  - bugfix code could be messed up when {tags} are used in multiple attributes https://github.com/smarty-php/smarty/issues/23

 04.05.2015
  - bugfix Smarty_Resource::parseResourceName incompatible with Google AppEngine (https://github.com/smarty-php/smarty/issues/22)
  - improvement use is_file() checks to avoid errors suppressed by @ which could still cause problems (https://github.com/smarty-php/smarty/issues/24)

 28.04.2015
  - bugfix plugins of merged subtemplates not loaded in 3.1.22-dev (forum topic 25508) 2nd fix

 28.04.2015
  - bugfix plugins of merged subtemplates not loaded in 3.1.22-dev (forum topic 25508)

 23.04.2015
  - bugfix a nocache template variable used as parameter at {insert} was by mistake cached

 20.04.2015
  - bugfix at a template function containing nocache code a parmeter could overwrite a template variable of same name

 27.03.2015
  - bugfix Smarty_Security->allow_constants=false; did also disable true, false and null (change of 16.03.2015)
  - improvement added a whitelist for trusted constants to security Smarty_Security::$trusted_constants (forum topic 25471)

 20.03.2015
  - bugfix make sure that function properties get saved only in compiled files containing the fuction definition {forum topic 25452}
  - bugfix correct update of global variable values on exit of template functions. (reported under Smarty Developers)

 16.03.2015
 - bugfix  problems with {function}{/function} and {call} tags in different subtemplate cache files {forum topic 25452}
 - bugfix  Smarty_Security->allow_constants=false; did not disallow direct usage of defined constants like {SMARTY_DIR} {forum topic 25457}
 - bugfix  {block}{/block} tags did not work inside double quoted strings https://github.com/smarty-php/smarty/issues/18


 15.03.2015
 - bugfix  $smarty->compile_check must be restored before rendering of a just updated cache file {forum 25452}

 14.03.2015
 - bugfix  {nocache}  {/nocache} tags corrupted code when used within a nocache section caused by a nocache template variable.

 - bugfix  template functions defined with {function} in an included subtemplate could not be called in nocache
           mode with {call... nocache} if the subtemplate had it's own cache file {forum 25452}

 10.03.2015
 - bugfix {include ... nocache} whith variable file or compile_id attribute was not executed in nocache mode.

 12.02.2015
 - bugfix multiple Smarty::fetch() of same template when $smarty->merge_compiled_includes = true; could cause function already defined error

 11.02.2015
 - bugfix recursive {includes} did create E_NOTICE message when $smarty->merge_compiled_includes = true; (github issue #16)

 22.01.2015
 - new feature security can now control access to static methods and properties
                see also NEW_FEATURES.txt

 21.01.2015
 - bugfix clearCompiledTemplates(), clearAll() and clear() could try to delete whole drive at wrong path permissions because realpath() fail (forum 25397)
 - bugfix 'self::' and 'parent::' was interpreted in template syntax as static class

 04.01.2015
 - push last weeks changes to github

 - different optimizations
 - improvement automatically create different versions of compiled templates and config files depending
   on property settings.
 - optimization restructure template processing by moving code into classes it better belongs to
 - optimization restructure config file processing

 31.12.2014
 - bugfix use function_exists('mb_get_info') for setting Smarty::$_MBSTRING.
   Function mb_split could be overloaded depending on php.ini mbstring.func_overload


 29.12.2014
 - new feature security can now limit the template nesting level by property $max_template_nesting
                see also NEW_FEATURES.txt (forum 25370)

 29.12.2014
 - new feature security can now disable special $smarty variables listed in property $disabled_special_smarty_vars
                see also NEW_FEATURES.txt (forum 25370)

 27.12.2014
  - bugfix clear internal _is_file_cache when plugins_dir was modified

 13.12.2014
  - improvement optimization of lexer and parser resulting in a up to 30% higher compiling speed

 11.12.2014
  - bugfix resolve parser ambiguity between constant print tag {CONST} and other smarty tags after change of 09.12.2014

 09.12.2014
  - bugfix variables $null, $true and $false did not work after the change of 12.11.2014 (forum 25342)
  - bugfix call of template function by a variable name did not work after latest changes (forum 25342)

 23.11.2014
  - bugfix a plugin with attached modifier could fail if the tag was immediately followed by another Smarty tag (since 3.1.21) (forum 25326)

 13.11.2014
  - improvement move autoload code into Autoloader.php. Use Composer autoloader when possible

 12.11.2014
 - new feature added support of namespaces to template code

 08.11.2014 - 10.11.2014
 - bugfix subtemplate called in nocache mode could be called with wrong compile_id when it did change on one of the calling templates
 - improvement add code of template functions called in nocache mode dynamically to cache file (related to bugfix of 01.11.2014)
 - bugfix Debug Console did not include all data from merged compiled subtemplates

 04.11.2014
 - new feature $smarty->debugging = true; => overwrite existing Debug Console window (old behaviour)
               $smarty->debugging = 2; => individual Debug Console window by template name

 03.11.2014
 - bugfix Debug Console did not show included subtemplates since 3.1.17 (forum 25301)
 - bugfix Modifier debug_print_var did not limit recursion or prevent recursive object display at Debug Console
    (ATTENTION: parameter order has changed to be able to specify maximum recursion)
 - bugfix Debug consol did not include subtemplate information with $smarty->merge_compiled_includes = true
 - improvement The template variables are no longer displayed as objects on the Debug Console
 - improvement $smarty->createData($parent = null, $name = null) new optional name parameter for display at Debug Console
 - addition of some hooks for future extension of Debug Console

 01.11.2014
 - bugfix and enhancement on subtemplate {include} and template {function} tags.
   * Calling a template which has a nocache section could fail if it was called from a cached and a not cached subtemplate.
   * Calling the same subtemplate cached and not cached with the $smarty->merge_compiled_includes enabled could cause problems
   * Many smaller related changes

 30.10.2014
 - bugfix access to class constant by object like {$object::CONST} or variable class name {$class::CONST} did not work (forum 25301)

 26.10.2014
 - bugfix E_NOTICE message was created during compilation when ASP tags '<%' or '%>' are in template source text
 - bugfix merge_compiled_includes option failed when caching  enables and same subtemplate was included cached and not cached

 - 3.1.21 - (18.10.2014)
 18.10.2014
  - composer moved to github

 17.10.2014
 - bugfix on $php_handling security and optimization of smarty_internal_parsetree (Thue Kristensen)

 16.10.2014
 - bugfix composer.json update

 15.10.2014
 - bugfix calling a new created cache file with fetch() and Smarty::CACHING_LIFETIME_SAVED multiple times did fail (forum 22350)

 14.10.2014
 - bugfix any tag placed within "<script language=php>" will throw a security exception to close all thinkable holes
 - bugfix classmap in root composer.json should start at "libs/..."
 - improvement cache is_file(file_exists) results of loadPlugin() to avoid unnecessary calls during compilation (Issue 201}

 12.10.2014
 - bugfix a comment like "<script{*foo*} language=php>" bypassed $php_handling checking (Thue Kristensen)
 - bugfix change of 08.10.2014 could create E_NOTICE meassage when using "<?php" tags
 - bugfix "<script language=php>" with $php_handling PHP_PASSTHRU was executed in {nocache} sections

 - 3.1.20 - (09.10.2014)
 08.10.2014
 - bugfix security mode of "<script language=php>" must be controlled by $php_handling property (Thue Kristensen)

 01.10.2014
 - bugfix template resource of inheritance blocks could get invalid if the default resource type is not 'file'(Issue 202)
 - bugfix existing child {block} tag must override parent {block} tag append / prepend setting (topic 25259)

 02.08.2014
 - bugfix modifier wordwrap did output break string wrong if first word was exceeding length with cut = true (topic 25193)

 24.07.2014
 - bugfix cache clear when cache folder does not exist

 16.07.2014
 - enhancement remove BOM automatically from template source (topic 25161)

 04.07.2014
 - bugfix the bufix of 02.06.2014 broke correct handling of child templates with same name but different template folders in extends resource (issue 194 and topic 25099)

 - 3.1.19 - (30.06.2014)
 20.06.2014
 - bugfix template variables could not be passed as parameter in {include} when the include was in a {nocache} section (topic 25131)

 17.06.2014
 - bugfix large template text of some charsets could cause parsing errors (topic 24630)

 08.06.2014
 - bugfix registered objects did not work after spelling fixes of 06.06.2014
 - bugfix {block} tags within {literal} .. {/literal} got not displayed correctly (topic 25024)
 - bugfix UNC WINDOWS PATH like "\\psf\path\to\dir" did not work as template directory (Issue 192)
 - bugfix {html_image} security check did fail on files relative to basedir (Issue 191)

 06.06.2014
 - fixed PHPUnit outputFilterTrimWhitespaceTests.php assertion of test result
 - fixed spelling, PHPDoc , minor errors, code cleanup

 02.06.2014
 - using multiple cwd with relative template dirs could result in identical compiled file names. (issue 194 and topic 25099)

 19.04.2014
 - bugfix calling createTemplate(template, data) with empty data array caused notice of array to string conversion (Issue 189)
 - bugfix clearCompiledTemplate() did not delete files on WINDOWS when a compile_id was specified

 18.04.2014
 - revert bugfix of 5.4.2014 because %-e date format is not supported on all operating systems

 - 3.1.18 - (07.04.2014)
 06.04.2014
 - bugfix template inheritance fail when using custom resource after patch of 8.3.2014 (Issue 187)
 - bugfix update of composer file (Issue 168 and 184)

 05.04.2014
 - bugfix default date format leads to extra spaces when displaying dates with single digit days (Issue 165)

 26.03.2014
 - bugfix Smart_Resource_Custom should not lowercase the resource name (Issue 183)

 24.03.2014
 - bugfix using a {foreach} property like @iteration could fail when used in inheritance parent templates (Issue 182)

 20.03.2014
 - bugfix $smarty->auto_literal and mbsting.func_overload 2, 6 or 7 did fail (forum topic 24899)

 18.03.2014
 - revert change of 17.03.2014

17.03.2014
 - bugfix $smarty->auto_literal and mbsting.func_overload 2, 6 or 7 did fail (forum topic 24899)

 15.03.2014
 - bugfix Smarty_CacheResource_Keyvaluestore did use different keys on read/writes and clearCache() calls (Issue 169)

 13.03.2014
 - bugfix clearXxx() change of 27.1.2014 did not work when specifing cache_id or compile_id  (forum topic 24868 and 24867)

 - 3.1.17 -
 08.03.2014
 - bugfix relative file path {include} within {block} of child templates did throw exception on first call (Issue 177)

 17.02.2014
 - bugfix Smarty failed when executing PHP on HHVM (Hip Hop 2.4) because uniqid('',true) does return string with ',' (forum topic 20343)

 16.02.2014
 - bugfix a '//' or '\\' in template_dir path could produce wrong path on relative filepath in {include} (Issue 175)

 05.02.2014
 - bugfix shared.literal_compiler_param.php did throw an exception when literal did contain a '-' (smarty-developers group)

 27.01.2014
 - bugfix $smarty->debugging = true; did show the variable of the $smarty object not the variables used in display() call (forum topic 24764)
 - bugfix clearCompiledTemplate(), clearAll() and clear() should use realpath to avoid possible exception from RecursiveDirectoryIterator (Issue 171)

 26.01.2014
 - bugfix  undo block nesting checks for {nocache} for reasons like forum topic 23280 (forum topic 24762)

 18.01.2014
 - bugfix the compiler did fail when using template inheritance and recursive {include} (smarty-developers group)

 11.01.2014
 - bugfix "* }" (spaces before right delimiter) was interpreted by mistake as comment end tag (Issue 170)
 - internals  content cache should be clear when updating cache file

 08.01.2014
 - bugfix Smarty_CacheResource_Custom did not handle template resource type specifications on clearCache() calls (Issue 169)
 - bugfix SmartyBC.class.php should use require_once to load Smarty.class.php (forum topic 24683)

 - 3.1.16 -
 15.12.2013
 - bugfix {include} with {block} tag handling (forum topic 24599, 24594, 24682) (Issue 161)
   Read 3.1.16_RELEASE_NOTES for more details
 - enhancement additional debug output at $smarty->_parserdebug = true;

 07.11.2013
 - bugfix too restrictive handling of {include} within {block} tags. 3.1.15 did throw errors where 3.1.14 did not (forum topic 24599)
 - bugfix compiler could fail if PHP mbstring.func_overload is enabled  (Issue 164)

 28.10.2013
 - bugfix variable resource name at custom resource plugin did not work within {block} tags (Issue 163)
 - bugfix notice "Trying to get property of non-object" removed (Issue 163)
 - bugfix correction of modifier capitalize fix from 3.10.2013  (issue 159)
 - bugfix multiple {block}s with same name in parent did not work (forum topic 24631)

 20.10.2013
 - bugfix a variable file name at {extends} tag did fail (forum topic 24618)

 14.10.2013
 - bugfix yesterdays fix could result in an undefined variable

 13.10.2013
 - bugfix variable names on {include} in template inheritance did unextepted error message (forum topic 24594) (Issue 161)
.- bugfix relative includes with same name like {include './foo.tpl'} from different folder failed (forum topic 24590)(Issue 161)

 04.10.2013
 - bugfix variable file names at {extends} had been disbabled by mistake with the rewrite of
   template inheritance of 24.08.2013   (forum topic 24585)

03.10.2013
 - bugfix loops using modifier capitalize did eat up memory (issue 159)

 - Smarty 3.1.15 -
01.10.2013
 - use current delimiters in compiler error messages (issue 157)
 - improvement on performance when using error handler and multiple template folders (issue 152)

17.09.2013
 - improvement added patch for additional SmartyCompilerException properties for better access to source information (forum topic 24559)

16.09.2013
 - bugfix recompiled templates did not show on first request with zend opcache cache (forum topic 24320)

13.09.2013
 - bugfix html_select_time defaulting error for the Meridian dropdown (forum topic 24549)

09.09.2012
- bugfix incorrect compiled code with array(object,method) callback at registered Variable Filter (forum topic 24542)

27.08.2013
- bugfix delimiter followed by linebreak did not work as auto literal after update from 24.08.2013 (forum topic 24518)

24.08.2013
- bugfix and enhancement
  Because several recent problems with template inheritance the {block} tag compiler has been rewriten
   - Error messages shown now the correct child template file and line number
   - The compiler could fail on some larger UTF-8 text block (forum topic 24455)
   - The {strip} tag can now be placed outside {block} tags in child templates (forum topic 24289)
- change SmartyException::$escape  is now false by default
- change PHP traceback has been remove for SmartyException and SmartyCompilerException

14.08.2013
- bugfix compiled filepath of config file did not observe different config_dir (forum topic 24493)

13.08.2013
- bugfix the internal resource cache did not observe config_dir changes (forum topic 24493)

12.08.2013
- bugfix internal $tmpx variables must be unique over all inheritance templates (Issue 149)

10.08.2013
- bugfix a newline was eaten when a <?xml ... ?> was passed by a Smarty variable and caching was enabled (forum topic 24482)

29.07.2013
- bugfix headers already send warning thrown when using 'SMARTY_DEBUG=on' from URL (Issue 148)

27.07.2013
- enhancement allow access to properties of registered opjects for Smarty2 BC (forum topic 24344)

26.07.2013
- bugfix template inheritance nesting problem (forum topic 24387)

15.7.2013
- update code generated by PSR-2 standards fixer which introduced PHP 5.4 incompatibilities of 14.7.2013

14.7.2013
- bugfix increase of internal maximum parser stacksize to allow more complex tag code {forum topic 24426}
- update for PHP 5.4 compatibility
- reformat source to PSR-2 standard

12.7.2013
- bugfix Do not remove '//' from file path at normalization (Issue 142)

2.7.2013
- bugfix trimwhitespace would replace captured items in wrong order (forum topic 24387)

## Smarty-3.1.14 -
27.06.2013
- bugfix removed PHP 5.5 deprecated preg_replace /e option in modifier capitalize (forum topic 24389)

17.06.2013
- fixed spelling in sources and documentation (from smarty-developers forum Veres Lajos)
- enhancement added constant SMARTY::CLEAR_EXPIRED for the change of 26.05.2013 (forum topic 24310)
- bugfix added smarty_security.php to composer.json (Issue 135)

26.05.2013
- enhancement an expire_time of -1 in clearCache() and clearAllCache() will delete outdated cache files
  by their individual cache_lifetime used at creation (forum topic 24310)

21.05.2013
- bugfix modifier strip_tags:true was compiled into wrong code (Forum Topic 24287)
- bugfix /n after ?> in Smarty.class.php did start output buffering (Issue 138)

25.04.2013
- bugfix escape and wordrap modifier could be compiled into wrong code when used in {nocache}{/nocache}
  section but caching is disabled  (Forum Topic 24260)

05.04.2013
- bugfix post filter must not run when compiling inheritance child blocks (Forum Topic 24094)
- bugfix after the fix for Issue #130 compiler exceptions got double escaped (Forum Topic 24199)

28.02.2013
- bugfix nocache blocks could be lost when using CACHING_LIFETIME_SAVED (Issue #133)
- bugfix Compile ID gets nulled when compiling child blocks (Issue #134)


24.01.2013
- bugfix wrong tag type in smarty_internal_templatecompilerbase.php could cause wrong plugin search order (Forum Topic 24028)

## Smarty-3.1.13 -
13.01.2013
- enhancement allow to disable exception message escaping by SmartyException::$escape = false;  (Issue #130)

09.01.2013
- bugfix compilation did fail when a prefilter did modify an {extends} tag c
- bugfix template inheritance could fail if nested {block} tags in childs did contain {$smarty.block.child} (Issue #127)
- bugfix template inheritance could fail if {block} tags in childs did have similar name as used plugins (Issue #128)
- added abstract method declaration doCompile() in Smarty_Internal_TemplateCompilerBase (Forum Topic 23969)

06.01.2013
- Allow '://' URL syntax in template names of stream resources  (Issue #129)

27.11.2012
- bugfix wrong variable usage in smarty_internal_utility.php (Issue #125)

26.11.2012
- bugfix global variable assigned within template function are not seen after template function exit (Forum Topic 23800)

24.11.2012
- made SmartyBC loadable via composer (Issue #124)

20.11.2012
- bugfix assignGlobal() called from plugins did not work (Forum Topic 23771)

13.11.2012
- adding attribute "strict" to html_options, html_checkboxes, html_radios to only print disabled/readonly attributes if their values are true or "disabled"/"readonly" (Issue #120)

01.11.2012
- bugfix muteExcpetedErrors() would screw up for non-readable paths (Issue #118)

## Smarty-3.1.12  -
14.09.2012
- bugfix template inheritance failed to compile with delimiters {/ and /} (Forum Topic 23008)

11.09.2012
- bugfix escape Smarty exception messages to avoid possible script execution

10.09.2012
- bugfix tag option flags and shorttag attributes did not work when rdel started with '=' (Forum Topic 22979)

31.08.2012
- bugfix resolving relative paths broke in some circumstances (Issue #114)

22.08.2012
- bugfix test MBString availability through mb_split, as it could've been compiled without regex support (--enable-mbregex).
  Either we get MBstring's full package, or we pretend it's not there at all.

21.08.2012
- bugfix $auto_literal = false did not work with { block} tags in child templates
  (problem was reintroduced after fix in 3.1.7)(Forum Topic 20581)

17.08.2012
- bugfix compiled code of nocache sections could contain wrong escaping (Forum Topic 22810)

15.08.2012
- bugfix template inheritance did produce wrong code if subtemplates with {block} was
  included several times (from smarty-developers forum)

14.08.2012
- bugfix PHP5.2 compatibility compromised by SplFileInfo::getBasename() (Issue 110)

01.08.2012
- bugfix avoid PHP error on $smarty->configLoad(...) with invalid section specification (Forum Topic 22608)

30.07.2012
-bugfix {assign} in a nocache section should not overwrite existing variable values
   during compilation (issue 109)

28.07.2012
- bugfix array access of config variables did not work (Forum Topic 22527)

19.07.2012
- bugfix the default plugin handler did create wrong compiled code for static class methods
  from external script files (issue 108)

## Smarty-3.1.11 -
30.06.2012
- bugfix {block.. hide} did not work as nested child (Forum Topic 22216)

25.06.2012
- bugfix the default plugin handler did not allow static class methods for modifier (issue 85)

24.06.2012
- bugfix escape modifier support for PHP < 5.2.3 (Forum Topic 21176)

11.06.2012
- bugfix the patch for Topic 21856 did break tabs between tag attributes (Forum Topic 22124)

## Smarty-3.1.10  -
09.06.2012
- bugfix the compiler did ignore registered compiler plugins for closing tags (Forum Topic 22094)
- bugfix the patch for Topic 21856 did break multiline tags (Forum Topic 22124)

## Smarty-3.1.9 -
07.06.2012
- bugfix fetch() and display() with relative paths (Issue 104)
- bugfix treat "0000-00-00" as 0 in modifier.date_format (Issue 103)

24.05.2012
- bugfix Smarty_Internal_Write_File::writeFile() could cause race-conditions on linux systems (Issue 101)
- bugfix attribute parameter names of plugins may now contain also "-"  and ":"  (Forum Topic 21856)
- bugfix add compile_id to cache key of of source (Issue 97)

22.05.2012
- bugfix recursive {include} within {section} did fail (Smarty developer group)

12.05.2012
- bugfix {html_options} did not properly escape values (Issue 98)

03.05.2012
- bugfix make HTTP protocall version variable (issue 96)

02.05.2012
- bugfix  {nocache}{block}{plugin}... did produce wrong compiled code when caching is disabled (Forum Topic 21572, issue 95)

12.04.2012
- bugfix Smarty did eat the linebreak after the <?xml...?> closing tag (Issue 93)
- bugfix concurrent cache updates could create a warning (Forum Topic 21403)

08.04.2012
- bugfix "\\" was not escaped correctly when generating nocache code (Forum Topic 21364)

30.03.2012
- bugfix template inheritance did  not throw exception when a parent template was deleted (issue 90)

27.03.2012
- bugfix prefilter did run multiple times on inline subtemplates compiled into several main templates (Forum Topic 21325)
- bugfix implement Smarty2's behaviour of variables assigned by reference in SmartyBC. {assign} will affect all references.
  (issue 88)

21.03.2012
- bugfix compileAllTemplates() and compileAllConfig() did not return the number of compiled files (Forum Topic 21286)

13.03.2012
- correction of yesterdays bugfix (Forum Topic 21175 and 21182)

12.03.2012
- bugfix a double quoted string of "$foo" did not compile into PHP "$foo" (Forum Topic 21175)
- bugfix template inheritance did set $merge_compiled_includes globally true

03.03.2012
- optimization of compiling speed when same modifier was used several times

02.03.2012
- enhancement the default plugin handler can now also resolve undefined modifier (Smarty::PLUGIN_MODIFIER)
  (Issue 85)

## Smarty-3.1.8  -
19.02.2012
- bugfix {include} could result in a fatal error if used in appended or prepended nested {block} tags
  (reported by mh and Issue 83)
- enhancement added Smarty special variable $smarty.template_object to return the current template object (Forum Topic 20289)


07.02.2012
- bugfix increase entropy of internal function names in compiled and cached template files (Forum Topic 20996)
- enhancement cacheable parameter added to default plugin handler, same functionality as in registerPlugin (request by calguy1000)

06.02.2012
- improvement stream_resolve_include_path() added to Smarty_Internal_Get_Include_Path (Forum Topic 20980)
- bugfix fetch('extends:foo.tpl') always yielded $source->exists == true (Forum Topic 20980)
- added modifier unescape:"url", fix (Forum Topic 20980)
- improvement replaced some calls of preg_replace with str_replace (Issue 73)

30.01.2012
- bugfix Smarty_Security internal $_resource_dir cache wasn't properly propagated

27.01.2012
- bugfix Smarty did not a template name of "0" (Forum Topic 20895)

20.01.2012
- bugfix typo in Smarty_Internal_Get_IncludePath did cause runtime overhead (Issue 74)
- improvment remove unneeded assigments (Issue 75 and 76)
- fixed typo in template parser
- bugfix output filter must not run before writing cache when template does contain nocache code (Issue 71)

02.01.2012
- bugfix {block foo nocache} did not load plugins within child {block} in nocache mode (Forum Topic 20753)

29.12.2011
- bugfix enable more entropy in Smarty_Internal_Write_File for "more uniqueness" and Cygwin compatibility (Forum Topic 20724)
- bugfix embedded quotes in single quoted strings did not compile correctly in {nocache} sections (Forum Topic 20730)

28.12.2011
- bugfix Smarty's internal header code must be excluded from postfilters (issue 71)

22.12.2011
- bugfix the new lexer of 17.12.2011 did fail if mbstring.func_overload != 0 (issue 70) (Forum Topic 20680)
- bugfix template inheritace did fail if mbstring.func_overload != 0 (issue 70) (Forum Topic 20680)

20.12.2011
- bugfix template inheritance: {$smarty.block.child} in nested child {block} tags did not return
  content after {$smarty.block.child} (Forum Topic 20564)

## Smarty-3.1.7 -
18.12.2011
- bugfix strings ending with " in multiline strings of config files failed to compile (issue #67)
- added chaining to Smarty_Internal_Templatebase
- changed unloadFilter() to not return a boolean in favor of chaining and API conformity
- bugfix unregisterObject() raised notice when object to unregister did not exist
- changed internals to use Smarty::$_MBSTRING ($_CHARSET, $_DATE_FORMAT) for better unit testing
- added Smarty::$_UTF8_MODIFIER for proper PCRE charset handling (Forum Topic 20452)
- added Smarty_Security::isTrustedUri() and Smarty_Security::$trusted_uri to validate
  remote resource calls through {fetch} and {html_image} (Forum Topic 20627)

17.12.2011
- improvement of compiling speed by new handling of plain text blocks in the lexer/parser (issue #68)

16.12.2011
- bugfix the source exits flag and timestamp was not setup when template was in php include path (issue #69)

9.12.2011
- bugfix {capture} tags around recursive {include} calls did throw exception (Forum Topic 20549)
- bugfix $auto_literal = false did not work with { block} tags in child templates (Forum Topic 20581)
- bugfix template inheritance: do not include code of {include} in overloaded {block} into compiled
  parent template (Issue #66}
- bugfix template inheritance: {$smarty.block.child} in nested child {block} tags did not return expected
  result (Forum Topic 20564)

## Smarty-3.1.6  -
30.11.2011
- bugfix is_cache() for individual cached subtemplates with $smarty->caching = CACHING_OFF did produce
  an exception (Forum Topic 20531)

29.11.2011
- bugfix added exception if the default plugin handler did return a not static callback (Forum Topic 20512)

25.11.2011
- bugfix {html_select_date} and {html_slecet_time} did not default to current time if "time" was not specified
  since r4432 (issue 60)

24.11.2011
- bugfix a subtemplate later used as main template did use old variable values

21.11.2011
- bugfix cache file could include unneeded modifier plugins under certain condition

18.11.2011
- bugfix declare all directory properties private to map direct access to getter/setter also on extended Smarty class

16.11.2011
- bugfix Smarty_Resource::load() did not always return a proper resource handler (Forum Topic 20414)
- added escape argument to html_checkboxes and html_radios (Forum Topic 20425)

## Smarty-3.1.5  -
14.11.2011
- bugfix allow space between function name and open bracket (forum topic 20375)

09.11.2011
- bugfix different behaviour of uniqid() on cygwin. See https://bugs.php.net/bug.php?id=34908
  (forum topic 20343)

01.11.2011
- bugfix {if} and {while} tags without condition did not throw a SmartyCompilerException (Issue #57)
- bugfix multiline strings in config files could fail on longer strings (reopened Issue #55)

22.10.2011
- bugfix smarty_mb_from_unicode() would not decode unicode-points properly
- bugfix use catch Exception instead UnexpectedValueException in
  clearCompiledTemplate to be PHP 5.2 compatible

21.10.2011
- bugfix apostrophe in plugins_dir path name failed (forum topic 20199)
- improvement sha1() for array keys longer than 150 characters
- add Smarty::$allow_ambiguous_resources to activate unique resource handling (Forum Topic 20128)

20.10.2011
- @silenced unlink() in Smarty_Internal_Write_File since debuggers go haywire without it.
- bugfix Smarty::clearCompiledTemplate() threw an Exception if $cache_id was not present in $compile_dir when $use_sub_dirs = true.
- bugfix {html_select_date} and {html_select_time} did not properly handle empty time arguments (Forum Topic 20190)
- improvement removed unnecessary sha1()

19.10.2011
- revert PHP4 constructor message
- fixed PHP4 constructor message

## Smarty-3.1.4 -
19.10.2011
- added exception when using PHP4 style constructor

16.10.2011
- bugfix testInstall() did not propery check cache_dir and compile_dir

15.10.2011
- bugfix Smarty_Resource and Smarty_CacheResource runtime caching (Forum Post 75264)

14.10.2011
- bugfix unique_resource did not properly apply to compiled resources (Forum Topic 20128)
- add locking to custom resources (Forum Post 75252)
- add Smarty_Internal_Template::clearCache() to accompany isCached() fetch() etc.

13.10.2011
- add caching for config files in Smarty_Resource
- bugfix disable of caching after isCached() call did not work (Forum Topic 20131)
- add concept unique_resource to combat potentially ambiguous template_resource values when custom resource handlers are used (Forum Topic 20128)
- bugfix multiline strings in config files could fail on longer strings (Issue #55)

11.10.2011
- add runtime checks for not matching {capture}/{/capture} calls (Forum Topic 20120)

10.10.2011
- bugfix variable name typo in {html_options} and {html_checkboxes} (Issue #54)
- bugfix <?xml> tag did create wrong output when caching enabled and the tag was in included subtemplate
- bugfix Smarty_CacheResource_mysql example was missing strtotime() calls

## Smarty-3.1.3  -
07.10.2011
- improvement removed html comments from {mailto} (Forum Topic 20092)
- bugfix testInstall() would not show path to internal plugins_dir (Forum Post 74627)
- improvement testInstall() now showing resolved paths and checking the include_path if necessary
- bugfix html_options plugin did not handle object values properly (Issue #49, Forum Topic 20049)
- improvement html_checkboxes and html_radios to accept null- and object values, and label_ids attribute
- improvement removed some unnecessary count()s
- bugfix parent pointer was not set when fetch() for other template was called on template object

06.10.2011
- bugfix switch lexer internals depending on mbstring.func_overload
- bugfix start_year and end_year of {html_select_date} did not use current year as offset base (Issue #53)

05.10.2011
- bugfix of problem introduced with r4342 by replacing strlen() with isset()
- add environment configuration issue with mbstring.func_overload Smarty cannot compensate for (Issue #45)
- bugfix nofilter tag option did not disable default modifier
- bugfix html_options plugin did not handle null- and object values properly (Issue #49, Forum Topic 20049)

04.10.2011
- bugfix assign() in plugins called in subtemplates did change value also in parent template
- bugfix of problem introduced with r4342 on math plugin
- bugfix output filter should not run on individually cached subtemplates
- add unloadFilter() method
- bugfix has_nocache_code flag was not reset before compilation

## Smarty-3.1.2  -
03.10.2011
- improvement add internal $joined_template_dir property instead computing it on the fly several times

01.10.2011
- improvement replaced most in_array() calls by more efficient isset() on array_flip()ed haystacks
- improvement replaced some strlen($foo) > 3 calls by isset($foo[3])
- improvement Smarty_Internal_Utility::clearCompiledTemplate() removed redundant strlen()s

29.09.2011
- improvement of Smarty_Internal_Config::loadConfigVars() dropped the in_array for index look up

28.09.2011
- bugfix on template functions called nocache calling other template functions

27.09.2011
- bugfix possible warning "attempt to modify property of non-object" in {section} (issue #34)
- added chaining to Smarty_Internal_Data so $smarty->assign('a',1)->assign('b',2); is possible now
- bugfix remove race condition when a custom resource did change timestamp during compilation
- bugfix variable property did not work on objects variable in template
- bugfix smarty_make_timestamp() failed to process DateTime objects properly
- bugfix wrong resource could be used on compile check of custom resource

26.09.2011
- bugfix repeated calls to same subtemplate did not make use of cached template object

24.09.2011
- removed internal muteExpectedErrors() calls in favor of having the implementor call this once from his application
- optimized muteExpectedErrors() to pass errors to the latest registered error handler, if appliccable
- added compile_dir and cache_dir to list of muted directories
- improvment better error message for undefined templates at {include}

23.09.2011
- remove unused properties
- optimization use real function instead anonymous function for preg_replace_callback
- bugfix a relative {include} in child template blocks failed
- bugfix direct setting of $template_dir, $config_dir, $plugins_dir in __construct() of an
  extended Smarty class created problems
- bugfix error muting was not implemented for cache locking

## Smarty 3.1.1  -
22.09.2011
- bugfix {foreachelse} does fail if {section} was nested inside {foreach}
- bugfix debug.tpl did not display correctly when it was compiled with escape_html = true

21.09.2011
- bugfix look for mixed case plugin file names as in 3.0 if not found try all lowercase
- added $error_muting to suppress error messages even for badly implemented error_handlers
- optimized autoloader
- reverted ./ and ../ handling in fetch() and display() - they're allowed again

20.09.2011
- bugfix removed debug echo output while compiling template inheritance
- bugfix relative paths in $template_dir broke relative path resolving in {include "../foo.tpl"}
- bugfix {include} did not work inside nested {block} tags
- bugfix {assign} with scope root and global did not work in all cases

19.09.2011
- bugfix regression in Smarty_CacheReource_KeyValueStore introduced by r4261
- bugfix output filter shall not run on included subtemplates

18.09.2011
- bugfix template caching did not care about file.tpl in different template_dir
- bugfix {include $file} was broken when merge_compiled_incluges = true
- bugfix {include} was broken when merge_compiled_incluges = true and same indluded template
  was used in different main templates in one compilation run
- bugfix for Smarty2 style compiler plugins on unnamed attribute passing like {tag $foo $bar}
- bugfix debug.tpl did not display correctly when it was compiled with escape_html = true

17.09.2011
- bugfix lock_id for file resource would create invalid filepath
- bugfix resource caching did not care about file.tpl in different template_dir

## Smarty 3.1.0  -
15/09/2011
- optimization of {foreach}; call internal _count() method only when "total" or "last" {foreach} properties are used

11/09/2011
- added  unregisterObject() method

06/09/2011
- bugfix  isset() did not work in templates on config variables

03/09/2011
- bugfix createTemplate() must default to cache_id and compile_id of Smarty object
- bugfix Smarty_CacheResource_KeyValueStore must include $source->uid in cache filepath to keep templates with same
  name but different folders separated
- added cacheresource.apc.php example in demo folder

02/09/2011
- bugfix cache lock file must use absolute filepath

01/09/2011
- update of cache locking

30/08/2011
- added locking mechanism to CacheResource API (implemented with File and KeyValueStores)

28/08/2011
- bugfix clearCompileTemplate() did not work for specific template subfolder or resource

27/08/2011
- bugfix {$foo|bar+1} did create syntax error

26/08/2011
- bugfix when generating nocache code which contains double \
- bugfix handle race condition if cache file was deleted between filemtime and include

17/08/2011
- bugfix CacheResource_Custom bad internal fetch() call

15/08/2011
- bugfix CacheResource would load content twice for KeyValueStore and Custom handlers

06/08/2011
- bugfix {include} with scope attribute could execute in wrong scope
- optimization of compile_check processing

03/08/2011
- allow comment tags to comment {block} tags out in child templates

26/07/2011
- bugfix experimental getTags() method did not work

24/07/2011
- sure opened output buffers are closed on exception
- bugfix {foreach} did not work on IteratorAggregate

22/07/2011
- clear internal caches on clearAllCache(), clearCache(), clearCompiledTemplate()

21/07/2011
- bugfix value changes of variable values assigned to Smarty object could not be seen on repeated $smarty->fetch() calls

17/07/2011
- bugfix {$smarty.block.child} did drop a notice at undefined child

15/07/2011
- bugfix individual cache_lifetime of {include} did not work correctly inside {block} tags
- added caches for Smarty_Internal_TemplateSource and Smarty_Internal_TemplateCompiled to reduce I/O for multiple cache_id rendering

14/07/2011
- made Smarty::loadPlugin() respect the include_path if required

13/07/2011
- optimized internal file write functionality
- bugfix PHP did eat line break on nocache sections
- fixed typo of Smarty_Security properties $allowed_modifiers and $disabled_modifiers

06/07/2011
- bugfix variable modifier must run befor gereral filtering/escaping

04/07/2011
- bugfix use (?P<name>) syntax at preg_match as some pcre libraries failed on (?<name>)
- some performance improvement when using generic getter/setter on template objects

30/06/2011
- bugfix generic getter/setter of Smarty properties used on template objects did throw exception
- removed is_dir and is_readable checks from directory setters for better performance

28/06/2011
- added back support of php template resource as undocumented feature
- bugfix automatic recompilation on version change could drop undefined index notice on old 3.0 cache and compiled files
- update of README_3_1_DEV.txt and moved into the distribution folder
- improvement show first characters of eval and string templates instead sha1 Uid in debug window

## Smarty 3.1-RC1 -
25/06/2011
- revert change of 17/06/2011. $_smarty varibale removed. call loadPlugin() from inside plugin code if required
- code cleanup, remove no longer used properties and methods
- update of PHPdoc comments

23/06/2011
- bugfix {html_select_date} would not respect current time zone

19/06/2011
- added $errors argument to testInstall() functions to suppress output.
- added plugin-file checks to testInstall()

18/06/2011
- bugfix mixed use of same subtemplate inline and not inline in same script could cause a warning during compilation

17/06/2011
- bugfix/change use $_smarty->loadPlugin() when loading nested depending plugins via loadPlugin
- bugfix {include ... inline} within {block}...{/block} did fail

16/06/2011
- bugfix do not overwrite '$smarty' template variable when {include ... scope=parent} is called
- bugfix complete empty inline subtemplates did fail

15/06/2011
- bugfix template variables where not accessable within inline subtemplates

12/06/2011
- bugfix removed unneeded merging of template variable when fetching includled subtemplates

10/06/2011
- made protected properties $template_dir, $plugins_dir, $cache_dir, $compile_dir, $config_dir accessible via magic methods

09/06/2011
- fix smarty security_policy issue in plugins {html_image} and {fetch}

05/06/2011
- update of SMARTY_VERSION
- bugfix made getTags() working again

04/06/2011
- allow extends resource in file attribute of {extends} tag

03/06/2011
- added {setfilter} tag to set filters for variable output
- added escape_html property to control autoescaping of variable output

27/05/2011
- added allowed/disabled tags and modifiers in security for sandboxing

23/05/2011
- added base64: and urlencode: arguments to eval and string resource types

22/05/2011
- made time-attribute of {html_select_date} and {html_select_time} accept arrays as defined by attributes prefix and field_array

13/05/2011
- remove setOption / getOption calls from SamrtyBC class

02/05/2011
- removed experimental setOption() getOption() methods
- output returned content also on opening tag calls of block plugins
- rewrite of default plugin handler
- compile code of variable filters for better performance

20/04/2011
- allow {php} {include_php} tags and PHP_ALLOW handling only with the SmartyBC class
- removed support of php template resource

20/04/2011
- added extendsall resource example
- optimization of template variable access
- optimization of subtemplate handling {include}
- optimization of template class

01/04/2011
- bugfix quote handling in capitalize modifier

28/03/2011
- bugfix stripslashes() requried when using PCRE e-modifier

04/03/2011
- upgrade to new PHP_LexerGenerator version 0.4.0 for better performance

27/02/2011
- ignore .svn folders when clearing cache and compiled files
- string resources do not need a modify check

26/02/2011
- replaced smarty_internal_wrapper by SmartyBC class
- load utility functions as static methods instead through __call()
- bugfix in extends resource when subresources are used
- optimization of modify checks

25/02/2011
- use $smarty->error_unassigned to control NOTICE handling on unassigned variables

21/02/2011
- added new new compile_check mode COMPILECHECK_CACHEMISS
- corrected new cloning behaviour of createTemplate()
- do no longer store the compiler object as property in the compile_tag classes to avoid possible memory leaks
  during compilation

19/02/2011
- optimizations on merge_compiled_includes handling
- a couple of optimizations and bugfixes related to new resource structure

17/02/2011
- changed ./ and ../ behaviour

14/02/2011
- added {block ... hide} option to suppress block if no child is defined

13/02/2011
- update handling of recursive subtemplate calls
- bugfix replace $smarty->triggerError() by exception in smarty_internal_resource_extends.php

12/02/2011
- new class Smarty_Internal_TemplateBase with shared methods of Smarty and Template objects
- optimizations of template processing
- made register... methods permanet
- code for default_plugin_handler
- add automatic recompilation at version change

04/02/2011
- change in Smarty_CacheResource_Custom
- bugfix cache_lifetime did not compile correctly at {include} after last update
- moved isCached processing into CacheResource class
- bugfix new CacheResource API did not work with disabled compile_check

03/02/2011
- handle template content as function to improve speed on multiple calls of same subtemplate and isCached()/display() calls
- bugfixes and improvents in the new resource API
- optimizations of template class code

25/01/2011
- optimized function html_select_time

22/01/2011
- added Smarty::$use_include_path configuration directive for Resource API

21/01/2011
- optimized function html_select_date

19/01/2011
- optimized outputfilter trimwhitespace

18/01/2011
- bugfix Config to use Smarty_Resource to fetch sources
- optimized Smarty_Security's isTrustedDir() and isTrustedPHPDir()

17/01/2011
- bugfix HTTP headers for CGI SAPIs

16/01/2011
- optimized internals of Smarty_Resource and Smarty_CacheResource

14/01/2011
- added modifiercompiler escape to improve performance of escaping html, htmlall, url, urlpathinfo, quotes, javascript
- added support to choose template_dir to load from: [index]filename.tpl

12/01/2011
- added unencode modifier to revert results of encode modifier
- added to_charset and from_charset modifier for character encoding

11/01/2011
- added SMARTY_MBSTRING to generalize MBString detection
- added argument $lc_rest to modifier.capitalize to lower-case anything but the first character of a word
- changed strip modifier to consider unicode white-space, too
- changed wordwrap modifier to accept UTF-8 strings
- changed count_sentences modifier to consider unicode characters and treat sequences delimited by ? and ! as sentences, too
- added argument $double_encode to modifier.escape (applies to html and htmlall only)
- changed escape modifier to be UTF-8 compliant
- changed textformat block to be UTF-8 compliant
- optimized performance of mailto function
- fixed spacify modifier so characters are not prepended and appended, made it unicode compatible
- fixed truncate modifier to properly use mb_string if possible
- removed UTF-8 frenzy from count_characters modifier
- fixed count_words modifier to treat "hello-world" as a single word like str_count_words() does
- removed UTF-8 frenzy from upper modifier
- removed UTF-8 frenzy from lower modifier

01/01/2011
- optimize smarty_modified_escape for hex, hexentity, decentity.

28/12/2010
- changed $tpl_vars, $config_vars and $parent to belong to Smarty_Internal_Data
- added Smarty::registerCacheResource() for dynamic cache resource object registration

27/12/2010
- added Smarty_CacheResource API and refactored existing cache resources accordingly
- added Smarty_CacheResource_Custom and Smarty_CacheResource_Mysql

26/12/2010
- added Smarty_Resource API and refactored existing resources accordingly
- added Smarty_Resource_Custom and Smarty_Resource_Mysql
- bugfix Smarty::createTemplate() to return properly cloned template instances

24/12/2010
- optimize smarty_function_escape_special_chars() for PHP >= 5.2.3

## SVN 3.0 trunk  -
14/05/2011
- bugfix error handling at stream resources

13/05/2011
- bugfix condition starting with "-" did fail at {if} and {while} tags

22/04/2011
- bugfix allow only fixed string as file attribute at {extends} tag

01/04/2011
- bugfix do not run filters and default modifier when displaying the debug template
- bugfix of embedded double quotes within multi line strings (""")

29/03/2011
- bugfix on error message in smarty_internal_compile_block.php
- bugfix mb handling in strip modifier
- bugfix for Smarty2 style registered compiler function on unnamed attribute passing like {tag $foo $bar}

17/03/2011
- bugfix on default {function} parameters when {function} was used in nocache sections
- bugfix on compiler object destruction. compiler_object property was by mistake unset.

09/03/2011
-bugfix a variable filter should run before modifiers on an output tag (see change of 23/07/2010)

08/03/2011
- bugfix loading config file without section should load only defaults

03/03/2011
- bugfix "smarty" template variable was not recreated when cached templated had expired
- bugfix internal rendered_content must be cleared after subtemplate was included

01/03/2011
- bugfix replace modifier did not work in 3.0.7 on systems without multibyte support
- bugfix {$smarty.template} could return in 3.0.7 parent template name instead of
         child name when it needed to compile

25/02/2011
- bugfix for Smarty2 style compiler plugins on unnamed attribute passing like {tag $foo $bar}

24/02/2011
- bugfix $smarty->clearCache('some.tpl') did by mistake cache the template object

18/02/2011
- bugfix removed possible race condition when isCached() was called for an individually cached subtemplate
- bugfix force default debug.tpl to be loaded by the file resource

17/02/2011
-improvement not to delete files starting with '.' from cache and template_c folders on clearCompiledTemplate() and clearCache()

16/02/2011
-fixed typo in exception message of Smarty_Internal_Template
-improvement allow leading spaces on } tag closing if auto_literal is enabled

13/02/2011
- bufix replace $smarty->triggerError() by exception
- removed obsolete {popup_init..} plugin from demo templates
- bugfix replace $smarty->triggerError() by exception in smarty_internal_resource_extends.php

## Smarty 3.0.7  -
09/02/2011
- patched vulnerability when using {$smarty.template}

01/02/2011
- removed assert() from config and template parser

31/01/2011
- bugfix the lexer/parser did fail on special characters like VT

16/01/2011
-bugfix of ArrayAccess object handling in internal _count() method
-bugfix of Iterator object handling in internal _count() method

14/01/2011
-bugfix removed memory leak while processing compileAllTemplates

12/01/2011
- bugfix in {if} and {while} tag compiler when using assignments as condition and nocache mode

10/01/2011
- bugfix when using {$smarty.block.child} and name of {block} was in double quoted string
- bugfix updateParentVariables() was called twice when leaving {include} processing

- bugfix mb_str_replace in replace and escape modifiers work with utf8

31/12/2010
- bugfix dynamic configuration of $debugging_crtl did not work
- bugfix default value of $config_read_hidden changed to false
- bugfix format of attribute array on compiler plugins
- bugfix getTemplateVars() could return value from wrong scope

28/12/2010
- bugfix multiple {append} tags failed to compile.

22/12/2010
- update do not clone the Smarty object an internal createTemplate() calls to increase performance

21/12/2010
- update html_options to support class and id attrs

17/12/2010
- bugfix added missing support of $cache_attrs for registered plugins

15/12/2010
- bugfix assignment as condition in {while} did drop an E_NOTICE

14/12/2010
- bugfix when passing an array as default parameter at {function} tag

13/12/2010
- bugfix {$smarty.template} in child template did not return right content
- bugfix Smarty3 did not search the PHP include_path for template files

## Smarty 3.0.6  -

12/12/2010
- bugfix fixed typo regarding yesterdays change to allow streamWrapper

11/12/2010
- bugfix nested block tags in template inheritance child templates did not work correctly
- bugfix {$smarty.current_dir} in child template did not point to dir of child template
- bugfix changed code when writing temporary compiled files to allow stream_wrapper

06/12/2010
- bugfix getTemplateVars() should return 'null' instead dropping E_NOTICE on an unassigned variable

05/12/2010
- bugfix missing declaration of $smarty in Smarty class
- bugfix empty($foo) in {if} did drop a notice when $foo was not assigned

01/12/2010
- improvement of {debug} tag output

27/11/2010
-change run output filter before cache file is written. (same as in Smarty2)

24/11/2011
-bugfix on parser at  !$foo|modifier
-change parser logic when assignments used as condition in {if] and {while} to allow assign to array element

23/11/2011
-bugfix allow integer as attribute name in plugin calls
-change  trimm whitespace from error message, removed long list of expected tokens

22/11/2010
- bugfix on template inheritance when an {extends} tag was inserted by a prefilter
- added error message for illegal variable file attributes at {extends...} tags

## Smarty 3.0.5  -


19/11/2010
- bugfix on block plugins with modifiers

18/11/2010
- change on handling of unassigned template variable -- default will drop E_NOTICE
- bugfix on Smarty2 wrapper load_filter() did not work

17/11/2010
- bugfix on {call} with variable function name
- bugfix on {block} if name did contain '-'
- bugfix in function.fetch.php , referece to undefined $smarty

16/11/2010
- bugfix whitespace in front of "<?php" in smarty_internal_compile_private_block_plugin.php
- bugfix {$smarty.now} did compile incorrectly
- bugfix on reset(),end(),next(),prev(),current() within templates
- bugfix on default parameter for {function}

15/11/2010
- bugfix when using {$smarty.session} as object
- bugfix scoping problem on $smarty object passed to filters
- bugfix captured content could not be accessed globally
- bugfix Smarty2 wrapper functions could not be call from within plugins

## Smarty 3.0.4  -

14/11/2010
- bugfix isset() did not allow multiple parameter
- improvment of some error messages
- bugfix html_image did use removed property $request_use_auto_globals
- small performace patch in Smarty class

13/11/2010
- bugfix  overloading problem when $smarty->fetch()/display() have been used in plugins
				(introduced with 3.0.2)
- code cleanup
								
## Smarty 3.0.3  -

13/11/2010
- bugfix on {debug}
- reverted location of loadPlugin() to Smarty class
- fixed comments in plugins
- fixed internal_config (removed unwanted code line)
- improvement  remove last linebreak from {function} definition

## Smarty 3.0.2  -

12/11/2010
- reactivated $error_reporting property handling
- fixed typo in compile_continue
- fixed security in {fetch} plugin
- changed back plugin parameters to two. second is template object
  with transparent access to Smarty object
- fixed {config_load} scoping form compile time to run time

## Smarty 3.0.0  -



11/11/2010
- major update including some API changes

10/11/2010
- observe compile_id also for config files

09/11/2010
-bugfix on  complex expressions as start value for {for} tag
request_use_auto_globals
04/11/2010
- bugfix do not allow access of dynamic and private object members of assigned objects when
  security is enabled.

01/11/2010
- bugfix related to E_NOTICE change.  {if empty($foo)} did fail when $foo contained a string

28/10/2010
- bugfix on compiling modifiers within $smarty special vars like {$smarty.post.{$foo|lower}}

27/10/2010
- bugfix default parameter values did not work for template functions included with {include}

25/10/2010
- bugfix for E_NOTICE change, array elements did not work as modifier parameter

20/10/2010
- bugfix for the E_NOTICE change

19/10/2010
- change Smarty does no longer mask out E_NOTICE by default during template processing

13/10/2010
- bugfix removed ambiguity between ternary and stream variable in template syntax
- bugfix use caching properties of template instead of smarty object when compiling child {block}
- bugfix {*block}...{/block*} did throw an exception in template inheritance
- bugfix on template inheritance using nested eval or string resource in {extends} tags
- bugfix on output buffer handling in isCached() method

##  RC4 -

01/10/2010
- added {break} and {continue} tags for flow control of {foreach},{section},{for} and {while} loops
- change of 'string' resource. It's no longer evaluated and compiled files are now stored
- new 'eval' resource which evaluates a template without saving the compiled file
- change in isCached() method to allow multiple calls for the same template

25/09/2010
- bugfix on some compiling modifiers

24/09/2010
- bugfix merge_compiled_includes flag was not restored correctly in {block} tag

22/09/2010
- bugfix on default modifier

18/09/2010
- bugfix untility compileAllConfig() did not create sha1 code for compiled template file names if template_dir was defined with no trailing DS
- bugfix on templateExists() for extends resource

17/09/2010
- bugfix {$smarty.template} and {$smarty.current_dir} did not compile correctly within {block} tags
- bugfix corrected error message on missing template files in extends resource
- bugfix untility compileAllTemplates() did not create sha1 code for compiled template file names if template_dir was defined with no trailing DS

16/09/2010
- bugfix when a doublequoted modifier parameter did contain Smarty tags and ':'

15/09/2010
- bugfix resolving conflict between '<%'/'%>' as custom Smarty delimiter and ASP tags
- use ucfirst for resource name on internal resource class names

12/09/2010
- bugfix for change of 08/09/2010 (final {block} tags in subtemplates did not produce correct results)

10/09/2010
- bugfix for change of 08/09/2010 (final {block} tags in subtemplates did not produce correct results)

08/09/2010
- allow multiple template inheritance branches starting in subtemplates

07/09/2010
- bugfix {counter} and {cycle} plugin assigned result to smarty variable not in local(template) scope
- bugfix templates containing just {strip} {/strip} tags did produce an error


23/08/2010
- fixed E_STRICT errors for uninitialized variables

22/08/2010
- added attribute cache_id to {include} tag

13/08/2010
- remove exception_handler property from Smarty class
- added Smarty's own exceptions SmartyException and SmartyCompilerException

09/08/2010
- bugfix on modifier with doublequoted strings as parameter containing embedded tags

06/08/2010
- bugfix when cascading some modifier like |strip|strip_tags modifier

05/08/2010
- added plugin type modifiercompiler to produce compiled modifier code
- changed standard modifier plugins to the compiling versions whenever possible
- bugfix in nocache sections {include} must not cache the subtemplate

02/08/2010
- bugfix strip did not work correctly in conjunction with comment lines

31/07/2010
- bugfix on nocache attribute at {assign} and {append}

30/07/2010
- bugfix passing scope attributes in doublequoted strings did not work at {include} {assign} and {append}

25/07/2010
- another bugfix of change from 23/07/2010 when compiling modifier

24/07/2010
- bugfix of change from 23/07/2010 when compiling modifier

23/07/2010
- changed execution order. A variable filter does now run before modifiers on output of variables
- bugfix use always { and } as delimiter for debug.tpl


22/07/2010
- bugfix in templateExists() method

20/07/2010
- fixed handling of { strip } tag with whitespaces

15/07/2010
- bufix  {$smarty.template} does include now the relative path, not just filename

##  RC3 -




15/07/2010
- make the date_format modifier work also on objects of the DateTime class
- implementation of parsetrees in the parser to close security holes and remove unwanted empty line in HTML output

08/07/2010
- bugfix on assigning multidimensional arrays within templates
- corrected bugfix for truncate modifier

07/07/2010
- bugfix the truncate modifier needs to check if the string is utf-8 encoded or not
- bugfix support of script files relative to trusted_dir

06/07/2010
- create exception on recursive {extends} calls
- fixed reported line number at "unexpected closing tag " exception
- bugfix on escape:'mail' modifier
- drop exception if 'item' variable is equal 'from' variable in {foreach} tag

01/07/2010
- removed call_user_func_array calls for optimization of compiled code when using registered modifiers and plugins

25/06/2010
- bugfix escaping " when block tags are used within doublequoted strings

24/06/2010
- replace internal get_time() calls with standard PHP5 microtime(true) calls in Smarty_Internal_Utility
- added $smarty->register->templateClass() and $smarty->unregister->templateClass() methods for supporting static classes with namespace


22/06/2010
- allow spaces between typecast and value in template syntax
- bugfix get correct count of traversables in {foreach} tag

21/06/2010
- removed use of PHP shortags SMARTY_PHP_PASSTHRU mode
- improved speed of cache->clear() when a compile_id was specified and use_sub_dirs is true

20/06/2010
- replace internal get_time() calls with standard PHP5 microtime(true) calls
- closed security hole when php.ini asp_tags = on

18/06/2010
- added __toString method to the Smarty_Variable class


14/06/2010
- make handling of Smarty comments followed by newline BC to Smarty2


##  RC2 -



13/06/2010
- bugfix Smarty3 did not handle hexadecimals like 0x0F as numerical value
- bugifx Smarty3 did not accept numerical constants like .1 or 2. (without a leading or trailing digit)

11/06/2010
- bugfix the lexer did fail on larger {literal} ... {/literal} sections

03/06/2010
- bugfix on calling template functions like Smarty tags

01/06/2010
- bugfix on template functions used with template inheritance
- removed /* vim: set expandtab: */ comments
- bugfix of auto literal problem introduce with fix of 31/05/2010

31/05/2010
- bugfix the parser did not allow some smarty variables with special name like $for, $if, $else and others.

27/05/2010
- bugfix on object chaining using variable properties
- make scope of {counter} and {cycle} tags again global as in Smarty2

26/05/2010
- bugfix removed decrepated register_resource call in smarty_internal_template.php

25/05/2010
- rewrite of template function handling to improve speed
- bugfix on file dependency when merge_compiled_includes = true


16/05/2010
- bugfix when passing parameter with numeric name like {foo 1='bar' 2='blar'}

14/05/2010
- bugfix compile new config files if compile_check and force_compile = false
- added variable static classes names to template syntax

11/05/2010
- bugfix  make sure that the cache resource is loaded in all conditions when template methods getCached... are called externally
- reverted the change 0f 30/04/2010. With the exception of forward references template functions can be again called by a standard tag.

10/05/2010
- bugfix on {foreach} and {for} optimizations of 27/04/2010

09/05/2010
- update of template and config file parser because of minor parser generator bugs

07/05/2010
- bugfix on {insert}

06/05/2010
- bugfix when merging compiled templates and objects are passed as parameter of the {include} tag

05/05/2010
- bugfix on {insert} to cache parameter
- implementation of $smarty->default_modifiers as in Smarty2
- bugfix on getTemplateVars method

01/05/2010
- bugfix on handling of variable method names at object chaning

30/04/2010
- bugfix when comparing timestamps in sysplugins/smarty_internal_config.php
- work around of a substr_compare bug in older PHP5 versions
- bugfix on template inheritance for tag names starting with "block"
- bugfix on {function} tag with name attribute in doublequoted strings
- fix to make calling of template functions unambiguously by madatory usage of the {call} tag

##  RC1 -

27/04/2010
- change default of $debugging_ctrl to 'NONE'
- optimization of compiled code of {foreach} and {for} loops
- change of compiler for config variables

27/04/2010
- bugfix in $smarty->cache->clear() method. (do not cache template object)


17/04/2010
- security fix in {math} plugin


12/04/2010
- bugfix in smarty_internal_templatecompilerbase (overloaded property)
- removed parser restrictions in using true,false and null as ID

07/04/2010
- bugfix typo in smarty_internal_templatecompilerbase

31/03/2010
- compile locking by touching old compiled files to avoid concurrent compilations

29/03/2010
- bugfix allow array definitions as modifier parameter
- bugfix observe compile_check property when loading config files
- added the template object as third filter parameter

25/03/2010
- change of utility->compileAllTemplates() log messages
- bugfix on nocache code in {function} tags
- new method utility->compileAllConfig() to compile all config files

24/03/2010
- bugfix on register->modifier() error messages

23/03/2010
- bugfix on template inheritance when calling multiple child/parent relations
- bugfix on caching mode SMARTY_CACHING_LIFETIME_SAVED and cache_lifetime = 0

22/03/2010
- bugfix make directory separator operating system independend in compileAllTemplates()

21/03/2010
- removed unused code in compileAllTemplates()

19/03/2010
- bugfix for multiple {/block} tags on same line

17/03/2010
- bugfix make $smarty->cache->clear() function independent from caching status

16/03/2010
- bugfix on assign attribute at registered template objects
- make handling of modifiers on expression BC to Smarty2

15/03/2010
- bugfix on block plugin calls

11/03/2010
- changed parsing of <?php and ?> back to Smarty2 behaviour

08/03/2010
- bugfix on uninitialized properties in smarty_internal_template
- bugfix on $smarty->disableSecurity()

04/03/2010
- bugfix allow uppercase chars in registered resource names
- bugfix on accessing chained objects of static classes

01/03/2010
- bugfix on nocache code in {block} tags if child template was included by {include}

27/02/2010
- allow block tags inside double quoted string

26/02/2010
- cache modified check implemented
- support of access to a class constant from an object (since PHP 5.3)

24/02/2010
- bugfix on expressions in doublequoted string enclosed in backticks
- added security property $static_classes for static class security

18/02/2010
- bugfix on parsing Smarty tags inside <?xml ... ?>
- bugfix on truncate modifier

17/02/2010
- removed restriction that modifiers did require surrounding parenthesis in some cases
- added {$smarty.block.child} special variable for template inheritance

16/02/2010
- bugfix on <?xml ... ?> tags for all php_handling modes
- bugfix on parameter of variablefilter.htmlspecialchars.php plugin

14/02/2010
- added missing _plugins property in smarty.class.php
- bugfix $smarty.const... inside doublequoted strings and backticks was compiled into wrong PHP code

12/02/2010
- bugfix on nested {block} tags
- changed Smarty special variable $smarty.parent to $smarty.block.parent
- added support of nested {bock} tags

10/02/2010
- avoid possible notice on $smarty->cache->clear(...), $smarty->clear_cache(....)
- allow Smarty tags inside <? ... ?> tags in SMARTY_PHP_QUOTE and SMARTY_PHP_PASSTHRU mode
- bugfix at new "for" syntax like {for $x=1 to 10 step 2}

09/02/2010
- added $smarty->_tag_stack for tracing block tag hierarchy

08/02/2010
- bugfix  use template fullpath at §smarty->cache->clear(...), $smarty->clear_cache(....)
- bugfix of cache filename on extended templates when force_compile=true

07/02/2010
- bugfix on changes of 05/02/2010
- preserve line endings type form template source
- API changes (see README file)

05/02/2010
- bugfix on modifier and block plugins with same name

02/02/2010
- retaining newlines at registered functions and function plugins

01/25/2010
- bugfix cache resource was not loaded when caching was globally off but enabled at a template object
- added test that $_SERVER['SCRIPT_NAME'] does exist in Smarty.class.php

01/22/2010
- new method $smarty->createData([$parent]) for creating a data object (required for bugfixes below)
- bugfix config_load() method now works also on a data object
- bugfix get_config_vars() method now works also on a data and template objects
- bugfix clear_config() method now works also on a data and template objects

01/19/2010
- bugfix on plugins if same plugin was called from a nocache section first and later from a cached section


###beta 7###


01/17/2010
- bugfix on $smarty.const... in double quoted strings

01/16/2010
- internal change of config file lexer/parser on handling of section names
- bugfix on registered objects (format parameter of register_object was not handled correctly)

01/14/2010
- bugfix on backslash within single quoted strings
- bugfix allow absolute filepath for config files
- bugfix on special Smarty variable $smarty.cookies
- revert handling of newline on no output tags like {if...}
- allow special characters in config file section names for Smarty2 BC

01/13/2010
- bugfix on {if} tags

01/12/2010
- changed back modifier handling in parser. Some restrictions still apply:
    if modifiers are used in side {if...} expression or in mathematical expressions
    parentheses must be used.
- bugfix the {function..} tag did not accept the name attribute in double quotes
- closed possible security hole at <?php ... ?> tags
- bugfix of config file parser on large config files


###beta 6####

01/11/2010
- added \n to the compiled code of the {if},{else},{elseif},{/if} tags to get output of newlines as expected by the template source
- added missing support of insert plugins
- added optional nocache attribute to {block} tags in parent template
- updated <?php...?> handling supporting now heredocs and newdocs. (thanks to Thue Jnaus Kristensen)

01/09/2010
- bugfix on nocache {block} tags in parent templates

01/08/2010
- bugfix on variable filters. filter/nofilter attributes did not work on output statements

01/07/2010
- bugfix on file dependency at template inheritance
- bugfix on nocache code at template inheritance

01/06/2010
- fixed typo in smarty_internal_resource_registered
- bugfix for custom delimiter at extends resource and {extends} tag

01/05/2010
- bugfix sha1() calculations at extends resource and some general improvments on sha1() handling


01/03/2010
- internal change on building cache files

01/02/2010
- update cached_timestamp at the template object after cache file is written to avoid possible side effects
- use internally always SMARTY_CACHING_LIFETIME_* constants

01/01/2010
- bugfix for obtaining plugins which must be included (related to change of 12/30/2009)
- bugfix for {php} tag (trow an exception if allow_php_tag = false)

12/31/2009
- optimization of generated code for doublequoted strings containing variables
- rewrite of {function} tag handling
  - can now be declared in an external subtemplate
  - can contain nocache sections (nocache_hash handling)
  - can be called in noccache sections (nocache_hash handling)
  - new {call..} tag to call template functions with a variable name {call name=$foo}
- fixed nocache_hash handling in merged compiled templates

12/30/2009
- bugfix for plugins defined in the script as smarty_function_foo

12/29/2009
- use sha1() for filepath encoding
- updates on nocache_hash handling
- internal change on merging some data
- fixed cache filename for custom resources

12/28/2009
- update for security fixes
- make modifier plugins always trusted
- fixed bug loading modifiers in child template at template inheritance

12/27/2009
--- this is a major update with a couple of internal changes ---
- new config file lexer/parser (thanks to Thue Jnaus Kristensen)
- template lexer/parser fixes for PHP and {literal} handing (thanks to Thue Jnaus Kristensen)
- fix on registered plugins with different type but same name
- rewrite of plugin handling (optimized execution speed)
- closed a security hole regarding PHP code injection into cache files
- fixed bug in clear cache handling
- Renamed a couple of internal classes
- code cleanup for merging compiled templates
- couple of runtime optimizations (still not all done)
- update of getCachedTimestamp()
- fixed bug on modifier plugins at nocache output

12/19/2009
- bugfix on comment lines in config files

12/17/2009
- bugfix of parent/global variable update at included/merged subtemplates
- encode final template filepath into filename of compiled and cached files
- fixed {strip} handling in auto literals

12/16/2009
- update of changelog
- added {include file='foo.tpl' inline}  inline option to merge compiled code of subtemplate into the calling template

12/14/2009
- fixed sideefect of last modification (objects in array index did not work anymore)

12/13/2009
- allow boolean negation ("!") as operator on variables outside {if} tag

12/12/2009
- bugfix on single quotes inside {function} tag
- fix short append/prepend attributes in {block} tags

12/11/2009
- bugfix on clear_compiled_tpl (avoid possible warning)

12/10/2009
- bugfix on {function} tags and template inheritance

12/05/2009
- fixed problem when a cached file was fetched several times
- removed unneeded lexer code

12/04/2009
- added max attribute to for loop
- added security mode allow_super_globals

12/03/2009
- template inheritance: child templates can now call functions defined by the {function} tag in the parent template
- added {for $foo = 1 to 5 step 2}  syntax
- bugfix for {$foo.$x.$y.$z}

12/01/2009
- fixed parsing of names of special formated tags like if,elseif,while,for,foreach
- removed direct access to constants in templates because of some syntax problems
- removed cache resource plugin for mysql from the distribution
- replaced most hard errors (exceptions) by softerrors(trigger_error) in plugins
- use $template_class property for template class name when compiling {include},{eval} and {extends} tags

11/30/2009
- map 'true' to SMARTY_CACHING_LIFETIME_CURRENT for the $smarty->caching parameter
- allow {function} tags within {block} tags

11/28/2009
- ignore compile_id at debug template
- added direct access to constants in templates
- some lexer/parser optimizations

11/27/2009
- added cache resource MYSQL plugin

11/26/2009
- bugfix on nested doublequoted strings
- correct line number on unknown tag error message
- changed {include} compiled code
- fix on checking dynamic varibales with error_unassigned = true

11/25/2009
- allow the following writing for boolean: true, TRUE, True, false, FALSE, False
- {strip} tag functionality rewritten

11/24/2009
- bugfix for $smarty->config_overwrite = false

11/23/2009
- suppress warnings on unlink caused by race conditions
- correct line number on unknown tag error message

------- beta 5
11/23/2009
- fixed configfile parser for text starting with a numeric char
- the default_template_handler_func may now return a filepath to a template source

11/20/2009
- bugfix for empty config files
- convert timestamps of registered resources to integer

11/19/2009
- compiled templates are no longer touched with the filemtime of template source

11/18/2009
- allow integer as attribute name in plugin calls

------- beta 4
11/18/2009
- observe umask settings when setting file permissions
- avoide unneeded cache file creation for subtemplates which did occur in some situations
- make $smarty->_current_file available during compilation for Smarty2 BC

11/17/2009
- sanitize compile_id and cache_id (replace illegal chars with _)
- use _dir_perms and _file_perms properties at file creation
- new constant SMARTY_RESOURCE_DATE_FORMAT (default '%b %e, %Y') which is used as default format in modifier date_format
- added {foreach $array as $key=>$value} syntax
- renamed extend tag and resource to extends: {extends file='foo.tol'} , $smarty->display('extends:foo.tpl|bar.tpl);
- bugfix cycle plugin

11/15/2009
- lexer/parser optimizations on quoted strings

11/14/2009
- bugfix on merging compiled templates when source files got removed or renamed.
- bugfix modifiers on registered object tags
- fixed locaion where outputfilters are running
- fixed config file definitions at EOF
- fix on merging compiled templates with nocache sections in nocache includes
- parser could run into a PHP error on wrong file attribute

11/12/2009
- fixed variable filenames in {include_php} and {insert}
- added scope to Smarty variables in the {block} tag compiler
- fix on nocache code in child {block} tags

11/11/2009
- fixed {foreachelse}, {forelse}, {sectionelse} compiled code at nocache variables
- removed checking for reserved variables
- changed debugging handling

11/10/2009
- fixed preg_qoute on delimiters

11/09/2009
- lexer/parser bugfix
- new SMARTY_SPL_AUTOLOAD constant to control the autoloader option
- bugfix for {function} block tags in included templates

11/08/2009
- fixed alphanumeric array index
- bugfix on complex double quoted strings

11/05/2009
- config_load method can now be called on data and template objects

11/04/2009
- added typecasting support for template variables
- bugfix on complex indexed special Smarty variables

11/03/2009
- fixed parser error on objects with special smarty vars
- fixed file dependency for {incude} inside {block} tag
- fixed not compiling on non existing compiled templates when compile_check = false
- renamed function names of autoloaded Smarty methods to Smarty_Method_....
- new security_class property (default is Smarty_Security)

11/02/2009
- added neq,lte,gte,mod as aliases to if conditions
- throw exception on illegal Smarty() constructor calls

10/31/2009
- change of filenames in sysplugins folder for internal spl_autoload function
- lexer/parser changed for increased compilation speed

10/27/2009
- fixed missing quotes in include_php.php

10/27/2009
- fixed typo in method.register_resource
- pass {} through as literal

10/26/2009
- merge only compiled subtemplates into the compiled code of the main template

10/24/2009
- fixed nocache vars at internal block tags
- fixed merging of recursive includes

10/23/2009
- fixed nocache var problem

10/22/2009
- fix trimwhitespace outputfilter parameter

10/21/2009
- added {$foo++}{$foo--} syntax
- buxfix changed PHP "if (..):" to "if (..){" because of possible bad code when concenating PHP tags
- autoload Smarty internal classes
- fixed file dependency for config files
- some code optimizations
- fixed function definitions on some autoloaded methods
- fixed nocache variable inside if condition of {if} tag

10/20/2009
- check at compile time for variable filter to improve rendering speed if no filter is used
- fixed bug at combination of {elseif} tag and {...} in double quoted strings of static class parameter

10/19/2009
- fixed compiled template merging on variable double quoted strings as name
- fixed bug in caching mode 2 and cache_lifetime -1
- fixed modifier support on block tags

10/17/2009
- remove ?>\n<?php and ?><?php sequences from compiled template

10/15/2009
- buxfix on assigning array elements inside templates
- parser bugfix on array access

10/15/2009
- allow bit operator '&' inside {if} tag
- implementation of ternary operator

10/13/2009
- do not recompile evaluated templates if reused just with other data
- recompile config files when config properties did change
- some lexer/parser otimizations

10/11/2009
- allow {block} tags inside included templates
- bugfix for resource plugins in Smarty2 format
- some optimizations of internal.template.php

10/11/2009
- fixed bug when template with same name is used with different data objects
- fixed bug with double quoted name attribute at {insert} tag
- reenabled assign_by_ref and append_by_ref methods

10/07/2009
- removed block nesting checks for {capture}

10/05/2009
- added support of "isinstance" to {if} tag

10/03/2009
- internal changes to improve performance
- fix registering of filters for classes

10/01/2009
- removed default timezone setting
- reactivated PHP resource for simple PHP templates. Must set allow_php_templates = true to enable
- {PHP} tag can be enabled by allow_php_tag = true

09/30/2009
- fixed handling template_exits method for all resource types
- bugfix for other cache resources than file
- the methods assign_by_ref is now wrapped to assign, append_by_ref to append
- allow arrays of variables pass in display, fetch and createTemplate calls
  $data = array('foo'=>'bar','foo2'=>'blar');
  $smarty->display('my.tpl',$data);

09/29/2009
- changed {php} tag handling
- removed support of Smarty::instance()
- removed support of PHP resource type
- improved execution speed of {foreach} tags
- fixed bug in {section} tag

09/23/2009
- improvements and bugfix on {include} tag handling
NOTICE: existing compiled template and cache files must be deleted

09/19/2009
- replace internal "eval()" calls by "include" during rendering process
- speed improvment for templates which have included subtemplates
    the compiled code of included templates is merged into the compiled code of the parent template
- added logical operator "xor" for {if} tag
- changed parameter ordering for Smarty2 BC
    fetch($template, $cache_id = null, $compile_id = null, $parent = null)
    display($template, $cache_id = null, $compile_id = null, $parent = null)
    createTemplate($template, $cache_id = null, $compile_id = null, $parent = null)
- property resource_char_set is now replaced by constant SMARTY_RESOURCE_CHAR_SET
- fixed handling of classes in registered blocks
- speed improvement of lexer on text sections

09/01/2009
- dropped nl2br as plugin
- added '<>' as comparission operator in {if} tags
- cached caching_lifetime property to cache_liftime for backward compatibility with Smarty2.
  {include} optional attribute is also now cache_lifetime
- fixed trigger_error method (moved into Smarty class)
- version is now  Beta!!!


08/30/2009
- some speed optimizations on loading internal plugins


08/29/2009
- implemented caching of registered Resources
- new property 'auto_literal'. if true(default)  '{ ' and ' }' interpreted as literal, not as Smarty delimiter


08/28/2009
- Fix on line breaks inside {if} tags

08/26/2009
- implemented registered resources as in Smarty2. NOTE: caching does not work yet
- new property 'force_cache'. if true it forces the creation of a new cache file
- fixed modifiers on arrays
- some speed optimization on loading internal classes


08/24/2009
- fixed typo in lexer definition for '!==' operator
- bugfix - the ouput of plugins was not cached
- added global variable SCRIPT_NAME

08/21/2009
- fixed problems whitespace in conjuction with custom delimiters
- Smarty tags can now be used as value anywhere

08/18/2009
- definition of template class name moded in internal.templatebase.php
- whitespace parser changes

08/12/2009
- fixed parser problems

08/11/2009
- fixed parser problems with custom delimiter

08/10/2009
- update of mb support in plugins


08/09/2009
- fixed problems with doublequoted strings at name attribute of {block} tag
- bugfix at scope attribute of {append} tag

08/08/2009
- removed all internal calls of Smarty::instance()
- fixed code in double quoted strings

08/05/2009
- bugfix mb_string support
- bugfix of \n.\t etc in double quoted strings

07/29/2009
- added syntax for variable config vars  like  #$foo#

07/28/2009
- fixed parsing of $smarty.session vars containing objects

07/22/2009
- fix of "$" handling in double quoted strings

07/21/2009
- fix that {$smarty.current_dir} return correct value within {block} tags.

07/20/2009
- drop error message on unmatched {block} {/block} pairs

07/01/2009
- fixed smarty_function_html_options call in plugin function.html_select_date.php (missing ,)

06/24/2009
- fixed smarty_function_html_options call in plugin function.html_select_date.php

06/22/2009
- fix on \n and spaces inside smarty tags
- removed request_use_auto_globals propert as it is no longer needed because Smarty 3 will always run under PHP 5


06/18/2009
- fixed compilation of block plugins when caching enabled
- added $smarty.current_dir  which returns the current working directory

06/14/2009
- fixed array access on super globals
- allow smarty tags within xml tags

06/13/2009
- bugfix at extend resource: create unique files for compiled template and cache for each combination of template files
- update extend resource to handle appen and prepend block attributes
- instantiate classes of plugins instead of calling them static

06/03/2009
- fixed repeat at block plugins

05/25/2009
- fixed problem with caching of compiler plugins

05/14/2009
- fixed directory separator handling

05/09/2009
- syntax change for stream variables
- fixed bug when using absolute template filepath and caching

05/08/2009
- fixed bug of {nocache}  tag in included templates

05/06/2009
- allow that plugins_dir folder names can end without directory separator

05/05/2009
- fixed E_STRICT incompabilities
- {function} tag bug fix
- security policy definitions have been moved from plugins folder to file Security.class.php in libs folder
- added allow_super_global configuration to security

04/30/2009
- functions defined with the {function} tag now always have global scope

04/29/2009
- fixed problem with directory setter methods
- allow that cache_dir can end without directory separator

04/28/2009
- the {function} tag can no longer overwrite standard smarty tags
- inherit functions defined by the {fuction} tag into subtemplates
- added {while <statement>} sytax to while tag

04/26/2009
- added trusted stream checking to security
- internal changes at file dependency check for caching

04/24/2009
- changed name of {template} tag to {function}
- added new {template} tag

04/23/2009
- fixed access of special smarty variables from included template

04/22/2009
- unified template stream syntax with standard Smarty resource syntax  $smarty->display('mystream:mytemplate')

04/21/2009
- change of new style syntax for forach. Now:  {foreach $array as $var}  like in PHP

04/20/2009
- fixed "$foo.bar  ..." variable replacement in double quoted strings
- fixed error in {include} tag with variable file attribute

04/18/2009
- added stream resources  ($smarty->display('mystream://mytemplate'))
- added stream variables  {$mystream:myvar}

04/14/2009
- fixed compile_id handling on {include} tags
- fixed append/prepend attributes in {block} tag
- added  {if  'expression' is in 'array'}  syntax
- use crc32 as hash for compiled config files.

04/13/2009
- fixed scope problem with parent variables when appending variables within templates.
- fixed code for {block} without childs (possible sources for notice errors removed)

04/12/2009
- added append and prepend attribute to {block} tag

04/11/2009
- fixed variables in 'file' attribute of {extend} tag
- fixed problems in modifiers (if mb string functions not present)

04/10/2009
- check if mb string functions available otherwise fallback to normal string functions
- added global variable scope SMARTY_GLOBAL_SCOPE
- enable 'variable' filter by default
- fixed {$smarty.block.parent.foo}
- implementation of a 'variable' filter as replacement for default modifier

04/09/2009
- fixed execution of filters defined by classes
- compile the always the content of {block} tags to make shure that the filters are running over it
- syntax corrections on variable object property
- syntax corrections on array access in dot syntax

04/08/2009
- allow variable object property

04/07/2009
- changed variable scopes to SMARTY_LOCAL_SCOPE, SMARTY_PARENT_SCOPE, SMARTY_ROOT_SCOPE to avoid possible conflicts with user constants
- Smarty variable global attribute replaced with scope attribute

04/06/2009
- variable scopes LOCAL_SCOPE, PARENT_SCOPE, ROOT_SCOPE
- more getter/setter methods

04/05/2009
- replaced new array looping syntax {for $foo in $array} with {foreach $foo in $array} to avoid confusion
- added append array for short form of assign  {$foo[]='bar'} and allow assignments to nested arrays {$foo['bla']['blue']='bar'}

04/04/2009
- make output of template default handlers cachable and save compiled source
- some fixes on yesterdays update

04/03/2006
- added registerDefaultTemplateHandler method and functionallity
- added registerDefaultPluginHandler method and functionallity
- added {append} tag to extend Smarty array variabled

04/02/2009
- added setter/getter methods
- added $foo@first and $foo@last properties at {for} tag
- added $set_timezone (true/false) property to setup optionally the default time zone

03/31/2009
- bugfix smarty.class and internal.security_handler
- added compile_check configuration
- added setter/getter methods

03/30/2009
- added all major setter/getter methods

03/28/2009
- {block} tags can be nested now
- md5 hash function replace with crc32 for speed optimization
- file order for exted resource inverted
- clear_compiled_tpl and clear_cache_all will not touch .svn folder any longer

03/27/2009
- added extend resource

03/26/2009
- fixed parser not to create error on `word` in double quoted strings
- allow PHP  array(...)
- implemented  $smarty.block.name.parent to access parent block content
- fixed smarty.class


03/23/2009
- fixed {foreachelse} and {forelse} tags

03/22/2009
- fixed possible sources for notice errors
- rearrange SVN into distribution and development folders

03/21/2009
- fixed exceptions in function plugins
- fixed notice error in Smarty.class.php
- allow chained objects to span multiple lines
- fixed error in modifiers

03/20/2009
- moved /plugins folder into /libs folder
- added noprint modifier
- autoappend a directory separator if the xxxxx_dir definition have no trailing one

03/19/2009
- allow array definition as modifier parameter
- changed modifier to use multi byte string funktions.

03/17/2009
- bugfix

03/15/2009
- added {include_php} tag for BC
- removed @ error suppression
- bugfix fetch did always repeat output of first call when calling same template several times
- PHPunit tests extended

03/13/2009
- changed block syntax to be Smarty like  {block:titel} -> {block name=titel}
- compiling of {block} and {extend} tags rewriten for better performance
- added special Smarty variable block  ($smarty.block.foo} returns the parent definition of block foo
- optimization of {block} tag compiled code.
- fixed problem with escaped double quotes in double quoted strings

03/12/2009
- added support of template inheritance by {extend } and {block } tags.
- bugfix comments within literals
- added scope attribuie to {include} tag

03/10/2009
- couple of bugfixes and improvements
- PHPunit tests extended

03/09/2009
- added support for global template vars.  {assign_global...}  $smarty->assign_global(...)
- added direct_access_security
- PHPunit tests extended
- added missing {if} tag conditions like "is div by" etc.

03/08/2009
- splitted up the Compiler class to make it easier to use a coustom compiler
- made default plugins_dir relative to Smarty root and not current working directory
- some changes to make the lexer parser better configurable
- implemented {section} tag for Smarty2 BC

03/07/2009
- fixed problem with comment tags
- fixed problem with #xxxx in double quoted string
- new {while} tag implemented
- made lexer and paser class configurable as $smarty property
- Smarty method get_template_vars implemented
- Smarty method get_registered_object implemented
- Smarty method trigger_error implemented
- PHPunit tests extended

03/06/2009
- final changes on config variable handling
- parser change - unquoted strings will by be converted into single quoted strings
- PHPunit tests extended
- some code cleanup
- fixed problem on catenate strings with expression
- update of count_words modifier
- bugfix on comment tags


03/05/2009
- bugfix on <?xml...> tag with caching enabled
- changes on exception handling (by Monte)

03/04/2009
- added support for config variables
- bugfix on <?xml...> tag

03/02/2009
- fixed unqouted strings within modifier parameter
- bugfix parsing of mofifier parameter

03/01/2009
- modifier chaining works now as in Smarty2

02/28/2009
- changed handling of unqouted strings

02/26/2009
- bugfix
- changed $smarty.capture.foo to be global for Smarty2 BC.

02/24/2009
- bugfix {php} {/php} tags for backward compatibility
- bugfix for expressions on arrays
- fixed usage of "null" value
- added $smarty.foreach.foo.first and $smarty.foreach.foo.last

02/06/2009
- bugfix for request variables without index  for example $smarty.get
- experimental solution for variable functions in static class

02/05/2009
- update of popup plugin
- added config variables to template parser (load config functions still missing)
- parser bugfix for empty quoted strings

02/03/2009
- allow array of objects as static class variabales.
- use htmlentities at source output at template errors.

02/02/2009
- changed search order on modifiers to look at plugins folder first
- parser bug fix for modifier on array elements  $foo.bar|modifier
- parser bug fix on single quoted srings
- internal: splitted up compiler plugin files

02/01/2009
- allow method chaining on static classes
- special Smarty variables  $smarty.... implemented
- added {PHP} {/PHP} tags for backward compatibility

01/31/2009
- added {math} plugin for Smarty2 BC
- added template_exists method
- changed Smarty3 method enable_security() to enableSecurity() to follow camelCase standards

01/30/2009
- bugfix in single quoted strings
- changed syntax for variable property access from $foo:property to $foo@property because of ambiguous syntax at modifiers

01/29/2009
- syntax for array definition changed from (1,2,3) to [1,2,3] to remove ambiguous syntax
- allow  {for $foo in [1,2,3]} syntax
- bugfix in double quoted strings
- allow <?xml...?> tags in template even if short_tags are enabled

01/28/2009
- fixed '!==' if condition.

01/28/2009
- added support of {strip} {/strip} tag.

01/27/2009
- bug fix on backticks in double quoted strings at objects

01/25/2009
- Smarty2 modfiers added to SVN

01/25/2009
- bugfix allow arrays at object properties in Smarty syntax
- the template object is now passed as additional parameter at plugin calls
- clear_compiled_tpl method completed

01/20/2009
- access to class constants implemented  ( class::CONSTANT )
- access to static class variables implemented ( class::$variable )
- call of static class methods implemented ( class::method() )

01/16/2009
- reallow leading _ in variable names  {$_var}
- allow array of objects  {$array.index->method()} syntax
- finished work on clear_cache and clear_cache_all methods

01/11/2009
- added support of {literal} tag
- added support of {ldelim} and {rdelim} tags
- make code compatible to run with E_STRICT error setting

01/08/2009
- moved clear_assign and clear_all_assign to internal.templatebase.php
- added assign_by_ref, append and append_by_ref methods

01/02/2009
- added load_filter method
- fished work on filter handling
- optimization of plugin loading

12/30/2008
- added compiler support of registered object
- added backtick support in doubled quoted strings for backward compatibility
- some minor bug fixes and improvments

12/23/2008
- fixed problem of not working "not" operator in if-expressions
- added handling of compiler function plugins
- finished work on (un)register_compiler_function method
- finished work on (un)register_modifier method
- plugin handling from plugins folder changed for modifier plugins
  deleted - internal.modifier.php
- added modifier chaining to parser

12/17/2008
- finished (un)register_function method
- finished (un)register_block method
- added security checking for PHP functions in PHP templates
- plugin handling from plugins folder rewritten
  new - internal.plugin_handler.php
  deleted - internal.block.php
  deleted - internal.function.php
- removed plugin checking from security handler

12/16/2008

- new start of this change_log file
