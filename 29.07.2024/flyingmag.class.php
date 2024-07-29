<?php

class thegulfheraldcom extends plugin_base
{

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
				'type' => 'article',
				'regexp' => [
					'/<url>(.*)<\/url>/Uis',
					'/<loc>(.*)<\/loc>/Uis',
				],
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="MuiBox-root css-1sincx0".*>(.*)<div><button/Uis',
			'author' => '/<span class="authorName">(.*)<\/span>/Uis',
			'article_date' => '/dateTime="(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = ''; // https://theaviationist.com/post-sitemap8.xml
		if (preg_match_all('/<loc>(https:\/\/theaviationist\.com\/post-sitemap\d+?\.xml)<\/loc>/Uis', $link, $matches)) {
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
				$this->array_index = 0; // Start from the first match
			}
		}

		if (isset($this->links[$this->array_index])) {
			$temp_link = str_replace('<loc>', '', $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>', '', $temp_link);
			$this->array_index++; // Move to the next match for subsequent calls
			return $temp_link;
		}

		return '';
	}

	protected function process_content($content, $article_data)
	{

		$content = preg_replace('//Uis', '', $content);

		$content = preg_replace('/<div class="mh-meta entry-meta">(.*)<\/header>/Uis', '', $content);
		$content = preg_replace('/<blockquote class="twitter-tweet">(.*)<\/blockquote>/Uis', '', $content);
		$content = preg_replace('/<div class="mh-author-box clearfix">(.*)<div class="entry-tags clearfix">/Uis', '', $content);
		$content = preg_replace('/(<ins.*<\/ins>)/Uis', '', $content);
		$content = preg_replace('/(<script.*<\/script>)/Uis', '', $content);



		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {
		// Regex to capture the date and time
		if (preg_match('/(.*)T(.*)/', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			
			// Return formatted date if creation was successful
			if ($article_date_obj) {
				$article_date = $article_date_obj->format('Y-m-d H:i:s');
			}
		}
	
		// If in '/columns', return current date
		if (strpos($this->settings['site_section_link'], '/columns')) {
			return date('Y-m-d H:i:s', time());
		}
	
		return $article_date;
	}
}
