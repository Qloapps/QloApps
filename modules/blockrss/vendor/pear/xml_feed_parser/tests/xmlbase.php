<?php

require_once 'XML_Feed_Parser_TestCase.php';

class xmlbase extends XML_Feed_Parser_TestCase
{
    function test_Base() {
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . "xmlbase.xml");
        try {
            $feed = new XML_Feed_Parser($file, false, true, true);    
        } catch (XML_Feed_Parser_Exception $e) {
            $this->assertTrue(false);
        }
        $entry = $feed->getEntryByOffset(0);
        $this->assertEquals($entry->link, 'http://www.tbray.org/ongoing/When/200x/2006/02/17/FSS');
    }
}

?>