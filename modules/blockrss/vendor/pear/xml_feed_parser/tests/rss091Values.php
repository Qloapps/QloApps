<?php

require_once 'XML_Feed_Parser_TestCase.php';

class rss091Values extends XML_Feed_Parser_TestCase
{
    function setUp()
    {
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . "rss091-complete.xml");
        $this->feed = new XML_Feed_Parser($this->file, false, true);
        $this->entry = $this->feed->getEntryByOffset(0);
    }

    function test_feedRights()
    {
        $value = "Copyright 1997-1999 UserLand Software, Inc.";
        $this->assertEquals($value, $this->feed->rights);
        $this->assertEquals($value, $this->feed->copyright);
    }

    function test_feedNumberItems()
    {
        $value = 1;
        $this->assertEquals($value, $this->feed->numberEntries);
    }

    function test_feedTitle()
    {
        $value = "Scripting News";
        $this->assertEquals($value, $this->feed->title);
    }
    
    function test_feedLink()
    {
        $value = "http://www.scripting.com/";
        $this->assertEquals($value, $this->feed->link);
    }
    
    function test_feedImage()
    {
        $value = array(
            "title" => "Scripting News",
            "link" => "http://www.scripting.com/",
            "url" => "http://www.scripting.com/gifs/tinyScriptingNews.gif",
            "description" => "What is this used for?",
            'height' => "40",
            'width' => "78");
        $this->assertEquals($value, $this->feed->image);
    }

    function test_feedDescription()
    {
        $value = "News and commentary from the cross-platform scripting community.";
        $this->assertEquals($value, $this->feed->description);
    }
    
    function test_feedSubtitleEquivalence()
    {
        $value = "News and commentary from the cross-platform scripting community.";
        $this->assertEquals($value, $this->feed->subtitle);
    }
    
    function test_feedDate()
    {
        $value = strtotime("Thu, 08 Jul 1999 07:00:00 GMT");
        $this->assertEquals($value, $this->feed->date);
    }
    
    function test_feedLastBuildDate()
    {
        $value = strtotime("Thu, 08 Jul 1999 16:20:26 GMT");
        $this->assertEquals($value, $this->feed->lastBuildDate);
    }
    
    function test_feedUpdatedEquivalence()
    {
        $value = strtotime("Thu, 08 Jul 1999 16:20:26 GMT");
        $this->assertEquals($value, $this->feed->updated);
    }
    
    function test_feedLanguage()
    {
        $value = "en-us";
        $this->assertEquals($value, $this->feed->language);
    }
    
    function test_feedSkipHours()
    {
        $value = array("6", "7", "8", "9", "10", "11");
        $this->assertEquals($value, $this->feed->skipHours);
    }

    function test_feedSkipDays()
    {
        $value = array("Sunday");
        $this->assertEquals($value, $this->feed->skipDays);
    }

    function test_feedDocs()
    {
        $value = "http://my.userland.com/stories/storyReader$11";
        $this->assertEquals($value, $this->feed->docs);
    }
    
    function test_feedManagingEditor()
    {
        $value = "dave@userland.com (Dave Winer)";
        $this->assertEquals($value, $this->feed->managingEditor);
    }
    
    function test_feedAuthorEquivalence()
    {
        $value = "dave@userland.com (Dave Winer)";
        $this->assertEquals($value, $this->feed->author);
    }
    
    function test_feedWebmaster()
    {
        $value = "dave@userland.com (Dave Winer)";
        $this->assertEquals($value, $this->feed->webMaster);
    }
    
    function test_entryTitle()
    {
        $value = "stuff";
        $this->assertEquals($value, $this->entry->title);
    }
    
    function test_entryLink()
    {
        $value = "http://bar";
        $this->assertEquals($value, $this->entry->link);
    }
    
    function test_entryDescription()
    {
        $value = "This is an article about some stuff";
        $this->assertEquals($value, $this->entry->description);
    }
}

?>