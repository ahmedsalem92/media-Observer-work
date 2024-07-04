<?php

class eqtisadonacom extends plugin_base {
	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0';
	protected $use_proxies = true;

	// CRAWL settings
	protected $stop_on_date = true;
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<span class="last-page first-last-pages">\s*<a href="([^"]*)"/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<a class="more-link button" href="([^"]*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1 class="post-title[^>]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content entry clearfix">(.*)(?:<div id="post-extra-info">|<\/article>)/Uis',
			'author' => false,
			'article_date' => '/^.*"datePublished":\s*"(.*)"/Uis'
		)
	);

	protected function process_article_link($link, $referer_link, $logic) {
		if ($link == 'https://www.eqtisadona.com/') return false;
		if ($link == 'https://www.eqtisadona.com') return false;
		return $link;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2018-05-21T08:20:26+00:00
		// 2024/05/05
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
