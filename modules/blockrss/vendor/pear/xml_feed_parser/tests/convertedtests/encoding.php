<?php

require_once dirname(dirname(__FILE__)) . '/XML_Feed_Parser_TestCase.php';

class encoding_TestCase extends XML_Feed_Parser_Converted_TestCase {

    function test_csucs4_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/csucs4.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_csunicode_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/csunicode.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_encoding_attribute_crash_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/encoding_attribute_crash.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_encoding_attribute_crash_2_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/encoding_attribute_crash_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Not yet implemented");
    }

    function test_euc_kr_attribute_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/euc-kr-attribute.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('<img alt="\ub144" />', $feed->getEntryByOffset(0)->description);
    }

    function test_euc_kr_item_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/euc-kr-item.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\ub144', $feed->getEntryByOffset(0)->description);
    }

    function test_euc_kr_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/euc-kr.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\ub144', $feed->title);
    }

    function test_http_text_xml_charset_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/http_text_xml_charset_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('This is a \xa3\u201ctest.\u201d', $feed->getEntryByOffset(0)->description);
    }

    function test_http_text_xml_charset_overrides_encoding_2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/http_text_xml_charset_overrides_encoding_2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('This is a \xa3\u201ctest.\u201d', $feed->getEntryByOffset(0)->description);
    }

    function test_iso_10646_ucs_2_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/iso-10646-ucs-2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_iso_10646_ucs_4_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/iso-10646-ucs-4.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_u16_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/u16.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_ucs_2_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/ucs-2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_ucs_4_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/ucs-4.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_16be_autodetect_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf-16be-autodetect.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_16be_bom_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf-16be-bom.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_16be_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf-16be.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_16le_autodetect_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf-16le-autodetect.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_16le_bom_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf-16le-bom.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_16le_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf-16le.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_32be_autodetect_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf-32be-autodetect.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_32be_bom_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf-32be-bom.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_32be_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf-32be.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_32le_autodetect_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf-32le-autodetect.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_32le_bom_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf-32le-bom.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_32le_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf-32le.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf16_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf16.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_16_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf_16.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_utf_32_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/utf_32.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_437_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_437.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_850_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_850.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_852_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_852.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_855_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_855.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0452', $feed->title);
    }

    function test_x80_857_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_857.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_860_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_860.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_861_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_861.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_862_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_862.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u05d0', $feed->title);
    }

    function test_x80_863_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_863.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_865_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_865.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_866_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_866.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0410', $feed->title);
    }

    function test_x80_cp037_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp037.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_cp1125_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp1125.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0410', $feed->title);
    }

    function test_x80_cp1250_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp1250.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_cp1251_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp1251.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0402', $feed->title);
    }

    function test_x80_cp1252_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp1252.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_cp1253_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp1253.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_cp1254_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp1254.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_cp1255_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp1255.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_cp1256_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp1256.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_cp1257_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp1257.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_cp1258_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp1258.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_cp437_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp437.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_cp500_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp500.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_cp737_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp737.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0391', $feed->title);
    }

    function test_x80_cp775_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp775.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0106', $feed->title);
    }

    function test_x80_cp850_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp850.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_cp852_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp852.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_cp855_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp855.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0452', $feed->title);
    }

    function test_x80_cp856_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp856.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u05d0', $feed->title);
    }

    function test_x80_cp857_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp857.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_cp860_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp860.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_cp861_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp861.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_cp862_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp862.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u05d0', $feed->title);
    }

    function test_x80_cp863_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp863.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_cp864_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp864.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xb0', $feed->title);
    }

    function test_x80_cp865_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp865.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_cp866_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp866.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0410', $feed->title);
    }

    function test_x80_cp874_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp874.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_cp875_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp875.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_cp_is_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cp_is.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_csibm037_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_csibm037.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_csibm500_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_csibm500.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_csibm855_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_csibm855.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0452', $feed->title);
    }

    function test_x80_csibm857_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_csibm857.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_csibm860_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_csibm860.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_csibm861_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_csibm861.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_csibm863_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_csibm863.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_csibm864_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_csibm864.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xb0', $feed->title);
    }

    function test_x80_csibm865_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_csibm865.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_csibm866_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_csibm866.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0410', $feed->title);
    }

    function test_x80_cskoi8r_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cskoi8r.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u2500', $feed->title);
    }

    function test_x80_csmacintosh_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_csmacintosh.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc4', $feed->title);
    }

    function test_x80_cspc775baltic_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cspc775baltic.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0106', $feed->title);
    }

    function test_x80_cspc850multilingual_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cspc850multilingual.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_cspc862latinhebrew_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cspc862latinhebrew.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u05d0', $feed->title);
    }

    function test_x80_cspc8codepage437_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cspc8codepage437.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_cspcp852_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_cspcp852.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_dbcs_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_dbcs.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_ebcdic_cp_be_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ebcdic_cp_be.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_ebcdic_cp_ca_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ebcdic_cp_ca.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_ebcdic_cp_ch_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ebcdic_cp_ch.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_ebcdic_cp_nl_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ebcdic_cp_nl.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_ebcdic_cp_us_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ebcdic_cp_us.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_ebcdic_cp_wt_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ebcdic_cp_wt.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_ibm037_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm037.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_ibm039_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm039.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_ibm1140_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm1140.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_ibm437_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm437.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_ibm500_0() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm500.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->markTestIncomplete("Expected result needs verification");
    }

    function test_x80_ibm775_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm775.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0106', $feed->title);
    }

    function test_x80_ibm850_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm850.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_ibm852_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm852.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_ibm855_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm855.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0452', $feed->title);
    }

    function test_x80_ibm857_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm857.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_ibm860_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm860.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_ibm861_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm861.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_ibm862_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm862.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u05d0', $feed->title);
    }

    function test_x80_ibm863_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm863.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_ibm864_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm864.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xb0', $feed->title);
    }

    function test_x80_ibm865_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm865.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc7', $feed->title);
    }

    function test_x80_ibm866_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ibm866.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0410', $feed->title);
    }

    function test_x80_koi8_r_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_koi8-r.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u2500', $feed->title);
    }

    function test_x80_koi8_t_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_koi8-t.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u049b', $feed->title);
    }

    function test_x80_koi8_u_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_koi8-u.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u2500', $feed->title);
    }

    function test_x80_mac_cyrillic_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_mac-cyrillic.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0410', $feed->title);
    }

    function test_x80_mac_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_mac.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc4', $feed->title);
    }

    function test_x80_maccentraleurope_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_maccentraleurope.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc4', $feed->title);
    }

    function test_x80_maccyrillic_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_maccyrillic.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0410', $feed->title);
    }

    function test_x80_macgreek_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_macgreek.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc4', $feed->title);
    }

    function test_x80_maciceland_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_maciceland.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc4', $feed->title);
    }

    function test_x80_macintosh_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_macintosh.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc4', $feed->title);
    }

    function test_x80_maclatin2_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_maclatin2.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc4', $feed->title);
    }

    function test_x80_macroman_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_macroman.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc4', $feed->title);
    }

    function test_x80_macturkish_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_macturkish.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc4', $feed->title);
    }

    function test_x80_ms_ansi_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ms-ansi.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_ms_arab_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ms-arab.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_ms_cyrl_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ms-cyrl.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0402', $feed->title);
    }

    function test_x80_ms_ee_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ms-ee.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_ms_greek_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ms-greek.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_ms_hebr_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ms-hebr.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_ms_turk_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_ms-turk.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_tcvn_5712_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_tcvn-5712.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc0', $feed->title);
    }

    function test_x80_tcvn_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_tcvn.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc0', $feed->title);
    }

    function test_x80_tcvn5712_1_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_tcvn5712-1.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\xc0', $feed->title);
    }

    function test_x80_viscii_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_viscii.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u1ea0', $feed->title);
    }

    function test_x80_winbaltrim_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_winbaltrim.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_windows_1250_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_windows-1250.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_windows_1251_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_windows-1251.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u0402', $feed->title);
    }

    function test_x80_windows_1252_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_windows-1252.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_windows_1253_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_windows-1253.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_windows_1254_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_windows-1254.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_windows_1255_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_windows-1255.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_windows_1256_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_windows-1256.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_windows_1257_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_windows-1257.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

    function test_x80_windows_1258_1() { 
        $content = file_get_contents($this->fp_test_dir . DIRECTORY_SEPARATOR . 'wellformed/encoding/x80_windows-1258.xml');

        $feed = new XML_Feed_Parser($content, false, true);

        $this->assertEquals('\u20ac', $feed->title);
    }

}
?>
