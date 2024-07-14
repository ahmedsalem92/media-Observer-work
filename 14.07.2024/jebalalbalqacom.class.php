<?php

class jebalalbalqacom extends plugin_base {

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
				'type' => 'article',
				'regexp' => '/<a id="ctl00_ContentPlaceHolder1_ctl00_grvItems_ctl.*" class="Item" href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_list1_link'
			)
		),
		'article' => array(
			'headline' => '/<span id="ctl00_ContentPlaceHolder1_ctl00_fvwItemDetail_lblItemTitle" class="ItemTitle">(.*)<\/span>/Uis',
			'content' => '/<div style="direction: rtl;">(.*)<\/span><\/div><\/div>/Uis',
			'author' => false,
			'article_date' => '/<div class="PageTitle">.*class="DateTime">(.*)<\/span>/Uis'
		)
	);

	protected $logic_press = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<div class="headline-entry article">.*<a href="(.*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_press_link'
			)
		),
		'article' => array(
			'headline' => '/<body[^>]*>.*(?:<h1 class="xn-hedline">|<div class="headline-col "><h1>)(.*)<\/h/Uis',
			'content' => '/(<div class="entry-content">.*)(?:<\/main|<ul class="sidebar|>\s*Continued…\.|>\s*#\s*#\s*#\s*<\/)/Uis',
			'author' => false,
			'article_date' => '/(?:<meta property="article:published_time" content="|<span itemprop="datePublished dateModified">|<div title="[^<]*>)([^"]*)(?:"|<\/span>|<\/div>)|"date":"([^"]*)","(?:feedCode|fileName|headline)|,"feedCode":"[^"]*","date":"([^"]*)"/Uis'
		)

	);

	protected function process_press_link($link, $referer_link, $logic) {
		return 'https://www.jebalalbalqa.com/' . $link;
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
		}

		return $article_date;
	}

	public function pre_get_page(&$page)
	{   //

		$this->ant->set_wait_for_load(true); //headless

	}
}
