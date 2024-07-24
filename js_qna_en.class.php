<?php

class qna_en extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $use_proxies = true;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Qatar';
	protected $logic = array(
		'list1' => array(
			0 => array(
				// the JSON data contains article links
				'type' => 'article',
				// define that the content processing now is JSON not HTML
				'data_type' => 'json',
				// which JSON path contains articles, when empty it means the articles are returned as array,
				// when set "content > articles", means there is a member in JSON data named "content" which
				// contains another member named "articles" where are all articles links
				'data_iterate' => 'Results',
				// within the path to the articles above, for each article define the path to the article link
				// cannot be empty, when "link" it means that the article contains a member "link" with the article link
				// when "more > link" it means the article contains a member "more" which has a member "link"
				'data_field' => 'Url',
				// define the target article format
				// when HTML it means that following the link will return a HTML page
				// when JSON it means that following the link will return JSON data
				// in this case the article is contained in the current JSON data, so it can extracted here, without further requests
				'article_type' => 'html',
				// define as true when the current JSON data contains the full article data required
				// so no further page requests are made, when the full data is not present, then further requests are needed
				'contains_article_data' => true,
				'append_domain' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1 class="article-title print-title-javascript field-main-title">(.*)<\/h1>/Uis',
			'content' => '/<div class="field-body-print field-body">(.*)<div class="bottom-social-share">/Uis',
			'author' => false,
			'article_date' => '/<div>([^<]*)<\/div>(?:<h2.*\/h2>|\s*)\s*<h1/Uis'
		)
	);

	protected function process_article_link($link, $referer_link, $logic_type) {

		$link_parts = explode('/', $link);
		$mixed_part = array_pop($link_parts);
		if (trim($mixed_part) == '') {
			$mixed_part = array_pop($link_parts);
		}
		$link_parts[] = urlencode(html_entity_decode($mixed_part));
		$result_link = implode('/', $link_parts);

		return $result_link;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//15 May 2022
		if (preg_match('/(\d+) (\w+) (\d{4})/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-F-d H:i:s',
				$matches[3] . '-'. $matches[2] . '-' . $matches[1] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}

}
