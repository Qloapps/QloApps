<?php

require_once 'XML_Feed_Parser_TestCase.php';

/**
 * This test is to make sure that we get sane values back for all
 * elements specified by the atom specification. It is okay for a feed
 * to not have some of these, but if they're not present we should
 * get a null or false return rather than an error. This test begins
 * to ensure consistency of our API.
 */
class accessTypes extends XML_Feed_Parser_TestCase
{
    function __construct()
    {
        $this->sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->files = array('atom10-example1.xml', 'grwifi-atom.xml', 'technorati.feed',
          'delicious.feed', 'rss2sample.xml', 'atom10-example2.xml', 'rss10-example1.xml', 
          'rss10-example2.xml');
    }

    function test_feedAuthor() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($feed->author, $feed->author());
        }
    }
    
    function test_feedIcon() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($feed->icon, $feed->icon());
        }
    }
    
    function test_feedId() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($feed->id, $feed->id());
        }
    }
    
    function test_feedRights() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($feed->rights, $feed->rights());
        }
    }
    
    function test_feedTitle() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($feed->title, $feed->title());
        }
    }
    
    function test_feedSubtitle() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($feed->subtitle, $feed->subtitle());
        }
    }
    
    function test_feedUpdated() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($feed->updated, $feed->updated());
        }
    }
    
    function test_feedLink() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($feed->link, $feed->link());
        }
    }
    
    function test_entryAuthor() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($entry->author, $entry->author());
        }
    }
    
    function test_entryId() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($entry->id, $entry->id());
        }
    }
    
    function test_entryPublished() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($entry->published, $entry->published());
        }
    }
    
    function testEntryTitle() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($entry->title, $entry->title());
        }
    }
    
    function testEntryRights() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($entry->rights, $entry->rights());
        }
    }
    
    function testEntrySummary() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($entry->summary, $entry->summary());
        }
    }
    
    function testEntryContent() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($entry->content, $entry->content());
        }
    }
    
    function testEntryLink() {
        foreach ($this->files as $file) {
          $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
          $feed = new XML_Feed_Parser($contents);
          $entry = $feed->getEntryByOffset(0);
          $this->assertEquals($entry->link, $entry->link());
        }
    }
}
