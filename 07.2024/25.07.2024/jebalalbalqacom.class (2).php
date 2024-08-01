<?php

class jebalalbalqacom extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = false;
	protected $stop_on_date = false;
	protected $use_headless = true;
	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<td><a\s+href="([^"]+)">([^<]+)<\/a>\s*<\/td>\s*(?!.*<td><a\s+href="[^"]+">[^<]+<\/a>\s*<\/td>)/Uis',
				'append_domain' => true,
				'process_link' => 'process_list1_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<a id="ctl00_ContentPlaceHolder1_ctl00_grvItems_ctl.*" class="Item" href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_list1_link'
			)
		),
		'article' => array(
			'headline' => '/<span id="ctl00_ContentPlaceHolder1_ctl00_fvwItemDetail_lblItemTitle" class="ItemTitle">(.*)<\/span>/Uis',
			'content' => '/<div style="direction: rtl;">(.*)(?:<\/div><\/div>|<\/span><\/div><\/div>)/Uis',
			'author' => false,
			'article_date' => '/<div class="PageTitle">.*class="DateTime">(.*)<\/span>/Uis'
		)
	);

	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => [
					'/<td class="HomeLeftSide1">(.*)<\/td>/Uis',
					'/<div style="float: right; width: 65%; padding: 4px"><a href="(.*)"/Uis',
				],
				'append_domain' => true,
				'process_link' => 'process_list1_link'
			),
			2 => array(
				'type' => 'article',
				'regexp' => [
					'/<td class="HomeRightSide1">(.*)<\/td>/Uis',
					'/<div style="margin-bottom: 5px"><a href="(.*)"/Uis',
				],
				'append_domain' => true,
				'process_link' => 'process_list1_link'
			)
		),
		'article' => array(
			'headline' => '/<span id="ctl00_ContentPlaceHolder1_ctl00_fvwItemDetail_lblItemTitle" class="ItemTitle">(.*)<\/span>/Uis',
			'content' => '/<div style="direction: rtl;">(.*)(?:<\/div><\/div>|<\/span><\/div><\/div>)/Uis',
			'author' => false,
			'article_date' => '/<div class="PageTitle">.*class="DateTime">(.*)<\/span>/Uis'
		)
	);

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

	protected function process_date($article_date)
	{

		if (preg_match('/(\d+) (\W+) , (\d{4})/Uis', $article_date, $matches)) {
			$matches[2] = preg_replace('/[^\x{0600}-\x{06FF}A-Za-z !@#$%^&*()]/u', ' ', $matches[2]);
			$month = $this->arabic_month_to_number(trim($matches[2]));
			$day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $month . '-' . $day . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		} elseif (preg_match('/\p{Arabic}+ , (\d{2}) (\D*) , (\d{4}) :: (\d{1,2}):(\d{2}) (م|ص)/u', $article_date, $matches)) {
			//	الأربعاء , 01 كانون الأول , 2021 :: 2:29 م
			$day = $matches[1];
			$arabic_month = $matches[2];
			$year = $matches[3];
			$hour = (int)$matches[4];
			$minute = $matches[5];
			$period = $matches[6];

			$arabic_months = [
				'كانون الثاني' => 1,
				'شباط' => 2,
				'آذار' => 3,
				'نيسان' => 4,
				'أيار' => 5,
				'حزيران' => 6,
				'تموز' => 7,
				'آب' => 8,
				'أيلول' => 9,
				'تشرين الأول' => 10,
				'تشرين الثاني' => 11,
				'كانون الأول' => 12,
			];

			// Convert month name to number
			if (isset($arabic_months[$arabic_month])) {
				$month = $arabic_months[$arabic_month];
			} else {
				$month = null;
				error_log("Undefined month: $arabic_month");
			}


			// Adjust hour for AM/PM
			if ($period === 'م' && $hour != 12) {
				$hour += 12; // Convert to 24-hour format
			} elseif ($period === 'ص' && $hour === 12) {
				$hour = 0; // Midnight case
			}

			$article_date = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, 0);
		}

		return $article_date;
	}

	public function pre_get_page(&$page)
	{

		$this->ant->set_wait_for_load(true);
	}
}
