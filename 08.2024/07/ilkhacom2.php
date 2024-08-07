<?php

class ttgmena extends plugin_base
{

	// ANT settings
	protected $ant_precision = 6;
	protected $stop_on_date = true;
	protected $use_proxies = true; // Proxy 
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';
	// CRAWL settings
	protected $stop_on_article_found = true;
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
					'/<li[^>]*>\s*<a[^>]*>Haberler(.*)<\/ul>/Uis',
					'/(<a.*<\/a>)/Uis'
				)
			)
		),
		'section' => array(
			'link' => '/href="([^"]*)"/Uis',
			'name' => '/<a[^<]*>(.*)<\/a>/Uis',
			'append_domain' => true,
			'process_link' => 'filter_sections'
		)
	);

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="about-post-items backroundcolor-white-ss">(.*)<div class="fixed-bottom">/Uis',
					'/<div class="bussiness-post-thumb">.*<a href=".*"/Uis',
				),
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<div class="post-content">\s*<h3 class="title">(.*)<\/h3>/Uis',
			'content' => '/<div class="post-text[^>]*>(.*)<div id="relatedNews/Uis',
			'author' => false,
			'article_date' => '/<ul class="author-social">\s*<li class="cat-red">(.*)</Uis'
		)
	);

	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<h(?:2|3|4|5) class="title short-titles2"[^>]*>\s*<a[^>]*href="([^"]*)"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<div class="feature-news-content">\s*<a[^>]*href="([^"]*)"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true
			),
			2 => array(
				'type' => 'article',
				'regexp' => '/<h3 class="title">\s*<a[^>]*href="([^"]*)"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' => '/<div class="post-content">\s*<h3 class="title">(.*)<\/h3>/Uis',
			'content' => '/<div class="post-text[^>]*>(.*)<div id="relatedNews/Uis',
			'author' => false,
			'article_date' => '/<ul class="author-social">\s*<li class="cat-red">(.*)</Uis'
		)
	);


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
		//19.10.2023 10:24:40
		if (preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4}) /Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $matches[2] . '-' . $matches[1] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}

}
