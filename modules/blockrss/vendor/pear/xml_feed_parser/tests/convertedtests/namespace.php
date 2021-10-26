<?php

require_once dirname(dirname(__FILE__)) . '/XML_Feed_Parser_TestCase.php';

class namespace_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_rss1_0withModules_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/namespace/rss1.0withModules.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Test Item - RSS 1.0', $feed->getEntryByOffset(0)->title);
    }

    function test_rss1_0withModulesNoDefNS_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/namespace/rss1.0withModulesNoDefNS.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Test Item - RSS 1.0 no Default NS', $feed->getEntryByOffset(0)->title);
    }

    function test_rss1_0withModulesNoDefNSLocalNameClash_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/namespace/rss1.0withModulesNoDefNSLocalNameClash.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('correct description', $feed->getEntryByOffset(0)->description);
    }

    function test_rss2_0noNSwithModules_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/namespace/rss2.0noNSwithModules.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Test Item - RSS 2.0 no NS', $feed->getEntryByOffset(0)->title);
    }

    function test_rss2_0noNSwithModulesLocalNameClash_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/namespace/rss2.0noNSwithModulesLocalNameClash.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('correct description', $feed->getEntryByOffset(0)->description);
    }

    function test_rss2_0NSwithModules_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/namespace/rss2.0NSwithModules.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Test Item - RSS2.0 w/ NS', $feed->getEntryByOffset(0)->title);
    }

    function test_rss2_0NSwithModulesNoDefNS_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/namespace/rss2.0NSwithModulesNoDefNS.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Test Item - - RSS2.0 w/ NS no default NS', $feed->getEntryByOffset(0)->title);
    }

    function test_rss2_0NSwithModulesNoDefNSLocalNameClash_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/namespace/rss2.0NSwithModulesNoDefNSLocalNameClash.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('correct description', $feed->getEntryByOffset(0)->description);
    }
}
?>
