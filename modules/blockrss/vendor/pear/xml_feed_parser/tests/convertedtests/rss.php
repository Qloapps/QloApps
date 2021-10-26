<?php

require_once dirname(dirname(__FILE__)) . '/XML_Feed_Parser_TestCase.php';

class rss_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_channel_author_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_author.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor (me@example.com)', $feed->author);
    }

    function test_channel_author_map_author_detail_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_author_map_author_detail_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->author(0, 'email'));
    }

    function test_channel_author_map_author_detail_email_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_author_map_author_detail_email_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me+spam@example.com', $feed->author(0, 'email'));
    }

    function test_channel_author_map_author_detail_email_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_author_map_author_detail_email_3.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->author(0, 'email'));
    }

    function test_channel_author_map_author_detail_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_author_map_author_detail_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->author(0, 'name'));
    }

    function test_channel_author_map_author_detail_name_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_author_map_author_detail_name_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->author(0, 'name'));
    }

    function test_channel_category_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_category.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example category', $feed->category);
    }

    function test_channel_category_domain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_category_domain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->categories[0][0]);
    }

    function test_channel_category_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_category_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/2', $feed->categories[1][0]);
    }

    function test_channel_category_multiple_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_category_multiple_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example category 2', $feed->categories[1][1]);
    }

    function test_channel_cloud_domain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_cloud_domain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('rpc.sys.com', $feed->cloud(0, 'domain'));
    }

    function test_channel_cloud_path_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_cloud_path.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('/RPC2', $feed->cloud(0, 'path'));
    }

    function test_channel_cloud_port_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_cloud_port.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('80', $feed->cloud(0, 'port'));
    }

    function test_channel_cloud_protocol_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_cloud_protocol.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('xml-rpc', $feed->cloud(0, 'protocol'));
    }

    function test_channel_cloud_registerProcedure_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_cloud_registerProcedure.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('myCloud.rssPleaseNotify', $feed->cloud(0, 'registerprocedure'));
    }

    function test_channel_copyright_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_copyright.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example copyright', $feed->copyright);
    }

    function test_channel_dc_author_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_author.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->author);
    }

    function test_channel_dc_author_map_author_detail_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_author_map_author_detail_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->author(0, 'email'));
    }

    function test_channel_dc_author_map_author_detail_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_author_map_author_detail_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->author(0, 'name'));
    }

    function test_channel_dc_contributor_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_contributor.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example contributor', $feed->contributors(0, 'name'));
    }

    function test_channel_dc_creator_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_creator.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->author);
    }

    function test_channel_dc_creator_map_author_detail_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_creator_map_author_detail_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->author(0, 'email'));
    }

    function test_channel_dc_creator_map_author_detail_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_creator_map_author_detail_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->author(0, 'name'));
    }

    function test_channel_dc_publisher_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_publisher.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->publisher);
    }

    function test_channel_dc_publisher_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_publisher_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->publisher(0, 'email'));
    }

    function test_channel_dc_publisher_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_publisher_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->publisher(0, 'name'));
    }

    function test_channel_dc_rights_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_rights.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example copyright', $feed->copyright);
    }

    function test_channel_dc_subject_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_subject.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example category', $feed->category);
    }

    function test_channel_dc_subject_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_subject_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example category', $feed->categories[0][1]);
    }

    function test_channel_dc_subject_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_subject_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example category 2', $feed->categories[1][1]);
    }

    function test_channel_dc_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_dc_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example title', $feed->title);
    }

    function test_channel_description_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_description.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example description', $feed->description);
    }

    function test_channel_description_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_description_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>Example description</p>', $feed->description);
    }

    function test_channel_description_map_tagline_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_description_map_tagline.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example description', $feed->tagline);
    }

    function test_channel_description_naked_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_description_naked_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>Example description</p>', $feed->description);
    }

    function test_channel_description_shorttag_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_description_shorttag.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('', $feed->description);
    }

    function test_channel_description_shorttag_2() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_description_shorttag.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->link);
    }

    function test_channel_docs_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_docs.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->docs);
    }

    function test_channel_generator_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_generator.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example generator', $feed->generator);
    }

    function test_channel_image_description_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_description.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Available in Netscape RSS 0.91', $feed->image(0, 'description'));
    }

    function test_channel_image_height_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_height.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(15, $feed->image(0, 'height'));
    }

    function test_channel_image_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.org/link', $feed->image(0, 'link'));
    }

    function test_channel_image_link_conflict_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_link_conflict.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://channel.example.com/', $feed->link);
    }

    function test_channel_image_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Sample image', $feed->image(0, 'title'));
    }

    function test_channel_image_title_conflict_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_title_conflict.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Real title', $feed->title);
    }

    function test_channel_image_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.org/url', $feed->image(0, 'url'));
    }

    function test_channel_image_width_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_image_width.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(80, $feed->image(0, 'width'));
    }

    function test_channel_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->link);
    }

    function test_channel_managingEditor_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_managingEditor.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->author);
    }

    function test_channel_managingEditor_map_author_detail_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_managingEditor_map_author_detail_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->author(0, 'email'));
    }

    function test_channel_managingEditor_map_author_detail_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_managingEditor_map_author_detail_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->author(0, 'name'));
    }

    function test_channel_textInput_description_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_description.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('textInput description', $feed->textinput(0, 'description'));
    }

    function test_channel_textInput_description_conflict_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_description_conflict.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Real description', $feed->description);
    }

    function test_channel_textInput_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://textinput.example.com/', $feed->textinput(0, 'link'));
    }

    function test_channel_textInput_link_conflict_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_link_conflict.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://channel.example.com/', $feed->link);
    }

    function test_channel_textInput_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('textinput name', $feed->textinput(0, 'name'));
    }

    function test_channel_textInput_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('textInput title', $feed->textinput(0, 'title'));
    }

    function test_channel_textInput_title_conflict_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_textInput_title_conflict.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Real title', $feed->title);
    }

    function test_channel_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example feed', $feed->title);
    }

    function test_channel_title_apos_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_title_apos.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals("Mark's title", $feed->title);
    }

    function test_channel_title_gt_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_title_gt.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('2 > 1', $feed->title);
    }

    function test_channel_title_lt_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_title_lt.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('1 < 2', $feed->title);
    }

    function test_channel_ttl_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_ttl.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('60', $feed->ttl);
    }

    function test_channel_webMaster_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_webMaster.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->publisher);
    }

    function test_channel_webMaster_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_webMaster_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->publisher(0, 'email'));
    }

    function test_channel_webMaster_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/channel_webMaster_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->publisher(0, 'name'));
    }

    function test_item_author_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_author.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->author);
    }

    function test_item_author_map_author_detail_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_author_map_author_detail_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->author(0, 'email'));
    }

    function test_item_author_map_author_detail_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_author_map_author_detail_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->author(0, 'name'));
    }

    function test_item_category_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_category.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example category', $feed->getEntryByOffset(0)->category);
    }

    function test_item_category_domain_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_category_domain.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/', $feed->getEntryByOffset(0)->categories[0][0]);
    }

    function test_item_category_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_category_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://www.example.com/2', $feed->getEntryByOffset(0)->categories[1][0]);
    }

    function test_item_category_multiple_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_category_multiple_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example category 2', $feed->getEntryByOffset(0)->categories[1][1]);
    }

    function test_item_comments_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_comments.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->comments);
    }

    function test_item_content_encoded_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_content_encoded.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>Example content</p>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_content_encoded_mode_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_content_encoded_mode.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_item_content_encoded_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_content_encoded_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/html', $feed->getEntryByOffset(0)->content(0, 'type'));
    }

    function test_item_dc_author_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_author.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->author);
    }

    function test_item_dc_author_map_author_detail_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_author_map_author_detail_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->author(0, 'email'));
    }

    function test_item_dc_author_map_author_detail_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_author_map_author_detail_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->author(0, 'name'));
    }

    function test_item_dc_contributor_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_contributor.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example contributor', $feed->getEntryByOffset(0)->contributors(0, 'name'));
    }

    function test_item_dc_creator_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_creator.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->author);
    }

    function test_item_dc_creator_map_author_detail_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_creator_map_author_detail_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->author(0, 'email'));
    }

    function test_item_dc_creator_map_author_detail_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_creator_map_author_detail_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->author(0, 'name'));
    }

    function test_item_dc_publisher_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_publisher.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->publisher);
    }

    function test_item_dc_publisher_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_publisher_email.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('me@example.com', $feed->getEntryByOffset(0)->publisher(0, 'email'));
    }

    function test_item_dc_publisher_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_publisher_name.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example editor', $feed->getEntryByOffset(0)->publisher(0, 'name'));
    }

    function test_item_dc_rights_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_rights.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example copyright', $feed->getEntryByOffset(0)->copyright);
    }

    function test_item_dc_subject_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_subject.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example category', $feed->getEntryByOffset(0)->category);
    }

    function test_item_dc_subject_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_subject_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example category', $feed->getEntryByOffset(0)->categories[0][1]);
    }

    function test_item_dc_subject_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_subject_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example category 2', $feed->getEntryByOffset(0)->categories[1][1]);
    }

    function test_item_dc_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_dc_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example title', $feed->getEntryByOffset(0)->title);
    }

    function test_item_description_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_and_summary_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_and_summary.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example description', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_and_summary_2() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_and_summary.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example summary', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_description_br_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_br.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('article title<br /><br /> article byline<br /><br />text of article', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_escaped_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_escaped_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>Example description</p>', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_map_summary_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_map_summary.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example description', $feed->getEntryByOffset(0)->summary);
    }

    function test_item_description_naked_markup_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_naked_markup.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>Example description</p>', $feed->getEntryByOffset(0)->description);
    }

    function test_item_description_not_a_doctype_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_description_not_a_doctype.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
        //$this->assertEquals("""&lt;!' <a href="foo">""", $feed->getEntryByOffset(0)->description);
    }

    function test_item_enclosure_length_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_enclosure_length.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('100000', $feed->getEntryByOffset(0)->enclosures(0, 'length'));
    }

    function test_item_enclosure_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_enclosure_multiple.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array('href' => 'http://example.com/2', 'length' => '200000', 'type' => 'image/gif'), $feed->getEntryByOffset(0)->enclosures[1]);
    }

    function test_item_enclosure_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_enclosure_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('image/jpeg', $feed->getEntryByOffset(0)->enclosures(0, 'type'));
    }

    function test_item_enclosure_url_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_enclosure_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->enclosures(0, 'url'));
    }

    function test_item_fullitem_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_fullitem.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>Example content</p>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_fullitem_mode_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_fullitem_mode.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_item_fullitem_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_fullitem_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('text/html', $feed->getEntryByOffset(0)->content(0, 'type'));
    }

    function test_item_guid_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://guid.example.com/', $feed->getEntryByOffset(0)->guid);
    }

    function test_item_guid_conflict_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_conflict_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://link.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_guid_guidislink_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_guidislink.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Expected result needs verification");
        //$this->assertEquals(, $feed->getEntryByOffset(0)->guidislink);
    }

    function test_item_guid_isPermaLink_conflict_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_isPermaLink_conflict_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://link.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_guid_isPermaLink_conflict_link_not_guidislink_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_isPermaLink_conflict_link_not_guidislink.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Expected result needs verification");
        //$this->assertEquals(, ! $feed->getEntryByOffset(0)->guidislink);
    }

    function test_item_guid_isPermaLink_guidislink_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_isPermaLink_guidislink.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Expected result needs verification");
        //$this->assertEquals(, $feed->getEntryByOffset(0)->guidislink);
    }

    function test_item_guid_isPermaLink_map_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_isPermaLink_map_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://guid.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_guid_map_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_map_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://guid.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_guid_not_permalink_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_not_permalink.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Expected result needs verification");
        //$this->assertEquals(, ! $feed->getEntryByOffset(0).has_key(->));
    }

    function test_item_guid_not_permalink_conflict_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_not_permalink_conflict_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://link.example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_guid_not_permalink_not_guidislink_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_not_permalink_not_guidislink.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Expected result needs verification");
        //$this->assertEquals(, ! $feed->getEntryByOffset(0)->guidislink);
    }

    function test_item_guid_not_permalink_not_guidislink_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_guid_not_permalink_not_guidislink_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Expected result needs verification");
        //$this->assertEquals(, ! $feed->getEntryByOffset(0)->guidislink);
    }

    function test_item_link_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_link.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('http://example.com/', $feed->getEntryByOffset(0)->link);
    }

    function test_item_source_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_source.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_item_source_url_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_source_url.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_item_summary_and_description_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_summary_and_description.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example summary', $feed->getEntryByOffset(0)->summary);
    }

    function test_item_summary_and_description_2() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_summary_and_description.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Example description', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_title_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_title.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Item 1 title', $feed->getEntryByOffset(0)->title);
    }

    function test_item_xhtml_body_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_xhtml_body.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('<p>Example content</p>', $feed->getEntryByOffset(0)->content(0, 'value'));
    }

    function test_item_xhtml_body_mode_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_xhtml_body_mode.xml');

        $feed = new XML_Feed_Parser($content);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_item_xhtml_body_type_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/item_xhtml_body_type.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('application/xhtml+xml', $feed->getEntryByOffset(0)->content(0, 'type'));
    }

    function test_rss_namespace_1_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_namespace_1.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Example description', $feed->description);
    }

    function test_rss_namespace_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_namespace_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Example description', $feed->description);
    }

    function test_rss_namespace_3_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_namespace_3.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Example description', $feed->description);
    }

    function test_rss_namespace_4_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_namespace_4.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Example description', $feed->description);
    }

    function test_rss_version_090_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_090.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertVersionMostlyCorrect('rss090', $feed->version());
    }

    function test_rss_version_091_netscape_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_091_netscape.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertVersionMostlyCorrect('rss20', $feed->version(), "Expected to load RSS 2.0 driver, because 0.92 is deprecated");
    }

    function test_rss_version_091_userland_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_091_userland.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertVersionMostlyCorrect('rss20', $feed->version(), "Expected to load RSS 2.0 driver, because 0.91 is deprecated");
    }

    function test_rss_version_092_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_092.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertVersionMostlyCorrect('rss20', $feed->version(), "Expected to load RSS 2.0 driver, because 0.92 is deprecated");
    }

    function test_rss_version_093_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_093.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertVersionMostlyCorrect('rss20', $feed->version(), "Expected to load RSS 2.0 driver, because 0.93 is deprecated");
    }

    function test_rss_version_094_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_094.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertVersionMostlyCorrect('rss20', $feed->version(), "Expected to load RSS 2.0 driver, because 0.94 is deprecated");
    }

    function test_rss_version_20_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_20.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertVersionMostlyCorrect('rss20', $feed->version());
    }

    function test_rss_version_201_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_201.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertVersionMostlyCorrect('rss20', $feed->version());
    }

    function test_rss_version_21_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_21.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertVersionMostlyCorrect('rss20', $feed->version());
    }

    function test_rss_version_missing_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/rss/rss_version_missing.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertVersionMostlyCorrect('rss', $feed->version());
    }
}
