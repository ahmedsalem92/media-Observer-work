<?php

class ttgitaliacom extends plugin_base {
	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
			),
		),
		'list2' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<article.*>.*<a href="(.*)"/Uis',
				'append_domain' => true,
			)
		),
		'article' => array(
			'headline' => '/<div class="headline"> <h2><span .*>(.*)<\/span>/Uis',
			'content' => '/<div class="paragraph texto".*>(.*)<div id="" class="portlet-boundary/Uis',
			'author' => false,
			'article_date' => '/<li class="date">(.*) <\/li>/Uis'
		)
	);


	protected function process_content($content, $article_data) {

		$content = preg_replace('/(<div class="related-items box">.*<\/div>\s*<\/div>\s*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="banner336x280 box">.*<\/div>)/Uis', '', $content);
		return $content;
	}

	protected function process_article_link($link, $referer_link, $logic) {

		if (strpos($link, '174138_copia_di_la_gran_bretagna_nel_2022_') ||
			strpos($link, '177028_copia_di_tre_nuove_strutture') ||
			strpos($link, 'stories/spotlight/177200') ||
			strpos($link, '/home/176958_internazionale_germania/')
		) {
			return false;
		}
		return $link;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//	13/10/2018
		if (preg_match('/(\d+)\/(\d+)\/(\d+?)/Uis', $article_date, $matches)) {

			$article_date_today = new DateTime();
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $matches[2] . '-' . $matches[1] . ' ' . $article_date_today->format('H:i:s'),
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;
	}

}
