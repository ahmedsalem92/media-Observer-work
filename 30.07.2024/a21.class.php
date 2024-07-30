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
			'article_date' => '/<span class="date">(.*)<\/span>/Uis'
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
			'article_date' => '/<span class="date">(.*)<\/span>/Uis'
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
		// Array to map Spanish month names to month numbers
		$months = [
			'Enero' => '01',
			'Febrero' => '02',
			'Marzo' => '03',
			'Abril' => '04',
			'Mayo' => '05',
			'Junio' => '06',
			'Julio' => '07',
			'Agosto' => '08',
			'Septiembre' => '09',
			'Octubre' => '10',
			'Noviembre' => '11',
			'Diciembre' => '12'
		];
	
		// Match the pattern 'Lunes, 29 Julio 2024 - 01:00'
		if (preg_match('/\w+,\s+(\d+)\s+(\w+)\s+(\d+)\s+-\s+(\d+):(\d+)/', $article_date, $matches)) {
			$day = $matches[1];
			$month = $months[$matches[2]];
			$year = $matches[3];
			$hour = $matches[4];
			$minute = $matches[5];
	
			// Create the date string in YYYY-mm-dd HH:ii:ss format
			$formatted_date = sprintf('%04d-%02d-%02d %02d:%02d:00', $year, $month, $day, $hour, $minute);
			
			// Convert to DateTime object
			$article_date_obj = DateTime::createFromFormat('Y-m-d H:i:s', $formatted_date, new DateTimeZone($this->site_timezone));
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
	
		return $article_date;
	}
}
