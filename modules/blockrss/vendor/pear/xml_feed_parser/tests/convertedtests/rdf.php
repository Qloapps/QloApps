<?php

require_once dirname(dirname(__FILE__)) . '/XML_Feed_Parser_TestCase.php';

class rdf_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_rdf_channel_description_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_channel_description.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example description', $feed->description);
    }

    function test_rdf_channel_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_channel_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->link);
    }

    function test_rdf_channel_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_channel_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example feed', $feed->title);
    }

    function test_rdf_item_description_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_item_description.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example description', $feed->getEntryByOffset(0)->description);
    }

    function test_rdf_item_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_item_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/1', $feed->getEntryByOffset(0)->link);
    }

    function test_rdf_item_rdf_about_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_item_rdf_about.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.org/1', $feed->getEntryByOffset(0)->id);
    }

    function test_rdf_item_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rdf_item_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example title', $feed->getEntryByOffset(0)->title);
    }

    function test_rss090_channel_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rss090_channel_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example title', $feed->title);
    }

    function test_rss090_item_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rss090_item_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Item title', $feed->getEntryByOffset(0)->title);
    }

    function test_rss_version_10_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rss_version_10.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('RSS 1.0', $feed->version());
    }

    function test_rss_version_10_not_default_ns_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rdf/rss_version_10_not_default_ns.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('RSS 1.0', $feed->version());
    }
}
?>
