<?php

class thegulfheraldcom extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = true;
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
			'content' => '/<\/h1>(.*)(?:<div id="travelandleisure-taglines_1-0"|<div class="loc article-right-rail">)/Uis',
			'author' => false,
			'article_date' => '/dateModified": "(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = ''; // https://www.travelandleisure.com/sitemap_1.xml
		if (preg_match_all('/<loc>(https:\/\/www\.travelandleisure\.com\/sitemap_\d+?\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
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
				$this->array_index = 0;
			}
		}
		if ($this->array_index < sizeof($this->links)) {
			$temp_link = str_replace('<loc>', '', $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>', '', $temp_link);
			$this->array_index++;
			return $temp_link;
		}

		return '';
	}

	protected function process_content($content, $article_data)
	{
		$content = preg_replace('/<div id="mntl-article-meta-dynamic_1-0".*>(.*)<div id="article-content_1-0".*>/Uis', '', $content);
		$content = preg_replace('/<div id="mntl-article-meta-dynamic_1-0".*>(.*)<div class="loc article-content">/Uis', '', $content);
		$content = preg_replace('/<figcaption.*>(.*)<\/figcaption>/Uis', '', $content);
			
		return $content;
	}

	protected function process_date($article_date) {
		// Example input: 2024-08-05T15:42:19.697-04:00
		if (preg_match('/^(.*)T(.*)\-(\d{2}:\d{2})$/', $article_date, $matches)) {
			// Convert the timezone offset into a DateTimeZone object
			$date_time = DateTime::createFromFormat(
				'Y-m-d\TH:i:s.uP', // 'u' for microseconds and 'P' for timezone with offset
				$article_date
			);
	
			if ($date_time) {
				$article_date = $date_time->format('Y-m-d H:i:s');
			}
		}
		return $article_date;
	}
}
