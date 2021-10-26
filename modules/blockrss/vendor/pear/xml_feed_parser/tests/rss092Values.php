<?php

require_once 'XML_Feed_Parser_TestCase.php';

class rss092Values extends XML_Feed_Parser_TestCase
{
    function setUp()
    {
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . "rss092-sample.xml");
        $this->feed = new XML_Feed_Parser($this->file, false, true);
        $this->entry = $this->feed->getEntryByOffset(0);
    }

    function test_feedNumberItems()
    {
        $value = 22;
        $this->assertEquals($value, $this->feed->numberEntries);
    }

    function test_feedTitle()
    {
        $value = "Dave Winer: Grateful Dead";
        $this->assertEquals($value, $this->feed->title);
    }
    
    function test_feedLink()
    {
        $value = "http://www.scripting.com/blog/categories/gratefulDead.html";
        $this->assertEquals($value, $this->feed->link);
    }

    function test_feedDescription()
    {
        $value = "A high-fidelity Grateful Dead song every day. This is where we're experimenting with enclosures on RSS news items that download when you're not using your computer. If it works (it will) it will be the end of the Click-And-Wait multimedia experience on the Internet. ";
        $this->assertEquals($value, $this->feed->description);
    }
    
    function test_feedSubtitleEquivalence()
    {
        $value = "A high-fidelity Grateful Dead song every day. This is where we're experimenting with enclosures on RSS news items that download when you're not using your computer. If it works (it will) it will be the end of the Click-And-Wait multimedia experience on the Internet. ";
        $this->assertEquals($value, $this->feed->subtitle);
    }
    
    function test_feedLastBuildDate()
    {
        $value = strtotime("Fri, 13 Apr 2001 19:23:02 GMT");
        $this->assertEquals($value, $this->feed->lastBuildDate);
    }
    
    function test_feedUpdatedEquivalence()
    {
        $value = strtotime("Fri, 13 Apr 2001 19:23:02 GMT");
        $this->assertEquals($value, $this->feed->updated);
    }
    
    function test_feedDocs()
    {
        $value = "http://backend.userland.com/rss092";
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
    
    function test_entryDescription()
    {
        $value = "It's been a few days since I added a song to the Grateful Dead channel. Now that there are all these new Radio users, many of whom are tuned into this channel (it's #16 on the hotlist of upstreaming Radio users, there's no way of knowing how many non-upstreaming users are subscribing, have to do something about this..). Anyway, tonight's song is a live version of Weather Report Suite from Dick's Picks Volume 7. It's wistful music. Of course a beautiful song, oft-quoted here on Scripting News. <i>A little change, the wind and rain.</i>";
        $this->assertEquals($value, trim($this->entry->description));
    }
    
    function test_entryEnclosure()
    {
        $value = array(
            'url' =>  'http://www.scripting.com/mp3s/weatherReportDicksPicsVol7.mp3',
            'length' => '6182912',
            'type' => 'audio/mpeg');
        $this->assertEquals($value, $this->entry->enclosure);
    }
}

?>