<?php

class ttgmena extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = true;
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
				'regexp'=> '/<div class="article-brief">\s*<A href="(.*)"/Uis',
				'append_domain' => true
			),
			2 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="col single-category-articles">(.*)<div class="row align-items-center justify-content-center article-paginator single-category-paginator">/Uis',
					'/<div class="col-5">.*<a href="(.*)"/Uis'
				),
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<\/h1>(.*)<div class="paywall-wrap text-center">/Uis',
			'author' => false,
			'article_date' => '/<meta property="article:published_time" content="(.*)"/Uis'
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
			'article_date' => '/<meta property="article:published_time" content="(.*)"/Uis'
		)
	);




	protected function process_content($content, $article_data)
	{


		$content = preg_replace('/<div class="journalist-info">(.*)<div class="article-share pb-3 pb-sm-0">/Uis', '', $content);
		$content = preg_replace('/<span>Subscribe Now<\/span>/Uis', '', $content);
		$content = preg_replace('/<span>Subscribe Now<\/span>/Uis', '', $content);

		return $content;
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
	protected function process_date($article_date) {
		//	2018-08-29T03:23:41+00:00
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
