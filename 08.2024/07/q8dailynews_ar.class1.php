<?php

class ttgmena extends plugin_base
{

	// ANT settings
	protected $ant_precision = 10;
	protected $stop_on_date = false;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';
	protected $use_headless = true;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	private $exclude_sections = array(
		'All Categories', 'News Archive', 'Newsletter Archive', 'Print Archives', 'Expert Opinion', 'In-Depth Reports', 'Videos', 'Webinars', 'Airshows &amp; Conventions', 'Aviation Events', 'Whitepapers', 'About AIN', 'Our Writers', 'History', 'Advertise', 'Contact Us', 'Airshows & Conventions'
	);
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/<ul class="menu main-menu sf-arrows">(.*)<\/ul>/Uis',
					'/<li>(.*)<\/li>/Uis'
				),
				'append_domain' => true
			)
		),
		'section' => array(
			'link' => '/href="(.*)"/Uis',
			'name' => '/<a.*>(.*)<\/a>/Uis',
			'append_domain' => true,
			'process_link' => 'filter_sections'
		)
	);

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="blog-posts">(.*)<button class="button-arounder load-more">/Uis',
					'/<div class="post-image">.*<a href="(.*)"/Uis'
				),
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<h2>(.*)<\/h2>/Uis',
			'content' => '/<p>(.*)<\/article>/Uis',
			'author' => false,
			'article_date' => '/<div class="post-date ms-0">(.*)<\/div>/Uis'
		)
	);
	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="CardGrid_card-item__S_kjv">(.*)<\/a><\/div>/Uis',
					'/href="(.*)"/Uis'
				),
				'append_domain' => true,
				'ignore_terminal_stop' => true
			),
			1 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="CardGrid_card-item--block__Eowmn">(.*)<\/a><\/div>/Uis',
					'/href="(.*)"/Uis'
				),
				'append_domain' => true,
				'ignore_terminal_stop' => true
			),
		),
		'article' => array(
			'headline' => '/(?:"headline": "|<h1[^<]*>)(.*)(?:"|<\/h1>)/Uis',
			'content' => '/(?:<div class="TextOnImage_text__3kBn5">|fieldText)(.*)(?:<\/div>|FieldParagraphAinTextFieldText)/Uis',
			'author' => false,
			'article_date' => '/updated_time" content="(.*)"/Uis'
		)
	);

	public function prepare_home($section_id)
	{

		$this->logic = $this->logic_home;
	}

	protected function filter_sections($section_link, $section_name, $referer_link, $logic)
	{
		if (in_array(trim($section_name), $this->exclude_sections)) {
			return '';
		}

		return $section_link;
	}

	protected function process_date($article_date) {
		// Example: <span class="day">6</span><span class="month">Aug</span>
		if (preg_match('/<span class="day">(\d+)<\/span><span class="month">(\w+)<\/span>/', $article_date, $matches)) {
			$day = $matches[1];
			$month = $matches[2];
	
			// List of month names to convert to numeric representation
			$months = [
				'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4,
				'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8,
				'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12
			];
	
			// Get the current year
			$year = date('Y');
	
			// Convert month name to number
			$month_number = $months[$month];
	
			// Create a date object
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				sprintf('%04d-%02d-%02d 00:00:00', $year, $month_number, $day),
				new DateTimeZone($this->site_timezone)
			);
	
			// Format the date as required
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		
		return $article_date;
	}

	public function pre_get_page(&$page)
	{

		$this->ant->set_wait_for_load(true);
	}
}
