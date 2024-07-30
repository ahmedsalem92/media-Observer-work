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
	private $exclude_sections = array();

	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/<ul class="menu sub-menu">(.*)<\/ul>/Uis',
					'/<li.*class="menu-item.*">(.*)<\/li>/Uis'
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
				'regexp' => '/<ul class="js-pager__items pager".*href="(.*)"/Uis',
				'append_domain' => true
			),
			1 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="item-list">(.*)<\/ul>/Uis',
					'/<li.*class="view-list-item.*".*>.*href="(.*)"/Uis'
				),
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="node__content clearfix">(.*)<div id="node-single-comment">/Uis',
			'author' => false,
			'article_date' => '/<div class="datetime">(.*)<\/div>/Uis'
		)
	);
	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="item-list">(.*)<\/ul>/Uis',
					'/<li.*class="view-list-item.*".*>.*href="(.*)"/Uis'
				),
				'append_domain' => true,
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="node__content clearfix">(.*)<div id="node-single-comment">/Uis',
			'author' => false,
			'article_date' => '/<div class="datetime">(.*)<\/div>/Uis'
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
		// 30/07/2024
		if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $article_date, $matches)) {
			// Create the date object from the matched parts
			$article_date_obj = DateTime::createFromFormat(
				'd/m/Y',
				$article_date,
				new DateTimeZone($this->site_timezone)
			);

			// Format the date as 'Y-m-d H:i:s'
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}
}
