<?php

require_once dirname(dirname(__FILE__)) . '/XML_Feed_Parser_TestCase.php';

class itunes_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_itunes_channel_block_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_block.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(1, $feed->itunes_block);
    }

    function test_itunes_channel_block_false_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_block_false.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->itunes_block);
    }

    function test_itunes_channel_block_no_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_block_no.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->itunes_block);
    }

    function test_itunes_channel_block_true_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_block_true.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->itunes_block);
    }

    function test_itunes_channel_block_uppercase_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_block_uppercase.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->itunes_block);
    }

    function test_itunes_channel_block_whitespace_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_block_whitespace.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->itunes_block);
    }

    function test_itunes_channel_category_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_category.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Technology', $feed->tags(0, 'term'));
    }

    function test_itunes_channel_category_nested_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_category_nested.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Gadgets', $feed->tags(0, 'term'));
    }

    function test_itunes_channel_category_scheme_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_category_scheme.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('http://www.itunes.com/', $feed->tags(0, 'scheme'));
    }

    function test_itunes_channel_explicit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_explicit.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(1, $feed->itunes_explicit);
    }

    function test_itunes_channel_explicit_false_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_explicit_false.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->itunes_explicit);
    }

    function test_itunes_channel_explicit_no_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_explicit_no.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->itunes_explicit);
    }

    function test_itunes_channel_explicit_true_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_explicit_true.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->itunes_explicit);
    }

    function test_itunes_channel_explicit_uppercase_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_explicit_uppercase.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->itunes_explicit);
    }

    function test_itunes_channel_explicit_whitespace_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_explicit_whitespace.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->itunes_explicit);
    }

    function test_itunes_channel_image_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_image.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('http://example.com/logo.jpg', $feed->image(0, 'href'));
    }

    function test_itunes_channel_keywords_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_keywords.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Technology', $feed->tags(0, 'term'));
    }

    function test_itunes_channel_keywords_duplicate_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_keywords_duplicate.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(1, count($feed->tags));
    }

    function test_itunes_channel_keywords_duplicate_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_keywords_duplicate_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(1, count($feed->tags));
    }

    function test_itunes_channel_keywords_multiple_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_keywords_multiple.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Gadgets', $feed->tags(0, 'term'));
    }

    function test_itunes_channel_link_image_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_link_image.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('http://example.com/logo.jpg', $feed->image(0, 'href'));
    }

    function test_itunes_channel_owner_email_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_owner_email.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('mark@example.com', $feed->publisher(0, 'email'));
    }

    function test_itunes_channel_owner_name_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_owner_name.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Mark Pilgrim', $feed->publisher(0, 'name'));
    }

    function test_itunes_channel_subtitle_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_subtitle.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Example subtitle', $feed->subtitle);
    }

    function test_itunes_channel_summary_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_channel_summary.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Example summary', $feed->description);
    }

    function test_itunes_core_element_uppercase_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_core_element_uppercase.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Example title', $feed->title);
    }

    function test_itunes_enclosure_url_maps_id_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_enclosure_url_maps_id.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('http://example.com/movie.mp4', $feed->getEntryByOffset(0)->id);
    }

    function test_itunes_enclosure_url_maps_id_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_enclosure_url_maps_id_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('http://example.com/id', $feed->getEntryByOffset(0)->id);
    }

    function test_itunes_item_author_map_author_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_author_map_author.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Mark Pilgrim', $feed->getEntryByOffset(0)->author);
    }

    function test_itunes_item_block_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_block.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(1, $feed->getEntryByOffset(0)->itunes_block);
    }

    function test_itunes_item_block_false_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_block_false.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_block);
    }

    function test_itunes_item_block_no_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_block_no.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_block);
    }

    function test_itunes_item_block_true_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_block_true.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_block);
    }

    function test_itunes_item_block_uppercase_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_block_uppercase.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_block);
    }

    function test_itunes_item_block_whitespace_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_block_whitespace.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_block);
    }

    function test_itunes_item_category_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_category.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Technology', $feed->getEntryByOffset(0)->tags(0, 'term'));
    }

    function test_itunes_item_category_nested_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_category_nested.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Gadgets', $feed->getEntryByOffset(0)->tags(0, 'term'));
    }

    function test_itunes_item_category_scheme_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_category_scheme.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('http://www.itunes.com/', $feed->getEntryByOffset(0)->tags(0, 'scheme'));
    }

    function test_itunes_item_duration_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_duration.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('3:00', $feed->getEntryByOffset(0)->itunes_duration);
    }

    function test_itunes_item_explicit_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_explicit.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(1, $feed->getEntryByOffset(0)->itunes_explicit);
    }

    function test_itunes_item_explicit_false_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_explicit_false.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_explicit);
    }

    function test_itunes_item_explicit_no_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_explicit_no.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_explicit);
    }

    function test_itunes_item_explicit_true_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_explicit_true.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_explicit);
    }

    function test_itunes_item_explicit_uppercase_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_explicit_uppercase.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_explicit);
    }

    function test_itunes_item_explicit_whitespace_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_explicit_whitespace.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(0, $feed->getEntryByOffset(0)->itunes_explicit);
    }

    function test_itunes_item_image_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_image.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('http://example.com/logo.jpg', $feed->getEntryByOffset(0)->image(0, 'href'));
    }

    function test_itunes_item_link_image_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_link_image.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('http://example.com/logo.jpg', $feed->getEntryByOffset(0)->image(0, 'href'));
    }

    function test_itunes_item_subtitle_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_subtitle.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Example subtitle', $feed->getEntryByOffset(0)->subtitle);
    }

    function test_itunes_item_summary_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_item_summary.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Example summary', $feed->getEntryByOffset(0)->summary);
    }

    function test_itunes_link_enclosure_maps_id_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_link_enclosure_maps_id.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('http://example.com/movie.mp4', $feed->getEntryByOffset(0)->id);
    }

    function test_itunes_link_enclosure_maps_id_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_link_enclosure_maps_id_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('http://example.com/id', $feed->getEntryByOffset(0)->id);
    }

    function test_itunes_namespace_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_namespace.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(1, $feed->itunes_block);
    }

    function test_itunes_namespace_example_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_namespace_example.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(1, $feed->itunes_block);
    }

    function test_itunes_namespace_lowercase_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_namespace_lowercase.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(1, $feed->itunes_block);
    }

    function test_itunes_namespace_uppercase_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/itunes/itunes_namespace_uppercase.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(1, $feed->itunes_block);
    }
}

?>
