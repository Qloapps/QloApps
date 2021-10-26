<?php

require_once dirname(dirname(__FILE__)) . '/XML_Feed_Parser_TestCase.php';

class date_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_channel_dc_date_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_dc_date.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2003-12-31T10:14:55Z', $feed->date);
    }

    function test_channel_dc_date_map_modified_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_dc_date_map_modified.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2003-12-31T10:14:55Z', $feed->modified);
    }

    function test_channel_dc_date_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_dc_date_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->date_parsed);
    }

    function test_channel_dc_date_w3dtf_utc_map_modified_parsed_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_dc_date_w3dtf_utc_map_modified_parsed.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->modified_parsed);
    }

    function test_channel_dcterms_created_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_dcterms_created.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2003-12-31T10:14:55Z', $feed->created);
    }

    function test_channel_dcterms_created_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_dcterms_created_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->created_parsed);
    }

    function test_channel_dcterms_issued_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_dcterms_issued.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2003-12-31T10:14:55Z', $feed->issued);
    }

    function test_channel_dcterms_issued_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_dcterms_issued_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->issued_parsed);
    }

    function test_channel_dcterms_modified_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_dcterms_modified.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2003-12-31T10:14:55Z', $feed->modified);
    }

    function test_channel_dcterms_modified_map_date_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_dcterms_modified_map_date.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2003-12-31T10:14:55Z', $feed->date);
    }

    function test_channel_dcterms_modified_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_dcterms_modified_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->modified_parsed);
    }

    function test_channel_dcterms_modified_w3dtf_utc_map_date_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_dcterms_modified_w3dtf_utc_map_date.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Thu, 01 Jan 2004 19:48:21 GMT', $feed->date);
    }

    function test_channel_pubDate_asctime_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_asctime.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 5, 0, 29, 6, 0, 5, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_disney_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_disney.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 26, 21, 31, 0, 0, 26, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_disney_at_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_disney_at.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 26, 20, 31, 0, 0, 26, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_disney_ct_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_disney_ct.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 26, 22, 31, 0, 0, 26, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_disney_mt_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_disney_mt.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 26, 23, 31, 0, 0, 26, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_disney_pt_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_disney_pt.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 27, 0, 31, 0, 1, 27, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_greek_1_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_greek_1.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 7, 11, 17, 0, 0, 6, 193, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_hungarian_1_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_hungarian_1.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 7, 13, 14, 15, 0, 1, 195, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_iso8601_ym_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_iso8601_ym.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 1, 0, 0, 0, 0, 335, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_iso8601_ym_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_iso8601_ym_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 1, 0, 0, 0, 0, 335, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_iso8601_ymd_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_iso8601_ymd.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 0, 0, 0, 2, 365, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_iso8601_ymd_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_iso8601_ymd_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 0, 0, 0, 2, 365, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_iso8601_yo_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_iso8601_yo_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 1, 0, 0, 0, 0, 335, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_korean_nate_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_korean_nate.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 5, 25, 14, 23, 17, 1, 146, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_map_modified_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_map_modified.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 1, 19, 48, 21, 3, 1, 0), $feed->modified_parsed);
    }

    function test_channel_pubDate_mssql_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_mssql.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 7, 8, 14, 56, 58, 3, 190, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_mssql_nofraction_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_mssql_nofraction.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 7, 8, 14, 56, 58, 3, 190, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_nosecond_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_nosecond.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 1, 0, 0, 0, 3, 1, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_notime_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_notime.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 1, 0, 0, 0, 3, 1, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_rfc2822_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_rfc2822.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 1, 19, 48, 21, 3, 1, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_rfc2822_rollover_june_31_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_rfc2822_rollover_june_31.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 7, 1, 19, 48, 21, 3, 183, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_rfc822_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_rfc822.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 1, 19, 48, 21, 3, 1, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_rollover_25h_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_rollover_25h.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 1, 1, 14, 55, 3, 1, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_rollover_61m_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_rollover_61m.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 11, 1, 55, 2, 365, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_rollover_61s_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_rollover_61s.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 15, 1, 2, 365, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_rollover_leapyear_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_rollover_leapyear.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 2, 29, 2, 14, 55, 6, 60, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_rollover_leapyear400_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_rollover_leapyear400.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2000, 2, 29, 2, 14, 55, 1, 60, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_rollover_nonleapyear_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_rollover_nonleapyear.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 3, 1, 2, 14, 55, 5, 60, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_sf_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_sf.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 18, 14, 55, 2, 365, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_tokyo_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_tokyo.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_y_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_y.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 1, 1, 0, 0, 0, 2, 1, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_ym_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_ym.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 1, 0, 0, 0, 0, 335, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_ymd_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_ymd.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 0, 0, 0, 2, 365, 0), $feed->date_parsed);
    }

    function test_channel_pubDate_w3dtf_ymd_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/channel_pubDate_w3dtf_ymd_2.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 0, 0, 0, 2, 365, 0), $feed->date_parsed);
    }

    function test_entry_created_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/entry_created.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Thu, 01 Jan 2004 19:48:21 GMT', $feed->getEntryByOffset(0)->created);
    }

    function test_entry_created_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/entry_created_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->getEntryByOffset(0)->created_parsed);
    }

    function test_entry_issued_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/entry_issued.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Thu, 01 Jan 2004 19:48:21 GMT', $feed->getEntryByOffset(0)->issued);
    }

    function test_entry_issued_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/entry_issued_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->getEntryByOffset(0)->issued_parsed);
    }

    function test_entry_modified_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/entry_modified.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Thu, 01 Jan 2004 19:48:21 GMT', $feed->getEntryByOffset(0)->modified);
    }

    function test_entry_modified_map_date_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/entry_modified_map_date.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2004, 1, 1, 19, 48, 21, 3, 1, 0), $feed->getEntryByOffset(0)->date_parsed);
    }

    function test_entry_modified_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/entry_modified_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->getEntryByOffset(0)->modified_parsed);
    }

    function test_entry_published_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/entry_published_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->getEntryByOffset(0)->published_parsed);
    }

    function test_entry_source_updated_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/entry_source_updated_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->getEntryByOffset(0)->source(0, 'updated_parsed'));
    }

    function test_entry_updated_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/entry_updated_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->getEntryByOffset(0)->updated_parsed);
    }

    function test_feed_modified_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('Thu, 01 Jan 2004 19:48:21 GMT', $feed->modified);
    }

    function test_feed_modified_asctime_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_asctime.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2004, 1, 5, 0, 29, 6, 0, 5, 0), $feed->modified_parsed);
    }

    function test_feed_modified_disney_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_disney.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2004, 1, 26, 21, 31, 0, 0, 26, 0), $feed->date_parsed);
    }

    function test_feed_modified_disney_at_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_disney_at.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2004, 1, 26, 20, 31, 0, 0, 26, 0), $feed->date_parsed);
    }

    function test_feed_modified_disney_ct_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_disney_ct.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2004, 1, 26, 22, 31, 0, 0, 26, 0), $feed->date_parsed);
    }

    function test_feed_modified_disney_mt_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_disney_mt.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2004, 1, 26, 23, 31, 0, 0, 26, 0), $feed->date_parsed);
    }

    function test_feed_modified_disney_pt_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_disney_pt.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2004, 1, 27, 0, 31, 0, 1, 27, 0), $feed->date_parsed);
    }

    function test_feed_modified_iso8601_ym_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_iso8601_ym.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 1, 0, 0, 0, 0, 335, 0), $feed->modified_parsed);
    }

    function test_feed_modified_iso8601_ym_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_iso8601_ym_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 1, 0, 0, 0, 0, 335, 0), $feed->modified_parsed);
    }

    function test_feed_modified_iso8601_ymd_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_iso8601_ymd.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 31, 0, 0, 0, 2, 365, 0), $feed->modified_parsed);
    }

    function test_feed_modified_iso8601_ymd_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_iso8601_ymd_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 31, 0, 0, 0, 2, 365, 0), $feed->modified_parsed);
    }

    function test_feed_modified_iso8601_yo_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_iso8601_yo_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 1, 0, 0, 0, 0, 335, 0), $feed->modified_parsed);
    }

    function test_feed_modified_map_date_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_map_date.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2004, 1, 1, 19, 48, 21, 3, 1, 0), $feed->date_parsed);
    }

    function test_feed_modified_rfc2822_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_rfc2822.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2004, 1, 1, 19, 48, 21, 3, 1, 0), $feed->modified_parsed);
    }

    function test_feed_modified_rfc2822_rollover_june_31_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_rfc2822_rollover_june_31.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2004, 7, 1, 19, 48, 21, 3, 183, 0), $feed->modified_parsed);
    }

    function test_feed_modified_rfc822_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_rfc822.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2004, 1, 1, 19, 48, 21, 3, 1, 0), $feed->modified_parsed);
    }

    function test_feed_modified_w3dtf_rollover_leapyear_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_w3dtf_rollover_leapyear.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2004, 2, 29, 2, 14, 55, 6, 60, 0), $feed->modified_parsed);
    }

    function test_feed_modified_w3dtf_rollover_leapyear400_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_w3dtf_rollover_leapyear400.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2000, 2, 29, 2, 14, 55, 1, 60, 0), $feed->modified_parsed);
    }

    function test_feed_modified_w3dtf_rollover_nonleapyear_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_w3dtf_rollover_nonleapyear.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 3, 1, 2, 14, 55, 5, 60, 0), $feed->modified_parsed);
    }

    function test_feed_modified_w3dtf_sf_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_w3dtf_sf.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 31, 18, 14, 55, 2, 365, 0), $feed->modified_parsed);
    }

    function test_feed_modified_w3dtf_tokyo_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_w3dtf_tokyo.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->modified_parsed);
    }

    function test_feed_modified_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->modified_parsed);
    }

    function test_feed_modified_w3dtf_y_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_w3dtf_y.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 1, 1, 0, 0, 0, 2, 1, 0), $feed->modified_parsed);
    }

    function test_feed_modified_w3dtf_ym_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_w3dtf_ym.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 1, 0, 0, 0, 0, 335, 0), $feed->modified_parsed);
    }

    function test_feed_modified_w3dtf_ymd_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_w3dtf_ymd.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 31, 0, 0, 0, 2, 365, 0), $feed->modified_parsed);
    }

    function test_feed_modified_w3dtf_ymd_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_modified_w3dtf_ymd_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals(array(2003, 12, 31, 0, 0, 0, 2, 365, 0), $feed->modified_parsed);
    }

    function test_feed_updated_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/feed_updated_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->updated_parsed);
    }

    function test_item_dc_date_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_dc_date.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2003-12-31T10:14:55Z', $feed->getEntryByOffset(0)->date);
    }

    function test_item_dc_date_map_modified_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_dc_date_map_modified.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2003-12-31T10:14:55Z', $feed->getEntryByOffset(0)->modified);
    }

    function test_item_dc_date_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_dc_date_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->getEntryByOffset(0)->date_parsed);
    }

    function test_item_dc_date_w3dtf_utc_map_modified_parsed_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_dc_date_w3dtf_utc_map_modified_parsed.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->getEntryByOffset(0)->modified_parsed);
    }

    function test_item_dcterms_created_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_dcterms_created.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2003-12-31T10:14:55Z', $feed->getEntryByOffset(0)->created);
    }

    function test_item_dcterms_created_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_dcterms_created_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->getEntryByOffset(0)->created_parsed);
    }

    function test_item_dcterms_issued_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_dcterms_issued.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2003-12-31T10:14:55Z', $feed->getEntryByOffset(0)->issued);
    }

    function test_item_dcterms_issued_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_dcterms_issued_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->getEntryByOffset(0)->issued_parsed);
    }

    function test_item_dcterms_modified_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_dcterms_modified.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2003-12-31T10:14:55Z', $feed->getEntryByOffset(0)->modified);
    }

    function test_item_dcterms_modified_map_date_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_dcterms_modified_map_date.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('2003-12-31T10:14:55Z', $feed->getEntryByOffset(0)->date);
    }

    function test_item_dcterms_modified_w3dtf_utc_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_dcterms_modified_w3dtf_utc.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->getEntryByOffset(0)->modified_parsed);
    }

    function test_item_dcterms_modified_w3dtf_utc_map_date_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_dcterms_modified_w3dtf_utc_map_date.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2003, 12, 31, 10, 14, 55, 2, 365, 0), $feed->getEntryByOffset(0)->date_parsed);
    }

    function test_item_expirationDate_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_expirationDate.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Thu, 01 Jan 2004 19:48:21 GMT', $feed->getEntryByOffset(0)->expired);
    }

    function test_item_expirationDate_rfc2822_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_expirationDate_rfc2822.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 1, 19, 48, 21, 3, 1, 0), $feed->getEntryByOffset(0)->expired_parsed);
    }

    function test_item_pubDate_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_pubDate.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals('Thu, 01 Jan 2004 19:48:21 GMT', $feed->getEntryByOffset(0)->date);
    }

    function test_item_pubDate_euc_kr_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_pubDate_euc-kr.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 5, 27, 16, 31, 15, 3, 148, 0), $feed->getEntryByOffset(0)->modified_parsed);
    }

    function test_item_pubDate_map_modified_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_pubDate_map_modified.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 1, 19, 48, 21, 3, 1, 0), $feed->getEntryByOffset(0)->modified_parsed);
    }

    function test_item_pubDate_rfc2822_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/date/item_pubDate_rfc2822.xml');

        $feed = new XML_Feed_Parser($content);

        $this->assertEquals(array(2004, 1, 1, 19, 48, 21, 3, 1, 0), $feed->getEntryByOffset(0)->date_parsed);
    }
}
?>
