<?php

class onemileatatimecom extends plugin_base {

	// ANT settings
	protected $ant_precision = 6;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';
	protected $use_proxies = true;


	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link',
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content mb-0 position-relative">(.*)<!-- .entry-content -->/Uis',
			'author' => false,
			'article_date' => '/dateModified":"(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = ''; //https://onemileatatime.com/post-sitemap19.xml
		if (preg_match_all('/<loc>(https:\/\/onemileatatime\.com\/post-sitemap\d+?\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}


	protected $logic_deals = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_deals',
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content mb-0 position-relative">(.*)<!-- .entry-content -->/Uis',
			'author' => false,
			'article_date' => '/dateModified":"(.*)"/Uis'
		)
	);

	protected function process_list1_link_deals($link, $referer_link, $logic)
	{

		$temp_link = ''; //https://onemileatatime.com/deals-sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/onemileatatime\.com\/deals-sitemap\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}

	public function prepare_deals($section_id) {

		$this->logic = $this->logic_deals;

	}

	protected $logic_guides = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_guides',
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content mb-0 position-relative">(.*)<!-- .entry-content -->/Uis',
			'author' => false,
			'article_date' => '/dateModified":"(.*)"/Uis'
		)
	);

	protected function process_list1_link_guides($link, $referer_link, $logic)
	{

		$temp_link = ''; //https://onemileatatime.com/guides-sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/onemileatatime\.com\/guides-sitemap\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}

	public function prepare_guides($section_id) {

		$this->logic = $this->logic_guides;

	}

	protected $logic_insights = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_insights',
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content mb-0 position-relative">(.*)<!-- .entry-content -->/Uis',
			'author' => false,
			'article_date' => '/dateModified":"(.*)"/Uis'
		)
	);

	protected function process_list1_link_insights($link, $referer_link, $logic)
	{

		$temp_link = ''; //https://onemileatatime.com/insights-sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/onemileatatime\.com\/insights-sitemap\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}

	public function prepare_insights($section_id) {

		$this->logic = $this->logic_insights;

	}

	protected $logic_news = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_news',
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content mb-0 position-relative">(.*)<!-- .entry-content -->/Uis',
			'author' => false,
			'article_date' => '/dateModified":"(.*)"/Uis'
		)
	);

	protected function process_list1_link_news($link, $referer_link, $logic)
	{

		$temp_link = ''; //https://onemileatatime.com/news-sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/onemileatatime\.com\/news-sitemap\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}

	public function prepare_news($section_id) {

		$this->logic = $this->logic_insights;

	}

	protected $logic_reviews = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_reviews',
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content mb-0 position-relative">(.*)<!-- .entry-content -->/Uis',
			'author' => false,
			'article_date' => '/dateModified":"(.*)"/Uis'
		)
	);

	protected function process_list1_link_reviews($link, $referer_link, $logic)
	{

		$temp_link = ''; //https://onemileatatime.com/reviews-sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/onemileatatime\.com\/reviews-sitemap\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}

	public function prepare_reviews($section_id) {

		$this->logic = $this->logic_insights;

	}

	private $links = array();
	private $array_index = 0;

	protected function process_article_link($link, $referer_link, $logic)
	{

		$temp_link = '';
		if (empty($this->links)) {
			$result = $this->ant->get($referer_link);
			if (preg_match_all('/<loc>(.*)<\/loc>/Uis', $result, $matches)) {
				$this->links = $matches[0];
				$this->array_index = sizeof($this->links);
			}
		}
		$this->array_index--;
		if ($this->array_index > 0 and isset($this->links[$this->array_index])) {
			$temp_link = str_replace('<loc>', '', $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>', '', $temp_link);
			return $temp_link;
		}

		return '';
	}


	protected function process_content($content, $article_data)
	{
		$content = preg_replace('/(Share)/Uis', '', $content);
		$content = preg_replace('/(Tweet)/Uis', '', $content);
		return $content;
	}


	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2024-07-30T06:29:14+00:00
		if (preg_match('/(.*)T(.*)\+/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}

}
