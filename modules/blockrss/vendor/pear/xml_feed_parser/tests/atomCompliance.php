<?php

require_once 'XML_Feed_Parser_TestCase.php';

/**
 * This test is to make sure that we get sane values back for all
 * elements specified by the atom specification. It is okay for a feed
 * to not have some of these, but if they're not present we should
 * get a null or false return rather than an error. This test begins
 * to ensure consistency of our API.
 */
class atomCompliance extends XML_Feed_Parser_TestCase
{
    
    function setUp()
    {
        $this->sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->files = array('atom10-example1.xml', 'atom10-example2.xml', 'rss10-example1.xml', 
          'rss10-example2.xml', 'rss2sample.xml', 'delicious.feed', 'technorati.feed', 'grwifi-atom.xml');
    }
    
    function checkString($attribute, $element) {
        $item = $element->$attribute;            
        $test = (is_string($item) or $item === false);
        return $test;
    }

    function checkNumeric($attribute, $element) {
        $item = $element->$attribute;            
        $test = (is_numeric($item) or $item === false);
        return $test;
    }

    function test_feedAuthor() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $this->assertTrue($this->checkString('author', $feed), "Failed for $file");
      }
    }

    function test_feedContributor()
    {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $this->assertTrue($this->checkString('contributor', $feed), "Failed for $file");
      }
    }

    function test_feedIcon() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $this->assertTrue($this->checkString('icon', $feed), "Failed for $file");
      }
    }
    
    function test_feedId() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $this->assertTrue($this->checkString('id', $feed), "Failed for $file");
      }
    }
    
    function test_feedRights() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $this->assertTrue($this->checkString('rights', $feed), "Failed for $file");
      }
    }
    
    function test_feedTitle() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $this->assertTrue($this->checkString('title', $feed), "Failed for $file");
      }
    }
    
    function test_feedSubtitle() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $this->assertTrue($this->checkString('subtitle', $feed), "Failed for $file");
      }
    }
    
    function test_feedUpdated() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $this->assertTrue($this->checkNumeric('updated', $feed), "Failed for $file");
      }
    }
    
    function test_feedLink() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $this->assertTrue($this->checkString('link', $feed), "Failed for $file");
      }
    }
    
    function test_entryAuthor() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $entry = $feed->getEntryByOffset(0);
        $this->assertTrue($this->checkString('author', $entry), "Failed for $file");
      }
    }

    function test_entryContributor()
    {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $entry = $feed->getEntryByOffset(0);
        $this->assertTrue($this->checkString('contributor', $entry), "Failed for $file");
      }
    }

    function test_entryId() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $entry = $feed->getEntryByOffset(0);
        $this->assertTrue($this->checkString('id', $entry), "Failed for $file");
      }
    }
    
    function test_entryPublished() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $entry = $feed->getEntryByOffset(0);
        $this->assertTrue($this->checkNumeric('published', $entry), "Failed for $file");
      }
    }
    
    function testEntryTitle() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $entry = $feed->getEntryByOffset(0);
        $this->assertTrue($this->checkString('title', $entry), "Failed for $file");
      }
    }
    
    function testEntryRights() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $entry = $feed->getEntryByOffset(0);
        $this->assertTrue($this->checkString('rights', $entry), "Failed for $file");
      }
    }
    
    function testEntrySummary() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $entry = $feed->getEntryByOffset(0);
        $this->assertTrue($this->checkString('summary', $entry), "Failed for $file");
      }
    }
    
    function testEntryContent() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $entry = $feed->getEntryByOffset(0);
        $this->assertTrue($this->checkString('content', $entry), "Failed for $file");
      }
    }
    
    function testEntryLink() {
      foreach ($this->files as $file) {
        $contents = file_get_contents($this->sample_dir . DIRECTORY_SEPARATOR . $file);
        $feed = new XML_Feed_Parser($contents);
        $entry = $feed->getEntryByOffset(0);
        $this->assertTrue($this->checkString('link', $entry), "Failed for $file");
      }
    }
}

