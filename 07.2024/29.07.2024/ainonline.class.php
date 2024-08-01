<?php

class ttgmena extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = false;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';

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
					'/<ul class="NavigationGroup_navigation-group__AQhAD">(.*)<\/ul>/Uis',
					'/<div class="NavigationItem_navigation-item__UKzM4">(.*)<\/div>/Uis'
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
				'type' => 'list1',
				'regexp' => '/<li class="pager__next">.*href="(.*)"/Uis',
				'append_domain' => true
			),
			1 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="List_left__PFKZV">(.*)<ul class="pager"/Uis',
					'/href="(.*)"/Uis'
				),
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/(?:"headline": "|<h1[^<]*>)(.*)(?:"|<\/h1>)/Uis',
			'content' => '/(?:<div class="TextOnImage_text__3kBn5">|fieldText)(.*)(?:<\/div>|FieldParagraphAinTextFieldText)/Uis',
			'author' => false,
			'article_date' => '/updated_time" content="(.*)"/Uis'
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

	protected function process_content($content, $article_data)
	{

		$content = str_replace('":{"processed":"\\', '', $content);
		$content = str_replace('\\n","__typename":"', '', $content);
		$content = str_replace('\\\\n\\n\\', '', $content);
		$content = str_replace('\\', '', $content);
		$content = preg_replace('/(\\u003c.*\\u003e)/Uis', '', $content);
		$content = str_replace('/(n","__typename":")/Uis', '', $content);
		$content = str_replace('/(\\\\n\\n\\)/Uis', '', $content);

		return $content;
	}


	protected function process_headline($headline, $article_data)
	{

		$headline = preg_replace('/(<span class="post-title" itemprop="headline"><\/span>)/Uis', 'No Headline', $headline);
		return $headline;
	}

	public function prepare_home($section_id)
	{

		$this->logic = $this->logic_home;
	}




	protected function filter_sections($section_link, $section_name, $referer_link, $logic)
	{

		// exclude these sections
		if (in_array(trim($section_name), $this->exclude_sections)) {
			return '';
		}

		return $section_link;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

		// Adjust the regex to capture the timezone offset correctly
		if (preg_match('/(.*)T(.*?)([-+]\d{2})(\d{2})/Uis', $article_date, $matches)) {
			// Combine the captured parts into a proper format
			$dateTimeStr = $matches[1] . ' ' . $matches[2] . ' ' . $matches[3] . ':' . $matches[4];

			// Create a DateTime object from the formatted string
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s O', // Include the timezone as an offset
				$dateTimeStr
			);

			// Format the date as required
			if ($article_date_obj !== false) {
				$article_date = $article_date_obj->format('Y-m-d H:i:s');
			}
		}


		return $article_date;
	}
}
