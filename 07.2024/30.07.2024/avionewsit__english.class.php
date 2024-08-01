<?php

class avionewsit__english extends plugin_base {

	// ANT settings
	protected $ant_precision = 6;
	protected $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';
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
			'headline' => '/<title>(.*)<\/title>/Uis',
			'content' => '/<div class="details.*">(.*)<div class="clear">/Uis',
			'author' => false,
			'article_date' => '/datePublished":"(.*)"/Uis'
		)
	);


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

	protected function process_date($article_date) {

		//2024-07-30T08:35:05+00:00
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