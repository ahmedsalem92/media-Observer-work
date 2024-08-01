<?php

class globaltimescn extends plugin_base {

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
		'HU SAYS' ,'VIDEO' ,'PHOTO','INFOGRAPHIC' ,'CARTOON'
	);
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array (
					'/<ul class="nav navbar-nav">(.*)<\/ul>/Uis',
					'/(<a.*<\/a>)/Uis'
				),
				'append_domain' => true
			)
		),
		'section' => array(
			'link' => '/href="([^"]*)"/Uis',
			'name' => '/(?:<\/i>|<a[^<]*>)([^<]*)<\/a>/Uis',
			'append_domain' => true,
			'process_link' => 'filter_sections'
		)
	);

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<div class="[^<]*_article_form[^<]*">\s*<a[^<]*href="([^<]*)"/Uis',
				'process_link' => 'process_article_link',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<div class="mid_title">\s*<a[^<]*href="([^<]*)"/Uis',
				'process_link' => 'process_article_link',
				'append_domain' => false
			),
			2 => array(
				'type' => 'article',
				'regexp' => '/<div class="list_info">\s*<a[^<]*href="([^<]*)"/Uis',
				'process_link' => 'process_article_link',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<div class="article_title">(.*)<\/div>/Uis',
			'content' => '/(?:<div class="article_content">|<div class="article_subtitle">)(.*)(?:<div class="author_share">|<div class="article_footer">)/Uis',
			'author' => '/<div class="card_author_name">\s*<a[^<]*>(.*)<\/a>/Uis',
			'article_date' => '/class="pub_time">(.*)<\/span>/Uis'
		)
	);

	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<a target="_blank" href="([^<]*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link',
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' => '/<div class="article_title">(.*)<\/div>/Uis',
			'content' => '/(?:<div class="article_content">|<div class="article_subtitle">)(.*)(?:<div class="author_share">|<div class="article_footer">)/Uis',
			'author' => '/<div class="card_author_name">\s*<a[^<]*>(.*)<\/a>/Uis',
			'article_date' => '/class="pub_time">(.*)<\/span>/Uis'
		)
	);

	protected function process_content($content, $article_data){

		$content = preg_replace('/(<div class="container signup.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="form-button.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="newsletter.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="newsletter-subscribe">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<iframe.*<\/iframe>)/Uis', 'VIDEO', $content);
		return $content;
	}



	public function prepare_home($section_id) {

		$this->logic = $this->logic_home;

	}

	protected function detect_section_link($link) {

		return 'https://www.globaltimes.cn/includes-n/navtop.html';

	}


	protected function filter_sections($section_link, $section_name, $referer_link, $logic) {

		// exclude these sections
		if (in_array(trim($section_name), $this->exclude_sections)) {
			return '';
		}

		return $section_link;

	}

	protected function process_article_link($link, $referer_link, $logic) {

		if (strpos($link, '/special-coverage/')) {
			return false;
		}
		return $link;
	}

	protected function process_date($article_date) {
		if (preg_match('/(\w+?) (\d+?), (\d+?)/Uis', $article_date, $matches)) {
			$month = date("m", strtotime($matches[1]));
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $month . '-' . $matches[2] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;
	}

}
