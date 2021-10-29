<?php

require_once 'XML_Feed_Parser_TestCase.php';

class atomEntryOnly extends XML_Feed_Parser_TestCase
{
    function setUp()
    {
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $xml = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'atom10-entryonly.xml');
        $this->feed = new XML_Feed_Parser($xml);
        $this->entry = $this->feed->getEntryByOffset(0);
    }

    function test_Title()
    {
        $value = 'Atom draft-07 snapshot';
        $this->assertEquals($this->entry->title, $value);
    }

    function test_Id()
    {
        $value = 'tag:example.org,2003:3.2397';
        $this->assertEquals($this->entry->id, $value);
    }

    function test_Updated()
    {
        $value = strtotime('2005-07-10T12:29:29Z');
        $this->assertEquals($this->entry->updated, $value);
    }

    function test_Published()
    {
        $value = strtotime('2003-12-13T08:29:29-04:00');
        $this->assertEquals($this->entry->published, $value);
    }
    
    function test_AuthorURI()
    {
        $value = 'http://example.org/';
        $this->assertEquals($value, $this->entry->author(null, array('param' => 'uri')));
    }

    function test_Contributor()
    {
        $value = 'Sam Ruby';
        $this->assertEquals($this->entry->contributor, $value);
    }

    function test_Contributor2()
    {
        $value = 'Joe Gregorio';
        $this->assertEquals($this->entry->contributor(1), $value);
    }

    function test_Content()
    {
        $value = '<p><i>[Update: The Atom draft is finished.]</i></p>';
        $content = trim(preg_replace('/\t/', ' ', $this->entry->content));
        $content = preg_replace('/(\s\s)+/', ' ', $content);
        $this->assertEquals($value, $content);
    }

    function test_Link()
    {
        $value = 'http://example.org/2005/04/02/atom';
        $this->assertEquals($this->entry->link, $value);
    }
    
    function test_Enclosure()
    {
        $value = array (
           'url' => 'http://example.org/audio/ph34r_my_podcast.mp3',
           'type' => 'audio/mpeg',
           'length' => '1337');
        $this->assertEquals($this->entry->enclosure, $value);
    }
    
    
    function test_entryXPath()
    {
        $this->assertEquals('http://example.org/2005/04/02/atom', 
            $this->entry->link(0, 'href', array('rel'=>'alternate')));
    }
}

