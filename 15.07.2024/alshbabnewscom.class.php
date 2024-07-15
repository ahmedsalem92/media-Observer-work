<?php

class alshbabnewscom extends plugin_base
{

	// ANT settings
	protected $ant_precision = 6;
	protected $use_proxies = true;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';


	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';


	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/<ul.*class="main-header-menu.*">(.*)<\/ul>/Uis',
					'/(<a.*<\/a>)/Uis'
				)
			)
		),
		'section' => array(
			'link' => '/href="([^"]*)"/Uis',
			'name' => '/(?:<a[^<]*>|<\/i>)([^<]*)(?:<\/span>|<\/a>)/Uis',
			'append_domain' => false
		)
	);
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="nex" href="([^"]*)" class="next ">/Uis',
				'append_domain' => false,
				'process_link' => 'next_page_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="col-sm-8">(.*)<div class="cs-main-sidebar cs-sticky-sidebar col-sm-4">/Uis',
					'/<h3 class="post-title"><a href="([^"]*)"/Uis'
				),
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<div class="single-post-title">\s*<h1>(.*)<\/h1>/Uis',
			'content' => '/<div class="single-post-text">(.*)(?:<div class="block-title">|<div class="single-post-tags">)/Uis',
			'author' => false,
			'article_date' => '/<meta property="article:published_time" content="([^"]*)"/Uis'
		)
	);
	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="nex" href="([^"]*)" class="next ">/Uis',
				'append_domain' => false,
				'process_link' => 'next_page_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<article.*<a href="(.*)"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="markdown prose.*>(.*)<\/div>/Uis',
			'author' => false,
			'article_date' => '/<meta property="article:published_time" content="([^"]*)"/Uis'
		)
	);

	public function prepare_home($section_id)
	{

		$this->logic = $this->logic_home;
	}



	protected function next_page_link($link, $referer_link)
	{

		return $this->settings['site_section_link'] . $link;
	}

	protected function process_content($content, $article_data)
	{

		if (strpos($this->settings['site_section_link'], '/islamiat')) {

			return 'video';
		}
		if (preg_match('/^\s*<\/div>\s*$/Uis', $content, $matches)) {
			return 'Video';
		}
		if ($content == '') {
			return 'Video';
		}
		$content = preg_replace('/(<a.*<\/a>)/Uis', '', $content);
		$content = preg_replace('/(اقرأ أيضا.*<\/div>)/Uis', '', $content);
		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

		//2018-05-21T
		if (preg_match('/(.*)T/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . ' 18:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}
}
