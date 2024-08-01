<?php

class ttgitaliacom extends plugin_base {
	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = false;
	protected $stop_on_date = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	private $exclude_sections = array('https://www.ttgitalia.com/ttg-webinar','https://www.ttgitalia.com/normative-e-fisco');

	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/class="menu_secc vertical"> <div class="nav-dropdown nav noSubNav">.*<ul class="parent-nav lst cf">(.*)<\/ul>/Uis',
					'/(<a.*<\/a>)/Uis'
				)
			)
		),
		'section' => array(
			'link' => '/href="([^"]*)"/Uis',
			'name' => '/<span class="sectionName">(.*)<\/span>/Uis',
			'append_domain' => true,
			'process_link' => 'filter_sections'
		)
	);

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<div class="paged noticias.*>(.*?)<\/div>/is',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="paged[^>]*>(.*)(?:<ul id="[^>]*myNavButtons">|<div class="col-60-40-top-two-col col-xs-12)/Uis',
					'/<div class="headline">\s*<a href="([^"]*)"/Uis'
					),
				'append_domain' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/(?:<h1><span class="priority-content".*>|<h1 class="headline[^>]*>|<div class="headline"> <h2><span .*>)(.*)(?:<\/span><\/h1>|<\/h1>|<\/span>)/Uis',
			'content' => '/(?:<div class="paragraph texto".*>|<div class="article-content">|<div class="paragraph texto".*>)(.*)(?:<div id="" class="portlet-boundary|<div class="share-buttons">|<div id="" class="portlet-boundary)/Uis',
			'author' => false,
			'article_date' => '/<li class="date">(.*) <\/li>/Uis'
		)
	);
	
	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<div class="headline">\s*<a href="([^"]*)"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true
			)			
		),
		'article' => array(
			'headline' => '/(?:<h1><span class="priority-content".*>|<h1 class="headline[^>]*>|<div class="headline"> <h2><span .*>)(.*)(?:<\/span><\/h1>|<\/h1>|<\/span>)/Uis',
			'content' => '/(?:<div class="paragraph texto".*>|<div class="article-content">|<div class="paragraph texto".*>)(.*)(?:<div id="" class="portlet-boundary|<div class="share-buttons">|<div id="" class="portlet-boundary)/Uis',
			'author' => false,
			'article_date' => '/<li class="date">(.*) <\/li>/Uis'
		)
	);
	public function prepare_home($section_id) {

		$this->logic = $this->logic_home;

	}
	protected function process_article_link($link, $referer_link, $logic)
	{

		if (strpos($link, '/autore/')) {
			return false;
		}
		return $link;
	}
	protected function filter_sections($section_link, $section_name, $referer_link, $logic) {

		// exclude these sections
		if (in_array(trim($section_link), $this->exclude_sections)) {
			return '';
		}
		if (in_array(trim($section_name), $this->exclude_sections)) {
			return '';
		}

		return $section_link;

	}

	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/(<div class="related-items box">.*<\/div>\s*<\/div>\s*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="banner336x280 box">.*<\/div>)/Uis', '', $content);
		return $content;
	}


	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

		//	13/10/2018
		if (preg_match('/(\d+)\/(\d+)\/(\d+?)/Uis', $article_date, $matches)) {

			$article_date_today = new DateTime();
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $matches[2] . '-' . $matches[1] . ' ' . $article_date_today->format('H:i:s'),
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}
}
