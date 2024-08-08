<?php

class themanualcom extends plugin_base
{

	// ANT settings
	protected $ant_precision = 6;
	protected $stop_on_date = false;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';
	protected $use_proxies = true;
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
			'content' => '/(?:<div class="EventDetailItem-article-copy">|<div class="MediaGallery_imageDescription">|<div class="MediaImage_description">|<div class="PageArticle_main">|<div class="PageArticle_body">)(.*)(?:<a href="\/events\/" class="ButtonMoreContent">|<div class="MediaGallery_imageNumberMobile">|<div class="PageArticle_content sticky-container">|<div class="ArticleComment">|<div class="Paywall_wrapper">)/Uis',
			'author' => false,
			'article_date' => '/modified_time" content="(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = ''; // https://www.fvw.de/touristik/sitemap.0.xml
		if (preg_match_all('/<loc>(https:\/\/www\.fvw\.de\/touristik\/sitemap(?:|\.\d+?)\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}

		return $temp_link;
	}

	protected $logic_counter = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_counter',
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
			'content' => '/(?:<div class="EventDetailItem-article-copy">|<div class="MediaGallery_imageDescription">|<div class="MediaImage_description">|<div class="PageArticle_main">|<div class="PageArticle_body">)(.*)(?:<a href="\/events\/" class="ButtonMoreContent">|<div class="MediaGallery_imageNumberMobile">|<div class="PageArticle_content sticky-container">|<div class="ArticleComment">|<div class="Paywall_wrapper">)/Uis',
			'author' => false,
			'article_date' => '/modified_time" content="(.*)"/Uis'
		)
	);

	protected function process_list1_link_counter($link, $referer_link, $logic)
	{
		$temp_link = ''; // https://www.fvw.de/counter/sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/www\.fvw\.de\/counter\/sitemap(?:|\.\d+?)\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}

		return $temp_link;
	}

	public function prepare_counter($section_id)
	{

		$this->logic = $this->logic_counter;
	}


	protected $logic_businesstravel = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_businesstravel',
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
			'content' => '/(?:<div class="EventDetailItem-article-copy">|<div class="MediaGallery_imageDescription">|<div class="MediaImage_description">|<div class="PageArticle_main">|<div class="PageArticle_body">)(.*)(?:<a href="\/events\/" class="ButtonMoreContent">|<div class="MediaGallery_imageNumberMobile">|<div class="PageArticle_content sticky-container">|<div class="ArticleComment">|<div class="Paywall_wrapper">)/Uis',
			'author' => false,
			'article_date' => '/modified_time" content="(.*)"/Uis'
		)
	);

	protected function process_list1_link_businesstravel($link, $referer_link, $logic)
	{
		$temp_link = ''; // Example: https://www.fvw.de/businesstravel/sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/www\.fvw\.de\/businesstravel\/sitemap(?:|\.\d+?)\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}

		return $temp_link;
	}

	public function prepare_businesstravel($section_id)
	{

		$this->logic = $this->logic_businesstravel;
	}


	protected $logic_international = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_international',
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
			'content' => '/(?:<div class="EventDetailItem-article-copy">|<div class="MediaGallery_imageDescription">|<div class="MediaImage_description">|<div class="PageArticle_main">|<div class="PageArticle_body">)(.*)(?:<a href="\/events\/" class="ButtonMoreContent">|<div class="MediaGallery_imageNumberMobile">|<div class="PageArticle_content sticky-container">|<div class="ArticleComment">|<div class="Paywall_wrapper">)/Uis',
			'author' => false,
			'article_date' => '/modified_time" content="(.*)"/Uis'
		)
	);

	protected function process_list1_link_international($link, $referer_link, $logic)
	{
		$temp_link = ''; // https://www.fvw.de/international/sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/www\.fvw\.de\/international\/sitemap(?:|\.\d+?)\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}

		return $temp_link;
	}

	public function prepare_international($section_id)
	{

		$this->logic = $this->logic_international;
	}

	protected $logic_green = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_green',
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
			'content' => '/(?:<div class="EventDetailItem-article-copy">|<div class="MediaGallery_imageDescription">|<div class="MediaImage_description">|<div class="PageArticle_main">|<div class="PageArticle_body">)(.*)(?:<a href="\/events\/" class="ButtonMoreContent">|<div class="MediaGallery_imageNumberMobile">|<div class="PageArticle_content sticky-container">|<div class="ArticleComment">|<div class="Paywall_wrapper">)/Uis',
			'author' => false,
			'article_date' => '/modified_time" content="(.*)"/Uis'
		)
	);

	protected function process_list1_link_green($link, $referer_link, $logic)
	{
		$temp_link = ''; // https://www.fvw.de/green/sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/www\.fvw\.de\/green\/sitemap(?:|\.\d+?)\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}

		return $temp_link;
	}

	public function prepare_green($section_id)
	{

		$this->logic = $this->logic_green;
	}



	protected $logic_galerien = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_galerien',
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
			'content' => '/(?:<div class="EventDetailItem-article-copy">|<div class="MediaGallery_imageDescription">|<div class="MediaImage_description">|<div class="PageArticle_main">|<div class="PageArticle_body">)(.*)(?:<a href="\/events\/" class="ButtonMoreContent">|<div class="MediaGallery_imageNumberMobile">|<div class="PageArticle_content sticky-container">|<div class="ArticleComment">|<div class="Paywall_wrapper">)/Uis',
			'author' => false,
			'article_date' => '/modified_time" content="(.*)"/Uis'
		)
	);

	protected function process_list1_link_galerien($link, $referer_link, $logic)
	{
		$temp_link = ''; // https://www.fvw.de/galerien/sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/www\.fvw\.de\/galerien\/sitemap(?:|\.\d+?)\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}

		return $temp_link;
	}

	public function prepare_galerien($section_id)
	{

		$this->logic = $this->logic_galerien;
	}



	protected $logic_galleries = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_galleries',
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
			'content' => '/(?:<div class="EventDetailItem-article-copy">|<div class="MediaGallery_imageDescription">|<div class="MediaImage_description">|<div class="PageArticle_main">|<div class="PageArticle_body">)(.*)(?:<a href="\/events\/" class="ButtonMoreContent">|<div class="MediaGallery_imageNumberMobile">|<div class="PageArticle_content sticky-container">|<div class="ArticleComment">|<div class="Paywall_wrapper">)/Uis',
			'author' => false,
			'article_date' => '/modified_time" content="(.*)"/Uis'
		)
	);

	protected function process_list1_link_galleries($link, $referer_link, $logic)
	{
		$temp_link = ''; // https://www.fvw.de/galleries/sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/www\.fvw\.de\/galleries\/sitemap(?:|\.\d+?)\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}

		return $temp_link;
	}

	public function prepare_galleries($section_id)
	{

		$this->logic = $this->logic_galleries;
	}



	protected $logic_events = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_events',
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
			'content' => '/(?:<div class="EventDetailItem-article-copy">|<div class="MediaGallery_imageDescription">|<div class="MediaImage_description">|<div class="PageArticle_main">|<div class="PageArticle_body">)(.*)(?:<a href="\/events\/" class="ButtonMoreContent">|<div class="MediaGallery_imageNumberMobile">|<div class="PageArticle_content sticky-container">|<div class="ArticleComment">|<div class="Paywall_wrapper">)/Uis',
			'author' => false,
			'article_date' => '/(?:<div class="EventDetailItem-article-time">|modified_time" content=")(.*)(?:<\/div>|")/Uis'
		)
	);

	protected function process_list1_link_events($link, $referer_link, $logic)
	{
		$temp_link = ''; // https://www.fvw.de/events/sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/www\.fvw\.de\/events\/sitemap(?:|\.\d+?)\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}

		return $temp_link;
	}

	public function prepare_events($section_id)
	{

		$this->logic = $this->logic_events;
	}



	protected $logic_suche = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_suche',
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
			'content' => '/(?:<p>|<div class="EventDetailItem-article-copy">|<div class="MediaGallery_imageDescription">|<div class="MediaImage_description">|<div class="PageArticle_main">|<div class="PageArticle_body">)(.*)(?:<\/p>|<a href="\/events\/" class="ButtonMoreContent">|<div class="MediaGallery_imageNumberMobile">|<div class="PageArticle_content sticky-container">|<div class="ArticleComment">|<div class="Paywall_wrapper">)/Uis',
			'author' => false,
			'article_date' => '/modified_time" content="(.*)"/Uis'
		)
	);

	protected function process_list1_link_suche($link, $referer_link, $logic)
	{
		$temp_link = ''; // https://www.fvw.de/suche/schlagworte/sitemap.0.xml
		if (preg_match_all('/<loc>(https:\/\/www\.fvw\.de\/suche\/schlagworte\/sitemap(?:|\.\d+?)\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}

		return $temp_link;
	}

	public function prepare_suche($section_id)
	{

		$this->logic = $this->logic_suche;
	}




	protected $logic_service = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_service',
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
			'content' => '/(?:<div class="ArticleCopy">|<div class="EventDetailItem-article-copy">|<div class="MediaGallery_imageDescription">|<div class="MediaImage_description">|<div class="PageArticle_main">|<div class="PageArticle_body">)(.*)(?:<footer class="SiteFooter">|<a href="\/events\/" class="ButtonMoreContent">|<div class="MediaGallery_imageNumberMobile">|<div class="PageArticle_content sticky-container">|<div class="ArticleComment">|<div class="Paywall_wrapper">)/Uis',
			'author' => false,
			'article_date' => '/modified_time" content="(.*)"/Uis'
		)
	);

	protected function process_list1_link_service($link, $referer_link, $logic)
	{
		$temp_link = ''; // https://www.fvw.de/service/sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/www\.fvw\.de\/service\/sitemap(?:|\.\d+?)\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}

		return $temp_link;
	}

	public function prepare_service($section_id)
	{

		$this->logic = $this->logic_service;
	}



	protected $logic_umfragen = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link_umfragen',
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'list3',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'list3' => array(
			0 => array(
				'type' => 'list3',
				'regexp' => '/<li class="NavigationPagination_item NavigationPagination_item-right"><a.*href="(.*)"/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<div class="PageIndexArticles_list">(.*)<footer class="SiteFooter">/Uis',
					'/<div class="ArticleTeaserSearchResultItem_content">.*<a href="(.*)"/Uis',
				],
				'append_domain' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(?:<div class="EventDetailItem-article-copy">|<div class="MediaGallery_imageDescription">|<div class="MediaImage_description">|<div class="PageArticle_main">|<div class="PageArticle_body">)(.*)(?:<a href="\/events\/" class="ButtonMoreContent">|<div class="MediaGallery_imageNumberMobile">|<div class="PageArticle_content sticky-container">|<div class="ArticleComment">|<div class="Paywall_wrapper">)/Uis',
			'author' => false,
			'article_date' => '/modified_time" content="(.*)"/Uis'
		)
	);

	protected function process_list1_link_umfragen($link, $referer_link, $logic)
	{
		$temp_link = ''; // https://www.fvw.de/umfragen/sitemap.0.xml
		if (preg_match_all('/<loc>(https:\/\/www\.fvw\.de\/umfragen\/sitemap(?:|\.\d+?)\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1][0];
		}

		return $temp_link;
	}

	public function prepare_umfragen($section_id)
	{

		$this->logic = $this->logic_umfragen;
	}



	private $links = array();
	private $array_index = 0;
	protected $date_article;

	protected function process_article_link($link, $referer_link, $logic)
	{
		$temp_link = '';

		if (empty($this->links)) {
			$result = $this->ant->get($referer_link);
			if (preg_match_all('/<loc>(.*)<\/loc>/Uis', $result, $matches)) {
				$this->links = $matches[0];
				$this->array_index = 0;
			}
		}

		if ($this->array_index < sizeof($this->links) && isset($this->links[$this->array_index])) {

			if (
				preg_match('/<loc>(.*)<\/loc>/Uis', $this->links[$this->array_index], $article_link) &&
				preg_match('/<lastmod>(.*)<\/lastmod>/Uis', $this->links[$this->array_index], $matches)
			) {
				$this->date_article = $matches[1];
			}

			$temp_link = str_replace('<loc>', '', $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>', '', $temp_link);

			$this->array_index++;
			return $temp_link;
		}

		return '';
	}

	protected function process_article_date($article_date, $article_data)
	{
		$article_date = $this->date_article;
		return $this->process_date($article_date);
	}

	protected function process_date($article_date)
	{
		// 2024-08-07T15:27:19Z 
		if (preg_match('/(.*)T(.*)Z/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			if ($article_date_obj !== false) {
				$article_date = $article_date_obj->format('Y-m-d H:i:s');
			}
		} // 2024-08-07T10:19:06+02:00

		elseif (preg_match('/(.*)T(.*)\+(.*)/Uis', $article_date, $matches)) {
			$article_date_obj = new DateTime($article_date);

			if ($this->site_timezone) {
				$article_date_obj->setTimezone(new DateTimeZone($this->site_timezone));
			}

			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		} elseif (preg_match('/(\d+.\d+.\d+) \| (\d+:\d+) Uhr - (\d+.\d+.\d+) \| (\d+:\d+) Uhr/', $article_date, $matches)) {

			$start_date_obj = DateTime::createFromFormat(
				'd.m.Y H:i',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);

			// Convert only the end time to 24-hour format
			$end_time = DateTime::createFromFormat('H:i', $matches[4], new DateTimeZone($this->site_timezone));
			$end_time_24h = $end_time->format('H:i');

			$end_date_obj = DateTime::createFromFormat(
				'd.m.Y H:i',
				$matches[3] . ' ' . $end_time_24h,
				new DateTimeZone($this->site_timezone)
			);

			if ($start_date_obj !== false && $end_date_obj !== false) {
				$start_date = $start_date_obj->format('Y-m-d H:i:s');
				$article_date = $end_date_obj->format('Y-m-d H:i:s');
				return $article_date;
			}
		} elseif (preg_match('/(\d{2}\.\d{2}\.\d{4}) \| (\d{2}:\d{2}) - (\d{2}:\d{2}) Uhr/', $article_date, $matches)) {

			$date = $matches[1]; // 04.07.2024
			$start_time = $matches[2]; // 11:00
			$end_time = $matches[3]; // 12:30

			$date_obj = DateTime::createFromFormat(
				'd.m.Y H:i',
				$date . ' ' . $end_time,
				new DateTimeZone($this->site_timezone)
			);

			if ($date_obj !== false) {
				$article_date = $date_obj->format('Y-m-d H:i:s');
			}
		} elseif (preg_match('/\s*(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2})(?:\.|Z)/Uis', $article_date, $matches)) {

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
