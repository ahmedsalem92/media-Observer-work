<?php

class ilkhacom extends plugin_base
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
                    '/<li[^>]*>\s*<a[^>]*>Haberler(.*)<\/ul>/Uis',
                    '/(<a.*<\/a>)/Uis'
                ),
                'append_domain' => true
            )
        ),
        'section' => array(
            'link' => '/href="([^"]*)"/Uis',
            'name' => '/<a[^<]*>(.*)<\/a>/Uis',
            'append_domain' => true,
            'process_link' => 'filter_sections'
        )
    );


    protected $logic = array(
        'list1' => array(
            0 => array(
                'type' => 'list1',
                'regexp' => '/^(.*)$/Uis',
                'append_domain' => true,
                'process_link' => 'process_list2_link'
            ),
            1 => array(
                'type' => 'article',
                'regexp' => [
                    '/<div class="about-post-items backroundcolor-white-ss">(.*)<div class="col-lg-4"/Uis',
                    '/<a href="(.*)"/Uis',
                ],
                'append_domain' => true,
            )
        ),
        'article' => array(
            'headline' => '/<div class="post-content">\s*<h3 class="title">(.*)<\/h3>/Uis',
            'content' => '/<div class="post-text[^>]*>(.*)<div id="relatedNews/Uis',
            'author' => false,
            'article_date' => '/<ul class="author-social">\s*<li class="cat-red">(.*)</Uis'
        )
    );


    protected $logic_home = array(
        'list1' => array(
            0 => array(
                'type' => 'article',
                'regexp' => '/<h(?:2|3|4|5) class="title short-titles2"[^>]*>\s*<a[^>]*href="([^"]*)"/Uis',
                'append_domain' => true,
                'ignore_terminal_stop' => true
            ),
            1 => array(
                'type' => 'article',
                'regexp' => '/<div class="feature-news-content">\s*<a[^>]*href="([^"]*)"/Uis',
                'append_domain' => true,
                'ignore_terminal_stop' => true
            ),
            2 => array(
                'type' => 'article',
                'regexp' => '/<h3 class="title">\s*<a[^>]*href="([^"]*)"/Uis',
                'append_domain' => true,
                'ignore_terminal_stop' => true
            )
        ),
        'article' => array(
            'headline' => '/<div class="post-content">\s*<h3 class="title">(.*)<\/h3>/Uis',
            'content' => '/<div class="post-text[^>]*>(.*)<div id="relatedNews/Uis',
            'author' => false,
            'article_date' => '/<ul class="author-social">\s*<li class="cat-red">(.*)</Uis'
        )
    );


    public function prepare_home($section_id)
    {
        $this->logic = $this->logic_home;
    }

    protected function process_list2_link($link, $referer_link, $logic_type)
    {


        $this->default_postback['firstItem'] += 10;
        $fake_link =  $this->list2_link . '?firstItem=' . $this->default_postback['firstItem'];
        $this->postbacks[$fake_link] = $this->default_postback;
        return $fake_link;
    }


    // process the date of the article, return in YYYY-MM-DD HH:ii:ss format
    protected function process_date($article_date)
    {
        //19.10.2023 10:24:40
        if (preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4}) /Uis', $article_date, $matches)) {
            $article_date_obj = DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $matches[3] . '-' . $matches[2] . '-' . $matches[1] . ' 16:00:00',
                new DateTimeZone($this->site_timezone)
            );
            $article_date = $article_date_obj->format('Y-m-d H:i:s');
        }

        return $article_date;
    }
}
