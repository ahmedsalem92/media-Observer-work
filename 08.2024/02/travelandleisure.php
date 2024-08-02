<?php

class ttgmena extends plugin_base
{
	// ANT settings
	protected $ant_precision = 2;
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
				'type' => 'list1',
				'regexp' => '/<li class="mntl-pagination__next">.*<a href="(.*)"/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<div id="mntl-search-results__list_1-0"(.*)<ol id="mntl-pagination_1-0"/Uis',
					'/<a id="mntl-card-list-items_21-0".*href="(.*)"/Uis',
				],
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<\/h1>(.*)<div class="loc article-right-rail">/Uis',
			'author' => false,
			'article_date' => '/dateModified": "(.*)"/Uis'
		)
	);


	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/(<div id="travelandleisure-bylines_1-0".*)<div class="loc article-content">/Uis', '', $content);

		return $content;
	}



	protected function process_date($article_date)
	{
		$article_date_obj = DateTime::createFromFormat(
			'Y-m-d\TH:i:s.uP', 
			$article_date,
			new DateTimeZone($this->site_timezone)
		);
		if ($article_date_obj) {
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;
	}
}
