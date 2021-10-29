<?php

require_once dirname(dirname(__FILE__)) . '/XML_Feed_Parser_TestCase.php';

class base_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function setUp() {
        if (LIBXML_VERSION <= 20632) {
            $this->markTestSkipped("Unable to test due to http://bugzilla.gnome.org/show_activity.cgi?id=565219");
        }

        parent::setUp();
    }

    function test_entry_content_xml_base_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_content_xml_base.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->content(0, 'base'));
    }

    function test_entry_content_xml_base_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_content_xml_base_inherit.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->content(0, 'base'));
    }

    function test_entry_content_xml_base_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_content_xml_base_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->content(0, 'base'));
    }

    function test_entry_content_xml_base_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_content_xml_base_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->content(0, 'base'));
    }

    function test_entry_content_xml_base_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_content_xml_base_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/parent/', $feed->getEntryByOffset(0)->content(0, 'base'));
    }

    function test_entry_summary_xml_base_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_summary_xml_base.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->summary(0, 'base'));
    }

    function test_entry_summary_xml_base_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_summary_xml_base_inherit.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->summary(0, 'base'));
    }

    function test_entry_summary_xml_base_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_summary_xml_base_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->summary(0, 'base'));
    }

    function test_entry_summary_xml_base_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_summary_xml_base_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/summary/', $feed->getEntryByOffset(0)->summary(0, 'base'));
    }

    function test_entry_summary_xml_base_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_summary_xml_base_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/parent/', $feed->getEntryByOffset(0)->summary(0, 'base'));
    }

    function test_entry_title_xml_base_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_title_xml_base.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->title(0, 'base'));
    }

    function test_entry_title_xml_base_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_title_xml_base_inherit.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->title(0, 'base'));
    }

    function test_entry_title_xml_base_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_title_xml_base_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->title(0, 'base'));
    }

    function test_entry_title_xml_base_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_title_xml_base_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->title(0, 'base'));
    }

    function test_entry_title_xml_base_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/entry_title_xml_base_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/parent/', $feed->getEntryByOffset(0)->title(0, 'base'));
    }

    function test_feed_copyright_xml_base_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_copyright_xml_base.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->copyright(0, 'base'));
    }

    function test_feed_copyright_xml_base_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_copyright_xml_base_inherit.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->copyright(0, 'base'));
    }

    function test_feed_copyright_xml_base_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_copyright_xml_base_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->copyright(0, 'base'));
    }

    function test_feed_copyright_xml_base_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_copyright_xml_base_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->copyright(0, 'base'));
    }

    function test_feed_copyright_xml_base_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_copyright_xml_base_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/parent/', $feed->copyright(0, 'base'));
    }

    function test_feed_info_xml_base_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_info_xml_base.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->info(0, 'base'));
    }

    function test_feed_info_xml_base_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_info_xml_base_inherit.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->info(0, 'base'));
    }

    function test_feed_info_xml_base_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_info_xml_base_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->info(0, 'base'));
    }

    function test_feed_info_xml_base_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_info_xml_base_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/info/', $feed->info(0, 'base'));
    }

    function test_feed_info_xml_base_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_info_xml_base_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/parent/', $feed->info(0, 'base'));
    }

    function test_feed_tagline_xml_base_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_tagline_xml_base.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->tagline(0, 'base'));
    }

    function test_feed_tagline_xml_base_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_tagline_xml_base_inherit.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->tagline(0, 'base'));
    }

    function test_feed_tagline_xml_base_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_tagline_xml_base_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->tagline(0, 'base'));
    }

    function test_feed_tagline_xml_base_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_tagline_xml_base_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->tagline(0, 'base'));
    }

    function test_feed_tagline_xml_base_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_tagline_xml_base_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/parent/', $feed->tagline(0, 'base'));
    }

    function test_feed_title_xml_base_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_title_xml_base.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->title(0, 'base'));
    }

    function test_feed_title_xml_base_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_title_xml_base_inherit.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->title(0, 'base'));
    }

    function test_feed_title_xml_base_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_title_xml_base_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->title(0, 'base'));
    }

    function test_feed_title_xml_base_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_title_xml_base_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->title(0, 'base'));
    }

    function test_feed_title_xml_base_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/feed_title_xml_base_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/parent/', $feed->title(0, 'base'));
    }

    function test_http_channel_docs_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_channel_docs_base_content_location.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/relative/uri', $feed->docs);
    }

    function test_http_channel_docs_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_channel_docs_base_docuri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://127.0.0.1:8097/relative/uri', $feed->docs);
    }

    function test_http_channel_link_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_channel_link_base_content_location.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/relative/uri', $feed->link);
    }

    function test_http_channel_link_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_channel_link_base_docuri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://127.0.0.1:8097/relative/uri', $feed->link);
    }

    function test_http_entry_author_url_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_author_url_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/relative/link', $feed->getEntryByOffset(0)->author(0, 'url'));
    }

    function test_http_entry_author_url_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_author_url_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://127.0.0.1:8097/relative/link', $feed->getEntryByOffset(0)->author(0, 'url'));
    }

    function test_http_entry_content_base64_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_content_base64_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_entry_content_base64_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_content_base64_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_entry_content_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_content_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_entry_content_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_content_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_entry_content_inline_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_content_inline_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_entry_content_inline_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_content_inline_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_entry_contributor_url_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_contributor_url_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/relative/link', $feed->getEntryByOffset(0)->contributors(0, 'url'));
    }

    function test_http_entry_contributor_url_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_contributor_url_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://127.0.0.1:8097/relative/link', $feed->getEntryByOffset(0)->contributors(0, 'url'));
    }

    function test_http_entry_id_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_id_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/relative/link', $feed->getEntryByOffset(0)->id);
    }

    function test_http_entry_id_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_id_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://127.0.0.1:8097/relative/link', $feed->getEntryByOffset(0)->id);
    }

    function test_http_entry_link_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_link_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/relative/link', $feed->getEntryByOffset(0)->links(0, 'href'));
    }

    function test_http_entry_link_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_link_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://127.0.0.1:8097/relative/link', $feed->getEntryByOffset(0)->links(0, 'href'));
    }

    function test_http_entry_summary_base64_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_summary_base64_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->summary);
    }

    function test_http_entry_summary_base64_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_summary_base64_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->summary);
    }

    function test_http_entry_summary_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_summary_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->summary);
    }

    function test_http_entry_summary_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_summary_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->summary);
    }

    function test_http_entry_summary_inline_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_summary_inline_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->summary);
    }

    function test_http_entry_summary_inline_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_summary_inline_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->summary);
    }

    function test_http_entry_title_base64_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_title_base64_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_http_entry_title_base64_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_title_base64_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_http_entry_title_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_title_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_http_entry_title_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_title_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_http_entry_title_inline_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_title_inline_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_http_entry_title_inline_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_entry_title_inline_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->getEntryByOffset(0)->title);
    }

    function test_http_feed_author_url_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_author_url_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/relative/link', $feed->author(0, 'url'));
    }

    function test_http_feed_author_url_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_author_url_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://127.0.0.1:8097/relative/link', $feed->author(0, 'url'));
    }

    function test_http_feed_contributor_url_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_contributor_url_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/relative/link', $feed->contributors(0, 'url'));
    }

    function test_http_feed_contributor_url_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_contributor_url_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://127.0.0.1:8097/relative/link', $feed->contributors(0, 'url'));
    }

    function test_http_feed_copyright_base64_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_copyright_base64_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->copyright);
    }

    function test_http_feed_copyright_base64_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_copyright_base64_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->copyright);
    }

    function test_http_feed_copyright_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_copyright_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->copyright);
    }

    function test_http_feed_copyright_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_copyright_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->copyright);
    }

    function test_http_feed_copyright_inline_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_copyright_inline_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->copyright);
    }

    function test_http_feed_copyright_inline_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_copyright_inline_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->copyright);
    }

    function test_http_feed_generator_url_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_generator_url_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/relative/link', $feed->generator(0, 'url'));
    }

    function test_http_feed_generator_url_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_generator_url_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://127.0.0.1:8097/relative/link', $feed->generator(0, 'url'));
    }

    function test_http_feed_id_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_id_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/relative/link', $feed->id);
    }

    function test_http_feed_id_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_id_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://127.0.0.1:8097/relative/link', $feed->id);
    }

    function test_http_feed_info_base64_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_info_base64_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->info);
    }

    function test_http_feed_info_base64_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_info_base64_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->info);
    }

    function test_http_feed_info_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_info_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->info);
    }

    function test_http_feed_info_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_info_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->info);
    }

    function test_http_feed_info_inline_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_info_inline_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->info);
    }

    function test_http_feed_info_inline_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_info_inline_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->info);
    }

    function test_http_feed_link_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_link_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/relative/link', $feed->links(0, 'href'));
    }

    function test_http_feed_link_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_link_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://127.0.0.1:8097/relative/link', $feed->links(0, 'href'));
    }

    function test_http_feed_tagline_base64_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_tagline_base64_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->tagline);
    }

    function test_http_feed_tagline_base64_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_tagline_base64_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->tagline);
    }

    function test_http_feed_tagline_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_tagline_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->tagline);
    }

    function test_http_feed_tagline_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_tagline_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->tagline);
    }

    function test_http_feed_tagline_inline_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_tagline_inline_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->tagline);
    }

    function test_http_feed_tagline_inline_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_tagline_inline_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->tagline);
    }

    function test_http_feed_title_base64_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_title_base64_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->title);
    }

    function test_http_feed_title_base64_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_title_base64_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->title);
    }

    function test_http_feed_title_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_title_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->title);
    }

    function test_http_feed_title_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_title_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->title);
    }

    function test_http_feed_title_inline_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_title_inline_base_content_location.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://example.com/relative/uri">click here</a></div>', $feed->title);
    }

    function test_http_feed_title_inline_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_feed_title_inline_base_docuri.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('<div><a href="http://127.0.0.1:8097/relative/uri">click here</a></div>', $feed->title);
    }

    function test_http_item_body_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_body_base_content_location.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<a href="http://example.com/relative/uri">click here</a>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_item_body_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_body_base_docuri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<a href="http://127.0.0.1:8097/relative/uri">click here</a>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_item_comments_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_comments_base_content_location.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/relative/uri', $feed->getEntryByOffset(0)->comments);
    }

    function test_http_item_comments_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_comments_base_docuri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://127.0.0.1:8097/relative/uri', $feed->getEntryByOffset(0)->comments);
    }

    function test_http_item_content_encoded_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_content_encoded_base_content_location.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<a href="http://example.com/relative/uri">click here</a>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_item_content_encoded_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_content_encoded_base_docuri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<a href="http://127.0.0.1:8097/relative/uri">click here</a>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_item_description_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_description_base_content_location.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<a href="http://example.com/relative/uri">click here</a>', $feed->getEntryByOffset(0)->description);
    }

    function test_http_item_description_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_description_base_docuri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<a href="http://127.0.0.1:8097/relative/uri">click here</a>', $feed->getEntryByOffset(0)->description);
    }

    function test_http_item_fullitem_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_fullitem_base_content_location.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<a href="http://example.com/relative/uri">click here</a>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_item_fullitem_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_fullitem_base_docuri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<a href="http://127.0.0.1:8097/relative/uri">click here</a>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_item_link_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_link_base_content_location.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/relative/uri', $feed->getEntryByOffset(0)->link);
    }

    function test_http_item_link_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_link_base_docuri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://127.0.0.1:8097/relative/uri', $feed->getEntryByOffset(0)->link);
    }

    function test_http_item_wfw_comment_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_wfw_comment_base_content_location.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/relative/uri', $feed->getEntryByOffset(0)->wfw_comment);
    }

    function test_http_item_wfw_comment_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_wfw_comment_base_docuri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://127.0.0.1:8097/relative/uri', $feed->getEntryByOffset(0)->wfw_comment);
    }

    function test_http_item_wfw_commentRSS_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_wfw_commentRSS_base_content_location.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/relative/uri', $feed->getEntryByOffset(0)->wfw_commentrss);
    }

    function test_http_item_wfw_commentRSS_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_wfw_commentRSS_base_docuri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://127.0.0.1:8097/relative/uri', $feed->getEntryByOffset(0)->wfw_commentrss);
    }

    function test_http_item_xhtml_body_base_content_location_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_xhtml_body_base_content_location.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<a href="http://example.com/relative/uri">click here</a>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_item_xhtml_body_base_docuri_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_item_xhtml_body_base_docuri.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<a href="http://127.0.0.1:8097/relative/uri">click here</a>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_http_relative_xml_base_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_relative_xml_base.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/feed/entry/title/', $feed->getEntryByOffset(0)->title(0, 'base'));
    }

    function test_http_relative_xml_base_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/http_relative_xml_base_2.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/entry/', $feed->getEntryByOffset(0)->title(0, 'base'));
    }

    function test_malformed_base_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/malformed_base.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->title(0, 'base'));
    }

    function test_relative_xml_base_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/relative_xml_base.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->title(0, 'base'));
    }

    function test_relative_xml_base_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/base/relative_xml_base_2.xml');

        $feed = new XML_Feed_Parser($content, true, true);

        $this->assertEquals('http://example.com/test/', $feed->getEntryByOffset(0)->title(0, 'base'));
    }
}
?>
