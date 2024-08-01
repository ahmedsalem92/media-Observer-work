<?php

class snabusinesscom extends plugin_base
{

	// ANT settings
	protected $ant_precision = 6;
	protected $use_proxies = true;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/60.0.3112.113 Chrome/60.0.3112.113 Safari/537.36';
	protected $use_headless = true;


	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(https:\/\/www\.snabusiness\.com\/(?:article|infographic|program)\/.*)<\/loc>/Uis',
				'append_domain' => false
			),
		),
		'article' => array(
			'headline' => '/(?:<h1[^<]*>|<h1 class="ContentHeader[^>]*>)(.*)<\/h/Uis',
			'content' => '/<p class="Article_summary__.*">(.*)<ul class="SocialShare_social/Uis',
			'author' => false,
			'article_date' => '/(?:<span class="TimeAgo_date__S66fS">|dateModified": ")(.*)(?:"|<\/span>)/Uis'
		)
	);

	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/(<ul class="SocialShare.*\/ul>)/Uis', '', $content);
		$content = preg_replace('/(<div class="StoryListItem.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="RelatedArticles_box.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="RelatedArticles_box__ZR4H6 ">.*)<p>/Uis', '', $content);
		$content = preg_replace('/<ul class="MuiTypography-root.*>(.*)<\/ul>/Uis', '', $content);
		if (empty($content)) {
			return 'video';
		} else {
			return $content;
		}


		return $content;
	}


	protected function process_date($article_date)
	{
		// Handle the specific format "12:49 - 29 يوليو 2024"
		if (preg_match('/(d{2}:d{2}) - (d+?) (w+?) (d+?)/u', $article_date, $matches)) {
			// Extract time, day, month, and year components
			$time = $matches[1];
			$day = $matches[2];
			$month = $matches[3];
			$year = $matches[4];

			// Convert month to numeric representation (assuming you have a mapping)
			// Replace 'يوليو' with the actual numeric month value
			$monthMap = [
				'يناير' => '01',
				'فبراير' => '02',
				'مارس' => '03',
				'أبريل' => '04',
				'مايو' => '05',
				'يونيو' => '06',
				'يوليو' => '07',
				'أغسطس' => '08',
				'سبتمبر' => '09',
				'أكتوبر' => '10',
				'نوفمبر' => '11',
				'ديسمبر' => '12'
			];
			$numericMonth = $monthMap[$month];

			// Create DateTime object
			$article_date_obj = DateTime::createFromFormat(
				'H:i d m Y',
				$time . ' ' . $day . ' ' . $numericMonth . ' ' . $year,
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		} elseif (preg_match('/(.*)T(.*)Z/Uis', $article_date, $matches)) {
			// Create DateTime object from the matched date part
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone('UTC') // Use UTC timezone for 'Z'
			);

			// Format to desired output
			$article_date = $article_date_obj->setTimezone(new DateTimeZone($this->site_timezone))->format('Y-m-d H:i:s');
		}else{
			$article_date = date('Y-m-d H:i:s');
		}


		return $article_date;
	}

	public function pre_get_page(&$page)
	{

		$this->ant->set_wait_for_load(true);
	}
}
