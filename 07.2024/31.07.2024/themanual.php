<?php

class thegulfheraldcom extends plugin_base
{

	// ANT settings
	protected $ant_precision = 6;
	protected $stop_on_date = false;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';

	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;

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
			'content' => '/<article.*>(.*)(?:<\/article>|<div class="b-related-links h-editors-recs">)/Uis',
			'author' => false,
			'article_date' => '/datePublished":"(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = ''; // https://www.themanual.com/sitemap-all-content_1.xml
		if (preg_match_all('/<loc>(https:\/\/www\.themanual\.com\/sitemap-all-content_\d+?\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
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
			'content' => '/<article.*>(.*)<div class="b-related-links h-editors-recs">/Uis',
			'author' => false,
			'article_date' => '/datePublished":"(.*)"/Uis'
		)
	);

	protected function process_list1_link_deals($link, $referer_link, $logic)
	{
		$temp_link = ''; // Example: https://www.themanual.com/sitemap-deals-sitemap_2024_07.xml
		if (preg_match_all('/<loc>(https:\/\/www\.themanual\.com\/sitemap-deals-sitemap_\d+?_\d+?\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}
	
		return $temp_link;
	}

	public function prepare_deals($section_id) {

		$this->logic = $this->logic_deals;

	}


	protected $logic_google = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_google',
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
			'content' => '/<article.*>(.*)<div class="b-related-links h-editors-recs">/Uis',
			'author' => false,
			'article_date' => '/datePublished":"(.*)"/Uis'
		)
	);

	protected function process_list1_link_google($link, $referer_link, $logic)
	{
		$temp_link = ''; // Example: https://www.themanual.com/sitemap-google-news_1.xml
		if (preg_match_all('/<loc>(https:\/\/www\.themanual\.com\/sitemap-google-news_\d+?\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}
	
		return $temp_link;
	}

	public function prepare_google($section_id) {

		$this->logic = $this->logic_google;

	}


	protected $logic_latest = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_latest',
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
			'content' => '/<article.*>(.*)<div class="b-related-links h-editors-recs">/Uis',
			'author' => false,
			'article_date' => '/datePublished":"(.*)"/Uis'
		)
	);

	protected function process_list1_link_latest($link, $referer_link, $logic)
	{
		$temp_link = ''; // Example: https://www.themanual.com/sitemap-latest-500_1.xml
		if (preg_match_all('/<loc>(https:\/\/www\.themanual\.com\/sitemap-latest-\d+?_\d+?\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}
	
		return $temp_link;
	}

	public function prepare_latest($section_id) {

		$this->logic = $this->logic_latest;

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
			'content' => '/<article.*>(.*)<div class="b-related-links h-editors-recs">/Uis',
			'author' => false,
			'article_date' => '/datePublished":"(.*)"/Uis'
		)
	);

	protected function process_list1_link_news($link, $referer_link, $logic)
	{
		$temp_link = ''; // Example: https://www.themanual.com/sitemap-news-sitemap_2024_07.xml
		if (preg_match_all('/<loc>(https:\/\/www\.themanual\.com\/sitemap-news-sitemap_\d+?_\d+?\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}
	
		return $temp_link;
	}

	public function prepare_news($section_id) {

		$this->logic = $this->logic_news;

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
				// Reset the index for the first link
				$this->array_index = 0;
			}
		}
	
		// Check if there are links available
		if ($this->array_index < sizeof($this->links) && isset($this->links[$this->array_index])) {
			$temp_link = str_replace('<loc>', '', $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>', '', $temp_link);
			
			// Increment the index for the next call
			$this->array_index++;
			return $temp_link;
		}
	
		return '';
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2024-07-31T09:00:56-04:00
		if (preg_match('/(.*)T(.*)\-/Uis', $article_date, $matches)) {

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
