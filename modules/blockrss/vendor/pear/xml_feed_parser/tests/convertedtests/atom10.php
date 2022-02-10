<?php

require_once dirname(dirname(__FILE__)) . '/XML_Feed_Parser_TestCase.php';

class atom10_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_atom10_namespace_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/atom10_namespace.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->title);
    }

    function test_atom10_version_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/atom10_version.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertVersionMostlyCorrect('atom10', $feed->version());
    }

    function test_entry_author_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_author_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->author(0, 'email'));
    }

    function test_entry_author_map_author_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_author_map_author.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author (me@example.com)', $feed->getEntryByOffset(0)->author);
    }

    function test_entry_author_map_author_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_author_map_author_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author', $feed->getEntryByOffset(0)->author);
    }

    function test_entry_author_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_author_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author', $feed->getEntryByOffset(0)->author(0, 'name'));
    }

    function test_entry_author_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_author_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->author(0, 'url'));
    }

    function test_entry_author_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_author_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->author(0, 'url'));
    }

    function test_entry_category_label_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_category_label.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Atom 1.0 tests', $feed->getEntryByOffset(0)->tags(0, 'label'));
    }

    function test_entry_category_scheme_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_category_scheme.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://feedparser.org/tests/', $feed->getEntryByOffset(0)->tags(0, 'scheme'));
    }

    function test_entry_category_term_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_category_term.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('atom10', $feed->getEntryByOffset(0)->tags(0, 'term'));
    }

    function test_entry_content_application_xml_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_content_application_xml.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_content_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_content_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_content_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_content_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_content_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_src_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_content_src.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/movie.mp4', $feed->getEntryByOffset(0)->content(0, 'src'));
    }

    function test_entry_content_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_content_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_text_plain_brackets_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_content_text_plain_brackets.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('History of the <blink> tag', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->content(0, 'type'));
    }

    function test_entry_content_type_text_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_content_type_text.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->content(0, 'type'));
    }

    function test_entry_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_entry_contributor_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_contributor_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->contributors(0, 'email'));
    }

    function test_entry_contributor_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_contributor_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(array('name' => 'Contributor 1', 'email' => 'me@example.com', 'href' => 'http://example.com/'), array('name' => 'Contributor 2', 'email' => 'you@example.com', 'href' => 'http://two.example.com/')), $feed->getEntryByOffset(0)->contributors);
    }

    function test_entry_contributor_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_contributor_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example contributor', $feed->getEntryByOffset(0)->contributors(0, 'name'));
    }

    function test_entry_contributor_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_contributor_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->contributors(0, 'url'));
    }

    function test_entry_contributor_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_contributor_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->contributors(0, 'url'));
    }

    function test_entry_id_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_id.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->id);
    }

    function test_entry_id_map_guid_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_id_map_guid.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->guid);
    }

    function test_entry_id_no_normalization_1_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_id_no_normalization_1.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.org/thing', $feed->getEntryByOffset(0)->id);
    }

    function test_entry_id_no_normalization_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_id_no_normalization_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.org/Thing', $feed->getEntryByOffset(0)->id);
    }

    function test_entry_id_no_normalization_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_id_no_normalization_3.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.EXAMPLE.org/thing', $feed->getEntryByOffset(0)->id);
    }

    function test_entry_id_no_normalization_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_id_no_normalization_4.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('HTTP://www.example.org/thing', $feed->getEntryByOffset(0)->id);
    }

    function test_entry_id_no_normalization_5_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_id_no_normalization_5.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/~bob', $feed->getEntryByOffset(0)->id);
    }

    function test_entry_id_no_normalization_6_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_id_no_normalization_6.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/%7ebob', $feed->getEntryByOffset(0)->id);
    }

    function test_entry_id_no_normalization_7_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_id_no_normalization_7.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/%7Ebob', $feed->getEntryByOffset(0)->id);
    }

    function test_entry_link_alternate_map_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_alternate_map_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_entry_link_alternate_map_link_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_alternate_map_link_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_entry_link_alternate_map_link_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_alternate_map_link_3.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/alternate', $feed->getEntryByOffset(0)->link);
    }

    function test_entry_link_href_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_href.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->getEntryByOffset(0)->links(0, 'href'));
    }

    function test_entry_link_hreflang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_hreflang.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->links(0, 'hreflang'));
    }

    function test_entry_link_length_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_length.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('42301', $feed->getEntryByOffset(0)->links(0, 'length'));
    }

    function test_entry_link_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(array('rel' => 'alternate', 'type' => 'application/xhtml+xml', 'href' => 'http://www.example.com/'), array('rel' => 'service.post', 'type' => 'application/atom+xml', 'href' => 'http://www.example.com/post')), $feed->getEntryByOffset(0)->links);
    }

    function test_entry_link_no_rel_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_no_rel.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('alternate', $feed->getEntryByOffset(0)->links(0, 'rel'));
    }

    function test_entry_link_rel_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_rel.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('alternate', $feed->getEntryByOffset(0)->links(0, 'rel'));
    }

    function test_entry_link_rel_enclosure_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_rel_enclosure.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('enclosure', $feed->getEntryByOffset(0)->links(0, 'rel'));
    }

    function test_entry_link_rel_enclosure_map_enclosure_length_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_rel_enclosure_map_enclosure_length.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('42301', $feed->getEntryByOffset(0)->enclosures(0, 'length'));
    }

    function test_entry_link_rel_enclosure_map_enclosure_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_rel_enclosure_map_enclosure_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('video/mpeg4', $feed->getEntryByOffset(0)->enclosures(0, 'type'));
    }

    function test_entry_link_rel_enclosure_map_enclosure_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_rel_enclosure_map_enclosure_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/movie.mp4', $feed->getEntryByOffset(0)->enclosures(0, 'href'));
    }

    function test_entry_link_rel_other_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_rel_other.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://feedparser.org/rel/test', $feed->getEntryByOffset(0)->links(0, 'rel'));
    }

    function test_entry_link_rel_related_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_rel_related.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('related', $feed->getEntryByOffset(0)->links(0, 'rel'));
    }

    function test_entry_link_rel_self_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_rel_self.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('self', $feed->getEntryByOffset(0)->links(0, 'rel'));
    }

    function test_entry_link_rel_via_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_rel_via.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('via', $feed->getEntryByOffset(0)->links(0, 'rel'));
    }

    function test_entry_link_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example title', $feed->getEntryByOffset(0)->links(0, 'title'));
    }

    function test_entry_link_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_link_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/html', $feed->getEntryByOffset(0)->links(0, 'type'));
    }

    function test_entry_rights_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_rights.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->rights);
    }

    function test_entry_rights_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_rights_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->rights);
    }

    function test_entry_rights_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_rights_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->rights);
    }

    function test_entry_rights_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_rights_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->getEntryByOffset(0)->rights);
    }

    function test_entry_rights_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_rights_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->getEntryByOffset(0)->rights);
    }

    function test_entry_rights_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_rights_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->rights);
    }

    function test_entry_rights_text_plain_brackets_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_rights_text_plain_brackets.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('History of the <blink> tag', $feed->getEntryByOffset(0)->rights);
    }

    function test_entry_rights_type_default_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_rights_type_default.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->rights(0, 'type'));
    }

    function test_entry_rights_type_text_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_rights_type_text.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->rights(0, 'type'));
    }

    function test_entry_source_author_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_author_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->source(0, "author(0, 'email')"));
    }

    function test_entry_source_author_map_author_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_author_map_author.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author (me@example.com)', $feed->getEntryByOffset(0)->source(0, 'author'));
    }

    function test_entry_source_author_map_author_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_author_map_author_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author', $feed->getEntryByOffset(0)->source(0, 'author'));
    }

    function test_entry_source_author_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_author_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author', $feed->getEntryByOffset(0)->source(0, "author(0, 'name')"));
    }

    function test_entry_source_author_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_author_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->source(0, "author(0, 'url')"));
    }

    function test_entry_source_category_label_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_category_label.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Atom 1.0 tests', $feed->getEntryByOffset(0)->source(0, 'tags->label'));
    }

    function test_entry_source_category_scheme_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_category_scheme.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://feedparser.org/tests/', $feed->getEntryByOffset(0)->source(0, 'tags->scheme'));
    }

    function test_entry_source_category_term_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_category_term.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('atom10', $feed->getEntryByOffset(0)->source(0, 'tags->term'));
    }

    function test_entry_source_contributor_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_contributor_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->source(0, 'contributors->email'));
    }

    function test_entry_source_contributor_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_contributor_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(array('name' => 'Contributor 1', 'email' => 'me@example.com', 'href' => 'http://example.com/'), array('name' => 'Contributor 2', 'email' => 'you@example.com', 'href' => 'http://two.example.com/')), $feed->getEntryByOffset(0)->source(0, 'contributors'));
    }

    function test_entry_source_contributor_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_contributor_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example contributor', $feed->getEntryByOffset(0)->source(0, 'contributors->name'));
    }

    function test_entry_source_contributor_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_contributor_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->source(0, 'contributors->url'));
    }

    function test_entry_source_generator_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_generator.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example generator', $feed->getEntryByOffset(0)->source(0, 'generator'));
    }

    function test_entry_source_generator_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_generator_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example generator', $feed->getEntryByOffset(0)->source(0, "generator(0, 'name')"));
    }

    function test_entry_source_generator_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_generator_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->source(0, "generator(0, 'href')"));
    }

    function test_entry_source_generator_version_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_generator_version.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2.65', $feed->getEntryByOffset(0)->source(0, "generator(0, 'version')"));
    }

    function test_entry_source_icon_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_icon.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/favicon.ico', $feed->getEntryByOffset(0)->source(0, 'icon'));
    }

    function test_entry_source_id_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_id.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->source(0, 'id'));
    }

    function test_entry_source_link_alternate_map_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_alternate_map_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->getEntryByOffset(0)->source(0, 'link'));
    }

    function test_entry_source_link_alternate_map_link_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_alternate_map_link_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->getEntryByOffset(0)->source(0, 'link'));
    }

    function test_entry_source_link_href_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_href.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->getEntryByOffset(0)->source(0, 'links->href'));
    }

    function test_entry_source_link_hreflang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_hreflang.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->source(0, 'links->hreflang'));
    }

    function test_entry_source_link_length_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_length.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('42301', $feed->getEntryByOffset(0)->source(0, 'links->length'));
    }

    function test_entry_source_link_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(array('rel' => 'alternate', 'type' => 'application/xhtml+xml', 'href' => 'http://www.example.com/'), array('rel' => 'service.post', 'type' => 'application/atom+xml', 'href' => 'http://www.example.com/post')), $feed->getEntryByOffset(0)->source(0, 'links'));
    }

    function test_entry_source_link_no_rel_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_no_rel.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('alternate', $feed->getEntryByOffset(0)->source(0, 'links->rel'));
    }

    function test_entry_source_link_rel_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_rel.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('alternate', $feed->getEntryByOffset(0)->source(0, 'links->rel'));
    }

    function test_entry_source_link_rel_other_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_rel_other.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://feedparser.org/rel/test', $feed->getEntryByOffset(0)->source(0, 'links->rel'));
    }

    function test_entry_source_link_rel_related_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_rel_related.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('related', $feed->getEntryByOffset(0)->source(0, 'links->rel'));
    }

    function test_entry_source_link_rel_self_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_rel_self.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('self', $feed->getEntryByOffset(0)->source(0, 'links->rel'));
    }

    function test_entry_source_link_rel_via_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_rel_via.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('via', $feed->getEntryByOffset(0)->source(0, 'links->rel'));
    }

    function test_entry_source_link_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example title', $feed->getEntryByOffset(0)->source(0, 'links->title'));
    }

    function test_entry_source_link_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_link_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/html', $feed->getEntryByOffset(0)->source(0, 'links->type'));
    }

    function test_entry_source_logo_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_logo.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/logo.jpg', $feed->getEntryByOffset(0)->source(0, 'logo'));
    }

    function test_entry_source_rights_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_rights.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->source(0, 'rights'));
    }

    function test_entry_source_rights_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_rights_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->source(0, 'rights'));
    }

    function test_entry_source_rights_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_rights_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->getEntryByOffset(0)->source(0, 'rights'));
    }

    function test_entry_source_rights_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_rights_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->source(0, "rights(0, 'type')"));
    }

    function test_entry_source_rights_content_type_text_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_rights_content_type_text.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->source(0, "rights(0, 'type')"));
    }

    function test_entry_source_rights_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_rights_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->source(0, 'rights'));
    }

    function test_entry_source_rights_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_rights_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->source(0, 'rights'));
    }

    function test_entry_source_rights_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_rights_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->getEntryByOffset(0)->source(0, 'rights'));
    }

    function test_entry_source_rights_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_rights_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->getEntryByOffset(0)->source(0, 'rights'));
    }

    function test_entry_source_rights_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_rights_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->source(0, 'rights'));
    }

    function test_entry_source_subittle_content_type_text_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_subittle_content_type_text.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->source(0, "subtitle(0, 'type')"));
    }

    function test_entry_source_subtitle_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_subtitle.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->source(0, 'subtitle'));
    }

    function test_entry_source_subtitle_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_subtitle_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->source(0, 'subtitle'));
    }

    function test_entry_source_subtitle_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_subtitle_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->getEntryByOffset(0)->source(0, 'subtitle'));
    }

    function test_entry_source_subtitle_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_subtitle_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->source(0, "subtitle(0, 'type')"));
    }

    function test_entry_source_subtitle_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_subtitle_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->source(0, 'subtitle'));
    }

    function test_entry_source_subtitle_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_subtitle_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->source(0, 'subtitle'));
    }

    function test_entry_source_subtitle_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_subtitle_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->getEntryByOffset(0)->source(0, 'subtitle'));
    }

    function test_entry_source_subtitle_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_subtitle_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->getEntryByOffset(0)->source(0, 'subtitle'));
    }

    function test_entry_source_subtitle_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_subtitle_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->source(0, 'subtitle'));
    }

    function test_entry_source_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->source(0, 'title'));
    }

    function test_entry_source_title_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_title_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->source(0, 'title'));
    }

    function test_entry_source_title_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_title_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->getEntryByOffset(0)->source(0, 'title'));
    }

    function test_entry_source_title_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_title_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->source(0, "title(0, 'type')"));
    }

    function test_entry_source_title_content_type_text_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_title_content_type_text.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->source(0, "title(0, 'type')"));
    }

    function test_entry_source_title_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_title_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->source(0, 'title'));
    }

    function test_entry_source_title_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_title_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->source(0, 'title'));
    }

    function test_entry_source_title_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_title_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->getEntryByOffset(0)->source(0, 'title'));
    }

    function test_entry_source_title_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_title_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->getEntryByOffset(0)->source(0, 'title'));
    }

    function test_entry_source_title_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_source_title_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->source(0, 'title'));
    }

    function test_entry_summary_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_summary.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_summary_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_summary_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_summary_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_summary_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_summary_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_summary_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_summary_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->summary);
    }

    function test_entry_summary_type_default_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_summary_type_default.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->summary(0, 'type'));
    }

    function test_entry_summary_type_text_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_summary_type_text.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->summary(0, 'type'));
    }

    function test_entry_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_title_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_title_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_title_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_title_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_title_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_title_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_title_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_text_plain_brackets_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_title_text_plain_brackets.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('History of the <blink> tag', $feed->getEntryByOffset(0)->title);
    }

    function test_entry_title_type_default_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_title_type_default.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->title(0, 'type'));
    }

    function test_entry_title_type_text_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/entry_title_type_text.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->getEntryByOffset(0)->title(0, 'type'));
    }

    function test_feed_author_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_author_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->author(0, 'email'));
    }

    function test_feed_author_map_author_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_author_map_author.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author (me@example.com)', $feed->author);
    }

    function test_feed_author_map_author_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_author_map_author_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author', $feed->author);
    }

    function test_feed_author_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_author_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example author', $feed->author(0, 'name'));
    }

    function test_feed_author_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_author_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->author(0, 'url'));
    }

    function test_feed_author_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_author_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->author(0, 'url'));
    }

    function test_feed_contributor_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_contributor_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->contributors(0, 'email'));
    }

    function test_feed_contributor_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_contributor_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(array('name' => 'Contributor 1', 'email' => 'me@example.com', 'href' => 'http://example.com/'), array('name' => 'Contributor 2', 'email' => 'you@example.com', 'href' => 'http://two.example.com/')), $feed->contributors);
    }

    function test_feed_contributor_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_contributor_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example contributor', $feed->contributors(0, 'name'));
    }

    function test_feed_contributor_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_contributor_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->contributors(0, 'url'));
    }

    function test_feed_contributor_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_contributor_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->contributors(0, 'url'));
    }

    function test_feed_generator_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_generator.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example generator', $feed->generator);
    }

    function test_feed_generator_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_generator_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example generator', $feed->generator(0, 'name'));
    }

    function test_feed_generator_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_generator_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->generator(0, 'href'));
    }

    function test_feed_generator_version_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_generator_version.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2.65', $feed->generator(0, 'version'));
    }

    function test_feed_icon_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_icon.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/favicon.ico', $feed->icon);
    }

    function test_feed_id_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_id.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->id);
    }

    function test_feed_id_map_guid_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_id_map_guid.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->guid);
    }

    function test_feed_link_alternate_map_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_alternate_map_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->link);
    }

    function test_feed_link_alternate_map_link_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_alternate_map_link_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->link);
    }

    function test_feed_link_href_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_href.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->links(0, 'href'));
    }

    function test_feed_link_hreflang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_hreflang.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->links(0, 'hreflang'));
    }

    function test_feed_link_length_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_length.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('42301', $feed->links(0, 'length'));
    }

    function test_feed_link_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(array('rel' => 'alternate', 'type' => 'application/xhtml+xml', 'href' => 'http://www.example.com/'), array('rel' => 'service.post', 'type' => 'application/atom+xml', 'href' => 'http://www.example.com/post')), $feed->links);
    }

    function test_feed_link_no_rel_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_no_rel.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('alternate', $feed->links(0, 'rel'));
    }

    function test_feed_link_rel_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_rel.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('alternate', $feed->links(0, 'rel'));
    }

    function test_feed_link_rel_other_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_rel_other.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://feedparser.org/rel/test', $feed->links(0, 'rel'));
    }

    function test_feed_link_rel_related_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_rel_related.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('related', $feed->links(0, 'rel'));
    }

    function test_feed_link_rel_self_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_rel_self.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('self', $feed->links(0, 'rel'));
    }

    function test_feed_link_rel_via_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_rel_via.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('via', $feed->links(0, 'rel'));
    }

    function test_feed_link_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example title', $feed->links(0, 'title'));
    }

    function test_feed_link_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_link_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/html', $feed->links(0, 'type'));
    }

    function test_feed_logo_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_logo.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/logo.jpg', $feed->logo);
    }

    function test_feed_rights_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_rights.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->rights);
    }

    function test_feed_rights_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_rights_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->rights);
    }

    function test_feed_rights_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_rights_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->rights);
    }

    function test_feed_rights_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_rights_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->rights(0, 'type'));
    }

    function test_feed_rights_content_type_text_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_rights_content_type_text.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->rights(0, 'type'));
    }

    function test_feed_rights_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_rights_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->rights);
    }

    function test_feed_rights_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_rights_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->rights);
    }

    function test_feed_rights_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_rights_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->rights);
    }

    function test_feed_rights_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_rights_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->rights);
    }

    function test_feed_rights_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_rights_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->rights);
    }

    function test_feed_subtitle_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_subtitle.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->subtitle);
    }

    function test_feed_subtitle_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_subtitle_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->subtitle);
    }

    function test_feed_subtitle_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_subtitle_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->subtitle);
    }

    function test_feed_subtitle_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_subtitle_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->subtitle(0, 'type'));
    }

    function test_feed_subtitle_content_type_text_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_subtitle_content_type_text.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->subtitle(0, 'type'));
    }

    function test_feed_subtitle_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_subtitle_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->subtitle);
    }

    function test_feed_subtitle_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_subtitle_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->subtitle);
    }

    function test_feed_subtitle_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_subtitle_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->subtitle);
    }

    function test_feed_subtitle_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_subtitle_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->subtitle);
    }

    function test_feed_subtitle_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_subtitle_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->subtitle);
    }

    function test_feed_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->title);
    }

    function test_feed_title_base64_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_title_base64.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->title);
    }

    function test_feed_title_base64_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_title_base64_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>History of the &lt;blink&gt; tag</p>', $feed->title);
    }

    function test_feed_title_content_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_title_content_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->title(0, 'type'));
    }

    function test_feed_title_content_type_text_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_title_content_type_text.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/plain', $feed->title(0, 'type'));
    }

    function test_feed_title_content_value_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_title_content_value.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->title);
    }

    function test_feed_title_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_title_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example <b>Atom</b>', $feed->title);
    }

    function test_feed_title_inline_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_title_inline_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <b>Atom</b></div>', $feed->title);
    }

    function test_feed_title_inline_markup_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_title_inline_markup_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>History of the &lt;blink&gt; tag</div>', $feed->title);
    }

    function test_feed_title_text_plain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/feed_title_text_plain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example Atom', $feed->title);
    }

    function test_relative_uri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/relative_uri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <a href="http://example.com/test/test.html">test</a></div>', $feed->title);
    }

    function test_relative_uri_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/relative_uri_inherit.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <a href="http://example.com/test/test.html">test</a></div>', $feed->title);
    }

    function test_relative_uri_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/atom10/relative_uri_inherit_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<div>Example <a href="http://example.com/test/test.html">test</a></div>', $feed->title);
    }

}
?>
