<?php

require_once 'XML_Feed_Parser_TestCase.php';

class japanese extends XML_Feed_Parser_TestCase
{
    function setUp()
    {
        $sample_dir = XML_Feed_Parser_TestCase::getSampleDir();
        $this->file = file_get_contents($sample_dir . DIRECTORY_SEPARATOR . "sixapart-jp.xml");
        $this->feed = new XML_Feed_Parser($this->file);
        $this->entry = $this->feed->getEntryByOffset(0);
    }

    function test_itemTitleJapanese()
    {
        $value = "ファイブ・ディーが、Movable Typeでブログプロモーションをスタート";
        $this->assertEquals($value, $this->entry->title);
    }
    
    function test_itemDescriptionJapanese()
    {
        $value = "<p><img alt=\"MIYAZAWAblog_banner.jpg\" src=\"http://www.sixapart.jp/MIYAZAWAblog_banner.jpg\" width=\"200\" height=\"88\" align=\"right\" /><br />
ファイブ・ディーは、Movable Typeで構築したプロモーション ブログ『宮沢和史 中南米ツアーblog Latin America 2005』を開設しました。</p>

<p>9月21日に開設されたこのブログは、ブラジル、ホンジュラス、ニカラグア、メキシコ、キューバの5か国を巡る「Latin America 2005」ツアーに合わせ、そのツアーの模様を同行マネージャーがレポートしていきます。<br />
さらに今月2日からは宮沢和史自身が日々録音した声をPodcastingするという点でも、ブログを使ったユニークなプロモーションとなっています。</p>

<p><a href=\"http://www.five-d.co.jp/miyazawa/jp/blog/la2005/\">「宮沢和史 中南米ツアーblog Latin America 2005」</a></p>

<p>※シックス・アパートではこうしたブログを使ったプロモーションに最適な製品をご用意しております。<br />
<ul><li><a href=\"/movabletype/\">Movable Type</a><br />
<li><a href=\"/typepad/typepad_promotion.html\">TypePad Promotion</a><br />
</ul></p>";
        $this->assertEquals($value, $this->entry->description);
    }
}

?>