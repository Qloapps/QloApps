<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'XML_Feed_Parser_Converted_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once dirname(dirname(__FILE__)) . '/XML_Feed_Parser_TestCase.php';

require_once 'atom10.php';
require_once 'base.php';
require_once 'encoding.php';
require_once 'http.php';
require_once 'lang.php';
require_once 'rdf.php';
require_once 'sanitize.php';
require_once 'amp.php';
require_once 'atom.php';
require_once 'date.php';
require_once 'feedburner.php';
require_once 'itunes.php';
require_once 'namespace.php';
require_once 'rss.php';

/**
 * AllTests suite testing XML_Feed_Parser; running
 * unit tests from universal feed parser
 *
 * @category  XML
 * @package   XML_Feed_Parser
 * @author    Daniel O'Connor <daniel.oconnor@gmail.com>
 * @copyright 2008 
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/XML_Feed_Parser
 */
class XML_Feed_Parser_Converted_AllTests
{
    // {{{ main()

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    // }}}
    // {{{ suite()

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('XML_Feed_Parser Converted Tests');
        $suite->addTestSuite('atom10_TestCase');
        $suite->addTestSuite('base_TestCase');
        $suite->addTestSuite('encoding_TestCase');
        $suite->addTestSuite('http_TestCase');
        $suite->addTestSuite('lang_TestCase');
        $suite->addTestSuite('rdf_TestCase');
        $suite->addTestSuite('sanitize_TestCase');
        $suite->addTestSuite('amp_TestCase');
        $suite->addTestSuite('atom_TestCase');
        $suite->addTestSuite('date_TestCase');
        $suite->addTestSuite('feedburner_TestCase');
        $suite->addTestSuite('itunes_TestCase');
        $suite->addTestSuite('namespace_TestCase');
        $suite->addTestSuite('rss_TestCase');

        return $suite;
    }

    // }}}
}

if (PHPUnit_MAIN_METHOD == 'XML_Feed_Parser_Converted_AllTests::main') {
    XML_Feed_Parser_Converted_AllTests::main();
}

?>

