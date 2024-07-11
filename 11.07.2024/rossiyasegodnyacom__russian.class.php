<?php

class rossiyasegodnyacom__russian extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_date = false;
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';


	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/,"nextpage": "(.*)"/Uis',
				'append_domain' => false ,
				'process_link' => 'process_list1_link'

			),
			2 => array(
				'type' => 'article',
				'regexp' => '/ "url": "(.*)"/Uis',
				'append_domain' => true,

			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<\/h1>(.*)<div class="footer-nav">/Uis',
			'author' => false,
			'article_date' => '/<div class="news-date">(.*)<\/div>/Uis'
		)
	);
	protected function process_list1_link($link, $referer_link, $logic) {
		// site section with catch link

	return 'https://xn--c1acbl2abdlkab1og.xn--p1ai/' . $link ;

	}

	protected function process_content($content, $article_data){
		$content = preg_replace('/<div class="news-date">(.*)<\/div>/Uis', '', $content);
		return $content;
	}
	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2018-05-21T08:20:26+00:00
		if (preg_match('/(\d+?) (\W+?)/Uis', $article_date, $matches)) {
			$month = $this->convert($matches[2]);

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				date("Y") . '-' . $month . '-' . $matches[1] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}
	function convert($string) {
		$russian = array('январь', 'февраль', 'март', 'апреля', 'мая', 'июнь','июня', 'июль', 'август', 'сентябрь', 'октябрь' ,'ноябрь','декабрь');
		$english = array('1', '2', '3', '4', '5', '6', '6', '7', '8', '9', '10' ,'11' ,'12');
		$english_month = str_replace($russian, $english, $string);
		return $english_month;
	}
}
