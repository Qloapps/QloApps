<?php

require_once 'XML_Feed_Parser_TestCase.php';

class rss1Values extends XML_Feed_Parser_TestCase
{
    function setUp()
    {
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . "rss10-example2.xml");
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(0);
    }

    function test_feedNumberItems()
    {
        $value = 1;
        $this->assertEquals($value, $this->feed->numberEntries);
    }
    function test_feedTitle()
    {
        $value = "Meerkat";
        $this->assertEquals($value, $this->feed->title);
    }
    
    function test_feedLink()
    {
        $value = "http://meerkat.oreillynet.com";
        $this->assertEquals($value, $this->feed->link);
    }
    
    function test_feedDescription()
    {
        $value = "Meerkat: An Open Wire Service";
        $this->assertEquals($value, $this->feed->description);
    }
    
    function test_feedSubtitleEquivalence()
    {
        $value = "Meerkat: An Open Wire Service";
        $this->assertEquals($value, $this->feed->subtitle);
    }
    
    function test_feedPublisher()
    {
        $value = "The O'Reilly Network";
        $this->assertEquals($value, $this->feed->publisher);
    }
    
    function test_feedCreator()
    {
        $value = "Rael Dornfest (mailto:rael@oreilly.com)";
        $this->assertEquals($value, $this->feed->creator);
    }
    
    function test_feedAuthorEquivalence()
    {
        $value = "Rael Dornfest (mailto:rael@oreilly.com)"; 
        $this->assertEquals($value, $this->feed->author);
    }
    
    function test_feedRights()
    {
        $value = "Copyright &copy; 2000 O'Reilly &amp; Associates, Inc.";
        $this->assertEquals($value, htmlentities(utf8_decode($this->feed->rights)));
    }
    
    function test_feedDate()
    {
        $value = strtotime("2000-01-01T12:00+00:00");
        $this->assertEquals($value, $this->feed->date);
    }
    
    function test_feedUpdatedEquivalence()
    {
        $value = strtotime("2000-01-01T12:00+00:00");
        $this->assertEquals($value, $this->feed->updated);
    }
    
    function test_feedUpdatePeriod()
    {
        $value = 'hourly';
        $this->assertEquals($value, $this->feed->updatePeriod);
    }
    
    function test_feedUpdateFrequency()
    {
        $value = "2";
        $this->assertEquals($value, $this->feed->updateFrequency);
    }
    
    function test_feedUpdateBase()
    {
        $value = strtotime("2000-01-01T12:00+00:00");
        $this->assertEquals($value, $this->feed->updateBase);
    }
    
    function test_feedImage()
    {
        $value = array(
            'title' => false,
            'link' => false,
            'url' => "http://meerkat.oreillynet.com/icons/meerkat-powered.jpg",
            'description' => false,
            'height' => false,
            'width' => false);
        $this->assertEquals($value, $this->feed->image);
    }
    
    function test_entryTitle()
    {
        $value = "XML: A Disruptive Technology";
        $this->assertEquals($value, $this->entry->title);
    }
    
    function test_entryLink()
    {
        $value = "http://c.moreover.com/click/here.pl?r123";
        $this->assertEquals($value, $this->entry->link);
    }
    
    function test_entryDescription()
    {
        $value  = "XML is placing increasingly heavy loads on the existing technical infrastructure of the Internet.";
        $description = trim($this->entry->description);
        $description = preg_replace("/\t/", " ", $description);
        $description = preg_replace("/(\s\s)+/", " ", $description);
        $description = preg_replace("/(\s\s)+/", " ", $description);
        $this->assertEquals($value, $description);
    }
    
    function test_entryRights()
    {
        $value = "Copyright &copy; 2000 O'Reilly &amp; Associates, Inc.";
        $this->assertEquals($value, htmlentities(utf8_decode($this->feed->rights)));
    }
    
    function test_entryCreator()
    {
        $value = "Simon St.Laurent (mailto:simonstl@simonstl.com)";
        $this->assertEquals($value, $this->entry->creator);
    }
    
    function test_entryAuthorEquivalence()
    {
        $value = "Simon St.Laurent (mailto:simonstl@simonstl.com)";
        $this->assertEquals($value, $this->entry->author);
    }
    
    function test_entryPublisher()
    {
        $value = "The O'Reilly Network";
        $this->assertEquals($value, $this->entry->publisher);
    }
    
    function test_entryCategory()
    {
        $value = "XML";
        $this->assertEquals($value, $this->entry->category);
    }
    
    function test_entryIdEquivalence()
    {
        $value = "http://c.moreover.com/click/here.pl?r123";
        $this->assertEquals($value, $this->entry->id);
    }
    
    function test_feedTextInput()
    {
        $value = array(
            'title' => null,
             'description' => null,
             'name' => null,
             'link' => "http://meerkat.oreillynet.com");
        $this->assertEquals($value, $this->feed->textinput);
    }
}

?>
