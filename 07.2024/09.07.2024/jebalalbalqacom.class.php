<?php

class jebalalbalqacom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<br \/>\s*<a\s*id="ctl00_ContentPlaceHolder.*href="(.*)"/Uis',
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/class="ItemTitle">(.*)<\/span>/Uis',
			'content' => '/class="ItemDetail">(.*)<div class="HSpaceLine.*>/Uis',
			'author' => false,
			'article_date' => '/<div class="PageTitle">.*class="DateTime">(.*)<\/span>/Uis'
		)
	);



	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		if (preg_match('/(\d+) (\W+) , (\d{4})/Uis', $article_date, $matches)) {
			$matches[2] = preg_replace('/[^\x{0600}-\x{06FF}A-Za-z !@#$%^&*()]/u',' ', $matches[2]);
			$month = $this->arabic_month_to_number(trim($matches[2]));
			$day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-'. $month . '-' . $day . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}

}
