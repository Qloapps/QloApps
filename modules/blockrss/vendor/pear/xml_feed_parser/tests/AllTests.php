<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'XML_Feed_Parser_AllTests::main');
}

require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'XML_Feed_Parser_TestCase.php';

require_once 'convertedtests/AllTests.php';

require_once 'accessTypes.php';
require_once 'atomValues.php';
require_once 'farsi.php';
require_once 'regressions.php';
require_once 'rss2Values.php';
require_once 'rss091Values.php';
require_once 'MalformedFeedTest.php';
require_once 'atomCompliance.php';
require_once 'iteration.php';
require_once 'rss092Values.php';
require_once 'xmlbase.php';
require_once 'atomEntryOnly.php';
require_once 'errors.php';
require_once 'japanese.php';
require_once 'rss1Values.php';

/**
 * AllTests suite testing XML_Feed_Parser
 *
 * @category  XML
 * @package   XML_Feed_Parser
 * @author    Daniel O'Connor <daniel.oconnor@gmail.com>
 * @copyright 2008 
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://pear.php.net/package/XML_Feed_Parser
 */
class XML_Feed_Parser_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('XML_Feed_Parser Tests');
        $suite->addTest(XML_Feed_Parser_Converted_AllTests::suite());

        $suite->addTestSuite('accessTypes');
        $suite->addTestSuite('atomValues');
        $suite->addTestSuite('farsi');
        $suite->addTestSuite('regressions');
        $suite->addTestSuite('rss2Values');
        $suite->addTestSuite('rss091Values');
        $suite->addTestSuite('MalformedFeedTest');
        $suite->addTestSuite('atomCompliance');
        $suite->addTestSuite('iteration');
        $suite->addTestSuite('rss092Values');
        $suite->addTestSuite('xmlbase');
        $suite->addTestSuite('atomEntryOnly');
        $suite->addTestSuite('errors');
        $suite->addTestSuite('japanese');
        $suite->addTestSuite('rss1Values');

        return $suite;
    }

    // }}}
}

if (PHPUnit_MAIN_METHOD == 'XML_Feed_Parser_AllTests::main') {
    XML_Feed_Parser_Converted_AllTests::main();
}

?>

