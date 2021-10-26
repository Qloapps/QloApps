<?php

require_once dirname(dirname(__FILE__)) . '/XML_Feed_Parser_TestCase.php';

class lang_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_channel_dc_language_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/channel_dc_language.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->language);
    }

    function test_channel_language_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/channel_language.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en-us', $feed->language);
    }

    function test_entry_content_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_content_xml_lang_blank_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_blank.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals("", $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_content_xml_lang_blank_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_blank_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals("", $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_content_xml_lang_blank_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_blank_3.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(1)->content->language);
    }

    function test_entry_content_xml_lang_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_inherit.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_content_xml_lang_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_content_xml_lang_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_content_xml_lang_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_content_xml_lang_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_entry_summary_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_summary_xml_lang.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->summary(0, 'language'));
    }

    function test_entry_summary_xml_lang_blank_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_summary_xml_lang_blank.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals("", $feed->getEntryByOffset(0)->summary(0, 'language'));
    }

    function test_entry_summary_xml_lang_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_summary_xml_lang_inherit.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->summary(0, 'language'));
    }

    function test_entry_summary_xml_lang_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_summary_xml_lang_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->summary(0, 'language'));
    }

    function test_entry_summary_xml_lang_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_summary_xml_lang_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->summary(0, 'language'));
    }

    function test_entry_summary_xml_lang_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_summary_xml_lang_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->summary(0, 'language'));
    }

    function test_entry_title_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_title_xml_lang.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_entry_title_xml_lang_blank_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_title_xml_lang_blank.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals("", $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_entry_title_xml_lang_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_title_xml_lang_inherit.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_entry_title_xml_lang_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_title_xml_lang_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_entry_title_xml_lang_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_title_xml_lang_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_entry_title_xml_lang_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/entry_title_xml_lang_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_feed_copyright_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_copyright_xml_lang.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->copyright(0, 'language'));
    }

    function test_feed_copyright_xml_lang_blank_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_copyright_xml_lang_blank.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals("", $feed->copyright(0, 'language'));
    }

    function test_feed_copyright_xml_lang_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_copyright_xml_lang_inherit.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->copyright(0, 'language'));
    }

    function test_feed_copyright_xml_lang_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_copyright_xml_lang_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->copyright(0, 'language'));
    }

    function test_feed_copyright_xml_lang_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_copyright_xml_lang_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->copyright(0, 'language'));
    }

    function test_feed_copyright_xml_lang_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_copyright_xml_lang_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->copyright(0, 'language'));
    }

    function test_feed_info_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_info_xml_lang.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->info(0, 'language'));
    }

    function test_feed_info_xml_lang_blank_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_info_xml_lang_blank.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals("", $feed->info(0, 'language'));
    }

    function test_feed_info_xml_lang_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_info_xml_lang_inherit.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->info(0, 'language'));
    }

    function test_feed_info_xml_lang_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_info_xml_lang_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->info(0, 'language'));
    }

    function test_feed_info_xml_lang_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_info_xml_lang_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->info(0, 'language'));
    }

    function test_feed_info_xml_lang_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_info_xml_lang_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->info(0, 'language'));
    }

    function test_feed_language_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_language.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->language);
    }

    function test_feed_language_override_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_language_override.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->language);
    }

    function test_feed_not_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_not_xml_lang.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->language);
    }

    function test_feed_not_xml_lang_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_not_xml_lang_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
        //$this->assertEquals(, ! $feed.has_key(->));
    }

    function test_feed_tagline_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_tagline_xml_lang.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->tagline(0, 'language'));
    }

    function test_feed_tagline_xml_lang_blank_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_tagline_xml_lang_blank.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals("", $feed->tagline(0, 'language'));
    }

    function test_feed_tagline_xml_lang_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_tagline_xml_lang_inherit.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->tagline(0, 'language'));
    }

    function test_feed_tagline_xml_lang_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_tagline_xml_lang_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('fr', $feed->tagline(0, 'language'));
    }

    function test_feed_tagline_xml_lang_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_tagline_xml_lang_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('de', $feed->tagline(0, 'language'));
    }

    function test_feed_tagline_xml_lang_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_tagline_xml_lang_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->tagline(0, 'language'));
    }

    function test_feed_title_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_title_xml_lang.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->title(0, 'language'));
    }

    function test_feed_title_xml_lang_blank_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_title_xml_lang_blank.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals("", $feed->title(0, 'language'));
    }

    function test_feed_title_xml_lang_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_title_xml_lang_inherit.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('de', $feed->title(0, 'language'));
    }

    function test_feed_title_xml_lang_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_title_xml_lang_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('de', $feed->title(0, 'language'));
    }

    function test_feed_title_xml_lang_inherit_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_title_xml_lang_inherit_3.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('de', $feed->title(0, 'language'));
    }

    function test_feed_title_xml_lang_inherit_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_title_xml_lang_inherit_4.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->title(0, 'language'));
    }

    function test_feed_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/feed_xml_lang.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->language);
    }

    function test_http_content_language_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/http_content_language.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->language);
    }

    function test_http_content_language_entry_title_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/http_content_language_entry_title_inherit.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_http_content_language_entry_title_inherit_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/http_content_language_entry_title_inherit_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->title(0, 'language'));
    }

    function test_http_content_language_feed_language_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/http_content_language_feed_language.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('fr', $feed->language);
    }

    function test_http_content_language_feed_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/http_content_language_feed_xml_lang.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('fr', $feed->language);
    }

    function test_item_content_encoded_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_content_encoded_xml_lang.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_item_content_encoded_xml_lang_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_content_encoded_xml_lang_inherit.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_item_dc_language_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_dc_language.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->language);
    }

    function test_item_fullitem_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_fullitem_xml_lang.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_item_fullitem_xml_lang_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_fullitem_xml_lang_inherit.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_item_xhtml_body_xml_lang_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_xhtml_body_xml_lang.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }

    function test_item_xhtml_body_xml_lang_inherit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/lang/item_xhtml_body_xml_lang_inherit.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('en', $feed->getEntryByOffset(0)->content(0, 'language'));
    }
}

?>
