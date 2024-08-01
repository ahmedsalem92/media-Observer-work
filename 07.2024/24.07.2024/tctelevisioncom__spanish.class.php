<?php

class tctelevisioncom__spanish extends plugin_base {
	// ANT settings
	protected $ant_precision = 6;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $use_proxies = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<span aria-current="page" class="current">.*href="(.*)"/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => array (
					'/<div class="col-md-8">(.*)<nav id="nav-below" class="navigation-paging">/Uis',
					'/<h3[^<]*>.*href="(.*)"/Uis'
				),
				'append_domain' => false
			),

		),
		'article' => array(

				'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
				'content' => '/<div class="post-content clearfix">(.*)<div class="davenport-social-share-fixed sideba[^<]*>/Uis',
				'author' => false,
				'article_date' => '/<meta property="article:published_time" content="(.*)"/Uis'

		)
	);

	protected function process_content($content, $article_data) {

		$content = preg_replace('/<figure class="(.*)<\/figure>/Uis', '', $content);
		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2018-05-21T08:20:26+00:00
		if (preg_match('/(.*)T/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}
}
