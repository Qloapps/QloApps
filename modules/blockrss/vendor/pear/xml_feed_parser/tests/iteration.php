<?php

require_once 'XML_Feed_Parser_TestCase.php';

class iteration extends XML_Feed_Parser_TestCase
{
    function setUp()
    {
        $this->sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
    }
    
    function tearDown() {
    }
    
    function test_Atom() {
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $feed = new XML_Feed_Parser(file_get_contents($this->sample_dir . "/grwifi-atom.xml"));
        $entries = array();
        foreach ($feed as $entry) {
            array_push($entries, $entry);
        }
        $this->assertNotSame($entries[0], $entries[1]);
    }

    function test_RSS1() {
        $feed = new XML_Feed_Parser(file_get_contents($this->sample_dir . "/delicious.feed"));
        $entries = array();
        foreach ($feed as $entry) {
            array_push($entries, $entry);
        }
        $this->assertNotSame($entries[0], $entries[1]);
    }
    
    function test_RSS2() {
        $feed = new XML_Feed_Parser(file_get_contents($this->sample_dir . "/rss2sample.xml"));
        $entries = array();
        foreach ($feed as $entry) {
            array_push($entries, $entry);
        }
        $this->assertNotSame($entries[0], $entries[1]);
    }
}

?>