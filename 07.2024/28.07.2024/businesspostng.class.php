<?php

class businesspostng extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_date = false;
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;


	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false
			),
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content.*">(.*)<div class="sharedaddy sd-sharing-enabled">/Uis',
			'author' => '/<strong>By(.*)<\/strong>/Uis',
			'article_date' => '/"article:published_time" content="(.*)"/Uis'
		)
	);



	protected function process_content($content, $article_data) {

		$content = preg_replace('/<div style="clear:both; margin-top:0em; margin-bottom:1em;">.*div>/Uis', '', $content);
		$content = preg_replace('/(<strong>By.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<h3 class="sd-title">Share this.*<em>Related<\/em>)/Uis', '', $content);
		$content = preg_replace('/(ADVERTISEMENT)/Uis', '', $content);
		$content = preg_replace('/(<div class="jnews_inline_related_post">.*)<p.*>/Uis', '', $content);
		
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
