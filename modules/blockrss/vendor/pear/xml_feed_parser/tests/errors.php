<?php

require_once 'XML_Feed_Parser_TestCase.php';

/**
 * This test is to make sure that we get errors when we should. In
 * particular we check that it throws an exception if we hand in an
 * illegal feed type.
 */
class errors extends XML_Feed_Parser_TestCase
{
    function test_fakeFeedType()
    {
        $file = "<myfeed><myitem /></myfeed>";
        try {
            $feed = new XML_Feed_Parser($file, false, true);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof XML_Feed_Parser_Exception);
        }
    }
    
    function test_badRSSVersion()
    {
        $file = "<?xml version=\"1.0\"?>
        <rss version=\"0.8\">
           <channel></channel></rss>";
       try {
           $feed = new XML_Feed_Parser($file, false, true);
       } catch (Exception $e) {
           $this->assertTrue($e instanceof XML_Feed_Parser_Exception);
       }
    }
    
    function test_emptyInput()
    {
        $file = null;
        try {
            $feed = new XML_Feed_Parser($file, false, true);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof XML_Feed_Parser_Exception);
        }
    }

    function test_nonXMLInput()
    {
        $file = "My string";
        try {
            $feed = new XML_Feed_Parser($file, false, true);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof XML_Feed_Parser_Exception);
        }
    }
    
    function test_missingElement() {
        $file = '<?xml version="1.0" encoding="utf-8"?>
        <rss version="2.0">
           <channel>
              <title>sample blog</title>
              <link>http://www.example.com/</link>
              <description>sample rss2 feed</description>
              <language>en</language>
              <copyright>Copyright 2006</copyright>
              <lastBuildDate>Tue, 25 Jul 2006 11:53:38 -0500</lastBuildDate>
              <generator>http://www.sixapart.com/movabletype/?v=3.31</generator>
              <docs>http://blogs.law.harvard.edu/tech/rss</docs> 
              <item>
                 <title>A sample entry</title>
                 <description>Sample content</description>
                 <link>http://www.example.com/archives/2006/07</link>
                 <guid>http://www.example.com/archives/2006/07</guid>
                 <category>Examples</category>
                 <pubDate>Tue, 25 Jul 2006 11:53:38 -0500</pubDate>
              </item>
            </channel></rss>';
          $feed = new XML_Feed_Parser($file, false, true);
          $entry = $feed->getEntryByOffset(0);
          $this->assertFalse($entry->enclosure());
    }
}

?>