<?php

class lahamag extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Riyadh';
	private $exclude_sections = array(
		'ابراج'
	);
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/<div class="menu">(.*)<\/ul>/Uis',
					'/(<a.*<\/a>)/Uis'
				)
			)
		),
		'section' => array(
			'link' => '/href="([^"]*)"/Uis',
			'name' => '/>([^<]*)</Uis',
			'append_domain' => true,
			'process_link' => 'filter_sections'
		)
	);
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="next-page" href="([^"]*)"/Uis',
				'append_domain' => false,
				'process_link'=> 'process_next_page'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<a class="ias item" href="([^"]*)"/Uis',
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<article class="main article_content"[^>]*>\s*<h1>(.*)<\/h1>/Uis',
			'content' => '/(?:<p class="caption">|<div class="text">)(.*)(?:<div class="tags">|<div class="share_icons">|<div class="trending">)/Uis',
			'author' => false,
			'article_date' => '/<span class="date red">(.*)<\/span>/Uis'
		)
	);
	protected $logic_tv = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="next" href="([^"]*)"/Uis',
				'append_domain' => false,
				'process_link'=> 'process_next_page'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<a class="ias item" href="([^"]*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_tv_article_link'
			)
		),
		'article' => array(
			'headline' => '/<div class="video_description">\s*<h1>(.*)<p>.*<\/h1>/Uis',
			'content' => '/<div class="video_description">(.*)<div class="episodes desktop">/Uis',
			'author' => false,
			'article_date' => '/<meta property="article:published_time" content="([^"]*)"/Uis'
		)
	);
	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<a class="item" href="([^"]*)"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<a href="([^"]*)" class="item"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' => '/<article class="main article_content"[^>]*>\s*<h1>(.*)<\/h1>/Uis',
			'content' => '/(?:<p class="caption">|<div class="text">)(.*)(?:<div class="tags">|<div class="share_icons">|<div class="trending">)/Uis',
			'author' => false,
			'article_date' => '/<span class="date red">(.*)<\/span>/Uis'
		)
	);

	protected function filter_sections($section_link, $section_name, $referer_link, $logic) {

		if (in_array(rtrim($section_name), $this->exclude_sections)) {
			return '';
		}
		return $section_link;
	}

	protected function section_link($section_link) {

		$link_parts = explode('/', $section_link);
		$mixed_part = array_pop($link_parts);
		if (trim($mixed_part) == '') {
			$mixed_part = array_pop($link_parts);
		}
		$link_parts[] = urlencode($mixed_part);
		$result_link = implode('/', $link_parts);

		return $result_link;

	}

	protected function process_tv_article_link($regexp_result, $referer_link, $logic) {

		$link_parts = explode('/', $regexp_result);
		$mixed_part = array_pop($link_parts);
		if (trim($mixed_part) == '') {
			$mixed_part = array_pop($link_parts);
		}
		$link_parts[] = urlencode($mixed_part);
		$result_link = implode('/', $link_parts);

		return 'http://tv.lahamag.com' . $result_link;

	}

	public function prepare_tv($section_id) {

		$this->logic = $this->logic_tv;

	}

	public function prepare_home($section_id) {

		$this->logic = $this->logic_home;

	}

	protected function process_next_page($regexp_result, $referer_link, $logic) {

		return $this->settings['site_section_link'] . $regexp_result;

	}

	protected function process_content($content, $article_data) {

		$content = preg_replace('/<div class="play">\s*<iframe.*div>/Uis', ' Video ', $content);
		if(preg_match('/<div class="trending">(.*)<\/ul>\s*<\/div>/Uis', $content, $matches)){
			$content = str_replace($matches[1], '', $content);
		}

		return $content;

	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//04 نوفمبر 2018
		if (preg_match('/(\d+) (\W+) (\d+?)/Uis', $article_date, $matches)) {

			$month = $this->arabic_month_to_number(trim($matches[2]));
			$date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $month . '-' . $matches[1] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			if ($date_obj instanceof DateTime) {
				$article_date = $date_obj->format('Y-m-d H:i:s');
			}
		}
		if (preg_match('/(.*)T(\d+):(\d+?)/Uis', $article_date, $matches)) {

			$date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2] . ':' . $matches[3] . ':00',
				new DateTimeZone($this->site_timezone)
			);
			if ($date_obj instanceof DateTime) {
				$article_date = $date_obj->format('Y-m-d H:i:s');
			}
		}

		return $article_date;

	}

}
