<?php

require_once dirname(dirname(__FILE__)) . '/XML_Feed_Parser_TestCase.php';

class atom_TestCase extends XML_Feed_Parser_Converted_TestCase {

    public function setUp() {
        parent::setUp();

        $this->markTestSkipped('The current behaviour of this package is to treat Atom 0.3 as Atom 1.0 and raise a warning.');
    }

    function test_atom_namespace_1_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/atom_namespace_1.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->title);
    }

    function test_atom_namespace_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/atom_namespace_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->title);
    }

    function test_atom_namespace_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/atom_namespace_3.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->title);
    }

    function test_atom_namespace_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/atom_namespace_4.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->title);
    }

    function test_atom_namespace_5_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/atom_namespace_5.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->title);
    }

    function test_entry_author_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_author_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->author(0, 'email'));
    }

    function test_entry_author_homepage_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_author_homepage.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->author(0, 'url'));
    }

    function test_entry_author_map_author_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_author_map_author.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author (me@example.com)', $feed->getEntryByOffset(0)->author);
    }

    function test_entry_author_map_author_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_author_map_author_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author', $feed->getEntryByOffset(0)->author);
    }

    function test_entry_author_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_author_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author', $feed->getEntryByOffset(0)->author(0, 'name'));
    }

    function test_entry_author_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_author_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->author(0, 'url'));
    }

    function test_entry_author_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_author_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->author(0, 'url'));
    }

    function test_entry_content_mode_base64_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_content_mode_base64.xml');

        $feed = new XML_Feed_Parser($content);

        //$this->assertEquals(, 1);
    }

    function test_entry_content_mode_escaped_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_content_mode_escaped.xml');

        $feed = new XML_Feed_Parser($content);

        //$this->assertEquals(, 1);
    }

    function test_entry_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->content(0, 'type'));
    }

    function test_entry_content_type_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_content_type_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->content(0, 'type'));
    }

    function test_entry_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_contributor_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_contributor_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->contributors(0, 'email'));
    }

    function test_entry_contributor_homepage_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_contributor_homepage.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->contributors(0, 'url'));
    }

    function test_entry_contributor_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_contributor_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(array('name' => 'Contributor 1', 'email' => 'me@example.com', 'href' => 'http://example.com/'), array('name' => 'Contributor 2', 'email' => 'you@example.com', 'href' => 'http://two.example.com/')), $feed->getEntryByOffset(0)->contributors);
    }

    function test_entry_contributor_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_contributor_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example contributor', $feed->getEntryByOffset(0)->contributors(0, 'name'));
    }

    function test_entry_contributor_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_contributor_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->contributors(0, 'url'));
    }

    function test_entry_contributor_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_contributor_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->contributors(0, 'url'));
    }

    function test_entry_id_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_id.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->id);
    }

    function test_entry_id_map_guid_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_id_map_guid.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->guid);
    }

    function test_entry_link_alternate_map_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_link_alternate_map_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_entry_link_alternate_map_link_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_link_alternate_map_link_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_entry_link_href_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_link_href.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->getEntryByOffset(0)->links(0, 'href'));
    }

    function test_entry_link_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_link_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(array('rel' => 'alternate', 'type' => 'application/xhtml+xml', 'href' => 'http://www.example.com/'), array('rel' => 'service.post', 'type' => 'application/atom+xml', 'href' => 'http://www.example.com/post')), $feed->getEntryByOffset(0)->links);
    }

    function test_entry_link_rel_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_link_rel.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('alternate', $feed->getEntryByOffset(0)->links(0, 'rel'));
    }

    function test_entry_link_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_link_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example title', $feed->getEntryByOffset(0)->links(0, 'title'));
    }

    function test_entry_link_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_link_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/html', $feed->getEntryByOffset(0)->links(0, 'type'));
    }

    function test_entry_summary_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_content_mode_base64_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary_content_mode_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_entry_summary_content_mode_escaped_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary_content_mode_escaped.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_entry_summary_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->summary(0, 'type'));
    }

    function test_entry_summary_content_type_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary_content_type_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->summary(0, 'type'));
    }

    function test_entry_summary_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_naked_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary_naked_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_summary_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_content_mode_base64_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_content_mode_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_entry_title_content_mode_escaped_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_content_mode_escaped.xml');

        $feed = new XML_Feed_Parser($content);

 //       $this->assertEquals(, 1);
    }

    function test_entry_title_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->title(0, 'type'));
    }

    function test_entry_title_content_type_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_content_type_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->title(0, 'type'));
    }

    function test_entry_title_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_naked_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_naked_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_text_plain_brackets_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/entry_title_text_plain_brackets.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('History of the <blink> tag', $feed->getEntryByOffset(0)->title);
    }

    function test_feed_author_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_author_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->author(0, 'email'));
    }

    function test_feed_author_homepage_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_author_homepage.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->author(0, 'url'));
    }

    function test_feed_author_map_author_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_author_map_author.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author (me@example.com)', $feed->author);
    }

    function test_feed_author_map_author_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_author_map_author_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author', $feed->author);
    }

    function test_feed_author_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_author_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author', $feed->author(0, 'name'));
    }

    function test_feed_author_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_author_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->author(0, 'url'));
    }

    function test_feed_author_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_author_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->author(0, 'url'));
    }

    function test_feed_contributor_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_contributor_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->contributors(0, 'email'));
    }

    function test_feed_contributor_homepage_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_contributor_homepage.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->contributors(0, 'url'));
    }

    function test_feed_contributor_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_contributor_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(array('name' => 'Contributor 1', 'email' => 'me@example.com', 'href' => 'http://example.com/'), array('name' => 'Contributor 2', 'email' => 'you@example.com', 'href' => 'http://two.example.com/')), $feed->contributors);
    }

    function test_feed_contributor_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_contributor_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example contributor', $feed->contributors(0, 'name'));
    }

    function test_feed_contributor_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_contributor_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->contributors(0, 'url'));
    }

    function test_feed_contributor_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_contributor_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->contributors(0, 'url'));
    }

    function test_feed_copyright_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->copyright);
    }

    function test_feed_copyright_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->copyright);
    }

    function test_feed_copyright_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->copyright);
    }

    function test_feed_copyright_content_mode_base64_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright_content_mode_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_feed_copyright_content_mode_escaped_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright_content_mode_escaped.xml');

        $feed = new XML_Feed_Parser($content);

 //       $this->assertEquals(, 1);
    }

    function test_feed_copyright_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->copyright(0, 'type'));
    }

    function test_feed_copyright_content_type_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright_content_type_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->copyright(0, 'type'));
    }

    function test_feed_copyright_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->copyright);
    }

    function test_feed_copyright_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->copyright);
    }

    function test_feed_copyright_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->copyright);
    }

    function test_feed_copyright_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->copyright);
    }

    function test_feed_copyright_naked_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright_naked_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->copyright);
    }

    function test_feed_copyright_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_copyright_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->copyright);
    }

    function test_feed_generator_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_generator.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example generator', $feed->generator);
    }

    function test_feed_generator_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_generator_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example generator', $feed->generator(0, 'name'));
    }

    function test_feed_generator_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_generator_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->generator(0, 'url'));
    }

    function test_feed_generator_version_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_generator_version.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2.65', $feed->generator(0, 'version'));
    }

    function test_feed_id_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_id.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->id);
    }

    function test_feed_id_map_guid_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_id_map_guid.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->guid);
    }

    function test_feed_info_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->info);
    }

    function test_feed_info_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->info);
    }

    function test_feed_info_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->info);
    }

    function test_feed_info_content_mode_base64_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info_content_mode_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_feed_info_content_mode_escaped_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info_content_mode_escaped.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_feed_info_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->info(0, 'type'));
    }

    function test_feed_info_content_type_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info_content_type_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->info(0, 'type'));
    }

    function test_feed_info_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->info);
    }

    function test_feed_info_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->info);
    }

    function test_feed_info_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->info);
    }

    function test_feed_info_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->info);
    }

    function test_feed_info_naked_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info_naked_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->info);
    }

    function test_feed_info_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_info_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->info);
    }

    function test_feed_link_alternate_map_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_link_alternate_map_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->link);
    }

    function test_feed_link_alternate_map_link_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_link_alternate_map_link_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->link);
    }

    function test_feed_link_href_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_link_href.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->links(0, 'href'));
    }

    function test_feed_link_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_link_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(array('rel' => 'alternate', 'type' => 'application/xhtml+xml', 'href' => 'http://www.example.com/'), array('rel' => 'service.post', 'type' => 'application/atom+xml', 'href' => 'http://www.example.com/post')), $feed->links);
    }

    function test_feed_link_rel_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_link_rel.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('alternate', $feed->links(0, 'rel'));
    }

    function test_feed_link_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_link_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example title', $feed->links(0, 'title'));
    }

    function test_feed_link_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_link_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/html', $feed->links(0, 'type'));
    }

    function test_feed_tagline_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->tagline);
    }

    function test_feed_tagline_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->tagline);
    }

    function test_feed_tagline_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->tagline);
    }

    function test_feed_tagline_content_mode_base64_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline_content_mode_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_feed_tagline_content_mode_escaped_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline_content_mode_escaped.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_feed_tagline_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->tagline(0, 'type'));
    }

    function test_feed_tagline_content_type_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline_content_type_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->tagline(0, 'type'));
    }

    function test_feed_tagline_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->tagline);
    }

    function test_feed_tagline_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->tagline);
    }

    function test_feed_tagline_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->tagline);
    }

    function test_feed_tagline_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->tagline);
    }

    function test_feed_tagline_naked_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline_naked_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->tagline);
    }

    function test_feed_tagline_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_tagline_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->tagline);
    }

    function test_feed_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->title);
    }

    function test_feed_title_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->title);
    }

    function test_feed_title_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->title);
    }

    function test_feed_title_content_mode_base64_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title_content_mode_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_feed_title_content_mode_escaped_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title_content_mode_escaped.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_feed_title_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->title(0, 'type'));
    }

    function test_feed_title_content_type_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title_content_type_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->title(0, 'type'));
    }

    function test_feed_title_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->title);
    }

    function test_feed_title_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->title);
    }

    function test_feed_title_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->title);
    }

    function test_feed_title_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->title);
    }

    function test_feed_title_naked_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title_naked_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->title);
    }

    function test_feed_title_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/feed_title_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->title);
    }

    function test_relative_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/relative_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <a href="http://example.com/test/test.html">test</a></div>', $feed->title);
    }

    function test_relative_uri_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/relative_uri_inherit.xml');

            $feed = new XML_Feed_Parser($content);
        
        $this->assertEquals('<div>Example <a href="http://example.com/test/test.html">test</a></div>', $feed->title);
    }

    function test_relative_uri_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom/relative_uri_inherit_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <a href="http://example.com/test/test.html">test</a></div>', $feed->title);
    }
}
?>
