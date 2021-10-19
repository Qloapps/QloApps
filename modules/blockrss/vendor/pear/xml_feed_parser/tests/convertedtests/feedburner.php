<?php

require_once dirname(dirname(__FILE__)) . '/XML_Feed_Parser_TestCase.php';

class feedburner_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_feedburner_browserfriendly_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/feedburner/feedburner_browserfriendly.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('This is an XML content feed. It is intended to be viewed in a newsreader or syndicated to another site.', $feed->info);
    }
}

?>
