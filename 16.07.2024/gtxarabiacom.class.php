<?php

class gtxarabiacom extends plugin_base {
	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';

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
				//'regexp'=> '/<loc>([^<]*)<\/loc>/Uis',
				'regexp'=> '/<guid isPermaLink="false">([^<]*)<\/g/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1 class="entry-title penci-entry-title[^>]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="penci-entry-content entry-content">(.*)(?:<\/div>\s*<\!-- \.entry-content -->|<div class="penci-review-score-total)/Uis',
			'author' => false,
			'article_date' => '/"datePublished": "([^"]*)"/Uis'
		)
	);

	protected function process_content($content, $article_data) {

		$content = preg_replace('/<iframe.*iframe>/Uis', 'Video', $content);
		$content = preg_replace('/<span itemprop="name">.*<\/span>/Uis', '', $content);
		$content = preg_replace('/<div class="penci-review.*<\/div>/Uis', '', $content);
		return $content;
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
