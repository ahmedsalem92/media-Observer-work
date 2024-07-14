<?php

class jebalalbalqacom extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = false;
	protected $stop_on_date = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<a id="ctl00_ContentPlaceHolder1_ctl00_grvItems_ctl.*" class="Item" href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_list1_link'
			)
		),
		'article' => array(
			'headline' => '/<span id="ctl00_ContentPlaceHolder1_ctl00_fvwItemDetail_lblItemTitle" class="ItemTitle">(.*)<\/span>/Uis',
			'content' => '/<span id="ctl00_ContentPlaceHolder1_ctl00_fvwItemDetail_lblItemDetail" class="ItemDetail">(.*)<div class="HSpaceLine1">/Uis',
			'author' => false,
			'article_date' => '/(?:class="DateTime">|<div class="PageTitle">.*class="DateTime">)(.*)<\/span>/Uis'
		)
	);

	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<a href=\'([^<]*)\' class=\'Item\'>/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' => '/<span id="ctl00_ContentPlaceHolder1_ctl00_fvwItemDetail_lblItemTitle" class="ItemTitle">(.*)<\/span>/Uis',
			'content' => '/<span id="ctl00_ContentPlaceHolder1_ctl00_fvwItemDetail_lblItemDetail" class="ItemDetail">(.*)<div class="HSpaceLine1">/Uis',
			'author' => false,
			'article_date' => '/(?:class="DateTime">|<div class="PageTitle">.*class="DateTime">)(.*)<\/span>/Uis'
		)

	);

	/*protected function process_home_link($link, $referer_link, $logic) {
		return 'https://www.jebalalbalqa.com/' . $link;
	}*/


	public function prepare_home($section_id)
	{

		$this->logic = $this->logic_home;
	}



	protected function process_list1_link($link, $referer_link, $logic)
	{

		$link =  str_replace('amp;', '', $link);
		$link =  str_replace('#038;', '', $link);
		return $link;
	}





	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{
		$arabic_day_names = [
			'الأحد' => 'Sunday',
			'الاثنين' => 'Monday',
			'الثلاثاء' => 'Tuesday',
			'الأربعاء' => 'Wednesday',
			'الخميس' => 'Thursday',
			'الجمعة' => 'Friday',
			'السبت' => 'Saturday',
		];

		$arabic_month_names = [
			'كانون الثاني' => '01',
			'شباط' => '02',
			'آذار' => '03',
			'نيسان' => '04',
			'أيار' => '05',
			'حزيران' => '06',
			'تموز' => '07',
			'آب' => '08',
			'أيلول' => '09',
			'تشرين الأول' => '10',
			'تشرين الثاني' => '11',
			'كانون الأول' => '12',
		];

		if (preg_match('/(ال\w{6}) , (\d+) (\w{6}) , (\d{4}) :: (\d+):(\d+)\s+(\w)/u', $article_date, $matches)) {
			$day = $arabic_day_names[$matches[1]];
			$month = $arabic_month_names[$matches[3]];
			$day = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
			$hour = $matches[5];
			$minute = $matches[6];
			$ampm = $matches[7];

			if (mb_strtolower($ampm, 'UTF-8') === 'م') {
				$hour = $hour < 12 ? $hour + 12 : $hour;
			}

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[4] . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':00',
				new DateTimeZone($this->site_timezone)
			);

			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}
}
