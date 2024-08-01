<?php

class ttgmena extends plugin_base
{

    // ANT settings
    protected $ant_precision = 2;
    protected $stop_on_date = true;
    protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';

    // CRAWL settings
    protected $stop_on_article_found = false;
    protected $stop_date_override = true;

    // DEFINITIONS
    protected $site_timezone = 'Asia/Amman';
    private $exclude_sections = array();
    protected $sections = array(
        'list1' => array(
            0 => array(
                'type' => 'section',
                'regexp' => array(
                    '/<div id="navFixed" class="mod-navigation">(.*)<a href="javascript:;">IDIOMAS/Uis',
                    '/(<a.*<\/a>)/Uis'
                ),
                'append_domain' => true
            ),
            1 => array(
                'type' => 'section',
                'regexp' => '/<div class="mod-tit">(<a.*<\/a>)/Uis',
                'append_domain' => true
            )
        ),
        'section' => array(
            'link' => '/href="([^"]*)"/Uis',
            'name' => '/(?:<\/i>|<a[^<]*>)([^<]*)<\/a>/Uis',
            'append_domain' => true,
            'process_link' => 'filter_sections'
        )
    );

    protected $logic = array(
        'list1' => array(
            0 => array(
                'type' => 'list1',
                'regexp' => '/<a href="([^<]*)"[^<]*rel="next">/Uis',
                'append_domain' => true
            ),
            1 => array(
                'type' => 'article',
                'regexp' => '/<a href="([^<]*\/)-[^<]*">\s* المزيد<\/a>/Uis',
                'append_domain' => false
            )
        ),
        'article' => array(
            'headline' => '/<title>(.*)<\/title>/Uis',
            'content' => '/(?:<p _ngcontent-[^<]*="" class="news-article__paragraph">|<div id="DetailsNews" >)(.*)(?:<div class="tags">|<div _ngcontent-[^<]*="" class="news-article__col-related-news">|<div class="related-news-inner">)/Uis',
            'author' => '/<meta property="article:author" content="(.*)"/Uis',
            'article_date' => '/<p class="short-head"><\/p>.*<ul>\s*<li>.*<li><a[^<]*>(.*)<\/a>/Uis'
        )
    );
    protected $logic_home = array(
        'list1' => array(
            0 => array(
                'type' => 'article',
                'regexp' => '/<h2 class="entry-title"><a href="(.*)"/Uis',
                'append_domain' => true,
                'ignore_terminal_stop' => true
            ),
            1 => array(
                'type' => 'article',
                'regexp' => '/<h2 class="entry-title"><a href="([^"]*)"/Uis',
                'append_domain' => false,
                'ignore_terminal_stop' => true
            ),
            2 => array(
                'type' => 'article',
                'regexp' => '/<h4 class="mb-0"><a href="([^"]*)"/Uis',
                'append_domain' => false,
                'ignore_terminal_stop' => true
            )
        ),
        'article' => array(
            'headline' => '/(<h1 class="post-title entry-title">.*<\/h1>)/Uis',
            'content' => '/(?:<div class="entry-content entry[^<]*>|<p _ngcontent-[^<]*="" class="news-article__paragraph">)(.*)(?:div id=\'jp-relatedposts\'|<div _ngcontent-[^<]*="" class="news-article__col-related-news">|<div class="addtoany_share_save[^<]*>)/Uis',
            'author' => '/<span class="meta-author"><a[^<]*>(.*)<\/a>/Uis',
            'article_date' => '/<meta property="article:published_time" content="([^"]*)"/Uis'
        )
    );

    protected function process_content($content, $article_data)
    {

        $content = preg_replace('/(<div class="container signup.*<\/div>)/Uis', '', $content);
        $content = preg_replace('/(<div class="form-button.*<\/div>)/Uis', '', $content);
        $content = preg_replace('/(<div class="newsletter.*<\/div>)/Uis', '', $content);
        $content = preg_replace('/(<div class="newsletter-subscribe">.*<\/div>)/Uis', '', $content);
        $content = preg_replace('/(<iframe.*<\/iframe>)/Uis', 'VIDEO', $content);
        return $content;
    }


    protected function process_headline($headline, $article_data)
    {

        $headline = preg_replace('/(<span class="post-title" itemprop="headline"><\/span>)/Uis', 'No Headline', $headline);
        return $headline;
    }

    public function prepare_home($section_id)
    {

        $this->logic = $this->logic_home;
    }




    protected function filter_sections($section_link, $section_name, $referer_link, $logic)
    {

        // exclude these sections
        if (in_array(trim($section_name), $this->exclude_sections)) {
            return '';
        }

        return $section_link;
    }

    // process the date of the article, return in YYYY-MM-DD HH:ii:ss format
    protected function process_date($article_date)
    {

        //2018-05-21T08:20:26+00:00
        if (preg_match('/(.*)T/Uis', $article_date, $matches)) {
            $article_date_obj = DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $matches[1] . ' 16:00:00',
                new DateTimeZone($this->site_timezone)
            );
            $article_date = $article_date_obj->format('Y-m-d H:i:s');
        }

        return $article_date;
    }
}
