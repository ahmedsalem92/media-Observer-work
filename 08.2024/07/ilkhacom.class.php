<?php

class ilkhacom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0';
	protected $use_headless = true;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = true;
	private $exclude_sections = array('https://ilkha.com/bilim-&-teknoloji', 'BİLİM &amp; TEKNOLOJİ');
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/<li[^>]*>\s*<a[^>]*>Haberler(.*)<\/ul>/Uis',
					'/(<a.*<\/a>)/Uis'
				)
			)
		),
		'section' => array(
			'link' => '/href="([^"]*)"/Uis',
			'name' => '/<a[^<]*>(.*)<\/a>/Uis',
			'append_domain' => true,
			'process_link' => 'filter_sections'
		)
	);

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' =>  false,
				'process_link' => 'process_list1_link'
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<div class="about-post-items backroundcolor-white-ss">(.*)<div class="fixed-bottom">/Uis',
					'/<div class="bussiness-post-thumb">.*<a href=".*"/Uis',
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
	private $postbacks = array();

	private $collection_link = false;
	private $default_postback = array(
		'sortOrder' => "ascending",
		'searchString' => "",
		'langId' => 1,
		'langUrl' => "",
		'firstItem' => 0
	);




	protected function process_list1_link($link, $referer_link, $logic_type)
	{

		if (preg_match('/ searchString: "([^"]*)"/Uis', $link, $matches)) {
			$this->default_postback['searchString'] = $matches[1];
		}
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



	protected function pre_get_page(&$page)
	{

		// when the page link has defined POST parameters
		if (array_key_exists($page, $this->postbacks)) {
			$this->collection_link = true;
			// set the POST parameters
			$this->ant->set_post($this->postbacks[$page]);
		} else {
			$this->collection_link = false;
			$this->ant->unset_post();
		}
	}

	// after page is retrieved, unset the post so the next request post can be set correctly (when defined)
	protected function post_get_page(&$result)
	{

		$this->ant->unset_post();
		$this->collection_link = false;
	}
}
