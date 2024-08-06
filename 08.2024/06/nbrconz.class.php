<?php

class nbrconz extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = true;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';
	protected $use_proxies = true;
	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	private $exclude_sections = array(
		'https://www.nbr.co.nz/nbr-radar', 'https://www.nbr.co.nz/the-nbr-list/archive'
	);
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/<div class="menu-items">(.*)<\/header>/Uis',
					'/(<a.*>.*<\/a>)/Uis'
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
				'regexp' => '/<a class="next" href="(.*)"/Uis',
				'append_domain' => true
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/(?:<\/h3>|<div class="article-brief">)\s*<A href="(.*)"/Uis',
				'append_domain' => true
			),
			2 => array(
				'type' => 'article',
				'regexp' => array(
					'/(?:<div class="row nbr-view-articles">|<div class="col single-category-articles">|<div class="row podcast-audios">)(.*)(?:<div\s*|<div )class="row align-items-center justify-content-center article-paginator single-category-paginator.*">/Uis',
					'/(?:<div class="article-brief">|<div class="col-5">|<div class="article-info">.*<\/a>).*<a href="(.*)"/Uis'
				),
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<\/h1>(.*)(?:(<div class="row article-copyright">)|<div class="paywall-wrap text-center">)/Uis',
			'author' => false,
			'article_date' => '/<meta property="article:modification_time" content="(.*)"/Uis'
		)
	);
	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="main-article d-xl-none main-article-mobile">(.*)<\/a>/Uis',
					'/href="(.*)"/Uis'
				),
				'append_domain' => true,
				'ignore_terminal_stop' => true,
			),
			1 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="col-6">(.*)<\/a>/Uis',
					'/href="(.*)"/Uis'
				),
				'append_domain' => true,
				'ignore_terminal_stop' => true,
			),
			2 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="item w-100">(.*)<\/a>/Uis',
					'/href="(.*)"/Uis'
				),
				'append_domain' => true,
				'ignore_terminal_stop' => true,
			),
			3 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="article-brief">(.*)<\/a>/Uis',
					'/href="(.*)"/Uis'
				),
				'append_domain' => true,
				'ignore_terminal_stop' => true,
			),
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<\/h1>(.*)(?:(<div class="row article-copyright">)|<div class="paywall-wrap text-center">)/Uis',
			'author' => false,
			'article_date' => '/<meta property="article:published_time" content="(.*)"/Uis'
		)
	);




	protected function process_content($content, $article_data)
	{


		$content = preg_replace('/<div class="journalist-info">(.*)<div class="article-share pb-3 pb-sm-0">/Uis', '', $content);
		$content = preg_replace('/<span>Subscribe Now<\/span>/Uis', '', $content);
		$content = preg_replace('/<span>Subscribe Now<\/span>/Uis', '', $content);
		$content = preg_replace('/<p class="main-image-caption">(.*)<\/p>/Uis', '', $content);
		$content = preg_replace('/<span class="d-block date-small">(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/<div class="content-end-journalist">(.*)<div class="row article-copyright">/Uis', '', $content);

		return $content;
	}


	public function prepare_home($section_id)
	{

		$this->logic = $this->logic_home;
	}


	protected function filter_sections($section_link, $section_name, $referer_link, $logic)
	{
		if (in_array(trim($section_link), $this->exclude_sections)) {
			return '';
		}
		return $section_link;
	}



	protected function process_date($article_date)
	{
		if (preg_match('/(.*)T(.*)\+/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;
	}


}
