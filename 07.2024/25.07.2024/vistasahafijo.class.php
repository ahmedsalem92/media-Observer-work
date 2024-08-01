<?php

class vistasahafijo extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = false;

	// DEFINITIONS
	protected $site_timezone = CORE_CURRENT_TIMEZONE;
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/<body(.*)<\/body>/Uis',
					'/<span class="more_page_link">(.*<strong>More<\/strong>)/Uis'
				)

			)
		),
		'section' => array(
			'link' => '/<a href="(.*)"/Uis',
			'name' => '/<strong>(.*)<\/strong>/Uis',
			'append_domain' => true
		)
	);
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<a href="(art\.php[^"]*)"[^>]*>/Uis',
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<div id="sah_main_title" class="top_story_h5">(.*)<\/div>/Uis',
			'content' => '/<div id="sah_main_body" class="article_body">(.*)<div class="left">/Uis',
			'author' => false,
			'article_date' => '/<div id="sah_item_date"[^>]*>(.*)<\/div>/Uis'
		)
	);

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		if (preg_match('/^(\d+)-([^-]*)-(\d+)$/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-M-d H:i:s',
				$matches[3] . '-' . $matches[2] . '-' . $matches[1] . ' 18:00:00',
				new DateTimeZone($this->site_timezone)
			);
			if ($article_date_obj instanceof DateTime) {
				$article_date = $article_date_obj->format("Y-m-d H:i:s");
			}
		}

		return $article_date;

	}

}
