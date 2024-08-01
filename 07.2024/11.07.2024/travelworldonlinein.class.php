<?php

class travelworldonlinein extends plugin_base {
	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';
	protected $stop_on_date = true;
	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'next_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<h3 class="dynamic" data-field-id="0"><a href="(.*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div id="brxe-a63575" class="brxe-post-[^<]*>(.*)<div id="brxe-09c2d4" class="brxe-block [^<]*>/Uis',
			'author' => False,
			'article_date' => '/(?:<span class="date">|<meta property="article:published_time" content=")(.*)(?:<\/|")/Uis'
		)
	);
	protected $page = 2 ;
	protected function next_link($link) {

		return 'https://travelworldonline.in/page/' . $this->page++  .'/?s=' ;
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
		}else{
			$article_date_obj = DateTime::createFromFormat('F j, Y', $article_date, new DateTimeZone($this->site_timezone));
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}

}
