<?php

class q8dailynews_ar extends plugin_base
{

	// ANT settings
	protected $stop_date_override = true;
	protected $ant_precision = 10;
	protected $use_proxies = true;
	protected $use_headless = true; // when issue in the site // by testing on site
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0';

	// CRAWL settings

	protected $stop_on_article_found = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/<div class="footer-bottom-section section " style=\'background:#fff;\'>(.*)<div class="footer-bottom-section section bg-dark">/Uis',
					'/<li>\s*(<a.*<\/a>)/Uis'
				)
			)
		),
		'section' => array(
			'link' => '/href="([^"]*)"/Uis',
			'name' => '/">(.*)<\/a>/Uis',
			'append_domain' => false,
			'process_link' => 'filter_sections'
		)
	);

	protected $logic = array(
		'list1' => array(
			1 => array(
				'type' => 'article',
				'regexp' => '/<a class="readmore" href="([^<]*)"/Uis',
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<h2 class="title">(.*)<\/h2>/Uis',
			'content' => '/<h2 class="title">.*<div class="head feature-head">(.*)<div class=\'col-md-4\'>/Uis',
			'author' => false,
			'article_date' => '/<span class="body-story2-date time">(.*)<\/span>/Uis'
		)
	);
	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<a class="reding-btn" href="(.*)"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<a class=\'reding-btn\'[^<]* href= "([^<]*)"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h2 class="title">(.*)<\/h2>/Uis',
			'content' => '/<h2 class="title">.*<div class="head feature-head">(.*)<div class=\'col-md-4\'>/Uis',
			'author' => false,
			'article_date' => '/<span class="body-story2-date time">(.*)<\/span>/Uis'
		)
	);

	public function prepare_home($section_id)
	{

		$this->logic = $this->logic_home;
	}

	protected function filter_sections($section_link, $section_name, $referer_link, $logic)
	{


		return $section_link . '?lang=ar';
	}

	protected function detect_section_link($link)
	{

		return 'http://q8dailynews.com/?lang=ar';
	}

	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/(>alanba<\/p>)/Uis', '', $content);
		$content = preg_replace('/(إن نص اللغة الأصلية لهذا البيان.*<\/p>)/Uis', '', $content);
		return $content;
	}

	protected function process_article_link($link, $referer_link, $logic)
	{
		return str_replace('storyad', 'story', $link);
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

		//12 سبتمبر 2021
		if (preg_match('/(\d+?) (\W+?) (\d+?)/Uis', $article_date, $matches)) {

			$day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
			$month = $this->arabic_month_to_number($matches[2]);
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $month . '-' . $day  . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);

			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}



	public function pre_get_page(&$page)
	{

		$this->ant->set_wait_for_load(true);
	}
}
