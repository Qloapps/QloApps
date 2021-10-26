<?php

require_once 'XML_Feed_Parser_TestCase.php';

class atomValues extends XML_Feed_Parser_TestCase
{
    function setUp()
    {
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . 'atom10-example2.xml');
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(0);
    }

    function test_feedNumberItems()
    {
        $value = 1;
        $this->assertEquals($value, $this->feed->numberEntries);
    }

    function test_FeedTitle()
    {
        $value = 'dive into mark';
        $this->assertEquals($value, $this->feed->title);
    }
    
    function test_feedSubtitle()
    {
        $value = 'A <em>lot</em> of effort
  went into making this effortless';
        $content = trim($this->feed->subtitle);
        $content = preg_replace('/\t/', ' ', $content);
        $content = preg_replace('/(  )+/', ' ', $content);
        $this->assertEquals($content, $value);
    }
    
    function test_feedUpdated()
    {
        $value = strtotime('2005-07-31T12:29:29Z');
        $this->assertEquals($this->feed->updated, $value);
    }
    
    function test_feedId()
    {
        $value = 'tag:example.org,2003:3';
        $this->assertEquals($this->feed->id, $value);
    }
    
    function test_feedRights()
    {
        $value = 'Copyright (c) 2003, Mark Pilgrim';
        $this->assertEquals($this->feed->rights, $value);
    }
    
    function test_feedLinkPlain()
    {
        $value = 'http://example.org/';
        $this->assertEquals($this->feed->link, $value);
    }

    function test_feedLinkAttributes()
    {
        $value = 'self';
        $link = $this->feed->link(0, 'rel', array('type' => 'application/atom+xml'));
        $this->assertEquals($link, $value);
    }
    
    function test_feedGenerator()
    {
        $value = 'Example Toolkit';
        $this->assertEquals($value, trim($this->feed->generator));
    }
    
    function test_entryTitle()
    {
        $value = 'Atom draft-07 snapshot';
        $this->assertEquals($value, trim($this->entry->title));
    }
    
    function test_entryLink()
    {
        $value = 'http://example.org/2005/04/02/atom';
        $this->assertEquals($value, trim($this->entry->link));
    }
    
    function test_entryId()
    {
        $value = 'tag:example.org,2003:3.2397';
        $this->assertEquals($value, trim($this->entry->id));
    }
    function test_entryUpdated()
    {
        $value = strtotime('2005-07-31T12:29:29Z');
        $this->assertEquals($value, $this->entry->updated);
    }
    
    function test_entryPublished()
    {
        $value = strtotime('2003-12-13T08:29:29-04:00');
        $this->assertEquals($value, $this->entry->published);
    }
    
    function test_entryContent()
    {
        $value = '<p><i>[Update: The Atom draft is finished.]</i></p>';
        $content = trim($this->entry->content);
        $content = preg_replace('/\t/', ' ', $content);
        $content = preg_replace('/(  )+/', ' ', $content);
        $this->assertEquals($value, $content);
    }
    
    function test_entryAuthorURL()
    {
        $value = 'http://example.org/';
        $name = $this->entry->author(false, array('param' => 'uri'));
        $this->assertEquals($value, $name);
    }
    
    function test_entryAuthorName()
    {
        $value = 'Mark Pilgrim (f8dy@example.com)';
        $this->assertEquals($value, $this->entry->author);
    }
    
    function test_entryContributor()
    {
        $value = 'Sam Ruby';
        $this->assertEquals($value, $this->entry->contributor);
    }
    
    function test_entryContributorOffset()
    {
        $value = 'Joe Gregorio';
        $this->assertEquals($value, $this->entry->contributor(1));
    }
    
    # According to RFC4287 section 4.2.7.2:
    # [..]If the 'rel' attribute is not present, the link element MUST be
    # interpreted as if the link relation type is "alternate".
    function test_getsLinkWithoutRel()
    {
        $source = '<?xml version="1.0" ?>
        <entry xmlns="http://www.w3.org/2005/Atom">
        <link href="http://example.org/2005/04/02/atom" />
        </entry>
        ';
        $feed = new XML_Feed_Parser($source);
        $entry = $feed->getEntryByOffset(0);

        // Output
        $this->assertEquals( "http://example.org/2005/04/02/atom", 
            $entry->link(0, 'href', array('rel'=>'alternate')));
    }
    
    function test_htmlUnencoding() {
      $source = '<entry xmlns="http://www.w3.org/2005/Atom">
        ...
        <summary type="html">
          &lt;P&gt;The &amp;lt;EM&amp;gt; tag emphasizes the content.&lt;/P&gt;
        </summary>
      </entry>';

      $atom = new XML_Feed_Parser($source);
      $this->assertEquals("<P>The &lt;EM&gt; tag emphasizes the content.</P>",
        trim($atom->getEntryByOffset(0)->summary));
    }
    
    # According to RFC4287 section 4.2.7.2:
    # The value "alternate" signifies that the IRI in the value of the
    # href attribute identifies an alternate version of the resource
    # described by the containing element.
    function test_getAlternativeLinkForItem()
    {
        $source = '<?xml version="1.0" ?>
        <entry xmlns="http://www.w3.org/2005/Atom">
        <link rel="replies" href="http://example.org/2005/04/02/atom/comments" />
        <link rel="edit" href="http://example.org/2005/04/02/atom/edit"/>
        <link rel="self" href="http://example.org/2005/04/02/atom/self"/>
        <link rel="alternate" href="http://example.org/2005/04/02/atom" />
        </entry>
        ';
        $feed = new XML_Feed_Parser($source);
        $entry = $feed->getEntryByOffset(0);

        // Output
        $this->assertEquals( "http://example.org/2005/04/02/atom",
            $entry->link(0, 'href', array()));
    }

}

?>