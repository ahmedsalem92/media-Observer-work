<?php

class dealtown extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = false;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';
	protected $use_proxies = true; // Proxy
	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list_press_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<section.*>(.*)<\/section>/Uis',
					'/<div class="css-13clzzd"><a target="_self" href="(.*)"/Uis',
				],
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<\/h1>(.*)<h2 class="css-/Uis',
			'author' => false,
			'article_date' => '/<span class="css-1w632qo">.*<span class="css-1w632qo">(.*)<\/span>/Uis'
		)
	);

	protected $date;
	protected $page_count = 1;
	protected function process_list_press_link($link, $referer_link, $logic)
	{
		if ($this->page_count < 150) {
			return 'https://deal.town/archive/' . $this->date = date('Y-m-d') . '?page=' . $this->page_count++;
		} else {
			return false;
		}
	}


	protected function process_content($content, $article_data)
	{
		$content = preg_replace('/<p class="css-1vq6eb">(.*)<\/p>/Uis', '', $content);
		$content = preg_replace('/<span id="showall".*>(Show all)<\/span>/Uis', '', $content);
		$content = str_replace('Facebook', '', $content);
		$content = str_replace('Facebook', '', $content);
		$content = str_replace('Instagram', '', $content);
		$content = str_replace('Instagram', '', $content);
		$content = str_replace('Twitter', '', $content);
		$content = str_replace('Twitter', '', $content);
		$content = str_replace('London | New York | Hong Kong | Tokyo | Sydney', '', $content);
		$content = preg_replace('/<div class="mktoText" id="header_001-txt534".*>(.*)<\/div>/Uis', '', $content);
		$content = str_replace('© 2024 | All rights reserved.', '', $content);
		$content = str_replace('NOUTATI', '', $content);
		$content = str_replace('BARBATI', '', $content);
		$content = str_replace('FEMEI', '', $content);
		$content = str_replace('COPII', '', $content);
		$content = str_replace('OUTLET', '', $content);
		$content = str_replace('You may or change your contact details at any time.', '', $content);
		$content = str_replace('SHOP NOW', '', $content);
		$content = str_replace('Button Text', '', $content);
		$content = str_replace('Custom', '', $content);

		return $content;
	}

	protected function process_date($article_date)
	{
		// August 6, 2024
		if (preg_match('/(\w+) (\d+), (\d+)/', $article_date, $matches)) {
			$month = date('m', strtotime($matches[1]));
			$day = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
			$year = $matches[3];

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				"$year-$month-$day 00:00:00"
			);

			if ($article_date_obj) {
				$article_date = $article_date_obj->format('Y-m-d H:i:s');
			}
		}

		return $article_date;
	}
}
