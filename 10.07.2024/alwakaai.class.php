<?php

class alwakaai extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';
	protected $stop_on_date = true;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	protected $site_timezone = 'Asia/Amman';

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<h1>(.*)<\/h1>/Uis',
			'content' => '/<div class="news-desc"\s*>(.*)<div class="post-content"/Uis',
			'author' => false,
			'article_date' => '/^.*<li itemprop="datePublished"><i class="far fa-clock"><\/i>(.*)<\/li/Uis'
		)
	);
	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<div class="title"><a href="([^"]*)"/Uis',
				'append_domain' => false,
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' => '/<h1>(.*)<\/h1>/Uis',
			'content' => '/<div class="news-desc"\s*>(.*)<div class="post-content"/Uis',
			'author' => false,
			'article_date' => '/^.*<li itemprop="datePublished"><i class="far fa-clock"><\/i>(.*)<\/li/Uis'
		)
	);

	public function prepare_home($section_id) {

		$this->logic = $this->logic_home;

	}



	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		if (preg_match('/(\d+)-(\d+)-(\d+?)/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . '-' . $matches[2] . '-' . $matches[3] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;

	}
}
