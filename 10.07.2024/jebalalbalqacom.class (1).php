<?php

class jebalalbalqacom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	private $exclude_sections = array(
		'http://www.jebalalbalqa.com/page.aspx?pg=7&si=16&md=news',
		'http://www.jebalalbalqa.com/page.aspx?pg=7&si=15&md=news',
		'http://www.jebalalbalqa.com/page.aspx?pg=7&si=3&md=news',
		'http://www.jebalalbalqa.com/page.aspx?pg=7&si=2&md=news',
		'http://www.jebalalbalqa.com/page.aspx?pg=7&si=5&md=news',
		'http://www.jebalalbalqa.com/page.aspx?pg=7&si=8&md=news',
		'http://www.jebalalbalqa.com/page.aspx?pg=7&si=10&md=news',
		'http://www.jebalalbalqa.com/page.aspx?pg=7&si=14&md=news'
	);
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array (
					'/<td colspan="3" class="HMenu">(.*)<td colspan="3" class="Banner">/Uis',
					'/(<a.*<\/a>)/Uis'
				)
			),
			1 => array(
				'type' => 'section',
				'regexp' => '/(<a[^<]*><div class=\'BlockTitle[^<]*\'>.*<\/a>)/Uis'
				)
		),
		'section' => array(
			'link' => '/href=(?:"|\')([^\']*)(?:"|\')/Uis',
			'name' => '/(?:<a.*>|<div class=\'BlockTitle.*>)([^<]*)<\//Uis',
			'append_domain' => true,
			'process_link' => 'filter_sections'
		)
	);
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<div class=\'Block.*<a href=(.*)>/Uis',
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
	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/id=\'Title-\d+\'><a[^>]*href=\'([^\']*)\'/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<a[^>]*href=\'([^\']*)\'[^>]*class=\'item/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' => '/class="ItemTitle">(.*)<\/span>/Uis',
			'content' => '/class="ItemDetail">(.*)<div class="HSpaceLine.*>/Uis',
			'author' => false,
			'article_date' => '/<div class="PageTitle">.*class="DateTime">(.*)<\/span>/Uis'
		)
	);

	public function prepare_home($section_id) {

		$this->logic = $this->logic_home;

	}

	protected function filter_sections($section_link, &$section_name, $referer_link, $logic) {

		// exclude these sections
		if (in_array(trim($section_link), $this->exclude_sections)) {
			return '';
		}

		$section_name = iconv('Windows-1256 ', 'UTF-8//TRANSLIT//IGNORE', $section_name);

		return $section_link;

	}


	protected function process_headline($headline , $article_data){

		return iconv('Windows-1256 ', 'UTF-8//TRANSLIT//IGNORE', $headline);
	}

	protected function process_content($content , $article_data){

		return iconv('Windows-1256 ', 'UTF-8//TRANSLIT//IGNORE', $content);
	}

	protected function process_article_date($article_date , $article_data){

		return iconv('Windows-1256 ', 'UTF-8//TRANSLIT//IGNORE', $article_date);
	}

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
